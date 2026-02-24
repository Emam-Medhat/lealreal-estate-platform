@extends('layouts.app')

@section('title', __('auth.social_accounts'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">{{ __('auth.social_accounts') }}</h1>
                    <p class="text-gray-600">{{ __('auth.manage_connected_accounts') }}</p>
                </div>
                <a href="{{ route('profile.settings') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left mr-2"></i>
                    {{ __('auth.back_to_settings') }}
                </a>
            </div>
        </div>

        <!-- Connected Accounts -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ __('auth.connected_accounts') }}</h2>
            
            <div class="space-y-4">
                @forelse ($socialAccounts as $account)
                    <div class="flex items-center justify-between p-4 border rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-{{ $account->provider }}-100 rounded-full p-3 mr-4">
                                <i class="fab fa-{{ $account->provider }} text-{{ $account->provider }}-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-800">{{ ucfirst($account->provider) }}</h3>
                                <p class="text-sm text-gray-600">{{ $account->provider_id }}</p>
                                <p class="text-xs text-gray-500">{{ __('common.connected') }} {{ $account->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                {{ __('auth.connected') }}
                            </span>
                            <form action="{{ route('social.disconnect', $account->id) }}" method="POST" onsubmit="return confirm('{{ __('auth.disconnect_confirm') }}')">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-unlink"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <i class="fas fa-link text-4xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('auth.no_connected_accounts') }}</h3>
                        <p class="text-gray-500">{{ __('auth.connect_social_desc') }}</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Available Connections -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ __('auth.connect_new_account') }}</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if (!in_array('google', $socialAccounts->pluck('provider')->toArray()))
                    <a href="{{ route('social.redirect', 'google') }}" class="flex items-center p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="bg-red-100 rounded-full p-3 mr-4">
                            <i class="fab fa-google text-red-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-800">Google</h3>
                            <p class="text-sm text-gray-600">{{ __('auth.connect_provider', ['provider' => 'Google']) }}</p>
                        </div>
                    </a>
                @endif
                
                @if (!in_array('facebook', $socialAccounts->pluck('provider')->toArray()))
                    <a href="{{ route('social.redirect', 'facebook') }}" class="flex items-center p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="bg-blue-100 rounded-full p-3 mr-4">
                            <i class="fab fa-facebook text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-800">Facebook</h3>
                            <p class="text-sm text-gray-600">{{ __('auth.connect_provider', ['provider' => 'Facebook']) }}</p>
                        </div>
                    </a>
                @endif
                
                @if (!in_array('twitter', $socialAccounts->pluck('provider')->toArray()))
                    <a href="{{ route('social.redirect', 'twitter') }}" class="flex items-center p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="bg-blue-100 rounded-full p-3 mr-4">
                            <i class="fab fa-twitter text-blue-400 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-800">Twitter</h3>
                            <p class="text-sm text-gray-600">{{ __('auth.connect_provider', ['provider' => 'Twitter']) }}</p>
                        </div>
                    </a>
                @endif
                
                @if (!in_array('linkedin', $socialAccounts->pluck('provider')->toArray()))
                    <a href="{{ route('social.redirect', 'linkedin') }}" class="flex items-center p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="bg-blue-100 rounded-full p-3 mr-4">
                            <i class="fab fa-linkedin text-blue-700 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-800">LinkedIn</h3>
                            <p class="text-sm text-gray-600">{{ __('auth.connect_provider', ['provider' => 'LinkedIn']) }}</p>
                        </div>
                    </a>
                @endif
                
                @if (!in_array('github', $socialAccounts->pluck('provider')->toArray()))
                    <a href="{{ route('social.redirect', 'github') }}" class="flex items-center p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                        <div class="bg-gray-100 rounded-full p-3 mr-4">
                            <i class="fab fa-github text-gray-800 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-800">GitHub</h3>
                            <p class="text-sm text-gray-600">{{ __('auth.connect_provider', ['provider' => 'GitHub']) }}</p>
                        </div>
                    </a>
                @endif
            </div>
        </div>

        <!-- Security Information -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">{{ __('auth.security_info') }}</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>{{ __('auth.social_benefit_title') }}</p>
                        <ul class="list-disc list-inside mt-2 space-y-1">
                            <li>{{ __('auth.social_benefit_1') }}</li>
                            <li>{{ __('auth.social_benefit_2') }}</li>
                            <li>{{ __('auth.social_benefit_3') }}</li>
                        </ul>
                        <p class="mt-2">{{ __('auth.disconnect_any_time') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
