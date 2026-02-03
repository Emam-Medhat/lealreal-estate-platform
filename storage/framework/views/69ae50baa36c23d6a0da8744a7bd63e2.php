

<?php $__env->startSection('title', 'عرض تقرير السوق'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-indigo-700 rounded-lg shadow-lg p-6 mb-6 text-white">
            <div class="flex justify-between items-center">
                <div class="flex items-center">
                    <a href="<?php echo e(route('reports.market.index')); ?>" class="text-white hover:text-purple-200 mr-4">
                        <i class="fas fa-arrow-right text-xl"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold mb-2"><?php echo e($report->title); ?></h1>
                        <p class="text-purple-100"><?php echo e($report->description); ?></p>
                    </div>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                        <?php echo e($report->status == 'completed' ? 'مكتمل' : 'قيد المعالجة'); ?>

                    </span>
                    <a href="<?php echo e(route('reports.market.data')); ?>" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg transition-colors">
                        <i class="fas fa-download ml-2"></i>
                        تحميل
                    </a>
                </div>
            </div>
        </div>

        <!-- Report Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Market Overview -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">نظرة عامة على السوق</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-600">متوسط سعر العقار</div>
                            <div class="text-2xl font-bold text-gray-800">$<?php echo e(number_format($report->average_property_price ?? 250000, 0)); ?></div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-600">السعر المتوسط</div>
                            <div class="text-2xl font-bold text-gray-800">$<?php echo e(number_format($report->median_property_price ?? 225000, 0)); ?></div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-600">إجمالي العقارات</div>
                            <div class="text-2xl font-bold text-gray-800"><?php echo e($report->total_listings ?? 150); ?></div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-600">إجمالي المبيعات</div>
                            <div class="text-2xl font-bold text-gray-800"><?php echo e($report->total_sales ?? 75); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Price Trends -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">اتجاهات الأسعار</h3>
                    <canvas id="priceTrendsChart" width="400" height="200"></canvas>
                </div>

                <!-- Market Segments -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">شرائح السوق</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="font-medium">فلل</span>
                            <div class="text-right">
                                <div class="font-semibold">$<?php echo e(number_format(300000, 0)); ?></div>
                                <div class="text-sm text-gray-500">60 عقار</div>
                            </div>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="font-medium">شقق</span>
                            <div class="text-right">
                                <div class="font-semibold">$<?php echo e(number_format(200000, 0)); ?></div>
                                <div class="text-sm text-gray-500">45 عقار</div>
                            </div>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="font-medium">كوندو</span>
                            <div class="text-right">
                                <div class="font-semibold">$<?php echo e(number_format(250000, 0)); ?></div>
                                <div class="text-sm text-gray-500">30 عقار</div>
                            </div>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                            <span class="font-medium">تجاري</span>
                            <div class="text-right">
                                <div class="font-semibold">$<?php echo e(number_format(500000, 0)); ?></div>
                                <div class="text-sm text-gray-500">15 عقار</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Report Info -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">معلومات التقرير</h3>
                    <div class="space-y-3">
                        <div>
                            <div class="text-sm text-gray-600">منطقة السوق</div>
                            <div class="font-medium"><?php echo e($report->market_area ?? 'الرياض'); ?></div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">الفترة</div>
                            <div class="font-medium">
                                <?php echo e($report->period_start ? $report->period_start->format('Y-m-d') : 'N/A'); ?> - <?php echo e($report->period_end ? $report->period_end->format('Y-m-d') : 'N/A'); ?>

                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">تاريخ الإنشاء</div>
                            <div class="font-medium"><?php echo e($report->created_at ? $report->created_at->format('Y-m-d H:i') : 'N/A'); ?></div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-600">التنسيق</div>
                            <div class="font-medium"><?php echo e(strtoupper($report->format ?? 'PDF')); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Market Indicators -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">مؤشرات السوق</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">حالة السوق</span>
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium">متوازن</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">اتجاه السعر</span>
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs font-medium">مستقر</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">حالة المخزون</span>
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-medium">طبيعي</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">طلب المشترين</span>
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs font-medium">متوسط</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">الإجراءات</h3>
                    <div class="space-y-3">
                        <a href="<?php echo e(route('reports.market.data')); ?>" class="w-full block text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-download ml-2"></i>
                            تحميل التقرير
                        </a>
                        <button class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-share ml-2"></i>
                            مشاركة التقرير
                        </button>
                        <button class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-print ml-2"></i>
                            طباعة التقرير
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize Price Trends Chart
const ctx = document.getElementById('priceTrendsChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
        datasets: [{
            label: 'متوسط السعر',
            data: [240000, 245000, 250000, 248000, 252000, 250000],
            borderColor: 'rgb(147, 51, 234)',
            backgroundColor: 'rgba(147, 51, 234, 0.1)',
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
                beginAtZero: false,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/reports/market/show.blade.php ENDPATH**/ ?>