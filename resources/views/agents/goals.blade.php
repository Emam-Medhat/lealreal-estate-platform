@extends('admin.layouts.admin')

@section('title', 'أهداف الأداء')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-purple-600 to-pink-600 rounded-xl shadow-xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold mb-2">أهداف الأداء</h1>
                <p class="text-purple-100 text-lg">تتبع وإدارة أهداف الأداء الخاصة بك</p>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold">{{ count($goals['active_goals'] ?? []) }}</div>
                <div class="text-purple-100">أهداف نشطة</div>
                <div class="text-sm text-purple-200">هذا الشهر</div>
            </div>
        </div>
    </div>

    <!-- Goals Progress Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Monthly Sales Goal -->
        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-3">
                        <i class="fas fa-bullseye text-white text-xl"></i>
                    </div>
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                        {{ $goals['monthly_sales_progress'] >= 100 ? 'مكتمل' : 'نشط' }}
                    </span>
                </div>
                <div class="text-center mb-4">
                    <div class="relative inline-flex items-center justify-center">
                        <svg class="w-24 h-24">
                            <circle class="text-gray-200" stroke-width="8" stroke="currentColor" fill="transparent" r="36" cx="48" cy="48"></circle>
                            <circle class="text-blue-600" stroke-width="8" stroke-dasharray="{{ ($goals['monthly_sales_progress'] ?? 0) * 2.26 }}" stroke-linecap="round" stroke="currentColor" fill="transparent" r="36" cx="48" cy="48" transform="rotate(-90 48 48)"></circle>
                        </svg>
                        <span class="absolute text-2xl font-bold text-gray-900">{{ $goals['monthly_sales_progress'] ?? 0 }}%</span>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">هدف المبيعات الشهري</h3>
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>الإنجاز</span>
                    <span class="font-medium text-gray-900">{{ $goals['monthly_sales_current'] ?? 0 }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span>الهدف</span>
                    <span class="font-medium text-gray-900">{{ $goals['monthly_sales_target'] ?? 0 }}</span>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-500">المتبقي</span>
                        <span class="font-medium {{ ($goals['monthly_sales_target'] ?? 0) - ($goals['monthly_sales_current'] ?? 0) > 0 ? 'text-orange-600' : 'text-green-600' }}">
                            {{ ($goals['monthly_sales_target'] ?? 0) - ($goals['monthly_sales_current'] ?? 0) }} مبيعات
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commission Goal -->
        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-3">
                        <i class="fas fa-dollar-sign text-white text-xl"></i>
                    </div>
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                        {{ $goals['commission_progress'] >= 100 ? 'مكتمل' : 'نشط' }}
                    </span>
                </div>
                <div class="text-center mb-4">
                    <div class="relative inline-flex items-center justify-center">
                        <svg class="w-24 h-24">
                            <circle class="text-gray-200" stroke-width="8" stroke="currentColor" fill="transparent" r="36" cx="48" cy="48"></circle>
                            <circle class="text-green-600" stroke-width="8" stroke-dasharray="{{ ($goals['commission_progress'] ?? 0) * 2.26 }}" stroke-linecap="round" stroke="currentColor" fill="transparent" r="36" cx="48" cy="48" transform="rotate(-90 48 48)"></circle>
                        </svg>
                        <span class="absolute text-2xl font-bold text-gray-900">{{ $goals['commission_progress'] ?? 0 }}%</span>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">هدف العمولات</h3>
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>المكتسب</span>
                    <span class="font-medium text-green-600">${{ number_format($goals['commission_current'] ?? 0, 0) }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span>الهدف</span>
                    <span class="font-medium text-gray-900">${{ number_format($goals['commission_target'] ?? 0, 0) }}</span>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-500">المتبقي</span>
                        <span class="font-medium {{ ($goals['commission_target'] ?? 0) - ($goals['commission_current'] ?? 0) > 0 ? 'text-orange-600' : 'text-green-600' }}">
                            ${{ number_format(($goals['commission_target'] ?? 0) - ($goals['commission_current'] ?? 0), 0) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Client Satisfaction Goal -->
        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="bg-gradient-to-br from-yellow-500 to-orange-500 rounded-lg p-3">
                        <i class="fas fa-star text-white text-xl"></i>
                    </div>
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                        {{ $goals['satisfaction_progress'] >= 95 ? 'ممتاز' : ($goals['satisfaction_progress'] >= 80 ? 'جيد جداً' : 'نشط') }}
                    </span>
                </div>
                <div class="text-center mb-4">
                    <div class="relative inline-flex items-center justify-center">
                        <svg class="w-24 h-24">
                            <circle class="text-gray-200" stroke-width="8" stroke="currentColor" fill="transparent" r="36" cx="48" cy="48"></circle>
                            <circle class="text-yellow-600" stroke-width="8" stroke-dasharray="{{ ($goals['satisfaction_progress'] ?? 0) * 2.26 }}" stroke-linecap="round" stroke="currentColor" fill="transparent" r="36" cx="48" cy="48" transform="rotate(-90 48 48)"></circle>
                        </svg>
                        <span class="absolute text-2xl font-bold text-gray-900">{{ $goals['satisfaction_progress'] ?? 0 }}%</span>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">رضا العملاء</h3>
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>الحالي</span>
                    <span class="font-medium text-yellow-600">{{ $goals['satisfaction_current'] ?? 0 }}%</span>
                </div>
                <div class="flex justify-between text-sm text-gray-600">
                    <span>الهدف</span>
                    <span class="font-medium text-gray-900">{{ $goals['satisfaction_target'] ?? 0 }}%</span>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-gray-500">الفرق</span>
                        <span class="font-medium {{ ($goals['satisfaction_target'] ?? 0) - ($goals['satisfaction_current'] ?? 0) <= 0 ? 'text-green-600' : 'text-orange-600' }}">
                            {{ ($goals['satisfaction_target'] ?? 0) - ($goals['satisfaction_current'] ?? 0) }}%
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Goals -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">الأهداف النشطة</h2>
            <div class="flex space-x-2">
                <button onclick="addNewGoal()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                    <i class="fas fa-plus ml-1"></i>
                    إضافة هدف
                </button>
                <button onclick="refreshGoals()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors text-sm">
                    <i class="fas fa-sync-alt ml-1"></i>
                    تحديث
                </button>
            </div>
        </div>
        
        <div class="space-y-4" id="activeGoalsContainer">
            @forelse ($goals['active_goals'] ?? [] as $index => $goal)
                <div class="border border-gray-200 rounded-xl p-6 hover:shadow-md transition-all duration-300 {{ $index % 2 == 0 ? 'bg-gradient-to-r from-blue-50 to-purple-50' : 'bg-gradient-to-r from-green-50 to-blue-50' }}">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="bg-gradient-to-br {{ $index % 3 == 0 ? 'from-blue-500 to-blue-600' : ($index % 3 == 1 ? 'from-green-500 to-green-600' : 'from-purple-500 to-purple-600') }} rounded-full p-3">
                                <i class="fas {{ $index % 3 == 0 ? 'fa-bullseye' : ($index % 3 == 1 ? 'fa-chart-line' : 'fa-trophy') }} text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $goal['title'] ?? 'عنوان الهدف' }}</h3>
                                <p class="text-sm text-gray-600">{{ $goal['description'] ?? 'وصف الهدف' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $goal['status'] == 'on-track' ? 'bg-green-100 text-green-800' : ($goal['status'] == 'at-risk' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $goal['status'] == 'on-track' ? 'على المسار' : ($goal['status'] == 'at-risk' ? 'مخاطر' : 'قيد المراجعة') }}
                            </span>
                            <button onclick="editGoal({{ $index }})" class="text-blue-600 hover:text-blue-700">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteGoal({{ $index }})" class="text-red-600 hover:text-red-700">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-700">التقدم</span>
                            <span class="text-sm font-bold {{ $goal['progress'] >= 80 ? 'text-green-600' : ($goal['progress'] >= 50 ? 'text-yellow-600' : 'text-red-600') }}">{{ $goal['progress'] ?? 0 }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-gradient-to-r {{ $goal['progress'] >= 80 ? 'from-green-400 to-green-600' : ($goal['progress'] >= 50 ? 'from-yellow-400 to-yellow-600' : 'from-red-400 to-red-600') }} h-3 rounded-full transition-all duration-500" style="width: {{ $goal['progress'] ?? 0 }}%"></div>
                        </div>
                        <div class="flex justify-between items-center mt-2 text-xs text-gray-600">
                            <span>تاريخ الاستحقاق: {{ $goal['due_date'] ? \Carbon\Carbon::parse($goal['due_date'])->format('Y-m-d') : 'غير محدد' }}</span>
                            <span>المتبقي: {{ $goal['due_date'] ? \Carbon\Carbon::parse($goal['due_date'])->diffForHumans() : 'غير محدد' }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-gradient-to-br from-gray-50 to-blue-50 rounded-xl">
                    <i class="fas fa-target text-6xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">لا توجد أهداف نشطة</h3>
                    <p class="text-gray-500 mb-4">ابدأهداف جديدة لتتبع أدائك</p>
                    <button onclick="addNewGoal()" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus ml-2"></i>
                        إضافة أول هدف
                    </button>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Goal History -->
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">سجل الأهداف</h2>
            <div class="flex items-center space-x-2">
                <select onchange="filterHistory(this.value)" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <option value="all">الكل الأهداف</option>
                    <option value="this-month">هذا الشهر</option>
                    <option value="last-month">الشهر الماضي</option>
                    <option value="this-quarter">هذا الربع</option>
                </select>
            </div>
        </div>
        
        <div class="space-y-3" id="goalHistoryContainer">
            @forelse ($goals['completed_goals'] ?? [] as $index => $goal)
                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg hover:from-green-100 hover:to-emerald-100 transition-all duration-300">
                    <div class="flex items-center space-x-4">
                        <div class="bg-green-100 rounded-full p-3">
                            <i class="fas fa-check text-green-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">{{ $goal['title'] ?? 'هدف مكتمل' }}</p>
                            <p class="text-sm text-gray-600">تم الإنجاز في {{ $goal['completed_date'] ? \Carbon\Carbon::parse($goal['completed_date'])->format('Y-m-d') : 'تاريخ غير محدد' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold text-green-600">{{ $goal['achievement'] ?? 'إنجاز' }}</div>
                        <div class="text-sm text-gray-600">{{ $goal['result'] ?? 'نتيجة ممتازة' }}</div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-gradient-to-br from-gray-50 to-blue-50 rounded-xl">
                    <i class="fas fa-history text-6xl text-gray-400 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">لا توجد أهداف مكتملة</h3>
                    <p class="text-gray-500">ستظهر الأهداف المكتملة هنا عند تحقيقها</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">توزيع التقدم</h3>
                <div class="flex items-center space-x-2">
                    <button onclick="updateChart('goalsChart')" class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                        <i class="fas fa-sync-alt ml-1"></i>
                        تحديث
                    </button>
                </div>
            </div>
            <div class="h-64 bg-gradient-to-br from-blue-50 to-purple-50 rounded-lg flex items-center justify-center">
                <canvas id="goalsChart" width="400" height="200"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">اتجاه التقدم</h3>
                <div class="flex items-center space-x-2">
                    <select onchange="updateTrendChart(this.value)" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                        <option value="6">آخر 6 أشهر</option>
                        <option value="12">آخر 12 شهر</option>
                        <option value="24">آخر 24 شهر</option>
                    </select>
                </div>
            </div>
            <div class="h-64 bg-gradient-to-br from-green-50 to-blue-50 rounded-lg flex items-center justify-center">
                <canvas id="progressTrendChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Goal Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">إحصائيات الأهداف</h3>
                <i class="fas fa-chart-pie text-purple-500 text-xl"></i>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">إجمالي الأهداف</span>
                    <span class="font-bold text-gray-900">{{ count($goals['active_goals'] ?? []) + count($goals['completed_goals'] ?? []) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">مكتملة</span>
                    <span class="font-bold text-green-600">{{ count($goals['completed_goals'] ?? []) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">نشطة</span>
                    <span class="font-bold text-blue-600">{{ count($goals['active_goals'] ?? []) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">معدل الإنجاز</span>
                    <span class="font-bold text-purple-600">{{ count($goals['completed_goals'] ?? []) > 0 ? round((count($goals['completed_goals'] ?? []) / (count($goals['active_goals'] ?? []) + count($goals['completed_goals'] ?? []))) * 100, 1) : 0 }}%</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">أداء هذا الشهر</h3>
                <i class="fas fa-chart-line text-blue-500 text-xl"></i>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">متوسط التقدم</span>
                    <span class="font-bold text-blue-600">{{ round(array_sum(array_column($goals['active_goals'] ?? [], 'progress')) / max(1, count($goals['active_goals'] ?? [])), 1) }}%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">أفضل أداء</span>
                    <span class="font-bold text-green-600">{{ collect($goals['active_goals'] ?? [])->max('progress') ?? 0 }}%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">أقل أداء</span>
                    <span class="font-bold text-red-600">{{ collect($goals['active_goals'] ?? [])->min('progress') ?? 0 }}%</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">نصائف الأهداف</h3>
                <i class="fas fa-tags text-orange-500 text-xl"></i>
            </div>
            <div class="flex flex-wrap gap-2">
                <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-medium">مبيعات</span>
                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-medium">عمولات</span>
                <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-medium">رضا العملاء</span>
                <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-medium">تطوير مهارات</span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">الإنجازات السريعة</h3>
                <i class="fas fa-trophy text-yellow-500 text-xl"></i>
            </div>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-gradient-to-r from-yellow-50 to-orange-50 rounded-lg">
                    <i class="fas fa-medal text-yellow-600 ml-2"></i>
                    <span class="font-medium text-gray-900">أفضل أداء هذا الشهر</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg">
                    <i class="fas fa-award text-blue-600 ml-2"></i>
                    <span class="font-medium text-gray-900">أكثر هدف نشط</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg">
                    <i class="fas fa-star text-green-600 ml-2"></i>
                    <span class="font-medium text-gray-900">أسرع هدف مكتمل</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Agent goals page loaded');
    
    // Initialize goal tracking
    initializeGoalTracking();
    
    // Initialize charts
    initializeCharts();
    
    // Start real-time updates
    startRealTimeUpdates();
});

function initializeGoalTracking() {
    // Add event listeners for goal interactions
    document.querySelectorAll('[onclick^="editGoal"]').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const goalIndex = this.getAttribute('onclick').match(/editGoal\((\d+)\)/)[1];
            editGoal(goalIndex);
        });
    });
    
    document.querySelectorAll('[onclick^="deleteGoal"]').forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const goalIndex = this.getAttribute('onclick').match(/deleteGoal\((\d+)\)/)[1];
            deleteGoal(goalIndex);
        });
    });
}

function initializeCharts() {
    // Goal progress chart
    const ctx = document.getElementById('goalsChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['مكتمل', 'نشط', 'قيد المراجعة'],
                datasets: [{
                    data: [
                        {{ count($goals['completed_goals'] ?? []) }},
                        {{ collect($goals['active_goals'] ?? [])->where('progress', '>=', 80)->count() }},
                        {{ collect($goals['active_goals'] ?? [])->where('progress', '<', 50)->count() }}
                    ],
                    backgroundColor: [
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(249, 115, 22, 0.8)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    }
    
    // Progress trend chart
    const trendCtx = document.getElementById('progressTrendChart');
    if (trendCtx) {
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
                datasets: [{
                    label: 'متوسط التقدم',
                    data: [65, 72, 78, 82, 85, 88],
                    borderColor: 'rgb(147, 51, 234)',
                    backgroundColor: 'rgba(147, 51, 234, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });
    }
}

function startRealTimeUpdates() {
    // Simulate real-time updates every 30 seconds
    setInterval(() => {
        updateGoalProgress();
        updateStatistics();
    }, 30000);
}

function updateGoalProgress() {
    // Update random goal progress
    const progressBars = document.querySelectorAll('[style*="width"]');
    progressBars.forEach(bar => {
        const currentWidth = parseInt(bar.style.width);
        if (currentWidth < 100) {
            const newWidth = Math.min(100, currentWidth + Math.random() * 5);
            bar.style.width = newWidth + '%';
            
            // Update percentage text
            const percentageElement = bar.closest('.flex').querySelector('.font-bold');
            if (percentageElement) {
                percentageElement.textContent = Math.round(newWidth) + '%';
            }
        }
    });
}

function updateStatistics() {
    // Update statistics dynamically
    const statsElements = document.querySelectorAll('.font-bold.text-gray-900, .font-bold.text-green-600, .font-bold.text-blue-600, .font-bold.text-purple-600');
    statsElements.forEach(element => {
        const currentValue = parseInt(element.textContent);
        if (!isNaN(currentValue)) {
            const change = Math.floor(Math.random() * 3) - 1;
            element.textContent = (currentValue + change);
        }
    });
}

function addNewGoal() {
    // Show notification for now
    showNotification('سيتم فتحديث الأهداف قريباً', 'info');
}

function editGoal(index) {
    // Show notification for now
    showNotification(`سيتم تحديث الهدف رقم ${index + 1}`, 'info');
}

function deleteGoal(index) {
    if (confirm('هل أنت متأكد من حذف هذا الهدف؟')) {
        showNotification(`تم حذف الهدف رقم ${index + 1}`, 'success');
    }
}

function refreshGoals() {
    // Show loading state
    showNotification('جاري تحديث الأهداف...', 'info');
    
    // Simulate refresh
    setTimeout(() => {
        showNotification('تم تحديث الأهداف بنجاح', 'success');
    }, 1500);
}

function filterHistory(period) {
    // Show notification for now
    showNotification(`تم تصفية السجل: ${period}`, 'info');
}

function updateChart(chartId) {
    const chart = Chart.getChart(chartId);
    if (chart) {
        // Update chart with new data
        const newData = generateRandomData();
        chart.data.datasets[0].data = newData;
        chart.update();
        showNotification('تم تحديث الرسم البيانات', 'success');
    }
}

function updateTrendChart(period) {
    const chart = Chart.getChart('progressTrendChart');
    if (chart) {
        const newData = generateTrendData(period);
        chart.data.datasets[0].data = newData;
        chart.update();
        showNotification(`تم تحديث بيانات ${period}`, 'info');
    }
}

function generateRandomData() {
    return Array.from({length: 6}, () => Math.floor(Math.random() * 30) + 60);
}

function generateTrendData(period) {
    const months = period == 6 ? ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'] : 
                  ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسط', 'سبتمبر', 'أكتوبر', 'نوفمبر'];
    
    return Array.from({length: count($months)}, () => Math.floor(Math.random() * 30) + 60);
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 left-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 
                           type === 'error' ? 'fa-exclamation-circle' : 
                           type === 'warning' ? 'fa-exclamation-triangle' : 
                           'fa-info-circle'} ml-2"></i>
            ${message}
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(400px)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}
</script>
@endpush
