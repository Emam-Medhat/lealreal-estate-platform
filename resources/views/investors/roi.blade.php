@extends('layouts.app')

@section('title', 'ROI Analysis')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">ROI Analysis</h1>
                    <p class="text-gray-600">{{ $investor->user->name ?? 'Investor' }}'s return on investment analysis</p>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Export Report -->
                    <button onclick="exportROIReport()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-download mr-2"></i>
                        Export Report
                    </button>
                    
                    <!-- Generate Report -->
                    <button onclick="generateReport()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-chart-line mr-2"></i>
                        Generate Report
                    </button>
                </div>
            </div>
        </div>

        <!-- ROI Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-percentage text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total ROI</p>
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($totalROI, 2) }}%</p>
                        <p class="text-xs text-green-600 mt-1">
                            <i class="fas fa-arrow-up mr-1"></i>
                            +5.2% vs last quarter
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-dollar-sign text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Returns</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($totalReturns, 2) }}</p>
                        <p class="text-xs text-green-600 mt-1">
                            <i class="fas fa-arrow-up mr-1"></i>
                            +12.8% growth
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-chart-line text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Average Annual ROI</p>
                        <p class="text-2xl font-bold text-gray-800">{{ number_format($averageAnnualROI, 2) }}%</p>
                        <p class="text-xs text-green-600 mt-1">
                            <i class="fas fa-arrow-up mr-1"></i>
                            +3.1% vs benchmark
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-calendar-alt text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Payback Period</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $paybackPeriod }}y</p>
                        <p class="text-xs text-gray-500 mt-1">Average across portfolio</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ROI Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold text-gray-800">ROI Performance Over Time</h2>
                <select id="chartPeriod" class="px-3 py-2 border rounded-lg text-sm">
                    <option value="3">Last 3 Months</option>
                    <option value="6">Last 6 Months</option>
                    <option value="12" selected>Last Year</option>
                    <option value="24">Last 2 Years</option>
                </select>
            </div>
            <div class="h-80 bg-gray-50 rounded-lg flex items-center justify-center">
                <canvas id="roiChart"></canvas>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Property Performance -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">Property Performance</h2>
                
                <div class="space-y-4">
                    @forelse($propertyPerformance as $property)
                        <div class="border rounded-lg p-4">
                            <div class="flex justify-between items-start mb-3">
                                <div>
                                    <h3 class="text-sm font-semibold text-gray-800">{{ $property['title'] }}</h3>
                                    <p class="text-xs text-gray-500">{{ $property['type'] }}</p>
                                </div>
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($property['roi'] >= 15)
                                        bg-green-100 text-green-800
                                    @elseif($property['roi'] >= 8)
                                        bg-yellow-100 text-yellow-800
                                    @else
                                        bg-red-100 text-red-800
                                    @endif
                                ">
                                    {{ number_format($property['roi'], 1) }}% ROI
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-600">Invested:</p>
                                    <p class="font-medium">${{ number_format($property['invested'], 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Current Value:</p>
                                    <p class="font-medium">${{ number_format($property['current_value'], 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Returns:</p>
                                    <p class="font-medium text-green-600">${{ number_format($property['returns'], 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600">Holding Period:</p>
                                    <p class="font-medium">{{ $property['holding_period'] }} months</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-building text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">No property performance data available</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- ROI by Property Type -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-6">ROI by Property Type</h2>
                
                <div class="space-y-4">
                    @foreach($roiByType as $type => $data)
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">{{ ucfirst($type) }}</span>
                                <span class="text-sm font-medium text-gray-900">{{ number_format($data['roi'], 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min($data['roi'] * 5, 100) }}%;"></div>
                            </div>
                            <div class="flex justify-between items-center mt-1">
                                <span class="text-xs text-gray-500">{{ $data['count'] }} properties</span>
                                <span class="text-xs text-gray-500">${{ number_format($data['total_invested'], 2) }} invested</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Monthly Breakdown -->
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Monthly ROI Breakdown</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Month</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Investments</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Returns</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Profit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ROI %</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($monthlyBreakdown as $month)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $month['month'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">${{ number_format($month['investments'], 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-green-600">${{ number_format($month['returns'], 2) }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium {{ $month['net_profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        ${{ number_format($month['net_profit'], 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium {{ $month['roi'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($month['roi'], 2) }}%
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center">
                                    <i class="fas fa-chart-line text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-gray-500">No monthly breakdown data available</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Key Insights -->
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Key Insights & Recommendations</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-md font-semibold text-gray-800 mb-4">Performance Insights</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span class="text-sm text-gray-700">Your portfolio is outperforming the market average by 3.2%</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-check-circle text-green-500 mt-1 mr-3"></i>
                            <span class="text-sm text-gray-700">Residential properties showing strongest ROI at 12.5%</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-exclamation-triangle text-yellow-500 mt-1 mr-3"></i>
                            <span class="text-sm text-gray-700">Commercial properties underperforming with 5.2% ROI</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                            <span class="text-sm text-gray-700">Q4 historically shows 15% higher returns</span>
                        </li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-md font-semibold text-gray-800 mb-4">Recommendations</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start">
                            <i class="fas fa-lightbulb text-yellow-500 mt-1 mr-3"></i>
                            <span class="text-sm text-gray-700">Consider reallocating 20% from commercial to residential</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-lightbulb text-yellow-500 mt-1 mr-3"></i>
                            <span class="text-sm text-gray-700">Increase investment in emerging markets for higher growth</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-lightbulb text-yellow-500 mt-1 mr-3"></i>
                            <span class="text-sm text-gray-700">Rebalance portfolio quarterly for optimal returns</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-lightbulb text-yellow-500 mt-1 mr-3"></i>
                            <span class="text-sm text-gray-700">Consider tax optimization strategies</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Chart period change
document.getElementById('chartPeriod').addEventListener('change', function(e) {
    const period = e.target.value;
    updateROIChart(period);
});

function updateROIChart(period) {
    const ctx = document.getElementById('roiChart');
    if (ctx) {
        // Simple chart update - integrate with Chart.js
        ctx.innerHTML = '<div class="text-gray-500">ROI performance chart for last ' + period + ' months</div>';
    }
}

// Export ROI report
function exportROIReport() {
    const format = prompt('Choose export format:', 'pdf');
    if (format && ['pdf', 'excel', 'csv'].includes(format)) {
        const period = document.getElementById('chartPeriod').value;
        
        const params = new URLSearchParams({
            format: format,
            period: period
        });
        
        window.location.href = '/investor/roi/{{ $investor->id }}/export?' + params.toString();
    }
}

// Generate detailed report
function generateReport() {
    const period = document.getElementById('chartPeriod').value;
    
    if (confirm('Generate detailed ROI report for the selected period?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/investor/roi/{{ $investor->id }}/generate';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        const periodInput = document.createElement('input');
        periodInput.type = 'hidden';
        periodInput.name = 'period';
        periodInput.value = period;
        form.appendChild(periodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Initialize chart
document.addEventListener('DOMContentLoaded', function() {
    updateROIChart(12);
});
</script>
@endsection
