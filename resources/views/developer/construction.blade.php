@extends('admin.layouts.admin')

@section('title', 'مشاريع البناء')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">مشاريع البناء</h1>
            <p class="text-gray-600 mt-2">إدارة وتتبع مشاريع البناء والتطوير</p>
        </div>
        <div class="flex space-x-3 space-x-reverse">
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus ml-2"></i>
                مشروع جديد
            </button>
            <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                <i class="fas fa-download ml-2"></i>
                تصدير
            </button>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">إجمالي المشاريع</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total_projects'] ?? 0 }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-building text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">المشاريع النشطة</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active_projects'] ?? 0 }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-tools text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">المشاريع المكتملة</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['completed_projects'] ?? 0 }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-lg">
                    <i class="fas fa-check-circle text-purple-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">إجمالي الميزانية</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_budget'] ?? 0, 0) }} ريال</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-coins text-yellow-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Projects Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">المشاريع الحديثة</h3>
                <div class="flex space-x-2 space-x-reverse">
                    <button class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-filter ml-1"></i>فلترة
                    </button>
                    <button class="px-3 py-1 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-sort ml-1"></i>ترتيب
                    </button>
                </div>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المشروع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المطور</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المقاول</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التقدم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الميزانية</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if($recentProjects->count() > 0)
                        @foreach($recentProjects as $project)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="bg-blue-100 p-2 rounded-lg ml-3">
                                        <i class="fas fa-building text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $project->name ?? 'مشروع بدون اسم' }}</p>
                                        <p class="text-sm text-gray-600">{{ $project->location ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ $project->company_name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ $project->contractor_name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($project->status == 'completed') bg-green-100 text-green-800
                                    @elseif($project->status == 'in_progress') bg-blue-100 text-blue-800
                                    @elseif($project->status == 'planning') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $project->status ?? 'planning' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="bg-gray-200 rounded-full h-2 w-20 ml-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $project->progress_percentage ?? 0 }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600">{{ $project->progress_percentage ?? 0 }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ number_format($project->total_cost ?? 0, 0) }} ريال</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-sm text-gray-900">{{ $project->created_at ? $project->created_at->format('Y-m-d') : 'N/A' }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-left">
                                <button class="text-blue-600 hover:text-blue-900 ml-3" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="text-green-600 hover:text-green-900 ml-3" title="تحرير">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-purple-600 hover:text-purple-900 ml-3" title="تحديث">
                                    <i class="fas fa-sync"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-900" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center">
                                <i class="fas fa-building text-gray-300 text-4xl mb-3"></i>
                                <p class="text-gray-500">لا توجد مشاريع حالياً</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
