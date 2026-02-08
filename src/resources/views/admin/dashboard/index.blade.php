@extends('layouts.app')

@section('content')
    <div class="dashboard-container">
        <div class="page-header mb-4">
            <h2 class="page-title">Dashboard</h2>
            <p class="text-muted">Welcome back! Here's what's happening today.</p>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card stat-danger">
                    <div class="stat-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-info">
                        <h3>{{ $lowStockCount + $outOfStockCount }}</h3>
                        <p>Low/Out of Stock</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card stat-success">
                    <div class="stat-icon">
                        <i class="fas fa-peso-sign"></i>
                    </div>
                    <div class="stat-info">
                        <h3>₱{{ number_format($todaySales, 0) }}</h3>
                        <p>Today's Sales</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card stat-info">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-week"></i>
                    </div>
                    <div class="stat-info">
                        <h3>₱{{ number_format($weeklySales, 0) }}</h3>
                        <p>Weekly Sales</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="stat-card stat-primary">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3>₱{{ number_format($monthlySales, 0) }}</h3>
                        <p>Monthly Sales</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="mini-stat-card">
                    <div class="mini-stat-icon bg-success">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="mini-stat-content">
                        <h4>{{ number_format($totalOrders) }}</h4>
                        <span>Total Orders</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="mini-stat-card">
                    <div class="mini-stat-icon bg-info">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="mini-stat-content">
                        <h4>₱{{ number_format($summary['average_order_value'], 0) }}</h4>
                        <span>Avg. Order Value</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="mini-stat-card">
                    <div class="mini-stat-icon bg-warning">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="mini-stat-content">
                        <h4>{{ $expiringSoonCount }}</h4>
                        <span>Expiring Soon</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="mini-stat-card">
                    <div class="mini-stat-icon bg-danger">
                        <i class="fas fa-box-open"></i>
                    </div>
                    <div class="mini-stat-content">
                        <h4>{{ $outOfStockCount }}</h4>
                        <span>Out of Stock</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
            <!-- Daily Sales Trend -->
            <div class="col-lg-8 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-chart-line text-primary"></i> Sales Trend (Last 7 Days)</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-wrapper">
                            <canvas id="dailySalesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Week Comparison -->
            <div class="col-lg-4 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-chart-bar text-info"></i> This Week vs Last</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-wrapper">
                            <canvas id="weekComparisonChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tables Row -->
        <div class="row mb-4">
            <!-- Stock Alerts -->
            <div class="col-lg-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-exclamation-circle text-danger"></i> Stock Alerts</h5>
                        <a href="{{ route('admin.product.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        @if($stockAlerts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product</th>
                                            <th class="text-center">Stock</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($stockAlerts as $product)
                                            <tr>
                                                <td><strong>{{ $product->name }}</strong></td>
                                                <td class="text-center">
                                                    <span class="badge bg-secondary">
                                                        {{ $product->remaining_stock }} {{ $product->unit_of_measurement->value ?? '' }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    @if($product->status->value === 2)
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-times-circle"></i> Out of Stock
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning text-dark">
                                                            <i class="fas fa-exclamation-triangle"></i> Low Stock
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <p class="mb-0">All products are well stocked!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Top Selling Items -->
            <div class="col-lg-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-trophy text-warning"></i> Top Selling Items</h5>
                        <a href="{{ route('admin.sales-report.index') }}" class="btn btn-sm btn-outline-primary">View Report</a>
                    </div>
                    <div class="card-body p-0">
                        @if(count($topSellingItems) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Item</th>
                                            <th class="text-center">Qty Sold</th>
                                            <th class="text-end">Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($topSellingItems as $index => $item)
                                            <tr>
                                                <td>
                                                    @if($index === 0)
                                                        <span class="badge bg-warning"><i class="fas fa-crown"></i></span>
                                                    @elseif($index === 1)
                                                        <span class="badge bg-secondary"><i class="fas fa-medal"></i></span>
                                                    @elseif($index === 2)
                                                        <span class="badge bg-danger"><i class="fas fa-award"></i></span>
                                                    @else
                                                        {{ $index + 1 }}
                                                    @endif
                                                </td>
                                                <td><strong>{{ $item['name'] }}</strong></td>
                                                <td class="text-center">
                                                    <span class="badge bg-success">{{ number_format($item['total_quantity']) }}</span>
                                                </td>
                                                <td class="text-end text-success fw-bold">
                                                    ₱{{ number_format($item['total_revenue'], 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-chart-pie fa-3x mb-3"></i>
                                <p class="mb-0">No sales data yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Row -->
        <div class="row">
            <!-- Expiring Soon -->
            <div class="col-lg-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-calendar-times text-warning"></i> Expiring Soon</h5>
                        <a href="{{ route('admin.product.index') }}" class="btn btn-sm btn-outline-warning">View All</a>
                    </div>
                    <div class="card-body p-0">
                        @if($expiringProducts->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Product</th>
                                            <th class="text-center">Expiry Date</th>
                                            <th class="text-center">Days Left</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($expiringProducts as $product)
                                            <tr>
                                                <td><strong>{{ $product->name }}</strong></td>
                                                <td class="text-center">
                                                    <i class="fas fa-calendar text-muted"></i>
                                                    {{ $product->expiration_date->format('M d, Y') }}
                                                </td>
                                                <td class="text-center">
                                                    @php $daysLeft = $product->days_until_expiration; @endphp
                                                    @if($daysLeft <= 3)
                                                        <span class="badge bg-danger">{{ $daysLeft }} days</span>
                                                    @elseif($daysLeft <= 7)
                                                        <span class="badge bg-warning text-dark">{{ $daysLeft }} days</span>
                                                    @else
                                                        <span class="badge bg-info">{{ $daysLeft }} days</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                <p class="mb-0">No products expiring soon</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="col-lg-6 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-shopping-cart text-primary"></i> Recent Orders</h5>
                        <a href="{{ route('admin.sales-report.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body p-0">
                        @if($recentOrders->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Order ID</th>
                                            <th class="text-center">Items</th>
                                            <th class="text-end">Total</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentOrders as $order)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.sales-report.show', $order->id) }}" class="text-decoration-none">
                                                        #{{ substr($order->id, 0, 8) }}
                                                    </a>
                                                    <br>
                                                    <small class="text-muted">{{ $order->created_at->diffForHumans() }}</small>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-secondary">{{ $order->items->count() }}</span>
                                                </td>
                                                <td class="text-end fw-bold text-success">
                                                    ₱{{ number_format($order->total, 2) }}
                                                </td>
                                                <td class="text-center">
                                                    @php
                                                        $badgeClass = match ($order->status->value) {
                                                            \App\Enums\OrderStatus::COMPLETED => 'success',
                                                            \App\Enums\OrderStatus::PENDING => 'warning',
                                                            \App\Enums\OrderStatus::CONFIRMED => 'info',
                                                            \App\Enums\OrderStatus::INPREPARATION => 'primary',
                                                            \App\Enums\OrderStatus::READY => 'secondary',
                                                            \App\Enums\OrderStatus::CANCELLED => 'danger',
                                                            default => 'secondary'
                                                        };
                                                        $statusLabel = match ($order->status->value) {
                                                            \App\Enums\OrderStatus::COMPLETED => 'Completed',
                                                            \App\Enums\OrderStatus::PENDING => 'Pending',
                                                            \App\Enums\OrderStatus::CONFIRMED => 'Confirmed',
                                                            \App\Enums\OrderStatus::INPREPARATION => 'Preparing',
                                                            \App\Enums\OrderStatus::READY => 'Ready',
                                                            \App\Enums\OrderStatus::CANCELLED => 'Cancelled',
                                                            default => 'Unknown'
                                                        };
                                                    @endphp
                                                    <span class="badge bg-{{ $badgeClass }}">{{ $statusLabel }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                                <p class="mb-0">No orders yet</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .dashboard-container {
            padding: 2rem;
            background-color: #f5f6fa;
            min-height: calc(100vh - 70px);
            margin-top: 70px;
        }

        .page-header {
            margin-bottom: 2rem;
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

        .stat-primary .stat-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .stat-success .stat-icon {
            background: linear-gradient(135deg, #1a4d2e 0%, #2d7a4e 100%);
        }

        .stat-info .stat-icon {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .stat-danger .stat-icon {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .stat-warning .stat-icon {
            background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
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

        /* Mini Stat Cards */
        .mini-stat-card {
            background: white;
            padding: 1rem 1.25rem;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: transform 0.3s ease;
            height: 100%;
        }

        .mini-stat-card:hover {
            transform: translateY(-3px);
        }

        .mini-stat-icon {
            width: 45px;
            height: 45px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
        }

        .mini-stat-content h4 {
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0;
            color: #2c3e50;
        }

        .mini-stat-content span {
            font-size: 0.8rem;
            color: #6c757d;
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
            font-size: 1rem;
        }

        /* Chart Wrapper */
        .chart-wrapper {
            position: relative;
            height: 280px;
        }

        /* Table Styling */
        .table thead th {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            border-bottom: 2px solid #dee2e6;
        }

        .table tbody td {
            vertical-align: middle;
            padding: 0.75rem;
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .stat-info h3 {
                font-size: 1.25rem;
            }

            .chart-wrapper {
                height: 220px;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Daily Sales Trend Chart
        const dailySalesCtx = document.getElementById('dailySalesChart').getContext('2d');
        new Chart(dailySalesCtx, {
            type: 'line',
            data: {
                labels: @json($dailySalesChart['labels']),
                datasets: [{
                    label: 'Sales',
                    data: @json($dailySalesChart['data']),
                    borderColor: '#1a4d2e',
                    backgroundColor: 'rgba(26, 77, 46, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#1a4d2e',
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
                    },
                    tooltip: {
                        backgroundColor: '#2c3e50',
                        titleFont: { size: 14 },
                        bodyFont: { size: 13 },
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return '₱' + context.raw.toLocaleString('en-PH', { minimumFractionDigits: 2 });
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000) {
                                    return '₱' + (value / 1000) + 'k';
                                }
                                return '₱' + value;
                            }
                        },
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

        // Week Comparison Chart
        const weekComparisonCtx = document.getElementById('weekComparisonChart').getContext('2d');
        new Chart(weekComparisonCtx, {
            type: 'bar',
            data: {
                labels: @json($weekComparison['labels']),
                datasets: [{
                    label: 'This Week',
                    data: @json($weekComparison['thisWeek']),
                    backgroundColor: 'rgba(26, 77, 46, 0.8)',
                    borderRadius: 5,
                    barThickness: 12
                }, {
                    label: 'Last Week',
                    data: @json($weekComparison['lastWeek']),
                    backgroundColor: 'rgba(108, 117, 125, 0.5)',
                    borderRadius: 5,
                    barThickness: 12
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
                    },
                    tooltip: {
                        backgroundColor: '#2c3e50',
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ₱' + context.raw.toLocaleString('en-PH', { minimumFractionDigits: 2 });
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000) {
                                    return '₱' + (value / 1000) + 'k';
                                }
                                return '₱' + value;
                            }
                        },
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
    </script>
@endsection
