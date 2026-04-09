@extends('layouts.app')

@section('title', 'Agent Commissions')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Commission Tracking</h1>
                    <p class="text-gray-600">Monitor your earnings and commission history</p>
                </div>
                <div class="flex items-center space-x-3">
                    <select onchange="changePeriod(this.value)" class="px-3 py-2 border rounded-lg text-sm">
                        <option value="month">This Month</option>
                        <option value="quarter">This Quarter</option>
                        <option value="year" selected>This Year</option>
                        <option value="all">All Time</option>
                    </select>
                    <button onclick="exportReport()" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-download mr-2"></i>
                        Export
                    </button>
                    <a href="{{ route('agent.dashboard') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Commission Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-dollar-sign text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Earned</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($stats['total_earned'], 2) }}</p>
                        <p class="text-xs text-green-600 mt-1">
                            <i class="fas fa-arrow-up mr-1"></i>{{ $stats['growth'] }}% from last period
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-clock text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Pending</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($stats['pending'], 2) }}</p>
                        <p class="text-xs text-gray-500 mt-1">Awaiting payment</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-percentage text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Avg. Rate</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['avg_rate'] }}%</p>
                        <p class="text-xs text-gray-500 mt-1">Commission rate</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-home text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Deals Closed</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['deals_closed'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">This period</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commission Chart -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Commission Trend</h2>
            <div class="h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                <div class="text-center">
                    <i class="fas fa-chart-line text-4xl text-gray-400 mb-2"></i>
                    <p class="text-gray-600">Commission earnings over time</p>
                </div>
            </div>
            <div class="flex justify-between mt-4 text-sm text-gray-600">
                @foreach ($monthlyEarnings as $month => $earning)
                    <span>{{ $month }}: ${{ number_format($earning, 0) }}</span>
                @endforeach
            </div>
        </div>

        <!-- Recent Commissions -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Recent Commissions</h2>
                    <div class="flex items-center space-x-3">
                        <input type="text" placeholder="Search commissions..." class="px-3 py-2 border rounded-lg text-sm">
                        <select class="px-3 py-2 border rounded-lg text-sm">
                            <option>All Status</option>
                            <option>Paid</option>
                            <option>Pending</option>
                            <option>Processing</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Property</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sale Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commission</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($commissions as $commission)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $commission->created_at->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $commission->property->title }}</div>
                                        <div class="text-sm text-gray-500">{{ $commission->property->address }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-gray-200 rounded-full w-8 h-8 mr-2 flex items-center justify-center">
                                            @if($commission->client->avatar)
                                                <img src="{{ $commission->client->avatar }}" alt="" class="w-8 h-8 rounded-full object-cover">
                                            @else
                                                <i class="fas fa-user text-gray-400 text-xs"></i>
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-900">{{ $commission->client->name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${{ number_format($commission->sale_price, 0) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">${{ number_format($commission->amount, 2) }}</div>
                                    @if($commission->bonus > 0)
                                        <div class="text-xs text-green-600">+${{ number_format($commission->bonus, 2) }} bonus</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $commission->rate }}%
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($commission->status === 'paid')
                                            bg-green-100 text-green-800
                                        @elseif($commission->status === 'pending')
                                            bg-yellow-100 text-yellow-800
                                        @elseif($commission->status === 'processing')
                                            bg-blue-100 text-blue-800
                                        @else
                                            bg-gray-100 text-gray-800
                                        @endif
                                    ">
                                        {{ ucfirst($commission->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex space-x-2">
                                        <button onclick="viewCommission({{ $commission->id }})" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        @if($commission->status === 'pending')
                                            <button onclick="markAsPaid({{ $commission->id }})" class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        <button onclick="downloadInvoice({{ $commission->id }})" class="text-gray-600 hover:text-gray-900">
                                            <i class="fas fa-download"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <i class="fas fa-dollar-sign text-6xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Commissions Yet</h3>
                                    <p class="text-gray-500">Your commission earnings will appear here once you close deals.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Commission Structure -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Commission Structure</h2>
                <div class="space-y-3">
                    @foreach ($commissionStructure as $tier)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-gray-800">{{ $tier['name'] }}</h4>
                                <p class="text-sm text-gray-600">{{ $tier['description'] }}</p>
                            </div>
                            <div class="text-right">
                                <div class="text-lg font-bold text-gray-800">{{ $tier['rate'] }}%</div>
                                @if($tier['bonus'])
                                    <div class="text-xs text-green-600">+${{ $tier['bonus'] }} bonus</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Payment Schedule</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 border rounded-lg">
                        <div>
                            <h4 class="font-medium text-gray-800">Processing Time</h4>
                            <p class="text-sm text-gray-600">Time from deal close to payment</p>
                        </div>
                        <span class="text-lg font-bold text-gray-800">7-14 days</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 border rounded-lg">
                        <div>
                            <h4 class="font-medium text-gray-800">Payment Method</h4>
                            <p class="text-sm text-gray-600">How commissions are paid</p>
                        </div>
                        <span class="text-lg font-bold text-gray-800">Direct Deposit</span>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 border rounded-lg">
                        <div>
                            <h4 class="font-medium text-gray-800">Tax Withholding</h4>
                            <p class="text-sm text-gray-600">Tax rate applied</p>
                        </div>
                        <span class="text-lg font-bold text-gray-800">{{ $taxRate }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Performers -->
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Top Earning Properties</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach ($topProperties as $property)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-medium text-gray-800">{{ $property['title'] }}</h4>
                                <p class="text-sm text-gray-600">{{ $property['address'] }}</p>
                            </div>
                            <span class="text-lg font-bold text-green-600">${{ number_format($property['commission'], 0) }}</span>
                        </div>
                        <div class="text-sm text-gray-600">
                            Sale: ${{ number_format($property['sale_price'], 0) }} | Rate: {{ $property['rate'] }}%
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
function changePeriod(period) {
    window.location.href = '?period=' + period;
}

function exportReport() {
    window.location.href = '/agent/commissions/export';
}

function viewCommission(commissionId) {
    window.location.href = '/agent/commissions/' + commissionId;
}

function markAsPaid(commissionId) {
    if (confirm('Mark this commission as paid?')) {
        fetch('/agent/commissions/' + commissionId + '/paid', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
}

function downloadInvoice(commissionId) {
    window.location.href = '/agent/commissions/' + commissionId + '/invoice';
}
</script>
@endsection
