

<?php $__env->startSection('title', 'لوحة التحكم'); ?>
<?php $__env->startSection('page-title', 'نظرة عامة'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* Custom styles for chart containers */
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    
    .chart-container canvas {
        position: absolute !important;
        top: 0;
        left: 0;
        width: 100% !important;
        height: 100% !important;
        max-width: none !important;
        max-height: none !important;
    }
    
    /* Prevent chart overflow */
    .bg-white.rounded-2xl {
        overflow: hidden;
    }
    
    /* Ensure responsive behavior */
    @media (max-width: 768px) {
        .chart-container {
            height: 250px;
        }
    }
    
    @media (max-width: 640px) {
        .chart-container {
            height: 200px;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div
                    class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">إجمالي المستخدمين</p>
                            <p class="text-3xl font-bold mt-2"><?php echo e($stats['site']['total_users']); ?></p>
                            <p class="text-blue-100 text-sm mt-2">
                                <i class="fas fa-arrow-up ml-1"></i>
                                +<?php echo e($stats['site']['new_users_today']); ?> اليوم
                            </p>
                        </div>
                        <div class="bg-white/20 rounded-full p-4">
                            <i class="fas fa-users text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-emerald-100 text-sm font-medium">العقارات</p>
                            <p class="text-3xl font-bold mt-2"><?php echo e($stats['site']['total_properties']); ?></p>
                            <p class="text-emerald-100 text-sm mt-2">
                                <i class="fas fa-arrow-up ml-1"></i>
                                +<?php echo e($stats['site']['new_properties_today']); ?> اليوم
                            </p>
                        </div>
                        <div class="bg-white/20 rounded-full p-4">
                            <i class="fas fa-home text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">المستثمرون</p>
                            <p class="text-3xl font-bold mt-2"><?php echo e($stats['site']['total_investors']); ?></p>
                            <p class="text-purple-100 text-sm mt-2">
                                <i class="fas fa-arrow-up ml-1"></i>
                                +<?php echo e($stats['site']['new_investors_today']); ?> اليوم
                            </p>
                        </div>
                        <div class="bg-white/20 rounded-full p-4">
                            <i class="fas fa-chart-line text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl p-6 text-white shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-amber-100 text-sm font-medium">الإيرادات</p>
                            <p class="text-3xl font-bold mt-2">$<?php echo e(number_format($stats['site']['total_revenue'], 0)); ?></p>
                            <p class="text-amber-100 text-sm mt-2">
                                <i class="fas fa-arrow-up ml-1"></i>
                                +$<?php echo e(number_format($stats['site']['revenue_today'], 0)); ?> اليوم
                            </p>
                        </div>
                        <div class="bg-white/20 rounded-full p-4">
                            <i class="fas fa-dollar-sign text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- User Growth Chart -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">نمو المستخدمين</h3>
                            <p class="text-sm text-gray-600">User Growth</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-2">
                            <i class="fas fa-chart-line text-blue-600"></i>
                        </div>
                    </div>
                    <div class="relative" style="height: 250px;">
                        <canvas id="userGrowthChart"></canvas>
                    </div>
                </div>

                <!-- Revenue Chart -->
                <div class="bg-white rounded-2xl shadow-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-800">اتجاه الإيرادات</h3>
                            <p class="text-sm text-gray-600">Revenue Trend</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-2">
                            <i class="fas fa-chart-bar text-green-600"></i>
                        </div>
                    </div>
                    <div class="relative" style="height: 250px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Activity & Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Recent Users -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Users</h3>
                    <div class="space-y-3">
                        <?php $__empty_1 = true; $__currentLoopData = $stats['recent_users']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="flex items-center">
                                <div class="bg-gray-100 rounded-full p-2 mr-3">
                                    <i class="fas fa-user text-gray-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800"><?php echo e($user->name); ?></p>
                                    <p class="text-xs text-gray-500"><?php echo e($user->email); ?></p>
                                </div>
                                <span class="text-xs text-gray-500"><?php echo e($user->created_at->diffForHumans()); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="text-gray-500 text-sm">No recent users</p>
                        <?php endif; ?>
                    </div>
                    <div class="mt-4">
                        <a href="<?php echo e(route('admin.users.index')); ?>"
                            class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View All Users →
                        </a>
                    </div>
                </div>

                <!-- Recent Properties -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Properties</h3>
                    <div class="space-y-3">
                        <?php $__empty_1 = true; $__currentLoopData = $stats['recent_properties']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $property): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="flex items-center">
                                <div class="bg-gray-100 rounded-full p-2 mr-3">
                                    <i class="fas fa-home text-gray-600 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800"><?php echo e($property->title); ?></p>
                                    <p class="text-xs text-gray-500">$<?php echo e(number_format($property->price, 0)); ?></p>
                                </div>
                                <span class="text-xs text-gray-500"><?php echo e($property->created_at->diffForHumans()); ?></span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <p class="text-gray-500 text-sm">No recent properties</p>
                        <?php endif; ?>
                    </div>
                    <div class="mt-4">
                        <a href="<?php echo e(route('admin.properties.index')); ?>"
                            class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            View All Properties →
                        </a>
                    </div>
                </div>

                <!-- System Status -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">System Status</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Database</span>
                            <span
                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Healthy
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Storage</span>
                            <span
                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                65% Used
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">API</span>
                            <span
                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Online
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Queue</span>
                            <span
                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                12 Jobs
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Priority Quick Actions</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <a href="<?php echo e(route('admin.users.create')); ?>"
                        class="bg-blue-50 border border-blue-100 rounded-xl p-4 hover:bg-blue-100 transition-all text-center group">
                        <i
                            class="fas fa-user-plus text-blue-600 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                        <p class="text-xs font-bold text-gray-800">New User</p>
                    </a>

                    <a href="<?php echo e(route('admin.properties.create')); ?>"
                        class="bg-emerald-50 border border-emerald-100 rounded-xl p-4 hover:bg-emerald-100 transition-all text-center group">
                        <i
                            class="fas fa-plus-circle text-emerald-600 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                        <p class="text-xs font-bold text-gray-800">New Property</p>
                    </a>

                    <a href="<?php echo e(route('admin.blog.posts.create')); ?>"
                        class="bg-amber-50 border border-amber-100 rounded-xl p-4 hover:bg-amber-100 transition-all text-center group">
                        <i class="fas fa-edit text-amber-600 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                        <p class="text-xs font-bold text-gray-800">New Post</p>
                    </a>

                    <a href="<?php echo e(route('investor.stats.public')); ?>"
                        class="bg-purple-50 border border-purple-100 rounded-xl p-4 hover:bg-purple-100 transition-all text-center group">
                        <i
                            class="fas fa-chart-line text-purple-600 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                        <p class="text-xs font-bold text-gray-800">Investor Stats</p>
                    </a>

                    <a href="<?php echo e(route('admin.settings')); ?>"
                        class="bg-slate-50 border border-slate-100 rounded-xl p-4 hover:bg-slate-100 transition-all text-center group">
                        <i class="fas fa-cog text-slate-600 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                        <p class="text-xs font-bold text-gray-800">Settings</p>
                    </a>

                    <a href="<?php echo e(route('admin.maintenance')); ?>"
                        class="bg-red-50 border border-red-100 rounded-xl p-4 hover:bg-red-100 transition-all text-center group">
                        <i class="fas fa-tools text-red-600 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                        <p class="text-xs font-bold text-gray-800">Maintenance</p>
                    </a>

                    <a href="<?php echo e(route('reports.index')); ?>"
                        class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 hover:bg-indigo-100 transition-all text-center group">
                        <i
                            class="fas fa-chart-line text-indigo-600 text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                        <p class="text-xs font-bold text-gray-800">All Reports</p>
                    </a>
                </div>
            </div>

            <!-- Management Sections -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-6 border-b pb-2">Administrative Management Hub</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

                    <!-- User & Identity -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-blue-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-user-shield text-blue-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">User & Identity</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="<?php echo e(route('admin.users.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-blue-600 transition-colors">
                                <i class="fas fa-users w-5"></i> All Users
                            </a>
                            <a href="<?php echo e(route('admin.users.create')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-blue-600 transition-colors">
                                <i class="fas fa-user-plus w-5"></i> Create User
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-blue-600 transition-colors">
                                <i class="fas fa-code w-5"></i> Developers
                            </a>
                            <a href="<?php echo e(route('investor.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-blue-600 transition-colors">
                                <i class="fas fa-hand-holding-usd w-5"></i> Investors
                            </a>
                            <a href="<?php echo e(route('admin.agents.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-blue-600 transition-colors">
                                <i class="fas fa-user-tie w-5"></i> Agents
                            </a>
                        </div>
                    </div>

                    <!-- Properties & Assets -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-emerald-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-city text-emerald-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Real Estate</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="<?php echo e(route('admin.properties.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-emerald-600 transition-colors">
                                <i class="fas fa-building w-5"></i> All Properties
                            </a>
                            <a href="<?php echo e(route('admin.properties.create')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-emerald-600 transition-colors">
                                <i class="fas fa-plus-circle w-5"></i> Add Property
                            </a>
                            <a href="<?php echo e(route('admin.projects.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-emerald-600 transition-colors">
                                <i class="fas fa-project-diagram w-5"></i> Projects
                            </a>
                            <a href="<?php echo e(route('admin.companies.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-emerald-600 transition-colors">
                                <i class="fas fa-briefcase w-5"></i> Companies
                            </a>
                            <a href="<?php echo e(route('properties.search.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-emerald-600 transition-colors">
                                <i class="fas fa-search-location w-5"></i> Advanced Search
                            </a>
                        </div>
                    </div>

                    <!-- Content Management -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-amber-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-pen-nib text-amber-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Content CMS</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="<?php echo e(route('admin.content.dashboard')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-amber-600 transition-colors">
                                <i class="fas fa-tachometer-alt w-5"></i> CMS Dashboard
                            </a>
                            <a href="<?php echo e(route('admin.blog.posts.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-amber-600 transition-colors">
                                <i class="fas fa-newspaper w-5"></i> Blog Posts
                            </a>
                            <a href="<?php echo e(route('admin.pages.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-amber-600 transition-colors">
                                <i class="fas fa-file-code w-5"></i> Static Pages
                            </a>
                            <a href="<?php echo e(route('admin.news.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-amber-600 transition-colors">
                                <i class="fas fa-bullhorn w-5"></i> News Central
                            </a>
                            <a href="<?php echo e(route('admin.guides.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-amber-600 transition-colors">
                                <i class="fas fa-map-signs w-5"></i> User Guides
                            </a>
                            <a href="<?php echo e(route('admin.faqs.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-amber-600 transition-colors">
                                <i class="fas fa-question-circle w-5"></i> FAQs
                            </a>
                        </div>
                    </div>

                    <!-- Maintenance Management -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-red-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-wrench text-red-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Maintenance Management</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="<?php echo e(route('maintenance.workorders.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-red-600 transition-colors">
                                <i class="fas fa-tasks w-5"></i> Work Orders
                            </a>
                            <a href="<?php echo e(route('maintenance.teams.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-red-600 transition-colors">
                                <i class="fas fa-users w-5"></i> Maintenance Teams
                            </a>
                            <a href="<?php echo e(route('maintenance.reports')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-red-600 transition-colors">
                                <i class="fas fa-chart-bar w-5"></i> Maintenance Reports
                            </a>
                        </div>
                    </div>

                    <!-- Warranty Management -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-indigo-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-shield-alt text-indigo-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Warranty Management</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                                <i class="fas fa-certificate w-5"></i> All Warranties
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                                <i class="fas fa-file-contract w-5"></i> Warranty Policies
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                                <i class="fas fa-exclamation-triangle w-5"></i> Warranty Claims
                            </a>
                        </div>
                    </div>

                    <!-- Warranty Management -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-indigo-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-shield-alt text-indigo-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Warranty Management</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                                <i class="fas fa-certificate w-5"></i> All Warranties
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                                <i class="fas fa-file-contract w-5"></i> Warranty Policies
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                                <i class="fas fa-exclamation-triangle w-5"></i> Warranty Claims
                            </a>
                        </div>
                    </div>

                    <!-- Inventory Management -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-green-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-boxes text-green-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Inventory Management</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-green-600 transition-colors">
                                <i class="fas fa-box-open w-5"></i> All Inventory
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-green-600 transition-colors">
                                <i class="fas fa-plus-square w-5"></i> Add Item
                            </a>
                        </div>
                    </div>

                    <!-- Digital Assets -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-red-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-wrench text-red-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Maintenance Management</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-red-600 transition-colors">
                                <i class="fas fa-tasks w-5"></i> Work Orders
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-red-600 transition-colors">
                                <i class="fas fa-users w-5"></i> Maintenance Teams
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-red-600 transition-colors">
                                <i class="fas fa-chart-bar w-5"></i> Maintenance Reports
                            </a>
                        </div>
                    </div>

                    <!-- Digital Assets -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-purple-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-photo-video text-purple-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Digital Assets</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-purple-600 transition-colors">
                                <i class="fas fa-images w-5"></i> Media Library
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-purple-600 transition-colors">
                                <i class="fas fa-stream w-5"></i> Menu Builder
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-purple-600 transition-colors">
                                <i class="fas fa-th-large w-5"></i> Theme Widgets
                            </a>
                        </div>
                    </div>

                    <!-- Financial Management -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-red-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-wallet text-red-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Financial Hub</h4>
                        </div>
                        <div class="space-y-2.5">
                            <div class="grid grid-cols-2 gap-2">
                                <a href="#" class="text-xs text-gray-600 hover:text-red-600"><i
                                        class="fas fa-credit-card mr-1"></i> Payments</a>
                                <a href="#" class="text-xs text-gray-600 hover:text-red-600"><i
                                        class="fas fa-exchange-alt mr-1"></i> Transactions</a>
                                <a href="#" class="text-xs text-gray-600 hover:text-red-600"><i
                                        class="fas fa-file-invoice-dollar mr-1"></i> Invoices</a>
                                <a href="#" class="text-xs text-gray-600 hover:text-red-600"><i
                                        class="fas fa-receipt mr-1"></i>
                                    Receipts</a>
                                <a href="#" class="text-xs text-gray-600 hover:text-red-600"><i
                                        class="fas fa-undo mr-1"></i>
                                    Refunds</a>
                                <a href="#" class="text-xs text-gray-600 hover:text-red-600"><i
                                        class="fas fa-lock mr-1"></i>
                                    Escrow</a>
                            </div>
                            <hr class="my-2 border-gray-200">
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-red-600 transition-colors">
                                <i class="fas fa-wallet w-5"></i> Digital Wallets
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-red-600 transition-colors">
                                <i class="fab fa-bitcoin w-5"></i> Crypto Payments
                            </a>
                        </div>
                    </div>

                    <!-- Mortgage & Loans -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-orange-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-landmark text-orange-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Loans & Mortgages</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-orange-600 transition-colors">
                                <i class="fas fa-home w-5"></i> Mortgage Apps
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-orange-600 transition-colors">
                                <i class="fas fa-funnel-dollar w-5"></i> Loan Management
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-orange-600 transition-colors">
                                <i class="fas fa-calculator w-5"></i> Financial Calc
                            </a>
                        </div>
                    </div>

                    <!-- Subscriptions -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-cyan-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-redo-alt text-cyan-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Subscriptions</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-cyan-600 transition-colors">
                                <i class="fas fa-list-ol w-5"></i> Subscriptions
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-cyan-600 transition-colors">
                                <i class="fas fa-layer-group w-5"></i> Plans & Pricing
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-cyan-600 transition-colors">
                                <i class="fas fa-check-double w-5"></i> Feature Flags
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-cyan-600 transition-colors">
                                <i class="fas fa-file-invoice w-5"></i> Billing Mgmt
                            </a>
                        </div>
                    </div>

                    <!-- Investment & ROI -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-rose-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-chart-pie text-rose-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Investments</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-rose-600 transition-colors">
                                <i class="fas fa-chart-line w-5"></i> Investor Stats
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-rose-600 transition-colors">
                                <i class="fas fa-lightbulb w-5"></i> Opportunities
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-rose-600 transition-colors">
                                <i class="fas fa-coins w-5"></i> Investment Funds
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-rose-600 transition-colors">
                                <i class="fas fa-users-cog w-5"></i> Crowdfunding
                            </a>
                        </div>
                    </div>

                    <!-- Communication -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-sky-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-comments text-sky-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Interaction</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="<?php echo e(route('messages.inbox')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-sky-600 transition-colors">
                                <i class="fas fa-inbox w-5"></i> Message Inbox
                            </a>
                            <a href="<?php echo e(route('messages.chat')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-sky-600 transition-colors">
                                <i class="fas fa-comment-dots w-5"></i> Live Chat
                            </a>
                            <a href="<?php echo e(route('messages.appointments')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-sky-600 transition-colors">
                                <i class="fas fa-calendar-check w-5"></i> Appointments
                            </a>
                            <a href="<?php echo e(route('messages.notifications')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-sky-600 transition-colors">
                                <i class="fas fa-bell w-5"></i> Multi-channel Notifs
                            </a>
                        </div>
                    </div>

                    <!-- Auctions & Commerce -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-stone-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-gavel text-stone-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Auctions</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="<?php echo e(route('messages.auctions.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-stone-600 transition-colors">
                                <i class="fas fa-balance-scale w-5"></i> Active Auctions
                            </a>
                            <a href="<?php echo e(route('messages.auctions.results')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-stone-600 transition-colors">
                                <i class="fas fa-trophy w-5"></i> Auction Results
                            </a>
                        </div>
                    </div>

                    <!-- Marketing & SEO -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-pink-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-search-dollar text-pink-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Marketing & SEO</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="<?php echo e(route('messages.auctions.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-pink-600 transition-colors">
                                <i class="fas fa-ad w-5"></i> Ad Campaigns
                            </a>
                            <a href="<?php echo e(route('admin.settings.seo')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-pink-600 transition-colors">
                                <i class="fas fa-search w-5"></i> Global SEO
                            </a>
                            <a href="<?php echo e(route('admin.seo.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-pink-600 transition-colors">
                                <i class="fas fa-search w-5"></i> SEO Management
                            </a>
                            <a href="<?php echo e(route('admin.seo.analyze')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-pink-600 transition-colors">
                                <i class="fas fa-chart-line w-5"></i> SEO Analysis
                            </a>
                            <a href="<?php echo e(route('reviews.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-pink-600 transition-colors">
                                <i class="fas fa-star w-5"></i> Review Center
                            </a>
                            <a href="<?php echo e(route('complaints.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-pink-600 transition-colors">
                                <i class="fas fa-exclamation-triangle w-5"></i> Complaints
                            </a>
                            <a href="<?php echo e(route('surveys.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-pink-600 transition-colors">
                                <i class="fas fa-poll-h w-5"></i> Survey System
                            </a>
                        </div>
                    </div>

                    <!-- Inventory & Supply -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-lime-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-cubes text-lime-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Inventory Management</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="<?php echo e(route('inventory.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-lime-600 transition-colors">
                                <i class="fas fa-warehouse w-5"></i> Command Dashboard
                            </a>
                            <a href="<?php echo e(route('inventory.items.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-lime-600 transition-colors">
                                <i class="fas fa-box w-5"></i> Stock Items
                            </a>
                            <a href="<?php echo e(route('inventory.suppliers.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-lime-600 transition-colors">
                                <i class="fas fa-truck-loading w-5"></i> Suppliers
                            </a>
                            <a href="<?php echo e(route('inventory.movements.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-lime-600 transition-colors">
                                <i class="fas fa-history w-5"></i> Stock History
                            </a>
                        </div>
                    </div>

                    <!-- Reports & Analytics -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-indigo-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-chart-bar text-indigo-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Analytics Center</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="<?php echo e(route('reports.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                                <i class="fas fa-copy w-5"></i> System Reports
                            </a>
                            <a href="<?php echo e(route('reports.market.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                                <i class="fas fa-globe w-5"></i> Market Reports
                            </a>
                            <a href="<?php echo e(route('reports.sales.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                                <i class="fas fa-piggy-bank w-5"></i> Sales Analytics
                            </a>
                            <a href="<?php echo e(route('reports.performance.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                                <i class="fas fa-tachometer-alt w-5"></i> Node Performance
                            </a>
                        </div>
                    </div>

                    <!-- Financial Ecosystem -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-violet-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-university text-violet-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Financial Ecosystem</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-violet-600 transition-colors">
                                <i class="fas fa-file-invoice-dollar w-5"></i> Tax Dashboard
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-violet-600 transition-colors">
                                <i class="fas fa-file-signature w-5"></i> Tax Filings
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-violet-600 transition-colors">
                                <i class="fas fa-chart-line w-5"></i> ROI & Cash Flow
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-violet-600 transition-colors">
                                <i class="fas fa-search-dollar w-5"></i> Asset Valuation
                            </a>
                        </div>
                    </div>

                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-indigo-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-brain text-indigo-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Advanced Analytics</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="<?php echo e(route('analytics-alt.dashboard')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                                <i class="fas fa-microchip w-5"></i> AI Insights
                            </a>
                            <a href="<?php echo e(route('analytics-alt.market.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                                <i class="fas fa-globe-americas w-5"></i> Market Trends
                            </a>
                            <a href="<?php echo e(route('analytics-alt.behavior.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                                <i class="fas fa-user-check w-5"></i> User Behavior
                            </a>
                            <a href="<?php echo e(route('analytics-alt.heatmap.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                                <i class="fas fa-fire w-5"></i> Visual Heatmaps
                            </a>
                        </div>
                    </div>

                    <!-- Advertising & Marketing -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-pink-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-ad text-pink-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Advertising</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="<?php echo e(route('ads.campaigns.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-pink-600 transition-colors">
                                <i class="fas fa-bullhorn w-5"></i> Ad Campaigns
                            </a>
                            <a href="<?php echo e(route('ads.placements.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-pink-600 transition-colors">
                                <i class="fas fa-layer-group w-5"></i> Placements
                            </a>
                            <a href="<?php echo e(route('ads.budgets.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-pink-600 transition-colors">
                                <i class="fas fa-money-bill-wave w-5"></i> Budgets
                            </a>
                            <a href="<?php echo e(route('ads.promoted-listings.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-pink-600 transition-colors">
                                <i class="fas fa-star w-5"></i> Featured Ads
                            </a>
                        </div>
                    </div>

                    <!-- Sales & Leads -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-orange-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-filter text-orange-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Sales Pipeline</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-orange-600 transition-colors">
                                <i class="fas fa-funnel-dollar w-5"></i> Leads Dashboard
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-orange-600 transition-colors">
                                <i class="fas fa-stream w-5"></i> Sales Pipeline
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-orange-600 transition-colors">
                                <i class="fas fa-sort-amount-up w-5"></i> Lead Scoring
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-orange-600 transition-colors">
                                <i class="fas fa-chart-pie w-5"></i> Conv. Analytics
                            </a>
                        </div>
                    </div>

                    <!-- Real Estate Ops -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-teal-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-file-contract text-teal-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Operations</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="<?php echo e(route('messages.contracts.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-teal-600 transition-colors">
                                <i class="fas fa-signature w-5"></i> Digital Contracts
                            </a>
                            <a href="<?php echo e(route('messages.offers.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-teal-600 transition-colors">
                                <i class="fas fa-handshake w-5"></i> Offer Mgmt
                            </a>
                            <a href="<?php echo e(route('messages.negotiations.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-teal-600 transition-colors">
                                <i class="fas fa-comments-dollar w-5"></i> Negotiations
                            </a>
                        </div>
                    </div>

                    <!-- Blockchain & DeFi Economy -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-blue-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-link text-blue-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Blockchain Hub</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="<?php echo e(route('blockchain.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-blue-600 transition-colors">
                                <i class="fas fa-cubes w-5"></i> Distributed Ledger
                            </a>
                            <a href="<?php echo e(route('blockchain.defi.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-blue-600 transition-colors">
                                <i class="fas fa-coins w-5"></i> DeFi Protocol
                            </a>
                            <a href="<?php echo e(route('blockchain.dao.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-blue-600 transition-colors">
                                <i class="fas fa-users-cog w-5"></i> DAO Governance
                            </a>
                        </div>
                    </div>

                    <!-- Metaverse Real Estate -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-purple-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-vr-cardboard text-purple-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Metaverse Systems</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-violet-600 transition-colors">
                                <i class="fas fa-globe w-5"></i> Virtual Property
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-violet-600 transition-colors">
                                <i class="fas fa-shopping-cart w-5"></i> V-Marketplace
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-violet-600 transition-colors">
                                <i class="fas fa-certificate w-5"></i> NFT Registry
                            </a>
                        </div>
                    </div>

                    <!-- Geospatial Intelligence -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-emerald-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-map-marked-alt text-emerald-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Geo Intel</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-emerald-600 transition-colors">
                                <i class="fas fa-chart-area w-5"></i> Spatial Analytics
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-emerald-600 transition-colors">
                                <i class="fas fa-shield-alt w-5"></i> Safety Metrics
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-emerald-600 transition-colors">
                                <i class="fas fa-satellite w-5"></i> Loc-Intelligence
                            </a>
                        </div>
                    </div>

                    <!-- Legal & Compliance Vault -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-amber-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-gavel text-amber-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Legal Vault</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-amber-600 transition-colors">
                                <i class="fas fa-check-shield w-5"></i> Compliance Audit
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-amber-600 transition-colors">
                                <i class="fas fa-stamp w-5"></i> Notary Central
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-amber-600 transition-colors">
                                <i class="fas fa-pen-nib w-5"></i> E-Signatures
                            </a>
                        </div>
                    </div>

                    <!-- Human Capital (Agents) -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-indigo-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-users-cog text-indigo-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Human Capital</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="<?php echo e(route('agents.performance', ['agent' => 1])); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                                <i class="fas fa-chart-pie w-5"></i> Performance
                            </a>
                            <a href="<?php echo e(route('agents.ranking')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                                <i class="fas fa-trophy w-5"></i> Leaderboard
                            </a>
                            <a href="<?php echo e(route('agents.goals')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                                <i class="fas fa-bullseye w-5"></i> KPI Goals
                            </a>
                        </div>
                    </div>

                    <!-- Warranty & Protection -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-sky-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-shield-alt text-sky-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Warranties</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="<?php echo e(route('warranties.policies.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-sky-600 transition-colors">
                                <i class="fas fa-file-contract w-5"></i> active Policies
                            </a>
                            <a href="<?php echo e(route('warranties.claims.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-sky-600 transition-colors">
                                <i class="fas fa-exclamation-triangle w-5"></i> Claim Center
                            </a>
                            <a href="<?php echo e(route('warranties.providers.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-sky-600 transition-colors">
                                <i class="fas fa-truck-loading w-5"></i> Providers
                            </a>
                        </div>
                    </div>

                    <!-- Advertising & Marketing Hub -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-rose-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-ad text-rose-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Ad Tech Hub</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="<?php echo e(route('ads.campaigns.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-rose-600 transition-colors">
                                <i class="fas fa-bullhorn w-5"></i> Ad Campaigns
                            </a>
                            <a href="<?php echo e(route('ads.placements.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-rose-600 transition-colors">
                                <i class="fas fa-layer-group w-5"></i> Ad Placements
                            </a>
                            <a href="<?php echo e(route('ads.analytics.dashboard')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-rose-600 transition-colors">
                                <i class="fas fa-chart-line w-5"></i> Ad Analytics
                            </a>
                        </div>
                    </div>

                    <!-- Tax & Compliance Ecosystem -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-indigo-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-file-invoice-dollar text-indigo-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Tax Systems</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="<?php echo e(route('taxes.filing')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                                <i class="fas fa-file-export w-5"></i> Tax Filings
                            </a>
                            <a href="<?php echo e(route('taxes.vat.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                                <i class="fas fa-percent w-5"></i> VAT Management
                            </a>
                            <a href="<?php echo e(route('taxes.reports.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                                <i class="fas fa-clipboard-list w-5"></i> Compliance
                            </a>
                        </div>
                    </div>

                    <!-- Big Data & AI Lab -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-cyan-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-database text-cyan-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Big Data Lab</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="<?php echo e(route('analytics-alt.bigdata.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-cyan-600 transition-colors">
                                <i class="fas fa-microchip w-5"></i> Predictive AI
                            </a>
                            <a href="<?php echo e(route('analytics-alt.heatmap.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-cyan-600 transition-colors">
                                <i class="fas fa-fire w-5"></i> Visual Heatmaps
                            </a>
                            <a href="<?php echo e(route('analytics-alt.sentiment.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-cyan-600 transition-colors">
                                <i class="fas fa-comment-dots w-5"></i> Sentiment Analysis
                            </a>
                        </div>
                    </div>

                    <!-- Investment & DeFi Center -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-violet-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-coins text-violet-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">DeFi Hub</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="<?php echo e(route('investors.crowdfunding.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-violet-600 transition-colors">
                                <i class="fas fa-users w-5"></i> Crowdfunding
                            </a>
                            <a href="<?php echo e(route('investors.defi.loans.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-violet-600 transition-colors">
                                <i class="fas fa-hand-holding-usd w-5"></i> DeFi Loans
                            </a>
                            <a href="#"
                                class="flex items-center text-sm text-gray-600 hover:text-violet-600 transition-colors">
                                <i class="fas fa-exclamation-circle w-5"></i> Risk Assessment
                            </a>
                        </div>
                    </div>

                    <!-- Corporate & Developer Suite -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-orange-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-building text-orange-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Developer Suite</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="<?php echo e(route('developer.bim.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-orange-600 transition-colors">
                                <i class="fas fa-cube w-5"></i> BIM Models
                            </a>
                            <a href="<?php echo e(route('developer.construction-updates.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-orange-600 transition-colors">
                                <i class="fas fa-tools w-5"></i> Construction
                            </a>
                            <a href="<?php echo e(route('developer.permits.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-orange-600 transition-colors">
                                <i class="fas fa-file-signature w-5"></i> Permits
                            </a>
                        </div>
                    </div>

                    <!-- Subscription & Billing Engine -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-fuchsia-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-box text-fuchsia-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Subscriptions</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="<?php echo e(route('subscriptions.plans.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-fuchsia-600 transition-colors">
                                <i class="fas fa-tags w-5"></i> Plan Engine
                            </a>
                            <a href="<?php echo e(route('subscriptions.invoices.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-fuchsia-600 transition-colors">
                                <i class="fas fa-file-invoice w-5"></i> Billing Hub
                            </a>
                            <a href="<?php echo e(route('subscriptions.usage.index', ['subscription' => 1])); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-fuchsia-600 transition-colors">
                                <i class="fas fa-tachometer-alt w-5"></i> Usage Tracking
                            </a>
                        </div>
                    </div>

                    <!-- God-Mode AI Suite -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-teal-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-robot text-teal-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">AI God-Mode</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="<?php echo e(route('ai.price-predictor.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-teal-600 transition-colors">
                                <i class="fas fa-crystal-ball w-5"></i> Price Predictor
                            </a>
                            <a href="<?php echo e(route('ai.fraud.detection')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-teal-600 transition-colors">
                                <i class="fas fa-user-secret w-5"></i> Fraud Detection
                            </a>
                            <a href="<?php echo e(route('ai.virtual-staging.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-teal-600 transition-colors">
                                <i class="fas fa-vr-cardboard w-5"></i> Virtual Staging
                            </a>
                        </div>
                    </div>

                    <!-- Supreme CMS -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-pink-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-pen-fancy text-pink-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Supreme CMS</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="<?php echo e(route('admin.blog.posts.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-pink-600 transition-colors">
                                <i class="fas fa-blog w-5"></i> Blog Network
                            </a>
                            <a href="<?php echo e(route('admin.menus.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-pink-600 transition-colors">
                                <i class="fas fa-bars w-5"></i> Menu Builder
                            </a>
                            <a href="<?php echo e(route('admin.media.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-pink-600 transition-colors">
                                <i class="fas fa-photo-video w-5"></i> Media Library
                            </a>
                        </div>
                    </div>

                    <!-- Agent CRM Pro -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-blue-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-user-tie text-blue-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Agent CRM Pro</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="<?php echo e(route('agent.crm.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-blue-600 transition-colors">
                                <i class="fas fa-address-book w-5"></i> CRM Dashboard
                            </a>
                            <a href="<?php echo e(route('agent.offers.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-blue-600 transition-colors">
                                <i class="fas fa-handshake w-5"></i> Offer System
                            </a>
                            <a href="<?php echo e(route('agents.commissions.index', ['agent' => 1])); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-blue-600 transition-colors">
                                <i class="fas fa-percent w-5"></i> Commissions
                            </a>
                        </div>
                    </div>

                    <!-- SEO & Utility Master -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-lime-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-search-dollar text-lime-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">SEO Master</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="<?php echo e(route('admin.seo.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-lime-600 transition-colors">
                                <i class="fas fa-search w-5"></i> SEO Tools
                            </a>
                            <a href="<?php echo e(route('admin.seo.generate-sitemap')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-lime-600 transition-colors">
                                <i class="fas fa-sitemap w-5"></i> Sitemap Gen
                            </a>
                            <a href="<?php echo e(route('admin.pages.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-lime-600 transition-colors">
                                <i class="fas fa-file-alt w-5"></i> Static Pages
                            </a>
                        </div>
                    </div>

                    <!-- CMS Extended (News & Help) -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-yellow-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-newspaper text-yellow-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">Content Hub</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="<?php echo e(route('admin.news.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-yellow-600 transition-colors">
                                <i class="fas fa-bullhorn w-5"></i> News Center
                            </a>
                            <a href="<?php echo e(route('admin.faqs.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-yellow-600 transition-colors">
                                <i class="fas fa-question-circle w-5"></i> Manage FAQs
                            </a>
                            <a href="<?php echo e(route('admin.guides.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-yellow-600 transition-colors">
                                <i class="fas fa-book w-5"></i> User Guides
                            </a>
                        </div>
                    </div>

                    <!-- Companies & Widgets -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-indigo-100 rounded-lg p-2 mr-3">
                                <i class="fas fa-cubes text-indigo-600"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">System Utilities</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="<?php echo e(route('admin.companies.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                                <i class="fas fa-building w-5"></i> Companies
                            </a>
                            <a href="<?php echo e(route('admin.widgets.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                                <i class="fas fa-puzzle-piece w-5"></i> Widget Mgr
                            </a>
                            <a href="<?php echo e(route('amenities.search')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-indigo-600 transition-colors">
                                <i class="fas fa-list-ul w-5"></i> Amenities
                            </a>
                        </div>
                    </div>

                    <!-- System Governance -->
                    <div class="border border-gray-100 rounded-xl p-5 hover:shadow-md transition-shadow bg-gray-50/30">
                        <div class="flex items-center mb-4">
                            <div class="bg-gray-200 rounded-lg p-2 mr-3">
                                <i class="fas fa-terminal text-gray-700"></i>
                            </div>
                            <h4 class="font-bold text-gray-900">System Core</h4>
                        </div>
                        <div class="space-y-2.5">
                            <a href="<?php echo e(route('requests.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-gray-900 transition-colors">
                                <i class="fas fa-exchange-alt w-5"></i> Request Logs
                            </a>
                            <a href="<?php echo e(route('admin.errors.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-gray-900 transition-colors">
                                <i class="fas fa-bug w-5"></i> Error Tracking
                            </a>
                            <a href="<?php echo e(route('routes.index')); ?>"
                                class="flex items-center text-sm text-gray-600 hover:text-gray-900 transition-colors">
                                <i class="fas fa-map w-5"></i> System Map
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Log -->
            <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Recent Activity</h3>
                    <button onclick="refreshActivity()" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>

                <div class="space-y-3">
                    <?php $__empty_1 = true; $__currentLoopData = $stats['recent_activity']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <div class="bg-blue-100 rounded-full p-2 mr-3">
                                <i class="fas fa-<?php echo e($activity['icon']); ?> text-blue-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-800"><?php echo e($activity['message']); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e($activity['time']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-gray-500 text-sm">No recent activity</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Modal -->
    <div id="reportsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg p-6 max-w-2xl mx-4 w-full">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">System Reports</h3>

            <div class="grid grid-cols-2 gap-4">
                <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer">
                    <h4 class="font-medium text-gray-800 mb-2">User Report</h4>
                    <p class="text-sm text-gray-600 mb-3">Detailed user statistics and analytics</p>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 text-sm">
                        Generate Report
                    </button>
                </div>

                <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer">
                    <h4 class="font-medium text-gray-800 mb-2">Financial Report</h4>
                    <p class="text-sm text-gray-600 mb-3">Revenue, payments, and transaction data</p>
                    <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 text-sm">
                        Generate Report
                    </button>
                </div>

                <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer">
                    <h4 class="font-medium text-gray-800 mb-2">Property Report</h4>
                    <p class="text-sm text-gray-600 mb-3">Property listings and performance metrics</p>
                    <button class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 text-sm">
                        Generate Report
                    </button>
                </div>

                <div class="border rounded-lg p-4 hover:bg-gray-50 cursor-pointer">
                    <h4 class="font-medium text-gray-800 mb-2">System Report</h4>
                    <p class="text-sm text-gray-600 mb-3">System performance and health metrics</p>
                    <button class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700 text-sm">
                        Generate Report
                    </button>
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <button onclick="closeReportsModal()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // User Growth Chart
            const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
            new Chart(userGrowthCtx, {
                type: 'line',
                data: {
                    labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
                    datasets: [{
                        label: 'المستخدمون الجدد',
                        data: [12, 19, 3, 5, 2, 3],
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgb(59, 130, 246)',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                font: {
                                    size: 12,
                                    family: 'system-ui'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            cornerRadius: 8,
                            titleFont: {
                                size: 14
                            },
                            bodyFont: {
                                size: 13
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });

            // Revenue Chart
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'bar',
                data: {
                    labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
                    datasets: [{
                        label: 'الإيرادات ($)',
                        data: [12000, 19000, 30000, 50000, 42000, 38000],
                        backgroundColor: [
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(34, 197, 94, 0.8)',
                            'rgba(34, 197, 94, 0.8)'
                        ],
                        borderColor: 'rgb(34, 197, 94)',
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                font: {
                                    size: 12,
                                    family: 'system-ui'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            cornerRadius: 8,
                            titleFont: {
                                size: 14
                            },
                            bodyFont: {
                                size: 13
                            },
                            callbacks: {
                                label: function(context) {
                                    return 'الإيرادات: $' + context.parsed.y.toLocaleString();
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                font: {
                                    size: 11
                                },
                                callback: function(value) {
                                    return '$' + value.toLocaleString();
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        });

        function refreshData() {
            location.reload();
        }

        function refreshActivity() {
            fetch('/admin/activity')
                .then(response => response.json())
                .then(data => {
                    location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        function showReportsModal() {
            document.getElementById('reportsModal').classList.remove('hidden');
        }

        function closeReportsModal() {
            document.getElementById('reportsModal').classList.add('hidden');
        }

        // Auto-refresh every 30 seconds
        setInterval(refreshData, 30000);
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>