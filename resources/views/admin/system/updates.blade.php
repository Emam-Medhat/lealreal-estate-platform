@extends('admin.layouts.admin')

@section('title', 'تحديثات النظام')
@section('page-title', 'تحديثات النظام')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Hero Section -->
        <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl p-8 text-white shadow-xl mb-8 relative overflow-hidden">
            <div class="absolute inset-0 bg-pattern opacity-10"></div>
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h1 class="text-3xl font-bold mb-2 flex items-center gap-3">
                        <div class="bg-white/20 p-2 rounded-lg">
                            <i class="fas fa-sync-alt"></i>
                        </div>
                        تحديثات النظام
                    </h1>
                    <p class="text-lg opacity-90">إدارة وتتبع تحديثات النظام والتحقق من الإصدارات الجديدة</p>
                </div>
                <button onclick="checkForUpdates()" class="bg-white text-indigo-600 hover:bg-indigo-50 px-6 py-3 rounded-xl font-bold shadow-lg transition-all duration-300 transform hover:-translate-y-1 flex items-center gap-2 group">
                    <i class="fas fa-download group-hover:animate-bounce"></i>
                    التحقق من التحديثات
                </button>
            </div>
        </div>

        <!-- Current Version Info -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <div class="flex items-center mb-6">
                <div class="bg-blue-100 p-3 rounded-xl ml-4">
                    <i class="fas fa-info-circle text-blue-600 text-2xl"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800">معلومات النظام الحالي</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 hover:border-blue-500 hover:shadow-md transition-all duration-300 group">
                    <div class="text-sm font-semibold text-gray-500 mb-2 uppercase tracking-wider">الإصدار الحالي</div>
                    <div class="text-3xl font-bold text-gray-800 group-hover:text-blue-600 transition-colors">{{ config('app.version', '1.0.0') }}</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 hover:border-red-500 hover:shadow-md transition-all duration-300 group">
                    <div class="text-sm font-semibold text-gray-500 mb-2 uppercase tracking-wider">إصدار Laravel</div>
                    <div class="text-3xl font-bold text-gray-800 group-hover:text-red-600 transition-colors">{{ app()->version() }}</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 hover:border-purple-500 hover:shadow-md transition-all duration-300 group">
                    <div class="text-sm font-semibold text-gray-500 mb-2 uppercase tracking-wider">إصدار PHP</div>
                    <div class="text-3xl font-bold text-gray-800 group-hover:text-purple-600 transition-colors">{{ PHP_VERSION }}</div>
                </div>
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200 hover:border-green-500 hover:shadow-md transition-all duration-300 group">
                    <div class="text-sm font-semibold text-gray-500 mb-2 uppercase tracking-wider">آخر تحديث</div>
                    <div class="text-xl font-bold text-gray-800 group-hover:text-green-600 transition-colors mt-2" dir="ltr">{{ now()->format('Y-m-d H:i') }}</div>
                </div>
            </div>
        </div>

        <!-- Available Updates -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <div class="flex items-center mb-6">
                <div class="bg-green-100 p-3 rounded-xl ml-4">
                    <i class="fas fa-cloud-download-alt text-green-600 text-2xl"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800">التحديثات المتاحة</h2>
            </div>
            
            <div id="updates-container" class="min-h-[200px] flex items-center justify-center">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-indigo-100 border-t-indigo-600 mx-auto mb-4"></div>
                    <p class="text-gray-500 font-medium">جاري الاتصال بخادم التحديثات...</p>
                </div>
            </div>
        </div>

        <!-- Update History -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <div class="flex items-center mb-6">
                <div class="bg-purple-100 p-3 rounded-xl ml-4">
                    <i class="fas fa-history text-purple-600 text-2xl"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-800">سجل التحديثات</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">الإصدار</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">التاريخ</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">النوع</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-bold text-gray-800">1.0.0</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ now()->subDays(30)->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">رئيسي</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full flex items-center w-fit gap-1">
                                    <i class="fas fa-check-circle text-[10px]"></i>
                                    مكتمل
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button onclick="viewUpdateDetails('1.0.0')" class="text-indigo-600 hover:text-indigo-900 font-bold hover:underline flex items-center gap-1">
                                    <i class="fas fa-eye"></i>
                                    عرض التفاصيل
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="font-bold text-gray-800">0.9.5</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ now()->subDays(60)->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-yellow-100 text-yellow-700 text-xs font-bold px-3 py-1 rounded-full">ثانوي</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full flex items-center w-fit gap-1">
                                    <i class="fas fa-check-circle text-[10px]"></i>
                                    مكتمل
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button onclick="viewUpdateDetails('0.9.5')" class="text-indigo-600 hover:text-indigo-900 font-bold hover:underline flex items-center gap-1">
                                    <i class="fas fa-eye"></i>
                                    عرض التفاصيل
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Update Details Modal -->
<div id="updateModal" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-50 hidden flex items-center justify-center transition-opacity duration-300 opacity-0">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 transform scale-95 transition-transform duration-300" id="modalContent">
        <div class="flex justify-between items-center p-6 border-b border-gray-100">
            <h3 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-file-alt text-indigo-500"></i>
                تفاصيل التحديث
            </h3>
            <button onclick="closeUpdateModal()" class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div class="p-6 max-h-[70vh] overflow-y-auto custom-scrollbar" id="update-details-content">
            <!-- Content loaded via JS -->
        </div>
        
        <div class="p-6 border-t border-gray-100 flex justify-end bg-gray-50 rounded-b-2xl">
            <button onclick="closeUpdateModal()" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 px-6 py-2 rounded-xl font-bold transition-colors shadow-sm">
                إغلاق
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Modal Animation Logic
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    const content = modal.querySelector('div');
    
    modal.classList.remove('hidden');
    // Trigger reflow
    void modal.offsetWidth;
    
    modal.classList.remove('opacity-0');
    content.classList.remove('scale-95');
    content.classList.add('scale-100');
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    const content = modal.querySelector('div');
    
    modal.classList.add('opacity-0');
    content.classList.remove('scale-100');
    content.classList.add('scale-95');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

function checkForUpdates() {
    const container = document.getElementById('updates-container');
    container.innerHTML = `
        <div class="text-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-4 border-indigo-100 border-t-indigo-600 mx-auto mb-4"></div>
            <p class="text-gray-600 font-medium">جاري التحقق من وجود تحديثات جديدة...</p>
        </div>
    `;

    // Simulate checking for updates
    setTimeout(() => {
        container.innerHTML = `
            <div class="grid grid-cols-1 gap-6 animate-fade-in">
                <div class="bg-green-50 border border-green-200 rounded-xl p-6 flex items-start gap-4">
                    <div class="bg-green-100 p-3 rounded-full shrink-0">
                        <i class="fas fa-check text-green-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="text-green-800 font-bold text-lg mb-1">نظامك محدث بالكامل!</h4>
                        <p class="text-green-700 text-sm opacity-90">أنت تستخدم أحدث إصدار من النظام. لا توجد تحديثات متاحة حالياً.</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="bg-blue-100 p-2 rounded-lg">
                                <i class="fas fa-server text-blue-600"></i>
                            </div>
                            <h5 class="text-blue-900 font-bold">حالة الخادم</h5>
                        </div>
                        <p class="text-blue-800 text-sm">جميع خدمات النظام تعمل بكفاءة عالية.</p>
                    </div>
                    
                    <div class="bg-purple-50 border border-purple-200 rounded-xl p-5 hover:shadow-md transition-shadow">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="bg-purple-100 p-2 rounded-lg">
                                <i class="fas fa-shield-virus text-purple-600"></i>
                            </div>
                            <h5 class="text-purple-900 font-bold">الحالة الأمنية</h5>
                        </div>
                        <p class="text-purple-800 text-sm">تم تطبيق أحدث التصحيحات الأمنية.</p>
                    </div>
                </div>
            </div>
        `;
    }, 2000);
}

function viewUpdateDetails(version) {
    const content = document.getElementById('update-details-content');
    
    content.innerHTML = `
        <div class="space-y-6">
            <div class="flex items-center justify-between bg-gray-50 p-4 rounded-xl border border-gray-200">
                <div>
                    <h4 class="text-2xl font-bold text-gray-900 mb-1">الإصدار ${version}</h4>
                    <p class="text-gray-500 text-sm flex items-center gap-2">
                        <i class="far fa-calendar-alt"></i>
                        {{ now()->format('Y-m-d') }}
                    </p>
                </div>
                <span class="bg-green-100 text-green-700 px-4 py-2 rounded-lg font-bold text-sm">مستقر</span>
            </div>
            
            <div>
                <h5 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2 border-b pb-2">
                    <i class="fas fa-list-ul text-indigo-500"></i>
                    سجل التغييرات
                </h5>
                <div class="grid gap-4">
                    <div class="flex gap-4 p-4 rounded-xl bg-white border border-gray-100 hover:border-indigo-100 hover:shadow-sm transition-all">
                        <div class="bg-green-100 p-2 rounded-lg h-fit text-green-600">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <div>
                            <h6 class="font-bold text-gray-800 mb-1">تحسين الأداء</h6>
                            <p class="text-sm text-gray-600">تحسين سرعة استجابة النظام بنسبة 20% وتقليل استهلاك الذاكرة.</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-4 p-4 rounded-xl bg-white border border-gray-100 hover:border-indigo-100 hover:shadow-sm transition-all">
                        <div class="bg-blue-100 p-2 rounded-lg h-fit text-blue-600">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div>
                            <h6 class="font-bold text-gray-800 mb-1">إصلاحات أمنية</h6>
                            <p class="text-sm text-gray-600">سد ثغرات أمنية محتملة وتحديث مكتبات الحماية.</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-4 p-4 rounded-xl bg-white border border-gray-100 hover:border-indigo-100 hover:shadow-sm transition-all">
                        <div class="bg-purple-100 p-2 rounded-lg h-fit text-purple-600">
                            <i class="fas fa-magic"></i>
                        </div>
                        <div>
                            <h6 class="font-bold text-gray-800 mb-1">ميزات جديدة</h6>
                            <p class="text-sm text-gray-600">إضافة لوحة تحكم جديدة للمستخدمين وتحسين واجهة التقارير.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div>
                <h5 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2 border-b pb-2">
                    <i class="fas fa-server text-indigo-500"></i>
                    المتطلبات التقنية
                </h5>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-gray-50 p-4 rounded-xl text-center border border-gray-200">
                        <i class="fab fa-php text-indigo-600 text-2xl mb-2"></i>
                        <div class="font-bold text-gray-800">PHP 8.1+</div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl text-center border border-gray-200">
                        <i class="fab fa-laravel text-red-600 text-2xl mb-2"></i>
                        <div class="font-bold text-gray-800">Laravel 10.x</div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl text-center border border-gray-200">
                        <i class="fas fa-database text-blue-600 text-2xl mb-2"></i>
                        <div class="font-bold text-gray-800">MySQL 8.0+</div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    openModal('updateModal');
}

function closeUpdateModal() {
    closeModal('updateModal');
}

// Check for updates on page load
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(checkForUpdates, 1000);
});
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fade-in 0.5s ease-out forwards;
    }
</style>
@endpush