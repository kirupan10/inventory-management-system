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

            // Calculate perfect 25px balanced margins based on canvas width
            $canvasWidth = 595; // A4 standard width
            $marginLeft = 25;
            $marginRight = 25;
            $totalMargins = $marginLeft + $marginRight; // 50px
            $perfectTableWidth = $canvasWidth - $totalMargins; // 595 - 50 = 545px

            $itemsAlignment = $letterheadConfig['items_alignment'] ?? [];
            $forceBalancedMargins = true; // Always use balanced margins
            $balancedStartX = $marginLeft; // 25px
            $balancedTableWidth = $perfectTableWidth; // 545px
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
            border: none;
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
                font-weight: {{ $positionMap['customer_name']['font_weight'] ?? 'normal' }};
            ">
                {{ $order->customer->name }}
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
                {{ $order->customer->phone ?? 'N/A' }}
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

            {{-- Unified Items and Payment Table --}}
            @if(isset($positionMap['items_table']))
            @php
                // Use calculated balanced margins (25px left, 545px width, 25px right)
                $startX = $balancedStartX; // Always 25px from left
                $tableWidth = $balancedTableWidth; // Always 545px width
                // This ensures: 25px + 545px + 25px = 595px (perfect balance)
            @endphp
            <div class="positioned-element" style="
                left: {{ $startX }}px;
                top: {{ $positionMap['items_table']['y'] ?? 240 }}px;
                font-size: {{ $positionMap['items_table']['font_size'] ?? 13 }}px;
            ">
                <table class="items-table" style="width: {{ $tableWidth }}px; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="width: {{ $tableWidth * 0.56 }}px; text-align: left; padding: 8px; background: #f5f5f5; border: 1px solid #ddd; font-size: 18px; font-weight: bold; color: #ff0000;">*** NEXORA TEST *** Item Details</th>
                            <th style="width: {{ $tableWidth * 0.12 }}px; text-align: center; padding: 8px; background: #f5f5f5; border: 1px solid #ddd;">Qty</th>
                            <th style="width: {{ $tableWidth * 0.16 }}px; text-align: right; padding: 8px; background: #f5f5f5; border: 1px solid #ddd;">Unit Price(LKR)</th>
                            <th style="width: {{ $tableWidth * 0.16 }}px; text-align: right; padding: 8px; background: #f5f5f5; border: 1px solid #ddd;">Total(LKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->details as $item)
                        <tr>
                            <td style="text-align: left; padding: 6px; vertical-align: top;">
                                <div style="font-weight: bold; margin-bottom: 2px;">{{ $item->product->name }}</div>
                                @if($item->serial_number || $item->warranty_years)
                                    <div style="font-size: 10px; color: #000;">
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
                            <td style="text-align: center; padding: 6px; vertical-align: middle;">{{ $item->quantity }}</td>
                            <td style="text-align: right; padding: 6px; vertical-align: middle;">{{ number_format($item->unitcost, 2, '.', ',') }}</td>
                            <td style="text-align: right; padding: 6px; vertical-align: middle; font-weight: bold;">{{ number_format($item->total, 2, '.', ',') }}</td>
                        </tr>
                        @endforeach

                        {{-- Payment Summary Rows --}}
                        <tr>
                            <td colspan="3" style="text-align: right; padding: 8px; font-weight: bold;">Subtotal:</td>
                            <td style="text-align: right; padding: 8px; font-weight: bold;">LKR {{ number_format($order->sub_total, 2, '.', ',') }}</td>
                        </tr>
                        @if($order->discount_amount > 0)
                        <tr>
                            <td colspan="3" style="text-align: right; padding: 8px; font-weight: bold;">Discount:</td>
                            <td style="text-align: right; padding: 8px; font-weight: bold; color: #dc3545;">-LKR {{ number_format($order->discount_amount, 2, '.', ',') }}</td>
                        </tr>
                        @endif
                        @if($order->service_charges > 0)
                        <tr>
                            <td colspan="3" style="text-align: right; padding: 8px; font-weight: bold;">Service Charges:</td>
                            <td style="text-align: right; padding: 8px; font-weight: bold;">LKR {{ number_format($order->service_charges, 2, '.', ',') }}</td>
                        </tr>
                        @endif
                        @if($order->vat > 0)
                        <tr>
                            <td colspan="3" style="text-align: right; padding: 8px; font-weight: bold;">VAT/Tax:</td>
                            <td style="text-align: right; padding: 8px; font-weight: bold;">LKR {{ number_format($order->vat, 2, '.', ',') }}</td>
                        </tr>
                        @endif
                        <tr style="border-top: 3px solid #000;">
                            <td colspan="3" style="text-align: right; padding: 12px; font-weight: bold; font-size: 16px; border-top: 2px solid #000; border-bottom: 2px solid #000; background: #e9ecef;">GRAND TOTAL:</td>
                            <td style="text-align: right; padding: 12px; font-weight: bold; font-size: 16px; border-top: 2px solid #000; border-bottom: 2px solid #000; background: #e9ecef;">LKR {{ number_format($order->total, 2, '.', ',') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @endif

            {{-- Payment Information Section --}}
            @if(isset($positionMap['payment_info']))
            <div class="positioned-element" style="
                left: {{ $positionMap['payment_info']['x'] ?? 50 }}px;
                top: {{ $positionMap['payment_info']['y'] ?? 500 }}px;
                font-size: {{ $positionMap['payment_info']['font_size'] ?? 12 }}px;
                font-weight: {{ $positionMap['payment_info']['font_weight'] ?? 'normal' }};
            ">
                <div style="font-weight: bold; margin-bottom: 8px; border-bottom: 1px solid #000; padding-bottom: 3px;">PAYMENT INFORMATION</div>
                <div style="margin-bottom: 3px;"><strong>Payment Method:</strong> {{ $order->payment_type ?? 'N/A' }}</div>
                <div style="margin-bottom: 3px;"><strong>Order Status:</strong> {{ $order->order_status->value ?? 'N/A' }}</div>
                <div style="margin-bottom: 3px;"><strong>Total Products:</strong> {{ $order->total_products ?? 0 }}</div>
                @if($order->pay > 0)
                <div style="margin-bottom: 3px;"><strong>Amount Paid:</strong> LKR {{ number_format($order->pay, 2, '.', ',') }}</div>
                @endif
                @if($order->due > 0)
                <div style="margin-bottom: 3px; color: #dc3545;"><strong>Amount Due:</strong> LKR {{ number_format($order->due, 2, '.', ',') }}</div>
                @endif
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
                <!-- NEXORA Brand Header -->
                <div style="text-align: center; margin-bottom: 20px; padding: 15px; background: #ff0000; color: white; font-size: 24px; font-weight: bold; border-radius: 8px;">
                    *** NEXORA INVENTORY SYSTEM ***
                </div>
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

                <!-- Customer Information -->
                <div style="margin-bottom: 25px;">
                    <div style="font-size: 14px; line-height: 1.6;">
                        {{ $order->customer->name }}<br>
                        {{ $order->customer->phone ?? 'N/A' }}
                        @if($order->customer->address)
                            <br>{{ $order->customer->address }}
                        @endif
                        @if($order->customer->email)
                            <br>{{ $order->customer->email }}
                        @endif
                    </div>
                </div>

                <!-- Unified Items and Payment Table - Perfectly Balanced 25px Margins ({{ $perfectTableWidth }}px) -->
                <div style="margin-left: {{ $marginLeft }}px; margin-right: {{ $marginRight }}px;">
                <table class="items-table" style="width: {{ $perfectTableWidth }}px; margin-bottom: 20px; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="width: 305px; text-align: left; padding: 10px; background: #f5f5f5; border: 1px solid #ddd; font-size: 16px; font-weight: bold; color: #ff0000;">*** NEXORA *** Item Details</th>
                            <th style="width: 65px; text-align: center; padding: 10px; background: #f5f5f5; border: 1px solid #ddd; font-size: 13px;">Qty</th>
                            <th style="width: 87px; text-align: right; padding: 10px; background: #f5f5f5; border: 1px solid #ddd; font-size: 13px;">Unit Price(LKR)</th>
                            <th style="width: 88px; text-align: right; padding: 10px; background: #f5f5f5; border: 1px solid #ddd; font-size: 13px;">Total(LKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->details as $item)
                        <tr>
                            <td style="text-align: left; padding: 8px; vertical-align: top;">
                                <div style="font-weight: bold; margin-bottom: 3px;">{{ $item->product->name }}</div>
                                @if($item->serial_number || $item->warranty_years)
                                    <div style="font-size: 10px; color: #000;">
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
                            <td style="text-align: center; padding: 8px; vertical-align: middle;">{{ $item->quantity }}</td>
                            <td style="text-align: right; padding: 8px; vertical-align: middle;">{{ number_format($item->unitcost, 2, '.', ',') }}</td>
                            <td style="text-align: right; padding: 8px; vertical-align: middle; font-weight: bold;">{{ number_format($item->total, 2, '.', ',') }}</td>
                        </tr>
                        @endforeach

                        {{-- Payment Summary Rows --}}
                        <tr>
                            <td colspan="3" style="text-align: right; padding: 10px; font-weight: bold; font-size: 13px;">Subtotal:</td>
                            <td style="text-align: right; padding: 10px; font-weight: bold; font-size: 13px;">LKR {{ number_format($order->sub_total, 2, '.', ',') }}</td>
                        </tr>
                        @if($order->discount_amount > 0)
                        <tr>
                            <td colspan="3" style="text-align: right; padding: 8px; font-weight: bold; font-size: 12px;">Discount:</td>
                            <td style="text-align: right; padding: 8px; font-weight: bold; color: #dc3545; font-size: 12px;">-LKR {{ number_format($order->discount_amount, 2, '.', ',') }}</td>
                        </tr>
                        @endif
                        @if($order->service_charges > 0)
                        <tr>
                            <td colspan="3" style="text-align: right; padding: 8px; font-weight: bold; font-size: 12px;">Service Charges:</td>
                            <td style="text-align: right; padding: 8px; font-weight: bold; font-size: 12px;">LKR {{ number_format($order->service_charges, 2, '.', ',') }}</td>
                        </tr>
                        @endif
                        @if($order->vat > 0)
                        <tr>
                            <td colspan="3" style="text-align: right; padding: 8px; font-weight: bold; font-size: 12px;">VAT/Tax:</td>
                            <td style="text-align: right; padding: 8px; font-weight: bold; font-size: 12px;">LKR {{ number_format($order->vat, 2, '.', ',') }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td colspan="3" style="text-align: right; padding: 12px; font-weight: bold; font-size: 16px;">GRAND TOTAL:</td>
                            <td style="text-align: right; padding: 12px; font-weight: bold; font-size: 16px;">LKR {{ number_format($order->total, 2, '.', ',') }}</td>
                        </tr>
                    </tbody>
                </table>
                </div>

                <!-- Payment Information Section -->
                <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px;">
                    <div style="font-weight: bold; font-size: 14px; margin-bottom: 10px; color: #2d3748;">PAYMENT INFORMATION</div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <div style="flex: 1;">
                            <div style="margin-bottom: 5px;"><strong>Payment Method:</strong> {{ $order->payment_type ?? 'N/A' }}</div>
                            <div style="margin-bottom: 5px;"><strong>Order Status:</strong> {{ $order->order_status->value ?? 'N/A' }}</div>
                            <div><strong>Total Products:</strong> {{ $order->total_products ?? 0 }}</div>
                        </div>
                        <div style="flex: 1; text-align: right;">
                            @if($order->pay > 0)
                            <div style="margin-bottom: 5px;"><strong>Amount Paid:</strong> LKR {{ number_format($order->pay, 2, '.', ',') }}</div>
                            @endif
                            @if($order->due > 0)
                            <div style="color: #dc3545;"><strong>Amount Due:</strong> LKR {{ number_format($order->due, 2, '.', ',') }}</div>
                            @endif
                        </div>
                    </div>
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
