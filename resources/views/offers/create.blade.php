@extends('layouts.app')

@section('title', 'Make an Offer - ' . $property->title)

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center mb-4">
            <a href="{{ route('properties.show', $property->id) }}" class="text-blue-600 hover:text-blue-700 mr-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Make an Offer</h1>
        </div>
        
        <!-- Property Summary -->
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-start">
                @if ($property->images->first())
                    <img src="{{ $property->images->first()->url }}" 
                         alt="{{ $property->title }}" 
                         class="w-20 h-20 object-cover rounded-lg mr-4">
                @else
                    <div class="w-20 h-20 bg-gray-200 rounded-lg mr-4 flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                @endif
                
                <div class="flex-1">
                    <h3 class="font-semibold text-lg">{{ $property->title }}</h3>
                    <p class="text-gray-600 text-sm">{{ $property->location }}</p>
                    <div class="flex items-center mt-2 space-x-4 text-sm text-gray-500">
                        <span>{{ $property->type }}</span>
                        <span>•</span>
                        <span>{{ $property->area }} sqft</span>
                        <span>•</span>
                        <span class="font-semibold text-gray-900">${{ number_format($property->price, 0) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Offer Form -->
    <form action="{{ route('offers.store') }}" method="POST" class="space-y-8">
        @csrf
        <input type="hidden" name="property_id" value="{{ $property->id }}">
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Offer Details -->
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-6">Offer Details</h2>
                    
                    <!-- Offer Amount -->
                    <div class="mb-6">
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Offer Amount *
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-lg">$</span>
                            <input type="number" 
                                   id="amount" 
                                   name="amount" 
                                   min="1" 
                                   step="1000"
                                   value="{{ round($property->price * 0.9, -3) }}"
                                   class="w-full pl-10 pr-3 py-3 text-lg border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   required>
                        </div>
                        <div class="mt-2 flex justify-between text-sm text-gray-500">
                            <span>Listing Price: ${{ number_format($property->price, 0) }}</span>
                            <span>{{ round((round($property->price * 0.9, -3) / $property->price) * 100) }}% of asking</span>
                        </div>
                    </div>
                    
                    <!-- Financing Type -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Financing Type *
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="financing_type" value="cash" checked class="mr-3">
                                <span>Cash Offer</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="financing_type" value="mortgage" class="mr-3">
                                <span>Mortgage Financing</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="financing_type" value="owner_financing" class="mr-3">
                                <span>Owner Financing</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Proposed Closing Date -->
                    <div class="mb-6">
                        <label for="proposed_closing_date" class="block text-sm font-medium text-gray-700 mb-2">
                            Proposed Closing Date *
                        </label>
                        <input type="date" 
                               id="proposed_closing_date" 
                               name="proposed_closing_date" 
                               min="{{ now()->addDays(30)->format('Y-m-d') }}"
                               max="{{ now()->addDays(180)->format('Y-m-d') }}"
                               value="{{ now()->addDays(60)->format('Y-m-d') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                               required>
                        <p class="text-xs text-gray-500 mt-1">Typical closing timeline: 30-90 days</p>
                    </div>
                    
                    <!-- Earnest Money -->
                    <div class="mb-6">
                        <label for="earnest_money_amount" class="block text-sm font-medium text-gray-700 mb-2">
                            Earnest Money Deposit
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 text-lg">$</span>
                            <input type="number" 
                                   id="earnest_money_amount" 
                                   name="earnest_money_amount" 
                                   min="0" 
                                   step="100"
                                   value="{{ round($property->price * 0.01, -2) }}"
                                   class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Typically 1-3% of offer amount</p>
                    </div>
                    
                    <!-- Inspection Period -->
                    <div class="mb-6">
                        <label for="inspection_period_days" class="block text-sm font-medium text-gray-700 mb-2">
                            Inspection Period (Days)
                        </label>
                        <select id="inspection_period_days" 
                                name="inspection_period_days" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="0">No Inspection</option>
                            <option value="5">5 Days</option>
                            <option value="7" selected>7 Days</option>
                            <option value="10">10 Days</option>
                            <option value="14">14 Days</option>
                            <option value="21">21 Days</option>
                        </select>
                    </div>
                </div>
                
                <!-- Offer Message -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Personal Message</h2>
                    <textarea name="message" 
                              rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Introduce yourself and explain why you're interested in this property..."></textarea>
                    <p class="text-xs text-gray-500 mt-1">This message will be sent to the property owner</p>
                </div>
            </div>
            
            <!-- Contingencies & Terms -->
            <div class="space-y-6">
                <!-- Contingencies -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Offer Contingencies</h2>
                    <div class="space-y-3">
                        <label class="flex items-start">
                            <input type="checkbox" name="contingencies[]" value="inspection" class="mt-1 mr-3">
                            <span class="text-sm">Property Inspection Contingency</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="contingencies[]" value="financing" class="mt-1 mr-3">
                            <span class="text-sm">Financing Approval Contingency</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="contingencies[]" value="appraisal" class="mt-1 mr-3">
                            <span class="text-sm">Appraisal Contingency</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="contingencies[]" value="title" class="mt-1 mr-3">
                            <span class="text-sm">Clear Title Contingency</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="contingencies[]" value="survey" class="mt-1 mr-3">
                            <span class="text-sm">Property Survey Contingency</span>
                        </label>
                        <label class="flex items-start">
                            <input type="checkbox" name="contingencies[]" value="home_warranty" class="mt-1 mr-3">
                            <span class="text-sm">Home Warranty Included</span>
                        </label>
                    </div>
                </div>
                
                <!-- Special Terms -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Special Terms</h2>
                    <div class="space-y-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="special_terms[]" value="as_is" class="mr-3">
                            <span class="text-sm">Property sold "As Is"</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="special_terms[]" value="quick_close" class="mr-3">
                            <span class="text-sm">Quick closing preferred</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="special_terms[]" value="rent_back" class="mr-3">
                            <span class="text-sm">Request rent-back option</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="special_terms[]" value="flexible_closing" class="mr-3">
                            <span class="text-sm">Flexible closing date</span>
                        </label>
                    </div>
                </div>
                
                <!-- Offer Validity -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Offer Validity</h2>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Offer Expires In
                        </label>
                        <select name="expires_at_hours" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="24">24 Hours</option>
                            <option value="48" selected>48 Hours</option>
                            <option value="72">72 Hours</option>
                            <option value="168">1 Week</option>
                        </select>
                    </div>
                </div>
                
                <!-- Property Owner Info -->
                <div class="bg-blue-50 rounded-lg p-6">
                    <h3 class="text-lg font-semibold mb-3">Property Owner</h3>
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                            <span class="text-lg font-medium">{{ strtoupper(substr($property->user->name, 0, 1)) }}</span>
                        </div>
                        <div class="ml-3">
                            <p class="font-medium">{{ $property->user->name }}</p>
                            <p class="text-sm text-gray-600">Property Owner</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Terms and Submit -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-3">Offer Agreement</h3>
                <div class="bg-yellow-50 rounded-lg p-4 mb-4">
                    <h4 class="font-medium text-yellow-800 mb-2">Important Information</h4>
                    <ul class="text-sm text-yellow-700 space-y-1">
                        <li>• This offer is legally binding if accepted by the seller</li>
                        <li>• You must have proof of funds or pre-approval ready</li>
                        <li>• Earnest money deposit is required upon acceptance</li>
                        <li>• All terms and conditions must be fulfilled as specified</li>
                        <li>• Consult with a real estate agent or attorney before submitting</li>
                    </ul>
                </div>
                
                <label class="flex items-start">
                    <input type="checkbox" name="terms" required class="mt-1 mr-3">
                    <span class="text-sm text-gray-600">
                        I have read and understand the terms of this offer and I am legally and financially prepared to complete this transaction if my offer is accepted.
                    </span>
                </label>
            </div>
            
            <div class="flex space-x-4">
                <a href="{{ route('properties.show', $property->id) }}" 
                   class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-center font-medium">
                    Cancel
                </a>
                <button type="submit" 
                        class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                    Submit Offer
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const amountInput = document.getElementById('amount');
    
    // Constants from Laravel
    const propertyPrice = @json($property->price);
    
    // Update percentage display
    function updatePercentage() {
        const amount = parseFloat(amountInput.value) || 0;
        const percentage = (amount / propertyPrice) * 100;
        const percentageDisplay = document.querySelector('.text-gray-500 span:last-child');
        if (percentageDisplay) {
            percentageDisplay.textContent = Math.round(percentage) + '% of asking';
        }
    }
    
    amountInput.addEventListener('input', updatePercentage);
    
    // Set minimum date to 30 days from now
    const proposedClosingDate = document.getElementById('proposed_closing_date');
    const today = new Date();
    const minDate = new Date(today.setDate(today.getDate() + 30));
    proposedClosingDate.min = minDate.toISOString().split('T')[0];
});
</script>
@endsection
