@extends('layouts.nexora')

@section('content')
<div class="page-body">
    <div class="container-xl">
        <x-alert/>

        <div class="row row-deck row-cards">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            {{ __('Edit Order') }}
                        </h3>
                    </div>

                    <form action="{{ route('orders.update', $order) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label required" for="customer_id">
                                            {{ __('Customer') }}
                                        </label>

                                        <select id="customer_id" name="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                                            <option value="">Select a customer</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}" @selected(old('customer_id', $order->customer_id) == $customer->id)>
                                                    {{ $customer->name }}@if($customer->phone) - {{ $customer->phone }}@endif
                                                </option>
                                            @endforeach
                                        </select>

                                        @error('customer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label required" for="order_date">
                                            {{ __('Order Date') }}
                                        </label>

                                        <input id="order_date" name="order_date" type="date"
                                               class="form-control @error('order_date') is-invalid @enderror"
                                               value="{{ old('order_date', $order->order_date) }}" required>

                                        @error('order_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="vat">
                                            {{ __('VAT (%)') }}
                                        </label>

                                        <input id="vat" name="vat" type="number" step="0.01" min="0" max="100"
                                               class="form-control @error('vat') is-invalid @enderror"
                                               value="{{ old('vat', $order->vat) }}">

                                        @error('vat')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label class="form-label required" for="total">
                                            {{ __('Total Amount') }}
                                        </label>

                                        <input id="total" name="total" type="number" step="0.01" min="0"
                                               class="form-control @error('total') is-invalid @enderror"
                                               value="{{ old('total', $order->total) }}" required>

                                        @error('total')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer text-end">
                            <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                {{ __('Update Order') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Order Summary') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <strong>Invoice No:</strong> {{ $order->invoice_no }}
                        </div>
                        <div class="mb-2">
                            <strong>Status:</strong>
                            <span class="badge bg-success">{{ $order->order_status->label() }}</span>
                        </div>
                        <div class="mb-2">
                            <strong>Total Products:</strong> {{ $order->total_products }}
                        </div>
                        <div class="mb-2">
                            <strong>Payment Type:</strong> {{ $order->payment_type }}
                        </div>
                        <div>
                            <strong>Created:</strong> {{ $order->created_at->format('M d, Y H:i') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
