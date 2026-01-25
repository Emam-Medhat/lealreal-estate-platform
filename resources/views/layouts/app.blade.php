<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel Real Estate Platform'))</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #0dcaf0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: box-shadow 0.15s ease-in-out;
        }
        
        .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
        
        .btn {
            border-radius: 0.375rem;
            font-weight: 500;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #0b5ed7;
            border-color: #0a58ca;
        }
        
        .text-primary {
            color: var(--primary-color) !important;
        }
        
        .bg-primary {
            background-color: var(--primary-color) !important;
        }
        
        .alert-purple {
            background-color: #6f42c1;
            color: white;
            border: none;
        }
        
        .badge.bg-purple {
            background-color: #6f42c1 !important;
        }
        
        .property-card {
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        
        .property-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        
        .property-image {
            transition: transform 0.3s ease-in-out;
        }
        
        .property-card:hover .property-image {
            transform: scale(1.05);
        }
        
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 1rem;
        }
        
        .footer {
            background-color: #343a40;
            color: white;
        }
        
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
        }
        
        .custom-toast {
            min-width: 250px;
        }
    </style>
    
    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 font-sans antialiased">

@unless(isset($showNavbar) && $showNavbar === false)
<!-- Navigation -->
<nav class="bg-gradient-to-r from-blue-600 to-indigo-700 shadow-lg sticky top-0 z-50">
    <div class="container mx-auto px-6">
        <div class="flex justify-between items-center py-4">
            <!-- Logo -->
            <div class="flex items-center">
                <i class="fas fa-building text-white text-2xl"></i>
                <span class="ml-3 text-xl font-bold text-white">Real Estate Pro</span>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-6">
                <a href="/" class="text-white hover:text-blue-200 transition font-medium">{{ __('Home') }}</a>
                
                @guest
                    <a href="{{ route('login') }}" class="text-white hover:text-blue-200 transition font-medium">{{ __('Login') }}</a>
                    <a href="{{ route('register') }}" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-semibold hover:bg-blue-50 transition">{{ __('Register') }}</a>
                @else
                    <!-- Public Routes -->
                    <a href="{{ route('properties.index') }}" class="text-white hover:text-blue-200 transition font-medium">{{ __('Properties') }}</a>
                    <a href="{{ route('agents.directory') }}" class="text-white hover:text-blue-200 transition font-medium">{{ __('Agents') }}</a>
                    <a href="{{ route('about') }}" class="text-white hover:text-blue-200 transition font-medium">{{ __('About') }}</a>
                    <a href="{{ route('contact') }}" class="text-white hover:text-blue-200 transition font-medium">{{ __('Contact') }}</a>
                    
                    <!-- Agent Routes -->
                    @if(Auth::user()->is_agent || Auth::user()->user_type === 'agent')
                        <div class="relative group">
                            <button class="text-white hover:text-blue-200 transition font-medium flex items-center">
                                {{ __('Agent Panel') }}
                                <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            </button>
                            <div class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                <!-- Dashboard -->
                                <a href="{{ route('agent.dashboard') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 rounded-t-lg">
                                    <i class="fas fa-tachometer-alt ml-2"></i> {{ __('Dashboard') }}
                                </a>
                                
                                <!-- Properties Section -->
                                <div class="border-t border-gray-100">
                                    <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        {{ __('Properties') }}
                                    </div>
                                    <a href="{{ route('agent.properties.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50">
                                        <i class="fas fa-home ml-2"></i> {{ __('All Properties') }}
                                    </a>
                                    <a href="{{ route('agent.properties.create') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50">
                                        <i class="fas fa-plus ml-2"></i> {{ __('Add Property') }}
                                    </a>
                                </div>
                                
                                <!-- CRM Section -->
                                <div class="border-t border-gray-100">
                                    <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        {{ __('CRM') }}
                                    </div>
                                    <a href="{{ route('agent.crm.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50">
                                        <i class="fas fa-users ml-2"></i> {{ __('All Leads') }}
                                    </a>
                                    <a href="{{ route('agent.crm.create') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50">
                                        <i class="fas fa-user-plus ml-2"></i> {{ __('Add Lead') }}
                                    </a>
                                </div>
                                
                                <!-- Appointments Section -->
                                <div class="border-t border-gray-100">
                                    <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        {{ __('Appointments') }}
                                    </div>
                                    <a href="{{ route('agent.appointments.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50">
                                        <i class="fas fa-calendar ml-2"></i> {{ __('All Appointments') }}
                                    </a>
                                    <a href="{{ route('agent.appointments.calendar') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50">
                                        <i class="fas fa-calendar-alt ml-2"></i> {{ __('Calendar View') }}
                                    </a>
                                    <a href="{{ route('agent.appointments.create') }}" class="block px-4 py-2 text-gray-700 hover:bg-blue-50 rounded-b-lg">
                                        <i class="fas fa-plus-circle ml-2"></i> {{ __('Schedule Appointment') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    <!-- User Dashboard -->
                    <a href="{{ route('dashboard') }}" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-semibold hover:bg-blue-50 transition">{{ __('Dashboard') }}</a>
                    
                    <!-- User Menu -->
                    <div class="relative group">
                        <button class="text-white hover:text-blue-200 transition flex items-center">
                            <i class="fas fa-user-circle text-xl"></i>
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                            <a href="{{ route('dashboard.profile') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 rounded-t-lg">
                                <i class="fas fa-user ml-2"></i> {{ __('Profile') }}
                            </a>
                            <a href="{{ route('dashboard.settings') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-cog ml-2"></i> {{ __('Settings') }}
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="border-t">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 rounded-b-lg">
                                    <i class="fas fa-sign-out-alt ml-2"></i> {{ __('Logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endguest
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button class="text-white focus:outline-none" onclick="toggleMobileMenu()">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden md:hidden pb-4">
            @guest
                <a href="/" class="block py-2 text-white hover:text-blue-200">{{ __('Home') }}</a>
                <a href="{{ route('login') }}" class="block py-2 text-white hover:text-blue-200">{{ __('Login') }}</a>
                <a href="{{ route('register') }}" class="block py-2 text-white hover:text-blue-200">{{ __('Register') }}</a>
            @else
                <a href="/" class="block py-2 text-white hover:text-blue-200">{{ __('Home') }}</a>
                <a href="{{ route('properties.index') }}" class="block py-2 text-white hover:text-blue-200">{{ __('Properties') }}</a>
                <a href="{{ route('agents.directory') }}" class="block py-2 text-white hover:text-blue-200">{{ __('Agents') }}</a>
                <a href="{{ route('about') }}" class="block py-2 text-white hover:text-blue-200">{{ __('About') }}</a>
                <a href="{{ route('contact') }}" class="block py-2 text-white hover:text-blue-200">{{ __('Contact') }}</a>
                
                @if(Auth::user()->is_agent || Auth::user()->user_type === 'agent')
                    <div class="border-t border-blue-400 pt-2 mt-2">
                        <p class="text-blue-200 font-semibold mb-2">{{ __('Agent Panel') }}</p>
                        <a href="{{ route('agent.dashboard') }}" class="block py-1 text-blue-100 hover:text-white pl-4">{{ __('Dashboard') }}</a>
                        
                        <div class="border-t border-blue-300 pt-1 mt-2">
                            <p class="text-blue-200 text-sm font-medium mb-1 pl-4">{{ __('Properties') }}</p>
                            <a href="{{ route('agent.properties.index') }}" class="block py-1 text-blue-100 hover:text-white pl-8">{{ __('All Properties') }}</a>
                            <a href="{{ route('agent.properties.create') }}" class="block py-1 text-blue-100 hover:text-white pl-8">{{ __('Add Property') }}</a>
                        </div>
                        
                        <div class="border-t border-blue-300 pt-1 mt-2">
                            <p class="text-blue-200 text-sm font-medium mb-1 pl-4">{{ __('CRM') }}</p>
                            <a href="{{ route('agent.crm.index') }}" class="block py-1 text-blue-100 hover:text-white pl-8">{{ __('All Leads') }}</a>
                            <a href="{{ route('agent.crm.create') }}" class="block py-1 text-blue-100 hover:text-white pl-8">{{ __('Add Lead') }}</a>
                        </div>
                        
                        <div class="border-t border-blue-300 pt-1 mt-2">
                            <p class="text-blue-200 text-sm font-medium mb-1 pl-4">{{ __('Appointments') }}</p>
                            <a href="{{ route('agent.appointments.index') }}" class="block py-1 text-blue-100 hover:text-white pl-8">{{ __('All Appointments') }}</a>
                            <a href="{{ route('agent.appointments.calendar') }}" class="block py-1 text-blue-100 hover:text-white pl-8">{{ __('Calendar View') }}</a>
                            <a href="{{ route('agent.appointments.create') }}" class="block py-1 text-blue-100 hover:text-white pl-8">{{ __('Schedule Appointment') }}</a>
                        </div>
                    </div>
                @endif
                
                <div class="border-t border-blue-400 pt-2 mt-2">
                    <a href="{{ route('dashboard') }}" class="block py-2 text-white hover:text-blue-200">{{ __('Dashboard') }}</a>
                    <a href="{{ route('dashboard.profile') }}" class="block py-2 text-white hover:text-blue-200">{{ __('Profile') }}</a>
                    <a href="{{ route('dashboard.settings') }}" class="block py-2 text-white hover:text-blue-200">{{ __('Settings') }}</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block py-2 text-red-300 hover:text-red-200">{{ __('Logout') }}</button>
                    </form>
                </div>
            @endguest
        </div>
    </div>
</nav>

<script>
function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    menu.classList.toggle('hidden');
}
</script>
@endunless

<!-- Flash Messages -->
@if (session('success'))
    <div class="bg-green-50 border-l-4 border-green-400 p-4 m-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    </div>
@endif

@if (session('error'))
    <div class="bg-red-50 border-l-4 border-red-400 p-4 m-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700">{{ session('error') }}</p>
            </div>
        </div>
    </div>
@endif

<!-- Main Content -->
<main>
    @yield('content')
</main>

<!-- Footer -->
<footer class="bg-gray-800 text-white py-12">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Company Info -->
            <div>
                <div class="flex items-center mb-4">
                    <i class="fas fa-building text-blue-400 text-2xl"></i>
                    <span class="ml-3 text-xl font-bold">Real Estate Pro</span>
                </div>
                <p class="text-gray-400">{{ __('Your trusted partner in finding the perfect property.') }}</p>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="text-lg font-semibold mb-4">{{ __('Quick Links') }}</h3>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#" class="hover:text-white transition">{{ __('About Us') }}</a></li>
                    <li><a href="#" class="hover:text-white transition">{{ __('Properties') }}</a></li>
                    <li><a href="#" class="hover:text-white transition">{{ __('Agents') }}</a></li>
                    <li><a href="#" class="hover:text-white transition">{{ __('Contact') }}</a></li>
                </ul>
            </div>

            <!-- Services -->
            <div>
                <h3 class="text-lg font-semibold mb-4">{{ __('Services') }}</h3>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#" class="hover:text-white transition">{{ __('Buy Property') }}</a></li>
                    <li><a href="#" class="hover:text-white transition">{{ __('Sell Property') }}</a></li>
                    <li><a href="#" class="hover:text-white transition">{{ __('Property Management') }}</a></li>
                    <li><a href="#" class="hover:text-white transition">{{ __('Consultation') }}</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h3 class="text-lg font-semibold mb-4">{{ __('Contact Us') }}</h3>
                <ul class="space-y-2 text-gray-400">
                    <li class="flex items-center">
                        <i class="fas fa-phone mr-2"></i>
                        +1 234 567 8900
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-envelope mr-2"></i>
                        info@realestatepro.com
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-map-marker-alt mr-2"></i>
                        123 Business St, City, State
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; {{ date('Y') }} Real Estate Pro. {{ __('All rights reserved.') }}</p>
        </div>
    </div>
</footer>

</body>
</html>