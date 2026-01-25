@extends('layouts.app')

@section('title', 'Server Error')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <div class="error-page">
                    <h1 class="display-1 fw-bold text-danger">500</h1>
                    <h2 class="mb-4">Server Error</h2>
                    <p class="lead text-muted mb-4">
                        Something went wrong on our end. Our team has been notified and is working to fix this issue.
                    </p>
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>Go Home
                        </a>
                        <button onclick="history.back()" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Go Back
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
