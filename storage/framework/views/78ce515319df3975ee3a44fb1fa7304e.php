

<?php $__env->startSection('title', 'لوحة التحكم'); ?>

<?php $__env->startSection('page-title', 'لوحة التحكم'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-lg shadow-lg p-6 mb-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold mb-2">لوحة التحكم الشاملة</h1>
                    <p class="text-blue-100">مرحباً <?php echo e(Auth::user()->name); ?>! كل تحكمات الموقع في مكان واحد</p>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-blue-100"><?php echo e(now()->format('M j, Y H:i')); ?></span>
                    <button onclick="refreshDashboard()" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg transition-colors" style="color:red !important">
                        <i class="fas fa-sync-alt mr-2"></i>
                        تحديث
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">المستخدمون</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo e($stats['site']['total_users']); ?></p>
                        <p class="text-sm text-green-600">+<?php echo e($stats['site']['new_users_today']); ?> اليوم</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-home text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">العقارات</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo e($stats['site']['total_properties']); ?></p>
                        <p class="text-sm text-green-600">+<?php echo e($stats['site']['new_properties_today']); ?> اليوم</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-project-diagram text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">المشاريع</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo e($stats['site']['total_projects']); ?></p>
                        <p class="text-sm text-gray-500"><?php echo e($stats['site']['total_tasks']); ?> مهمة</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-bell text-yellow-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">الإشعارات</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo e($stats['user']['unread_notifications']); ?></p>
                        <p class="text-sm text-red-600">غير مقروءة</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
            <!-- Admin Dashboard Card -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow text-white">
                <div class="flex items-center mb-4">
                    <div class="bg-white bg-opacity-20 rounded-full p-3 mr-3">
                        <i class="fas fa-shield-alt text-white text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-white">لوحة الأدمن</h3>
                </div>
                <div class="space-y-2">
                    <a href="<?php echo e(route('admin.dashboard')); ?>" class="flex items-center p-3 rounded-lg hover:bg-white hover:bg-opacity-10 transition-colors">
                        <i class="fas fa-tachometer-alt text-white mr-3 text-sm"></i>
                        <span class="text-sm text-white">لوحة تحكم الأدمن</span>
                        <i class="fas fa-chevron-left text-white mr-auto text-xs"></i>
                    </a>
                    <a href="<?php echo e(route('admin.users.index')); ?>" class="flex items-center p-3 rounded-lg hover:bg-white hover:bg-opacity-10 transition-colors">
                        <i class="fas fa-users text-white mr-3 text-sm"></i>
                        <span class="text-sm text-white">إدارة المستخدمين</span>
                        <i class="fas fa-chevron-left text-white mr-auto text-xs"></i>
                    </a>
                    <a href="<?php echo e(route('admin.activity')); ?>" class="flex items-center p-3 rounded-lg hover:bg-white hover:bg-opacity-10 transition-colors">
                        <i class="fas fa-history text-white mr-3 text-sm"></i>
                        <span class="text-sm text-white">سجل الأنشطة</span>
                        <i class="fas fa-chevron-left text-white mr-auto text-xs"></i>
                    </a>
                </div>
            </div>

            <?php $__currentLoopData = $stats['quick_links']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category => $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center mb-4">
                        <div class="bg-<?php echo e($data['color']); ?>-100 rounded-full p-3 mr-3">
                            <i class="<?php echo e($data['icon']); ?> text-<?php echo e($data['color']); ?>-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800"><?php echo e($data['title']); ?></h3>
                    </div>
                    <div class="space-y-2">
                        <?php $__currentLoopData = $data['links']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route($link['route'])); ?>" class="flex items-center p-2 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="<?php echo e($link['icon']); ?> text-gray-400 mr-3 text-sm"></i>
                                <span class="text-sm text-gray-700"><?php echo e($link['title']); ?></span>
                                <i class="fas fa-chevron-left text-gray-300 mr-auto text-xs"></i>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- Charts and Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Charts Section -->
            <div class="lg:col-span-2 space-y-6">
                <!-- User Growth Chart -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">نمو المستخدمين</h3>
                    <canvas id="userGrowthChart" width="400" height="200"></canvas>
                </div>
                
                <!-- Property Growth Chart -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">نمو العقارات</h3>
                    <canvas id="propertyGrowthChart" width="400" height="200"></canvas>
                </div>
            </div>
            
            <!-- System Status -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">حالة النظام</h3>
                <div class="space-y-3">
                    <?php $__currentLoopData = $stats['system_status']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="text-sm font-medium text-gray-700"><?php echo e(ucfirst($key)); ?></span>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                <?php if($status === 'Online' || $status === 'Active' || $status === 'Configured'): ?>
                                    bg-green-100 text-green-800
                                <?php else: ?>
                                    bg-yellow-100 text-yellow-800
                                <?php endif; ?>">
                                <?php echo e($status); ?>

                            </span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

        <!-- Recent Activity Tables -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Recent Users -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">المستخدمون الجدد</h3>
                    <a href="<?php echo e(route('admin.users.index')); ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                        عرض الكل →
                    </a>
                </div>
                <div class="space-y-3">
                    <?php $__empty_1 = true; $__currentLoopData = $stats['recent_users']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
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
                        <p class="text-gray-500 text-sm text-center py-4">لا يوجد مستخدمين جدد</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Recent Properties -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">العقارات الجديدة</h3>
                    <a href="<?php echo e(route('properties.index')); ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                        عرض الكل →
                    </a>
                </div>
                <div class="space-y-3">
                    <?php $__empty_1 = true; $__currentLoopData = $stats['recent_properties']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $property): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
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
                        <p class="text-gray-500 text-sm text-center py-4">لا يوجد عقارات جديدة</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Quick Actions Bar -->
        <div class="bg-gradient-to-r from-green-600 to-teal-600 rounded-lg shadow-lg p-6 text-white">
            <h3 class="text-xl font-bold mb-4">إجراءات سريعة</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="<?php echo e(route('properties.create')); ?>" class="bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg p-4 text-center transition-colors">
                    <i class="fas fa-plus-circle text-2xl mb-2"></i>
                    <p class="font-medium">إضافة عقار</p>
                </a>
                <a href="/agents/create" class="bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg p-4 text-center transition-colors">
                    <i class="fas fa-user-plus text-2xl mb-2"></i>
                    <p class="font-medium">إضافة وكيل</p>
                </a>
                <a href="<?php echo e(route('projects.create')); ?>" class="bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg p-4 text-center transition-colors">
                    <i class="fas fa-project-diagram text-2xl mb-2"></i>
                    <p class="font-medium">إضافة مشروع</p>
                </a>
                <a href="<?php echo e(route('reports.sales.index')); ?>" class="bg-white bg-opacity-20 hover:bg-opacity-30 rounded-lg p-4 text-center transition-colors">
                    <i class="fas fa-chart-bar text-2xl mb-2"></i>
                    <p class="font-medium">عرض التقارير</p>
                </a>
            </div>
        </div>

        <!-- All Routes Section -->
        <div class="mt-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-6">جميع روابط النظام</h3>
                
                <!-- Properties Routes -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3">
                        <i class="fas fa-home text-blue-600 mr-2"></i>العقارات
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <a href="<?php echo e(route('properties.index')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-list text-gray-400 ml-3"></i>
                            <span class="text-sm">قائمة العقارات</span>
                        </a>
                        <a href="<?php echo e(route('properties.create')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-plus text-gray-400 ml-3"></i>
                            <span class="text-sm">إضافة عقار</span>
                        </a>
                        <a href="<?php echo e(route('properties.recommendations')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-star text-gray-400 ml-3"></i>
                            <span class="text-sm">توصيات العقارات</span>
                        </a>
                        <a href="<?php echo e(route('property-types.index')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-tags text-gray-400 ml-3"></i>
                            <span class="text-sm">أنواع العقارات</span>
                        </a>
                    </div>
                </div>

                <!-- Agents Routes -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3">
                        <i class="fas fa-user-tie text-green-600 mr-2"></i>الوكلاء
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <a href="<?php echo e(route('agents.directory')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-address-book text-gray-400 ml-3"></i>
                            <span class="text-sm">دليل الوكلاء</span>
                        </a>
                        <a href="/agents" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-users text-gray-400 ml-3"></i>
                            <span class="text-sm">قائمة الوكلاء</span>
                        </a>
                    </div>
                </div>

                <!-- Projects Routes -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3">
                        <i class="fas fa-project-diagram text-purple-600 mr-2"></i>المشاريع
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <a href="<?php echo e(route('projects.index')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-list text-gray-400 ml-3"></i>
                            <span class="text-sm">قائمة المشاريع</span>
                        </a>
                        <a href="<?php echo e(route('projects.create')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-plus text-gray-400 ml-3"></i>
                            <span class="text-sm">إضافة مشروع</span>
                        </a>
                        <a href="<?php echo e(route('projects.dashboard')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-tachometer-alt text-gray-400 ml-3"></i>
                            <span class="text-sm">لوحة المشاريع</span>
                        </a>
                        <a href="<?php echo e(route('projects.gantt.dashboard')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-chart-gantt text-gray-400 ml-3"></i>
                            <span class="text-sm">مخطط جانت</span>
                        </a>
                    </div>
                </div>

                <!-- Companies Routes -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3">
                        <i class="fas fa-building text-indigo-600 mr-2"></i>الشركات
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <a href="<?php echo e(route('companies.index')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-list text-gray-400 ml-3"></i>
                            <span class="text-sm">قائمة الشركات</span>
                        </a>
                        <a href="<?php echo e(route('companies.create')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-plus text-gray-400 ml-3"></i>
                            <span class="text-sm">إضافة شركة</span>
                        </a>
                    </div>
                </div>

                <!-- Reports Routes -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3">
                        <i class="fas fa-chart-line text-red-600 mr-2"></i>التقارير
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <a href="<?php echo e(route('reports.index')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-file-alt text-gray-400 ml-3"></i>
                            <span class="text-sm">قائمة التقارير</span>
                        </a>
                        <a href="<?php echo e(route('reports.dashboard')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-tachometer-alt text-gray-400 ml-3"></i>
                            <span class="text-sm">لوحة التقارير</span>
                        </a>
                        <a href="<?php echo e(route('reports.sales.index')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-dollar-sign text-gray-400 ml-3"></i>
                            <span class="text-sm">تقارير المبيعات</span>
                        </a>
                        <a href="<?php echo e(route('reports.performance.index')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-chart-bar text-gray-400 ml-3"></i>
                            <span class="text-sm">تقارير الأداء</span>
                        </a>
                    </div>
                </div>

                <!-- Financial Routes -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3">
                        <i class="fas fa-calculator text-yellow-600 mr-2"></i>التحليل المالي
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <a href="<?php echo e(route('financial.index')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-chart-pie text-gray-400 ml-3"></i>
                            <span class="text-sm">التحليل المالي</span>
                        </a>
                        <a href="<?php echo e(route('financial.dashboard')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-tachometer-alt text-gray-400 ml-3"></i>
                            <span class="text-sm">لوحة التحليل المالي</span>
                        </a>
                        <a href="<?php echo e(route('financial.analyses.create')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-plus text-gray-400 ml-3"></i>
                            <span class="text-sm">إنشاء تحليل</span>
                        </a>
                    </div>
                </div>

                <!-- User Management Routes -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3">
                        <i class="fas fa-users-cog text-teal-600 mr-2"></i>إدارة المستخدمين
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <a href="<?php echo e(route('user.dashboard')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-user text-gray-400 ml-3"></i>
                            <span class="text-sm">لوحة المستخدم</span>
                        </a>
                        <a href="<?php echo e(route('user.profile')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-id-card text-gray-400 ml-3"></i>
                            <span class="text-sm">الملف الشخصي</span>
                        </a>
                        <a href="<?php echo e(route('kyc.index')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-shield-alt text-gray-400 ml-3"></i>
                            <span class="text-sm">التحقق من الهوية</span>
                        </a>
                    </div>
                </div>

                <!-- Developer Routes -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3">
                        <i class="fas fa-hard-hat text-orange-600 mr-2"></i>المطورون
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <a href="<?php echo e(route('developer.index')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-list text-gray-400 ml-3"></i>
                            <span class="text-sm">قائمة المطورين</span>
                        </a>
                        <a href="<?php echo e(route('developer.create')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-plus text-gray-400 ml-3"></i>
                            <span class="text-sm">إضافة مطور</span>
                        </a>
                        <a href="<?php echo e(route('developer.projects.index')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-project-diagram text-gray-400 ml-3"></i>
                            <span class="text-sm">مشاريع المطور</span>
                        </a>
                    </div>
                </div>

                <!-- System Routes -->
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3">
                        <i class="fas fa-cogs text-gray-600 mr-2"></i>النظام
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-tachometer-alt text-gray-400 ml-3"></i>
                            <span class="text-sm">لوحة التحكم الرئيسية</span>
                        </a>
                        <a href="<?php echo e(route('dashboard.settings')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-cog text-gray-400 ml-3"></i>
                            <span class="text-sm">الإعدادات</span>
                        </a>
                        <a href="<?php echo e(route('analytics.dashboard')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-chart-line text-gray-400 ml-3"></i>
                            <span class="text-sm">التحليلات</span>
                        </a>
                        <a href="<?php echo e(url('admin/errors')); ?>" class="flex items-center p-3 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                            <i class="fas fa-bug text-red-600 ml-3"></i>
                            <span class="text-sm">فحص أخطاء النظام</span>
                        </a>
                        <a href="/db-check" class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <i class="fas fa-database text-blue-600 ml-3"></i>
                            <span class="text-sm">فحص قاعدة البيانات</span>
                        </a>
                        <a href="<?php echo e(url('requests')); ?>" class="flex items-center p-3 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
                            <i class="fas fa-database text-blue-600 ml-3"></i>
                            <span class="text-sm">مراقبه النظام </span>
                        </a>
                        <a href="<?php echo e(route('home')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-home text-gray-400 ml-3"></i>
                            <span class="text-sm">الصفحة الرئيسية</span>
                        </a>
                        <a href="<?php echo e(route('about')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-info-circle text-gray-400 ml-3"></i>
                            <span class="text-sm">من نحن</span>
                        </a>
                        <a href="<?php echo e(route('contact')); ?>" class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <i class="fas fa-envelope text-gray-400 ml-3"></i>
                            <span class="text-sm">اتصل بنا</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Initialize User Growth Chart
            const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
            new Chart(userGrowthCtx, {
                type: 'line',
                data: {
                    labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
                    datasets: [{
                        label: 'المستخدمون الجدد',
                        data: <?php echo json_encode($stats['charts']['user_growth'], 15, 512) ?>,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Initialize Property Growth Chart
            const propertyGrowthCtx = document.getElementById('propertyGrowthChart').getContext('2d');
            new Chart(propertyGrowthCtx, {
                type: 'bar',
                data: {
                    labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
                    datasets: [{
                        label: 'العقارات الجديدة',
                        data: <?php echo json_encode($stats['charts']['property_growth'], 15, 512) ?>,
                        backgroundColor: 'rgba(34, 197, 94, 0.8)'
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            function refreshDashboard() {
                location.reload();
            }

            // Auto-refresh every 5 minutes
            setInterval(refreshDashboard, 300000);
        </script>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/dashboard/index.blade.php ENDPATH**/ ?>