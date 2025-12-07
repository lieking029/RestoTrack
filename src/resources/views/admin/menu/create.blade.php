@extends('layouts.app')

@section('content')

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="menu-create-container">
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="page-title">Add New Menu Item</h2>
                <p class="text-muted">Create a new dish for your restaurant menu</p>
            </div>
            <a href="{{ route('admin.menu.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Menu
            </a>
        </div>
    </div>

    <form action="{{ route('admin.menu.store') }}" method="POST" enctype="multipart/form-data" id="menuForm">
        @csrf
        
        <div class="row">
            <!-- Main Information -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-utensils"></i> Menu Information</h5>
                    </div>
                    <div class="card-body">
                        <!-- Dish Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Dish Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-utensils"></i></span>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name') }}" 
                                       required
                                       placeholder="e.g., Chicken Adobo">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-align-left"></i></span>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          name="description" 
                                          rows="4" 
                                          required
                                          placeholder="Describe your dish...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Provide a detailed description of the dish</small>
                        </div>

                        <!-- Price and Category -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-peso-sign"></i></span>
                                    <input type="number" 
                                           class="form-control @error('price') is-invalid @enderror" 
                                           id="price" 
                                           name="price" 
                                           value="{{ old('price') }}" 
                                           step="0.01" 
                                           min="0"
                                           required
                                           placeholder="0.00">
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-tags"></i></span>
                                    <select class="form-select @error('category') is-invalid @enderror" 
                                            id="category" 
                                            name="category" 
                                            required>
                                        <option value="">Select Category</option>
                                        @foreach(\App\Enums\MenuType::toSelectArray() as $value => $label)
                                            <option value="{{ $value }}" {{ old('category') == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Dish Image Upload -->
                        <div class="mb-3">
                            <label for="dish_picture" class="form-label">Dish Image <span class="text-danger">*</span></label>
                            <input type="file" 
                                   class="form-control @error('dish_picture') is-invalid @enderror" 
                                   id="dish_picture" 
                                   name="dish_picture" 
                                   accept="image/*"
                                   required>
                            @error('dish_picture')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Upload a high-quality image of the dish (JPG, PNG)</small>
                            
                            <!-- Image Preview -->
                            <div id="imagePreview" class="mt-3" style="display: none;">
                                <img id="preview" src="" alt="Preview" class="img-thumbnail" style="max-height: 200px;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ingredients Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-carrot"></i> Ingredients Required</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Select the ingredients needed for this dish and specify the quantities.
                        </div>

                        <div id="ingredientsContainer">
                            <div class="ingredient-row mb-3">
                                <div class="row">
                                    <div class="col-md-8">
                                        <label class="form-label">Ingredient</label>
                                        <select class="form-select ingredient-select" name="ingredients[0][product_id]">
                                            <option value="">Select Ingredient</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-unit="{{ \App\Enums\UnitOfMeasurement::getLabel($product->unit_of_measurement->value) }}">
                                                    {{ $product->name }} ({{ $product->remaining_stock }} {{ \App\Enums\UnitOfMeasurement::getLabel($product->unit_of_measurement->value) }} available)
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Quantity Needed</label>
                                        <input type="number" 
                                               class="form-control" 
                                               name="ingredients[0][quantity_needed]" 
                                               step="0.01" 
                                               min="0.01"
                                               placeholder="0.00">
                                    </div>
                                    <div class="col-md-1 d-flex align-items-end">
                                        <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeIngredient(this)" disabled>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-outline-success" onclick="addIngredient()">
                            <i class="fas fa-plus-circle"></i> Add Another Ingredient
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Status -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-toggle-on"></i> Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="status" 
                                   name="status" 
                                   value="0"
                                   {{ old('status', '0') == '0' ? 'checked' : '' }}>
                            <label class="form-check-label" for="status">
                                Available for Order
                            </label>
                        </div>
                        <small class="text-muted">Toggle to make this dish available or unavailable</small>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <button type="submit" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-save"></i> Create Menu Item
                        </button>
                        <button type="reset" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-redo"></i> Reset Form
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .menu-create-container {
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

    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .input-group-text {
        background-color: #f8f9fa;
        border-right: none;
    }

    .form-control,
    .form-select {
        border-left: none;
        border-radius: 0 8px 8px 0;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #1a4d2e;
        box-shadow: 0 0 0 0.2rem rgba(26, 77, 46, 0.15);
    }

    .input-group-text + .form-control:focus,
    .input-group-text + .form-select:focus {
        border-left: none;
    }

    .btn-success {
        background: linear-gradient(135deg, #1a4d2e 0%, #2d7a4e 100%);
        border: none;
        padding: 0.65rem 1.5rem;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(26, 77, 46, 0.3);
    }

    .ingredient-row {
        padding: 1rem;
        background-color: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 1rem;
    }

    .form-check-input:checked {
        background-color: #1a4d2e;
        border-color: #1a4d2e;
    }
</style>

<script>
    let ingredientIndex = 1;

    // Image Preview
    document.getElementById('dish_picture').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview').src = e.target.result;
                document.getElementById('imagePreview').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    // Add Ingredient
    function addIngredient() {
        const container = document.getElementById('ingredientsContainer');
        const newRow = document.querySelector('.ingredient-row').cloneNode(true);
        
        // Update names and clear values
        newRow.querySelectorAll('select, input').forEach(field => {
            const name = field.getAttribute('name');
            if (name) {
                field.setAttribute('name', name.replace(/\[\d+\]/, `[${ingredientIndex}]`));
            }
            field.value = '';
        });
        
        // Enable remove button
        newRow.querySelector('.btn-danger').disabled = false;
        
        container.appendChild(newRow);
        ingredientIndex++;
    }

    // Remove Ingredient
    function removeIngredient(button) {
        button.closest('.ingredient-row').remove();
    }

    // Auto-capitalize dish name
    document.getElementById('name').addEventListener('blur', function() {
        this.value = this.value.split(' ').map(word => 
            word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()
        ).join(' ');
    });
</script>
@endsection