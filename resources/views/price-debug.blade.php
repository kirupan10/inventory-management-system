<!DOCTYPE html>
<html>
<head>
    <title>Price Debug</title>
    <style>body { font-family: Arial; padding: 20px; } .debug { background: #f0f0f0; padding: 10px; margin: 10px 0; }</style>
</head>
<body>
    <h1>NEXORA PRICE DEBUG</h1>

    <?php
    // Find the latest order
    $order = \App\Models\Order::with(['details.product', 'customer'])->latest()->first();

    if ($order) {
        echo "<h2>Order #{$order->id} - Invoice: {$order->invoice_no}</h2>";

        echo "<div class='debug'>";
        echo "<h3>Order Totals Debug:</h3>";
        echo "<p><strong>Raw sub_total from DB:</strong> " . $order->getOriginal('sub_total') . "</p>";
        echo "<p><strong>Accessor sub_total:</strong> " . $order->sub_total . "</p>";
        echo "<p><strong>Formatted accessor:</strong> LKR " . number_format($order->sub_total, 2, '.', ',') . "</p>";
        echo "<p><strong>Manual division (raw/100):</strong> LKR " . number_format($order->getOriginal('sub_total') / 100, 2, '.', ',') . "</p>";
        echo "</div>";

        foreach ($order->details as $index => $item) {
            echo "<div class='debug'>";
            echo "<h3>Item " . ($index + 1) . ": {$item->product->name}</h3>";
            echo "<p><strong>Raw unitcost from DB:</strong> " . $item->getOriginal('unitcost') . "</p>";
            echo "<p><strong>Accessor unitcost:</strong> " . $item->unitcost . "</p>";
            echo "<p><strong>Formatted accessor:</strong> LKR " . number_format($item->unitcost, 2, '.', ',') . "</p>";
            echo "<p><strong>Manual division (raw/100):</strong> LKR " . number_format($item->getOriginal('unitcost') / 100, 2, '.', ',') . "</p>";
            echo "<p><strong>Raw total from DB:</strong> " . $item->getOriginal('total') . "</p>";
            echo "<p><strong>Accessor total:</strong> " . $item->total . "</p>";
            echo "<p><strong>Formatted accessor total:</strong> LKR " . number_format($item->total, 2, '.', ',') . "</p>";
            echo "</div>";

            if ($index >= 2) break; // Only show first 3 items
        }

        echo "<h2>WHAT SHOULD APPEAR IN PDF:</h2>";
        echo "<p>If unitcost accessor shows 880, then PDF should show LKR 880.00</p>";
        echo "<p>If unitcost accessor shows 88000, then PDF should show LKR 88,000.00</p>";
        echo "<p>If raw unitcost is 88000 but accessor shows 880, then accessor is working correctly (88000/100=880)</p>";
        echo "<p>If raw unitcost is 8800000 but accessor shows 88000, then accessor is working correctly (8800000/100=88000)</p>";

    } else {
        echo "<p>No orders found!</p>";
    }
    ?>

</body>
</html>
