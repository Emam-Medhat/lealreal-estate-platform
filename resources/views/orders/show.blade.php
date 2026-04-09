@extends('admin.layouts.admin')

@section('title', 'تفاصيل الطلب #' . $order->order_number)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('orders.index') }}" class="text-gray-600 hover:text-gray-900 transition-colors">
                            <i class="fas fa-arrow-right text-xl"></i>
                        </a>
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-receipt text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                                تفاصيل الطلب
                            </h1>
                            <p class="text-gray-600 text-lg">رقم الطلب: {{ $order->order_number }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row gap-3">
                    @if($order->status === 'pending')
                        <a href="{{ route('orders.cancel', $order) }}" 
                           onclick="return confirm('هل أنت متأكد من إلغاء هذا الطلب؟')"
                           class="bg-red-600 text-white px-6 py-3 rounded-2xl hover:bg-red-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center gap-2">
                            <i class="fas fa-times"></i>
                            إلغاء الطلب
                        </a>
                    @endif
                    
                    @if($order->payment_status === 'pending')
                        <a href="{{ route('orders.payment.status', $order) }}" 
                           class="bg-green-600 text-white px-6 py-3 rounded-2xl hover:bg-green-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center gap-2">
                            <i class="fas fa-credit-card"></i>
                            تحديث الدفع
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Order Status Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">حالة الطلب</h3>
                    <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-shopping-bag text-blue-600"></i>
                    </div>
                </div>
                <div class="space-y-2">
                    <p class="text-2xl font-bold text-gray-900">{{ $order->status_text }}</p>
                    <p class="text-sm text-gray-600">آخر تحديث: {{ $order->updated_at->diffForHumans() }}</p>
                </div>
            </div>

            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">حالة الدفع</h3>
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-green-600"></i>
                    </div>
                </div>
                <div class="space-y-2">
                    <p class="text-2xl font-bold text-gray-900">{{ $order->payment_status_text }}</p>
                    @if($order->paid_at)
                        <p class="text-sm text-gray-600">تم الدفع: {{ $order->paid_at->format('Y-m-d H:i') }}</p>
                    @endif
                </div>
            </div>

            <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">الإجمالي</h3>
                    <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="fas fa-calculator text-purple-600"></i>
                    </div>
                </div>
                <div class="space-y-2">
                    <p class="text-2xl font-bold text-gray-900">{{ $order->formatted_total }}</p>
                    <p class="text-sm text-gray-600">{{ $order->items->count() }} منتجات</p>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">المنتجات</h2>
            
            <div class="space-y-4">
                @foreach($order->items as $item)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors duration-200">
                        <div class="flex items-center gap-4">
                            @if($item->itemable && $item->itemable->image_path)
                                <img src="{{ asset('storage/' . $item->itemable->image_path) }}" 
                                     alt="{{ $item->item_name }}" 
                                     class="w-16 h-16 object-cover rounded-xl">
                            @else
                                <div class="w-16 h-16 bg-gray-200 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                            @endif
                            
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $item->item_name }}</h4>
                                <p class="text-sm text-gray-600">{{ $item->item_description }}</p>
                                <p class="text-xs text-gray-500">الكمية: {{ $item->quantity }}</p>
                            </div>
                        </div>
                        
                        <div class="text-right">
                            <p class="font-semibold text-gray-900">${{ number_format($item->price, 2) }}</p>
                            <p class="text-sm text-gray-600">الإجمالي: ${{ number_format($item->total, 2) }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Order Summary -->
            <div class="mt-8 pt-6 border-t">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-lg font-semibold text-gray-700">الإجمالي:</span>
                    <span class="text-2xl font-bold text-gray-900">{{ $order->formatted_total }}</span>
                </div>
                
                @if($order->notes)
                    <div class="bg-blue-50 p-4 rounded-xl">
                        <h4 class="font-semibold text-blue-900 mb-2">ملاحظات:</h4>
                        <p class="text-blue-700">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Order Timeline -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8 mt-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">الخط الزمني للطلب</h2>
            
            <div class="space-y-6">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900">تم إنشاء الطلب</h4>
                        <p class="text-sm text-gray-600">{{ $order->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>

                @if($order->paid_at)
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-green-600"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">تم الدفع</h4>
                            <p class="text-sm text-gray-600">{{ $order->paid_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                @endif

                @if($order->delivered_at)
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-truck text-green-600"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">تم التسليم</h4>
                            <p class="text-sm text-gray-600">{{ $order->delivered_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                @endif

                @if($order->status === 'cancelled')
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-times text-red-600"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900">تم إلغاء الطلب</h4>
                            <p class="text-sm text-gray-600">{{ $order->updated_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
