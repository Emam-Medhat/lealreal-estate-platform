@extends('admin.layouts.admin')

@section('title', 'ضريبة القيمة المضافة')
@section('page-title', 'ضريبة القيمة المضافة')

@section('content')
    <!-- Page Header -->
    <div class="mb-8">
        <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 rounded-3xl shadow-2xl p-8 text-white relative overflow-hidden">
            <!-- Animated Background Pattern -->
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full -mr-48 -mt-48 animate-pulse"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-white rounded-full -ml-32 -mb-32 animate-pulse" style="animation-delay: 1s;"></div>
                <div class="absolute top-1/2 left-1/2 w-48 h-48 bg-yellow-300 rounded-full -ml-24 -mt-24 animate-pulse" style="animation-delay: 2s;"></div>
            </div>
            
            <!-- Grid Pattern Overlay -->
            <div class="absolute inset-0 opacity-10">
                <div class="h-full w-full" style="background-image: linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 50px 50px;"></div>
            </div>
            
            <div class="relative z-10">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="bg-white bg-opacity-25 rounded-2xl p-4 ml-4 backdrop-blur-sm border border-white border-opacity-40 shadow-xl transform hover:scale-105 transition-transform duration-300">
                                <i class="fas fa-file-invoice-dollar text-white text-3xl"></i>
                            </div>
                            <div>
                                <h1 class="text-5xl font-black text-white mb-2 bg-clip-text text-transparent bg-gradient-to-r from-white to-yellow-200">ضريبة القيمة المضافة</h1>
                                <p class="text-white text-xl font-medium">إدارة سجلات ضريبة القيمة المضافة بأسلوب احترافي</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-6 mt-6">
                            <div class="bg-white bg-opacity-25 rounded-xl px-6 py-3 backdrop-blur-sm border border-white border-opacity-40 shadow-lg">
                                <span class="text-sm text-white font-medium">إجمالي السجلات:</span>
                                <span class="text-lg font-black text-white ml-2">{{ $records->total() }}</span>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-xl px-6 py-3 backdrop-blur-sm border border-white border-opacity-40 shadow-lg">
                                <span class="text-sm text-white font-medium">قابل للدفع:</span>
                                <span class="text-lg font-black text-white ml-2">{{ $records->where('status', 'payable')->count() }}</span>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-xl px-6 py-3 backdrop-blur-sm border border-white border-opacity-40 shadow-lg">
                                <span class="text-sm text-white font-medium">مدفوع:</span>
                                <span class="text-lg font-black text-white ml-2">{{ $records->where('status', 'paid')->count() }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-reverse space-x-4">
                        <a href="{{ route('taxes.vat.calculator') }}" class="inline-flex items-center px-8 py-4 bg-white text-indigo-600 rounded-2xl text-sm font-bold hover:bg-yellow-50 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-white transition-all duration-300 transform hover:scale-105 shadow-2xl hover:shadow-yellow-300/50">
                            <i class="fas fa-calculator ml-2"></i>
                            حاسبة الضريبة
                        </a>
                        <a href="{{ route('taxes.vat.create') }}" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-yellow-400 to-orange-500 text-white rounded-2xl text-sm font-bold hover:from-yellow-500 hover:to-orange-600 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-yellow-500 transition-all duration-300 transform hover:scale-105 shadow-2xl hover:shadow-yellow-500/50">
                            <i class="fas fa-plus ml-2"></i>
                            إضافة سجل جديد
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('taxes.vat.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="period" class="block text-sm font-medium text-gray-700 mb-2">الفترة</label>
                <select name="period" id="period" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">جميع الفترات</option>
                    <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>شهري</option>
                    <option value="quarterly" {{ request('period') == 'quarterly' ? 'selected' : '' }}>ربع سنوي</option>
                    <option value="annually" {{ request('period') == 'annually' ? 'selected' : '' }}>سنوي</option>
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلق</option>
                    <option value="payable" {{ request('status') == 'payable' ? 'selected' : '' }}>قابل للدفع</option>
                    <option value="refundable" {{ request('status') == 'refundable' ? 'selected' : '' }}>قابل للاسترداد</option>
                    <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>مقدم</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>مدفوع</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-200">
                    <i class="fas fa-filter ml-2"></i>تصفية
                </button>
            </div>
            <div class="flex items-end">
                <a href="{{ route('taxes.vat.index') }}" class="w-full bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200 text-center">
                    <i class="fas fa-times ml-2"></i>مسح
                </a>
            </div>
        </form>
    </div>

    <!-- Records Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">سجلات ضريبة القيمة المضافة</h3>
        </div>
        @if($records->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم السجل</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الفترة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المبيعات الخاضعة للضريبة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المشتريات الخاضعة للضريبة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">ضريبة القيمة المضافة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">ضريبة القيمة المضافة المدفوعة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">ضريبة القيمة المضافة المستحقة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($records as $record)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $record->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $record->period }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($record->taxable_sales, 2) }} ريال</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($record->taxable_purchases, 2) }} ريال</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($record->vat_collected, 2) }} ريال</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($record->vat_paid, 2) }} ريال</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <span class="{{ $record->vat_payable > 0 ? 'text-red-600 font-semibold' : 'text-green-600 font-semibold' }}">
                                        {{ number_format(abs($record->vat_payable), 2) }} ريال
                                        <span class="text-xs">({{ $record->vat_payable > 0 ? 'مستحق' : 'مسترد' }})</span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @switch($record->status)
                                        @case('pending')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">معلق</span>
                                            @break
                                        @case('payable')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">قابل للدفع</span>
                                            @break
                                        @case('refundable')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">قابل للاسترداد</span>
                                            @break
                                        @case('submitted')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">مقدم</span>
                                            @break
                                        @case('paid')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">مدفوع</span>
                                            @break
                                        @default
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">{{ $record->status }}</span>
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-reverse space-x-2">
                                        <a href="{{ route('taxes.vat.show', $record) }}" class="text-indigo-600 hover:text-indigo-900 transition-colors duration-150">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($record->status === 'pending' || $record->status === 'payable' || $record->status === 'refundable')
                                            <form action="{{ route('taxes.vat.submit', $record) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900 transition-colors duration-150" onclick="return confirm('هل أنت متأكد من تقديم هذا الإقرار؟')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        @if($record->status === 'submitted' && $record->vat_payable > 0)
                                            <button type="button" class="text-blue-600 hover:text-blue-900 transition-colors duration-150" data-bs-toggle="modal" data-bs-target="#paymentModal{{ $record->id }}">
                                                <i class="fas fa-money-bill"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        عرض <span class="font-medium">{{ $records->firstItem() }}</span> إلى <span class="font-medium">{{ $records->lastItem() }}</span> من <span class="font-medium">{{ $records->total() }}</span> سجل
                    </div>
                    <div class="flex items-center space-x-reverse space-x-2">
                        {{ $records->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-file-invoice-dollar fa-4x text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد سجلات ضريبة القيمة المضافة</h3>
                <p class="text-gray-500 mb-6">ابدأ بإضافة سجل جديد أو تعديل الفلاتر</p>
                <a href="{{ route('taxes.vat.create') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-yellow-400 to-orange-500 text-white rounded-lg font-medium hover:from-yellow-500 hover:to-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-all duration-200">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة سجل جديد
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Payment Modals -->
@foreach($records as $record)
    @if($record->status === 'submitted' && $record->vat_payable > 0)
        <div class="modal fade" id="paymentModal{{ $record->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">سداد ضريبة القيمة المضافة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('taxes.vat.pay', $record) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">رقم السجل</label>
                                <input type="text" class="form-control" value="#{{ $record->id }}" readonly>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">المبلغ المستحق</label>
                                <input type="text" class="form-control" value="{{ number_format($record->vat_payable, 2) }} ريال" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="payment_method" class="form-label">طريقة الدفع</label>
                                <select name="payment_method" id="payment_method" class="form-select" required>
                                    <option value="">اختر طريقة الدفع</option>
                                    <option value="bank_transfer">تحويل بنكي</option>
                                    <option value="credit_card">بطاقة ائتمان</option>
                                    <option value="cash">نقدي</option>
                                    <option value="check">شيك</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="reference_number" class="form-label">الرقم المرجعي (اختياري)</label>
                                <input type="text" name="reference_number" id="reference_number" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary">تأكيد الدفع</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endforeach
@endsection
