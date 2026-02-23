@extends('admin.layouts.admin')

@section('title', 'إضافة سجل ضريبة القيمة المضافة')
@section('page-title', 'إضافة سجل ضريبة القيمة المضافة')

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
                                <i class="fas fa-plus text-white text-3xl"></i>
                            </div>
                            <div>
                                <h1 class="text-5xl font-black text-white mb-2 bg-clip-text text-transparent bg-gradient-to-r from-white to-yellow-200">إضافة سجل ضريبة القيمة المضافة</h1>
                                <p class="text-white text-xl font-medium">إنشاء سجل جديد لضريبة القيمة المضافة بأسلوب احترافي</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-reverse space-x-4">
                        <a href="{{ route('taxes.vat.index') }}" class="inline-flex items-center px-8 py-4 bg-white text-indigo-600 rounded-2xl text-sm font-bold hover:bg-yellow-50 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-white transition-all duration-300 transform hover:scale-105 shadow-2xl hover:shadow-yellow-300/50">
                            <i class="fas fa-arrow-right ml-2"></i>
                            العودة للسجلات
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">معلومات سجل ضريبة القيمة المضافة</h3>
        </div>
        <div class="p-6">
            <form method="POST" action="{{ route('taxes.vat.store') }}" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="period" class="block text-sm font-medium text-gray-700 mb-2">الفترة *</label>
                        <select name="period" id="period" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                            <option value="">اختر الفترة</option>
                            <option value="monthly">شهري</option>
                            <option value="quarterly">ربع سنوي</option>
                            <option value="annually">سنوي</option>
                        </select>
                        @error('period')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="vat_rate" class="block text-sm font-medium text-gray-700 mb-2">نسبة ضريبة القيمة المضافة (%) *</label>
                        <input type="number" name="vat_rate" id="vat_rate" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                               value="15" min="0" max="100" step="0.01" required>
                        @error('vat_rate')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="taxable_sales" class="block text-sm font-medium text-gray-700 mb-2">المبيعات الخاضعة للضريبة (ريال) *</label>
                        <input type="number" name="taxable_sales" id="taxable_sales" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                               min="0" step="0.01" required>
                        @error('taxable_sales')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="vat_collected" class="block text-sm font-medium text-gray-700 mb-2">ضريبة القيمة المضافة المحصلة (ريال) *</label>
                        <input type="number" name="vat_collected" id="vat_collected" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                               min="0" step="0.01" required>
                        @error('vat_collected')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="taxable_purchases" class="block text-sm font-medium text-gray-700 mb-2">المشتريات الخاضعة للضريبة (ريال) *</label>
                        <input type="number" name="taxable_purchases" id="taxable_purchases" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                               min="0" step="0.01" required>
                        @error('taxable_purchases')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="vat_paid" class="block text-sm font-medium text-gray-700 mb-2">ضريبة القيمة المضافة المدفوعة (ريال) *</label>
                        <input type="number" name="vat_paid" id="vat_paid" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                               min="0" step="0.01" required>
                        @error('vat_paid')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- VAT Calculation Preview -->
                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg p-6 border border-indigo-200">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">حساب ضريبة القيمة المضافة المستحقة</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white rounded-lg p-4 shadow-sm">
                            <div class="text-sm text-gray-600 mb-1">ضريبة القيمة المضافة المحصلة:</div>
                            <div class="text-xl font-bold text-green-600" id="preview_vat_collected">0.00 ريال</div>
                        </div>
                        <div class="bg-white rounded-lg p-4 shadow-sm">
                            <div class="text-sm text-gray-600 mb-1">ضريبة القيمة المضافة المدفوعة:</div>
                            <div class="text-xl font-bold text-red-600" id="preview_vat_paid">0.00 ريال</div>
                        </div>
                        <div class="bg-white rounded-lg p-4 shadow-sm">
                            <div class="text-sm text-gray-600 mb-1">ضريبة القيمة المضافة المستحقة:</div>
                            <div class="text-xl font-bold" id="preview_vat_payable">0.00 ريال</div>
                            <span id="preview_status" class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800"></span>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                    <a href="{{ route('taxes.vat.index') }}" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors duration-200">
                        <i class="fas fa-times ml-2"></i>إلغاء
                    </a>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-yellow-400 to-orange-500 text-white rounded-lg font-medium hover:from-yellow-500 hover:to-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-all duration-200">
                        <i class="fas fa-save ml-2"></i>حفظ السجل
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const taxableSales = document.getElementById('taxable_sales');
    const vatCollected = document.getElementById('vat_collected');
    const taxablePurchases = document.getElementById('taxable_purchases');
    const vatPaid = document.getElementById('vat_paid');
    const vatRate = document.getElementById('vat_rate');

    function updatePreview() {
        const collected = parseFloat(vatCollected.value) || 0;
        const paid = parseFloat(vatPaid.value) || 0;
        const payable = collected - paid;

        document.getElementById('preview_vat_collected').textContent = collected.toFixed(2);
        document.getElementById('preview_vat_paid').textContent = paid.toFixed(2);
        document.getElementById('preview_vat_payable').textContent = Math.abs(payable).toFixed(2);

        const statusElement = document.getElementById('preview_status');
        if (payable > 0) {
            statusElement.textContent = 'مستحق الدفع';
            statusElement.className = 'badge bg-danger ms-2';
        } else if (payable < 0) {
            statusElement.textContent = 'مسترد';
            statusElement.className = 'badge bg-success ms-2';
        } else {
            statusElement.textContent = 'متوازن';
            statusElement.className = 'badge bg-info ms-2';
        }
    }

    // Auto-calculate VAT from sales
    function calculateVatFromSales() {
        const sales = parseFloat(taxableSales.value) || 0;
        const rate = parseFloat(vatRate.value) || 0;
        const calculatedVat = sales * (rate / 100);
        vatCollected.value = calculatedVat.toFixed(2);
        updatePreview();
    }

    // Auto-calculate VAT from purchases
    function calculateVatFromPurchases() {
        const purchases = parseFloat(taxablePurchases.value) || 0;
        const rate = parseFloat(vatRate.value) || 0;
        const calculatedVat = purchases * (rate / 100);
        vatPaid.value = calculatedVat.toFixed(2);
        updatePreview();
    }

    taxableSales.addEventListener('input', calculateVatFromSales);
    taxablePurchases.addEventListener('input', calculateVatFromPurchases);
    vatCollected.addEventListener('input', updatePreview);
    vatPaid.addEventListener('input', updatePreview);
    vatRate.addEventListener('input', function() {
        calculateVatFromSales();
        calculateVatFromPurchases();
    });

    // Initial calculation
    updatePreview();
});
</script>
@endsection
