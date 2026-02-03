<?php $__env->startSection('title', 'تفاصيل الضمان'); ?>

<?php $__env->startSection('page-title', 'تفاصيل الضمان'); ?>

<?php $__env->startSection('content'); ?>
<!-- Page Header -->
<div class="mb-8">
    <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 rounded-2xl shadow-xl p-8 text-white">
        <div class="flex justify-between items-center">
            <div>
                <div class="flex items-center mb-3">
                    <div class="bg-white bg-opacity-20 rounded-xl p-3 ml-4">
                        <i class="fas fa-shield-alt text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white">تفاصيل الضمان</h1>
                        <p class="text-blue-100 mt-1"><?php echo e($policy->warranty_number); ?> - <?php echo e($policy->title); ?></p>
                    </div>
                </div>
                <div class="flex items-center space-x-4 mt-4">
                    <div class="bg-white bg-opacity-20 rounded-lg px-3 py-1">
                        <span class="text-sm text-blue-100">الحالة:</span>
                        <span class="text-sm font-semibold text-white"><?php echo e($policy->status_label); ?></span>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg px-3 py-1">
                        <span class="text-sm text-blue-100">النوع:</span>
                        <span class="text-sm font-semibold text-white"><?php echo e($policy->warranty_type_label); ?></span>
                    </div>
                </div>
            </div>
            <div class="flex space-x-reverse space-x-3">
                <a href="<?php echo e(route('warranties.policies.index')); ?>" class="inline-flex items-center px-6 py-3 bg-white bg-opacity-20 backdrop-blur-sm border border-white border-opacity-30 rounded-xl text-sm font-medium text-white hover:bg-opacity-30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-300">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة للضمانات
                </a>
                <a href="<?php echo e(route('warranties.policies.edit', $policy->id)); ?>" class="inline-flex items-center px-6 py-3 bg-white bg-opacity-20 backdrop-blur-sm border border-white border-opacity-30 rounded-xl text-sm font-medium text-white hover:bg-opacity-30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-300">
                    <i class="fas fa-edit ml-2"></i>
                    تعديل
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium mb-2">مبلغ التغطية</p>
                <p class="text-2xl font-bold text-white"><?php echo e(number_format($policy->coverage_amount, 2)); ?></p>
                <div class="mt-2 flex items-center text-xs text-blue-100">
                    <i class="fas fa-dollar-sign ml-1"></i>
                    <span>ريال سعودي</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-money-bill-wave text-white text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium mb-2">المتبقي</p>
                <p class="text-2xl font-bold text-white"><?php echo e(number_format($policy->coverage_amount - $stats['total_claimed_amount'], 2)); ?></p>
                <div class="mt-2 flex items-center text-xs text-green-100">
                    <i class="fas fa-piggy-bank ml-1"></i>
                    <span>ريال سعودي</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-piggy-bank text-white text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-amber-100 text-sm font-medium mb-2">الأيام المتبقية</p>
                <p class="text-2xl font-bold text-white"><?php echo e($stats['days_remaining']); ?></p>
                <div class="mt-2 flex items-center text-xs text-amber-100">
                    <i class="fas fa-calendar-alt ml-1"></i>
                    <span>يوم</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-calendar-alt text-white text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm font-medium mb-2">مجموع المطالبات</p>
                <p class="text-2xl font-bold text-white"><?php echo e($stats['total_claims']); ?></p>
                <div class="mt-2 flex items-center text-xs text-purple-100">
                    <i class="fas fa-clipboard-list ml-1"></i>
                    <span>مطالبة</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-clipboard-list text-white text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Warranty Details -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Basic Information -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <div class="bg-blue-500 rounded-lg p-2 ml-3">
                    <i class="fas fa-info-circle text-white"></i>
                </div>
                المعلومات الأساسية
            </h3>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">رقم الضمان</span>
                <span class="text-sm font-semibold text-gray-900"><?php echo e($policy->warranty_number); ?></span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">العنوان</span>
                <span class="text-sm font-semibold text-gray-900"><?php echo e($policy->title); ?></span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">نوع الضمان</span>
                <span class="text-sm font-semibold text-gray-900"><?php echo e($policy->warranty_type_label); ?></span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">الحالة</span>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                    <?php if($policy->status == 'active'): ?> bg-green-100 text-green-800
                    <?php elseif($policy->status == 'expired'): ?> bg-red-100 text-red-800
                    <?php else: ?> bg-gray-100 text-gray-800 <?php endif; ?>">
                    <?php echo e($policy->status_label); ?>

                </span>
            </div>
        </div>
    </div>

    <!-- Coverage Information -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <div class="bg-green-500 rounded-lg p-2 ml-3">
                    <i class="fas fa-shield-alt text-white"></i>
                </div>
                معلومات التغطية
            </h3>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">مبلغ التغطية</span>
                <span class="text-sm font-semibold text-gray-900"><?php echo e(number_format($policy->coverage_amount, 2)); ?> ريال</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">مبلغ الخصم</span>
                <span class="text-sm font-semibold text-gray-900"><?php echo e(number_format($policy->deductible_amount, 2)); ?> ريال</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">المبلغ المدفوع</span>
                <span class="text-sm font-semibold text-gray-900"><?php echo e(number_format($stats['total_claimed_amount'], 2)); ?> ريال</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">المتبقي</span>
                <span class="text-sm font-semibold text-gray-900"><?php echo e(number_format($policy->coverage_amount - $stats['total_claimed_amount'], 2)); ?> ريال</span>
            </div>
        </div>
    </div>
</div>

<!-- Period and Property Information -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Period Information -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <div class="bg-amber-500 rounded-lg p-2 ml-3">
                    <i class="fas fa-calendar text-white"></i>
                </div>
                فترة الضمان
            </h3>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">تاريخ البدء</span>
                <span class="text-sm font-semibold text-gray-900"><?php echo e($policy->start_date->format('Y-m-d')); ?></span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">تاريخ الانتهاء</span>
                <span class="text-sm font-semibold text-gray-900"><?php echo e($policy->end_date->format('Y-m-d')); ?></span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">المدة</span>
                <span class="text-sm font-semibold text-gray-900"><?php echo e($policy->duration_months); ?> شهر</span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">الأيام المتبقية</span>
                <span class="text-sm font-semibold text-gray-900"><?php echo e($stats['days_remaining']); ?> يوم</span>
            </div>
        </div>
    </div>

    <!-- Property Information -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <div class="bg-purple-500 rounded-lg p-2 ml-3">
                    <i class="fas fa-home text-white"></i>
                </div>
                معلومات العقار
            </h3>
        </div>
        <div class="p-6 space-y-4">
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">العقار</span>
                <span class="text-sm font-semibold text-gray-900"><?php echo e($policy->property->title ?? 'N/A'); ?></span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">مقدم الخدمة</span>
                <span class="text-sm font-semibold text-gray-900"><?php echo e($policy->serviceProvider->name ?? 'N/A'); ?></span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">الشخص المسؤول</span>
                <span class="text-sm font-semibold text-gray-900"><?php echo e($policy->contact_person ?? 'N/A'); ?></span>
            </div>
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                <span class="text-sm font-medium text-gray-600">رقم الهاتف</span>
                <span class="text-sm font-semibold text-gray-900"><?php echo e($policy->contact_phone ?? 'N/A'); ?></span>
            </div>
        </div>
    </div>
</div>

<!-- Description and Terms -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Description -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <div class="bg-indigo-500 rounded-lg p-2 ml-3">
                    <i class="fas fa-align-left text-white"></i>
                </div>
                الوصف
            </h3>
        </div>
        <div class="p-6">
            <p class="text-gray-700 leading-relaxed"><?php echo e($policy->description ?? 'لا يوجد وصف'); ?></p>
        </div>
    </div>

    <!-- Coverage Details -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <div class="bg-teal-500 rounded-lg p-2 ml-3">
                    <i class="fas fa-file-contract text-white"></i>
                </div>
                تفاصيل التغطية
            </h3>
        </div>
        <div class="p-6">
            <p class="text-gray-700 leading-relaxed"><?php echo e($policy->coverage_details ?? 'لا توجد تفاصيل'); ?></p>
        </div>
    </div>
</div>

<!-- Claims Section -->
<?php if($policy->claims->count() > 0): ?>
<div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <div class="bg-red-500 rounded-lg p-2 ml-3">
                <i class="fas fa-clipboard-list text-white"></i>
            </div>
            المطالبات
        </h3>
    </div>
    <div class="p-6">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم المطالبة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المبلغ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php $__currentLoopData = $policy->claims; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $claim): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo e($claim->claim_number); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e($claim->claim_date->format('Y-m-d')); ?></td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo e(number_format($claim->amount, 2)); ?> ريال</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                <?php if($claim->status == 'approved'): ?> bg-green-100 text-green-800
                                <?php elseif($claim->status == 'rejected'): ?> bg-red-100 text-red-800
                                <?php elseif($claim->status == 'pending'): ?> bg-yellow-100 text-yellow-800
                                <?php else: ?> bg-gray-100 text-gray-800 <?php endif; ?>">
                                <?php echo e(ucfirst($claim->status)); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="#" class="text-indigo-600 hover:text-indigo-900">عرض</a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/warranties/policies/show.blade.php ENDPATH**/ ?>