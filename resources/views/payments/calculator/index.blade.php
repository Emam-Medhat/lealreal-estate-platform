@extends('layouts.app')

@section('title', 'حاسبة التمويل العقاري')

@section('content')
<div class="bg-gray-50 min-h-screen py-8">
    <div class="container mx-auto px-4 sm:px-6">
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
            <div class="text-right">
                <h1 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-calculator ml-2 text-blue-600"></i>
                    حاسبة التمويل العقاري
                </h1>
                <p class="text-gray-600 mt-2">احسب أقساطك الشهرية وتفاصيل التمويل العقاري بسهولة</p>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('properties.index') }}" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-200 rounded-xl font-semibold hover:bg-gray-50 transition duration-300 shadow-sm">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة للعقارات
                </a>
            </div>
        </div>
        
        <div class="max-w-6xl mx-auto">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 md:p-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Calculator Form -->
                <div class="space-y-6">
                    <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 pb-3 border-b border-gray-200 flex items-center">
                            <i class="fas fa-calculator ml-3 text-blue-600"></i>
                            تفاصيل التمويل
                        </h3>
                        
                        <div class="space-y-5">
                            <!-- Loan Amount -->
                            <div class="form-group">
                                <label for="loan_amount" class="block text-sm font-medium text-gray-700 mb-2 text-right">
                                    <i class="fas fa-money-bill-wave ml-2 text-blue-600"></i>
                                    مبلغ التمويل
                                </label>
                                <div class="relative">
                                    <input type="text" 
                                           id="loan_amount" 
                                           class="form-input block w-full pr-12 py-3 text-right text-gray-800 border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                           placeholder="1,000,000"
                                           value="1,000,000"
                                           dir="ltr">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500 font-medium">
                                            ر.س
                                        </span>
                                    </div>
                                </div>
                                <p class="mt-1 text-xs text-gray-500 text-right">أدخل مبلغ التمويل المطلوب</p>
                            </div>

                    <div class="form-group">
                        <label for="interest_rate" class="block text-sm font-medium text-gray-700 mb-1 text-right">
                            نسبة الفائدة السنوية
                        </label>
                        <div class="relative rounded-md shadow-sm">
                            <input type="number" 
                                   id="interest_rate" 
                                   step="0.01"
                                   class="form-input block w-full pr-10 sm:text-sm sm:leading-5 text-right" 
                                   placeholder="0.00"
                                   value="4.5">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm sm:leading-5">
                                    %
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="loan_term" class="block text-sm font-medium text-gray-700 mb-1 text-right">
                            مدة التمويل (بالسنوات)
                        </label>
                        <input type="number" 
                               id="loan_term" 
                               class="form-input block w-full sm:text-sm sm:leading-5 text-right"
                               value="30">
                    </div>

                    <div class="form-group">
                        <label for="down_payment" class="block text-sm font-medium text-gray-700 mb-1 text-right">
                            الدفعة المقدمة (اختياري)
                        </label>
                        <div class="relative rounded-md shadow-sm">
                            <input type="number" 
                                   id="down_payment" 
                                   class="form-input block w-full pr-10 sm:text-sm sm:leading-5 text-right" 
                                   placeholder="0.00"
                                   value="20000">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm sm:leading-5">
                                    ر.س
                                </span>
                            </div>
                        </div>
                    </div>

                    <button id="calculate" 
                            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold py-3 px-6 rounded-lg transition-all duration-200 ease-in-out transform hover:-translate-y-0.5 shadow-md hover:shadow-lg">
                        <i class="fas fa-calculator ml-2"></i>
                        احسب القرض
                    </button>
                </div>

                <!-- Results -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-2xl border border-blue-100">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 pb-3 border-b border-blue-200 text-right">
                        <i class="fas fa-chart-pie ml-3 text-blue-600"></i>
                        ملخص الدفعات
                    </h3>
                    
                    <div class="space-y-4">
                        <!-- Monthly Payment -->
                        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                                        <i class="fas fa-calendar-check text-lg"></i>
                                    </div>
                                    <span class="mr-3 text-sm font-medium text-gray-600">الدفعة الشهرية</span>
                                </div>
                                <span id="monthly_payment" class="text-xl font-bold text-blue-600">0.00 ر.س</span>
                            </div>
                        </div>
                        
                        <!-- Total Payment -->
                        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <div class="p-2 bg-green-50 rounded-lg text-green-600">
                                        <i class="fas fa-money-bill-wave text-lg"></i>
                                    </div>
                                    <span class="mr-3 text-sm font-medium text-gray-600">إجمالي المبلغ المدفوع</span>
                                </div>
                                <span id="total_payment" class="text-lg font-semibold text-gray-800">0.00 ر.س</span>
                            </div>
                        </div>
                        
                        <!-- Total Interest -->
                        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow duration-300">
                            <div class="flex justify-between items-center">
                                <div class="flex items-center">
                                    <div class="p-2 bg-red-50 rounded-lg text-red-600">
                                        <i class="fas fa-percentage text-lg"></i>
                                    </div>
                                    <span class="mr-3 text-sm font-medium text-gray-600">إجمالي الفائدة</span>
                                </div>
                                <span id="total_interest" class="text-lg font-semibold text-red-500">0.00 ر.س</span>
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 my-4"></div>
                        
                        <!-- Additional Details -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">سعر الفائدة الشهري</p>
                                <p id="monthly_rate" class="text-sm font-semibold text-gray-800">0.00%</p>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-xs text-gray-500 mb-1">عدد الدفعات</p>
                                <p id="num_payments" class="text-sm font-semibold text-gray-800">0 شهر</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <h4 class="text-md font-semibold text-gray-800 mb-3 text-right">
                            <i class="fas fa-chart-pie ml-2 text-blue-600"></i>
                            توزيع المدفوعات
                        </h4>
                        <div class="bg-white p-4 rounded-xl border border-gray-100">
                            <div class="w-full h-4 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full flex items-center justify-center text-white text-xs font-bold transition-all duration-1000 ease-in-out" 
                                     style="width: 100%" 
                                     id="payment_chart">
                                </div>
                            </div>
                            <div class="flex justify-between mt-3 text-sm text-gray-600">
                                <span class="inline-flex items-center">
                                    <span class="w-3 h-3 bg-blue-500 rounded-full mr-1"></span>
                                    أصل القرض
                                </span>
                                <span class="inline-flex items-center">
                                    <span class="w-3 h-3 bg-green-500 rounded-full mr-1"></span>
                                    الفائدة
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Amortization Schedule -->
            <div class="mt-12 pt-8 border-t border-gray-200" id="schedule_section" style="display: none;">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">
                            <i class="fas fa-calendar-alt ml-3 text-blue-600"></i>
                            جدول الأقساط التفصيلي
                        </h3>
                        <p class="text-gray-600 mt-1">عرض تفاصيل جميع الأقساط الشهرية وخطة سداد القرض</p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <button id="print_schedule" class="inline-flex items-center px-4 py-2 bg-white text-gray-700 border border-gray-200 rounded-xl font-semibold hover:bg-gray-50 transition duration-300 shadow-sm">
                            <i class="fas fa-print ml-2"></i>
                            طباعة الجدول
                        </button>
                    </div>
                </div>
                <div class="overflow-x-auto rounded-xl border border-gray-200 bg-white">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr class="text-right">
                                <th class="px-6 py-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center justify-end">
                                        <span>الدفعة</span>
                                        <i class="fas fa-sort ml-1 text-gray-400"></i>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center justify-end">
                                        <span>المبلغ الشهري</span>
                                        <i class="fas fa-sort ml-1 text-gray-400"></i>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center justify-end">
                                        <span>أصل القرض</span>
                                        <i class="fas fa-sort ml-1 text-gray-400"></i>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center justify-end">
                                        <span>الفائدة</span>
                                        <i class="fas fa-sort ml-1 text-gray-400"></i>
                                    </div>
                                </th>
                                <th class="px-6 py-4 text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <div class="flex items-center justify-end">
                                        <span>الرصيد المتبقي</span>
                                        <i class="fas fa-sort ml-1 text-gray-400"></i>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="amortization_body">
                            <!-- Will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
                </div>
            </div>
            
            <!-- Help Section -->
            <div class="mt-12 bg-blue-50 rounded-2xl p-6 border border-blue-100">
                <div class="flex flex-col md:flex-row items-start md:items-center">
                    <div class="md:ml-6 mb-4 md:mb-0">
                        <h4 class="text-lg font-semibold text-gray-900 mb-2">
                            <i class="fas fa-question-circle text-blue-600 ml-2"></i>
                            هل تحتاج إلى مساعدة؟
                        </h4>
                        <p class="text-gray-600 text-sm">فريقنا من الخبراء جاهز لمساعدتك في أي استفسارات بخصوص التمويل العقاري</p>
                    </div>
                    <div class="flex-shrink-0">
                        <a href="{{ route('contact') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transition duration-300 shadow-sm">
                            <i class="fas fa-headset ml-2"></i>
                            اتصل بنا الآن
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// RTL Support
function isRTL() {
    return document.documentElement.dir === 'rtl';
}

// Format number with commas
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

document.addEventListener('DOMContentLoaded', function() {
    // Set RTL direction if not already set
    if (isRTL()) {
        document.body.setAttribute('dir', 'rtl');
    }
    
    // Print functionality
    document.getElementById('print_schedule')?.addEventListener('click', function() {
        const printContent = document.getElementById('schedule_section').innerHTML;
        const originalContent = document.body.innerHTML;
        
        document.body.innerHTML = `
            <div class="p-8">
                <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold mb-2">جدول الأقساط التفصيلي</h1>
                    <p class="text-gray-600">${new Date().toLocaleDateString('ar-SA')}</p>
                </div>
                ${printContent}
            </div>
        `;
        
        window.print();
        document.body.innerHTML = originalContent;
        window.location.reload();
    });
    const calculateBtn = document.getElementById('calculate');
    const form = document.querySelector('form');
    
    // Format currency with RTL support
    const formatCurrency = (amount) => {
        if (isNaN(amount)) return '0.00 ر.س';
        
        // Format number with commas
        const formattedNumber = new Intl.NumberFormat('en-US').format(amount.toFixed(2));
        
        // Add RTL currency
        return isRTL() ? `${formattedNumber} ر.س` : `ر.س ${formattedNumber}`;
    };
    
    // Parse input value to number
    const parseInputValue = (value) => {
        if (!value) return 0;
        return parseFloat(value.toString().replace(/,/g, '')) || 0;
    };
    
    // Calculate mortgage
    const calculateMortgage = () => {
        // Get and clean input values
        const loanAmount = parseInputValue(document.getElementById('loan_amount').value);
        const interestRate = parseInputValue(document.getElementById('interest_rate').value);
        const loanTerm = parseInputValue(document.getElementById('loan_term').value);
        const downPayment = parseInputValue(document.getElementById('down_payment').value);
        
        // Calculate loan details
        const principal = loanAmount - downPayment;
        const monthlyRate = (interestRate / 100) / 12;
        const numPayments = loanTerm * 12;
        
        // Calculate monthly payment
        const monthlyPayment = principal * 
            (monthlyRate * Math.pow(1 + monthlyRate, numPayments)) / 
            (Math.pow(1 + monthlyRate, numPayments) - 1) || 0;
        
        // Calculate totals
        const totalPayment = monthlyPayment * numPayments;
        const totalInterest = totalPayment - principal;
        
        // Update UI
        document.getElementById('monthly_payment').textContent = formatCurrency(monthlyPayment);
        document.getElementById('total_payment').textContent = formatCurrency(totalPayment);
        document.getElementById('total_interest').textContent = formatCurrency(totalInterest);
        document.getElementById('monthly_rate').textContent = (monthlyRate * 100).toFixed(2) + '%';
        document.getElementById('num_payments').textContent = numPayments;
        
        // Update payment chart with animation
        const principalPercent = (principal / totalPayment * 100).toFixed(1);
        const interestPercent = (totalInterest / totalPayment * 100).toFixed(1);
        
        const chart = document.getElementById('payment_chart');
        chart.style.width = '0%';
        chart.innerHTML = `
            <div class="w-full flex">
                <div class="bg-blue-500 h-full flex items-center justify-center transition-all duration-1000 ease-in-out" 
                     style="width: ${principalPercent}%"
                     id="principal-portion">
                    ${principalPercent}%
                </div>
                <div class="bg-green-500 h-full flex items-center justify-center transition-all duration-1000 ease-in-out" 
                     style="width: ${interestPercent}%"
                     id="interest-portion">
                    ${interestPercent}%
                </div>
            </div>
        `;
        
        // Animate the chart
        setTimeout(() => {
            chart.style.width = '100%';
        }, 100);
        
        // Generate amortization schedule
        generateAmortizationSchedule(principal, monthlyRate, numPayments, monthlyPayment);
        
        // Show schedule section
        document.getElementById('schedule_section').style.display = 'block';
    };
    
    // Generate amortization schedule
    const generateAmortizationSchedule = (principal, monthlyRate, numPayments, monthlyPayment) => {
        let balance = principal;
        const tbody = document.getElementById('amortization_body');
        tbody.innerHTML = '';
        
        // Add monthly payments with animation
        const tbody = document.getElementById('amortization_body');
        tbody.innerHTML = '';
        
        // Add header row
        const headerRow = document.createElement('tr');
        headerRow.className = 'bg-gray-50';
        headerRow.innerHTML = `
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                الدفعة
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                المبلغ الشهري
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                أصل القرض
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                الفائدة
            </th>
            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                الرصيد المتبقي
            </th>
        `;
        tbody.appendChild(headerRow);
        
        // Add data rows with staggered animation
        for (let i = 1; i <= numPayments; i++) {
            const interestPayment = balance * monthlyRate;
            const principalPayment = monthlyPayment - interestPayment;
            balance -= principalPayment;
            
            // Create row with animation
            const row = document.createElement('tr');
            row.className = i % 2 === 0 ? 'bg-gray-50' : 'bg-white';
            row.style.opacity = '0';
            row.style.transform = 'translateY(10px)';
            row.style.transition = `opacity 0.3s ease, transform 0.3s ease ${i * 0.03}s`;
            
            // Add cells with formatted numbers
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-right">
                    ${i.toLocaleString('ar-SA')}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right font-medium">
                    ${formatCurrency(monthlyPayment)}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-right">
                    ${formatCurrency(principalPayment)}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-500 text-right">
                    ${formatCurrency(interestPayment)}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-right">
                    ${formatCurrency(Math.max(0, balance))}
                </td>
            `;
            
            tbody.appendChild(row);
            
            // Animate row in
            setTimeout(() => {
                row.style.opacity = '1';
                row.style.transform = 'translateY(0)';
            }, 10);
            
            // Stop if balance is paid off
            if (balance <= 0) break;
        }
    };
    
    // Event listeners
    calculateBtn.addEventListener('click', calculateMortgage);
    
    // Prevent form submission
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            calculateMortgage();
        });
    }
    
    // Format input values on blur
    const formatInputValue = (input) => {
        if (!input) return;
        
        const value = input.value.replace(/,/g, '');
        if (value === '') return;
        
        const num = parseFloat(value);
        if (!isNaN(num)) {
            input.value = num.toLocaleString('en-US');
        }
    };
    
    // Add input validation and formatting
    const inputs = ['loan_amount', 'interest_rate', 'loan_term', 'down_payment'];
    inputs.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            // Format on blur
            input.addEventListener('blur', function() {
                formatInputValue(this);
                calculateMortgage();
            });
            
            // Handle input changes
            input.addEventListener('input', function() {
                // Allow only numbers and decimal point
                this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');
                
                // Calculate on input for better UX
                calculateMortgage();
            });
            
            // Prevent invalid characters
            input.addEventListener('keypress', function(e) {
                if (e.key === 'e' || e.key === 'E' || e.key === '+' || e.key === '-') {
                    e.preventDefault();
                }
            });
        }
    });
    
    // Initial calculation with slight delay to allow for animations
    setTimeout(() => {
        calculateMortgage();
    }, 300);
});
</script>
@endpush

@endsection
