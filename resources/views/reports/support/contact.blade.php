@extends('layouts.app')

@section('title', $title ?? 'تواصل معنا')

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
                            <i class="fas fa-envelope text-white text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold text-white mb-1">تواصل معنا</h1>
                            <p class="text-white text-lg">نحن هنا لمساعدتك في أي استفسارات أو مشاكل</p>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-reverse space-x-3">
                    <a href="{{ $backRoute ?? route('reports.index') }}" class="inline-flex items-center px-6 py-3 bg-white text-black rounded-xl text-sm font-medium hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-arrow-left ml-2"></i>
                        {{ $backLabel ?? 'العودة' }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contact Form -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Contact Form -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8">
            <h2 class="text-2xl font-bold text-black mb-6 flex items-center">
                <div class="bg-slate-600 rounded-lg p-2 ml-3 shadow-md">
                    <i class="fas fa-paper-plane text-white"></i>
                </div>
                أرسل رسالتك
            </h2>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <i class="fas fa-check-circle ml-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <i class="fas fa-exclamation-circle ml-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('reports.support.send_contact') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-black mb-2">الاسم الكامل *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent bg-white text-black transition-all duration-200"
                               placeholder="أدخل اسمك الكامل">
                        @error('name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-black mb-2">البريد الإلكتروني *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent bg-white text-black transition-all duration-200"
                               placeholder="example@email.com">
                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-black mb-2">الموضوع *</label>
                    <input type="text" name="subject" value="{{ old('subject') }}" required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent bg-white text-black transition-all duration-200"
                           placeholder="موضوع الرسالة">
                    @error('subject')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-bold text-black mb-2">نوع التقرير (اختياري)</label>
                    <select name="report_type" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent bg-white text-black transition-all duration-200">
                        <option value="">اختر نوع التقرير</option>
                        <option value="sales">تقارير المبيعات</option>
                        <option value="performance">تقارير الأداء</option>
                        <option value="market">تقارير السوق</option>
                        <option value="financial">تقارير مالية</option>
                        <option value="property">تقارير العقارات</option>
                        <option value="custom">تقارير مخصصة</option>
                        <option value="other">أخرى</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-black mb-2">الرسالة *</label>
                    <textarea name="message" rows="6" required
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-500 focus:border-transparent bg-white text-black transition-all duration-200"
                              placeholder="اكتب رسالتك هنا...">{{ old('message') }}</textarea>
                    @error('message')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <p class="text-sm text-black">جميع الحقول المطلوبة يجب ملؤها *</p>
                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-slate-600 text-white rounded-xl text-sm font-medium hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                        <i class="fas fa-paper-plane ml-2"></i>
                        إرسال الرسالة
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Contact Info -->
    <div class="space-y-6">
        <!-- Quick Contact Info -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-black mb-4 flex items-center">
                <div class="bg-slate-600 rounded-lg p-2 ml-3 shadow-md">
                    <i class="fas fa-info-circle text-white"></i>
                </div>
                معلومات التواصل
            </h3>
            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="bg-blue-100 rounded-lg p-2 ml-3">
                        <i class="fas fa-envelope text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-black font-medium">البريد الإلكتروني</p>
                        <p class="text-black text-sm">support@example.com</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="bg-green-100 rounded-lg p-2 ml-3">
                        <i class="fas fa-phone text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-black font-medium">الهاتف</p>
                        <p class="text-black text-sm">+966 12 345 6789</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="bg-purple-100 rounded-lg p-2 ml-3">
                        <i class="fas fa-clock text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-black font-medium">ساعات العمل</p>
                        <p class="text-black text-sm">الأحد - الخميس: 9:00 ص - 5:00 م</p>
                        <p class="text-black text-sm">الجمعة: 9:00 ص - 2:00 م</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-black mb-4 flex items-center">
                <div class="bg-slate-600 rounded-lg p-2 ml-3 shadow-md">
                    <i class="fas fa-question-circle text-white"></i>
                </div>
                أسئلة شائعة
            </h3>
            <div class="space-y-3">
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="text-black font-medium mb-2">كم من الوقت يستغرق إنشاء تقرير؟</h4>
                    <p class="text-black text-sm">عادةً ما يستغرق إنشاء التقرير من 5-15 دقيقة حسب حجم البيانات.</p>
                </div>
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="text-black font-medium mb-2">هل يمكنني تصدير التقارير بصيغ مختلفة؟</h4>
                    <p class="text-black text-sm">نعم، يمكنك تصدير التقارير بصيغ PDF, Excel, و CSV.</p>
                </div>
                <div class="border border-gray-200 rounded-lg p-4">
                    <h4 class="text-black font-medium mb-2">كيف يمكنني تخصيص التقارير؟</h4>
                    <p class="text-black text-sm">يمكنك استخدام التقارير المخصصة لإنشاء تقارير حسب احتياجاتك.</p>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
@endsection
