@extends('admin.layouts.admin')

@section('title', 'قائمة التوقعات - الذكاء الاصطناعي')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">سجل التوقعات</h1>
            <p class="text-gray-600 mt-2">عرض وتصفية جميع توقعات الأسعار السابقة.</p>
        </div>
        <div class="flex space-x-3 space-x-reverse">
            <a href="{{ route('ai.price-predictor.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                <i class="fas fa-plus ml-2"></i>
                توقع جديد
            </a>
            <a href="{{ route('ai.price-predictor.export') }}" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center">
                <i class="fas fa-download ml-2"></i>
                تصدير البيانات
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <form action="{{ route('ai.price-predictor.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
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

            <!-- Accuracy Filter -->
            <div>
                <label for="accuracy_min" class="block text-sm font-medium text-gray-700 mb-2">الحد الأدنى للدقة</label>
                <input type="number" step="0.01" min="0" max="1" name="accuracy_min" id="accuracy_min" value="{{ request('accuracy_min') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200" placeholder="0.80">
            </div>

            <!-- Date Filter -->
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">من تاريخ</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
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
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">السعر المتوقع</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الدقة</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النموذج</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ التوقع</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($predictions as $prediction)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $prediction->property->title ?? 'غير معروف' }}</div>
                            <div class="text-sm text-gray-500">{{ $prediction->property->location ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ number_format($prediction->predicted_price, 2) }} SAR</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $prediction->accuracy > 0.8 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ number_format($prediction->accuracy * 100, 1) }}%
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $prediction->prediction_model }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $prediction->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($prediction->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $prediction->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('ai.price-predictor.show', $prediction) }}" class="text-blue-600 hover:text-blue-900 ml-3">عرض</a>
                            <form action="{{ route('ai.price-predictor.destroy', $prediction) }}" method="POST" class="inline-block" onsubmit="return confirm('هل أنت متأكد من حذف هذا التوقع؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">حذف</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">لا توجد توقعات حالياً.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $predictions->links() }}
        </div>
    </div>
</div>
@endsection
