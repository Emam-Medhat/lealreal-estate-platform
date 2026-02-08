@extends('layouts.app')

@section('title', 'Mint NFT')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Mint Property NFT</h3>
                    <div class="card-tools">
                        <a href="{{ route('blockchain.nfts.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('blockchain.nfts.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="property_id" class="property">Select Property</label>
                                    <select class="form-control" id="property_id" name="property_id" required>
                                        <option value="">Choose a property</option>
                                        @foreach($properties as $property)
                                            <option value="{{ $property->id }}">{{ $property->title }} - {{ $property->location }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="standard" class="form-label">NFT Standard</label>
                                    <select class="form-control" id="standard" name="standard" required>
                                        <option value="ERC721">ERC-721 (Non-Fungible)</option>
                                        <option value="ERC1155">ERC-1155 (Multi-Token)</option>
                                        <option value="ERC998">ERC-998 (Composable)</option>
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
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="owner_address" class="form-label">Owner Address</label>
                                    <input type="text" class="form-control" id="owner_address" name="owner_address" 
                                           placeholder="0x742d35Cc663c4AaA8c8e4f2d1a2" required>
                                    <small class="text-muted">Ethereum address of the NFT owner</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="created_by" class="form-label">Created By</label>
                                    <select class="form-control" id="created_by" name="created_by" required>
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
                                    <label for="metadata" class="form-label">Metadata (Optional)</label>
                                    <textarea class="form-control" id="metadata" name="metadata" rows="4" 
                                              placeholder='{"image": "https://example.com/property-image.jpg", "attributes": [{"trait_type": "bedrooms", "value": 3}, {"trait_type": "area", "value": 1500}]}'></textarea>
                                    <small class="text-muted">NFT metadata in JSON format</small>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-image"></i> Mint NFT
                            </button>
                            <a href="{{ route('blockchain.nfts.index') }}" class="btn btn-secondary">
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
