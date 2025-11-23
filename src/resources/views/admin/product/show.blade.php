@extends('layouts.app')

@section('content')

<div class="product-detail-container">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title">Product Details</h2>
                <p class="text-muted">View complete product information</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.product.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Inventory
                </a>
                <a href="{{ route('admin.product.edit', $product->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Product
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Product Information Card -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-info-circle text-primary"></i> Product Information</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <!-- Product Name -->
                        <div class="col-md-6 mb-4">
                            <label class="detail-label">Product Name</label>
                            <h4 class="detail-value">{{ $product->name }}</h4>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6 mb-4">
                            <label class="detail-label">Status</label>
                            <div>
                                @if($product->status->value == 0)
                                    <span class="badge badge-lg bg-success">
                                        <i class="fas fa-check-circle"></i> {{ $product->status->description }}
                                    </span>
                                @elseif($product->status->value == 1)
                                    <span class="badge badge-lg bg-warning">
                                        <i class="fas fa-exclamation-triangle"></i> {{ $product->status->description }}
                                    </span>
                                @else
                                    <span class="badge badge-lg bg-danger">
                                        <i class="fas fa-times-circle"></i> {{ $product->status->description }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Initial Stock -->
                        <div class="col-md-4 mb-4">
                            <label class="detail-label">Initial Stock</label>
                            <h5 class="detail-value text-info">
                                <i class="fas fa-cubes"></i> {{ $product->initial_stock }} {{ \App\Enums\UnitOfMeasurement::getDescription($product->unit_of_measurement->value) }}
                            </h5>
                        </div>

                        <!-- Current Stock -->
                        <div class="col-md-4 mb-4">
                            <label class="detail-label">Current Stock</label>
                            <h5 class="detail-value {{ $product->remaining_stock == 0 ? 'text-danger' : ($product->remaining_stock <= ($product->initial_stock * 0.2) ? 'text-warning' : 'text-success') }}">
                                <i class="fas fa-layer-group"></i> {{ $product->remaining_stock }} {{ \App\Enums\UnitOfMeasurement::getDescription($product->unit_of_measurement->value) }}
                            </h5>
                        </div>

                        <!-- Stock Out -->
                        <div class="col-md-4 mb-4">
                            <label class="detail-label">Stock Out</label>
                            <h5 class="detail-value text-danger">
                                <i class="fas fa-minus-circle"></i> {{ $product->stock_out }} {{ \App\Enums\UnitOfMeasurement::getDescription($product->unit_of_measurement->value) }}
                            </h5>
                        </div>

                        <!-- Unit of Measurement -->
                        <div class="col-md-6 mb-4">
                            <label class="detail-label">Unit of Measurement</label>
                            <h5 class="detail-value">
                                <span class="badge badge-lg bg-secondary">
                                    <i class="fas fa-balance-scale"></i> {{ strtoupper(\App\Enums\UnitOfMeasurement::getDescription($product->unit_of_measurement->value)) }}
                                </span>
                            </h5>
                        </div>

                        <!-- Stock Percentage -->
                        <div class="col-md-6 mb-4">
                            <label class="detail-label">Stock Level</label>
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar {{ $product->stock_percentage > 50 ? 'bg-success' : ($product->stock_percentage > 20 ? 'bg-warning' : 'bg-danger') }}" 
                                     role="progressbar" 
                                     style="width: {{ $product->stock_percentage }}%;" 
                                     aria-valuenow="{{ $product->stock_percentage }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                    <strong>{{ $product->stock_percentage }}%</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Expiration Date -->
                        <div class="col-md-6 mb-4">
                            <label class="detail-label">Expiration Date</label>
                            <h5 class="detail-value {{ $product->is_expired ? 'text-danger' : ($product->is_expiring_soon ? 'text-warning' : '') }}">
                                @if($product->is_expired)
                                    <i class="fas fa-times-circle"></i>
                                @elseif($product->is_expiring_soon)
                                    <i class="fas fa-exclamation-triangle"></i>
                                @else
                                    <i class="fas fa-calendar-alt"></i>
                                @endif
                                {{ $product->expiration_date->format('F d, Y') }}
                            </h5>
                            <small class="text-muted">
                                @if($product->is_expired)
                                    <i class="fas fa-exclamation-circle text-danger"></i> Expired {{ abs($product->days_until_expiration) }} days ago
                                @elseif($product->is_expiring_soon)
                                    <i class="fas fa-clock text-warning"></i> Expires in {{ $product->days_until_expiration }} days
                                @else
                                    {{ $product->days_until_expiration }} days remaining
                                @endif
                            </small>
                        </div>

                        <!-- Date Added -->
                        <div class="col-md-6 mb-4">
                            <label class="detail-label">Date Added</label>
                            <h5 class="detail-value">
                                <i class="fas fa-clock"></i> {{ $product->created_at->format('F d, Y') }}
                            </h5>
                            <small class="text-muted">{{ $product->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats & Actions -->
        <div class="col-lg-4">
            <!-- Quick Stats Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-chart-bar text-success"></i> Quick Stats</h5>
                </div>
                <div class="card-body p-3">
                    <!-- Stock Turnover -->
                    <div class="stat-item">
                        <div class="stat-icon bg-primary">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <div class="stat-content">
                            <small>Stock Turnover</small>
                            <h5>{{ round(($product->stock_out / $product->initial_stock) * 100, 1) }}%</h5>
                        </div>
                    </div>

                    <!-- Days Since Added -->
                    <div class="stat-item">
                        <div class="stat-icon bg-info">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div class="stat-content">
                            <small>Days Since Added</small>
                            <h5>{{ $product->created_at->diffInDays(now()) }} days</h5>
                        </div>
                    </div>

                    <!-- Days Until Expiration -->
                    <div class="stat-item">
                        <div class="stat-icon {{ $product->is_expired ? 'bg-danger' : ($product->is_expiring_soon ? 'bg-warning' : 'bg-success') }}">
                            <i class="fas fa-hourglass-half"></i>
                        </div>
                        <div class="stat-content">
                            <small>Until Expiration</small>
                            <h5>{{ $product->is_expired ? 'Expired' : $product->days_until_expiration . ' days' }}</h5>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-tools text-warning"></i> Actions</h5>
                </div>
                <div class="card-body p-3">
                    <a href="{{ route('admin.product.edit', $product->id) }}" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-edit"></i> Edit Product
                    </a>
                    <button onclick="deleteProduct('{{ $product->id }}', '{{ $product->name }}')" class="btn btn-danger w-100 mb-2">
                        <i class="fas fa-trash"></i> Delete Product
                    </button>
                    <a href="{{ route('admin.product.index') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-list"></i> Back to List
                    </a>
                </div>
            </div>

            @if($product->is_expired || $product->is_expiring_soon || $product->isLowOnStock() || $product->isOutOfStock())
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-bell text-danger"></i> Alerts</h5>
                </div>
                <div class="card-body p-3">
                    @if($product->is_expired)
                        <div class="alert alert-danger mb-2">
                            <i class="fas fa-times-circle"></i> <strong>Expired!</strong><br>
                            <small>This product expired {{ abs($product->days_until_expiration) }} days ago</small>
                        </div>
                    @endif

                    @if($product->is_expiring_soon && !$product->is_expired)
                        <div class="alert alert-warning mb-2">
                            <i class="fas fa-exclamation-triangle"></i> <strong>Expiring Soon!</strong><br>
                            <small>Expires in {{ $product->days_until_expiration }} days</small>
                        </div>
                    @endif

                    @if($product->isOutOfStock())
                        <div class="alert alert-danger mb-2">
                            <i class="fas fa-times-circle"></i> <strong>Out of Stock!</strong><br>
                            <small>Restock immediately</small>
                        </div>
                    @endif

                    @if($product->isLowOnStock())
                        <div class="alert alert-warning mb-2">
                            <i class="fas fa-exclamation-triangle"></i> <strong>Low Stock!</strong><br>
                            <small>Only {{ $product->remaining_stock }} {{ \App\Enums\UnitOfMeasurement::getDescription($product->unit_of_measurement->value) }} remaining</small>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .product-detail-container {
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
        font-size: 1rem;
    }

    .detail-label {
        font-size: 0.85rem;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
        display: block;
    }

    .detail-value {
        color: #2c3e50;
        font-weight: 700;
        margin: 0;
    }

    .badge-lg {
        padding: 0.6rem 1rem;
        font-size: 0.95rem;
        font-weight: 600;
    }

    .progress {
        border-radius: 10px;
        background-color: #e9ecef;
    }

    .progress-bar {
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
    }

    /* Quick Stats */
    .stat-item {
        display: flex;
        align-items: center;
        padding: 1rem;
        background: #f8f9fa;
        border-radius: 10px;
        margin-bottom: 0.75rem;
    }

    .stat-item:last-child {
        margin-bottom: 0;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.3rem;
        margin-right: 1rem;
    }

    .stat-content small {
        color: #6c757d;
        font-size: 0.8rem;
        display: block;
    }

    .stat-content h5 {
        margin: 0;
        color: #2c3e50;
        font-weight: 700;
    }

    .btn {
        padding: 0.65rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .alert {
        border-radius: 8px;
        font-size: 0.9rem;
    }
</style>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function deleteProduct(id, name) {
    Swal.fire({
        title: 'Delete Product?',
        html: `Are you sure you want to delete <strong>${name}</strong>?<br>This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-trash"></i> Yes, Delete',
        cancelButtonText: '<i class="fas fa-times-circle"></i> Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/products/${id}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const methodField = document.createElement('input');
            methodField.type = 'hidden';
            methodField.name = '_method';
            methodField.value = 'DELETE';
            
            form.appendChild(csrfToken);
            form.appendChild(methodField);
            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
@endpush