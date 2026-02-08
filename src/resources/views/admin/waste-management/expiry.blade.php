@extends('layouts.app')

@section('content')

<div class="expiry-tracking-container">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title">Expiry Date Tracking</h2>
                <p class="text-muted">Monitor and manage product expiration dates</p>
            </div>
            <a href="{{ route('admin.waste-management.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Expiry Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-2 col-4 mb-3">
            <a href="{{ route('admin.waste-management.expiry', ['filter' => 'expired']) }}" class="text-decoration-none">
                <div class="expiry-card {{ $filter === 'expired' ? 'active' : '' }} critical">
                    <div class="expiry-icon">
                        <i class="fas fa-skull-crossbones"></i>
                    </div>
                    <h3>{{ $expirySummary['expired'] }}</h3>
                    <p>Expired</p>
                </div>
            </a>
        </div>
        <div class="col-md-2 col-4 mb-3">
            <a href="{{ route('admin.waste-management.expiry', ['filter' => 'today']) }}" class="text-decoration-none">
                <div class="expiry-card {{ $filter === 'today' ? 'active' : '' }} danger">
                    <div class="expiry-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <h3>{{ $expirySummary['expiring_today'] }}</h3>
                    <p>Today</p>
                </div>
            </a>
        </div>
        <div class="col-md-2 col-4 mb-3">
            <a href="{{ route('admin.waste-management.expiry', ['filter' => '3days']) }}" class="text-decoration-none">
                <div class="expiry-card {{ $filter === '3days' ? 'active' : '' }} warning">
                    <div class="expiry-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3>{{ $expirySummary['expiring_3_days'] }}</h3>
                    <p>3 Days</p>
                </div>
            </a>
        </div>
        <div class="col-md-2 col-4 mb-3">
            <a href="{{ route('admin.waste-management.expiry', ['filter' => '7days']) }}" class="text-decoration-none">
                <div class="expiry-card {{ $filter === '7days' ? 'active' : '' }} info">
                    <div class="expiry-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>{{ $expirySummary['expiring_7_days'] }}</h3>
                    <p>7 Days</p>
                </div>
            </a>
        </div>
        <div class="col-md-2 col-4 mb-3">
            <a href="{{ route('admin.waste-management.expiry', ['filter' => '30days']) }}" class="text-decoration-none">
                <div class="expiry-card {{ $filter === '30days' ? 'active' : '' }} success">
                    <div class="expiry-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3>{{ $expirySummary['expiring_30_days'] }}</h3>
                    <p>30 Days</p>
                </div>
            </a>
        </div>
        <div class="col-md-2 col-4 mb-3">
            <a href="{{ route('admin.waste-management.expiry', ['filter' => 'all']) }}" class="text-decoration-none">
                <div class="expiry-card {{ $filter === 'all' ? 'active' : '' }} primary">
                    <div class="expiry-icon">
                        <i class="fas fa-list"></i>
                    </div>
                    <h3>All</h3>
                    <p>Products</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Bulk Actions for Expired -->
    @if($filter === 'expired' && $products->count() > 0)
        <div class="card border-0 shadow-sm mb-4 bg-danger-subtle">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="text-danger mb-1"><i class="fas fa-exclamation-circle"></i> Expired Products Detected</h5>
                    <p class="mb-0 text-muted">These products have passed their expiration date and should be disposed of immediately.</p>
                </div>
                <form action="{{ route('admin.waste-management.bulk-dispose') }}" method="POST" id="bulkDisposeForm">
                    @csrf
                    @foreach($products as $product)
                        <input type="hidden" name="product_ids[]" value="{{ $product->id }}">
                    @endforeach
                    <button type="button" class="btn btn-danger" onclick="confirmBulkDispose()">
                        <i class="fas fa-trash-alt"></i> Dispose All ({{ $products->count() }})
                    </button>
                </form>
            </div>
        </div>
    @endif

    <!-- Products Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">
                <i class="fas fa-boxes text-primary"></i>
                {{ ucfirst(str_replace('days', ' Days', $filter)) }} Products
                <span class="badge bg-secondary ms-2">{{ $products->count() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if($products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Product</th>
                                <th class="text-center">Stock</th>
                                <th class="text-center">Expiry Date</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($products as $product)
                                @php
                                    $daysLeft = $product->days_until_expiration;
                                    $isExpired = $product->is_expired;
                                @endphp
                                <tr class="{{ $isExpired ? 'table-danger' : ($daysLeft <= 3 ? 'table-warning' : '') }}">
                                    <td>
                                        <strong>{{ $product->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $product->unit_of_measurement->value ?? '' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary fs-6">{{ $product->remaining_stock }}</span>
                                    </td>
                                    <td class="text-center">
                                        <i class="fas fa-calendar {{ $isExpired ? 'text-danger' : 'text-muted' }}"></i>
                                        {{ $product->expiration_date->format('M d, Y') }}
                                    </td>
                                    <td class="text-center">
                                        @if($isExpired)
                                            <span class="badge bg-danger">
                                                <i class="fas fa-skull-crossbones"></i> Expired ({{ abs($daysLeft) }} days ago)
                                            </span>
                                        @elseif($daysLeft === 0)
                                            <span class="badge bg-danger">
                                                <i class="fas fa-exclamation-circle"></i> Expires Today
                                            </span>
                                        @elseif($daysLeft <= 3)
                                            <span class="badge bg-warning text-dark">
                                                <i class="fas fa-exclamation-triangle"></i> {{ $daysLeft }} day(s) left
                                            </span>
                                        @elseif($daysLeft <= 7)
                                            <span class="badge bg-info">
                                                <i class="fas fa-clock"></i> {{ $daysLeft }} days left
                                            </span>
                                        @else
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle"></i> {{ $daysLeft }} days left
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.product.edit', $product->id) }}"
                                               class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($isExpired || $daysLeft <= 0)
                                                <button type="button" class="btn btn-sm btn-danger"
                                                        onclick="confirmDispose('{{ $product->id }}', '{{ $product->name }}')"
                                                        title="Dispose">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            @else
                                                <a href="{{ route('admin.waste-management.create', ['product_id' => $product->id]) }}"
                                                   class="btn btn-sm btn-outline-danger" title="Log Waste">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                    <h4>All Clear!</h4>
                    <p>No products found for this filter.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Dispose Form (Hidden) -->
<form id="disposeForm" action="" method="POST" style="display: none;">
    @csrf
</form>

<style>
    .expiry-tracking-container {
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

    /* Expiry Cards */
    .expiry-card {
        background: white;
        padding: 1.25rem;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        text-align: center;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        height: 100%;
    }

    .expiry-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .expiry-card.active {
        border-color: #2c3e50;
    }

    .expiry-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin: 0 auto 0.75rem;
        color: white;
    }

    .expiry-card h3 {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0;
        color: #2c3e50;
    }

    .expiry-card p {
        margin: 0;
        color: #6c757d;
        font-size: 0.85rem;
    }

    .expiry-card.critical .expiry-icon {
        background-color: #721c24;
    }

    .expiry-card.danger .expiry-icon {
        background-color: #dc3545;
    }

    .expiry-card.warning .expiry-icon {
        background-color: #ffc107;
        color: #000;
    }

    .expiry-card.info .expiry-icon {
        background-color: #17a2b8;
    }

    .expiry-card.success .expiry-icon {
        background-color: #28a745;
    }

    .expiry-card.primary .expiry-icon {
        background-color: #667eea;
    }

    /* Cards */
    .card {
        border-radius: 15px;
    }

    .card-header {
        padding: 1rem 1.25rem;
        border-radius: 15px 15px 0 0 !important;
    }

    .card-header h5 {
        color: #2c3e50;
        font-weight: 700;
    }

    /* Table */
    .table thead th {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
    }

    .table tbody td {
        vertical-align: middle;
        padding: 1rem;
    }

    .bg-danger-subtle {
        background-color: #f8d7da !important;
    }

    @media (max-width: 768px) {
        .expiry-tracking-container {
            padding: 1rem;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function confirmDispose(productId, productName) {
        Swal.fire({
            title: 'Dispose Product?',
            html: `Are you sure you want to dispose <strong>${productName}</strong>?<br><small class="text-muted">This will remove all remaining stock.</small>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash-alt"></i> Yes, Dispose',
            cancelButtonText: '<i class="fas fa-times"></i> Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('disposeForm');
                form.action = `/admin/waste-management/dispose/${productId}`;
                form.submit();
            }
        });
    }

    function confirmBulkDispose() {
        Swal.fire({
            title: 'Dispose All Expired Products?',
            html: 'This will dispose <strong>ALL expired products</strong> listed above.<br><small class="text-muted">This action cannot be undone.</small>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash-alt"></i> Yes, Dispose All',
            cancelButtonText: '<i class="fas fa-times"></i> Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('bulkDisposeForm').submit();
            }
        });
    }
</script>

@endsection
