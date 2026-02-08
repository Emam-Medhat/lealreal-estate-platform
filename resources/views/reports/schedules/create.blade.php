@extends('layouts.app')

@section('title', 'جدولة تقرير جديد')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">جدولة تقرير جديد</h1>
            <p class="text-gray-600 mt-2">إعداد إرسال تلقائي للتقارير الدورية</p>
        </div>
        <div>
            <a href="{{ route('reports.schedules.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                <i class="fas fa-arrow-right ml-2"></i>
                إلغاء
            </a>
        </div>
    </div>

    <!-- Create Form -->
    <div class="bg-white rounded-lg shadow-lg p-6 max-w-4xl mx-auto">
        <form action="{{ route('reports.schedules.store') }}" method="POST">
            @csrf
            <!-- Report Selection -->
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="report_type">
                    نوع التقرير
                </label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" id="report_type" name="report_type">
                    <option value="">اختر التقرير...</option>
                    <option value="financial">التقرير المالي</option>
                    <option value="sales">تقرير المبيعات</option>
                    <option value="performance">تقرير الأداء</option>
                    <option value="market">تحليل السوق</option>
                </select>
            </div>

            <!-- Schedule Settings -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="frequency">
                        التكرار
                    </label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" id="frequency" name="frequency">
                        <option value="daily">يومي</option>
                        <option value="weekly">أسبوعي</option>
                        <option value="monthly">شهري</option>
                        <option value="quarterly">ربع سنوي</option>
                    </select>
                </div>
                <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="time">
                        وقت الإرسال
                    </label>
                    <input type="time" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" id="time" name="time">
                </div>
            </div>

            <!-- Recipients -->
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="recipients">
                    المستلمون (بريد إلكتروني)
                </label>
                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" id="recipients" name="recipients" placeholder="email@example.com, another@example.com">
                <p class="text-gray-500 text-xs mt-1">افصل بين العناوين بفاصلة</p>
            </div>

            <!-- Format -->
            <div class="mb-8">
                <label class="block text-gray-700 text-sm font-bold mb-2">
                    صيغة التقرير
                </label>
                <div class="flex space-x-4 space-x-reverse">
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="form-checkbox text-blue-600" name="formats[]" value="pdf" checked>
                        <span class="mr-2">PDF</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" class="form-checkbox text-blue-600" name="formats[]" value="excel">
                        <span class="mr-2">Excel</span>
                    </label>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="fas fa-save ml-2"></i>
                    حفظ الجدولة
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
