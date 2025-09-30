<!DOCTYPE html>
<html>
<head>
    <title>Nexora - Calculation Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa; }
        .debug-container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .debug-section { margin-bottom: 30px; padding: 15px; border: 1px solid #dee2e6; border-radius: 5px; }
        .debug-title { color: #2d3748; font-size: 18px; font-weight: bold; margin-bottom: 15px; border-bottom: 2px solid #3182ce; padding-bottom: 5px; }
        .calc-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dotted #ccc; }
        .calc-label { font-weight: bold; color: #4a5568; }
        .calc-value { color: #2d3748; }
        .error { color: #e53e3e; font-weight: bold; }
        .success { color: #38a169; font-weight: bold; }
        .warning { color: #d69e2e; font-weight: bold; }
        .grand-total { background: #e2e8f0; padding: 10px; border-radius: 5px; font-size: 18px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="debug-container">
        <h1 style="text-align: center; color: #2d3748;">Nexora - Order Calculation Debug Tool</h1>

        @if(isset($order))
        <div class="debug-section">
            <div class="debug-title">Order Information</div>
            <div class="calc-row">
                <span class="calc-label">Order ID:</span>
                <span class="calc-value">{{ $order->id }}</span>
            </div>
            <div class="calc-row">
                <span class="calc-label">Invoice No:</span>
                <span class="calc-value">{{ $order->invoice_no }}</span>
            </div>
            <div class="calc-row">
                <span class="calc-label">Created:</span>
                <span class="calc-value">{{ $order->created_at->format('Y-m-d H:i:s') }}</span>
            </div>
        </div>

        <div class="debug-section">
            <div class="debug-title">Raw Database Values (in cents)</div>
            <div class="calc-row">
                <span class="calc-label">Sub Total (cents):</span>
                <span class="calc-value">{{ number_format($order->sub_total) }}</span>
            </div>
            <div class="calc-row">
                <span class="calc-label">Discount (cents):</span>
                <span class="calc-value">{{ number_format($order->discount_amount) }}</span>
            </div>
            <div class="calc-row">
                <span class="calc-label">Service Charges (cents):</span>
                <span class="calc-value">{{ number_format($order->service_charges) }}</span>
            </div>
            <div class="calc-row">
                <span class="calc-label">Total (cents):</span>
                <span class="calc-value">{{ number_format($order->total) }}</span>
            </div>
        </div>

        <div class="debug-section">
            <div class="debug-title">Converted Values (LKR)</div>
            @php
                $subTotal = $order->sub_total / 100;
                $discount = $order->discount_amount / 100;
                $serviceCharges = $order->service_charges / 100;
                $grandTotal = $order->total / 100;
            @endphp
            <div class="calc-row">
                <span class="calc-label">Sub Total:</span>
                <span class="calc-value">LKR {{ number_format($subTotal, 2) }}</span>
            </div>
            <div class="calc-row">
                <span class="calc-label">Discount:</span>
                <span class="calc-value">-LKR {{ number_format($discount, 2) }}</span>
            </div>
            <div class="calc-row">
                <span class="calc-label">Service Charges:</span>
                <span class="calc-value">+LKR {{ number_format($serviceCharges, 2) }}</span>
            </div>
            <div class="calc-row grand-total">
                <span class="calc-label">Grand Total:</span>
                <span class="calc-value">LKR {{ number_format($grandTotal, 2) }}</span>
            </div>
        </div>

        <div class="debug-section">
            <div class="debug-title">Manual Calculation Verification</div>
            @php
                $calculatedTotal = $subTotal - $discount + $serviceCharges;
                $isCorrect = abs($calculatedTotal - $grandTotal) < 0.01; // Allow for rounding
            @endphp
            <div class="calc-row">
                <span class="calc-label">Manual Calculation:</span>
                <span class="calc-value">{{ number_format($subTotal, 2) }} - {{ number_format($discount, 2) }} + {{ number_format($serviceCharges, 2) }} = {{ number_format($calculatedTotal, 2) }}</span>
            </div>
            <div class="calc-row">
                <span class="calc-label">Database Total:</span>
                <span class="calc-value">LKR {{ number_format($grandTotal, 2) }}</span>
            </div>
            <div class="calc-row">
                <span class="calc-label">Difference:</span>
                <span class="calc-value {{ $isCorrect ? 'success' : 'error' }}">
                    {{ $isCorrect ? 'CORRECT ✓' : 'ERROR: ' . number_format($calculatedTotal - $grandTotal, 2) }}
                </span>
            </div>
        </div>

        <div class="debug-section">
            <div class="debug-title">Order Items Breakdown</div>
            @php $itemsTotal = 0; @endphp
            @foreach($order->orderItems as $item)
                @php
                    $itemPrice = $item->unit_price / 100;
                    $itemSubtotal = ($item->unit_price * $item->quantity) / 100;
                    $itemsTotal += $itemSubtotal;
                @endphp
                <div class="calc-row">
                    <span class="calc-label">{{ $item->product->name ?? 'Product' }} ({{ $item->quantity }}x):</span>
                    <span class="calc-value">{{ number_format($itemPrice, 2) }} × {{ $item->quantity }} = LKR {{ number_format($itemSubtotal, 2) }}</span>
                </div>
            @endforeach
            <div class="calc-row" style="border-top: 2px solid #000; margin-top: 10px; padding-top: 10px;">
                <span class="calc-label">Items Total:</span>
                <span class="calc-value {{ abs($itemsTotal - $subTotal) < 0.01 ? 'success' : 'error' }}">
                    LKR {{ number_format($itemsTotal, 2) }} {{ abs($itemsTotal - $subTotal) < 0.01 ? '✓' : '✗' }}
                </span>
            </div>
        </div>

        <div class="debug-section">
            <div class="debug-title">JavaScript Format Test</div>
            <div class="calc-row">
                <span class="calc-label">Sub Total (JS Format):</span>
                <span class="calc-value" id="js-subtotal"></span>
            </div>
            <div class="calc-row">
                <span class="calc-label">Final Total (JS Format):</span>
                <span class="calc-value" id="js-total"></span>
            </div>
        </div>

        @else
        <div class="debug-section">
            <div class="error">No order data provided. Please provide an order ID.</div>
        </div>
        @endif
    </div>

    <script>
        // Test JavaScript formatting
        function formatCurrency(amount) {
            return amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        }

        @if(isset($order))
        const subTotal = {{ $order->sub_total / 100 }};
        const discount = {{ $order->discount_amount / 100 }};
        const serviceCharges = {{ $order->service_charges / 100 }};
        const finalTotal = Math.round((subTotal - discount + serviceCharges) * 100) / 100;

        document.getElementById('js-subtotal').textContent = 'LKR ' + formatCurrency(subTotal);
        document.getElementById('js-total').textContent = 'LKR ' + formatCurrency(finalTotal);
        @endif

        // Log calculations to console for debugging
        console.log('=== NEXORA CALCULATION DEBUG ===');
        @if(isset($order))
        console.log('Sub Total:', {{ $order->sub_total / 100 }});
        console.log('Discount:', {{ $order->discount_amount / 100 }});
        console.log('Service Charges:', {{ $order->service_charges / 100 }});
        console.log('Expected Total:', {{ $order->sub_total / 100 }} - {{ $order->discount_amount / 100 }} + {{ $order->service_charges / 100 }}, '=', ({{ $order->sub_total / 100 }} - {{ $order->discount_amount / 100 }} + {{ $order->service_charges / 100 }}));
        console.log('Database Total:', {{ $order->total / 100 }});
        @endif
    </script>
</body>
</html>
