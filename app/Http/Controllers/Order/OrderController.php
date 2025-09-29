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
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        Cart::instance('order')
            ->destroy();

        return view('orders.create', [
            'carts' => Cart::content(),
            'customers' => Customer::all(['id', 'name', 'phone']),
            'products' => Product::with(['category', 'unit'])->get(),
        ]);
    }

    public function store(OrderStoreRequest $request)
    {
        DB::beginTransaction();

        try {
            $order = Order::create($request->all());

            // Create Order Details
            $contents = Cart::instance('order')->content();
            $orderDetails = [];

            foreach ($contents as $content) {
                $orderDetails[] = [
                    'order_id' => $order->id,
                    'product_id' => $content->id,
                    'quantity' => $content->qty,
                    'unitcost' => $content->price,
                    'total' => $content->subtotal,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Bulk insert order details
            OrderDetails::insert($orderDetails);

            // Clear cart
            Cart::instance('order')->destroy();

            DB::commit();

            return redirect()
                ->route('orders.index')
                ->with('success', 'Order has been created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create order. Please try again.');
        }
    }

    public function show(Order $order)
    {
        $order->loadMissing(['customer', 'details.product']);

        return view('orders.show', [
            'order' => $order,
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

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()
            ->route('orders.index')
            ->with('success', 'Order has been deleted successfully!');
    }

    public function downloadInvoice($order)
    {
        $order = Order::with(['customer', 'details'])
            ->where('id', $order)
            ->first();

        return view('orders.print-invoice', [
            'order' => $order,
        ]);
    }
}
