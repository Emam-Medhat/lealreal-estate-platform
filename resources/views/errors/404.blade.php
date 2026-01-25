@extends('layouts.app')

@section('title', 'Page Not Found')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6 text-center">
                <div class="error-page">
                    <h1 class="display-1 fw-bold text-primary">404</h1>
                    <h2 class="mb-4">Page Not Found</h2>
                    <p class="lead text-muted mb-4">
                        Sorry, the page you are looking for doesn't exist or has been moved.
                    </p>
                    <div class="d-flex gap-3 justify-content-center">
                        <a href="{{ route('home') }}" class="btn btn-primary">
                            <i class="fas fa-home me-2"></i>Go Home
                        </a>
                        <a href="{{ route('properties.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-building me-2"></i>Browse Properties
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
