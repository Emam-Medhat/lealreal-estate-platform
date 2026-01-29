@extends('layouts.app')

@section('title', 'Register Company')

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="container mx-auto px-6">
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Register Your Company</h1>
                <p class="mt-2 text-gray-600">Join our network of real estate professionals and reach more clients.</p>
            </div>

            <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <!-- Basic Information -->
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center mr-3 text-sm">1</span>
                        Basic Information
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Company Name *</label>
                            <input type="text" name="name" id="name" required value="{{ old('name') }}"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror">
                            @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">Company Type *</label>
                            <select name="type" id="type" required
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                                <option value="agency" {{ old('type') == 'agency' ? 'selected' : '' }}>Agency</option>
                                <option value="developer" {{ old('type') == 'developer' ? 'selected' : '' }}>Developer</option>
                                <option value="contractor" {{ old('type') == 'contractor' ? 'selected' : '' }}>Contractor</option>
                            </select>
                        </div>

                        <div>
                            <label for="registration_number" class="block text-sm font-semibold text-gray-700 mb-2">Registration Number</label>
                            <input type="text" name="registration_number" id="registration_number" value="{{ old('registration_number') }}"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center mr-3 text-sm">2</span>
                        Contact Details
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Company Email *</label>
                            <input type="email" name="email" id="email" required value="{{ old('email') }}"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone Number</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="md:col-span-2">
                            <label for="website" class="block text-sm font-semibold text-gray-700 mb-2">Website URL</label>
                            <input type="url" name="website" id="website" value="{{ old('website') }}" placeholder="https://"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">Office Address</label>
                            <input type="text" name="address" id="address" value="{{ old('address') }}"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Profile Branding -->
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center mr-3 text-sm">3</span>
                        Branding & Description
                    </h2>
                    
                    <div class="space-y-6">
                        <div>
                            <label for="logo" class="block text-sm font-semibold text-gray-700 mb-2">Company Logo</label>
                            <div class="mt-1 flex items-center">
                                <input type="file" name="logo" id="logo" accept="image/*"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            <p class="mt-2 text-xs text-gray-500">Recommended: Square PNG or JPG, at least 400x400px</p>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">About the Company</label>
                            <textarea name="description" id="description" rows="5"
                                      class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Describe your company, services, and expertise...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('companies.index') }}" class="px-6 py-3 text-sm font-semibold text-gray-700 hover:text-gray-900 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-10 py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-all shadow-lg shadow-blue-200">
                        Register Company
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
