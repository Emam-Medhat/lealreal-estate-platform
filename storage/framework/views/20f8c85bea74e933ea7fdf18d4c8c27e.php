

<?php $__env->startSection('title', 'تقارير السوق'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-700 rounded-lg shadow-lg p-6 mb-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold mb-2">تقارير السوق</h1>
                    <p class="text-purple-100">تحليل شامل لاتجاهات السوق والمنافسة</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="<?php echo e(route('reports.market.create')); ?>" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        تقرير سوق جديد
                    </a>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">إجمالي التقارير</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo e($stats['total_reports'] ?? 0); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-trending-up text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">متوسط نمو السوق</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo e($stats['avg_growth'] ?? '0%'); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-home text-purple-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">العقارات المتاحة</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo e($stats['available_properties'] ?? 0); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-dollar-sign text-yellow-600 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">متوسط السعر</p>
                        <p class="text-2xl font-bold text-gray-800">$<?php echo e(number_format($stats['avg_price'] ?? 0, 0)); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Period Filter -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <label class="text-sm font-medium text-gray-700">الفترة:</label>
                    <div class="flex space-x-2">
                        <a href="<?php echo e(route('reports.market.index', ['period' => 'week'])); ?>" 
                           class="px-4 py-2 rounded-lg <?php echo e($period == 'week' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'); ?> transition-colors">
                            أسبوع
                        </a>
                        <a href="<?php echo e(route('reports.market.index', ['period' => 'month'])); ?>" 
                           class="px-4 py-2 rounded-lg <?php echo e($period == 'month' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'); ?> transition-colors">
                            شهر
                        </a>
                        <a href="<?php echo e(route('reports.market.index', ['period' => 'quarter'])); ?>" 
                           class="px-4 py-2 rounded-lg <?php echo e($period == 'quarter' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'); ?> transition-colors">
                            ربع سنة
                        </a>
                        <a href="<?php echo e(route('reports.market.index', ['period' => 'year'])); ?>" 
                           class="px-4 py-2 rounded-lg <?php echo e($period == 'year' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'); ?> transition-colors">
                            سنة
                        </a>
                    </div>
                </div>
                <button onclick="refreshMarketData()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fas fa-sync-alt ml-2"></i>
                    تحديث
                </button>
            </div>
        </div>

        <!-- Reports List -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">تقارير السوق الحديثة</h3>
            </div>
            <div class="overflow-x-auto">
                <?php if($reports->count() > 0): ?>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التقرير</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الفترة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النمو</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900"><?php echo e($report->title); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo e($report->description); ?></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo e($report->period_start->format('Y-m-d')); ?> - <?php echo e($report->period_end->format('Y-m-d')); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold text-green-600">
                                            <?php echo e($report->growth_rate ?? '0%'); ?>

                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            مكتمل
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo e($report->created_at->diffForHumans()); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="<?php echo e(route('reports.market.show', $report->id)); ?>" class="text-blue-600 hover:text-blue-900">عرض</a>
                                        <a href="<?php echo e(route('reports.market.data')); ?>" class="ml-4 text-green-600 hover:text-green-900">تحميل</a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                    
                    <!-- Pagination -->
                    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <?php echo e($reports->links()); ?>

                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    عرض <span class="font-medium"><?php echo e($reports->firstItem()); ?></span>
                                    إلى <span class="font-medium"><?php echo e($reports->lastItem()); ?></span>
                                    من <span class="font-medium"><?php echo e($reports->total()); ?></span> نتائج
                                </p>
                            </div>
                            <div>
                                <?php echo e($reports->links()); ?>

                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="text-center py-12">
                        <i class="fas fa-chart-pie text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد تقارير سوق</h3>
                        <p class="text-gray-500 mb-4">ابدأ بإنشاء أول تقرير سوق للحصول على رؤى قيمة</p>
                        <a href="<?php echo e(route('reports.market.create')); ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-plus ml-2"></i>
                            إنشاء تقرير سوق
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function refreshMarketData() {
    location.reload();
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/reports/market/index.blade.php ENDPATH**/ ?>