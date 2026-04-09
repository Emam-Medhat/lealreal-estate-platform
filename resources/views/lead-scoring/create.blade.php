@extends('admin.layouts.admin')

@section('title', 'تقييم عميل جديد')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="container mx-auto px-4">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2 bg-gradient-to-r from-purple-600 to-blue-600 bg-clip-text text-transparent">
                    <i class="fas fa-star text-purple-600 mr-3"></i>
                    تقييم عميل جديد
                </h1>
                <p class="text-gray-600 text-lg">إضافة تقييم شامل لعميل محتمل</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('lead-scoring.index') }}" 
                   class="px-6 py-3 bg-gradient-to-r from-gray-200 to-gray-300 text-gray-700 rounded-xl hover:from-gray-300 hover:to-gray-400 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-xl">
                    <form action="{{ route('lead-scoring.store') }}" method="POST">
                        @csrf
                        
                        <div class="space-y-8">
                            <!-- Lead Selection -->
                            <div class="bg-gradient-to-r from-purple-50 to-blue-50 rounded-xl p-6 border-2 border-purple-200 shadow-lg">
                                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                    <i class="fas fa-user text-purple-600 mr-3"></i>
                                    اختيار العميل
                                </h2>
                                <div>
                                    <label for="lead_id" class="block text-sm font-semibold text-gray-700 mb-3">العميل المحتمل</label>
                                    <select name="lead_id" id="lead_id" 
                                            class="w-full rounded-xl border-2 border-purple-300 focus:border-purple-500 focus:ring-purple-500 bg-white shadow-lg text-lg font-medium transition-all duration-300" 
                                            required onchange="updateLeadInfo()">
                                        <option value="">اختر العميل...</option>
                                        @foreach($leads as $lead)
                                            <option value="{{ $lead->id }}" 
                                                    data-email="{{ $lead->email ?? '' }}"
                                                    data-phone="{{ $lead->phone ?? '' }}"
                                                    data-company="{{ $lead->company ?? '' }}"
                                                    data-budget="{{ $lead->budget_max ?? '' }}"
                                                    data-source="{{ $lead->lead_source ?? '' }}"
                                                    data-status="{{ $lead->lead_status ?? '' }}"
                                                    class="py-3 hover:bg-purple-50">
                                                {{ $lead->first_name }} {{ $lead->last_name }} ({{ $lead->email ?? 'بدون بريد إلكتروني' }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('lead_id')
                                        <p class="mt-2 text-sm text-red-600 font-medium bg-red-50 p-3 rounded-lg border border-red-200">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Lead Info Display -->
                            <div id="lead_info" class="hidden bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border-2 border-blue-200 shadow-lg">
                                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                    <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                                    معلومات العميل
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-md">
                                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                            <i class="fas fa-envelope text-blue-500 mr-2"></i>
                                            معلومات الاتصال
                                        </h4>
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm font-medium text-gray-700">البريد الإلكتروني:</span>
                                                <span id="info_email" class="text-sm font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-lg">-</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm font-medium text-gray-700">الهاتف:</span>
                                                <span id="info_phone" class="text-sm font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-lg">-</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-md">
                                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                            <i class="fas fa-building text-green-500 mr-2"></i>
                                            معلومات الشركة
                                        </h4>
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm font-medium text-gray-700">الشركة:</span>
                                                <span id="info_company" class="text-sm font-bold text-green-600 bg-green-50 px-3 py-1 rounded-lg">-</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm font-medium text-gray-700">الميزانية:</span>
                                                <span id="info_budget" class="text-sm font-bold text-green-600 bg-green-50 px-3 py-1 rounded-lg">-</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-md">
                                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                            <i class="fas fa-chart-line text-orange-500 mr-2"></i>
                                            معلومات إضافية
                                        </h4>
                                        <div class="space-y-3">
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm font-medium text-gray-700">المصدر:</span>
                                                <span id="info_source" class="text-sm font-bold text-orange-600 bg-orange-50 px-3 py-1 rounded-lg">-</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm font-medium text-gray-700">الحالة:</span>
                                                <span id="info_status" class="text-sm font-bold text-orange-600 bg-orange-50 px-3 py-1 rounded-lg">-</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Scoring Categories -->
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-200">
                                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                    <i class="fas fa-chart-line text-green-600 mr-3"></i>
                                    فئات التقييم
                                </h2>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Engagement Score -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">مستوى التفاعل (0-25)</label>
                                        <div class="flex items-center space-x-3">
                                            <input type="range" name="engagement_score" id="engagement_range" min="0" max="25" value="0" 
                                                   class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                                                   oninput="updateScoreDisplay('engagement', this.value)">
                                            <input type="number" name="engagement_score" id="engagement_score" min="0" max="25" value="0" 
                                                   class="w-16 rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 text-center text-sm"
                                                   oninput="updateScoreDisplay('engagement', this.value)">
                                        </div>
                                        <div class="mt-2 text-xs text-gray-500">البريد الإلكتروني، المكالمات، الزيارات</div>
                                    </div>

                                    <!-- Budget Score -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">الميزانية (0-25)</label>
                                        <div class="flex items-center space-x-3">
                                            <input type="range" name="budget_score" id="budget_range" min="0" max="25" value="0" 
                                                   class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                                                   oninput="updateScoreDisplay('budget', this.value)">
                                            <input type="number" name="budget_score" id="budget_score" min="0" max="25" value="0" 
                                                   class="w-16 rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 text-center text-sm"
                                                   oninput="updateScoreDisplay('budget', this.value)">
                                        </div>
                                        <div class="mt-2 text-xs text-gray-500">مطابقة الميزانية مع الأسعار</div>
                                    </div>

                                    <!-- Timeline Score -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">الجدول الزمني (0-25)</label>
                                        <div class="flex items-center space-x-3">
                                            <input type="range" name="timeline_score" id="timeline_range" min="0" max="25" value="0" 
                                                   class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                                                   oninput="updateScoreDisplay('timeline', this.value)">
                                            <input type="number" name="timeline_score" id="timeline_score" min="0" max="25" value="0" 
                                                   class="w-16 rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 text-center text-sm"
                                                   oninput="updateScoreDisplay('timeline', this.value)">
                                        </div>
                                        <div class="mt-2 text-xs text-gray-500">استعداد للشراء في الوقت المناسب</div>
                                    </div>

                                    <!-- Qualification Score -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">التأهيل (0-25)</label>
                                        <div class="flex items-center space-x-3">
                                            <input type="range" name="qualification_score" id="qualification_range" min="0" max="25" value="0" 
                                                   class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                                                   oninput="updateScoreDisplay('qualification', this.value)">
                                            <input type="number" name="qualification_score" id="qualification_score" min="0" max="25" value="0" 
                                                   class="w-16 rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 text-center text-sm"
                                                   oninput="updateScoreDisplay('qualification', this.value)">
                                        </div>
                                        <div class="mt-2 text-xs text-gray-500">القدرة المالية والقرار</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Score Display -->
                            <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-xl p-6 text-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-lg font-bold mb-1">التقييم الإجمالي</h3>
                                        <p class="text-sm opacity-90">مجموع جميع الفئات</p>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-4xl font-bold" id="total_score">0</div>
                                        <div class="text-sm opacity-90">من 100</div>
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <div class="w-full bg-white/20 rounded-full h-3">
                                        <div id="score_progress" class="bg-white rounded-full h-3 transition-all duration-300" style="width: 0%"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Factors -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">عوامل التقييم التفصيلية</label>
                                <div id="factors_container" class="space-y-3">
                                    <div class="flex items-center space-x-3 bg-gray-50 rounded-lg p-3">
                                        <i class="fas fa-tag text-gray-400"></i>
                                        <input type="text" name="factors[]" placeholder="عامل التقييم (مثلاً: اكتمال الملف الشخصي)" 
                                               class="flex-1 rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 bg-white">
                                        <button type="button" onclick="removeFactor(this)" 
                                                class="text-red-500 hover:text-red-700 p-2 hover:bg-red-50 rounded-lg transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="button" onclick="addFactor()" 
                                        class="mt-3 text-sm text-purple-600 hover:text-purple-700 font-medium flex items-center px-4 py-2 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                                    <i class="fas fa-plus ml-2"></i>
                                    إضافة عامل آخر
                                </button>
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">ملاحظات إضافية</label>
                                <textarea name="notes" id="notes" rows="4" 
                                          class="w-full rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500" 
                                          placeholder="أي ملاحظات حول هذا التقييم..."></textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-end space-x-3 space-x-reverse">
                            <button type="button" onclick="window.history.back()" 
                                    class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                                إلغاء
                            </button>
                            <button type="submit" 
                                    class="px-8 py-3 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-xl hover:from-purple-700 hover:to-blue-700 font-medium transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                                <i class="fas fa-save ml-2"></i>
                                حفظ التقييم
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Scoring Guide -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xl mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-question-circle text-blue-600 mr-3"></i>
                        دليل التقييم
                    </h3>
                    <div class="space-y-4">
                        <div class="border-r-4 border-green-500 pr-3">
                            <div class="font-semibold text-green-700">80-100 ممتاز</div>
                            <div class="text-sm text-gray-600">عميل جاهز للشراء فوراً</div>
                        </div>
                        <div class="border-r-4 border-blue-500 pr-3">
                            <div class="font-semibold text-blue-700">60-79 جيد جداً</div>
                            <div class="text-sm text-gray-600">عميل مؤهل ومتحمس</div>
                        </div>
                        <div class="border-r-4 border-yellow-500 pr-3">
                            <div class="font-semibold text-yellow-700">40-59 متوسط</div>
                            <div class="text-sm text-gray-600">يحتاج متابعة إضافية</div>
                        </div>
                        <div class="border-r-4 border-red-500 pr-3">
                            <div class="font-semibold text-red-700">0-39 ضعيف</div>
                            <div class="text-sm text-gray-600">يحتاج تأهيل طويل</div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-xl">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-chart-bar text-purple-600 mr-3"></i>
                        إحصائيات سريعة
                    </h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">إجمالي العملاء:</span>
                            <span class="font-semibold text-gray-900">{{ count($leads) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">عملاء بدون تقييم:</span>
                            <span class="font-semibold text-orange-600">{{ count($leads) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">متوسط التقييم:</span>
                            <span class="font-semibold text-green-600">--</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function updateLeadInfo() {
        const select = document.getElementById('lead_id');
        const infoDiv = document.getElementById('lead_info');
        const option = select.options[select.selectedIndex];
        
        if (select.value) {
            // Show lead info section
            infoDiv.classList.remove('hidden');
            
            // Update lead info
            document.getElementById('info_email').textContent = option.dataset.email || '-';
            document.getElementById('info_phone').textContent = option.dataset.phone || '-';
            document.getElementById('info_company').textContent = option.dataset.company || '-';
            document.getElementById('info_budget').textContent = option.dataset.budget ? 
                new Intl.NumberFormat('ar-SA', { style: 'currency', currency: 'SAR' }).format(option.dataset.budget) : '-';
        } else {
            // Hide lead info section
            infoDiv.classList.add('hidden');
        }
    }

    function updateScoreDisplay(category, value) {
        // Update both range and number inputs
        document.getElementById(category + '_range').value = value;
        document.getElementById(category + '_score').value = value;
        
        // Calculate total score
        calculateTotalScore();
    }

    function calculateTotalScore() {
        const engagement = parseInt(document.getElementById('engagement_score').value) || 0;
        const budget = parseInt(document.getElementById('budget_score').value) || 0;
        const timeline = parseInt(document.getElementById('timeline_score').value) || 0;
        const qualification = parseInt(document.getElementById('qualification_score').value) || 0;
        
        const total = engagement + budget + timeline + qualification;
        
        // Update total score display
        document.getElementById('total_score').textContent = total;
        
        // Update progress bar
        const progressBar = document.getElementById('score_progress');
        progressBar.style.width = total + '%';
        
        // Update progress bar color based on score
        if (total >= 80) {
            progressBar.className = 'bg-green-400 rounded-full h-3 transition-all duration-300';
        } else if (total >= 60) {
            progressBar.className = 'bg-blue-400 rounded-full h-3 transition-all duration-300';
        } else if (total >= 40) {
            progressBar.className = 'bg-yellow-400 rounded-full h-3 transition-all duration-300';
        } else {
            progressBar.className = 'bg-red-400 rounded-full h-3 transition-all duration-300';
        }
    }

    function addFactor() {
        const container = document.getElementById('factors_container');
        const div = document.createElement('div');
        div.className = 'flex items-center space-x-3 bg-gray-50 rounded-lg p-3';
        div.innerHTML = `
            <i class="fas fa-tag text-gray-400"></i>
            <input type="text" name="factors[]" placeholder="عامل التقييم" 
                   class="flex-1 rounded-lg border-gray-300 focus:border-purple-500 focus:ring-purple-500 bg-white">
            <button type="button" onclick="removeFactor(this)" 
                    class="text-red-500 hover:text-red-700 p-2 hover:bg-red-50 rounded-lg transition-colors">
                <i class="fas fa-trash"></i>
            </button>
        `;
        container.appendChild(div);
    }

    function removeFactor(btn) {
        const container = document.getElementById('factors_container');
        if (container.children.length > 1) {
            btn.closest('div').remove();
        } else {
            // Clear input instead of removing last one
            btn.previousElementSibling.value = '';
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        calculateTotalScore();
    });
</script>
@endpush
@endsection
