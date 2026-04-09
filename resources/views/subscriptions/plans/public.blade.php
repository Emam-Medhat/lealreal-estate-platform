@extends('layouts.app')

@section('title', 'Subscription Plans - ' . config('app.name'))

@section('content')
<div class="container-fluid py-5">
    <!-- Hero Section -->
    <section class="text-center mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold text-primary mb-4">
                    <i class="fas fa-crown me-3"></i>Subscription Plans
                </h1>
                <p class="lead text-muted mb-4">
                    Choose the perfect plan for your real estate needs. All plans include core features with varying levels of support and advanced capabilities.
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <span class="badge bg-success fs-6">
                        <i class="fas fa-check-circle me-2"></i>30-Day Free Trial
                    </span>
                    <span class="badge bg-info fs-6">
                        <i class="fas fa-shield-alt me-2"></i>Secure Payment
                    </span>
                    <span class="badge bg-warning fs-6">
                        <i class="fas fa-headset me-2"></i>24/7 Support
                    </span>
                </div>
            </div>
        </div>
    </section>

    @if($plans->count() > 0)
        <!-- Pricing Cards -->
        <section class="mb-5">
            <div class="row g-4 justify-content-center">
                @foreach($plans as $plan)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-lg @if($plan->is_popular) border-primary border-2 @endif position-relative">
                            @if($plan->is_popular)
                                <div class="position-absolute top-0 start-50 translate-middle mt-3">
                                    <span class="badge bg-primary text-white fs-6 px-3 py-2">
                                        <i class="fas fa-star me-2"></i>Most Popular
                                    </span>
                                </div>
                            @endif
                            
                            <div class="card-body text-center p-4">
                                <!-- Plan Icon -->
                                <div class="mb-4">
                                    @if($plan->name === 'Basic')
                                        <i class="fas fa-home fa-3x text-primary"></i>
                                    @elseif($plan->name === 'Professional')
                                        <i class="fas fa-building fa-3x text-success"></i>
                                    @else
                                        <i class="fas fa-city fa-3x text-warning"></i>
                                    @endif
                                </div>
                                
                                <!-- Plan Name -->
                                <h3 class="card-title h2 fw-bold mb-3">{{ $plan->name }}</h3>
                                
                                <!-- Plan Description -->
                                <p class="text-muted mb-4">{{ $plan->description }}</p>
                                
                                <!-- Price -->
                                <div class="mb-4">
                                    <div class="h1 fw-bold text-primary">
                                        ${{ number_format($plan->price, 2) }}
                                    </div>
                                    <div class="text-muted">
                                        <small>per {{ $plan->billing_cycle }}</small>
                                        @if($plan->trial_days > 0)
                                            <div class="text-success fw-semibold mt-2">
                                                <i class="fas fa-gift me-2"></i>{{ $plan->trial_days }} days free trial
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <!-- Features -->
                                @if($plan->features)
                                    <div class="mb-4 text-start">
                                        <h5 class="fw-bold mb-3">
                                            <i class="fas fa-check-circle text-success me-2"></i>What's Included:
                                        </h5>
                                        <ul class="list-unstyled">
                                            @foreach(json_decode($plan->features) as $feature)
                                                <li class="mb-2">
                                                    <i class="fas fa-check text-success me-2"></i>
                                                    <span>{{ $feature }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                
                                <!-- Action Buttons -->
                                <div class="d-grid gap-2">
                                    @if($plan->is_active)
                                        @if(auth()->check())
                                            <form action="{{ route('subscriptions.subscribe', $plan) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-primary btn-lg fw-semibold">
                                                    <i class="fas fa-rocket me-2"></i>Get Started
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('login') }}" class="btn btn-primary btn-lg fw-semibold">
                                                <i class="fas fa-sign-in-alt me-2"></i>Login to Subscribe
                                            </a>
                                        @endif
                                    @else
                                        <button disabled class="btn btn-secondary btn-lg fw-semibold">
                                            <i class="fas fa-clock me-2"></i>Coming Soon
                                        </button>
                                    @endif
                                    
                                    <button class="btn btn-outline-primary" onclick="showPlanDetails('{{ $plan->id }}')">
                                        <i class="fas fa-info-circle me-2"></i>View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- Compare Section -->
        <section class="text-center mb-5">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card bg-light">
                        <div class="card-body p-4">
                            <h4 class="fw-bold mb-3">
                                <i class="fas fa-balance-scale me-2"></i>Not sure which plan to choose?
                            </h4>
                            <p class="text-muted mb-4">
                                Our team is here to help you find the perfect solution for your real estate business.
                            </p>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                <a href="{{ route('contact') }}" class="btn btn-primary btn-lg">
                                    <i class="fas fa-phone me-2"></i>Contact Sales
                                </a>
                                <button class="btn btn-outline-primary btn-lg" onclick="comparePlans()">
                                    <i class="fas fa-exchange-alt me-2"></i>Compare Plans
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="mb-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h3 class="text-center fw-bold mb-4">
                        <i class="fas fa-question-circle me-2"></i>Frequently Asked Questions
                    </h3>
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    Can I change my plan later?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes! You can upgrade or downgrade your plan at any time. Changes take effect immediately.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Is there a contract?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    No, we offer month-to-month subscriptions. You can cancel anytime without penalties.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    What payment methods do you accept?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    We accept all major credit cards, PayPal, and bank transfers for enterprise plans.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @else
        <!-- No Plans Available -->
        <section class="text-center py-5">
            <div class="row justify-content-center">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body p-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-4"></i>
                            <h3 class="h2 fw-bold mb-3">No Subscription Plans Available</h3>
                            <p class="text-muted mb-4">
                                We're working on our subscription plans. Please check back soon or contact our sales team for custom solutions.
                            </p>
                            <a href="{{ route('contact') }}" class="btn btn-primary btn-lg">
                                <i class="fas fa-envelope me-2"></i>Contact Us
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif
</div>

<!-- JavaScript -->
<script>
function handleSubscription(planId) {
    // Handle subscription logic here
    alert('Subscription feature coming soon! Plan ID: ' + planId);
}

function showPlanDetails(planId) {
    // Show plan details modal or navigate to details page
    alert('Plan details coming soon! Plan ID: ' + planId);
}

function comparePlans() {
    // Navigate to compare page
    window.location.href = '{{ route("subscriptions.plans.compare") }}';
}
</script>
@endsection
