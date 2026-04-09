@extends('admin.layouts.admin')

@section('title', 'تقارير أوامر العمل')

@section('page-title', 'تقارير أوامر العمل')

@section('content')
<!-- Page Header -->
<div class="mb-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">تقارير أوامر العمل</h1>
            <p class="text-gray-600 mt-1">تحليل مفصل لأوامر العمل وحالتها</p>
        </div>
        <div class="flex space-x-reverse space-x-3">
            <a href="{{ route('maintenance.reports.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-arrow-right ml-2"></i>
                العودة للتقارير
            </a>
            <button onclick="window.print()" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <i class="fas fa-print ml-2"></i>
                طباعة
            </button>
        </div>
    </div>
</div>

<!-- Statistics Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-blue-600 rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium">إجمالي الأوامر</p>
                <p class="text-3xl font-bold text-white mt-2">{{ $stats['total'] ?? 0 }}</p>
            </div>
            <div class="bg-blue-700 bg-opacity-50 rounded-full p-3">
                <i class="fas fa-tasks text-white text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-green-600 rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium">مكتملة</p>
                <p class="text-3xl font-bold text-white mt-2">{{ $stats['completed'] ?? 0 }}</p>
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
                <p class="text-3xl font-bold text-white mt-2">{{ $stats['in_progress'] ?? 0 }}</p>
            </div>
            <div class="bg-amber-700 bg-opacity-50 rounded-full p-3">
                <i class="fas fa-spinner text-white text-xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-red-600 rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-red-100 text-sm font-medium">في الانتظار</p>
                <p class="text-3xl font-bold text-white mt-2">{{ $stats['pending'] ?? 0 }}</p>
            </div>
            <div class="bg-red-700 bg-opacity-50 rounded-full p-3">
                <i class="fas fa-clock text-white text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Work Orders Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">قائمة أوامر العمل</h3>
    </div>
    <div class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم الأمر</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العنوان</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العقار</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الفريق المسند</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الأولوية</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if(isset($workOrders) && $workOrders->count() > 0)
                        @foreach($workOrders as $workOrder)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $workOrder->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $workOrder->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $workOrder->property->title ?? 'N/A' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $workOrder->assignedTeam->name ?? 'غير محدد' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($workOrder->status == 'completed') bg-green-100 text-green-800
                                        @elseif($workOrder->status == 'in_progress') bg-amber-100 text-amber-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ __('maintenance.workorders.status_' . $workOrder->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($workOrder->priority == 'high') bg-red-100 text-red-800
                                        @elseif($workOrder->priority == 'medium') bg-amber-100 text-amber-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ __('maintenance.workorders.priority_' . $workOrder->priority) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $workOrder->created_at->format('Y-m-d') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('maintenance.workorders.show', $workOrder) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <i class="fas fa-inbox text-gray-400 text-3xl mb-3"></i>
                                <p class="text-gray-500">لا توجد أوامر عمل حالياً</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Status Distribution Charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">توزيع الحالات</h3>
        </div>
        <div class="p-6">
            <canvas id="statusChart" width="400" height="200"></canvas>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">توزيع الأولويات</h3>
        </div>
        <div class="p-6">
            <canvas id="priorityChart" width="400" height="200"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Status Distribution Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['مكتمل', 'قيد التنفيذ', 'في الانتظار'],
            datasets: [{
                data: [{{ $stats['completed'] ?? 0 }}, {{ $stats['in_progress'] ?? 0 }}, {{ $stats['pending'] ?? 0 }}],
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: {
                            family: 'Tajawal'
                        }
                    }
                }
            }
        }
    });

    // Priority Distribution Chart
    const priorityCtx = document.getElementById('priorityChart').getContext('2d');
    const priorityChart = new Chart(priorityCtx, {
        type: 'bar',
        data: {
            labels: ['عالية', 'متوسطة', 'منخفضة'],
            datasets: [{
                label: 'عدد الأوامر',
                data: [{{ $stats['high_priority'] ?? 0 }}, {{ $stats['medium_priority'] ?? 0 }}, {{ $stats['low_priority'] ?? 0 }}],
                backgroundColor: ['#ef4444', '#f59e0b', '#3b82f6']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endpush
