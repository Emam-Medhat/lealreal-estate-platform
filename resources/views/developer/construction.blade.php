@extends('admin.layouts.admin')

@section('title', 'مشاريع البناء')
@section('page-title', 'مشاريع البناء')

@section('content')
    <!-- Page Header -->
    <div class="mb-8">
        <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 rounded-3xl shadow-2xl p-8 text-white relative overflow-hidden">
            <!-- Animated Background Pattern -->
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full -mr-48 -mt-48 animate-pulse"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-white rounded-full -ml-32 -mb-32 animate-pulse" style="animation-delay: 1s;"></div>
                <div class="absolute top-1/2 left-1/2 w-48 h-48 bg-yellow-300 rounded-full -ml-24 -mt-24 animate-pulse" style="animation-delay: 2s;"></div>
            </div>
            
            <!-- Grid Pattern Overlay -->
            <div class="absolute inset-0 opacity-10">
                <div class="h-full w-full" style="background-image: linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 50px 50px;"></div>
            </div>
            
            <div class="relative z-10">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="bg-white bg-opacity-25 rounded-2xl p-4 ml-4 backdrop-blur-sm border border-white border-opacity-40 shadow-xl transform hover:scale-105 transition-transform duration-300">
                                <i class="fas fa-hard-hat text-white text-3xl"></i>
                            </div>
                            <div>
                                <h1 class="text-5xl font-black text-white mb-2 bg-clip-text text-transparent bg-gradient-to-r from-white to-yellow-200">مشاريع البناء</h1>
                                <p class="text-white text-xl font-medium">إدارة وتتبع مشاريع البناء والتطوير بأسلوب احترافي</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-6 mt-6">
                            <div class="bg-white bg-opacity-25 rounded-xl px-6 py-3 backdrop-blur-sm border border-white border-opacity-40 shadow-lg">
                                <span class="text-sm text-white font-medium">إجمالي المشاريع:</span>
                                <span class="text-lg font-black text-white ml-2">{{ $stats['total_projects'] ?? 0 }}</span>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-xl px-6 py-3 backdrop-blur-sm border border-white border-opacity-40 shadow-lg">
                                <span class="text-sm text-white font-medium">المشاريع النشطة:</span>
                                <span class="text-lg font-black text-white ml-2">{{ $stats['active_projects'] ?? 0 }}</span>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-xl px-6 py-3 backdrop-blur-sm border border-white border-opacity-40 shadow-lg">
                                <span class="text-sm text-white font-medium">المكتملة:</span>
                                <span class="text-lg font-black text-white ml-2">{{ $stats['completed_projects'] ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-reverse space-x-4">
                        <button class="inline-flex items-center px-8 py-4 bg-white text-indigo-600 rounded-2xl text-sm font-bold hover:bg-yellow-50 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-white transition-all duration-300 transform hover:scale-105 shadow-2xl hover:shadow-yellow-300/50">
                            <i class="fas fa-plus ml-2"></i>
                            مشروع جديد
                        </button>
                        <button class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-green-400 to-emerald-500 text-white rounded-2xl text-sm font-bold hover:from-green-500 hover:to-emerald-600 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-green-500 transition-all duration-300 transform hover:scale-105 shadow-2xl hover:shadow-green-500/50">
                            <i class="fas fa-download ml-2"></i>
                            تصدير
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl shadow-lg border border-blue-200 p-6 transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-600 font-medium mb-1">إجمالي المشاريع</p>
                    <p class="text-3xl font-black text-blue-900">{{ $stats['total_projects'] ?? 0 }}</p>
                    <p class="text-xs text-blue-600 mt-2">جميع المشاريع المسجلة</p>
                </div>
                <div class="bg-gradient-to-br from-blue-400 to-indigo-600 p-4 rounded-2xl shadow-lg">
                    <i class="fas fa-building text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-2xl shadow-lg border border-green-200 p-6 transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-600 font-medium mb-1">المشاريع النشطة</p>
                    <p class="text-3xl font-black text-green-900">{{ $stats['active_projects'] ?? 0 }}</p>
                    <p class="text-xs text-green-600 mt-2">قيد التنفيذ حالياً</p>
                </div>
                <div class="bg-gradient-to-br from-green-400 to-emerald-600 p-4 rounded-2xl shadow-lg">
                    <i class="fas fa-tools text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-pink-100 rounded-2xl shadow-lg border border-purple-200 p-6 transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-purple-600 font-medium mb-1">المشاريع المكتملة</p>
                    <p class="text-3xl font-black text-purple-900">{{ $stats['completed_projects'] ?? 0 }}</p>
                    <p class="text-xs text-purple-600 mt-2">تم الانتهاء منها</p>
                </div>
                <div class="bg-gradient-to-br from-purple-400 to-pink-600 p-4 rounded-2xl shadow-lg">
                    <i class="fas fa-check-circle text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-yellow-50 to-orange-100 rounded-2xl shadow-lg border border-yellow-200 p-6 transform hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-yellow-600 font-medium mb-1">إجمالي الميزانية</p>
                    <p class="text-3xl font-black text-yellow-900">{{ number_format($stats['total_budget'] ?? 0, 0) }}</p>
                    <p class="text-xs text-yellow-600 mt-2">ريال سعودي</p>
                </div>
                <div class="bg-gradient-to-br from-yellow-400 to-orange-600 p-4 rounded-2xl shadow-lg">
                    <i class="fas fa-coins text-white text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Projects Table -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-list-alt ml-3 text-indigo-600"></i>
                    المشاريع الحديثة
                </h3>
                <div class="flex space-x-2 space-x-reverse">
                    <button class="px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-xl hover:bg-indigo-50 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 shadow-sm border border-gray-200">
                        <i class="fas fa-filter ml-2"></i>فلترة
                    </button>
                    <button class="px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-xl hover:bg-indigo-50 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200 shadow-sm border border-gray-200">
                        <i class="fas fa-sort ml-2"></i>ترتيب
                    </button>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gradient-to-r from-indigo-50 to-purple-50">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-bold text-indigo-700 uppercase tracking-wider border-l border-indigo-100">المشروع</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-indigo-700 uppercase tracking-wider border-l border-indigo-100">المطور</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-indigo-700 uppercase tracking-wider border-l border-indigo-100">المقاول</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-indigo-700 uppercase tracking-wider border-l border-indigo-100">الحالة</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-indigo-700 uppercase tracking-wider border-l border-indigo-100">التقدم</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-indigo-700 uppercase tracking-wider border-l border-indigo-100">الميزانية</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-indigo-700 uppercase tracking-wider border-l border-indigo-100">التاريخ</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-indigo-700 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @if($recentProjects->count() > 0)
                        @foreach($recentProjects as $project)
                        <tr class="hover:bg-gradient-to-r hover:from-indigo-50 hover:to-purple-50 transition-all duration-200">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="bg-gradient-to-br from-blue-400 to-indigo-600 p-3 rounded-xl ml-3 shadow-lg">
                                        <i class="fas fa-building text-white"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 text-lg">{{ $project->name ?? 'مشروع بدون اسم' }}</p>
                                        <p class="text-sm text-gray-600 flex items-center mt-1">
                                            <i class="fas fa-map-marker-alt ml-2 text-red-500"></i>
                                            {{ $project->location ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="bg-gray-100 p-2 rounded-lg ml-2">
                                        <i class="fas fa-user-tie text-gray-600 text-sm"></i>
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $project->company_name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="bg-gray-100 p-2 rounded-lg ml-2">
                                        <i class="fas fa-hard-hat text-gray-600 text-sm"></i>
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $project->contractor_name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold shadow-sm
                                    @if($project->status == 'completed') bg-gradient-to-r from-green-400 to-emerald-500 text-white
                                    @elseif($project->status == 'in_progress') bg-gradient-to-r from-blue-400 to-indigo-500 text-white
                                    @elseif($project->status == 'planning') bg-gradient-to-r from-yellow-400 to-orange-500 text-white
                                    @else bg-gradient-to-r from-gray-400 to-gray-500 text-white @endif">
                                    @if($project->status == 'completed')
                                        <i class="fas fa-check-circle ml-2"></i>مكتمل
                                    @elseif($project->status == 'in_progress')
                                        <i class="fas fa-spinner ml-2"></i>قيد التنفيذ
                                    @elseif($project->status == 'planning')
                                        <i class="fas fa-clock ml-2"></i>التخطيط
                                    @else
                                        <i class="fas fa-pause ml-2"></i>{{ $project->status ?? 'planning' }}
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="bg-gray-200 rounded-full h-3 w-24 ml-3 overflow-hidden">
                                        <div class="bg-gradient-to-r from-blue-400 to-indigo-600 h-3 rounded-full transition-all duration-500" style="width: {{ $project->progress_percentage ?? 0 }}%"></div>
                                    </div>
                                    <span class="text-sm font-bold text-indigo-600">{{ $project->progress_percentage ?? 0 }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="bg-yellow-100 p-2 rounded-lg ml-2">
                                        <i class="fas fa-coins text-yellow-600 text-sm"></i>
                                    </div>
                                    <span class="font-bold text-gray-900">{{ number_format($project->total_cost ?? 0, 0) }}</span>
                                    <span class="text-sm text-gray-600 mr-1">ريال</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="bg-gray-100 p-2 rounded-lg ml-2">
                                        <i class="fas fa-calendar text-gray-600 text-sm"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">{{ $project->created_at ? $project->created_at->format('Y-m-d') : 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex space-x-reverse space-x-2">
                                    <button class="p-2 bg-blue-100 text-blue-600 rounded-lg hover:bg-blue-200 hover:text-blue-800 transition-all duration-200 shadow-sm" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="p-2 bg-green-100 text-green-600 rounded-lg hover:bg-green-200 hover:text-green-800 transition-all duration-200 shadow-sm" title="تحرير">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="p-2 bg-purple-100 text-purple-600 rounded-lg hover:bg-purple-200 hover:text-purple-800 transition-all duration-200 shadow-sm" title="تحديث">
                                        <i class="fas fa-sync"></i>
                                    </button>
                                    <button class="p-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200 hover:text-red-800 transition-all duration-200 shadow-sm" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="bg-gradient-to-br from-gray-100 to-gray-200 p-8 rounded-3xl mb-6">
                                        <i class="fas fa-building text-gray-400 text-6xl"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-700 mb-2">لا توجد مشاريع حالياً</h3>
                                    <p class="text-gray-500 mb-6">ابدأ بإضافة مشروع جديد للبدء في إدارة مشاريع البناء</p>
                                    <button class="px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl font-medium hover:from-indigo-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-lg">
                                        <i class="fas fa-plus ml-2"></i>
                                        إضافة مشروع جديد
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
