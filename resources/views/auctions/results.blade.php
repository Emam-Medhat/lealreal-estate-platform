@extends('layouts.app')

@section('title', 'Auction Results')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Auction Results</h1>
        <p class="text-gray-600">View completed auction results and final prices</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="bg-blue-100 rounded-full p-3 mr-4">
                    <i class="fas fa-gavel text-blue-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Completed</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['total_completed'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="bg-green-100 rounded-full p-3 mr-4">
                    <i class="fas fa-dollar-sign text-green-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-800">${{ number_format($stats['total_revenue'], 0) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="bg-purple-100 rounded-full p-3 mr-4">
                    <i class="fas fa-chart-line text-purple-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Average Price</p>
                    <p class="text-2xl font-bold text-gray-800">${{ number_format($stats['average_price'], 0) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="bg-orange-100 rounded-full p-3 mr-4">
                    <i class="fas fa-calendar text-orange-600"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">This Month</p>
                    <p class="text-2xl font-bold text-gray-800">{{ $stats['this_month'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form method="GET" action="{{ route('messages.auctions.results') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Winner</label>
                    <select name="winner_id" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">All Winners</option>
                        {{-- @foreach ($users ?? [] as $user)
                            <option value="{{ $user->id }}" {{ request('winner_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach --}}
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Creator</label>
                    <select name="creator_id" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">All Creators</option>
                        {{-- @foreach ($creators ?? [] as $creator)
                            <option value="{{ $creator->id }}" {{ request('creator_id') == $creator->id ? 'selected' : '' }}>
                                {{ $creator->name }}
                            </option>
                        @endforeach --}}
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Min Price</label>
                    <input type="number" name="min_price" value="{{ request('min_price') }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Min Price">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Max Price</label>
                    <input type="number" name="max_price" value="{{ request('max_price') }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2" placeholder="Max Price">
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Apply Filters
                </button>
                <a href="{{ route('messages.auctions.results') }}" class="ml-2 bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Results List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($results->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Property
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Winner
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Final Price
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Completed
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($results as $result)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($result->auction && $result->auction->property && $result->auction->property->images->first())
                                            <img src="{{ $result->auction->property->images->first()->url }}" 
                                                 alt="{{ $result->auction->title }}" 
                                                 class="w-10 h-10 rounded-full object-cover mr-3">
                                        @else
                                            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-home text-gray-500"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $result->auction->title ?? 'N/A' }}</div>
                                            <div class="text-sm text-gray-500">{{ $result->auction->property->title ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        @if($result->winner)
                                            <div class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center mr-2">
                                                <span class="text-xs font-medium">{{ strtoupper(substr($result->winner->name, 0, 1)) }}</span>
                                            </div>
                                            <div class="text-sm text-gray-900">{{ $result->winner->name }}</div>
                                        @else
                                            <div class="text-sm text-gray-500">No winner</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-lg font-semibold text-green-600">
                                        ${{ number_format($result->final_price, 0) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $result->completed_at->format('M j, Y g:i A') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('messages.auctions.results.show', $result->id) }}" 
                                       class="text-blue-600 hover:text-blue-900 mr-3">
                                        View Details
                                    </a>
                                    @if($result->auction && $result->auction->created_by === Auth::id())
                                        <a href="{{ route('messages.auctions.results.show', $result->id) }}#manage" 
                                           class="text-green-600 hover:text-green-900">
                                            Manage
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    {{ $results->links() }}
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">{{ $results->firstItem() }}</span>
                            to
                            <span class="font-medium">{{ $results->lastItem() }}</span>
                            of
                            <span class="font-medium">{{ $results->total() }}</span>
                            results
                        </p>
                    </div>
                    <div>
                        {{ $results->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-gavel text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No auction results found</h3>
                <p class="text-gray-500 mb-4">There are no completed auctions to display.</p>
                <a href="{{ route('messages.auctions.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    View Active Auctions
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
