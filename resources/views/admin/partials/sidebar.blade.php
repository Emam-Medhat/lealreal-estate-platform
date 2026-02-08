<!-- Dynamic Sidebar -->
<aside class="w-64 bg-white shadow-xl h-screen sticky top-0 border-r border-gray-200 flex flex-col ">
    <!-- Logo Section -->
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center">
            <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg p-2 mr-3">
                <i class="fas fa-crown text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">لوحة التحكم</h2>
                <p class="text-xs text-gray-500">نظام إدارة متكامل</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="p-4 flex-1 overflow-y-auto">
        <div class="space-y-2">
            <!-- Dashboard -->
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center p-3 rounded-lg hover:bg-blue-50 transition-colors group {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-700' }}">
                <i
                    class="fas fa-home w-5 ml-3 {{ request()->routeIs('admin.dashboard') ? 'text-blue-600' : 'text-gray-500' }}"></i>
                <span class="font-medium">الرئيسية</span>
            </a>

            <!-- Users Section -->
            <div class="nav-section">
                <button onclick="toggleSection('users')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-blue-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-users w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">المستخدمون</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="users-arrow"></i>
                </button>
                <div id="users-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('admin.users.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('admin.users.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-list w-4 ml-2"></i>
                        كل المستخدمين
                    </a>
                    <a href="{{ route('admin.users.create') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-plus w-4 ml-2"></i>
                        إضافة مستخدم
                    </a>
                    <a href="{{ route('admin.agents.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-user-tie w-4 ml-2"></i>
                        الوكلاء
                    </a>
                    <a href="{{ route('developer.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-code w-4 ml-2"></i>
                        المطورين
                    </a>
                    <a href="{{ route('investor.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-hand-holding-usd w-4 ml-2"></i>
                        المستثمرين
                    </a>
                    <a href="{{ route('investor.stats.public') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-chart-line w-4 ml-2"></i>
                        إحصائيات المستثمرين
                    </a>
                </div>
            </div>

            <!-- Properties Section -->
            <div class="nav-section">
                <button onclick="toggleSection('properties')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-emerald-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-home w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">العقارات</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="properties-arrow"></i>
                </button>
                <div id="properties-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('admin.properties.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('admin.properties.*') ? 'bg-emerald-50 text-emerald-600' : '' }}">
                        <i class="fas fa-list w-4 ml-2"></i>
                        كل العقارات
                    </a>
                    <a href="{{ route('admin.properties.create') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-plus w-4 ml-2"></i>
                        إضافة عقار
                    </a>
                    <a href="{{ route('admin.companies.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-building w-4 ml-2"></i>
                        الشركات
                    </a>
                    <a href="{{ route('projects.index') }}" 
                       class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('projects.*') ? 'bg-emerald-50 text-emerald-600' : '' }}">
                        <i class="fas fa-project-diagram w-4 ml-2"></i>
                        المشاريع
                    </a>
                    <a href="{{ route('properties.search.index') }}" 
                       class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('properties.search.*') ? 'bg-emerald-50 text-emerald-600' : '' }}">
                        <i class="fas fa-search-location w-4 ml-2"></i>
                        بحث متقدم
                    </a>
                </div>
            </div>

            <!-- Content Management Section -->
            <div class="nav-section">
                <button onclick="toggleSection('content')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-amber-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-pen-nib w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">إدارة المحتوى</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="content-arrow"></i>
                </button>
                <div id="content-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('admin.content.dashboard') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('admin.content.dashboard') ? 'bg-amber-50 text-amber-600' : '' }}">
                        <i class="fas fa-tachometer-alt w-4 ml-2"></i>
                        لوحة CMS
                    </a>
                    <a href="{{ route('admin.blog.posts.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-newspaper w-4 ml-2"></i>
                        المقالات
                    </a>
                    <a href="{{ route('admin.pages.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-file-code w-4 ml-2"></i>
                        الصفحات
                    </a>
                    <a href="{{ route('admin.news.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-bullhorn w-4 ml-2"></i>
                        الأخبار
                    </a>
                    <a href="{{ route('admin.guides.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-map-signs w-4 ml-2"></i>
                        الأدلة
                    </a>
                    <a href="{{ route('admin.faqs.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-question-circle w-4 ml-2"></i>
                        الأسئلة الشائعة
                    </a>
                    <a href="{{ route('admin.menus.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('admin.menus.*') ? 'bg-amber-50 text-amber-600' : '' }}">
                        <i class="fas fa-bars w-4 ml-2"></i>
                        منشئ القوائم
                    </a>
                    <a href="{{ route('admin.media.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('admin.media.*') ? 'bg-amber-50 text-amber-600' : '' }}">
                        <i class="fas fa-photo-video w-4 ml-2"></i>
                        المكتبة الوسائطية
                    </a>
                    <a href="{{ route('admin.widgets.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('admin.widgets.*') ? 'bg-amber-50 text-amber-600' : '' }}">
                        <i class="fas fa-puzzle-piece w-4 ml-2"></i>
                        الودجات (Widgets)
                    </a>
                </div>
            </div>

            <!-- Sales & Leads Section -->
            <div class="nav-section">
                <button onclick="toggleSection('sales')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-orange-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-filter w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">المبيعات والعملاء</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="sales-arrow"></i>
                </button>
                <div id="sales-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('leads.dashboard') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-funnel-dollar w-4 ml-2"></i>
                        لوحة العملاء المحتملين
                    </a>
                    <a href="{{ route('leads.pipeline') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-stream w-4 ml-2"></i>
                        خطوات المبيعات
                    </a>
                    <a href="{{ route('lead-scoring.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-sort-amount-up w-4 ml-2"></i>
                        تقييم العملاء
                    </a>
                    <a href="{{ route('lead-conversions.analytics') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-chart-pie w-4 ml-2"></i>
                        تحليلات التحويل
                    </a>
                </div>
            </div>

            <!-- Operations Section -->
            <div class="nav-section">
                <button onclick="toggleSection('operations')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-teal-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-file-contract w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">العمليات</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="operations-arrow"></i>
                </button>
                <div id="operations-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('messages.contracts.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-signature w-4 ml-2"></i>
                        العقود الرقمية
                    </a>
                    <a href="{{ route('messages.offers.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-handshake w-4 ml-2"></i>
                        إدارة العروض
                    </a>
                    <a href="{{ route('messages.negotiations.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-comments-dollar w-4 ml-2"></i>
                        المفاوضات
                    </a>
                </div>
            </div>

            <!-- Inventory Section -->
            <div class="nav-section">
                <button onclick="toggleSection('inventory')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-lime-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-boxes w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">إدارة المخزون</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="inventory-arrow"></i>
                </button>
                <div id="inventory-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('inventory.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-warehouse w-4 ml-2"></i>
                        لوحة التحكم
                    </a>
                    <a href="{{ route('inventory.items.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-box w-4 ml-2"></i>
                        العناصر
                    </a>
                    <a href="{{ route('inventory.suppliers.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-truck-loading w-4 ml-2"></i>
                        الموردون
                    </a>
                    <a href="{{ route('inventory.movements.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-history w-4 ml-2"></i>
                        سجل الحركة
                    </a>
                </div>
            </div>

            <!-- Maintenance Section -->
            <div class="nav-section">
                <button onclick="toggleSection('maintenance')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-red-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-wrench w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">الصيانة</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="maintenance-arrow"></i>
                </button>
                <div id="maintenance-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('maintenance.workorders.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-tasks w-4 ml-2"></i>
                        أوامر العمل
                    </a>
                    <a href="{{ route('maintenance.teams.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-users w-4 ml-2"></i>
                        فرق الصيانة
                    </a>
                    <a href="{{ route('maintenance.reports.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-chart-bar w-4 ml-2"></i>
                        تقارير الصيانة
                    </a>
                </div>
            </div>

            <!-- Warranties Section -->
            <div class="nav-section">
                <button onclick="toggleSection('warranties')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-indigo-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-shield-alt w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">الضمانات</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="warranties-arrow"></i>
                </button>
                <div id="warranties-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('warranties.policies.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-certificate w-4 ml-2"></i>
                        جميع الضمانات
                    </a>
                    <a href="{{ route('warranties.claims.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-exclamation-triangle w-4 ml-2"></i>
                        مطالبات الضمان
                    </a>
                    <a href="{{ route('warranties.providers.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-truck-loading w-4 ml-2"></i>
                        مقدمو الخدمة
                    </a>
                </div>
            </div>

            <!-- Subscriptions Section -->
            <div class="nav-section">
                <button onclick="toggleSection('subscriptions')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-cyan-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-redo-alt w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">الاشتراكات</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="subscriptions-arrow"></i>
                </button>
                <div id="subscriptions-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('subscriptions.plans.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-layer-group w-4 ml-2"></i>
                        الخطط والأسعار
                    </a>
                    <a href="{{ route('subscriptions.invoices.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-file-invoice w-4 ml-2"></i>
                        إدارة الفواتير
                    </a>
                    <a href="#" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-check-double w-4 ml-2"></i>
                        ميزات المنتج
                    </a>
                </div>
            </div>

            <!-- Investment Section -->
            <div class="nav-section">
                <button onclick="toggleSection('investment')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-rose-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-chart-pie w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">الاستثمار</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="investment-arrow"></i>
                </button>
                <div id="investment-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('investor.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-users w-4 ml-2"></i>
                        المستثمرين
                    </a>
                    <a href="{{ route('investor.dashboard.quick.stats') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-tachometer-alt w-4 ml-2"></i>
                        لوحة المستثمر
                    </a>
                    <a href="{{ route('investors.opportunities.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('investors.opportunities.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-lightbulb w-4 ml-2"></i>
                        الفرص
                    </a>
                    <a href="{{ route('investor.funds.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-coins w-4 ml-2"></i>
                        صناديق الاستثمار
                    </a>
                    <a href="{{ route('investor.crowdfunding.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-users-cog w-4 ml-2"></i>
                        التمويل الجماعي
                    </a>
                    <a href="{{ route('investor.portfolio.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-briefcase w-4 ml-2"></i>
                        المحفظة الاستثمارية
                    </a>
                    <a href="{{ route('investor.transactions.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-exchange-alt w-4 ml-2"></i>
                        المعاملات
                    </a>
                    <a href="{{ route('investor.roi.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-calculator w-4 ml-2"></i>
                        حساب العائد ROI
                    </a>
                    <a href="{{ route('investor.risk.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-shield-alt w-4 ml-2"></i>
                        تقييم المخاطر
                    </a>
                    <a href="{{ route('investor.defi.loans.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-hand-holding-usd w-4 ml-2"></i>
                        قروض DeFi
                    </a>
                    <a href="{{ route('investor.defi.staking.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-layer-group w-4 ml-2"></i>
                        تخزين DeFi
                    </a>
                </div>
            </div>

            <!-- Communication Section -->
            <div class="nav-section">
                <button onclick="toggleSection('communication')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-sky-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-comments w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">التواصل</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="communication-arrow"></i>
                </button>
                <div id="communication-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('messages.inbox') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-inbox w-4 ml-2"></i>
                        صندوق الوارد
                    </a>
                    <a href="{{ route('messages.chat') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-comment-dots w-4 ml-2"></i>
                        الدردشة المباشرة
                    </a>
                    <a href="{{ route('messages.appointments') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-calendar-check w-4 ml-2"></i>
                        المواعيد
                    </a>
                    <a href="{{ route('messages.notifications') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-bell w-4 ml-2"></i>
                        الإشعارات متعددة القنوات
                    </a>
                </div>
            </div>

            <!-- Auctions Section -->
            <div class="nav-section">
                <button onclick="toggleSection('auctions')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-stone-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-gavel w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">المزادات</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="auctions-arrow"></i>
                </button>
                <div id="auctions-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('messages.auctions.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-balance-scale w-4 ml-2"></i>
                        المزادات النشطة
                    </a>
                    <a href="{{ route('messages.auctions.results') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-trophy w-4 ml-2"></i>
                        نتائج المزادات
                    </a>
                </div>
            </div>

            <!-- Advanced Analytics Section -->
            <div class="nav-section">
                <button onclick="toggleSection('analytics')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-indigo-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-brain w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">التحليلات المتقدمة</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="analytics-arrow"></i>
                </button>
                <div id="analytics-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('ai.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-microchip w-4 ml-2"></i>
                        رؤى الذكاء الاصطناعي
                    </a>
                    <a href="{{ route('analytics.market.trends') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-globe-americas w-4 ml-2"></i>
                        اتجاهات السوق
                    </a>
                    <a href="{{ route('analytics.behavior.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-user-check w-4 ml-2"></i>
                        سلوك المستخدمين
                    </a>
                    <a href="{{ route('analytics.heatmap.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-fire w-4 ml-2"></i>
                        الخرائط الحرارية
                    </a>
                </div>
            </div>

            <!-- Blockchain Section -->
            <div class="nav-section">
                <button onclick="toggleSection('blockchain')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-blue-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-link w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">البلوك تشين</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="blockchain-arrow"></i>
                </button>
                <div id="blockchain-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('blockchain.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-cubes w-4 ml-2"></i>
                        دفتر الأستاذ الموزع
                    </a>
                    <a href="{{ route('blockchain.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-coins w-4 ml-2"></i>
                        بروتوكول DeFi
                    </a>
                    <a href="{{ route('blockchain.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-users-cog w-4 ml-2"></i>
                        حوكمة DAO
                    </a>
                </div>
            </div>

            <!-- Metaverse Section -->
            <div class="nav-section">
                <button onclick="toggleSection('metaverse')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-purple-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-vr-cardboard w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">الميتافيرس</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="metaverse-arrow"></i>
                </button>
                <div id="metaverse-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('blockchain.metaverse.properties') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-globe w-4 ml-2"></i>
                        العقارات الافتراضية
                    </a>
                    <a href="{{ route('blockchain.metaverse.marketplace') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-shopping-cart w-4 ml-2"></i>
                        السوق الافتراضي
                    </a>
                    <a href="{{ route('blockchain.metaverse.nft') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-certificate w-4 ml-2"></i>
                        سجل NFT
                    </a>
                </div>
            </div>

            <!-- Geospatial Section -->
            <div class="nav-section">
                <button onclick="toggleSection('geospatial')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-emerald-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-map-marked-alt w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">الذكاء المكاني</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="geospatial-arrow"></i>
                </button>
                <div id="geospatial-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('blockchain.geospatial.analysis') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-chart-area w-4 ml-2"></i>
                        التحليلات المكانية
                    </a>
                    <a href="{{ route('blockchain.geospatial.security') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-shield-alt w-4 ml-2"></i>
                        مقاييس الأمان
                    </a>
                    <a href="{{ route('blockchain.geospatial.intelligence') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-satellite w-4 ml-2"></i>
                        الاستخبارات المكانية
                    </a>
                </div>
            </div>

            <!-- Legal Section -->
            <div class="nav-section">
                <button onclick="toggleSection('legal')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-amber-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-gavel w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">القانوني</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="legal-arrow"></i>
                </button>
                <div id="legal-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('blockchain.legal.compliance') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-check-shield w-4 ml-2"></i>
                        تدقيق الامتثال
                    </a>
                    <a href="{{ route('blockchain.legal.notary') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-stamp w-4 ml-2"></i>
                        الكاتب العدل المركزي
                    </a>
                    <a href="{{ route('blockchain.legal.signatures') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-pen-nib w-4 ml-2"></i>
                        التوقيعات الإلكترونية
                    </a>
                </div>
            </div>

            <!-- Document Management Section -->
            <div class="nav-section">
                <button onclick="toggleSection('documents')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-teal-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-file-alt w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">إدارة المستندات</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="documents-arrow"></i>
                </button>
                <div id="documents-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('documents.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-folder-open w-4 ml-2"></i>
                        المستندات
                    </a>
                    <a href="{{ route('document-templates.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-file-code w-4 ml-2"></i>
                        القوالب
                    </a>
                    <a href="{{ route('document-generation.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-magic w-4 ml-2"></i>
                        توليد المستندات
                    </a>
                    <a href="{{ route('contracts.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-file-contract w-4 ml-2"></i>
                        العقود
                    </a>
                    <a href="{{ route('document-signatures.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-signature w-4 ml-2"></i>
                        التوقيعات
                    </a>
                    <a href="{{ route('notary.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-stamp w-4 ml-2"></i>
                        الكاتب العدل
                    </a>
                    <a href="{{ route('documents.compliance.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-check-double w-4 ml-2"></i>
                        الامتثال
                    </a>
                </div>
            </div>

            <!-- Human Capital Section -->
            <div class="nav-section">
                <button onclick="toggleSection('human-capital')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-indigo-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-users-cog w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">رأس المال البشري</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="human-capital-arrow"></i>
                </button>
                <div id="human-capital-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('agents.performance') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-chart-pie w-4 ml-2"></i>
                        الأداء
                    </a>
                    <a href="{{ route('agents.ranking') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-trophy w-4 ml-2"></i>
                        لوحة المتصدرين
                    </a>
                    <a href="{{ route('agents.goals') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-bullseye w-4 ml-2"></i>
                        أهداف KPI
                    </a>
                </div>
            </div>

            <!-- Tax Systems Section -->
            <div class="nav-section">
                <button onclick="toggleSection('tax')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-violet-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-file-invoice-dollar w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">الضرائب</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="tax-arrow"></i>
                </button>
                <div id="tax-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('taxes.filing') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-file-export w-4 ml-2"></i>
                        تقديم الضرائب
                    </a>
                    <a href="{{ route('taxes.vat.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-percent w-4 ml-2"></i>
                        إدارة ضريبة القيمة المضافة
                    </a>
                    <a href="{{ route('taxes.reports.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-clipboard-list w-4 ml-2"></i>
                        الامتثال
                    </a>
                </div>
            </div>

            <!-- Big Data Section -->
            <div class="nav-section">
                <button onclick="toggleSection('bigdata')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-cyan-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-database w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">البيانات الضخمة</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="bigdata-arrow"></i>
                </button>
                <div id="bigdata-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('bigdata.predictive-ai') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-microchip w-4 ml-2"></i>
                        الذكاء الاصطناعي التنبؤي
                    </a>
                    <a href="{{ route('bigdata.heatmaps') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-fire w-4 ml-2"></i>
                        الخرائط الحرارية
                    </a>
                    <a href="{{ route('bigdata.sentiment-analysis') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-comment-dots w-4 ml-2"></i>
                        تحليل المشاعر
                    </a>
                </div>
            </div>

            <!-- DeFi Section -->
            <div class="nav-section">
                <button onclick="toggleSection('defi')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-violet-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-coins w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">DeFi</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="defi-arrow"></i>
                </button>
                <div id="defi-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('defi.crowdfunding.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-users w-4 ml-2"></i>
                        التمويل الجماعي
                    </a>
                    <a href="{{ route('defi.loans.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-hand-holding-usd w-4 ml-2"></i>
                        قروض DeFi
                    </a>
                    <a href="{{ route('defi.risk-assessment.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-exclamation-circle w-4 ml-2"></i>
                        تقييم المخاطر
                    </a>
                </div>
            </div>

            <!-- Developer Suite Section -->
            <div class="nav-section">
                <button onclick="toggleSection('developer')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-orange-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-building w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">مجموعة المطورين</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="developer-arrow"></i>
                </button>
                <div id="developer-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('developer.bim.models') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('developer.bim.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-cube w-4 ml-2"></i>
                        نماذج BIM
                    </a>
                    <a href="{{ route('developer.construction') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('developer.construction') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-tools w-4 ml-2"></i>
                        البناء
                    </a>
                    <a href="{{ route('developer.permits.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('developer.permits.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-file-signature w-4 ml-2"></i>
                        التصاريح
                    </a>
                    <a href="{{ route('developer.projects.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('developer.projects.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-project-diagram w-4 ml-2"></i>
                        المشاريع
                    </a>
                    <a href="{{ route('developer.phases.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('developer.phases.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-layer-group w-4 ml-2"></i>
                        مراحل المشروع
                    </a>
                    <a href="{{ route('developer.contractors.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('developer.contractors.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-hard-hat w-4 ml-2"></i>
                        المقاولون
                    </a>
                    <a href="{{ route('developer.milestones.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('developer.milestones.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-flag-checkered w-4 ml-2"></i>
                        المراحل الرئيسية
                    </a>
                    <a href="{{ route('developer.financings.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('developer.financings.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-money-bill-wave w-4 ml-2"></i>
                        التمويل
                    </a>
                    <a href="{{ route('developer.certifications.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('developer.certifications.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-certificate w-4 ml-2"></i>
                        الشهادات
                    </a>
                    <a href="{{ route('developer.portfolios.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('developer.portfolios.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-folder-open w-4 ml-2"></i>
                        معرض الأعمال
                    </a>
                    <a href="{{ route('developer.construction-updates.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('developer.construction-updates.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-hard-hat w-4 ml-2"></i>
                        تحديثات البناء
                    </a>
                    <a href="{{ route('developer.metaverses.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('developer.metaverses.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-vr-cardboard w-4 ml-2"></i>
                        الميتافيرس
                    </a>
                </div>
            </div>

            <!-- AI God-Mode Section -->
            <div class="nav-section">
                <button onclick="toggleSection('ai')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-teal-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-robot w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">الذكاء الاصطناعي</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="ai-arrow"></i>
                </button>
                <div id="ai-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('ai.price.prediction') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('ai.price.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-crystal-ball w-4 ml-2"></i>
                        متوقع الأسعار
                    </a>
                    <a href="{{ route('ai.fraud-detection.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('ai.fraud.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-user-secret w-4 ml-2"></i>
                        كشف الاحتيال
                    </a>
                    <a href="{{ route('ai.virtual.reality') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('ai.virtual.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-vr-cardboard w-4 ml-2"></i>
                        العرض الافتراضي
                    </a>
                </div>
            </div>

            <!-- Supreme CMS Section -->
            <div class="nav-section">
                <button onclick="toggleSection('supreme-cms')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-pink-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-pen-fancy w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">CMS المتفوق</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="supreme-cms-arrow"></i>
                </button>
                <div id="supreme-cms-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('cms.blog.network') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('cms.blog.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-blog w-4 ml-2"></i>
                        شبكة المدونات
                    </a>
                    <a href="{{ route('cms.menu.builder') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('cms.menu.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-bars w-4 ml-2"></i>
                        منشئ القوائم
                    </a>
                    <a href="{{ route('cms.media.library') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('cms.media.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-photo-video w-4 ml-2"></i>
                        المكتبة الوسائطية
                    </a>
                </div>
            </div>

            <!-- Agent CRM Section -->
            <div class="nav-section">
                <button onclick="toggleSection('agent-crm')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-blue-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-user-tie w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">CRM الوكلاء</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="agent-crm-arrow"></i>
                </button>
                <div id="agent-crm-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('crm.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('crm.index') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-users w-4 ml-2"></i>
                        العملاء
                    </a>
                    <a href="{{ route('crm.dashboard') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('crm.dashboard') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-address-book w-4 ml-2"></i>
                        لوحة CRM
                    </a>
                    <a href="{{ route('crm.offers') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('crm.offers') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-handshake w-4 ml-2"></i>
                        نظام العروض
                    </a>
                    <a href="{{ route('crm.commissions') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('crm.commissions') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-percent w-4 ml-2"></i>
                        العمولات
                    </a>
                </div>
            </div>
            <div class="nav-section">
                <button onclick="toggleSection('marketing')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-pink-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-search-dollar w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">التسويق و SEO</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="marketing-arrow"></i>
                </button>
                <div id="marketing-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('admin.seo.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('admin.seo*') ? 'bg-slate-50 text-slate-600' : '' }}">
                        <i class="fas fa-search w-4 ml-2"></i>
                        تحسينات
                    </a>
                    <a href="{{ route('admin.seo.analyze') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-chart-line w-4 ml-2"></i>
                        تحليل SEO
                    </a>
                    <a href="{{ route('admin.seo.keywords') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-key w-4 ml-2"></i>
                        الكلمات المفتاحية
                    </a>
                    <a href="{{ route('admin.seo.meta.tags') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-tags w-4 ml-2"></i>
                        Meta Tags
                    </a>
                    <a href="{{ route('admin.seo.sitemap') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-sitemap w-4 ml-2"></i>
                        خريطة الموقع
                    </a>
                    <a href="{{ route('admin.seo.robots') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-robot w-4 ml-2"></i>
                        Robots.txt
                    </a>
                    <a href="{{ route('admin.settings.seo') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-cog w-4 ml-2"></i>
                        إعدادات SEO العامة
                    </a>
                    <a href="{{ route('marketing.campaigns') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('marketing.campaigns') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-ad w-4 ml-2"></i>
                        الحملات الإعلانية
                    </a>
                    <a href="{{ route('ads.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('ads.index') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-bullseye w-4 ml-2"></i>
                        إدارة الإعلانات
                    </a>
                    <a href="{{ route('ads.promoted-listings.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('ads.promoted-listings.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-star w-4 ml-2"></i>
                        القوائم المروجة
                    </a>
                    <a href="{{ route('ads.placements.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('ads.placements.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-map-marker-alt w-4 ml-2"></i>
                        أماكن الإعلانات
                    </a>
                    <a href="{{ route('ads.budgets.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('ads.budgets.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-wallet w-4 ml-2"></i>
                        الميزانيات
                    </a>
                    <a href="{{ route('ads.campaigns.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('ads.campaigns.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-layer-group w-4 ml-2"></i>
                        حملات المنصة
                    </a>
                    <a href="{{ route('admin.banner-ads.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('admin.banner-ads.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-image w-4 ml-2"></i>
                        إعلانات البانر
                    </a>
                    <a href="{{ route('ads.analytics.dashboard') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('ads.analytics.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-chart-area w-4 ml-2"></i>
                        تحليلات الإعلانات
                    </a>
                    <a href="{{ route('surveys.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('surveys.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-poll w-4 ml-2"></i>
                        الاستبيانات
                    </a>
                    <a href="{{ route('marketing.reviews') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('marketing.reviews') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-star w-4 ml-2"></i>
                        مركز التقييمات
                    </a>
                    <a href="{{ route('marketing.complaints') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('marketing.complaints') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-exclamation-triangle w-4 ml-2"></i>
                        الشكاوى
                    </a>
                </div>
            </div>

            <!-- Financial Section -->
            <div class="nav-section">
                <button onclick="toggleSection('financial')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-red-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-wallet w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">المالية</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="financial-arrow"></i>
                </button>
                <div id="financial-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('admin.financial.dashboard') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('admin.financial.dashboard') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-tachometer-alt w-4 ml-2"></i>
                        لوحة المالية
                    </a>
                    <a href="{{ route('admin.financial.transactions') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('admin.financial.transactions') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-exchange-alt w-4 ml-2"></i>
                        المعاملات
                    </a>
                    <a href="{{ route('admin.financial.invoices') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('admin.financial.invoices') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-file-invoice-dollar w-4 ml-2"></i>
                        فواتير الإدارة
                    </a>
                    <a href="{{ route('admin.financial.analytics') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('admin.financial.analytics') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-chart-pie w-4 ml-2"></i>
                        التحليلات المالية
                    </a>
                    <a href="{{ route('taxes.payments.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('taxes.payments.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-credit-card w-4 ml-2"></i>
                        المدفوعات الضريبية
                    </a>
                    <a href="{{ route('payments.invoices.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('payments.invoices.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-file-invoice-dollar w-4 ml-2"></i>
                        الفواتير
                    </a>
                    <a href="{{ route('payments.wallets.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('payments.wallets.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-wallet w-4 ml-2"></i>
                        المحافظ الرقمية
                    </a>
                    <a href="{{ route('payments.crypto.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('payments.crypto.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-bitcoin w-4 ml-2"></i>
                        العملات الرقمية
                    </a>
                    <a href="{{ route('orders.index') }}" 
                       class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->is('orders.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-shopping-bag w-4 ml-2"></i>
                        طلباتي
                    </a>
                    <a href="{{ route('payments.gateways.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('payments.gateways.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-network-wired w-4 ml-2"></i>
                        بوابات الدفع
                    </a>
                    <a href="{{ route('payments.methods.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('payments.methods.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-credit-card w-4 ml-2"></i>
                        طرق الدفع
                    </a>
                    <a href="{{ route('payments.receipts.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('payments.receipts.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-receipt w-4 ml-2"></i>
                        الإيصالات
                    </a>
                    <a href="{{ route('payments.refunds.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('payments.refunds.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-undo w-4 ml-2"></i>
                        الاسترداد
                    </a>
                    <a href="{{ route('payments.escrow.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('payments.escrow.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-hand-holding-usd w-4 ml-2"></i>
                        الضمان (Escrow)
                    </a>
                    <a href="{{ route('payments.mortgage.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('payments.mortgage.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-home w-4 ml-2"></i>
                        الرهن العقاري
                    </a>
                    <a href="{{ route('payments.calculator.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->routeIs('payments.calculator.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-calculator w-4 ml-2"></i>
                        حاسبة الرهن
                    </a>
                </div>
            </div>

            <!-- Reports Center Section -->
            <div class="nav-section">
                <button onclick="toggleSection('reports')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-indigo-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-chart-bar w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">مركز التقارير</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="reports-arrow"></i>
                </button>
                <div id="reports-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('reports.dashboard') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-tachometer-alt w-4 ml-2"></i>
                        لوحة التقارير
                    </a>
                    <a href="{{ route('reports.sales.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-chart-line w-4 ml-2"></i>
                        تقارير المبيعات
                    </a>
                    <a href="{{ route('reports.financial.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-file-invoice-dollar w-4 ml-2"></i>
                        التقارير المالية
                    </a>
                    <a href="{{ route('reports.property.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-building w-4 ml-2"></i>
                        تقارير العقارات
                    </a>
                    <a href="{{ route('reports.performance.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-tasks w-4 ml-2"></i>
                        تقارير الأداء
                    </a>
                    <a href="{{ route('reports.custom.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-edit w-4 ml-2"></i>
                        تقارير مخصصة
                    </a>
                    <a href="{{ route('reports.schedules.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-clock w-4 ml-2"></i>
                        التقارير المجدولة
                    </a>
                </div>
            </div>

            <!-- Global Services Module Integration -->
            <div class="nav-section">
                <button onclick="toggleSection('global-services')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-100 transition-all text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-globe w-5 ml-3"></i>
                        <span class="font-medium">الخدمات العالمية</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="global-services-arrow"></i>
                </button>
                <div id="global-services-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('currency.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->is('currency*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-money-bill-wave w-4 ml-2"></i>
                        العملات
                    </a>
                    <a href="{{ route('language.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->is('language*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-language w-4 ml-2"></i>
                        متعدد اللغات
                    </a>
                    <a href="{{ route('gamification.dashboard') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->is('gamification*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-gamepad w-4 ml-2"></i>
                        التحفيز والنقاط
                    </a>
                    <a href="{{ route('gamification.achievements') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-trophy w-4 ml-2"></i>
                        الإنجازات
                    </a>
                    <a href="{{ route('gamification.leaderboard') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-list-ol w-4 ml-2"></i>
                        لوحة المتصدرين
                    </a>
                    <a href="{{ route('blockchain.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->is('blockchain*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-link w-4 ml-2"></i>
                        البلوك تشين
                    </a>
                    <a href="{{ route('blockchain.smartcontracts.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-file-contract w-4 ml-2"></i>
                        العقود الذكية
                    </a>
                    <a href="{{ route('blockchain.nfts.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-images w-4 ml-2"></i>
                        NFTs
                    </a>
                    <a href="{{ route('ai.descriptions') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->is('ai*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-robot w-4 ml-2"></i>
                        خدمات الذكاء الاصطناعي
                    </a>
                    <a href="{{ route('ai.index') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-brain w-4 ml-2"></i>
                        لوحة الذكاء الاصطناعي
                    </a>
                    <a href="{{ route('ai.chat') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-comments w-4 ml-2"></i>
                        المحادثة الذكية
                    </a>
                    <a href="{{ route('enterprise.dashboard') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600 {{ request()->is('enterprise*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-building w-4 ml-2"></i>
                        حلول المؤسسات
                    </a>
                    <a href="{{ route('enterprise.accounts') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-building w-4 ml-2"></i>
                        حسابات المؤسسات
                    </a>
                    <a href="{{ route('enterprise.dashboard') }}" class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-users w-4 ml-2"></i>
                        المستأجرين
                    </a>
                </div>
            </div>

            <!-- Reports Center Section -->
            <div class="nav-section">
                <button onclick="toggleSection('reports')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-indigo-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-chart-bar w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">مركز التقارير</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="reports-arrow"></i>
                </button>
                <div id="reports-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('reports.dashboard') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-tachometer-alt w-4 ml-2"></i>
                        لوحة التقارير
                    </a>
                    <a href="{{ route('reports.sales.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-chart-line w-4 ml-2"></i>
                        تقارير المبيعات
                    </a>
                    <a href="{{ route('reports.financial.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-file-invoice-dollar w-4 ml-2"></i>
                        التقارير المالية
                    </a>
                    <a href="{{ route('reports.market.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-globe w-4 ml-2"></i>
                        تقارير السوق
                    </a>
                    <a href="{{ route('reports.property.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-building w-4 ml-2"></i>
                        تقارير العقارات
                    </a>
                    <a href="{{ route('reports.performance.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-tasks w-4 ml-2"></i>
                        تقارير الأداء
                    </a>
                    <a href="{{ route('reports.commission.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-percentage w-4 ml-2"></i>
                        تقارير العمولات
                    </a>
                    <a href="{{ route('reports.custom.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-edit w-4 ml-2"></i>
                        تقارير مخصصة
                    </a>
                    <a href="{{ route('reports.schedules.index') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-clock w-4 ml-2"></i>
                        التقارير المجدولة
                    </a>
                </div>
            </div>

            <!-- Modules Section -->
            <div class="nav-section">
                <button onclick="toggleSection('modules')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-purple-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-cubes w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">الموديولات</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="modules-arrow"></i>
                </button>
                <div id="modules-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('modules.dashboard') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-tachometer-alt w-4 ml-2"></i>
                        لوحة الموديولات
                    </a>
                </div>
            </div>

            <!-- Performance Section -->
            <div class="nav-section">
                <button onclick="toggleSection('performance')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-red-50 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-tachometer-fast w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">الأداء</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="performance-arrow"></i>
                </button>
                <div id="performance-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('admin.performance.dashboard') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-chart-line w-4 ml-2"></i>
                        لوحة الأداء
                    </a>
                    <a href="{{ route('admin.performance.database') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-database w-4 ml-2"></i>
                        أداء قاعدة البيانات
                    </a>
                    <a href="{{ route('admin.performance.cache') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-bolt w-4 ml-2"></i>
                        أداء الكاش
                    </a>
                    <a href="{{ route('admin.performance.queries') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-search w-4 ml-2"></i>
                        تحليل الاستعلامات
                    </a>
                    <a href="{{ route('admin.performance.system') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-server w-4 ml-2"></i>
                        موارد النظام
                    </a>
                    <a href="{{ route('admin.performance.realtime') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-stopwatch w-4 ml-2"></i>
                        مراقبة لحظية
                    </a>
                    <a href="{{ route('admin.performance.recommendations') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-lightbulb w-4 ml-2"></i>
                        توصيات التحسين
                    </a>
                </div>
            </div>

            <!-- System Management Section -->
            <div class="nav-section">
                <button onclick="toggleSection('system')"
                    class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-gray-200 transition-colors text-gray-700 group">
                    <div class="flex items-center">
                        <i class="fas fa-cogs w-5 ml-3 text-gray-500"></i>
                        <span class="font-medium">إدارة النظام</span>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 transition-transform" id="system-arrow"></i>
                </button>
                <div id="system-submenu" class="hidden mt-2 space-y-1 mr-8">
                    <a href="{{ route('admin.system.dashboard') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-server w-4 ml-2"></i>
                        حالة النظام
                    </a>
                    <a href="{{ route('admin.system.logs') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-clipboard-list w-4 ml-2"></i>
                        سجلات النظام
                    </a>
                    <a href="{{ route('admin.system.security') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-shield-alt w-4 ml-2"></i>
                        الأمان والصلاحيات
                    </a>
                    <a href="{{ route('admin.system.backup') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-database w-4 ml-2"></i>
                        النسخ الاحتياطي
                    </a>
                    <a href="{{ route('admin.system.cache') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-memory w-4 ml-2"></i>
                        إدارة الكاش
                    </a>
                    <a href="{{ route('admin.system.queue') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-tasks w-4 ml-2"></i>
                        طوابير الانتظار
                    </a>
                    <a href="{{ route('admin.system.storage') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-hdd w-4 ml-2"></i>
                        التخزين
                    </a>
                    <a href="{{ route('admin.system.database') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-database w-4 ml-2"></i>
                        قاعدة البيانات
                    </a>
                    <a href="{{ route('admin.system.monitoring') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-desktop w-4 ml-2"></i>
                        المراقبة الحية
                    </a>
                    <a href="{{ route('admin.system.updates') }}"
                        class="flex items-center p-2 rounded-lg hover:bg-gray-100 text-sm text-gray-600">
                        <i class="fas fa-sync w-4 ml-2"></i>
                        التحديثات
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- User Profile Section -->

</aside>

<script>
    function toggleSection(section) {
        const submenu = document.getElementById(section + '-submenu');
        const arrow = document.getElementById(section + '-arrow');

        // Close all other sections
        document.querySelectorAll('[id$="-submenu"]').forEach(menu => {
            if (menu.id !== section + '-submenu') {
                menu.classList.add('hidden');
            }
        });

        document.querySelectorAll('[id$="-arrow"]').forEach(arr => {
            if (arr.id !== section + '-arrow') {
                arr.classList.remove('rotate-180');
            }
        });

        // Toggle current section
        submenu.classList.toggle('hidden');
        arrow.classList.toggle('rotate-180');
    }

    function toggleProfileSection() {
        const profileContent = document.getElementById('profile-content');
        const chevron = document.getElementById('profile-chevron');
        
        profileContent.classList.toggle('hidden');
        chevron.classList.toggle('rotate-180');
    }

    function toggleNotifications() {
        // Add your notifications toggle logic here
        console.log('Toggle notifications');
    }

    // Auto-open section based on current route
    document.addEventListener('DOMContentLoaded', function () {
        const currentRoute = window.location.pathname;

        if (currentRoute.includes('/users')) {
            toggleSection('users');
        } else if (currentRoute.includes('/properties')) {
            toggleSection('properties');
        } else if (currentRoute.includes('/seo')) {
            toggleSection('marketing');
        } else if (currentRoute.includes('/reports')) {
            toggleSection('reports');
        } else if (currentRoute.includes('/settings')) {
            toggleSection('system');
        } else if (currentRoute.includes('/inventory')) {
            toggleSection('inventory');
        } else if (currentRoute.includes('/maintenance')) {
            toggleSection('maintenance');
        } else if (currentRoute.includes('/warranties')) {
            toggleSection('warranties');
        } else if (currentRoute.includes('/subscriptions')) {
            toggleSection('subscriptions');
        } else if (currentRoute.includes('/messages')) {
            toggleSection('communication');
        } else if (currentRoute.includes('/auctions')) {
            toggleSection('auctions');
        } else if (currentRoute.includes('/blockchain')) {
            toggleSection('blockchain');
        } else if (currentRoute.includes('/taxes')) {
            toggleSection('tax');
        } else if (currentRoute.includes('/agents')) {
            toggleSection('human-capital');
        } else if (currentRoute.includes('/currency')) {
            toggleSection('global-services');
        } else if (currentRoute.includes('/language')) {
            toggleSection('global-services');
        } else if (currentRoute.includes('/gamification')) {
            toggleSection('global-services');
        } else if (currentRoute.includes('/ai')) {
            toggleSection('global-services');
        } else if (currentRoute.includes('/enterprise')) {
            toggleSection('global-services');
        } else if (currentRoute.includes('/modules')) {
            toggleSection('global-services');
        }
    });
</script>