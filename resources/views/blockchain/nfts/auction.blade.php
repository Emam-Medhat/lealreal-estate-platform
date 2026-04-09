@extends('layouts.app')

@section('title', 'NFT Auction')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Auction for NFT: {{ $nft->name }}</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <img src="{{ $nft->image_url ?? asset('images/placeholder-nft.jpg') }}" class="img-fluid rounded" alt="{{ $nft->name }}">
                        </div>
                        <div class="col-md-6">
                            <h4>{{ $nft->name }}</h4>
                            <p>{{ $nft->description }}</p>
                            <hr>
                            <p><strong>Current Bid:</strong> {{ $nft->highest_bid ?? $nft->price }} {{ $nft->currency }}</p>
                            <p><strong>Auction Ends:</strong> {{ $nft->sale_end_time ?? 'N/A' }}</p>
                            
                            <form action="#" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label>Your Bid Amount</label>
                                    <input type="number" step="0.0001" class="form-control" placeholder="Enter amount">
                                </div>
                                <button type="button" class="btn btn-warning btn-lg btn-block mt-3" onclick="alert('Bidding functionality coming soon!')">
                                    <i class="fas fa-gavel"></i> Place Bid
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="{{ route('blockchain.nfts.index') }}" class="btn btn-secondary">Back to NFTs</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
