@extends('layouts.app')

@section('content')

<div class="waste-management-container">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title">Waste Management</h2>
                <p class="text-muted">Track and manage inventory waste and spoilage</p>
            </div>
            <a href="{{ route('admin.waste-management.create') }}" class="btn btn-danger">
                <i class="fas fa-trash-alt"></i> Log Waste
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card stat-danger">
                <div class="stat-icon">
                    <i class="fas fa-trash"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($wasteStats['total_waste']) }}</h3>
                    <p>Total Waste (All Time)</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card stat-warning">
                <div class="stat-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($wasteStats['today']) }}</h3>
                    <p>Today's Waste</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card stat-info">
                <div class="stat-icon">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($wasteStats['this_week']) }}</h3>
                    <p>This Week</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stat-card stat-primary">
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($wasteStats['this_month']) }}</h3>
                    <p>This Month</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Expiry Alert Cards -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-calendar-times text-warning"></i> Expiry Alerts</h5>
                    <a href="{{ route('admin.waste-management.expiry') }}" class="btn btn-sm btn-outline-primary">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 col-4 text-center mb-3">
                            <div class="expiry-stat critical">
                                <h4>{{ $expirySummary['expired'] }}</h4>
                                <small>Expired</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-4 text-center mb-3">
                            <div class="expiry-stat danger">
                                <h4>{{ $expirySummary['expiring_today'] }}</h4>
                                <small>Today</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-4 text-center mb-3">
                            <div class="expiry-stat warning">
                                <h4>{{ $expirySummary['expiring_3_days'] }}</h4>
                                <small>3 Days</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-4 text-center mb-3">
                            <div class="expiry-stat info">
                                <h4>{{ $expirySummary['expiring_7_days'] }}</h4>
                                <small>7 Days</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-4 text-center mb-3">
                            <div class="expiry-stat success">
                                <h4>{{ $expirySummary['expiring_30_days'] }}</h4>
                                <small>30 Days</small>
                            </div>
                        </div>
                        <div class="col-md-2 col-4 text-center mb-3">
                            <a href="{{ route('admin.waste-management.expiry', ['filter' => 'expired']) }}" class="btn btn-danger btn-sm w-100">
                                <i class="fas fa-trash-alt"></i> Dispose Expired
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Daily Waste Chart -->
        <div class="col-lg-8 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-chart-line text-danger"></i> Waste Trend (Last 7 Days)</h5>
                </div>
                <div class="card-body">
                    <div class="chart-wrapper">
                        <canvas id="wasteChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Waste by Reason -->
        <div class="col-lg-4 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-chart-pie text-info"></i> Waste by Reason</h5>
                </div>
                <div class="card-body">
                    @if(count($wasteStats['by_reason']) > 0)
                        <div class="chart-wrapper">
                            <canvas id="wasteReasonChart"></canvas>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-chart-pie fa-3x mb-3"></i>
                            <p>No waste data available</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row">
        <!-- Recent Waste Logs -->
        <div class="col-lg-7 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-history text-secondary"></i> Recent Waste Logs</h5>
                    <a href="{{ route('admin.waste-management.logs') }}" class="btn btn-sm btn-outline-primary">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($recentWasteLogs->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Product</th>
                                        <th class="text-center">Qty</th>
                                        <th>Reason</th>
                                        <th>By</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentWasteLogs as $log)
                                        <tr>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $log->created_at->format('M d, Y') }}<br>
                                                    {{ $log->created_at->format('h:i A') }}
                                                </small>
                                            </td>
                                            <td>
                                                <strong>{{ $log->inventoryItem?->product?->name ?? 'N/A' }}</strong>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-danger">{{ $log->quantity }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $log->waste_reason ?? 'Other' }}</span>
                                            </td>
                                            <td>
                                                <small>{{ $log->performer?->full_name ?? 'System' }}</small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-clipboard-list fa-3x mb-3"></i>
                            <p>No waste logs yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Wasted Products -->
        <div class="col-lg-5 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle text-warning"></i> Top Wasted Products (This Month)</h5>
                </div>
                <div class="card-body p-0">
                    @if($topWastedProducts->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Product</th>
                                        <th class="text-end">Waste Qty</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topWastedProducts as $index => $item)
                                        <tr>
                                            <td>
                                                @if($index === 0)
                                                    <span class="badge bg-danger"><i class="fas fa-arrow-up"></i></span>
                                                @else
                                                    {{ $index + 1 }}
                                                @endif
                                            </td>
                                            <td><strong>{{ $item->inventoryItem?->product?->name ?? 'N/A' }}</strong></td>
                                            <td class="text-end">
                                                <span class="text-danger fw-bold">{{ number_format($item->total_waste) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <p>No waste recorded this month</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .waste-management-container {
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

    /* Stat Cards */
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
    }

    .stat-danger .stat-icon {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    }

    .stat-warning .stat-icon {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
    }

    .stat-info .stat-icon {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    }

    .stat-primary .stat-icon {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .stat-info h3 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
        color: #2c3e50;
    }

    .stat-info p {
        margin: 0;
        color: #6c757d;
        font-size: 0.85rem;
    }

    /* Expiry Stats */
    .expiry-stat {
        padding: 1rem;
        border-radius: 10px;
        transition: transform 0.3s ease;
    }

    .expiry-stat:hover {
        transform: scale(1.05);
    }

    .expiry-stat h4 {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0;
    }

    .expiry-stat small {
        color: #6c757d;
    }

    .expiry-stat.critical {
        background-color: #f8d7da;
    }

    .expiry-stat.critical h4 {
        color: #721c24;
    }

    .expiry-stat.danger {
        background-color: #f5c6cb;
    }

    .expiry-stat.danger h4 {
        color: #721c24;
    }

    .expiry-stat.warning {
        background-color: #fff3cd;
    }

    .expiry-stat.warning h4 {
        color: #856404;
    }

    .expiry-stat.info {
        background-color: #d1ecf1;
    }

    .expiry-stat.info h4 {
        color: #0c5460;
    }

    .expiry-stat.success {
        background-color: #d4edda;
    }

    .expiry-stat.success h4 {
        color: #155724;
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

    /* Chart Wrapper */
    .chart-wrapper {
        position: relative;
        height: 280px;
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
    }

    @media (max-width: 768px) {
        .waste-management-container {
            padding: 1rem;
        }

        .chart-wrapper {
            height: 220px;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Waste Trend Chart
    const wasteCtx = document.getElementById('wasteChart').getContext('2d');
    new Chart(wasteCtx, {
        type: 'line',
        data: {
            labels: @json($dailyWasteChart['labels']),
            datasets: [{
                label: 'Waste Quantity',
                data: @json($dailyWasteChart['data']),
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#dc3545',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    @if(count($wasteStats['by_reason']) > 0)
    // Waste by Reason Chart
    const reasonCtx = document.getElementById('wasteReasonChart').getContext('2d');
    new Chart(reasonCtx, {
        type: 'doughnut',
        data: {
            labels: @json(array_keys($wasteStats['by_reason'])),
            datasets: [{
                data: @json(array_values($wasteStats['by_reason'])),
                backgroundColor: [
                    '#dc3545',
                    '#fd7e14',
                    '#ffc107',
                    '#28a745',
                    '#17a2b8',
                    '#6c757d'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 15
                    }
                }
            }
        }
    });
    @endif
</script>

@endsection
