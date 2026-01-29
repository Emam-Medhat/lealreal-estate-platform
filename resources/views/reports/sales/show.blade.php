@extends('layouts.app')

@section('title', $report->title . ' - Sales Report')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-8">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold">{{ $report->title }}</h1>
                    <p class="text-blue-100 mt-2">{{ $report->description }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('reports.sales.index') }}" class="px-4 py-2 bg-white text-blue-600 rounded-lg font-semibold hover:bg-blue-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Reports
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Alert -->
    @if(session('success'))
        <div class="container mx-auto px-4 mt-4">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <!-- Report Content -->
    <div class="container mx-auto px-4 py-8">
        @if(!$salesReport)
            <!-- Generating Status -->
            <div class="max-w-2xl mx-auto">
                <div class="bg-white rounded-xl shadow-lg p-8 text-center">
                    <div class="mb-4">
                        <i class="fas fa-spinner fa-spin text-6xl text-blue-600"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Generating Report</h2>
                    <p class="text-gray-600 mb-6">Your sales report is being generated. This may take a few moments.</p>
                    <div class="bg-gray-100 rounded-lg p-4">
                        <p class="text-sm text-gray-600">
                            <strong>Report ID:</strong> {{ $report->id }}<br>
                            <strong>Status:</strong> {{ ucfirst($report->status) }}<br>
                            <strong>Format:</strong> {{ strtoupper($report->format) }}
                        </p>
                    </div>
                    <div class="mt-6">
                        <button onclick="location.reload()" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                            <i class="fas fa-sync mr-2"></i>
                            Refresh Status
                        </button>
                    </div>
                </div>
            </div>
        @else
            <!-- Report Data -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <!-- Stats Overview -->
                    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Sales Overview</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-green-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-dollar-sign text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Total Sales</p>
                                        <p class="text-xl font-bold text-gray-900">${{ number_format($salesReport->total_sales, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-blue-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-home text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Properties Sold</p>
                                        <p class="text-xl font-bold text-gray-900">{{ $salesReport->properties_sold }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-chart-line text-purple-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Average Price</p>
                                        <p class="text-xl font-bold text-gray-900">${{ number_format($salesReport->average_sale_price, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-orange-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-percentage text-orange-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600">Commission</p>
                                        <p class="text-xl font-bold text-gray-900">${{ number_format($salesReport->total_commission, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sales by Property Type -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-4">Sales by Property Type</h2>
                        @if(isset($salesReport->sales_by_property_type) && count($salesReport->sales_by_property_type) > 0)
                            <div class="space-y-3">
                                @foreach($salesReport->sales_by_property_type as $type)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $type['type'] }}</p>
                                            <p class="text-sm text-gray-600">{{ $type['count'] }} properties</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold text-gray-900">${{ number_format($type['total_value'], 2) }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500">No property type data available</p>
                        @endif
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Report Info -->
                    <div class="bg-white rounded-xl shadow-lg p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Report Information</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-600">Period</p>
                                <p class="font-medium">{{ $salesReport->period_start->format('M d, Y') }} - {{ $salesReport->period_end->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Status</p>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ ucfirst($report->status) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Generated</p>
                                <p class="font-medium">{{ $report->generated_at ? $report->generated_at->format('M d, Y H:i') : 'In progress' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white rounded-xl shadow-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                        <div class="space-y-3">
                            <a href="{{ route('reports.sales.export', $report->id) }}?format=pdf" class="block w-full text-center px-4 py-2 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-colors">
                                <i class="fas fa-file-pdf mr-2"></i>
                                Export PDF
                            </a>
                            <a href="{{ route('reports.sales.export', $report->id) }}?format=excel" class="block w-full text-center px-4 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-colors">
                                <i class="fas fa-file-excel mr-2"></i>
                                Export Excel
                            </a>
                            <a href="{{ route('reports.sales.export', $report->id) }}?format=csv" class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                                <i class="fas fa-file-csv mr-2"></i>
                                Export CSV
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
