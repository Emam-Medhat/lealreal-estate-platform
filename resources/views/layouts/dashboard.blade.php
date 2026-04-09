<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard')</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Fonts - Cairo -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        * {
            font-family: 'Cairo', 'Segoe UI', sans-serif;
        }

        body {
            background: #f5f7fa;
        }

        .sidebar {
            background: #ffffff;
            box-shadow: 2px 0 8px rgba(0, 0, 0, 0.08);
        }

        .nav-item {
            transition: all 0.3s ease;
            color: #4a5568;
            font-weight: 500;
        }

        .nav-item:hover {
            background: #f0f4f8;
            color: #2563eb;
            padding-right: 20px;
        }

        .nav-item.active {
            background: #e3f2fd;
            color: #2563eb;
            border-inline-start: 4px solid #2563eb;
            padding-inline-start: calc(1rem - 4px);
        }

        .card-stat {
            background: #ffffff;
            border-radius: 12px;
            padding: 24px;
            border-left: 4px solid;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .card-stat:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
        }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #1a202c;
            margin: 12px 0;
        }

        .stat-label {
            color: #718096;
            font-size: 14px;
            font-weight: 500;
        }

        .stat-change {
            color: #48bb78;
            font-size: 12px;
            font-weight: 600;
            margin-top: 8px;
        }

        .stat-change.down {
            color: #f56565;
        }

        .notification-item {
            padding: 16px;
            border-right: 4px solid;
            background: #f9fafb;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .notification-item:hover {
            background: #ffffff;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-secondary {
            background: #ffffff;
            color: #2563eb;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            border: 2px solid #2563eb;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .btn-secondary:hover {
            background: #f0f4f8;
        }

        .hero-section {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color: white;
            padding: 48px;
            border-radius: 16px;
            margin-bottom: 32px;
        }

        .hero-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .hero-subtitle {
            opacity: 0.9;
            font-size: 16px;
        }

        .dropdown-menu {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            border: 1px solid #e2e8f0;
            min-width: 320px;
        }

        .search-input {
            background: #f0f4f8;
            border: 1px solid #cbd5e0;
            padding: 10px 16px;
            border-radius: 8px;
            color: #2d3748;
            font-size: 14px;
        }

        .search-input:focus {
            outline: none;
            border-color: #2563eb;
            background: white;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .section-title {
            font-size: 14px;
            font-weight: 700;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 24px;
            margin-bottom: 12px;
            margin-inline-start: 16px;
        }

        .badge-count {
            background: #2563eb;
            color: white;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 4px;
            margin-right: 8px;
        }

        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }
    </style>
    @stack('styles')
</head>

<body dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}" class="bg-gray-100">

    <div class="flex">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200 sticky top-0 z-40">
                <div class="px-8 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">@yield('title', 'Admin Dashboard')</h1>
                            <p class="text-sm text-gray-600 mt-1">Welcome back!</p>
                        </div>

                        <div class="flex items-center space-x-6 space-x-reverse">
                            <!-- Search -->
                            <div class="hidden lg:flex items-center">
                                <div class="relative w-80">
                                    <input type="text" placeholder="Search..."
                                        class="search-input w-full pl-10">
                                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                                </div>
                            </div>

                            <!-- Notifications -->
                            <div class="relative">
                                <button class="relative p-2 text-gray-600 hover:text-gray-900 transition-colors">
                                    <i class="fas fa-bell text-xl"></i>
                                    <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                                </button>
                            </div>

                            <!-- User Menu -->
                            <div class="relative">
                                <button class="flex items-center space-x-3 space-x-reverse text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @if(auth()->user()->avatar_url)
                                        <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                                            class="w-8 h-8 rounded-full object-cover">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-user text-gray-500"></i>
                                        </div>
                                    @endif
                                    <span class="hidden md:block font-medium text-gray-700">{{ auth()->user()->name }}</span>
                                    <i class="fas fa-chevron-down text-gray-400"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>

</html>
