<div>
    @if (!$paymentCompleted)
        <!-- Payment Form -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Complete Payment</h3>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Order Summary -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h4>Order Summary</h4>
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <td>Subtotal:</td>
                                    <td class="text-end">LKR {{ number_format($subTotal, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>VAT (15%):</td>
                                    <td class="text-end">LKR {{ number_format($vat, 2) }}</td>
                                </tr>
                                <tr class="fw-bold">
                                    <td>Total:</td>
                                    <td class="text-end">LKR {{ number_format($total, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <h4>Cart Items ({{ count($cartItems) }})</h4>
                        <div class="list-group list-group-flush" style="max-height: 200px; overflow-y: auto;">
                            @foreach ($cartItems as $item)
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    <div>
                                        <strong>{{ $item['name'] ?? 'Unknown Product' }}</strong><br>
                                        <small class="text-muted">{{ $item['quantity'] ?? 0 }} × LKR {{ number_format($item['price'] ?? 0, 2) }}</small>
                                    </div>
                                    <span class="badge bg-primary">LKR {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 0), 2) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <form wire:submit.prevent="processPayment">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Customer *</label>
                                <select class="form-select @error('customerId') is-invalid @enderror" wire:model="customerId" required>
                                    <option value="">Select Customer</option>
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}">{{ $customer->name }} - {{ $customer->phone }}</option>
                                    @endforeach
                                </select>
                                @error('customerId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Payment Method *</label>
                                <select class="form-select @error('paymentType') is-invalid @enderror" wire:model="paymentType" required>
                                    @foreach ($paymentTypes as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('paymentType')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Payment Amount *</label>
                                <div class="input-group">
                                    <span class="input-group-text">LKR</span>
                                    <input type="number"
                                           class="form-control @error('paymentAmount') is-invalid @enderror"
                                           wire:model.live="paymentAmount"
                                           step="0.01"
                                           min="0"
                                           required>
                                </div>
                                @error('paymentAmount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if ($paymentAmount > $total)
                                    <small class="text-success">
                                        Change: LKR {{ number_format($paymentAmount - $total, 2) }}
                                    </small>
                                @elseif ($paymentAmount < $total && $paymentAmount > 0)
                                    <small class="text-danger">
                                        Insufficient amount. Required: LKR {{ number_format($total, 2) }}
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit"
                                class="btn btn-success btn-lg px-4"
                                wire:loading.attr="disabled"
                                wire:target="processPayment"
                                {{ $isProcessing ? 'disabled' : '' }}>
                            <div wire:loading.remove wire:target="processPayment">
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                    <path d="M5 12l5 5l10 -10"/>
                                </svg>
                                Complete Payment
                            </div>
                            <div wire:loading wire:target="processPayment">
                                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                Processing...
                            </div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <!-- Payment Success -->
        <div class="card border-success">
            <div class="card-header bg-success text-white">
                <h3 class="card-title mb-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon me-2" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                        <path d="M5 12l5 5l10 -10"/>
                    </svg>
                    Payment Completed Successfully!
                </h3>
            </div>
            <div class="card-body">
                @if ($orderDetails)
                    <div class="row">
                        <div class="col-md-6">
                            <h4>Transaction Details</h4>
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td><strong>Invoice No:</strong></td>
                                        <td>{{ $orderDetails['invoice_no'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Transaction ID:</strong></td>
                                        <td>{{ $orderDetails['transaction_id'] }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Customer:</strong></td>
                                        <td>{{ $orderDetails['customer']->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Payment Method:</strong></td>
                                        <td>{{ $paymentType }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Date & Time:</strong></td>
                                        <td>{{ $orderDetails['created_at']->format('Y-m-d H:i:s') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <h4>Payment Summary</h4>
                            <table class="table table-borderless">
                                <tbody>
                                    <tr>
                                        <td><strong>Total Amount:</strong></td>
                                        <td>LKR {{ number_format($orderDetails['total_amount'], 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Amount Paid:</strong></td>
                                        <td>LKR {{ number_format($orderDetails['payment_amount'], 2) }}</td>
                                    </tr>
                                    @if ($orderDetails['change_amount'] > 0)
                                        <tr class="text-success">
                                            <td><strong>Change Due:</strong></td>
                                            <td><strong>LKR {{ number_format($orderDetails['change_amount'], 2) }}</strong></td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <a href="{{ route('orders.show', $orderDetails['order_id']) }}" class="btn btn-primary me-2">
                            View Order Details
                        </a>
                        <button type="button" class="btn btn-outline-success" wire:click="resetPayment">
                            Process New Payment
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
