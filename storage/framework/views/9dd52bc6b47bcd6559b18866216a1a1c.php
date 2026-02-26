<?php $__env->startSection('content'); ?>
    <div class="p-6 bg-gray-50 min-h-screen rtl" dir="rtl">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">📊 لوحة تحكم الذكاء الاصطناعي</h1>
                <p class="text-gray-600 mt-2">مراقبة الأداء والتكاليف والاستهلاك في الوقت الفعلي</p>
            </div>
            <div class="flex gap-4">
                <select
                    class="bg-white border border-gray-300 rounded-lg px-4 py-2 text-gray-700 shadow-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="<?php echo e(now()->month); ?>"><?php echo e(now()->format('F Y')); ?></option>
                </select>
                <button onclick="window.location.reload()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-md transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    تحديث
                </button>
            </div>
        </div>

        <?php if(isset($error)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-8 rounded-r-lg shadow-sm" role="alert">
                <p class="font-bold">تنبيه</p>
                <p><?php echo e($error); ?></p>
            </div>
        <?php endif; ?>

        <!-- Top Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Budget Card -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-blue-50 rounded-xl">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <?php $percent = $budget > 0 ? ($totalSpent / $budget) * 100 : 0; ?>
                    <span class="text-sm font-medium <?php echo e($percent > 80 ? 'text-red-600' : 'text-green-600'); ?>">
                        <?php echo e(round($percent, 1)); ?>% من الميزانية
                    </span>
                </div>
                <h3 class="text-gray-500 text-sm font-medium">إجمالي الإنفاق</h3>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-bold">$<?php echo e(number_format($totalSpent, 2)); ?></span>
                    <span class="text-gray-400 text-sm">/ $<?php echo e(number_format($budget, 0)); ?></span>
                </div>
                <div class="mt-4 w-full bg-gray-100 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-1000"
                        style="width: <?php echo e(min(100, $percent)); ?>%"></div>
                </div>
            </div>

            <!-- Predictions Card -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-purple-50 rounded-xl">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-gray-500 text-sm font-medium">توقعات الأسعار</h3>
                <span class="text-2xl font-bold"><?php echo e(number_format($stats['total_predictions'] ?? 0)); ?></span>
                <p class="text-gray-400 text-xs mt-2">دقة التوقع: <?php echo e(round($stats['avg_prediction_accuracy'] ?? 0, 1)); ?>%
                </p>
            </div>

            <!-- Image Analysis Card -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-green-50 rounded-xl">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-gray-500 text-sm font-medium">تحليل الصور</h3>
                <span class="text-2xl font-bold"><?php echo e(number_format($stats['total_image_analyses'] ?? 0)); ?></span>
                <p class="text-gray-400 text-xs mt-2">عقارات تمت معالجتها</p>
            </div>

            <!-- Searches Card -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-yellow-50 rounded-xl">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-gray-500 text-sm font-medium">عمليات البحث الذكية</h3>
                <span class="text-2xl font-bold"><?php echo e(number_format($stats['total_searches'] ?? 0)); ?></span>
                <p class="text-gray-400 text-xs mt-2">استفسارات بصوت طبيعي</p>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Usage Chart -->
            <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-6">📉 اتجاه الاستهلاك (آخر 14 يوم)</h3>
                <div class="relative h-64 w-full">
                    <canvas id="usageChart"></canvas>
                </div>
            </div>

            <!-- Feature Breakdown -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-800 mb-6">🧱 توزيع التكاليف</h3>
                <div class="space-y-6">
                    <?php $__currentLoopData = $costs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span
                                    class="font-medium text-gray-700"><?php echo e(ucfirst(str_replace('_', ' ', $item->feature))); ?></span>
                                <span class="text-gray-500">$<?php echo e(number_format($item->total_cost, 2)); ?></span>
                            </div>
                            <?php $itemPercent = $totalSpent > 0 ? ($item->total_cost / $totalSpent) * 100 : 0; ?>
                            <div class="w-full bg-gray-50 rounded-full h-1.5">
                                <div class="bg-blue-500 h-1.5 rounded-full" style="width: <?php echo e($itemPercent); ?>%"></div>
                            </div>
                            <div class="flex justify-between mt-1 text-[10px] text-gray-400">
                                <span><?php echo e(number_format($item->calls)); ?> طلب</span>
                                <span><?php echo e(number_format($item->total_tokens)); ?> Token</span>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('usageChart').getContext('2d');

            const labels = <?php echo json_encode($trends->pluck('date'), 15, 512) ?>;
            const counts = <?php echo json_encode($trends->pluck('count'), 15, 512) ?>;
            const costs = <?php echo json_encode($trends->pluck('cost'), 15, 512) ?>;

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'عدد الطلبات',
                            data: counts,
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.1)',
                            fill: true,
                            tension: 0.4,
                            yAxisID: 'y'
                        },
                        {
                            label: 'التكلفة ($)',
                            data: costs,
                            borderColor: '#9333ea',
                            borderDash: [5, 5],
                            fill: false,
                            tension: 0.4,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            rtl: true,
                            labels: { font: { family: 'inherit' } }
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: { display: true, text: 'الطلبات' }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: { drawOnChartArea: false },
                            title: { display: true, text: 'التكلفة ($)' }
                        }
                    }
                }
            });
        });
    </script>

    <style>
        @font-face {
            font-family: 'Outfit';
            src: url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap');
        }

        .rtl {
            direction: rtl;
        }

        body {
            font-family: 'Outfit', 'Inter', sans-serif;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/ai/dashboard.blade.php ENDPATH**/ ?>