@extends('layouts.dashboard')

@section('title', 'لوحة التحكم')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Stats Cards -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 ml-4">
                    <i class="fas fa-building fa-2x"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">عقاراتي</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $stats['properties_count'] ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600 ml-4">
                    <i class="fas fa-heart fa-2x"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">المفضلة</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $stats['favorites_count'] ?? 0 }}</h3>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 ml-4">
                    <i class="fas fa-wallet fa-2x"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">المحفظة</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ number_format($stats['wallet_balance'] ?? 0, 2) }} ر.س
                    </h3>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600 ml-4">
                    <i class="fas fa-eye fa-2x"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">الزيارات</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $stats['login_count'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Activity -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">آخر النشاطات</h2>
            <div class="space-y-4">
                <p class="text-gray-500 text-center py-4">لا توجد نشاطات حديثة لعرضها</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">روابط سريعة</h2>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('properties.create') }}"
                    class="flex flex-col items-center p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-plus-circle text-blue-600 mb-2 font-2x"></i>
                    <span class="text-sm font-medium">إضافة عقار</span>
                </a>
                <a href="{{ route('dashboard.profile') }}"
                    class="flex flex-col items-center p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-user-edit text-green-600 mb-2 font-2x"></i>
                    <span class="text-sm font-medium">تعديل الملف</span>
                </a>
                <a href="{{ route('analytics.dashboard') }}"
                    class="flex flex-col items-center p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-chart-pie text-purple-600 mb-2 font-2x"></i>
                    <span class="text-sm font-medium">التحليلات</span>
                </a>
                <a href="{{ route('dashboard.settings') }}"
                    class="flex flex-col items-center p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-sliders-h text-gray-600 mb-2 font-2x"></i>
                    <span class="text-sm font-medium">الإعدادات</span>
                </a>
            </div>
        </div>
    </div>
@endsection