@extends('layouts.app')

@section('title', __('Agent Dashboard'))

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-6 py-8">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ __('Agent Dashboard') }}</h1>
                <p class="text-gray-600 mt-1">{{ __('Welcome back, :name! Here\'s what\'s happening with your properties.', ['name' => Auth::user()->name]) }}</p>
            </div>
            <div class="mt-4 md:mt-0 flex space-x-3">
                <a href="{{ route('agent.properties.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition duration-300 shadow-sm">
                    <i class="fas fa-plus mr-2"></i> {{ __('Add Property') }}
                </a>
                <a href="{{ route('agent.profile') }}" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-200 rounded-xl font-semibold hover:bg-gray-50 transition duration-300 shadow-sm">
                    <i class="fas fa-user-edit mr-2"></i> {{ __('Edit Profile') }}
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Properties Stat -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition duration-300">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-2xl">
                        <i class="fas fa-home text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">{{ __('My Properties') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_properties'] ?? 0 }}</p>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-green-500 font-semibold">{{ $stats['active_properties'] ?? 0 }} {{ __('Active') }}</span>
                    <span class="mx-2 text-gray-300">|</span>
                    <span class="text-blue-500 font-semibold">{{ $stats['sold_properties'] ?? 0 }} {{ __('Sold') }}</span>
                </div>
            </div>

            <!-- Leads Stat -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition duration-300">
                <div class="flex items-center">
                    <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">{{ __('Total Leads') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_leads'] ?? 0 }}</p>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-amber-500 font-semibold">{{ $stats['pending_leads'] ?? 0 }} {{ __('Pending') }}</span>
                </div>
            </div>

            <!-- Appointments Stat -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition duration-300">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-50 text-purple-600 rounded-2xl">
                        <i class="fas fa-calendar-check text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">{{ __('Appointments') }}</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_appointments'] ?? 0 }}</p>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm">
                    <span class="text-purple-500 font-semibold">{{ $stats['today_appointments_count'] ?? 0 }} {{ __('Today') }}</span>
                </div>
            </div>

            <!-- Earnings Stat -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition duration-300">
                <div class="flex items-center">
                    <div class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl">
                        <i class="fas fa-wallet text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500 uppercase tracking-wider">{{ __('Total Commission') }}</p>
                        <p class="text-2xl font-bold text-gray-900">${{ number_format($stats['total_commission'] ?? 0) }}</p>
                    </div>
                </div>
                <div class="mt-4 flex items-center text-sm text-emerald-500 font-semibold">
                    {{ __('This Month:') }} ${{ number_format($stats['this_month_commissions'] ?? 0) }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Properties & Activity -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Recent Properties -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900">{{ __('Recent Properties') }}</h2>
                        <a href="{{ route('agent.properties.index') }}" class="text-blue-600 font-semibold text-sm hover:text-blue-700 transition">
                            {{ __('View All') }} <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="p-0">
                        @forelse($recentProperties as $property)
                            <div class="flex items-center p-6 hover:bg-gray-50 transition border-b border-gray-50 last:border-0">
                                <div class="w-20 h-20 rounded-xl overflow-hidden flex-shrink-0 bg-gray-100">
                                    @if($property->media && $property->media->where('media_type', 'image')->count() > 0)
                                        <img src="{{ $property->media->where('media_type', 'image')->first()->url }}" alt="{{ $property->title }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                                            <i class="fas fa-building text-2xl"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-6 flex-grow">
                                    <h3 class="font-bold text-gray-900 text-lg mb-1">{{ $property->title }}</h3>
                                    <p class="text-gray-500 text-sm flex items-center">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        @if($property->location)
                                            {{ $property->location->city }}, {{ $property->location->country }}
                                        @else
                                            {{ $property->address }}
                                        @endif
                                    </p>
                                </div>
                                <div class="text-right ml-4">
                                    <p class="text-lg font-bold text-blue-600">
                                        @if($property->pricing)
                                            {{ number_format($property->pricing->price) }} {{ $property->pricing->currency }}
                                        @else
                                            {{ number_format($property->price) }}
                                        @endif
                                    </p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $property->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }} mt-1 uppercase tracking-wider">
                                        {{ $property->status }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="p-12 text-center">
                                <div class="bg-gray-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-home text-gray-300 text-2xl"></i>
                                </div>
                                <h3 class="text-gray-900 font-bold">{{ __('No Properties Yet') }}</h3>
                                <p class="text-gray-500 mt-1">{{ __('Start by adding your first property listing.') }}</p>
                                <a href="{{ route('agent.properties.create') }}" class="mt-4 inline-block text-blue-600 font-bold hover:text-blue-700">
                                    {{ __('Add Property') }}
                                </a>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Leads -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900">{{ __('Recent Leads') }}</h2>
                        <a href="{{ route('agent.leads') }}" class="text-blue-600 font-semibold text-sm hover:text-blue-700 transition">
                            {{ __('View All') }} <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Client') }}</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Source') }}</th>
                                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                                    <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($recentLeads as $lead)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                                    {{ substr($lead->name, 0, 1) }}
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-bold text-gray-900">{{ $lead->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $lead->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $lead->source ?? __('Website') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 uppercase">
                                                {{ $lead->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm">
                                            <a href="{{ route('agent.crm.create', ['lead_id' => $lead->id]) }}" class="text-blue-600 font-bold hover:text-blue-800">
                                                {{ __('Follow Up') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                            {{ __('No leads found.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column: Appointments & Activity -->
            <div class="space-y-8">
                <!-- Today's Appointments -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-xl font-bold text-gray-900">{{ __('Today\'s Appointments') }}</h2>
                    </div>
                    <div class="p-6">
                        @forelse($todayAppointments as $appointment)
                            <div class="flex items-start mb-6 last:mb-0">
                                <div class="w-12 flex-shrink-0 text-center">
                                    <p class="text-sm font-bold text-blue-600">{{ $appointment->appointment_date->format('H:i') }}</p>
                                </div>
                                <div class="ml-4 border-l-2 border-blue-500 pl-4">
                                    <h4 class="font-bold text-gray-900">{{ $appointment->lead->name ?? __('Private Viewing') }}</h4>
                                    <p class="text-sm text-gray-500">{{ $appointment->property->title ?? __('No Property Linked') }}</p>
                                    <span class="text-xs text-gray-400 mt-1 block">
                                        <i class="far fa-clock mr-1"></i> {{ $appointment->status }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <div class="bg-purple-50 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="far fa-calendar-times text-purple-400 text-xl"></i>
                                </div>
                                <p class="text-gray-500 text-sm">{{ __('No appointments today.') }}</p>
                            </div>
                        @endforelse
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <a href="{{ route('agent.appointments.index') }}" class="block text-center bg-gray-50 text-gray-700 py-3 rounded-xl font-bold hover:bg-gray-100 transition">
                                {{ __('Manage Calendar') }}
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Appointments -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-xl font-bold text-gray-900">{{ __('Upcoming') }}</h2>
                    </div>
                    <div class="p-6">
                        @forelse($upcomingAppointments as $appointment)
                            <div class="flex items-center justify-between mb-4 last:mb-0">
                                <div>
                                    <p class="font-bold text-gray-900 text-sm">{{ $appointment->lead->name ?? __('Unknown') }}</p>
                                    <p class="text-xs text-gray-500">{{ $appointment->appointment_date->format('M d, Y') }}</p>
                                </div>
                                <span class="text-xs font-bold text-blue-600">{{ $appointment->appointment_date->format('H:i') }}</span>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm text-center">{{ __('No upcoming appointments.') }}</p>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h2 class="text-xl font-bold text-gray-900">{{ __('Recent Activity') }}</h2>
                    </div>
                    <div class="p-6">
                        <div class="space-y-6 relative before:absolute before:inset-0 before:left-3 before:w-0.5 before:bg-gray-100">
                            @forelse($recentActivities as $activity)
                                <div class="relative flex items-center">
                                    <div class="absolute left-0 w-6 h-6 rounded-full bg-white border-4 border-blue-500"></div>
                                    <div class="ml-10">
                                        <p class="text-sm font-bold text-gray-900">{{ $activity->action }}</p>
                                        <p class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</p>
                                        <p class="text-xs text-gray-400 mt-1">{{ $activity->details }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-sm text-center ml-0">{{ __('No recent activity.') }}</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
