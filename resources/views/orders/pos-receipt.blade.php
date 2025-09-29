<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Receipt - {{ $order->invoice_no }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            background-color: #f5f5f5;
            padding: 20px;
            line-height: 1.4;
        }

        .receipt-container {
            background: white;
            max-width: 380px;
            margin: 0 auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            background: #e2e8f0;
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            font-size: 18px;
            color: #64748b;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .close-btn:hover {
            background: #cbd5e1;
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 20px;
        }

        .company-logo {
            width: 60px;
            height: 60px;
            background: #f59e0b;
            border-radius: 50%;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            font-size: 24px;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .company-address {
            font-size: 12px;
            color: #666;
            margin-bottom: 2px;
        }

        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
            font-size: 12px;
        }

        .receipt-info div {
            text-align: right;
        }

        .receipt-info div:first-child {
            text-align: left;
        }

        .customer-section {
            margin: 20px 0;
            padding: 10px 0;
            border-top: 1px dashed #ccc;
            border-bottom: 1px dashed #ccc;
        }

        .customer-title {
            font-weight: bold;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .customer-info {
            font-size: 12px;
            color: #666;
        }

        .customer-info div {
            margin-bottom: 2px;
        }

        .items-section {
            margin: 20px 0;
        }

        .items-header {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            padding-bottom: 5px;
            border-bottom: 1px solid #ccc;
            font-size: 12px;
        }

        .item-row {
            display: grid;
            grid-template-columns: 1fr auto auto auto;
            gap: 8px;
            align-items: flex-start;
            padding: 8px 0;
            border-bottom: 1px dotted #ddd;
            font-size: 11px;
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .item-details {
            grid-column: 1;
        }

        .item-name {
            font-weight: bold;
            margin-bottom: 2px;
            word-wrap: break-word;
        }

        .item-meta {
            font-size: 10px;
            color: #666;
        }

        .item-pricing {
            display: contents;
        }

        .item-pricing > div {
            text-align: right;
            white-space: nowrap;
        }

        .warranty {
            color: #3b82f6;
            font-size: 10px;
            margin-top: 2px;
        }

        .totals-section {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px dashed #ccc;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 12px;
        }

        .total-row.final {
            font-weight: bold;
            font-size: 14px;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid #ccc;
        }

        .print-actions {
            margin-top: 20px;
            text-align: center;
            padding-top: 15px;
            border-top: 1px dashed #ccc;
        }

        .print-btn {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            margin-right: 10px;
        }

        .print-btn:hover {
            background: #2563eb;
        }

        .back-btn {
            background: #6b7280;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            display: inline-block;
        }

        .back-btn:hover {
            background: #4b5563;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .receipt-container {
                box-shadow: none;
                border-radius: 0;
                max-width: none;
                width: 80mm;
                margin: 0;
                padding: 10px;
            }
            
            .close-btn,
            .print-actions {
                display: none !important;
            }
        }

        @media screen and (max-width: 480px) {
            .receipt-container {
                margin: 10px;
                max-width: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container" id="receipt">
        <button class="close-btn" onclick="window.close()" title="Close">&times;</button>
        
        <div class="receipt-header">
            <div class="company-logo">A</div>
            <div class="company-name">Aura PC Factory</div>
            <div class="company-address">Kodikama Road, Samiyan Arasady, Nellaidy.</div>
            <div class="company-address">+94770221046 | ikirupan@gmail.com</div>
        </div>

        <div class="receipt-info">
            <div>
                <strong>Receipt #:</strong><br>
                <strong>Date:</strong>
            </div>
            <div>
                {{ $order->invoice_no }}<br>
                {{ \Carbon\Carbon::parse($order->order_date)->format('d/m/Y, H:i:s') }}
            </div>
        </div>

        <div class="customer-section">
            <div class="customer-title">Customer Details</div>
            <div class="customer-info">
                <div><strong>Name:</strong> {{ $order->customer->name }}</div>
                <div><strong>Phone:</strong> {{ $order->customer->phone }}</div>
                <div><strong>Email:</strong> {{ $order->customer->email }}</div>
            </div>
        </div>

        <div class="items-section">
            <div class="items-header">
                <span>Item</span>
                <span>Qty</span>
                <span>Price</span>
                <span>Total</span>
            </div>

            @foreach($order->details as $index => $item)
            <div class="item-row">
                <div class="item-details">
                    <div class="item-name">{{ $index + 1 }}. {{ $item->product->name }}</div>
                    <div class="item-meta">
                        @if($item->product->code)
                            S/N: {{ $item->product->code }}<br>
                        @endif
                        @if($item->product->category)
                            <span class="warranty">Warranty: 3 years</span>
                        @endif
                    </div>
                </div>
                <div>{{ $item->quantity }}</div>
                <div>LKR {{ number_format($item->unitcost / 100, 2) }}</div>
                <div>LKR {{ number_format($item->total / 100, 2) }}</div>
            </div>
            @endforeach
        </div>

        <div class="totals-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>LKR {{ number_format($order->sub_total / 100, 2) }}</span>
            </div>
            @if($order->sub_total != $order->total)
                <div class="total-row">
                    <span>Discount:</span>
                    <span>-LKR {{ number_format(($order->sub_total - $order->total) / 100, 2) }}</span>
                </div>
            @endif
            <div class="total-row final">
                <span>TOTAL:</span>
                <span>LKR {{ number_format($order->total / 100, 2) }}</span>
            </div>
        </div>

        <div class="print-actions">
            <button class="print-btn" onclick="window.print()">🖨️ Print Receipt</button>
            <a href="{{ route('orders.create') }}" class="back-btn">← New Order</a>
        </div>
    </div>

    <script>
        // Auto-print when page loads (optional)
        // window.onload = function() {
        //     setTimeout(function() {
        //         window.print();
        //     }, 500);
        // };

        // Print function
        function printReceipt() {
            window.print();
        }

        // Close on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                window.close();
            }
        });
    </script>
</body>
</html>