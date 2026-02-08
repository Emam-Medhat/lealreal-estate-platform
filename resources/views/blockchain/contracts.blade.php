@extends('layouts.app')

@section('title', 'Smart Contracts')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Smart Contracts</h3>
                    <div class="card-tools">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#deployContractModal">
                            <i class="fas fa-plus"></i> Deploy Contract
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Network</th>
                                    <th>Address</th>
                                    <th>Status</th>
                                    <th>Gas Used</th>
                                    <th>Deployed At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($contracts) && $contracts->count() > 0)
                                    @foreach($contracts as $contract)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-file-contract mr-2 text-primary"></i>
                                                    <div>
                                                        <strong>{{ $contract->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ Str::limit($contract->type, 30) }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $contract->type }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="flag-icon flag-icon-{{ strtolower($contract->network) }} mr-2"></span>
                                                    {{ ucfirst($contract->network) }}
                                                </div>
                                            </td>
                                            <td>
                                                <code class="text-truncate d-block" style="max-width: 120px;">
                                                    {{ $contract->address }}
                                                </code>
                                                <button class="btn btn-sm btn-outline-secondary copy-address" data-address="{{ $contract->address }}">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $contract->status == 'deployed' ? 'success' : ($contract->status == 'pending' ? 'warning' : 'danger') }}">
                                                    {{ $contract->status }}
                                                </span>
                                            </td>
                                            <td>{{ number_format($contract->gas_used ?? 0) }}</td>
                                            <td>{{ $contract->deployed_at ? $contract->deployed_at->format('M j, Y') : 'N/A' }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary view-contract" data-address="{{ $contract->address }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-success execute-function" data-address="{{ $contract->address }}">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                    <a href="{{ $contract->getExplorerUrl() }}" target="_blank" class="btn btn-outline-info">
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-file-contract fa-3x mb-3"></i>
                                            <p>No contracts deployed yet</p>
                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#deployContractModal">
                                                Deploy Your First Contract
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contract Statistics -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $contracts->count() }}</h4>
                            <p>Total Contracts</p>
                        </div>
                        <div>
                            <i class="fas fa-file-contract fa-2x"></i>
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
                            <h4>{{ $contracts->where('status', 'deployed')->count() }}</h4>
                            <p>Deployed</p>
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
                            <h4>{{ number_format($contracts->sum('gas_used')) }}</h4>
                            <p>Total Gas Used</p>
                        </div>
                        <div>
                            <i class="fas fa-fire fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $contracts->sum('deployment_cost') }}</h4>
                            <p>Total Cost (ETH)</p>
                        </div>
                        <div>
                            <i class="fas fa-coins fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Deploy Contract Modal -->
<div class="modal fade" id="deployContractModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Deploy Smart Contract</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="deployContractForm">
                    <div class="mb-3">
                        <label for="contractName" class="form-label">Contract Name</label>
                        <input type="text" class="form-control" id="contractName" required>
                    </div>
                    <div class="mb-3">
                        <label for="contractType" class="form-label">Contract Type</label>
                        <select class="form-select" id="contractType" required>
                            <option value="">Select Type</option>
                            <option value="property_ownership">Property Ownership</option>
                            <option value="rental_agreement">Rental Agreement</option>
                            <option value="escrow_service">Escrow Service</option>
                            <option value="property_tokenization">Property Tokenization</option>
                            <option value="dao_governance">DAO Governance</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="contractNetwork" class="form-label">Network</label>
                        <select class="form-select" id="contractNetwork" required>
                            <option value="">Select Network</option>
                            <option value="ethereum">Ethereum Mainnet</option>
                            <option value="polygon">Polygon Mainnet</option>
                            <option value="bsc">Binance Smart Chain</option>
                            <option value="arbitrum">Arbitrum One</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="deployContract()">Deploy Contract</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.copy-address').on('click', function() {
        const address = $(this).data('address');
        navigator.clipboard.writeText(address);
        
        const btn = $(this);
        const originalHtml = btn.html();
        btn.html('<i class="fas fa-check"></i>');
        setTimeout(() => {
            btn.html(originalHtml);
        }, 2000);
    });
    
    $('.view-contract').on('click', function() {
        const address = $(this).data('address');
        window.open(`/blockchain/contracts/${address}`, '_blank');
    });
});

function deployContract() {
    const formData = {
        name: $('#contractName').val(),
        type: $('#contractType').val(),
        network: $('#contractNetwork').val(),
        deployed_by: {{ auth()->id() }}
    };

    $.ajax({
        url: '/api/blockchain/deploy-contract',
        method: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                alert('Contract deployed successfully!');
                $('#deployContractModal').modal('hide');
                location.reload();
            } else {
                alert('Deployment failed: ' + response.message);
            }
        },
        error: function() {
            alert('Error deploying contract');
        }
    });
}
</script>
@endpush
