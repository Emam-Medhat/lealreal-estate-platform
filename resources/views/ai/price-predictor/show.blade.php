@extends('admin.layouts.admin')

@section('title', 'تفاصيل التوقع - ' . ($prediction->property->title ?? 'عقار غير معروف'))

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">تفاصيل التوقع</h1>
            <p class="text-gray-600 mt-2">توقع السعر للعقار: {{ $prediction->property->title ?? 'عقار غير معروف' }}</p>
        </div>
        <div class="flex space-x-3 space-x-reverse">
            <a href="{{ route('ai.price-predictor.index') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition-colors flex items-center">
                <i class="fas fa-arrow-right ml-2"></i>
                عودة للقائمة
            </a>
            @if($prediction->status !== 'completed')
                <a href="{{ route('ai.price-predictor.edit', $prediction->id) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition-colors flex items-center">
                    <i class="fas fa-edit ml-2"></i>
                    تعديل
                </a>
            @endif
        </div>
    </div>

    <!-- Prediction Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Price Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">السعر المتوقع</h3>
                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">{{ $prediction->prediction_model }}</span>
            </div>
            <div class="text-4xl font-bold text-blue-600 mb-2">
                {{ number_format($prediction->predicted_price, 2) }} SAR
            </div>
            <p class="text-sm text-gray-500">
                النطاق: {{ number_format($prediction->confidence_interval['lower_bound'] ?? 0, 2) }} - {{ number_format($prediction->confidence_interval['upper_bound'] ?? 0, 2) }}
            </p>
        </div>

        <!-- Accuracy Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">دقة التوقع</h3>
                <div class="w-12 h-12 rounded-full bg-{{ $prediction->accuracy > 0.8 ? 'green' : ($prediction->accuracy > 0.5 ? 'yellow' : 'red') }}-100 flex items-center justify-center">
                    <i class="fas fa-bullseye text-{{ $prediction->accuracy > 0.8 ? 'green' : ($prediction->accuracy > 0.5 ? 'yellow' : 'red') }}-600 text-xl"></i>
                </div>
            </div>
            <div class="text-4xl font-bold text-gray-900 mb-2">
                {{ number_format($prediction->accuracy * 100, 1) }}%
            </div>
            <p class="text-sm text-gray-500">بناءً على {{ count($prediction->historical_data ?? []) }} نقطة بيانات تاريخية</p>
        </div>

        <!-- Status Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">حالة التوقع</h3>
                <span class="px-3 py-1 rounded-full text-sm font-medium 
                    {{ $prediction->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ ucfirst($prediction->status) }}
                </span>
            </div>
            <div class="text-sm text-gray-600">
                <p class="mb-2"><span class="font-medium">تاريخ الإنشاء:</span> {{ $prediction->created_at->format('Y-m-d H:i') }}</p>
                <p class="mb-2"><span class="font-medium">آخر تحديث:</span> {{ $prediction->updated_at->format('Y-m-d H:i') }}</p>
                <p><span class="font-medium">بواسطة:</span> {{ $prediction->user->name ?? 'System' }}</p>
            </div>
        </div>
    </div>

    <!-- Analysis Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Market Analysis -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">تحليل السوق</h3>
            <div class="space-y-4">
                @foreach($marketAnalysis['market_trends'] ?? [] as $key => $value)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                        <span class="font-medium">{{ is_numeric($value) ? number_format($value, 2) : $value }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Economic Outlook -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">المؤشرات الاقتصادية</h3>
            <div class="space-y-4">
                @foreach($prediction->economic_indicators ?? [] as $key => $value)
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">{{ ucfirst(str_replace('_', ' ', $key)) }}</span>
                        <span class="font-medium">{{ is_numeric($value) ? number_format($value, 4) : $value }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Property Details -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">تفاصيل العقار المستخدمة</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($prediction->property_features ?? [] as $key => $value)
                <div class="bg-gray-50 p-3 rounded-lg">
                    <p class="text-xs text-gray-500 uppercase">{{ str_replace('_', ' ', $key) }}</p>
                    <p class="font-semibold text-gray-900">{{ $value }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Notes -->
    @if($prediction->notes)
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
            <h3 class="text-lg font-semibold text-yellow-800 mb-2">ملاحظات</h3>
            <p class="text-yellow-700">{{ $prediction->notes }}</p>
        </div>
    @endif
</div>
@endsection
