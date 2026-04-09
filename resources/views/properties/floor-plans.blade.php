@extends('layouts.app')

@section('title', 'Floor Plans')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Floor Plans</h1>
                    <p class="text-gray-600">{{ $property->title }} - Available Layouts</p>
                </div>
                <a href="{{ route('properties.show', $property) }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Property
                </a>
            </div>
        </div>

        <!-- Floor Plans Grid -->
        @forelse ($floorPlans as $plan)
            <div class="bg-white rounded-lg shadow-sm overflow-hidden mb-6">
                <!-- Plan Header -->
                <div class="p-6 border-b">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800 mb-2">{{ $plan->title }}</h2>
                            <p class="text-gray-600 mb-4">{{ $plan->description }}</p>
                            <div class="flex items-center space-x-6 text-sm text-gray-600">
                                <span><i class="fas fa-bed mr-2"></i>{{ $plan->bedrooms }} Bedrooms</span>
                                <span><i class="fas fa-bath mr-2"></i>{{ $plan->bathrooms }} Bathrooms</span>
                                <span><i class="fas fa-ruler-combined mr-2"></i>{{ $plan->square_feet }} sqft</span>
                                <span><i class="fas fa-dollar-sign mr-2"></i>${{ number_format($plan->price_per_sqft ?? 0, 0) }}/sqft</span>
                            </div>
                        </div>
                        <div class="text-right">
                            @if($plan->price)
                                <div class="text-2xl font-bold text-gray-800">${{ number_format($plan->price, 0) }}</div>
                                @if($plan->status === 'for_rent')
                                    <div class="text-sm text-gray-600">/month</div>
                                @endif
                            @endif
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                @if($plan->status === 'available')
                                    bg-green-100 text-green-800
                                @elseif($plan->status === 'occupied')
                                    bg-red-100 text-red-800
                                @else
                                    bg-yellow-100 text-yellow-800
                                @endif
                            ">
                                {{ ucfirst($plan->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Floor Plan Image -->
                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Plan Image -->
                        <div>
                            <div class="aspect-w-16 aspect-h-12 bg-gray-200 rounded-lg overflow-hidden">
                                @if($plan->image)
                                    <img src="{{ $plan->image }}" alt="{{ $plan->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                        <div class="text-center">
                                            <i class="fas fa-floor-plan text-gray-400 text-4xl mb-2"></i>
                                            <p class="text-gray-500">Floor Plan Image</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Image Controls -->
                            <div class="flex justify-center space-x-3 mt-4">
                                <button onclick="zoomIn({{ $plan->id }})" class="bg-gray-600 text-white px-3 py-2 rounded hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-search-plus"></i>
                                </button>
                                <button onclick="zoomOut({{ $plan->id }})" class="bg-gray-600 text-white px-3 py-2 rounded hover:bg-gray-700 transition-colors">
                                    <i class="fas fa-search-minus"></i>
                                </button>
                                <button onclick="downloadPlan({{ $plan->id }})" class="bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-download"></i>
                                </button>
                                <button onclick="printPlan({{ $plan->id }})" class="bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700 transition-colors">
                                    <i class="fas fa-print"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Room Details -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Room Dimensions</h3>
                            <div class="space-y-3">
                                @if($plan->rooms)
                                    @foreach ($plan->rooms as $room)
                                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <h4 class="font-medium text-gray-800">{{ $room['name'] }}</h4>
                                                <p class="text-sm text-gray-600">{{ $room['description'] ?? '' }}</p>
                                            </div>
                                            <div class="text-right">
                                                <div class="font-medium text-gray-800">{{ $room['dimensions'] ?? 'N/A' }}</div>
                                                <div class="text-sm text-gray-600">{{ $room['area'] ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-8">
                                        <i class="fas fa-info-circle text-gray-400 text-3xl mb-2"></i>
                                        <p class="text-gray-500">Room details not available</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Features -->
                            @if($plan->features)
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 mt-6">Features</h3>
                                <div class="grid grid-cols-2 gap-3">
                                    @foreach ($plan->features as $feature)
                                        <div class="flex items-center">
                                            <i class="fas fa-check text-green-500 mr-2"></i>
                                            <span class="text-sm text-gray-700">{{ $feature }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Actions -->
                            <div class="flex space-x-3 mt-6">
                                <button onclick="scheduleTour({{ $plan->id }})" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-calendar mr-2"></i>
                                    Schedule Tour
                                </button>
                                <button onclick="contactAgent({{ $plan->id }})" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                    <i class="fas fa-phone mr-2"></i>
                                    Contact Agent
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Details -->
                @if($plan->additional_info || $plan->amenities)
                    <div class="border-t p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($plan->additional_info)
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Additional Information</h3>
                                    <p class="text-gray-600">{{ $plan->additional_info }}</p>
                                </div>
                            @endif
                            
                            @if($plan->amenities)
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Amenities</h3>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($plan->amenities as $amenity)
                                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs">
                                                {{ $amenity }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <i class="fas fa-floor-plan text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Floor Plans Available</h3>
                <p class="text-gray-500 mb-6">This property doesn't have any floor plans uploaded yet.</p>
                <a href="{{ route('properties.show', $property) }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                    Back to Property
                </a>
            </div>
        @endforelse

        <!-- Compare Plans -->
        @if($floorPlans->count() > 1)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Compare Floor Plans</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Feature
                                </th>
                                @foreach ($floorPlans as $plan)
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ $plan->title }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Bedrooms
                                </td>
                                @foreach ($floorPlans as $plan)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $plan->bedrooms }}
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Bathrooms
                                </td>
                                @foreach ($floorPlans as $plan)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $plan->bathrooms }}
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Square Feet
                                </td>
                                @foreach ($floorPlans as $plan)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ number_format($plan->square_feet) }}
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Price
                                </td>
                                @foreach ($floorPlans as $plan)
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($plan->price)
                                            ${{ number_format($plan->price, 0) }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    Status
                                </td>
                                @foreach ($floorPlans as $plan)
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            @if($plan->status === 'available')
                                                bg-green-100 text-green-800
                                            @elseif($plan->status === 'occupied')
                                                bg-red-100 text-red-800
                                            @else
                                                bg-yellow-100 text-yellow-800
                                            @endif
                                        ">
                                            {{ ucfirst($plan->status) }}
                                        </span>
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function zoomIn(planId) {
    const img = document.querySelector('[data-plan-id="' + planId + '"] img');
    if (img) {
        img.style.transform = 'scale(1.5)';
        img.style.transition = 'transform 0.3s ease';
    }
}

function zoomOut(planId) {
    const img = document.querySelector('[data-plan-id="' + planId + '"] img');
    if (img) {
        img.style.transform = 'scale(1)';
    }
}

function downloadPlan(planId) {
    // Implement download functionality
    alert('Download feature coming soon!');
}

function printPlan(planId) {
    window.print();
}

function scheduleTour(planId) {
    window.location.href = '/properties/' + {{ $property->id }} + '/tour?plan=' + planId;
}

function contactAgent(planId) {
    window.location.href = '/contact?property=' + {{ $property->id }} + '&plan=' + planId;
}
</script>
@endsection
