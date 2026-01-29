@extends('layouts.app')

@section('title', 'Agent ' . $agent->user->name . ' - Real Estate Pro')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <!-- Breadcrumbs -->
        <nav class="flex mb-8 text-sm text-gray-600" aria-label="Breadcrumb">
            <ol class="flex items-center space-x-2">
                <li><a href="/" class="hover:text-blue-600">Home</a></li>
                <li><i class="fas fa-chevron-right text-xs mx-2"></i></li>
                <li><a href="{{ route('agents.directory') }}" class="hover:text-blue-600">Agents</a></li>
                <li><i class="fas fa-chevron-right text-xs mx-2"></i></li>
                <li class="text-gray-900 font-medium">{{ $agent->user->name }}</li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Agent Info -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden sticky top-8">
                    <!-- Agent Header/Avatar -->
                    <div class="relative h-32 bg-blue-600">
                        @if($agent->profile && $agent->profile->company_logo)
                            <div class="absolute top-4 right-4 bg-white p-2 rounded-lg shadow-sm">
                                <img src="{{ Storage::url($agent->profile->company_logo) }}" alt="Company Logo" class="h-8 object-contain">
                            </div>
                        @endif
                    </div>
                    <div class="px-6 pb-6">
                        <div class="relative flex justify-center -mt-16 mb-4">
                            @if($agent->profile && $agent->profile->photo)
                                <img src="{{ Storage::url($agent->profile->photo) }}" alt="{{ $agent->user->name }}" class="w-32 h-32 rounded-full border-4 border-white shadow-md object-cover">
                            @else
                                <div class="w-32 h-32 rounded-full border-4 border-white shadow-md bg-gray-100 flex items-center justify-center">
                                    <i class="fas fa-user text-gray-400 text-4xl"></i>
                                </div>
                            @endif
                            @if($agent->is_verified)
                                <div class="absolute bottom-0 right-1/2 translate-x-12 bg-blue-500 text-white p-1.5 rounded-full border-2 border-white" title="Verified Agent">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <div class="text-center mb-6">
                            <h1 class="text-2xl font-bold text-gray-900">{{ $agent->user->name }}</h1>
                            <p class="text-blue-600 font-medium">{{ $agent->specialization ?? 'Real Estate Agent' }}</p>
                            @if($agent->company)
                                <p class="text-gray-600 text-sm mt-1">{{ $agent->company->name }}</p>
                            @endif
                        </div>

                        <!-- Stats Grid -->
                        <div class="grid grid-cols-2 gap-4 py-4 border-y border-gray-100 mb-6">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['total_properties'] }}</p>
                                <p class="text-xs text-gray-500 uppercase">Listings</p>
                            </div>
                            <div class="text-center border-l border-gray-100">
                                <p class="text-2xl font-bold text-gray-900">{{ $stats['experience_years'] }}</p>
                                <p class="text-xs text-gray-500 uppercase">Years Exp.</p>
                            </div>
                        </div>

                        <!-- Contact Info -->
                        <div class="space-y-4 mb-8">
                            @if($agent->profile && $agent->profile->phone)
                                <div class="flex items-center text-gray-600">
                                    <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-phone text-blue-600 text-sm"></i>
                                    </div>
                                    <span class="text-sm">{{ $agent->profile->phone }}</span>
                                </div>
                            @endif
                            @if($agent->profile && $agent->profile->email)
                                <div class="flex items-center text-gray-600">
                                    <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-envelope text-blue-600 text-sm"></i>
                                    </div>
                                    <span class="text-sm">{{ $agent->profile->email }}</span>
                                </div>
                            @endif
                            @if($agent->profile && $agent->profile->address)
                                <div class="flex items-center text-gray-600">
                                    <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-map-marker-alt text-blue-600 text-sm"></i>
                                    </div>
                                    <span class="text-sm">{{ $agent->profile->address }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Contact Form / CTA -->
                        @if($agent->profile && $agent->profile->phone)
                            <a href="tel:{{ $agent->profile->phone }}" class="w-full bg-blue-600 text-white py-3 rounded-xl font-semibold hover:bg-blue-700 transition-colors mb-3 block text-center">
                                <i class="fas fa-phone mr-2"></i> Contact Agent
                            </a>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $agent->profile->phone) }}?text=Hi%20{{ urlencode($agent->user->name) }}%2C%20I'm%20interested%20in%20your%20real%20estate%20services" 
                               target="_blank" 
                               class="w-full bg-green-500 text-white py-3 rounded-xl font-semibold hover:bg-green-600 transition-colors block text-center">
                                <i class="fab fa-whatsapp mr-2"></i> WhatsApp
                            </a>
                        @else
                            <button disabled class="w-full bg-gray-300 text-gray-500 py-3 rounded-xl font-semibold cursor-not-allowed mb-3">
                                <i class="fas fa-phone mr-2"></i> Contact Not Available
                            </button>
                            <button disabled class="w-full bg-gray-300 text-gray-500 py-3 rounded-xl font-semibold cursor-not-allowed">
                                <i class="fab fa-whatsapp mr-2"></i> WhatsApp Not Available
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Details & Listings -->
            <div class="lg:col-span-2">
                <!-- About Agent -->
                <div class="bg-white rounded-2xl shadow-sm p-8 mb-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">About {{ $agent->user->name }}</h2>
                    <div class="prose max-w-none text-gray-600">
                        @if($agent->profile && $agent->profile->bio)
                            {!! nl2br(e($agent->profile->bio)) !!}
                        @else
                            <p>Professional real estate agent dedicated to helping clients find their dream properties. Specializing in {{ $agent->specialization ?? 'residential and commercial real estate' }}.</p>
                        @endif
                    </div>
                    
                    @if($agent->profile && $agent->profile->languages)
                        <div class="mt-6">
                            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-3">Languages</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($agent->profile->languages as $lang)
                                    <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">{{ $lang }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Agent Listings -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Active Listings</h2>
                        <a href="{{ route('properties.index', ['agent' => $agent->id]) }}" class="text-blue-600 font-semibold hover:text-blue-700">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @forelse($agent->properties()->where('status', 'active')->limit(4)->get() as $property)
                            @include('properties.partials.card', ['property' => $property])
                        @empty
                            <div class="col-span-2 bg-white rounded-2xl p-12 text-center border-2 border-dashed border-gray-200">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-building text-gray-400 text-2xl"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900">No active listings</h3>
                                <p class="text-gray-500"></p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Reviews Section -->
                <div class="bg-white rounded-2xl shadow-sm p-8">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Client Reviews</h2>
                            <div class="flex items-center mt-1">
                                <div class="flex text-yellow-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= round($stats['average_rating']) ? '' : 'text-gray-200' }}"></i>
                                    @endfor
                                </div>
                                <span class="ml-2 text-sm font-medium text-gray-900">{{ number_format($stats['average_rating'], 1) }}</span>
                                <span class="mx-2 text-gray-300">•</span>
                                <span class="text-sm text-gray-500">{{ $stats['total_reviews'] }} reviews</span>
                            </div>
                        </div>
                        <button onclick="openReviewModal()" class="bg-gray-900 text-white px-6 py-2 rounded-xl font-semibold hover:bg-gray-800 transition-colors">
                            <i class="fas fa-star mr-2"></i> Write a Review
                        </button>
                    </div>

                    <div class="space-y-8">
                        @forelse($agent->reviews as $review)
                            <div class="border-b border-gray-100 last:border-0 pb-8 last:pb-0">
                                <div class="flex items-center mb-4">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold">
                                        {{ substr($review->user->name, 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="font-bold text-gray-900">{{ $review->user->name }}</h4>
                                        <div class="flex items-center">
                                            <div class="flex text-yellow-400 text-xs">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $review->rating ? '' : 'text-gray-200' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="ml-2 text-xs text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-gray-600 leading-relaxed">{{ $review->comment }}</p>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <p class="text-gray-500 italic">No reviews yet for this agent.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div id="reviewModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Write a Review</h3>
                <button onclick="closeReviewModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="reviewForm" onsubmit="submitReview(event)">
                @csrf
                <input type="hidden" name="agent_id" value="{{ $agent->id }}">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating *</label>
                    <div class="flex space-x-2" id="ratingStars">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" onclick="setRating({{ $i }})" class="text-2xl text-gray-300 hover:text-yellow-400 transition-colors">
                                <i class="fas fa-star" data-rating="{{ $i }}"></i>
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="ratingInput" required>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Your Review *</label>
                    <textarea name="review_text" rows="4" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Share your experience with this agent..."></textarea>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Your Name *</label>
                    <input type="text" name="reviewer_name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="John Doe">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Your Email *</label>
                    <input type="email" name="reviewer_email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="john@example.com">
                </div>
                
                <div class="flex space-x-3">
                    <button type="submit" class="flex-1 bg-blue-600 text-white py-2 rounded-lg font-semibold hover:bg-blue-700 transition-colors">
                        Submit Review
                    </button>
                    <button type="button" onclick="closeReviewModal()" class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let selectedRating = 0;

function openReviewModal() {
    document.getElementById('reviewModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closeReviewModal() {
    document.getElementById('reviewModal').classList.add('hidden');
    document.body.style.overflow = 'auto';
    resetReviewForm();
}

function setRating(rating) {
    selectedRating = rating;
    document.getElementById('ratingInput').value = rating;
    
    // Update star display
    const stars = document.querySelectorAll('#ratingStars i');
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('text-gray-300');
            star.classList.add('text-yellow-400');
        } else {
            star.classList.remove('text-yellow-400');
            star.classList.add('text-gray-300');
        }
    });
}

function resetReviewForm() {
    document.getElementById('reviewForm').reset();
    selectedRating = 0;
    const stars = document.querySelectorAll('#ratingStars i');
    stars.forEach(star => {
        star.classList.remove('text-yellow-400');
        star.classList.add('text-gray-300');
    });
}

function submitReview(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    
    fetch('/agent-reviews', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Review submitted successfully! It will be visible after approval.', 'success');
            closeReviewModal();
            // Optionally refresh the page after a delay
            setTimeout(() => location.reload(), 2000);
        } else {
            showToast(data.message || 'Error submitting review', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error submitting review', 'error');
    });
}

function showToast(message, type = 'info') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white font-medium z-50 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        'bg-blue-500'
    }`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    // Remove after 3 seconds
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
</script>

@endsection
