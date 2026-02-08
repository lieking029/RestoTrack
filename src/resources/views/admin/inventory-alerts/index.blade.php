@extends('layouts.app')

@section('content')

<div class="alerts-container">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title">Inventory Alerts</h2>
                <p class="text-muted">Monitor stock levels and expiring products</p>
            </div>
            <button class="btn btn-outline-primary" onclick="refreshPage()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Alert Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <a href="{{ route('admin.inventory-alerts.index', ['filter' => 'all']) }}" class="text-decoration-none">
                <div class="alert-stat-card {{ $filter === 'all' ? 'active' : '' }}">
                    <div class="alert-stat-icon bg-primary">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div class="alert-stat-info">
                        <h3>{{ $alertCounts['total'] }}</h3>
                        <p>Total Alerts</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <a href="{{ route('admin.inventory-alerts.index', ['filter' => 'critical']) }}" class="text-decoration-none">
                <div class="alert-stat-card {{ $filter === 'critical' ? 'active' : '' }} critical">
                    <div class="alert-stat-icon bg-danger">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div class="alert-stat-info">
                        <h3>{{ $alertCounts['critical'] }}</h3>
                        <p>Critical</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <a href="{{ route('admin.inventory-alerts.index', ['filter' => 'low_stock']) }}" class="text-decoration-none">
                <div class="alert-stat-card {{ $filter === 'low_stock' ? 'active' : '' }}">
                    <div class="alert-stat-icon bg-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="alert-stat-info">
                        <h3>{{ $alertCounts['low_stock'] }}</h3>
                        <p>Low Stock</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <a href="{{ route('admin.inventory-alerts.index', ['filter' => 'expiring']) }}" class="text-decoration-none">
                <div class="alert-stat-card {{ $filter === 'expiring' ? 'active' : '' }}">
                    <div class="alert-stat-icon bg-info">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="alert-stat-info">
                        <h3>{{ $alertCounts['expiring_soon'] }}</h3>
                        <p>Expiring Soon</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5 class="mb-0">
                    <i class="fas fa-filter text-primary"></i>
                    {{ ucfirst(str_replace('_', ' ', $filter)) }} Alerts
                </h5>
                <div class="btn-group flex-wrap" role="group">
                    <a href="{{ route('admin.inventory-alerts.index', ['filter' => 'all']) }}"
                       class="btn btn-sm {{ $filter === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
                        <i class="fas fa-list"></i> All
                    </a>
                    <a href="{{ route('admin.inventory-alerts.index', ['filter' => 'out_of_stock']) }}"
                       class="btn btn-sm {{ $filter === 'out_of_stock' ? 'btn-danger' : 'btn-outline-danger' }}">
                        <i class="fas fa-times-circle"></i> Out of Stock ({{ $alertCounts['out_of_stock'] }})
                    </a>
                    <a href="{{ route('admin.inventory-alerts.index', ['filter' => 'low_stock']) }}"
                       class="btn btn-sm {{ $filter === 'low_stock' ? 'btn-warning' : 'btn-outline-warning' }}">
                        <i class="fas fa-exclamation-triangle"></i> Low Stock ({{ $alertCounts['low_stock'] }})
                    </a>
                    <a href="{{ route('admin.inventory-alerts.index', ['filter' => 'expiring']) }}"
                       class="btn btn-sm {{ $filter === 'expiring' ? 'btn-info' : 'btn-outline-info' }}">
                        <i class="fas fa-clock"></i> Expiring ({{ $alertCounts['expiring_soon'] }})
                    </a>
                    <a href="{{ route('admin.inventory-alerts.index', ['filter' => 'expired']) }}"
                       class="btn btn-sm {{ $filter === 'expired' ? 'btn-dark' : 'btn-outline-dark' }}">
                        <i class="fas fa-skull-crossbones"></i> Expired ({{ $alertCounts['expired'] }})
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts List -->
    <div class="alerts-list">
        @if($alerts->count() > 0)
            @foreach($alerts as $alert)
                <div class="alert-card {{ $alert['priority'] }}">
                    <div class="alert-card-icon {{ $alert['icon_class'] }}">
                        <i class="fas {{ $alert['icon'] }}"></i>
                    </div>
                    <div class="alert-card-content">
                        <div class="alert-card-header">
                            <h5 class="alert-title">{{ $alert['title'] }}</h5>
                            <span class="alert-priority-badge {{ $alert['priority'] }}">
                                {{ ucfirst($alert['priority']) }}
                            </span>
                        </div>
                        <p class="alert-message">{{ $alert['message'] }}</p>
                        <div class="alert-meta">
                            <span class="alert-time">
                                <i class="fas fa-clock"></i> {{ $alert['time_ago'] }}
                            </span>
                            @if(isset($alert['percentage']))
                                <span class="alert-stock-bar">
                                    <span class="stock-label">Stock Level:</span>
                                    <div class="progress" style="width: 100px; height: 8px;">
                                        <div class="progress-bar {{ $alert['percentage'] <= 20 ? 'bg-danger' : ($alert['percentage'] <= 50 ? 'bg-warning' : 'bg-success') }}"
                                             style="width: {{ $alert['percentage'] }}%"></div>
                                    </div>
                                    <span class="stock-percent">{{ $alert['percentage'] }}%</span>
                                </span>
                            @endif
                            @if(isset($alert['days_left']))
                                <span class="alert-days {{ $alert['days_left'] <= 3 ? 'text-danger' : 'text-warning' }}">
                                    <i class="fas fa-calendar-times"></i> {{ $alert['days_left'] }} days left
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="alert-card-action">
                        <a href="{{ $alert['action_url'] }}" class="btn btn-sm btn-primary">
                            {{ $alert['action_label'] }} <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        @else
            <div class="no-alerts">
                <div class="no-alerts-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h4>All Clear!</h4>
                <p>No {{ $filter === 'all' ? '' : str_replace('_', ' ', $filter) }} alerts at this time.</p>
                <a href="{{ route('admin.product.index') }}" class="btn btn-outline-primary">
                    <i class="fas fa-boxes"></i> View Inventory
                </a>
            </div>
        @endif
    </div>
</div>

<style>
    .alerts-container {
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

    /* Alert Stat Cards */
    .alert-stat-card {
        background: white;
        padding: 1.25rem;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s ease;
        height: 100%;
        border: 2px solid transparent;
    }

    .alert-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
    }

    .alert-stat-card.active {
        border-color: var(--primary-green, #1a4d2e);
        background: linear-gradient(135deg, #f0f7f4 0%, #ffffff 100%);
    }

    .alert-stat-card.critical {
        background: linear-gradient(135deg, #fff5f5 0%, #ffffff 100%);
    }

    .alert-stat-card.critical.active {
        border-color: #dc3545;
    }

    .alert-stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        flex-shrink: 0;
    }

    .alert-stat-info h3 {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0;
        color: #2c3e50;
    }

    .alert-stat-info p {
        margin: 0;
        color: #6c757d;
        font-size: 0.85rem;
    }

    /* Card Styling */
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

    /* Alert Cards */
    .alert-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 1.25rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        border-left: 4px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .alert-card:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .alert-card.critical {
        border-left-color: #dc3545;
        background: linear-gradient(135deg, #fff5f5 0%, #ffffff 100%);
    }

    .alert-card.high {
        border-left-color: #fd7e14;
        background: linear-gradient(135deg, #fffaf0 0%, #ffffff 100%);
    }

    .alert-card.medium {
        border-left-color: #ffc107;
    }

    .alert-card.low {
        border-left-color: #17a2b8;
    }

    .alert-card-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
        background-color: #f8f9fa;
    }

    .alert-card-icon.text-danger {
        background-color: #f8d7da;
        color: #dc3545;
    }

    .alert-card-icon.text-warning {
        background-color: #fff3cd;
        color: #856404;
    }

    .alert-card-content {
        flex: 1;
        min-width: 0;
    }

    .alert-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .alert-title {
        font-size: 1rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 0;
    }

    .alert-priority-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .alert-priority-badge.critical {
        background-color: #dc3545;
        color: white;
    }

    .alert-priority-badge.high {
        background-color: #fd7e14;
        color: white;
    }

    .alert-priority-badge.medium {
        background-color: #ffc107;
        color: #000;
    }

    .alert-priority-badge.low {
        background-color: #17a2b8;
        color: white;
    }

    .alert-message {
        font-size: 0.9rem;
        color: #495057;
        margin-bottom: 0.75rem;
        line-height: 1.5;
    }

    .alert-meta {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        flex-wrap: wrap;
        font-size: 0.8rem;
        color: #6c757d;
    }

    .alert-time {
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .alert-stock-bar {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .stock-label {
        color: #6c757d;
    }

    .stock-percent {
        font-weight: 600;
        color: #2c3e50;
    }

    .alert-days {
        font-weight: 600;
    }

    .alert-card-action {
        flex-shrink: 0;
    }

    /* No Alerts */
    .no-alerts {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .no-alerts-icon {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }

    .no-alerts-icon i {
        font-size: 3rem;
        color: #28a745;
    }

    .no-alerts h4 {
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .no-alerts p {
        color: #6c757d;
        margin-bottom: 1.5rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .alerts-container {
            padding: 1rem;
        }

        .alert-card {
            flex-direction: column;
        }

        .alert-card-action {
            align-self: flex-end;
        }

        .alert-meta {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
    }
</style>

<script>
    function refreshPage() {
        location.reload();
    }

    // Auto-refresh page every 2 minutes
    setInterval(function() {
        location.reload();
    }, 120000);
</script>

@endsection
