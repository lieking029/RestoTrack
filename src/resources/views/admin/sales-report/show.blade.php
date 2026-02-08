@extends('layouts.app')

@section('content')

<div class="order-detail-container">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title">Order Details</h2>
                <p class="text-muted">Order #{{ substr($order->id, 0, 8) }}...</p>
            </div>
            <a href="{{ route('admin.sales-report.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Reports
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Order Info -->
        <div class="col-md-8">
            <!-- Order Items -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart text-primary"></i> Order Items</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th class="text-center">Qty</th>
                                <th class="text-end">Unit Price</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td><strong>{{ $item->name }}</strong></td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary">{{ $item->quantity }}</span>
                                    </td>
                                    <td class="text-end">₱{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end fw-bold">₱{{ number_format($item->total, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="3" class="text-end"><strong>Subtotal:</strong></td>
                                <td class="text-end">₱{{ number_format($order->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="3" class="text-end"><strong>Tax:</strong></td>
                                <td class="text-end text-info">₱{{ number_format($order->tax, 2) }}</td>
                            </tr>
                            <tr class="table-success">
                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                <td class="text-end fw-bold fs-5 text-success">₱{{ number_format($order->total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Payment Info -->
            @if($order->payments->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-credit-card text-success"></i> Payment Information</h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Method</th>
                                    <th class="text-end">Amount</th>
                                    <th>Processed By</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->payments as $payment)
                                    <tr>
                                        <td>
                                            <span class="badge bg-info">
                                                <i class="fas fa-money-bill"></i> {{ ucfirst($payment->method ?? 'Cash') }}
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold text-success">₱{{ number_format($payment->amount, 2) }}</td>
                                        <td>{{ $payment->processor->full_name ?? 'N/A' }}</td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $payment->created_at->format('M d, Y h:i A') }}
                                            </small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Order Summary -->
        <div class="col-md-4">
            <!-- Status Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-info-circle text-info"></i> Order Status</h5>
                </div>
                <div class="card-body text-center">
                    @php
                        $badgeClass = match ($order->status->value) {
                            \App\Enums\OrderStatus::COMPLETED => 'success',
                            \App\Enums\OrderStatus::PENDING => 'warning',
                            \App\Enums\OrderStatus::CONFIRMED => 'info',
                            \App\Enums\OrderStatus::INPREPARATION => 'primary',
                            \App\Enums\OrderStatus::READY => 'secondary',
                            \App\Enums\OrderStatus::CANCELLED => 'danger',
                            default => 'secondary'
                        };

                        $statusLabel = match ($order->status->value) {
                            \App\Enums\OrderStatus::COMPLETED => 'Completed',
                            \App\Enums\OrderStatus::PENDING => 'Pending',
                            \App\Enums\OrderStatus::CONFIRMED => 'Confirmed',
                            \App\Enums\OrderStatus::INPREPARATION => 'In Preparation',
                            \App\Enums\OrderStatus::READY => 'Ready',
                            \App\Enums\OrderStatus::CANCELLED => 'Cancelled',
                            default => 'Unknown'
                        };
                    @endphp
                    <span class="badge bg-{{ $badgeClass }} fs-5 px-4 py-2">
                        {{ $statusLabel }}
                    </span>
                </div>
            </div>

            <!-- Order Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-file-alt text-primary"></i> Order Details</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <small class="text-muted d-block">Order ID</small>
                            <strong class="text-break">{{ $order->id }}</strong>
                        </li>
                        <li class="mb-3">
                            <small class="text-muted d-block">Date & Time</small>
                            <strong>
                                <i class="fas fa-calendar-alt text-primary"></i>
                                {{ $order->created_at->format('M d, Y') }}
                                <br>
                                <i class="fas fa-clock text-primary"></i>
                                {{ $order->created_at->format('h:i A') }}
                            </strong>
                        </li>
                        <li class="mb-3">
                            <small class="text-muted d-block">Items Count</small>
                            <strong>{{ $order->items->count() }} item(s)</strong>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Staff Info -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-users text-success"></i> Staff Information</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @if($order->creator)
                            <li class="mb-3">
                                <small class="text-muted d-block">Created By</small>
                                <strong>
                                    <i class="fas fa-user text-primary"></i>
                                    {{ $order->creator->full_name }}
                                </strong>
                            </li>
                        @endif
                        @if($order->cashier)
                            <li class="mb-3">
                                <small class="text-muted d-block">Processed By (Cashier)</small>
                                <strong>
                                    <i class="fas fa-cash-register text-success"></i>
                                    {{ $order->cashier->full_name }}
                                </strong>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .order-detail-container {
        padding: 2rem;
        background-color: #f5f6fa;
        min-height: calc(100vh - 70px);
        margin-top: 70px;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
    }

    .card {
        border-radius: 15px;
    }

    .card-header {
        padding: 1.25rem 1.5rem;
        border-radius: 15px 15px 0 0 !important;
    }

    .card-header h5 {
        color: #2c3e50;
        font-weight: 700;
    }

    .card-body.p-0 .table th,
    .card-body.p-0 .table td {
        padding: 0.75rem 1rem;
    }

    .table tfoot tr:last-child td {
        border-bottom: none;
    }
</style>

@endsection
