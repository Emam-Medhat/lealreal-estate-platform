@extends('admin.layouts.admin')

@section('title', 'صناديق الاستثمار')
@section('page-title', 'صناديق الاستثمار')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-800 mb-8">صناديق الاستثمار</h1>
            
            @if($funds->isNotEmpty())
                <p class="text-green-600">Found {{ $funds->count() }} funds</p>
                @foreach($funds as $fund)
                    <div class="bg-white p-4 mb-4 rounded-lg">
                        <h3>{{ $fund->name }}</h3>
                        <p>Min Investment: ${{ number_format((float)$fund->min_investment) }}</p>
                        <p>Expected Return: {{ number_format((float)$fund->expected_return, 2) }}%</p>
                    </div>
                @endforeach
            @else
                <p class="text-red-600">No funds found</p>
            @endif
        </div>
    </div>
@endsection
