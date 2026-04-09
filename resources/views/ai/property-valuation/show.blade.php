@extends('admin.layouts.admin')

@section('title', 'تفاصيل التقييم العقاري')
@section('page-title', 'تفاصيل التقييم العقاري')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">
                <i class="fas fa-file-contract text-blue-500 ml-2"></i>
                تقييم العقار: {{ $valuation->property->title ?? 'غير معروف' }}
            </h1>
            <p class="text-gray-600 mt-1">تاريخ التقييم: {{ $valuation->created_at->format('Y-m-d H:i') }}</p>
        </div>
        <div class="flex space-x-reverse space-x-3">
            @if($valuation->status !== 'completed')
            <a href="{{ route('ai.valuation.edit', $valuation) }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600 transition">
                <i class="fas fa-edit ml-1"></i>
                تعديل
            </a>
            @endif
            <a href="{{ route('ai.valuation.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                <i class="fas fa-arrow-right ml-1"></i>
                عودة
            </a>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Valuation Summary Card -->
        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-blue-500">
            <h2 class="text-lg font-bold text-gray-800 mb-4">ملخص التقييم</h2>
            <div class="space-y-4">
                <div class="flex justify-between items-center border-b pb-2">
                    <span class="text-gray-600">القيمة المقدرة</span>
                    <span class="text-xl font-bold text-blue-600">{{ number_format($valuation->estimated_value, 2) }} SAR</span>
                </div>
                <div class="flex justify-between items-center border-b pb-2">
                    <span class="text-gray-600">نقطة الثقة</span>
                    <span class="text-lg font-bold {{ $valuation->confidence_score >= 0.8 ? 'text-green-600' : ($valuation->confidence_score >= 0.5 ? 'text-yellow-600' : 'text-red-600') }}">
                        {{ number_format($valuation->confidence_score * 100, 1) }}%
                    </span>
                </div>
                <div class="flex justify-between items-center border-b pb-2">
                    <span class="text-gray-600">نموذج التقييم</span>
                    <span class="text-gray-800">{{ $valuationDetails['model_used'] ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">الحالة</span>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                        {{ $valuation->status === 'completed' ? 'bg-green-100 text-green-800' : 
                           ($valuation->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ match($valuation->status) {
                            'completed' => 'مكتمل',
                            'pending' => 'قيد المعالجة',
                            'failed' => 'فشل',
                            default => $valuation->status
                        } }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Property Details Card -->
        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-green-500">
            <h2 class="text-lg font-bold text-gray-800 mb-4">تفاصيل العقار</h2>
            <div class="space-y-3">
                <p><span class="text-gray-600 ml-2">العنوان:</span> {{ $valuation->property->address ?? 'غير متوفر' }}</p>
                <p><span class="text-gray-600 ml-2">المساحة:</span> {{ $valuation->property->area ?? 0 }} م²</p>
                <p><span class="text-gray-600 ml-2">الغرف:</span> {{ $valuation->property->bedrooms ?? 0 }}</p>
                <p><span class="text-gray-600 ml-2">الحمامات:</span> {{ $valuation->property->bathrooms ?? 0 }}</p>
                <p><span class="text-gray-600 ml-2">السعر الأصلي:</span> {{ number_format($valuation->property->price ?? 0, 2) }} SAR</p>
            </div>
        </div>

        <!-- Market Analysis Card -->
        <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-purple-500">
            <h2 class="text-lg font-bold text-gray-800 mb-4">تحليل السوق</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">الطلب في السوق:</span>
                    <span class="font-semibold">{{ number_format(($marketAnalysis['market_demand'] ?? 0) * 100, 1) }}%</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">اتجاه الأسعار:</span>
                    <span class="font-semibold capitalize">{{ $marketAnalysis['price_trend'] ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">متوسط سعر المتر:</span>
                    <span class="font-semibold">{{ number_format($marketAnalysis['price_per_sqm'] ?? 0, 2) }} SAR</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        <!-- Features Used -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">الميزات المستخدمة في التقييم</h3>
            @if(!empty($valuation->property_features))
                <div class="grid grid-cols-2 gap-4">
                    @foreach($valuation->property_features as $key => $value)
                    <div class="bg-gray-50 p-3 rounded border">
                        <span class="text-gray-600 text-sm block">{{ str_replace('_', ' ', ucfirst($key)) }}</span>
                        <span class="font-semibold text-gray-800">{{ $value }}</span>
                    </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">لا توجد ميزات مسجلة.</p>
            @endif
        </div>

        <!-- Comparable Properties -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">عقارات مشابهة</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">العقار</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">المساحة</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500">السعر</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($comparableProperties as $comp)
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-900">{{ $comp->title }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500">{{ $comp->area }} م²</td>
                            <td class="px-4 py-2 text-sm font-medium text-gray-900">{{ number_format($comp->price, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-center text-gray-500">لا توجد عقارات مشابهة.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Notes Section -->
    @if($valuation->notes)
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h3 class="text-lg font-bold text-gray-800 mb-2">ملاحظات</h3>
        <p class="text-gray-700 bg-yellow-50 p-4 rounded border border-yellow-100">{{ $valuation->notes }}</p>
    </div>
    @endif
</div>
@endsection
