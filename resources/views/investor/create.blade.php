@extends('layouts.app')

@section('title', 'Create New Investor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <strong>Error:</strong> {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <strong>Please fix the following errors:</strong>
                <ul class="list-disc list-inside mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Create New Investor</h1>
                    <p class="text-gray-600">Add a new investor to the platform</p>
                </div>
                <div>
                    <a href="{{ route('investors.index') }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Investors
                    </a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('investors.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Personal Information -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Personal Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">First Name <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name" id="first_name" required value="{{ old('first_name') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('first_name') border-red-500 @enderror">
                        @error('first_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" id="last_name" required value="{{ old('last_name') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('last_name') border-red-500 @enderror">
                        @error('last_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" required value="{{ old('email') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror">
                        @error('phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('company_name') border-red-500 @enderror">
                        @error('company_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="investor_type" class="block text-sm font-medium text-gray-700 mb-2">Investor Type <span class="text-red-500">*</span></label>
                        <select name="investor_type" id="investor_type" required
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('investor_type') border-red-500 @enderror">
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
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Investment Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" id="status"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror">
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="verified" {{ old('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                        </select>
                        @error('status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="risk_tolerance" class="block text-sm font-medium text-gray-700 mb-2">Risk Tolerance <span class="text-red-500">*</span></label>
                        <select name="risk_tolerance" id="risk_tolerance" required
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('risk_tolerance') border-red-500 @enderror">
                            <option value="">Select risk tolerance</option>
                            <option value="conservative" {{ old('risk_tolerance') == 'conservative' ? 'selected' : '' }}>Conservative</option>
                            <option value="moderate" {{ old('risk_tolerance') == 'moderate' ? 'selected' : '' }}>Moderate</option>
                            <option value="aggressive" {{ old('risk_tolerance') == 'aggressive' ? 'selected' : '' }}>Aggressive</option>
                            <option value="very_aggressive" {{ old('risk_tolerance') == 'very_aggressive' ? 'selected' : '' }}>Very Aggressive</option>
                        </select>
                        @error('risk_tolerance') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="experience_years" class="block text-sm font-medium text-gray-700 mb-2">Experience Years</label>
                        <input type="number" name="experience_years" id="experience_years" value="{{ old('experience_years') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('experience_years') border-red-500 @enderror">
                        @error('experience_years') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="verification_status" class="block text-sm font-medium text-gray-700 mb-2">Verification Status</label>
                        <select name="verification_status" id="verification_status"
                                class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('verification_status') border-red-500 @enderror">
                            <option value="pending" {{ old('verification_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="verified" {{ old('verification_status') == 'verified' ? 'selected' : '' }}>Verified</option>
                            <option value="rejected" {{ old('verification_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        @error('verification_status') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="total_invested" class="block text-sm font-medium text-gray-700 mb-2">Total Invested</label>
                        <input type="number" name="total_invested" id="total_invested" step="0.01" value="{{ old('total_invested') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('total_invested') border-red-500 @enderror">
                        @error('total_invested') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="total_returns" class="block text-sm font-medium text-gray-700 mb-2">Total Returns</label>
                        <input type="number" name="total_returns" id="total_returns" step="0.01" value="{{ old('total_returns') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('total_returns') border-red-500 @enderror">
                        @error('total_returns') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="accredited_investor" value="1" {{ old('accredited_investor') ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Accredited Investor</span>
                        </label>
                        @error('accredited_investor') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

                <!-- Investment Preferences -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Investment Preferences</h2>
                
                <div class="space-y-4">
                    <!-- Investment Goals -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Investment Goals</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="investment_goals[]" value="long_term_growth" {{ in_array('long_term_growth', old('investment_goals', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Long Term Growth</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="investment_goals[]" value="passive_income" {{ in_array('passive_income', old('investment_goals', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Passive Income</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="investment_goals[]" value="capital_preservation" {{ in_array('capital_preservation', old('investment_goals', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Capital Preservation</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="investment_goals[]" value="speculative_growth" {{ in_array('speculative_growth', old('investment_goals', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Speculative Growth</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="investment_goals[]" value="dividend_income" {{ in_array('dividend_income', old('investment_goals', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Dividend Income</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="investment_goals[]" value="tax_optimization" {{ in_array('tax_optimization', old('investment_goals', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Tax Optimization</span>
                            </label>
                        </div>
                        @error('investment_goals') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Preferred Sectors -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Sectors</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="preferred_sectors[]" value="real_estate" {{ in_array('real_estate', old('preferred_sectors', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Real Estate</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="preferred_sectors[]" value="technology" {{ in_array('technology', old('preferred_sectors', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Technology</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="preferred_sectors[]" value="healthcare" {{ in_array('healthcare', old('preferred_sectors', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Healthcare</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="preferred_sectors[]" value="finance" {{ in_array('finance', old('preferred_sectors', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Finance</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="preferred_sectors[]" value="energy" {{ in_array('energy', old('preferred_sectors', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Energy</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="preferred_sectors[]" value="consumer_goods" {{ in_array('consumer_goods', old('preferred_sectors', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Consumer Goods</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="preferred_sectors[]" value="industrial" {{ in_array('industrial', old('preferred_sectors', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Industrial</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="preferred_sectors[]" value="telecommunications" {{ in_array('telecommunications', old('preferred_sectors', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Telecommunications</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="preferred_sectors[]" value="utilities" {{ in_array('utilities', old('preferred_sectors', [])) ? 'checked' : '' }}
                                       class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Utilities</span>
                            </label>
                        </div>
                        @error('preferred_sectors') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

                <!-- Address Information -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Address Information</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label for="address_street" class="block text-sm font-medium text-gray-700 mb-2">Street Address</label>
                        <input type="text" name="address[street]" id="address_street" value="{{ old('address.street') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('address.street') border-red-500 @enderror">
                        @error('address.street') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="address_city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                        <input type="text" name="address[city]" id="address_city" value="{{ old('address.city') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('address.city') border-red-500 @enderror">
                        @error('address.city') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="address_state" class="block text-sm font-medium text-gray-700 mb-2">State/Province</label>
                        <input type="text" name="address[state]" id="address_state" value="{{ old('address.state') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('address.state') border-red-500 @enderror">
                        @error('address.state') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="address_country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                        <input type="text" name="address[country]" id="address_country" value="{{ old('address.country') ?? 'Saudi Arabia' }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('address.country') border-red-500 @enderror">
                        @error('address.country') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="address_postal_code" class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                        <input type="text" name="address[postal_code]" id="address_postal_code" value="{{ old('address.postal_code') }}"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('address.postal_code') border-red-500 @enderror">
                        @error('address.postal_code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Social Media Links -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Social Media Links</h2>
                
                <div class="space-y-3" id="social-links-container">
                    <div class="social-link-item grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Platform</label>
                            <select name="social_links[0][platform]" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Platform</option>
                                <option value="linkedin" {{ old('social_links.0.platform') == 'linkedin' ? 'selected' : '' }}>LinkedIn</option>
                                <option value="twitter" {{ old('social_links.0.platform') == 'twitter' ? 'selected' : '' }}>Twitter</option>
                                <option value="facebook" {{ old('social_links.0.platform') == 'facebook' ? 'selected' : '' }}>Facebook</option>
                                <option value="instagram" {{ old('social_links.0.platform') == 'instagram' ? 'selected' : '' }}>Instagram</option>
                                <option value="website" {{ old('social_links.0.platform') == 'website' ? 'selected' : '' }}>Website</option>
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">URL</label>
                            <input type="url" name="social_links[0][url]" value="{{ old('social_links.0.url') }}"
                                   class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="https://example.com/profile">
                        </div>
                    </div>
                </div>
                
                <button type="button" onclick="addSocialLink()" class="mt-3 px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors text-sm">
                    <i class="fas fa-plus mr-2"></i>Add Social Link
                </button>
            </div>

            <!-- Profile Picture & Bio -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Profile Picture & Bio</h2>
                
                <div class="space-y-4">
                    <div>
                        <label for="profile_picture" class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                        <input type="file" name="profile_picture" id="profile_picture" accept="image/*"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('profile_picture') border-red-500 @enderror">
                        <p class="mt-1 text-sm text-gray-500">PNG, JPG, GIF up to 5MB</p>
                        @error('profile_picture') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                        <textarea name="bio" id="bio" rows="4"
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('bio') border-red-500 @enderror">{{ old('bio') }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">Brief description about the investor (max 2000 characters)</p>
                        @error('bio') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('investors.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Create Investor
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let socialLinkIndex = 1;

function addSocialLink() {
    const container = document.getElementById('social-links-container');
    const newLink = document.createElement('div');
    newLink.className = 'social-link-item grid grid-cols-1 md:grid-cols-3 gap-4';
    newLink.innerHTML = `
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Platform</label>
            <select name="social_links[${socialLinkIndex}][platform]" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option value="">Select Platform</option>
                <option value="linkedin">LinkedIn</option>
                <option value="twitter">Twitter</option>
                <option value="facebook">Facebook</option>
                <option value="instagram">Instagram</option>
                <option value="website">Website</option>
            </select>
        </div>
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">URL</label>
            <div class="flex gap-2">
                <input type="url" name="social_links[${socialLinkIndex}][url]"
                       class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                       placeholder="https://example.com/profile">
                <button type="button" onclick="removeSocialLink(this)" class="px-3 py-2 bg-red-100 text-red-600 rounded-md hover:bg-red-200 transition-colors text-sm">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(newLink);
    socialLinkIndex++;
}

function removeSocialLink(button) {
    button.closest('.social-link-item').remove();
}
</script>
@endsection
