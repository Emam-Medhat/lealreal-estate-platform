@extends('layouts.app')

@section('title', 'Currency Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $currency->name }} Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('currency.edit', $currency->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('currency.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Code</th>
                                    <td><code>{{ $currency->code }}</code></td>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td>{{ $currency->name }}</td>
                                </tr>
                                <tr>
                                    <th>Native Name</th>
                                    <td>{{ $currency->native_name }}</td>
                                </tr>
                                <tr>
                                    <th>Symbol</th>
                                    <td>{{ $currency->symbol }}</td>
                                </tr>
                                <tr>
                                    <th>Precision</th>
                                    <td>{{ $currency->precision }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Status</th>
                                    <td>
                                        <span class="badge badge-{{ $currency->is_active ? 'success' : 'secondary' }}">
                                            {{ $currency->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Default</th>
                                    <td>
                                        <span class="badge badge-{{ $currency->is_default ? 'primary' : 'secondary' }}">
                                            {{ $currency->is_default ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Provider</th>
                                    <td>{{ $currency->exchange_rate_provider }}</td>
                                </tr>
                                <tr>
                                    <th>Last Rate Update</th>
                                    <td>
                                        {{ $currency->last_rate_update ? $currency->last_rate_update->format('M j, Y H:i') : 'Never' }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($currency->metadata)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Metadata</h5>
                            <pre class="bg-light p-3 rounded"><code>{{ json_encode($currency->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                        </div>
                    </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="btn-group">
                                <a href="{{ route('currency.edit', $currency->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('currency.destroy', $currency->id) }}" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this currency?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                                <a href="{{ route('currency.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Exchange Rate History -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Exchange Rate History</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="targetCurrency">Target Currency</label>
                        <select class="form-control" id="targetCurrency" onchange="loadRateHistory()">
                            @foreach($currencies as $code => $currency)
                                @if($code !== $currency->code)
                                    <option value="{{ $code }}">{{ $code }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div id="rateHistoryChart" style="height: 300px;">
                        <canvas id="historyChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4>{{ $currency->rates()->count() }}</h4>
                            <small>Total Rates</small>
                        </div>
                        <div class="col-6">
                            <h4>{{ $currency->transactions()->count() }}</h4>
                            <small>Transactions</small>
                        </div>
                    </div>
                    <div class="row text-center mt-3">
                        <div class="col-6">
                            <h4>{{ $currency->users()->count() }}</h4>
                            <small>Users</small>
                        </div>
                        <div class="col-6">
                            <h4>{{ $currency->incomingRates()->count() }}</h4>
                            <small>Incoming Rates</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="updateRates()">
                            <i class="fas fa-sync-alt"></i> Update Rates
                        </button>
                        <button class="btn btn-outline-info btn-sm" onclick="exportData()">
                            <i class="fas fa-download"></i> Export Data
                        </button>
                        <button class="btn btn-outline-success btn-sm" onclick="testConversion()">
                            <i class="fas fa-exchange-alt"></i> Test Conversion
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function loadRateHistory() {
    const targetCurrency = document.getElementById('targetCurrency').value;
    const canvas = document.getElementById('historyChart');
    const ctx = canvas.getContext('2d');
    
    // Clear canvas
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    
    // Mock data for demonstration
    const days = 30;
    const data = [];
    const labels = [];
    
    for (let i = days - 1; i >= 0; i--) {
        const date = new Date();
        date.setDate(date.getDate() - i);
        labels.push(date.toLocaleDateString());
        // Mock rate data
        data.push(1 + Math.random() * 0.2 - 0.1);
    }
    
    // Simple line chart
    const maxValue = Math.max(...data);
    const minValue = Math.min(...data);
    const range = maxValue - minValue;
    
    // Draw axes
    ctx.strokeStyle = '#ddd';
    ctx.lineWidth = 1;
    ctx.beginPath();
    ctx.moveTo(50, 20);
    ctx.lineTo(canvas.width - 20, 20);
    ctx.moveTo(50, 20);
    ctx.lineTo(50, canvas.height - 20);
    ctx.stroke();
    
    // Draw data line
    ctx.strokeStyle = '#007bff';
    ctx.lineWidth = 2;
    ctx.beginPath();
    
    data.forEach((value, index) => {
        const x = 50 + (index / (data.length - 1)) * (canvas.width - 70);
        const y = canvas.height - 20 - ((value - minValue) / range) * (canvas.height - 40);
        
        if (index === 0) {
            ctx.moveTo(x, y);
        } else {
            ctx.lineTo(x, y);
        }
    });
    
    ctx.stroke();
    
    // Draw points
    data.forEach((value, index) => {
        const x = 50 + (index / (data.length - 1)) * (canvas.width - 70);
        const y = canvas.height - 20 - ((value - minValue) / range) * (canvas.height - 40);
        
        ctx.fillStyle = '#007bff';
        ctx.beginPath();
        ctx.arc(x, y, 3, 0, 2 * Math.PI);
        ctx.fill();
    });
}

function updateRates() {
    fetch('/api/currency/update-rates', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Rates updated successfully!');
            location.reload();
        } else {
            alert('Failed to update rates: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error updating rates:', error);
        alert('Error updating rates');
    });
}

function exportData() {
    const data = {
        currency: '{{ $currency->code }}',
        name: '{{ $currency->name }}',
        rates: {{ $currency->rates()->get()->map(function($rate) {
            return {
                to_currency: $rate->to_currency,
                rate: $rate->rate,
                date: $rate->date
            };
        })->toJson() }},
        statistics: {
            total_rates: {{ $currency->rates()->count() }},
        transactions: {{ $currency->transactions()->count() }},
        users: {{ $currency->users()->count() }}
    };
    
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `currency_{{ $currency->code }}_data.json`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

function testConversion() {
    const amount = 100;
    const fromCurrency = '{{ $currency->code }}';
    const toCurrency = document.getElementById('targetCurrency').value;
    
    fetch('/api/currency/convert', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            amount: amount,
            from_currency: fromCurrency,
            to_currency: toCurrency
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`${amount} ${fromCurrency} = ${data.formatted_amount} ${toCurrency}`);
        } else {
            alert('Conversion failed: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error testing conversion:', error);
        alert('Error testing conversion');
    });
}

// Load rate history on page load
document.addEventListener('DOMContentLoaded', function() {
    loadRateHistory();
});
</script>
@endpush
