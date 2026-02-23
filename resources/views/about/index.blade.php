@extends('layouts.app')

@section('title', 'About Us - Real Estate Pro')

@section('content')
<div class="min-h-screen bg-white">
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-primary to-info text-white py-20 position-relative overflow-hidden">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center">
                    <div class="animate__animated animate__fadeInDown">
                        <h1 class="display-3 fw-bold mb-4">About Real Estate Pro</h1>
                        <p class="lead fs-5 mb-5">
                            Your trusted partner in finding the perfect property. We connect buyers, sellers, and renters with experienced real estate professionals across the country.
                        </p>
                        <div class="d-flex justify-content-center gap-3 flex-wrap">
                            <a href="{{ route('properties.index') }}" class="btn btn-light btn-lg">
                                <i class="fas fa-search me-2"></i>Browse Properties
                            </a>
                            <a href="{{ route('agents.directory') }}" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-user-tie me-2"></i>Find Agents
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Decorative Elements -->
        <div class="position-absolute bottom-0 start-0 w-100">
            <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 120L60 110C120 100 240 80 360 70C480 60 600 60 720 65C840 70 960 80 1080 85C1200 90 1320 90 1380 90L1440 90V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0V120Z" fill="white"/>
            </svg>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <div class="animate__animated animate__fadeInLeft">
                        <h2 class="display-5 fw-bold text-primary mb-4">Our Mission</h2>
                        <p class="fs-5 text-muted mb-4">
                            At Real Estate Pro, we're dedicated to making real estate transactions simple, transparent, and successful for everyone involved. Whether you're buying your first home, selling a property, or looking for the perfect rental, we're here to guide you every step of the way.
                        </p>
                        <p class="fs-5 text-muted mb-4">
                            We believe in the power of technology combined with human expertise to create exceptional real estate experiences. Our platform brings together the best agents, cutting-edge tools, and comprehensive property listings to serve your unique needs.
                        </p>
                        <div class="d-flex gap-3">
                            <div class="text-center">
                                <div class="h3 fw-bold text-primary">15+</div>
                                <div class="text-muted">Years Experience</div>
                            </div>
                            <div class="text-center">
                                <div class="h3 fw-bold text-success">98%</div>
                                <div class="text-muted">Client Satisfaction</div>
                            </div>
                            <div class="text-center">
                                <div class="h3 fw-bold text-info">24/7</div>
                                <div class="text-muted">Support Available</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="animate__animated animate__fadeInRight">
                        <div class="bg-light rounded-4 p-5 shadow-lg">
                            <div class="row g-4">
                                <div class="col-6 text-center">
                                    <div class="display-4 fw-bold text-primary mb-2">10,000+</div>
                                    <div class="text-muted">Properties Listed</div>
                                </div>
                                <div class="col-6 text-center">
                                    <div class="display-4 fw-bold text-success mb-2">500+</div>
                                    <div class="text-muted">Expert Agents</div>
                                </div>
                                <div class="col-6 text-center">
                                    <div class="display-4 fw-bold text-info mb-2">5,000+</div>
                                    <div class="text-muted">Happy Clients</div>
                                </div>
                                <div class="col-6 text-center">
                                    <div class="display-4 fw-bold text-warning mb-2">50+</div>
                                    <div class="text-muted">Cities Covered</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold text-primary mb-3">Our Core Values</h2>
                <p class="fs-5 text-muted mx-auto" style="max-width: 600px;">
                    These principles guide everything we do, from how we build our platform to how we serve our clients.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm animate__animated animate__fadeInUp">
                        <div class="card-body text-center p-4">
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-handshake text-primary fs-2"></i>
                            </div>
                            <h4 class="h5 fw-bold text-primary mb-3">Trust</h4>
                            <p class="text-muted">Building lasting relationships through honesty, transparency, and reliability in every interaction.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                        <div class="card-body text-center p-4">
                            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-award text-success fs-2"></i>
                            </div>
                            <h4 class="h5 fw-bold text-success mb-3">Excellence</h4>
                            <p class="text-muted">Delivering exceptional service and results that exceed expectations in every real estate transaction.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                        <div class="card-body text-center p-4">
                            <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-lightbulb text-info fs-2"></i>
                            </div>
                            <h4 class="h5 fw-bold text-info mb-3">Innovation</h4>
                            <p class="text-muted">Embracing technology and creative solutions to make real estate processes more efficient and user-friendly.</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                        <div class="card-body text-center p-4">
                            <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                <i class="fas fa-heart text-warning fs-2"></i>
                            </div>
                            <h4 class="h5 fw-bold text-warning mb-3">Care</h4>
                            <p class="text-muted">Putting our clients' needs first and providing personalized attention to every detail of their journey.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold text-primary mb-3">Meet Our Leadership Team</h2>
                <p class="fs-5 text-muted mx-auto" style="max-width: 600px;">
                    The passionate professionals behind Real Estate Pro, working to revolutionize the real estate industry.
                </p>
            </div>

            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm animate__animated animate__fadeInUp">
                        <div class="card-body text-center p-4">
                            <div class="bg-gradient-to-br from-primary to-info rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 120px; height: 120px;">
                                <span class="text-white fs-1 fw-bold">JD</span>
                            </div>
                            <h4 class="h5 fw-bold mb-2">John Davidson</h4>
                            <p class="text-primary mb-3">CEO & Founder</p>
                            <p class="text-muted">With over 20 years in real estate, John founded Real Estate Pro with a vision to make property transactions seamless for everyone.</p>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="#" class="text-primary"><i class="fab fa-linkedin"></i></a>
                                <a href="#" class="text-primary"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="text-primary"><i class="fas fa-envelope"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                        <div class="card-body text-center p-4">
                            <div class="bg-gradient-to-br from-success to-info rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 120px; height: 120px;">
                                <span class="text-white fs-1 fw-bold">SC</span>
                            </div>
                            <h4 class="h5 fw-bold mb-2">Sarah Chen</h4>
                            <p class="text-success mb-3">Chief Technology Officer</p>
                            <p class="text-muted">Sarah leads our technology innovation, ensuring our platform remains cutting-edge and user-friendly for all users.</p>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="#" class="text-success"><i class="fab fa-linkedin"></i></a>
                                <a href="#" class="text-success"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="text-success"><i class="fas fa-envelope"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                        <div class="card-body text-center p-4">
                            <div class="bg-gradient-to-br from-warning to-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 120px; height: 120px;">
                                <span class="text-white fs-1 fw-bold">MR</span>
                            </div>
                            <h4 class="h5 fw-bold mb-2">Michael Rodriguez</h4>
                            <p class="text-warning mb-3">Head of Operations</p>
                            <p class="text-muted">Michael ensures smooth operations across all departments, maintaining our high standards of service delivery.</p>
                            <div class="d-flex justify-content-center gap-2">
                                <a href="#" class="text-warning"><i class="fab fa-linkedin"></i></a>
                                <a href="#" class="text-warning"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="text-warning"><i class="fas fa-envelope"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-gradient-to-r from-primary to-info text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="display-5 fw-bold mb-4">Ready to Start Your Real Estate Journey?</h2>
                    <p class="fs-5 mb-4">Join thousands of satisfied clients who have found their perfect properties through our platform.</p>
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="{{ route('properties.index') }}" class="btn btn-light btn-lg px-4">
                            <i class="fas fa-search me-2"></i>Browse Properties
                        </a>
                        <a href="{{ route('agents.directory') }}" class="btn btn-outline-light btn-lg px-4">
                            <i class="fas fa-user-tie me-2"></i>Find an Agent
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add Animate.css for animations -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
@endsection
