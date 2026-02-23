@extends('admin.layouts.admin')

@section('title', 'تعديل التوقع - ' . ($prediction->property->title ?? 'عقار غير معروف'))

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">تعديل التوقع</h1>
        <p class="text-gray-600">تحديث بيانات التوقع للعقار: {{ $prediction->property->title ?? 'عقار غير معروف' }}</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('ai.price-predictor.update', $prediction) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Property (Disabled) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">العقار</label>
                    <input type="text" value="{{ $prediction->property->title ?? 'عقار غير معروف' }}" class="w-full rounded-md border-gray-300 bg-gray-100 cursor-not-allowed" disabled>
                </div>

                <!-- Prediction Model (Disabled) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">نموذج التوقع</label>
                    <input type="text" value="{{ $predictionModels[$prediction->prediction_model] ?? $prediction->prediction_model }}" class="w-full rounded-md border-gray-300 bg-gray-100 cursor-not-allowed" disabled>
                </div>

                <!-- Notes -->
                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">ملاحظات إضافية</label>
                    <textarea name="notes" id="notes" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">{{ old('notes', $prediction->notes) }}</textarea>
                    @error('notes')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <a href="{{ route('ai.price-predictor.show', $prediction) }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg mr-2 hover:bg-gray-300 transition-colors">إلغاء</a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-save ml-2"></i> حفظ التعديلات
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
