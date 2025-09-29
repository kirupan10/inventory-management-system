@extends('layouts.tabler')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <x-alert/>

        @if($errors->any())
            <div class="alert alert-danger">
                <h6>Please fix the following errors:</h6>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- POS Header -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="page-title">
                        {{ __('Point of Sale') }}
                    </h1>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M9 14l-4 -4l4 -4"/>
                            <path d="M5 10h11a4 4 0 1 1 0 8h-1"/>
                        </svg>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <form id="order-form" action="{{ route('orders.store') }}" method="POST">
            @csrf
            <!-- Hidden date field with current date -->
            <input name="date" id="date" type="hidden" value="{{ now()->format('Y-m-d') }}">
            <!-- Hidden reference field with default value -->
            <input name="reference" type="hidden" value="ORDR">

            <div class="row" style="min-height: calc(100vh - 200px);">
                <!-- LEFT SECTION: Product Search (60%) -->
                <div class="col-lg-7 col-xl-7">
                    <div class="card h-100">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h3 class="card-title mb-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M4 7v-1a2 2 0 0 1 2 -2h2"/>
                                            <path d="M4 17v1a2 2 0 0 0 2 2h2"/>
                                            <path d="M16 4h2a2 2 0 0 1 2 2v1"/>
                                            <path d="M16 20h2a2 2 0 0 0 2 -2v-1"/>
                                            <path d="M8 11l0 .01"/>
                                            <path d="M12 11l0 .01"/>
                                            <path d="M16 11l0 .01"/>
                                            <path d="M8 15l0 .01"/>
                                            <path d="M12 15l0 .01"/>
                                            <path d="M16 15l0 .01"/>
                                        </svg>
                                        Product Search
                                    </h3>
                                </div>
                                <div class="text-muted">
                                    <small id="current-order-date">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z"/>
                                            <path d="M16 3v4"/>
                                            <path d="M8 3v4"/>
                                            <path d="M4 11h16"/>
                                            <path d="M11 15h1"/>
                                            <path d="M12 15v3"/>
                                        </svg>
                                        Order Date: <span id="order-date-display">{{ now()->format('d/m/Y, H:i:s') }}</span>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <!-- Product Search Section -->
                            <div class="mb-4">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <input type="text" class="form-control form-control-lg" placeholder="Search products by name, SKU, or scan barcode..." autocomplete="off">
                                    </div>
                                </div>
                            </div>

                            <!-- Products Grid -->
                            <div class="flex-1" style="overflow-y: auto; overflow-x: hidden; max-height: 500px;">
                                <h4 class="mb-3">Products ({{ $products->count() }} items)</h4>
                                <div class="row g-2" style="margin: 0;">
                                    @foreach($products as $product)
                                    <div class="col-md-6 col-lg-4" style="padding: 0.25rem;">
                                        <div class="card product-card {{ $product->quantity <= 0 ? 'out-of-stock' : 'cursor-pointer hover-shadow' }}" 
                                             data-product-id="{{ $product->id }}" 
                                             data-stock="{{ $product->quantity }}"
                                             style="border: {{ $product->quantity <= 0 ? '2px solid #ef4444' : '1px solid #e9ecef' }}; 
                                                    border-radius: 8px; 
                                                    min-height: 50px; 
                                                    width: 100%; 
                                                    {{ $product->quantity <= 0 ? 'opacity: 0.6; cursor: not-allowed;' : '' }}">
                                            <div class="card-body p-3">
                                                <div class="text-start">
                                                    <div class="fw-bold {{ $product->quantity <= 0 ? 'text-muted' : 'text-dark' }} mb-1" style="font-size: 14px; line-height: 1.2; word-wrap: break-word;">
                                                        {{ Str::limit($product->name, 25) }}
                                                        @if($product->quantity <= 0)
                                                            <small class="text-danger ms-1">(Out of Stock)</small>
                                                        @endif
                                                    </div>
                                                    <div class="text-muted small mb-2" style="font-size: 11px;">
                                                        PRD-{{ str_pad($product->id, 6, '0', STR_PAD_LEFT) }}
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center" style="flex-wrap: wrap;">
                                                    <span class="fw-bold {{ $product->quantity <= 0 ? 'text-muted' : 'text-success' }}" style="font-size: 14px; white-space: nowrap;">LKR {{ number_format($product->selling_price, 0) }}</span>
                                                    @if($product->quantity > 0)
                                                        <span class="badge rounded-pill" style="background-color: #3b82f6; color: white; font-size: 10px; padding: 4px 8px; white-space: nowrap;">Stock: {{ $product->quantity }}</span>
                                                    @else
                                                        <span class="badge rounded-pill" style="background-color: #ef4444; color: white; font-size: 10px; padding: 4px 8px; white-space: nowrap;">Out of Stock</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT SECTION: Cart & Customer (40%) -->
                <div class="col-lg-5 col-xl-5">
                    <div class="card h-100">
                        <div class="card-header">
                            <h3 class="card-title" id="cart-title">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                                    <path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                                    <path d="M17 17h-11v-14h-2"/>
                                    <path d="M6 5l14 1l-1 7h-13"/>
                                </svg>
                                Cart (<span id="cart-count">0</span>)
                            </h3>
                            <div class="card-actions">
                                <button type="button" class="btn btn-outline-secondary btn-sm">Clear</button>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column p-3">
                            <!-- Customer Selection -->
                            <div class="mb-4">
                                <div class="input-group">
                                    <select id="customer_id" name="customer_id"
                                            class="form-select @error('customer_id') is-invalid @enderror">
                                        <option value="">Walk-In Customer</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>
                                                {{ $customer->name }}@if($customer->phone) - {{ $customer->phone }}@endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <a href="{{ route('customers.create') }}" class="btn btn-primary btn-icon" title="Add New Customer" target="_blank">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                            <path d="M12 5l0 14"/>
                                            <path d="M5 12l14 0"/>
                                        </svg>
                                    </a>
                                </div>
                                @error('customer_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Cart Items -->
                            <div class="mb-4" style="height: auto; min-height: 350px;">
                                <div class="text-center py-5" id="empty-cart">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-lg text-muted mb-3" width="48" height="48" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                                        <path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                                        <path d="M17 17h-11v-14h-2"/>
                                        <path d="M6 5l14 1l-1 7h-13"/>
                                    </svg>
                                    <p class="text-muted">Cart is empty</p>
                                </div>
                                <!-- Cart items will be dynamically added here -->
                                <div id="cart-items" style="display: none; max-height: 350px; overflow-y: auto; overflow-x: hidden; padding-right: 5px;">
                                    <!-- Dynamic cart items -->
                                </div>
                            </div>

                            <!-- Discount and Service Charges (shown only when cart has items) -->
                            <div id="cart-adjustments" class="mb-3" style="display: none;">
                                <div class="row g-2 mb-2">
                                    <div class="col">
                                        <label class="form-label small">Discount (LKR)</label>
                                        <input type="number" id="discount-amount" class="form-control form-control-sm" step="0.01" min="0" value="0" placeholder="0.00" name="discount_amount">
                                    </div>
                                    <div class="col">
                                        <label class="form-label small">Service Charges (LKR)</label>
                                        <input type="number" id="service-charges" class="form-control form-control-sm" step="0.01" min="0" value="0" placeholder="0.00" name="service_charges">
                                    </div>
                                </div>
                            </div>

                            <!-- Order Summary -->
                            <div class="border-top pt-3">
                                <div class="row mb-2">
                                    <div class="col">Subtotal:</div>
                                    <div class="col-auto fw-bold" id="subtotal-amount">LKR 0.00</div>
                                </div>
                                <div class="row mb-2" id="discount-row" style="display: none;">
                                    <div class="col text-danger">Discount:</div>
                                    <div class="col-auto fw-bold text-danger" id="discount-display">-LKR 0.00</div>
                                </div>
                                <div class="row mb-2" id="service-row" style="display: none;">
                                    <div class="col text-info">Service Charges:</div>
                                    <div class="col-auto fw-bold text-info" id="service-display">+LKR 0.00</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col"><strong>Total:</strong></div>
                                    <div class="col-auto"><strong id="total-amount" class="h4 text-primary">LKR 0.00</strong></div>
                                </div>

                                <!-- Payment Method -->
                                <div class="mb-3">
                                    <label class="form-label">Payment Method</label>
                                    <select class="form-select" name="payment_type">
                                        <option value="Cash">Cash</option>
                                        <option value="Card">Card</option>
                                        <option value="Bank Transfer">Bank Transfer</option>
                                    </select>
                                </div>

                                <script>
                                    // Show/hide amount received field based on payment method
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const paymentTypeSelect = document.querySelector('select[name="payment_type"]');
                                        const paymentAmountSection = document.getElementById('payment-amount-section');
                                        const paymentAmountInput = document.getElementById('payment-amount-input');
                                        const completeBtn = document.getElementById('complete-payment-btn');

                                        // Create feedback element for balance/shortage
                                        let balanceFeedback = document.getElementById('balance-feedback');
                                        if (!balanceFeedback) {
                                            balanceFeedback = document.createElement('div');
                                            balanceFeedback.id = 'balance-feedback';
                                            balanceFeedback.style.marginTop = '0.5rem';
                                            paymentAmountInput && paymentAmountInput.parentNode.appendChild(balanceFeedback);
                                        }

                                        function sanitizeAmount(text) {
                                            if (!text) return 0;
                                            return parseFloat(String(text).replace(/[^0-9.]/g, '')) || 0;
                                        }

                                        function updateBalanceFeedback() {
                                            if (!paymentAmountInput) return;
                                            const enteredAmount = parseFloat(paymentAmountInput.value) || 0;
                                            const totalText = document.getElementById('total-amount')?.textContent || '';
                                            const totalAmount = sanitizeAmount(totalText);
                                            const diff = enteredAmount - totalAmount;

                                            // Reset visual state
                                            paymentAmountInput.classList.remove('is-invalid');
                                            paymentAmountInput.classList.remove('is-valid');
                                            paymentAmountInput.classList.remove('border-danger');
                                            paymentAmountInput.classList.remove('border-info');

                                            // Default: disable when no amount or zero
                                            if (!enteredAmount) {
                                                balanceFeedback.innerHTML = '';
                                                if (completeBtn) completeBtn.disabled = true;
                                                return;
                                            }

                                            if (diff < 0) {
                                                // Not enough
                                                balanceFeedback.innerHTML = `<span style="color: #dc2626; font-weight: 500;">Insufficient amount: LKR ${Math.abs(diff).toFixed(2)}</span>`;
                                                paymentAmountInput.classList.add('is-invalid');
                                                paymentAmountInput.classList.add('border-danger');
                                                if (completeBtn) completeBtn.disabled = true;
                                            } else if (diff === 0) {
                                                // Exact amount
                                                balanceFeedback.innerHTML = '';
                                                paymentAmountInput.classList.add('is-valid');
                                                if (completeBtn) completeBtn.disabled = false;
                                            } else {
                                                // Change to give
                                                balanceFeedback.innerHTML = `<span style="color: #2563eb; font-weight: 500;">Change to give: LKR ${diff.toFixed(2)}</span>`;
                                                paymentAmountInput.classList.add('border-info');
                                                if (completeBtn) completeBtn.disabled = false;
                                            }
                                        }

                                        if (paymentAmountInput) {
                                            paymentAmountInput.addEventListener('input', updateBalanceFeedback);
                                        }

                                        if (paymentTypeSelect) {
                                            paymentTypeSelect.addEventListener('change', function() {
                                                if (this.value === 'Cash') {
                                                    paymentAmountSection.style.display = 'block';
                                                    if (completeBtn) completeBtn.disabled = true; // wait for valid amount
                                                    updateBalanceFeedback();
                                                } else {
                                                    paymentAmountSection.style.display = 'none';
                                                    if (balanceFeedback) balanceFeedback.innerHTML = '';
                                                    if (paymentAmountInput) {
                                                        paymentAmountInput.value = '';
                                                        paymentAmountInput.classList.remove('is-invalid','is-valid','border-danger','border-info');
                                                    }
                                                    if (completeBtn) completeBtn.disabled = false; // card/bank: allow
                                                }
                                            });
                                            // Trigger change on load
                                            if (paymentTypeSelect.value === 'Cash') {
                                                paymentAmountSection.style.display = 'block';
                                                if (completeBtn) completeBtn.disabled = true;
                                                updateBalanceFeedback();
                                            } else {
                                                paymentAmountSection.style.display = 'none';
                                                if (balanceFeedback) balanceFeedback.innerHTML = '';
                                                if (completeBtn) completeBtn.disabled = false;
                                            }
                                        }
                                    });
                                </script>

                                <!-- Amount Received (Initially Hidden) -->
                                <div class="row g-2 mb-3" id="payment-amount-section" style="display: none;">
                                    <div class="col">
                                        <label class="form-label fw-bold text-primary">Amount (LKR)</label>
                                        <input type="number" id="payment-amount-input" class="form-control form-control-lg" step="0.01" min="0" placeholder="0.00" name="pay">
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="row g-2">
                                    <div class="col">
                                        <button type="button" id="complete-payment-btn" class="btn btn-primary w-100 btn-lg" onclick="submitOrder()">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M12 19h-7a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2h4l3 3h7a2 2 0 0 1 2 2v2"/>
                                                <path d="M16 19h6"/>
                                                <path d="M19 16v6"/>
                                            </svg>
                                            Complete Payment
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden cart data -->
            <input type="hidden" name="cart_items" id="cart-items-input" value="">
        </form>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal modal-lg fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="paymentModalLabel">Process Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="payment-processor-container">
                    <!-- Payment processor will be loaded here -->
                    <livewire:payment.payment-processor />
                </div>
            </div>
        </div>
    </div>
</div>

<!-- POS Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <div class="modal-body p-0">
                <div class="receipt-container" id="receipt-content">
                    <!-- Receipt content will be loaded here dynamically -->
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('page-styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .cursor-pointer { cursor: pointer; }
        .hover-shadow:hover { box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; }
        .product-card:hover { transform: translateY(-2px); transition: all 0.2s ease-in-out; }
        
        /* Out of stock product styles */
        .out-of-stock {
            cursor: not-allowed !important;
            opacity: 0.6;
        }
        
        .out-of-stock:hover {
            transform: none !important;
            box-shadow: none !important;
        }
        
        .product-card:not(.out-of-stock):hover {
            border-color: #3b82f6 !important;
        }
        .cart-item {
            border-bottom: 1px solid #e9ecef;
            padding: 12px;
            min-height: 95px;
            max-height: none;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            box-sizing: border-box;
            margin-bottom: 8px;
            background: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            position: relative;
        }
        .cart-item:last-child { border-bottom: none; margin-bottom: 0; }
        .quantity-btn { width: 32px; height: 32px; padding: 0; font-size: 14px; }

        /* Ensure cart items don't overlap */
        #cart-items .cart-item:not(:last-child) {
            margin-bottom: 12px;
        }

        /* Better form control styling in cart */
        .cart-item .form-control,
        .cart-item .form-select {
            border: 1px solid #ced4da;
            border-radius: 4px;
        }

        .cart-item .form-control:focus,
        .cart-item .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        .card { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
        .overflow-auto { max-height: 400px; }
        .flex-1 { flex: 1; }
        .btn-icon { display: inline-flex; align-items: center; justify-content: center; }

        /* Prevent horizontal scrollbar in product section */
        .product-card {
            max-width: 100%;
            box-sizing: border-box;
        }

        .card-body {
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        /* Make the POS layout responsive */
        @media (max-width: 991px) {
            .col-lg-7, .col-lg-5 {
                margin-bottom: 1rem;
            }
        }

        /* Product grid responsiveness */
        .product-card {
            min-height: 120px;
            transition: all 0.2s ease-in-out;
            background: white;
        }

        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            border-color: #3b82f6 !important;
        }

        /* Cart item styling */
        .cart-item-name {
            font-weight: 600;
            color: #1e293b;
        }

        .cart-item-price {
            color: #059669;
            font-weight: 600;
        }

        /* Customer dropdown styling */
        .ts-dropdown .option {
            padding: 10px 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .ts-dropdown .option:last-child {
            border-bottom: none;
        }

        .ts-dropdown .option:hover {
            background-color: #f8fafc;
        }

        .ts-dropdown .option.active {
            background-color: #e0f2fe;
        }

        .ts-control {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
        }

        .ts-control.focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* POS Receipt Modal Styles */
        .receipt-container {
            font-family: 'Courier New', monospace;
            background: white;
            padding: 20px;
            line-height: 1.4;
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
            z-index: 10;
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
            display: grid;
            grid-template-columns: 1fr auto auto auto;
            gap: 8px;
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

        /* Print styles for modal */
        @media print {
            .modal {
                position: static !important;
                display: block !important;
            }

            .modal-dialog {
                margin: 0 !important;
                max-width: none !important;
                width: 80mm !important;
            }

            .modal-content {
                border: none !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            .receipt-container {
                padding: 10px !important;
            }

            .close-btn,
            .print-actions {
                display: none !important;
            }

            body * {
                visibility: hidden;
            }

            #receiptModal, #receiptModal * {
                visibility: visible;
            }

            #receiptModal {
                position: absolute;
                left: 0;
                top: 0;
            }
        }
    </style>
@endpush

@push('page-scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <script>
        // Check Bootstrap availability and provide debug info
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Bootstrap available:', typeof bootstrap !== 'undefined');
            console.log('jQuery available:', typeof $ !== 'undefined');
            if (typeof bootstrap !== 'undefined') {
                console.log('Bootstrap version:', bootstrap);
            }
            if (typeof $ !== 'undefined' && $.fn.modal) {
                console.log('jQuery modal available');
            }
        });

        // Function to get current date and time
        function getCurrentDateTime() {
            const now = new Date();

            // Get current date in YYYY-MM-DD format
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const currentDate = `${year}-${month}-${day}`;

            // Get current time in HH:MM:SS format
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            const currentTime = `${hours}:${minutes}:${seconds}`;

            // Get current datetime in ISO format (YYYY-MM-DDTHH:MM:SS)
            const currentDateTime = `${currentDate}T${currentTime}`;

            console.log('Current Date:', currentDate);
            console.log('Current Time:', currentTime);
            console.log('Current DateTime:', currentDateTime);

            return {
                date: currentDate,
                time: currentTime,
                datetime: currentDateTime,
                timestamp: now.getTime(),
                formatted: now.toLocaleString()
            };
        }

        // Set current date when page loads
        document.addEventListener('DOMContentLoaded', function() {
            const dateTime = getCurrentDateTime();

            // Set the hidden date field with current date
            const dateField = document.getElementById('date');
            if (dateField) {
                dateField.value = dateTime.date;
            }

            // Update the order date display
            const orderDateDisplay = document.getElementById('order-date-display');
            if (orderDateDisplay) {
                orderDateDisplay.textContent = dateTime.formatted;
            }

            // Update the date display every second
            setInterval(function() {
                const currentDateTime = getCurrentDateTime();
                if (orderDateDisplay) {
                    orderDateDisplay.textContent = currentDateTime.formatted;
                }
            }, 1000);

            // Display current date and time in console
            console.log('Order form loaded at:', dateTime.formatted);
        });

        // POS System JavaScript
        let cart = [];
        let cartCount = 0;
        let cartTotal = 0.00;

        // Initialize Tom Select for customer dropdown
        new TomSelect("#customer_id", {
            create: false,
            sortField: {
                field: "text",
                direction: "asc"
            },
            placeholder: "Search customer by name, phone, or email",
            searchField: ['text'],
            maxOptions: null,
            render: {
                option: function(data, escape) {
                    const parts = data.text.split(' - ');
                    const name = parts[0] || '';
                    const phone = parts[1] || '';

                    return '<div class="p-2">' +
                        '<div class="fw-bold text-dark">' + escape(name) + '</div>' +
                        (phone ? '<div class="small text-muted mt-1">' +
                        '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-sm me-1" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">' +
                        '<path stroke="none" d="M0 0h24v24H0z" fill="none"/>' +
                        '<path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2"/>' +
                        '</svg>' + escape(phone) + '</div>' : '') +
                        '</div>';
                },
                item: function(data, escape) {
                    const parts = data.text.split(' - ');
                    const name = parts[0] || '';
                    const phone = parts[1] || '';

                    return '<div>' +
                        '<span class="fw-bold">' + escape(name) + '</span>' +
                        (phone ? ' <small class="text-muted">(' + escape(phone) + ')</small>' : '') +
                        '</div>';
                }
            }
        });

        // Add product click handlers
        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('click', function() {
                // Check if product is out of stock
                if (this.classList.contains('out-of-stock')) {
                    // Show a more user-friendly message for out of stock products
                    const productName = this.querySelector('.fw-bold').textContent.replace('(Out of Stock)', '').trim();
                    
                    // Create and show a toast notification instead of alert
                    showToast(`"${productName}" is currently out of stock and cannot be added to cart.`, 'error');
                    return;
                }
                
                const productId = this.dataset.productId;
                addToCart(productId, this);
            });
        });

        // Clear cart button
        const clearButton = document.querySelector('.card-actions button');
        if (clearButton) {
            clearButton.addEventListener('click', clearCart);
        }

        // Product search functionality
        const searchInput = document.querySelector('input[placeholder*="Search products"]');
        if (searchInput) {
            searchInput.addEventListener('input', filterProducts);
        }

        // Discount and service charges event listeners
        const discountInput = document.getElementById('discount-amount');
        const serviceChargesInput = document.getElementById('service-charges');

        if (discountInput) {
            discountInput.addEventListener('input', updateOrderTotals);
        }

        if (serviceChargesInput) {
            serviceChargesInput.addEventListener('input', updateOrderTotals);
        }

        function addToCart(productId, productElement) {
            // Get product name (handle both in-stock and out-of-stock cases)
            const productNameElement = productElement.querySelector('.fw-bold');
            const productName = productNameElement.textContent.replace('(Out of Stock)', '').trim();
            
            // Get product price - try multiple selectors to be more robust
            let priceElement = productElement.querySelector('.fw-bold.text-success') || 
                              productElement.querySelector('.fw-bold.text-muted') ||
                              productElement.querySelector('.text-success') ||
                              productElement.querySelector('.text-muted');
            
            let productPrice = 0;
            if (priceElement) {
                // Clean price text: remove 'LKR ', commas, and any other non-numeric characters except decimal points
                const priceText = priceElement.textContent.replace('LKR', '').replace(/,/g, '').trim();
                productPrice = parseFloat(priceText);
                
                // If still NaN, try to find any numbers in the text
                if (isNaN(productPrice)) {
                    const numbers = priceText.match(/[\d.]+/);
                    productPrice = numbers ? parseFloat(numbers[0]) : 0;
                }
            }
            
            // Debug log for troubleshooting
            console.log('Price parsing:', {
                priceElement: priceElement ? priceElement.textContent : 'not found',
                productPrice: productPrice,
                productName: productName
            });
            
            // Get stock from data attribute (more reliable)
            const stock = parseInt(productElement.dataset.stock || '0');

            // Validate price
            if (isNaN(productPrice) || productPrice <= 0) {
                showToast(`Unable to add "${productName}" - invalid price information.`, 'error');
                console.error('Invalid price for product:', productName, 'Price:', productPrice);
                return;
            }

            // Double-check stock availability
            if (stock <= 0 || productElement.classList.contains('out-of-stock')) {
                showToast(`"${productName}" is currently out of stock and cannot be added to cart.`, 'error');
                return;
            }

            // Check if product already exists in cart
            const existingItem = cart.find(item => item.id === productId);

            if (existingItem) {
                // Get current available stock (accounting for items already in cart from other sessions)
                const availableStock = stock;
                const totalInCart = existingItem.quantity;

                // Check if we can increase quantity
                if (totalInCart + 1 <= availableStock) {
                    existingItem.quantity += 1;
                    existingItem.total = existingItem.quantity * existingItem.price;
                    // Update the stock reference in case it changed
                    existingItem.stock = stock;
                    updateCartDisplay();
                } else {
                    alert(`Cannot add more items. Available stock: ${availableStock}, Currently in cart: ${totalInCart}`);
                }
            } else {
                // Create new cart item
                const lineId = (typeof crypto !== 'undefined' && crypto.randomUUID) ? crypto.randomUUID() : (Date.now().toString(36) + Math.random().toString(36).slice(2));
                cart.push({
                    lineId: lineId,
                    id: productId,
                    name: productName,
                    price: productPrice,
                    quantity: 1,
                    stock: stock,
                    total: productPrice,
                    serial_number: '',
                    warranty_years: null
                });
                updateCartDisplay();
            }
        }

        function updateCartDisplay() {
            const emptyCart = document.getElementById('empty-cart');
            const cartItems = document.getElementById('cart-items');
            const cartTitle = document.querySelector('h3.card-title');
            const cartAdjustments = document.getElementById('cart-adjustments');

            cartCount = cart.reduce((sum, item) => sum + item.quantity, 0);
            cartTotal = cart.reduce((sum, item) => sum + item.total, 0);

            // Update cart count in the title
            const cartCountElement = document.getElementById('cart-count');
            if (cartCountElement) {
                cartCountElement.textContent = cartCount;
            }

            if (cart.length === 0) {
                if (emptyCart) emptyCart.style.display = 'block';
                if (cartItems) cartItems.style.display = 'none';
                if (cartAdjustments) cartAdjustments.style.display = 'none';
            } else {
                if (emptyCart) emptyCart.style.display = 'none';
                if (cartItems) {
                    cartItems.style.display = 'block';
                    if (cartAdjustments) cartAdjustments.style.display = 'block';

                    cartItems.innerHTML = cart.map(item => `
                        <div class="cart-item">
                            <!-- Product Name and Remove Button -->
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="flex-1">
                                    <div class="cart-item-name fw-bold text-dark" style="font-size: 13px; line-height: 1.2;">${item.name}</div>
                                </div>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeFromCart('${item.lineId}')" style="padding: 4px 8px; font-size: 12px;">
                                    ×
                                </button>
                            </div>

                            <!-- Quantity Controls and Price -->
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center">
                                    <button type="button" class="btn btn-outline-secondary btn-sm me-2" onclick="updateQuantity('${item.lineId}', -1)" style="width: 28px; height: 28px; padding: 0; font-size: 14px;">-</button>
                                    <span class="me-2 px-2 fw-bold" style="min-width: 25px; text-align: center; font-size: 14px;">${item.quantity}</span>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="updateQuantity('${item.lineId}', 1)" style="width: 28px; height: 28px; padding: 0; font-size: 14px;">+</button>
                                </div>
                                <div class="cart-item-price fw-bold text-success" style="font-size: 14px;">LKR ${item.total.toLocaleString()}</div>
                            </div>

                            <!-- Serial Number and Warranty in Same Row -->
                            <div class="d-flex gap-2">
                                <div class="flex-1">
                                    <input type="text"
                                           class="form-control form-control-sm"
                                           placeholder="Serial number"
                                           value="${item.serial_number || ''}"
                                           oninput="updateSerial('${item.lineId}', this.value)"
                                           style="font-size: 12px; padding: 6px 8px;">
                                </div>
                                <div style="min-width: 120px;">
                                    <select class="form-select form-select-sm"
                                            onchange="updateWarranty('${item.lineId}', this.value)"
                                            style="font-size: 12px; padding: 6px 8px;">
                                        <option value="" ${item.warranty_years == null ? 'selected' : ''}>No warranty</option>
                                        <option value="1" ${item.warranty_years == 1 ? 'selected' : ''}>1 year</option>
                                        <option value="2" ${item.warranty_years == 2 ? 'selected' : ''}>2 years</option>
                                        <option value="3" ${item.warranty_years == 3 ? 'selected' : ''}>3 years</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    `).join('');
                }
            }

            // Update totals with discount and service charges
            updateOrderTotals();
        }

        function removeFromCart(lineId) {
            cart = cart.filter(item => item.lineId !== lineId);
            updateCartDisplay();
        }

        function updateSerial(lineId, serial) {
            const item = cart.find(i => i.lineId === lineId);
            if (item) {
                item.serial_number = serial;
            }
        }

        function updateWarranty(lineId, years) {
            const item = cart.find(i => i.lineId === lineId);
            if (item) {
                item.warranty_years = years ? parseInt(years) : null;
            }
        }

        function updateQuantity(lineId, change) {
            const item = cart.find(item => item.lineId === lineId);
            if (item) {
                const newQuantity = item.quantity + change;
                if (newQuantity > 0 && newQuantity <= item.stock) {
                    item.quantity = newQuantity;
                    item.total = item.quantity * item.price;
                    updateCartDisplay();
                } else if (newQuantity <= 0) {
                    removeFromCart(lineId);
                } else {
                    alert(`Cannot add more items. Maximum stock available: ${item.stock}`);
                }
            }
        }

        function clearCart() {
            if (cart.length > 0 && confirm('Are you sure you want to clear the cart?')) {
                cart = [];
                // Reset discount and service charges
                document.getElementById('discount-amount').value = '0';
                document.getElementById('service-charges').value = '0';
                updateCartDisplay();
            }
        }

        // Toast notification function
        function showToast(message, type = 'info') {
            // Remove any existing toast
            const existingToast = document.querySelector('.custom-toast');
            if (existingToast) {
                existingToast.remove();
            }

            // Create toast element
            const toast = document.createElement('div');
            toast.className = `custom-toast alert alert-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'} alert-dismissible fade show`;
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                min-width: 300px;
                max-width: 500px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            `;
            toast.innerHTML = `
                <strong>${type === 'error' ? 'Error:' : type === 'success' ? 'Success:' : 'Info:'}</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            // Add to document
            document.body.appendChild(toast);

            // Auto remove after 4 seconds
            setTimeout(() => {
                if (toast && toast.parentNode) {
                    toast.remove();
                }
            }, 4000);
        }

        // Function to update order totals including discount and service charges
        function updateOrderTotals() {
            const discountAmount = parseFloat(document.getElementById('discount-amount').value) || 0;
            const serviceCharges = parseFloat(document.getElementById('service-charges').value) || 0;

            const subtotalElement = document.getElementById('subtotal-amount');
            const discountRow = document.getElementById('discount-row');
            const discountDisplay = document.getElementById('discount-display');
            const serviceRow = document.getElementById('service-row');
            const serviceDisplay = document.getElementById('service-display');
            const totalElement = document.getElementById('total-amount');

            // Update subtotal
            if (subtotalElement) {
                subtotalElement.textContent = `LKR ${cartTotal.toLocaleString()}`;
            }

            // Show/hide and update discount
            if (discountAmount > 0) {
                if (discountRow) discountRow.style.display = 'flex';
                if (discountDisplay) discountDisplay.textContent = `-LKR ${discountAmount.toLocaleString()}`;
            } else {
                if (discountRow) discountRow.style.display = 'none';
            }

            // Show/hide and update service charges
            if (serviceCharges > 0) {
                if (serviceRow) serviceRow.style.display = 'flex';
                if (serviceDisplay) serviceDisplay.textContent = `+LKR ${serviceCharges.toLocaleString()}`;
            } else {
                if (serviceRow) serviceRow.style.display = 'none';
            }

            // Calculate and update final total
            const finalTotal = cartTotal - discountAmount + serviceCharges;
            if (totalElement) {
                totalElement.textContent = `LKR ${finalTotal.toLocaleString()}`;
            }
        }

        function filterProducts() {
            const searchTerm = event.target.value.toLowerCase();
            const productCards = document.querySelectorAll('.product-card');

            productCards.forEach(card => {
                // Handle both in-stock (.text-dark) and out-of-stock (.text-muted) products
                const productNameElement = card.querySelector('.fw-bold');
                const productName = productNameElement.textContent.toLowerCase().replace('(out of stock)', '').trim();

                if (productName.includes(searchTerm)) {
                    card.parentElement.style.display = 'block';
                } else {
                    card.parentElement.style.display = 'none';
                }
            });
        }

        // Payment validation handler - REMOVED conflicting event listener
        // The button now uses only onclick="submitOrder()" to avoid conflicts

        function loadPaymentProcessor() {
            const customerId = document.getElementById('customer_id').value;

            // Dispatch Livewire event to load payment data
            if (window.Livewire) {
                Livewire.emit('load-payment-data', {
                    cartItems: cart,
                    customerId: customerId
                });
            }
        }

        // Listen for payment completion events
        document.addEventListener('livewire:load', function () {
            Livewire.on('payment-completed', function (data) {
                // Hide the payment modal
                const paymentModalElement = document.getElementById('paymentModal');
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    const modal = bootstrap.Modal.getInstance(paymentModalElement);
                    if (modal) {
                        modal.hide();
                    }
                } else if (typeof $ !== 'undefined' && $.fn.modal) {
                    $('#paymentModal').modal('hide');
                } else {
                    // Fallback
                    paymentModalElement.style.display = 'none';
                    paymentModalElement.classList.remove('show');
                }

                // Clear the cart
                cart = [];
                updateCartDisplay();

                // Show success notification
                showSuccessNotification(`Payment completed successfully! Invoice: ${data[0].invoice_no}`);

                // Redirect to order details after 3 seconds
                setTimeout(() => {
                    window.location.href = `{{ route('orders.show', '') }}/${data[0].order_id}`;
                }, 3000);
            });
        });

        function showSuccessNotification(message) {
            // Create a nice success notification
            const notification = document.createElement('div');
            notification.className = 'alert alert-success alert-dismissible position-fixed top-0 start-50 translate-middle-x';
            notification.style.zIndex = '9999';
            notification.style.marginTop = '20px';
            notification.innerHTML = `
                <div class="d-flex align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2 text-success" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M5 12l5 5l10 -10"/>
                    </svg>
                    <div>
                        <strong>Success!</strong> ${message}
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            document.body.appendChild(notification);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 5000);
        }

        // Make cart data available globally for Livewire component
        window.getCartData = function() {
            return cart;
        };

        // Reset payment processor when modal is closed
        if (paymentModal) {
            paymentModal.addEventListener('hidden.bs.modal', function () {
                if (window.Livewire) {
                    Livewire.emit('reset-payment');
                }
            });
        }

        // Show receipt modal function
        function showReceiptModal(orderData) {
            // Use actual order data from server response
            const customer = orderData.customer;
            const invoiceNo = orderData.invoice_no;
            const dateTime = orderData.order_date;
            const items = orderData.items;
            const subtotal = orderData.subtotal;
            const discount = orderData.discount;
            const serviceCharges = orderData.service_charges;
            const total = orderData.total;

            // Generate receipt HTML
            const receiptHTML = `
                <button class="close-btn" onclick="closeReceiptModal()" title="Close">&times;</button>

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
                        ${invoiceNo}<br>
                        ${dateTime}
                    </div>
                </div>

                <div class="customer-section">
                    <div class="customer-title">Customer Details</div>
                    <div class="customer-info">
                        <div><strong>Name:</strong> ${customer.name}</div>
                        ${customer.phone ? `<div><strong>Phone:</strong> ${customer.phone}</div>` : ''}
                        ${customer.email ? `<div><strong>Email:</strong> ${customer.email}</div>` : ''}
                    </div>
                </div>

                <div class="items-section">
                    <div class="items-header">
                        <span>Item</span>
                        <span>Qty</span>
                        <span>Price</span>
                        <span>Total</span>
                    </div>

                    ${items.map((item, index) => `
                        <div class="item-row">
                            <div class="item-details">
                                <div class="item-name">${index + 1}. ${item.name}</div>
                                <div class="item-meta">
                                    ${item.serial_number ? `S/N: ${item.serial_number}<br>` : ''}
                                    ${item.warranty_years && Number(item.warranty_years) > 0 ? `<span class="warranty">Warranty: ${item.warranty_years} ${Number(item.warranty_years) === 1 ? 'year' : 'years'}</span>` : ''}
                                </div>
                            </div>
                            <div style="text-align: center;">${item.quantity}</div>
                            <div style="text-align: right;">LKR ${item.price.toLocaleString()}</div>
                            <div style="text-align: right;">LKR ${item.total.toLocaleString()}</div>
                        </div>
                    `).join('')}

                <div class="totals-section">
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span>LKR ${subtotal.toLocaleString()}</span>
                    </div>
                    ${orderData.discount && orderData.discount > 0 ? `
                        <div class="total-row">
                            <span>Discount:</span>
                            <span>-LKR ${orderData.discount.toLocaleString()}</span>
                        </div>
                    ` : ''}
                    ${orderData.service_charges && orderData.service_charges > 0 ? `
                        <div class="total-row">
                            <span>Service Charges:</span>
                            <span>+LKR ${orderData.service_charges.toLocaleString()}</span>
                        </div>
                    ` : ''}
                    <div class="total-row final">
                        <span>TOTAL:</span>
                        <span>LKR ${total.toLocaleString()}</span>
                    </div>
                </div>

                <div class="print-actions">
                    <button class="print-btn" onclick="printReceipt()">🖨️ Print Receipt</button>
                    <button class="back-btn" onclick="startNewOrder()">← New Order</button>
                </div>
            `;

            // Insert receipt content and show modal
            document.getElementById('receipt-content').innerHTML = receiptHTML;

            // Try multiple approaches to show the modal
            const receiptModalElement = document.getElementById('receiptModal');
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                // Bootstrap 5 approach
                const receiptModal = new bootstrap.Modal(receiptModalElement);
                receiptModal.show();
            } else if (typeof $ !== 'undefined' && $.fn.modal) {
                // jQuery/Bootstrap 4 approach
                $('#receiptModal').modal('show');
            } else {
                // Fallback: manually show modal
                receiptModalElement.style.display = 'block';
                receiptModalElement.classList.add('show');
                receiptModalElement.setAttribute('aria-hidden', 'false'); // Fix accessibility issue
                receiptModalElement.setAttribute('aria-modal', 'true');
                document.body.classList.add('modal-open');

                // Create backdrop
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                backdrop.id = 'receipt-modal-backdrop';
                document.body.appendChild(backdrop);
            }

            // Ensure aria-hidden is properly set for all approaches
            receiptModalElement.setAttribute('aria-hidden', 'false');
            receiptModalElement.setAttribute('aria-modal', 'true');
        }

        // Close receipt modal
        function closeReceiptModal() {
            const receiptModalElement = document.getElementById('receiptModal');

            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                // Bootstrap 5 approach
                const receiptModal = bootstrap.Modal.getInstance(receiptModalElement);
                if (receiptModal) {
                    receiptModal.hide();
                }
            } else if (typeof $ !== 'undefined' && $.fn.modal) {
                // jQuery/Bootstrap 4 approach
                $('#receiptModal').modal('hide');
            } else {
                // Fallback: manually hide modal
                receiptModalElement.style.display = 'none';
                receiptModalElement.classList.remove('show');
                receiptModalElement.setAttribute('aria-hidden', 'true');
                receiptModalElement.removeAttribute('aria-modal');
                document.body.classList.remove('modal-open');

                // Remove backdrop
                const backdrop = document.getElementById('receipt-modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
            }
        }

        // Print receipt function
        function printReceipt() {
            window.print();
        }

        // Function to update stock display after successful payment
        function updateStockDisplay(soldItems) {
            console.log('Updating stock display for items:', soldItems);

            soldItems.forEach(soldItem => {
                // Find the product card on the page
                const productCard = document.querySelector(`[data-product-id="${soldItem.product_id}"]`);
                if (productCard) {
                    const stockBadge = productCard.querySelector('.badge');
                    if (stockBadge) {
                        const currentStockText = stockBadge.textContent;
                        const currentStock = parseInt(currentStockText.replace('Stock: ', ''));
                        const newStock = Math.max(0, currentStock - soldItem.quantity);

                        // Update the stock display
                        stockBadge.textContent = `Stock: ${newStock}`;

                        // Update the badge color based on stock level
                        stockBadge.className = 'badge rounded-pill';
                        if (newStock > 0) {
                            stockBadge.style.backgroundColor = '#3b82f6';
                            stockBadge.style.color = 'white';
                        } else {
                            stockBadge.style.backgroundColor = '#ef4444';
                            stockBadge.style.color = 'white';
                            // Optionally disable the product card
                            productCard.style.opacity = '0.6';
                            productCard.style.pointerEvents = 'none';
                        }

                        console.log(`Updated ${soldItem.product_name}: ${currentStock} → ${newStock}`);
                    }
                }
            });
        }

        // Start new order function
        function startNewOrder() {
            // Clear cart and reset form
            cart = [];
            updateCartDisplay();

            // Reset customer selection
            const customerSelect = document.getElementById('customer_id');
            if (customerSelect.tomselect) {
                customerSelect.tomselect.clear();
            }

            // Reset payment method
            document.querySelector('select[name="payment_type"]').value = 'Cash';

            // Reset payment amount
            const paymentAmountInput = document.getElementById('payment-amount-input');
            if (paymentAmountInput) {
                paymentAmountInput.value = '';
            }

            // Reset discount and service charges
            document.getElementById('discount-amount').value = '0';
            document.getElementById('service-charges').value = '0';
            updateOrderTotals();

            // Close modal
            closeReceiptModal();

            // Show success message
            showSuccessNotification('Ready for new order!');
        }

        // Handle escape key to close modal
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const receiptModalElement = document.getElementById('receiptModal');
                // Check if modal is visible (has show class or display block)
                if (receiptModalElement.classList.contains('show') || receiptModalElement.style.display === 'block') {
                    closeReceiptModal();
                }
            }
        });

        // Submit order function
        function submitOrder() {
            console.log('Submit order called');

            // Validate required fields
            const customerId = document.getElementById('customer_id').value;
            const paymentType = document.querySelector('select[name="payment_type"]').value;

            console.log('Customer ID:', customerId);
            console.log('Payment Type:', paymentType);
            console.log('Cart items:', cart);

            // If no customer selected, it will be saved as Walk-In Customer by server

            if (!paymentType) {
                alert('Please select a payment method');
                return;
            }

            if (cart.length === 0) {
                alert('Please add items to cart');
                return;
            }

            // Handle payment amount
            const payAmountInput = document.getElementById('payment-amount-input');
            if (paymentType === 'Cash') {
                const payAmount = payAmountInput.value;
                if (!payAmount || parseFloat(payAmount) <= 0) {
                    alert('Please enter payment amount');
                    return;
                }
            } else {
                // For non-cash payments, set pay amount to total
                const cartTotalAmount = cart.reduce((sum, item) => sum + item.total, 0);
                if (payAmountInput) {
                    payAmountInput.value = cartTotalAmount;
                }
            }

            // Populate hidden cart data
            const cartData = JSON.stringify(cart);
            document.getElementById('cart-items-input').value = cartData;

            console.log('Cart data being sent:', cartData);
            console.log('Discount amount:', document.getElementById('discount-amount').value);
            console.log('Service charges:', document.getElementById('service-charges').value);

            // Submit the form using AJAX to avoid redirect issues
            // Use the specific order form ID to avoid confusion with logout form
            const form = document.getElementById('order-form');
            if (!form) {
                console.error('❌ Order form not found!');
                alert('Error: Order form not found');
                return;
            }
            const formData = new FormData(form);

            console.log('🔍 FORM DEBUG INFO:');
            console.log('Form element:', form);
            console.log('Form action attribute:', form.getAttribute('action'));
            console.log('Form action property:', form.action);
            console.log('Current URL:', window.location.href);
            console.log('Form data:', Object.fromEntries(formData));

            // Force absolute URL to prevent any relative path issues
            const targetUrl = form.action || 'http://127.0.0.1:8000/simple-test';
            console.log('🚀 Final target URL:', targetUrl);

            fetch(targetUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || document.querySelector('input[name="_token"]')?.value
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                return response.text(); // Get raw text first
            })
            .then(data => {
                console.log('Raw response:', data);

                // Try to parse as JSON first (from OrderController)
                try {
                    const jsonResponse = JSON.parse(data);
                    console.log('✅ JSON RESPONSE RECEIVED!');
                    console.log('� Server response:', jsonResponse);

                    if (jsonResponse.success) {
                        // Update stock display with sold items
                        if (jsonResponse.soldItems && jsonResponse.soldItems.length > 0) {
                            updateStockDisplay(jsonResponse.soldItems);
                        }

                        // Clear cart and reset UI
                        const cartItemsBeforeClear = [...cart]; // Store cart items for potential stock update
                        cart = [];
                        updateCartDisplay();
                        const payInput = document.getElementById('payment-amount-input');
                        const feedback = document.getElementById('balance-feedback');
                        if (payInput) {
                            payInput.value = '';
                            payInput.classList.remove('is-invalid','is-valid','border-danger','border-info');
                        }
                        if (feedback) feedback.innerHTML = '';
                        document.getElementById('cart-items-input').value = '[]';

                        // If server doesn't provide soldItems, use cart data as fallback
                        if (!jsonResponse.soldItems && cartItemsBeforeClear.length > 0) {
                            const fallbackSoldItems = cartItemsBeforeClear.map(item => ({
                                product_id: item.id,
                                product_name: item.name,
                                quantity: item.quantity
                            }));
                            updateStockDisplay(fallbackSoldItems);
                        }

                        // Show success message briefly, then show receipt modal
                        showSuccessNotification('Order created successfully!');
                        setTimeout(() => {
                            showReceiptModal(jsonResponse.order);
                        }, 800);
                    } else {
                        alert('❌ Order creation failed: ' + (jsonResponse.message || 'Unknown error'));
                    }
                } catch (e) {
                    // Fallback for HTML responses (like our debug route)
                    console.log('Response is not JSON, checking for SUCCESS marker');
                    if (data.includes('SUCCESS!')) {
                        console.log('✅ DEBUG SUCCESS RESPONSE!');
                        alert('✅ SUCCESS! Check console for details.');
                        const newWindow = window.open();
                        newWindow.document.write(data);
                    } else {
                        console.log('❌ Unexpected response:', data);
                        alert('❌ Got unexpected response. Check console for details.');
                    }
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Network error: ' + error.message);
            });
        }
    </script>
@endpush
