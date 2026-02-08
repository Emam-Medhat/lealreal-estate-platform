@extends('layouts.app')

@section('title', 'Lead Conversion Funnel - Real Estate Pro')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-green-600 to-green-800 text-white py-8">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold">Lead Conversion Funnel</h1>
                    <p class="text-green-100 mt-2">Visualize your lead conversion pipeline</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('lead-conversions.index') }}" class="px-4 py-2 bg-white text-green-600 rounded-lg font-semibold hover:bg-green-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Conversions
                    </a>
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-green-700 text-white rounded-lg font-semibold hover:bg-green-800 transition-colors">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Funnel Visualization -->
    <div class="container mx-auto px-4 py-8">
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Conversion Funnel</h2>
            
            <!-- Funnel Stages -->
            <div class="space-y-4">
                @foreach ($stages as $stage => $count)
                    <div class="flex items-center">
                        <div class="w-32 text-right font-semibold text-gray-700 mr-4">
                            {{ ucfirst($stage) }}
                        </div>
                        <div class="flex-1">
                            <div class="bg-gray-200 rounded-full h-12 relative overflow-hidden">
                                <div class="bg-gradient-to-r from-green-400 to-green-600 h-full rounded-full flex items-center justify-center text-white font-bold"
                                     style="width: {{ $count > 0 ? min(100, ($count / max($stages)) * 100) : 0 }}%">
                                    {{ $count }}
                                </div>
                            </div>
                        </div>
                        <div class="w-20 text-left text-gray-600 ml-4">
                            @if($count > 0)
                                {{ round(($count / max($stages)) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Total Leads</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $stages['new'] ?? 0 }}</p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Converted</p>
                        <p class="text-3xl font-bold text-green-600">{{ $stages['converted'] ?? 0 }}</p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium">Conversion Rate</p>
                        <p class="text-3xl font-bold text-purple-600">
                            @if(($stages['new'] ?? 0) > 0)
                                {{ round((($stages['converted'] ?? 0) / $stages['new']) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stage Breakdown -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Stage Breakdown</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($stages as $stage => $count)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="font-semibold text-gray-700">{{ ucfirst($stage) }}</h4>
                            <span class="text-2xl font-bold text-gray-800">{{ $count }}</span>
                        </div>
                        <div class="text-sm text-gray-500">
                            @if(isset($stages['new']) && $stages['new'] > 0)
                                {{ round(($count / $stages['new']) * 100, 1) }}% of new leads
                            @else
                                No data available
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
