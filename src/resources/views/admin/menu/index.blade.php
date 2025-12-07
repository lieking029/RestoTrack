@extends('layouts.app')

@section('content')

<div class="menu-index-container">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title">Menu Management</h2>
                <p class="text-muted">Manage your restaurant menu items and dishes</p>
            </div>
            <a href="{{ route('admin.menu.create') }}" class="btn btn-success">
                <i class="fas fa-plus-circle"></i> Add Menu Item
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stat-card stat-primary">
                <div class="stat-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ \App\Models\Menu::count() }}</h3>
                    <p>Total Items</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stat-card stat-success">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ \App\Models\Menu::where('status', \App\Enums\MenuStatus::Available)->count() }}</h3>
                    <p>Available</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stat-card stat-danger">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ \App\Models\Menu::where('status', \App\Enums\MenuStatus::Unavailable)->count() }}</h3>
                    <p>Unavailable</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stat-card stat-info">
                <div class="stat-icon">
                    <i class="fas fa-utensils"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ \App\Models\Menu::where('category', \App\Enums\MenuType::MainCourse)->count() }}</h3>
                    <p>Main Course</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stat-card stat-warning">
                <div class="stat-icon">
                    <i class="fas fa-ice-cream"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ \App\Models\Menu::where('category', \App\Enums\MenuType::Dessert)->count() }}</h3>
                    <p>Desserts</p>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="stat-card stat-dark">
                <div class="stat-icon">
                    <i class="fas fa-coffee"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ \App\Models\Menu::where('category', \App\Enums\MenuType::Beverage)->count() }}</h3>
                    <p>Beverages</p>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTable Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h5 class="mb-0">Menu Items</h5>
                
                <div class="d-flex gap-2 flex-wrap">
                    <!-- Status Filters -->
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-secondary active" onclick="filterStatus('all')">
                            <i class="fas fa-list"></i> All
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="filterStatus('0')">
                            <i class="fas fa-check-circle"></i> Available
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="filterStatus('1')">
                            <i class="fas fa-times-circle"></i> Unavailable
                        </button>
                    </div>

                    <!-- Category Filters -->
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="filterCategory('0')">
                            <i class="fas fa-salad"></i> Appetizers
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="filterCategory('1')">
                            <i class="fas fa-utensils"></i> Main Course
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="filterCategory('2')">
                            <i class="fas fa-ice-cream"></i> Desserts
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success" onclick="filterCategory('3')">
                            <i class="fas fa-coffee"></i> Beverages
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="filterCategory('4')">
                            <i class="fas fa-cookie-bite"></i> Snacks
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
    .menu-index-container {
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

    .stat-danger .stat-icon {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    }

    .stat-info .stat-icon {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .stat-warning .stat-icon {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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

    #menu_dataTable {
        font-size: 0.9rem;
    }

    #menu_dataTable thead th {
        background: linear-gradient(135deg, #1a4d2e 0%, #2d7a4e 100%);
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        border: none;
        padding: 1rem;
    }

    #menu_dataTable tbody tr {
        transition: all 0.2s ease;
    }

    #menu_dataTable tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.01);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    #menu_dataTable tbody td {
        padding: 0.875rem;
        vertical-align: middle;
    }

    .menu-name {
        font-weight: 600;
        color: #2c3e50;
    }

    .dish-thumbnail {
        border: 2px solid #dee2e6;
        transition: transform 0.3s ease;
    }

    .dish-thumbnail:hover {
        transform: scale(1.5);
        z-index: 1000;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
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
        let currentCategoryFilter = null;

        function filterStatus(status) {
            currentStatusFilter = status;
            applyFilters();

            // Update active button styling for status group
            document.querySelectorAll('.btn-group:first-of-type .btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.closest('.btn').classList.add('active');

            // Clear category filter buttons
            document.querySelectorAll('.btn-group:last-of-type .btn').forEach(btn => {
                btn.classList.remove('active');
            });
            currentCategoryFilter = null;
        }

        function filterCategory(category) {
            currentCategoryFilter = category;
            applyFilters();

            document.querySelectorAll('.btn-group:last-of-type .btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.closest('.btn').classList.add('active');

            document.querySelectorAll('.btn-group:first-of-type .btn').forEach(btn => {
                btn.classList.remove('active');
            });
            document.querySelector('.btn-group:first-of-type .btn:first-child').classList.add('active');
            currentStatusFilter = 'all';
        }

        function applyFilters() {
            const table = $('#menu_dataTable').DataTable();

            table.columns().search('');

            if (currentStatusFilter !== 'all') {
                table.column(4).search(currentStatusFilter);
            }

            if (currentCategoryFilter) {
                table.column(3).search(currentCategoryFilter);
            }

            table.draw();
        }
    </script>
@endpush