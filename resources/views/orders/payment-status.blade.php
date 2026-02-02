@extends('admin.layouts.admin')

@section('title', 'تحديث حالة الدفع - طلب #' . $order->order_number)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('orders.show', $order) }}" class="text-gray-600 hover:text-gray-900 transition-colors">
                            <i class="fas fa-arrow-right text-xl"></i>
                        </a>
                        <div class="w-12 h-12 bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl flex items-center justify-center">
                            <i class="fas fa-credit-card text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                                تحديث حالة الدفع
                            </h1>
                            <p class="text-gray-600 text-lg">طلب #{{ $order->order_number }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Status Form -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8">
            <form action="{{ route('orders.update.payment.status', $order) }}" method="POST" class="space-y-6">
                @csrf
                @method('PATCH')

                <!-- Current Status -->
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">الحالة الحالية</h3>
                    <div class="flex items-center gap-4">
                        <span class="px-4 py-2 rounded-full text-sm font-medium
                            @if($order->payment_status === 'pending') bg-orange-100 text-orange-800
                            @elseif($order->payment_status === 'paid') bg-green-100 text-green-800
                            @elseif($order->payment_status === 'failed') bg-red-100 text-red-800
                            @elseif($order->payment_status === 'refunded') bg-gray-100 text-gray-800
                            @endif">
                            {{ $order->payment_status_text }}
                        </span>
                        <span class="text-sm text-gray-600">
                            آخر تحديث: {{ $order->updated_at->diffForHumans() }}
                        </span>
                    </div>
                </div>

                <!-- Update Payment Status -->
                <div class="space-y-4">
                    <label class="block text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-edit ml-2 text-blue-600"></i>
                        تحديث حالة الدفع
                    </label>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">حالة الدفع الجديدة</label>
                        <select name="payment_status" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-gray-50 hover:bg-white">
                            <option value="">اختر الحالة</option>
                            <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>مدفوع</option>
                            <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>فشل</option>
                            <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>مسترد</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">طريقة الدفع</label>
                        <input type="text" name="payment_method" value="{{ $order->payment_method ?? '' }}"
                               class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-gray-50 hover:bg-white"
                               placeholder="مثال: بطاقة ائتمان، باي بال، تحويل بنكي">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">رقم المعاملة (اختياري)</label>
                        <input type="text" name="transaction_id" value="{{ $order->transaction_id ?? '' }}"
                               class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-gray-50 hover:bg-white"
                               placeholder="رقم المعاملة أو المرجع">
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="bg-gray-50 rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">ملخص الطلب</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">رقم الطلب:</span>
                            <span class="font-medium">{{ $order->order_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">تاريخ الطلب:</span>
                            <span class="font-medium">{{ $order->created_at->format('Y-m-d H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">عدد المنتجات:</span>
                            <span class="font-medium">{{ $order->items->count() }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold">
                            <span>الإجمالي:</span>
                            <span class="text-blue-600">{{ $order->formatted_total }}</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('orders.show', $order) }}" 
                       class="flex-1 bg-gray-200 text-gray-700 px-6 py-3 rounded-2xl hover:bg-gray-300 transition-all duration-300 font-semibold text-center">
                        <i class="fas fa-times ml-2"></i>
                        إلغاء
                    </a>
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-green-600 to-emerald-600 text-white px-6 py-3 rounded-2xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-save ml-2"></i>
                        حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
