@extends('layouts.app')

@section('title', $company->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ $company->name }}</h1>
                    <p class="text-gray-600">Company details and management</p>
                </div>
                <div class="flex space-x-2">
                    <a href="{{ route('admin.companies.edit', $company->id) }}" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </a>
                    <a href="{{ route('admin.companies.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Companies
                    </a>
                </div>
            </div>
        </div>

        <!-- Company Profile -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="text-center">
                        @if($company->logo_url)
                            <img class="h-24 w-24 rounded-full mx-auto object-cover" src="{{ asset('storage/' . $company->logo_url) }}" alt="{{ $company->name }}">
                        @else
                            <div class="h-24 w-24 rounded-full bg-gray-300 mx-auto flex items-center justify-center">
                                <span class="text-gray-600 font-bold text-2xl">{{ strtoupper(substr($company->name, 0, 1)) }}</span>
                            </div>
                        @endif
                        
                        <h2 class="mt-4 text-xl font-bold text-gray-900">{{ $company->name }}</h2>
                        
                        <div class="mt-4 space-y-2">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ ucfirst($company->type) }}
                            </span>
                            <br>
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($company->status == 'active') bg-green-100 text-green-800
                                @elseif($company->status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($company->status == 'suspended') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($company->status) }}
                            </span>
                            @if($company->is_verified)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check mr-1"></i>Verified
                                </span>
                            @endif
                            @if($company->is_featured)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                    <i class="fas fa-star mr-1"></i>Featured
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="mt-6 pt-6 border-t">
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $company->employee_count ?? 0 }}</p>
                                <p class="text-sm text-gray-500">Employees</p>
                            </div>
                            <div>
                                <p class="text-2xl font-bold text-gray-900">{{ $company->rating ?? '0.0' }}</p>
                                <p class="text-sm text-gray-500">Rating</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Contact Information -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Email</p>
                            <p class="text-gray-900">{{ $company->email ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Phone</p>
                            <p class="text-gray-900">{{ $company->phone ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Website</p>
                            <p class="text-gray-900">
                                @if($company->website)
                                    <a href="{{ $company->website }}" target="_blank" class="text-blue-600 hover:text-blue-800">{{ $company->website }}</a>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Founded Date</p>
                            <p class="text-gray-900">{{ $company->founded_date ? \Carbon\Carbon::parse($company->founded_date)->format('M j, Y') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Business Details -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Business Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Registration Number</p>
                            <p class="text-gray-900">{{ $company->registration_number ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Tax ID</p>
                            <p class="text-gray-900">{{ $company->tax_id ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Annual Revenue</p>
                            <p class="text-gray-900">${{ number_format($company->annual_revenue ?? 0, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Subscription Plan</p>
                            <p class="text-gray-900">{{ ucfirst($company->subscription_plan ?? 'N/A') }}</p>
                        </div>
                    </div>
                    
                    @if($company->description)
                        <div class="mt-4">
                            <p class="text-sm font-medium text-gray-500">Description</p>
                            <p class="text-gray-900 mt-1">{{ $company->description }}</p>
                        </div>
                    @endif
                </div>

                <!-- Address -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Address</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <p class="text-sm font-medium text-gray-500">Street Address</p>
                            <p class="text-gray-900">{{ $company->address ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">City</p>
                            <p class="text-gray-900">{{ $company->city ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">State/Province</p>
                            <p class="text-gray-900">{{ $company->state ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Country</p>
                            <p class="text-gray-900">{{ $company->country ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Postal Code</p>
                            <p class="text-gray-900">{{ $company->postal_code ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Verification Status -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Verification Status</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Verification Level</p>
                            <p class="text-gray-900">
                                @switch($company->verification_level)
                                    @case(0)
                                        Not Verified
                                        @break
                                    @case(1)
                                        Basic
                                        @break
                                    @case(2)
                                        Standard
                                        @break
                                    @case(3)
                                        Premium
                                        @break
                                    @default
                                        N/A
                                @endswitch
                            </p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Reviews</p>
                            <p class="text-gray-900">{{ $company->total_reviews ?? 0 }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Subscription Expires</p>
                            <p class="text-gray-900">{{ $company->subscription_expires_at ? \Carbon\Carbon::parse($company->subscription_expires_at)->format('M j, Y') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- API Settings -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">API Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">API Key</p>
                            <p class="text-gray-900 font-mono text-sm">{{ $company->api_key ?? 'Not Generated' }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Webhook URL</p>
                            <p class="text-gray-900">
                                @if($company->webhook_url)
                                    <a href="{{ $company->webhook_url }}" target="_blank" class="text-blue-600 hover:text-blue-800 text-sm">{{ $company->webhook_url }}</a>
                                @else
                                    N/A
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Cover Image -->
                @if($company->cover_image_url)
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 pb-2 border-b">Cover Image</h3>
                        <img src="{{ asset('storage/' . $company->cover_image_url) }}" alt="Cover Image" class="w-full rounded-lg">
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-6 bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    Created: {{ $company->created_at?->format('M j, Y g:i A') ?? 'N/A' }} | 
                    Updated: {{ $company->updated_at?->format('M j, Y g:i A') ?? 'N/A' }}
                </div>
                <div class="flex space-x-2">
                    <form action="{{ route('admin.companies.destroy', $company->id) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors" onclick="return confirm('Are you sure you want to delete this company?')">
                            <i class="fas fa-trash mr-2"></i>Delete Company
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
