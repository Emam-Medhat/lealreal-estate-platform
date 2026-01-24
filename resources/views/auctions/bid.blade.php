@extends('layouts.app')

@section('title', 'Place Bid - ' . $auction->title)

@section('content')
<div class="container mx-auto px-4 py-8 max-w-2xl">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <a href="{{ route('auctions.show', $auction->id) }}" class="text-blue-600 hover:text-blue-700 mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Place Your Bid</h1>
        </div>
        
        <!-- Auction Summary -->
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm text-gray-600">Property:</span>
                <span class="font-medium">{{ $auction->title }}</span>
            </div>
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm text-gray-600">Current Price:</span>
                <span class="font-semibold text-lg text-blue-600">{{ $auction->getFormattedCurrentPrice() }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Time Remaining:</span>
                <span class="font-medium {{ $auction->hasEnded() ? 'text-red-600' : 'text-green-600' }}">
                    {{ $auction->getTimeRemaining() }}
                </span>
            </div>
        </div>
    </div>

    <!-- Bid Form -->
    <div class="bg-white rounded-lg shadow-lg p-8">
        <form id="bidForm" action="{{ route('auctions.bid', $auction->id) }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Manual Bid Section -->
            <div>
                <h2 class="text-lg font-semibold mb-4">Your Bid Amount</h2>
                
                <div class="mb-4">
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                        Bid Amount
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-lg">$</span>
                        <input type="number" 
                               id="amount" 
                               name="amount" 
                               min="{{ $auction->getNextBidAmount() }}" 
                               step="{{ $auction->bid_increment }}" 
                               value="{{ $auction->getNextBidAmount() }}"
                               class="w-full pl-10 pr-3 py-3 text-lg border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">
                        Minimum bid: {{ $auction->getFormattedCurrentPrice() }} + {{ $auction->getFormattedBidIncrement() }} = {{ $auction->getFormattedCurrentPrice() + $auction->bid_increment }}
                    </p>
                </div>
                
                <!-- Bid Amount Buttons -->
                <div class="grid grid-cols-3 gap-3 mb-6">
                    <button type="button" class="bid-amount-btn px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm"
                            data-amount="{{ $auction->getNextBidAmount() }}">
                        Min Bid
                    </button>
                    <button type="button" class="bid-amount-btn px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm"
                            data-amount="{{ $auction->getNextBidAmount() + ($auction->bid_increment * 2) }}">
                        +2 Increments
                    </button>
                    <button type="button" class="bid-amount-btn px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm"
                            data-amount="{{ $auction->getNextBidAmount() + ($auction->bid_increment * 5) }}">
                        +5 Increments
                    </button>
                </div>
            </div>

            <!-- Auto Bidding Section -->
            <div class="border-t pt-6">
                <div class="flex items-center mb-4">
                    <input type="checkbox" id="auto_bid" name="auto_bid" class="mr-3 w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="auto_bid" class="text-lg font-medium cursor-pointer">
                        Enable Auto-Bidding
                    </label>
                </div>
                
                <div id="autoBidDetails" class="hidden bg-blue-50 rounded-lg p-4 mb-4">
                    <p class="text-sm text-blue-800 mb-3">
                        Auto-bidding will automatically place bids on your behalf up to your maximum amount when you're outbid.
                    </p>
                    
                    <div>
                        <label for="max_auto_bid" class="block text-sm font-medium text-gray-700 mb-2">
                            Maximum Auto Bid Amount
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-lg">$</span>
                            <input type="number" 
                                   id="max_auto_bid" 
                                   name="max_auto_bid" 
                                   min="{{ $auction->getNextBidAmount() }}"
                                   placeholder="Enter maximum amount"
                                   class="w-full pl-10 pr-3 py-3 text-lg border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <p class="text-sm text-gray-500 mt-2">
                            You won't be charged more than this amount
                        </p>
                    </div>
                </div>
            </div>

            <!-- Recent Bids -->
            <div class="border-t pt-6">
                <h3 class="text-lg font-semibold mb-4">Recent Activity</h3>
                <div class="space-y-2 max-h-48 overflow-y-auto">
                    @forelse ($recentBids as $bid)
                        <div class="flex justify-between items-center py-2 border-b">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center">
                                    <span class="text-xs font-medium">{{ strtoupper(substr($bid->user->name, 0, 1)) }}</span>
                                </div>
                                <div class="ml-2">
                                    <p class="text-sm font-medium">{{ $bid->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $bid->getTimeAgo() }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold">{{ $bid->getFormattedAmount() }}</p>
                                @if ($bid->is_auto_bid)
                                    <p class="text-xs text-purple-600">Auto</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-4">No bids yet</p>
                    @endforelse
                </div>
            </div>

            <!-- Terms and Conditions -->
            <div class="border-t pt-6">
                <div class="bg-yellow-50 rounded-lg p-4 mb-4">
                    <h4 class="font-medium text-yellow-800 mb-2">Important Information</h4>
                    <ul class="text-sm text-yellow-700 space-y-1">
                        <li>• By placing a bid, you commit to purchase if you win</li>
                        <li>• All bids are binding and cannot be retracted once placed</li>
                        <li>• You must be able to complete the transaction within 30 days</li>
                        <li>• Review the property details carefully before bidding</li>
                    </ul>
                </div>
                
                <label class="flex items-start">
                    <input type="checkbox" name="terms" required class="mt-1 mr-3">
                    <span class="text-sm text-gray-600">
                        I have read and agree to the auction terms and conditions, and I commit to purchase the property if I win the auction.
                    </span>
                </label>
            </div>

            <!-- Submit Button -->
            <div class="flex space-x-4">
                <a href="{{ route('auctions.show', $auction->id) }}" 
                   class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-center font-medium">
                    Cancel
                </a>
                <button type="submit" 
                        class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    Place Bid
                </button>
            </div>
        </form>
    </div>

    <!-- Confirmation Modal -->
    <div id="confirmModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold mb-4">Confirm Your Bid</h3>
            <p class="text-gray-600 mb-6">
                You are about to place a bid of <span id="confirmAmount" class="font-bold text-blue-600"></span>.
                This bid is binding and cannot be retracted.
            </p>
            <div class="flex space-x-4">
                <button id="cancelBid" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button id="confirmBid" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Confirm Bid
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bidForm = document.getElementById('bidForm');
    const amountInput = document.getElementById('amount');
    const autoBidCheckbox = document.getElementById('auto_bid');
    const autoBidDetails = document.getElementById('autoBidDetails');
    const maxAutoBidInput = document.getElementById('max_auto_bid');
    const confirmModal = document.getElementById('confirmModal');
    const confirmAmount = document.getElementById('confirmAmount');
    const cancelBid = document.getElementById('cancelBid');
    const confirmBid = document.getElementById('confirmBid');
    
    // Constants from Laravel
    const minBidAmount = @json($auction->getNextBidAmount());
    const bidIncrement = @json($auction->bid_increment);
    const currentPrice = @json($auction->current_price);
    
    // Auto-bid toggle
    autoBidCheckbox.addEventListener('change', function() {
        autoBidDetails.classList.toggle('hidden', !this.checked);
        if (this.checked) {
            maxAutoBidInput.required = true;
        } else {
            maxAutoBidInput.required = false;
            maxAutoBidInput.value = '';
        }
    });
    
    // Bid amount buttons
    document.querySelectorAll('.bid-amount-btn').forEach(button => {
        button.addEventListener('click', function() {
            const amount = parseFloat(this.dataset.amount);
            amountInput.value = amount;
        });
    });
    
    // Auto-refresh current price
    setInterval(function() {
        fetch('{{ route("auctions.highest-bid", $auction->id) }}')
            .then(response => response.json())
            .then(data => {
                if (data.highest_bid && data.highest_bid.amount > currentPrice) {
                    // Update minimum bid
                    const newMinBid = data.highest_bid.amount + bidIncrement;
                    amountInput.min = newMinBid;
                    
                    // Update bid amount buttons
                    document.querySelectorAll('.bid-amount-btn').forEach((button, index) => {
                        const multiplier = index + 1;
                        button.dataset.amount = newMinBid + (bidIncrement * multiplier);
                    });
                    
                    // Show notification
                    const notification = document.createElement('div');
                    notification.className = 'fixed top-4 right-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg z-50';
                    notification.innerHTML = `
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            New bid placed! Current price: $${data.highest_bid.amount.toLocaleString()}
                        </div>
                    `;
                    document.body.appendChild(notification);
                    
                    setTimeout(() => {
                        notification.remove();
                    }, 5000);
                }
            });
    }, 3000); // Refresh every 3 seconds
    
    // Form submission with confirmation
    bidForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const amount = parseFloat(amountInput.value);
        const isAutoBid = autoBidCheckbox.checked;
        const maxAutoBid = isAutoBid ? parseFloat(maxAutoBidInput.value) : null;
        
        // Validation
        if (amount < minBidAmount) {
            alert('Bid amount must be at least $' + minBidAmount.toLocaleString());
            return;
        }
        
        if (isAutoBid && maxAutoBid && maxAutoBid <= amount) {
            alert('Maximum auto bid must be greater than your bid amount');
            return;
        }
        
        // Show confirmation
        confirmAmount.textContent = '$' + amount.toLocaleString();
        confirmModal.classList.remove('hidden');
    });
    
    // Cancel confirmation
    cancelBid.addEventListener('click', function() {
        confirmModal.classList.add('hidden');
    });
    
    // Confirm bid
    confirmBid.addEventListener('click', function() {
        confirmModal.classList.add('hidden');
        bidForm.submit();
    });
});
</script>
@endsection
    