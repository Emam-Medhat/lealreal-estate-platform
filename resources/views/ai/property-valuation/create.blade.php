@extends('admin.layouts.admin')

@section('title', 'تقييم عقاري جديد - الذكاء الاصطناعي')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">تقييم عقاري جديد</h1>
        <p class="text-gray-600">قم بإنشاء تقييم جديد لعقار باستخدام نماذج الذكاء الاصطناعي.</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('ai.valuation.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Property Selection -->
                <div>
                    <label for="property_id" class="block text-sm font-medium text-gray-700 mb-2">العقار</label>
                    <select name="property_id" id="property_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                        <option value="">اختر العقار...</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                                {{ $property->title }} ({{ $property->location ?? 'غير محدد' }})
                            </option>
                        @endforeach
                    </select>
                    @error('property_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Valuation Model -->
                <div>
                    <label for="valuation_model" class="block text-sm font-medium text-gray-700 mb-2">نموذج التقييم</label>
                    <select name="valuation_model" id="valuation_model" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                        @foreach($valuationModels as $key => $label)
                            <option value="{{ $key }}" {{ old('valuation_model') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('valuation_model')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Valuation Date -->
                <div>
                    <label for="valuation_date" class="block text-sm font-medium text-gray-700 mb-2">تاريخ التقييم</label>
                    <input type="date" name="valuation_date" id="valuation_date" value="{{ old('valuation_date', date('Y-m-d')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                    @error('valuation_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Hidden/Default Fields for Arrays (Simplified for UI) -->
            <!-- In a real app, these would be dynamic lists -->
            <input type="hidden" name="data_points[]" value="point1">
            <input type="hidden" name="data_points[]" value="point2">
            <input type="hidden" name="data_points[]" value="point3">
            <input type="hidden" name="data_points[]" value="point4">
            <input type="hidden" name="data_points[]" value="point5">
            
            <input type="hidden" name="property_features[]" value="feature1">
            <input type="hidden" name="market_conditions[]" value="condition1">

            <!-- Notes -->
            <div class="mb-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">ملاحظات إضافية</label>
                <textarea name="notes" id="notes" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <a href="{{ route('ai.valuation.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg mr-2 hover:bg-gray-300 transition-colors">إلغاء</a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-calculator ml-2"></i> بدء التقييم
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
