@extends('layouts.app')

@section('title', 'Blockchain Transactions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Transaction History</h3>
                    <div class="card-tools">
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary active" data-period="all">All</button>
                            <button class="btn btn-sm btn-outline-primary" data-period="day">24h</button>
                            <button class="btn btn-sm btn-outline-primary" data-period="week">Week</button>
                            <button class="btn btn-sm btn-outline-primary" data-period="month">Month</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>TX Hash</th>
                                    <th>Type</th>
                                    <th>Contract</th>
                                    <th>Function</th>
                                    <th>Network</th>
                                    <th>Gas Used</th>
                                    <th>Cost</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="transactionsTable">
                                <tr>
                                    <td colspan="10" class="text-center text-muted py-4">
                                        <i class="fas fa-spinner fa-spin"></i> Loading transactions...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Statistics -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 id="totalTransactions">0</h4>
                            <p>Total Transactions</p>
                        </div>
                        <div>
                            <i class="fas fa-exchange-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 id="successfulTransactions">0</h4>
                            <p>Successful</p>
                        </div>
                        <div>
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 id="pendingTransactions">0</h4>
                            <p>Pending</p>
                        </div>
                        <div>
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 id="failedTransactions">0</h4>
                            <p>Failed</p>
                        </div>
                        <div>
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Network Distribution -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Network Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="networkChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Gas Usage Trend</h5>
                </div>
                <div class="card-body">
                    <canvas id="gasChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    loadTransactions('all');
    
    $('[data-period]').on('click', function() {
        $('[data-period]').removeClass('active');
        $(this).addClass('active');
        loadTransactions($(this).data('period'));
    });
});

function loadTransactions(period) {
    $.ajax({
        url: '/api/blockchain/transactions',
        method: 'GET',
        data: { period: period },
        success: function(response) {
            if (response.success) {
                updateTransactionsTable(response.transactions);
                updateStatistics(response.statistics);
                updateCharts(response.charts);
            }
        },
        error: function() {
            $('#transactionsTable').html(`
                <tr>
                    <td colspan="10" class="text-center text-danger py-4">
                        <i class="fas fa-exclamation-triangle"></i> Failed to load transactions
                    </td>
                </tr>
            `);
        }
    });
}

function updateTransactionsTable(transactions) {
    const tbody = $('#transactionsTable');
    tbody.empty();

    if (transactions.length === 0) {
        tbody.html(`
            <tr>
                <td colspan="10" class="text-center text-muted py-4">
                    <i class="fas fa-info-circle"></i> No transactions found
                </td>
            </tr>
        `);
        return;
    }

    transactions.forEach(tx => {
        const row = `
            <tr>
                <td>
                    <code class="text-truncate d-block" style="max-width: 100px;">
                        ${tx.tx_hash.substring(0, 10)}...
                    </code>
                    <button class="btn btn-sm btn-outline-secondary copy-hash" data-hash="${tx.tx_hash}">
                        <i class="fas fa-copy"></i>
                    </button>
                </td>
                <td>
                    <span class="badge badge-info">${tx.type}</span>
                </td>
                <td>
                    <code class="text-truncate d-block" style="max-width: 80px;">
                        ${tx.contract_address.substring(0, 8)}...
                    </code>
                </td>
                <td>${tx.function}</td>
                <td>
                    <span class="flag-icon flag-icon-${tx.network}"></span>
                    ${tx.network}
                </td>
                <td>${number_format(tx.gas_used)}</td>
                <td>${tx.cost} ETH</td>
                <td>
                    <span class="badge badge-${getStatusClass(tx.status)}">
                        ${tx.status}
                    </span>
                </td>
                <td>${new Date(tx.created_at).toLocaleString()}</td>
                <td>
                    <a href="${getExplorerUrl(tx.network, tx.tx_hash)}" target="_blank" 
                       class="btn btn-sm btn-outline-info">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

function getStatusClass(status) {
    switch(status) {
        case 'confirmed': return 'success';
        case 'pending': return 'warning';
        case 'failed': return 'danger';
        default: return 'secondary';
    }
}

function getExplorerUrl(network, txHash) {
    const explorers = {
        ethereum: 'https://etherscan.io',
        polygon: 'https://polygonscan.com',
        bsc: 'https://bscscan.com',
        arbitrum: 'https://arbiscan.io'
    };
    return `${explorers[network] || 'https://etherscan.io'}/tx/${txHash}`;
}

function updateStatistics(stats) {
    $('#totalTransactions').text(stats.total || 0);
    $('#successfulTransactions').text(stats.successful || 0);
    $('#pendingTransactions').text(stats.pending || 0);
    $('#failedTransactions').text(stats.failed || 0);
}

function updateCharts(charts) {
    // Update network distribution chart
    if (charts.network) {
        updateNetworkChart(charts.network);
    }
    
    // Update gas usage chart
    if (charts.gas) {
        updateGasChart(charts.gas);
    }
}

function updateNetworkChart(data) {
    // Implement chart update logic
}

function updateGasChart(data) {
    // Implement chart update logic
}

// Copy hash functionality
$(document).on('click', '.copy-hash', function() {
    const hash = $(this).data('hash');
    navigator.clipboard.writeText(hash);
    
    const btn = $(this);
    const originalHtml = btn.html();
    btn.html('<i class="fas fa-check"></i>');
    setTimeout(() => {
        btn.html(originalHtml);
    }, 2000);
});
</script>
@endpush
