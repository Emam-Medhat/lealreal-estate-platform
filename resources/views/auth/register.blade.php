@extends('layouts.auth')

@section('content')

    <div
        class="min-h-screen bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Enhanced Animated Background -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <!-- Floating orbs with enhanced animations -->
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full mix-blend-multiply filter blur-xl opacity-30 animate-pulse animation-delay-2000"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-gradient-to-r from-pink-500 to-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse animation-delay-4000"></div>
            
            <!-- Additional decorative elements -->
            <div class="absolute top-20 left-20 w-32 h-32 bg-gradient-to-r from-yellow-400 to-orange-400 rounded-full mix-blend-multiply filter blur-lg opacity-20 animate-bounce"></div>
            <div class="absolute bottom-20 right-20 w-40 h-40 bg-gradient-to-r from-green-400 to-blue-400 rounded-full mix-blend-multiply filter blur-lg opacity-20 animate-bounce animation-delay-1000"></div>
            
            <!-- Particle effect simulation -->
            <div class="absolute inset-0">
                <div class="stars"></div>
                <div class="stars2"></div>
                <div class="stars3"></div>
            </div>
        </div>

        <div class="max-w-4xl w-full relative z-10">
            <!-- Header Section with Enhanced Animation -->
            <div class="text-center mb-8 relative animate-fade-in-down">
                <div
                    class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-blue-600 via-purple-600 to-pink-600 rounded-3xl shadow-2xl mb-6 transform hover:scale-110 transition-all duration-500 hover:rotate-12 relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent animate-shimmer"></div>
                    <svg class="w-14 h-14 text-white relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                </div>
                <h1 class="text-6xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 mb-4 tracking-tight animate-gradient bg-gradient-animate">
                    {{ __('auth.join_us_today') }}
                </h1>
                <p class="text-2xl text-gray-200 mb-6 animate-fade-in-up">
                    {{ __('auth.create_account_journey') }}
                </p>
                <div class="flex flex-wrap items-center justify-center gap-4 text-gray-300 animate-fade-in-up animation-delay-200">
                    <span class="bg-white/10 px-4 py-2 rounded-full backdrop-blur-sm">{{ __('auth.already_have_account') }}</span>
                    <a href="{{ route('login') }}"
                        class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-2 rounded-full font-semibold transition-all duration-300 transform hover:scale-105 hover:shadow-lg">
                        {{ __('auth.sign_in') }}
                    </a>
                    <span class="text-gray-400">|</span>
                    <a href="{{ route('admin.login') }}"
                        class="bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white px-6 py-2 rounded-full font-semibold transition-all duration-300 transform hover:scale-105 hover:shadow-lg">
                        <i class="fas fa-user-shield mr-2"></i>
                        {{ __('auth.admin_login') }}
                    </a>
                </div>
            </div>

            <!-- Registration Form with Enhanced Glass Effect -->
            <form class="bg-white/5 backdrop-blur-xl border border-white/30 rounded-3xl shadow-2xl overflow-hidden relative animate-fade-in-up"
                action="{{ route('register') }}" method="POST" id="registrationForm">
                @csrf
                
                <!-- Form decorative elements -->
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500"></div>
                <div class="absolute -top-10 -right-10 w-20 h-20 bg-gradient-to-r from-purple-400 to-pink-400 rounded-full opacity-20 blur-xl"></div>
                <div class="absolute -bottom-10 -left-10 w-20 h-20 bg-gradient-to-r from-blue-400 to-indigo-400 rounded-full opacity-20 blur-xl"></div>

                <!-- Enhanced Progress Bar -->
                <div class="h-2 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 relative overflow-hidden">
                    <div id="progressBar" class="h-full bg-gradient-to-r from-white/50 to-white/30 transition-all duration-700 relative" style="width: 33%">
                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent animate-shimmer"></div>
                    </div>
                </div>

                <div class="p-8">
                    <!-- Session Error Display -->
                    @if(session('error'))
                        <div class="rounded-xl bg-red-500/20 border border-red-500/30 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-300">
                                        {{ __('auth.registration_error') }}
                                    </h3>
                                    <div class="mt-2 text-sm text-red-200">
                                        {{ session('error') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="rounded-xl bg-red-500/20 border border-red-500/30 p-4 mb-6">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-300">
                                        {{ __('auth.submission_errors') }}
                                    </h3>
                                    <div class="mt-2 text-sm text-red-200">
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Step 1: Account Type with Enhanced Cards -->
                    <div class="step-content" id="step1">
                        <div class="flex items-center mb-8">
                            <div class="bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 rounded-xl p-4 mr-4 shadow-xl">
                                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-3xl font-bold text-white mb-2">{{ __('auth.choose_role') }}</h3>
                                <p class="text-gray-300">{{ __('auth.select_your_account_type') }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                            <!-- User Card Enhanced -->
                            <div
                                class="role-card group relative bg-gradient-to-br from-blue-600/20 to-blue-700/20 border-2 border-blue-500/30 rounded-2xl p-6 hover:border-blue-400 hover:shadow-2xl hover:shadow-blue-500/25 transition-all duration-500 cursor-pointer transform hover:-translate-y-2">
                                <div class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-transparent rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                <input type="radio" name="user_type" value="user" id="role_user" class="hidden" required>
                                <label for="role_user" class="cursor-pointer relative z-10">
                                    <div class="text-6xl mb-4 text-center transform group-hover:scale-110 transition-transform duration-300">🏠</div>
                                    <h4 class="text-xl font-bold text-white mb-3 text-center">{{ __('auth.user_role') }}</h4>
                                    <p class="text-gray-300 text-sm text-center leading-relaxed">{{ __('auth.user_role_desc') }}</p>
                                    <div class="mt-4 text-center">
                                        <span class="inline-block px-3 py-1 bg-blue-500/30 text-blue-300 text-xs rounded-full">{{ __('auth.popular') }}</span>
                                    </div>
                                </label>
                            </div>

                            <!-- Agent Card Enhanced -->
                            <div
                                class="role-card group relative bg-gradient-to-br from-purple-600/20 to-purple-700/20 border-2 border-purple-500/30 rounded-2xl p-6 hover:border-purple-400 hover:shadow-2xl hover:shadow-purple-500/25 transition-all duration-500 cursor-pointer transform hover:-translate-y-2">
                                <div class="absolute inset-0 bg-gradient-to-br from-purple-500/10 to-transparent rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                <input type="radio" name="user_type" value="agent" id="role_agent" class="hidden">
                                <label for="role_agent" class="cursor-pointer relative z-10">
                                    <div class="text-6xl mb-4 text-center transform group-hover:scale-110 transition-transform duration-300">👔</div>
                                    <h4 class="text-xl font-bold text-white mb-3 text-center">{{ __('auth.agent_role') }}</h4>
                                    <p class="text-gray-300 text-sm text-center leading-relaxed">{{ __('auth.agent_role_desc') }}</p>
                                    <div class="mt-4 text-center">
                                        <span class="inline-block px-3 py-1 bg-purple-500/30 text-purple-300 text-xs rounded-full">{{ __('auth.professional') }}</span>
                                    </div>
                                </label>
                            </div>

                            <!-- Company Card Enhanced -->
                            <div
                                class="role-card group relative bg-gradient-to-br from-indigo-600/20 to-indigo-700/20 border-2 border-indigo-500/30 rounded-2xl p-6 hover:border-indigo-400 hover:shadow-2xl hover:shadow-indigo-500/25 transition-all duration-500 cursor-pointer transform hover:-translate-y-2">
                                <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/10 to-transparent rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                <input type="radio" name="user_type" value="company" id="role_company" class="hidden">
                                <label for="role_company" class="cursor-pointer relative z-10">
                                    <div class="text-6xl mb-4 text-center transform group-hover:scale-110 transition-transform duration-300">🏢</div>
                                    <h4 class="text-xl font-bold text-white mb-3 text-center">{{ __('auth.company_role') }}</h4>
                                    <p class="text-gray-300 text-sm text-center leading-relaxed">{{ __('auth.company_role_desc') }}</p>
                                    <div class="mt-4 text-center">
                                        <span class="inline-block px-3 py-1 bg-indigo-500/30 text-indigo-300 text-xs rounded-full">{{ __('auth.business') }}</span>
                                    </div>
                                </label>
                            </div>

                            <!-- Admin Card Enhanced -->
                            <div
                                class="role-card group relative bg-gradient-to-br from-pink-600/20 to-pink-700/20 border-2 border-pink-500/30 rounded-2xl p-6 hover:border-pink-400 hover:shadow-2xl hover:shadow-pink-500/25 transition-all duration-500 cursor-pointer transform hover:-translate-y-2">
                                <div class="absolute inset-0 bg-gradient-to-br from-pink-500/10 to-transparent rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                <input type="radio" name="user_type" value="admin" id="role_admin" class="hidden">
                                <label for="role_admin" class="cursor-pointer relative z-10">
                                    <div class="text-6xl mb-4 text-center transform group-hover:scale-110 transition-transform duration-300">🛡️</div>
                                    <h4 class="text-xl font-bold text-white mb-3 text-center">{{ __('auth.admin_role') }}</h4>
                                    <p class="text-gray-300 text-sm text-center leading-relaxed">{{ __('auth.admin_role_desc') }}</p>
                                    <div class="mt-4 text-center">
                                        <span class="inline-block px-3 py-1 bg-pink-500/30 text-pink-300 text-xs rounded-full">{{ __('auth.verified') }}</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <button type="button"
                            class="next-step w-full py-4 px-4 border border-transparent rounded-2xl shadow-sm text-sm font-bold text-white bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 hover:from-blue-700 hover:via-purple-700 hover:to-pink-700 transition-all duration-500 transform hover:scale-[1.02] hover:shadow-xl relative overflow-hidden group">
                            <span class="relative z-10">{{ __('auth.continue') }} →</span>
                            <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 transform translate-x-full group-hover:translate-x-0 transition-transform duration-700"></div>
                        </button>
                    </div>

                    <!-- Step 2: Personal Information with Enhanced Layout -->
                    <div class="step-content hidden" id="step2">
                        <div class="flex items-center mb-8">
                            <div class="bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 rounded-xl p-4 mr-4 shadow-xl">
                                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-3xl font-bold text-white mb-2">{{ __('auth.personal_info') }}</h3>
                                <p class="text-gray-300">{{ __('auth.fill_personal_details') }}</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <!-- Basic Information Section -->
                            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6">
                                <h4 class="text-lg font-semibold text-white mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    {{ __('auth.basic_info') }}
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- First Name -->
                                    <div class="group">
                                        <label for="first_name" class="block text-sm font-semibold text-gray-200 mb-2">
                                            {{ __('common.first_name') }} <span class="text-red-400">*</span>
                                        </label>
                                        <div class="relative">
                                            <input id="first_name" name="first_name" type="text" required
                                                class="block w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white/20 transition-all duration-300 group-hover:border-blue-400/50"
                                                value="{{ old('first_name') }}" placeholder="John">
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Last Name -->
                                    <div class="group">
                                        <label for="last_name" class="block text-sm font-semibold text-gray-200 mb-2">
                                            {{ __('common.last_name') }} <span class="text-red-400">*</span>
                                        </label>
                                        <div class="relative">
                                            <input id="last_name" name="last_name" type="text" required
                                                class="block w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white/20 transition-all duration-300 group-hover:border-blue-400/50"
                                                value="{{ old('last_name') }}" placeholder="Doe">
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="md:col-span-2 group">
                                        <label for="email" class="block text-sm font-semibold text-gray-200 mb-2">
                                            {{ __('auth.email') }} <span class="text-red-400">*</span>
                                        </label>
                                        <div class="relative">
                                            <input id="email" name="email" type="email" required
                                                class="block w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white/20 transition-all duration-300 group-hover:border-blue-400/50"
                                                value="{{ old('email') }}" placeholder="john.doe@example.com">
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Phone -->
                                    <div class="group">
                                        <label for="phone" class="block text-sm font-semibold text-gray-200 mb-2">
                                            {{ __('common.phone') }} <span class="text-red-400">*</span>
                                        </label>
                                        <div class="relative">
                                            <input id="phone" name="phone" type="tel" required
                                                class="block w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white/20 transition-all duration-300 group-hover:border-blue-400/50"
                                                value="{{ old('phone') }}" placeholder="+1 234 567 8900">
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Username -->
                                    <div class="group">
                                        <label for="username" class="block text-sm font-semibold text-gray-200 mb-2">
                                            {{ __('common.username') }} <span class="text-red-400">*</span>
                                        </label>
                                        <div class="relative">
                                            <input id="username" name="username" type="text" required
                                                class="block w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white/20 transition-all duration-300 group-hover:border-blue-400/50"
                                                value="{{ old('username') }}" placeholder="johndoe123">
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Location Information Section -->
                            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6">
                                <h4 class="text-lg font-semibold text-white mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ __('auth.location_info') }}
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <!-- Country -->
                                    <div class="group">
                                        <label for="country" class="block text-sm font-semibold text-gray-200 mb-2">
                                            {{ __('common.country') }} <span class="text-red-400">*</span>
                                        </label>
                                        <div class="relative">
                                            <select id="country" name="country" required
                                                class="block w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white/20 transition-all duration-300 group-hover:border-blue-400/50 appearance-none">
                                                <option value="">{{ __('auth.select_country') }}</option>
                                                <option value="Egypt" {{ old('country') == 'Egypt' ? 'selected' : '' }}>Egypt</option>
                                                <option value="Saudi Arabia" {{ old('country') == 'Saudi Arabia' ? 'selected' : '' }}>Saudi Arabia</option>
                                                <option value="UAE" {{ old('country') == 'UAE' ? 'selected' : '' }}>UAE</option>
                                                <option value="USA" {{ old('country') == 'USA' ? 'selected' : '' }}>USA</option>
                                                <option value="UK" {{ old('country') == 'UK' ? 'selected' : '' }}>UK</option>
                                            </select>
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- City -->
                                    <div class="group">
                                        <label for="city" class="block text-sm font-semibold text-gray-200 mb-2">
                                            {{ __('common.city') }} <span class="text-red-400">*</span>
                                        </label>
                                        <div class="relative">
                                            <input id="city" name="city" type="text" required
                                                class="block w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white/20 transition-all duration-300 group-hover:border-blue-400/50"
                                                value="{{ old('city') }}" placeholder="Cairo">
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Date of Birth -->
                                    <div class="group">
                                        <label for="date_of_birth" class="block text-sm font-semibold text-gray-200 mb-2">
                                            {{ __('common.date_of_birth') }} <span class="text-red-400">*</span>
                                        </label>
                                        <div class="relative">
                                            <input id="date_of_birth" name="date_of_birth" type="date" required
                                                class="block w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white/20 transition-all duration-300 group-hover:border-blue-400/50"
                                                value="{{ old('date_of_birth') }}">
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Preferences Section -->
                            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6">
                                <h4 class="text-lg font-semibold text-white mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ __('auth.preferences') }}
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <!-- Gender -->
                                    <div class="group">
                                        <label for="gender" class="block text-sm font-semibold text-gray-200 mb-2">
                                            {{ __('common.gender') }} <span class="text-red-400">*</span>
                                        </label>
                                        <div class="relative">
                                            <select id="gender" name="gender" required
                                                class="block w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white/20 transition-all duration-300 group-hover:border-blue-400/50 appearance-none">
                                                <option value="">{{ __('auth.select_gender') }}</option>
                                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Language -->
                                    <div class="group">
                                        <label for="language" class="block text-sm font-semibold text-gray-200 mb-2">
                                            {{ __('common.language') }} <span class="text-red-400">*</span>
                                        </label>
                                        <div class="relative">
                                            <select id="language" name="language" required
                                                class="block w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white/20 transition-all duration-300 group-hover:border-blue-400/50 appearance-none">
                                                <option value="">{{ __('auth.select_language') }}</option>
                                                <option value="ar" {{ old('language') == 'ar' ? 'selected' : '' }}>العربية</option>
                                                <option value="en" {{ old('language') == 'en' ? 'selected' : '' }}>English</option>
                                                <option value="fr" {{ old('language') == 'fr' ? 'selected' : '' }}>Français</option>
                                            </select>
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Currency -->
                                    <div class="group">
                                        <label for="currency" class="block text-sm font-semibold text-gray-200 mb-2">
                                            {{ __('common.currency') }} <span class="text-red-400">*</span>
                                        </label>
                                        <div class="relative">
                                            <select id="currency" name="currency" required
                                                class="block w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white/20 transition-all duration-300 group-hover:border-blue-400/50 appearance-none">
                                                <option value="">{{ __('auth.select_currency') }}</option>
                                                <option value="EGP" {{ old('currency') == 'EGP' ? 'selected' : '' }}>EGP - جنيه مصري</option>
                                                <option value="SAR" {{ old('currency') == 'SAR' ? 'selected' : '' }}>SAR - ريال سعودي</option>
                                                <option value="AED" {{ old('currency') == 'AED' ? 'selected' : '' }}>AED - درهم إماراتي</option>
                                                <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                                <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                            </select>
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <button type="button"
                                class="prev-step flex-1 py-4 px-4 border-2 border-white/20 rounded-2xl shadow-sm text-sm font-bold text-white hover:bg-white/10 transition-all duration-300">
                                ← {{ __('common.back') }}
                            </button>
                            <button type="button"
                                class="next-step flex-1 py-4 px-4 border border-transparent rounded-2xl shadow-sm text-sm font-bold text-white bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 hover:from-blue-700 hover:via-purple-700 hover:to-pink-700 transition-all duration-500 transform hover:scale-[1.02] hover:shadow-xl relative overflow-hidden group">
                                <span class="relative z-10">{{ __('auth.continue') }} →</span>
                                <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 transform translate-x-full group-hover:translate-x-0 transition-transform duration-700"></div>
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Security & Preferences with Enhanced Layout -->
                    <div class="step-content hidden" id="step3">
                        <div class="flex items-center mb-8">
                            <div class="bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 rounded-xl p-4 mr-4 shadow-xl">
                                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-3xl font-bold text-white mb-2">{{ __('auth.security') }}</h3>
                                <p class="text-gray-300">{{ __('auth.setup_secure_password') }}</p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <!-- Password Section -->
                            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6">
                                <h4 class="text-lg font-semibold text-white mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    {{ __('auth.password_security') }}
                                </h4>
                                <div class="space-y-4">
                                    <!-- Password -->
                                    <div class="group">
                                        <label for="password" class="block text-sm font-semibold text-gray-200 mb-2">
                                            {{ __('auth.password') }} <span class="text-red-400">*</span>
                                        </label>
                                        <div class="relative">
                                            <input id="password" name="password" type="password" required minlength="8"
                                                class="block w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white/20 transition-all duration-300 group-hover:border-blue-400/50"
                                                placeholder="{{ __('auth.password_placeholder') }}">
                                            <button type="button" id="togglePassword"
                                                class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-300 transition-colors duration-300">
                                                <svg id="eyeIcon" class="h-5 w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                    </path>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="mt-3 flex items-center space-x-2">
                                            <div class="flex-1 bg-white/10 rounded-full h-2 overflow-hidden">
                                                <div id="passwordStrength" class="h-full bg-gradient-to-r from-red-500 to-yellow-500 transition-all duration-300" style="width: 0%"></div>
                                            </div>
                                            <span id="strengthText" class="text-xs text-gray-400">{{ __('auth.weak') }}</span>
                                        </div>
                                        <p class="mt-2 text-xs text-gray-300">{{ __('auth.min_8_chars') }}</p>
                                    </div>

                                    <!-- Confirm Password -->
                                    <div class="group">
                                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-200 mb-2">
                                            {{ __('auth.confirm_password') }} <span class="text-red-400">*</span>
                                        </label>
                                        <div class="relative">
                                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                                class="block w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white/20 transition-all duration-300 group-hover:border-blue-400/50"
                                                placeholder="{{ __('auth.confirm_password_placeholder') }}">
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Marketing Preferences Section -->
                            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6">
                                <h4 class="text-lg font-semibold text-white mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                    </svg>
                                    {{ __('auth.notification_preferences') }}
                                </h4>
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-xl hover:bg-white/10 transition-colors duration-300">
                                        <div class="flex items-center">
                                            <input type="hidden" name="marketing_consent" value="0">
                                            <input id="marketing_consent" name="marketing_consent" type="checkbox" value="1"
                                                class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded bg-white/10">
                                            <label for="marketing_consent" class="ml-3 block text-sm text-gray-200 cursor-pointer">
                                                <div class="font-medium">{{ __('auth.marketing_consent') }}</div>
                                                <div class="text-xs text-gray-400">{{ __('auth.marketing_consent_desc') }}</div>
                                            </label>
                                        </div>
                                        <div class="text-blue-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                                            </svg>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-xl hover:bg-white/10 transition-colors duration-300">
                                        <div class="flex items-center">
                                            <input type="hidden" name="newsletter_subscribed" value="0">
                                            <input id="newsletter_subscribed" name="newsletter_subscribed" type="checkbox" value="1"
                                                class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded bg-white/10">
                                            <label for="newsletter_subscribed" class="ml-3 block text-sm text-gray-200 cursor-pointer">
                                                <div class="font-medium">{{ __('auth.newsletter') }}</div>
                                                <div class="text-xs text-gray-400">{{ __('auth.newsletter_desc') }}</div>
                                            </label>
                                        </div>
                                        <div class="text-green-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-xl hover:bg-white/10 transition-colors duration-300">
                                        <div class="flex items-center">
                                            <input type="hidden" name="sms_notifications" value="0">
                                            <input id="sms_notifications" name="sms_notifications" type="checkbox" value="1"
                                                class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded bg-white/10">
                                            <label for="sms_notifications" class="ml-3 block text-sm text-gray-200 cursor-pointer">
                                                <div class="font-medium">{{ __('auth.sms_notifications') }}</div>
                                                <div class="text-xs text-gray-400">{{ __('auth.sms_notifications_desc') }}</div>
                                            </label>
                                        </div>
                                        <div class="text-purple-400">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Terms Section -->
                            <div class="bg-white/5 backdrop-blur-sm border border-white/10 rounded-2xl p-6">
                                <h4 class="text-lg font-semibold text-white mb-4 flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                    {{ __('auth.terms_conditions') }}
                                </h4>
                                <div class="flex items-start p-4 bg-white/5 rounded-xl">
                                    <input type="hidden" name="terms" value="0">
                                    <input id="terms" name="terms" type="checkbox" value="1" required
                                        class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded bg-white/10 mt-1">
                                    <label for="terms" class="ml-3 block text-sm text-gray-200 cursor-pointer">
                                        <div class="font-medium mb-1">{{ __('auth.i_agree_to_terms') }}</div>
                                        <div class="text-xs text-gray-400 leading-relaxed">
                                            {{ __('auth.terms_agreement_text') }}
                                            <a href="#" class="text-blue-400 hover:text-blue-300 underline mx-1">{{ __('auth.terms_of_service') }}</a>
                                            {{ __('auth.and') }}
                                            <a href="#" class="text-blue-400 hover:text-blue-300 underline mx-1">{{ __('auth.privacy_policy') }}</a>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <button type="button"
                                class="prev-step flex-1 py-4 px-4 border-2 border-white/20 rounded-2xl shadow-sm text-sm font-bold text-white hover:bg-white/10 transition-all duration-300">
                                ← {{ __('Back') }}
                            </button>
                            <button type="submit"
                                class="flex-1 py-4 px-4 border border-transparent rounded-2xl shadow-sm text-sm font-bold text-white bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 hover:from-green-700 hover:via-emerald-700 hover:to-teal-700 transition-all duration-500 transform hover:scale-[1.02] hover:shadow-xl relative overflow-hidden group">
                                <span class="relative z-10">{{ __('auth.register') }} ✓</span>
                                <div class="absolute inset-0 bg-gradient-to-r from-white/0 via-white/20 to-white/0 transform translate-x-full group-hover:translate-x-0 transition-transform duration-700"></div>
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Enhanced Social Registration -->
            <div class="mt-6 bg-white/5 backdrop-blur-xl border border-white/30 rounded-3xl p-6 animate-fade-in-up animation-delay-400">
                <div class="relative mb-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gradient-to-r from-transparent via-white/30 to-transparent"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-transparent text-gray-200 font-medium">{{ __('auth.register_with') }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <a href="#"
                        class="flex items-center justify-center px-4 py-4 border border-white/20 rounded-xl shadow-sm text-sm font-medium text-gray-200 bg-white/10 hover:bg-white/20 transition-all duration-500 transform hover:scale-105 hover:shadow-lg group">
                        <svg class="h-6 w-6 mr-2" viewBox="0 0 24 24">
                            <path fill="#4285F4"
                                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                            <path fill="#34A853"
                                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                            <path fill="#FBBC05"
                                d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                            <path fill="#EA4335"
                                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                        </svg>
                        <span class="group-hover:text-white transition-colors duration-300">Google</span>
                    </a>
                    <a href="#"
                        class="flex items-center justify-center px-4 py-4 border border-white/20 rounded-xl shadow-sm text-sm font-medium text-gray-200 bg-white/10 hover:bg-white/20 transition-all duration-500 transform hover:scale-105 hover:shadow-lg group">
                        <svg class="h-6 w-6 mr-2" fill="#1877F2" viewBox="0 0 24 24">
                            <path
                                d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                        </svg>
                        <span class="group-hover:text-white transition-colors duration-300">Facebook</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let currentStep = 1;
            const totalSteps = 3;

            // Role Card Selection
            document.querySelectorAll('.role-card').forEach(card => {
                card.addEventListener('click', function () {
                    document.querySelectorAll('.role-card').forEach(c => {
                        c.classList.remove('border-blue-500', 'bg-white/20');
                        c.classList.add('border-white/20');
                    });
                    this.classList.remove('border-white/20');
                    this.classList.add('border-blue-500', 'bg-white/20');
                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;
                });
            });

            // Step Navigation
            document.querySelectorAll('.next-step').forEach(btn => {
                btn.addEventListener('click', function () {
                    if (validateStep(currentStep)) {
                        currentStep++;
                        showStep(currentStep);
                        updateProgressBar();
                    }
                });
            });

            document.querySelectorAll('.prev-step').forEach(btn => {
                btn.addEventListener('click', function () {
                    currentStep--;
                    showStep(currentStep);
                    updateProgressBar();
                });
            });

            function showStep(step) {
                document.querySelectorAll('.step-content').forEach(content => content.classList.add('hidden'));
                document.getElementById('step' + step).classList.remove('hidden');
            }

            function updateProgressBar() {
                const progress = (currentStep / totalSteps) * 100;
                document.getElementById('progressBar').style.width = progress + '%';
            }

            function validateStep(step) {
                const stepElement = document.getElementById('step' + step);
                const requiredFields = stepElement.querySelectorAll('[required]');
                let valid = true;

                requiredFields.forEach(field => {
                    if (!field.value && field.type !== 'checkbox') {
                        field.classList.add('border-red-500');
                        valid = false;
                    } else if (field.type === 'checkbox' && !field.checked) {
                        valid = false;
                    } else {
                        field.classList.remove('border-red-500');
                    }
                });

                if (!valid) {
                    alert('Please fill in all required fields');
                }

                return valid;
            }

            // Password Toggle and Strength Checker
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const passwordStrength = document.getElementById('passwordStrength');
            const strengthText = document.getElementById('strengthText');
            
            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function () {
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        eyeIcon.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                    `;
                    } else {
                        passwordInput.type = 'password';
                        eyeIcon.innerHTML = `
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    `;
                    }
                });
                
                // Password strength checker
                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    let strength = 0;
                    
                    if (password.length >= 8) strength++;
                    if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
                    if (password.match(/[0-9]/)) strength++;
                    if (password.match(/[^a-zA-Z0-9]/)) strength++;
                    
                    // Update strength indicator
                    if (passwordStrength && strengthText) {
                        passwordStrength.className = 'h-full transition-all duration-300 rounded-full';
                        
                        if (strength === 0 || strength === 1) {
                            passwordStrength.style.width = '25%';
                            passwordStrength.classList.add('bg-gradient-to-r', 'from-red-500', 'to-orange-500');
                            strengthText.textContent = '{{ __("auth.weak") }}';
                            strengthText.className = 'text-xs text-red-400';
                        } else if (strength === 2) {
                            passwordStrength.style.width = '50%';
                            passwordStrength.classList.add('bg-gradient-to-r', 'from-orange-500', 'to-yellow-500');
                            strengthText.textContent = '{{ __("auth.medium") }}';
                            strengthText.className = 'text-xs text-yellow-400';
                        } else if (strength === 3) {
                            passwordStrength.style.width = '75%';
                            passwordStrength.classList.add('bg-gradient-to-r', 'from-yellow-500', 'to-blue-500');
                            strengthText.textContent = '{{ __("auth.good") }}';
                            strengthText.className = 'text-xs text-blue-400';
                        } else {
                            passwordStrength.style.width = '100%';
                            passwordStrength.classList.add('bg-gradient-to-r', 'from-green-500', 'to-emerald-500');
                            strengthText.textContent = '{{ __("auth.strong") }}';
                            strengthText.className = 'text-xs text-green-400';
                        }
                    }
                });
            }
        });

        // Console error logging
        window.addEventListener('error', function (e) {
            console.error('JavaScript Error:', {
                message: e.message,
                filename: e.filename,
                lineno: e.lineno,
                colno: e.colno,
                error: e.error
            });
        });

        // Form submission error handling
        document.getElementById('registrationForm').addEventListener('submit', function (e) {
            console.log('Form submission started');
            console.log('Form data:', new FormData(this));

            // Add loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>' + "{{ __('auth.processing') }}";
            }
        });
    </script>

@endsection