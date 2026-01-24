@extends('layouts.auth')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-blue-100">
                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                {{ __('Verify your email address') }}
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                {{ __('Before proceeding, please check your email for a verification link.') }}
            </p>
        </div>

        @if (session('verified'))
            <div class="rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ __('Your email has been verified successfully!') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if (session('status'))
            <div class="rounded-md bg-blue-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-blue-800">
                            {{ session('status') }}
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white shadow rounded-lg p-6">
            <div class="space-y-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-gray-700">
                            {{ __('A verification link has been sent to your email address:') }}
                        </p>
                        <p class="text-sm font-medium text-gray-900">{{ auth()->user()->email }}</p>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <p class="text-sm text-gray-600 mb-4">
                        {{ __("Didn't receive the email?") }}
                    </p>
                    
                    <form action="{{ route('verification.send') }}" method="POST" class="space-y-3">
                        @csrf
                        
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            {{ __('Resend Verification Email') }}
                        </button>
                    </form>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <form action="{{ route('logout') }}" method="POST" class="space-y-3">
                        @csrf
                        @method('POST')
                        
                        <button type="submit" class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            {{ __('Sign Out') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="{{ route('dashboard') }}" class="text-sm text-blue-600 hover:text-blue-500">
                {{ __('Continue to dashboard') }}
            </a>
        </div>
    </div>
</div>
@endsection
