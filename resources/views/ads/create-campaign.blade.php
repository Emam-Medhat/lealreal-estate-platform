@extends('layouts.app')

@section('title', 'إنشاء حملة إعلانية جديدة')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-12">
    <div class="max-w-4xl mx-auto px-6">

        <!-- Header -->
        <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-8 mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <div class="flex items-center mb-4">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-full p-3 mr-4">
                            <i class="fas fa-plus text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-slate-800">إنشاء حملة إعلانية جديدة</h1>
                            <p class="text-slate-600 mt-1">أنشئ حملة إعلانية فعالة ومستهدفة</p>
                        </div>
                    </div>
                </div>
                <a href="{{ route('ads.campaigns.index') }}"
                   class="bg-white border-2 border-slate-300 text-slate-700 px-6 py-3 rounded-2xl hover:bg-slate-50 transition-colors font-medium flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    العودة للحملات
                </a>
            </div>

            <!-- Progress Indicator -->
            <div class="flex items-center justify-center space-x-4 space-x-reverse">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-bold">1</div>
                    <span class="mr-2 text-sm font-medium text-blue-600">المعلومات الأساسية</span>
                </div>
                <div class="w-12 h-0.5 bg-slate-300"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-slate-300 rounded-full flex items-center justify-center text-slate-500 text-sm font-bold">2</div>
                    <span class="mr-2 text-sm font-medium text-slate-500">الميزانية</span>
                </div>
                <div class="w-12 h-0.5 bg-slate-300"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-slate-300 rounded-full flex items-center justify-center text-slate-500 text-sm font-bold">3</div>
                    <span class="mr-2 text-sm font-medium text-slate-500">الاستهداف</span>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('ads.campaigns.store') }}" class="space-y-8">
            @csrf

            <!-- Basic Information Section -->
            <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-8">
                <div class="flex items-center mb-6">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-full p-2 mr-4">
                        <i class="fas fa-info-circle text-white"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800">المعلومات الأساسية</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Campaign Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">اسم الحملة *</label>
                        <div class="relative">
                            <input type="text" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 pl-12"
                                   id="name" name="name" required value="{{ old('name') }}" maxlength="255"
                                   placeholder="أدخل اسم الحملة الإعلانية">
                            <i class="fas fa-bullhorn absolute right-4 top-4 text-slate-400"></i>
                        </div>
                        <p class="text-sm text-slate-500 mt-1">اسم وصفي وجذاب للحملة</p>
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Campaign Objective -->
                    <div>
                        <label for="objective" class="block text-sm font-semibold text-slate-700 mb-2">هدف الحملة *</label>
                        <div class="relative">
                            <select class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 appearance-none pl-12"
                                    id="objective" name="objective" required>
                                <option value="">اختر هدف الحملة</option>
                                <option value="awareness" {{ old('objective') == 'awareness' ? 'selected' : '' }}>زيادة الوعي</option>
                                <option value="traffic" {{ old('objective') == 'traffic' ? 'selected' : '' }}>زيادة الزيارات</option>
                                <option value="conversions" {{ old('objective') == 'conversions' ? 'selected' : '' }}>زيادة التحويلات</option>
                                <option value="engagement" {{ old('objective') == 'engagement' ? 'selected' : '' }}>زيادة التفاعل</option>
                            </select>
                            <i class="fas fa-target absolute right-4 top-4 text-slate-400"></i>
                            <i class="fas fa-chevron-down absolute left-4 top-4 text-slate-400"></i>
                        </div>
                        <p class="text-sm text-slate-500 mt-1">الهدف الرئيسي للحملة</p>
                        @error('objective')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Campaign Type -->
                    <div>
                        <label for="campaign_type" class="block text-sm font-semibold text-slate-700 mb-2">نوع الحملة</label>
                        <div class="relative">
                            <select class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 appearance-none pl-12"
                                    id="campaign_type" name="campaign_type">
                                <option value="search" {{ old('campaign_type') == 'search' ? 'selected' : '' }}>بحث</option>
                                <option value="display" {{ old('campaign_type') == 'display' ? 'selected' : '' }}>عرض</option>
                                <option value="video" {{ old('campaign_type') == 'video' ? 'selected' : '' }}>فيديو</option>
                                <option value="social" {{ old('campaign_type') == 'social' ? 'selected' : '' }}>تواصل اجتماعي</option>
                            </select>
                            <i class="fas fa-ad absolute right-4 top-4 text-slate-400"></i>
                            <i class="fas fa-chevron-down absolute left-4 top-4 text-slate-400"></i>
                        </div>
                        <p class="text-sm text-slate-500 mt-1">نوع الإعلانات في الحملة</p>
                    </div>

                    <!-- Campaign Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">وصف الحملة</label>
                        <div class="relative">
                            <textarea class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 resize-none"
                                      id="description" name="description" rows="4" maxlength="500"
                                      placeholder="صف الحملة الإعلانية وأهدافها...">{{ old('description') }}</textarea>
                            <div class="absolute bottom-3 left-3 text-xs text-slate-400">
                                <span id="char-count">0</span>/500
                            </div>
                        </div>
                        <p class="text-sm text-slate-500 mt-1">وصف مفصل للحملة وأهدافها</p>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Schedule Section -->
            <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-8">
                <div class="flex items-center mb-6">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-full p-2 mr-4">
                        <i class="fas fa-calendar-alt text-white"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800">جدولة الحملة</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="block text-sm font-semibold text-slate-700 mb-2">تاريخ البدء *</label>
                        <div class="relative">
                            <input type="date" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 pl-12"
                                   id="start_date" name="start_date" required
                                   value="{{ old('start_date', now()->format('Y-m-d')) }}">
                            <i class="fas fa-calendar-plus absolute right-4 top-4 text-slate-400"></i>
                        </div>
                        @error('start_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- End Date -->
                    <div>
                        <label for="end_date" class="block text-sm font-semibold text-slate-700 mb-2">تاريخ الانتهاء *</label>
                        <div class="relative">
                            <input type="date" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 pl-12"
                                   id="end_date" name="end_date" required
                                   value="{{ old('end_date', now()->addDays(30)->format('Y-m-d')) }}">
                            <i class="fas fa-calendar-times absolute right-4 top-4 text-slate-400"></i>
                        </div>
                        @error('end_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Target Audience Size -->
                    <div>
                        <label for="target_audience_size" class="block text-sm font-semibold text-slate-700 mb-2">حجم الجمهور المستهدف</label>
                        <div class="relative">
                            <input type="number" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 pl-12"
                                   id="target_audience_size" name="target_audience_size" min="1"
                                   value="{{ old('target_audience_size') }}" placeholder="10000">
                            <i class="fas fa-users absolute right-4 top-4 text-slate-400"></i>
                        </div>
                        <p class="text-sm text-slate-500 mt-1">عدد المستخدمين المستهدفين</p>
                    </div>

                    <!-- Estimated Reach -->
                    <div>
                        <label for="estimated_reach" class="block text-sm font-semibold text-slate-700 mb-2">الوصول المقدر</label>
                        <div class="relative">
                            <input type="number" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 pl-12"
                                   id="estimated_reach" name="estimated_reach" min="1"
                                   value="{{ old('estimated_reach') }}" placeholder="50000">
                            <i class="fas fa-bullseye absolute right-4 top-4 text-slate-400"></i>
                        </div>
                        <p class="text-sm text-slate-500 mt-1">عدد المستخدمين المتوقع الوصول إليهم</p>
                    </div>
                </div>
            </div>

            <!-- Budget Section -->
            <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-8">
                <div class="flex items-center mb-6">
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-full p-2 mr-4">
                        <i class="fas fa-money-bill-wave text-white"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800">الميزانية والتكلفة</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Daily Budget -->
                    <div>
                        <label for="daily_budget" class="block text-sm font-semibold text-slate-700 mb-2">الميزانية اليومية *</label>
                        <div class="relative">
                            <input type="number" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 pl-12 pr-16"
                                   id="daily_budget" name="daily_budget" step="0.01" min="0.01" required
                                   value="{{ old('daily_budget') }}" placeholder="50.00">
                            <span class="absolute right-4 top-3.5 text-slate-500 font-medium">ريال</span>
                            <i class="fas fa-coins absolute left-4 top-4 text-slate-400"></i>
                        </div>
                        <p class="text-sm text-slate-500 mt-1">المبلغ المخصص يومياً للحملة</p>
                        @error('daily_budget')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Total Budget -->
                    <div>
                        <label for="total_budget" class="block text-sm font-semibold text-slate-700 mb-2">الميزانية الإجمالية</label>
                        <div class="relative">
                            <input type="number" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 pl-12 pr-16 bg-slate-50"
                                   id="total_budget" name="total_budget" step="0.01" min="0" readonly
                                   value="{{ old('total_budget') }}" placeholder="حساب تلقائي">
                            <span class="absolute right-4 top-3.5 text-slate-500 font-medium">ريال</span>
                            <i class="fas fa-calculator absolute left-4 top-4 text-slate-400"></i>
                        </div>
                        <p class="text-sm text-slate-500 mt-1">المبلغ الإجمالي المحسوب تلقائياً</p>
                    </div>
                </div>

                <!-- Budget Summary -->
                <div class="mt-6 p-4 bg-gradient-to-r from-slate-50 to-slate-100 rounded-2xl border border-slate-200">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600">فترة الحملة:</span>
                        <span class="font-semibold text-slate-800" id="campaign-duration">-- يوم</span>
                    </div>
                    <div class="flex items-center justify-between text-sm mt-2">
                        <span class="text-slate-600">التكلفة المتوقعة:</span>
                        <span class="font-semibold text-slate-800" id="estimated-cost">-- ريال</span>
                    </div>
                </div>
            </div>

            <!-- Submit Section -->
            <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-8">
                <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
                    <div class="text-center sm:text-right">
                        <h3 class="text-lg font-semibold text-slate-800 mb-1">جاهز لإنشاء الحملة؟</h3>
                        <p class="text-slate-600 text-sm">تأكد من مراجعة جميع البيانات قبل الإنشاء</p>
                    </div>

                    <div class="flex gap-4">
                        <a href="{{ route('ads.campaigns.index') }}"
                           class="px-8 py-3 border-2 border-slate-300 text-slate-700 rounded-2xl hover:bg-slate-50 transition-colors font-medium">
                            إلغاء
                        </a>
                        <button type="submit"
                                class="px-8 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-2xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 font-medium">
                            <i class="fas fa-rocket mr-2"></i>
                            إنشاء الحملة
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
/* Custom scrollbar */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Focus styles enhancement */
input:focus, select:focus, textarea:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Custom animations */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-slide-in {
    animation: slideInUp 0.5s ease-out;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Character count for description
    const descriptionTextarea = document.getElementById('description');
    const charCount = document.getElementById('char-count');

    if (descriptionTextarea && charCount) {
        descriptionTextarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });

        // Initial count
        charCount.textContent = descriptionTextarea.value.length;
    }

    // Auto calculate total budget
    const dailyBudgetInput = document.getElementById('daily_budget');
    const totalBudgetInput = document.getElementById('total_budget');
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    const campaignDuration = document.getElementById('campaign-duration');
    const estimatedCost = document.getElementById('estimated-cost');

    function calculateBudget() {
        const dailyBudget = parseFloat(dailyBudgetInput.value) || 0;
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);

        if (startDate && endDate && dailyBudget > 0) {
            const diffTime = Math.abs(endDate - startDate);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
            const totalBudget = dailyBudget * diffDays;

            totalBudgetInput.value = totalBudget.toFixed(2);
            campaignDuration.textContent = diffDays + ' يوم';
            estimatedCost.textContent = totalBudget.toFixed(2) + ' ريال';
        }
    }

    // Attach event listeners
    if (dailyBudgetInput) dailyBudgetInput.addEventListener('input', calculateBudget);
    if (startDateInput) startDateInput.addEventListener('change', calculateBudget);
    if (endDateInput) endDateInput.addEventListener('change', calculateBudget);

    // Initial calculation
    calculateBudget();

    // Form validation enhancement
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('border-red-500');
                isValid = false;
            } else {
                field.classList.remove('border-red-500');
            }
        });

        if (!isValid) {
            e.preventDefault();
            alert('يرجى ملء جميع الحقول المطلوبة');
        }
    });

    // Animate sections on load
    const sections = document.querySelectorAll('form > div');
    sections.forEach((section, index) => {
        section.style.opacity = '0';
        section.style.transform = 'translateY(20px)';
        setTimeout(() => {
            section.style.transition = 'all 0.5s ease-out';
            section.style.opacity = '1';
            section.style.transform = 'translateY(0)';
        }, index * 100);
    });
});
</script>
@endsection
