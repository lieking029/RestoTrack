@extends('layouts.app')

@section('content')
    <div class="product-form-container">
        <!-- Page Header -->
        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="page-title">Edit Product</h2>
                    <p class="text-muted">Update product information</p>
                </div>
                <a href="{{ route('admin.product.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Inventory
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0">Product Information</h5>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('admin.product.update', $product->id) }}" method="POST" id="productForm">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Product Name -->
                        <div class="col-md-6 mb-4">
                            <label for="name" class="form-label">
                                Product Name <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-box"></i></span>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $product->name) }}"
                                    placeholder="Enter product name" required>
                            </div>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Initial Stock -->
                        <div class="col-md-6 mb-4">
                            <label for="initial_stock" class="form-label">
                                Initial Stock <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-cubes"></i></span>
                                <input type="number" class="form-control @error('initial_stock') is-invalid @enderror"
                                    id="initial_stock" name="initial_stock"
                                    value="{{ old('initial_stock', $product->initial_stock) }}"
                                    placeholder="Enter initial stock quantity" min="1" required>
                            </div>
                            @error('initial_stock')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Current Stock -->
                        <div class="col-md-6 mb-4">
                            <label for="remaining_stock" class="form-label">
                                Current Stock <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                                <input type="number" class="form-control @error('remaining_stock') is-invalid @enderror"
                                    id="remaining_stock" name="remaining_stock"
                                    value="{{ old('remaining_stock', $product->remaining_stock) }}"
                                    placeholder="Enter current stock quantity" min="0" required>
                            </div>
                            @error('remaining_stock')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Current available stock: {{ $product->remaining_stock }}
                                ({{ $product->stock_percentage }}%)</small>
                        </div>

                        <!-- Stock Out -->
                        <div class="col-md-6 mb-4">
                            <label for="stock_out" class="form-label">
                                Stock Out
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-minus-circle"></i></span>
                                <input type="number" class="form-control @error('stock_out') is-invalid @enderror"
                                    id="stock_out" name="stock_out" value="{{ old('stock_out', $product->stock_out) }}"
                                    placeholder="Enter stock out quantity" min="0" readonly>
                            </div>
                            @error('stock_out')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">This will be calculated automatically</small>
                        </div>

                        <!-- Unit of Measurement -->
                        <div class="col-md-6 mb-4">
                            <label for="unit_of_measurement" class="form-label">
                                Unit of Measurement <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-balance-scale"></i></span>
                                <select class="form-select @error('unit_of_measurement') is-invalid @enderror"
                                    id="unit_of_measurement" name="unit_of_measurement" required>
                                    <option value="">Select unit</option>
                                    @foreach ($unitOfMeasurements as $category => $units)
                                        <optgroup label="{{ $category }}">
                                            @foreach ($units as $value => $label)
                                                <option value="{{ $value }}"
                                                    {{ old('unit_of_measurement', $product->unit_of_measurement->value) == $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                            @error('unit_of_measurement')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Expiration Date -->
                        <div class="col-md-6 mb-4">
                            <label for="expiration_date" class="form-label">
                                Expiration Date <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                <input type="date" class="form-control @error('expiration_date') is-invalid @enderror"
                                    id="expiration_date" name="expiration_date"
                                    value="{{ old('expiration_date', $product->expiration_date->format('Y-m-d')) }}"
                                    min="{{ date('Y-m-d') }}" required>
                            </div>
                            @error('expiration_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @if ($product->is_expiring_soon)
                                <small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Expiring in
                                    {{ $product->days_until_expiration }} days</small>
                            @elseif($product->is_expired)
                                <small class="text-danger"><i class="fas fa-times-circle"></i> This product has
                                    expired</small>
                            @else
                                <small class="text-muted">{{ $product->days_until_expiration }} days remaining</small>
                            @endif
                        </div>
                    </div>

                    <!-- Current Status -->
                    <div class="alert alert-{{ $product->status->value == 0 ? 'success' : ($product->status->value == 1 ? 'warning' : 'danger') }} d-flex align-items-center"
                        role="alert">
                        <i
                            class="fas {{ $product->status->value == 0 ? 'fa-check-circle' : ($product->status->value == 1 ? 'fa-exclamation-triangle' : 'fa-times-circle') }} me-3 fs-5"></i>
                        <div>
                            <strong>Current Status:</strong> {{ $product->status->description }}
                            <br>
                            <small>Status will be updated automatically based on the remaining stock quantity.</small>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.product.index') }}" class="btn btn-light">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .product-form-container {
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

        .form-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
            color: var(--primary-green, #1a4d2e);
        }

        .form-control,
        .form-select {
            border-left: none;
            padding: 0.75rem 1rem;
            border-radius: 0 8px 8px 0;
        }

        .input-group .form-control:focus,
        .input-group .form-select:focus {
            border-color: var(--primary-green, #1a4d2e);
            box-shadow: 0 0 0 0.2rem rgba(26, 77, 46, 0.15);
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

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .invalid-feedback {
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
@endsection

@push('scripts')
    <script>
        // Auto-calculate stock_out when initial_stock or remaining_stock changes
        function calculateStockOut() {
            const initialStock = parseInt(document.getElementById('initial_stock').value) || 0;
            const remainingStock = parseInt(document.getElementById('remaining_stock').value) || 0;
            const stockOut = Math.max(0, initialStock - remainingStock);

            document.getElementById('stock_out').value = stockOut;
        }

        document.getElementById('initial_stock').addEventListener('input', calculateStockOut);
        document.getElementById('remaining_stock').addEventListener('input', calculateStockOut);

        document.getElementById('productForm').addEventListener('submit', function(e) {
            const initialStock = parseInt(document.getElementById('initial_stock').value);
            const remainingStock = parseInt(document.getElementById('remaining_stock').value);

            if (remainingStock > initialStock) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Stock',
                    text: 'Remaining stock cannot exceed initial stock',
                    confirmButtonColor: '#1a4d2e'
                });
            }
        });
    </script>
@endpush
