@extends('admin.layouts.admin')

@section('title', 'سجل التقييمات - الذكاء الاصطناعي')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">سجل التقييمات</h1>
            <p class="text-gray-600 mt-2">عرض وتصفية جميع عمليات تقييم العقارات السابقة.</p>
        </div>
        <a href="{{ route('ai.valuation.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
            <i class="fas fa-plus ml-2"></i>
            تقييم جديد
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <form action="{{ route('ai.valuation.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Property Filter -->
            <div>
                <label for="property_id" class="block text-sm font-medium text-gray-700 mb-2">العقار</label>
                <select name="property_id" id="property_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    <option value="">الكل</option>
                    @foreach($properties as $property)
                        <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                            {{ $property->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    <option value="">الكل</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">من تاريخ</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            </div>

            <!-- Date To -->
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">إلى تاريخ</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            </div>

            <div class="md:col-span-4 flex justify-end">
                <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-lg hover:bg-gray-900 transition-colors">
                    <i class="fas fa-filter ml-2"></i> تصفية النتائج
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العقار</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">القيمة المقدرة</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نموذج التقييم</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ التقييم</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">بواسطة</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($valuations as $valuation)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $valuation->property->title ?? 'غير معروف' }}</div>
                            <div class="text-sm text-gray-500">{{ $valuation->property->location ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900 font-bold">{{ number_format($valuation->estimated_value, 2) }} SAR</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $valuation->valuation_model }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $valuation->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                   ($valuation->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($valuation->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $valuation->created_at->format('Y-m-d') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $valuation->user->name ?? 'غير معروف' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">لا توجد تقييمات مطابقة</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $valuations->links() }}
        </div>
    </div>
</div>
@endsection
