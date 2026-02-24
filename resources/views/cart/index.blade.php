@extends('layouts.app')

@section('title', __('cart.title'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-12">
    <div class="max-w-6xl mx-auto px-6">
        
        <!-- Header -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center mb-4">
                <div class="bg-white rounded-full p-4 shadow-lg">
                    <i class="fas fa-shopping-cart text-3xl text-indigo-600"></i>
                </div>
            </div>
            <h1 class="text-4xl font-light text-slate-800 mb-2">{{ __('cart.title') }}</h1>
            <p class="text-lg text-slate-600">{{ __('cart.subtitle') }}</p>
            
            @if(!empty($cartItems))
                <div class="mt-6 inline-flex items-center bg-white rounded-full px-6 py-2 shadow-md">
                    <i class="fas fa-shopping-bag text-indigo-600 mr-2"></i>
                    <span class="font-semibold text-slate-700">{{ count($cartItems) }} {{ __('cart.items_count') }}</span>
                </div>
            @endif
        </div>

        <!-- Cart Items -->
        @if(!empty($cartItems))
            <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-8 mb-8">
                <div class="space-y-6">
                    @foreach($cartItems as $item)
                        <div class="flex items-center justify-between p-6 bg-slate-50 rounded-2xl hover:bg-slate-100 transition-colors">
                            <!-- Product Info -->
                            <div class="flex items-center flex-1">
                                <div class="relative">
                                    <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-24 h-24 rounded-xl object-cover shadow-md">
                                    <span class="absolute -top-2 -right-2 bg-indigo-600 text-white text-xs px-2 py-1 rounded-full font-medium">
                                        {{ $item['type'] }}
                                    </span>
                                </div>
                                <div class="mr-6">
                                    <h3 class="text-xl font-semibold text-slate-800 mb-1">{{ $item['name'] }}</h3>
                                    <div class="flex items-center text-2xl font-bold text-emerald-600">
                                        <i class="fab fa-ethereum text-xl mr-2"></i>
                                        {{ $item['price'] }} ETH
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="flex items-center space-x-3 ml-6">
                                <button onclick="removeFromCart({{ $item['id'] }})" class="p-3 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-all">
                                    <i class="fas fa-trash-alt text-lg"></i>
                                </button>
                                <button class="px-6 py-3 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-colors font-medium">
                                    <i class="fas fa-shopping-bag mr-2"></i>
                                    {{ __('cart.buy_now') }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Summary -->
            <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-8">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h3 class="text-2xl font-light text-slate-800 mb-2">{{ __('cart.order_summary') }}</h3>
                        <p class="text-slate-600">{{ __('cart.total') }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-4xl font-bold text-emerald-600 flex items-center justify-end">
                            <i class="fab fa-ethereum text-3xl mr-3"></i>
                            {{ collect($cartItems)->sum('price') }} ETH
                        </div>
                    </div>
                </div>
                
                <div class="flex space-x-4">
                    <button onclick="clearCart()" class="flex-1 px-6 py-4 border-2 border-slate-300 text-slate-700 rounded-2xl hover:bg-slate-50 transition-colors font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>
                        {{ __('cart.continue_shopping') }}
                    </button>
                    <button class="flex-1 px-6 py-4 bg-gradient-to-r from-emerald-600 to-emerald-700 text-white rounded-2xl hover:from-emerald-700 hover:to-emerald-800 transition-all font-medium shadow-lg">
                        <i class="fas fa-credit-card mr-2"></i>
                        {{ __('cart.checkout') }}
                    </button>
                </div>
            </div>
        @else
            <!-- Empty Cart -->
            <div class="text-center py-20">
                <div class="bg-white rounded-3xl shadow-lg border border-slate-200 p-12 max-w-md mx-auto mb-8">
                    <i class="fas fa-shopping-cart text-6xl text-slate-300 mb-6"></i>
                    <h3 class="text-3xl font-light text-slate-700 mb-3">{{ __('cart.cart_empty') }}</h3>
                    <p class="text-lg text-slate-600 mb-8">{{ __('cart.no_items') }}</p>
                    <a href="{{ route('blockchain.metaverse.nft') }}" class="inline-flex items-center px-8 py-4 bg-indigo-600 text-white rounded-2xl hover:bg-indigo-700 transition-colors font-medium">
                        <i class="fas fa-cube mr-3"></i>
                        {{ __('cart.explore_nfts') }}
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
/* Custom Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeInUp 0.6s ease-out;
}

/* Smooth Scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>

<script>
function removeFromCart(id) {
    if(confirm('{{ __('cart.remove_confirm') }}')) {
        fetch(`/cart/remove/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert(data.message || 'حدث خطأ');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('{{ __('cart.remove_error') }}');
        });
    }
}

function clearCart() {
    if(confirm('{{ __('cart.clear_confirm') }}')) {
        fetch('/cart/clear', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            } else {
                alert(data.message || 'حدث خطأ');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('{{ __('cart.clear_error') }}');
        });
    }
}
</script>
@endsection
