@extends('layouts.app')

@section('title', 'Create Smart Contract')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Deploy Smart Contract</h3>
                    <div class="card-tools">
                        <a href="{{ route('blockchain.contracts') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('blockchain.contracts.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Contract Name</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           placeholder="Property Ownership Contract" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type" class="form-label">Contract Type</label>
                                    <select class="form-control" id="type" name="type" required>
                                        <option value="property_ownership">Property Ownership</option>
                                        <option value="rental_agreement">Rental Agreement</option>
                                        <option value="escrow_service">Escrow Service</option>
                                        <property_tokenization">Property Tokenization</option>
                                        <option value="dao_governance">DAO Governance</option>
                                        <option value="marketplace">Marketplace</option>
                                        <auction">Auction</option>
                                        <option value="staking">Staking</option>
                                        <option value="governance">Governance</option>
                                        <option value="custom">Custom</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="network" class="form-label">Network</label>
                                    <select class="form-control" id="network" name="network" required>
                                        <option value="ethereum">Ethereum Mainnet</option>
                                        <option value="polygon">Polygon Mainnet</option>
                                        <option value="bsc">Binance Smart Chain</option>
                                        <option value="arbitrum">Arbitrum One</option>
                                        <option value="optimism">Optimism</option>
                                        <option value="goerli">Goerli Testnet</option>
                                        <option value="mumbai">Mumbai Testnet</option>
                                        <option value="bsc_testnet">BSC Testnet</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="deployed_by" class="form-label">Deployed By</label>
                                    <select class="form-control" id="deployed_by" name="deployed_by" required>
                                        <option value="{{ auth()->id() }}">{{ auth()->user()->name }}</option>
                                        @foreach($users as $user)
                                            @if($user->id !== auth()->id())
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="abi" class="form-label">Contract ABI</label>
                                    <textarea class="form-control" id="abi" name="abi" rows="8" 
                                              placeholder='[{"type": "function", "name": "transfer", "inputs": [{"name": "to", "type": "address"}, {"name": "amount", "type": "uint256"}], "outputs": [{"name": "success", "type": "bool"}]}' required></textarea>
                                    <small class="text-muted">Contract ABI in JSON format</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bytecode" class="form-label">Bytecode (Optional)</label>
                                    <textarea class="form-control" id="bytecode" name="bytecode" rows="4" 
                                              placeholder="0x60806040238260091723301500"></textarea>
                                    <small class="text-muted">Contract bytecode</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="metadata" class="form-label">Metadata (Optional)</label>
                                    <textarea class="form-control" id="metadata" name="metadata" rows="4" 
                                              placeholder='{"gas_limit": 1000000, "verification_status": "pending", "deployment_cost": 0.5}'></textarea>
                                    <small class="text-muted">Additional metadata in JSON format</small>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-rocket"></i> Deploy Contract
                            </button>
                            <a href="{{ route('blockchain.contracts') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
