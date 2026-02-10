

<?php $__env->startSection('title', 'لوحة التقارير'); ?>

<?php $__env->startSection('content'); ?>

<div class="max-w-7xl mx-auto">
    <!-- Reports Header -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-xl shadow-lg p-8 mb-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-3 flex items-center">
                    <i class="fas fa-chart-line ml-3 text-blue-200"></i>
                    لوحة التقارير
                </h1>
                <p class="text-blue-100 text-lg">إدارة وتحليل تقارير العقارات</p>
            </div>
            <div class="flex items-center space-x-3 space-x-reverse">
                <a href="<?php echo e(route('reports.create')); ?>" class="bg-white text-blue-600 px-6 py-3 rounded-lg hover:bg-blue-50 transition-all duration-200 shadow-md hover:shadow-lg font-medium">
                    <i class="fas fa-plus ml-2"></i>
                    تقرير جديد
                </a>
                <a href="<?php echo e(route('reports.index')); ?>" class="bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-400 transition-all duration-200 shadow-md hover:shadow-lg font-medium">
                    <i class="fas fa-list ml-2"></i>
                    كل التقارير
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-blue-500 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">إجمالي التقارير</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-1"><?php echo e($reportStats['total_reports'] ?? 0); ?></h3>
                    <div class="flex items-center mt-2">
                        <i class="fas fa-arrow-up text-green-500 text-xs ml-1"></i>
                        <p class="text-xs text-green-600 font-medium">+5 هذا الشهر</p>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-blue-100 to-blue-200 text-blue-600 p-4 rounded-xl shadow-inner">
                    <i class="fas fa-file-alt text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-green-500 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">التقارير المنجزة</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-1"><?php echo e($reportStats['completed_reports'] ?? 0); ?></h3>
                    <div class="flex items-center mt-2">
                        <i class="fas fa-arrow-up text-green-500 text-xs ml-1"></i>
                        <p class="text-xs text-green-600 font-medium">+3 هذا الأسبوع</p>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-green-100 to-green-200 text-green-600 p-4 rounded-xl shadow-inner">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-purple-500 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">قيد المعالجة</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-1"><?php echo e($reportStats['pending_reports'] ?? 0); ?></h3>
                    <div class="flex items-center mt-2">
                        <i class="fas fa-exclamation-circle text-orange-500 text-xs ml-1"></i>
                        <p class="text-xs text-orange-600 font-medium">2 عاجل</p>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-purple-100 to-purple-200 text-purple-600 p-4 rounded-xl shadow-inner">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-6 border-l-4 border-orange-500 transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 font-medium">معدل النجاح</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-1"><?php echo e($reportStats['success_rate'] ?? 0); ?>%</h3>
                    <div class="flex items-center mt-2">
                        <i class="fas fa-arrow-up text-green-500 text-xs ml-1"></i>
                        <p class="text-xs text-green-600 font-medium">+2% هذا الشهر</p>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-orange-100 to-orange-200 text-orange-600 p-4 rounded-xl shadow-inner">
                    <i class="fas fa-chart-line text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reports -->
    <div class="bg-white rounded-xl shadow-lg p-7 mb-8">
        <div class="flex items-center justify-between mb-6 border-b pb-4">
            <h2 class="text-xl font-bold text-gray-800 flex items-center">
                <i class="fas fa-history ml-3 text-blue-600"></i>
                التقارير الحديثة
            </h2>
            <a href="<?php echo e(route('reports.index')); ?>" class="text-blue-600 hover:text-blue-800 font-medium text-sm">
                عرض الكل <i class="fas fa-arrow-left mr-1"></i>
            </a>
        </div>
        <div class="space-y-3">
            <?php $__empty_1 = true; $__currentLoopData = $recentReports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center justify-between p-5 bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl hover:from-blue-50 hover:to-blue-100 transition-all duration-200 border border-gray-200 hover:border-blue-300">
                    <div class="flex items-center">
                        <div class="bg-gradient-to-br from-blue-100 to-blue-200 text-blue-600 p-3 rounded-xl ml-4 shadow-inner">
                            <i class="fas fa-file-alt text-lg"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-lg"><?php echo e($report->title ?? 'تقرير غير مسمى'); ?></p>
                            <div class="flex items-center mt-1 text-sm text-gray-600">
                                <span class="bg-gray-200 px-2 py-1 rounded-md text-xs font-medium ml-2"><?php echo e($report->type ?? 'عام'); ?></span>
                                <span class="flex items-center">
                                    <i class="fas fa-clock ml-1 text-gray-400"></i>
                                    <?php echo e($report->created_at->diffForHumans()); ?>

                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <span class="px-3 py-1 text-xs font-bold rounded-full <?php echo e($report->status === 'completed' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-yellow-100 text-yellow-800 border border-yellow-200'); ?>">
                            <?php echo e($report->status === 'completed' ? 'مكتمل' : 'قيد المعالجة'); ?>

                        </span>
                        <a href="<?php echo e(route('reports.show', $report->id)); ?>" class="bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-12 text-gray-500">
                    <div class="bg-gray-100 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-file-alt text-3xl text-gray-400"></i>
                    </div>
                    <p class="text-lg font-medium text-gray-600 mb-2">لا توجد تقارير حديثة</p>
                    <p class="text-sm text-gray-500 mb-4">ابدأ بإنشاء أول تقرير لك</p>
                    <a href="<?php echo e(route('reports.create')); ?>" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors inline-flex items-center shadow-md hover:shadow-lg">
                        <i class="fas fa-plus ml-2"></i>
                        إنشاء أول تقرير
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-8 transform hover:-translate-y-2 border border-gray-100">
            <div class="text-center">
                <div class="bg-gradient-to-br from-blue-100 to-blue-200 text-blue-600 p-6 rounded-2xl inline-block mb-6 shadow-lg">
                    <i class="fas fa-chart-bar text-3xl"></i>
                </div>
                <h3 class="font-bold text-gray-800 mb-3 text-xl">تقارير المبيعات</h3>
                <p class="text-gray-600 mb-6 leading-relaxed">تحليلات شاملة للمبيعات والأداء المالي</p>
                <a href="<?php echo e(route('reports.sales.index')); ?>" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium shadow-md hover:shadow-lg inline-flex items-center">
                    عرض التقارير
                    <i class="fas fa-arrow-left mr-2"></i>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-8 transform hover:-translate-y-2 border border-gray-100">
            <div class="text-center">
                <div class="bg-gradient-to-br from-green-100 to-green-200 text-green-600 p-6 rounded-2xl inline-block mb-6 shadow-lg">
                    <i class="fas fa-tachometer-alt text-3xl"></i>
                </div>
                <h3 class="font-bold text-gray-800 mb-3 text-xl">تقارير الأداء</h3>
                <p class="text-gray-600 mb-6 leading-relaxed">قياسات الأداء والكفاءة التشغيلية</p>
                <a href="<?php echo e(route('reports.performance.index')); ?>" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-all duration-200 font-medium shadow-md hover:shadow-lg inline-flex items-center">
                    عرض التقارير
                    <i class="fas fa-arrow-left mr-2"></i>
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 p-8 transform hover:-translate-y-2 border border-gray-100">
            <div class="text-center">
                <div class="bg-gradient-to-br from-purple-100 to-purple-200 text-purple-600 p-6 rounded-2xl inline-block mb-6 shadow-lg">
                    <i class="fas fa-chart-line text-3xl"></i>
                </div>
                <h3 class="font-bold text-gray-800 mb-3 text-xl">تقارير السوق</h3>
                <p class="text-gray-600 mb-6 leading-relaxed">تحليلات السوق والاتجاهات العقارية</p>
                <a href="<?php echo e(route('reports.market.index')); ?>" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-all duration-200 font-medium shadow-md hover:shadow-lg inline-flex items-center">
                    عرض التقارير
                    <i class="fas fa-arrow-left mr-2"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/reports/dashboard.blade.php ENDPATH**/ ?>