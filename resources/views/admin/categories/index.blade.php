@extends('admin.layouts.app')

@section('title', __('Categories'))

@section('content')
<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">{{ __('Categories') }}</h1>
    <p class="page-subtitle">{{ __('Manage product categories and translations') }}</p>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('Dashboard') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Categories') }}</li>
        </ol>
    </nav>
</div>

<!-- Action Bar -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <span class="text-muted">{{ __('Total Categories') }}: <strong>{{ $categories->count() }}</strong></span>
    </div>
    @if(auth('admin')->user()->hasPermission('categories.create'))
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
        <i class="bi bi-plus-circle me-2"></i>{{ __('Add New Category') }}
    </button>
    @endif
</div>

<!-- Categories Table -->
<div class="content-card">
    <div class="card-body">
        @if($categories->count() > 0)
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">{{ __('Image') }}</th>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Slug') }}</th>
                        <th style="width: 120px;">{{ __('Products') }}</th>
                        <th style="width: 100px;">{{ __('Sort Order') }}</th>
                        <th style="width: 100px;">{{ __('Status') }}</th>
                        <th style="width: 150px;" class="text-end">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr>
                        <td>
                            @if($category->image)
                            <div style="width: 50px; height: 50px; border-radius: 8px; overflow: hidden; background: var(--content-bg);">
                                <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name() }}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            @else
                            <div style="width: 50px; height: 50px; border-radius: 8px; background: var(--content-bg); display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-image" style="font-size: 1.5rem; color: var(--text-muted);"></i>
                            </div>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $category->name() }}</strong>
                            @if($category->description())
                            <br><small class="text-muted">{{ Str::limit($category->description(), 60) }}</small>
                            @endif
                        </td>
                        <td>
                            <code>{{ $category->slug }}</code>
                        </td>
                        <td>
                            <span class="badge badge-info">{{ $category->products_count ?? $category->products()->count() }}</span>
                        </td>
                        <td class="text-center">
                            {{ $category->sort_order }}
                        </td>
                        <td>
                            @if($category->is_active)
                            <span class="badge badge-success">{{ __('Active') }}</span>
                            @else
                            <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                @if(auth('admin')->user()->hasPermission('categories.edit'))
                                <button type="button" class="btn btn-sm btn-icon btn-primary" 
                                        onclick="editCategory({{ $category->id }})" 
                                        title="{{ __('Edit') }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                @endif
                                
                                @if(auth('admin')->user()->hasPermission('categories.delete'))
                                <form action="{{ route('admin.categories.destroy', $category->id) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('{{ __('Are you sure you want to delete this category?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-icon btn-danger" title="{{ __('Delete') }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <i class="bi bi-folder2-open" style="font-size: 4rem; color: var(--text-muted); opacity: 0.3;"></i>
            <p class="text-muted mt-3">{{ __('No categories found') }}</p>
            @if(auth('admin')->user()->hasPermission('categories.create'))
            <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                <i class="bi bi-plus-circle me-2"></i>{{ __('Create Your First Category') }}
            </button>
            @endif
        </div>
        @endif
    </div>
</div>

<!-- Create Category Modal -->
@if(auth('admin')->user()->hasPermission('categories.create'))
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="createCategoryModalLabel">{{ __('Create New Category') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Tabs for Languages -->
                    <ul class="nav nav-tabs mb-3" id="createCategoryTabs" role="tablist">
                        @foreach($locales as $index => $locale)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                    id="create-{{ $locale }}-tab" 
                                    data-bs-toggle="tab" 
                                    data-bs-target="#create-{{ $locale }}" 
                                    type="button" 
                                    role="tab">
                                {{ strtoupper($locale) }} 
                                @if($locale === 'en')
                                <span class="text-danger">*</span>
                                @endif
                            </button>
                        </li>
                        @endforeach
                    </ul>

                    <div class="tab-content" id="createCategoryTabsContent">
                        @foreach($locales as $index => $locale)
                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                             id="create-{{ $locale }}" 
                             role="tabpanel">
                            
                            <!-- Name -->
                            <div class="form-group">
                                <label for="name_{{ $locale }}" class="form-label">
                                    {{ __('Category Name') }} ({{ strtoupper($locale) }})
                                    @if($locale === 'en')
                                    <span class="text-danger">*</span>
                                    @endif
                                </label>
                                <input type="text" 
                                       class="form-control @error('name_' . $locale) is-invalid @enderror" 
                                       id="name_{{ $locale }}" 
                                       name="name_{{ $locale }}" 
                                       value="{{ old('name_' . $locale) }}"
                                       {{ $locale === 'en' ? 'required' : '' }}
                                       placeholder="{{ __('Enter category name') }}">
                                @error('name_' . $locale)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="form-group">
                                <label for="description_{{ $locale }}" class="form-label">
                                    {{ __('Description') }} ({{ strtoupper($locale) }})
                                </label>
                                <textarea class="form-control @error('description_' . $locale) is-invalid @enderror" 
                                          id="description_{{ $locale }}" 
                                          name="description_{{ $locale }}" 
                                          rows="3"
                                          placeholder="{{ __('Enter category description (optional)') }}">{{ old('description_' . $locale) }}</textarea>
                                @error('description_' . $locale)
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Image Upload -->
                    <div class="form-group">
                        <label for="image" class="form-label">{{ __('Category Image') }}</label>
                        <input type="file" 
                               class="form-control @error('image') is-invalid @enderror" 
                               id="image" 
                               name="image" 
                               accept="image/*">
                        <small class="form-text text-muted">{{ __('Recommended size: 800x600px. Max size: 2MB') }}</small>
                        @error('image')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <!-- Sort Order -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sort_order" class="form-label">{{ __('Sort Order') }}</label>
                                <input type="number" 
                                       class="form-control @error('sort_order') is-invalid @enderror" 
                                       id="sort_order" 
                                       name="sort_order" 
                                       value="{{ old('sort_order', 0) }}" 
                                       min="0">
                                @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">{{ __('Status') }}</label>
                                <div class="form-check form-switch" style="margin-top: 10px;">
                                    <input type="checkbox" 
                                           class="form-check-input" 
                                           id="is_active" 
                                           name="is_active" 
                                           checked>
                                    <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>{{ __('Create Category') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- Edit Category Modal -->
@if(auth('admin')->user()->hasPermission('categories.edit'))
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editCategoryForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">{{ __('Edit Category') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Loading State -->
                    <div id="editCategoryLoading" class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">{{ __('Loading...') }}</span>
                        </div>
                    </div>

                    <!-- Form Content -->
                    <div id="editCategoryContent" style="display: none;">
                        <!-- Tabs for Languages -->
                        <ul class="nav nav-tabs mb-3" id="editCategoryTabs" role="tablist">
                            @foreach($locales as $index => $locale)
                            <li class="nav-item" role="presentation">
                                <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                        id="edit-{{ $locale }}-tab" 
                                        data-bs-toggle="tab" 
                                        data-bs-target="#edit-{{ $locale }}" 
                                        type="button" 
                                        role="tab">
                                    {{ strtoupper($locale) }}
                                    @if($locale === 'en')
                                    <span class="text-danger">*</span>
                                    @endif
                                </button>
                            </li>
                            @endforeach
                        </ul>

                        <div class="tab-content" id="editCategoryTabsContent">
                            @foreach($locales as $index => $locale)
                            <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                                 id="edit-{{ $locale }}" 
                                 role="tabpanel">
                                
                                <!-- Name -->
                                <div class="form-group">
                                    <label for="edit_name_{{ $locale }}" class="form-label">
                                        {{ __('Category Name') }} ({{ strtoupper($locale) }})
                                        @if($locale === 'en')
                                        <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="edit_name_{{ $locale }}" 
                                           name="name_{{ $locale }}" 
                                           {{ $locale === 'en' ? 'required' : '' }}>
                                </div>

                                <!-- Description -->
                                <div class="form-group">
                                    <label for="edit_description_{{ $locale }}" class="form-label">
                                        {{ __('Description') }} ({{ strtoupper($locale) }})
                                    </label>
                                    <textarea class="form-control" 
                                              id="edit_description_{{ $locale }}" 
                                              name="description_{{ $locale }}" 
                                              rows="3"></textarea>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Current Image -->
                        <div id="currentImageSection" style="display: none;">
                            <div class="form-group">
                                <label class="form-label">{{ __('Current Image') }}</label>
                                <div class="d-flex align-items-center gap-3">
                                    <img id="currentImage" src="" alt="Current" style="width: 100px; height: 100px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-color);">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="remove_image" name="remove_image">
                                        <label class="form-check-label" for="remove_image">{{ __('Remove image') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- New Image Upload -->
                        <div class="form-group">
                            <label for="edit_image" class="form-label">{{ __('New Image') }}</label>
                            <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                            <small class="form-text text-muted">{{ __('Leave empty to keep current image') }}</small>
                        </div>

                        <div class="row">
                            <!-- Sort Order -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_sort_order" class="form-label">{{ __('Sort Order') }}</label>
                                    <input type="number" class="form-control" id="edit_sort_order" name="sort_order" min="0">
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Status') }}</label>
                                    <div class="form-check form-switch" style="margin-top: 10px;">
                                        <input type="checkbox" class="form-check-input" id="edit_is_active" name="is_active">
                                        <label class="form-check-label" for="edit_is_active">{{ __('Active') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>{{ __('Update Category') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
// Edit Category Function
function editCategory(categoryId) {
    const modal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
    const form = document.getElementById('editCategoryForm');
    const loading = document.getElementById('editCategoryLoading');
    const content = document.getElementById('editCategoryContent');
    
    // Show loading
    loading.style.display = 'block';
    content.style.display = 'none';
    
    // Show modal
    modal.show();
    
    // Fetch category data
    fetch(`/admin/categories/${categoryId}`)
        .then(response => response.json())
        .then(data => {
            // Set form action
            form.action = `/admin/categories/${categoryId}`;
            
            // Populate translations
            @foreach($locales as $locale)
            const translation_{{ $locale }} = data.translations.find(t => t.locale === '{{ $locale }}');
            if (translation_{{ $locale }}) {
                document.getElementById('edit_name_{{ $locale }}').value = translation_{{ $locale }}.name || '';
                document.getElementById('edit_description_{{ $locale }}').value = translation_{{ $locale }}.description || '';
            }
            @endforeach
            
            // Populate other fields
            document.getElementById('edit_sort_order').value = data.sort_order || 0;
            document.getElementById('edit_is_active').checked = data.is_active === 1;
            
            // Handle current image
            const currentImageSection = document.getElementById('currentImageSection');
            const currentImage = document.getElementById('currentImage');
            
            if (data.image) {
                currentImage.src = `/storage/${data.image}`;
                currentImageSection.style.display = 'block';
            } else {
                currentImageSection.style.display = 'none';
            }
            
            // Hide loading, show content
            loading.style.display = 'none';
            content.style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('{{ __('Error loading category data') }}');
            modal.hide();
        });
}

// Keep modals open on validation errors
@if($errors->any())
    @if(old('_method') === 'PUT')
        // Edit modal
        const editModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
        editModal.show();
    @else
        // Create modal
        const createModal = new bootstrap.Modal(document.getElementById('createCategoryModal'));
        createModal.show();
    @endif
@endif
</script>
@endpush