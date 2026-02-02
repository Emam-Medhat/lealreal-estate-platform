@extends('admin.layouts.admin')

@section('title', 'مقاييس الأمان')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-red-50 to-orange-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-r from-red-600 to-orange-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-shield-alt text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                                مقاييس الأمان
                            </h1>
                            <p class="text-gray-600 text-lg">حماية وتأمين البيانات المكانية الحساسة</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <button class="bg-red-600 text-white px-6 py-3 rounded-2xl hover:bg-red-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-lock ml-2"></i>
                        تدقيق الأمان
                    </button>
                </div>
            </div>
        </div>

        <!-- Security Status -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                    <span class="text-sm text-green-600 font-medium">آمن</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-1">98.7%</h3>
                <p class="text-gray-600 text-sm">مستوى الأمان</p>
            </div>

            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-user-shield text-blue-600 text-xl"></i>
                    </div>
                    <span class="text-sm text-blue-600 font-medium">نشط</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-1">1,247</h3>
                <p class="text-gray-600 text-sm">مستخدمين محميين</p>
            </div>

            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-lock text-purple-600 text-xl"></i>
                    </div>
                    <span class="text-sm text-purple-600 font-medium">مشفرة</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-1">AES-256</h3>
                <p class="text-gray-600 text-sm">تشفير البيانات</p>
            </div>

            <div class="bg-white/80 backdrop-blur-xl rounded-2xl shadow-xl border border-white/20 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-orange-600 text-xl"></i>
                    </div>
                    <span class="text-sm text-orange-600 font-medium">3</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-1">تهديدات</h3>
                <p class="text-gray-600 text-sm">تحت المراقبة</p>
            </div>
        </div>

        <!-- Security Features -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Data Encryption -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 bg-gradient-to-r from-green-500 to-emerald-500 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-key text-white text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">تشفير البيانات</h3>
                        <p class="text-gray-600">حماية البيانات المكانية بأعلى معايير الأمان</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="bg-green-50 border border-green-200 rounded-2xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-green-800">تشفير AES-256</span>
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <p class="text-sm text-green-700">تشفير من طرف إلى طرف لجميع البيانات المكانية</p>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-blue-800">TLS 1.3</span>
                            <i class="fas fa-check-circle text-blue-600"></i>
                        </div>
                        <p class="text-sm text-blue-700">بروتوكول نقل آمن للاتصالات</p>
                    </div>
                    <div class="bg-purple-50 border border-purple-200 rounded-2xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-purple-800">Hashing SHA-256</span>
                            <i class="fas fa-check-circle text-purple-600"></i>
                        </div>
                        <p class="text-sm text-purple-700">تشفير لا رجعي للبيانات الحساسة</p>
                    </div>
                </div>
            </div>

            <!-- Access Control -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-user-lock text-white text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">التحكم في الوصول</h3>
                        <p class="text-gray-600">إدارة صلاحيات الوصول للبيانات المكانية</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-users text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium">المستخدمون المصرح لهم</div>
                                    <div class="text-xs text-gray-500">247 مستخدم نشط</div>
                                </div>
                            </div>
                            <button class="text-blue-600 hover:text-blue-700">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-shield-alt text-green-600 text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium">الأدوار والصلاحيات</div>
                                    <div class="text-xs text-gray-500">8 أدوار محددة</div>
                                </div>
                            </div>
                            <button class="text-green-600 hover:text-green-700">
                                <i class="fas fa-edit"></i>
                            </button>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-clock text-purple-600 text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium">الجلسات النشطة</div>
                                    <div class="text-xs text-gray-500">89 جلسة</div>
                                </div>
                            </div>
                            <button class="text-purple-600 hover:text-purple-700">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <button class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white py-3 rounded-2xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 font-semibold">
                        إدارة المستخدمين
                    </button>
                </div>
            </div>
        </div>

        <!-- Security Monitoring -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Threat Detection -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 bg-gradient-to-r from-red-500 to-pink-500 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-radar text-white text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">كشف التهديدات</h3>
                        <p class="text-gray-600">مراقبة التهديدات الأمنية في الوقت الفعلي</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="bg-red-50 border border-red-200 rounded-2xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-red-800">محاولة وصول غير مصرح بها</span>
                            <span class="text-xs text-red-600">منذ 5 دقائق</span>
                        </div>
                        <p class="text-sm text-red-700">IP: 192.168.1.100 - تم الحظر تلقائياً</p>
                    </div>
                    <div class="bg-orange-50 border border-orange-200 rounded-2xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-orange-800">نشاط مشبوه</span>
                            <span class="text-xs text-orange-600">منذ 15 دقيقة</span>
                        </div>
                        <p class="text-sm text-orange-700">عدد كبير من الطلبات من مستخدم واحد</p>
                    </div>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">التهديدات المحظورة اليوم</span>
                            <span class="font-medium text-red-600">12</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">التنبيهات النشطة</span>
                            <span class="font-medium text-orange-600">3</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Audit Logs -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 bg-gradient-to-r from-orange-500 to-yellow-500 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-history text-white text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">سجل التدقيق</h3>
                        <p class="text-gray-600">تتبع جميع الأنشطة الأمنية</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-xl">
                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                            <i class="fas fa-sign-in-alt text-green-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-medium">تسجيل دخول ناجح</div>
                            <div class="text-xs text-gray-500">admin@example.com - 10:30 AM</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-xl">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                            <i class="fas fa-download text-blue-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-medium">تحميل بيانات مكانية</div>
                            <div class="text-xs text-gray-500">user@example.com - 10:15 AM</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-xl">
                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                            <i class="fas fa-cog text-purple-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <div class="text-sm font-medium">تغيير الإعدادات الأمنية</div>
                            <div class="text-xs text-gray-500">admin@example.com - 09:45 AM</div>
                        </div>
                    </div>
                </div>
                <button class="w-full bg-gradient-to-r from-orange-600 to-yellow-600 text-white py-3 rounded-2xl hover:from-orange-700 hover:to-yellow-700 transition-all duration-300 font-semibold mt-4">
                    عرض السجل الكامل
                </button>
            </div>

            <!-- Compliance -->
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-14 h-14 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-2xl flex items-center justify-center">
                        <i class="fas fa-certificate text-white text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">الامتثال التنظيمي</h3>
                        <p class="text-gray-600">الالتزام بالمعايير واللوائح الأمنية</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-emerald-800">GDPR</span>
                            <i class="fas fa-check-circle text-emerald-600"></i>
                        </div>
                        <p class="text-sm text-emerald-700">حماية البيانات الشخصية الأوروبية</p>
                    </div>
                    <div class="bg-teal-50 border border-teal-200 rounded-2xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-teal-800">ISO 27001</span>
                            <i class="fas fa-check-circle text-teal-600"></i>
                        </div>
                        <p class="text-sm text-teal-700">إدارة أمن المعلومات</p>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="font-medium text-blue-800">SOC 2</span>
                            <i class="fas fa-check-circle text-blue-600"></i>
                        </div>
                        <p class="text-sm text-blue-700">التحكم في الخدمات السحابية</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Recommendations -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">توصيات الأمان</h3>
                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-medium">5 توصيات جديدة</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-4">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 bg-yellow-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-sm"></i>
                        </div>
                        <span class="font-medium text-yellow-800">عالية الأولوية</span>
                    </div>
                    <p class="text-sm text-yellow-700 mb-3">تحديث شهادات SSL للخوادم المكانية</p>
                    <button class="text-yellow-600 hover:text-yellow-700 text-sm font-medium">
                        تنفيذ الآن →
                    </button>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 bg-blue-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-info-circle text-blue-600 text-sm"></i>
                        </div>
                        <span class="font-medium text-blue-800">متوسطة الأولوية</span>
                    </div>
                    <p class="text-sm text-blue-700 mb-3">تفعيل المصادقة الثنائية للمستخدمين</p>
                    <button class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        جدولة →
                    </button>
                </div>
                <div class="bg-green-50 border border-green-200 rounded-2xl p-4">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 bg-green-200 rounded-lg flex items-center justify-center">
                            <i class="fas fa-lightbulb text-green-600 text-sm"></i>
                        </div>
                        <span class="font-medium text-green-800">تحسين</span>
                    </div>
                    <p class="text-sm text-green-700 mb-3">تحسين كلمات المرور للمستخدمين</p>
                    <button class="text-green-600 hover:text-green-700 text-sm font-medium">
                        مراجعة →
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
