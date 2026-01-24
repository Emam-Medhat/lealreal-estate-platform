@auth
    @php
        $user = auth()->user();
        $isAgent = $user->is_agent || $user->user_type === 'agent';
        $isAdmin = $user->role === 'admin'; // Adjust based on your role system
        $isUser = $user->role === 'user'; // Adjust based on your role system
    @endphp

    <!-- Desktop Navigation -->
    <nav class="hidden lg:flex items-center justify-between bg-white border-b border-gray-200 px-4 py-3 shadow-sm">
        <!-- Left Side - Logo & Main Navigation -->
        <div class="flex items-center space-x-8">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
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
                    Search Properties
                </a>

                <!-- Agents Directory -->
                <a href="{{ route('agents.directory') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                    <i class="fas fa-users mr-2"></i>
                    Agents
                </a>

                <!-- Agent Navigation -->
                @if($isAgent)
                    <div class="relative group">
                        <button class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
                            <i class="fas fa-briefcase mr-2"></i>
                            Agent Panel
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>
                        <div class="absolute left-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <!-- Dashboard -->
                            <a href="{{ route('agent.dashboard') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 border-b border-gray-100">
                                <i class="fas fa-tachometer-alt mr-2"></i>
                                Dashboard
                            </a>

                            <!-- Properties Section -->
                            <div class="border-b border-gray-100">
                                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Properties
                                </div>
                                <a href="{{ route('agent.properties.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-home mr-2"></i>
                                    All Properties
                                </a>
                                <a href="{{ route('agent.properties.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-plus mr-2"></i>
                                    Add Property
                                </a>
                                <a href="{{ route('agent.properties.featured') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-star mr-2"></i>
                                    Featured Properties
                                </a>
                                {{-- <a href="{{ route('agent.properties.sold') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Sold Properties
                                </a> --}}
                            </div>

                            <!-- CRM Section -->
                            <div class="border-b border-gray-100">
                                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    CRM
                                </div>
                                <a href="{{ route('agent.crm.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-users mr-2"></i>
                                    All Clients
                                </a>
                                <a href="{{ route('agent.crm.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-user-plus mr-2"></i>
                                    Add Client
                                </a>
                                <a href="{{ route('agent.crm.leads') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-lightbulb mr-2"></i>
                                    Leads
                                </a>
                                {{-- <a href="{{ route('agent.crm.followups') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-redo mr-2"></i>
                                    Follow-ups
                                </a> --}}
                            </div>

                            <!-- Reports Section -->
                            <div class="border-b border-gray-100">
                                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Reports
                                </div>
                                <a href="{{ route('reports.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-chart-line mr-2"></i>
                                    Reports Dashboard
                                </a>
                                <a href="{{ route('reports.sales.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-shopping-cart mr-2"></i>
                                    Sales Reports
                                </a>
                                <a href="{{ route('reports.performance.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-tachometer-alt mr-2"></i>
                                    Performance Reports
                                </a>
                            </div>

                            <!-- Appointments Section -->
                            <div class="border-b border-gray-100">
                                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Appointments
                                </div>
                                <a href="{{ route('agent.appointments.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-calendar mr-2"></i>
                                    All Appointments
                                </a>
                                <a href="{{ route('agent.appointments.calendar') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-calendar-alt mr-2"></i>
                                    Calendar View
                                </a>
                                <a href="{{ route('agent.appointments.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-plus-circle mr-2"></i>
                                    Schedule Appointment
                                </a>
                            </div>

                            <!-- Offers Section -->
                            <div>
                                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Offers
                                </div>
                                <a href="{{ route('agent.offers.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-handshake mr-2"></i>
                                    All Offers
                                </a>
                                <a href="{{ route('agent.offers.received') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-inbox mr-2"></i>
                                    Received Offers
                                </a>
                                <a href="{{ route('agent.offers.sent') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Sent Offers
                                </a>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Admin Navigation -->
                @if($isAdmin)
                    <div class="relative group">
                        <button class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
                            <i class="fas fa-cog mr-2"></i>
                            Admin
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>
                        <div class="absolute left-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                            <!-- Dashboard -->
                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 border-b border-gray-100">
                                <i class="fas fa-tachometer-alt mr-2"></i>
                                Admin Dashboard
                            </a>

                            <!-- System Section -->
                            <div class="border-b border-gray-100">
                                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    System
                                </div>
                                {{-- Route Management - Not yet implemented
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-route mr-2"></i>
                                    Route Map
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-download mr-2"></i>
                                    Export Routes
                                </a> --}}
                            </div>

                            <!-- Users Section -->
                            <div class="border-b border-gray-100">
                                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Users Management
                                </div>
                                <a href="{{ route('admin.users') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-users mr-2"></i>
                                    All Users
                                </a>
                                {{-- <a href="{{ route('admin.agents.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-briefcase mr-2"></i>
                                    Agents
                                </a> --}}
                                <a href="{{ route('admin.users.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-user-plus mr-2"></i>
                                    Add User
                                </a>
                                {{-- <a href="{{ route('admin.users.roles') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-user-tag mr-2"></i>
                                    Roles & Permissions
                                </a> --}}
                            </div>

                            <!-- Properties Section -->
                            <div class="border-b border-gray-100">
                                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Properties
                                </div>
                                <a href="{{ route('admin.properties') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-home mr-2"></i>
                                    All Properties
                                </a>
                                {{-- <a href="{{ route('admin.properties.pending') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-clock mr-2"></i>
                                    Pending Approval
                                </a>
                                <a href="{{ route('admin.properties.featured') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-star mr-2"></i>
                                    Featured Properties
                                </a>
                                <a href="{{ route('admin.properties.categories') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-tags mr-2"></i>
                                    Categories
                                </a> --}}
                            </div>

                            <!-- Reports Section -->
                            <div class="border-b border-gray-100">
                                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Reports
                                </div>
                                <a href="{{ route('reports.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-chart-line mr-2"></i>
                                    Reports Dashboard
                                </a>
                                <a href="{{ route('reports.sales.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-shopping-cart mr-2"></i>
                                    Sales Reports
                                </a>
                                <a href="{{ route('reports.performance.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-tachometer-alt mr-2"></i>
                                    Performance Reports
                                </a>
                                <a href="{{ route('reports.market.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-chart-bar mr-2"></i>
                                    Market Reports
                                </a>
                                <a href="{{ route('reports.financial.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-dollar-sign mr-2"></i>
                                    Financial Reports
                                </a>
                                {{-- <a href="{{ route('reports.custom.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-cog mr-2"></i>
                                    Custom Reports
                                </a> --}}
                            </div>

                            <!-- Content Management Section -->
                            <div class="border-b border-gray-100">
                                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Content Management
                                </div>
                                <a href="{{ route('admin.blog.posts.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-blog mr-2"></i>
                                    Blog Posts
                                </a>
                                <a href="{{ route('admin.blog.categories.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-folder mr-2"></i>
                                    Blog Categories
                                </a>
                                <a href="{{ route('admin.pages.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-file-alt mr-2"></i>
                                    Pages
                                </a>
                                <a href="{{ route('admin.news.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-newspaper mr-2"></i>
                                    News
                                </a>
                                {{-- <a href="{{ route('admin.guides.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-book mr-2"></i>
                                    Guides
                                </a>
                                <a href="{{ route('admin.faqs.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-question-circle mr-2"></i>
                                    FAQs
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-images mr-2"></i>
                                    Media Library
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-sitemap mr-2"></i>
                                    Menus
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-cube mr-2"></i>
                                    Widgets
                                </a> --}}
                            </div>

                            {{-- Ads Section - Not yet implemented
                            <div class="border-b border-gray-100">
                                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Advertising
                                </div>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-ad mr-2"></i>
                                    Ads Dashboard
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-bullhorn mr-2"></i>
                                    Campaigns
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-star mr-2"></i>
                                    Promoted Listings
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-chart-pie mr-2"></i>
                                    Ads Analytics
                                </a>
                            </div> --}}

                            {{-- Auctions Section - Not yet implemented
                            <div class="border-b border-gray-100">
                                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Auctions
                                </div>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-gavel mr-2"></i>
                                    All Auctions
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-plus mr-2"></i>
                                    Create Auction
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-play-circle mr-2"></i>
                                    Active Auctions
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Completed Auctions
                                </a>
                            </div> --}}

                            {{-- Blog Section --}}
                            <div class="border-b border-gray-100">
                                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Content
                                </div>
                                <a href="{{ route('blog.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-blog mr-2"></i>
                                    Blog Posts
                                </a>
                                <a href="{{ route('blog.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-plus-circle mr-2"></i>
                                    New Post
                                </a>
                                {{-- <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-folder mr-2"></i>
                                    Categories
                                </a> --}}
                            </div>

                            <!-- Settings Section -->
                            <div>
                                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    System
                                </div>
                                <a href="{{ route('admin.settings.general') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-cog mr-2"></i>
                                    General Settings
                                </a>
                                <a href="{{ route('admin.settings.system') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-server mr-2"></i>
                                    System Settings
                                </a>
                                <a href="{{ route('admin.settings.email') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-envelope mr-2"></i>
                                    Email Settings
                                </a>
                                {{-- <a href="{{ route('admin.settings.payments') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                    <i class="fas fa-credit-card mr-2"></i>
                                    Payment Settings
                                </a> --}}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Lead Management -->
                <div class="relative group">
                    <button class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
                        <i class="fas fa-user-tie mr-2"></i>
                        Lead Management
                        <i class="fas fa-chevron-down ml-1 text-xs"></i>
                    </button>
                    <div class="absolute left-0 mt-2 w-72 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <a href="{{ route('leads.index') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 border-b border-gray-100">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Leads Dashboard
                        </a>
                        <div class="border-b border-gray-100">
                            <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Lead Management
                            </div>
                            <a href="{{ route('leads.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-plus mr-2"></i>
                                New Lead
                            </a>
                            <a href="{{ route('leads.pipeline') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-filter mr-2"></i>
                                Pipeline
                            </a>
                            <a href="{{ route('lead-analytics.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-chart-bar mr-2"></i>
                                Analytics
                            </a>
                        </div>
                        <div class="border-b border-gray-100">
                            <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Lead Tools
                            </div>
                            <a href="{{ route('lead-scoring.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-star mr-2"></i>
                                Lead Scoring
                            </a>
                            <a href="{{ route('lead-nurturing.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-seedling mr-2"></i>
                                Nurturing
                            </a>
                            <a href="{{ route('lead-assignment.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-user-friends mr-2"></i>
                                Assignment
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Tax & Compliance -->
                <div class="relative group">
                    <button class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
                        <i class="fas fa-receipt mr-2"></i>
                        Tax & Compliance
                        <i class="fas fa-chevron-down ml-1 text-xs"></i>
                    </button>
                    <div class="absolute left-0 mt-2 w-72 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <a href="{{ route('taxes.index') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 border-b border-gray-100">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Tax Dashboard
                        </a>
                        <div class="border-b border-gray-100">
                            <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Tax Management
                            </div>
                            <a href="{{ route('taxes.calculator.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-calculator mr-2"></i>
                                Tax Calculator
                            </a>
                            <a href="{{ route('taxes.filings.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-file-invoice mr-2"></i>
                                Tax Filing
                            </a>
                            <a href="{{ route('taxes.payments.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-credit-card mr-2"></i>
                                Tax Payments
                            </a>
                        </div>
                        <div class="border-b border-gray-100">
                            <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Property Tax
                            </div>
                            <a href="{{ route('taxes.property.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-home mr-2"></i>
                                Property Tax
                            </a>
                            <a href="{{ route('taxes.capitalGains.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-chart-line mr-2"></i>
                                Capital Gains
                            </a>
                            <a href="{{ route('taxes.vat.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-percentage mr-2"></i>
                                VAT Management
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Maintenance & Operations -->
                <div class="relative group">
                    <button class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
                        <i class="fas fa-tools mr-2"></i>
                        Maintenance
                        <i class="fas fa-chevron-down ml-1 text-xs"></i>
                    </button>
                    <div class="absolute left-0 mt-2 w-72 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <a href="{{ route('maintenance.index') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 border-b border-gray-100">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Maintenance Dashboard
                        </a>
                        <div class="border-b border-gray-100">
                            <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Maintenance Management
                            </div>
                            <a href="{{ route('maintenance.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-plus mr-2"></i>
                                New Request
                            </a>
                            <a href="{{ route('maintenance.schedule.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                Schedule
                            </a>
                            <a href="{{ route('maintenance.workorders.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-clipboard-list mr-2"></i>
                                Work Orders
                            </a>
                        </div>
                        <div class="border-b border-gray-100">
                            <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Operations
                            </div>
                            <a href="{{ route('maintenance.teams.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-users mr-2"></i>
                                Maintenance Teams
                            </a>
                            <a href="{{ route('inventory.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-boxes mr-2"></i>
                                Inventory
                            </a>
                            <a href="{{ route('warranties.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-shield-alt mr-2"></i>
                                Warranties
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Projects & Development -->
                <div class="relative group">
                    <button class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
                        <i class="fas fa-project-diagram mr-2"></i>
                        Projects
                        <i class="fas fa-chevron-down ml-1 text-xs"></i>
                    </button>
                    <div class="absolute left-0 mt-2 w-72 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <a href="{{ route('projects.index') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 border-b border-gray-100">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Projects Dashboard
                        </a>
                        <div class="border-b border-gray-100">
                            <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Project Management
                            </div>
                            <a href="{{ route('projects.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-plus mr-2"></i>
                                New Project
                            </a>
                            <a href="{{ route('projects.gantt.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-chart-gantt mr-2"></i>
                                Gantt Chart
                            </a>
                            {{-- <a href="{{ route('projects.kanban') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-columns mr-2"></i>
                                Kanban Board
                            </a> --}}
                        </div>
                        <div class="border-b border-gray-100">
                            <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Project Components
                            </div>
                            <a href="{{ route('projects.phases.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-tasks mr-2"></i>
                                Phases
                            </a>
                            {{-- <a href="{{ route('projects.tasks.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-clipboard-check mr-2"></i>
                                Tasks
                            </a> --}}
                            <a href="{{ route('projects.milestones.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-flag mr-2"></i>
                                Milestones
                            </a>
                            {{-- <a href="{{ route('projects.budget.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-dollar-sign mr-2"></i>
                                Budget
                            </a> --}}
                        </div>
                    </div>
                </div>

                <!-- Documents & Legal -->
                <div class="relative group">
                    <button class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
                        <i class="fas fa-file-contract mr-2"></i>
                        Documents
                        <i class="fas fa-chevron-down ml-1 text-xs"></i>
                    </button>
                    <div class="absolute left-0 mt-2 w-72 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <a href="{{ route('documents.index') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 border-b border-gray-100">
                            <i class="fas fa-folder-open mr-2"></i>
                            Documents Hub
                        </a>
                        <div class="border-b border-gray-100">
                            <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Document Management
                            </div>
                            <a href="{{ route('documents.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-plus mr-2"></i>
                                New Document
                            </a>
                            <a href="{{ route('documents.templates.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-file-alt mr-2"></i>
                                Templates
                            </a>
                            <a href="{{ route('documents.versions.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-history mr-2"></i>
                                Version Control
                            </a>
                        </div>
                        <div class="border-b border-gray-100">
                            <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Contracts & Legal
                            </div>
                            {{-- Contracts routes not yet implemented
                            <a href="{{ route('contracts.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-handshake mr-2"></i>
                                Contracts
                            </a>
                            <a href="{{ route('contracts.create') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-file-contract mr-2"></i>
                                New Contract
                            </a>
                            <a href="{{ route('contracts.sign') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-signature mr-2"></i>
                                E-Signature
                            </a> --}}
                        </div>
                    </div>
                </div>

                <!-- Financial Analysis -->
                <div class="relative group">
                    <button class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
                        <i class="fas fa-chart-line mr-2"></i>
                        Financial Analysis
                        <i class="fas fa-chevron-down ml-1 text-xs"></i>
                    </button>
                    <div class="absolute left-0 mt-2 w-72 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <a href="{{ route('financial.index') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 border-b border-gray-100">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Financial Dashboard
                        </a>
                        <div class="border-b border-gray-100">
                            <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Calculators
                            </div>
                            <a href="{{ route('financial.roi.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-percentage mr-2"></i>
                                ROI Calculator
                            </a>
                            <a href="{{ route('financial.cashflow.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-money-bill-wave mr-2"></i>
                                Cash Flow Analysis
                            </a>
                            <a href="{{ route('financial.caprate.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-calculator mr-2"></i>
                                Cap Rate Calculator
                            </a>
                            <a href="{{ route('financial.appreciation.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-chart-area mr-2"></i>
                                Appreciation Calculator
                            </a>
                        </div>
                        <div class="border-b border-gray-100">
                            <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Analysis Tools
                            </div>
                            <a href="{{ route('financial.valuation.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-home mr-2"></i>
                                Property Valuation
                            </a>
                            <a href="{{ route('financial.scenario.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-project-diagram mr-2"></i>
                                Investment Scenarios
                            </a>
                            <a href="{{ route('financial.portfolio.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-briefcase mr-2"></i>
                                Portfolio Analysis
                            </a>
                            <a href="{{ route('financial.tax.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-receipt mr-2"></i>
                                Tax Benefits
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Advanced Features -->
                <div class="relative group">
                    <button class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
                        <i class="fas fa-rocket mr-2"></i>
                        Advanced Features
                        <i class="fas fa-chevron-down ml-1 text-xs"></i>
                    </button>
                    <div class="absolute left-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <div class="border-b border-gray-100">
                            <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                AI & Analytics
                            </div>
                            {{-- AI & Analytics - Routes partially implemented
                            <a href="{{ route('ai.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-brain mr-2"></i>
                                AI Dashboard
                            </a>
                            <a href="{{ route('ai.property.recommendations') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-magic mr-2"></i>
                                Property Recommendations
                            </a>
                            <a href="{{ route('ai.market.predictions') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-chart-line mr-2"></i>
                                Market Predictions
                            </a>
                            <a href="{{ route('analytics.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-chart-bar mr-2"></i>
                                Analytics Dashboard
                            </a> --}}
                        </div>
                        <div class="border-b border-gray-100">
                            <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Emerging Tech
                            </div>
                            {{-- Emerging Tech - Routes partially implemented
                            <a href="{{ route('blockchain.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-link mr-2"></i>
                                Blockchain
                            </a>
                            <a href="{{ route('metaverse.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-vr-cardboard mr-2"></i>
                                Metaverse
                            </a>
                            <a href="{{ route('iot.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-microchip mr-2"></i>
                                IoT Features
                            </a> --}}
                        </div>
                        <div class="border-b border-gray-100">
                            <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Specialized Tools
                            </div>
                            {{-- Specialized Tools - Routes partially implemented
                            <a href="{{ route('gamification.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-gamepad mr-2"></i>
                                Gamification
                            </a>
                            <a href="{{ route('defi.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-coins mr-2"></i>
                                DeFi Integration
                            </a>
                            <a href="{{ route('geospatial.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-map-marked-alt mr-2"></i>
                                Geospatial Analysis
                            </a> --}}
                        </div>
                    </div>
                </div>

                <!-- Resources -->
                <div class="relative group">
                    <button class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors flex items-center">
                        <i class="fas fa-book-open mr-2"></i>
                        Resources
                        <i class="fas fa-chevron-down ml-1 text-xs"></i>
                    </button>
                    <div class="absolute left-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <a href="{{ route('blog.index') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 border-b border-gray-100">
                            <i class="fas fa-blog mr-2"></i>
                            Blog
                        </a>
                        <a href="{{ route('guides.index') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 border-b border-gray-100">
                            <i class="fas fa-book mr-2"></i>
                            Guides
                        </a>
                        <a href="{{ route('news.index') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 border-b border-gray-100">
                            <i class="fas fa-newspaper mr-2"></i>
                            News
                        </a>
                        <a href="{{ route('faq.index') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                            <i class="fas fa-question-circle mr-2"></i>
                            FAQs
                        </a>
                    </div>
                </div>

                <!-- About & Contact -->
                <a href="{{ route('about') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                    <i class="fas fa-info-circle mr-2"></i>
                    About
                </a>
                <a href="{{ route('contact') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                    <i class="fas fa-phone mr-2"></i>
                    Contact
                </a>
            </div>
        </div>

        <!-- Right Side - User Menu -->
        <div class="flex items-center space-x-4">
            <!-- Notifications -->
            <div class="relative">
                <button class="text-gray-600 hover:text-gray-900 p-2 rounded-full hover:bg-gray-100 transition-colors">
                    <i class="fas fa-bell"></i>
                    <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
            </div>

            <!-- Messages -->
            <div class="relative">
                <button class="text-gray-600 hover:text-gray-900 p-2 rounded-full hover:bg-gray-100 transition-colors">
                    <i class="fas fa-envelope"></i>
                    <span class="absolute top-0 right-0 w-2 h-2 bg-green-500 rounded-full"></span>
                </button>
            </div>

            <!-- User Dropdown -->
            <div class="relative group">
                <button class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-semibold">{{ substr($user->first_name ?? $user->name, 0, 1) }}</span>
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-medium text-gray-900">{{ $user->first_name ?? $user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $user->user_type ?? 'user' }}</p>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                </button>

                <div class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                    <!-- User Info -->
                    <div class="px-4 py-3 border-b border-gray-200">
                        <p class="text-sm font-medium text-gray-900">{{ $user->first_name ?? $user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                    </div>

                    <!-- Navigation Links -->
                    <div class="py-2">
                        <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Dashboard
                        </a>
                        <a href="{{ route('dashboard.profile') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                            <i class="fas fa-user mr-2"></i>
                            Profile
                        </a>
                        <a href="{{ route('dashboard.settings') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                            <i class="fas fa-cog mr-2"></i>
                            Settings
                        </a>

                        @if($isAgent)
                            <a href="{{ route('agent.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-briefcase mr-2"></i>
                                Agent Panel
                            </a>
                        @endif

                        @if($isAdmin)
                            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600">
                                <i class="fas fa-shield-alt mr-2"></i>
                                Admin Panel
                            </a>
                        @endif
                    </div>

                    <div class="border-t border-gray-200 py-2">
                        <form action="{{ route('logout') }}" method="POST" class="block">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                <i class="fas fa-sign-out-alt mr-2"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Navigation -->
    <nav class="lg:hidden bg-white border-b border-gray-200 shadow-sm">
        <div class="flex items-center justify-between px-4 py-3">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-white text-sm"></i>
                </div>
                <span class="text-xl font-bold text-gray-900">Real Estate Pro</span>
            </a>

            <!-- Mobile Menu Button -->
            <button id="mobileMenuButton" class="text-gray-600 hover:text-gray-900 p-2">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobileMenu" class="hidden border-t border-gray-200">
            <div class="px-4 py-2 space-y-1">
                <!-- Home -->
                <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                    <i class="fas fa-home mr-2"></i>
                    Home
                </a>

                <!-- Public Properties -->
                <a href="{{ route('properties.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                    <i class="fas fa-search mr-2"></i>
                    Search Properties
                </a>

                <!-- Agents Directory -->
                <a href="{{ route('agents.directory') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                    <i class="fas fa-users mr-2"></i>
                    Agents
                </a>

                <!-- Agent Navigation -->
                @if($isAgent)
                    <div class="pt-2 pb-1">
                        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Agent Panel</p>
                        <a href="{{ route('agent.dashboard') }}" class="mt-1 block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Dashboard
                        </a>

                        <div class="mt-2">
                            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Properties</p>
                            <a href="{{ route('agent.properties.index') }}" class="mt-1 block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-home mr-2"></i>
                                All Properties
                            </a>
                            <a href="{{ route('agent.properties.create') }}" class="block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-plus mr-2"></i>
                                Add Property
                            </a>
                            <a href="{{ route('agent.properties.featured') }}" class="block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-star mr-2"></i>
                                Featured Properties
                            </a>
                        </div>

                        <div class="mt-2">
                            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">CRM</p>
                            <a href="{{ route('agent.crm.index') }}" class="mt-1 block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-users mr-2"></i>
                                All Clients
                            </a>
                            <a href="{{ route('agent.crm.create') }}" class="block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-user-plus mr-2"></i>
                                Add Client
                            </a>
                            <a href="{{ route('agent.crm.leads') }}" class="block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-lightbulb mr-2"></i>
                                Leads
                            </a>
                        </div>

                        <div class="mt-2">
                            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Appointments</p>
                            <a href="{{ route('agent.appointments.index') }}" class="mt-1 block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-calendar mr-2"></i>
                                All Appointments
                            </a>
                            <a href="{{ route('agent.appointments.calendar') }}" class="block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                Calendar View
                            </a>
                            <a href="{{ route('agent.appointments.create') }}" class="block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-plus-circle mr-2"></i>
                                Schedule Appointment
                            </a>
                        </div>

                        <div class="mt-2">
                            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Offers</p>
                            <a href="{{ route('agent.offers.index') }}" class="mt-1 block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-handshake mr-2"></i>
                                All Offers
                            </a>
                            <a href="{{ route('agent.offers.received') }}" class="block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-inbox mr-2"></i>
                                Received Offers
                            </a>
                            <a href="{{ route('agent.offers.sent') }}" class="block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Sent Offers
                            </a>
                        </div>
                    </div>
                @endif

                <!-- Admin Navigation -->
                @if($isAdmin)
                    <div class="pt-2 pb-1">
                        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Admin</p>
                        <a href="{{ route('admin.dashboard') }}" class="mt-1 block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                            <i class="fas fa-tachometer-alt mr-2"></i>
                            Admin Dashboard
                        </a>

                        <div class="mt-2">
                            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Users Management</p>
                            <a href="{{ route('admin.users') }}" class="mt-1 block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-users mr-2"></i>
                                All Users
                            </a>
                            {{-- <a href="{{ route('admin.agents.index') }}" class="block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-briefcase mr-2"></i>
                                Agents
                            </a> --}}
                            <a href="{{ route('admin.users.create') }}" class="block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-user-plus mr-2"></i>
                                Add User
                            </a>
                        </div>

                        <div class="mt-2">
                            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Properties</p>
                            <a href="{{ route('admin.properties') }}" class="mt-1 block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-home mr-2"></i>
                                All Properties
                            </a>
                            {{-- <a href="{{ route('admin.properties.pending') }}" class="block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-clock mr-2"></i>
                                Pending Approval
                            </a>
                            <a href="{{ route('admin.properties.featured') }}" class="block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-star mr-2"></i>
                                Featured Properties
                            </a> --}}
                        </div>

                        <div class="mt-2">
                            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Reports</p>
                            <a href="{{ route('reports.dashboard') }}" class="mt-1 block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-chart-line mr-2"></i>
                                Reports Dashboard
                            </a>
                            <a href="{{ route('reports.sales.index') }}" class="block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-shopping-cart mr-2"></i>
                                Sales Reports
                            </a>
                            <a href="{{ route('reports.performance.index') }}" class="block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-tachometer-alt mr-2"></i>
                                Performance Reports
                            </a>
                            <a href="{{ route('reports.market.index') }}" class="block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-chart-bar mr-2"></i>
                                Market Reports
                            </a>
                            <a href="{{ route('reports.financial.index') }}" class="block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-dollar-sign mr-2"></i>
                                Financial Reports
                            </a>
                        </div>

                        <div class="mt-2">
                            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Advertising</p>
                            {{-- Advertising routes not yet implemented
                            <a href="#" class="mt-1 block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-ad mr-2"></i>
                                Ads Dashboard
                            </a>
                            <a href="#" class="block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-bullhorn mr-2"></i>
                                Campaigns
                            </a>
                            <a href="#" class="block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-star mr-2"></i>
                                Promoted Listings
                            </a>
                            <a href="#" class="block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-chart-pie mr-2"></i>
                                Ads Analytics
                            </a> --}}
                        </div>

                        <div class="mt-2">
                            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Auctions</p>
                            {{-- Auction routes not yet implemented
                            <a href="#" class="mt-1 block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-gavel mr-2"></i>
                                All Auctions
                            </a>
                            <a href="#" class="block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-plus mr-2"></i>
                                Create Auction
                            </a>
                            <a href="#" class="block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-play-circle mr-2"></i>
                                Active Auctions
                            </a> --}}
                        </div>

                        <div class="mt-2">
                            <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">System</p>
                            <a href="{{ route('admin.settings.general') }}" class="mt-1 block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-cog mr-2"></i>
                                General Settings
                            </a>
                            <a href="{{ route('admin.settings.system') }}" class="block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-server mr-2"></i>
                                System Settings
                            </a>
                            <a href="{{ route('admin.settings.email') }}" class="block px-6 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                                <i class="fas fa-envelope mr-2"></i>
                                Email Settings
                            </a>
                        </div>
                    </div>
                @endif

                <!-- About & Contact -->
                <a href="{{ route('about') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                    <i class="fas fa-info-circle mr-2"></i>
                    About
                </a>
                <a href="{{ route('contact') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                    <i class="fas fa-phone mr-2"></i>
                    Contact
                </a>

                <!-- User Menu -->
                <div class="pt-4 pb-2 border-t border-gray-200">
                    <div class="flex items-center space-x-3 px-3 py-2">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-semibold">{{ substr($user->first_name ?? $user->name, 0, 1) }}</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">{{ $user->first_name ?? $user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $user->user_type ?? 'user' }}</p>
                        </div>
                    </div>

                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Dashboard
                    </a>
                    <a href="{{ route('dashboard.profile') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                        <i class="fas fa-user mr-2"></i>
                        Profile
                    </a>
                    <a href="{{ route('dashboard.settings') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                        <i class="fas fa-cog mr-2"></i>
                        Settings
                    </a>

                    <form action="{{ route('logout') }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2 rounded-md text-base font-medium text-red-600 hover:bg-red-50">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Menu JavaScript -->
    <script>
        document.getElementById('mobileMenuButton')?.addEventListener('click', function() {
            const menu = document.getElementById('mobileMenu');
            menu.classList.toggle('hidden');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('mobileMenu');
            const button = document.getElementById('mobileMenuButton');

            if (!menu.contains(event.target) && !button.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>
@endauth

<!-- Guest Navigation -->
@guest
    <!-- Desktop Navigation -->
    <nav class="hidden lg:flex items-center justify-between bg-white border-b border-gray-200 px-4 py-3 shadow-sm">
        <!-- Left Side - Logo & Main Navigation -->
        <div class="flex items-center space-x-8">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-white text-sm"></i>
                </div>
                <span class="text-xl font-bold text-gray-900">Real Estate Pro</span>
            </a>

            <!-- Main Navigation Links -->
            <div class="flex items-center space-x-6">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                    <i class="fas fa-home mr-2"></i>
                    Home
                </a>
                <a href="{{ route('properties.index') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                    <i class="fas fa-search mr-2"></i>
                    Search Properties
                </a>
                {{-- <a href="{{ route('agents.directory') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                    <i class="fas fa-users mr-2"></i>
                    Agents
                </a> --}}
                <a href="{{ route('about') }}" class="text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                    <i class="fas fa-info-circle mr-2"></i>
                    About
                </a>
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

    <!-- Mobile Navigation -->
    <nav class="lg:hidden bg-white border-b border-gray-200 shadow-sm">
        <div class="flex items-center justify-between px-4 py-3">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center space-x-2">
                <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-building text-white text-sm"></i>
                </div>
                <span class="text-xl font-bold text-gray-900">Real Estate Pro</span>
            </a>

            <!-- Mobile Menu Button -->
            <button id="guestMobileMenuButton" class="text-gray-600 hover:text-gray-900 p-2">
                <i class="fas fa-bars text-xl"></i>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="guestMobileMenu" class="hidden border-t border-gray-200">
            <div class="px-4 py-2 space-y-1">
                <a href="{{ route('home') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                    <i class="fas fa-home mr-2"></i>
                    Home
                </a>
                <a href="{{ route('properties.index') }}" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">
                    <i class="fas fa-search mr-2"></i>
                    Search Properties
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
    </nav>

    <!-- Guest Mobile Menu JavaScript -->
    <script>
        document.getElementById('guestMobileMenuButton')?.addEventListener('click', function() {
            const menu = document.getElementById('guestMobileMenu');
            menu.classList.toggle('hidden');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const menu = document.getElementById('guestMobileMenu');
            const button = document.getElementById('guestMobileMenuButton');

            if (!menu.contains(event.target) && !button.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>
@endguest
