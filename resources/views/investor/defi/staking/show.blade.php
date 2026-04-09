@extends('layouts.dashboard')

@section('title', 'DeFi Staking Details')

@section('content')
<style>
    .mr-64 { margin-right: 0px !important; }
</style>
<div class="min-h-screen bg-gradient-to-b from-slate-50 to-white">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-6xl mx-auto">

            {{-- ===== Header ===== --}}
            <div class="bg-gradient-to-r from-purple-600 via-indigo-600 to-blue-700 rounded-2xl shadow-xl p-6 md:p-8 mb-6 text-white overflow-hidden relative">
                <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 15% 25%, rgba(255,255,255,0.4), transparent 40%), radial-gradient(circle at 85% 20%, rgba(255,255,255,0.3), transparent 45%);"></div>
                <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 relative">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                                <i class="fas fa-coins text-white text-lg"></i>
                            </div>
                            <h1 class="text-2xl md:text-3xl font-bold">{{ $staking->staking_purpose }}</h1>
                        </div>
                        <p class="text-purple-100 text-sm md:text-base">{{ $staking->token_symbol }} &bull; {{ $staking->protocol_name }}</p>
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold border bg-white/20 border-white/30 text-white">
                            {{ ucfirst($staking->status) }}
                        </span>
                        <a href="{{ route('investor.defi.staking.index') }}"
                           class="bg-white/20 hover:bg-white/30 text-white px-5 py-2.5 rounded-xl transition-all duration-300 flex items-center gap-2 text-sm font-medium">
                            <i class="fas fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>

            {{-- ===== Main Content Grid ===== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                {{-- Left Column - Main Info --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Overview Card --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-chart-line text-purple-600"></i>
                            Overview
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="text-center">
                                <p class="text-xs text-gray-500 mb-1">Amount Staked</p>
                                <p class="text-lg font-bold text-gray-900">{{ number_format($staking->amount_staked, 4) }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-500 mb-1">APY Rate</p>
                                <p class="text-lg font-bold text-emerald-600">{{ number_format($staking->apy_rate, 1) }}%</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-500 mb-1">Rewards Earned</p>
                                <p class="text-lg font-bold text-blue-600">{{ number_format($staking->total_rewards_earned, 4) }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-xs text-gray-500 mb-1">Current Value</p>
                                <p class="text-lg font-bold text-purple-600">{{ number_format($staking->current_value, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Progress Card --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-clock text-purple-600"></i>
                            Staking Progress
                        </h3>
                        <div class="space-y-4">
                            <div>
                                <div class="flex justify-between text-sm text-gray-600 mb-2">
                                    <span>Days Staked: {{ $staking->created_at->diffInDays(now()) }}</span>
                                    <span>Total Period: {{ $staking->staking_period_days }} days</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-3">
                                    @php
                                        $progress = $staking->staking_period_days > 0
                                            ? min(($staking->created_at->diffInDays(now()) / $staking->staking_period_days) * 100, 100)
                                            : 0;
                                    @endphp
                                    <div class="h-3 rounded-full bg-gradient-to-r from-purple-500 to-indigo-500"
                                         style="width: {{ $progress }}%"></div>
                                </div>
                                <p class="text-right text-sm font-semibold text-purple-600 mt-1">{{ number_format($progress, 0) }}% Complete</p>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500">Start Date</p>
                                    <p class="font-semibold text-gray-900">{{ $staking->created_at->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500">Unstake Date</p>
                                    <p class="font-semibold text-gray-900">{{ $staking->unstake_date->format('M d, Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Actions Card --}}
                    @if($staking->status === 'active')
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                <i class="fas fa-tools text-purple-600"></i>
                                Actions
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <button onclick="openUnstakeModal()"
                                        class="flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-xl hover:from-red-700 hover:to-pink-700 transition-all shadow-sm hover:shadow-md font-medium">
                                    <i class="fas fa-unlock"></i> Unstake
                                </button>
                                <button onclick="openClaimRewardsModal()"
                                        class="flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white rounded-xl hover:from-emerald-700 hover:to-teal-700 transition-all shadow-sm hover:shadow-md font-medium">
                                    <i class="fas fa-gift"></i> Claim Rewards
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Right Column - Details --}}
                <div class="space-y-6">
                    {{-- Token Info --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-coins text-purple-600"></i>
                            Token Information
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs text-gray-500">Token Symbol</p>
                                <p class="font-semibold text-gray-900">{{ $staking->token_symbol }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Token Address</p>
                                <p class="font-mono text-xs text-gray-600 break-all">{{ $staking->token_address }}</p>
                            </div>
                            @if($staking->rewards_token_address)
                                <div>
                                    <p class="text-xs text-gray-500">Rewards Token</p>
                                    <p class="font-semibold text-gray-900">{{ $staking->rewards_token_symbol ?? 'Unknown' }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Protocol Info --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-shield-alt text-purple-600"></i>
                            Protocol Details
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs text-gray-500">Protocol Name</p>
                                <p class="font-semibold text-gray-900">{{ $staking->protocol_name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Blockchain Network</p>
                                <p class="font-semibold text-gray-900">{{ str_replace('_', ' ', $staking->blockchain_network) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Smart Contract</p>
                                <p class="font-mono text-xs text-gray-600 break-all">{{ $staking->smart_contract_address }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Risk Level</p>
                                <span class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-semibold border
                                    @if($staking->risk_level === 'low') bg-emerald-100 text-emerald-700 border-emerald-200
                                    @elseif($staking->risk_level === 'medium') bg-amber-100 text-amber-700 border-amber-200
                                    @elseif($staking->risk_level === 'high') bg-red-100 text-red-700 border-red-200
                                    @else bg-rose-100 text-rose-800 border-rose-200 @endif">
                                    {{ ucfirst($staking->risk_level) }} Risk
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Settings --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-cog text-purple-600"></i>
                            Settings
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs text-gray-500">Reward Frequency</p>
                                <p class="font-semibold text-gray-900">{{ ucfirst($staking->reward_frequency) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Lockup Period</p>
                                <p class="font-semibold text-gray-900">{{ str_replace('_', ' ', $staking->lockup_period) }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Auto-Compound</p>
                                <p class="font-semibold text-gray-900">{{ $staking->auto_compound ? 'Enabled' : 'Disabled' }}</p>
                            </div>
                            @if($staking->early_withdrawal_penalty > 0)
                                <div>
                                    <p class="text-xs text-gray-500">Early Withdrawal Penalty</p>
                                    <p class="font-semibold text-gray-900">{{ number_format($staking->early_withdrawal_penalty, 1) }}%</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notes Section --}}
            @if($staking->notes)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="fas fa-sticky-note text-purple-600"></i>
                        Notes
                    </h3>
                    <p class="text-gray-700">{{ $staking->notes }}</p>
                </div>
            @endif

        </div>
    </div>
</div>

{{-- Unstake Modal --}}
<div id="unstakeModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Unstake Tokens</h3>
            <form id="unstakeForm">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount to Unstake</label>
                    <input type="number" name="amount_to_unstake" required step="0.00000001" min="0" max="{{ $staking->amount_staked }}"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="0.0">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason (Optional)</label>
                    <textarea name="reason" rows="3"
                              class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                              placeholder="Reason for unstaking..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-red-600 to-pink-600 text-white px-4 py-2.5 rounded-xl hover:from-red-700 hover:to-pink-700 transition-all font-medium">
                        Confirm Unstake
                    </button>
                    <button type="button" onclick="closeUnstakeModal()" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Claim Rewards Modal --}}
<div id="claimRewardsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full mx-4">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Claim Rewards</h3>
            <form id="claimRewardsForm">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Available Rewards</label>
                    <input type="number" name="amount_to_claim" required step="0.00000001" min="0" max="{{ $staking->total_rewards_earned }}"
                           class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                           placeholder="0.0" value="{{ $staking->total_rewards_earned }}">
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-emerald-600 to-teal-600 text-white px-4 py-2.5 rounded-xl hover:from-emerald-700 hover:to-teal-700 transition-all font-medium">
                        Claim Rewards
                    </button>
                    <button type="button" onclick="closeClaimRewardsModal()" class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openUnstakeModal() {
    document.getElementById('unstakeModal').style.display = 'flex';
}

function closeUnstakeModal() {
    document.getElementById('unstakeModal').style.display = 'none';
}

function openClaimRewardsModal() {
    document.getElementById('claimRewardsModal').style.display = 'flex';
}

function closeClaimRewardsModal() {
    document.getElementById('claimRewardsModal').style.display = 'none';
}

// Handle unstake form submission
document.getElementById('unstakeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('{{ route("investor.defi.staking.unstake", $staking) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error occurred');
    });
});

// Handle claim rewards form submission
document.getElementById('claimRewardsForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    
    fetch('{{ route("investor.defi.staking.claim.rewards", $staking) }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error occurred');
    });
});
</script>
@endsection
