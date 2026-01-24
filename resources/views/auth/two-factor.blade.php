@extends('layouts.auth')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                {{ __('Two-Factor Authentication') }}
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                {{ __('Enter the 6-digit code from your authenticator app') }}
            </p>
        </div>

        <form class="mt-8 space-y-6" action="{{ route('two-factor.verify') }}" method="POST">
            @csrf

            @if ($errors->any())
                <div class="rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                {{ __('Invalid authentication code') }}
                            </h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>{{ __('The code you entered is invalid. Please try again.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div>
                <label for="code" class="sr-only">{{ __('Authentication Code') }}</label>
                <div class="mt-1">
                    <input id="code" name="code" type="text" inputmode="numeric" pattern="[0-9]*" maxlength="6" required
                           class="appearance-none relative block w-full px-3 py-3 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm text-center text-2xl tracking-widest"
                           placeholder="000000"
                           autocomplete="one-time-code"
                           autofocus>
                </div>
                @error('code')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember_device" name="remember_device" type="checkbox" 
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="remember_device" class="ml-2 block text-sm text-gray-900">
                        {{ __('Trust this device for 30 days') }}
                    </label>
                </div>
            </div>

            <div>
                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-blue-500 group-hover:text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </span>
                    {{ __('Verify and Continue') }}
                </button>
            </div>
        </form>

        <div class="mt-6">
            <div class="relative">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-gray-50 text-gray-500">{{ __('Having trouble?') }}</span>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 gap-3">
                <a href="{{ route('two-factor.backup') }}" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                    {{ __('Use backup code') }}
                </a>
                
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    @method('POST')
                    <button type="submit" class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        {{ __('Cancel and sign out') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-focus and auto-submit when 6 digits are entered
document.getElementById('code').addEventListener('input', function(e) {
    const value = e.target.value.replace(/\D/g, '');
    e.target.value = value;
    
    if (value.length === 6) {
        // Auto-submit after a short delay to allow user to see the full code
        setTimeout(() => {
            e.target.form.submit();
        }, 500);
    }
});

// Paste handling
document.getElementById('code').addEventListener('paste', function(e) {
    e.preventDefault();
    const pastedData = (e.clipboardData || window.clipboardData).getData('text');
    const numbers = pastedData.replace(/\D/g, '').slice(0, 6);
    e.target.value = numbers;
    
    if (numbers.length === 6) {
        setTimeout(() => {
            e.target.form.submit();
        }, 500);
    }
});
</script>
@endsection
