@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-100 via-purple-50 to-pink-100">
    <!-- Animated Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-yellow-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-2000"></div>
        <div class="absolute top-40 left-40 w-80 h-80 bg-pink-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-blob animation-delay-4000"></div>
    </div>
    
    <!-- Header Section -->
    <div class="relative bg-white/80 backdrop-blur-lg shadow-xl border-b border-white/20">
        <div class="container mx-auto px-4 py-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-4xl font-black bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 bg-clip-text text-transparent animate-gradient">
                        <i class="fas fa-plus-circle mr-3 text-purple-600"></i>Create Financial Analysis
                    </h1>
                    <p class="text-gray-700 mt-3 text-lg font-medium">Add a new property financial analysis to your portfolio</p>
                </div>
                <a href="{{ route('financial.analysis.index') }}" 
                   class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 font-bold rounded-2xl hover:from-gray-200 hover:to-gray-300 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl">
                    <i class="fas fa-arrow-left mr-3"></i>Back to Analyses
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="relative container mx-auto px-4 py-8">
        <div class="max-w-5xl mx-auto">
            <!-- Form Card -->
            <div class="bg-white/90 backdrop-blur-xl rounded-3xl shadow-2xl overflow-hidden border border-white/20">
                <div class="px-8 py-6 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 border-b border-white/20">
                    <h2 class="text-2xl font-bold text-white flex items-center">
                        <i class="fas fa-chart-line mr-3"></i>Property Analysis Details
                    </h2>
                    <p class="text-indigo-100 mt-2">Fill in the details below to create your financial analysis</p>
                </div>
                
                <form action="{{ route('financial.analyses.store') }}" method="POST" class="p-6">
                    @csrf
                    
                    <!-- Property Selection -->
                    <div class="mb-10">
                        <label class="block text-lg font-bold text-gray-800 mb-5">
                            <i class="fas fa-building mr-3 text-indigo-600"></i>Select Property
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @if($properties->count() > 0)
                                @foreach($properties as $property)
                                    <label class="group relative flex flex-col p-6 border-3 border-gray-200 rounded-2xl cursor-pointer hover:border-indigo-400 hover:bg-gradient-to-br hover:from-indigo-50 hover:to-purple-50 transition-all duration-300 transform hover:scale-105 hover:shadow-lg">
                                        <input type="radio" name="property_id" value="{{ $property->id }}" class="sr-only" required>
                                        <div class="flex items-start space-x-4">
                                            <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                                <i class="fas fa-building text-white text-xl"></i>
                                            </div>
                                            <div class="flex-1">
                                                <div class="font-bold text-gray-900 text-lg mb-1">{{ $property->title }}</div>
                                                <div class="text-gray-600 text-sm mb-2 flex items-center">
                                                    <i class="fas fa-map-marker-alt mr-2 text-red-400"></i>{{ $property->address }}
                                                </div>
                                                <div class="text-2xl font-black bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">${{ number_format($property->price ?? 0, 0) }}</div>
                                            </div>
                                        </div>
                                        <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                            <div class="w-8 h-8 bg-indigo-600 rounded-full flex items-center justify-center">
                                                <i class="fas fa-check text-white text-sm"></i>
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            @else
                                <div class="col-span-full text-center py-12 bg-gradient-to-br from-yellow-50 to-orange-50 rounded-3xl border-2 border-yellow-200">
                                    <i class="fas fa-exclamation-triangle text-6xl text-yellow-500 mb-4 animate-pulse"></i>
                                    <p class="text-gray-700 text-lg mb-6 font-semibold">No available properties found.</p>
                                    <a href="/properties/create" class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-yellow-400 to-orange-500 text-white font-bold rounded-2xl hover:from-yellow-500 hover:to-orange-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                                        <i class="fas fa-plus mr-3"></i>Add Property
                                    </a>
                                </div>
                            @endif
                        </div>
                        @error('property_id')
                            <div class="mt-4 p-4 bg-red-50 border-2 border-red-200 rounded-2xl flex items-center">
                                <i class="fas fa-exclamation-triangle text-red-500 text-xl mr-3"></i>
                                <p class="text-red-700 font-medium">{{ $message }}</p>
                            </div>
                        @enderror
                    </div>

                    <!-- Financial Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-10">
                        <div>
                            <label class="block text-lg font-bold text-gray-800 mb-3">
                                <i class="fas fa-dollar-sign mr-3 text-green-600"></i>Purchase Price
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-xl font-bold">$</span>
                                </div>
                                <input type="number" name="purchase_price" step="0.01" min="0" required
                                    class="pl-12 w-full px-4 py-4 text-lg border-3 border-gray-300 rounded-2xl focus:ring-4 focus:ring-green-500 focus:border-green-500 transition-all duration-300 font-semibold"
                                    placeholder="0.00">
                            </div>
                            @error('purchase_price')
                                <div class="mt-3 p-3 bg-red-50 border-2 border-red-200 rounded-xl flex items-center">
                                    <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                                    <p class="text-red-700 font-medium">{{ $message }}</p>
                                </div>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-lg font-bold text-gray-800 mb-3">
                                <i class="fas fa-chart-line mr-3 text-purple-600"></i>Current Value
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <span class="text-gray-500 text-xl font-bold">$</span>
                                </div>
                                <input type="number" name="current_value" step="0.01" min="0" required
                                    class="pl-12 w-full px-4 py-4 text-lg border-3 border-gray-300 rounded-2xl focus:ring-4 focus:ring-purple-500 focus:border-purple-500 transition-all duration-300 font-semibold"
                                    placeholder="0.00">
                            </div>
                            @error('current_value')
                                <div class="mt-3 p-3 bg-red-50 border-2 border-red-200 rounded-xl flex items-center">
                                    <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                                    <p class="text-red-700 font-medium">{{ $message }}</p>
                                </div>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-lg font-bold text-gray-800 mb-3">
                                <i class="fas fa-calendar mr-3 text-orange-600"></i>Purchase Date
                            </label>
                            <input type="date" name="purchase_date" required
                                class="w-full px-4 py-4 text-lg border-3 border-gray-300 rounded-2xl focus:ring-4 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 font-semibold">
                            @error('purchase_date')
                                <div class="mt-3 p-3 bg-red-50 border-2 border-red-200 rounded-xl flex items-center">
                                    <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                                    <p class="text-red-700 font-medium">{{ $message }}</p>
                                </div>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-lg font-bold text-gray-800 mb-3">
                                <i class="fas fa-tag mr-3 text-indigo-600"></i>Analysis Type
                            </label>
                            <select name="analysis_type" required
                                class="w-full px-4 py-4 text-lg border-3 border-gray-300 rounded-2xl focus:ring-4 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-300 font-semibold">
                                <option value="">Select Analysis Type</option>
                                <option value="roi">ROI Analysis</option>
                                <option value="cash_flow">Cash Flow Analysis</option>
                                <option value="cap_rate">Cap Rate Analysis</option>
                                <option value="comprehensive">Comprehensive Analysis</option>
                            </select>
                            @error('analysis_type')
                                <div class="mt-3 p-3 bg-red-50 border-2 border-red-200 rounded-xl flex items-center">
                                    <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                                    <p class="text-red-700 font-medium">{{ $message }}</p>
                                </div>
                            @enderror
                        </div>
                    </div>

                    <!-- Additional Information -->
                    <div class="mb-10">
                        <label class="block text-lg font-bold text-gray-800 mb-3">
                            <i class="fas fa-sticky-note mr-3 text-blue-600"></i>Notes
                        </label>
                        <textarea name="notes" rows="5"
                            class="w-full px-4 py-4 text-lg border-3 border-gray-300 rounded-2xl focus:ring-4 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 font-semibold"
                            placeholder="Add any additional notes about this analysis..."></textarea>
                        @error('notes')
                            <div class="mt-3 p-3 bg-red-50 border-2 border-red-200 rounded-xl flex items-center">
                                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                                <p class="text-red-700 font-medium">{{ $message }}</p>
                            </div>
                        @enderror
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-6">
                        <button type="submit" 
                            class="flex-1 inline-flex items-center justify-center px-8 py-5 bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 text-white font-black text-lg rounded-2xl shadow-2xl hover:shadow-3xl transform hover:scale-105 transition-all duration-300 border-4 border-white/50">
                            <i class="fas fa-save mr-3"></i>Create Analysis
                        </button>
                        <a href="{{ route('financial.analysis.index') }}" 
                            class="inline-flex items-center justify-center px-8 py-5 bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 font-black text-lg rounded-2xl hover:from-gray-200 hover:to-gray-300 transition-all duration-300 transform hover:scale-105 shadow-lg border-4 border-white/50">
                            <i class="fas fa-times mr-3"></i>Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes blob {
    0% {
        transform: translate(0px, 0px) scale(1);
    }
    33% {
        transform: translate(30px, -50px) scale(1.1);
    }
    66% {
        transform: translate(-20px, 20px) scale(0.9);
    }
    100% {
        transform: translate(0px, 0px) scale(1);
    }
}

@keyframes gradient {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

.animate-blob {
    animation: blob 7s infinite;
}

.animation-delay-2000 {
    animation-delay: 2s;
}

.animation-delay-4000 {
    animation-delay: 4s;
}

.animate-gradient {
    background-size: 200% 200%;
    animation: gradient 3s ease infinite;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Radio button styling
    const radioInputs = document.querySelectorAll('input[type="radio"][name="property_id"]');
    const labels = document.querySelectorAll('input[type="radio"][name="property_id"] + label');
    
    radioInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Reset all labels
            labels.forEach(label => {
                label.classList.remove('border-blue-400', 'bg-blue-50');
                label.classList.add('border-gray-200');
            });
            
            // Highlight selected label
            if (this.checked) {
                const label = this.closest('label');
                label.classList.remove('border-gray-200');
                label.classList.add('border-blue-400', 'bg-blue-50');
            }
        });
    });
});
</script>
@endsection
