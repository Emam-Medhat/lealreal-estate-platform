@extends('admin.layouts.admin')

@section('title', 'لوحة العملاء المحتملين')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">لوحة العملاء المحتملين</h1>
            <p class="text-gray-600 mt-1">إدارة وتتبع العملاء المحتملين</p>
        </div>
        <a href="{{ route('leads.create') }}" 
           class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-6 py-3 rounded-lg font-medium flex items-center space-x-2 space-x-reverse transition-all duration-200 shadow-lg hover:shadow-xl">
            <i class="fas fa-plus"></i>
            <span>عميل جديد</span>
        </a>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">إجمالي العملاء</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_leads'] }}</p>
            </div>
            <div class="bg-orange-100 rounded-full p-3">
                <i class="fas fa-users text-orange-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">عملاء جدد</p>
                <p class="text-2xl font-bold text-blue-600">{{ $stats['new_leads'] }}</p>
            </div>
            <div class="bg-blue-100 rounded-full p-3">
                <i class="fas fa-user-plus text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">عملاء مؤهلين</p>
                <p class="text-2xl font-bold text-green-600">{{ $stats['qualified_leads'] }}</p>
            </div>
            <div class="bg-green-100 rounded-full p-3">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">عملاء محولين</p>
                <p class="text-2xl font-bold text-purple-600">{{ $stats['converted_leads'] }}</p>
            </div>
            <div class="bg-purple-100 rounded-full p-3">
                <i class="fas fa-chart-line text-purple-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Conversion Rate -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">معدل التحويل</h3>
            <p class="text-sm text-gray-600 mt-1">نسبة العملاء الذين تم تحويلهم إلى عملاء</p>
        </div>
        <div class="text-right">
            <p class="text-3xl font-bold text-orange-600">{{ number_format($stats['conversion_rate'], 2) }}%</p>
            <p class="text-sm text-gray-500">معدل التحويل</p>
        </div>
    </div>
    <div class="mt-4">
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 h-2 rounded-full transition-all duration-500" 
                 style="width: {{ min($stats['conversion_rate'], 100) }}%"></div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Lead Sources Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">مصادر العملاء</h3>
        <div class="space-y-3">
            @forelse($leadSources as $source)
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 bg-orange-100 rounded-full mr-3"></div>
                        <span class="text-sm font-medium text-gray-700">{{ $source->name }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-semibold text-gray-900">{{ $source->leads_count }}</span>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-sm">لا توجد بيانات</p>
            @endforelse
        </div>
    </div>

    <!-- Lead Statuses Chart -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">حالات العملاء</h3>
        <div class="space-y-3">
            @forelse($leadStatuses as $status)
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-3 h-3 rounded-full mr-3" style="background-color: {{ $status->color }}"></div>
                        <span class="text-sm font-medium text-gray-700">{{ $status->name }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-sm font-semibold text-gray-900">{{ $status->leads_count }}</span>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-sm">لا توجد بيانات</p>
            @endforelse
        </div>
    </div>
</div>

<!-- Recent Leads -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">آخر العملاء</h3>
    </div>
    <div class="overflow-x-auto">
        @forelse($recentLeads as $lead)
            <div class="px-6 py-4 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4 space-x-reverse">
                        <div class="bg-gray-200 rounded-full p-2">
                            <i class="fas fa-user text-gray-600 text-sm"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">{{ $lead->first_name }} {{ $lead->last_name }}</h4>
                            <p class="text-xs text-gray-500">{{ $lead->email }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2 space-x-reverse">
                        @if($lead->status)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" 
                                  style="background-color: {{ $lead->status->color }}20; color: {{ $lead->status->color }}">
                                {{ $lead->status->name }}
                            </span>
                        @endif
                        <span class="text-xs text-gray-500">{{ $lead->created_at->format('Y-m-d') }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-8">
                <div class="bg-gray-100 rounded-full p-4 w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <i class="fas fa-users text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد عملاء</h3>
                <p class="text-gray-600 mb-6">ابدأ بإضافة عملاء جدد</p>
                <a href="{{ route('leads.create') }}" 
                   class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200 inline-flex items-center space-x-2 space-x-reverse">
                    <i class="fas fa-plus"></i>
                    <span>إضافة عميل</span>
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
