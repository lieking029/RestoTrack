@extends('layouts.app')

@section('content')

<div class="sales-report-container">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title">Sales Reports</h2>
                <p class="text-muted">View and analyze your sales data</p>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card stat-primary">
                <div class="stat-icon">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($totalOrders) }}</h3>
                    <p>Total Orders</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-success">
                <div class="stat-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-info">
                    <h3>₱{{ number_format($todaySales, 2) }}</h3>
                    <p>Today's Sales</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-info">
                <div class="stat-icon">
                    <i class="fas fa-calendar-week"></i>
                </div>
                <div class="stat-info">
                    <h3>₱{{ number_format($weeklySales, 2) }}</h3>
                    <p>Weekly Sales</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-warning">
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-info">
                    <h3>₱{{ number_format($monthlySales, 2) }}</h3>
                    <p>Monthly Sales</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Daily Sales Chart -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-chart-line text-primary"></i> Daily Sales (Last 7 Days)</h5>
                </div>
                <div class="card-body">
                    <canvas id="dailySalesChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Week Comparison Chart -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-chart-bar text-info"></i> This Week vs Last Week</h5>
                </div>
                <div class="card-body">
                    <canvas id="weekComparisonChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top & Least Selling Items -->
    <div class="row mb-4">
        <!-- Top Selling Items -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-trophy text-warning"></i> Top Selling Items</h5>
                </div>
                <div class="card-body p-0">
                    @if(count($topSellingItems) > 0)
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Item Name</th>
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
                                            @else
                                                {{ $index + 1 }}
                                            @endif
                                        </td>
                                        <td><strong>{{ $item['name'] }}</strong></td>
                                        <td class="text-center"><span class="badge bg-success">{{ number_format($item['total_quantity']) }}</span></td>
                                        <td class="text-end text-success fw-bold">₱{{ number_format($item['total_revenue'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-chart-pie fa-3x mb-3"></i>
                            <p>No sales data available yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Least Selling Items -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-arrow-down text-danger"></i> Least Selling Items</h5>
                </div>
                <div class="card-body p-0">
                    @if(count($leastSellingItems) > 0)
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Item Name</th>
                                    <th class="text-center">Qty Sold</th>
                                    <th class="text-end">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leastSellingItems as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td><strong>{{ $item['name'] }}</strong></td>
                                        <td class="text-center"><span class="badge bg-secondary">{{ number_format($item['total_quantity']) }}</span></td>
                                        <td class="text-end text-muted">₱{{ number_format($item['total_revenue'], 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-chart-pie fa-3x mb-3"></i>
                            <p>No sales data available yet</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- DataTable Card -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-list"></i> Order History</h5>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-secondary active" onclick="filterStatus('all')">
                        <i class="fas fa-list"></i> All
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="filterStatus('4')">
                        <i class="fas fa-check-circle"></i> Completed
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="filterStatus('0')">
                        <i class="fas fa-clock"></i> Pending
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="filterStatus('5')">
                        <i class="fas fa-times-circle"></i> Cancelled
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
    .sales-report-container {
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

    .stat-success .stat-icon {
        background: linear-gradient(135deg, #1a4d2e 0%, #2d7a4e 100%);
    }

    .stat-info .stat-icon {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .stat-warning .stat-icon {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
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
        font-size: 0.9rem;
    }

    /* Card Styling */
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

    /* DataTable Styling */
    #sales_report_dataTable {
        font-size: 0.9rem;
    }

    #sales_report_dataTable thead th {
        background: linear-gradient(135deg, #1a4d2e 0%, #2d7a4e 100%);
        color: white;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        border: none;
        padding: 1rem;
    }

    #sales_report_dataTable tbody tr {
        transition: all 0.2s ease;
    }

    #sales_report_dataTable tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.01);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    #sales_report_dataTable tbody td {
        padding: 0.875rem;
        vertical-align: middle;
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

    /* Table in Cards */
    .card-body.p-0 .table {
        margin-bottom: 0;
    }

    .card-body.p-0 .table th,
    .card-body.p-0 .table td {
        padding: 0.75rem 1rem;
    }
</style>

@endsection

@push('scripts')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Daily Sales Chart
        const dailySalesCtx = document.getElementById('dailySalesChart').getContext('2d');
        new Chart(dailySalesCtx, {
            type: 'line',
            data: {
                labels: @json($dailySalesChart['labels']),
                datasets: [{
                    label: 'Sales (₱)',
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
                                return '₱' + value.toLocaleString();
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
                    borderRadius: 5
                }, {
                    label: 'Last Week',
                    data: @json($weekComparison['lastWeek']),
                    backgroundColor: 'rgba(108, 117, 125, 0.5)',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
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
                                return '₱' + value.toLocaleString();
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

        // Filter by Status
        function filterStatus(status) {
            const table = $('#sales_report_dataTable').DataTable();

            if (status === 'all') {
                table.column(5).search('').draw(); // Column 5 is status
            } else {
                table.column(5).search(status).draw();
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
