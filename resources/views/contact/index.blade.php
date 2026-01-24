@extends('layouts.app')

@section('title', 'Contact Us - Real Estate Pro')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-16">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-6">Get in Touch</h1>
                <p class="text-xl max-w-3xl mx-auto">
                    Have questions about our services? Need help with your property search? We're here to help. Reach out to us and we'll respond as soon as possible.
                </p>
            </div>
        </div>
    </section>

    <!-- Contact Content -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <!-- Contact Form -->
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Send us a Message</h2>
                    
                    @if(session('success'))
                        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('contact.send') }}" method="POST" class="space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Your Name <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="text" 
                                    id="name" 
                                    name="name" 
                                    required
                                    value="{{ old('name') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="John Doe"
                                >
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email" 
                                    required
                                    value="{{ old('email') }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="john@example.com"
                                >
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number
                            </label>
                            <input 
                                type="tel" 
                                id="phone" 
                                name="phone" 
                                value="{{ old('phone') }}"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="+1 (555) 123-4567"
                            >
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                                Subject <span class="text-red-500">*</span>
                            </label>
                            <select 
                                id="subject" 
                                name="subject" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">Select a subject</option>
                                <option value="General Inquiry" {{ old('subject') == 'General Inquiry' ? 'selected' : '' }}>General Inquiry</option>
                                <option value="Property Listing" {{ old('subject') == 'Property Listing' ? 'selected' : '' }}>Property Listing</option>
                                <option value="Agent Services" {{ old('subject') == 'Agent Services' ? 'selected' : '' }}>Agent Services</option>
                                <option value="Technical Support" {{ old('subject') == 'Technical Support' ? 'selected' : '' }}>Technical Support</option>
                                <option value="Partnership" {{ old('subject') == 'Partnership' ? 'selected' : '' }}>Partnership</option>
                                <option value="Other" {{ old('subject') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('subject')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                                Message <span class="text-red-500">*</span>
                            </label>
                            <textarea 
                                id="message" 
                                name="message" 
                                rows="6" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                placeholder="Tell us more about your inquiry..."
                            >{{ old('message') }}</textarea>
                            @error('message')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="w-full px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Send Message
                        </button>
                    </form>
                </div>

                <!-- Contact Information -->
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Contact Information</h2>
                    
                    <div class="space-y-8">
                        <!-- Office Location -->
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Office Location</h3>
                                <p class="text-gray-600">
                                    123 Business Avenue<br>
                                    Suite 456<br>
                                    New York, NY 10001<br>
                                    United States
                                </p>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-phone text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Phone</h3>
                                <p class="text-gray-600">
                                    Main: +1 (555) 123-4567<br>
                                    Toll-free: +1 (800) 123-4567
                                </p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-envelope text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Email</h3>
                                <p class="text-gray-600">
                                    General: info@realestatepro.com<br>
                                    Support: support@realestatepro.com<br>
                                    Sales: sales@realestatepro.com
                                </p>
                            </div>
                        </div>

                        <!-- Business Hours -->
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-clock text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Business Hours</h3>
                                <p class="text-gray-600">
                                    Monday - Friday: 9:00 AM - 6:00 PM<br>
                                    Saturday: 10:00 AM - 4:00 PM<br>
                                    Sunday: Closed
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div class="mt-12">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Quick Links</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <a href="{{ route('properties.index') }}" class="text-blue-600 hover:text-blue-700 flex items-center">
                                <i class="fas fa-home mr-2"></i>
                                Browse Properties
                            </a>
                            <a href="{{ route('agents.directory') }}" class="text-blue-600 hover:text-blue-700 flex items-center">
                                <i class="fas fa-users mr-2"></i>
                                Find Agents
                            </a>
                            <a href="{{ route('about') }}" class="text-blue-600 hover:text-blue-700 flex items-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                About Us
                            </a>
                            <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700 flex items-center">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                Sign In
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Frequently Asked Questions</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Quick answers to common questions about our services and platform.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">How do I find a reliable real estate agent?</h3>
                    <p class="text-gray-600">
                        Use our agent directory to browse verified agents with ratings, reviews, and specializations. You can filter by location, expertise, and client feedback to find the perfect match.
                    </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Is it free to use Real Estate Pro?</h3>
                    <p class="text-gray-600">
                        Yes! Our platform is free for buyers and renters to search properties and connect with agents. Agents pay a subscription fee for premium features and enhanced visibility.
                    </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">How quickly will you respond to my inquiry?</h3>
                    <p class="text-gray-600">
                        We typically respond to all inquiries within 24 hours during business days. For urgent matters, please call our office directly for immediate assistance.
                    </p>
                </div>

                <div class="bg-gray-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Can I list my property on your platform?</h3>
                    <p class="text-gray-600">
                        Properties are listed through our network of verified agents. Contact an agent to discuss listing your property, or reach out to us for recommendations based on your location.
                    </p>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
