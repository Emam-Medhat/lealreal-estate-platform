@extends('layouts.auth')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl w-full">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-blue-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-indigo-500 rounded-full mix-blend-multiply filter blur-xl opacity-10 animate-pulse"></div>
        </div>

        <!-- Header Section -->
        <div class="text-center mb-8 relative">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-blue-600 to-purple-600 rounded-2xl shadow-2xl mb-6 transform hover:scale-105 transition-transform duration-300">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </div>
            <h1 class="text-5xl font-bold text-white mb-4 tracking-tight">
                {{ __('Join Us Today') }}
            </h1>
            <p class="text-xl text-gray-300 mb-6">
                {{ __('Create your account and start your journey') }}
            </p>
            <div class="flex items-center justify-center space-x-4 text-gray-400">
                <span>{{ __('Already have an account?') }}</span>
                <a href="{{ route('login') }}" class="text-blue-400 hover:text-blue-300 font-semibold transition-colors duration-200">
                    {{ __('Sign In') }}
                </a>
                <span class="text-gray-500">|</span>
                <a href="{{ route('admin.login') }}" class="text-purple-400 hover:text-purple-300 font-semibold transition-colors duration-200">
                    <i class="fas fa-user-shield mr-1"></i>
                    {{ __('Admin Login') }}
                </a>
            </div>
        </div>

        <!-- Registration Form -->
        <form class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-3xl shadow-2xl overflow-hidden" action="{{ route('register') }}" method="POST" id="registrationForm">
            @csrf

            <!-- Progress Bar -->
            <div class="h-1 bg-gradient-to-r from-blue-500 via-purple-500 to-indigo-500">
                <div id="progressBar" class="h-full bg-white/30 transition-all duration-500" style="width: 33%"></div>
            </div>

            <div class="p-8">
                <!-- Session Error Display -->
                @if(session('error'))
                    <div class="rounded-xl bg-red-500/20 border border-red-500/30 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-300">
                                    {{ __('Registration Error') }}
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
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-300">
                                    {{ __('There were some errors with your submission') }}
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

                <!-- Step 1: Account Type -->
                <div class="step-content" id="step1">
                    <div class="flex items-center mb-6">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-3 mr-4 shadow-lg">
                            <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white">{{ __('Choose Your Role') }}</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <!-- User -->
                        <div class="role-card border-2 border-white/20 rounded-xl p-6 hover:border-blue-500 hover:bg-white/20 transition-all cursor-pointer">
                            <input type="radio" name="user_type" value="user" id="role_user" class="hidden" required>
                            <label for="role_user" class="cursor-pointer">
                                <div class="text-5xl mb-3 text-center">🏠</div>
                                <h4 class="text-xl font-bold text-white mb-2 text-center">{{ __('User') }}</h4>
                                <p class="text-gray-300 text-sm text-center">{{ __('Search and find properties') }}</p>
                            </label>
                        </div>

                        <!-- Agent -->
                        <div class="role-card border-2 border-white/20 rounded-xl p-6 hover:border-blue-500 hover:bg-white/20 transition-all cursor-pointer">
                            <input type="radio" name="user_type" value="agent" id="role_agent" class="hidden">
                            <label for="role_agent" class="cursor-pointer">
                                <div class="text-5xl mb-3 text-center">👔</div>
                                <h4 class="text-xl font-bold text-white mb-2 text-center">{{ __('Agent') }}</h4>
                                <p class="text-gray-300 text-sm text-center">{{ __('Manage properties & clients') }}</p>
                            </label>
                        </div>

                        <!-- Company -->
                        <div class="role-card border-2 border-white/20 rounded-xl p-6 hover:border-blue-500 hover:bg-white/20 transition-all cursor-pointer">
                            <input type="radio" name="user_type" value="company" id="role_company" class="hidden">
                            <label for="role_company" class="cursor-pointer">
                                <div class="text-5xl mb-3 text-center">🏢</div>
                                <h4 class="text-xl font-bold text-white mb-2 text-center">{{ __('Company') }}</h4>
                                <p class="text-gray-300 text-sm text-center">{{ __('Manage team & business') }}</p>
                            </label>
                        </div>

                        <!-- Admin -->
                        <div class="role-card border-2 border-white/20 rounded-xl p-6 hover:border-purple-500 hover:bg-purple-20/20 transition-all cursor-pointer">
                            <input type="radio" name="user_type" value="admin" id="role_admin" class="hidden">
                            <label for="role_admin" class="cursor-pointer">
                                <div class="text-5xl mb-3 text-center">🛡️</div>
                                <h4 class="text-xl font-bold text-white mb-2 text-center">{{ __('Admin') }}</h4>
                                <p class="text-gray-300 text-sm text-center">{{ __('System administration') }}</p>
                            </label>
                        </div>
                    </div>

                    <button type="button" class="next-step w-full py-4 px-4 border border-transparent rounded-2xl shadow-sm text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-[1.02]">
                        {{ __('Continue') }} →
                    </button>
                </div>

                <!-- Step 2: Personal Information -->
                <div class="step-content hidden" id="step2">
                    <div class="flex items-center mb-6">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-3 mr-4 shadow-lg">
                            <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white">{{ __('Personal Information') }}</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- First Name -->
                        <div>
                            <label for="first_name" class="block text-sm font-semibold text-gray-200 mb-2">
                                {{ __('First Name') }} <span class="text-red-400">*</span>
                            </label>
                            <input id="first_name" name="first_name" type="text" required
                                   class="block w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white/20 transition-all duration-300"
                                   value="{{ old('first_name') }}"
                                   placeholder="John">
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label for="last_name" class="block text-sm font-semibold text-gray-200 mb-2">
                                {{ __('Last Name') }} <span class="text-red-400">*</span>
                            </label>
                            <input id="last_name" name="last_name" type="text" required
                                   class="block w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white/20 transition-all duration-300"
                                   value="{{ old('last_name') }}"
                                   placeholder="Doe">
                        </div>

                        <!-- Email -->
                        <div class="md:col-span-2">
                            <label for="email" class="block text-sm font-semibold text-gray-200 mb-2">
                                {{ __('Email Address') }} <span class="text-red-400">*</span>
                            </label>
                            <input id="email" name="email" type="email" required
                                   class="block w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white/20 transition-all duration-300"
                                   value="{{ old('email') }}"
                                   placeholder="john.doe@example.com">
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-200 mb-2">
                                {{ __('Phone Number') }} <span class="text-red-400">*</span>
                            </label>
                            <input id="phone" name="phone" type="tel" required
                                   class="block w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white/20 transition-all duration-300"
                                   value="{{ old('phone') }}"
                                   placeholder="+1 234 567 8900">
                        </div>

                        <!-- Country -->
                        <div>
                            <label for="country" class="block text-sm font-semibold text-gray-200 mb-2">
                                {{ __('Country') }} <span class="text-red-400">*</span>
                            </label>
                            <select id="country" name="country" required
                                    class="block w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white/20 transition-all duration-300">
                                <option value="">{{ __('Select Country') }}</option>
                                <option value="Egypt" {{ old('country') == 'Egypt' ? 'selected' : '' }}>Egypt</option>
                                <option value="Saudi Arabia" {{ old('country') == 'Saudi Arabia' ? 'selected' : '' }}>Saudi Arabia</option>
                                <option value="UAE" {{ old('country') == 'UAE' ? 'selected' : '' }}>UAE</option>
                                <option value="USA" {{ old('country') == 'USA' ? 'selected' : '' }}>USA</option>
                                <option value="UK" {{ old('country') == 'UK' ? 'selected' : '' }}>UK</option>
                            </select>
                        </div>

                        <!-- City -->
                        <div>
                            <label for="city" class="block text-sm font-semibold text-gray-200 mb-2">
                                {{ __('City') }} <span class="text-red-400">*</span>
                            </label>
                            <input id="city" name="city" type="text" required
                                   class="block w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white/20 transition-all duration-300"
                                   value="{{ old('city') }}"
                                   placeholder="Cairo">
                        </div>

                        <!-- Date of Birth -->
                        <div>
                            <label for="date_of_birth" class="block text-sm font-semibold text-gray-200 mb-2">
                                {{ __('Date of Birth') }} <span class="text-red-400">*</span>
                            </label>
                            <input id="date_of_birth" name="date_of_birth" type="date" required
                                   class="block w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white/20 transition-all duration-300"
                                   value="{{ old('date_of_birth') }}">
                        </div>

                        <!-- Gender -->
                        <div>
                            <label for="gender" class="block text-sm font-semibold text-gray-200 mb-2">
                                {{ __('Gender') }} <span class="text-red-400">*</span>
                            </label>
                            <select id="gender" name="gender" required
                                    class="block w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white/20 transition-all duration-300">
                                <option value="">{{ __('Select Gender') }}</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <!-- Username -->
                        <div class="md:col-span-2">
                            <label for="username" class="block text-sm font-semibold text-gray-200 mb-2">
                                {{ __('Username') }} <span class="text-red-400">*</span>
                            </label>
                            <input id="username" name="username" type="text" required
                                   class="block w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white/20 transition-all duration-300"
                                   value="{{ old('username') }}"
                                   placeholder="johndoe123">
                        </div>

                        <!-- Language -->
                        <div>
                            <label for="language" class="block text-sm font-semibold text-gray-200 mb-2">
                                {{ __('Language') }} <span class="text-red-400">*</span>
                            </label>
                            <select id="language" name="language" required
                                    class="block w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white/20 transition-all duration-300">
                                <option value="">{{ __('Select Language') }}</option>
                                <option value="ar" {{ old('language') == 'ar' ? 'selected' : '' }}>العربية</option>
                                <option value="en" {{ old('language') == 'en' ? 'selected' : '' }}>English</option>
                                <option value="fr" {{ old('language') == 'fr' ? 'selected' : '' }}>Français</option>
                            </select>
                        </div>

                        <!-- Currency -->
                        <div>
                            <label for="currency" class="block text-sm font-semibold text-gray-200 mb-2">
                                {{ __('Currency') }} <span class="text-red-400">*</span>
                            </label>
                            <select id="currency" name="currency" required
                                    class="block w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white/20 transition-all duration-300">
                                <option value="">{{ __('Select Currency') }}</option>
                                <option value="EGP" {{ old('currency') == 'EGP' ? 'selected' : '' }}>EGP - جنيه مصري</option>
                                <option value="SAR" {{ old('currency') == 'SAR' ? 'selected' : '' }}>SAR - ريال سعودي</option>
                                <option value="AED" {{ old('currency') == 'AED' ? 'selected' : '' }}>AED - درهم إماراتي</option>
                                <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <button type="button" class="prev-step flex-1 py-4 px-4 border-2 border-white/20 rounded-2xl shadow-sm text-sm font-bold text-white hover:bg-white/10 transition-all duration-300">
                            ← {{ __('Back') }}
                        </button>
                        <button type="button" class="next-step flex-1 py-4 px-4 border border-transparent rounded-2xl shadow-sm text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 transition-all duration-300 transform hover:scale-[1.02]">
                            {{ __('Continue') }} →
                        </button>
                    </div>
                </div>

                <!-- Step 3: Password -->
                <div class="step-content hidden" id="step3">
                    <div class="flex items-center mb-6">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl p-3 mr-4 shadow-lg">
                            <svg class="h-7 w-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-white">{{ __('Security') }}</h3>
                    </div>

                    <div class="space-y-6 mb-6">
                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-200 mb-2">
                                {{ __('Password') }} <span class="text-red-400">*</span>
                            </label>
                            <div class="relative">
                                <input id="password" name="password" type="password" required minlength="8"
                                       class="block w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white/20 transition-all duration-300"
                                       placeholder="Enter strong password">
                                <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-300">
                                    <svg id="eyeIcon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                            </div>
                            <p class="mt-2 text-xs text-gray-300">{{ __('Minimum 8 characters') }}</p>
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-200 mb-2">
                                {{ __('Confirm Password') }} <span class="text-red-400">*</span>
                            </label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required
                                   class="block w-full px-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white/20 transition-all duration-300"
                                   placeholder="Confirm your password">
                        </div>

                        <!-- Marketing Preferences -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="flex items-center">
                                <input type="hidden" name="marketing_consent" value="0">
                                <input id="marketing_consent" name="marketing_consent" type="checkbox" value="1"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded bg-white/10">
                                <label for="marketing_consent" class="ml-2 block text-sm text-gray-300">
                                    {{ __('Marketing Consent') }}
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="hidden" name="newsletter_subscribed" value="0">
                                <input id="newsletter_subscribed" name="newsletter_subscribed" type="checkbox" value="1"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded bg-white/10">
                                <label for="newsletter_subscribed" class="ml-2 block text-sm text-gray-300">
                                    {{ __('Newsletter') }}
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="hidden" name="sms_notifications" value="0">
                                <input id="sms_notifications" name="sms_notifications" type="checkbox" value="1"
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded bg-white/10">
                                <label for="sms_notifications" class="ml-2 block text-sm text-gray-300">
                                    {{ __('SMS Notifications') }}
                                </label>
                            </div>
                        </div>

                        <!-- Terms -->
                        <div class="flex items-start">
                            <input type="hidden" name="terms" value="0">
                            <input id="terms" name="terms" type="checkbox" value="1" required
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded bg-white/10 mt-1">
                            <label for="terms" class="ml-2 block text-sm text-gray-300">
                                {{ __('I agree to the') }}
                                <a href="#" class="text-blue-400 hover:text-blue-300">{{ __('Terms and Conditions') }}</a>
                                {{ __('and') }}
                                <a href="#" class="text-blue-400 hover:text-blue-300">{{ __('Privacy Policy') }}</a>
                            </label>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <button type="button" class="prev-step flex-1 py-4 px-4 border-2 border-white/20 rounded-2xl shadow-sm text-sm font-bold text-white hover:bg-white/10 transition-all duration-300">
                            ← {{ __('Back') }}
                        </button>
                        <button type="submit" class="flex-1 py-4 px-4 border border-transparent rounded-2xl shadow-sm text-sm font-bold text-white bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 transition-all duration-300 transform hover:scale-[1.02]">
                            {{ __('Create Account') }} ✓
                        </button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Social Registration -->
        <div class="mt-6 bg-white/10 backdrop-blur-lg border border-white/20 rounded-3xl p-6">
            <div class="relative mb-4">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-600"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-transparent text-gray-400">{{ __('Or register with') }}</span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <a href="#" class="flex items-center justify-center px-4 py-3 border border-gray-600 rounded-xl shadow-sm text-sm font-medium text-gray-300 bg-white/10 hover:bg-white/20 transition-all duration-300">
                    <svg class="h-5 w-5 mr-2" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    Google
                </a>
                <a href="#" class="flex items-center justify-center px-4 py-3 border border-gray-600 rounded-xl shadow-sm text-sm font-medium text-gray-300 bg-white/10 hover:bg-white/20 transition-all duration-300">
                    <svg class="h-5 w-5 mr-2" fill="#1877F2" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                    Facebook
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = 3;

    // Role Card Selection
    document.querySelectorAll('.role-card').forEach(card => {
        card.addEventListener('click', function() {
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
        btn.addEventListener('click', function() {
            if (validateStep(currentStep)) {
                currentStep++;
                showStep(currentStep);
                updateProgressBar();
            }
        });
    });

    document.querySelectorAll('.prev-step').forEach(btn => {
        btn.addEventListener('click', function() {
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

    // Password Toggle
    const togglePassword = document.getElementById('togglePassword');
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

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
    }
});

// Console error logging
window.addEventListener('error', function(e) {
    console.error('JavaScript Error:', {
        message: e.message,
        filename: e.filename,
        lineno: e.lineno,
        colno: e.colno,
        error: e.error
    });
});

// Form submission error handling
document.getElementById('registrationForm').addEventListener('submit', function(e) {
    console.log('Form submission started');
    console.log('Form data:', new FormData(this));
    
    // Add loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    }
});
</script>

@endsection
