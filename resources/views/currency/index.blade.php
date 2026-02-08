@extends('layouts.app')

@section('title', 'Currency Exchange')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Currency Exchange</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <form id="currencyConverter">
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Amount</label>
                                    <input type="number" class="form-control" id="amount" step="0.01" value="100" required>
                                </div>
                                <div class="mb-3">
                                    <label for="fromCurrency" class="form-label">From</label>
                                    <select class="form-select" id="fromCurrency" required>
                                        @foreach($currencies as $code => $currency)
                                            <option value="{{ $code }}" {{ $code == 'USD' ? 'selected' : '' }}>
                                                {{ $currency['symbol'] }} {{ $currency['name'] }} ({{ $code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="toCurrency" class="form-label">To</label>
                                    <select class="form-select" id="toCurrency" required>
                                        @foreach($currencies as $code => $currency)
                                            <option value="{{ $code }}" {{ $code == 'EUR' ? 'selected' : '' }}>
                                                {{ $currency['symbol'] }} {{ $currency['name'] }} ({{ $code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-exchange-alt"></i> Convert
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <div id="conversionResult" class="text-center p-4" style="display: none;">
                                <h4>Result</h4>
                                <div class="display-4 text-primary" id="resultAmount">0.00</div>
                                <div class="text-muted" id="exchangeRate">1 USD = 0.00 EUR</div>
                                <div class="text-muted small" id="lastUpdate">Last updated: --</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Exchange Rates</h3>
                    <button class="btn btn-sm btn-outline-primary float-end" id="refreshRates">
                        <i class="fas fa-sync-alt"></i> Refresh
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="ratesTable">
                            <thead>
                                <tr>
                                    <th>Currency</th>
                                    <th>Rate (USD)</th>
                                    <th>Change (24h)</th>
                                    <th>Last Updated</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Rates will be populated here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Rate History</h3>
                </div>
                <div class="card-body">
                    <canvas id="rateChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Statistics</h3>
                </div>
                <div class="card-body">
                    <div id="currencyStats">
                        <!-- Stats will be populated here -->
                    </div>
                </div>
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
