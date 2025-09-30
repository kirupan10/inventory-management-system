<div>

    @session('message')
    <div class="p-4 bg-green-100">
        {{ $value }}
    </div>
    @endsession


    <table class="table table-bordered" id="products_table">
        <thead class="thead-dark">
            <tr>
                <th class="align-middle">Product</th>
                <th class="align-middle text-center">Quantity</th>
                <th class="align-middle text-center">Price</th>
                <th class="align-middle text-center">Total</th>
                <th class="align-middle text-center">Action</th>
            </tr>
        </thead>

        <tbody>
{{--            @php--}}
{{--                dd(Cart::instance('order')->content())--}}
{{--            @endphp--}}

            @foreach ($invoiceProducts as $index => $invoiceProduct)
            <tr wire:key="product-row-{{ $index }}">
                <td class="align-middle">
                    @if($invoiceProduct['is_saved'])
                        <input type="hidden" name="invoiceProducts[{{$index}}][product_id]" value="{{ $invoiceProduct['product_id'] }}">

                        {{ $invoiceProduct['product_name'] }}
                    @else

                        <div class="position-relative">
                            <input type="text"
                                   wire:model.live.debounce.300ms="invoiceProducts.{{$index}}.product_search"
                                   wire:focus="focusSearch({{$index}})"
                                   wire:blur="blurSearch({{$index}})"
                                   id="product_search_{{$index}}"
                                   class="product-search-input form-control @error('invoiceProducts.' . $index . '.product_id') is-invalid @enderror"
                                   placeholder="Click to see products or start typing to search..."
                                   autocomplete="off"
                            >

                            <input type="hidden"
                                   wire:model="invoiceProducts.{{$index}}.product_id"
                                   id="product_id_{{$index}}"
                            >

                            <!-- Hot Search Results Dropdown -->
                            <div class="product-search-results position-absolute w-100"
                                 id="search_results_{{$index}}"
                                 style="top: 100%; z-index: 1000; max-height: 350px; overflow-y: auto; display: {{ (isset($searchFocused[$index]) && $searchFocused[$index]) || (isset($invoiceProducts[$index]['product_search']) && strlen($invoiceProducts[$index]['product_search']) > 0) ? 'block' : 'none' }};"
                                 >

                                @php
                                    $filteredProducts = $this->getFilteredProducts($index);
                                @endphp

                                @if((isset($searchFocused[$index]) && $searchFocused[$index]) || (isset($invoiceProducts[$index]['product_search']) && strlen($invoiceProducts[$index]['product_search']) > 0))
                                    @if($filteredProducts->count() > 0)
                                        <div class="bg-white border border-top-0 rounded-bottom shadow">
                                            @foreach($filteredProducts as $product)
                                                <div class="product-search-item p-3 border-bottom cursor-pointer hover-bg-light"
                                                     data-product-id="{{ $product->id }}"
                                                     data-product-name="{{ $product->name }}"
                                                     data-product-code="{{ $product->code }}"
                                                     data-product-price="{{ $product->selling_price }}"
                                                     data-product-stock="{{ $product->quantity }}"
                                                     wire:click="selectProduct({{$index}}, {{$product->id}}, '{{ addslashes($product->name) }}')"
                                                     onclick="hideSearchResults({{$index}})">

                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <div class="fw-bold text-dark">
                                                                {!! $this->highlightSearch($product->name, $invoiceProducts[$index]['product_search'] ?? '') !!}
                                                            </div>
                                                            <small class="text-muted">
                                                                Code: {!! $this->highlightSearch($product->code, $invoiceProducts[$index]['product_search'] ?? '') !!}
                                                            </small>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="fw-bold text-success">${{ number_format($product->selling_price, 2) }}</div>
                                                            <small class="text-muted">Stock: {{ $product->quantity }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach

                                            <!-- Quick stats at bottom -->
                                            <div class="p-2 bg-light border-top">
                                                <small class="text-muted">
                                                    @if(isset($invoiceProducts[$index]['product_search']) && strlen($invoiceProducts[$index]['product_search']) > 0)
                                                        Showing {{ $filteredProducts->count() }} search results
                                                        @if($this->allProducts->count() > $filteredProducts->count())
                                                            of {{ $this->allProducts->count() }} total products
                                                        @endif
                                                    @else
                                                        Showing first {{ $filteredProducts->count() }} products - start typing to search
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                    @else
                                        <div class="bg-white border border-top-0 rounded-bottom shadow">
                                            <div class="p-3 text-center">
                                                @if(isset($invoiceProducts[$index]['product_search']) && strlen($invoiceProducts[$index]['product_search']) > 0)
                                                    <div class="text-muted">
                                                        <i class="ti ti-search"></i>
                                                        No products found for "{{ $invoiceProducts[$index]['product_search'] }}"
                                                    </div>
                                                    <small class="text-muted">Try a different search term</small>
                                                @else
                                                    <div class="text-muted">
                                                        <i class="ti ti-package"></i>
                                                        No products available
                                                    </div>
                                                    <small class="text-muted">Add products to your inventory first</small>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                @endif
                            </div>
                        </div>

                        @error('invoiceProducts.' . $index)
                            <em class="text-danger">
                                {{ $message }}
                            </em>
                        @enderror
                    @endif
                </td>

                <td class="align-middle text-center">
                    @if($invoiceProduct['is_saved'])
                        {{ $invoiceProduct['quantity'] }}

                        <input type="hidden"
                               name="invoiceProducts[{{$index}}][quantity]"
                               value="{{ $invoiceProduct['quantity'] }}"
                        >
                    @else
                        <input type="number"
                               wire:model="invoiceProducts.{{$index}}.quantity"
                               id="invoiceProducts[{{$index}}][quantity]"
                               class="form-control"
                        />
                    @endif
                </td>

                {{--- Unit Price ---}}
                <td class="align-middle text-center">
                    @if($invoiceProduct['is_saved'])
                        {{ $unit_cost = number_format($invoiceProduct['product_price'], 2) }}

                        <input type="hidden"
                               name="invoiceProducts[{{$index}}][unitcost]"
                               value="{{ $unit_cost }}"
                        >
                    @endif
                </td>

                {{--- Total ---}}
                <td class="align-middle text-center">
                    {{ $product_total = $invoiceProduct['product_price'] * $invoiceProduct['quantity'] }}

                    <input type="hidden"
                           name="invoiceProducts[{{$index}}][total]"
                           value="{{ $product_total }}"
                    >
                </td>

                <td class="align-middle text-center">
                    @if($invoiceProduct['is_saved'])
                        <button type="button" wire:click="editProduct({{$index}})" class="btn btn-icon btn-outline-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-nexora icon-nexora-pencil" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                        </button>

                    @elseif($invoiceProduct['product_id'])

                        <button type="button" wire:click="saveProduct({{$index}})" class="btn btn-icon btn-outline-success mr-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-nexora icon-nexora-device-floppy" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" /><path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M14 4l0 4l-6 0l0 -4" /></svg>
                        </button>
                    @endif

                    <button type="button" wire:click="removeProduct({{$index}})" class="btn btn-icon btn-outline-danger">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-nexora icon-nexora-trash" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 7l16 0" /><path d="M10 11l0 6" /><path d="M14 11l0 6" /><path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" /><path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" /></svg>
                    </button>
                </td>
            </tr>
            @endforeach
            <tr>
                <td colspan="4"></td>
                <td class="text-center">
                    <button type="button" wire:click="addProduct" class="btn btn-icon btn-success">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-nexora icon-nexora-plus" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 5l0 14" /><path d="M5 12l14 0" /></svg>
                    </button>
                </td>
            </tr>
            <tr>
                <th colspan="4" class="align-middle text-end">
                    Subtotal
                </th>
                <td class="text-center">
{{--                    ${{ number_format($subtotal, 2) }}--}}
                    {{ Number::currency($subtotal, 'LKR') }}
                </td>
            </tr>
            <tr>
                <th colspan="4" class="align-middle text-end">
                    Taxes
                </th>
                <td width="150" class="align-middle text-center">
                    <input wire:model.blur="taxes" type="number" id="taxes" class="form-control w-75 d-inline" min="0" max="100">
                    %

                    @error('taxes')
                    <em class="invalid-feedback">
                        {{ $message }}
                    </em>
                    @enderror
                </td>
            </tr>
            <tr>
                <th colspan="4" class="align-middle text-end">
                    Total
                </th>
                <td class="text-center">
                    {{ Number::currency($total, 'LKR') }}
                    <input type="hidden" name="total_amount" value="{{ $total }}">
                </td>
            </tr>

        </tbody>
    </table>
</div>

@push('page-styles')
    <style>
        .product-search-input {
            min-width: 300px;
            position: relative;
        }

        .product-search-input:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        .product-search-results {
            background: white;
            border: 1px solid #0d6efd;
            border-top: none;
            border-radius: 0 0 0.375rem 0.375rem;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            display: none;
        }

        .product-search-item {
            cursor: pointer;
            transition: all 0.15s ease-in-out;
            border-left: 3px solid transparent;
        }

        .product-search-item:hover {
            background-color: #f8f9fa !important;
            border-left-color: #0d6efd;
            transform: translateX(2px);
        }

        .product-search-item.active {
            background-color: #0d6efd !important;
            color: white !important;
            border-left-color: #0a58ca;
        }

        .product-search-item.active .text-success {
            color: #90ee90 !important;
        }

        .product-search-item.active .text-muted {
            color: #e9ecef !important;
        }

        .product-search-item:last-child {
            border-bottom: none !important;
        }

        .hover-bg-light:hover {
            background-color: #f8f9fa;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        /* Highlight search matches */
        mark.bg-warning {
            background-color: #fff3cd !important;
            color: #664d03 !important;
            padding: 1px 2px;
            border-radius: 2px;
            font-weight: bold;
        }

        .product-search-item.active mark.bg-warning {
            background-color: #ffc107 !important;
            color: #000 !important;
        }

        /* Loading spinner */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        /* Smooth animations */
        .product-search-results {
            animation: fadeIn 0.15s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Focus ring for better accessibility */
        .product-search-input:focus + .product-search-results {
            border-color: #0d6efd;
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .product-search-input {
                min-width: 200px;
            }

            .product-search-results {
                max-height: 250px;
            }

            .product-search-item {
                padding: 0.75rem !important;
            }
        }
    </style>
@endpush

@push('page-scripts')
    <script>
        let searchTimeouts = {};

        // Hide search results for a specific index
        function hideSearchResults(index) {
            const searchResults = document.getElementById('search_results_' + index);
            if (searchResults) {
                setTimeout(() => {
                    searchResults.style.display = 'none';
                }, 150);
            }
        }

        // Handle search input focus and blur events
        document.addEventListener('DOMContentLoaded', function() {
            initializeHotSearch();
        });

        function initializeHotSearch() {
            const searchInputs = document.querySelectorAll('.product-search-input');

            searchInputs.forEach(function(input) {
                const index = input.id.replace('product_search_', '');

                // Clear any existing timeout for this input
                if (searchTimeouts[index]) {
                    clearTimeout(searchTimeouts[index]);
                }

                // Show results on focus (always show, either first 5 products or search results)
                input.addEventListener('focus', function() {
                    // Trigger Livewire focus event to show initial products
                    if (window.Livewire) {
                        this.dispatchEvent(new Event('focus'));
                    }

                    const searchResults = document.getElementById('search_results_' + index);
                    if (searchResults) {
                        searchResults.style.display = 'block';
                    }
                });

                // Hide results on blur (with delay to allow clicks)
                input.addEventListener('blur', function() {
                    // Let Livewire handle the blur event first
                    setTimeout(() => {
                        const searchResults = document.getElementById('search_results_' + index);
                        if (searchResults) {
                            searchResults.style.display = 'none';
                        }
                    }, 200);
                });

                // Don't interfere with input - let Livewire handle it
                // Just manage the display visibility based on content
                input.addEventListener('input', function() {
                    // Livewire will handle the update automatically with wire:model.live.debounce
                });

                // Handle keyboard navigation
                input.addEventListener('keydown', function(e) {
                    const searchResults = document.getElementById('search_results_' + index);
                    if (!searchResults || searchResults.style.display === 'none') return;

                    const items = searchResults.querySelectorAll('.product-search-item');
                    const activeItem = searchResults.querySelector('.product-search-item.active');
                    let activeIndex = activeItem ? Array.from(items).indexOf(activeItem) : -1;

                    switch(e.key) {
                        case 'ArrowDown':
                            e.preventDefault();
                            activeIndex = (activeIndex + 1) % items.length;
                            updateActiveItem(items, activeIndex);
                            break;

                        case 'ArrowUp':
                            e.preventDefault();
                            activeIndex = activeIndex <= 0 ? items.length - 1 : activeIndex - 1;
                            updateActiveItem(items, activeIndex);
                            break;

                        case 'Enter':
                            e.preventDefault();
                            if (activeItem) {
                                activeItem.click();
                            } else if (items.length > 0) {
                                items[0].click();
                            }
                            break;

                        case 'Escape':
                            searchResults.style.display = 'none';
                            break;
                    }
                });
            });
        }

        function updateActiveItem(items, activeIndex) {
            items.forEach((item, index) => {
                if (index === activeIndex) {
                    item.classList.add('active', 'bg-primary', 'text-white');
                } else {
                    item.classList.remove('active', 'bg-primary', 'text-white');
                }
            });
        }

        // Reinitialize when Livewire updates
        document.addEventListener('livewire:updated', function() {
            setTimeout(function() {
                initializeHotSearch();
            }, 100);
        });

        // Handle Livewire morphing
        document.addEventListener('livewire:morph', function() {
            setTimeout(function() {
                initializeHotSearch();
            }, 100);
        });

        // Listen for Livewire events to hide search results
        document.addEventListener('livewire:initialized', function() {
            Livewire.on('hide-search-results', function(data) {
                setTimeout(() => {
                    const searchResults = document.getElementById('search_results_' + data.index);
                    if (searchResults) {
                        searchResults.style.display = 'none';
                    }
                }, 150);
            });
        });

        // Handle clicks outside to close dropdowns
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.position-relative')) {
                const searchResults = document.querySelectorAll('.product-search-results');
                searchResults.forEach(function(result) {
                    result.style.display = 'none';
                });
            }
        });

        // Performance optimization: Throttle Livewire updates
        let livewireUpdateThrottle = {};

        function throttledLivewireUpdate(component, method, ...args) {
            const key = method + JSON.stringify(args);

            if (livewireUpdateThrottle[key]) {
                clearTimeout(livewireUpdateThrottle[key]);
            }

            livewireUpdateThrottle[key] = setTimeout(() => {
                if (window.Livewire && component) {
                    component.call(method, ...args);
                }
                delete livewireUpdateThrottle[key];
            }, 100);
        }
    </script>
@endpush
