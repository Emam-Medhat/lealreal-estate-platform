@extends('layouts.app')

@section('title', 'Property Auctions')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #F1F3F8 0%, #E8EEF7 100%) !important;
    }
    
    :root {
        --blue: #0D6EFD;
        --blue-g: linear-gradient(135deg, #0D6EFD, #0a58ca);
        --purple: #6F42C1;
        --purple-g: linear-gradient(135deg, #6F42C1, #59359a);
        --green: #198754;
        --green-g: linear-gradient(135deg, #198754, #146c43);
        --orange: #FD7E14;
        --orange-g: linear-gradient(135deg, #FD7E14, #dc6a0c);
        --teal: #0DCAF0;
        --red: #DC3545;
        --yellow: #FFC107;
        --sc-blue: linear-gradient(135deg, #0D6EFD 0%, #0a58ca 100%);
        --sc-purple: linear-gradient(135deg, #6F42C1 0%, #59359a 100%);
        --sc-green: linear-gradient(135deg, #198754 0%, #146c43 100%);
        --sc-orange: linear-gradient(135deg, #FD7E14 0%, #dc6a0c 100%);
        --bg: #F1F3F8;
        --bg2: #EAECF3;
        --card: #FFFFFF;
        --sh-sm: 0 1px 4px rgba(0, 0, 0, .06);
        --sh-md: 0 4px 16px rgba(0, 0, 0, .08);
        --sh-blue: 0 6px 24px rgba(13, 110, 253, .3);
        --sh-purple: 0 6px 24px rgba(111, 66, 193, .25);
        --sh-green: 0 6px 24px rgba(25, 135, 84, .25);
        --r1: 10px;
        --r2: 14px;
        --r3: 18px;
        --tr: all .25s cubic-bezier(.4, 0, .2, 1);
        --tr-sp: all .35s cubic-bezier(.34, 1.3, .64, 1);
    }

    .premium-card {
        background: var(--card);
        border: 1px solid #E5E7EB;
        border-radius: var(--r3);
        transition: var(--tr);
        box-shadow: var(--sh-sm);
    }
    .premium-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--sh-md);
    }

    .sc {
        border-radius: var(--r3);
        padding: 22px 20px;
        position: relative;
        overflow: hidden;
        transition: var(--tr-sp);
        color: white;
    }
    .sc:hover { transform: translateY(-6px) scale(1.015); }
    .sc-blue { background: var(--sc-blue); box-shadow: var(--sh-blue); }
    .sc-purple { background: var(--sc-purple); box-shadow: var(--sh-purple); }
    .sc-green { background: var(--sc-green); box-shadow: var(--sh-green); }
    .sc-orange { background: var(--sc-orange); box-shadow: var(--sh-orange); }

    .sc-head { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 14px; position: relative; z-index: 1; }
    .sc-label { font-size: 12px; font-weight: 700; opacity: .88; }
    .sc-ic { width: 42px; height: 42px; border-radius: var(--r1); background: rgba(255, 255, 255, .18); display: flex; align-items: center; justify-content: center; font-size: 17px; }
    .sc-val { font-size: 32px; font-weight: 800; line-height: 1; margin-bottom: 10px; position: relative; z-index: 1; }
    .sc-footer { display: flex; align-items: center; gap: 8px; position: relative; z-index: 1; }
    .sc-delta { display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 20px; font-size: 11px; font-weight: 700; background: rgba(255, 255, 255, .18); }

    .glass-header {
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.95), rgba(147, 51, 234, 0.95)) !important;
        backdrop-filter: blur(10px) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }

    .premium-card {
        background: #FFFFFF !important;
        border: 1px solid #E5E7EB !important;
        border-radius: 18px !important;
        transition: all 0.25s cubic-bezier(.4, 0, .2, 1) !important;
        box-shadow: 0 1px 4px rgba(0, 0, 0, .06) !important;
    }
    
    .premium-card:hover {
        transform: translateY(-4px) !important;
        box-shadow: 0 4px 16px rgba(0, 0, 0, .08) !important;
    }
</style>

<div class="content-wrapper" style="background: linear-gradient(135deg, #F1F3F8 0%, #E8EEF7 100%) !important; min-height: 100vh; margin-top:130px">
    <div class="w-full px-4 md:px-6 lg:px-8">
        <!-- Colorful Header -->
        <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-600 rounded-2xl shadow-xl p-6 mb-8">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="bg-white/20 backdrop-blur p-4 rounded-xl">
                        <i class="fas fa-gavel text-white text-3xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-black text-white">Property Auctions</h1>
                        <p class="text-blue-100">Browse and participate in live property auctions</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('auctions.my') }}" class="px-6 py-3 bg-white text-blue-600 rounded-xl hover:bg-blue-50 transition-all duration-300 flex items-center shadow-lg">
                        <i class="fas fa-list ml-2"></i>
                        My Auctions
                    </a>
                    <a href="{{ route('messages.auctions.results') }}" class="px-6 py-3 bg-white/20 backdrop-blur text-white rounded-xl hover:bg-white/30 transition-all duration-300 flex items-center border border-white/30">
                        <i class="fas fa-trophy ml-2"></i>
                        Results
                    </a>
                </div>
            </div>
        </div>

    <!-- Colorful Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Auctions Card -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 hover:shadow-xl hover:scale-105 transition-all duration-300">
            <div class="flex items-center">
                <div class="bg-white/20 backdrop-blur p-3 rounded-lg mr-4">
                    <i class="fas fa-gavel text-white text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-blue-100 font-medium">Total Auctions</p>
                    <p class="text-2xl font-black text-white">{{ $stats['total'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Active Auctions Card -->
        <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl shadow-lg p-6 hover:shadow-xl hover:scale-105 transition-all duration-300">
            <div class="flex items-center">
                <div class="bg-white/20 backdrop-blur p-3 rounded-lg mr-4">
                    <i class="fas fa-bolt text-white text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-green-100 font-medium">Active</p>
                    <p class="text-2xl font-black text-white">{{ $stats['active'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Completed Auctions Card -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 hover:shadow-xl hover:scale-105 transition-all duration-300">
            <div class="flex items-center">
                <div class="bg-white/20 backdrop-blur p-3 rounded-lg mr-4">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-purple-100 font-medium">Completed</p>
                    <p class="text-2xl font-black text-white">{{ $stats['completed'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <!-- Upcoming Auctions Card -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl shadow-lg p-6 hover:shadow-xl hover:scale-105 transition-all duration-300">
            <div class="flex items-center">
                <div class="bg-white/20 backdrop-blur p-3 rounded-lg mr-4">
                    <i class="fas fa-clock text-white text-xl"></i>
                </div>
                <div>
                    <p class="text-sm text-orange-100 font-medium">Upcoming</p>
                    <p class="text-2xl font-black text-white">{{ $stats['upcoming'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Colorful Filter Section -->
    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl shadow-lg p-6 mb-8 border border-indigo-100">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-900">Filter Auctions</h2>
            <div class="bg-indigo-100 p-2 rounded-lg">
                <i class="fas fa-filter text-indigo-600"></i>
            </div>
        </div>
        <form method="GET" action="{{ route('messages.auctions.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-blue-700 mb-2">Type</label>
                    <select name="type" class="w-full px-3 py-2 bg-white border-2 border-blue-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Types</option>
                        <option value="public" {{ request('type') == 'public' ? 'selected' : '' }}>Public</option>
                        <option value="private" {{ request('type') == 'private' ? 'selected' : '' }}>Private</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-green-700 mb-2">Min Price</label>
                    <input type="number" name="min_price" value="{{ request('min_price') }}" 
                           class="w-full px-3 py-2 bg-white border-2 border-green-200 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500" placeholder="Min Price">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-purple-700 mb-2">Max Price</label>
                    <input type="number" name="max_price" value="{{ request('max_price') }}" 
                           class="w-full px-3 py-2 bg-white border-2 border-purple-200 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500" placeholder="Max Price">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-orange-700 mb-2">Sort By</label>
                    <select name="sort" class="w-full px-3 py-2 bg-white border-2 border-orange-200 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="ending_soon" {{ request('sort') == 'ending_soon' ? 'selected' : '' }}>Ending Soon</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price High to Low</option>
                    </select>
                </div>
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="reset" class="px-4 py-2 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-lg hover:from-gray-600 hover:to-gray-700 transition-all duration-300">
                    <i class="fas fa-times mr-2"></i>
                    Clear Filters
                </button>
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-300 shadow-lg">
                    <i class="fas fa-check mr-2"></i>
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Colorful Auctions Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @forelse ($auctions as $auction)
            <div class="bg-white rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300 overflow-hidden">
                <!-- Image -->
                <div class="relative h-48 bg-gradient-to-br from-gray-100 to-gray-200 rounded-t-xl overflow-hidden">
                    @if ($auction->property->images->first())
                        <img src="{{ $auction->property->images->first()->url }}" 
                             alt="{{ $auction->property->title }}" 
                             class="w-full h-full object-cover hover:scale-110 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-50 to-purple-50">
                            <i class="fas fa-home text-4xl text-blue-300"></i>
                        </div>
                    @endif
                    
                    <!-- Status Badge -->
                    <div class="absolute top-3 right-3">
                        @if ($auction->isActive())
                            <span class="px-3 py-1 bg-gradient-to-r from-green-500 to-emerald-500 text-white text-xs font-bold rounded-full shadow-lg">Active</span>
                        @elseif ($auction->isUpcoming())
                            <span class="px-3 py-1 bg-gradient-to-r from-yellow-500 to-orange-500 text-white text-xs font-bold rounded-full shadow-lg">Upcoming</span>
                        @else
                            <span class="px-3 py-1 bg-gradient-to-r from-gray-500 to-gray-600 text-white text-xs font-bold rounded-full shadow-lg">Ended</span>
                        @endif
                    </div>
                </div>
                
                <!-- Content -->
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $auction->title }}</h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $auction->description }}</p>
                    
                    <!-- Price Info -->
                    <div class="space-y-3 mb-4">
                        <div class="flex justify-between items-center p-3 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg">
                            <span class="text-sm font-bold text-blue-700">Current Price:</span>
                            <span class="font-black text-lg text-blue-900">{{ $auction->getFormattedCurrentPrice() }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg">
                            <span class="text-sm font-bold text-purple-700">Bids:</span>
                            <span class="font-black text-lg text-purple-900">{{ $auction->bid_count }}</span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gradient-to-r from-orange-50 to-yellow-50 rounded-lg">
                            <span class="text-sm font-bold text-orange-700">Ends In:</span>
                            <span class="font-black text-lg text-orange-900">{{ $auction->getTimeRemaining() }}</span>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="space-y-3">
                        <a href="{{ route('messages.auctions.show', $auction->id) }}" 
                           class="w-full block text-center px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 font-bold shadow-lg">
                            <i class="fas fa-eye ml-2"></i>
                            View Auction
                        </a>
                        
                        @if ($auction->isActive() && auth()->user() && $auction->canUserParticipate(auth()->user()))
                            @if (!$auction->isUserParticipant(auth()->user()))
                                <form action="{{ route('messages.auctions.join', $auction->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full px-4 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 font-bold shadow-lg">
                                        <i class="fas fa-sign-in-alt ml-2"></i>
                                        Join Auction
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('messages.auctions.bid', $auction->id) }}" 
                                   class="w-full block text-center px-4 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all duration-300 font-bold shadow-lg">
                                    <i class="fas fa-gavel ml-2"></i>
                                    Place Bid
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16">
                <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl p-12 max-w-md mx-auto border border-blue-100">
                    <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-purple-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                        <i class="fas fa-gavel text-white text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">No auctions found</h3>
                    <p class="text-gray-600 mb-6">Try adjusting your filters or check back later.</p>
                    <a href="{{ route('messages.auctions.index') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 font-bold shadow-lg">
                        <i class="fas fa-redo ml-2"></i>
                        Reset Filters
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        <div class="bg-white rounded-xl shadow-lg p-4 inline-flex">
            {{ $auctions->links() }}
        </div>
    </div>
</div>
@endsection
