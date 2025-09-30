<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->invoice_no }}</title>
    <style>
        @php
            $letterheadConfig = json_decode(file_get_contents(storage_path('app/letterhead_config.json')), true) ?? [];
            $positions = $letterheadConfig['positions'] ?? [];

            // Convert positions array to associative array for easier lookup
            $positionMap = [];
            foreach ($positions as $pos) {
                $positionMap[$pos['field']] = $pos;
            }
        @endphp

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 10px; /* Default font size for printable elements */
            line-height: 1.3;
            color: #000;
            background: transparent; /* Transparent background for overlay */
        }

        .page {
            width: 595px;
            height: 842px;
            position: relative;
            margin: 0 auto;
            background: transparent; /* No background - content only */
        }

        .positioned-element {
            position: absolute;
            font-family: Arial, sans-serif;
            color: #000;
            line-height: 1;
        }

        .items-table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 15px;
        }

        .items-table th {
            background: rgba(245, 245, 245, 0.95);
            border: 1px solid #333;
            padding: 8px 6px;
            text-align: left;
            font-weight: bold;
            font-size: 10px;
            line-height: 1.2;
        }

        .items-table td {
            border: none;
            padding: 6px;
            font-size: 10px;
            background: transparent;
            vertical-align: top;
            line-height: 1.2;
        }

        .item-name {
            font-weight: bold;
            margin-bottom: 2px;
            font-size: 10px;
        }

        .item-details {
            font-size: 8px;
            color: #000;
            margin-bottom: 1px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .payment-table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }

        .payment-table td {
            padding: 4px 8px;
            font-size: 10px;
            border-bottom: 1px solid #ddd;
            background: rgba(255, 255, 255, 0.95);
        }

        .payment-table .label {
            text-align: left;
            font-weight: normal;
        }

        .payment-table .amount {
            text-align: right;
            font-weight: bold;
        }

        .payment-table .total-row td {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 6px 8px;
            font-weight: bold;
            font-size: 11px;
        }

        .discount-amount {
            color: #dc3545;
        }

        .warranty-text {
            font-size: 9px;
            line-height: 1.3;
            width: 500px;
            background: rgba(255, 255, 255, 0.9);
            padding: 8px;
            border: 1px solid #ddd;
        }

        .warranty-title {
            font-weight: bold;
            margin-bottom: 4px;
            font-size: 10px;
        }

        .warranty-duration {
            font-weight: bold;
            margin-bottom: 4px;
            font-size: 9px;
        }

        .warranty-clause {
            margin-bottom: 3px;
            line-height: 1.2;
        }

        @media print {
            body {
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
    <div class="page">
        {{-- Content overlay for PDF letterhead --}}







        {{-- Invoice Number --}}
        @if(isset($positionMap['invoice_no']))
        <div class="positioned-element" style="
            left: {{ $positionMap['invoice_no']['x'] ?? 400 }}px;
            top: {{ $positionMap['invoice_no']['y'] ?? 50 }}px;
            font-size: {{ $positionMap['invoice_no']['font_size'] ?? 12 }}px;
            font-weight: {{ $positionMap['invoice_no']['font_weight'] ?? 'bold' }};
            /* background removed for better positioning */

        ">
            {{ $order->invoice_no }}
        </div>
        @endif

        {{-- Invoice Date --}}
        @if(isset($positionMap['invoice_date']))
        <div class="positioned-element" style="
            left: {{ $positionMap['invoice_date']['x'] ?? 400 }}px;
            top: {{ $positionMap['invoice_date']['y'] ?? 70 }}px;
            font-size: {{ $positionMap['invoice_date']['font_size'] ?? 10 }}px;
            font-weight: {{ $positionMap['invoice_date']['font_weight'] ?? 'normal' }};
            /* background removed for better positioning */

        ">
            {{ $order->order_date->format('d/m/Y') }}
        </div>
        @endif

        {{-- Customer Details Header --}}
        @if(isset($positionMap['customer_name']) && (!isset($elementToggles['customer_name']) || $elementToggles['customer_name']))
        <div class="positioned-element" style="
            left: {{ $positionMap['customer_name']['x'] ?? 50 }}px;
            top: {{ ($positionMap['customer_name']['y'] ?? 150) - 20 }}px;
            font-size: {{ ($positionMap['customer_name']['font_size'] ?? 10) + 1 }}px;
            font-weight: bold;
        ">
            Customer Details
        </div>
        @endif

        {{-- Customer Name --}}
        @if(isset($positionMap['customer_name']) && (!isset($elementToggles['customer_name']) || $elementToggles['customer_name']))
        <div class="positioned-element" style="
            left: {{ $positionMap['customer_name']['x'] ?? 50 }}px;
            top: {{ $positionMap['customer_name']['y'] ?? 150 }}px;
            font-size: {{ $positionMap['customer_name']['font_size'] ?? 10 }}px;
            font-weight: {{ $positionMap['customer_name']['font_weight'] ?? 'normal' }};
        ">
            {{ $order->customer->name }}
        </div>
        @endif

        {{-- Customer Phone --}}
        @if(isset($positionMap['customer_phone']) && (!isset($elementToggles['customer_phone']) || $elementToggles['customer_phone']))
        <div class="positioned-element" style="
            left: {{ $positionMap['customer_phone']['x'] ?? 50 }}px;
            top: {{ $positionMap['customer_phone']['y'] ?? 170 }}px;
            font-size: {{ $positionMap['customer_phone']['font_size'] ?? 10 }}px;
            font-weight: {{ $positionMap['customer_phone']['font_weight'] ?? 'normal' }};
        ">
            {{ $order->customer->phone ?? 'N/A' }}
        </div>
        @endif

        {{-- Customer Address --}}
        @if(isset($positionMap['customer_address']) && (!isset($elementToggles['customer_address']) || $elementToggles['customer_address']))
        <div class="positioned-element" style="
            left: {{ $positionMap['customer_address']['x'] ?? 50 }}px;
            top: {{ $positionMap['customer_address']['y'] ?? 190 }}px;
            font-size: {{ $positionMap['customer_address']['font_size'] ?? 10 }}px;
            font-weight: {{ $positionMap['customer_address']['font_weight'] ?? 'normal' }};
            line-height: 1.3;
            /* background removed for better positioning */

        ">
            Address: {{ $order->customer->address ?? 'N/A' }}
        </div>
        @endif

        {{-- Customer Email --}}
        @if(isset($positionMap['customer_email']) && (!isset($elementToggles['customer_email']) || $elementToggles['customer_email']))
        <div class="positioned-element" style="
            left: {{ $positionMap['customer_email']['x'] ?? 50 }}px;
            top: {{ $positionMap['customer_email']['y'] ?? 210 }}px;
            font-size: {{ $positionMap['customer_email']['font_size'] ?? 10 }}px;
            font-weight: {{ $positionMap['customer_email']['font_weight'] ?? 'normal' }};
            /* background removed for better positioning */

        ">
            Email: {{ $order->customer->email ?? 'N/A' }}
        </div>
        @endif

        {{-- Unified Items and Payment Table --}}
        @if(isset($positionMap['items_table']))
        <div class="positioned-element" style="
            left: {{ $positionMap['items_table']['x'] ?? 50 }}px;
            top: {{ $positionMap['items_table']['y'] ?? 240 }}px;
            width: 500px;
        ">
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 60%; text-align: left;">Item Details</th>
                        <th style="width: 10%; text-align: center;">Qty</th>
                        <th style="width: 15%; text-align: right;">Unit Price</th>
                        <th style="width: 15%; text-align: right;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->details as $item)
                    <tr>
                        <td style="text-align: left;">
                            <div class="item-name">{{ $item->product->name }}</div>
                            @if($item->serial_number || $item->warranty_years)
                                <div class="item-details">
                                    @if($item->serial_number)
                                        S/N: {{ $item->serial_number }}
                                    @endif
                                    @if($item->serial_number && $item->warranty_years)
                                        &nbsp;|&nbsp;
                                    @endif
                                    @if($item->warranty_years)
                                        Warranty: {{ $item->warranty_years }} {{ $item->warranty_years == 1 ? 'year' : 'years' }}
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td style="text-align: center; vertical-align: middle;">{{ $item->quantity }}</td>
                        <td style="text-align: right; vertical-align: middle;">LKR {{ number_format($item->unitcost / 100, 2) }}</td>
                        <td style="text-align: right; vertical-align: middle; font-weight: bold;">LKR {{ number_format($item->total / 100, 2) }}</td>
                    </tr>
                    @endforeach

                    {{-- Payment Summary Rows --}}
                    <tr style="border-top: 2px solid #000;">
                        <td colspan="3" style="text-align: right; padding: 8px; font-weight: bold; background: rgba(249, 249, 249, 0.95); font-size: 10px;">Subtotal:</td>
                        <td style="text-align: right; padding: 8px; font-weight: bold; background: rgba(249, 249, 249, 0.95); font-size: 10px;">LKR {{ number_format($order->sub_total / 100, 2) }}</td>
                    </tr>
                    @if($order->discount_amount > 0)
                    <tr>
                        <td colspan="3" style="text-align: right; padding: 6px; font-weight: bold; background: rgba(249, 249, 249, 0.95); font-size: 10px;">Discount:</td>
                        <td style="text-align: right; padding: 6px; font-weight: bold; color: #dc3545; background: rgba(249, 249, 249, 0.95); font-size: 10px;">-LKR {{ number_format($order->discount_amount / 100, 2) }}</td>
                    </tr>
                    @endif
                    @if($order->service_charges > 0)
                    <tr>
                        <td colspan="3" style="text-align: right; padding: 6px; font-weight: bold; background: rgba(249, 249, 249, 0.95); font-size: 10px;">Service Charges:</td>
                        <td style="text-align: right; padding: 6px; font-weight: bold; background: rgba(249, 249, 249, 0.95); font-size: 10px;">LKR {{ number_format($order->service_charges / 100, 2) }}</td>
                    </tr>
                    @endif
                    @if($order->vat > 0)
                    <tr>
                        <td colspan="3" style="text-align: right; padding: 6px; font-weight: bold; background: rgba(249, 249, 249, 0.95); font-size: 10px;">VAT/Tax:</td>
                        <td style="text-align: right; padding: 6px; font-weight: bold; background: rgba(249, 249, 249, 0.95); font-size: 10px;">LKR {{ number_format($order->vat / 100, 2) }}</td>
                    </tr>
                    @endif
                    <tr style="border-top: 3px solid #000;">
                        <td colspan="3" style="text-align: right; padding: 10px; font-weight: bold; font-size: 11px; border-top: 2px solid #000; border-bottom: 2px solid #000; background: rgba(233, 236, 239, 0.95);">GRAND TOTAL:</td>
                        <td style="text-align: right; padding: 10px; font-weight: bold; font-size: 11px; border-top: 2px solid #000; border-bottom: 2px solid #000; background: rgba(233, 236, 239, 0.95);">LKR {{ number_format($order->total / 100, 2) }}</td>
                    </tr>
                    @if($order->due > 0)
                    <tr>
                        <td colspan="3" style="text-align: right; padding: 8px; font-weight: bold; background: rgba(248, 215, 218, 0.95); font-size: 10px;">Amount Due:</td>
                        <td style="text-align: right; padding: 8px; font-weight: bold; color: #dc3545; background: rgba(248, 215, 218, 0.95); font-size: 10px;">LKR {{ number_format($order->due / 100, 2) }}</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        @endif

        {{-- Warranty Section --}}
        @if(isset($positionMap['warranty_section']))
        <div class="positioned-element warranty-text" style="
            left: {{ $positionMap['warranty_section']['x'] ?? 50 }}px;
            top: {{ $positionMap['warranty_section']['y'] ?? 600 }}px;
            font-size: {{ $positionMap['warranty_section']['font_size'] ?? 9 }}px;
            font-weight: {{ $positionMap['warranty_section']['font_weight'] ?? 'normal' }};
        ">
            <div class="warranty-title">WARRANTY TERMS & CONDITIONS</div>
            <div class="warranty-duration">(6 Months, 1Y=350 Days, 2Y=700 Days, 3Y=1050 Days, N/W=No Warranty)</div>
            <div class="warranty-clause">Defect parts will be repaired within 14 days. No warranty for chip burn, physical damage, corrosion, misuse, negligence or improper operations. Printers include demonstration cartridges and toners. Warranty void if refill or compatible cartridges are used.</div>
            <div class="warranty-clause">Replacement warranty for laptops - Repair warranty only. Goods sold once not returnable. Warranty covers monitors and laptop LCD/LED panels for over seven dead pixels.</div>
            <div class="warranty-clause" style="font-weight: bold;">Submit this invoice for warranty claims.</div>
            <div class="warranty-clause">Cheques should be crossed ACCOUNT PAYEE ONLY.</div>
            <div class="warranty-clause" style="font-weight: bold;">This is a computer-generated invoice and does not require a signature.</div>
        </div>
        @endif
    </div>
</body>
</html>
