<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'لوحة التحكم') - نظام إدارة العقارات</title>
    <script src="https://cdn.tailwindcss.com"></script>
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
                            <div class="relative">
                                <button class="text-gray-500 hover:text-gray-700 relative">
                                    <i class="fas fa-bell text-xl"></i>
                                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">3</span>
                                </button>
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
</body>
</html>
