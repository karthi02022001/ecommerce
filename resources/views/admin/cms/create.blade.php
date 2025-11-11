{{-- resources/views/admin/cms/create.blade.php --}}
@extends('admin.layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="mb-0">{{ $title }}</h2>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.cms.store') }}" method="POST" id="cmsForm">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-8">
                        <label for="slug" class="form-label">{{ __('Slug') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                               id="slug" name="slug" value="{{ old('slug') }}" required>
                        <small class="text-muted">{{ __('URL-friendly identifier (e.g., about-us, terms-conditions)') }}</small>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="status" class="form-label">{{ __('Status') }} <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="mb-3">{{ __('Translations') }}</h5>

                {{-- Error Alert Container --}}
                <div id="validationErrors" class="alert alert-danger d-none" role="alert">
                    <i class="bi bi-exclamation-triangle"></i>
                    <span id="validationMessage"></span>
                </div>

                <ul class="nav nav-tabs" role="tablist" id="languageTabs">
                    @foreach($locales as $index => $locale)
                        <li class="nav-item">
                            <a class="nav-link text-dark {{ $index === 0 ? 'active' : '' }}" 
                               id="{{ $locale }}-tab-link"
                               data-bs-toggle="tab" 
                               href="#{{ $locale }}-tab"
                               data-locale="{{ $locale }}">
                                {{ strtoupper($locale) }}
                                <span class="badge bg-danger ms-1 d-none" id="{{ $locale }}-error-badge">!</span>
                            </a>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content border border-top-0 p-4">
                    @foreach($locales as $index => $locale)
                        <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                             id="{{ $locale }}-tab"
                             data-locale="{{ $locale }}">
                            <input type="hidden" name="translations[{{ $index }}][locale]" value="{{ $locale }}">

                            <div class="mb-3">
                                <label class="form-label">{{ __('Title') }} <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control translation-title @error('translations.'.$index.'.title') is-invalid @enderror" 
                                       name="translations[{{ $index }}][title]" 
                                       value="{{ old('translations.'.$index.'.title') }}"
                                       data-locale="{{ $locale }}"
                                       data-field="title">
                                @error('translations.'.$index.'.title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" style="display: none;"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Content') }} <span class="text-danger">*</span></label>
                                <textarea class="form-control translation-content @error('translations.'.$index.'.content') is-invalid @enderror" 
                                          name="translations[{{ $index }}][content]" 
                                          rows="10"
                                          data-locale="{{ $locale }}"
                                          data-field="content">{{ old('translations.'.$index.'.content') }}</textarea>
                                @error('translations.'.$index.'.content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" style="display: none;"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Meta Title') }}</label>
                                <input type="text" class="form-control" 
                                       name="translations[{{ $index }}][meta_title]" 
                                       value="{{ old('translations.'.$index.'.meta_title') }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Meta Description') }}</label>
                                <textarea class="form-control" 
                                          name="translations[{{ $index }}][meta_description]" 
                                          rows="3">{{ old('translations.'.$index.'.meta_description') }}</textarea>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> {{ __('Save CMS Page') }}
                    </button>
                    <a href="{{ route('admin.cms.index') }}" class="btn btn-secondary">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('cmsForm');
    const validationErrorsDiv = document.getElementById('validationErrors');
    const validationMessage = document.getElementById('validationMessage');

    form.addEventListener('submit', function(e) {
        // Clear previous errors
        validationErrorsDiv.classList.add('d-none');
        document.querySelectorAll('.invalid-feedback').forEach(el => {
            el.style.display = 'none';
            el.textContent = '';
        });
        document.querySelectorAll('.is-invalid').forEach(el => {
            el.classList.remove('is-invalid');
        });
        document.querySelectorAll('[id$="-error-badge"]').forEach(badge => {
            badge.classList.add('d-none');
        });

        let isValid = true;
        let errors = [];
        let firstErrorTab = null;

        // Validate translations
        const locales = ['en'];
        
        locales.forEach((locale, index) => {
            const titleInput = document.querySelector(`input[data-locale="${locale}"][data-field="title"]`);
            const contentTextarea = document.querySelector(`textarea[data-locale="${locale}"][data-field="content"]`);
            
            let hasError = false;

            // Validate title
            if (!titleInput.value.trim()) {
                titleInput.classList.add('is-invalid');
                const feedback = titleInput.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.style.display = 'block';
                    feedback.textContent = '{{ __("The title field is required") }}';
                }
                hasError = true;
                isValid = false;
                errors.push(`${locale.toUpperCase()}: {{ __("Title is required") }}`);
            }

            // Validate content
            if (!contentTextarea.value.trim()) {
                contentTextarea.classList.add('is-invalid');
                const feedback = contentTextarea.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.style.display = 'block';
                    feedback.textContent = '{{ __("The content field is required") }}';
                }
                hasError = true;
                isValid = false;
                errors.push(`${locale.toUpperCase()}: {{ __("Content is required") }}`);
            }

            // Mark tab with error
            if (hasError) {
                const errorBadge = document.getElementById(`${locale}-error-badge`);
                if (errorBadge) {
                    errorBadge.classList.remove('d-none');
                }
                
                // Remember first error tab
                if (!firstErrorTab) {
                    firstErrorTab = locale;
                }
            }
        });

        // If validation fails
        if (!isValid) {
            e.preventDefault();
            
            // Show error message
            validationMessage.textContent = '{{ __("Please fill in all required fields in all language tabs") }}';
            validationErrorsDiv.classList.remove('d-none');
            
            // Switch to first error tab
            if (firstErrorTab) {
                const tabLink = document.getElementById(`${firstErrorTab}-tab-link`);
                const tab = new bootstrap.Tab(tabLink);
                tab.show();
            }
            
            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
            
            return false;
        }

        return true;
    });

    // Remove error styling when user types
    document.querySelectorAll('.translation-title, .translation-content').forEach(field => {
        field.addEventListener('input', function() {
            this.classList.remove('is-invalid');
            const feedback = this.nextElementSibling;
            if (feedback && feedback.classList.contains('invalid-feedback')) {
                feedback.style.display = 'none';
            }
            
            // Remove error badge from tab
            const locale = this.dataset.locale;
            const badge = document.getElementById(`${locale}-error-badge`);
            
            // Check if both title and content are filled for this locale
            const titleInput = document.querySelector(`input[data-locale="${locale}"][data-field="title"]`);
            const contentTextarea = document.querySelector(`textarea[data-locale="${locale}"][data-field="content"]`);
            
            if (titleInput.value.trim() && contentTextarea.value.trim()) {
                if (badge) {
                    badge.classList.add('d-none');
                }
            }
        });
    });
});
</script>
@endpush
@endsection