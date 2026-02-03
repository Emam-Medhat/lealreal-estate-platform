<?php $__env->startSection('title', 'فرق الصيانة'); ?>

<?php $__env->startSection('content'); ?>
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">فرق الصيانة</h1>
            <p class="text-gray-600 mt-1">إدارة فرق الصيانة والموظفين</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="<?php echo e(route('maintenance.teams.create')); ?>" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium flex items-center space-x-2 space-x-reverse transition-colors duration-200">
                <i class="fas fa-plus"></i>
                <span>إنشاء فريق</span>
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">إجمالي الفرق</p>
                <p class="text-2xl font-bold text-gray-900"><?php echo e($teams->count()); ?></p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-users text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">الفرق النشطة</p>
                <p class="text-2xl font-bold text-green-600"><?php echo e($teams->where('is_active', true)->count()); ?></p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">الفرق غير النشطة</p>
                <p class="text-2xl font-bold text-red-600"><?php echo e($teams->where('is_active', false)->count()); ?></p>
            </div>
            <div class="bg-red-100 rounded-full p-3">
                <i class="fas fa-times-circle text-red-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">إجمالي الأعضاء</p>
                <p class="text-2xl font-bold text-purple-600"><?php echo e($teams->sum(function($team) { return $team->members ? $team->members->count() : 0; })); ?></p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <i class="fas fa-user-friends text-purple-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filters -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <form method="GET" action="<?php echo e(route('maintenance.teams.index')); ?>" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[300px]">
            <div class="relative">
                <input type="text" name="search" 
                       value="<?php echo e(request('search')); ?>"
                       placeholder="البحث عن فريق..." 
                       class="w-full pr-10 pl-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
        </div>
        
        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="">جميع الحالات</option>
            <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>نشط</option>
            <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>>غير نشط</option>
        </select>
        
        <button type="submit" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors duration-200">
            <i class="fas fa-filter ml-2"></i>
            فلترة
        </button>
        
        <a href="<?php echo e(route('maintenance.teams.index')); ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg transition-colors duration-200">
            <i class="fas fa-redo ml-2"></i>
            إعادة تعيين
        </a>
    </form>
</div>

<!-- Teams Table -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاسم</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">القائد</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الأعضاء</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التخصصات</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عبء العمل</th>
                    <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php $__empty_1 = true; $__currentLoopData = $teams; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $team): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                <?php echo e($team->name); ?>

                            </div>
                            <?php if($team->description): ?>
                                <div class="text-sm text-gray-500"><?php echo e(Str::limit($team->description, 50)); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <?php if($team->leader): ?>
                                    <?php echo e($team->leader->name); ?>

                                <?php else: ?>
                                    <span class="text-gray-400">غير محدد</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <?php if($team->members): ?>
                                    <?php echo e($team->members->count()); ?> عضو
                                <?php else: ?>
                                    <span class="text-gray-400">0 عضو</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <?php if($team->specializations): ?>
                                    <?php echo e(implode(', ', array_slice($team->specializations, 0, 2))); ?>

                                    <?php if(count($team->specializations) > 2): ?>
                                        <span class="text-gray-500">...</span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="text-gray-400">غير محدد</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                <?php if($team->is_active): ?>
                                    bg-green-100 text-green-800
                                <?php else: ?>
                                    bg-red-100 text-red-800
                                <?php endif; ?>">
                                <?php if($team->is_active): ?>
                                    نشط
                                <?php else: ?>
                                    غير نشط
                                <?php endif; ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <?php echo e($team->workload ?? 0); ?> أمر عمل
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <a href="<?php echo e(route('maintenance.teams.show', $team)); ?>" 
                                   class="text-blue-600 hover:text-blue-800 transition-colors duration-150"
                                   title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('maintenance.teams.edit', $team)); ?>" 
                                   class="text-green-600 hover:text-green-800 transition-colors duration-150"
                                   title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?php echo e(route('maintenance.teams.workload', $team)); ?>" 
                                   class="text-purple-600 hover:text-purple-800 transition-colors duration-150"
                                   title="عبء العمل">
                                    <i class="fas fa-chart-bar"></i>
                                </a>
                                <form method="POST" action="<?php echo e(route('maintenance.teams.toggle-status', $team)); ?>" 
                                      class="inline"
                                      onsubmit="return confirm('هل أنت متأكد من تغيير حالة الفريق؟');">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" 
                                            class="text-yellow-600 hover:text-yellow-800 transition-colors duration-150"
                                            title="<?php echo e($team->is_active ? 'إلغاء تفعيل' : 'تفعيل'); ?>">
                                        <i class="fas fa-power-off"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="bg-gray-100 rounded-full p-4 mb-4">
                                    <i class="fas fa-users text-gray-400 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد فرق</h3>
                                <p class="text-gray-500 mb-4">لم يتم إنشاء أي فرق صيانة بعد</p>
                                <a href="<?php echo e(route('maintenance.teams.create')); ?>" 
                                   class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                                    <i class="fas fa-plus ml-2"></i>
                                    إنشاء فريق أول
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if($teams->hasPages()): ?>
        <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
            <?php echo e($teams->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/maintenance/teams/index.blade.php ENDPATH**/ ?>