<?php $__env->startSection('title', 'تحليلات العملاء'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow p-6">
            <div class="text-sm text-slate-500 mb-1">عدد المستخدمين</div>
            <div class="text-3xl font-bold"><?php echo e(number_format($totals['users'] ?? 0)); ?></div>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <div class="text-sm text-slate-500 mb-1">إجمالي الاستفسارات</div>
            <div class="text-3xl font-bold"><?php echo e(number_format($totals['inquiries'] ?? 0)); ?></div>
        </div>
        <div class="bg-white rounded-xl shadow p-6">
            <div class="text-sm text-slate-500 mb-1">الإعجابات</div>
            <div class="text-3xl font-bold"><?php echo e(number_format($totals['favorites'] ?? 0)); ?></div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold">أعلى المدن تفاعلاً (استفسارات)</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-right">
                <thead>
                    <tr class="text-slate-600 border-b">
                        <th class="py-2 pr-2">المدينة</th>
                        <th class="py-2">إجمالي الاستفسارات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $topCities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="border-b">
                            <td class="py-2 pr-2"><?php echo e($row->city ?? '—'); ?></td>
                            <td class="py-2"><?php echo e(number_format($row->total_inquiries ?? 0)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td class="py-4 text-center" colspan="2">لا توجد بيانات</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/reports/client_analytics.blade.php ENDPATH**/ ?>