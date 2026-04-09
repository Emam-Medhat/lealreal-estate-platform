@extends('admin.layouts.admin')

@section('title', 'تفاصيل القرض')

@section('page-title', 'تفاصيل القرض')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    <i class="fas fa-hand-holding-usd text-blue-500 ml-3"></i>
                    تفاصيل القرض #{{ $loan->id }}
                </h1>
                <p class="text-gray-600 mt-2">عرض معلومات القرض الكاملة</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('defi.loans.index') }}" 
                   class="bg-white border border-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-50 transition-all duration-300 shadow-sm hover:shadow-md">
                    <i class="fas fa-arrow-right ml-2"></i>
                    عودة للقائمة
                </a>
                @if($loan->status === 'pending' && auth()->user()->is_admin)
                    <form action="{{ route('defi.loans.approve', $loan->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" 
                                class="bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition-all duration-300 shadow-sm hover:shadow-md">
                            <i class="fas fa-check ml-2"></i>
                            موافقة
                        </button>
                    </form>
                    <form action="{{ route('defi.loans.reject', $loan->id) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" 
                                class="bg-red-500 text-white px-6 py-3 rounded-lg hover:bg-red-600 transition-all duration-300 shadow-sm hover:shadow-md">
                            <i class="fas fa-times ml-2"></i>
                            رفض
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Status Alert -->
        <div class="mb-6">
            @if($loan->status === 'pending')
                <div class="bg-yellow-50 border-r-4 border-yellow-400 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-clock text-yellow-400 text-xl"></i>
                        </div>
                        <div class="mr-3">
                            <p class="text-sm text-yellow-700">
                                هذا القرض في انتظار المراجعة والموافقة
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($loan->status === 'approved')
                <div class="bg-green-50 border-r-4 border-green-400 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400 text-xl"></i>
                        </div>
                        <div class="mr-3">
                            <p class="text-sm text-green-700">
                                تمت الموافقة على هذا القرض بنجاح
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($loan->status === 'active')
                <div class="bg-blue-50 border-r-4 border-blue-400 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-play-circle text-blue-400 text-xl"></i>
                        </div>
                        <div class="mr-3">
                            <p class="text-sm text-blue-700">
                                هذا القرض نشط وجاري السداد
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($loan->status === 'completed')
                <div class="bg-gray-50 border-r-4 border-gray-400 p-4 rounded-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-double text-gray-400 text-xl"></i>
                        </div>
                        <div class="mr-3">
                            <p class="text-sm text-gray-700">
                                تم سداد هذا القرض بالكامل
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Loan Information Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-12 h-12 bg-white bg-opacity-20 rounded-full">
                            <i class="fas fa-money-bill-wave text-white text-xl"></i>
                        </div>
                        <h2 class="text-xl font-semibold text-white mr-3">معلومات القرض</h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-500 mb-1">رقم القرض</div>
                            <div class="text-lg font-semibold text-gray-900">#{{ $loan->id }}</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-500 mb-1">الحالة</div>
                            <div class="text-lg font-semibold">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-{{ $loan->status_color ?? 'secondary' }} text-white">
                                    {{ $loan->status_text ?? 'غير معروف' }}
                                </span>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-500 mb-1">مبلغ القرض</div>
                            <div class="text-lg font-semibold text-green-600">{{ number_format($loan->loan_amount, 2) }} ريال</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-500 mb-1">سعر الفائدة</div>
                            <div class="text-lg font-semibold text-blue-600">{{ $loan->interest_rate ?? 0 }}%</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-500 mb-1">مدة القرض</div>
                            <div class="text-lg font-semibold text-gray-900">{{ $loan->loan_term_months ?? 0 }} شهر</div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="text-sm text-gray-500 mb-1">قيمة الضمان</div>
                            <div class="text-lg font-semibold text-orange-600">{{ number_format($loan->collateral_value ?? 0, 2) }} ريال</div>
                        </div>
                    </div>
                    
                    <div class="mt-6 space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-500">الغرض من القرض</span>
                            <span class="font-medium text-gray-900">{{ $loan->purpose ?? 'غير محدد' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-500">الدخل الشهري</span>
                            <span class="font-medium text-gray-900">{{ number_format($loan->monthly_income ?? 0, 2) }} ريال</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                            <span class="text-gray-500">درجة الائتمان</span>
                            <span class="font-medium text-gray-900">{{ $loan->credit_score ?? 0 }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-500">تاريخ الموافقة</span>
                            <span class="font-medium text-gray-900">
                                {{ $loan->approved_date ? (is_string($loan->approved_date) ? $loan->approved_date : $loan->approved_date->format('Y-m-d H:i')) : 'لم يتم الموافقة بعد' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Borrower Information Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-6">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-12 h-12 bg-white bg-opacity-20 rounded-full">
                            <i class="fas fa-user-tie text-white text-xl"></i>
                        </div>
                        <h2 class="text-xl font-semibold text-white mr-3">معلومات المقترض</h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center mb-6">
                        <div class="flex items-center justify-center w-16 h-16 bg-purple-100 rounded-full">
                            <i class="fas fa-user text-purple-600 text-2xl"></i>
                        </div>
                        <div class="mr-4">
                            <h3 class="text-xl font-semibold text-gray-900">{{ $loan->borrower_name ?? 'غير محدد' }}</h3>
                            <p class="text-gray-500">{{ $loan->borrower_email ?? 'غير محدد' }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                            <i class="fas fa-phone text-gray-400 ml-3"></i>
                            <div>
                                <div class="text-sm text-gray-500">رقم الهاتف</div>
                                <div class="font-medium text-gray-900">{{ $loan->borrower_phone ?? 'غير محدد' }}</div>
                            </div>
                        </div>
                        
                        <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                            <i class="fas fa-home text-gray-400 ml-3"></i>
                            <div>
                                <div class="text-sm text-gray-500">العقار</div>
                                <div class="font-medium text-gray-900">{{ $loan->property_title ?? 'غير محدد' }}</div>
                            </div>
                        </div>
                        
                        <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                            <i class="fas fa-map-marker-alt text-gray-400 ml-3"></i>
                            <div>
                                <div class="text-sm text-gray-500">موقع العقار</div>
                                <div class="font-medium text-gray-900">{{ $loan->location ?? 'غير محدد' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Repayments History -->
        @if($repayments->count() > 0)
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-green-500 to-green-600 p-6">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-12 h-12 bg-white bg-opacity-20 rounded-full">
                        <i class="fas fa-history text-white text-xl"></i>
                    </div>
                    <h2 class="text-xl font-semibold text-white mr-3">سجل الدفعات</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الدفعة رقم</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المبلغ</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الدفع</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">طريقة الدفع</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($repayments as $repayment)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $repayment->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600 font-semibold">{{ number_format($repayment->amount, 2) }} ريال</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ is_string($repayment->payment_date) ? $repayment->payment_date : $repayment->payment_date->format('Y-m-d H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $repayment->payment_method ?? 'غير محدد' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Action Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            @if($loan->status === 'pending' && auth()->user()->is_admin)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-yellow-500 to-orange-500 p-6">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-12 h-12 bg-white bg-opacity-20 rounded-full">
                            <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                        </div>
                        <h2 class="text-xl font-semibold text-white mr-3">إجراءات الموافقة</h2>
                    </div>
                </div>
                <div class="p-6 text-center">
                    <p class="text-gray-600 mb-6">هذا القرض في انتظار الموافقة. الرجاء مراجعة المعلومات واتخاذ القرار المناسب.</p>
                    <div class="flex justify-center gap-4">
                        <form action="{{ route('defi.loans.approve', $loan->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" 
                                    class="bg-green-500 text-white px-8 py-3 rounded-lg hover:bg-green-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                <i class="fas fa-check ml-2"></i>
                                موافقة على القرض
                            </button>
                        </form>
                        <form action="{{ route('defi.loans.reject', $loan->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" 
                                    class="bg-red-500 text-white px-8 py-3 rounded-lg hover:bg-red-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                <i class="fas fa-times ml-2"></i>
                                رفض القرض
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            @if($loan->status === 'active' && (auth()->user()->is_admin || auth()->user()->email === $loan->borrower_email))
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-teal-500 p-6">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-12 h-12 bg-white bg-opacity-20 rounded-full">
                            <i class="fas fa-dollar-sign text-white text-xl"></i>
                        </div>
                        <h2 class="text-xl font-semibold text-white mr-3">سداد الدفعات</h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="text-center mb-6">
                        <p class="text-gray-600 mb-4">هذا القرض نشط ويمكن سداد الدفعات</p>
                        <a href="{{ route('defi.loans.repay.form', $loan->id) }}" 
                           class="bg-blue-500 text-white px-8 py-3 rounded-lg hover:bg-blue-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1 inline-block">
                            <i class="fas fa-plus ml-2"></i>
                            تسجيل دفعة جديدة
                        </a>
                    </div>
                    
                    <!-- Quick Repayment Form -->
                    <div class="border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">دفع سريع</h3>
                        <form action="{{ route('defi.loans.repay', $loan->id) }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">المبلغ</label>
                                    <input type="number" name="amount" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" 
                                           placeholder="0.00" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">الطريقة</label>
                                    <select name="payment_method" 
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                        <option value="crypto">عملة رقمية</option>
                                        <option value="bank">تحويل بنكي</option>
                                    </select>
                                </div>
                                <div class="flex items-end">
                                    <button type="submit" 
                                            class="w-full bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                        <i class="fas fa-check ml-2"></i>
                                        دفع
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
