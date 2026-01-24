<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Laravel Real Estate Platform'))</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50">

<div class="flex h-screen">
    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md">
        <div class="p-6 border-b">
            <h1 class="text-xl font-bold text-gray-800">Real Estate Platform</h1>
        </div>
        
        <nav class="p-4">
            <div class="space-y-2">
                <a href="{{ route('dashboard') }}" class="flex items-center px-4 py-2 text-gray-700 bg-gray-100 rounded-lg">
                    <i class="fas fa-home ml-3"></i>
                    لوحة التحكم
                </a>
                <a href="{{ route('dashboard.profile') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-user ml-3"></i>
                    الملف الشخصي
                </a>
                <a href="{{ route('dashboard.settings') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-cog ml-3"></i>
                    الإعدادات
                </a>
                
                <div class="pt-4 mt-4 border-t">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase mb-2">العقارات</h3>
                    <a href="{{ route('properties.index') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-building ml-3"></i>
                        كل العقارات
                    </a>
                    <a href="{{ route('properties.create') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-plus ml-3"></i>
                        إضافة عقار
                    </a>
                </div>
                
                <div class="pt-4 mt-4 border-t">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase mb-2">التحليلات</h3>
                    <a href="{{ route('analytics.dashboard') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-chart-line ml-3"></i>
                        التحليلات
                    </a>
                    <a href="{{ route('reports.dashboard') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-file-alt ml-3"></i>
                        التقارير
                    </a>
                </div>
                
                @if(auth()->user()->is_admin ?? false)
                <div class="pt-4 mt-4 border-t">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase mb-2">المشرف</h3>
                    <a href="{{ route('routes.index') }}" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-route ml-3"></i>
                        خريطة الروتات
                    </a>
                </div>
                @endif
            </div>
        </nav>
        
        <!-- User Section -->
        <div class="absolute bottom-0 w-full p-4 border-t bg-white">
            <div class="flex items-center">
                <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . auth()->user()->name }}" 
                     alt="{{ auth()->user()->name }}" 
                     class="w-8 h-8 rounded-full">
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-700">{{ auth()->user()->name }}</p>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-xs text-red-600 hover:text-red-800">تسجيل خروج</button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 overflow-y-auto">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-semibold text-gray-800">@yield('title', 'Dashboard')</h1>
                    
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <input type="text" placeholder="بحث..." class="pl-10 pr-4 py-2 border rounded-lg">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                        
                        <button class="relative text-gray-600 hover:text-gray-800">
                            <i class="fas fa-bell"></i>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">3</span>
                        </button>
                        
                        <button class="relative text-gray-600 hover:text-gray-800">
                            <i class="fas fa-envelope"></i>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center">5</span>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <div class="p-6">
            @yield('content')
        </div>
    </main>
</div>

@stack('scripts')
</body>
</html>
