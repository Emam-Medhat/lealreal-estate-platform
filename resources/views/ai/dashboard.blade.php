@extends('admin.layouts.admin')

@section('title', 'لوحة تحكم الذكاء الاصطناعي')
@section('page-title', 'مراقبة الذكاء الاصطناعي')

@section('content')
<style>
    :root {
        --blue: #0D6EFD;
        --blue-g: linear-gradient(135deg, #0D6EFD, #0a58ca);
        --purple: #6F42C1;
        --purple-g: linear-gradient(135deg, #6F42C1, #59359a);
        --green: #198754;
        --green-g: linear-gradient(135deg, #198754, #146c43);
        --orange: #FD7E14;
        --orange-g: linear-gradient(135deg, #FD7E14, #dc6a0c);
        --teal: #0DCAF0;
        --red: #DC3545;
        --yellow: #FFC107;
        --sc-blue: linear-gradient(135deg, #0D6EFD 0%, #0a58ca 100%);
        --sc-purple: linear-gradient(135deg, #6F42C1 0%, #59359a 100%);
        --sc-green: linear-gradient(135deg, #198754 0%, #146c43 100%);
        --sc-orange: linear-gradient(135deg, #FD7E14 0%, #dc6a0c 100%);
        --bg: #F1F3F8;
        --bg2: #EAECF3;
        --card: #FFFFFF;
        --sh-sm: 0 1px 4px rgba(0, 0, 0, .06);
        --sh-md: 0 4px 16px rgba(0, 0, 0, .08);
        --sh-blue: 0 6px 24px rgba(13, 110, 253, .3);
        --sh-purple: 0 6px 24px rgba(111, 66, 193, .25);
        --sh-green: 0 6px 24px rgba(25, 135, 84, .25);
        --r1: 10px;
        --r2: 14px;
        --r3: 18px;
        --tr: all .25s cubic-bezier(.4, 0, .2, 1);
        --tr-sp: all .35s cubic-bezier(.34, 1.3, .64, 1);
    }

    .premium-card {
        background: var(--card);
        border: 1px solid #E5E7EB;
        border-radius: var(--r3);
        transition: var(--tr);
        box-shadow: var(--sh-sm);
    }
    .premium-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--sh-md);
    }

    .sc {
        border-radius: var(--r3);
        padding: 22px 20px;
        position: relative;
        overflow: hidden;
        transition: var(--tr-sp);
        color: white;
    }
    .sc:hover { transform: translateY(-6px) scale(1.015); }
    .sc-blue { background: var(--sc-blue); box-shadow: var(--sh-blue); }
    .sc-purple { background: var(--sc-purple); box-shadow: var(--sh-purple); }
    .sc-green { background: var(--sc-green); box-shadow: var(--sh-green); }
    .sc-orange { background: var(--sc-orange); box-shadow: var(--sh-orange); }

    .sc-head { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 14px; position: relative; z-index: 1; }
    .sc-label { font-size: 12px; font-weight: 700; opacity: .88; }
    .sc-ic { width: 42px; height: 42px; border-radius: var(--r1); background: rgba(255, 255, 255, .18); display: flex; align-items: center; justify-content: center; font-size: 17px; }
    .sc-val { font-size: 32px; font-weight: 800; line-height: 1; margin-bottom: 10px; position: relative; z-index: 1; }
    .sc-footer { display: flex; align-items: center; gap: 8px; position: relative; z-index: 1; }
    .sc-delta { display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 20px; font-size: 11px; font-weight: 700; background: rgba(255, 255, 255, .18); }

    .glass-header {
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.95), rgba(147, 51, 234, 0.95));
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .progress-custom {
        background: rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        height: 8px;
        overflow: hidden;
    }

    .progress-bar-custom {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 20px;
        transition: width 1s ease;
    }
    
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
    }
    .status-active { background: #f0fdf4; color: #16a34a; border: 1px solid #dcfce7; }
    .status-inactive { background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; }

    .mod-btn {
        padding: 10px 16px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .mod-btn-primary { background: var(--sc-blue); color: white !important; }
    .mod-btn-secondary { background: #f8fafc; color: #475569 !important; border: 1px solid #e2e8f0; }
    .mod-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
</style>

<div class="content-wrapper">
    <div class="w-full px-4 md:px-6 lg:px-8">
        
        <!-- Animated Header -->
        <div class="relative overflow-hidden glass-header rounded-3xl shadow-2xl p-8 mb-10 transform hover:scale-[1.01] transition-all duration-500">
            <div class="absolute inset-0 bg-gradient-to-r from-white/10 to-transparent pointer-events-none"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between flex-wrap gap-6 text-right">
                    <div class="space-y-3">
                        <div class="flex items-center space-x-reverse space-x-3">
                            <div class="bg-white/20 backdrop-blur-md p-4 rounded-2xl shadow-inner">
                                <i class="fas fa-robot text-white text-3xl"></i>
                            </div>
                            <div>
                                <h1 class="text-4xl font-black text-white tracking-tight">لوحة تحكم الذكاء الاصطناعي</h1>
                                <p class="text-indigo-100 text-lg font-medium opacity-90">مراقبة الأداء والتكاليف والاستهلاك في الوقت الفعلي</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <select class="bg-white/10 backdrop-blur-xl text-white px-6 py-3 rounded-2xl border border-white/20 hover:bg-white/20 transition-all duration-300 border-white/20 hover:border-white/40 hover:shadow-2xl">
                            <option value="{{ now()->month }}">{{ now()->format('F Y') }}</option>
                        </select>
                        <button onclick="window.location.reload()" class="group bg-white text-indigo-700 px-8 py-4 rounded-2xl hover:bg-indigo-50 transition-all duration-300 shadow-xl hover:shadow-2xl hover:scale-105 flex items-center">
                            <i class="fas fa-sync-alt ml-3 group-hover:rotate-180 transition-transform duration-500"></i>
                            <span class="font-bold text-lg">تحديث</span>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Decorative elements -->
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-3xl opacity-50"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl opacity-50"></div>
        </div>

    @if(isset($error))
        <div class="alert alert-danger" role="alert">
            <strong>تنبيه:</strong> {{ $error }}
        </div>
    @endif

    <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10 text-right">
            <div class="sc sc-blue anim a1">
                <div class="sc-head">
                    <div class="sc-label">إجمالي الإنفاق</div>
                    <div class="sc-ic"><i class="fas fa-dollar-sign fa-lg"></i></div>
                </div>
                @php $percent = $budget > 0 ? ($totalSpent / $budget) * 100 : 0; @endphp
                <div class="sc-val">${{ number_format($totalSpent, 2) }}</div>
                <div class="sc-footer">
                    <span class="sc-delta">{{ $percent > 80 ? '⚠️' : '✅' }} {{ round($percent, 1) }}%</span>
                    <div class="progress-custom flex-fill">
                        <div class="progress-bar-custom" style="width: {{ min(100, $percent) }}%"></div>
                    </div>
                </div>
            </div>

            <div class="sc sc-purple anim a2">
                <div class="sc-head">
                    <div class="sc-label">توقعات الأسعار</div>
                    <div class="sc-ic"><i class="fas fa-chart-line fa-lg"></i></div>
                </div>
                <div class="sc-val">{{ number_format($stats['total_predictions'] ?? 0) }}</div>
                <div class="sc-footer">
                    <span class="sc-delta"><i class="fas fa-bullseye"></i> {{ round($stats['avg_prediction_accuracy'] ?? 0, 1) }}% دقة</span>
                </div>
            </div>

            <div class="sc sc-green anim a3">
                <div class="sc-head">
                    <div class="sc-label">تحليل الصور</div>
                    <div class="sc-ic"><i class="fas fa-image fa-lg"></i></div>
                </div>
                <div class="sc-val">{{ number_format($stats['total_image_analyses'] ?? 0) }}</div>
                <div class="sc-footer">
                    <span class="sc-delta"><i class="fas fa-home"></i> عقارات</span>
                </div>
            </div>

            <div class="sc sc-orange anim a4">
                <div class="sc-head">
                    <div class="sc-label">عمليات البحث</div>
                    <div class="sc-ic"><i class="fas fa-search fa-lg"></i></div>
                </div>
                <div class="sc-val">{{ number_format($stats['total_searches'] ?? 0) }}</div>
                <div class="sc-footer">
                    <span class="sc-delta"><i class="fas fa-microphone"></i> صوتي</span>
                </div>
            </div>
        </div>

    <!-- AI Modules Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-10 text-right" id="aiModulesGrid">
            <!-- Budget Management -->
            <div class="premium-card p-8 flex flex-col h-full">
                <div class="flex items-start justify-between mb-6">
                    <div class="bg-indigo-50 p-4 rounded-2xl">
                        <i class="fas fa-dollar-sign text-3xl text-indigo-600"></i>
                    </div>
                    <span class="status-badge status-active">نشط</span>
                </div>
                
                <h3 class="text-xl font-black text-gray-900 mb-2">إدارة الميزانية</h3>
                <p class="text-gray-500 text-sm mb-6 leading-relaxed flex-grow">مراقبة الاستهلاك والتحكم في ميزانية الذكاء الاصطناعي</p>
                
                <div class="space-y-4 mb-8">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">الروابط المدمجة</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-gray-50 text-gray-600 px-3 py-1 rounded-lg text-xs font-bold border border-gray-100">التقارير</span>
                        <span class="bg-gray-50 text-gray-600 px-3 py-1 rounded-lg text-xs font-bold border border-gray-100">التحليل</span>
                        <span class="bg-gray-50 text-gray-600 px-3 py-1 rounded-lg text-xs font-bold border border-gray-100">التنبؤ</span>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <a href="#" class="mod-btn mod-btn-secondary flex-1">
                        <i class="fas fa-eye"></i> عرض
                    </a>
                    <a href="#" class="mod-btn mod-btn-primary flex-1">
                        <i class="fas fa-cog"></i> إعدادات
                    </a>
                </div>
            </div>

            <!-- Prediction Services -->
            <div class="premium-card p-8 flex flex-col h-full">
                <div class="flex items-start justify-between mb-6">
                    <div class="bg-indigo-50 p-4 rounded-2xl">
                        <i class="fas fa-chart-line text-3xl text-indigo-600"></i>
                    </div>
                    <span class="status-badge status-active">نشط</span>
                </div>
                
                <h3 class="text-xl font-black text-gray-900 mb-2">خدمات التنبؤ</h3>
                <p class="text-gray-500 text-sm mb-6 leading-relaxed flex-grow">توقعات الأسعار الذكية وتحليلات السوق العقاري</p>
                
                <div class="space-y-4 mb-8">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">الروابط المدمجة</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-gray-50 text-gray-600 px-3 py-1 rounded-lg text-xs font-bold border border-gray-100">الأسعار</span>
                        <span class="bg-gray-50 text-gray-600 px-3 py-1 rounded-lg text-xs font-bold border border-gray-100">التحليل</span>
                        <span class="bg-gray-50 text-gray-600 px-3 py-1 rounded-lg text-xs font-bold border border-gray-100">التقارير</span>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <a href="#" class="mod-btn mod-btn-secondary flex-1">
                        <i class="fas fa-eye"></i> عرض
                    </a>
                    <a href="#" class="mod-btn mod-btn-primary flex-1">
                        <i class="fas fa-cog"></i> إعدادات
                    </a>
                </div>
            </div>

            <!-- Image Analysis -->
            <div class="premium-card p-8 flex flex-col h-full">
                <div class="flex items-start justify-between mb-6">
                    <div class="bg-indigo-50 p-4 rounded-2xl">
                        <i class="fas fa-image text-3xl text-indigo-600"></i>
                    </div>
                    <span class="status-badge status-active">نشط</span>
                </div>
                
                <h3 class="text-xl font-black text-gray-900 mb-2">تحليل الصور</h3>
                <p class="text-gray-500 text-sm mb-6 leading-relaxed flex-grow">معالجة الصور العقارية والتعرف على الميزات تلقائياً</p>
                
                <div class="space-y-4 mb-8">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">الروابط المدمجة</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-gray-50 text-gray-600 px-3 py-1 rounded-lg text-xs font-bold border border-gray-100">الرفع</span>
                        <span class="bg-gray-50 text-gray-600 px-3 py-1 rounded-lg text-xs font-bold border border-gray-100">التحليل</span>
                        <span class="bg-gray-50 text-gray-600 px-3 py-1 rounded-lg text-xs font-bold border border-gray-100">النتائج</span>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <a href="#" class="mod-btn mod-btn-secondary flex-1">
                        <i class="fas fa-eye"></i> عرض
                    </a>
                    <a href="#" class="mod-btn mod-btn-primary flex-1">
                        <i class="fas fa-cog"></i> إعدادات
                    </a>
                </div>
            </div>
        </div>

        <!-- Usage Chart Section -->
        <div class="premium-card p-8 mb-10 anim a5">
            <div class="flex items-center justify-between mb-8">
                <div class="bg-indigo-100 p-3 rounded-xl">
                    <i class="fas fa-chart-area text-indigo-600 text-xl"></i>
                </div>
                <h3 class="text-2xl font-black text-gray-900">اتجاه الاستهلاك</h3>
            </div>
            
            <div class="relative h-64 w-full">
                <canvas id="usageChart"></canvas>
            </div>
        </div>

    </div>
</div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('usageChart').getContext('2d');

            const labels = @json($trends->pluck('date'));
            const counts = @json($trends->pluck('count'));
            const costs = @json($trends->pluck('cost'));

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'عدد الطلبات',
                            data: counts,
                            borderColor: '#0D6EFD',
                            backgroundColor: 'rgba(13, 110, 253, 0.1)',
                            fill: true,
                            tension: 0.4,
                            yAxisID: 'y',
                            borderWidth: 3,
                            pointBackgroundColor: '#0D6EFD',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7
                        },
                        {
                            label: 'التكلفة ($)',
                            data: costs,
                            borderColor: '#6F42C1',
                            backgroundColor: 'rgba(111, 66, 193, 0.1)',
                            borderDash: [5, 5],
                            fill: false,
                            tension: 0.4,
                            yAxisID: 'y1',
                            borderWidth: 3,
                            pointBackgroundColor: '#6F42C1',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7
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
                            labels: { 
                                font: { family: 'inherit', size: 12 },
                                usePointStyle: true,
                                padding: 20
                            }
                        }
                    },
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: { 
                                display: true, 
                                text: 'الطلبات',
                                font: { size: 12 }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            grid: { drawOnChartArea: false },
                            title: { 
                                display: true, 
                                text: 'التكلفة ($)',
                                font: { size: 12 }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endsection