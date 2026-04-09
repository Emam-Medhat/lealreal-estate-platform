@extends('layouts.app')

@section('title', 'Company Directory')

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Page Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="container mx-auto px-6 py-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Company Directory</h1>
                    <p class="mt-1 text-gray-600">Discover and connect with top real estate companies</p>
                </div>
                @auth
                    <div class="mt-4 md:mt-0">
                        <a href="{{ route('companies.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Register Company
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- Search & Filters -->
    <div class="container mx-auto px-6 py-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
            <form method="GET" action="{{ route('companies.index') }}" id="searchForm">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Basic Search -->
                    <div class="lg:col-span-2">
                        <label for="search" class="block text-sm font-semibold text-gray-700 mb-2">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" 
                                   class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm" 
                                   placeholder="Company name, email...">
                        </div>
                    </div>

                    <!-- Company Type -->
                    <div>
                        <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">Company Type</label>
                        <select name="type" id="type" 
                                class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 text-sm appearance-none">
                            <option value="">All Types</option>
                            <option value="agency" {{ request('type') == 'agency' ? 'selected' : '' }}>Agency</option>
                            <option value="developer" {{ request('type') == 'developer' ? 'selected' : '' }}>Developer</option>
                            <option value="contractor" {{ request('type') == 'contractor' ? 'selected' : '' }}>Contractor</option>
                        </select>
                    </div>

                    <!-- Search Actions -->
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-blue-600 text-white px-4 py-2.5 rounded-lg font-semibold hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-all duration-300 flex items-center justify-center">
                            <i class="fas fa-filter mr-2 text-sm"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Companies Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($companies as $company)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-300">
                    <!-- Company Cover -->
                    <div class="h-32 bg-gradient-to-r from-blue-500 to-blue-600 relative">
                        @if($company->profile && $company->profile->cover_image)
                            <img src="{{ asset('storage/' . $company->profile->cover_image) }}" alt="" class="w-full h-full object-cover opacity-50">
                        @endif
                        <div class="absolute -bottom-10 left-6">
                            <div class="w-20 h-20 rounded-xl bg-white p-1 shadow-md border border-gray-100">
                                @if($company->logo_url)
                                    <img src="{{ asset('storage/' . $company->logo_url) }}" alt="{{ $company->name }}" class="w-full h-full object-contain rounded-lg">
                                @else
                                    <div class="w-full h-full bg-gray-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-building text-2xl text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Company Info -->
                    <div class="pt-12 px-6 pb-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 leading-tight">
                                    <a href="{{ route('companies.show', $company) }}" class="hover:text-blue-600 transition-colors">
                                        {{ $company->name }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-500 mt-1 flex items-center">
                                    <i class="fas fa-map-marker-alt mr-1.5 text-gray-400"></i>
                                    {{ $company->address ?? 'Location not specified' }}
                                </p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $company->type === 'agency' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ ucfirst($company->type) }}
                            </span>
                        </div>

                        <div class="flex items-center space-x-4 mb-6 text-sm text-gray-600">
                            <div class="flex items-center">
                                <i class="fas fa-home mr-1.5 text-gray-400"></i>
                                <span>{{ $company->properties_count ?? 0 }} Listings</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-users mr-1.5 text-gray-400"></i>
                                <span>{{ $company->members_count ?? 0 }} Agents</span>
                            </div>
                        </div>

                        <div class="border-t border-gray-50 pt-5 flex items-center justify-between">
                            <div class="flex -space-x-2">
                                <!-- Could add member avatars here -->
                            </div>
                            <a href="{{ route('companies.show', $company) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 flex items-center transition-colors">
                                View Profile
                                <i class="fas fa-arrow-right ml-1.5 text-xs"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                        <i class="fas fa-building text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">No companies found</h3>
                    <p class="text-gray-500 mt-1">Try adjusting your search or filters to find what you're looking for.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $companies->links() }}
        </div>
    </div>
</div>
@endsection
