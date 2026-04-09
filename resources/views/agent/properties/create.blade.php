@extends('layouts.app')

@section('title', 'Add New Property')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Add New Property</h1>
                <p class="text-gray-600 mt-2">Create a new property listing</p>
            </div>
            <div class="flex space-x-4">
                <a href="{{ route('agent.properties.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Properties
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('agent.properties.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 border-b pb-3">Basic Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title -->
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Property Title *</label>
                    <input type="text" id="title" name="title" required
                           value="{{ old('title') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter property title">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                    <textarea id="description" name="description" rows="4" required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Describe the property...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Property Type -->
                <div>
                    <label for="property_type" class="block text-sm font-medium text-gray-700 mb-2">Property Type *</label>
                    <select id="property_type" name="property_type" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select type</option>
                        <option value="apartment" {{ old('property_type') == 'apartment' ? 'selected' : '' }}>Apartment</option>
                        <option value="villa" {{ old('property_type') == 'villa' ? 'selected' : '' }}>Villa</option>
                        <option value="house" {{ old('property_type') == 'house' ? 'selected' : '' }}>House</option>
                        <option value="land" {{ old('property_type') == 'land' ? 'selected' : '' }}>Land</option>
                        <option value="commercial" {{ old('property_type') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                    </select>
                    @error('property_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Listing Type -->
                <div>
                    <label for="listing_type" class="block text-sm font-medium text-gray-700 mb-2">Listing Type *</label>
                    <select id="listing_type" name="listing_type" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select listing type</option>
                        <option value="sale" {{ old('listing_type') == 'sale' ? 'selected' : '' }}>For Sale</option>
                        <option value="rent" {{ old('listing_type') == 'rent' ? 'selected' : '' }}>For Rent</option>
                    </select>
                    @error('listing_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Price -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price *</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required
                           value="{{ old('price') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0.00">
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Currency -->
                <div>
                    <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Currency *</label>
                    <select id="currency" name="currency" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="SAR" {{ old('currency') == 'SAR' ? 'selected' : '' }}>SAR</option>
                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                    </select>
                    @error('currency')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Area -->
                <div>
                    <label for="area" class="block text-sm font-medium text-gray-700 mb-2">Area *</label>
                    <input type="number" id="area" name="area" step="0.01" min="0" required
                           value="{{ old('area') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0.00">
                    @error('area')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Area Unit -->
                <div>
                    <label for="area_unit" class="block text-sm font-medium text-gray-700 mb-2">Area Unit *</label>
                    <select id="area_unit" name="area_unit" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="sq_m" {{ old('area_unit') == 'sq_m' ? 'selected' : '' }}>Square Meters</option>
                        <option value="sq_ft" {{ old('area_unit') == 'sq_ft' ? 'selected' : '' }}>Square Feet</option>
                    </select>
                    @error('area_unit')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bedrooms -->
                <div>
                    <label for="bedrooms" class="block text-sm font-medium text-gray-700 mb-2">Bedrooms</label>
                    <input type="number" id="bedrooms" name="bedrooms" min="0"
                           value="{{ old('bedrooms') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0">
                    @error('bedrooms')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bathrooms -->
                <div>
                    <label for="bathrooms" class="block text-sm font-medium text-gray-700 mb-2">Bathrooms</label>
                    <input type="number" id="bathrooms" name="bathrooms" min="0"
                           value="{{ old('bathrooms') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0">
                    @error('bathrooms')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Floors -->
                <div>
                    <label for="floors" class="block text-sm font-medium text-gray-700 mb-2">Floors</label>
                    <input type="number" id="floors" name="floors" min="0"
                           value="{{ old('floors') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0">
                    @error('floors')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Year Built -->
                <div>
                    <label for="year_built" class="block text-sm font-medium text-gray-700 mb-2">Year Built</label>
                    <input type="number" id="year_built" name="year_built" min="1900" max="{{ date('Y') }}"
                           value="{{ old('year_built') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="2024">
                    @error('year_built')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select id="status" name="status" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select status</option>
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="sold" {{ old('status') == 'sold' ? 'selected' : '' }}>Sold</option>
                        <option value="rented" {{ old('status') == 'rented' ? 'selected' : '' }}>Rented</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Featured -->
                <div class="flex items-center">
                    <input type="checkbox" id="featured" name="featured" value="1"
                           {{ old('featured') ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="featured" class="ml-2 text-sm text-gray-700">Featured Property</label>
                </div>

                <!-- Premium -->
                <div class="flex items-center">
                    <input type="checkbox" id="premium" name="premium" value="1"
                           {{ old('premium') ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="premium" class="ml-2 text-sm text-gray-700">Premium Property</label>
                </div>
            </div>
        </div>

        <!-- Location Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 border-b pb-3">Location Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Address -->
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                    <input type="text" id="address" name="address" required
                           value="{{ old('address') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Enter full address">
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- City -->
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                    <input type="text" id="city" name="city" required
                           value="{{ old('city') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="City name">
                    @error('city')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- State -->
                <div>
                    <label for="state" class="block text-sm font-medium text-gray-700 mb-2">State</label>
                    <input type="text" id="state" name="state"
                           value="{{ old('state') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="State name">
                    @error('state')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Country -->
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country *</label>
                    <input type="text" id="country" name="country" required
                           value="{{ old('country') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Country name">
                    @error('country')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Postal Code -->
                <div>
                    <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code"
                           value="{{ old('postal_code') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="Postal code">
                    @error('postal_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Latitude -->
                <div>
                    <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">Latitude</label>
                    <input type="number" id="latitude" name="latitude" step="any" min="-90" max="90"
                           value="{{ old('latitude') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="24.7136">
                    @error('latitude')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Longitude -->
                <div>
                    <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">Longitude</label>
                    <input type="number" id="longitude" name="longitude" step="any" min="-180" max="180"
                           value="{{ old('longitude') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="46.6753">
                    @error('longitude')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 border-b pb-3">Additional Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Amenities -->
                <div class="md:col-span-2">
                    <label for="amenities" class="block text-sm font-medium text-gray-700 mb-2">Amenities</label>
                    <textarea id="amenities" name="amenities" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Swimming Pool, Parking, Garden, Security...">{{ old('amenities') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">Separate amenities with commas</p>
                </div>

                <!-- Nearby Places -->
                <div class="md:col-span-2">
                    <label for="nearby_places" class="block text-sm font-medium text-gray-700 mb-2">Nearby Places</label>
                    <textarea id="nearby_places" name="nearby_places" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="School, Hospital, Mall, Park...">{{ old('nearby_places') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">Separate places with commas</p>
                </div>

                <!-- Virtual Tour URL -->
                <div>
                    <label for="virtual_tour_url" class="block text-sm font-medium text-gray-700 mb-2">Virtual Tour URL</label>
                    <input type="url" id="virtual_tour_url" name="virtual_tour_url"
                           value="{{ old('virtual_tour_url') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="https://example.com/virtual-tour">
                    @error('virtual_tour_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Video URL -->
                <div>
                    <label for="video_url" class="block text-sm font-medium text-gray-700 mb-2">Video URL</label>
                    <input type="url" id="video_url" name="video_url"
                           value="{{ old('video_url') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="https://youtube.com/...">
                    @error('video_url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Media Upload -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6 border-b pb-3">Property Images</h2>

            <div class="space-y-6">
                <!-- Image Upload Input -->
                <div>
                    <label for="images" class="block text-sm font-medium text-gray-700 mb-2">Upload Images</label>
                    <input type="file" id="images" name="images[]" multiple accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <p class="mt-1 text-sm text-gray-500">You can select multiple images. Maximum file size: 5MB per image.</p>
                    @error('images')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('images.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Image Preview -->
                <div id="image-preview" class="grid grid-cols-2 md:grid-cols-4 gap-4"></div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="bg-white rounded-lg shadow-md p-6 flex justify-between">
            <a href="{{ route('agent.properties.index') }}" class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                <i class="fas fa-save mr-2"></i>
                Create Property
            </button>
        </div>
    </form>
</div>

<script>
document.getElementById('images').addEventListener('change', function(e) {
    const preview = document.getElementById('image-preview');
    preview.innerHTML = '';

    Array.from(this.files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(event) {
            const div = document.createElement('div');
            div.className = 'relative group';
            div.innerHTML = `
                <img src="${event.target.result}" alt="Preview ${index}" class="w-full h-32 object-cover rounded-lg">
                <button type="button" class="absolute top-2 right-2 bg-red-600 text-white p-1 rounded opacity-0 group-hover:opacity-100 transition" onclick="this.parentElement.remove()">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            preview.appendChild(div);
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endsection
