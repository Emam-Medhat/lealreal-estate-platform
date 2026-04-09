@extends('layouts.app')
@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    <!-- Header Section -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                        <i class="fas fa-chart-line mr-3"></i>Financial Analyses
                    </h1>
                    <p class="text-gray-600 mt-2">Manage and track your property investment analyses</p>
                </div>
                <a href="{{ route('financial.analyses.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                    <i class="fas fa-plus mr-2"></i>New Analysis
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Analyses</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ $analyses->count() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-chart-pie text-blue-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Investment</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">
                            ${{ number_format($analyses->sum('purchase_price') ?? 0, 0) }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-green-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Current Value</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">
                            ${{ number_format($analyses->sum('current_value') ?? 0, 0) }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-trending-up text-purple-600"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Avg ROI</p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">
                            @php
                                $totalRoi = 0;
                                $count = 0;
                                foreach($analyses as $analysis) {
                                    if($analysis->current_value > 0 && $analysis->purchase_price > 0) {
                                        $totalRoi += (($analysis->current_value - $analysis->purchase_price) / $analysis->purchase_price) * 100;
                                        $count++;
                                    }
                                }
                                $avgRoi = $count > 0 ? $totalRoi / $count : 0;
                            @endphp
                            {{ number_format($avgRoi, 1) }}%
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-percentage text-orange-600"></i>
                    </div>
                </div>
            </div>
        </div>
        
        @if($analyses->count() > 0)
            <!-- Analyses Table -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-purple-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800">
                        <i class="fas fa-list mr-2"></i>Recent Analyses
                    </h2>
                </div>
                <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Property</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Purchase Price</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Current Value</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">ROI</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($analyses as $analysis)
                            <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 transition-colors duration-200">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-building text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ $analysis->property->title ?? 'N/A' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <i class="fas fa-map-marker-alt mr-1"></i>{{ $analysis->property->address ?? 'N/A' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900">
                                        ${{ number_format($analysis->purchase_price ?? 0, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-gray-900">
                                        ${{ number_format($analysis->current_value ?? 0, 2) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-3 py-1 text-xs font-bold rounded-full 
                                        @if(($analysis->current_value ?? 0) > 0 && $analysis->purchase_price > 0)
                                            {{ ((($analysis->current_value - $analysis->purchase_price) / $analysis->purchase_price) * 100) > 0 ? 'bg-gradient-to-r from-green-400 to-green-600 text-white' : 'bg-gradient-to-r from-red-400 to-red-600 text-white' }}
                                        @else
                                            bg-gray-100 text-gray-800
                                        @endif">
                                        @if(($analysis->current_value ?? 0) > 0 && $analysis->purchase_price > 0)
                                            <i class="fas fa-{{ ((($analysis->current_value - $analysis->purchase_price) / $analysis->purchase_price) * 100) > 0 ? 'arrow-up' : 'arrow-down' }} mr-1"></i>
                                            {{ number_format((($analysis->current_value - $analysis->purchase_price) / $analysis->purchase_price) * 100, 2) }}%
                                        @else
                                            N/A
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900">
                                        <i class="fas fa-calendar mr-1"></i>{{ $analysis->created_at->format('M d, Y') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('financial.analyses.show', $analysis->id) }}" 
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors duration-200">
                                            <i class="fas fa-eye mr-1"></i>View
                                        </a>
                                        <a href="{{ route('financial.analyses.edit', $analysis->id) }}" 
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-purple-600 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors duration-200">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                    {{ $analyses->links() }}
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-chart-line text-3xl text-blue-600"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">No financial analyses found</h3>
                <p class="text-gray-600 mb-8 max-w-md mx-auto">
                    Start tracking your property investments by creating your first financial analysis.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('financial.analyses.create') }}" 
                       class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                        <i class="fas fa-plus mr-2"></i>Create Your First Analysis
                    </a>
                    <a href="/financial/roi-calculator" 
                       class="inline-flex items-center px-8 py-3 bg-white border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:border-gray-400 transition-all duration-300">
                        <i class="fas fa-calculator mr-2"></i>Try ROI Calculator
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
