@extends('admin.layouts.admin')

@section('title', 'السلة')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">السلة</h1>
                <p class="text-gray-600">العناصر المضافة إلى سلة التسوق</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-full font-medium">
                    <i class="fas fa-shopping-cart ml-2"></i>
                    {{ count($cartItems) }} عناصر
                </div>
                @if(!empty($cartItems))
                    <button onclick="clearCart()" class="bg-red-500 text-white px-6 py-2 rounded-xl hover:bg-red-600 transition-colors">
                        <i class="fas fa-trash ml-2"></i>
                        تفريغ السلة
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Cart Items -->
    @if(!empty($cartItems))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @foreach($cartItems as $item)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-shadow">
                    <div class="aspect-w-16 aspect-h-9">
                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-full h-48 object-cover">
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $item['name'] }}</h3>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-2xl font-bold text-green-600">{{ $item['price'] }} ETH</span>
                            <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm">
                                {{ $item['type'] }}
                            </span>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="removeFromCart({{ $item['id'] }})" class="flex-1 bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 transition-colors">
                                <i class="fas fa-trash ml-2"></i>
                                حذف
                            </button>
                            <button class="flex-1 bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition-colors">
                                <i class="fas fa-shopping-bag ml-2"></i>
                                شراء
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Summary -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-semibold text-gray-900">ملخص الطلب</h3>
                <div class="text-right">
                    <p class="text-gray-600">الإجمالي:</p>
                    <p class="text-3xl font-bold text-green-600">{{ collect($cartItems)->sum('price') }} ETH</p>
                </div>
            </div>
            <div class="flex space-x-4">
                <button class="flex-1 bg-gray-500 text-white py-3 rounded-xl hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-right ml-2"></i>
                    متابعة التسوق
                </button>
                <button class="flex-1 bg-gradient-to-r from-green-500 to-emerald-600 text-white py-3 rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all">
                    <i class="fas fa-credit-card ml-2"></i>
                    إتمام الشراء
                </button>
            </div>
        </div>
    @else
        <div class="text-center py-16">
            <div class="bg-gray-100 rounded-full p-8 w-32 h-32 mx-auto mb-6 flex items-center justify-center">
                <i class="fas fa-shopping-cart text-gray-400 text-4xl"></i>
            </div>
            <h3 class="text-2xl font-semibold text-gray-700 mb-4">السلة فارغة</h3>
            <p class="text-gray-600 mb-8">لا توجد عناصر في سلة التسوق حالياً</p>
            <a href="{{ route('blockchain.metaverse.nft') }}" class="bg-blue-500 text-white px-8 py-3 rounded-xl hover:bg-blue-600 transition-colors">
                <i class="fas fa-cube ml-2"></i>
                استكشاف NFTs
            </a>
        </div>
    @endif
</div>

<script>
function removeFromCart(id) {
    if(confirm('هل أنت متأكد من حذف هذا العنصر من السلة؟')) {
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
            alert('حدث خطأ أثناء حذف العنصر');
        });
    }
}

function clearCart() {
    if(confirm('هل أنت متأكد من تفريغ السلة؟')) {
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
            alert('حدث خطأ أثناء تفريغ السلة');
        });
    }
}
</script>
@endsection
