@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">{{ __('Appointments') }}</h1>
            <p class="text-gray-600 mt-2">{{ __('Manage your property viewing appointments') }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('agent.appointments.calendar') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition duration-300">
                {{ __('Calendar View') }}
            </a>
            <a href="{{ route('agent.appointments.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                {{ __('Schedule Appointment') }}
            </a>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">{{ __('Today') }}</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['today_appointments'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">{{ __('Completed') }}</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['completed_appointments'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">{{ __('Upcoming') }}</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['upcoming_appointments'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">{{ __('Cancelled') }}</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['cancelled_appointments'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Search') }}</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Search appointments...') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Status') }}</label>
                <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>{{ __('Scheduled') }}</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>{{ __('Confirmed') }}</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>{{ __('Cancelled') }}</option>
                    <option value="no_show" {{ request('status') == 'no_show' ? 'selected' : '' }}>{{ __('No Show') }}</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Date Range') }}</label>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-gray-600 text-white py-2 px-4 rounded-lg hover:bg-gray-700 transition duration-300">
                    {{ __('Filter') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Appointments List -->
    @if(isset($appointments) && count($appointments) > 0)
        <div class="space-y-4">
            @foreach($appointments as $appointment)
                <div class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition duration-300">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center mb-2">
                                <h3 class="text-lg font-semibold text-gray-800 mr-3">
                                    {{ $appointment->lead->name ?? 'Unknown Lead' }}
                                </h3>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full
                                    @if($appointment->status == 'scheduled') bg-blue-100 text-blue-800
                                    @elseif($appointment->status == 'confirmed') bg-green-100 text-green-800
                                    @elseif($appointment->status == 'completed') bg-gray-100 text-gray-800
                                    @elseif($appointment->status == 'cancelled') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst($appointment->status) }}
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <p class="text-sm text-gray-500">{{ __('Property') }}</p>
                                    <p class="font-medium text-gray-800">{{ $appointment->property->title ?? 'No Property' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">{{ __('Date & Time') }}</p>
                                    <p class="font-medium text-gray-800">
                                        {{ $appointment->start_time->format('M j, Y') }} at {{ $appointment->start_time->format('h:i A') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">{{ __('Contact') }}</p>
                                    <p class="font-medium text-gray-800">{{ $appointment->lead->email ?? '' }}</p>
                                </div>
                            </div>

                            @if($appointment->notes)
                                <div class="mb-4">
                                    <p class="text-sm text-gray-500">{{ __('Notes') }}</p>
                                    <p class="text-gray-700">{{ Str::limit($appointment->notes, 150) }}</p>
                                </div>
                            @endif

                            <div class="flex gap-2">
                                <a href="{{ route('agent.appointments.show', $appointment) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-300 text-sm">
                                    {{ __('View Details') }}
                                </a>
                                
                                @if($appointment->status == 'scheduled')
                                    <form method="POST" action="{{ route('agent.appointments.confirm', $appointment) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-300 text-sm">
                                            {{ __('Confirm') }}
                                        </button>
                                    </form>
                                @endif

                                @if(in_array($appointment->status, ['scheduled', 'confirmed']))
                                    <form method="POST" action="{{ route('agent.appointments.complete', $appointment) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition duration-300 text-sm">
                                            {{ __('Mark Complete') }}
                                        </button>
                                    </form>
                                @endif

                                @if(in_array($appointment->status, ['scheduled', 'confirmed']))
                                    <form method="POST" action="{{ route('agent.appointments.cancel', $appointment) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-300 text-sm">
                                            {{ __('Cancel') }}
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-8">
            {{ $appointments->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ __('No Appointments Found') }}</h3>
            <p class="text-gray-600 mb-6">{{ __('Schedule your first property viewing appointment.') }}</p>
            <a href="{{ route('agent.appointments.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-300 inline-block">
                {{ __('Schedule First Appointment') }}
            </a>
        </div>
    @endif
</div>
@endsection
