@auth
    @php
        $user = auth()->user();
        $isAgent = $user->is_agent || $user->user_type === 'agent';
        $isAdmin = $user->role === 'admin';
        $isUser = $user->role === 'user';
    @endphp

    <!-- Desktop Navigation -->
    <nav class="hidden lg:flex items-center justify-between bg-white border-b border-gray-200 px-6 py-3 shadow-md">
        <!-- Left Side - Logo & Main Navigation -->
        <div class="flex items-center space-x-8">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg flex items-center justify-center shadow">
                    <i class="fas fa-building text-white text-sm"></i>
                </div>
                <span class="text-xl font-bold text-gray-900">Real Estate Pro</span>
            </a>

            <!-- Main Navigation Links -->
            <div class="flex items-center space-x-6">
                <!-- Home -->
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                    <i class="fas fa-home mr-2"></i>
                    Home
                </a>

                <!-- Properties -->
                <a href="{{ route('properties.index') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                    <i class="fas fa-building mr-2"></i>
                    Properties
                </a>

                <!-- Agents -->
                <a href="{{ route('agents.directory') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                    <i class="fas fa-users mr-2"></i>
                    Agents
                </a>

                <!-- About -->
                <a href="{{ route('about') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                    <i class="fas fa-info-circle mr-2"></i>
                    About
                </a>

                <!-- Agent Panel -->
                @if($isAgent)
                    <a href="{{ route('agent.dashboard') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-briefcase mr-2"></i>
                        Agent Panel
                    </a>
                @endif

                <!-- Admin Panel -->
                @if($isAdmin)
                    <a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-cog mr-2"></i>
                        Admin Panel
                    </a>
                @endif

                <!-- User Panel -->
                @if($isUser)
                    <a href="{{ route('user.dashboard') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                        <i class="fas fa-user mr-2"></i>
                        Dashboard
                    </a>
                @endif
            </div>
        </div>

        <!-- Right Side - User Menu -->
        <div class="flex items-center space-x-4">
            <!-- Notifications -->
            @php
                $notifications = auth()->user()->unreadNotifications;
            @endphp
            <div class="relative">
                <button onclick="toggleNotifications()" class="relative text-gray-700 hover:text-blue-600 p-2 rounded-lg transition-colors">
                    <i class="fas fa-bell text-lg"></i>
                    @if($notifications->count() > 0)
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    @endif
                </button>

                <!-- Notifications Dropdown -->
                <div id="notificationsDropdown" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 opacity-0 invisible transition-all duration-200 z-50 overflow-hidden">
                    <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center">
                        <p class="text-sm font-medium text-gray-900">Notifications</p>
                        @if($notifications->count() > 0)
                            <span class="text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded-full">{{ $notifications->count() }} New</span>
                        @endif
                    </div>

                    <div class="max-h-96 overflow-y-auto">
                        @forelse($notifications as $notification)
                            <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100 transition-colors">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-building text-sm"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3 w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900">{{ $notification->data['title'] ?? 'Notification' }}</p>
                                        <p class="text-xs text-gray-500 mt-1">{{ $notification->data['message'] ?? '' }}</p>
                                        <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-6 text-center text-gray-500">
                                <p class="text-sm">No new notifications</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- User Menu -->
            <div class="relative">
                <button onclick="toggleUserMenu()" class="flex items-center space-x-2 text-gray-700 hover:text-blue-600 px-3 py-2 rounded-lg transition-colors">
                    <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-gray-600 text-sm"></i>
                    </div>
                    <span class="text-sm font-medium">{{ $user->name }}</span>
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                
                <!-- Dropdown Menu -->
                <div id="userDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 opacity-0 invisible transition-all duration-200 z-50">
                    <div class="px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                    </div>
                    
                    <div class="py-2">
                        <a href="{{ route('dashboard.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                            <i class="fas fa-user mr-2"></i>
                            Profile
                        </a>
                        <a href="{{ route('dashboard.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                            <i class="fas fa-cog mr-2"></i>
                            Settings
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu Button -->
    <div class="lg:hidden flex items-center justify-between px-4 py-3 bg-white border-b border-gray-200">
        <a href="{{ route('home') }}" class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg flex items-center justify-center">
                <i class="fas fa-building text-white text-sm"></i>
            </div>
            <span class="text-xl font-bold text-gray-900">Real Estate Pro</span>
        </a>
        
        <button onclick="toggleMobileMenu()" class="text-gray-700 hover:text-blue-600 p-2 rounded-lg">
            <i class="fas fa-bars text-lg"></i>
        </button>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden lg:hidden bg-white border-b border-gray-200">
        <div class="px-4 py-2 space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                <i class="fas fa-home mr-2"></i>
                Home
            </a>
            <a href="{{ route('properties.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                <i class="fas fa-search mr-2"></i>
                Properties
            </a>
            <a href="{{ route('agents.directory') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                <i class="fas fa-users mr-2"></i>
                Agents
            </a>
            <a href="{{ route('about') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                <i class="fas fa-info-circle mr-2"></i>
                About
            </a>
            
            @if($isAgent)
                <a href="{{ route('agent.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                    <i class="fas fa-briefcase mr-2"></i>
                    Agent Panel
                </a>
            @endif
            
            @if($isAdmin)
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                    <i class="fas fa-cog mr-2"></i>
                    Admin Panel
                </a>
            @endif
            
            @if($isUser)
                <a href="{{ route('user.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                    <i class="fas fa-user mr-2"></i>
                    Dashboard
                </a>
            @endif
        </div>
    </nav>

    <!-- JavaScript -->
    <script>
        function toggleUserMenu() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('opacity-100');
            dropdown.classList.toggle('visible');
            dropdown.classList.toggle('opacity-0');
            dropdown.classList.toggle('invisible');
        }

        function toggleNotifications() {
            console.log('Notifications clicked');
        }

        function toggleMobileMenu() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        }

        document.addEventListener('click', function(event) {
            const userDropdown = document.getElementById('userDropdown');
            const userButton = event.target.closest('button[onclick="toggleUserMenu()"]');
            
            if (userDropdown && !userButton && !userDropdown.contains(event.target)) {
                userDropdown.classList.remove('opacity-100', 'visible');
                userDropdown.classList.add('opacity-0', 'invisible');
            }

            const notifDropdown = document.getElementById('notificationsDropdown');
            const notifButton = event.target.closest('button[onclick="toggleNotifications()"]');
            
            if (notifDropdown && !notifButton && !notifDropdown.contains(event.target)) {
                notifDropdown.classList.remove('opacity-100', 'visible');
                notifDropdown.classList.add('opacity-0', 'invisible');
            }
        });
    </script>
@endauth

<!-- Guest Navigation -->
@guest
    <!-- Desktop Navigation -->
    <nav class="hidden lg:flex items-center justify-between bg-white border-b border-gray-200 px-6 py-3 shadow-md">
        <!-- Left Side - Logo & Main Navigation -->
        <div class="flex items-center space-x-8">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg flex items-center justify-center shadow">
                    <i class="fas fa-building text-white text-sm"></i>
                </div>
                <span class="text-xl font-bold text-gray-900">Real Estate Pro</span>
            </a>

            <!-- Main Navigation Links -->
            <div class="flex items-center space-x-6">
                <!-- Home -->
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                    <i class="fas fa-home mr-2"></i>
                    Home
                </a>

                <!-- Properties -->
                <a href="{{ route('properties.index') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                    <i class="fas fa-search mr-2"></i>
                    Properties
                </a>

                <!-- Agents -->
                <a href="{{ route('agents.directory') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                    <i class="fas fa-users mr-2"></i>
                    Agents
                </a>

                <!-- About -->
                <a href="{{ route('about') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                    <i class="fas fa-info-circle mr-2"></i>
                    About
                </a>

                <!-- Contact -->
                <a href="{{ route('contact') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                    <i class="fas fa-phone mr-2"></i>
                    Contact
                </a>
            </div>
        </div>

        <!-- Right Side - Auth Buttons -->
        <div class="flex items-center space-x-4">
            <a href="{{ route('login') }}" class="text-gray-700 hover:text-blue-600 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Login
            </a>
            <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">
                <i class="fas fa-user-plus mr-2"></i>
                Register
            </a>
        </div>
    </nav>

    <!-- Mobile Menu Button -->
    <div class="lg:hidden flex items-center justify-between px-4 py-3 bg-white border-b border-gray-200">
        <a href="{{ route('home') }}" class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg flex items-center justify-center">
                <i class="fas fa-building text-white text-sm"></i>
            </div>
            <span class="text-xl font-bold text-gray-900">Real Estate Pro</span>
        </a>
        
        <button onclick="toggleGuestMobileMenu()" class="text-gray-700 hover:text-blue-600 p-2 rounded-lg">
            <i class="fas fa-bars text-lg"></i>
        </button>
    </div>

    <!-- Mobile Menu -->
    <div id="guestMobileMenu" class="hidden lg:hidden bg-white border-b border-gray-200">
        <div class="px-4 py-2 space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                <i class="fas fa-home mr-2"></i>
                Home
            </a>
            <a href="{{ route('properties.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                <i class="fas fa-search mr-2"></i>
                Properties
            </a>
            <a href="{{ route('agents.directory') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                <i class="fas fa-users mr-2"></i>
                Agents
            </a>
            <a href="{{ route('about') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                <i class="fas fa-info-circle mr-2"></i>
                About
            </a>
            <a href="{{ route('contact') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                <i class="fas fa-phone mr-2"></i>
                Contact
            </a>

            <div class="pt-4 pb-2 border-t border-gray-200">
                <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Login
                </a>
                <a href="{{ route('register') }}" class="block px-3 py-2 rounded-md text-base font-medium text-blue-600 hover:bg-blue-50">
                    <i class="fas fa-user-plus mr-2"></i>
                    Register
                </a>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        function toggleGuestMobileMenu() {
            const menu = document.getElementById('guestMobileMenu');
            menu.classList.toggle('hidden');
        }

        document.addEventListener('click', function(event) {
            const menu = document.getElementById('guestMobileMenu');
            const button = event.target.closest('button[onclick="toggleGuestMobileMenu()"]');
            
            if (menu && !button && !menu.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>
@endguest
