@extends('layouts.app')

@section('title', $title ?? 'Report Under Construction')

@section('content')
<div class="container mx-auto px-4 py-8">
<!-- Page Header -->
<div class="mb-8">
    <div class="bg-gradient-to-r from-slate-700 to-slate-900 rounded-2xl shadow-2xl p-8 text-white relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full -mr-32 -mt-32"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-white rounded-full -ml-24 -mb-24"></div>
        </div>
        
        <div class="relative z-10">
            <div class="flex justify-between items-center">
                <div>
                    <div class="flex items-center mb-3">
                        <div class="bg-white bg-opacity-20 rounded-xl p-3 ml-4 backdrop-blur-sm border border-white border-opacity-30">
                            <i class="fas fa-hard-hat text-white text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold text-white mb-1">{{ $title ?? 'Report Under Construction' }}</h1>
                            <p class="text-white text-lg">نحن نعمل حالياً على هذا النوع من التقارير</p>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-reverse space-x-3">
                    <a href="{{ $backRoute ?? route('reports.index') }}" class="inline-flex items-center px-6 py-3 bg-white text-black rounded-xl text-sm font-medium hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-arrow-left ml-2"></i>
                        {{ $backLabel ?? 'Return to Reports' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Construction Message -->
<div class="bg-white rounded-2xl shadow-lg border-2 border-gray-300 p-12 text-center">
    <div class="mb-8">
        <div class="bg-amber-100 rounded-full p-6 inline-block mb-6">
            <i class="fas fa-tools text-amber-600 text-5xl"></i>
        </div>
        <h2 class="text-3xl font-bold text-black mb-4">قيد التطوير</h2>
        <p class="text-lg text-black mb-6">
            نحن نعمل حالياً على تطوير هذا النوع من التقارير. 
            <br>يرجى التحقق مرة أخرى لاحقًا.
        </p>
        
        <!-- Progress Indicators -->
        <div class="max-w-md mx-auto mb-8">
            <div class="bg-gray-200 rounded-full h-3 mb-4">
                <div class="bg-gradient-to-r from-amber-500 to-amber-600 h-3 rounded-full" style="width: 65%"></div>
            </div>
            <p class="text-sm text-black">تقدم التطوير: 65%</p>
        </div>
        
        <!-- Features Coming Soon -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                <i class="fas fa-chart-bar text-blue-600 text-2xl mb-3"></i>
                <h3 class="text-black font-semibold mb-2">تحليلات متقدمة</h3>
                <p class="text-black text-sm">رسوم بيانية تفصيلية وتحليلات شاملة</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                <i class="fas fa-download text-green-600 text-2xl mb-3"></i>
                <h3 class="text-black font-semibold mb-2">تصدير البيانات</h3>
                <p class="text-black text-sm">تصدير التقارير بصيغ متعددة (PDF, Excel, CSV)</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                <i class="fas fa-filter text-purple-600 text-2xl mb-3"></i>
                <h3 class="text-black font-semibold mb-2">فلاتر متقدمة</h3>
                <p class="text-black text-sm">فلاتر قوية وتخصيص شامل للبيانات</p>
            </div>
        </div>
        
        <!-- Contact Info -->
        <div class="bg-slate-50 rounded-xl p-6 border border-slate-200">
            <h3 class="text-black font-semibold mb-3">هل تحتاج مساعدة؟</h3>
            <p class="text-black text-sm mb-4">
                إذا كان لديك أي استفسارات أو اقتراحات، لا تترد في التواصل معنا
            </p>
            <div class="flex justify-center space-x-reverse space-x-4">
                <a href="{{ route('reports.support.contact') }}" class="inline-flex items-center px-4 py-2 bg-slate-600 text-white rounded-lg text-sm font-medium hover:bg-slate-700 transition-colors">
                    <i class="fas fa-envelope ml-2"></i>
                    تواصل معنا
                </a>
                <a href="{{ route('reports.support.help') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                    <i class="fas fa-question-circle ml-2"></i>
                    مركز المساعدة
                </a>
            </div>
        </div>
    </div>
</div>

</div>
@endsection
