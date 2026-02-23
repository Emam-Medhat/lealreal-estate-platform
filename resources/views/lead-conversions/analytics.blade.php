@extends('admin.layouts.admin')

@section('title', 'Lead Conversions Analytics')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-chart-line text-blue-600 mr-3"></i>
                    Lead Conversions Analytics
                </h1>
                <p class="text-gray-600">Track and analyze lead conversion performance</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('lead-conversions.index') }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-list mr-2"></i>
                    All Conversions
                </a>
                <a href="{{ route('lead-conversions.funnel') }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors">
                    <i class="fas fa-filter mr-2"></i>
                    Conversion Funnel
                </a>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_conversions'] }}</p>
                        <p class="text-gray-600 text-sm">Total Conversions</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exchange-alt text-green-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats['conversion_rate'], 2) }}%</p>
                        <p class="text-gray-600 text-sm">Conversion Rate</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-percentage text-blue-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['avg_conversion_time'] }} days</p>
                        <p class="text-gray-600 text-sm">Avg Conversion Time</p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-2xl font-bold text-gray-900">${{ number_format($stats['total_conversion_value'], 2) }}</p>
                        <p class="text-gray-600 text-sm">Total Conversion Value</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Monthly Conversions -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-calendar text-green-500 mr-2"></i>
                    Monthly Conversions
                </h3>
                <div class="space-y-4">
                    @forelse($monthlyConversions as $conversion)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-chart-line text-green-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $conversion->month }} {{ $conversion->year }}</p>
                                    <p class="text-sm text-gray-500">{{ $conversion->count }} conversions</p>
                                </div>
                            </div>
                            <div class="text-lg font-bold text-green-600">
                                {{ $conversion->count }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-chart-line text-gray-300 text-4xl mb-4"></i>
                            <p class="text-gray-500 text-lg">No monthly data available</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Conversion by Source -->
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-bullseye text-blue-500 mr-2"></i>
                    Conversions by Source
                </h3>
                <div class="space-y-4">
                    @forelse($conversionBySource as $source)
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-link text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">{{ $source->source_name }}</p>
                                    <p class="text-sm text-gray-500">{{ $source->count }} conversions</p>
                                </div>
                            </div>
                            <div class="text-lg font-bold text-blue-600">
                                {{ $source->count }}
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <i class="fas fa-bullseye text-gray-300 text-4xl mb-4"></i>
                            <p class="text-gray-500 text-lg">No source data available</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Conversion by Type -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-tags text-purple-500 mr-2"></i>
                Conversions by Type
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($conversionByType as $type)
                    <div class="text-center p-6 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="mb-4">
                            <i class="fas 
                                @if($type->converted_to_type == 'client') fa-user text-green-600
                                @elseif($type->converted_to_type == 'opportunity') fa-lightbulb text-green-600
                                @else fa-home text-orange-600
                                @endif text-4xl"></i>
                        </div>
                        <h4 class="text-xl font-bold text-gray-900 mb-2">
                            @if($type->converted_to_type == 'client') Clients
                            @elseif($type->converted_to_type == 'opportunity') Opportunities
                            @else Properties
                            @endif
                        </h4>
                        <p class="text-3xl font-bold text-gray-900">{{ $type->count }}</p>
                        <p class="text-sm text-gray-500">conversions</p>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-chart-pie text-gray-300 text-4xl mb-4"></i>
                        <p class="text-gray-500 text-lg">No type data available</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-tachometer-alt text-indigo-500 mr-2"></i>
                Performance Metrics
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center p-6 bg-gray-50 rounded-lg">
                    <div class="mb-3">
                        <i class="fas fa-tachometer-alt text-blue-500 text-3xl"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['best_month_conversions'] }}</p>
                    <p class="text-sm text-gray-500 font-medium">Best Month</p>
                </div>
                
                <div class="text-center p-6 bg-gray-50 rounded-lg">
                    <div class="mb-3">
                        <i class="fas fa-award text-green-500 text-3xl"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['best_source'] }}</p>
                    <p class="text-sm text-gray-500 font-medium">Best Source</p>
                </div>
                
                <div class="text-center p-6 bg-gray-50 rounded-lg">
                    <div class="mb-3">
                        <i class="fas fa-chart-line text-orange-500 text-3xl"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['conversion_trend'] }}%</p>
                    <p class="text-sm text-gray-500 font-medium">Conversion Trend</p>
                </div>
                
                <div class="text-center p-6 bg-gray-50 rounded-lg">
                    <div class="mb-3">
                        <i class="fas fa-trophy text-purple-500 text-3xl"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['top_conversion_type'] }}</p>
                    <p class="text-sm text-gray-500 font-medium">Top Type</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
