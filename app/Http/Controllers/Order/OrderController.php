<?php

namespace App\Http\Controllers\Order;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderStoreRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderDetails;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Gloudemans\Shoppingcart\Facades\Cart;
use PDF;
use Illuminate\Support\Facades\File;
use setasign\Fpdi\Fpdi;
use FPDF;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['customer'])
            ->latest()
            ->get();

        return view('orders.index', [
            'orders' => $orders,
        ]);
    }

    public function create()
    {
        return view('orders.create', [
            'customers' => Customer::all(['id', 'name', 'phone']),
            'products' => Product::with(['category', 'unit'])->get(),
        ]);
    }

    public function store(OrderStoreRequest $request)
    {
        // Log the incoming request
        \Log::info('Order store request received', [
            'customer_id' => $request->customer_id,
            'payment_type' => $request->payment_type,
            'cart_items' => $request->cart_items,
            'pay' => $request->pay
        ]);

        DB::beginTransaction();

        try {
            // Get cart items from JSON
            $cartItems = json_decode($request->cart_items, true);

            if (empty($cartItems)) {
                \Log::warning('Empty cart items in order store');
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'No items in cart. Please add items before completing the order.');
            }

            // Validate stock availability before creating order
            $stockErrors = [];
            foreach ($cartItems as $item) {
                $product = Product::find($item['id']);
                if (!$product) {
                    $stockErrors[] = "Product with ID {$item['id']} not found.";
                    continue;
                }

                if ($product->quantity < $item['quantity']) {
                    $stockErrors[] = "Insufficient stock for {$product->name}. Available: {$product->quantity}, Requested: {$item['quantity']}";
                }
            }

            if (!empty($stockErrors)) {
                \Log::warning('Stock validation failed', [
                    'errors' => $stockErrors,
                    'cart_items' => $cartItems
                ]);

                // Return JSON error for AJAX requests
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stock validation failed: ' . implode(', ', $stockErrors)
                    ], 400);
                }

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Stock validation failed: ' . implode(', ', $stockErrors));
            }

            // Create order with explicit status and proper field mapping
            $orderData = $request->validated();
            \Log::info('Validated order data:', $orderData);
            $orderData['order_status'] = OrderStatus::PENDING; // New orders start as pending
            $orderData['order_date'] = $request->date ?? now()->format('Y-m-d'); // Map date to order_date

            // Calculate totals from cart
            $cartItems = json_decode($request->cart_items, true);
            $subTotal = collect($cartItems)->sum('total');
            $discountAmount = (float) ($request->discount_amount ?? 0);
            $serviceCharges = (float) ($request->service_charges ?? 0);
            $vat = 0; // Assuming no VAT for now
            $total = $subTotal - $discountAmount + $serviceCharges + $vat;

            $orderData['total_products'] = count($cartItems);
            $orderData['sub_total'] = (int) round($subTotal * 100); // Convert to cents
            $orderData['discount_amount'] = (int) round($discountAmount * 100);
            $orderData['service_charges'] = (int) round($serviceCharges * 100);
            $orderData['vat'] = (int) round($vat * 100);
            $orderData['total'] = (int) round($total * 100);
            $orderData['due'] = max(0, (int) round($total * 100) - (int) round($request->pay * 100));

            // Convert pay to cents
            $orderData['pay'] = (int) round($request->pay * 100);

            // Resolve default Walk-In Customer if not provided
            if (empty($orderData['customer_id'])) {
                $walkIn = Customer::firstOrCreate(
                    ['name' => 'Walk-In Customer'],
                    ['phone' => null, 'email' => null, 'address' => null]
                );
                $orderData['customer_id'] = $walkIn->id;
            }

            \Log::info('Final order data before create:', $orderData);
            $order = Order::create($orderData);

            // Create Order Details from cart items
            $orderDetails = [];

            foreach ($cartItems as $item) {
                $orderDetails[] = [
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'serial_number' => array_key_exists('serial_number', $item) && $item['serial_number'] !== '' ? (string) $item['serial_number'] : null,
                    'warranty_years' => isset($item['warranty_years']) ? (int) $item['warranty_years'] : null,
                    'quantity' => $item['quantity'],
                    'unitcost' => (int) round($item['price'] * 100), // Convert to cents
                    'total' => (int) round($item['total'] * 100), // Convert to cents
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Bulk insert order details
            OrderDetails::insert($orderDetails);

            // Update product inventory - reduce stock quantities
            foreach ($cartItems as $item) {
                $product = Product::find($item['id']);
                if ($product) {
                    $newQuantity = $product->quantity - $item['quantity'];

                    // Ensure quantity doesn't go negative
                    if ($newQuantity < 0) {
                        \Log::warning('Product stock going negative', [
                            'product_id' => $item['id'],
                            'product_name' => $product->name,
                            'current_stock' => $product->quantity,
                            'ordered_quantity' => $item['quantity'],
                            'order_id' => $order->id
                        ]);
                        $newQuantity = 0; // Set to 0 instead of negative
                    }

                    // Update product quantity
                    $product->update(['quantity' => $newQuantity]);

                    \Log::info('Product inventory updated', [
                        'product_id' => $item['id'],
                        'product_name' => $product->name,
                        'old_quantity' => $product->quantity + $item['quantity'],
                        'new_quantity' => $newQuantity,
                        'ordered_quantity' => $item['quantity'],
                        'order_id' => $order->id
                    ]);
                } else {
                    \Log::error('Product not found for inventory update', [
                        'product_id' => $item['id'],
                        'order_id' => $order->id
                    ]);
                }
            }

            DB::commit();

            // Check if this is an AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                // Clear the server-side cart instance if used
                try {
                    Cart::instance('sale')->destroy();
                } catch (\Throwable $e) {
                    // ignore if cart not set
                }
                // Load the order with relationships for the receipt
                $order->load(['customer', 'details.product']);

                // Prepare sold items data for frontend stock update
                $soldItems = collect($cartItems)->map(function($item) {
                    $product = Product::find($item['id']);
                    return [
                        'product_id' => $item['id'],
                        'product_name' => $product ? $product->name : 'Unknown Product',
                        'quantity' => $item['quantity'],
                        'new_stock' => $product ? $product->quantity : 0
                    ];
                })->toArray();

                return response()->json([
                    'success' => true,
                    'message' => 'Order has been created successfully!',
                    'order_id' => $order->id,
                    'soldItems' => $soldItems, // Add sold items for stock update
                    'order' => [
                        'id' => $order->id,
                        'invoice_no' => $order->invoice_no,
                        'order_date' => $order->order_date->format('d/m/Y, H:i:s'),
                        'customer' => [
                            'name' => $order->customer->name,
                            'phone' => $order->customer->phone,
                            'email' => $order->customer->email,
                        ],
                        'items' => $order->details->map(function($detail) {
                            return [
                                'name' => $detail->product->name,
                                'code' => $detail->product->code,
                                'serial_number' => $detail->serial_number,
                                'warranty_years' => $detail->warranty_years,
                                'quantity' => $detail->quantity,
                                'price' => $detail->unitcost / 100,
                                'total' => $detail->total / 100,
                            ];
                        }),
                        'subtotal' => $order->sub_total,
                        'discount' => $order->discount_amount,
                        'service_charges' => $order->service_charges,
                        'total' => $order->total,
                    ],
                    'redirect_url' => route('orders.receipt', $order)
                ]);
            }

            return redirect()
                ->route('orders.receipt', $order)
                ->with('success', 'Order has been created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Check if this is an AJAX request
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create order. Error: ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create order. Please try again. Error: ' . $e->getMessage());
        }
    }    public function show(Order $order)
    {
        $order->loadMissing(['customer', 'details.product']);

        return view('orders.show', [
            'order' => $order,
        ]);
    }

    public function edit(Order $order)
    {
        $order->loadMissing(['customer', 'details.product']);

        return view('orders.edit', [
            'order' => $order,
            'customers' => Customer::all(['id', 'name', 'phone']),
            'products' => Product::with(['category', 'unit'])->get(),
        ]);
    }

    public function update(Order $order, Request $request)
    {
        DB::beginTransaction();

        try {
            // Update order status to complete and reduce stock
            if ($order->order_status !== OrderStatus::COMPLETE) {
                $orderDetails = $order->details()->with('product')->get();

                // Update product quantities
                foreach ($orderDetails as $detail) {
                    $detail->product->decrement('quantity', $detail->quantity);
                }

                $order->update(['order_status' => OrderStatus::COMPLETE]);
            }

            DB::commit();

            return redirect()
                ->route('orders.index')
                ->with('success', 'Order has been completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->with('error', 'Failed to complete order. Please try again.');
        }
    }

    public function downloadPdfBill(Order $order)
    {
        // Load the order with its relationships
        $order->loadMissing(['customer', 'details.product']);

        // Check if we have a PDF letterhead
        $letterheadConfig = $this->getLetterheadConfig();
        $letterheadType = $letterheadConfig['letterhead_type'] ?? 'image';
        $letterheadFile = $letterheadConfig['letterhead_file'] ?? null;

        if ($letterheadType === 'pdf' && $letterheadFile) {
            // Handle PDF letterhead with overlay
            return $this->generatePdfWithPdfLetterhead($order, $letterheadFile);
        } else {
            // Handle image letterhead or no letterhead
            return $this->generateStandardPdf($order);
        }
    }

    private function generateStandardPdf(Order $order)
    {
        // Get letterhead configuration for toggles
        $letterheadConfig = $this->getLetterheadConfig();

        // Generate PDF using DomPDF (standard approach)
        $pdf = PDF::loadView('orders.pdf-bill', [
            'order' => $order,
            'letterheadConfig' => $letterheadConfig,
        ]);

        // Set paper size to A4 and orientation to portrait
        $pdf->setPaper('A4', 'portrait');

        // Set options for better rendering
        $pdf->setOptions([
            'dpi' => 72, // Match the positioning canvas DPI
            'defaultFont' => 'Arial',
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'isRemoteEnabled' => true,
        ]);

        // Generate filename
        $filename = "Invoice_{$order->invoice_no}_{$order->order_date->format('Y-m-d')}.pdf";

        // Return PDF download
        return $pdf->download($filename);
    }

    private function generatePdfWithPdfLetterhead(Order $order, $letterheadFile)
    {
        try {
            // Get letterhead configuration for toggles and positioning
            $letterheadConfig = $this->getLetterheadConfig();

            // First, generate the content PDF without letterhead background
            $contentPdf = PDF::loadView('orders.pdf-bill-overlay', [
                'order' => $order,
                'letterheadConfig' => $letterheadConfig,
                'positionMap' => $this->buildPositionMap($letterheadConfig['positions'] ?? []),
                'elementToggles' => $letterheadConfig['element_toggles'] ?? [],
            ]);

            $contentPdf->setPaper('A4', 'portrait');
            $contentPdf->setOptions([
                'dpi' => 72, // Match the positioning canvas DPI
                'defaultFont' => 'Arial',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'isRemoteEnabled' => true,
            ]);

            // Save content PDF to temporary file
            $tempContentPath = storage_path('app/temp_content_' . $order->id . '.pdf');
            file_put_contents($tempContentPath, $contentPdf->output());

            // Path to letterhead PDF
            $letterheadPath = public_path('letterheads/' . $letterheadFile);

            // Try to merge PDFs using FPDI if available
            if (class_exists('setasign\Fpdi\Fpdi')) {
                $mergedPdf = $this->mergePdfsWithFpdi($letterheadPath, $tempContentPath);

                // Clean up temp file
                if (File::exists($tempContentPath)) {
                    File::delete($tempContentPath);
                }

                $filename = "Invoice_{$order->invoice_no}_{$order->order_date->format('Y-m-d')}.pdf";

                return response($mergedPdf, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]);
            } else {
                // Fallback to standard PDF generation
                File::delete($tempContentPath);
                return $this->generateStandardPdf($order);
            }

        } catch (\Exception $e) {
            // Log error and fallback to standard generation
            \Log::error('PDF merge failed: ' . $e->getMessage());
            return $this->generateStandardPdf($order);
        }
    }

    private function mergePdfsWithFpdi($letterheadPath, $contentPath)
    {
        $fpdi = new Fpdi();

        // Import letterhead PDF
        $fpdi->setSourceFile($letterheadPath);
        $letterheadTemplate = $fpdi->importPage(1);

        // Import content PDF
        $fpdi->setSourceFile($contentPath);
        $contentTemplate = $fpdi->importPage(1);

        // Create new page
        $fpdi->AddPage();

        // Use letterhead as background
        $fpdi->useTemplate($letterheadTemplate);

        // Overlay content
        $fpdi->useTemplate($contentTemplate);

        return $fpdi->Output('S'); // Return as string
    }

    private function getLetterheadConfig()
    {
        $configPath = storage_path('app/letterhead_config.json');
        if (File::exists($configPath)) {
            return json_decode(File::get($configPath), true);
        }
        return [];
    }

    private function buildPositionMap($positions)
    {
        $map = [];
        foreach ($positions as $position) {
            $map[$position['field']] = $position;
        }
        return $map;
    }

    public function showReceipt(Order $order)
    {
        $order->loadMissing(['customer', 'details.product']);

        return view('orders.pos-receipt', [
            'order' => $order,
        ]);
    }
}
