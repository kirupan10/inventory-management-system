<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\OrderDetails;
use App\Enums\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    /**
     * Process payment for an order
     */
    public function processPayment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_data' => 'required|array',
            'order_data.customer_id' => 'required|exists:customers,id',
            'order_data.payment_type' => 'required|string',
            'order_data.cart_items' => 'required|array|min:1',
            'payment_amount' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $orderData = $request->order_data;
            $cartItems = $orderData['cart_items'];
            $paymentAmount = $request->payment_amount;

            // Calculate totals
            $subTotal = 0;
            $totalProducts = count($cartItems);

            foreach ($cartItems as $item) {
                $subTotal += $item['price'] * $item['quantity'];
            }

            $vat = $subTotal * 0.15; // 15% VAT
            $total = $subTotal + $vat;

            // Validate payment amount matches total
            if (abs($paymentAmount - $total) > 0.01) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment amount does not match order total',
                    'required_amount' => $total,
                    'provided_amount' => $paymentAmount
                ], 400);
            }

            // Check product availability
            foreach ($cartItems as $item) {
                $product = Product::find($item['id']);
                if (!$product) {
                    return response()->json([
                        'success' => false,
                        'message' => "Product with ID {$item['id']} not found"
                    ], 404);
                }

                if ($product->quantity < $item['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for product: {$product->name}. Available: {$product->quantity}, Requested: {$item['quantity']}"
                    ], 400);
                }
            }

            // Generate invoice number
            $invoiceNumber = 'INV-' . strtoupper(uniqid());

            // Create order
            $order = Order::create([
                'customer_id' => $orderData['customer_id'],
                'order_date' => now()->format('Y-m-d'),
                'order_status' => OrderStatus::COMPLETE,
                'total_products' => $totalProducts,
                'sub_total' => $subTotal * 100, // Store in cents
                'vat' => $vat * 100,
                'total' => $total * 100,
                'invoice_no' => $invoiceNumber,
                'payment_type' => $orderData['payment_type'],
                'pay' => $paymentAmount * 100,
                'due' => 0, // No due amount for complete payment
            ]);

            // Create order details and update product quantities
            foreach ($cartItems as $item) {
                OrderDetails::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'unitcost' => $item['price'] * 100, // Store in cents
                    'total' => ($item['price'] * $item['quantity']) * 100,
                ]);

                // Update product quantity
                Product::where('id', $item['id'])
                    ->decrement('quantity', $item['quantity']);
            }

            // Create payment record
            $payment = Payment::create([
                'order_id' => $order->id,
                'amount' => $paymentAmount,
                'payment_method' => $orderData['payment_type'],
                'status' => 'completed',
                'transaction_id' => 'TXN-' . strtoupper(uniqid()),
                'processed_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'data' => [
                    'order_id' => $order->id,
                    'invoice_no' => $order->invoice_no,
                    'payment_id' => $payment->id,
                    'transaction_id' => $payment->transaction_id,
                    'total_amount' => $total,
                    'payment_amount' => $paymentAmount,
                    'change_amount' => max(0, $paymentAmount - $total),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get payment details for an order
     */
    public function show($orderId): JsonResponse
    {
        try {
            $order = Order::with(['payments', 'customer', 'details.product'])
                ->findOrFail($orderId);

            return response()->json([
                'success' => true,
                'data' => $order
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
        }
    }
}
