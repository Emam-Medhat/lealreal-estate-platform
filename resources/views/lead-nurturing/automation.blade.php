@extends('admin.layouts.admin')

@section('title', 'أتمتة رعاية العملاء')

@section('content')
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">أتمتة رعاية العملاء</h1>
            <p class="text-gray-600 mt-1">قواعد الأتمتة لإرسال الرسائل وتعيين المهام</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('lead-nurturing.create-automation') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-plus ml-2"></i>
                قاعدة أتمتة جديدة
            </a>
            <a href="{{ route('lead-nurturing.index') }}" class="text-gray-600 hover:text-gray-900 font-medium ml-4">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة للقائمة
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="text-center py-12">
        <i class="fas fa-robot text-gray-300 text-5xl mb-4"></i>
        <h3 class="text-lg font-medium text-gray-900">لا توجد قواعد أتمتة حالياً</h3>
        <p class="text-gray-500 mt-2">ابدأ بإنشاء قاعدة جديدة لأتمتة عملية رعاية العملاء.</p>
    </div>
</div>
@endsection
