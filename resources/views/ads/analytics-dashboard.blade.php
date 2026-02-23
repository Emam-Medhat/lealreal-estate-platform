@extends('layouts.app')

@section('title', 'Analytics Dashboard | Advertising System')

@section('content')
    <div class="analytics-dashboard bg-gray-50 min-h-screen py-8" dir="rtl">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight flex items-center">
                        <span class="bg-blue-600 text-white p-2 rounded-lg ml-3 shadow-lg shadow-blue-200">
                            <i class="fas fa-chart-line"></i>
                        </span>
                        لوحة تحليلات نظام الإعلانات
                    </h1>
                    <p class="mt-2 text-gray-600">نظرة شاملة على أداء نظام الإعلانات والنمو المالي.</p>
                </div>
                <div class="flex items-center space-x-3 space-x-reverse">
                    <button onclick="window.location.reload()"
                        class="flex items-center px-4 py-2 bg-white border border-gray-200 rounded-xl shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 transition-all">
                        <i class="fas fa-sync-alt ml-2 text-blue-500"></i>
                        تحديث البيانات
                    </button>
                    <div
                        class="flex items-center px-4 py-2 bg-blue-600 rounded-xl shadow-lg shadow-blue-200 text-sm font-medium text-white">
                        <i class="fas fa-calendar-alt ml-2"></i>
                        {{ now()->format('Y-M-d') }}
                    </div>
                </div>
            </div>

            <!-- Metric Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Impressions -->
                <div
                    class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div
                        class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center text-blue-600">
                                <i class="fas fa-eye text-xl"></i>
                            </div>
                            <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg">+12%</span>
                        </div>
                        <div class="text-3xl font-black text-gray-900 mb-1">
                            {{ number_format($overview['total_impressions']) }}</div>
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">إجمالي المشاهدات</div>
                    </div>
                </div>

                <!-- Clicks -->
                <div
                    class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div
                        class="absolute -right-4 -top-4 w-24 h-24 bg-green-50 rounded-full group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-green-100 flex items-center justify-center text-green-600">
                                <i class="fas fa-mouse-pointer text-xl"></i>
                            </div>
                            <span class="text-xs font-bold text-green-600 bg-green-50 px-2 py-1 rounded-lg">+5.4%</span>
                        </div>
                        <div class="text-3xl font-black text-gray-900 mb-1">{{ number_format($overview['total_clicks']) }}
                        </div>
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">إجمالي النقرات</div>
                    </div>
                </div>

                <!-- Conversions -->
                <div
                    class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div
                        class="absolute -right-4 -top-4 w-24 h-24 bg-purple-50 rounded-full group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div
                                class="w-12 h-12 rounded-2xl bg-purple-100 flex items-center justify-center text-purple-600">
                                <i class="fas fa-bullseye text-xl"></i>
                            </div>
                            <span class="text-xs font-bold text-purple-600 bg-purple-50 px-2 py-1 rounded-lg">+8.2%</span>
                        </div>
                        <div class="text-3xl font-black text-gray-900 mb-1">
                            {{ number_format($overview['total_conversions']) }}</div>
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">إجمالي التحويلات</div>
                    </div>
                </div>

                <!-- Revenue -->
                <div
                    class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow relative overflow-hidden group border-r-4 border-r-amber-400">
                    <div
                        class="absolute -right-4 -top-4 w-24 h-24 bg-amber-50 rounded-full group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center text-amber-600">
                                <i class="fas fa-wallet text-xl"></i>
                            </div>
                            <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-lg">+15.7%</span>
                        </div>
                        <div class="text-3xl font-black text-gray-900 mb-1">
                            {{ number_format($overview['total_revenue'], 2) }}</div>
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wider">إجمالي الإيرادات (SAR)</div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <!-- Charts Section -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Performance Chart -->
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 h-full">
                        <div class="flex items-center justify-between mb-8">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">اتجاهات الأداء</h3>
                                <p class="text-sm text-gray-500">ثبات المشاهدات والتفاعل خلال الـ 30 يوماً الماضية.</p>
                            </div>
                            <div class="flex bg-gray-50 p-1 rounded-xl">
                                <button
                                    class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all bg-white text-blue-600 shadow-sm">الأيام</button>
                                <button
                                    class="px-4 py-1.5 rounded-lg text-xs font-bold text-gray-400 hover:text-gray-600 transition-all">الأسابيع</button>
                            </div>
                        </div>
                        <div class="h-80 relative">
                            <canvas id="performanceChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Extra Stats Sidebar -->
                <div class="space-y-8">
                    <!-- Platform Stats -->
                    <div
                        class="bg-gradient-to-br from-indigo-900 to-blue-800 rounded-3xl p-8 text-white shadow-xl shadow-blue-200 relative overflow-hidden">
                        <div class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none">
                            <svg class="h-full w-full" preserveAspectRatio="none" viewBox="0 0 100 100">
                                <path d="M0 100 Q 25 50 50 100 T 100 100" fill="none" stroke="white" stroke-width="0.5" />
                                <path d="M0 80 Q 25 30 50 80 T 100 80" fill="none" stroke="white" stroke-width="0.5" />
                            </svg>
                        </div>
                        <div class="relative z-10">
                            <h3 class="text-lg font-bold mb-6 flex items-center">
                                <i class="fas fa-server ml-3 text-blue-300"></i>
                                إحصائيات المنصة
                            </h3>
                            <div class="space-y-6">
                                <div
                                    class="flex items-center justify-between leading-none p-3 bg-white/10 rounded-2xl backdrop-blur-sm border border-white/10">
                                    <div>
                                        <div class="text-xs text-blue-200 opacity-80 mb-1">إجمالي الإعلانات</div>
                                        <div class="text-2xl font-black">{{ number_format($overview['total_ads']) }}</div>
                                    </div>
                                    <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center">
                                        <i class="fas fa-layer-group text-blue-300"></i>
                                    </div>
                                </div>
                                <div
                                    class="flex items-center justify-between leading-none p-3 bg-white/10 rounded-2xl backdrop-blur-sm border border-white/10">
                                    <div>
                                        <div class="text-xs text-blue-200 opacity-80 mb-1">الحملات النشطة</div>
                                        <div class="text-2xl font-black">{{ number_format($overview['active_campaigns']) }}
                                        </div>
                                    </div>
                                    <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center">
                                        <i class="fas fa-rocket text-blue-300"></i>
                                    </div>
                                </div>
                                <div
                                    class="flex items-center justify-between leading-none p-3 bg-white/10 rounded-2xl backdrop-blur-sm border border-white/10">
                                    <div>
                                        <div class="text-xs text-blue-200 opacity-80 mb-1">المعلنون النشطون</div>
                                        <div class="text-2xl font-black">
                                            {{ number_format($overview['active_advertisers']) }}</div>
                                    </div>
                                    <div class="w-10 h-10 rounded-full bg-blue-500/20 flex items-center justify-center">
                                        <i class="fas fa-users text-blue-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ROI Circle Chart (CSS based) -->
                    <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 text-center">
                        <h3 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-6">معدل التحويل العام</h3>
                        <div class="relative w-32 h-32 mx-auto mb-6">
                            @php
                                $convRate = $overview['total_clicks'] > 0 ? ($overview['total_conversions'] / $overview['total_clicks']) * 100 : 0;
                            @endphp
                            <svg class="w-full h-full transform -rotate-90">
                                <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent"
                                    class="text-gray-100" />
                                <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="8" fill="transparent"
                                    class="text-blue-500"
                                    style="stroke-dasharray: 364.4; stroke-dashoffset: {{ 364.4 * (1 - ($convRate / 100)) }}" />
                            </svg>
                            <div
                                class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 text-2xl font-black text-gray-900">
                                {{ number_format($convRate, 1) }}%
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 leading-relaxed">
                            يُظهر هذا المعدل نسبة النقرات التي انتهت بعملية تحويل حقيقية داخل منصتنا.
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8 mb-8">
                <!-- Top Campaigns Table -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">أفضل الحملات الإعلانية</h3>
                        <button class="text-sm font-bold text-blue-600 hover:text-blue-700">عرض الكل</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-right">
                            <thead>
                                <tr class="bg-gray-50/50 text-gray-400 text-[10px] uppercase font-bold tracking-widest">
                                    <th class="px-6 py-4">الحملة</th>
                                    <th class="px-6 py-4">المعلن</th>
                                    <th class="px-6 py-4 text-center">المشاهدات</th>
                                    <th class="px-6 py-4 text-center">النقرات</th>
                                    <th class="px-6 py-4 text-left">الحالة</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($topCampaigns as $campaign)
                                    <tr class="hover:bg-gray-50/80 transition-colors group">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div
                                                    class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center ml-3 font-bold group-hover:scale-110 transition-transform">
                                                    {{ substr($campaign->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="text-sm font-bold text-gray-900">{{ $campaign->name }}</div>
                                                    <div class="text-[10px] text-gray-400">{{ $campaign->objective }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-600">{{ $campaign->user->name ?? 'مجهول' }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-center font-mono text-sm font-bold text-gray-900">
                                            {{ number_format($campaign->ads_sum_impressions_count ?? 0) }}
                                        </td>
                                        <td
                                            class="px-6 py-4 text-center font-mono text-sm font-bold text-gray-900 text-blue-600">
                                            {{ number_format($campaign->ads_sum_clicks_count ?? 0) }}
                                        </td>
                                        <td class="px-6 py-4 text-left">
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-black uppercase tracking-tighter {{ $campaign->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                                {{ $campaign->status == 'active' ? 'نشطة' : 'متوقفة' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Top Advertisers Section -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-50 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">أكثر المعلنين نشاطاً</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-6">
                            @foreach($topAdvertisers as $advertiser)
                                <div class="flex items-center group">
                                    <div class="relative ml-4">
                                        @if($advertiser->profile_image)
                                            <img src="{{ $advertiser->avatar_url }}"
                                                class="w-12 h-12 rounded-2xl object-cover shadow-sm ring-2 ring-gray-100 group-hover:ring-blue-200 transition-all">
                                        @else
                                            <div
                                                class="w-12 h-12 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-500 font-black shadow-sm group-hover:from-blue-50 group-hover:to-blue-100 transition-all">
                                                {{ substr($advertiser->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div
                                            class="absolute -bottom-1 -left-1 w-5 h-5 bg-blue-600 text-white rounded-full flex items-center justify-center text-[10px] font-bold border-2 border-white">
                                            {{ $loop->iteration }}
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="text-sm font-bold text-gray-900">{{ $advertiser->name }}</div>
                                            <div class="text-xs font-bold text-blue-600">
                                                {{ number_format($advertiser->ads_sum_impressions_count ?? 0) }} ظهور</div>
                                        </div>
                                        <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                            @php
                                                $maxImpressions = $topAdvertisers->max('ads_sum_impressions_count') ?: 1;
                                                $width = (($advertiser->ads_sum_impressions_count ?? 0) / $maxImpressions) * 100;
                                            @endphp
                                            <div class="bg-blue-600 h-full rounded-full transition-all duration-1000 group-hover:bg-blue-400"
                                                style="width: {{ $width }}%"></div>
                                        </div>
                                        <div
                                            class="flex items-center justify-between mt-2 text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                                            <span>{{ $advertiser->campaigns_count }} حملة</span>
                                            <span>{{ $advertiser->ads_count }} إعلان مُفعل</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Best Performing Ads -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 mb-8 p-8">
                <h3 class="text-xl font-bold text-gray-900 mb-8">الإعلانات الأعلى تحويلاً</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">
                    @foreach($topAds as $ad)
                        <div
                            class="bg-gray-50 rounded-2xl p-4 border border-gray-100 hover:bg-white hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group">
                            <div class="aspect-video bg-gray-200 rounded-xl mb-4 overflow-hidden relative">
                                <!-- We use a placeholder image if no real image exists -->
                                <img src="{{ $ad->image_url_full ?? 'https://via.placeholder.com/300x169?text=Ad+Creative' }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                <div
                                    class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-3">
                                    <div class="text-white text-[10px] font-bold uppercase tracking-wider">{{ $ad->type }}</div>
                                    <div class="text-white text-xs font-bold truncate">{{ $ad->title }}</div>
                                </div>
                            </div>
                            <div class="space-y-3">
                                <div class="text-sm font-bold text-gray-900 truncate">{{ $ad->title }}</div>
                                <div class="flex items-center justify-between text-[10px] text-gray-500 font-bold">
                                    <span>CTR:
                                        {{ number_format($ad->clicks_count > 0 && $ad->impressions_count > 0 ? ($ad->clicks_count / $ad->impressions_count) * 100 : 0, 2) }}%</span>
                                    <span class="text-blue-600">{{ number_format($ad->impressions_count) }} ظهور</span>
                                </div>
                                <div class="pt-3 border-t border-gray-200/50 flex items-center justify-between">
                                    <div class="text-[10px] text-gray-400">حملة: <span
                                            class="text-gray-600">{{ $ad->campaign->name ?? 'N/A' }}</span></div>
                                    <a href="{{ route('ads.show', $ad->id) }}"
                                        class="w-6 h-6 rounded-lg bg-white text-gray-400 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-colors border border-gray-100 shadow-sm">
                                        <i class="fas fa-chevron-left text-[8px]"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Charts Script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('performanceChart').getContext('2d');

            // Performance Data integration
            const recentPerformance = @json($recentPerformance);
            const labels = recentPerformance.map(item => item.date);
            const impressions = recentPerformance.map(item => item.impressions);

            // Create gradient
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(37, 99, 235, 0.2)');
            gradient.addColorStop(1, 'rgba(37, 99, 235, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'المشاهدات',
                        data: impressions,
                        fill: true,
                        backgroundColor: gradient,
                        borderColor: '#2563eb',
                        borderWidth: 3,
                        tension: 0.4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#2563eb',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    family: 'sans-serif',
                                    size: 10
                                },
                                color: '#9ca3af'
                            }
                        },
                        y: {
                            grid: {
                                borderDash: [5, 5],
                                color: '#f3f4f6'
                            },
                            ticks: {
                                font: {
                                    family: 'sans-serif',
                                    size: 10
                                },
                                color: '#9ca3af'
                            },
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>

    <style>
        /* Smooth Scroll & Transitions */
        * {
            transition: all 0.2s ease-out;
        }

        .analytics-dashboard {
            font-family: 'Inter', 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        /* Custom Webkit scrollbar for premium feel */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #e5e7eb;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #d1d5db;
        }

        /* Glassmorphism utility */
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
@endsection