@extends('layouts.app')

@section('content')
    <div class="inventory-container">
        <!-- Page Header -->
        <div class="page-header mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="page-title">Inventory Management</h2>
                    <p class="text-muted">Manage your restaurant inventory and stock levels</p>
                </div>
                <a href="{{ route('admin.product.create') }}" class="btn btn-success">
                    <i class="fas fa-plus-circle"></i> Add New Product
                </a>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="stat-card stat-primary">
                    <div class="stat-icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ \App\Models\Product::count() }}</h3>
                        <p>Total Products</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="stat-card stat-success">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ \App\Models\Product::where('status', \App\Enums\InventoryStatus::OnStock)->count() }}</h3>
                        <p>In Stock</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="stat-card stat-warning">
                    <div class="stat-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ \App\Models\Product::where('status', \App\Enums\InventoryStatus::LowOnStock)->count() }}</h3>
                        <p>Low Stock</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="stat-card stat-danger">
                    <div class="stat-icon">
                        <i class="fas fa-times-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ \App\Models\Product::where('status', \App\Enums\InventoryStatus::NoStock)->count() }}</h3>
                        <p>Out of Stock</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="stat-card stat-orange">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ \App\Models\Product::whereDate('expiration_date', '<=', now()->addDays(7))->whereDate('expiration_date', '>=', now())->count() }}</h3>
                        <p>Expiring Soon</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                <div class="stat-card stat-dark">
                    <div class="stat-icon">
                        <i class="fas fa-ban"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ \App\Models\Product::whereDate('expiration_date', '<', now())->count() }}</h3>
                        <p>Expired</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- DataTable Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h5 class="mb-0">Product List</h5>
                    
                    <div class="d-flex gap-2 flex-wrap">
                        <!-- Stock Status Filters -->
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-secondary active" onclick="filterStatus('all')">
                                <i class="fas fa-list"></i> All
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-success" onclick="filterStatus('0')">
                                <i class="fas fa-check-circle"></i> In Stock
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="filterStatus('1')">
                                <i class="fas fa-exclamation-triangle"></i> Low Stock
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="filterStatus('2')">
                                <i class="fas fa-times-circle"></i> Out of Stock
                            </button>
                        </div>

                        <!-- Expiry Filters -->
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="filterExpiry('expiring')">
                                <i class="fas fa-clock"></i> Expiring Soon
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-dark" onclick="filterExpiry('expired')">
                                <i class="fas fa-ban"></i> Expired
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                {{ $dataTable->table(['class' => 'table table-hover table-bordered dt-responsive nowrap w-100']) }}
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

        /* Quick Stats Cards */
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

        .stat-primary .stat-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-success .stat-icon {
            background: linear-gradient(135deg, #1a4d2e 0%, #2d7a4e 100%);
        }

        .stat-warning .stat-icon {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .stat-danger .stat-icon {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        }

        .stat-orange .stat-icon {
            background: linear-gradient(135deg, #fa8e42 0%, #feb47b 100%);
        }

        .stat-dark .stat-icon {
            background: linear-gradient(135deg, #434343 0%, #000000 100%);
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

        /* DataTable Styling */
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

        #product_dataTable {
            font-size: 0.9rem;
        }

        #product_dataTable thead th {
            background: linear-gradient(135deg, #1a4d2e 0%, #2d7a4e 100%);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            border: none;
            padding: 1rem;
        }

        #product_dataTable tbody tr {
            transition: all 0.2s ease;
        }

        #product_dataTable tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        #product_dataTable tbody td {
            padding: 0.875rem;
            vertical-align: middle;
        }

        .product-name {
            font-weight: 600;
            color: #2c3e50;
        }

        /* DataTable Controls */
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            padding: 0.5rem 1rem;
        }

        .dataTables_wrapper .dataTables_filter input {
            margin-left: 0.5rem;
        }

        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #1a4d2e;
            box-shadow: 0 0 0 0.2rem rgba(26, 77, 46, 0.15);
        }

        /* Pagination */
        /* .dataTables_wrapper .dataTables_paginate .paginate_button {
            border-radius: 8px;
            margin: 0 2px;
            padding: 0.5rem 1rem;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: linear-gradient(135deg, #1a4d2e 0%, #2d7a4e 100%);
            color: white !important;
            border: none;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f0f7f4;
            color: #1a4d2e !important;
            border: none;
        } */

        /* Buttons */
        .dt-buttons {
            margin-bottom: 1rem;
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .dt-button {
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .dt-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        /* Filter Buttons */
        .btn-group .btn {
            transition: all 0.3s ease;
        }

        .btn-group .btn:hover {
            transform: translateY(-2px);
        }

        .btn-group .btn.active {
            font-weight: 700;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .stat-info h3 {
                font-size: 1.5rem;
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                font-size: 1.5rem;
            }
        }
    </style>
@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let currentStatusFilter = 'all';
        let currentExpiryFilter = null;

        function filterStatus(status) {
            currentStatusFilter = status;
            applyFilters();

            // Update active button styling for status group
            document.querySelectorAll('.btn-group:first-of-type .btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.closest('.btn').classList.add('active');

            // Clear expiry filter buttons
            document.querySelectorAll('.btn-group:last-of-type .btn').forEach(btn => {
                btn.classList.remove('active');
            });
            currentExpiryFilter = null;
        }

        function filterExpiry(type) {
            currentExpiryFilter = type;
            applyFilters();

            // Update active button styling for expiry group
            document.querySelectorAll('.btn-group:last-of-type .btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.closest('.btn').classList.add('active');

            // Clear status filter (except "All")
            document.querySelectorAll('.btn-group:first-of-type .btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector('.btn-group:first-of-type .btn:first-child').classList.add('active');
            currentStatusFilter = 'all';
        }

        function applyFilters() {
            const table = $('#product_dataTable').DataTable();

            table.columns().search('');

            if (currentStatusFilter !== 'all') {
                table.column(4).search(currentStatusFilter);
            }

            if (currentExpiryFilter) {
                table.column(5).search(currentExpiryFilter);
            }

            table.draw();
        }
    </script>
@endpush