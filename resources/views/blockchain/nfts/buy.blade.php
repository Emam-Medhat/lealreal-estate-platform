@extends('layouts.app')

@section('title', 'Buy NFT')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Buy NFT: {{ $nft->name }}</h3>
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
                            <p><strong>Price:</strong> {{ $nft->price }} {{ $nft->currency }}</p>
                            <p><strong>Owner:</strong> {{ $nft->owner_address }}</p>
                            
                            <form action="#" method="POST">
                                @csrf
                                <button type="button" class="btn btn-success btn-lg btn-block" onclick="alert('Purchase functionality coming soon!')">
                                    <i class="fas fa-shopping-cart"></i> Buy Now
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
