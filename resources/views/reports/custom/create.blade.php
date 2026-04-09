@extends('admin.layouts.admin')

@section('title', 'إنشاء تقرير مخصص')

@section('content')
<div class="container-fluid py-6">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-8 border border-blue-200 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2 flex items-center">
                    <i class="fas fa-file-plus text-blue-600 ml-4"></i>
                    إنشاء تقرير مخصص جديد
                </h1>
                <p class="text-gray-600 text-lg">قم بإنشاء تقارير مخصصة تناسب احتياجاتك</p>
            </div>
            <a href="{{ route('reports.custom.index') }}" 
               class="bg-white hover:bg-gray-50 text-gray-700 px-6 py-3 rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl border border-gray-300 flex items-center">
                <i class="fas fa-arrow-left ml-2"></i>
                رجوع للتقارير
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-8">
                <form action="{{ route('reports.custom.store') }}" method="POST" id="createReportForm">
                    @csrf
                    
                    @if(request()->has('template'))
                        <input type="hidden" name="template_id" value="{{ request('template') }}">
                    @endif

                    <div class="space-y-8">
                        <!-- Basic Information -->
                        <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-6 border border-blue-200">
                            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-info-circle text-blue-600 ml-3"></i>
                                المعلومات الأساسية
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-3">اسم التقرير</label>
                                    <input type="text" 
                                           class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:border-blue-500 focus:ring-blue-500 bg-white text-lg shadow-sm transition-all duration-300" 
                                           id="title" name="title" value="{{ old('title') }}" required
                                           placeholder="أدخل اسم التقرير...">
                                    @error('title')
                                        <p class="mt-2 text-sm text-red-600 font-medium bg-red-50 p-3 rounded-lg border border-red-200">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <div>
                                    <label for="category" class="block text-sm font-semibold text-gray-700 mb-3">التصنيف</label>
                                    <select class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:border-blue-500 focus:ring-blue-500 bg-white text-lg shadow-sm transition-all duration-300" 
                                            id="category" name="category">
                                        <option value="">اختر التصنيف</option>
                                        <option value="sales" {{ old('category') == 'sales' ? 'selected' : '' }}>المبيعات</option>
                                        <option value="customers" {{ old('category') == 'customers' ? 'selected' : '' }}>العملاء</option>
                                        <option value="inventory" {{ old('category') == 'inventory' ? 'selected' : '' }}>المخزون</option>
                                        <option value="financial" {{ old('category') == 'financial' ? 'selected' : '' }}>مالي</option>
                                        <option value="marketing" {{ old('category') == 'marketing' ? 'selected' : '' }}>تسويق</option>
                                        <option value="operations" {{ old('category') == 'operations' ? 'selected' : '' }}>عمليات</option>
                                        <option value="analytics" {{ old('category') == 'analytics' ? 'selected' : '' }}>تحليلات</option>
                                    </select>
                                    @error('category')
                                        <p class="mt-2 text-sm text-red-600 font-medium bg-red-50 p-3 rounded-lg border border-red-200">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Data Sources -->
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-200">
                            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-database text-green-600 ml-3"></i>
                                مصادر البيانات
                            </h3>
                            
                            <div>
                                <label for="data_sources" class="block text-sm font-semibold text-gray-700 mb-3">مصادر البيانات</label>
                                <select class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:border-green-500 focus:ring-green-500 bg-white text-lg shadow-sm transition-all duration-300" 
                                        id="data_sources" name="data_sources[]" multiple required>
                                    <option value="properties">العقارات</option>
                                    <option value="users">المستخدمين</option>
                                    <option value="transactions">المعاملات</option>
                                    <option value="agents">الوكلاء</option>
                                    <option value="leads">العملاء المحتملين</option>
                                    <option value="appointments">المواعيد</option>
                                    <option value="contracts">العقود</option>
                                    <option value="invoices">الفواتير</option>
                                </select>
                                <p class="mt-2 text-sm text-gray-600 bg-blue-50 p-3 rounded-lg border border-blue-200">
                                    <i class="fas fa-info-circle text-blue-500 ml-2"></i>
                                    يمكنك اختيار أكثر من مصدر (اضغط Ctrl للتحديد المتعدد)
                                </p>
                                @error('data_sources')
                                    <p class="mt-2 text-sm text-red-600 font-medium bg-red-50 p-3 rounded-lg border border-red-200">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl p-6 border border-orange-200">
                            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-align-left text-orange-600 ml-3"></i>
                                الوصف
                            </h3>
                            
                            <div>
                                <label for="description" class="block text-sm font-semibold text-gray-700 mb-3">وصف التقرير</label>
                                <textarea class="w-full border-2 border-gray-300 rounded-xl px-4 py-3 focus:border-orange-500 focus:ring-orange-500 bg-white text-lg shadow-sm transition-all duration-300" 
                                         id="description" name="description" rows="4" 
                                         placeholder="أدخل وصفاً مفصلاً للتقرير...">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-2 text-sm text-red-600 font-medium bg-red-50 p-3 rounded-lg border border-red-200">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Report Settings -->
                        <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-xl p-6 border border-purple-200">
                            <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                                <i class="fas fa-cog text-purple-600 ml-3"></i>
                                إعدادات التقرير
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="flex items-center space-x-3 bg-white rounded-xl p-4 border border-gray-200 shadow-md">
                                    <input type="checkbox" 
                                           class="w-5 h-5 text-purple-600 rounded focus:ring-purple-500 border-purple-300" 
                                           id="is_public" name="is_public" value="1" {{ old('is_public') ? 'checked' : '' }}>
                                    <label for="is_public" class="text-sm font-semibold text-gray-700 cursor-pointer">
                                        <i class="fas fa-globe text-purple-500 ml-2"></i>
                                        تقرير عام
                                    </label>
                                </div>
                                
                                <div class="flex items-center space-x-3 bg-white rounded-xl p-4 border border-gray-200 shadow-md">
                                    <input type="checkbox" 
                                           class="w-5 h-5 text-purple-600 rounded focus:ring-purple-500 border-purple-300" 
                                           id="is_template" name="is_template" value="1" {{ old('is_template') ? 'checked' : '' }}>
                                    <label for="is_template" class="text-sm font-semibold text-gray-700 cursor-pointer">
                                        <i class="fas fa-layer-group text-purple-500 ml-2"></i>
                                        حفظ كقالب
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden fields for query config -->
                        <input type="hidden" name="query_config[columns][]" value="*">
                        <input type="hidden" name="query_config[limit]" value="100">

                        <!-- Submit Button -->
                        <div class="flex justify-center mt-8">
                            <button type="submit" 
                                    class="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-12 py-4 rounded-2xl font-bold text-lg transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:scale-105 flex items-center">
                                <i class="fas fa-save ml-3"></i>
                                حفظ التقرير
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Quick Templates -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-6 mb-6">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-layer-group text-blue-600 ml-3"></i>
                    القوالب السريعة
                </h3>
                
                <div class="space-y-3">
                    <a href="#" onclick="loadTemplate('sales_report')" 
                       class="block p-4 bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl border border-blue-200 hover:from-blue-100 hover:to-blue-200 transition-all duration-300 group">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 group-hover:text-blue-700">تقرير المبيعات</h4>
                                <p class="text-xs text-gray-600">تقرير شامل للمبيعات</p>
                            </div>
                            <i class="fas fa-chart-line text-blue-500"></i>
                        </div>
                    </a>
                    
                    <a href="#" onclick="loadTemplate('customer_analysis')" 
                       class="block p-4 bg-gradient-to-r from-green-50 to-green-100 rounded-xl border border-green-200 hover:from-green-100 hover:to-green-200 transition-all duration-300 group">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 group-hover:text-green-700">تحليل العملاء</h4>
                                <p class="text-xs text-gray-600">تحليل بيانات العملاء</p>
                            </div>
                            <i class="fas fa-users text-green-500"></i>
                        </div>
                    </a>
                    
                    <a href="#" onclick="loadTemplate('financial_summary')" 
                       class="block p-4 bg-gradient-to-r from-orange-50 to-orange-100 rounded-xl border border-orange-200 hover:from-orange-100 hover:to-orange-200 transition-all duration-300 group">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 group-hover:text-orange-700">ملخص مالي</h4>
                                <p class="text-xs text-gray-600">تقرير مالي شامل</p>
                            </div>
                            <i class="fas fa-dollar-sign text-orange-500"></i>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Help Section -->
            <div class="bg-gradient-to-r from-purple-50 to-indigo-50 rounded-2xl p-6 border border-purple-200">
                <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-question-circle text-purple-600 ml-3"></i>
                    مساعدة
                </h3>
                
                <div class="space-y-4">
                    <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-md">
                        <h4 class="text-sm font-bold text-gray-900 mb-3 flex items-center">
                            <i class="fas fa-lightbulb text-yellow-500 ml-2"></i>
                            نصائح سريعة
                        </h4>
                        <ul class="space-y-2 text-sm text-gray-700">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 ml-2 mt-1"></i>
                                <span>اختر مصادر البيانات المناسبة للتقرير</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 ml-2 mt-1"></i>
                                <span>استخدم وصفاً واضحاً لسهولة البحث</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 ml-2 mt-1"></i>
                                <span>حدد الفئة المناسبة للتنظيم</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="bg-white rounded-xl p-4 border border-gray-200 shadow-md">
                        <h4 class="text-sm font-bold text-gray-900 mb-3 flex items-center">
                            <i class="fas fa-book text-blue-500 ml-2"></i>
                            دليل المستخدم
                        </h4>
                        <div class="space-y-2 text-sm text-gray-700">
                            <p>• يمكن حفظ التقارير كقوالب للاستخدام لاحقاً</p>
                            <p>• التقارير العامة متاحة لجميع المستخدمين</p>
                            <p>• يمكن تعديل التقارير في أي وقت</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function loadTemplate(templateType) {
    // Load template data based on type
    const templates = {
        'sales_report': {
            title: 'تقرير المبيعات',
            category: 'sales',
            data_sources: ['transactions', 'users'],
            description: 'تقرير شامل للمبيعات الشهرية'
        },
        'customer_analysis': {
            title: 'تحليل العملاء',
            category: 'customers',
            data_sources: ['users', 'leads'],
            description: 'تحليل شامل لبيانات العملاء'
        },
        'financial_summary': {
            title: 'ملخص مالي',
            category: 'financial',
            data_sources: ['transactions', 'invoices'],
            description: 'تقرير مالي شامل للشهر'
        }
    };
    
    const template = templates[templateType];
    if (template) {
        document.getElementById('title').value = template.title;
        document.getElementById('category').value = template.category;
        document.getElementById('description').value = template.description;
        
        // Set data sources
        const dataSourcesSelect = document.getElementById('data_sources');
        Array.from(dataSourcesSelect.options).forEach(option => {
            option.selected = template.data_sources.includes(option.value);
        });
        
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

// Auto-save functionality
let autoSaveTimer;
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('createReportForm');
    const inputs = form.querySelectorAll('input, textarea, select');
    
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(() => {
                // Save to localStorage
                const formData = new FormData(form);
                const data = {};
                for (let [key, value] of formData.entries()) {
                    data[key] = value;
                }
                localStorage.setItem('report_draft', JSON.stringify(data));
            }, 2000);
        });
    });
    
    // Load draft if exists
    const draft = localStorage.getItem('report_draft');
    if (draft) {
        const data = JSON.parse(draft);
        Object.keys(data).forEach(key => {
            const input = form.querySelector(`[name="${key}"]`);
            if (input) {
                if (input.type === 'checkbox') {
                    input.checked = data[key] === 'on' || data[key] === true;
                } else if (input.type === 'select-multiple') {
                    // Handle multiple select
                    if (Array.isArray(data[key])) {
                        Array.from(input.options).forEach(option => {
                            option.selected = data[key].includes(option.value);
                        });
                    }
                } else {
                    input.value = data[key];
                }
            }
        });
    }
});
</script>
@endpush
@endsection
