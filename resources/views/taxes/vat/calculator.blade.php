@extends('admin.layouts.admin')

@section('title', 'حاسبة ضريبة القيمة المضافة')
@section('page-title', 'حاسبة ضريبة القيمة المضافة')

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
                                <i class="fas fa-calculator text-white text-3xl"></i>
                            </div>
                            <div>
                                <h1 class="text-5xl font-black text-white mb-2 bg-clip-text text-transparent bg-gradient-to-r from-white to-yellow-200">حاسبة ضريبة القيمة المضافة</h1>
                                <p class="text-white text-xl font-medium">حساب ضريبة القيمة المضافة بأسلوب احترافي</p>
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

    <!-- Calculator and Results -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Calculator Form -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">حساب ضريبة القيمة المضافة</h3>
            </div>
            <div class="p-6">
                <form id="vatCalculator" class="space-y-6">
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">المبلغ</label>
                        <input type="number" name="amount" id="amount" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                               min="0" step="0.01" required>
                    </div>

                    <div>
                        <label for="vat_rate" class="block text-sm font-medium text-gray-700 mb-2">نسبة ضريبة القيمة المضافة (%)</label>
                        <input type="number" name="vat_rate" id="vat_rate" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" 
                               value="15" min="0" max="100" step="0.01" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">نوع الحساب</label>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <input class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" type="radio" name="calculation_type" 
                                       id="exclusive" value="exclusive" checked>
                                <label for="exclusive" class="mr-3 block text-sm text-gray-700">
                                    المبلغ لا يشمل الضريبة (إضافة ضريبة القيمة المضافة)
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" type="radio" name="calculation_type" 
                                       id="inclusive" value="inclusive">
                                <label for="inclusive" class="mr-3 block text-sm text-gray-700">
                                    المبلغ يشمل الضريبة (استخراج ضريبة القيمة المضافة)
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-6 py-3 rounded-lg font-medium hover:from-yellow-500 hover:to-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-all duration-200">
                        <i class="fas fa-calculator ml-2"></i>حساب
                    </button>
                </form>
            </div>
        </div>

        <!-- Results Card -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">نتائج الحساب</h3>
            </div>
            <div class="p-6" id="results" style="display: none;">
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <span class="text-gray-600 font-medium">المبلغ الصافي:</span>
                        <span class="text-xl font-bold text-gray-900" id="net_amount">0.00 ريال</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-gray-100">
                        <span class="text-gray-600 font-medium">ضريبة القيمة المضافة:</span>
                        <span class="text-xl font-bold text-red-600" id="vat_amount">0.00 ريال</span>
                    </div>
                    <div class="flex justify-between items-center py-3">
                        <span class="text-gray-600 font-medium">المبلغ الإجمالي:</span>
                        <span class="text-xl font-bold text-green-600" id="total_amount">0.00 ريال</span>
                    </div>
                    <div class="flex justify-between items-center py-3 bg-gray-50 rounded-lg px-4">
                        <span class="text-gray-600 font-medium">نسبة الضريبة:</span>
                        <span class="text-lg font-bold text-indigo-600" id="vat_rate_display">15%</span>
                    </div>
                </div>
            </div>

            <div class="p-12 text-center text-gray-500" id="placeholder">
                <i class="fas fa-calculator fa-4x text-gray-300 mb-4"></i>
                <p class="text-lg font-medium">أدخل المعلومات وقم بالحساب لعرض النتائج</p>
            </div>
        </div>
    </div>

    <!-- Quick VAT Rates -->
    <div class="mt-8 bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">نسب ضريبة القيمة المضافة الشائعة</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <button class="quick-rate px-4 py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-indigo-100 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200" data-rate="0">
                    0% (معفى)
                </button>
                <button class="quick-rate px-4 py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-indigo-100 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200" data-rate="5">
                    5%
                </button>
                <button class="quick-rate px-4 py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-indigo-100 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200" data-rate="15">
                    15% (السعودية)
                </button>
                <button class="quick-rate px-4 py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-indigo-100 hover:text-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all duration-200" data-rate="20">
                    20% (مصر)
                </button>
            </div>
        </div>
    </div>

    <!-- VAT Information -->
    <div class="mt-8 bg-white rounded-lg shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">معلومات ضريبة القيمة المضافة</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-3">ما هي ضريبة القيمة المضافة؟</h4>
                    <p class="text-gray-600 leading-relaxed">ضريبة القيمة المضافة هي ضريبة غير مباشرة تفرض على السلع والخدمات في كل مراحل الإنتاج والتوزيع، وتُحصل من المستهلك النهائي.</p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 mb-3">كيف يتم حسابها؟</h4>
                    <ul class="text-gray-600 space-y-2">
                        <li class="flex items-start">
                            <i class="fas fa-circle text-xs text-indigo-600 mt-2 ml-2 flex-shrink-0"></i>
                            <span><strong>حالة شاملة الضريبة:</strong> المبلغ الإجمالي ÷ (1 + نسبة الضريبة) = المبلغ الصافي</span>
                        </li>
                        <li class="flex items-start">
                            <i class="fas fa-circle text-xs text-indigo-600 mt-2 ml-2 flex-shrink-0"></i>
                            <span><strong>حالة مستبعدة الضريبة:</strong> المبلغ الصافي × نسبة الضريبة = قيمة الضريبة</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('vatCalculator');
    const resultsDiv = document.getElementById('results');
    const placeholderDiv = document.getElementById('placeholder');
    const quickRateButtons = document.querySelectorAll('.quick-rate');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        calculateVAT();
    });

    quickRateButtons.forEach(button => {
        button.addEventListener('click', function() {
            const rate = this.dataset.rate;
            document.getElementById('vat_rate').value = rate;
            calculateVAT();
        });
    });

    // Auto-calculate on input change
    ['amount', 'vat_rate'].forEach(id => {
        document.getElementById(id).addEventListener('input', calculateVAT);
    });

    document.querySelectorAll('input[name="calculation_type"]').forEach(radio => {
        radio.addEventListener('change', calculateVAT);
    });

    function calculateVAT() {
        const amount = parseFloat(document.getElementById('amount').value) || 0;
        const vatRate = parseFloat(document.getElementById('vat_rate').value) || 0;
        const calculationType = document.querySelector('input[name="calculation_type"]:checked').value;

        if (amount <= 0 || vatRate < 0) {
            resultsDiv.style.display = 'none';
            placeholderDiv.style.display = 'block';
            return;
        }

        let netAmount, vatAmount, totalAmount;

        if (calculationType === 'inclusive') {
            // Amount includes VAT
            netAmount = amount / (1 + (vatRate / 100));
            vatAmount = amount - netAmount;
            totalAmount = amount;
        } else {
            // Amount excludes VAT
            netAmount = amount;
            vatAmount = amount * (vatRate / 100);
            totalAmount = amount + vatAmount;
        }

        // Update results
        document.getElementById('net_amount').textContent = netAmount.toFixed(2) + ' ريال';
        document.getElementById('vat_amount').textContent = vatAmount.toFixed(2) + ' ريال';
        document.getElementById('total_amount').textContent = totalAmount.toFixed(2) + ' ريال';
        document.getElementById('vat_rate_display').textContent = vatRate.toFixed(2) + '%';

        // Show results, hide placeholder
        resultsDiv.style.display = 'block';
        placeholderDiv.style.display = 'none';
    }
});
</script>
@endsection
