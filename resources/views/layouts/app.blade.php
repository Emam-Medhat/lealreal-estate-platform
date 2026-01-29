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
        
        /* Remove underline from all links */
        a {
            text-decoration: none !important;
        }
        
        a:hover {
            text-decoration: none !important;
        }
    </style>
    
    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Navigation Functions -->
    <script>
    console.log('Layout script loaded');

    function toggleMobileMenu() {
        const menu = document.getElementById('mobileMenu');
        if (menu) {
            menu.classList.toggle('hidden');
        }
    }

    function toggleUserMenu() {
        try {
            console.log('toggleUserMenu called');
            const dropdown = document.getElementById('userDropdown');
            if (!dropdown) {
                console.error('userDropdown element not found');
                return;
            }
            
            if (dropdown.classList.contains('opacity-0')) {
                dropdown.classList.remove('opacity-0', 'invisible');
                dropdown.classList.add('opacity-100', 'visible');
                console.log('Dropdown opened');
            } else {
                dropdown.classList.remove('opacity-100', 'visible');
                dropdown.classList.add('opacity-0', 'invisible');
                console.log('Dropdown closed');
            }
        } catch (error) {
            console.error('Error in toggleUserMenu:', error);
        }
    }

    function toggleNotifications() {
        try {
            const dropdown = document.getElementById('notificationsDropdown');
            if (dropdown) {
                if (dropdown.classList.contains('opacity-0')) {
                    dropdown.classList.remove('opacity-0', 'invisible');
                    dropdown.classList.add('opacity-100', 'visible');
                    loadNotifications();
                } else {
                    dropdown.classList.remove('opacity-100', 'visible');
                    dropdown.classList.add('opacity-0', 'invisible');
                }
            }
        } catch (error) {
            console.error('Error in toggleNotifications:', error);
        }
    }

    function loadNotifications() {
        // This can be enhanced to load notifications via AJAX
        // For now, they're loaded server-side
    }

    // Make functions globally available
    window.toggleUserMenu = toggleUserMenu;
    window.toggleNotifications = toggleNotifications;
    window.toggleMobileMenu = toggleMobileMenu;
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const userDropdown = document.getElementById('userDropdown');
        const notificationsDropdown = document.getElementById('notificationsDropdown');
        const userButton = event.target.closest('button[onclick="toggleUserMenu()"]');
        const notifButton = event.target.closest('button[onclick="toggleNotifications()"]');
        
        // Close user dropdown
        if (!userButton && userDropdown && !userDropdown.contains(event.target)) {
            userDropdown.classList.remove('opacity-100', 'visible');
            userDropdown.classList.add('opacity-0', 'invisible');
        }
        
        // Close notifications dropdown
        if (!notifButton && notificationsDropdown && !notificationsDropdown.contains(event.target)) {
            notificationsDropdown.classList.remove('opacity-100', 'visible');
            notificationsDropdown.classList.add('opacity-0', 'invisible');
        }
    });
    </script>
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
                    <a href="{{ url('login') }}" class="text-white hover:text-blue-200 transition font-medium">{{ __('Login') }}</a>
                    <a href="{{ url('register') }}" class="bg-white text-blue-600 px-4 py-2 rounded-lg font-semibold hover:bg-blue-50 transition">{{ __('Register') }}</a>
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
                    
                    <!-- Notifications Bell -->
                    <div class="relative">
                        <button onclick="toggleNotifications()" class="text-white hover:text-blue-200 transition relative focus:outline-none">
                            <i class="fas fa-bell text-xl"></i>
                            @auth
                                <span id="notification-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center {{ Auth::user()->unreadNotifications()->count() > 0 ? '' : 'hidden' }}">
                                    {{ Auth::user()->unreadNotifications()->count() > 99 ? '99+' : Auth::user()->unreadNotifications()->count() }}
                                </span>
                            @endauth
                        </button>
                        
                        <!-- Notifications Dropdown -->
                        <div id="notificationsDropdown" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl opacity-0 invisible transition-all duration-200 z-50 max-h-96 overflow-y-auto">
                            <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center">
                                <h3 class="text-sm font-medium text-gray-900">{{ __('Notifications') }}</h3>
                                <a href="{{ route('user.notifications') }}" class="text-xs text-blue-600 hover:text-blue-800">{{ __('View All') }}</a>
                            </div>
                            
                            <div id="notificationsList" class="max-h-64 overflow-y-auto">
                                @auth
                                    @php
                                        $recentNotifications = Auth::user()->notifications()->latest()->limit(5)->get();
                                    @endphp
                                    
                                    @if($recentNotifications->count() > 0)
                                        @foreach($recentNotifications as $notification)
                                            <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100 cursor-pointer notification-item {{ $notification->read_at ? '' : 'bg-blue-50' }}" 
                                                 onclick="markNotificationAsRead('{{ $notification->id }}')">
                                                <div class="flex items-start">
                                                    <div class="flex-shrink-0">
                                                        @if(isset($notification->data['type']) && $notification->data['type'] === 'property_created')
                                                            <i class="fas fa-home text-green-500"></i>
                                                        @elseif($notification->type === 'property_favorited')
                                                            <i class="fas fa-heart text-red-500"></i>
                                                        @elseif($notification->type === 'wallet_deposit')
                                                            <i class="fas fa-arrow-down text-green-500"></i>
                                                        @elseif($notification->type === 'wallet_withdrawal')
                                                            <i class="fas fa-arrow-up text-red-500"></i>
                                                        @elseif($notification->type === 'property_inquiry')
                                                            <i class="fas fa-envelope text-blue-500"></i>
                                                        @else
                                                            <i class="fas fa-info-circle text-gray-500"></i>
                                                        @endif
                                                    </div>
                                                    <div class="ml-3 flex-1">
                                                        <p class="text-sm text-gray-900 {{ $notification->read_at ? 'font-normal' : 'font-semibold' }}">
                                                            {{ $notification->data['title'] ?? 'Notification' }}
                                                        </p>
                                                        <p class="text-xs text-gray-500 mt-1">
                                                            {{ $notification->created_at->diffForHumans() }}
                                                        </p>
                                                    </div>
                                                    @if(!$notification->read_at)
                                                        <div class="flex-shrink-0">
                                                            <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div id="no-notifications-msg" class="px-4 py-6 text-center text-gray-500">
                                            <i class="fas fa-bell-slash text-2xl mb-2"></i>
                                            <p class="text-sm">{{ __('No notifications yet') }}</p>
                                        </div>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>

                    @auth
                    <script type="module">
                        // Listen for notifications
                        if (typeof Echo !== 'undefined') {
                            Echo.private('App.Models.User.{{ Auth::id() }}')
                                .notification((notification) => {
                                    console.log('Notification received:', notification);
                                    
                                    // Update Badge
                                    const badge = document.getElementById('notification-badge');
                                    if (badge) {
                                        let count = parseInt(badge.innerText) || 0;
                                        if (badge.classList.contains('hidden')) {
                                            count = 0;
                                            badge.classList.remove('hidden');
                                        }
                                        count++;
                                        badge.innerText = count > 99 ? '99+' : count;
                                    }

                                    // Add to List
                                    const list = document.getElementById('notificationsList');
                                    const noMsg = document.getElementById('no-notifications-msg');
                                    
                                    if (noMsg) {
                                        noMsg.remove();
                                    }
                                    
                                    if (list) {
                                        const newItem = document.createElement('div');
                                        newItem.className = "px-4 py-3 hover:bg-gray-50 border-b border-gray-100 cursor-pointer notification-item bg-blue-50";
                                        
                                        // Determine Icon
                                        let iconHtml = '<i class="fas fa-info-circle text-gray-500"></i>';
                                        if (notification.type === 'property_created') {
                                            iconHtml = '<i class="fas fa-home text-green-500"></i>';
                                        } else if (notification.type === 'agent_created') {
                                            iconHtml = '<i class="fas fa-user-tie text-blue-500"></i>';
                                        } else if (notification.type === 'developer_created') {
                                            iconHtml = '<i class="fas fa-building text-purple-500"></i>';
                                        } else if (notification.type === 'task_created') {
                                            iconHtml = '<i class="fas fa-tasks text-yellow-500"></i>';
                                        }

                                        newItem.innerHTML = `
                                            <div class="flex items-start">
                                                <div class="flex-shrink-0">
                                                    ${iconHtml}
                                                </div>
                                                <div class="ml-3 flex-1">
                                                    <p class="text-sm text-gray-900 font-semibold">
                                                        ${notification.title || 'New Notification'}
                                                    </p>
                                                    <p class="text-xs text-gray-500 mt-1">
                                                        Just now
                                                    </p>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
                                                </div>
                                            </div>
                                        `;
                                        
                                        list.insertBefore(newItem, list.firstChild);
                                    }
                                });
                        }
                    </script>
                    @endauth
                    
                    <!-- User Menu -->
                    <div class="relative">
                        <button onclick="toggleUserMenu()" class="text-white hover:text-blue-200 transition flex items-center focus:outline-none">
                            <i class="fas fa-user-circle text-xl"></i>
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>
                        <div id="userDropdown" class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-xl opacity-0 invisible transition-all duration-200 z-50">
                            <!-- User Info -->
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                            </div>
                            
                            <!-- Menu Items -->
                            <div class="py-1">
                                <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-tachometer-alt w-4 mr-2"></i> {{ __('Dashboard') }}
                                </a>
                                <a href="{{ route('user.profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-user w-4 mr-2"></i> {{ __('Profile') }}
                                </a>
                                <a href="{{ route('wallet.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-wallet w-4 mr-2"></i> {{ __('My Wallet') }}
                                </a>
                                <a href="{{ route('properties.favorites') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-heart w-4 mr-2"></i> {{ __('Favorites') }}
                                </a>
                                <a href="{{ route('user.notifications') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-bell w-4 mr-2"></i> {{ __('Notifications') }}
                                </a>
                                
                                @if(Auth::user()->is_agent || Auth::user()->user_type === 'agent')
                                <a href="{{ route('agent.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-briefcase w-4 mr-2"></i> {{ __('Agent Panel') }}
                                </a>
                                @endif
                                
                                @if(Auth::user()->is_admin || Auth::user()->user_type === 'admin')
                                <div class="border-t border-gray-100 mt-1 pt-1">
                                    <a href="{{ route('maintenance.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                        <i class="fas fa-tools w-4 mr-2"></i> {{ __('Maintenance') }}
                                    </a>
                                    <a href="{{ route('inventory.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                        <i class="fas fa-boxes w-4 mr-2"></i> {{ __('Inventory') }}
                                    </a>
                                    <a href="{{ route('projects.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                        <i class="fas fa-project-diagram w-4 mr-2"></i> {{ __('Projects') }}
                                    </a>
                                </div>
                                @endif
                                
                                <div class="border-t border-gray-100 mt-1 pt-1">
                                    <a href="{{ route('settings.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                        <i class="fas fa-cog w-4 mr-2"></i> {{ __('Settings') }}
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <i class="fas fa-sign-out-alt w-4 mr-2"></i> {{ __('Logout') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
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
                <a href="{{ url('login') }}" class="block py-2 text-white hover:text-blue-200">{{ __('Login') }}</a>
                <a href="{{ url('register') }}" class="block py-2 text-white hover:text-blue-200">{{ __('Register') }}</a>
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
console.log('Layout script loaded');

function toggleMobileMenu() {
    const menu = document.getElementById('mobileMenu');
    menu.classList.toggle('hidden');
}

function toggleUserMenu() {
    try {
        console.log('toggleUserMenu called');
        const dropdown = document.getElementById('userDropdown');
        if (!dropdown) {
            console.error('userDropdown element not found');
            return;
        }
        
        if (dropdown.classList.contains('opacity-0')) {
            dropdown.classList.remove('opacity-0', 'invisible');
            dropdown.classList.add('opacity-100', 'visible');
            console.log('Dropdown opened');
        } else {
            dropdown.classList.remove('opacity-100', 'visible');
            dropdown.classList.add('opacity-0', 'invisible');
            console.log('Dropdown closed');
        }
    } catch (error) {
        console.error('Error in toggleUserMenu:', error);
    }
}

// Make functions globally available
window.toggleUserMenu = toggleUserMenu;
window.toggleNotifications = toggleNotifications;
window.toggleMobileMenu = toggleMobileMenu;

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('userDropdown');
    const button = event.target.closest('button[onclick="toggleUserMenu()"]');
    
    if (dropdown && !button && !dropdown.contains(event.target)) {
        dropdown.classList.remove('opacity-100', 'visible');
        dropdown.classList.add('opacity-0', 'invisible');
    }
});

// Notifications functions
function toggleNotifications() {
    const dropdown = document.getElementById('notificationsDropdown');
    if (dropdown.classList.contains('opacity-0')) {
        dropdown.classList.remove('opacity-0', 'invisible');
        dropdown.classList.add('opacity-100', 'visible');
        // Load notifications via AJAX
        loadNotifications();
    } else {
        dropdown.classList.remove('opacity-100', 'visible');
        dropdown.classList.add('opacity-0', 'invisible');
    }
}

function loadNotifications() {
    // This can be enhanced to load notifications via AJAX
    // For now, they're loaded server-side
}

function markNotificationAsRead(notificationId) {
    fetch(`/notifications/${notificationId}/read`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI
            const notificationItem = document.querySelector(`[onclick="markNotificationAsRead('${notificationId}')"]`);
            if (notificationItem) {
                notificationItem.classList.remove('bg-blue-50');
                notificationItem.classList.add('bg-white');
                const dot = notificationItem.querySelector('.bg-blue-600');
                if (dot) dot.remove();
            }
            updateNotificationCount();
        }
    })
    .catch(error => console.error('Error:', error));
}

function markAllNotificationsAsRead() {
    fetch('/notifications/read-all', {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI
            document.querySelectorAll('.notification-item').forEach(item => {
                item.classList.remove('bg-blue-50');
                item.classList.add('bg-white');
                const dot = item.querySelector('.bg-blue-600');
                if (dot) dot.remove();
            });
            updateNotificationCount();
        }
    })
    .catch(error => console.error('Error:', error));
}

function updateNotificationCount() {
    fetch('/notifications/count', {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    })
    .then(response => response.json())
    .then(data => {
        const badge = document.querySelector('.fa-bell').nextElementSibling;
        if (badge && data.count > 0) {
            badge.textContent = data.count > 99 ? '99+' : data.count;
        } else if (badge) {
            badge.remove();
        }
    })
    .catch(error => console.error('Error:', error));
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

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    {{-- <script src="{{ asset('js/agents-directory.js') }}"></script> --}}
</body>
</html>