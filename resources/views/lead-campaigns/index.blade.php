@extends('admin.layouts.admin')

@section('title', 'حملات التسويق')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">حملات التسويق</h1>
            <p class="text-gray-600 mt-1">إدارة حملات جذب العملاء المحتملين</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <!-- Actions -->
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <div class="text-center py-8">
        <i class="fas fa-bullhorn text-gray-400 text-4xl mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900">الحملات النشطة</h3>
        <p class="text-gray-500 mt-2">لا توجد حملات نشطة حالياً.</p>
    </div>
</div>
@endsection
