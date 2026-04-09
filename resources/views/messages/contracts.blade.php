@extends('layouts.app')

@section('title', 'العقود والرسائل')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-blue-50 to-purple-50 py-8 relative overflow-hidden">
    <!-- Animated Background Elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-blue-200 to-indigo-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-br from-purple-200 to-pink-200 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse" style="animation-delay: 2s"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-gradient-to-br from-indigo-200 to-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-pulse" style="animation-delay: 4s"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Enhanced Header -->
        <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/50 p-8 mb-8 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-r from-blue-500/5 via-purple-500/5 to-pink-500/5"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 rounded-2xl p-5 mr-4 shadow-2xl transform hover:scale-110 transition-all duration-300 hover:rotate-3">
                                <i class="fas fa-file-contract text-white text-4xl"></i>
                            </div>
                            <div>
                                <h1 class="text-5xl font-bold bg-gradient-to-r from-blue-800 via-purple-800 to-pink-800 bg-clip-text text-transparent mb-2">العقود والرسائل</h1>
                                <p class="text-gray-600 text-xl">إدارة العقود والمراسلات بذكاء واحترافية</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-4">
                        <a href="{{ route('messages.inbox') }}" class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-4 rounded-2xl hover:from-blue-700 hover:to-blue-800 transition-all duration-300 transform hover:scale-105 shadow-xl hover:shadow-2xl flex items-center group">
                            <i class="fas fa-inbox ml-3 text-xl group-hover:translate-x-1 transition-transform duration-300"></i>
                            <span class="font-bold text-lg">الرسائل</span>
                        </a>
                        <a href="{{ route('contracts.index') }}" class="bg-gradient-to-r from-purple-600 to-pink-600 text-white px-8 py-4 rounded-2xl hover:from-purple-700 hover:to-pink-700 transition-all duration-300 transform hover:scale-105 shadow-xl hover:shadow-2xl flex items-center group">
                            <i class="fas fa-file-contract ml-3 text-xl group-hover:translate-x-1 transition-transform duration-300"></i>
                            <span class="font-bold text-lg">العقود</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 border-2 border-blue-200 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 p-8 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-20 h-20 bg-blue-200/30 rounded-full -mr-10 -mt-10 group-hover:scale-150 transition-transform duration-300"></div>
                <div class="flex items-center relative z-10">
                    <div class="flex-shrink-0 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl p-5 shadow-xl transform group-hover:rotate-12 transition-transform duration-300">
                        <i class="fas fa-envelope text-white text-3xl"></i>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-bold text-blue-700 text-lg">الرسائل الجديدة</p>
                        <p class="text-4xl font-bold text-blue-900">12</p>
                        <p class="text-xs text-blue-600 mt-1">+3 من أمس</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 border-2 border-purple-200 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 p-8 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-20 h-20 bg-purple-200/30 rounded-full -mr-10 -mt-10 group-hover:scale-150 transition-transform duration-300"></div>
                <div class="flex items-center relative z-10">
                    <div class="flex-shrink-0 bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl p-5 shadow-xl transform group-hover:rotate-12 transition-transform duration-300">
                        <i class="fas fa-file-contract text-white text-3xl"></i>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-bold text-purple-700 text-lg">العقود النشطة</p>
                        <p class="text-4xl font-bold text-purple-900">8</p>
                        <p class="text-xs text-purple-600 mt-1">2 تحت المراجعة</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-green-50 to-green-100 border-2 border-green-200 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 p-8 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-20 h-20 bg-green-200/30 rounded-full -mr-10 -mt-10 group-hover:scale-150 transition-transform duration-300"></div>
                <div class="flex items-center relative z-10">
                    <div class="flex-shrink-0 bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-5 shadow-xl transform group-hover:rotate-12 transition-transform duration-300">
                        <i class="fas fa-check-circle text-white text-3xl"></i>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-bold text-green-700 text-lg">العقود المكتملة</p>
                        <p class="text-4xl font-bold text-green-900">24</p>
                        <p class="text-xs text-green-600 mt-1">+5 هذا الشهر</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-yellow-50 to-orange-100 border-2 border-yellow-200 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 p-8 relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-20 h-20 bg-yellow-200/30 rounded-full -mr-10 -mt-10 group-hover:scale-150 transition-transform duration-300"></div>
                <div class="flex items-center relative z-10">
                    <div class="flex-shrink-0 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-2xl p-5 shadow-xl transform group-hover:rotate-12 transition-transform duration-300">
                        <i class="fas fa-clock text-white text-3xl"></i>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-bold text-yellow-700 text-lg">في انتظار التوقيع</p>
                        <p class="text-4xl font-bold text-yellow-900">3</p>
                        <p class="text-xs text-yellow-600 mt-1">1 مستعجل</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Enhanced Recent Messages -->
            <div class="bg-gradient-to-br from-white to-gray-50 border-2 border-gray-200 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300">
                <div class="p-8 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                    <h3 class="text-3xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent flex items-center">
                        <i class="fas fa-envelope text-blue-500 ml-4 text-3xl"></i>
                        الرسائل الأخيرة
                    </h3>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Enhanced Message Item -->
                    <div class="flex items-start space-x-5 p-6 bg-gradient-to-r from-blue-50 to-blue-100 border-2 border-blue-200 rounded-3xl hover:from-blue-100 hover:to-blue-200 transition-all duration-300 transform hover:scale-102 hover:shadow-lg group">
                        <div class="w-16 h-16 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0 shadow-xl group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-user text-white text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-blue-900 text-xl">أحمد محمد</h4>
                                <span class="text-sm text-blue-600 font-bold bg-blue-50 px-3 py-1 rounded-full">منذ 5 دقائق</span>
                            </div>
                            <p class="text-gray-700 mt-2 text-lg">مرحباً، أود الاستفسار عن تفاصيل العقد...</p>
                            <div class="flex items-center space-x-2 mt-3">
                                <span class="text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded-full">مهم</span>
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">عقود</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-5 p-6 bg-gradient-to-r from-purple-50 to-purple-100 border-2 border-purple-200 rounded-3xl hover:from-purple-100 hover:to-purple-200 transition-all duration-300 transform hover:scale-102 hover:shadow-lg group">
                        <div class="w-16 h-16 bg-gradient-to-r from-purple-400 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0 shadow-xl group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-building text-white text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-purple-900 text-xl">شركة العقارات</h4>
                                <span class="text-sm text-purple-600 font-bold bg-purple-50 px-3 py-1 rounded-full">منذ ساعة</span>
                            </div>
                            <p class="text-gray-700 mt-2 text-lg">تم إرسال نسخة العقد للموافقة</p>
                            <div class="flex items-center space-x-2 mt-3">
                                <span class="text-xs text-purple-600 bg-purple-50 px-2 py-1 rounded-full">عقد</span>
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">مرفق</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-5 p-6 bg-gradient-to-r from-green-50 to-green-100 border-2 border-green-200 rounded-3xl hover:from-green-100 hover:to-green-200 transition-all duration-300 transform hover:scale-102 hover:shadow-lg group">
                        <div class="w-16 h-16 bg-gradient-to-r from-green-400 to-green-600 rounded-full flex items-center justify-center flex-shrink-0 shadow-xl group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-check text-white text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-green-900 text-xl">نظام التوقيع</h4>
                                <span class="text-sm text-green-600 font-bold bg-green-50 px-3 py-1 rounded-full">منذ ساعتين</span>
                            </div>
                            <p class="text-gray-700 mt-2 text-lg">تم توقيع العقد بنجاح</p>
                            <div class="flex items-center space-x-2 mt-3">
                                <span class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded-full">مكتمل</span>
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">نظام</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-6 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                    <a href="{{ route('messages.inbox') }}" class="text-blue-600 hover:text-blue-700 font-bold text-xl flex items-center justify-center group">
                        عرض جميع الرسائل
                        <i class="fas fa-arrow-left mr-3 text-xl group-hover:translate-x-2 transition-transform duration-300"></i>
                    </a>
                </div>
            </div>

            <!-- Enhanced Recent Contracts -->
            <div class="bg-gradient-to-br from-white to-gray-50 border-2 border-gray-200 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300">
                <div class="p-8 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                    <h3 class="text-3xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent flex items-center">
                        <i class="fas fa-file-contract text-purple-500 ml-4 text-3xl"></i>
                        العقود الأخيرة
                    </h3>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Enhanced Contract Item -->
                    <div class="flex items-start space-x-5 p-6 bg-gradient-to-r from-purple-50 to-purple-100 border-2 border-purple-200 rounded-3xl hover:from-purple-100 hover:to-purple-200 transition-all duration-300 transform hover:scale-102 hover:shadow-lg group">
                        <div class="w-16 h-16 bg-gradient-to-r from-purple-400 to-purple-600 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-xl group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-file-contract text-white text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-purple-900 text-xl">عقد إيجار شقة</h4>
                                <span class="px-4 py-2 bg-yellow-100 text-yellow-800 text-sm font-bold rounded-full border-2 border-yellow-300">معلق</span>
                            </div>
                            <p class="text-gray-700 mt-2 text-lg">الطرف الأول: أحمد محمد</p>
                            <p class="text-gray-600 text-sm mt-3">تاريخ البدء: 2024-03-15</p>
                            <div class="flex items-center space-x-2 mt-3">
                                <span class="text-xs text-purple-600 bg-purple-50 px-2 py-1 rounded-full">إيجار</span>
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">سكني</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-5 p-6 bg-gradient-to-r from-green-50 to-green-100 border-2 border-green-200 rounded-3xl hover:from-green-100 hover:to-green-200 transition-all duration-300 transform hover:scale-102 hover:shadow-lg group">
                        <div class="w-16 h-16 bg-gradient-to-r from-green-400 to-green-600 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-xl group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-file-contract text-white text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-green-900 text-xl">عقد بيع عقار</h4>
                                <span class="px-4 py-2 bg-green-100 text-green-800 text-sm font-bold rounded-full border-2 border-green-300">مكتمل</span>
                            </div>
                            <p class="text-gray-700 mt-2 text-lg">الطرف الأول: شركة العقارات</p>
                            <p class="text-gray-600 text-sm mt-3">تاريخ البدء: 2024-02-01</p>
                            <div class="flex items-center space-x-2 mt-3">
                                <span class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded-full">بيع</span>
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">عقار</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-5 p-6 bg-gradient-to-r from-blue-50 to-blue-100 border-2 border-blue-200 rounded-3xl hover:from-blue-100 hover:to-blue-200 transition-all duration-300 transform hover:scale-102 hover:shadow-lg group">
                        <div class="w-16 h-16 bg-gradient-to-r from-blue-400 to-blue-600 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-xl group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-file-contract text-white text-2xl"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <h4 class="font-bold text-blue-900 text-xl">عقد شراء سيارة</h4>
                                <span class="px-4 py-2 bg-blue-100 text-blue-800 text-sm font-bold rounded-full border-2 border-blue-300">نشط</span>
                            </div>
                            <p class="text-gray-700 mt-2 text-lg">الطرف الأول: محمد علي</p>
                            <p class="text-gray-600 text-sm mt-3">تاريخ البدء: 2024-03-01</p>
                            <div class="flex items-center space-x-2 mt-3">
                                <span class="text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded-full">شراء</span>
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">سيارة</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-6 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                    <a href="{{ route('contracts.index') }}" class="text-purple-600 hover:text-purple-700 font-bold text-xl flex items-center justify-center group">
                        عرض جميع العقود
                        <i class="fas fa-arrow-left mr-3 text-xl group-hover:translate-x-2 transition-transform duration-300"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Enhanced Quick Actions -->
        <div class="bg-gradient-to-br from-yellow-50 via-orange-50 to-red-50 border-2 border-yellow-200 rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-300 p-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-yellow-200 to-orange-200 rounded-full -mr-16 -mt-16 opacity-30 animate-pulse"></div>
            <h3 class="text-3xl font-bold bg-gradient-to-r from-yellow-800 via-orange-600 to-red-600 bg-clip-text text-transparent mb-8 flex items-center relative z-10">
                <i class="fas fa-bolt text-yellow-500 ml-4 text-3xl"></i>
                إجراءات سريعة
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative z-10">
                <a href="{{ route('messages.send') }}" class="flex items-center space-x-4 p-6 bg-white/90 rounded-2xl hover:bg-white transition-all duration-300 border-2 border-yellow-200 hover:border-yellow-300 transform hover:scale-105 hover:shadow-lg group">
                    <div class="w-14 h-14 bg-gradient-to-r from-blue-400 to-blue-600 rounded-full flex items-center justify-center flex-shrink-0 shadow-xl group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-paper-plane text-white text-xl"></i>
                    </div>
                    <span class="font-bold text-gray-800 text-lg">إرسال رسالة</span>
                </a>
                
                <a href="{{ route('contracts.create') }}" class="flex items-center space-x-4 p-6 bg-white/90 rounded-2xl hover:bg-white transition-all duration-300 border-2 border-yellow-200 hover:border-yellow-300 transform hover:scale-105 hover:shadow-lg group">
                    <div class="w-14 h-14 bg-gradient-to-r from-purple-400 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0 shadow-xl group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-plus text-white text-xl"></i>
                    </div>
                    <span class="font-bold text-gray-800 text-lg">إنشاء عقد</span>
                </a>
                
                <a href="{{ route('messages.appointments') }}" class="flex items-center space-x-4 p-6 bg-white/90 rounded-2xl hover:bg-white transition-all duration-300 border-2 border-yellow-200 hover:border-yellow-300 transform hover:scale-105 hover:shadow-lg group">
                    <div class="w-14 h-14 bg-gradient-to-r from-green-400 to-green-600 rounded-full flex items-center justify-center flex-shrink-0 shadow-xl group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-calendar text-white text-xl"></i>
                    </div>
                    <span class="font-bold text-gray-800 text-lg">المواعيد</span>
                </a>
                
                <a href="#" class="flex items-center space-x-4 p-6 bg-white/90 rounded-2xl hover:bg-white transition-all duration-300 border-2 border-yellow-200 hover:border-yellow-300 transform hover:scale-105 hover:shadow-lg group">
                    <div class="w-14 h-14 bg-gradient-to-r from-red-400 to-red-600 rounded-full flex items-center justify-center flex-shrink-0 shadow-xl group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-bell text-white text-xl"></i>
                    </div>
                    <span class="font-bold text-gray-800 text-lg">الإشعارات</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
