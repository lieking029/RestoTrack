@extends('layouts.app')

@section('content')

<div class="log-waste-container">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title">Log Waste</h2>
                <p class="text-muted">Record waste for inventory items</p>
            </div>
            <a href="{{ route('admin.waste-management.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-trash-alt text-danger"></i> Waste Details</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.waste-management.store') }}" method="POST">
                        @csrf

                        <!-- Product Selection -->
                        <div class="mb-4">
                            <label for="product_id" class="form-label">
                                Product <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-lg @error('product_id') is-invalid @enderror"
                                    id="product_id" name="product_id" required>
                                <option value="">Select a product...</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}"
                                            data-stock="{{ $p->remaining_stock }}"
                                            data-unit="{{ $p->unit_of_measurement->value ?? '' }}"
                                            {{ (old('product_id', $product?->id) == $p->id) ? 'selected' : '' }}>
                                        {{ $p->name }} ({{ $p->remaining_stock }} {{ $p->unit_of_measurement->value ?? '' }} available)
                                    </option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Quantity -->
                        <div class="mb-4">
                            <label for="quantity" class="form-label">
                                Quantity <span class="text-danger">*</span>
                            </label>
                            <div class="input-group input-group-lg">
                                <input type="number" class="form-control @error('quantity') is-invalid @enderror"
                                       id="quantity" name="quantity" value="{{ old('quantity', 1) }}"
                                       min="1" max="{{ $product?->remaining_stock ?? 9999 }}" required>
                                <span class="input-group-text" id="unitDisplay">units</span>
                            </div>
                            <small class="text-muted">
                                Available: <span id="availableStock">{{ $product?->remaining_stock ?? '-' }}</span>
                            </small>
                            @error('quantity')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Reason -->
                        <div class="mb-4">
                            <label for="reason" class="form-label">
                                Waste Reason <span class="text-danger">*</span>
                            </label>
                            <select class="form-select form-select-lg @error('reason') is-invalid @enderror"
                                    id="reason" name="reason" required>
                                <option value="">Select reason...</option>
                                @foreach($wasteReasons as $key => $label)
                                    <option value="{{ $key }}" {{ old('reason') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror"
                                      id="notes" name="notes" rows="3"
                                      placeholder="Add any additional details about the waste...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Actions -->
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.waste-management.index') }}" class="btn btn-light btn-lg">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-danger btn-lg">
                                <i class="fas fa-trash-alt"></i> Log Waste
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-info-circle text-info"></i> Waste Categories</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <span class="badge bg-dark me-2">Expired</span>
                            Products past their expiration date
                        </li>
                        <li class="mb-3">
                            <span class="badge bg-warning text-dark me-2">Spoiled</span>
                            Products showing signs of spoilage
                        </li>
                        <li class="mb-3">
                            <span class="badge bg-danger me-2">Damaged</span>
                            Products damaged during handling
                        </li>
                        <li class="mb-3">
                            <span class="badge bg-info me-2">Contaminated</span>
                            Products contaminated or unsafe
                        </li>
                        <li class="mb-3">
                            <span class="badge bg-primary me-2">Quality Issue</span>
                            Products not meeting quality standards
                        </li>
                        <li class="mb-3">
                            <span class="badge bg-secondary me-2">Overstock</span>
                            Excess inventory being disposed
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card border-0 shadow-sm bg-danger-subtle">
                <div class="card-body">
                    <h6 class="text-danger"><i class="fas fa-exclamation-triangle"></i> Important</h6>
                    <p class="small mb-0">
                        Logging waste will permanently reduce the product's stock count.
                        This action is tracked for auditing purposes.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .log-waste-container {
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

    .form-label {
        font-weight: 600;
        color: #2c3e50;
    }

    .form-select:focus,
    .form-control:focus {
        border-color: #1a4d2e;
        box-shadow: 0 0 0 0.2rem rgba(26, 77, 46, 0.15);
    }

    .bg-danger-subtle {
        background-color: #f8d7da !important;
    }

    @media (max-width: 768px) {
        .log-waste-container {
            padding: 1rem;
        }
    }
</style>

<script>
    document.getElementById('product_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const stock = selectedOption.dataset.stock || '-';
        const unit = selectedOption.dataset.unit || 'units';

        document.getElementById('availableStock').textContent = stock;
        document.getElementById('unitDisplay').textContent = unit;
        document.getElementById('quantity').max = stock;
    });
</script>

@endsection
