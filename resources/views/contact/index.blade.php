{{-- resources/views/contact/index.blade.php --}}
@extends('layouts.app')

@section('title', __('Contact Us'))

@section('content')
<!-- Contact Hero Section -->
<section class="contact-hero py-5" style="background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);">
    <div class="container">
        <div class="row justify-content-center text-center text-white">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">{{ __('Get in Touch') }}</h1>
                <p class="lead mb-0">{{ __('Have a question or feedback? We\'d love to hear from you!') }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Content Section -->
<section class="py-5">
    <div class="container">
        <div class="row g-4">
            <!-- Contact Form -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="h3 mb-4">{{ __('Send Us a Message') }}</h2>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-x-circle me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <form action="{{ route('contact.store') }}" method="POST" id="contactForm">
                            @csrf

                            <div class="row g-3">
                                <!-- Name -->
                                <div class="col-md-6">
                                    <label for="name" class="form-label">
                                        {{ __('Full Name') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', auth()->check() ? auth()->user()->name : '') }}"
                                           placeholder="{{ __('John Doe') }}"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label for="email" class="form-label">
                                        {{ __('Email Address') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', auth()->check() ? auth()->user()->email : '') }}"
                                           placeholder="{{ __('john@example.com') }}"
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">
                                        {{ __('Phone Number') }} <span class="text-muted">({{ __('Optional') }})</span>
                                    </label>
                                    <input type="tel" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" 
                                           name="phone" 
                                           value="{{ old('phone') }}"
                                           placeholder="+1 (555) 123-4567">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Subject -->
                                <div class="col-md-6">
                                    <label for="subject" class="form-label">
                                        {{ __('Subject') }} <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('subject') is-invalid @enderror" 
                                            id="subject" 
                                            name="subject" 
                                            required>
                                        <option value="">{{ __('Select a subject') }}</option>
                                        <option value="General Inquiry" {{ old('subject') == 'General Inquiry' ? 'selected' : '' }}>
                                            {{ __('General Inquiry') }}
                                        </option>
                                        <option value="Product Question" {{ old('subject') == 'Product Question' ? 'selected' : '' }}>
                                            {{ __('Product Question') }}
                                        </option>
                                        <option value="Order Support" {{ old('subject') == 'Order Support' ? 'selected' : '' }}>
                                            {{ __('Order Support') }}
                                        </option>
                                        <option value="Shipping & Delivery" {{ old('subject') == 'Shipping & Delivery' ? 'selected' : '' }}>
                                            {{ __('Shipping & Delivery') }}
                                        </option>
                                        <option value="Returns & Refunds" {{ old('subject') == 'Returns & Refunds' ? 'selected' : '' }}>
                                            {{ __('Returns & Refunds') }}
                                        </option>
                                        <option value="Technical Issue" {{ old('subject') == 'Technical Issue' ? 'selected' : '' }}>
                                            {{ __('Technical Issue') }}
                                        </option>
                                        <option value="Partnership Opportunity" {{ old('subject') == 'Partnership Opportunity' ? 'selected' : '' }}>
                                            {{ __('Partnership Opportunity') }}
                                        </option>
                                        <option value="Feedback" {{ old('subject') == 'Feedback' ? 'selected' : '' }}>
                                            {{ __('Feedback') }}
                                        </option>
                                        <option value="Other" {{ old('subject') == 'Other' ? 'selected' : '' }}>
                                            {{ __('Other') }}
                                        </option>
                                    </select>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Message -->
                                <div class="col-12">
                                    <label for="message" class="form-label">
                                        {{ __('Message') }} <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('message') is-invalid @enderror" 
                                              id="message" 
                                              name="message" 
                                              rows="6" 
                                              placeholder="{{ __('Tell us more about your inquiry...') }}"
                                              required>{{ old('message') }}</textarea>
                                    <small class="form-text text-muted">
                                        {{ __('Minimum 10 characters, maximum 2000 characters') }}
                                    </small>
                                    @error('message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Submit Button -->
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
                                        <i class="bi bi-send me-2"></i>
                                        {{ __('Send Message') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Contact Information Sidebar -->
            <div class="col-lg-4">
                <!-- Contact Details -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h3 class="h5 mb-4">{{ __('Contact Information') }}</h3>

                        <!-- Address -->
                        <div class="d-flex mb-4">
                            <div class="flex-shrink-0">
                                <div class="contact-icon">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="h6 mb-1">{{ __('Address') }}</h4>
                                <p class="text-muted mb-0 small">
                                    123 Business Street<br>
                                    Suite 100<br>
                                    City, State 12345
                                </p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="d-flex mb-4">
                            <div class="flex-shrink-0">
                                <div class="contact-icon">
                                    <i class="bi bi-envelope"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="h6 mb-1">{{ __('Email') }}</h4>
                                <a href="mailto:info@example.com" class="text-muted text-decoration-none small">
                                    info@example.com
                                </a>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="d-flex mb-4">
                            <div class="flex-shrink-0">
                                <div class="contact-icon">
                                    <i class="bi bi-telephone"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="h6 mb-1">{{ __('Phone') }}</h4>
                                <a href="tel:+15551234567" class="text-muted text-decoration-none small">
                                    +1 (555) 123-4567
                                </a>
                            </div>
                        </div>

                        <!-- Working Hours -->
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="contact-icon">
                                    <i class="bi bi-clock"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="h6 mb-1">{{ __('Working Hours') }}</h4>
                                <p class="text-muted mb-0 small">
                                    {{ __('Monday - Friday') }}: 9:00 AM - 6:00 PM<br>
                                    {{ __('Saturday') }}: 10:00 AM - 4:00 PM<br>
                                    {{ __('Sunday') }}: {{ __('Closed') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h3 class="h5 mb-4">{{ __('Follow Us') }}</h3>
                        <div class="d-flex gap-3">
                            <a href="#" class="btn btn-outline-primary btn-sm rounded-circle" style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a href="#" class="btn btn-outline-primary btn-sm rounded-circle" style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-twitter"></i>
                            </a>
                            <a href="#" class="btn btn-outline-primary btn-sm rounded-circle" style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-instagram"></i>
                            </a>
                            <a href="#" class="btn btn-outline-primary btn-sm rounded-circle" style="width: 40px; height: 40px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                <i class="bi bi-linkedin"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Response Time -->
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #20b2aa 0%, #1a8f88 100%); color: white;">
                    <div class="card-body p-4 text-center">
                        <i class="bi bi-lightning-charge" style="font-size: 2.5rem;"></i>
                        <h3 class="h5 mt-3 mb-2">{{ __('Quick Response') }}</h3>
                        <p class="mb-0 small">
                            {{ __('We typically respond within 24-48 hours during business days') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section (Optional) -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm overflow-hidden">
                    <div style="height: 400px; background-color: #e9ecef;">
                        <!-- Embed your Google Maps iframe here -->
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3151.8354345093716!2d144.9537353153167!3d-37.81720997975171!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x6ad65d4c2b349649%3A0xb6899234e561db11!2sEnvato!5e0!3m2!1sen!2s!4v1234567890123!5m2!1sen!2s" 
                            width="100%" 
                            height="400" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center mb-4">
            <div class="col-lg-8 text-center">
                <h2 class="mb-3">{{ __('Frequently Asked Questions') }}</h2>
                <p class="text-muted">{{ __('Find quick answers to common questions') }}</p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <!-- FAQ Item 1 -->
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                {{ __('What are your shipping times?') }}
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                {{ __('We typically process orders within 1-2 business days. Shipping times vary based on your location, but most domestic orders arrive within 3-5 business days.') }}
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                {{ __('What is your return policy?') }}
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                {{ __('We offer a 30-day return policy for most items. Products must be unused and in original packaging. Contact us to initiate a return.') }}
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                {{ __('Do you ship internationally?') }}
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                {{ __('Yes, we ship to most countries worldwide. International shipping times vary by destination, typically 7-14 business days.') }}
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 4 -->
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                {{ __('How can I track my order?') }}
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                {{ __('Once your order ships, you\'ll receive a tracking number via email. You can also track your order by logging into your account.') }}
                            </div>
                        </div>
                    </div>

                    <!-- FAQ Item 5 -->
                    <div class="accordion-item border-0 shadow-sm">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                {{ __('What payment methods do you accept?') }}
                            </button>
                        </h2>
                        <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                {{ __('We accept all major credit cards, debit cards, and PayPal. All transactions are secure and encrypted.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.contact-icon {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #20b2aa 0%, #1a8f88 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}

.accordion-button:not(.collapsed) {
    background-color: #20b2aa;
    color: white;
}

.accordion-button:focus {
    border-color: #20b2aa;
    box-shadow: 0 0 0 0.25rem rgba(32, 178, 170, 0.25);
}
</style>
@endsection