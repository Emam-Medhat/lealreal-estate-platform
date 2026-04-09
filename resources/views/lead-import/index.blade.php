@extends('admin.layouts.admin')

@section('title', 'استيراد العملاء')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">استيراد العملاء</h1>
            <p class="text-gray-600 mt-1">استيراد بيانات العملاء من ملفات Excel أو CSV</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('lead-import.create') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-file-import ml-2"></i>
                استيراد جديد
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="text-center py-8">
        <i class="fas fa-history text-gray-400 text-4xl mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900">سجل عمليات الاستيراد</h3>
        <p class="text-gray-500 mt-2">لا توجد عمليات استيراد سابقة.</p>
    </div>
</div>
@endsection
