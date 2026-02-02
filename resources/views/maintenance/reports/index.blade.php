@extends('admin.layouts.admin')

@section('title', 'تقارير الصيانة')

@section('page-title', 'تقارير الصيانة')

@section('content')
<!-- Page Header -->
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">تقارير الصيانة</h1>
            <p class="text-gray-600 mt-1">نظرة شاملة على أداء الصيانة والإحصائيات</p>
        </div>
        <div class="flex space-x-reverse space-x-3">
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-print ml-2"></i>
                طباعة التقرير
            </button>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-blue-600 rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium">إجمالي أوامر العمل</p>
                <p class="text-3xl font-bold text-white mt-2">{{ App\Models\WorkOrder::count() }}</p>
            </div>
            <div class="bg-blue-700 bg-opacity-50 rounded-full p-3">
                <i class="fas fa-tasks text-white text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-green-600 rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium">أوامر مكتملة</p>
                <p class="text-3xl font-bold text-white mt-2">{{ App\Models\WorkOrder::where('status', 'completed')->count() }}</p>
            </div>
            <div class="bg-green-700 bg-opacity-50 rounded-full p-3">
                <i class="fas fa-check-circle text-white text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-amber-600 rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-amber-100 text-sm font-medium">قيد التنفيذ</p>
                <p class="text-3xl font-bold text-white mt-2">{{ App\Models\WorkOrder::where('status', 'in_progress')->count() }}</p>
            </div>
            <div class="bg-amber-700 bg-opacity-50 rounded-full p-3">
                <i class="fas fa-spinner text-white text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-purple-600 rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-purple-100 text-sm font-medium">فرق الصيانة</p>
                <p class="text-3xl font-bold text-white mt-2">{{ App\Models\MaintenanceTeam::count() }}</p>
            </div>
            <div class="bg-purple-700 bg-opacity-50 rounded-full p-3">
                <i class="fas fa-users text-white text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Quick Reports and Recent Activity -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Quick Reports -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">تقارير سريعة</h3>
        </div>
        <div class="p-6">
            <div class="space-y-3">
                <a href="{{ route('maintenance.reports.workorders') }}" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex items-center">
                        <div class="bg-blue-100 rounded-lg p-2 ml-3">
                            <i class="fas fa-tasks text-blue-600"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">تقارير أوامر العمل</h4>
                            <p class="text-xs text-gray-500">تحليل مفصل لأوامر العمل</p>
                        </div>
                    </div>
                    <i class="fas fa-arrow-left text-gray-400"></i>
                </a>
                
                <a href="{{ route('maintenance.reports.teams') }}" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex items-center">
                        <div class="bg-green-100 rounded-lg p-2 ml-3">
                            <i class="fas fa-users text-green-600"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">تقارير الفرق</h4>
                            <p class="text-xs text-gray-500">أداء الفرق والإحصائيات</p>
                        </div>
                    </div>
                    <i class="fas fa-arrow-left text-gray-400"></i>
                </a>
                
                <a href="{{ route('maintenance.reports.performance') }}" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex items-center">
                        <div class="bg-amber-100 rounded-lg p-2 ml-3">
                            <i class="fas fa-chart-line text-amber-600"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">تقارير الأداء</h4>
                            <p class="text-xs text-gray-500">تحليل الأداء الشهري</p>
                        </div>
                    </div>
                    <i class="fas fa-arrow-left text-gray-400"></i>
                </a>
                
                <a href="{{ route('maintenance.reports.costs') }}" class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex items-center">
                        <div class="bg-purple-100 rounded-lg p-2 ml-3">
                            <i class="fas fa-dollar-sign text-purple-600"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">تقارير التكاليف</h4>
                            <p class="text-xs text-gray-500">تحليل التكاليف والميزانيات</p>
                        </div>
                    </div>
                    <i class="fas fa-arrow-left text-gray-400"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">آخر النشاطات</h3>
        </div>
        <div class="p-6">
            @php
                $recentWorkOrders = App\Models\WorkOrder::with(['property', 'assignedTeam'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
            @endphp
            @if($recentWorkOrders->count() > 0)
                <div class="space-y-4">
                    @foreach($recentWorkOrders as $workOrder)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900">{{ $workOrder->title }}</h4>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $workOrder->property->title ?? 'N/A' }} - 
                                    {{ $workOrder->assignedTeam->name ?? 'غير محدد' }}
                                </p>
                            </div>
                            <div class="mr-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($workOrder->status == 'completed') bg-green-100 text-green-800
                                    @elseif($workOrder->status == 'in_progress') bg-amber-100 text-amber-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ __('maintenance.workorders.status_' . $workOrder->status) }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-gray-400 text-3xl mb-3"></i>
                    <p class="text-gray-500">لا توجد أوامر عمل حالياً</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-refresh every 30 seconds
    setInterval(function() {
        location.reload();
    }, 30000);
</script>
@endpush
