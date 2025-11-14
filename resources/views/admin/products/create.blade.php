@extends('admin.layouts.app')

@section('title', __('Create Product'))

@section('content')
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">{{ __('Create Product') }}</h1>
        <p class="page-subtitle">{{ __('Add a new product to your store') }}</p>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">{{ __('Products') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Create') }}</li>
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
                        <!-- Language Tabs -->
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            @foreach ($locales as $locale)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                        id="tab-{{ $locale }}" data-bs-toggle="tab"
                                        data-bs-target="#content-{{ $locale }}" type="button" role="tab">
                                        {{ strtoupper($locale) }}
                                        @if ($locale === 'en')
                                            <span style="color: var(--danger-color);">*</span>
                                        @endif
                                    </button>
                                </li>
                            @endforeach
                        </ul>

                        <!-- Tab Content -->
                        <div class="tab-content">
                            @foreach ($locales as $locale)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                    id="content-{{ $locale }}" role="tabpanel">

                                    <!-- Product Name -->
                                    <div class="form-group">
                                        <label for="name_{{ $locale }}" class="form-label">
                                            {{ __('Product Name') }} ({{ strtoupper($locale) }})
                                            @if ($locale === 'en')
                                                <span style="color: var(--danger-color);">*</span>
                                            @endif
                                        </label>
                                        <input type="text" name="name_{{ $locale }}"
                                            id="name_{{ $locale }}"
                                            class="form-control @error('name_' . $locale) is-invalid @enderror"
                                            value="{{ old('name_' . $locale) }}" {{ $locale === 'en' ? 'required' : '' }}>
                                        @error('name_' . $locale)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Description -->
                                    <div class="form-group">
                                        <label for="description_{{ $locale }}" class="form-label">
                                            {{ __('Description') }} ({{ strtoupper($locale) }})
                                        </label>
                                        <textarea name="description_{{ $locale }}" id="description_{{ $locale }}"
                                            class="form-control @error('description_' . $locale) is-invalid @enderror" rows="5">{{ old('description_' . $locale) }}</textarea>
                                        @error('description_' . $locale)
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Pricing & Inventory -->
                <div class="content-card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Pricing & Inventory') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Price -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price" class="form-label">
                                        {{ __('Price') }} <span style="color: var(--danger-color);">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="price" id="price"
                                            class="form-control @error('price') is-invalid @enderror"
                                            value="{{ old('price') }}" step="0.01" min="0" required>
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Compare Price -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="compare_price" class="form-label">{{ __('Compare at Price') }}</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" name="compare_price" id="compare_price"
                                            class="form-control @error('compare_price') is-invalid @enderror"
                                            value="{{ old('compare_price') }}" step="0.01" min="0">
                                    </div>
                                    @error('compare_price')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text">{{ __('Original price for showing discounts') }}</small>
                                </div>
                            </div>

                            <!-- SKU -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sku" class="form-label">{{ __('SKU') }}</label>
                                    <input type="text" name="sku" id="sku"
                                        class="form-control @error('sku') is-invalid @enderror" value="{{ old('sku') }}"
                                        placeholder="SKU-001">
                                    @error('sku')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text">{{ __('Leave blank to auto-generate') }}</small>
                                </div>
                            </div>

                            <!-- Stock Quantity -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="stock_quantity" class="form-label">
                                        {{ __('Stock Quantity') }} <span style="color: var(--danger-color);">*</span>
                                    </label>
                                    <input type="number" name="stock_quantity" id="stock_quantity"
                                        class="form-control @error('stock_quantity') is-invalid @enderror"
                                        value="{{ old('stock_quantity', 0) }}" min="0" required>
                                    @error('stock_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Images -->
                <div class="content-card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Product Images') }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="images" class="form-label">{{ __('Upload Images') }}</label>
                            <input type="file" name="images[]" id="images"
                                class="form-control @error('images') is-invalid @enderror" accept="image/*" multiple>
                            @error('images')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small
                                class="form-text">{{ __('You can select multiple images. First image will be primary.') }}</small>
                        </div>

                        <!-- Image Preview -->
                        <div id="imagePreview" class="d-flex flex-wrap gap-2 mt-3"></div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Product Settings -->
                <div class="content-card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Product Settings') }}</h3>
                    </div>
                    <div class="card-body">
                        <!-- Category -->
                        <div class="form-group">
                            <label for="category_id" class="form-label">
                                {{ __('Category') }} <span style="color: var(--danger-color);">*</span>
                            </label>
                            <select name="category_id" id="category_id"
                                class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">{{ __('Select Category') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name() }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <div class="form-check">
                                <!-- Hidden input ensures unchecked = 0 -->
                                <input type="hidden" name="is_active" value="0">

                                <input type="checkbox" name="is_active" id="is_active" class="form-check-input"
                                    value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label for="is_active" class="form-check-label">
                                    {{ __('Active') }}
                                </label>
                            </div>
                            <small class="form-text">{{ __('Inactive products are hidden from store') }}</small>
                        </div>


                        <!-- Featured -->
                        <div class="form-group">
                            <div class="form-check">
                                <input type="hidden" name="is_featured" value="0">
                                <input type="checkbox" name="is_featured" id="is_featured" class="form-check-input"
                                    value="1" {{ old('is_featured') ? 'checked' : '' }}>
                                <label for="is_featured" class="form-check-label">
                                    {{ __('Featured Product') }}
                                </label>
                            </div>
                            <small class="form-text">{{ __('Featured products appear on homepage') }}</small>
                        </div>

                    </div>
                </div>

                <!-- Help Card -->
                <div class="content-card mt-3">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Guidelines') }}</h3>
                    </div>
                    <div class="card-body">
                        <ul style="padding-left: 20px; margin: 0; font-size: 0.9rem;">
                            <li class="mb-2">{{ __('English name is required') }}</li>
                            <li class="mb-2">{{ __('Add multiple images for better display') }}</li>
                            <li class="mb-2">{{ __('Set compare price for discount display') }}</li>
                            <li class="mb-2">{{ __('SKU auto-generates if left empty') }}</li>
                        </ul>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="content-card mt-3">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> {{ __('Create Product') }}
                            </button>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> {{ __('Cancel') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Image preview
            const imageInput = document.getElementById('images');
            const imagePreview = document.getElementById('imagePreview');

            if (imageInput) {
                imageInput.addEventListener('change', function(e) {
                    imagePreview.innerHTML = '';

                    if (e.target.files) {
                        Array.from(e.target.files).forEach((file, index) => {
                            const reader = new FileReader();

                            reader.onload = function(e) {
                                const div = document.createElement('div');
                                div.style.cssText =
                                    'position: relative; width: 100px; height: 100px;';

                                const img = document.createElement('img');
                                img.src = e.target.result;
                                img.style.cssText =
                                    'width: 100%; height: 100%; object-fit: cover; border-radius: 8px; border: 2px solid var(--border-color);';

                                if (index === 0) {
                                    const badge = document.createElement('span');
                                    badge.className = 'badge badge-primary';
                                    badge.textContent = '{{ __('Primary') }}';
                                    badge.style.cssText =
                                        'position: absolute; top: 5px; left: 5px; font-size: 0.7rem;';
                                    div.appendChild(badge);
                                }

                                div.appendChild(img);
                                imagePreview.appendChild(div);
                            };

                            reader.readAsDataURL(file);
                        });
                    }
                });
            }
        });
    </script>
@endpush
