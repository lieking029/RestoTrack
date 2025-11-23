@extends('layouts.app')

@section('content')
    <div class="dashboard-container">
        <div class="page-header mb-4">
            <h2 class="page-title">Dashboard</h2>
            <p class="text-muted">Welcome back! Here's what's happening today.</p>
        </div>

        <div class="stats-cards mb-5">
            <div class="stat-card">
                <div class="stat-label">Low on Stock</div>
                <div class="stat-value">2</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Daily Sales</div>
                <div class="stat-value">₱13,000</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Weekly Sales</div>
                <div class="stat-value">₱87,000</div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Monthly Sales</div>
                <div class="stat-value">₱1,500,000</div>
            </div>
        </div>

        <div class="chart-container mb-5">
            <h4 class="section-title">Daily Sales</h4>
            <div class="chart-wrapper">
                <canvas id="dailySalesChart"></canvas>
            </div>
        </div>

        <div class="tables-row">
            <div class="table-card">
                <h5 class="table-title">Stock Alert</h5>
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>5/01/25</td>
                                <td>Item 1</td>
                                <td>25</td>
                                <td><span class="badge-status badge-success"><i class="bi bi-check-circle"></i> On
                                        Stock</span></td>
                            </tr>
                            <tr>
                                <td>4/30/25</td>
                                <td>Item 2</td>
                                <td>66</td>
                                <td><span class="badge-status badge-success"><i class="bi bi-check-circle"></i> On
                                        Stock</span></td>
                            </tr>
                            <tr>
                                <td>4/25/25</td>
                                <td>Item 3</td>
                                <td>3</td>
                                <td><span class="badge-status badge-danger"><i class="bi bi-exclamation-circle"></i> Low on
                                        Stock</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="table-card">
                <h5 class="table-title">Top Selling Dishes</h5>
                <div class="table-responsive">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Quantity</th>
                                <th>Item</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>16</td>
                                <td>Item 1</td>
                            </tr>
                            <tr>
                                <td>30</td>
                                <td>Item 3</td>
                            </tr>
                            <tr>
                                <td>45</td>
                                <td>Item 5</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style>
        .dashboard-container {
            padding: 2rem;
            min-height: calc(100vh - 70px);
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

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .stat-card {
            background: linear-gradient(135deg, var(--primary-green, #1a4d2e) 0%, var(--light-green, #2d7a4e) 100%);
            padding: 1.5rem;
            border-radius: 12px;
            text-align: center;
            color: white;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(26, 77, 46, 0.3);
        }

        .stat-label {
            font-size: 0.85rem;
            font-weight: 500;
            opacity: 0.95;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
        }

        .chart-container {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 1.5rem;
        }

        .chart-wrapper {
            position: relative;
            height: 350px;
        }

        .tables-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 1.5rem;
        }

        .table-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .table-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
        }

        .custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .custom-table thead th {
            background-color: #f8f9fa;
            color: #495057;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 1rem;
            text-align: left;
            border-bottom: 2px solid #dee2e6;
        }

        .custom-table tbody td {
            padding: 1rem;
            color: #2c3e50;
            font-size: 0.95rem;
            border-bottom: 1px solid #f1f3f5;
        }

        .custom-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .custom-table tbody tr:last-child td {
            border-bottom: none;
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .badge-status i {
            font-size: 0.9rem;
        }

        @media (max-width: 1200px) {
            .tables-row {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }

            .stats-cards {
                grid-template-columns: repeat(2, 1fr);
                padding: 1rem;
            }

            .stat-value {
                font-size: 1.5rem;
            }

            .chart-wrapper {
                height: 250px;
            }

            .page-title {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .stats-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        const ctx = document.getElementById('dailySalesChart').getContext('2d');
        const dailySalesChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                        label: 'This Week',
                        data: [12000, 15000, 10000, 18000, 14000, 20000, 16000],
                        backgroundColor: '#22c55e',
                        borderRadius: 8,
                        barThickness: 40
                    },
                    {
                        label: 'Last Week',
                        data: [10000, 14000, 12000, 16000, 13000, 18000, 15000],
                        backgroundColor: '#166534',
                        borderRadius: 8,
                        barThickness: 40
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ₱' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + (value / 1000) + 'k';
                            },
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            color: '#f1f3f5'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });
    </script>
@endsection
