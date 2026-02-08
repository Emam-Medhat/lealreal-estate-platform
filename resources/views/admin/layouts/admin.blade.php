<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="user-id" content="{{ auth()->id() }}">
    @endauth
    <title>@yield('title', 'لوحة التحكم') - نظام إدارة العقارات</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap');
        body {
            font-family: 'Tajawal', sans-serif;
        }
        .rotate-180 {
            transform: rotate(180deg);
        }
        .nav-section button:hover i {
            color: currentColor !important;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex">
        <!-- Sidebar -->
        @include('admin.partials.sidebar')
        
        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
            <!-- Top Bar -->
            <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-10">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <button onclick="toggleSidebar()" class="text-gray-500 hover:text-gray-700 mr-4">
                                <i class="fas fa-bars text-xl"></i>
                            </button>
                            <h1 class="text-xl font-semibold text-gray-800">@yield('page-title', 'لوحة التحكم')</h1>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <!-- Notifications -->
                            <div class="relative" id="notification-dropdown">
                                <button class="text-gray-500 hover:text-gray-700 relative focus:outline-none" onclick="toggleNotifications()">
                                    <i class="fas fa-bell text-xl"></i>
                                    <span id="notification-count" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">0</span>
                                </button>
                                
                                <!-- Dropdown Menu -->
                                <div id="notification-menu" class="absolute left-0 mt-2 w-80 bg-white rounded-lg shadow-xl overflow-hidden z-50 hidden border border-gray-100" style="left: -10rem;">
                                    <div class="p-3 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                                        <h3 class="text-sm font-semibold text-gray-700">الإشعارات</h3>
                                        <button class="text-xs text-blue-600 hover:text-blue-800" onclick="markAllAsRead()">تحديد الكل كمقروء</button>
                                    </div>
                                    <div id="notification-list" class="max-h-96 overflow-y-auto">
                                        <!-- Notifications will be injected here -->
                                        <div class="p-4 text-center text-gray-500 text-sm" id="no-notifications">
                                            لا توجد إشعارات جديدة
                                        </div>
                                    </div>
                                    <div class="p-2 bg-gray-50 border-t border-gray-100 text-center">
                                        <a href="#" class="text-xs text-blue-600 hover:text-blue-800 font-medium">عرض كل الإشعارات</a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Search -->
                            <div class="relative">
                                <input type="text" placeholder="بحث..." class="bg-gray-100 rounded-lg px-4 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <i class="fas fa-search absolute left-3 top-2.5 text-gray-400"></i>
                            </div>
                            
                            <!-- User Menu -->
                            <div class="relative">
                                <button class="flex items-center space-x-2 text-gray-700 hover:text-gray-900">
                                    <div class="bg-gradient-to-r from-blue-500 to-purple-500 rounded-full p-2">
                                        <i class="fas fa-user text-white text-sm"></i>
                                    </div>
                                    <span class="text-sm font-medium">{{ auth()->user()->name }}</span>
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </button>
                            </div>
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

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="fixed top-4 left-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-pulse">
            <div class="flex items-center">
                <i class="fas fa-check-circle ml-2"></i>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="fixed top-4 left-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-pulse">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle ml-2"></i>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('aside');
            sidebar.classList.toggle('hidden');
        }

        // Auto-hide success/error messages after 5 seconds
        setTimeout(() => {
            document.querySelectorAll('.fixed.top-4').forEach(el => {
                el.remove();
            });
        }, 5000);
    </script>

    @stack('scripts')
    <script>
        // Ensure jQuery is loaded before running any jQuery-dependent scripts
        if (typeof $ === 'undefined') {
            console.error('jQuery is not loaded. Please check your internet connection.');
        }
    </script>
</body>
</html>
