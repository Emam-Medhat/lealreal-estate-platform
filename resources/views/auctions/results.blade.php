@extends('layouts.app')

@section('title', 'نتائج المزادات')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #F1F3F8 0%, #E8EEF7 100%) !important;
    }
    
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

    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fade-in 0.6s ease-out;
    }

    .animation-delay-2000 {
        animation-delay: 2s;
    }

    .animation-delay-4000 {
        animation-delay: 4s;
    }

    .backdrop-blur-xl {
        backdrop-filter: blur(24px);
    }

    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
    }

    ::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: rgba(255, 255, 255, 0.5);
    }

    /* Force styles to override any conflicts */
    .glass-header {
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.95), rgba(147, 51, 234, 0.95)) !important;
        backdrop-filter: blur(10px) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }

    .premium-card {
        background: #FFFFFF !important;
        border: 1px solid #E5E7EB !important;
        border-radius: 18px !important;
        transition: all 0.25s cubic-bezier(.4, 0, .2, 1) !important;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .06) !important;
    }
    
    .premium-card:hover {
        transform: translateY(-4px) !important;
        box-shadow: 0 4px 16px rgba(0, 0, 0, .08) !important;
    }

    /* Ensure gradient backgrounds are visible */
    .bg-gradient-to-r {
        background-image: linear-gradient(to right, var(--tw-gradient-stops)) !important;
    }

    .from-blue-600 {
        --tw-gradient-from: #2563eb !important;
        --tw-gradient-stops: var(--tw-gradient-from), var(--tw-gradient-to, rgb(37 99 235 / 0)) !important;
    }

    .to-purple-600 {
        --tw-gradient-to: #9333ea !important;
    }

    .via-purple-600 {
        --tw-gradient-stops: var(--tw-gradient-from), #9333ea, var(--tw-gradient-to, rgb(147 51 234 / 0)) !important;
    }
</style>

<div class="content-wrapper" style="background: linear-gradient(135deg, #F1F3F8 0%, #E8EEF7 100%) !important; min-height: 100vh; margin-top:130px !important;">
    <div class="w-full px-4 md:px-6 lg:px-8">
    <!-- Animated Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-20 w-96 h-96 bg-gradient-to-r from-blue-400 to-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-pulse"></div>
        <div class="absolute top-60 right-20 w-80 h-80 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-pulse animation-delay-2000"></div>
        <div class="absolute bottom-40 left-1/3 w-72 h-72 bg-gradient-to-r from-indigo-400 to-blue-400 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-pulse animation-delay-4000"></div>
    </div>

    <div class="relative z-10 min-h-screen">
        <!-- Animated Header -->
        <div class="relative overflow-hidden glass-header rounded-3xl shadow-2xl p-8 mb-10 transform hover:scale-[1.01] transition-all duration-500" style="background: linear-gradient(135deg, rgba(79, 70, 229, 0.95), rgba(147, 51, 234, 0.95)) !important; backdrop-filter: blur(10px) !important; border: 1px solid rgba(255, 255, 255, 0.1) !important;">
            <div class="absolute inset-0 bg-gradient-to-r from-white/10 to-transparent pointer-events-none"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between flex-wrap gap-6">
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <div class="bg-white/20 backdrop-blur-md p-4 rounded-2xl shadow-inner">
                                <i class="fas fa-gavel text-white text-3xl"></i>
                            </div>
                            <div>
                                <h1 class="text-4xl font-black text-white tracking-tight">نتائج المزادات</h1>
                                <p class="text-indigo-100 text-lg font-medium opacity-90">تصفح نتائج المزادات المكتملة</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <a href="{{ route('auctions.index') }}" class="group bg-white text-indigo-700 px-8 py-4 rounded-2xl hover:bg-indigo-50 transition-all duration-300 shadow-xl hover:shadow-2xl hover:scale-105 flex items-center">
                            <i class="fas fa-hammer ml-3 group-hover:rotate-12 transition-transform duration-500"></i>
                            <span class="font-bold text-lg">المزادات</span>
                        </a>
                        <a href="{{ route('auctions.my') }}" class="group bg-white/10 backdrop-blur-xl text-white px-8 py-4 rounded-2xl hover:bg-white/20 transition-all duration-300 border border-white/20 hover:border-white/40 hover:shadow-2xl flex items-center">
                            <i class="fas fa-list ml-3 group-hover:scale-110 transition-transform"></i>
                            <span class="font-bold text-lg">مزاداتي</span>
                        </a>
                        <a href="{{ route('auctions.my-results') }}" class="group bg-white/10 backdrop-blur-xl text-white px-8 py-4 rounded-2xl hover:bg-white/20 transition-all duration-300 border border-white/20 hover:border-white/40 hover:shadow-2xl flex items-center">
                            <i class="fas fa-trophy ml-3 group-hover:scale-110 transition-transform"></i>
                            <span class="font-bold text-lg">نتائجي</span>
                        </a>
                    </div>
                </div>
            </div>
            <!-- Decorative elements -->
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-3xl opacity-50"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl opacity-50"></div>
        </div>

            <!-- Filter Section -->
            <div class="premium-card p-8 overflow-hidden relative">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-bl-full opacity-50"></div>
                <div class="flex items-center justify-between mb-8 relative z-10">
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 mb-1">البحث والفلترة</h2>
                        <p class="text-sm text-gray-500">ابحث عن نتائج المزادات المحددة</p>
                    </div>
                    <div class="bg-indigo-100 p-3 rounded-xl">
                        <i class="fas fa-filter text-indigo-600 text-xl"></i>
                    </div>
                </div>
                <form method="GET" action="{{ route('auctions.results') }}" class="flex flex-col lg:flex-row gap-4 relative z-10">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" name="winner_id" placeholder="بحث عن الفائز..." 
                                value="{{ request('winner_id') }}"
                                class="w-full px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-2xl text-gray-700 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 transition-all duration-300 text-lg">
                            <i class="fas fa-search absolute right-4 top-1/2 transform -translate-y-1/2 text-blue-400"></i>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <input type="date" name="date_from" placeholder="من تاريخ" 
                                value="{{ request('date_from') }}"
                                class="px-4 py-3 bg-gradient-to-r from-purple-50 to-pink-50 border-2 border-purple-200 rounded-2xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 transition-all duration-300">
                        <input type="date" name="date_to" placeholder="إلى تاريخ" 
                                value="{{ request('date_to') }}"
                                class="px-4 py-3 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-2xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500/50 focus:border-green-500 transition-all duration-300">
                        <input type="number" name="min_price" placeholder="السعر الأدنى" 
                                value="{{ request('min_price') }}"
                                class="px-4 py-3 bg-gradient-to-r from-orange-50 to-yellow-50 border-2 border-orange-200 rounded-2xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-orange-500/50 focus:border-orange-500 transition-all duration-300">
                        <input type="number" name="max_price" placeholder="السعر الأعلى" 
                                value="{{ request('max_price') }}"
                                class="px-4 py-3 bg-gradient-to-r from-red-50 to-pink-50 border-2 border-red-200 rounded-2xl text-gray-700 focus:outline-none focus:ring-2 focus:ring-red-500/50 focus:border-red-500 transition-all duration-300">
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-2xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-105 font-bold shadow-lg shadow-blue-200" style="background: linear-gradient(to right, #2563eb, #9333ea) !important;">
                            <i class="fas fa-filter ml-2"></i>
                            فلترة
                        </button>
                    </div>
                </form>
            </div>

            <!-- Results Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
                @forelse($results as $result)
                <div class="premium-card overflow-hidden hover:shadow-2xl transition-all duration-500 transform hover:scale-105">
                    <!-- Result Header -->
                    <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 p-6" style="background: linear-gradient(to right, #2563eb, #9333ea, #4f46e5) !important;">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-4 space-x-reverse">
                                <div class="relative">
                                    <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center shadow-lg">
                                        <i class="fas fa-gavel text-white text-2xl"></i>
                                    </div>
                                    @if($result->winner_id)
                                        <div class="absolute -top-1 -right-1 w-6 h-6 bg-amber-500 rounded-full flex items-center justify-center">
                                            <i class="fas fa-crown text-white text-xs"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-white mb-1">{{ $result->auction->property->title }}</h3>
                                    <p class="text-indigo-100 text-sm">{{ $result->auction->property->address }}</p>
                                </div>
                            </div>
                            <div class="text-left">
                                <span class="text-sm text-white/90">مكتمل: {{ $result->result_time->format('Y-m-d H:i') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <!-- Winner Information -->
                        @if($result->winner)
                        <div class="bg-gradient-to-r from-emerald-50 to-green-50 border border-emerald-200 rounded-xl p-4 mb-4">
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-trophy text-emerald-600 text-xl"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-emerald-800">الفائز: {{ $result->winner->name }}</p>
                                    <p class="text-sm text-emerald-600">السعر النهائي: {{ number_format($result->final_price, 2) }} ريال</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Result Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-3">
                                <p class="text-blue-600 text-sm font-medium mb-1">السعر الابتدائي</p>
                                <p class="text-blue-900 font-black text-lg">{{ number_format($result->starting_price, 2) }} ريال</p>
                            </div>
                            <div class="bg-gradient-to-r from-emerald-50 to-green-50 rounded-lg p-3">
                                <p class="text-emerald-600 text-sm font-medium mb-1">السعر النهائي</p>
                                <p class="text-emerald-900 font-black text-lg">{{ number_format($result->final_price, 2) }} ريال</p>
                            </div>
                            <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg p-3">
                                <p class="text-purple-600 text-sm font-medium mb-1">عدد المزايدات</p>
                                <p class="text-purple-900 font-black text-lg">{{ $result->auction->bids_count ?? 0 }}</p>
                            </div>
                            <div class="bg-gradient-to-r from-orange-50 to-yellow-50 rounded-lg p-3">
                                <p class="text-orange-600 text-sm font-medium mb-1">المدة</p>
                                <p class="text-orange-900 font-black text-lg">{{ $result->auction->duration ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('auctions.results.show', $result->id) }}" 
                                class="py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 font-bold text-center shadow-lg" style="background: linear-gradient(to right, #2563eb, #4f46e5) !important;">
                                <i class="fas fa-eye ml-2"></i>
                                عرض التفاصيل
                            </a>
                            @if($result->winner_id)
                                <a href="{{ route('auctions.results.confirm-winner', $result->id) }}" 
                                    class="py-3 bg-gradient-to-r from-emerald-600 to-green-600 text-white rounded-xl hover:from-emerald-700 hover:to-green-700 transition-all duration-300 font-bold text-center shadow-lg" style="background: linear-gradient(to right, #059669, #16a34a) !important;">
                                    <i class="fas fa-check ml-2"></i>
                                    تأكيد الفائز
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($results->hasPages())
            <div class="flex justify-center mt-8">
                <div class="premium-card p-4">
                    {{ $results->links() }}
                </div>
            </div>
            @endif

            <!-- Back to Auctions -->
            <div class="text-center mt-10">
                <a href="{{ route('auctions.index') }}" class="group inline-flex items-center px-8 py-4 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-2xl hover:from-gray-700 hover:to-gray-800 transition-all duration-300 font-bold shadow-lg hover:shadow-xl transform hover:scale-105">
                    <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
                    العودة إلى المزادات
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
