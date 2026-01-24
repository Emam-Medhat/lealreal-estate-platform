@extends('layouts.app')

@section('title', $auction->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $auction->title }}</h1>
                <div class="flex items-center space-x-4 text-sm text-gray-600">
                    <span>{{ $auction->property->type }}</span>
                    <span>•</span>
                    <span>{{ $auction->property->location }}</span>
                    <span>•</span>
                    <span>{{ $auction->property->area }} sqft</span>
                </div>
            </div>
            
            <!-- Status -->
            <div class="text-right">
                @if ($auction->isActive())
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                        Active Auction
                    </span>
                @elseif ($auction->isUpcoming())
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        <span class="w-2 h-2 bg-yellow-400 rounded-full mr-2"></span>
                        Upcoming
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                        Ended
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Property Images -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="aspect-w-16 aspect-h-9 bg-gray-200">
                    @if ($auction->property->images->first())
                        <img src="{{ $auction->property->images->first()->url }}" 
                             alt="{{ $auction->property->title }}" 
                             class="w-full h-96 object-cover">
                    @else
                        <div class="w-full h-96 flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                    @endif
                </div>
                
                @if ($auction->property->images->count() > 1)
                    <div class="grid grid-cols-4 gap-2 p-4">
                        @foreach ($auction->property->images->take(8) as $image)
                            <img src="{{ $image->url }}" alt="Property image" class="w-full h-20 object-cover rounded cursor-pointer hover:opacity-75">
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Description -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Property Description</h2>
                <p class="text-gray-700 leading-relaxed">{{ $auction->description }}</p>
                
                @if ($auction->property->features->isNotEmpty())
                    <h3 class="text-lg font-semibold mt-6 mb-3">Features</h3>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach ($auction->property->features as $feature)
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $feature->name }}
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Bid History -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Recent Bids</h2>
                <div class="space-y-3">
                    @forelse ($bidHistory as $bid)
                        <div class="flex justify-between items-center py-2 border-b">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium">{{ strtoupper(substr($bid->user->name, 0, 1)) }}</span>
                                </div>
                                <div class="ml-3">
                                    <p class="font-medium">{{ $bid->user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $bid->getTimeAgo() }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold">{{ $bid->getFormattedAmount() }}</p>
                                @if ($bid->is_auto_bid)
                                    <p class="text-xs text-purple-600">Auto Bid</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No bids yet</p>
                    @endforelse
                </div>
                
                @if ($bidHistory->count() >= 10)
                    <div class="mt-4 text-center">
                        <button class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            View All Bids
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Auction Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Auction Details</h2>
                
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500">Current Price</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $auction->getFormattedCurrentPrice() }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Starting Price</p>
                        <p class="text-lg font-medium">{{ $auction->getFormattedStartingPrice() }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Bid Increment</p>
                        <p class="text-lg font-medium">{{ $auction->getFormattedBidIncrement() }}</p>
                    </div>
                    
                    @if ($auction->reserve_price)
                        <div>
                            <p class="text-sm text-gray-500">Reserve Price</p>
                            <p class="text-lg font-medium">{{ $auction->getFormattedReservePrice() }}</p>
                        </div>
                    @endif
                    
                    <div>
                        <p class="text-sm text-gray-500">Time Remaining</p>
                        <p class="text-lg font-medium {{ $auction->hasEnded() ? 'text-red-600' : 'text-green-600' }}">
                            {{ $auction->getTimeRemaining() }}
                        </p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Total Bids</p>
                        <p class="text-lg font-medium">{{ $auction->bid_count }}</p>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Participants</p>
                        <p class="text-lg font-medium">{{ $auction->participants->count() }}</p>
                    </div>
                </div>
            </div>

            <!-- Bidding Actions -->
            @if ($auction->isActive() && auth()->user())
                @if ($auction->canUserParticipate(auth()->user()))
                    @if (!$isParticipant)
                        <form action="{{ route('auctions.join', $auction->id) }}" method="POST" class="bg-white rounded-lg shadow p-6">
                            @csrf
                            <button type="submit" class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                                Join Auction
                            </button>
                        </form>
                    @else
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold mb-4">Place Your Bid</h3>
                            <form id="bidForm" action="{{ route('auctions.bid', $auction->id) }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Bid Amount</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                                        <input type="number" name="amount" 
                                               min="{{ $auction->getNextBidAmount() }}" 
                                               step="{{ $auction->bid_increment }}" 
                                               value="{{ $auction->getNextBidAmount() }}"
                                               class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                               required>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Minimum bid: {{ $auction->getFormattedCurrentPrice() }} + {{ $auction->getFormattedBidIncrement() }}</p>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="auto_bid" class="mr-2">
                                        <span class="text-sm">Enable auto-bidding</span>
                                    </label>
                                </div>
                                
                                <div id="maxBidField" class="mb-4 hidden">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Auto Bid</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                                        <input type="number" name="max_auto_bid" 
                                               min="{{ $auction->getNextBidAmount() }}"
                                               class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    </div>
                                </div>
                                
                                <button type="submit" class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                                    Place Bid
                                </button>
                            </form>
                        </div>
                    @endif
                @else
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="text-center">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-gray-600">You cannot participate in this auction</p>
                        </div>
                    </div>
                @endif
            @else
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="text-center">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <p class="text-gray-600 mb-3">
                            @if (!auth()->user())
                                Please <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-700">log in</a> to participate
                            @else
                                This auction is not currently active
                            @endif
                        </p>
                    </div>
                </div>
            @endif

            <!-- Auction Creator -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Auction Host</h3>
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                        <span class="text-lg font-medium">{{ strtoupper(substr($auction->creator->name, 0, 1)) }}</span>
                    </div>
                    <div class="ml-3">
                        <p class="font-medium">{{ $auction->creator->name }}</p>
                        <p class="text-sm text-gray-500">Verified Host</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const autoBidCheckbox = document.querySelector('input[name="auto_bid"]');
    const maxBidField = document.getElementById('maxBidField');
    
    if (autoBidCheckbox) {
        autoBidCheckbox.addEventListener('change', function() {
            maxBidField.classList.toggle('hidden', !this.checked);
        });
    }
    
    // Auto-refresh auction data
    const isActive = @json($auction->isActive());
    if (isActive) {
        setInterval(function() {
            fetch('{{ route("auctions.highest-bid", $auction->id) }}')
                .then(response => response.json())
                .then(data => {
                    if (data.highest_bid) {
                        // Update current price display
                        const priceElements = document.querySelectorAll('.text-blue-600');
                        priceElements.forEach(el => {
                            if (el.textContent.includes('$')) {
                                el.textContent = '$' + data.highest_bid.amount.toLocaleString();
                            }
                        });
                    }
                });
        }, 5000); // Refresh every 5 seconds
    }
});
</script>
@endsection
