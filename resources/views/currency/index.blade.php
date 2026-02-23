@extends('admin.layouts.admin')

@section('title', 'تحويل العملات')
@section('page-title', 'تحويل العملات')

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
                                <i class="fas fa-exchange-alt text-white text-3xl"></i>
                            </div>
                            <div>
                                <h1 class="text-5xl font-black text-white mb-2 bg-clip-text text-transparent bg-gradient-to-r from-white to-yellow-200">تحويل العملات</h1>
                                <p class="text-white text-xl font-medium">تحويل العملات بأسعار الصرف الحالية</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-6 mt-6">
                            <div class="bg-white bg-opacity-25 rounded-xl px-6 py-3 backdrop-blur-sm border border-white border-opacity-40 shadow-lg">
                                <span class="text-sm text-white font-medium">العملات المتاحة:</span>
                                <span class="text-lg font-black text-white ml-2">{{ count($currencies) }}</span>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-xl px-6 py-3 backdrop-blur-sm border border-white border-opacity-40 shadow-lg">
                                <span class="text-sm text-white font-medium">التحديث الأخير:</span>
                                <span class="text-lg font-black text-white ml-2">{{ now()->format('H:i') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-reverse space-x-4">
                        <button id="refreshRates" class="inline-flex items-center px-8 py-4 bg-white text-indigo-600 rounded-2xl text-sm font-bold hover:bg-yellow-50 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-white transition-all duration-300 transform hover:scale-105 shadow-2xl hover:shadow-yellow-300/50">
                            <i class="fas fa-sync-alt ml-2"></i>
                            تحديث الأسعار
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Currency Converter Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Converter Form -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 border-b border-blue-200">
                <h3 class="text-xl font-bold text-blue-900 flex items-center">
                    <i class="fas fa-calculator ml-3 text-blue-600"></i>
                    محول العملات
                </h3>
            </div>
            
            <div class="p-6">
                <form id="currencyConverter" class="space-y-6">
                    <div>
                        <label for="amount" class="block text-sm font-bold text-blue-700 mb-3">
                            <i class="fas fa-dollar-sign ml-2 text-blue-600"></i>
                            المبلغ
                        </label>
                        <input type="number" 
                               id="amount" 
                               step="0.01" 
                               value="100" 
                               required
                               class="w-full px-6 py-4 border border-blue-300 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-500 focus:border-transparent text-lg font-medium bg-blue-50 hover:bg-white transition-all duration-300">
                    </div>
                    
                    <div>
                        <label for="fromCurrency" class="block text-sm font-bold text-blue-700 mb-3">
                            <i class="fas fa-arrow-right ml-2 text-blue-600"></i>
                            من
                        </label>
                        <select id="fromCurrency" 
                                required
                                class="w-full px-6 py-4 border border-blue-300 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-500 focus:border-transparent text-lg font-medium bg-blue-50 hover:bg-white transition-all duration-300">
                            @foreach($currencies as $code => $currency)
                                <option value="{{ $code }}" {{ $code == 'USD' ? 'selected' : '' }}>
                                    {{ $currency['symbol'] }} {{ $currency['name'] }} ({{ $code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="toCurrency" class="block text-sm font-bold text-blue-700 mb-3">
                            <i class="fas fa-arrow-left ml-2 text-blue-600"></i>
                            إلى
                        </label>
                        <select id="toCurrency" 
                                required
                                class="w-full px-6 py-4 border border-blue-300 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-500 focus:border-transparent text-lg font-medium bg-blue-50 hover:bg-white transition-all duration-300">
                            @foreach($currencies as $code => $currency)
                                <option value="{{ $code }}" {{ $code == 'EUR' ? 'selected' : '' }}>
                                    {{ $currency['symbol'] }} {{ $currency['name'] }} ({{ $code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-bold py-4 rounded-2xl hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 transform hover:scale-[1.02] shadow-lg hover:shadow-xl flex items-center justify-center gap-2">
                        <i class="fas fa-exchange-alt text-xl"></i>
                        <span class="text-lg">تحويل</span>
                    </button>
                </form>
            </div>
        </div>
        <!-- Conversion Result -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-4 border-b border-green-200">
                <h3 class="text-xl font-bold text-green-900 flex items-center">
                    <i class="fas fa-chart-line ml-3 text-green-600"></i>
                    نتيجة التحويل
                </h3>
            </div>
            
            <div class="p-6">
                <div id="conversionResult" class="text-center" style="display: none;">
                    <div class="bg-gradient-to-br from-green-100 to-emerald-100 rounded-2xl p-8 mb-6">
                        <h4 class="text-2xl font-bold text-green-800 mb-4">النتيجة</h4>
                        <div class="text-5xl font-black text-green-600 mb-4" id="resultAmount">0.00</div>
                        <div class="text-green-700 font-medium" id="exchangeRate">1 USD = 0.00 EUR</div>
                        <div class="text-green-600 text-sm mt-2" id="lastUpdate">آخر تحديث: --</div>
                    </div>
                </div>
                
                <div id="placeholderResult" class="text-center py-12">
                    <div class="bg-gray-100 rounded-2xl p-8">
                        <i class="fas fa-chart-pie text-gray-400 text-6xl mb-4"></i>
                        <p class="text-gray-500 text-lg">أدخل المبلغ واختر العملات للحصول على نتيجة التحويل</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Exchange Rates Table -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-purple-50 to-pink-50 px-6 py-4 border-b border-purple-200">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-purple-900 flex items-center">
                    <i class="fas fa-table ml-3 text-purple-600"></i>
                    أسعار الصرف
                </h3>
                <button id="refreshRates" class="inline-flex items-center px-6 py-2 bg-purple-600 text-white rounded-xl text-sm font-bold hover:bg-purple-700 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-purple-500 transition-all duration-300 transform hover:scale-105 shadow-lg">
                    <i class="fas fa-sync-alt ml-2"></i>
                    تحديث
                </button>
            </div>
        </div>
        
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full" id="ratesTable">
                    <thead>
                        <tr class="bg-gradient-to-r from-purple-50 to-pink-50 border-b border-purple-200">
                            <th class="px-6 py-4 text-right text-sm font-bold text-purple-700">العملة</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-purple-700">السعر (USD)</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-purple-700">التغيير (24س)</th>
                            <th class="px-6 py-4 text-right text-sm font-bold text-purple-700">آخر تحديث</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <!-- Rates will be populated here -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Charts and Statistics -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Rate History Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-50 to-blue-50 px-6 py-4 border-b border-indigo-200">
                <h3 class="text-xl font-bold text-indigo-900 flex items-center">
                    <i class="fas fa-chart-area ml-3 text-indigo-600"></i>
                    سجل الأسعار
                </h3>
            </div>
            
            <div class="p-6">
                <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl p-8">
                    <canvas id="rateChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Statistics -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-orange-50 to-yellow-50 px-6 py-4 border-b border-orange-200">
                <h3 class="text-xl font-bold text-orange-900 flex items-center">
                    <i class="fas fa-chart-pie ml-3 text-orange-600"></i>
                    الإحصائيات
                </h3>
            </div>
            
            <div class="p-6">
                <div id="currencyStats" class="space-y-4">
                    <!-- Stats will be populated here -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Currency converter
    $('#currencyConverter').on('submit', function(e) {
        e.preventDefault();
        convertCurrency();
    });

    // Load exchange rates
    loadExchangeRates();

    // Refresh rates
    $('#refreshRates').on('click', loadExchangeRates);
});

function convertCurrency() {
    const amount = $('#amount').val();
    const fromCurrency = $('#fromCurrency').val();
    const toCurrency = $('#toCurrency').val();

    // Show loading state
    $('#resultAmount').text('...');
    $('#exchangeRate').text('جاري الحساب...');
    $('#lastUpdate').text('جاري التحميل...');
    $('#conversionResult').show();
    $('#placeholderResult').hide();

    $.ajax({
        url: '/api/currency/convert',
        method: 'POST',
        data: {
            amount: amount,
            from_currency: fromCurrency,
            to_currency: toCurrency,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $('#resultAmount').text(response.formatted_amount);
                $('#exchangeRate').text(`1 ${fromCurrency} = ${response.exchange_rate} ${toCurrency}`);
                $('#lastUpdate').text(`آخر تحديث: ${new Date(response.converted_at).toLocaleString('ar-SA')}`);
                
                // Add success animation
                $('#conversionResult .bg-gradient-to-br').addClass('animate-pulse');
                setTimeout(() => {
                    $('#conversionResult .bg-gradient-to-br').removeClass('animate-pulse');
                }, 1000);
            } else {
                // Show error state
                $('#resultAmount').text('خطأ');
                $('#exchangeRate').text('فشل التحويل: ' + response.message);
                $('#lastUpdate').text('--');
            }
        },
        error: function() {
            $('#resultAmount').text('خطأ');
            $('#exchangeRate').text('حدث خطأ في التحويل');
            $('#lastUpdate').text('--');
        }
    });
}

function loadExchangeRates() {
    $.ajax({
        url: '/api/currency/rates',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                populateRatesTable(response.rates);
                updateStatistics(response);
            }
        }
    });
}

function populateRatesTable(rates) {
    const tbody = $('#ratesTable tbody');
    tbody.empty();

    for (const [code, rate] of Object.entries(rates)) {
        const row = `
            <tr>
                <td>
                    <span class="flag-icon flag-icon-${code.toLowerCase()}"></span>
                    ${code}
                </td>
                <td>${rate}</td>
                <td><span class="text-success">+0.00%</span></td>
                <td>${new Date().toLocaleString()}</td>
            </tr>
        `;
        tbody.append(row);
    }
}

function updateStatistics(data) {
    const stats = `
        <div class="mb-3">
            <strong>Base Currency:</strong> ${data.base_currency}
        </div>
        <div class="mb-3">
            <strong>Total Currencies:</strong> ${Object.keys(data.rates).length}
        </div>
        <div class="mb-3">
            <strong>Last Update:</strong> ${new Date(data.updated_at).toLocaleString()}
        </div>
    `;
    $('#currencyStats').html(stats);
}
</script>
@endpush
