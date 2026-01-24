@extends('layouts.auth')

@section('title', 'Admin Login')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-80 h-80 bg-purple-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
            <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-blue-500 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-indigo-500 rounded-full mix-blend-multiply filter blur-xl opacity-10 animate-pulse"></div>
        </div>

        <!-- Header Section -->
        <div class="text-center mb-8 relative">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-2xl shadow-2xl mb-6 transform hover:scale-105 transition-transform duration-300">
                <i class="fas fa-user-shield text-white text-3xl"></i>
            </div>
            <h1 class="text-4xl font-bold text-white mb-4 tracking-tight">
                {{ __('Admin Login') }}
            </h1>
            <p class="text-lg text-gray-300 mb-6">
                {{ __('Enter your admin credentials') }}
            </p>
            <div class="flex items-center justify-center space-x-2 text-gray-400">
                <span>{{ __('Not an admin?') }}</span>
                <a href="{{ route('login') }}" class="text-blue-400 hover:text-blue-300 font-semibold transition-colors duration-200">
                    {{ __('User Login') }}
                </a>
                <span class="text-gray-500">|</span>
                <a href="{{ route('register') }}" class="text-purple-400 hover:text-purple-300 font-semibold transition-colors duration-200">
                    {{ __('Register') }}
                </a>
            </div>
        </div>

        <!-- Admin Login Form -->
        <form class="bg-white/10 backdrop-blur-lg border border-white/20 rounded-3xl shadow-2xl overflow-hidden" action="{{ route('admin.login.post') }}" method="POST">
            @csrf

            <!-- Progress Bar -->
            <div class="h-1 bg-gradient-to-r from-purple-500 to-indigo-500">
                <div class="h-full bg-white/30" style="width: 100%"></div>
            </div>

            <div class="p-8">
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
                                    {{ __('Login Failed') }}
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

                <!-- Email/Username -->
                <div class="mb-6">
                    <label for="login" class="block text-sm font-semibold text-gray-200 mb-2">
                        {{ __('Email or Username') }} <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input id="login" name="login" type="text" required
                               class="block w-full pl-10 pr-4 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-400 focus:border-purple-400 focus:bg-white/20 transition-all duration-300"
                               value="{{ old('login') }}"
                               placeholder="admin@example.com">
                    </div>
                    @error('login')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-semibold text-gray-200 mb-2">
                        {{ __('Password') }} <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input id="password" name="password" type="password" required
                               class="block w-full pl-10 pr-12 py-4 bg-white/10 border border-white/20 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-purple-400 focus:border-purple-400 focus:bg-white/20 transition-all duration-300"
                               placeholder="Enter your password">
                        <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-300">
                            <svg id="eyeIcon" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <input type="hidden" name="remember" value="false">
                        <input id="remember" name="remember" type="checkbox" value="true"
                               class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded bg-white/10">
                        <label for="remember" class="ml-2 block text-sm text-gray-300">
                            {{ __('Remember me') }}
                        </label>
                    </div>
                    <a href="{{ route('password.request') }}" class="text-sm text-purple-400 hover:text-purple-300 transition-colors duration-200">
                        {{ __('Forgot password?') }}
                    </a>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-4 px-4 border border-transparent rounded-2xl shadow-sm text-sm font-bold text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 transition-all duration-300 transform hover:scale-[1.02]">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    {{ __('Sign In as Admin') }}
                </button>
            </div>
        </form>

        <!-- Security Notice -->
        <div class="mt-6 bg-yellow-500/10 border border-yellow-500/20 rounded-xl p-4">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-yellow-400 mt-1 mr-3"></i>
                <div class="text-sm text-gray-300">
                    <p class="font-semibold text-yellow-300 mb-1">{{ __('Security Notice') }}</p>
                    <p>{{ __('This login is for administrators only. Unauthorized access attempts will be logged and may result in account suspension.') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
</script>

@endsection
