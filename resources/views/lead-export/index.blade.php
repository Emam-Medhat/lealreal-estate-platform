@extends('admin.layouts.admin')

@section('title', 'تصدير العملاء')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">تصدير العملاء</h1>
            <p class="text-gray-600 mt-1">تصدير بيانات العملاء المحتملين بصيغ مختلفة</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('lead-export.create') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-file-export ml-2"></i>
                تصدير جديد
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="text-center py-8">
        <i class="fas fa-file-csv text-gray-400 text-4xl mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900">سجل عمليات التصدير</h3>
        <p class="text-gray-500 mt-2">لا توجد عمليات تصدير سابقة.</p>
    </div>
</div>
@endsection
