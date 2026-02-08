@extends('layouts.app')

@section('title', 'جدولة التقارير')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">جدولة التقارير</h1>
            <p class="text-gray-600 mt-2">إدارة الجداول الزمنية لإرسال التقارير تلقائياً</p>
        </div>
        <div class="flex space-x-4 space-x-reverse">
            <a href="{{ route('reports.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة للتقارير
            </a>
            <a href="{{ route('reports.schedules.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus ml-2"></i>
                جدولة جديدة
            </a>
        </div>
    </div>

    <!-- Schedules List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        اسم التقرير
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        التكرار
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        وقت الإرسال
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        المستلمون
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        الحالة
                    </th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        الإجراءات
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <!-- Empty State -->
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <i class="far fa-calendar-alt text-4xl mb-4 text-gray-300"></i>
                            <p class="text-lg">لا توجد تقارير مجدولة حالياً</p>
                            <p class="text-sm mt-1">قم بإنشاء جدولة جديدة لتبدأ باستلام التقارير تلقائياً</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
