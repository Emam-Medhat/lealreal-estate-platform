@extends('layouts.app')

@section('content')
<div class="container-fluid px-6 py-4">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Financial Dashboard</h1>
            <p class="text-gray-500 text-sm">Real-time financial overview</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('reports.financial.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-plus mr-2"></i> New Report
            </a>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Revenue -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Revenue</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">${{ number_format($totalRevenue, 2) }}</h3>
                </div>
                <div class="p-2 bg-green-50 rounded-lg">
                    <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Net Profit -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Net Profit</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">${{ number_format($netProfit, 2) }}</h3>
                </div>
                <div class="p-2 bg-blue-50 rounded-lg">
                    <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Expenses -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-red-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Expenses</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">${{ number_format($totalExpenses, 2) }}</h3>
                </div>
                <div class="p-2 bg-red-50 rounded-lg">
                    <i class="fas fa-receipt text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Revenue by Property -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Revenue by Property</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">Property</th>
                            <th class="px-6 py-3">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($revenueByProperty as $item)
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $item['property_name'] }}
                                </td>
                                <td class="px-6 py-4">
                                    ${{ number_format($item['revenue'], 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-center">No revenue data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Revenue by Company -->
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Revenue by Company</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">Company</th>
                            <th class="px-6 py-3">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($revenueByCompany as $item)
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ $item['company_name'] }}
                                </td>
                                <td class="px-6 py-4">
                                    ${{ number_format($item['revenue'], 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-center">No revenue data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Monthly Trend -->
        <div class="bg-white rounded-xl shadow-sm p-6 col-span-1 lg:col-span-2">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Monthly Revenue Trend</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-6 py-3">Month</th>
                            <th class="px-6 py-3">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($monthlyRevenue as $item)
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    {{ \Carbon\Carbon::create()->month($item->month)->format('F') }} {{ $item->year }}
                                </td>
                                <td class="px-6 py-4">
                                    ${{ number_format($item->total, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-center">No trend data available</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
