@extends('admin.layouts.admin')

@section('title', 'قاعدة أتمتة جديدة')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">إنشاء قاعدة أتمتة</h1>
            <p class="text-gray-600 mt-1">تعريف قواعد الأتمتة لتعيين وتحديث العملاء</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('lead-automation.index') }}" class="text-gray-600 hover:text-gray-900 font-medium">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة للقائمة
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 max-w-2xl mx-auto">
    <div class="text-center py-8">
        <i class="fas fa-tools text-gray-400 text-4xl mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900">تحت الإنشاء</h3>
        <p class="text-gray-500 mt-2">نموذج الأتمتة قيد التطوير.</p>
    </div>
</div>
@endsection
