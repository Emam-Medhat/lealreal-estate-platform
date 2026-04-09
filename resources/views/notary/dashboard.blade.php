@extends('layouts.dashboard')

@section('title', 'لوحة تحكم التوثيق')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-indigo-50">
    <div class="p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Animated Header -->
            <div class="relative overflow-hidden bg-gradient-to-r from-purple-700 via-pink-600 to-indigo-700 rounded-3xl shadow-2xl p-8 mb-10 transform hover:scale-[1.02] transition-all duration-500">
                <div class="absolute inset-0 bg-gradient-to-r from-purple-600/20 to-pink-600/20 animate-pulse"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between">
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <div class="bg-white/20 backdrop-blur-sm p-3 rounded-xl">
                                    <i class="fas fa-certificate text-white text-2xl"></i>
                                </div>
                                <div>
                                    <h1 class="text-4xl font-bold text-white tracking-tight">لوحة تحكم التوثيق</h1>
                                    <p class="text-purple-100 text-lg font-medium">إدارة طلبات التوثيق والتحقق من المستندات</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-3 space-x-reverse">
                            <a href="{{ route('notary.index') }}" class="group bg-white/10 backdrop-blur-md text-white px-6 py-3 rounded-xl hover:bg-white/20 transition-all duration-300 border border-white/20 hover:border-white/40 hover:shadow-xl">
                                <i class="fas fa-list ml-2 group-hover:scale-110 transition-transform"></i>
                                <span class="font-medium">جميع الطلبات</span>
                            </a>
                            @if(Route::has('notary.create'))
                                <a href="{{ route('notary.create') }}" class="group bg-white text-purple-700 px-6 py-3 rounded-xl hover:bg-purple-50 transition-all duration-300 shadow-lg hover:shadow-xl hover:scale-105">
                                    <i class="fas fa-plus ml-2 group-hover:rotate-90 transition-transform duration-300"></i>
                                    <span class="font-semibold">طلب تحقق جديد</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <!-- Decorative elements -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-purple-400/20 rounded-full blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-pink-400/20 rounded-full blur-3xl"></div>
            </div>

            <!-- Enhanced Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
                <div class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-yellow-400/10 to-orange-400/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-gradient-to-br from-yellow-400 to-orange-500 text-white rounded-2xl p-4 shadow-lg group-hover:scale-110 transition-transform duration-500">
                                <i class="fas fa-clock text-2xl"></i>
                            </div>
                            <div class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-semibold">
                                +12% هذا الشهر
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-medium mb-1">في الانتظار</p>
                            <p class="text-4xl font-bold text-gray-900 group-hover:text-yellow-600 transition-colors">{{ $stats['pending_verifications'] ?? 0 }}</p>
                            <div class="mt-3 h-1 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full animate-pulse" style="width: 65%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-green-400/10 to-emerald-400/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-gradient-to-br from-green-400 to-emerald-500 text-white rounded-2xl p-4 shadow-lg group-hover:scale-110 transition-transform duration-500">
                                <i class="fas fa-check-circle text-2xl"></i>
                            </div>
                            <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                                +8% هذا الشهر
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-medium mb-1">موثق اليوم</p>
                            <p class="text-4xl font-bold text-gray-900 group-hover:text-green-600 transition-colors">{{ $stats['verified_today'] ?? 0 }}</p>
                            <div class="mt-3 h-1 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-green-400 to-emerald-500 rounded-full animate-pulse" style="width: 85%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="group relative bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-2 overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-400/10 to-cyan-400/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                    <div class="relative p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-gradient-to-br from-blue-400 to-cyan-500 text-white rounded-2xl p-4 shadow-lg group-hover:scale-110 transition-transform duration-500">
                                <i class="fas fa-chart-line text-2xl"></i>
                            </div>
                            <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold">
                                +15% هذا الشهر
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 font-medium mb-1">إجمالي الموثق</p>
                            <p class="text-4xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $stats['total_verified'] ?? 0 }}</p>
                            <div class="mt-3 h-1 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-blue-400 to-cyan-500 rounded-full animate-pulse" style="width: 95%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Pending Verifications -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-purple-100">
                    <div class="bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600 px-8 py-6">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-white flex items-center">
                                <div class="bg-white/20 backdrop-blur p-2 rounded-lg ml-3">
                                    <i class="fas fa-clock text-white"></i>
                                </div>
                                طلبات في الانتظار
                            </h2>
                            <div class="bg-white/20 backdrop-blur text-white px-4 py-2 rounded-lg text-sm font-medium">
                                {{ $myVerifications->count() }} طلب
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($myVerifications->count() > 0)
                            <div class="space-y-4">
                                @foreach($myVerifications as $verification)
                                    <div class="group relative bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-xl p-4 hover:from-purple-100 hover:to-pink-100 transition-all duration-300 hover:shadow-lg">
                                        <div class="absolute top-0 right-0 w-2 h-full bg-gradient-to-b from-purple-500 to-pink-500 rounded-r-xl"></div>
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <h4 class="font-bold text-gray-900 mb-2 flex items-center">
                                                    <div class="bg-purple-100 text-purple-600 p-2 rounded-lg ml-3">
                                                        <i class="fas fa-file-alt text-sm"></i>
                                                    </div>
                                                    {{ $verification->document->title ?? 'مستند بدون عنوان' }}
                                                </h4>
                                                <div class="flex items-center space-x-4 space-x-reverse text-sm">
                                                    <span class="bg-gradient-to-r from-purple-100 to-pink-100 text-purple-700 px-3 py-1 rounded-lg font-mono font-semibold">
                                                        {{ $verification->verification_code }}
                                                    </span>
                                                    <span class="text-gray-500 flex items-center">
                                                        <i class="fas fa-calendar ml-2 text-purple-500"></i>
                                                        {{ $verification->created_at->format('Y-m-d H:i') }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex space-x-2 space-x-reverse">
                                                <a href="{{ route('notary.show', $verification) }}" class="group/btn bg-gradient-to-r from-purple-500 to-pink-500 text-white hover:from-purple-600 hover:to-pink-600 px-4 py-2 rounded-xl transition-all duration-300 hover:scale-105 shadow-lg">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <div class="bg-gradient-to-r from-purple-100 to-pink-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-check-circle text-4xl text-purple-500"></i>
                                </div>
                                <p class="text-xl font-bold text-gray-600 mb-2">لا توجد طلبات في الانتظار</p>
                                <p class="text-sm text-gray-500">جميع الطلبات تمت معالجتها</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Verifications -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-green-100">
                    <div class="bg-gradient-to-r from-green-600 via-emerald-600 to-green-600 px-8 py-6">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-white flex items-center">
                                <div class="bg-white/20 backdrop-blur p-2 rounded-lg ml-3">
                                    <i class="fas fa-check-circle text-white"></i>
                                </div>
                                التوثيق الأخير
                            </h2>
                            <div class="bg-white/20 backdrop-blur text-white px-4 py-2 rounded-lg text-sm font-medium">
                                {{ $recentVerifications->count() }} توثيق
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        @if($recentVerifications->count() > 0)
                            <div class="space-y-4">
                                @foreach($recentVerifications as $verification)
                                    <div class="group relative bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4 hover:from-green-100 hover:to-emerald-100 transition-all duration-300 hover:shadow-lg">
                                        <div class="absolute top-0 right-0 w-2 h-full bg-gradient-to-b from-green-500 to-emerald-500 rounded-r-xl"></div>
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <h4 class="font-bold text-gray-900 mb-2 flex items-center">
                                                    <div class="bg-green-100 text-green-600 p-2 rounded-lg ml-3">
                                                        <i class="fas fa-file-check text-sm"></i>
                                                    </div>
                                                    {{ $verification->document->title ?? 'مستند بدون عنوان' }}
                                                </h4>
                                                <div class="flex items-center space-x-4 space-x-reverse text-sm">
                                                    <span class="bg-gradient-to-r from-green-100 to-emerald-100 text-green-700 px-3 py-1 rounded-lg font-mono font-semibold">
                                                        {{ $verification->verification_code }}
                                                    </span>
                                                    @if($verification->verified_at)
                                                        <span class="text-gray-500 flex items-center">
                                                            <i class="fas fa-check ml-2 text-green-500"></i>
                                                            {{ $verification->verified_at->format('Y-m-d H:i') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex space-x-2 space-x-reverse">
                                                <a href="{{ route('notary.show', $verification) }}" class="group/btn bg-gradient-to-r from-green-500 to-emerald-500 text-white hover:from-green-600 hover:to-emerald-600 px-4 py-2 rounded-xl transition-all duration-300 hover:scale-105 shadow-lg">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @if($verification->status === 'valid' && Route::has('notary.certificate'))
                                                    <a href="{{ route('notary.certificate', $verification) }}" class="group/btn bg-gradient-to-r from-blue-500 to-cyan-500 text-white hover:from-blue-600 hover:to-cyan-600 px-4 py-2 rounded-xl transition-all duration-300 hover:scale-105 shadow-lg">
                                                        <i class="fas fa-certificate"></i>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <div class="bg-gradient-to-r from-green-100 to-emerald-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-history text-4xl text-green-500"></i>
                                </div>
                                <p class="text-xl font-bold text-gray-600 mb-2">لا توجد توثيقات سابقة</p>
                                <p class="text-sm text-gray-500">ابدأ بالتوثيق لرؤية النتائج هنا</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
