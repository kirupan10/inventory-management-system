@extends('layouts.tabler')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <x-alert/>

        <!-- POS Header -->
        <div class="row mb-3">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="page-title">
                        {{ __('Point of Sale') }}
                    </h1>
                    <p class="text-muted mb-0">Process new transactions</p>
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

        <form action="{{ route('invoice.create') }}" method="POST">
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
                            <h3 class="card-title">
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
                        <div class="card-body d-flex flex-column">
                            <!-- Product Search Section -->
                            <div class="mb-4">
                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label class="form-label">Search products by name, SKU, or scan barcode...</label>
                                        <input type="text" class="form-control form-control-lg" placeholder="Search products by name, SKU, or scan barcode..." autocomplete="off">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Barcode</label>
                                        <button type="button" class="btn btn-primary btn-lg w-100">
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
                                            Scan
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Products Grid -->
                            <div class="flex-1 overflow-auto">
                                <h4 class="mb-3">Products</h4>
                                <div class="row g-3">
                                    @foreach($products as $product)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card card-sm cursor-pointer hover-shadow product-card" data-product-id="{{ $product->id }}">
                                            <div class="card-body text-center p-3">
                                                <div class="mb-2">
                                                    <strong class="text-dark">{{ Str::limit($product->name, 20) }}</strong>
                                                </div>
                                                <div class="mb-2">
                                                    <small class="text-muted">{{ $product->code }}</small>
                                                </div>
                                                <div class="mb-2">
                                                    <span class="h4 text-primary">LKR {{ number_format($product->selling_price, 0) }}</span>
                                                </div>
                                                <div class="mb-2">
                                                    @if($product->quantity > 0)
                                                        <span class="badge bg-success">Stock: {{ $product->quantity }}</span>
                                                    @else
                                                        <span class="badge bg-danger">Stock: 0</span>
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
                            <h3 class="card-title">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                                    <path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                                    <path d="M17 17h-11v-14h-2"/>
                                    <path d="M6 5l14 1l-1 7h-13"/>
                                </svg>
                                Cart (0)
                            </h3>
                            <div class="card-actions">
                                <button type="button" class="btn btn-outline-secondary btn-sm">Clear</button>
                            </div>
                        </div>
                        <div class="card-body d-flex flex-column p-3">
                            <!-- Customer Selection -->
                            <div class="mb-4">
                                <label for="customer_id" class="form-label fw-bold">Customer</label>
                                <div class="input-group">
                                    <select id="customer_id" name="customer_id"
                                            class="form-select @error('customer_id') is-invalid @enderror"
                                            required>
                                        <option value="">Search customer by name, phone, or email</option>
                                        @foreach($customers as $customer)
                                            <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>
                                                {{ $customer->name }} - {{ $customer->phone }}
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
                            <div class="flex-1 overflow-auto mb-4">
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
                                <div id="cart-items" style="display: none;">
                                    <!-- Dynamic cart items -->
                                </div>
                            </div>

                            <!-- Order Summary -->
                            <div class="border-top pt-3">
                                <div class="row mb-2">
                                    <div class="col">Subtotal:</div>
                                    <div class="col-auto fw-bold">LKR 0.00</div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col"><strong>Total:</strong></div>
                                    <div class="col-auto"><strong class="h4 text-primary">LKR 0.00</strong></div>
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

                                <!-- Amount Received -->
                                <div class="row g-2 mb-3">
                                    <div class="col">
                                        <label class="form-label">Amount received (LKR)</label>
                                        <input type="number" class="form-control" step="0.01" min="0" placeholder="0.00">
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="row g-2">
                                    <div class="col">
                                        <button type="submit" class="btn btn-primary w-100">
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

            <!-- Include the Order Form Livewire Component (hidden for now) -->
            <div style="display: none;">
                <livewire:order-form :cart-instance="'order'" />
            </div>
        </form>
    </div>
</div>
@endsection

@push('page-styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .cursor-pointer { cursor: pointer; }
        .hover-shadow:hover { box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; }
        .product-card:hover { transform: translateY(-2px); transition: all 0.2s ease-in-out; }
        .cart-item { border-bottom: 1px solid #e9ecef; padding: 12px 0; }
        .cart-item:last-child { border-bottom: none; }
        .quantity-btn { width: 32px; height: 32px; padding: 0; font-size: 14px; }
        .card { box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075); }
        .overflow-auto { max-height: 400px; }
        .flex-1 { flex: 1; }
        .btn-icon { display: inline-flex; align-items: center; justify-content: center; }

        /* Make the POS layout responsive */
        @media (max-width: 991px) {
            .col-lg-7, .col-lg-5 {
                margin-bottom: 1rem;
            }
        }

        /* Product grid responsiveness */
        .product-card {
            min-height: 140px;
            display: flex;
            align-items: center;
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
    </style>
@endpush

@push('page-scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

    <script>
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

            // Display current date and time in console and optionally in UI
            console.log('Order form loaded at:', dateTime.formatted);

            // Optional: Add current date/time display to the page

            // Find a place to insert the time display (after the form header)
            const cardHeader = document.querySelector('.card-header');
            if (cardHeader) {
                const timeContainer = document.createElement('div');
                timeContainer.className = 'mt-2';
                timeContainer.appendChild(currentTimeDisplay);
                cardHeader.appendChild(timeContainer);
            }
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
                    return '<div>' +
                        '<span class="fw-bold">' + escape(data.text.split(' - ')[0]) + '</span>' +
                        (data.text.includes(' - ') ? '<br><small class="text-muted">📞 ' + escape(data.text.split(' - ')[1]) + '</small>' : '') +
                        '</div>';
                },
                item: function(data, escape) {
                    return '<div>' + 
                        '<span class="fw-bold">' + escape(data.text.split(' - ')[0]) + '</span>' +
                        (data.text.includes(' - ') ? ' <small class="text-muted">(' + escape(data.text.split(' - ')[1]) + ')</small>' : '') +
                        '</div>';
                }
            }
        });

        // Add product click handlers
        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('click', function() {
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

        function addToCart(productId, productElement) {
            const productName = productElement.querySelector('strong').textContent;
            const productCode = productElement.querySelector('.text-muted').textContent;
            const productPrice = parseFloat(productElement.querySelector('.text-primary').textContent.replace('LKR ', '').replace(',', ''));
            const stockElement = productElement.querySelector('.badge');
            const stock = parseInt(stockElement.textContent.replace('Stock: ', ''));

            if (stock <= 0) {
                alert('This product is out of stock!');
                return;
            }

            // Check if product already in cart
            const existingItem = cart.find(item => item.id === productId);
            if (existingItem) {
                if (existingItem.quantity < stock) {
                    existingItem.quantity++;
                    existingItem.total = existingItem.quantity * existingItem.price;
                } else {
                    alert('Cannot add more items. Stock limit reached!');
                    return;
                }
            } else {
                cart.push({
                    id: productId,
                    name: productName,
                    code: productCode,
                    price: productPrice,
                    quantity: 1,
                    stock: stock,
                    total: productPrice
                });
            }

            updateCartDisplay();
        }

        function updateCartDisplay() {
            const emptyCart = document.getElementById('empty-cart');
            const cartItems = document.getElementById('cart-items');
            const cartTitle = document.querySelector('h3.card-title');

            cartCount = cart.reduce((sum, item) => sum + item.quantity, 0);
            cartTotal = cart.reduce((sum, item) => sum + item.total, 0);

            // Update cart title
            if (cartTitle && cartTitle.innerHTML.includes('Cart')) {
                cartTitle.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                        <path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0"/>
                        <path d="M17 17h-11v-14h-2"/>
                        <path d="M6 5l14 1l-1 7h-13"/>
                    </svg>
                    Cart (${cartCount})
                `;
            }

            if (cart.length === 0) {
                if (emptyCart) emptyCart.style.display = 'block';
                if (cartItems) cartItems.style.display = 'none';
            } else {
                if (emptyCart) emptyCart.style.display = 'none';
                if (cartItems) {
                    cartItems.style.display = 'block';
                    cartItems.innerHTML = cart.map(item => `
                        <div class="cart-item">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="flex-1">
                                    <div class="cart-item-name">${item.name}</div>
                                    <small class="text-muted">${item.code}</small>
                                </div>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeFromCart('${item.id}')">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M18 6l-12 12"/>
                                        <path d="M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <button type="button" class="btn btn-outline-secondary quantity-btn me-2" onclick="updateQuantity('${item.id}', -1)">-</button>
                                    <span class="me-2">${item.quantity}</span>
                                    <button type="button" class="btn btn-outline-secondary quantity-btn" onclick="updateQuantity('${item.id}', 1)">+</button>
                                </div>
                                <div class="cart-item-price">LKR ${item.total.toLocaleString()}</div>
                            </div>
                        </div>
                    `).join('');
                }
            }

            // Update totals
            const subtotalRows = document.querySelectorAll('.row');
            subtotalRows.forEach(row => {
                const firstCol = row.querySelector('.col');
                const lastCol = row.querySelector('.col-auto');
                if (firstCol && lastCol) {
                    if (firstCol.textContent.includes('Subtotal:')) {
                        lastCol.textContent = `LKR ${cartTotal.toLocaleString()}`;
                    }
                    if (firstCol.textContent.includes('Total:')) {
                        const strongElement = lastCol.querySelector('strong');
                        if (strongElement) {
                            strongElement.textContent = `LKR ${cartTotal.toLocaleString()}`;
                        }
                    }
                }
            });
        }

        function removeFromCart(productId) {
            cart = cart.filter(item => item.id !== productId);
            updateCartDisplay();
        }

        function updateQuantity(productId, change) {
            const item = cart.find(item => item.id === productId);
            if (item) {
                const newQuantity = item.quantity + change;
                if (newQuantity > 0 && newQuantity <= item.stock) {
                    item.quantity = newQuantity;
                    item.total = item.quantity * item.price;
                    updateCartDisplay();
                } else if (newQuantity <= 0) {
                    removeFromCart(productId);
                } else {
                    alert('Cannot add more items. Stock limit reached!');
                }
            }
        }

        function clearCart() {
            if (cart.length > 0 && confirm('Are you sure you want to clear the cart?')) {
                cart = [];
                updateCartDisplay();
            }
        }

        function filterProducts() {
            const searchTerm = event.target.value.toLowerCase();
            const productCards = document.querySelectorAll('.product-card');

            productCards.forEach(card => {
                const productName = card.querySelector('strong').textContent.toLowerCase();
                const productCode = card.querySelector('.text-muted').textContent.toLowerCase();

                if (productName.includes(searchTerm) || productCode.includes(searchTerm)) {
                    card.parentElement.style.display = 'block';
                } else {
                    card.parentElement.style.display = 'none';
                }
            });
        }

        // Form submission handler
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                if (cart.length === 0) {
                    e.preventDefault();
                    alert('Please add items to cart before completing the order.');
                    return;
                }

                const customerId = document.getElementById('customer_id').value;
                if (!customerId) {
                    e.preventDefault();
                    alert('Please select a customer.');
                    return;
                }

                console.log('Order submitted:', { cart, customerId });
            });
        }
    </script>
@endpush
