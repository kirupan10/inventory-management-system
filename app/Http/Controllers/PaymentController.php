<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function modal(Request $request)
    {
        $cartItems = $request->input('cart_items', []);
        $customerId = $request->input('customer_id');

        return view('partials.payment-modal', [
            'cartItems' => $cartItems,
            'customerId' => $customerId,
            'customers' => Customer::all(['id', 'name', 'phone']),
        ]);
    }
}
