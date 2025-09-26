@extends('layouts.tabler')

@section('content')
<div class="page-body">
    <div class="container-xl">

        <x-alert/>

        <div class="row row-cards">
            <form action="{{ route('invoice.create') }}" method="POST">
                @csrf
                <div class="row">

                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <div>
                                    <h3 class="card-title">
                                        {{ __('Create Order') }}
                                    </h3>
                                </div>

                                <div class="card-actions btn-actions">
                                    {{--- {{ URL::previous() }} ---}}
                                    <a href="{{ route('orders.index') }}" class="btn-action">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M18 6l-12 12"></path><path d="M6 6l12 12"></path></svg>
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">

                                <!-- Hidden date field with current date -->
                                <input name="date" id="date" type="hidden" value="{{ now()->format('Y-m-d') }}">

                                <!-- Hidden reference field with default value -->
                                <input name="reference" type="hidden" value="ORDR">

                                <div class="row gx-3 mb-3">
                                    <div class="col-md-12">
                                        <label for="customer_id" class="form-label required">
                                            {{ __('Customers') }}
                                        </label>

                                        <div class="input-group">
                                            <select id="customer_id" name="customer_id" placeholder="Select Customer" autocomplete="off"
                                                    class="form-control form-select @error('customer_id') is-invalid @enderror"
                                            >
                                                <option value="">
                                                    Select a customer...
                                                </option>

                                                @foreach($customers as $customer)
                                                    <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>
                                                        {{ $customer->name }}
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
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>

                                <livewire:order-form :cart-instance="'order'" />
                                {{-- livewire:product-cart :cartInstance="'orders'"/>--}}
                            </div>

                            <div class="card-footer text-end">
                                {{--- onclick="return confirm('Are you sure you want to purchase?')" ---}}
                                {{--- @disabled($errors->isNotEmpty()) ---}}
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Create Invoice') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('page-styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
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
            const currentTimeDisplay = document.createElement('small');
            currentTimeDisplay.className = 'text-muted';
            currentTimeDisplay.innerHTML = `<i class="ti ti-clock"></i> Order created on: ${dateTime.formatted}`;

            // Find a place to insert the time display (after the form header)
            const cardHeader = document.querySelector('.card-header');
            if (cardHeader) {
                const timeContainer = document.createElement('div');
                timeContainer.className = 'mt-2';
                timeContainer.appendChild(currentTimeDisplay);
                cardHeader.appendChild(timeContainer);
            }
        });

        // Initialize Tom Select for customer dropdown
        new TomSelect("#customer_id", {
            create: true,
            sortField: {
                field: "text",
                direction: "asc"
            }
        });

        // Optional: Update time every second (real-time clock)
        setInterval(function() {
            const dateTime = getCurrentDateTime();
            const timeDisplay = document.querySelector('.current-time-display');
            if (timeDisplay) {
                timeDisplay.textContent = dateTime.formatted;
            }
        }, 1000);
    </script>
@endpush
