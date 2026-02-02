@extends('layouts.app')

@section('title', 'Create Investor')

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="container mx-auto px-6">
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Create New Investor</h1>
                <p class="mt-2 text-gray-600">Add a new investor to the platform</p>
            </div>

            <form action="{{ route('investor.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf

                <!-- Personal Information -->
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center mr-3 text-sm">1</span>
                        Personal Information
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="first_name" class="block text-sm font-semibold text-gray-700 mb-2">First Name *</label>
                            <input type="text" name="first_name" id="first_name" required value="{{ old('first_name') }}"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 @error('first_name') border-red-500 @enderror">
                            @error('first_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="last_name" class="block text-sm font-semibold text-gray-700 mb-2">Last Name *</label>
                            <input type="text" name="last_name" id="last_name" required value="{{ old('last_name') }}"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 @error('last_name') border-red-500 @enderror">
                            @error('last_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email *</label>
                            <input type="email" name="email" id="email" required value="{{ old('email') }}"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                            @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Phone</label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror">
                            @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="company_name" class="block text-sm font-semibold text-gray-700 mb-2">Company Name</label>
                            <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 @error('company_name') border-red-500 @enderror">
                            @error('company_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="investor_type" class="block text-sm font-semibold text-gray-700 mb-2">Investor Type *</label>
                            <select name="investor_type" id="investor_type" required
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 @error('investor_type') border-red-500 @enderror">
                                <option value="">Select investor type</option>
                                <option value="individual" {{ old('investor_type') == 'individual' ? 'selected' : '' }}>Individual</option>
                                <option value="company" {{ old('investor_type') == 'company' ? 'selected' : '' }}>Company</option>
                                <option value="fund" {{ old('investor_type') == 'fund' ? 'selected' : '' }}>Fund</option>
                                <option value="bank" {{ old('investor_type') == 'bank' ? 'selected' : '' }}>Bank</option>
                                <option value="government" {{ old('investor_type') == 'government' ? 'selected' : '' }}>Government</option>
                                <option value="institution" {{ old('investor_type') == 'institution' ? 'selected' : '' }}>Institution</option>
                            </select>
                            @error('investor_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Investment Information -->
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="w-8 h-8 rounded-lg bg-green-100 text-green-600 flex items-center justify-center mr-3 text-sm">2</span>
                        Investment Information
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                            <select name="status" id="status"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror">
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                <option value="verified" {{ old('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                            </select>
                            @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="risk_tolerance" class="block text-sm font-semibold text-gray-700 mb-2">Risk Tolerance *</label>
                            <select name="risk_tolerance" id="risk_tolerance" required
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 @error('risk_tolerance') border-red-500 @enderror">
                                <option value="">Select risk tolerance</option>
                                <option value="conservative" {{ old('risk_tolerance') == 'conservative' ? 'selected' : '' }}>Conservative</option>
                                <option value="moderate" {{ old('risk_tolerance') == 'moderate' ? 'selected' : '' }}>Moderate</option>
                                <option value="aggressive" {{ old('risk_tolerance') == 'aggressive' ? 'selected' : '' }}>Aggressive</option>
                                <option value="very_aggressive" {{ old('risk_tolerance') == 'very_aggressive' ? 'selected' : '' }}>Very Aggressive</option>
                            </select>
                            @error('risk_tolerance') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="experience_years" class="block text-sm font-semibold text-gray-700 mb-2">Experience Years</label>
                            <input type="number" name="experience_years" id="experience_years" value="{{ old('experience_years') }}"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 @error('experience_years') border-red-500 @enderror">
                            @error('experience_years') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="verification_status" class="block text-sm font-semibold text-gray-700 mb-2">Verification Status</label>
                            <select name="verification_status" id="verification_status"
                                    class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 @error('verification_status') border-red-500 @enderror">
                                <option value="pending" {{ old('verification_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="verified" {{ old('verification_status') == 'verified' ? 'selected' : '' }}>Verified</option>
                                <option value="rejected" {{ old('verification_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                            @error('verification_status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="total_invested" class="block text-sm font-semibold text-gray-700 mb-2">Total Invested</label>
                            <input type="number" name="total_invested" id="total_invested" step="0.01" value="{{ old('total_invested') }}"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 @error('total_invested') border-red-500 @enderror">
                            @error('total_invested') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="total_returns" class="block text-sm font-semibold text-gray-700 mb-2">Total Returns</label>
                            <input type="number" name="total_returns" id="total_returns" step="0.01" value="{{ old('total_returns') }}"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 @error('total_returns') border-red-500 @enderror">
                            @error('total_returns') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="accredited_investor" value="1" {{ old('accredited_investor') ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm font-medium text-gray-700">Accredited Investor</span>
                            </label>
                            @error('accredited_investor') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Profile Picture & Bio -->
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <span class="w-8 h-8 rounded-lg bg-purple-100 text-purple-600 flex items-center justify-center mr-3 text-sm">3</span>
                        Profile Picture & Bio
                    </h2>
                    
                    <div class="space-y-6">
                        <div>
                            <label for="profile_picture" class="block text-sm font-semibold text-gray-700 mb-2">Profile Picture</label>
                            <input type="file" name="profile_picture" id="profile_picture" accept="image/*"
                                   class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 @error('profile_picture') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500">PNG, JPG, GIF up to 10MB</p>
                            @error('profile_picture') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="bio" class="block text-sm font-semibold text-gray-700 mb-2">Bio</label>
                            <textarea name="bio" id="bio" rows="4" value="{{ old('bio') }}"
                                      class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-blue-500 focus:border-blue-500 @error('bio') border-red-500 @enderror">{{ old('bio') }}</textarea>
                            @error('bio') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('investor.index') }}" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-xl hover:bg-blue-700 transition-colors">
                        Create Investor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
