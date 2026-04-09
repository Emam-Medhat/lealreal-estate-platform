@extends('admin.layouts.admin')

@section('title', 'توقع جديد - الذكاء الاصطناعي')

@section('content')
<div class="container-fluid px-4 py-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-800">توقع سعر جديد</h1>
        <p class="text-gray-600">قم بإنشاء توقع جديد لسعر عقار باستخدام نماذج الذكاء الاصطناعي.</p>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form action="{{ route('ai.price-predictor.store') }}" method="POST">
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

                <!-- Prediction Model -->
                <div>
                    <label for="prediction_model" class="block text-sm font-medium text-gray-700 mb-2">نموذج التوقع</label>
                    <select name="prediction_model" id="prediction_model" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                        @foreach($predictionModels as $key => $label)
                            <option value="{{ $key }}" {{ old('prediction_model') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('prediction_model')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Time Horizon -->
                <div>
                    <label for="time_horizon" class="block text-sm font-medium text-gray-700 mb-2">المدى الزمني</label>
                    <select name="time_horizon" id="time_horizon" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                        @foreach($timeHorizons as $key => $label)
                            <option value="{{ $key }}" {{ old('time_horizon') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('time_horizon')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Prediction Date -->
                <div>
                    <label for="prediction_date" class="block text-sm font-medium text-gray-700 mb-2">تاريخ التوقع</label>
                    <input type="date" name="prediction_date" id="prediction_date" value="{{ old('prediction_date', date('Y-m-d')) }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" required>
                    @error('prediction_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

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
                <a href="{{ route('ai.price-predictor.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg mr-2 hover:bg-gray-300 transition-colors">إلغاء</a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-magic ml-2"></i> إنشاء التوقع
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
