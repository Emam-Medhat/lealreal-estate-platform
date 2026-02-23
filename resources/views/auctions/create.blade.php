@extends('layouts.app')

@section('title', 'Create Auction')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <div class="mb-8 flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Create New Auction</h1>
                <p class="text-gray-600">Set up an auction for your property</p>
            </div>
            <a href="{{ route('auctions.my') }}" class="text-gray-600 hover:text-gray-900">
                &larr; Back to My Auctions
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6 md:p-8">
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700">
                                    Please correct the following errors:
                                </p>
                                <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('auctions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Property Selection -->
                    <div>
                        <label for="property_id" class="block text-sm font-medium text-gray-700 mb-2">Select Property</label>
                        <select name="property_id" id="property_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">Select a property...</option>
                            @foreach($properties as $property)
                                <option value="{{ $property->id }}" {{ old('property_id') == $property->id ? 'selected' : '' }}>
                                    {{ $property->title }} ({{ $property->location ?? 'No location' }})
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-sm text-gray-500">Only approved properties can be auctioned.</p>
                    </div>

                    <!-- Basic Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Auction Title</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>

                        <div class="col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" id="description" rows="4" 
                                      class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="starting_price" class="block text-sm font-medium text-gray-700 mb-2">Starting Price ($)</label>
                            <input type="number" step="0.01" name="starting_price" id="starting_price" value="{{ old('starting_price') }}" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>

                        <div>
                            <label for="reserve_price" class="block text-sm font-medium text-gray-700 mb-2">Reserve Price ($) (Optional)</label>
                            <input type="number" step="0.01" name="reserve_price" id="reserve_price" value="{{ old('reserve_price') }}" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Minimum price to sell</p>
                        </div>

                        <div>
                            <label for="bid_increment" class="block text-sm font-medium text-gray-700 mb-2">Min Bid Increment ($)</label>
                            <input type="number" step="0.01" name="bid_increment" id="bid_increment" value="{{ old('bid_increment', 100) }}" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>
                    </div>

                    <!-- Timing -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                            <input type="datetime-local" name="start_time" id="start_time" value="{{ old('start_time') }}" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>

                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                            <input type="datetime-local" name="end_time" id="end_time" value="{{ old('end_time') }}" 
                                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                        </div>
                    </div>

                    <!-- Settings -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Auction Type</label>
                            <select name="type" id="type" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                <option value="public" {{ old('type') == 'public' ? 'selected' : '' }}>Public (Anyone can bid)</option>
                                <option value="private" {{ old('type') == 'private' ? 'selected' : '' }}>Private (Invite only)</option>
                            </select>
                        </div>

                        <div class="flex items-center mt-8">
                            <input type="checkbox" name="auto_extend" id="auto_extend" value="1" {{ old('auto_extend') ? 'checked' : '' }}
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="auto_extend" class="ml-2 block text-sm text-gray-900">
                                Enable Auto-Extension
                                <p class="text-xs text-gray-500">Extend auction if bid placed in last 5 minutes</p>
                            </label>
                        </div>
                    </div>

                    <!-- Images -->
                    <div>
                        <label for="images" class="block text-sm font-medium text-gray-700 mb-2">Auction Images</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="images" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Upload files</span>
                                        <input id="images" name="images[]" type="file" class="sr-only" multiple accept="image/*">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">
                                    PNG, JPG, GIF up to 2MB
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Actions -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="{{ route('auctions.my') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors shadow-md">
                            Create Auction
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
