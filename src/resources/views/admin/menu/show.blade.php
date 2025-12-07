@extends('layouts.app')

@section('content')
<div class="menu-show-container">
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title">{{ $menu->name }}</h2>
                <p class="text-muted">Menu Item Details</p>
            </div>
            <a href="{{ route('admin.menu.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Menu
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-utensils"></i> Dish Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-5 text-center mb-3 mb-md-0">
                            <img src="{{ $menu->dish_picture_url }}" 
                                 alt="{{ $menu->name }}" 
                                 class="img-fluid rounded shadow"
                                 style="max-height: 300px; object-fit: cover;">
                        </div>
                        <div class="col-md-7">
                            <div class="detail-item mb-3">
                                <label class="detail-label"><i class="fas fa-utensils text-primary"></i> Dish Name</label>
                                <p class="detail-value">{{ $menu->name }}</p>
                            </div>

                            <div class="detail-item mb-3">
                                <label class="detail-label"><i class="fas fa-align-left text-info"></i> Description</label>
                                <p class="detail-value">{{ $menu->description }}</p>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="detail-item mb-3">
                                        <label class="detail-label"><i class="fas fa-peso-sign text-success"></i> Price</label>
                                        <p class="detail-value">
                                            <span class="badge bg-success fs-5">{{ $menu->formatted_price }}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-item mb-3">
                                        <label class="detail-label"><i class="fas fa-tags text-warning"></i> Category</label>
                                        <p class="detail-value">
                                            <span class="badge bg-{{ \App\Enums\MenuType::getBadgeClass($menu->category->value) }}">
                                                <i class="{{ \App\Enums\MenuType::getIcon($menu->category->value) }}"></i>
                                                {{ $menu->category->description }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="detail-item">
                                <label class="detail-label"><i class="fas fa-toggle-on text-secondary"></i> Status</label>
                                <p class="detail-value">
                                    <span class="badge bg-{{ $menu->status_badge_class }}">
                                        <i class="{{ \App\Enums\MenuStatus::getIcon($menu->status->value) }}"></i>
                                        {{ $menu->status->description }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ingredients List -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-carrot"></i> Ingredients Required</h5>
                </div>
                <div class="card-body">
                    @if($menu->products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50%">Ingredient</th>
                                        <th width="20%" class="text-center">Quantity Needed</th>
                                        <th width="20%" class="text-center">Available Stock</th>
                                        <th width="10%" class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($menu->products as $product)
                                        <tr>
                                            <td>
                                                <i class="fas fa-box text-muted"></i>
                                                <strong>{{ $product->name }}</strong>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info">
                                                    {{ $product->pivot->quantity_needed }} {{ \App\Enums\UnitOfMeasurement::getLabel($product->unit_of_measurement->value) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $product->status_badge_class }}">
                                                    {{ $product->remaining_stock }} {{ \App\Enums\UnitOfMeasurement::getLabel($product->unit_of_measurement->value) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if($product->remaining_stock >= $product->pivot->quantity_needed)
                                                    <i class="fas fa-check-circle text-success" title="Sufficient"></i>
                                                @else
                                                    <i class="fas fa-exclamation-triangle text-danger" title="Insufficient"></i>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Stock Status Alert -->
                        @if(!$menu->hasIngredientsInStock())
                            <div class="alert alert-warning mt-3">
                                <i class="fas fa-exclamation-triangle"></i>
                                <strong>Warning:</strong> Some ingredients are running low or out of stock. This dish may be unavailable.
                            </div>
                        @else
                            <div class="alert alert-success mt-3">
                                <i class="fas fa-check-circle"></i>
                                <strong>Good to go!</strong> All ingredients are in stock.
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            No ingredients have been added to this menu item yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.menu.edit', $menu->id) }}" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-edit"></i> Edit Menu Item
                    </a>
                    <button type="button" class="btn btn-danger w-100 mb-2" onclick="deleteMenu()">
                        <i class="fas fa-trash"></i> Delete Menu Item
                    </button>
                    <a href="{{ route('admin.menu.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-list"></i> Back to Menu List
                    </a>
                </div>
            </div>

            <!-- Menu Statistics -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="stat-item mb-3">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-primary">
                                <i class="fas fa-carrot"></i>
                            </div>
                            <div class="stat-details">
                                <p class="stat-value">{{ $menu->products->count() }}</p>
                                <p class="stat-label">Ingredients</p>
                            </div>
                        </div>
                    </div>

                    <div class="stat-item mb-3">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-success">
                                <i class="fas fa-peso-sign"></i>
                            </div>
                            <div class="stat-details">
                                <p class="stat-value">{{ $menu->formatted_price }}</p>
                                <p class="stat-label">Selling Price</p>
                            </div>
                        </div>
                    </div>

                    <div class="stat-item">
                        <div class="d-flex align-items-center">
                            <div class="stat-icon bg-info">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div class="stat-details">
                                <p class="stat-value">{{ $menu->created_at->diffInDays(now()) }}</p>
                                <p class="stat-label">Days on Menu</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Additional Info</h5>
                </div>
                <div class="card-body">
                    <div class="info-item mb-3">
                        <label><i class="fas fa-calendar-plus text-muted"></i> Date Added</label>
                        <p class="mb-0">{{ $menu->created_at->format('F d, Y') }}</p>
                        <small class="text-muted">{{ $menu->created_at->diffForHumans() }}</small>
                    </div>

                    <div class="info-item">
                        <label><i class="fas fa-calendar-check text-muted"></i> Last Updated</label>
                        <p class="mb-0">{{ $menu->updated_at->format('F d, Y') }}</p>
                        <small class="text-muted">{{ $menu->updated_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .menu-show-container {
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

    .card {
        border-radius: 15px;
    }

    .card-header {
        padding: 1.25rem 1.5rem;
        border-radius: 15px 15px 0 0 !important;
        border-bottom: 1px solid #e9ecef;
    }

    .card-header h5 {
        color: #2c3e50;
        font-weight: 700;
    }

    .detail-item {
        border-bottom: 1px solid #e9ecef;
        padding-bottom: 0.75rem;
    }

    .detail-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .detail-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 0.25rem;
        display: block;
    }

    .detail-value {
        font-size: 1rem;
        color: #2c3e50;
        margin-bottom: 0;
    }

    .stat-item {
        padding: 1rem;
        background-color: #f8f9fa;
        border-radius: 8px;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        margin-right: 1rem;
    }

    .stat-details {
        flex: 1;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 0;
        line-height: 1;
    }

    .stat-label {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 0;
        margin-top: 0.25rem;
    }

    .info-item {
        padding: 0.75rem 0;
        border-bottom: 1px solid #e9ecef;
    }

    .info-item:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .info-item label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 0.25rem;
        display: block;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 0.65rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .btn-danger {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        border: none;
        padding: 0.65rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(250, 112, 154, 0.3);
    }

    .table thead {
        background: linear-gradient(135deg, #1a4d2e 0%, #2d7a4e 100%);
        color: white;
    }

    .table thead th {
        border: none;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        padding: 1rem;
    }

    .table tbody tr {
        transition: all 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.01);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function deleteMenu() {
        Swal.fire({
            title: 'Delete Menu Item?',
            html: 'Are you sure you want to delete <strong>{{ $menu->name }}</strong>?<br>This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash"></i> Yes, Delete',
            cancelButtonText: '<i class="fas fa-times-circle"></i> Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("admin.menu.destroy", $menu->id) }}';
                
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                
                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';
                
                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }
</script>
@endsection