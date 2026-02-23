@extends('admin.layouts.admin')

@section('title', 'تذكيرات المتابعة')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">تذكيرات المتابعة</h1>
            <p class="text-gray-600 mt-1">قائمة بالعملاء الذين يحتاجون إلى متابعة</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('lead-nurturing.index') }}" class="text-gray-600 hover:text-gray-900 font-medium ml-4">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة للقائمة
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    @if(isset($reminders) && $reminders->count() > 0)
        <table class="w-full text-right">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-900">العميل</th>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-900">الحالة</th>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-900">آخر تواصل</th>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-900">المسؤول</th>
                    <th class="px-6 py-4 text-sm font-semibold text-gray-900">الإجراء المطلوب</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($reminders as $reminder)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="font-medium text-gray-900">{{ $reminder->first_name }} {{ $reminder->last_name }}</div>
                            <div class="text-sm text-gray-500">{{ $reminder->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $reminder->status->name ?? 'غير محدد' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $reminder->last_contact_at ? $reminder->last_contact_at->diffForHumans() : 'لم يتم التواصل' }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $reminder->assignedUser->name ?? 'غير معين' }}
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('lead-nurturing.create', ['lead_id' => $reminder->id]) }}" class="text-purple-600 hover:text-purple-900 font-medium">متابعة الآن</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="text-center py-12">
            <i class="fas fa-check-circle text-green-500 text-5xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900">لا توجد تذكيرات</h3>
            <p class="text-gray-500 mt-2">جميع العملاء تمت متابعتهم بشكل جيد.</p>
        </div>
    @endif
</div>
@endsection
