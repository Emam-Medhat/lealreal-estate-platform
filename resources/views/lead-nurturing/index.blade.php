@extends('admin.layouts.admin')

@section('title', 'رعاية العملاء')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">رعاية العملاء</h1>
            <p class="text-gray-600 mt-1">إدارة حملات وأنشطة رعاية العملاء المحتملين</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('lead-nurturing.create') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-plus ml-2"></i>
                نشاط جديد
            </a>
            <a href="{{ route('lead-nurturing.automation') }}" class="bg-white text-gray-700 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-robot ml-2"></i>
                الأتمتة
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    @if($leads->count() > 0)
        <table class="w-full text-right">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-900">العميل</th>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-900">المصدر</th>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-900">الحالة</th>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-900">آخر تواصل</th>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-900">المسؤول</th>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-900">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($leads as $lead)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $lead->first_name }} {{ $lead->last_name }}</div>
                            <div class="text-sm text-gray-500">{{ $lead->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $lead->source->name ?? 'غير محدد' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $lead->status->name ?? 'غير محدد' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $lead->last_contact_at ? $lead->last_contact_at->diffForHumans() : 'لم يتم التواصل' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $lead->assignedUser->name ?? 'غير معين' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('lead-nurturing.show', $lead->id) }}" class="text-purple-600 hover:text-purple-900">عرض</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $leads->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900">لا توجد عملاء للمتابعة</h3>
            <p class="text-gray-500 mt-2">جميع العملاء المحتملين تم التواصل معهم مؤخراً.</p>
        </div>
    @endif
</div>
@endsection
