<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->invoice_no }}</title>
    <style>
        @php
            $letterheadConfig = json_decode(file_get_contents(storage_path('app/letterhead_config.json')), true) ?? [];
            $hasLetterhead = isset($letterheadConfig['letterhead_file']) && $letterheadConfig['letterhead_file'];
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
            font-size: 14px;
            line-height: 1.4;
            color: #000;
            background: white;
        }

        .page {
            width: 595px;
            height: 842px;
            position: relative;
            margin: 0 auto;
            @if($hasLetterhead)
            background-image: url('{{ public_path('letterheads/' . $letterheadConfig['letterhead_file']) }}');
            background-size: 595px 842px;
            background-repeat: no-repeat;
            background-position: top left;
            @else
            background: white;
            @endif
        }

        .positioned-element {
            position: absolute;
            font-family: Arial, sans-serif;
        }

        .items-table {
            border-collapse: collapse;
            width: 500px;
        }

        .items-table th {
            background: #f5f5f5;
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            font-size: 13px;
        }

        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 12px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-row {
            margin-bottom: 5px;
        }

        .total-final {
            font-weight: bold;
            font-size: 16px;
            border-top: 2px solid #000;
            padding-top: 5px;
            margin-top: 10px;
        }

        .warranty-text {
            font-size: 11px;
            line-height: 1.4;
            width: 500px;
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
        @if($hasLetterhead)
            {{-- With Custom Letterhead - Use Positioned Elements --}}

            {{-- Company Name --}}
            @if(isset($positionMap['company_name']))
            <div class="positioned-element" style="
                left: {{ $positionMap['company_name']['x'] ?? 50 }}px;
                top: {{ $positionMap['company_name']['y'] ?? 50 }}px;
                font-size: {{ $positionMap['company_name']['font_size'] ?? 18 }}px;
                font-weight: {{ $positionMap['company_name']['font_weight'] ?? 'bold' }};
            ">
                AURA PC FACTORY (PVT) LTD
            </div>
            @endif

            {{-- Company Address --}}
            @if(isset($positionMap['company_address']))
            <div class="positioned-element" style="
                left: {{ $positionMap['company_address']['x'] ?? 50 }}px;
                top: {{ $positionMap['company_address']['y'] ?? 80 }}px;
                font-size: {{ $positionMap['company_address']['font_size'] ?? 14 }}px;
                font-weight: {{ $positionMap['company_address']['font_weight'] ?? 'normal' }};
                line-height: 1.3;
            ">
                KALANCHIYAM THODDAM,<br>
                KARAVEDDY EAST, KARAVEDDY,<br>
                NORTHERN PROVINCE 40,000<br>
                SRI LANKA
            </div>
            @endif

            {{-- Company Contact --}}
            @if(isset($positionMap['company_contact']))
            <div class="positioned-element" style="
                left: {{ $positionMap['company_contact']['x'] ?? 50 }}px;
                top: {{ $positionMap['company_contact']['y'] ?? 110 }}px;
                font-size: {{ $positionMap['company_contact']['font_size'] ?? 12 }}px;
                font-weight: {{ $positionMap['company_contact']['font_weight'] ?? 'normal' }};
            ">
                📧 AuraPCFactory@gmail.com &nbsp;&nbsp; 📞 +94 77 022 1046
            </div>
            @endif

            {{-- Invoice Number --}}
            @if(isset($positionMap['invoice_no']))
            <div class="positioned-element" style="
                left: {{ $positionMap['invoice_no']['x'] ?? 400 }}px;
                top: {{ $positionMap['invoice_no']['y'] ?? 50 }}px;
                font-size: {{ $positionMap['invoice_no']['font_size'] ?? 14 }}px;
                font-weight: {{ $positionMap['invoice_no']['font_weight'] ?? 'bold' }};
            ">
                INVOICE: {{ $order->invoice_no }}
            </div>
            @endif

            {{-- Invoice Date --}}
            @if(isset($positionMap['invoice_date']))
            <div class="positioned-element" style="
                left: {{ $positionMap['invoice_date']['x'] ?? 400 }}px;
                top: {{ $positionMap['invoice_date']['y'] ?? 70 }}px;
                font-size: {{ $positionMap['invoice_date']['font_size'] ?? 14 }}px;
                font-weight: {{ $positionMap['invoice_date']['font_weight'] ?? 'normal' }};
            ">
                DATE: {{ $order->order_date->format('d/m/Y') }}
            </div>
            @endif

            {{-- Customer Name --}}
            @if(isset($positionMap['customer_name']))
            <div class="positioned-element" style="
                left: {{ $positionMap['customer_name']['x'] ?? 50 }}px;
                top: {{ $positionMap['customer_name']['y'] ?? 150 }}px;
                font-size: {{ $positionMap['customer_name']['font_size'] ?? 14 }}px;
                font-weight: {{ $positionMap['customer_name']['font_weight'] ?? 'bold' }};
            ">
                Customer: {{ $order->customer->name }}
            </div>
            @endif

            {{-- Customer Phone --}}
            @if(isset($positionMap['customer_phone']))
            <div class="positioned-element" style="
                left: {{ $positionMap['customer_phone']['x'] ?? 50 }}px;
                top: {{ $positionMap['customer_phone']['y'] ?? 170 }}px;
                font-size: {{ $positionMap['customer_phone']['font_size'] ?? 13 }}px;
                font-weight: {{ $positionMap['customer_phone']['font_weight'] ?? 'normal' }};
            ">
                Phone: {{ $order->customer->phone ?? 'N/A' }}
            </div>
            @endif

            {{-- Customer Address --}}
            @if(isset($positionMap['customer_address']))
            <div class="positioned-element" style="
                left: {{ $positionMap['customer_address']['x'] ?? 50 }}px;
                top: {{ $positionMap['customer_address']['y'] ?? 190 }}px;
                font-size: {{ $positionMap['customer_address']['font_size'] ?? 13 }}px;
                font-weight: {{ $positionMap['customer_address']['font_weight'] ?? 'normal' }};
                line-height: 1.3;
            ">
                Address: {{ $order->customer->address ?? 'N/A' }}
            </div>
            @endif

            {{-- Customer Email --}}
            @if(isset($positionMap['customer_email']))
            <div class="positioned-element" style="
                left: {{ $positionMap['customer_email']['x'] ?? 50 }}px;
                top: {{ $positionMap['customer_email']['y'] ?? 210 }}px;
                font-size: {{ $positionMap['customer_email']['font_size'] ?? 13 }}px;
                font-weight: {{ $positionMap['customer_email']['font_weight'] ?? 'normal' }};
            ">
                Email: {{ $order->customer->email ?? 'N/A' }}
            </div>
            @endif

            {{-- Items Table --}}
            @if(isset($positionMap['items_table']))
            @php
                $itemsAlignment = $letterheadConfig['items_alignment'] ?? [];
                $startX = $itemsAlignment['start_x'] ?? ($positionMap['items_table']['x'] ?? 40);
                $tableWidth = $itemsAlignment['width'] ?? 515;
            @endphp
            <div class="positioned-element" style="
                left: {{ $startX }}px;
                top: {{ $positionMap['items_table']['y'] ?? 240 }}px;
                font-size: {{ $positionMap['items_table']['font_size'] ?? 13 }}px;
            ">
                <table class="items-table" style="width: {{ $tableWidth }}px;">
                    <thead>
                        <tr>
                            <th style="width: {{ $tableWidth * 0.56 }}px; text-align: left; padding: 8px;">Item Details</th>
                            <th style="width: {{ $tableWidth * 0.12 }}px; text-align: center; padding: 8px;">Qty</th>
                            <th style="width: {{ $tableWidth * 0.16 }}px; text-align: right; padding: 8px;">Unit Price</th>
                            <th style="width: {{ $tableWidth * 0.16 }}px; text-align: right; padding: 8px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->details as $item)
                        <tr>
                            <td style="text-align: left; padding: 6px; vertical-align: top;">
                                <div style="font-weight: bold; margin-bottom: 2px;">{{ $item->product->name }}</div>
                                @if($item->serial_number)
                                    <div style="font-size: 11px; color: #000; margin-bottom: 1px;">S/N: {{ $item->serial_number }}</div>
                                @endif
                                @if($item->warranty_years)
                                    <div style="font-size: 9px; color: #000;">Warranty: {{ $item->warranty_years }} {{ $item->warranty_years == 1 ? 'year' : 'years' }}</div>
                                @endif
                            </td>
                            <td style="text-align: center; padding: 6px; vertical-align: middle;">{{ $item->quantity }}</td>
                            <td style="text-align: right; padding: 6px; vertical-align: middle;">LKR {{ number_format($item->unitcost / 100, 2) }}</td>
                            <td style="text-align: right; padding: 6px; vertical-align: middle; font-weight: bold;">LKR {{ number_format($item->total / 100, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            {{-- Total Section --}}
            @if(isset($positionMap['total_section']))
            <div class="positioned-element" style="
                left: {{ $positionMap['total_section']['x'] ?? 350 }}px;
                top: {{ $positionMap['total_section']['y'] ?? 520 }}px;
                font-size: {{ $positionMap['total_section']['font_size'] ?? 14 }}px;
                width: 200px;
            ">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="text-align: left; padding: 3px 0; border-bottom: 1px solid #eee;">Subtotal:</td>
                        <td style="text-align: right; padding: 3px 0; border-bottom: 1px solid #eee; font-weight: bold;">LKR {{ number_format($order->sub_total / 100, 2) }}</td>
                    </tr>
                    @if($order->discount_amount > 0)
                    <tr>
                        <td style="text-align: left; padding: 3px 0; border-bottom: 1px solid #eee;">Discount:</td>
                        <td style="text-align: right; padding: 3px 0; border-bottom: 1px solid #eee; font-weight: bold; color: #dc3545;">-LKR {{ number_format($order->discount_amount / 100, 2) }}</td>
                    </tr>
                    @endif
                    @if($order->service_charges > 0)
                    <tr>
                        <td style="text-align: left; padding: 3px 0; border-bottom: 1px solid #eee;">Service Charges:</td>
                        <td style="text-align: right; padding: 3px 0; border-bottom: 1px solid #eee; font-weight: bold;">LKR {{ number_format($order->service_charges / 100, 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="text-align: left; padding: 8px 0; border-top: 2px solid #000; font-weight: bold; font-size: 14px;">GRAND TOTAL:</td>
                        <td style="text-align: right; padding: 8px 0; border-top: 2px solid #000; font-weight: bold; font-size: 14px;">LKR {{ number_format($order->total / 100, 2) }}</td>
                    </tr>
                </table>
            </div>
            @endif

            {{-- Warranty Section --}}
            @if(isset($positionMap['warranty_section']))
            <div class="positioned-element warranty-text" style="
                left: {{ $positionMap['warranty_section']['x'] ?? 50 }}px;
                top: {{ $positionMap['warranty_section']['y'] ?? 600 }}px;
                font-size: {{ $positionMap['warranty_section']['font_size'] ?? 11 }}px;
                font-weight: {{ $positionMap['warranty_section']['font_weight'] ?? 'normal' }};
            ">
                <div style="font-weight: bold; margin-bottom: 5px;">WARRANTY TERMS & CONDITION</div>
                <div style="font-weight: bold; margin-bottom: 5px;">(6Month Days , 1Y=350 Days , 2Y=700 Days , 3Y=1050 Days , N/W= No Warranty)</div>
                <div style="margin-bottom: 3px;">Defect part will be repaired within 14 days time period. No warranty for chip burnt,physical damage, corroded , misuse,negligence or improper operations.Printers are included with demonstration cartridges and toners. warranty void if refill or compatible cartridges are used. Replacement warranty for laptop , Only Repair Warranty,Goods sold once not returnable.Warranty Covers for monitor and Laptop LCD or LED Panel for over seven Death Pixels.</div>
                <div style="font-weight: bold; margin-bottom: 3px;">Submit this invoice for warranty Claim.</div>
                <div style="margin-bottom: 3px;">Cheques to the drawn in favour of "AURA PC FACTORY (PVT) LTD" and crossed ACCOUNT PAYEE ONLY</div>
                <div style="font-weight: bold;">This is a computer-generated invoice and does not require a signature.</div>
            </div>
            @endif

        @else
            {{-- Fallback without letterhead - Original Layout --}}
            <div style="padding: 20px;">
                <!-- Header Section -->
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 30px; border-bottom: 2px solid #000; padding-bottom: 20px;">
                    <div style="flex: 1;">
                        <div style="font-size: 18px; font-weight: bold; margin-bottom: 5px;">AURA PC FACTORY (PVT) LTD</div>
                        <div style="font-size: 14px; line-height: 1.3; margin-bottom: 8px;">
                            KALANCHIYAM THODDAM,<br>
                            KARAVEDDY EAST, KARAVEDDY,<br>
                            NORTHERN PROVINCE 40,000<br>
                            SRI LANKA
                        </div>
                        <div style="font-size: 12px;">
                            📧 AuraPCFactory@gmail.com &nbsp;&nbsp; 📞 +94 77 022 1046
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 14px; font-weight: bold;">INVOICE: {{ $order->invoice_no }}</div>
                        <div style="font-size: 14px;">DATE: {{ $order->order_date->format('d/m/Y') }}</div>
                    </div>
                </div>

                <!-- Customer Details -->
                <div style="margin-bottom: 25px;">
                    <div style="font-weight: bold; font-size: 14px; margin-bottom: 8px;">Customer Details:</div>
                    <div style="font-size: 13px; line-height: 1.5;">
                        <strong>Customer:</strong> {{ $order->customer->name }}<br>
                        <strong>Phone:</strong> {{ $order->customer->phone ?? 'N/A' }}<br>
                        <strong>Address:</strong> {{ $order->customer->address ?? 'N/A' }}<br>
                        <strong>Email:</strong> {{ $order->customer->email ?? 'N/A' }}
                    </div>
                </div>

                <!-- Items Table -->
                <table class="items-table" style="width: 100%; margin-bottom: 20px; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="width: 50%; text-align: left; padding: 10px; background: #f5f5f5; border: 1px solid #ddd; font-size: 13px;">Item Details</th>
                            <th style="width: 15%; text-align: center; padding: 10px; background: #f5f5f5; border: 1px solid #ddd; font-size: 13px;">Qty</th>
                            <th style="width: 20%; text-align: right; padding: 10px; background: #f5f5f5; border: 1px solid #ddd; font-size: 13px;">Unit Price</th>
                            <th style="width: 15%; text-align: right; padding: 10px; background: #f5f5f5; border: 1px solid #ddd; font-size: 13px;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->details as $item)
                        <tr>
                            <td style="text-align: left; padding: 8px; border: 1px solid #ddd; vertical-align: top;">
                                <div style="font-weight: bold; margin-bottom: 3px;">{{ $item->product->name }}</div>
                                @if($item->serial_number)
                                    <div style="font-size: 11px; color: #000; margin-bottom: 2px;">S/N: {{ $item->serial_number }}</div>
                                @endif
                                @if($item->warranty_years)
                                    <div style="font-size: 9px; color: #000;">Warranty: {{ $item->warranty_years }} {{ $item->warranty_years == 1 ? 'year' : 'years' }}</div>
                                @endif
                            </td>
                            <td style="text-align: center; padding: 8px; border: 1px solid #ddd; vertical-align: middle;">{{ $item->quantity }}</td>
                            <td style="text-align: right; padding: 8px; border: 1px solid #ddd; vertical-align: middle;">LKR {{ number_format($item->unitcost / 100, 2) }}</td>
                            <td style="text-align: right; padding: 8px; border: 1px solid #ddd; vertical-align: middle; font-weight: bold;">LKR {{ number_format($item->total / 100, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Totals -->
                <div style="margin-left: auto; width: 300px; margin-top: 15px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="text-align: left; padding: 5px 0; border-bottom: 1px solid #eee; font-size: 12px;">Subtotal:</td>
                            <td style="text-align: right; padding: 5px 0; border-bottom: 1px solid #eee; font-weight: bold; font-size: 12px;">LKR {{ number_format($order->sub_total / 100, 2) }}</td>
                        </tr>
                        @if($order->discount_amount > 0)
                        <tr>
                            <td style="text-align: left; padding: 5px 0; border-bottom: 1px solid #eee; font-size: 12px;">Discount:</td>
                            <td style="text-align: right; padding: 5px 0; border-bottom: 1px solid #eee; font-weight: bold; font-size: 12px; color: #dc3545;">-LKR {{ number_format($order->discount_amount / 100, 2) }}</td>
                        </tr>
                        @endif
                        @if($order->service_charges > 0)
                        <tr>
                            <td style="text-align: left; padding: 5px 0; border-bottom: 1px solid #eee; font-size: 12px;">Service Charges:</td>
                            <td style="text-align: right; padding: 5px 0; border-bottom: 1px solid #eee; font-weight: bold; font-size: 12px;">LKR {{ number_format($order->service_charges / 100, 2) }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td style="text-align: left; padding: 10px 0; border-top: 2px solid #000; font-weight: bold; font-size: 14px;">GRAND TOTAL:</td>
                            <td style="text-align: right; padding: 10px 0; border-top: 2px solid #000; font-weight: bold; font-size: 14px;">LKR {{ number_format($order->total / 100, 2) }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Warranty Section -->
                <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
                    <div style="font-weight: bold; font-size: 12px; margin-bottom: 10px;">WARRANTY TERMS & CONDITION</div>
                    <div style="font-size: 9px; line-height: 1.4; margin-bottom: 8px;">
                        <strong>(6Month Days , 1Y=350 Days , 2Y=700 Days , 3Y=1050 Days , N/W= No Warranty)</strong>
                    </div>
                    <div style="font-size: 9px; line-height: 1.4; margin-bottom: 8px;">
                        Defect part will be repaired within 14 days time period. No warranty for chip burnt,physical damage, corroded , misuse,negligence or improper operations.Printers are included with demonstration cartridges and toners. warranty void if refill or compatible cartridges are used. Replacement warranty for laptop , Only Repair Warranty,Goods sold once not returnable.Warranty Covers for monitor and Laptop LCD or LED Panel for over seven Death Pixels.
                    </div>
                    <div style="font-size: 9px; line-height: 1.4; margin-bottom: 8px;">
                        <strong>Submit this invoice for warranty Claim.</strong>
                    </div>
                    <div style="font-size: 9px; line-height: 1.4; margin-bottom: 8px;">
                        Cheques to the drawn in favour of "AURA PC FACTORY (PVT) LTD" and crossed ACCOUNT PAYEE ONLY
                    </div>
                    <div style="font-size: 9px; line-height: 1.4;">
                        <strong>This is a computer-generated invoice and does not require a signature.</strong>
                    </div>
                </div>
            </div>
        @endif
    </div>
</body>
</html>
