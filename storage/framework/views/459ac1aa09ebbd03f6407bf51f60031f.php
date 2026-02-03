<?php $__env->startSection('title', 'أوامر العمل'); ?>

<?php $__env->startSection('content'); ?>
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">أوامر العمل</h1>
            <p class="text-gray-600 mt-1">إدارة أوامر العمل والصيانة</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="<?php echo e(route('maintenance.workorders.create')); ?>" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium flex items-center space-x-2 space-x-reverse transition-colors duration-200">
                <i class="fas fa-plus"></i>
                <span>إنشاء أمر عمل</span>
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">إجمالي أوامر العمل</p>
                <p class="text-2xl font-bold text-gray-900"><?php echo e($workOrders->total()); ?></p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-clipboard-list text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">قيد الانتظار</p>
                <p class="text-2xl font-bold text-yellow-600"><?php echo e($workOrders->where('status', 'pending')->count()); ?></p>
            </div>
            <div class="bg-yellow-100 rounded-full p-3">
                <i class="fas fa-clock text-yellow-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">قيد التنفيذ</p>
                <p class="text-2xl font-bold text-blue-600"><?php echo e($workOrders->where('status', 'in_progress')->count()); ?></p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-cogs text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">مكتملة</p>
                <p class="text-2xl font-bold text-green-600"><?php echo e($workOrders->where('status', 'completed')->count()); ?></p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <form method="GET" action="<?php echo e(route('maintenance.workorders.index')); ?>" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[300px]">
            <div class="relative">
                <input type="text" name="search" 
                       value="<?php echo e(request('search')); ?>"
                       placeholder="البحث عن أمر عمل..." 
                       class="w-full pr-10 pl-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
        </div>
        
        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">جميع الحالات</option>
            <option value="pending" <?php echo e(request('status') == 'pending' ? 'selected' : ''); ?>>قيد الانتظار</option>
            <option value="in_progress" <?php echo e(request('status') == 'in_progress' ? 'selected' : ''); ?>>قيد التنفيذ</option>
            <option value="completed" <?php echo e(request('status') == 'completed' ? 'selected' : ''); ?>>مكتمل</option>
            <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>ملغي</option>
        </select>
        
        <select name="priority" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">جميع الأولويات</option>
            <option value="low" <?php echo e(request('priority') == 'low' ? 'selected' : ''); ?>>منخفض</option>
            <option value="medium" <?php echo e(request('priority') == 'medium' ? 'selected' : ''); ?>>متوسط</option>
            <option value="high" <?php echo e(request('priority') == 'high' ? 'selected' : ''); ?>>عالي</option>
            <option value="emergency" <?php echo e(request('priority') == 'emergency' ? 'selected' : ''); ?>>طوارئ</option>
        </select>
        
        <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors duration-200">
            <i class="fas fa-filter ml-2"></i>
            فلترة
        </button>
        
        <a href="<?php echo e(route('maintenance.workorders.index')); ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg transition-colors duration-200">
            <i class="fas fa-redo ml-2"></i>
            إعادة تعيين
        </a>
    </form>
</div>

<!-- Work Orders Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الرقم</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العنوان</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الأولوية</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الموقع</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الفريق</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php $__empty_1 = true; $__currentLoopData = $workOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $workOrder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                #<?php echo e($workOrder->id); ?>

                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                <?php echo e($workOrder->title); ?>

                            </div>
                            <?php if($workOrder->description): ?>
                                <div class="text-sm text-gray-500"><?php echo e(Str::limit($workOrder->description, 50)); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                <?php if($workOrder->status == 'pending'): ?>
                                    bg-yellow-100 text-yellow-800
                                <?php elseif($workOrder->status == 'in_progress'): ?>
                                    bg-blue-100 text-blue-800
                                <?php elseif($workOrder->status == 'completed'): ?>
                                    bg-green-100 text-green-800
                                <?php elseif($workOrder->status == 'cancelled'): ?>
                                    bg-red-100 text-red-800
                                <?php else: ?>
                                    bg-gray-100 text-gray-800
                                <?php endif; ?>">
                                <?php if($workOrder->status == 'pending'): ?>
                                    قيد الانتظار
                                <?php elseif($workOrder->status == 'in_progress'): ?>
                                    قيد التنفيذ
                                <?php elseif($workOrder->status == 'completed'): ?>
                                    مكتمل
                                <?php elseif($workOrder->status == 'cancelled'): ?>
                                    ملغي
                                <?php else: ?>
                                    غير محدد
                                <?php endif; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                <?php if($workOrder->priority == 'low'): ?>
                                    bg-gray-100 text-gray-800
                                <?php elseif($workOrder->priority == 'medium'): ?>
                                    bg-blue-100 text-blue-800
                                <?php elseif($workOrder->priority == 'high'): ?>
                                    bg-orange-100 text-orange-800
                                <?php elseif($workOrder->priority == 'emergency'): ?>
                                    bg-red-100 text-red-800
                                <?php else: ?>
                                    bg-gray-100 text-gray-800
                                <?php endif; ?>">
                                <?php if($workOrder->priority == 'low'): ?>
                                    منخفض
                                <?php elseif($workOrder->priority == 'medium'): ?>
                                    متوسط
                                <?php elseif($workOrder->priority == 'high'): ?>
                                    عالي
                                <?php elseif($workOrder->priority == 'emergency'): ?>
                                    طوارئ
                                <?php else: ?>
                                    غير محدد
                                <?php endif; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <?php if($workOrder->property): ?>
                                    <?php echo e($workOrder->property->title); ?>

                                <?php else: ?>
                                    <span class="text-gray-400">غير محدد</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <?php if($workOrder->assignedTeam): ?>
                                    <?php echo e($workOrder->assignedTeam->name); ?>

                                <?php else: ?>
                                    <span class="text-gray-400">غير محدد</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <?php echo e($workOrder->created_at->format('Y-m-d')); ?>

                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <a href="<?php echo e(route('maintenance.workorders.show', $workOrder)); ?>" 
                                   class="text-blue-600 hover:text-blue-800 transition-colors duration-150"
                                   title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('maintenance.workorders.edit', $workOrder)); ?>" 
                                   class="text-green-600 hover:text-green-800 transition-colors duration-150"
                                   title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if($workOrder->status == 'pending'): ?>
                                    <form method="POST" action="<?php echo e(route('maintenance.workorders.start', $workOrder)); ?>" 
                                          class="inline"
                                          onsubmit="return confirm('هل أنت متأكد من بدء أمر العمل؟');">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" 
                                                class="text-yellow-600 hover:text-yellow-800 transition-colors duration-150"
                                                title="بدء">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="bg-gray-100 rounded-full p-4 mb-4">
                                    <i class="fas fa-clipboard-list text-gray-400 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد أوامر عمل</h3>
                                <p class="text-gray-500 mb-4">لم يتم إنشاء أي أوامر عمل بعد</p>
                                <a href="<?php echo e(route('maintenance.workorders.create')); ?>" 
                                   class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                                    <i class="fas fa-plus ml-2"></i>
                                    إنشاء أمر عمل أول
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if($workOrders->hasPages()): ?>
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            <?php echo e($workOrders->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/maintenance/workorders/index.blade.php ENDPATH**/ ?>