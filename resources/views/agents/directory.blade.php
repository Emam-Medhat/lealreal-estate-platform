@extends('layouts.app')

@section('title', 'Find Real Estate Agents - Real Estate Pro')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-16">
        <div class="container mx-auto px-4">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Find Your Perfect Real Estate Agent</h1>
                <p class="text-xl mb-8">Connect with experienced agents who can help you buy, sell, or rent properties</p>
                
                <!-- Search Form -->
                <form action="{{ route('agents.directory') }}" method="GET" class="max-w-2xl mx-auto">
                    <div class="flex flex-col md:flex-row gap-4">
                        <input 
                            type="text" 
                            name="search" 
                            placeholder="Search by name, license, or bio..."
                            value="{{ request('search') }}"
                            class="flex-1 px-4 py-3 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-300"
                        >
                        <button type="submit" class="px-6 py-3 bg-white text-blue-600 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                            <i class="fas fa-search mr-2"></i>
                            Search
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Filters Section -->
    <section class="bg-white border-b border-gray-200 py-6">
        <div class="container mx-auto px-4">
            <form action="{{ route('agents.directory') }}" method="GET" class="flex flex-wrap gap-4 items-end">
                <input type="hidden" name="search" value="{{ request('search') }}">
                
                <!-- Specialization Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Specialization</label>
                    <select name="specialization" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Specializations</option>
                        @foreach($specializations as $specialization)
                            <option value="{{ $specialization }}" {{ request('specialization') == $specialization ? 'selected' : '' }}>
                                {{ ucfirst($specialization) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Location Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Service Area</label>
                    <input 
                        type="text" 
                        name="location" 
                        placeholder="City or area..."
                        value="{{ request('location') }}"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                <!-- Rating Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Rating</label>
                    <select name="rating" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Any Rating</option>
                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4+ Stars</option>
                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3+ Stars</option>
                        <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2+ Stars</option>
                        <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1+ Stars</option>
                    </select>
                </div>

                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Apply Filters
                </button>
                
                @if(request()->hasAny(['specialization', 'location', 'rating']))
                    <a href="{{ route('agents.directory') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                        Clear Filters
                    </a>
                @endif
            </form>
        </div>
    </section>

    <!-- Agents Grid -->
    <section class="py-12">
        <div class="container mx-auto px-4">
            <div class="mb-6 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900">
                    {{ $agents->total() }} Professional Agent{{ $agents->total() != 1 ? 's' : '' }} Found
                </h2>
                <div class="text-sm text-gray-600">
                    Showing {{ $agents->firstItem() }} to {{ $agents->lastItem() }} of {{ $agents->total() }}
                </div>
            </div>

            @if($agents->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($agents as $agent)
                        <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                            <!-- Agent Header -->
                            <div class="p-6 border-b border-gray-100">
                                <div class="flex items-center space-x-4">
                                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                        @if($agent->profile && $agent->profile->profile_photo)
                                            <img src="{{ asset('storage/' . $agent->profile->profile_photo) }}" 
                                                 alt="{{ $agent->user->name }}" 
                                                 class="w-16 h-16 rounded-full object-cover">
                                        @else
                                            <span class="text-white text-xl font-bold">
                                                {{ substr($agent->user->name, 0, 1) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $agent->user->name }}</h3>
                                        <p class="text-sm text-gray-600">License: {{ $agent->license_number }}</p>
                                        @if($agent->company)
                                            <p class="text-sm text-gray-600">{{ $agent->company->name }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Agent Details -->
                            <div class="p-6">
                                @if($agent->profile && $agent->profile->bio)
                                    <p class="text-gray-700 mb-4 line-clamp-3">{{ Str::limit($agent->profile->bio, 100) }}</p>
                                @endif

                                <!-- Specializations -->
                                @if($agent->profile && $agent->profile->specializations)
                                    <div class="mb-4">
                                        <p class="text-sm font-semibold text-gray-700 mb-2">Specializations:</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach(array_slice($agent->profile->specializations, 0, 3) as $specialization)
                                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">
                                                    {{ ucfirst($specialization) }}
                                                </span>
                                            @endforeach
                                            @if(count($agent->profile->specializations) > 3)
                                                <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">
                                                    +{{ count($agent->profile->specializations) - 3 }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- Stats -->
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div class="text-center">
                                        <div class="text-lg font-semibold text-gray-900">
                                            {{ $agent->properties()->where('status', 'sold')->count() }}
                                        </div>
                                        <div class="text-xs text-gray-600">Properties Sold</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-lg font-semibold text-gray-900">
                                            {{ number_format($agent->reviews()->avg('rating') ?? 0, 1) }}
                                        </div>
                                        <div class="text-xs text-gray-600">Avg Rating</div>
                                    </div>
                                </div>

                                <!-- Rating Stars -->
                                @if($agent->reviews()->count() > 0)
                                    <div class="mb-4">
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= round($agent->reviews()->avg('rating')) ? 'text-yellow-400' : 'text-gray-300' }} text-sm"></i>
                                            @endfor
                                            <span class="ml-2 text-sm text-gray-600">
                                                ({{ $agent->reviews()->count() }} review{{ $agent->reviews()->count() != 1 ? 's' : '' }})
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                <!-- Action Buttons -->
                                <div class="flex space-x-3">
                                    <a href="{{ route('agents.show', $agent) }}" class="flex-1 text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                        View Profile
                                    </a>
                                    <button class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-12">
                    {{ $agents->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">No agents found</h3>
                    <p class="text-gray-600 mb-6">Try adjusting your search criteria or filters</p>
                    <a href="{{ route('agents.directory') }}" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Clear All Filters
                    </a>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
