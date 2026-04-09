@extends('admin.layouts.admin')

@section('title', 'أمان النظام')
@section('page-title', 'أمان النظام')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-2xl p-8 text-white shadow-xl mb-8 text-center relative overflow-hidden">
            <div class="absolute inset-0 bg-pattern opacity-10"></div>
            <h1 class="text-3xl font-bold mb-2 relative z-10">أمان النظام</h1>
            <p class="text-lg opacity-90 relative z-10">مراقبة وإدارة أمان النظام وحماية البيانات</p>
        </div>

        <!-- Security Overview -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="bg-blue-100 p-2 rounded-lg ml-3">
                    <i class="fas fa-chart-line text-blue-600"></i>
                </div>
                نظرة عامة على الأمان
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-gray-50 rounded-xl p-6 text-center border border-gray-200 hover:shadow-md transition-all duration-300 group">
                    <div class="text-4xl font-bold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors">98%</div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">مستوى الأمان</div>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-6 text-center border border-gray-200 hover:shadow-md transition-all duration-300 group">
                    <div class="text-4xl font-bold text-gray-800 mb-2 group-hover:text-red-600 transition-colors">0</div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">تهديدات نشطة</div>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-6 text-center border border-gray-200 hover:shadow-md transition-all duration-300 group">
                    <div class="text-4xl font-bold text-gray-800 mb-2 group-hover:text-green-600 transition-colors">24</div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">فحصات اليوم</div>
                </div>
                
                <div class="bg-gray-50 rounded-xl p-6 text-center border border-gray-200 hover:shadow-md transition-all duration-300 group">
                    <div class="text-4xl font-bold text-gray-800 mb-2 group-hover:text-indigo-600 transition-colors">100%</div>
                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">التحديثات الأمنية</div>
                </div>
            </div>
        </div>

        <!-- Threat Level -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="bg-yellow-100 p-2 rounded-lg ml-3">
                    <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                </div>
                مستوى التهديد
            </h2>
            
            <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-xl p-6 text-white flex items-center gap-6 shadow-md">
                <div class="bg-white/20 p-4 rounded-full">
                    <i class="fas fa-check-circle text-3xl"></i>
                </div>
                <div>
                    <div class="font-bold text-2xl mb-1">منخفض</div>
                    <div class="opacity-90 text-sm">لا توجد تهديدات أمنية حالية، النظام يعمل بشكل طبيعي</div>
                </div>
            </div>
        </div>

        <!-- Security Features -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
            <h2 class="text-xl font-bold mb-6 flex items-center text-gray-800">
                <div class="bg-purple-100 p-2 rounded-lg ml-3">
                    <i class="fas fa-cogs text-purple-600"></i>
                </div>
                ميزات الأمان
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white border border-gray-200 rounded-xl p-6 hover:border-blue-500 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="bg-orange-100 p-2 rounded-lg">
                                <i class="fas fa-fire text-orange-600 text-lg"></i>
                            </div>
                            <span class="font-bold text-gray-800">جدار الحماية</span>
                        </div>
                        <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-md">نشط</span>
                    </div>
                    <p class="text-gray-500 text-sm leading-relaxed">حماية متقدمة ضد الهجمات الخارجية ومحاولات الاختراق</p>
                </div>
                
                <div class="bg-white border border-gray-200 rounded-xl p-6 hover:border-blue-500 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="bg-green-100 p-2 rounded-lg">
                                <i class="fas fa-lock text-green-600 text-lg"></i>
                            </div>
                            <span class="font-bold text-gray-800">التشفير</span>
                        </div>
                        <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-md">مفعل</span>
                    </div>
                    <p class="text-gray-500 text-sm leading-relaxed">تشفير البيانات الحساسة باستخدام أحدث خوارزميات التشفير</p>
                </div>
                
                <div class="bg-white border border-gray-200 rounded-xl p-6 hover:border-blue-500 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="bg-blue-100 p-2 rounded-lg">
                                <i class="fas fa-user-shield text-blue-600 text-lg"></i>
                            </div>
                            <span class="font-bold text-gray-800">التحقق الثنائي</span>
                        </div>
                        <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2 py-1 rounded-md">اختياري</span>
                    </div>
                    <p class="text-gray-500 text-sm leading-relaxed">حماية إضافية للوصول تتطلب خطوة تحقق ثانية</p>
                </div>
                
                <div class="bg-white border border-gray-200 rounded-xl p-6 hover:border-blue-500 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="bg-purple-100 p-2 rounded-lg">
                                <i class="fas fa-database text-purple-600 text-lg"></i>
                            </div>
                            <span class="font-bold text-gray-800">نسخ احتياطي</span>
                        </div>
                        <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-md">يومي</span>
                    </div>
                    <p class="text-gray-500 text-sm leading-relaxed">نسخ احتياطي تلقائي للبيانات لضمان عدم فقدانها</p>
                </div>
            </div>
        </div>

        <!-- Security Activity Log -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold flex items-center text-gray-800">
                    <div class="bg-indigo-100 p-2 rounded-lg ml-3">
                        <i class="fas fa-history text-indigo-600"></i>
                    </div>
                    سجل نشاط الأمان
                </h2>
                <button onclick="runSecurityScan()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center gap-2">
                    <i class="fas fa-sync-alt"></i>
                    فحص جديد
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النشاط</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الوصف</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الوقت</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-2.5 w-2.5 rounded-full bg-green-500 ml-3"></div>
                                    <span class="text-sm font-medium text-gray-900">فحص أمان ناجح</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">اكتمل فحص الأمان الشامل دون مشاكل</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">منذ 5 دقائق</td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-2.5 w-2.5 rounded-full bg-blue-500 ml-3"></div>
                                    <span class="text-sm font-medium text-gray-900">تحديث أمني</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">تم تثبيت تحديث أمني جديد للنظام</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">منذ ساعة</td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-2.5 w-2.5 rounded-full bg-yellow-500 ml-3"></div>
                                    <span class="text-sm font-medium text-gray-900">محاولة وصول مشبوهة</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">تم حظر محاولة وصول غير مصرح بها من IP غريب</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">منذ ساعتين</td>
                        </tr>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-2.5 w-2.5 rounded-full bg-green-500 ml-3"></div>
                                    <span class="text-sm font-medium text-gray-900">نسخ احتياطي</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">تم إنشاء نسخة احتياطية للبيانات بنجاح</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">منذ 3 ساعات</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function runSecurityScan() {
    // Show loading state
    const button = event.currentTarget;
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i>جاري الفحص...';
    button.disabled = true;
    button.classList.add('opacity-75', 'cursor-not-allowed');
    
    // Simulate security scan
    setTimeout(() => {
        button.innerHTML = '<i class="fas fa-check ml-2"></i>اكتمل الفحص';
        button.classList.remove('bg-indigo-600', 'hover:bg-indigo-700');
        button.classList.add('bg-green-600', 'hover:bg-green-700');
        
        // Show success notification
        showNotification('فحص الأمان اكتمل بنجاح! لا توجد تهديدات.', 'success');
        
        // Reset button after 2 seconds
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
            button.classList.remove('opacity-75', 'cursor-not-allowed', 'bg-green-600', 'hover:bg-green-700');
            button.classList.add('bg-indigo-600', 'hover:bg-indigo-700');
        }, 2000);
    }, 3000);
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
    
    notification.className = `fixed top-4 left-4 ${bgColor} text-white px-6 py-4 rounded-xl shadow-2xl z-50 animate-bounce flex items-center gap-3`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} text-xl"></i>
        <span class="font-medium">${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
        setTimeout(() => {
            notification.remove();
        }, 500);
    }, 3000);
}
</script>
@endpush