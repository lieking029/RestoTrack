@extends('layouts.app')

@section('content')

<div class="waste-logs-container">
    <!-- Page Header -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title">Waste Logs</h2>
                <p class="text-muted">Complete history of all waste records</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.waste-management.create') }}" class="btn btn-danger">
                    <i class="fas fa-plus"></i> Log Waste
                </a>
                <a href="{{ route('admin.waste-management.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.waste-management.logs') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date"
                           value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date"
                           value="{{ $endDate }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('admin.waste-management.logs') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="summary-card">
                <div class="summary-icon bg-danger">
                    <i class="fas fa-trash"></i>
                </div>
                <div class="summary-content">
                    <h4>{{ number_format($wasteStats['total_waste']) }}</h4>
                    <span>Total Waste</span>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="summary-card">
                <div class="summary-icon bg-info">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div class="summary-content">
                    <h4>{{ number_format($wasteStats['waste_count']) }}</h4>
                    <span>Total Records</span>
                </div>
            </div>
        </div>
        @foreach(array_slice($wasteStats['by_reason'], 0, 2) as $reason => $count)
            <div class="col-md-3 col-6 mb-3">
                <div class="summary-card">
                    <div class="summary-icon bg-warning">
                        <i class="fas fa-tag"></i>
                    </div>
                    <div class="summary-content">
                        <h4>{{ number_format($count) }}</h4>
                        <span>{{ $reason }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Waste Logs Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="mb-0">
                <i class="fas fa-history text-primary"></i> Waste History
                <span class="badge bg-secondary ms-2">{{ $wasteLogs->count() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if($wasteLogs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date & Time</th>
                                <th>Product</th>
                                <th class="text-center">Quantity</th>
                                <th>Reason</th>
                                <th>Notes</th>
                                <th>Performed By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($wasteLogs as $log)
                                <tr>
                                    <td>
                                        <strong>{{ $log->created_at->format('M d, Y') }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $log->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        @if($log->inventoryItem?->product)
                                            <strong>{{ $log->inventoryItem->product->name }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $log->inventoryItem->product->unit_of_measurement->value ?? '' }}
                                            </small>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger fs-6">{{ $log->quantity }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $reasonClass = match($log->waste_reason) {
                                                'EXPIRED' => 'bg-dark',
                                                'SPOILED' => 'bg-warning text-dark',
                                                'DAMAGED' => 'bg-danger',
                                                'CONTAMINATED' => 'bg-info',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $reasonClass }}">
                                            {{ $log->waste_reason ?? 'Other' }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $log->parsed_notes ?? '-' }}</small>
                                    </td>
                                    <td>
                                        @if($log->performer)
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="user-avatar-sm">
                                                    {{ strtoupper(substr($log->performer->first_name ?? 'U', 0, 1)) }}
                                                </div>
                                                <span>{{ $log->performer->full_name }}</span>
                                            </div>
                                        @else
                                            <span class="text-muted">System</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-clipboard-list fa-4x mb-3"></i>
                    <h4>No Waste Logs Found</h4>
                    <p>No waste has been recorded for the selected period.</p>
                    <a href="{{ route('admin.waste-management.create') }}" class="btn btn-danger">
                        <i class="fas fa-plus"></i> Log Waste
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .waste-logs-container {
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

    /* Summary Cards */
    .summary-card {
        background: white;
        padding: 1rem;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        display: flex;
        align-items: center;
        gap: 1rem;
        height: 100%;
    }

    .summary-icon {
        width: 45px;
        height: 45px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        color: white;
        flex-shrink: 0;
    }

    .summary-content h4 {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0;
        color: #2c3e50;
    }

    .summary-content span {
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
        padding: 1rem;
    }

    /* User Avatar */
    .user-avatar-sm {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1a4d2e 0%, #2d7a4e 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
    }

    @media (max-width: 768px) {
        .waste-logs-container {
            padding: 1rem;
        }
    }
</style>

@endsection
