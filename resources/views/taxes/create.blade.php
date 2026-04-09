@extends('layouts.app')

@section('title', 'إضافة ضريبة جديدة')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-blue-50 to-indigo-100">
    <!-- Animated Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-20 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
        <div class="absolute top-40 right-20 w-96 h-96 bg-blue-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse animation-delay-2000"></div>
        <div class="absolute bottom-20 left-1/2 w-80 h-80 bg-indigo-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse animation-delay-4000"></div>
    </div>

    <div class="relative z-10 container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl p-8 mb-8 border border-white/20">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent mb-3">
                            <i class="fas fa-plus-circle ml-3"></i>
                            إضافة ضريبة جديدة
                        </h1>
                        <p class="text-gray-600 text-lg">إضافة ضريبة جديدة إلى النظام</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('taxes.index') }}" class="bg-white/80 backdrop-blur text-gray-700 px-6 py-3 rounded-xl hover:bg-white transition-all duration-300 shadow-md border border-gray-200">
                            <i class="fas fa-arrow-left ml-2"></i>
                            العودة
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white/90 backdrop-blur-lg rounded-2xl shadow-xl border border-white/20 p-8">
                <form method="POST" action="{{ route('taxes.store') }}" class="space-y-6">
                    @csrf

                    <!-- Tax Name -->
                    <div>
                        <label for="name" class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-tag ml-2 text-purple-600"></i>
                            اسم الضريبة
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-700 transition-all duration-300"
                               placeholder="أدخل اسم الضريبة">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tax Type -->
                    <div>
                        <label for="tax_type" class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-list ml-2 text-purple-600"></i>
                            نوع الضريبة
                        </label>
                        <select id="tax_type" 
                                name="tax_type" 
                                required
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-700 transition-all duration-300">
                            <option value="">اختر نوع الضريبة</option>
                            <option value="property">ضريبة عقارية</option>
                            <option value="income">ضريبة الدخل</option>
                            <option value="capital_gains">ضريبة أرباح رأس المال</option>
                            <option value="vat">ضريبة القيمة المضافة</option>
                            <option value="stamp_duty">ضريبة الدمغة</option>
                            <option value="municipality">ضريبة بلدية</option>
                            <option value="other">ضريبة أخرى</option>
                        </select>
                        @error('tax_type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tax Rate -->
                    <div>
                        <label for="rate" class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-percentage ml-2 text-purple-600"></i>
                            نسبة الضريبة (%)
                        </label>
                        <input type="number" 
                               id="rate" 
                               name="rate" 
                               required
                               step="0.01"
                               min="0"
                               max="100"
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-700 transition-all duration-300"
                               placeholder="أدخل نسبة الضريبة">
                        @error('rate')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-align-left ml-2 text-purple-600"></i>
                            الوصف
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-700 transition-all duration-300"
                                  placeholder="أدخل وصف الضريبة"></textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Effective Date -->
                    <div>
                        <label for="effective_date" class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-calendar ml-2 text-purple-600"></i>
                            تاريخ البدء
                        </label>
                        <input type="date" 
                               id="effective_date" 
                               name="effective_date" 
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white text-gray-700 transition-all duration-300">
                        @error('effective_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-toggle-on ml-2 text-purple-600"></i>
                            الحالة
                        </label>
                        <div class="flex items-center space-x-6 space-x-reverse">
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" 
                                       name="is_active" 
                                       value="1" 
                                       checked
                                       class="ml-2 text-purple-600 focus:ring-purple-500">
                                <span class="text-gray-700">نشط</span>
                            </label>
                            <label class="flex items-center cursor-pointer">
                                <input type="radio" 
                                       name="is_active" 
                                       value="0"
                                       class="ml-2 text-purple-600 focus:ring-purple-500">
                                <span class="text-gray-700">غير نشط</span>
                            </label>
                        </div>
                        @error('is_active')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="flex justify-end space-x-3 space-x-reverse pt-6 border-t border-gray-200">
                        <a href="{{ route('taxes.index') }}" class="px-6 py-3 bg-gray-500 text-white rounded-xl hover:bg-gray-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-times ml-2"></i>
                            إلغاء
                        </a>
                        <button type="submit" class="bg-gradient-to-r from-purple-600 to-blue-600 text-white px-8 py-3 rounded-xl hover:from-purple-700 hover:to-blue-700 transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fas fa-save ml-2"></i>
                            حفظ الضريبة
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.3s ease-out;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

.backdrop-blur-lg {
    backdrop-filter: blur(16px);
}

.backdrop-blur-sm {
    backdrop-filter: blur(4px);
}
</style>
@endsection
