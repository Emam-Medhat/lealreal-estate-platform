@extends('layouts.dashboard')

@section('title', 'DeFi Staking')

@section('content')
<style>
    .mr-64 { margin-right: 0px !important; }
</style>
<div class="min-h-screen bg-gradient-to-b from-slate-50 to-white">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-7xl mx-auto">

            {{-- ===== Hero Header ===== --}}
            <div class="bg-gradient-to-r from-purple-600 via-indigo-600 to-blue-700 rounded-2xl shadow-xl p-6 md:p-8 mb-6 text-white overflow-hidden relative">
                <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 15% 25%, rgba(255,255,255,0.4), transparent 40%), radial-gradient(circle at 85% 20%, rgba(255,255,255,0.3), transparent 45%);"></div>
                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 relative">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                                <i class="fas fa-coins text-white text-lg"></i>
                            </div>
                            <h1 class="text-2xl md:text-3xl font-bold">DeFi Staking</h1>
                        </div>
                        <p class="text-purple-100 text-sm md:text-base">Manage your DeFi staking positions and earn rewards</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('investor.defi.staking.create') }}"
                           class="bg-white/20 hover:bg-white/30 text-white px-5 py-2.5 rounded-xl transition-all duration-300 flex items-center gap-2 text-sm font-medium">
                            <i class="fas fa-plus"></i> New Staking
                        </a>
                    </div>
                </div>
            </div>

            {{-- ===== Alerts ===== --}}
            @if(session('success'))
                <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-xl flex items-center gap-3">
                    <i class="fas fa-check-circle text-green-500 text-lg"></i>
                    <p class="text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            @endif
            @if(session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-xl flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-red-500 text-lg"></i>
                    <p class="text-red-700 font-medium">{{ session('error') }}</p>
                </div>
            @endif

            {{-- ===== Stats ===== --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Total Positions</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $staking->total() }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-purple-100 to-indigo-100 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-coins text-purple-600 text-lg"></i>
                        </div>
                    </div>
                    <div class="mt-3 h-1 bg-gradient-to-r from-purple-400 to-indigo-500 rounded-full opacity-30 group-hover:opacity-60 transition-opacity"></div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Active Positions</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $staking->where('status', 'active')->count() }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-100 to-teal-100 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-play-circle text-emerald-600 text-lg"></i>
                        </div>
                    </div>
                    <div class="mt-3 h-1 bg-gradient-to-r from-emerald-400 to-teal-500 rounded-full opacity-30 group-hover:opacity-60 transition-opacity"></div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Total Staked</p>
                            <p class="text-3xl font-bold text-gray-900">{{ number_format($staking->sum('amount_staked'), 2) }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-lock text-blue-600 text-lg"></i>
                        </div>
                    </div>
                    <div class="mt-3 h-1 bg-gradient-to-r from-blue-400 to-indigo-500 rounded-full opacity-30 group-hover:opacity-60 transition-opacity"></div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-lg transition-all duration-300 group">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Total Rewards</p>
                            <p class="text-3xl font-bold text-gray-900">{{ number_format($staking->sum('total_rewards_earned'), 2) }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-100 to-orange-100 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <i class="fas fa-gift text-amber-600 text-lg"></i>
                        </div>
                    </div>
                    <div class="mt-3 h-1 bg-gradient-to-r from-amber-400 to-orange-500 rounded-full opacity-30 group-hover:opacity-60 transition-opacity"></div>
                </div>
            </div>

            {{-- ===== Filters ===== --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
                <form method="GET" action="{{ url()->current() }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div class="md:col-span-2 relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                               placeholder="Search by purpose or token symbol..."
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
                    </div>
                    <div>
                        <select name="status" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <select name="blockchain_network" class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 text-sm">
                            <option value="">All Networks</option>
                            <option value="ethereum" {{ request('blockchain_network') == 'ethereum' ? 'selected' : '' }}>Ethereum</option>
                            <option value="polygon" {{ request('blockchain_network') == 'polygon' ? 'selected' : '' }}>Polygon</option>
                            <option value="bnb_chain" {{ request('blockchain_network') == 'bnb_chain' ? 'selected' : '' }}>BNB Chain</option>
                            <option value="avalanche" {{ request('blockchain_network') == 'avalanche' ? 'selected' : '' }}>Avalanche</option>
                            <option value="arbitrum" {{ request('blockchain_network') == 'arbitrum' ? 'selected' : '' }}>Arbitrum</option>
                            <option value="solana" {{ request('blockchain_network') == 'solana' ? 'selected' : '' }}>Solana</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit"
                                class="flex-1 bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-4 py-2.5 rounded-xl hover:from-purple-700 hover:to-indigo-700 transition-all shadow-sm hover:shadow-md text-sm font-medium">
                            <i class="fas fa-search mr-1"></i> Filter
                        </button>
                        <a href="{{ url()->current() }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-colors flex items-center justify-center">
                            <i class="fas fa-redo text-sm"></i>
                        </a>
                    </div>
                </form>
            </div>

            {{-- ===== Staking Grid ===== --}}
            @if($staking->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
                    @foreach($staking as $stake)
                        @php
                            $rawRisk = $stake->getRawOriginal('risk_level') ?? 'medium';
                            $rawNetwork = $stake->getRawOriginal('blockchain_network') ?? 'ethereum';
                            $rawStatus = $stake->getRawOriginal('status') ?? 'active';

                            $progress = $stake->staking_period_days > 0
                                ? min(($stake->created_at->diffInDays(now()) / $stake->staking_period_days) * 100, 100)
                                : 0;

                            $statusColors = [
                                'active' => ['bg-emerald-100 text-emerald-700 border-emerald-200', 'text-emerald-600'],
                                'completed' => ['bg-blue-100 text-blue-700 border-blue-200', 'text-blue-600'],
                                'cancelled' => ['bg-red-100 text-red-700 border-red-200', 'text-red-600'],
                            ];
                            [$statusBadge, $statusText] = $statusColors[$rawStatus] ?? ['bg-gray-100 text-gray-700 border-gray-200', 'text-gray-600'];

                            $networkIcons = [
                                'ethereum' => 'fab fa-ethereum',
                                'polygon' => 'fas fa-shapes',
                                'bnb_chain' => 'fas fa-coins',
                                'avalanche' => 'fas fa-mountain',
                                'arbitrum' => 'fas fa-infinity',
                                'solana' => 'fas fa-sun',
                            ];
                            $networkIcon = $networkIcons[$rawNetwork] ?? 'fas fa-link';
                        @endphp

                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">
                            <div class="h-1.5 bg-gradient-to-r from-purple-500 to-indigo-500"></div>

                            <div class="p-6 flex flex-col flex-1">
                                {{-- Header --}}
                                <div class="flex items-start justify-between gap-3 mb-4">
                                    <div class="flex items-start gap-3">
                                        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-purple-100 to-indigo-100 flex items-center justify-center flex-shrink-0">
                                            <i class="fas {{ $networkIcon }} text-purple-600"></i>
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-gray-900 text-base leading-snug">
                                                {{ $stake->getRawOriginal('staking_purpose') }}
                                            </h3>
                                            <p class="text-xs text-gray-500 mt-0.5 capitalize">
                                                {{ $stake->getRawOriginal('token_symbol') }}
                                                &bull; {{ str_replace('_', ' ', $rawNetwork) }}
                                            </p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold border flex-shrink-0 {{ $statusBadge }}">
                                        {{ ucfirst($rawStatus) }}
                                    </span>
                                </div>

                                {{-- Key Metrics --}}
                                <div class="grid grid-cols-2 gap-3 mb-4">
                                    <div class="bg-purple-50 rounded-xl p-3 text-center">
                                        <p class="text-xs text-gray-500 mb-0.5">Amount Staked</p>
                                        <p class="text-sm font-bold text-purple-700">{{ number_format($stake->amount_staked, 4) }}</p>
                                    </div>
                                    <div class="bg-emerald-50 rounded-xl p-3 text-center">
                                        <p class="text-xs text-gray-500 mb-0.5">APY Rate</p>
                                        <p class="text-sm font-bold text-emerald-700">{{ number_format($stake->apy_rate, 1) }}%</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-3 mb-4">
                                    <div class="bg-blue-50 rounded-xl p-3 text-center">
                                        <p class="text-xs text-gray-500 mb-0.5">Rewards Earned</p>
                                        <p class="text-sm font-bold text-blue-700">{{ number_format($stake->total_rewards_earned, 4) }}</p>
                                    </div>
                                    <div class="bg-amber-50 rounded-xl p-3 text-center">
                                        <p class="text-xs text-gray-500 mb-0.5">Current Value</p>
                                        <p class="text-sm font-bold text-amber-700">{{ number_format($stake->current_value, 2) }}</p>
                                    </div>
                                </div>

                                {{-- Progress --}}
                                <div class="mb-4">
                                    <div class="flex justify-between text-xs text-gray-500 mb-1.5">
                                        <span>{{ $stake->created_at->diffInDays(now()) }} days staked</span>
                                        <span>{{ $stake->staking_period_days }} days total</span>
                                    </div>
                                    <div class="w-full bg-gray-100 rounded-full h-2">
                                        <div class="h-2 rounded-full bg-gradient-to-r from-purple-500 to-indigo-500"
                                             style="width: {{ $progress }}%"></div>
                                    </div>
                                    <p class="text-right text-xs font-semibold {{ $statusText }} mt-1">{{ number_format($progress, 0) }}% complete</p>
                                </div>

                                {{-- Protocol Info --}}
                                <div class="flex items-center justify-between text-xs text-gray-400 mb-4">
                                    <span><i class="fas fa-shield-alt mr-1"></i>{{ $stake->getRawOriginal('protocol_name') }}</span>
                                    <span class="font-semibold text-{{ $rawRisk == 'low' ? 'emerald' : ($rawRisk == 'medium' ? 'amber' : 'red') }}-600">{{ ucfirst($rawRisk) }} Risk</span>
                                </div>

                                {{-- CTA --}}
                                <div class="mt-auto pt-2">
                                    <a href="{{ route('investor.defi.staking.show', $stake) }}"
                                       class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl hover:from-purple-700 hover:to-indigo-700 transition-all shadow-sm hover:shadow-md text-sm font-semibold">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-6 py-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <p class="text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-1 text-gray-400"></i>
                            Showing {{ $staking->firstItem() }}–{{ $staking->lastItem() }} of <strong>{{ $staking->total() }}</strong> staking positions
                        </p>
                        <div>{{ $staking->withQueryString()->links() }}</div>
                    </div>
                </div>

            @else
                {{-- Empty State --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 py-20 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-purple-100 to-indigo-100 rounded-2xl mb-5 shadow-inner">
                        <i class="fas fa-coins text-purple-400 text-3xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Staking Positions Found</h3>
                    <p class="text-gray-400 mb-6 max-w-sm mx-auto">
                        @if(request()->hasAny(['search', 'status', 'blockchain_network']))
                            No staking positions match your filters. Try adjusting your search criteria.
                        @else
                            You haven't created any DeFi staking positions yet. Start earning rewards on your crypto assets!
                        @endif
                    </p>
                    @if(request()->hasAny(['search', 'status', 'blockchain_network']))
                        <a href="{{ url()->current() }}"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl hover:from-purple-700 hover:to-indigo-700 transition-all shadow-md hover:shadow-lg font-medium">
                            <i class="fas fa-redo"></i> Clear Filters
                        </a>
                    @else
                        <a href="{{ route('investor.defi.staking.create') }}"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl hover:from-purple-700 hover:to-indigo-700 transition-all shadow-md hover:shadow-lg font-medium">
                            <i class="fas fa-plus"></i> Create First Staking Position
                        </a>
                    @endif
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
