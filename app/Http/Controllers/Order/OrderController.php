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
        $orders = Order::latest()->get();

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
        // Create order with COMPLETE status by default
        $orderData = $request->all();
        $orderData['order_status'] = OrderStatus::COMPLETE;

        $order = Order::create($orderData);

        // Create Order Details
        $contents = Cart::instance('order')->content();

        foreach ($contents as $content) {
            OrderDetails::create([
                'order_id' => $order->id,
                'product_id' => $content->id,
                'quantity' => $content->qty,
                'unitcost' => $content->price,
                'total' => $content->subtotal,
            ]);

            // Reduce stock immediately
            Product::where('id', $content->id)
                ->decrement('quantity', $content->qty);
        }

        // Clear cart
        Cart::destroy();

        return redirect()
            ->route('orders.index')
            ->with('success', 'Order has been created successfully!');
    }

    public function show(Order $order)
    {
        $order->loadMissing(['customer', 'details'])->get();

        return view('orders.show', [
            'order' => $order,
        ]);
    }

    public function update(Order $order, Request $request)
    {
        // Simple order update - basic info only
        $order->update($request->only(['customer_id', 'order_date', 'vat', 'total']));

        return redirect()
            ->route('orders.index')
            ->with('success', 'Order has been updated successfully!');
    }

    public function edit(Order $order)
    {
        return view('orders.edit', [
            'order' => $order,
            'customers' => Customer::all(['id', 'name', 'phone']),
        ]);
    }

    public function destroy(Order $order)
    {
        // Restore stock before deleting order
        $orderDetails = OrderDetails::where('order_id', $order->id)->get();

        foreach ($orderDetails as $detail) {
            Product::where('id', $detail->product_id)
                ->increment('quantity', $detail->quantity);
        }

        // Delete order details first
        OrderDetails::where('order_id', $order->id)->delete();

        // Delete the order
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
