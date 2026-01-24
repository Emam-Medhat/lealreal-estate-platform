@extends('layouts.app')

@section('title', 'Payment Methods')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Payment Methods</h1>
                    <p class="text-gray-600">Manage your payment methods and billing information</p>
                </div>
                <a href="{{ route('payments.methods.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Add Payment Method
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-credit-card text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Methods</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $paymentMethods->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Active Methods</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $paymentMethods->where('status', 'active')->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-shield-alt text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Verified Methods</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $paymentMethods->where('is_verified', true)->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-star text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Default Method</p>
                        <p class="text-lg font-bold text-gray-800">
                            @if($default = $paymentMethods->where('is_default', true)->first())
                                {{ $default->nickname }}
                            @else
                                None Set
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Methods List -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-800">Your Payment Methods</h2>
                    <div class="flex items-center space-x-4">
                        <!-- Filter -->
                        <select class="px-3 py-2 border rounded-lg text-sm">
                            <option value="">All Types</option>
                            <option value="card">Credit Cards</option>
                            <option value="bank">Bank Accounts</option>
                            <option value="crypto">Crypto Wallets</option>
                        </select>
                        
                        <!-- Search -->
                        <div class="relative">
                            <input type="text" placeholder="Search methods..." class="pl-10 pr-4 py-2 border rounded-lg text-sm">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="divide-y">
                @forelse ($paymentMethods as $method)
                    <div class="p-6 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center flex-1">
                                <!-- Payment Method Icon -->
                                <div class="mr-4">
                                    @if($method->type === 'card')
                                        <div class="bg-blue-100 rounded-full p-3">
                                            <i class="fas fa-credit-card text-blue-600"></i>
                                        </div>
                                    @elseif($method->type === 'bank')
                                        <div class="bg-green-100 rounded-full p-3">
                                            <i class="fas fa-university text-green-600"></i>
                                        </div>
                                    @elseif($method->type === 'crypto')
                                        <div class="bg-orange-100 rounded-full p-3">
                                            <i class="fab fa-bitcoin text-orange-600"></i>
                                        </div>
                                    @else
                                        <div class="bg-gray-100 rounded-full p-3">
                                            <i class="fas fa-wallet text-gray-600"></i>
                                        </div>
                                    @endif
                                </div>

                                <!-- Payment Method Details -->
                                <div class="flex-1">
                                    <div class="flex items-center mb-2">
                                        <h3 class="text-lg font-semibold text-gray-800 mr-3">{{ $method->nickname }}</h3>
                                        
                                        <!-- Status Badges -->
                                        <div class="flex items-center space-x-2">
                                            @if($method->is_default)
                                                <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full font-medium">Default</span>
                                            @endif
                                            
                                            @if($method->is_verified)
                                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-medium">Verified</span>
                                            @else
                                                <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full font-medium">Unverified</span>
                                            @endif
                                            
                                            @if($method->status === 'active')
                                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-medium">Active</span>
                                            @else
                                                <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full font-medium">Inactive</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="text-sm text-gray-600">
                                        @if($method->type === 'card')
                                            <div>{{ $method->card_brand }} ending in {{ $method->card_last_four }}</div>
                                            <div>Expires {{ $method->card_expiry_month }}/{{ $method->card_expiry_year }}</div>
                                        @elseif($method->type === 'bank')
                                            <div>{{ $method->bank_name }}</div>
                                            <div>Account ending in {{ substr($method->bank_account_number, -4) }}</div>
                                        @elseif($method->type === 'crypto')
                                            <div>{{ $method->wallet_network }} Wallet</div>
                                            <div>{{ substr($method->wallet_address, 0, 10) }}...</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center space-x-2 ml-4">
                                @if(!$method->is_default)
                                    <button onclick="setDefaultMethod({{ $method->id }})" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        <i class="fas fa-star mr-1"></i>
                                        Set Default
                                    </button>
                                @endif
                                
                                @if(!$method->is_verified)
                                    <button onclick="verifyMethod({{ $method->id }})" class="text-green-600 hover:text-green-800 text-sm font-medium">
                                        <i class="fas fa-check mr-1"></i>
                                        Verify
                                    </button>
                                @endif
                                
                                <a href="{{ route('payments.methods.edit', $method) }}" class="text-gray-600 hover:text-gray-800 text-sm font-medium">
                                    <i class="fas fa-edit mr-1"></i>
                                    Edit
                                </a>
                                
                                <button onclick="deleteMethod({{ $method->id }})" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                    <i class="fas fa-trash mr-1"></i>
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <i class="fas fa-credit-card text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">No Payment Methods</h3>
                        <p class="text-gray-600 mb-6">You haven't added any payment methods yet.</p>
                        <a href="{{ route('payments.methods.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors inline-block">
                            <i class="fas fa-plus mr-2"></i>
                            Add Your First Payment Method
                        </a>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pagination -->
        @if($paymentMethods->hasPages())
            <div class="mt-6">
                {{ $paymentMethods->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Confirmation Modal -->
<div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md mx-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Confirm Action</h3>
        <p id="confirmMessage" class="text-gray-600 mb-6"></p>
        
        <div class="flex justify-end space-x-3">
            <button onclick="closeConfirmModal()" class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                            </button>
            <button id="confirmButton" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                Confirm
                            </button>
        </div>
    </div>
</div>

<script>
let currentAction = null;
let currentMethodId = null;

function setDefaultMethod(methodId) {
    currentAction = 'setDefault';
    currentMethodId = methodId;
    
    document.getElementById('confirmMessage').textContent = 'Are you sure you want to set this payment method as default?';
    document.getElementById('confirmButton').textContent = 'Set as Default';
    document.getElementById('confirmButton').className = 'px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors';
    document.getElementById('confirmModal').classList.remove('hidden');
}

function verifyMethod(methodId) {
    currentAction = 'verify';
    currentMethodId = methodId;
    
    document.getElementById('confirmMessage').textContent = 'Are you sure you want to verify this payment method?';
    document.getElementById('confirmButton').textContent = 'Verify Method';
    document.getElementById('confirmButton').className = 'px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors';
    document.getElementById('confirmModal').classList.remove('hidden');
}

function deleteMethod(methodId) {
    currentAction = 'delete';
    currentMethodId = methodId;
    
    document.getElementById('confirmMessage').textContent = 'Are you sure you want to delete this payment method? This action cannot be undone.';
    document.getElementById('confirmButton').textContent = 'Delete';
    document.getElementById('confirmButton').className = 'px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors';
    document.getElementById('confirmModal').classList.remove('hidden');
}

function closeConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    currentAction = null;
    currentMethodId = null;
}

document.getElementById('confirmButton').addEventListener('click', function() {
    if (currentAction && currentMethodId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/payments/methods/${currentMethodId}/${currentAction}`;
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        document.body.appendChild(form);
        form.submit();
    }
});

// Close modal when clicking outside
document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeConfirmModal();
    }
});
</script>
@endsection
