@extends('layouts.app')

@section('title', 'Edit Investor')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center mb-4">
                <a href="{{ route('investor.show', $investor) }}" class="text-blue-600 hover:text-blue-800 mr-4">
                    <i class="fas fa-arrow-left"></i> Back to Investor
                </a>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit Investor</h1>
            <p class="text-gray-600">Update investor information for {{ $investor->first_name }} {{ $investor->last_name }}</p>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <form method="POST" action="{{ route('investor.update', $investor) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Personal Information -->
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h3>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name *</label>
                        <input type="text" name="first_name" value="{{ $investor->first_name }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('first_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name *</label>
                        <input type="text" name="last_name" value="{{ $investor->last_name }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('last_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                        <input type="email" name="email" value="{{ $investor->email }}" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input type="tel" name="phone" value="{{ $investor->phone }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                        <input type="text" name="company_name" value="{{ $investor->company_name }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('company_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Investor Type *</label>
                        <select name="investor_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Type</option>
                            <option value="individual" {{ $investor->investor_type == 'individual' ? 'selected' : '' }}>Individual</option>
                            <option value="company" {{ $investor->investor_type == 'company' ? 'selected' : '' }}>Company</option>
                            <option value="fund" {{ $investor->investor_type == 'fund' ? 'selected' : '' }}>Fund</option>
                            <option value="bank" {{ $investor->investor_type == 'bank' ? 'selected' : '' }}>Bank</option>
                            <option value="government" {{ $investor->investor_type == 'government' ? 'selected' : '' }}>Government</option>
                            <option value="institution" {{ $investor->investor_type == 'institution' ? 'selected' : '' }}>Institution</option>
                        </select>
                        @error('investor_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="active" {{ $investor->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $investor->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="suspended" {{ $investor->status == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="verified" {{ $investor->status == 'verified' ? 'selected' : '' }}>Verified</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Risk Tolerance *</label>
                        <select name="risk_tolerance" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Select Risk Tolerance</option>
                            <option value="conservative" {{ $investor->risk_tolerance == 'conservative' ? 'selected' : '' }}>Conservative</option>
                            <option value="moderate" {{ $investor->risk_tolerance == 'moderate' ? 'selected' : '' }}>Moderate</option>
                            <option value="aggressive" {{ $investor->risk_tolerance == 'aggressive' ? 'selected' : '' }}>Aggressive</option>
                            <option value="very_aggressive" {{ $investor->risk_tolerance == 'very_aggressive' ? 'selected' : '' }}>Very Aggressive</option>
                        </select>
                        @error('risk_tolerance')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Experience Years</label>
                        <input type="number" name="experience_years" value="{{ $investor->experience_years }}" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('experience_years')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Investment Information -->
                    <div class="md:col-span-2 mt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Investment Information</h3>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Total Invested</label>
                        <input type="number" name="total_invested" value="{{ $investor->total_invested }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('total_invested')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Total Returns</label>
                        <input type="number" name="total_returns" value="{{ $investor->total_returns }}" step="0.01" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('total_returns')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Verification Status</label>
                        <select name="verification_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="pending" {{ $investor->verification_status == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="verified" {{ $investor->verification_status == 'verified' ? 'selected' : '' }}>Verified</option>
                            <option value="rejected" {{ $investor->verification_status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                        @error('verification_status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="md:col-span-2">
                        <div class="flex items-center">
                            <input type="checkbox" name="accredited_investor" value="1" {{ $investor->accredited_investor ? 'checked' : '' }} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label class="ml-2 block text-sm text-gray-900">Accredited Investor</label>
                        </div>
                        @error('accredited_investor')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Profile Picture -->
                    <div class="md:col-span-2 mt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Profile Picture</h3>
                    </div>
                    
                    <div class="md:col-span-2">
                        @if($investor->profile_picture)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Current Profile Picture</label>
                                <img src="{{ asset('storage/' . $investor->profile_picture) }}" alt="Current profile picture" class="h-20 w-20 rounded-full object-cover">
                            </div>
                        @endif
                        
                        <label class="block text-sm font-medium text-gray-700 mb-2">Update Profile Picture</label>
                        <input type="file" name="profile_picture" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        @error('profile_picture')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Bio -->
                    <div class="md:col-span-2 mt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Bio</h3>
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                        <textarea name="bio" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ $investor->bio }}</textarea>
                        @error('bio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('investor.show', $investor) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Update Investor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
