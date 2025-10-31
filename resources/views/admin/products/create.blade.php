@extends('admin.layouts.app')

@section('title', __('Add New Product'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">{{ __('Add New Product') }}</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">{{ __('Products') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Add New') }}</li>
        </ol>
    </nav>
</div>

<form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Product Information -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Product Information') }}</h3>
                </div>
                <div class="card-body">
                    <!-- English Name -->
                    <div class="form-group">
                        <label for="name_en" class="form-label">{{ __('Product Name') }} (English) <span style="color: var(--danger-color);">*</span></label>
                        <input type="text" name="name_en" id="name_en" class="form-control @error('name_en') is-invalid @enderror" value="{{ old('name_en') }}" required>
                        @error('name_en')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- English Description -->
                    <div class="form-group">
                        <label for="description_en" class="form-label">{{ __('Description') }} (English)</label>
                        <textarea name="description_en" id="description_en" rows="5" class="form-control @error('description_en') is-invalid @enderror">{{ old('description_en') }}</textarea>
                        @error('description_en')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <hr style="margin: 25px 0;">
                    
                    <!-- Spanish Name -->
                    <div class="form-group">
                        <label for="name_es" class="form-label">{{ __('Product Name') }} (Spanish)</label>
                        <input type="text" name="name_es" id="name_es" class="form-control @error('name_es') is-invalid @enderror" value="{{ old('name_es') }}">
                        @error('name_es')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Spanish Description -->
                    <div class="form-group">
                        <label for="description_es" class="form-label">{{ __('Description') }} (Spanish)</label>
                        <textarea name="description_es" id="description_es" rows="5" class="form-control @error('description_es') is-invalid @enderror">{{ old('description_es') }}</textarea>
                        @error('description_es')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
            
            <!-- Product Images -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Product Images') }}</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="images" class="form-label">{{ __('Upload Images') }}</label>
                        <input type="file" name="images[]" id="images" class="form-control @error('images.*') is-invalid @enderror" multiple accept="image/*">
                        <div class="form-text">{{ __('You can upload multiple images. First image will be set as primary.') }}</div>
                        @error('images.*')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Image Preview -->
                    <div id="imagePreview" class="d-flex gap-3 flex-wrap mt-3"></div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Publish -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Publish') }}</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" id="is_active" class="form-check-input" value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
                            <label for="is_active" class="form-check-label">{{ __('Active') }}</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" name="is_featured" id="is_featured" class="form-check-input" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                            <label for="is_featured" class="form-check-label">{{ __('Featured Product') }}</label>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-circle me-2"></i>{{ __('Create Product') }}
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary w-100 mt-2">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </div>
            
            <!-- Product Data -->
            <div class="content-card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Product Data') }}</h3>
                </div>
                <div class="card-body">
                    <!-- Category -->
                    <div class="form-group">
                        <label for="category_id" class="form-label">{{ __('Category') }} <span style="color: var(--danger-color);">*</span></label>
                        <select name="category_id" id="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                            <option value="">{{ __('Select Category') }}</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name() }}
                            </option>
                            @endforeach
                        </select>
                        @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- SKU -->
                    <div class="form-group">
                        <label for="sku" class="form-label">{{ __('SKU') }}</label>
                        <input type="text" name="sku" id="sku" class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku') }}" placeholder="{{ __('Auto-generated if empty') }}">
                        @error('sku')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Price -->
                    <div class="form-group">
                        <label for="price" class="form-label">{{ __('Price') }} (₹) <span style="color: var(--danger-color);">*</span></label>
                        <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" step="0.01" min="0" required>
                        @error('price')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Compare Price -->
                    <div class="form-group">
                        <label for="compare_price" class="form-label">{{ __('Compare at Price') }} (₹)</label>
                        <input type="number" name="compare_price" id="compare_price" class="form-control @error('compare_price') is-invalid @enderror" value="{{ old('compare_price') }}" step="0.01" min="0">
                        <div class="form-text">{{ __('Original price for showing discounts') }}</div>
                        @error('compare_price')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Stock -->
                    <div class="form-group">
                        <label for="stock_quantity" class="form-label">{{ __('Stock Quantity') }} <span style="color: var(--danger-color);">*</span></label>
                        <input type="number" name="stock_quantity" id="stock_quantity" class="form-control @error('stock_quantity') is-invalid @enderror" value="{{ old('stock_quantity', 0) }}" min="0" required>
                        @error('stock_quantity')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.getElementById('images').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    Array.from(e.target.files).forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.style.position = 'relative';
                div.innerHTML = `
                    <img src="${e.target.result}" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px;">
                    ${index === 0 ? '<span class="badge badge-primary" style="position: absolute; top: 5px; left: 5px;">Primary</span>' : ''}
                `;
                preview.appendChild(div);
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endpush