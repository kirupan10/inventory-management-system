<?php
/**
 * Test PDF Generation with All Parameters
 * Run with: php artisan tinker
 * Then: include('test_pdf_data.php')
 */

// Get the latest order with all relationships
$order = \App\Models\Order::with(['details.product', 'customer'])->latest()->first();

if (!$order) {
    echo "No orders found in database.\n";
    return;
}

echo "=== NEXORA PDF DATA TEST ===\n\n";

echo "Order Information:\n";
echo "- ID: {$order->id}\n";
echo "- Invoice No: {$order->invoice_no}\n";
echo "- Date: {$order->order_date->format('Y-m-d')}\n";
echo "- Status: {$order->order_status->value}\n";
echo "- Payment Type: {$order->payment_type}\n";
echo "- Total Products: {$order->total_products}\n\n";

echo "Customer Information:\n";
echo "- Name: {$order->customer->name}\n";
echo "- Phone: {$order->customer->phone}\n";
echo "- Email: {$order->customer->email}\n";
echo "- Address: {$order->customer->address}\n\n";

echo "Financial Summary:\n";
echo "- Subtotal: LKR " . number_format($order->sub_total, 2) . "\n";
echo "- Discount: LKR " . number_format($order->discount_amount, 2) . "\n";
echo "- Service Charges: LKR " . number_format($order->service_charges, 2) . "\n";
echo "- VAT: LKR " . number_format($order->vat, 2) . "\n";
echo "- Grand Total: LKR " . number_format($order->total, 2) . "\n";
echo "- Amount Paid: LKR " . number_format($order->pay, 2) . "\n";
echo "- Amount Due: LKR " . number_format($order->due, 2) . "\n\n";

echo "Order Items:\n";
foreach ($order->details as $index => $item) {
    $itemNumber = $index + 1;
    echo "  {$itemNumber}. {$item->product->name}\n";
    echo "     - Quantity: {$item->quantity}\n";
    echo "     - Unit Price: LKR " . number_format($item->unitcost, 2) . "\n";
    echo "     - Total: LKR " . number_format($item->total, 2) . "\n";
    if ($item->serial_number) {
        echo "     - Serial Number: {$item->serial_number}\n";
    }
    if ($item->warranty_years) {
        echo "     - Warranty: {$item->warranty_years} year(s)\n";
    }
    echo "\n";
}

echo "=== All parameters are now included in PDF template ===\n";
echo "To test PDF generation, visit: http://127.0.0.1:8002/orders/{$order->id}/pdf\n";
?>
