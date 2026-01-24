@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">{{ __('Agent Dashboard') }}</h1>
        <p class="text-gray-600 mt-2">{{ __('Welcome back! Here\'s your real estate overview.') }}</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">{{ __('Total Properties') }}</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_properties'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">{{ __('Total Leads') }}</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_leads'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">{{ __('Appointments') }}</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_appointments'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-full">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">{{ __('Commission') }}</p>
                    <p class="text-2xl font-bold text-gray-800">${{ number_format($stats['total_commission'] ?? 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Properties -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-xl font-semibold text-gray-800">{{ __('Recent Properties') }}</h2>
            </div>
            <div class="p-6">
                @if(isset($recentProperties) && count($recentProperties) > 0)
                    <div class="space-y-4">
                        @foreach($recentProperties as $property)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <h3 class="font-medium text-gray-800">{{ $property->title }}</h3>
                                    <p class="text-sm text-gray-500">{{ $property->address }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-blue-600">${{ number_format($property->price) }}</p>
                                    <span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded-full">
                                        {{ $property->status }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">{{ __('No properties yet') }}</p>
                @endif
                <div class="mt-6">
                    <a href="{{ route('agent.properties.index') }}" class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-300 text-center block">
                        {{ __('View All Properties') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Today's Appointments -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <h2 class="text-xl font-semibold text-gray-800">{{ __('Today\'s Appointments') }}</h2>
            </div>
            <div class="p-6">
                @if(isset($todayAppointments) && count($todayAppointments) > 0)
                    <div class="space-y-4">
                        @foreach($todayAppointments as $appointment)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <h3 class="font-medium text-gray-800">{{ $appointment->lead->name ?? 'Unknown' }}</h3>
                                    <p class="text-sm text-gray-500">{{ $appointment->property->title ?? 'No Property' }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-800">{{ $appointment->start_time->format('h:i A') }}</p>
                                    <span class="text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded-full">
                                        {{ $appointment->status }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-center py-8">{{ __('No appointments today') }}</p>
                @endif
                <div class="mt-6">
                    <a href="{{ route('agent.appointments.index') }}" class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700 transition duration-300 text-center block">
                        {{ __('View All Appointments') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Chart -->
    <div class="bg-white rounded-lg shadow mt-8">
        <div class="p-6 border-b">
            <h2 class="text-xl font-semibold text-gray-800">{{ __('Monthly Performance') }}</h2>
        </div>
        <div class="p-6">
            <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                <p class="text-gray-500">{{ __('Performance chart will be displayed here') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
