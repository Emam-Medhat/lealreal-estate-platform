@extends('admin.layouts.admin')

@section('title', 'تعديل التقييم العقاري')
@section('page-title', 'تعديل التقييم العقاري')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">تعديل التقييم العقاري</h1>
            <a href="{{ route('ai.valuation.show', $valuation) }}" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-right ml-1"></i>
                إلغاء
            </a>
        </div>

        @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                </div>
                <div class="mr-3">
                    <p class="text-sm text-red-700">يرجى تصحيح الأخطاء التالية:</p>
                    <ul class="list-disc list-inside text-sm text-red-600 mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <form action="{{ route('ai.valuation.update', $valuation) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Property Info (Read-only) -->
            <div class="mb-6 p-4 bg-gray-50 rounded border border-gray-200">
                <label class="block text-sm font-medium text-gray-700 mb-2">العقار</label>
                <input type="text" value="{{ $valuation->property->title ?? 'غير معروف' }}" disabled class="w-full rounded-md border-gray-300 bg-gray-100 text-gray-500 shadow-sm">
                <p class="text-xs text-gray-500 mt-1">لا يمكن تغيير العقار لتقييم موجود.</p>
            </div>

            <!-- Notes -->
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">ملاحظات</label>
                <textarea name="notes" id="notes" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">{{ old('notes', $valuation->notes) }}</textarea>
            </div>

            <!-- Note about dynamic fields -->
            <div class="mb-6 p-4 bg-blue-50 rounded border border-blue-100">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-500"></i>
                    </div>
                    <div class="mr-3">
                        <p class="text-sm text-blue-700">
                            عند تحديث هذا التقييم، سيتم إعادة تشغيل عملية التحليل بالذكاء الاصطناعي بناءً على البيانات الجديدة.
                            قد تتغير القيمة المقدرة ونقطة الثقة.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-reverse space-x-3">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition shadow-lg">
                    <i class="fas fa-save ml-1"></i>
                    حفظ وتحديث التقييم
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
