@extends('admin.layouts.admin')

@section('title', 'إنشاء مطالبة جديدة')

@section('page-title', 'إنشاء مطالبة جديدة')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="bg-gradient-to-r from-red-600 via-red-700 to-pink-800 rounded-2xl shadow-xl p-8 text-white">
        <div class="flex justify-between items-center">
            <div>
                <div class="flex items-center mb-3">
                    <div class="bg-white bg-opacity-20 rounded-xl p-3 ml-4">
                        <i class="fas fa-plus-circle text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white">إنشاء مطالبة جديدة</h1>
                        <p class="text-red-100 mt-1">تقديم مطالبة ضمان جديدة</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4 mt-4">
                    <div class="bg-white bg-opacity-20 rounded-lg px-3 py-1">
                        <span class="text-sm text-red-100">النظام:</span>
                        <span class="text-sm font-semibold text-white">إدارة المطالبات</span>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg px-3 py-1">
                        <span class="text-sm text-red-100">المستخدم:</span>
                        <span class="text-sm font-semibold text-white">{{ Auth::user()->name }}</span>
                    </div>
                </div>
            </div>
            <div class="flex space-x-reverse space-x-3">
                <a href="{{ route('warranties.claims.index') }}" class="inline-flex items-center px-6 py-3 bg-white bg-opacity-20 backdrop-blur-sm border border-white border-opacity-30 rounded-xl text-sm font-medium text-white hover:bg-opacity-30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-300">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة للمطالبات
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium mb-2">إجمالي المطالبات</p>
                <p class="text-3xl font-bold text-white">{{ App\Models\WarrantyClaim::count() }}</p>
                <div class="mt-2 flex items-center text-xs text-blue-100">
                    <i class="fas fa-clipboard-list ml-1"></i>
                    <span>جميع المطالبات</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-clipboard-list text-white text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium mb-2">الضمانات النشطة</p>
                <p class="text-3xl font-bold text-white">{{ App\Models\Warranty::where('status', 'active')->count() }}</p>
                <div class="mt-2 flex items-center text-xs text-green-100">
                    <i class="fas fa-shield-alt ml-1"></i>
                    <span>متاحة للمطالبات</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-shield-alt text-white text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-yellow-100 text-sm font-medium mb-2">مطالبات معلقة</p>
                <p class="text-3xl font-bold text-white">{{ App\Models\WarrantyClaim::where('status', 'pending')->count() }}</p>
                <div class="mt-2 flex items-center text-xs text-yellow-100">
                    <i class="fas fa-clock ml-1"></i>
                    <span>في انتظار المراجعة</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-clock text-white text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm font-medium mb-2">مجموع المدفوعات</p>
                <p class="text-3xl font-bold text-white">{{ number_format(App\Models\WarrantyClaim::where('status', 'approved')->sum('amount'), 2) }}</p>
                <div class="mt-2 flex items-center text-xs text-purple-100">
                    <i class="fas fa-money-bill-wave ml-1"></i>
                    <span>ريال سعودي</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-money-bill-wave text-white text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Claim Form -->
<div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
            <div class="bg-red-500 rounded-lg p-2 ml-3">
                <i class="fas fa-edit text-white"></i>
            </div>
            نموذج المطالبة
        </h3>
    </div>
    
    <form method="POST" action="{{ route('warranties.claims.store') }}" class="p-6">
        @csrf
        
        <!-- Basic Information Section -->
        <div class="mb-8">
            <h4 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-3 ml-3 shadow-lg">
                    <i class="fas fa-info-circle text-white text-lg"></i>
                </div>
                <div>
                    <span class="text-gray-900">المعلومات الأساسية</span>
                    <p class="text-sm text-gray-500 font-normal">بيانات المطالبة الأساسية</p>
                </div>
            </h4>
            
            <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 shadow-sm border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-shield-alt text-blue-500 ml-2 text-xs"></i>
                            الضمان *
                        </label>
                        <div class="relative">
                            <select name="warranty_id" required
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 appearance-none bg-white shadow-sm transition-all duration-200 hover:shadow-md">
                                <option value="">اختر الضمان</option>
                                @foreach($warranties as $warranty)
                                    <option value="{{ $warranty->id }}">{{ $warranty->title }} - {{ $warranty->warranty_number }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-shield-alt text-blue-400"></i>
                            </div>
                        </div>
                        @error('warranty_id')
                            <p class="mt-2 text-sm text-red-600 flex items-center animate-pulse">
                                <i class="fas fa-exclamation-circle ml-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-calendar text-green-500 ml-2 text-xs"></i>
                            تاريخ المطالبة *
                        </label>
                        <div class="relative">
                            <input type="date" name="claim_date" required
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white shadow-sm transition-all duration-200 hover:shadow-md">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar text-green-400"></i>
                            </div>
                        </div>
                        @error('claim_date')
                            <p class="mt-2 text-sm text-red-600 flex items-center animate-pulse">
                                <i class="fas fa-exclamation-circle ml-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-calendar-alt text-amber-500 ml-2 text-xs"></i>
                            تاريخ الحادث
                        </label>
                        <div class="relative">
                            <input type="date" name="incident_date"
                                class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white shadow-sm transition-all duration-200 hover:shadow-md">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-calendar-alt text-amber-400"></i>
                            </div>
                        </div>
                        @error('incident_date')
                            <p class="mt-2 text-sm text-red-600 flex items-center animate-pulse">
                                <i class="fas fa-exclamation-circle ml-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <i class="fas fa-money-bill-wave text-purple-500 ml-2 text-xs"></i>
                            المبلغ *
                        </label>
                        <div class="relative">
                            <input type="number" name="amount" required min="0" step="0.01"
                                class="w-full px-4 py-3 pr-16 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white shadow-sm transition-all duration-200 hover:shadow-md"
                                placeholder="0.00">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 text-sm">ريال</span>
                            </div>
                        </div>
                        @error('amount')
                            <p class="mt-2 text-sm text-red-600 flex items-center animate-pulse">
                                <i class="fas fa-exclamation-circle ml-2"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                        <i class="fas fa-align-left text-indigo-500 ml-2 text-xs"></i>
                        وصف المطالبة *
                    </label>
                    <div class="relative">
                        <textarea name="description" required rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none bg-white shadow-sm transition-all duration-200 hover:shadow-md"
                            placeholder="أدخل وصفاً مفصلاً للمطالبة"></textarea>
                    </div>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600 flex items-center animate-pulse">
                            <i class="fas fa-exclamation-circle ml-2"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex justify-end space-x-reverse space-x-3 pt-6 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100 -mx-6 px-6 -mb-6 pb-6 rounded-b-2xl">
            <a href="{{ route('warranties.claims.index') }}" class="inline-flex items-center px-6 py-3 bg-white border border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-105">
                <i class="fas fa-times ml-2"></i>
                إلغاء
            </a>
            <button type="submit" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-red-600 to-red-700 border border-transparent rounded-xl text-sm font-medium text-white hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                <i class="fas fa-save ml-2"></i>
                حفظ المطالبة
            </button>
        </div>
    </form>
</div>

@endsection
