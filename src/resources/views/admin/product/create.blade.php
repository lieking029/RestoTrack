@extends('layouts.app')

@section('content')
    <div class="product-form-container">
        <!-- Page Header -->
        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="page-title">Add New Product</h2>
                    <p class="text-muted">Add a new product to your inventory</p>
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
                <form action="{{ route('admin.product.store') }}" method="POST" id="productForm">
                    @csrf

                    <div class="row">
                        <!-- Product Name -->
                        <div class="col-md-6 mb-4">
                            <label for="name" class="form-label">
                                Product Name <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-box"></i></span>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name') }}"
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
                                    id="initial_stock" name="initial_stock" value="{{ old('initial_stock') }}"
                                    placeholder="Enter initial stock quantity" min="1" required>
                            </div>
                            @error('initial_stock')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
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
                                                    {{ old('unit_of_measurement') == $value ? 'selected' : '' }}>
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
                                    id="expiration_date" name="expiration_date" value="{{ old('expiration_date') }}"
                                    min="{{ date('Y-m-d') }}" required>
                            </div>
                            @error('expiration_date')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Select the expiration date of this product</small>
                        </div>
                    </div>

                    <!-- Info Alert -->
                    <div class="alert alert-info d-flex align-items-center" role="alert">
                        <i class="fas fa-info-circle me-3 fs-5"></i>
                        <div>
                            <strong>Note:</strong> The remaining stock will be automatically set to equal the initial stock.
                            Stock status will be calculated automatically based on the remaining quantity.
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('admin.product.index') }}" class="btn btn-light">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Save Product
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

        .input-group .form-control:focus~.input-group-text,
        .form-control:focus+.input-group-text {
            border-color: var(--primary-green, #1a4d2e);
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

        .btn-success {
            background: linear-gradient(135deg, var(--primary-green, #1a4d2e) 0%, var(--light-green, #2d7a4e) 100%);
            border: none;
        }

        .alert-info {
            background-color: #e3f2fd;
            border-color: #90caf9;
            color: #0d47a1;
            border-radius: 8px;
        }

        .invalid-feedback {
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
@endsection

@push('scripts')
    <script>
        document.getElementById('productForm').addEventListener('submit', function(e) {
            const initialStock = document.getElementById('initial_stock').value;

            if (initialStock < 1) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Stock',
                    text: 'Initial stock must be at least 1',
                    confirmButtonColor: '#1a4d2e'
                });
            }
        });

        document.getElementById('name').addEventListener('blur', function() {
            this.value = this.value.replace(/\b\w/g, l => l.toUpperCase());
        });
    </script>
@endpush
