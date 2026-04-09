@extends('admin.layouts.admin')

@section('title', 'طلباتي')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-list-alt text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                                طلباتي
                            </h1>
                            <p class="text-gray-600 text-lg">إدارة وتتبع جميع طلباتك</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <a href="{{ route('blockchain.metaverse.marketplace') }}" 
                       class="bg-blue-600 text-white px-6 py-3 rounded-2xl hover:bg-blue-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center gap-2">
                        <i class="fas fa-shopping-cart"></i>
                        العودة للسوق
                    </a>
                </div>
            </div>
        </div>

        <!-- Orders List -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8">
            @if($orders->count() > 0)
                <div class="space-y-4">
                    @foreach($orders as $order)
                        <div class="border border-gray-200 rounded-2xl p-6 hover:shadow-lg transition-all duration-300">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            <a href="{{ route('orders.show', $order) }}" class="hover:text-blue-600 transition-colors">
                                                {{ $order->order_number }}
                                            </a>
                                        </h3>
                                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                            @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                            @elseif($order->status === 'completed') bg-green-100 text-green-800
                                            @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                                            @elseif($order->status === 'refunded') bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $order->status_text }}
                                        </span>
                                        <span class="px-3 py-1 rounded-full text-xs font-medium
                                            @if($order->payment_status === 'pending') bg-orange-100 text-orange-800
                                            @elseif($order->payment_status === 'paid') bg-green-100 text-green-800
                                            @elseif($order->payment_status === 'failed') bg-red-100 text-red-800
                                            @elseif($order->payment_status === 'refunded') bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $order->payment_status_text }}
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center gap-6 text-sm text-gray-600">
                                        <span>
                                            <i class="fas fa-calendar ml-1"></i>
                                            {{ $order->created_at->format('Y-m-d H:i') }}
                                        </span>
                                        <span>
                                            <i class="fas fa-box ml-1"></i>
                                            {{ $order->items->count() }} منتجات
                                        </span>
                                        <span>
                                            <i class="fas fa-dollar-sign ml-1"></i>
                                            {{ $order->formatted_total }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('orders.show', $order) }}" 
                                       class="bg-blue-600 text-white px-4 py-2 rounded-xl hover:bg-blue-700 transition-all duration-300 font-medium text-sm">
                                        <i class="fas fa-eye ml-1"></i>
                                        عرض
                                    </a>
                                    
                                    @if($order->status === 'pending')
                                        <a href="{{ route('orders.cancel', $order) }}" 
                                           onclick="return confirm('هل أنت متأكد من إلغاء هذا الطلب؟')"
                                           class="bg-red-600 text-white px-4 py-2 rounded-xl hover:bg-red-700 transition-all duration-300 font-medium text-sm">
                                            <i class="fas fa-times ml-1"></i>
                                            إلغاء
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $orders->links() }}
                </div>
            @else
                <div class="text-center py-20">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-shopping-bag text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">لا توجد طلبات بعد</h3>
                    <p class="text-gray-600 mb-6">ابدأ التسوق من السوق الافتراضي لإنشاء طلباتك الأولى</p>
                    <a href="{{ route('blockchain.metaverse.marketplace') }}" 
                       class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-3 rounded-2xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 inline-flex items-center justify-center gap-2">
                        <i class="fas fa-shopping-cart"></i>
                        ابدأ التسوق
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
