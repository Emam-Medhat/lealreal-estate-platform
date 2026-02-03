

<?php $__env->startSection('title', 'التقارير'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">التقارير</h1>
            <p class="text-gray-600 mt-2">إدارة وعرض جميع التقارير المتاحة</p>
        </div>
        <div class="flex space-x-4 space-x-reverse">
            <a href="<?php echo e(route('reports.dashboard')); ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-chart-line ml-2"></i>
                لوحة التحكم
            </a>
            <a href="<?php echo e(route('reports.create')); ?>" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-plus ml-2"></i>
                إنشاء تقرير
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form method="GET" action="<?php echo e(route('reports.index')); ?>" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="ابحث عن تقرير...">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">نوع التقرير</label>
                <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">جميع الأنواع</option>
                    <option value="sales" <?php echo e(request('type') == 'sales' ? 'selected' : ''); ?>>المبيعات</option>
                    <option value="performance" <?php echo e(request('type') == 'performance' ? 'selected' : ''); ?>>الأداء</option>
                    <option value="market" <?php echo e(request('type') == 'market' ? 'selected' : ''); ?>>السوق</option>
                    <option value="financial" <?php echo e(request('type') == 'financial' ? 'selected' : ''); ?>>المالي</option>
                    <option value="custom" <?php echo e(request('type') == 'custom' ? 'selected' : ''); ?>>مخصص</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">جميع الحالات</option>
                    <option value="completed" <?php echo e(request('status') == 'completed' ? 'selected' : ''); ?>>مكتمل</option>
                    <option value="generating" <?php echo e(request('status') == 'generating' ? 'selected' : ''); ?>>قيد الإنشاء</option>
                    <option value="failed" <?php echo e(request('status') == 'failed' ? 'selected' : ''); ?>>فشل</option>
                    <option value="scheduled" <?php echo e(request('status') == 'scheduled' ? 'selected' : ''); ?>>مجدول</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-filter ml-2"></i>
                    تطبيق الفلاتر
                </button>
            </div>
        </form>
    </div>

    <!-- Reports List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            التقرير
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            النوع
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الحالة
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الإنشاء
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الحجم
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            المشاهدات
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الإجراءات
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__empty_1 = true; $__currentLoopData = $reports; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900"><?php echo e($report->title); ?></div>
                                    <?php if($report->description): ?>
                                        <div class="text-sm text-gray-500"><?php echo e(Str::limit($report->description, 50)); ?></div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <?php echo e(__('reports.types.' . $report->type)); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo e($report->status === 'completed' ? 'bg-green-100 text-green-800' : ($report->status === 'generating' ? 'bg-blue-100 text-blue-800' : ($report->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800'))); ?>">
                                    <?php echo e($report->status === 'completed' ? 'مكتمل' : ($report->status === 'generating' ? 'قيد الإنشاء' : ($report->status === 'failed' ? 'فشل' : ($report->status === 'scheduled' ? 'مجدول' : 'غير معروف')))); ?>

                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo e($report->created_at->format('Y-m-d H:i')); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo e($report->status === 'completed' && $report->file_path ? '2.5 MB' : 'N/A'); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo e($report->view_count); ?>

                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                <div class="flex space-x-2 space-x-reverse">
                                    <?php if($report->status === 'completed' && $report->file_path): ?>
                                        <a href="<?php echo e(route('reports.download', $report)); ?>" class="text-blue-600 hover:text-blue-900" title="تحميل">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?php echo e(route('reports.show', $report)); ?>" class="text-green-600 hover:text-green-900" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if($report->status === 'completed'): ?>
                                        <a href="<?php echo e(route('reports.regenerate', $report)); ?>" class="text-yellow-600 hover:text-yellow-900" title="إعادة إنشاء">
                                            <i class="fas fa-sync"></i>
                                        </a>
                                    <?php endif; ?>
                                    <form method="POST" action="<?php echo e(route('reports.destroy', $report)); ?>" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="حذف" onclick="return confirm('هل أنت متأكد من حذف هذا التقرير؟')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                <div class="py-8">
                                    <i class="fas fa-file-alt text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-lg font-medium">لا توجد تقارير</p>
                                    <p class="text-sm">ابدأ بإنشاء أول تقرير لك</p>
                                    <a href="<?php echo e(route('reports.create')); ?>" class="mt-4 inline-block bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                        <i class="fas fa-plus ml-2"></i>
                                        إنشاء تقرير
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if($reports->hasPages()): ?>
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <?php echo e($reports->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/reports/index.blade.php ENDPATH**/ ?>