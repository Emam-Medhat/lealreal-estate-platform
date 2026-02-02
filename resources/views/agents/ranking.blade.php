@extends('admin.layouts.admin')

@section('title', 'لوحة المتصدرين')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-yellow-500 to-orange-600 rounded-xl shadow-xl p-8 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold mb-2">لوحة المتصدرين</h1>
                <p class="text-yellow-100 text-lg">عرض ترتيب الوكلاء ومقارنات الأداء</p>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold">{{ $agentRanking['current_rank'] ?? 1 }}</div>
                <div class="text-yellow-100">ترتيبك الحالي</div>
                <div class="text-sm text-yellow-200">من {{ $agentRanking['total_agents'] ?? 0 }} وكيل</div>
            </div>
        </div>
    </div>

    <!-- Current Agent Stats -->
    @if(isset($agent))
    <div class="bg-white rounded-xl shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-900">أدائك الحالي</h2>
            <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                المرتبة {{ $agentRanking['current_rank'] ?? 1 }}
            </span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg">
                <div class="text-2xl font-bold text-blue-600">{{ $agentRanking['current_rank'] ?? 1 }}</div>
                <div class="text-sm text-gray-600">الترتيب</div>
            </div>
            <div class="text-center p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-lg">
                <div class="text-2xl font-bold text-green-600">{{ $agentRanking['total_agents'] ?? 0 }}</div>
                <div class="text-sm text-gray-600">إجمالي الوكلاء</div>
            </div>
            <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg">
                <div class="text-2xl font-bold text-purple-600">{{ round((($agentRanking['total_agents'] - $agentRanking['current_rank'] + 1) / $agentRanking['total_agents']) * 100, 1) }}%</div>
                <div class="text-sm text-gray-600">نسبة الأداء</div>
            </div>
            <div class="text-center p-4 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-lg">
                <div class="text-2xl font-bold text-yellow-600">{{ $agent->name ?? 'Agent' }}</div>
                <div class="text-sm text-gray-600">اسم الوكيل</div>
            </div>
        </div>
    </div>
    @endif

    <!-- Rankings Table -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900">ترتيب الأداء</h2>
                <div class="flex items-center space-x-4">
                    <select onchange="changePeriod(this.value)" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>شهري</option>
                        <option value="quarterly" {{ $period == 'quarterly' ? 'selected' : '' }}>ربع سنوي</option>
                        <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>سنوي</option>
                    </select>
                    <button onclick="refreshRankings()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                        <i class="fas fa-sync-alt ml-1"></i>
                        تحديث
                    </button>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الترتيب</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الوكيل</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المبيعات</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العمولة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التقييم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نسبة التغيير</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="rankingsTableBody">
                    @forelse ($rankings ?? [] as $index => $agent)
                        <tr class="hover:bg-gray-50 transition-colors {{ isset($agent) && isset($agent['id']) && $agent->id == $agent['id'] ? 'bg-blue-50 border-l-4 border-blue-500' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-end">
                                    @if ($index == 0)
                                        <span class="text-3xl">🥇</span>
                                    @elseif ($index == 1)
                                        <span class="text-3xl">🥈</span>
                                    @elseif ($index == 2)
                                        <span class="text-3xl">🥉</span>
                                    @else
                                        <span class="text-lg font-bold {{ $index < 10 ? 'text-green-600' : 'text-gray-600' }}">{{ $agent['rank'] ?? ($index + 1) }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-end">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        <div class="h-12 w-12 rounded-full bg-gradient-to-br {{ $index == 0 ? 'from-yellow-400 to-yellow-600' : ($index == 1 ? 'from-gray-400 to-gray-600' : ($index == 2 ? 'from-orange-400 to-orange-600' : 'from-blue-400 to-blue-600')) }} flex items-center justify-center">
                                            <span class="text-white font-bold">{{ substr($agent['name'] ?? 'Agent', 0, 1) }}</span>
                                        </div>
                                    </div>
                                    <div class="mr-4 text-right">
                                        <div class="text-sm font-medium text-gray-900">{{ $agent['name'] ?? 'Unknown Agent' }}</div>
                                        <div class="text-sm text-gray-500">{{ $agent['company'] ?? 'شركة العقارات' }}</div>
                                        @if(isset($agent) && isset($agent['id']) && $agent->id == $agent['id'])
                                            <span class="text-xs text-blue-600 font-medium">أنت</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-right">
                                    <div class="text-lg font-bold text-gray-900">{{ number_format($agent['sales'] ?? 0) }}</div>
                                    <div class="text-xs text-gray-500">وحدة</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-right">
                                    <div class="text-lg font-bold text-green-600">${{ number_format($agent['commission'] ?? 0, 0) }}</div>
                                    <div class="text-xs text-gray-500">ريال</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center justify-end">
                                    <div class="flex text-yellow-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($agent['rating'] ?? 0))
                                                <i class="fas fa-star"></i>
                                            @elseif($i - 0.5 <= ($agent['rating'] ?? 0))
                                                <i class="fas fa-star-half-alt"></i>
                                            @else
                                                <i class="far fa-star"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-sm text-gray-900 mr-1">{{ number_format($agent['rating'] ?? 0, 1) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $agent['status'] == 'Active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $agent['status'] == 'Active' ? 'نشط' : 'غير نشط' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-right">
                                    @if($index == 0)
                                        <span class="text-green-600 font-medium">
                                            <i class="fas fa-arrow-up ml-1"></i>
                                            +{{ rand(5, 15) }}%
                                        </span>
                                    @elseif($index == 1)
                                        <span class="text-blue-600 font-medium">
                                            <i class="fas fa-minus ml-1"></i>
                                            {{ rand(-2, 2) }}%
                                        </span>
                                    @else
                                        <span class="text-gray-600 font-medium">
                                            <i class="fas fa-arrow-down ml-1"></i>
                                            -{{ rand(1, 8) }}%
                                        </span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-trophy text-6xl mb-4 text-gray-300"></i>
                                    <h3 class="text-lg font-semibold text-gray-600 mb-2">لا توجد بيانات ترتيب</h3>
                                    <p class="text-gray-500">سيتم عرض بيانات الترتيب هنا عند توفرها</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Performance Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">أفضل 3 وكلاء</h3>
                <i class="fas fa-trophy text-yellow-500 text-xl"></i>
            </div>
            <div class="space-y-3">
                @for($i = 0; $i < min(3, count($rankings ?? [])); $i++)
                    @php $agent = $rankings[$i]; @endphp
                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-yellow-50 to-orange-50 rounded-lg">
                        <div class="flex items-center">
                            <span class="text-lg font-bold text-yellow-600 mr-3">{{ $i + 1 }}</span>
                            <div>
                                <div class="font-medium text-gray-900">{{ $agent['name'] }}</div>
                                <div class="text-sm text-gray-600">{{ number_format($agent['sales']) }} مبيعات</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="font-bold text-green-600">${{ number_format($agent['commission'], 0) }}</div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">إحصائيات الفترة</h3>
                <i class="fas fa-chart-bar text-blue-500 text-xl"></i>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">إجمالي المبيعات</span>
                    <span class="font-bold text-gray-900">{{ array_sum(array_column($rankings ?? [], 'sales')) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">متوسط العمولة</span>
                    <span class="font-bold text-green-600">${{ number_format(array_sum(array_column($rankings ?? [], 'commission')) / max(1, count($rankings ?? [])), 0) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">متوسط التقييم</span>
                    <span class="font-bold text-yellow-600">{{ number_format(array_sum(array_column($rankings ?? [], 'rating')) / max(1, count($rankings ?? [])), 1) }}</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">الأداء العام</h3>
                <i class="fas fa-chart-line text-purple-500 text-xl"></i>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">الوكلاء النشطون</span>
                    <span class="font-bold text-green-600">{{ count(array_filter($rankings ?? [], fn($r) => $r['status'] == 'Active')) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">معدل النمو</span>
                    <span class="font-bold text-blue-600">+{{ rand(10, 25) }}%</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-600">أفضل أداء</span>
                    <span class="font-bold text-purple-600">{{ $rankings[0]['name'] ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Agent rankings page loaded');
    
    // Initialize real-time updates
    startRealTimeUpdates();
});

function changePeriod(period) {
    // Show loading state
    showNotification('جاري تحديث البيانات...', 'info');
    
    // Redirect to new period
    window.location.href = `/agents/ranking?period=${period}`;
}

function refreshRankings() {
    const button = event.target;
    const originalText = button.innerHTML;
    
    // Show loading state
    button.innerHTML = '<i class="fas fa-spinner fa-spin ml-1"></i> جاري التحديث...';
    button.disabled = true;
    
    // Fetch fresh data
    fetch(`/agents/ranking?period={{ $period }}&refresh=${Date.now()}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html',
        }
    })
    .then(response => response.text())
    .then(html => {
        // Update the page content
        document.documentElement.innerHTML = html;
        showNotification('تم تحديث الترتيبات بنجاح', 'success');
    })
    .catch(error => {
        console.error('Error refreshing rankings:', error);
        showNotification('فشل تحديث الترتيبات', 'error');
    })
    .finally(() => {
        // Reset button state
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function startRealTimeUpdates() {
    // Simulate real-time updates every 30 seconds
    setInterval(() => {
        updateRandomMetrics();
    }, 30000);
}

function updateRandomMetrics() {
    // Update random commission values
    const commissionCells = document.querySelectorAll('td:nth-child(4) .text-lg');
    commissionCells.forEach(cell => {
        if (cell.textContent.includes('$')) {
            const currentValue = parseFloat(cell.textContent.replace(/[$,]/g, ''));
            const change = (Math.random() - 0.5) * 1000;
            cell.textContent = '$' + (currentValue + change).toLocaleString('en-US', {maximumFractionDigits: 0});
        }
    });
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 left-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 
                           type === 'error' ? 'fa-exclamation-circle' : 
                           type === 'warning' ? 'fa-exclamation-triangle' : 
                           'fa-info-circle'} ml-2"></i>
            ${message}
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(400px)';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}
</script>
@endpush
