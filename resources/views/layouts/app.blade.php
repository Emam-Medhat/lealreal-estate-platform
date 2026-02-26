<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
        <meta name="user-id" content="{{ auth()->id() }}">
    @endauth

    <title>@yield('title', config('app.name', 'Laravel Real Estate Platform'))</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Tailwind CSS & Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

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

        .footer-heading {
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.75);
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: white;
        }

        .footer-contact {
            color: rgba(255, 255, 255, 0.75);
        }

        .footer-contact i {
            color: #0d6efd;
        }

        .hover-lift {
            transition: transform 0.2s ease, color 0.2s ease;
        }

        .hover-lift:hover {
            transform: translateY(-2px);
            color: white;
        }

        .bg-gradient-dark {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, .3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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

        // Global CSRF setup for fetch requests
        const originalFetch = window.fetch;
        window.fetch = function () {
            let [resource, config] = arguments;
            if (config == null) {
                return originalFetch(resource);
            }

            if (config.method && ['POST', 'PUT', 'PATCH', 'DELETE'].includes(config.method.toUpperCase())) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (csrfToken) {
                    config.headers = {
                        ...config.headers,
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    };
                }
            }
            return originalFetch(resource, config);
        };

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
        document.addEventListener('click', function (event) {
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
    @stack('styles')
</head>

<body class="bg-gray-50 font-sans antialiased">

    @unless(isset($showNavbar) && $showNavbar === false)
        <!-- Navigation -->
        @include('partials.navbar')
    @endunless

    <!-- Toast Container -->
    <div id="toast-container" class="toast-container"></div>

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

    @if (session('success') || session('error'))
        <script>
            (function(){
                try {
                    var msg = {!! json_encode(session('success') ?? session('error')) !!};
                    var type = {!! json_encode(session()->has('success') ? 'success' : 'error') !!};
                    var container = document.getElementById('toast-container');
                    if (!container) {
                        container = document.createElement('div');
                        container.id = 'toast-container';
                        container.className = 'toast-container';
                        document.body.appendChild(container);
                    }
                    var toast = document.createElement('div');
                    toast.className = 'alert ' + (type === 'success' ? 'alert-success' : 'alert-danger') + ' custom-toast fade-in';
                    toast.style.margin = '10px';
                    toast.innerHTML = (type === 'success' ? '<i class="fas fa-check-circle ml-2"></i>' : '<i class="fas fa-exclamation-circle ml-2"></i>') + msg;
                    container.appendChild(toast);
                    setTimeout(function(){ toast.remove(); }, 5000);
                } catch (e) {}
            })();
        </script>
    @endif

    <!-- Footer -->
    @include('components.footer')

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Notification System -->
    <script>
        // Simple notification system
        window.showNotification = function(message, type = 'success', duration = 5000) {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `alert alert-${type} custom-toast fade-in`;
            toast.style.margin = '10px';

            const icon = type === 'success' ? 'fa-check-circle' :
                        type === 'error' ? 'fa-exclamation-circle' :
                        type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';

            toast.innerHTML = `<i class="fas ${icon} ml-2"></i> ${message}`;
            container.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, duration);
        };

        // Auto-show flash messages
        @if(session('success'))
            showNotification('{{ session('success') }}', 'success');
        @endif

        @if(session('error'))
            showNotification('{{ session('error') }}', 'error');
        @endif
    </script>

    <!-- AI Chatbot Widget -->
    @include('ai.widget')

    @auth
    @if(config('intelligence.realtime.enabled'))
    <script>
        // Disable Echo/Pusher for now to avoid WebSocket errors
        console.log('Real-time features temporarily disabled for debugging');
        // window.Pusher = Pusher;
        // window.Echo = new Echo({
        //     broadcaster: 'pusher',
        //     key: '{{ config('broadcasting.connections.pusher.key') }}',
        //     cluster: '{{ data_get(config('broadcasting.connections.pusher.options'), 'cluster', 'mt1') }}',
        //     forceTLS: {{ data_get(config('broadcasting.connections.pusher.options'), 'useTLS', true) ? 'true' : 'false' }},
        //     authEndpoint: '/broadcasting/auth',
        //     csrfToken: '{{ csrf_token() }}'
        // });
    </script>
    @endif
    @endauth

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('scripts')
    @stack('scripts')
</body>

</html>
