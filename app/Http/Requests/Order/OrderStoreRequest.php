<?php

namespace App\Http\Requests\Order;

use App\Enums\OrderStatus;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class OrderStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => 'nullable|exists:customers,id',
            'payment_type' => 'required|in:Cash,Card,Bank Transfer',
            'pay' => 'nullable|numeric|min:0',
            'cart_items' => 'required|json|min:3',
            'date' => 'nullable|date',
            'reference' => 'nullable|string',
            'invoice_no' => 'nullable|string',
        ];
    }

    public function messages()
    {
        return [
            'customer_id.exists' => 'Selected customer does not exist.',
            'payment_type.required' => 'Please select a payment method.',
            'payment_type.in' => 'Invalid payment method selected.',
            'cart_items.required' => 'Cart cannot be empty.',
            'cart_items.json' => 'Invalid cart data format.',
            'cart_items.min' => 'Cart data is too short.',
        ];
    }

    public function prepareForValidation(): void
    {
        \Log::info('OrderStoreRequest prepareForValidation called', [
            'cart_items_raw' => $this->cart_items,
            'customer_id' => $this->customer_id,
            'payment_type' => $this->payment_type
        ]);

        // Get cart items from JSON
        $cartItems = json_decode($this->cart_items, true) ?? [];

        \Log::info('Cart items decoded', ['cart_items' => $cartItems]);

        // Calculate totals from cart items
        $totalProducts = array_sum(array_column($cartItems, 'quantity'));
        $subTotal = array_sum(array_column($cartItems, 'total'));
        $vat = $subTotal * 0.1; // 10% VAT
        $total = $subTotal + $vat;
        $payAmount = (float) ($this->pay ?? 0);

        // Convert to integers (multiply by 100 to store cents)
        $subTotalInt = (int) round($subTotal * 100);
        $vatInt = (int) round($vat * 100);
        $totalInt = (int) round($total * 100);
        $payInt = (int) round($payAmount * 100);
        $dueInt = $totalInt - $payInt;

        $this->merge([
            'order_date' => Carbon::now()->format('Y-m-d'),
            'order_status' => OrderStatus::PENDING->value,
            'total_products' => $totalProducts,
            'sub_total' => $subTotalInt,
            'vat' => $vatInt,
            'total' => $totalInt,
            'invoice_no' => $this->generateInvoiceNumber(),
            'pay' => $payInt,
            'due' => $dueInt,
        ]);
    }

    private function generateInvoiceNumber(): string
    {
        // Get the last order with the new ORDR00001 format (5 digits)
        $lastOrder = \App\Models\Order::where('invoice_no', 'REGEXP', '^ORDR[0-9]{5}$')
            ->orderBy('invoice_no', 'desc')
            ->first();

        if ($lastOrder) {
            // Extract number from last invoice (e.g., ORDR00001 -> 1)
            $lastNumber = (int) substr($lastOrder->invoice_no, 4);
            $nextNumber = $lastNumber + 1;
        } else {
            // Start from 1 if no orders with new format exist
            $nextNumber = 1;
        }

        // Format as ORDR00001, ORDR00002, etc.
        return 'ORDR' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
