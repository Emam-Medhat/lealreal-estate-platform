@extends('layouts.app')

@section('title', $title ?? 'Report Under Construction')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <div class="mb-4">
                <i class="fas fa-hard-hat text-warning fa-4x"></i>
            </div>
            <h2 class="mb-3">{{ $title ?? 'Report Under Construction' }}</h2>
            <p class="lead text-muted mb-4">
                We are currently working on this report type. Please check back later.
            </p>
            <a href="{{ route('reports.financial.index') }}" class="btn btn-primary">
                <i class="fas fa-arrow-right ms-2"></i>
                Return to Financial Reports
            </a>
        </div>
    </div>
</div>
@endsection
