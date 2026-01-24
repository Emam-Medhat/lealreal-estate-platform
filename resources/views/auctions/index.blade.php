@extends('layouts.app')

@section('title', 'Property Auctions')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Property Auctions</h1>
        <p class="text-gray-600">Browse and participate in live property auctions</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Auctions</p>
                    <p class="text-2xl font-semibold">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Active</p>
                    <p class="text-2xl font-semibold">{{ $stats['active'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Completed</p>
                    <p class="text-2xl font-semibold">{{ $stats['completed'] }}</p>
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
                    <p class="text-sm text-gray-500">Upcoming</p>
                    <p class="text-2xl font-semibold">{{ $stats['upcoming'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form method="GET" action="{{ route('auctions.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">All Types</option>
                        <option value="public" {{ request('type') == 'public' ? 'selected' : '' }}>Public</option>
                        <option value="private" {{ request('type') == 'private' ? 'selected' : '' }}>Private</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Min Price</label>
                    <input type="number" name="min_price" value="{{ request('min_price') }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Min Price">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Price</label>
                    <input type="number" name="max_price" value="{{ request('max_price') }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Max Price">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                    <select name="sort" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="ending_soon" {{ request('sort') == 'ending_soon' ? 'selected' : '' }}>Ending Soon</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price High to Low</option>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end space-x-4">
                <button type="reset" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                    Clear Filters
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Auctions Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($auctions as $auction)
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
                <!-- Image -->
                <div class="relative h-48 bg-gray-200 rounded-t-lg overflow-hidden">
                    @if ($auction->property->images->first())
                        <img src="{{ $auction->property->images->first()->url }}" 
                             alt="{{ $auction->property->title }}" 
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                    @endif
                    
                    <!-- Status Badge -->
                    <div class="absolute top-2 right-2">
                        @if ($auction->isActive())
                            <span class="px-2 py-1 bg-green-500 text-white text-xs rounded-full">Active</span>
                        @elseif ($auction->isUpcoming())
                            <span class="px-2 py-1 bg-yellow-500 text-white text-xs rounded-full">Upcoming</span>
                        @else
                            <span class="px-2 py-1 bg-gray-500 text-white text-xs rounded-full">Ended</span>
                        @endif
                    </div>
                </div>
                
                <!-- Content -->
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-2">{{ $auction->title }}</h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $auction->description }}</p>
                    
                    <!-- Price Info -->
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Current Price:</span>
                            <span class="font-semibold text-lg">{{ $auction->getFormattedCurrentPrice() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Bids:</span>
                            <span class="text-sm">{{ $auction->bid_count }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">Ends In:</span>
                            <span class="text-sm font-medium">{{ $auction->getTimeRemaining() }}</span>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="space-y-2">
                        <a href="{{ route('auctions.show', $auction->id) }}" 
                           class="w-full block text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            View Auction
                        </a>
                        
                        @if ($auction->isActive() && auth()->user() && $auction->canUserParticipate(auth()->user()))
                            @if (!$auction->isUserParticipant(auth()->user()))
                                <form action="{{ route('auctions.join', $auction->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                        Join Auction
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('auctions.bid', $auction->id) }}" 
                                   class="w-full block text-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                                    Place Bid
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No auctions found</h3>
                <p class="text-gray-500">Try adjusting your filters or check back later.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $auctions->links() }}
    </div>
</div>
@endsection
