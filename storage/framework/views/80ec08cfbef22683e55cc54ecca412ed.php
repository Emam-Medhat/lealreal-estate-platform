<?php $__env->startSection('title', $customReport->name); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-8 border border-blue-200 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2 flex items-center">
                    <i class="fas fa-chart-pie text-blue-600 ml-4"></i>
                    <?php echo e($customReport->name); ?>

                </h1>
                <p class="text-gray-600 text-lg"><?php echo e($customReport->description); ?></p>
                <div class="mt-4 flex space-x-4 space-x-reverse">
                    <span class="bg-blue-100 text-blue-800 text-sm font-medium px-3 py-1 rounded-full">
                        <i class="fas fa-database ml-1"></i>
                        <?php echo e(implode(', ', $customReport->data_sources)); ?>

                    </span>
                    <?php if($customReport->is_public): ?>
                        <span class="bg-green-100 text-green-800 text-sm font-medium px-3 py-1 rounded-full">
                            <i class="fas fa-globe ml-1"></i>
                            عام
                        </span>
                    <?php endif; ?>
                    <?php if($customReport->is_template): ?>
                        <span class="bg-purple-100 text-purple-800 text-sm font-medium px-3 py-1 rounded-full">
                            <i class="fas fa-layer-group ml-1"></i>
                            قالب
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex space-x-3 space-x-reverse">
                <a href="<?php echo e(route('reports.custom.index')); ?>"
                   class="bg-white hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-xl font-medium transition-all duration-300 shadow hover:shadow-md border border-gray-300 flex items-center">
                    <i class="fas fa-arrow-left ml-2"></i>
                    رجوع
                </a>
                <a href="<?php echo e(route('reports.custom.edit', $customReport)); ?>"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl font-medium transition-all duration-300 shadow hover:shadow-md flex items-center">
                    <i class="fas fa-edit ml-2"></i>
                    تعديل
                </a>
                <form action="<?php echo e(route('reports.custom.run', $customReport)); ?>" method="POST" class="inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl font-medium transition-all duration-300 shadow hover:shadow-md flex items-center">
                        <i class="fas fa-play ml-2"></i>
                        تشغيل التقرير
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Report Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Data Visualization -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Data Table -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-table text-blue-600 ml-3"></i>
                    البيانات
                </h3>

                <?php if(isset($report) && $report->status === 'completed'): ?>
                    <div class="overflow-x-auto">
                        <!-- Report data table will be rendered here -->
                        <div class="text-center p-8 text-gray-500">
                            سيتم عرض البيانات هنا قريباً
                        </div>
                    </div>
                <?php elseif(isset($report) && $report->status === 'generating'): ?>
                    <div class="text-center p-12">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                        <p class="text-gray-600">جاري معالجة البيانات...</p>
                    </div>
                <?php else: ?>
                    <div class="text-center p-12 bg-gray-50 rounded-xl">
                        <i class="fas fa-chart-bar text-4xl text-gray-300 mb-4"></i>
                        <p class="text-gray-600">لم يتم تشغيل التقرير بعد</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Visualizations -->
            <?php if(isset($customReport->visualization_config) && !empty($customReport->visualization_config)): ?>
                <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <i class="fas fa-chart-pie text-purple-600 ml-3"></i>
                        الرسوم البيانية
                    </h3>
                    <div class="h-96" id="chartContainer">
                        <!-- Charts will be rendered here -->
                        <div class="text-center p-8 text-gray-500 h-full flex items-center justify-center">
                            منطقة الرسم البياني
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar Info -->
        <div class="lg:col-span-1 space-y-8">
            <!-- Report Details -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-info-circle text-gray-600 ml-3"></i>
                    تفاصيل التقرير
                </h3>

                <div class="space-y-4">
                    <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                        <span class="text-gray-600">تاريخ الإنشاء</span>
                        <span class="font-medium text-gray-900"><?php echo e($customReport->created_at->format('Y-m-d')); ?></span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                        <span class="text-gray-600">آخر تحديث</span>
                        <span class="font-medium text-gray-900"><?php echo e($customReport->updated_at->format('Y-m-d')); ?></span>
                    </div>
                    <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                        <span class="text-gray-600">المنشئ</span>
                        <span class="font-medium text-gray-900"><?php echo e($customReport->creator->name ?? 'غير معروف'); ?></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">مرات الاستخدام</span>
                        <span class="font-medium text-gray-900"><?php echo e($customReport->usage_count ?? 0); ?></span>
                    </div>
                </div>
            </div>

            <!-- Configuration Summary -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-cogs text-gray-600 ml-3"></i>
                    إعدادات الاستعلام
                </h3>

                <div class="space-y-3">
                    <?php $__currentLoopData = $customReport->query_config; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if(is_string($value) || is_numeric($value)): ?>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-600"><?php echo e(ucfirst($key)); ?></span>
                                <span class="font-medium text-gray-900"><?php echo e($value); ?></span>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /app/resources/views/reports/custom/show.blade.php ENDPATH**/ ?>