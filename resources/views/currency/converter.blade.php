@extends('layouts.app')

@section('title', 'Currency Exchange')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Currency Converter</h3>
                </div>
                <div class="card-body">
                    <form id="currencyConverter">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="amount" class="form-label">Amount</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="amount" step="0.01" value="100" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">USD</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="fromCurrency" class="form-label">From</label>
                                <select class="form-select" id="fromCurrency" required>
                                    @foreach($currencies as $code => $currency)
                                        <option value="{{ $code }}" {{ $code == 'USD' ? 'selected' : '' }}>
                                            {{ $currency['symbol'] }} {{ $currency['name'] }} ({{ $code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="toCurrency" class="form-label">To</label>
                                <select class="form-select" id="toCurrency" required>
                                    @foreach($currencies as $code => $currency)
                                        <option value="{{ $code }}" {{ $code == 'EUR' ? 'selected' : '' }}>
                                            {{ $currency['symbol'] }} {{ $currency['name'] }} ({{ $code }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-exchange-alt"></i> Convert
                                </button>
                            </div>
                        </div>
                    </form>

                    <div class="text-center mt-4" id="conversionResult" style="display: none;">
                        <div class="alert alert-success">
                            <h4 class="mb-2">Conversion Result</h4>
                            <div class="display-4 text-primary" id="resultAmount">0.00</div>
                            <div class="text-muted" id="exchangeRate">1 USD = 0.00 EUR</div>
                            <div class="text-muted small" id="lastUpdate">Last updated: --</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exchange Rates Table -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title">Live Exchange Rates</h5>
                    <button class="btn btn-sm btn-outline-primary" onclick="refreshRates()">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Currency</th>
                                    <th>Rate (USD)</th>
                                    <th>Change (24h)</th>
                                    <th>Last Updated</th>
                                </tr>
                            </thead>
                            <tbody id="ratesTable">
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="fas fa-spinner fa-spin"></i> Loading rates...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Quick Convert -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Quick Convert</h5>
                </div>
                <div class="card-body">
                    <div class="quick-convert-buttons">
                        <button class="btn btn-outline-primary btn-block mb-2" onclick="quickConvert('USD', 'EUR')">
                            USD → EUR
                        </button>
                        <button class="btn btn-outline-primary btn-block mb-2" onclick="quickConvert('USD', 'GBP')">
                            USD → GBP
                        </button>
                        <button class="btn btn-outline-primary btn-block mb-2" onclick="quickConvert('EUR', 'USD')">
                            EUR → USD
                        </button>
                        <button class="btn btn-outline-primary btn-block mb-2" onclick="quickConvert('GBP', 'USD')">
                            GBP → USD
                        </button>
                    </div>
                </div>
            </div>

            <!-- Currency Calculator -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Calculator</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Amount</label>
                        <input type="number" class="form-control" id="calcAmount" placeholder="Enter amount">
                    </div>
                    <div class="form-group">
                        <label>From</label>
                        <select class="form-control" id="calcFrom">
                            @foreach($currencies as $code => $currency)
                                <option value="{{ $code }}">{{ $code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>To</label>
                        <select class="form-control" id="calcTo">
                            @foreach($currencies as $code => $currency)
                                <option value="{{ $code }}">{{ $code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-primary btn-block" onclick="calculateConversion()">
                        Calculate
                    </button>
                    <div class="mt-3" id="calcResult" style="display: none;">
                        <div class="alert alert-info">
                            <strong>Result:</strong> <span id="calcResultAmount">0.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Favorite Conversions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Favorite Conversions</h5>
                </div>
                <div class="card-body">
                    <div id="favoriteConversions">
                        <div class="text-muted text-center py-3">
                            <i class="fas fa-star"></i>
                            <p>No favorite conversions yet</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rate History Chart -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Rate History</h5>
                    <div class="card-tools">
                        <select class="form-control form-control-sm" id="historyFromCurrency">
                            @foreach($currencies as $code => $currency)
                                <option value="{{ $code }}">{{ $code }}</option>
                            @endforeach
                        </select>
                        <select class="form-control form-control-sm" id="historyToCurrency">
                            @foreach($currencies as $code => $currency)
                                <option value="{{ $code }}">{{ $code }}</option>
                            @endforeach
                        </select>
                        <select class="form-control form-control-sm" id="historyDays">
                            <option value="7">7 Days</option>
                            <option value="30" selected>30 Days</option>
                            <option value="90">90 Days</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="rateChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    loadExchangeRates();
    loadRateHistory();
    
    $('#currencyConverter').on('submit', function(e) {
        e.preventDefault();
        convertCurrency();
    });
    
    $('#historyFromCurrency, #historyToCurrency, #historyDays').on('change', function() {
        loadRateHistory();
    });
});

function convertCurrency() {
    const amount = $('#amount').val();
    const fromCurrency = $('#fromCurrency').val();
    const toCurrency = $('#toCurrency').val();

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
                $('#lastUpdate').text(`Last updated: ${new Date(response.converted_at).toLocaleString()}`);
                $('#conversionResult').show();
            } else {
                alert('Conversion failed: ' + response.message);
            }
        },
        error: function() {
            alert('Error converting currency');
        }
    });
}

function loadExchangeRates() {
    $.ajax({
        url: '/api/currency/rates',
        method: 'GET',
        success: function(response) {
            if (response.success) {
                updateRatesTable(response.rates);
            }
        },
        error: function() {
            $('#ratesTable').html(`
                <tr>
                    <td colspan="4" class="text-center text-danger py-4">
                        <i class="fas fa-exclamation-triangle"></i> Failed to load rates
                    </td>
                </tr>
            `);
        }
    });
}

function updateRatesTable(rates) {
    const tbody = $('#ratesTable');
    tbody.empty();

    Object.keys(rates).forEach(code => {
        const row = `
            <tr>
                <td>
                    <span class="flag-icon flag-icon-${code.toLowerCase()} mr-2"></span>
                    <strong>${code}</strong>
                </td>
                <td>${rates[code]}</td>
                <td><span class="text-success">+0.00%</span></td>
                <td>${new Date().toLocaleString()}</td>
            </tr>
        `;
        tbody.append(row);
    });
}

function refreshRates() {
    loadExchangeRates();
    loadRateHistory();
}

function quickConvert(from, to) {
    $('#fromCurrency').val(from);
    $('#toCurrency').val(to);
    convertCurrency();
}

function calculateConversion() {
    const amount = $('#calcAmount').val();
    const fromCurrency = $('#calcFrom').val();
    const toCurrency = $('#calcTo').val();

    if (!amount) {
        alert('Please enter an amount');
        return;
    }

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
                $('#calcResultAmount').text(response.formatted_amount);
                $('#calcResult').show();
            } else {
                alert('Calculation failed: ' + response.message);
            }
        },
        error: function() {
            alert('Error calculating conversion');
        }
    });
}

function loadRateHistory() {
    const fromCurrency = $('#historyFromCurrency').val();
    const toCurrency = $('#historyToCurrency').val();
    const days = $('#historyDays').val();

    $.ajax({
        url: '/api/currency/history',
        method: 'GET',
        data: {
            from_currency: fromCurrency,
            to_currency: toCurrency,
            days: days
        },
        success: function(response) {
            if (response.success) {
                updateRateChart(response.history);
            }
        },
        error: function() {
            console.error('Failed to load rate history');
        }
    });
}

function updateRateChart(history) {
    const ctx = document.getElementById('rateChart').getContext('2d');
    
    // Clear existing chart
    ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
    
    // Simple line chart implementation
    const labels = history.map(item => new Date(item.date).toLocaleDateString());
    const data = history.map(item => item.rate);
    
    const maxValue = Math.max(...data);
    const minValue = Math.min(...data);
    const range = maxValue - minValue;
    
    // Draw axes
    ctx.strokeStyle = '#ddd';
    ctx.lineWidth = 1;
    ctx.beginPath();
    ctx.moveTo(50, 20);
    ctx.lineTo(ctx.canvas.width - 20, 20);
    ctx.moveTo(50, 20);
    ctx.lineTo(50, ctx.canvas.height - 20);
    ctx.stroke();
    
    // Draw data line
    ctx.strokeStyle = '#007bff';
    ctx.lineWidth = 2;
    ctx.beginPath();
    
    history.forEach((item, index) => {
        const x = 50 + (index / (history.length - 1)) * (ctx.canvas.width - 70);
        const y = ctx.canvas.height - 20 - ((item.rate - minValue) / range) * (ctx.canvas.height - 40);
        
        if (index === 0) {
            ctx.moveTo(x, y);
        } else {
            ctx.lineTo(x, y);
        }
    });
    
    ctx.stroke();
    
    // Draw points
    history.forEach((item, index) => {
        const x = 50 + (index / (history.length - 1)) * (ctx.canvas.width - 70);
        const y = ctx.canvas.height - 20 - ((item.rate - minValue) / range) * (ctx.canvas.height - 40));
        
        ctx.fillStyle = '#007bff';
        ctx.beginPath();
        ctx.arc(x, y, 4, 0, 2 * Math.PI);
        ctx.fill();
    });
    
    // Draw labels
    ctx.fillStyle = '#666';
    ctx.font = '12px Arial';
    ctx.textAlign = 'center';
    
    labels.forEach((label, index) => {
        const x = 50 + (index / (labels.length - 1)) * (ctx.canvas.width - 70);
        ctx.fillText(label, x, ctx.canvas.height - 5);
    });
}
</script>
@endpush
