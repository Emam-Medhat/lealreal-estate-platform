@extends('layouts.app')

@section('title', 'About Us - Real Estate Pro')

@section('content')
<div class="min-h-screen bg-white">
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-6">About Real Estate Pro</h1>
                <p class="text-xl max-w-3xl mx-auto">
                    Your trusted partner in finding the perfect property. We connect buyers, sellers, and renters with experienced real estate professionals across the country.
                </p>
            </div>
        </div>
    </section>

    <!-- Mission Section -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Our Mission</h2>
                    <p class="text-lg text-gray-700 mb-6">
                        At Real Estate Pro, we're dedicated to making real estate transactions simple, transparent, and successful for everyone involved. Whether you're buying your first home, selling a property, or looking for the perfect rental, we're here to guide you every step of the way.
                    </p>
                    <p class="text-lg text-gray-700 mb-6">
                        We believe in the power of technology combined with human expertise to create exceptional real estate experiences. Our platform brings together the best agents, cutting-edge tools, and comprehensive property listings to serve your unique needs.
                    </p>
                </div>
                <div class="bg-gray-100 rounded-2xl p-8">
                    <div class="grid grid-cols-2 gap-8">
                        <div class="text-center">
                            <div class="text-4xl font-bold text-blue-600 mb-2">10,000+</div>
                            <div class="text-gray-600">Properties Listed</div>
                        </div>
                        <div class="text-center">
                            <div class="text-4xl font-bold text-blue-600 mb-2">500+</div>
                            <div class="text-gray-600">Expert Agents</div>
                        </div>
                        <div class="text-center">
                            <div class="text-4xl font-bold text-blue-600 mb-2">5,000+</div>
                            <div class="text-gray-600">Happy Clients</div>
                        </div>
                        <div class="text-center">
                            <div class="text-4xl font-bold text-blue-600 mb-2">15+</div>
                            <div class="text-gray-600">Years Experience</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Values Section -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Our Core Values</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    These principles guide everything we do, from how we build our platform to how we serve our clients.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-handshake text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Trust</h3>
                    <p class="text-gray-600">Building lasting relationships through honesty, transparency, and reliability in every interaction.</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-award text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Excellence</h3>
                    <p class="text-gray-600">Delivering exceptional service and results that exceed expectations in every real estate transaction.</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-lightbulb text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Innovation</h3>
                    <p class="text-gray-600">Embracing technology and creative solutions to make real estate processes more efficient and user-friendly.</p>
                </div>

                <div class="text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-heart text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Care</h3>
                    <p class="text-gray-600">Putting our clients' needs first and providing personalized attention to every detail of their journey.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Meet Our Leadership Team</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    The passionate professionals behind Real Estate Pro, working to revolutionize the real estate industry.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-32 h-32 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white text-4xl font-bold">JD</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">John Davidson</h3>
                    <p class="text-blue-600 mb-3">CEO & Founder</p>
                    <p class="text-gray-600">With over 20 years in real estate, John founded Real Estate Pro with a vision to make property transactions seamless for everyone.</p>
                </div>

                <div class="text-center">
                    <div class="w-32 h-32 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white text-4xl font-bold">SC</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Sarah Chen</h3>
                    <p class="text-blue-600 mb-3">Chief Technology Officer</p>
                    <p class="text-gray-600">Sarah leads our technology innovation, ensuring our platform remains cutting-edge and user-friendly for all users.</p>
                </div>

                <div class="text-center">
                    <div class="w-32 h-32 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="text-white text-4xl font-bold">MR</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Michael Rodriguez</h3>
                    <p class="text-blue-600 mb-3">Head of Operations</p>
                    <p class="text-gray-600">Michael ensures smooth operations across all departments, maintaining our high standards of service delivery.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-blue-600 text-white py-16">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl font-bold mb-4">Ready to Start Your Real Estate Journey?</h2>
            <p class="text-xl mb-8">Join thousands of satisfied clients who have found their perfect properties through our platform.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('properties.index') }}" class="px-8 py-3 bg-white text-blue-600 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                    Browse Properties
                </a>
                <a href="{{ route('agents.directory') }}" class="px-8 py-3 bg-blue-700 text-white rounded-lg font-semibold hover:bg-blue-800 transition-colors">
                    Find an Agent
                </a>
            </div>
        </div>
    </section>
</div>
@endsection
