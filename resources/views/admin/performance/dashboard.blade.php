@extends('admin.layouts.admin')

@section('title', 'لوحة الأداء')
@section('page-title', 'مراقبة الأداء')

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
</style>

<div class="content-wrapper">
    <div class="w-full px-4 md:px-6 lg:px-8">
        
        <!-- Animated Header -->
        <div class="relative overflow-hidden glass-header rounded-3xl shadow-2xl p-8 mb-10 transform hover:scale-[1.01] transition-all duration-500">
            <div class="absolute inset-0 bg-gradient-to-r from-white/10 to-transparent pointer-events-none"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between flex-wrap gap-6">
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <div class="bg-white/20 backdrop-blur-md p-4 rounded-2xl shadow-inner">
                                <i class="fas fa-tachometer-alt text-white text-3xl"></i>
                            </div>
                            <div>
                                <h1 class="text-4xl font-black text-white tracking-tight">لوحة تحكم الأداء</h1>
                                <p class="text-indigo-100 text-lg font-medium opacity-90">مراقبة حية لأداء النظام والمقاييس الحيوية</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('admin.performance.clear_cache') }}" class="group bg-white text-indigo-700 px-8 py-4 rounded-2xl hover:bg-indigo-50 transition-all duration-300 shadow-xl hover:shadow-2xl hover:scale-105 flex items-center">
                            <i class="fas fa-broom ml-3 group-hover:rotate-12 transition-transform duration-500"></i>
                            <span class="font-bold text-lg">تنظيف الكاش</span>
                        </a>
                        <a href="{{ route('admin.system.logs') }}" class="group bg-white/10 backdrop-blur-xl text-white px-8 py-4 rounded-2xl hover:bg-white/20 transition-all duration-300 border border-white/20 hover:border-white/40 hover:shadow-2xl flex items-center">
                            <i class="fas fa-list ml-3 group-hover:scale-110 transition-transform"></i>
                            <span class="font-bold text-lg">السجلات</span>
                        </a>
                    </div>
                </div>
            </div>
            <!-- Decorative elements -->
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-3xl opacity-50"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl opacity-50"></div>
        </div>

        <!-- Enhanced Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
            <!-- CPU Usage Card -->
            <div class="sc sc-blue anim a1">
                <div class="sc-head">
                    <div class="sc-label">استهلاك المعالج</div>
                    <div class="sc-ic"><i class="fas fa-microchip fa-lg"></i></div>
                </div>
                <div class="sc-val">{{ $metrics['system']['cpu_usage'] ?? 0 }}%</div>
                <div class="sc-footer">
                    <span class="sc-delta"><i class="fas fa-tachometer-alt"></i> CPU</span>
                    <a href="#" class="mr-auto text-white/90 hover:text-white transition-colors">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </div>
            </div>

            <!-- Memory Usage Card -->
            <div class="sc sc-green anim a2">
                <div class="sc-head">
                    <div class="sc-label">الذاكرة المستخدمة</div>
                    <div class="sc-ic"><i class="fas fa-memory fa-lg"></i></div>
                </div>
                <div class="sc-val">{{ $metrics['memory']['current_usage'] ?? 0 }}</div>
                <div class="sc-footer">
                    <span class="sc-delta"><i class="fas fa-database"></i> MB</span>
                    <div class="w-full bg-white/20 rounded-full h-1.5">
                        <div class="bg-white h-1.5 rounded-full" style="width: {{ $metrics['memory']['usage_percentage'] ?? 0 }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Cache Hit Rate Card -->
            <div class="sc sc-purple anim a3">
                <div class="sc-head">
                    <div class="sc-label">معدل إصابة الكاش</div>
                    <div class="sc-ic"><i class="fas fa-bolt fa-lg"></i></div>
                </div>
                <div class="sc-val">{{ $metrics['cache']['hit_rate'] ?? 0 }}%</div>
                <div class="sc-footer">
                    <span class="sc-delta"><i class="fas fa-rocket"></i> Cache</span>
                </div>
            </div>

            <!-- Active Connections Card -->
            <div class="sc sc-orange anim a4">
                <div class="sc-head">
                    <div class="sc-label">الاتصالات النشطة</div>
                    <div class="sc-ic"><i class="fas fa-network-wired fa-lg"></i></div>
                </div>
                <div class="sc-val">{{ $metrics['database']['active_connections'] ?? 0 }}</div>
                <div class="sc-footer">
                    <span class="sc-delta"><i class="fas fa-plug"></i> DB</span>
                </div>
            </div>
        </div>

        <!-- Charts & Details Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10">
            <!-- Core Web Vitals -->
            <div class="lg:col-span-2 premium-card p-8 overflow-hidden relative">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-bl-full opacity-50"></div>
                <div class="flex items-center justify-between mb-8 relative z-10">
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 mb-1">مؤشرات الويب الأساسية</h2>
                        <p class="text-sm text-gray-500">Core Web Vitals Performance Metrics</p>
                    </div>
                    <div class="bg-indigo-100 p-3 rounded-xl">
                        <i class="fas fa-chart-bar text-indigo-600 text-xl"></i>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 relative z-10">
                    <!-- LCP -->
                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 border border-indigo-200 rounded-xl p-4 text-center hover:from-indigo-100 hover:to-blue-100 transition-all duration-300">
                        <h4 class="text-indigo-700 font-bold mb-2">LCP</h4>
                        <div class="text-2xl font-black {{ ($metrics['performance']['largest_contentful_paint'] ?? 0) < 2500 ? 'text-green-600' : 'text-amber-600' }} mb-1">
                            {{ $metrics['performance']['largest_contentful_paint'] ?? 0 }}ms
                        </div>
                        <p class="text-xs text-gray-600">Largest Contentful Paint</p>
                    </div>

                    <!-- FID -->
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 border border-purple-200 rounded-xl p-4 text-center hover:from-purple-100 hover:to-pink-100 transition-all duration-300">
                        <h4 class="text-purple-700 font-bold mb-2">FID</h4>
                        <div class="text-2xl font-black {{ ($metrics['performance']['first_input_delay'] ?? 0) < 100 ? 'text-green-600' : 'text-amber-600' }} mb-1">
                            {{ $metrics['performance']['first_input_delay'] ?? 0 }}ms
                        </div>
                        <p class="text-xs text-gray-600">First Input Delay</p>
                    </div>

                    <!-- CLS -->
                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4 text-center hover:from-green-100 hover:to-emerald-100 transition-all duration-300">
                        <h4 class="text-green-700 font-bold mb-2">CLS</h4>
                        <div class="text-2xl font-black {{ ($metrics['performance']['cumulative_layout_shift'] ?? 0) < 0.1 ? 'text-green-600' : 'text-amber-600' }} mb-1">
                            {{ $metrics['performance']['cumulative_layout_shift'] ?? 0 }}
                        </div>
                        <p class="text-xs text-gray-600">Cumulative Layout Shift</p>
                    </div>
                </div>
            </div>

            <!-- System Info -->
            <div class="premium-card p-8 overflow-hidden relative">
                <div class="absolute top-0 left-0 w-32 h-32 bg-purple-50 rounded-br-full opacity-50"></div>
                <div class="flex items-center justify-between mb-8 relative z-10">
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 mb-1">معلومات النظام</h2>
                        <p class="text-sm text-gray-500">System Information</p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-xl">
                        <i class="fas fa-server text-purple-600 text-xl"></i>
                    </div>
                </div>
                
                <div class="space-y-4 relative z-10">
                    <div class="flex justify-between items-center p-3 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-100">
                        <span class="text-blue-700 font-medium">وقت التشغيل</span>
                        <span class="font-mono text-blue-600 font-bold">{{ $metrics['system']['uptime'] ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg border border-purple-100">
                        <span class="text-purple-700 font-medium">متوسط الحمل (1د)</span>
                        <span class="font-mono text-purple-600 font-bold">{{ $metrics['system']['load_average']['1min'] ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-100">
                        <span class="text-green-700 font-medium">إصدار PHP</span>
                        <span class="font-mono text-green-600 font-bold">{{ phpversion() }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gradient-to-r from-orange-50 to-red-50 rounded-lg border border-orange-100">
                        <span class="text-orange-700 font-medium">إصدار Laravel</span>
                        <span class="font-mono text-orange-600 font-bold">{{ app()->version() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Database & Requests Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
            <!-- Database Metrics -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-database text-blue-500 ml-2"></i>
                    مقاييس قاعدة البيانات
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <tbody class="divide-y divide-gray-100">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">الاتصال</td>
                                <td class="py-3 font-medium text-gray-800" dir="ltr">{{ $metrics['database']['connection'] ?? 'N/A' }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">حجم قاعدة البيانات</td>
                                <td class="py-3 font-medium text-gray-800" dir="ltr">{{ $metrics['database']['size'] ?? 'N/A' }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">إجمالي الاستعلامات</td>
                                <td class="py-3 font-medium text-gray-800">{{ number_format($metrics['queries']['total_queries'] ?? 0) }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">استعلامات بطيئة</td>
                                <td class="py-3 font-medium text-red-500">{{ $metrics['queries']['slow_queries'] ?? 0 }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Request Metrics -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-globe text-emerald-500 ml-2"></i>
                    مقاييس الطلبات (Requests)
                </h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <tbody class="divide-y divide-gray-100">
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">إجمالي الطلبات</td>
                                <td class="py-3 font-medium text-gray-800">{{ number_format($metrics['requests']['total_requests'] ?? 0) }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">متوسط زمن الاستجابة</td>
                                <td class="py-3 font-medium text-gray-800">{{ $metrics['requests']['average_response_time'] ?? 0 }}ms</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">طلبات/ثانية</td>
                                <td class="py-3 font-medium text-gray-800">{{ $metrics['requests']['requests_per_second'] ?? 0 }}</td>
                            </tr>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="py-3 text-gray-600">معدل الخطأ</td>
                                <td class="py-3 font-medium {{ ($metrics['requests']['error_rate'] ?? 0) > 1 ? 'text-red-500' : 'text-green-500' }}">
                                    {{ $metrics['requests']['error_rate'] ?? 0 }}%
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Additional Tools Section -->
        <div class="premium-card p-8 mt-10">
            <h2 class="text-2xl font-black text-gray-900 mb-8 flex items-center">
                <div class="bg-indigo-600 text-white p-3 rounded-2xl ml-4 shadow-lg shadow-indigo-100">
                    <i class="fas fa-tools"></i>
                </div>
                أدوات إضافية
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 text-right">
                <form action="{{ route('admin.performance.clear_cache') }}" method="POST" class="group">
                    @csrf
                    <button type="submit" class="w-full border border-gray-100 rounded-2xl p-6 hover:bg-indigo-50 hover:border-indigo-200 transition-all duration-300 text-left">
                        <div class="bg-indigo-50 text-indigo-600 p-3 rounded-xl mb-4 group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300 w-fit">
                            <i class="fas fa-broom text-xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-1">تنظيف الكاش</h3>
                        <p class="text-xs text-gray-500">مسح ذاكرة التخزين المؤقت</p>
                    </button>
                </form>

                <a href="{{ route('admin.system.cache') }}" class="group border border-gray-100 rounded-2xl p-6 hover:bg-green-50 hover:border-green-200 transition-all duration-300">
                    <div class="bg-green-50 text-green-600 p-3 rounded-xl mb-4 group-hover:scale-110 group-hover:bg-green-600 group-hover:text-white transition-all duration-300 w-fit">
                        <i class="fas fa-cog text-xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-1">إعدادات الكاش</h3>
                    <p class="text-xs text-gray-500">تكوين إعدادات التخزين</p>
                </a>

                <a href="{{ route('admin.system.logs') }}" class="group border border-gray-100 rounded-2xl p-6 hover:bg-purple-50 hover:border-purple-200 transition-all duration-300">
                    <div class="bg-purple-50 text-purple-600 p-3 rounded-xl mb-4 group-hover:scale-110 group-hover:bg-purple-600 group-hover:text-white transition-all duration-300 w-fit">
                        <i class="fas fa-list text-xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-1">سجلات النظام</h3>
                    <p class="text-xs text-gray-500">عرض سجلات النشاط</p>
                </a>

                <a href="#" class="group border border-gray-100 rounded-2xl p-6 hover:bg-orange-50 hover:border-orange-200 transition-all duration-300">
                    <div class="bg-orange-50 text-orange-600 p-3 rounded-xl mb-4 group-hover:scale-110 group-hover:bg-orange-600 group-hover:text-white transition-all duration-300 w-fit">
                        <i class="fas fa-chart-line text-xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-1">تحليلات متقدمة</h3>
                    <p class="text-xs text-gray-500">تقارير مفصلة</p>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
