@extends('layouts.app')

@section('title', $company->name)

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Company Header -->
    <div class="bg-white border-b border-gray-200">
        <!-- Cover Image -->
        <div class="h-64 bg-blue-600 relative overflow-hidden">
            @if($company->profile && $company->profile->cover_image)
                <img src="{{ asset('storage/' . $company->profile->cover_image) }}" alt="" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-gradient-to-r from-blue-600 to-blue-800 opacity-90"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-building text-9xl text-white opacity-10"></i>
                </div>
            @endif
        </div>

        <div class="container mx-auto px-6">
            <div class="relative pb-8">
                <!-- Logo -->
                <div class="absolute -top-16 left-0">
                    <div class="w-32 h-32 rounded-2xl bg-white p-2 shadow-lg border border-gray-100">
                        @if($company->logo_url)
                            <img src="{{ asset('storage/' . $company->logo_url) }}" alt="{{ $company->name }}" class="w-full h-full object-contain rounded-xl">
                        @else
                            <div class="w-full h-full bg-gray-50 rounded-xl flex items-center justify-center">
                                <i class="fas fa-building text-4xl text-gray-300"></i>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Info Header -->
                <div class="pt-20 flex flex-col md:flex-row md:items-end md:justify-between gap-6">
                    <div>
                        <div class="flex items-center gap-3 flex-wrap">
                            <h1 class="text-3xl font-bold text-gray-900">{{ $company->name }}</h1>
                            @if($company->is_verified)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-check-circle mr-1"></i>Verified
                                </span>
                            @endif
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 uppercase">
                                {{ $company->type }}
                            </span>
                        </div>
                        <p class="mt-2 text-gray-600 flex items-center">
                            <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>
                            {{ $company->address ?? 'Address not available' }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        @auth
                            @if(auth()->user()->can('update', $company))
                                <a href="{{ route('companies.edit', $company) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                                    <i class="fas fa-edit mr-2"></i>Edit Profile
                                </a>
                            @endif
                        @endauth
                        <button class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors shadow-sm">
                            Contact Company
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="flex border-t border-gray-100">
                <a href="#" class="px-6 py-4 text-sm font-semibold text-blue-600 border-b-2 border-blue-600">Overview</a>
                <a href="#" class="px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700">Properties ({{ $company->properties_count ?? 0 }})</a>
                <a href="#" class="px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700">Agents ({{ $company->members_count ?? 0 }})</a>
                <a href="#" class="px-6 py-4 text-sm font-medium text-gray-500 hover:text-gray-700">Reviews</a>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="container mx-auto px-6 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- About Section -->
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">About {{ $company->name }}</h2>
                    <div class="prose prose-blue max-w-none text-gray-600">
                        @if($company->description)
                            {!! nl2br(e($company->description)) !!}
                        @elseif($company->profile && $company->profile->description)
                            {!! nl2br(e($company->profile->description)) !!}
                        @else
                            <p>No description provided for this company.</p>
                        @endif
                    </div>

                    @if($company->profile && $company->profile->services)
                        <div class="mt-8">
                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-4">Our Services</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($company->profile->services as $service)
                                    <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-sm">{{ $service }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ $company->properties_count ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Active Listings</div>
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ $company->members_count ?? 0 }}</div>
                        <div class="text-sm text-gray-500">Expert Agents</div>
                    </div>
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ $company->rating ?? '0.0' }}</div>
                        <div class="text-sm text-gray-500">Avg. Rating</div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Contact Info -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h2 class="text-lg font-bold text-gray-900 mb-6">Contact Information</h2>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-phone text-blue-600"></i>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 uppercase font-semibold">Phone</div>
                                <div class="text-gray-900">{{ $company->phone ?? 'Not provided' }}</div>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-envelope text-blue-600"></i>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 uppercase font-semibold">Email</div>
                                <div class="text-gray-900">{{ $company->email ?? 'Not provided' }}</div>
                            </div>
                        </li>
                        <li class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-globe text-blue-600"></i>
                            </div>
                            <div>
                                <div class="text-xs text-gray-500 uppercase font-semibold">Website</div>
                                <div class="text-gray-900">
                                    @if($company->website)
                                        <a href="{{ $company->website }}" target="_blank" class="text-blue-600 hover:underline">{{ parse_url($company->website, PHP_URL_HOST) ?: $company->website }}</a>
                                    @else
                                        Not provided
                                    @endif
                                </div>
                            </div>
                        </li>
                    </ul>

                    @if($company->profile && $company->profile->social_links)
                        <div class="mt-8 pt-6 border-t border-gray-100">
                            <div class="flex gap-4">
                                @foreach($company->profile->social_links as $platform => $url)
                                    @if($url)
                                        <a href="{{ $url }}" target="_blank" class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition-all">
                                            <i class="fab fa-{{ $platform }}"></i>
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Business Hours -->
                @if($company->profile && $company->profile->business_hours)
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <h2 class="text-lg font-bold text-gray-900 mb-6">Business Hours</h2>
                        <ul class="space-y-3">
                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                <li class="flex justify-between text-sm">
                                    <span class="text-gray-500">{{ $day }}</span>
                                    <span class="text-gray-900 font-medium">{{ $company->profile->business_hours[strtolower($day)] ?? 'Closed' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
