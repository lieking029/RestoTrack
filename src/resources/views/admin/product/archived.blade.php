@extends('layouts.app')

@section('content')
    <div class="inventory-container">
        <!-- Page Header -->
        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="page-title">Archived Inventory</h2>
                    <p class="text-muted">Restore archived products back to your inventory</p>
                </div>
                <a href="{{ route('admin.product.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Inventory
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card stat-secondary">
                    <div class="stat-icon">
                        <i class="fas fa-archive"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $archivedProducts->count() }}</h3>
                        <p>Archived Products</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0"><i class="fas fa-archive text-secondary"></i> Archived Products</h5>
            </div>
            <div class="card-body">
                @if($archivedProducts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="archivedTable">
                            <thead>
                                <tr>
                                    <th>Product Name</th>
                                    <th class="text-center">Initial Stock</th>
                                    <th class="text-center">Last Stock</th>
                                    <th class="text-center">Unit</th>
                                    <th class="text-center">Expiration Date</th>
                                    <th class="text-center">Archived On</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($archivedProducts as $product)
                                    <tr>
                                        <td>
                                            <div class="product-name">{{ $product->name }}</div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">{{ $product->initial_stock }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $product->remaining_stock > 0 ? 'success' : 'danger' }}">
                                                {{ $product->remaining_stock }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-secondary">
                                                {{ \App\Enums\UnitOfMeasurement::getLabel($product->unit_of_measurement->value) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($product->expiration_date)
                                                <span class="{{ $product->is_expired ? 'text-danger fw-bold' : 'text-muted' }}">
                                                    {{ $product->expiration_date->format('M d, Y') }}
                                                </span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <small class="text-muted">{{ $product->deleted_at->format('M d, Y h:i A') }}</small>
                                        </td>
                                        <td class="text-center">
                                            <button type="button"
                                                    class="btn btn-sm btn-success"
                                                    title="Restore Product"
                                                    onclick="restoreProduct('{{ $product->id }}', '{{ $product->name }}')">
                                                <i class="fas fa-undo"></i> Restore
                                            </button>
                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    title="Permanently Delete"
                                                    onclick="forceDeleteProduct('{{ $product->id }}', '{{ $product->name }}')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-archive fa-4x mb-3"></i>
                        <h5>No Archived Products</h5>
                        <p>Products you archive will appear here for restoration.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .inventory-container {
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

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
            flex-shrink: 0;
        }

        .stat-secondary .stat-icon {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }

        .stat-info h3 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            color: #2c3e50;
        }

        .stat-info p {
            margin: 0;
            color: #6c757d;
            font-size: 0.9rem;
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

        #archivedTable {
            font-size: 0.9rem;
        }

        #archivedTable thead th {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            border: none;
            padding: 1rem;
        }

        #archivedTable tbody tr {
            transition: all 0.2s ease;
        }

        #archivedTable tbody tr:hover {
            background-color: #f8f9fa;
        }

        #archivedTable tbody td {
            padding: 0.875rem;
            vertical-align: middle;
        }

        .product-name {
            font-weight: 600;
            color: #2c3e50;
        }
    </style>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function restoreProduct(id, name) {
            Swal.fire({
                title: 'Restore Product?',
                html: `Are you sure you want to restore <strong>${name}</strong> back to inventory?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-undo"></i> Yes, Restore',
                cancelButtonText: '<i class="fas fa-times-circle"></i> Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/product/${id}/restore`;

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    form.appendChild(csrfToken);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function forceDeleteProduct(id, name) {
            Swal.fire({
                title: 'Permanently Delete?',
                html: `Are you sure you want to <strong>permanently delete</strong> <strong>${name}</strong>?<br><br><span class="text-danger">This action cannot be undone.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash"></i> Yes, Delete Forever',
                cancelButtonText: '<i class="fas fa-times-circle"></i> Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/product/${id}/force-delete`;

                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';

                    form.appendChild(csrfToken);
                    form.appendChild(methodInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
@endpush
