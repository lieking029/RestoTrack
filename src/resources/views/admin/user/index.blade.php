@extends('layouts.app')

@section('content')

<div class="user-index-container">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title">User Management</h2>
                <p class="text-muted">Manage system users and their roles</p>
            </div>
            <a href="{{ route('admin.user.create') }}" class="btn btn-success">
                <i class="fas fa-user-plus"></i> Add New User
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card stat-primary">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ \App\Models\User::count() }}</h3>
                    <p>Total Users</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-danger">
                <div class="stat-icon">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ \App\Models\User::where('user_type', \App\Enums\UserType::Admin)->count() }}</h3>
                    <p>Administrators</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-info">
                <div class="stat-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ \App\Models\User::where('user_type', \App\Enums\UserType::Manager)->count() }}</h3>
                    <p>Managers</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-success">
                <div class="stat-icon">
                    <i class="fas fa-user"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ \App\Models\User::where('user_type', \App\Enums\UserType::Employee)->count() }}</h3>
                    <p>Employees</p>
                </div>
            </div>
        </div>
    </div>

    <!-- DataTable Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">User List</h5>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-secondary active" onclick="filterRole('all')">
                        <i class="fas fa-users"></i> All
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="filterRole('0')">
                        <i class="fas fa-crown"></i> Admins
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="filterRole('1')">
                        <i class="fas fa-user-tie"></i> Managers
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="filterRole('2')">
                        <i class="fas fa-user"></i> Employees
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            {{ $dataTable->table(['class' => 'table table-hover table-bordered dt-responsive nowrap w-100']) }}
        </div>
    </div>
</div>

<style>
    .user-index-container {
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
    }

    .stat-primary .stat-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .stat-danger .stat-icon {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .stat-info .stat-icon {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .stat-success .stat-icon {
        background: linear-gradient(135deg, #1a4d2e 0%, #2d7a4e 100%);
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

    #user_dataTable {
        font-size: 0.9rem;
    }

    #user_dataTable thead th {
        background: linear-gradient(135deg, #1a4d2e 0%, #2d7a4e 100%);
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        border: none;
        padding: 1rem;
    }

    #user_dataTable tbody tr {
        transition: all 0.2s ease;
    }

    #user_dataTable tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.01);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    #user_dataTable tbody td {
        padding: 0.875rem;
        vertical-align: middle;
    }

    .user-name {
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
    .dataTables_wrapper .dataTables_paginate .paginate_button {
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
</style>

@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        function filterRole(role) {
            const table = $('#user_dataTable').DataTable();
            
            if (role === 'all') {
                table.column(1).search('').draw(); // Column 1 is user_type
            } else {
                table.column(1).search(role).draw();
            }

            // Update active button styling
            document.querySelectorAll('.btn-group .btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.closest('.btn').classList.add('active');
        }

        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>
@endpush