<?php $__env->startSection('content'); ?>
    <div class="p-6 bg-gradient-to-br from-slate-50 to-blue-50/30 min-h-screen rtl" dir="rtl">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">📊 لوحة تحكم الذكاء الاصطناعي</h1>
                <p class="text-slate-600 mt-2">مراقبة الأداء والتكاليف والاستهلاك في الوقت الفعلي</p>
            </div>
            <div class="flex gap-4">
                <select
                    class="bg-white/80 backdrop-blur-sm border border-slate-200 rounded-lg px-4 py-2 text-slate-700 shadow-sm focus:ring-2 focus:ring-blue-500/50 focus:border-blue-400 outline-none transition-all">
                    <option value="<?php echo e(now()->month); ?>"><?php echo e(now()->format('F Y')); ?></option>
                </select>
                <button onclick="window.location.reload()"
                    class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-6 py-2 rounded-lg shadow-lg shadow-blue-500/25 transition-all duration-300 flex items-center gap-2 hover:scale-[1.02]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    تحديث
                </button>
            </div>
        </div>

        <?php if(isset($error)): ?>
            <div class="bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-500 text-red-700 p-4 mb-8 rounded-r-lg shadow-md shadow-red-500/10" role="alert">
                <p class="font-bold text-red-800">تنبيه</p>
                <p class="text-red-600"><?php echo e($error); ?></p>
            </div>
        <?php endif; ?>

        <!-- Top Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Budget Card -->
            <div class="bg-white/90 backdrop-blur-sm p-6 rounded-2xl shadow-lg shadow-slate-200/50 border border-slate-100/50 hover:shadow-xl hover:shadow-slate-300/30 transition-all duration-300">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-sm">
                        <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <?php $percent = $budget > 0 ? ($totalSpent / $budget) * 100 : 0; ?>
                    <span class="text-sm font-medium <?php echo e($percent > 80 ? 'text-red-600 bg-red-50 px-2 py-1 rounded-full' : 'text-green-600 bg-green-50 px-2 py-1 rounded-full'); ?>">
                        <?php echo e(round($percent, 1)); ?>% من الميزانية
                    </span>
                </div>
                <h3 class="text-slate-500 text-sm font-medium">إجمالي الإنفاق</h3>
                <div class="flex items-baseline gap-2">
                    <span class="text-2xl font-bold">$<?php echo e(number_format($totalSpent, 2)); ?></span>
                    <span class="text-gray-400 text-sm">/ $<?php echo e(number_format($budget, 0)); ?></span>
                </div>
                <div class="mt-4 w-full bg-gradient-to-r from-slate-100 to-slate-200 rounded-full h-2">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all duration-1000 shadow-sm"
                        style="width: <?php echo e(min(100, $percent)); ?>%"></div>
                </div>
            </div>

            <!-- Predictions Card -->
            <div class="bg-white/90 backdrop-blur-sm p-6 rounded-2xl shadow-lg shadow-slate-200/50 border border-slate-100/50 hover:shadow-xl hover:shadow-slate-300/30 transition-all duration-300">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-sm">
                        <svg class="w-6 h-6 text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-slate-500 text-sm font-medium">توقعات الأسعار</h3>
                <span class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-purple-700 bg-clip-text text-transparent"><?php echo e(number_format($stats['total_predictions'] ?? 0)); ?></span>
                <p class="text-slate-400 text-xs mt-2">دقة التوقع: <?php echo e(round($stats['avg_prediction_accuracy'] ?? 0, 1)); ?>%
                </p>
            </div>

            <!-- Image Analysis Card -->
            <div class="bg-white/90 backdrop-blur-sm p-6 rounded-2xl shadow-lg shadow-slate-200/50 border border-slate-100/50 hover:shadow-xl hover:shadow-slate-300/30 transition-all duration-300">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-sm">
                        <svg class="w-6 h-6 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-slate-500 text-sm font-medium">تحليل الصور</h3>
                <span class="text-2xl font-bold bg-gradient-to-r from-green-600 to-green-700 bg-clip-text text-transparent"><?php echo e(number_format($stats['total_image_analyses'] ?? 0)); ?></span>
                <p class="text-slate-400 text-xs mt-2">عقارات تمت معالجتها</p>
            </div>

            <!-- Searches Card -->
            <div class="bg-white/90 backdrop-blur-sm p-6 rounded-2xl shadow-lg shadow-slate-200/50 border border-slate-100/50 hover:shadow-xl hover:shadow-slate-300/30 transition-all duration-300">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-3 bg-gradient-to-br from-yellow-50 to-amber-100 rounded-xl shadow-sm">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-slate-500 text-sm font-medium">عمليات البحث الذكية</h3>
                <span class="text-2xl font-bold bg-gradient-to-r from-amber-600 to-amber-700 bg-clip-text text-transparent"><?php echo e(number_format($stats['total_searches'] ?? 0)); ?></span>
                <p class="text-slate-400 text-xs mt-2">استفسارات بصوت طبيعي</p>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Usage Chart -->
            <div class="lg:col-span-2 bg-white/90 backdrop-blur-sm p-6 rounded-2xl shadow-lg shadow-slate-200/50 border border-slate-100/50 hover:shadow-xl hover:shadow-slate-300/30 transition-all duration-300">
                <h3 class="text-lg font-bold bg-gradient-to-r from-slate-700 to-slate-800 bg-clip-text text-transparent mb-6">📉 اتجاه الاستهلاك (آخر 14 يوم)</h3>
                <div class="relative h-64 w-full">
                    <canvas id="usageChart"></canvas>
                </div>
            </div>

            <!-- Feature Breakdown -->
            <div class="bg-white/90 backdrop-blur-sm p-6 rounded-2xl shadow-lg shadow-slate-200/50 border border-slate-100/50 hover:shadow-xl hover:shadow-slate-300/30 transition-all duration-300">
                <h3 class="text-lg font-bold bg-gradient-to-r from-slate-700 to-slate-800 bg-clip-text text-transparent mb-6">🧱 توزيع التكاليف</h3>
                <div class="space-y-6">
                    <?php $__currentLoopData = $costs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="font-medium text-slate-700"><?php echo e(ucfirst(str_replace('_', ' ', $item->feature))); ?></span>
                                <span class="text-slate-500">$<?php echo e(number_format($item->total_cost, 2)); ?></span>
                            </div>
                            <?php $itemPercent = $totalSpent > 0 ? ($item->total_cost / $totalSpent) * 100 : 0; ?>
                            <div class="w-full bg-gradient-to-r from-slate-50 to-slate-100 rounded-full h-1.5">
                                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-1.5 rounded-full shadow-sm" style="width: <?php echo e($itemPercent); ?>%"></div>
                            </div>
                            <div class="flex justify-between mt-1 text-[10px] text-gray-400">
                                <span class="text-slate-400"><?php echo e(number_format($item->calls)); ?> طلب</span>
                                <span class="text-slate-400"><?php echo e(number_format($item->total_tokens)); ?> Token</span>
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
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.15)',
                            fill: true,
                            tension: 0.4,
                            yAxisID: 'y',
                            borderWidth: 3,
                            pointBackgroundColor: '#3b82f6',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'التكلفة ($)',
                            data: costs,
                            borderColor: '#8b5cf6',
                            backgroundColor: 'rgba(139, 92, 246, 0.1)',
                            borderDash: [5, 5],
                            fill: false,
                            tension: 0.4,
                            yAxisID: 'y1',
                            borderWidth: 3,
                            pointBackgroundColor: '#8b5cf6',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
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
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap');
        
        .rtl {
            direction: rtl;
        }

        body {
            font-family: 'Outfit', 'Inter', sans-serif;
        }

        /* Custom animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.6s ease-out;
        }

        .animate-pulse-slow {
            animation: pulse 3s ease-in-out infinite;
        }

        /* Glass morphism effect */
        .glass-morphism {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        /* Enhanced hover effects */
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-4px);
        }

        /* Gradient text animation */
        .gradient-text {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            background-size: 200% 200%;
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: gradient-shift 3s ease infinite;
        }

        @keyframes gradient-shift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        }
    </style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\larvel state big\resources\views/ai/dashboard.blade.php ENDPATH**/ ?>