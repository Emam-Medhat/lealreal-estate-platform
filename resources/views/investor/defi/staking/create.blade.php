@extends('layouts.dashboard')

@section('title', 'Create DeFi Staking')

@section('content')
<style>
    .mr-64 { margin-right: 0px !important; }
</style>
<div class="min-h-screen bg-gradient-to-b from-slate-50 to-white">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">

            {{-- ===== Header ===== --}}
            <div class="bg-gradient-to-r from-purple-600 via-indigo-600 to-blue-700 rounded-2xl shadow-xl p-6 md:p-8 mb-6 text-white overflow-hidden relative">
                <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(circle at 15% 25%, rgba(255,255,255,0.4), transparent 40%), radial-gradient(circle at 85% 20%, rgba(255,255,255,0.3), transparent 45%);"></div>
                <div class="flex items-center gap-4 relative">
                    <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                        <i class="fas fa-plus text-white text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-bold">Create DeFi Staking Position</h1>
                        <p class="text-purple-100 text-sm md:text-base mt-1">Add a new DeFi staking position to earn rewards</p>
                    </div>
                </div>
            </div>

            {{-- ===== Form ===== --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                <form method="POST" action="{{ route('investor.defi.staking.store') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- Basic Information --}}
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-info-circle text-purple-600"></i>
                            Basic Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Staking Purpose *</label>
                                <input type="text" name="staking_purpose" required
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                       placeholder="e.g., ETH Staking for Passive Income">
                                @error('staking_purpose')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Protocol Name *</label>
                                <input type="text" name="protocol_name" required
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                       placeholder="e.g., Lido Finance">
                                @error('protocol_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Token Information --}}
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-coins text-purple-600"></i>
                            Token Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Token Address *</label>
                                <input type="text" name="token_address" required
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                       placeholder="0x...">
                                @error('token_address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Token Symbol *</label>
                                <input type="text" name="token_symbol" required maxlength="10"
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                       placeholder="e.g., ETH">
                                @error('token_symbol')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Staking Details --}}
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-lock text-purple-600"></i>
                            Staking Details
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Amount Staked *</label>
                                <input type="number" name="amount_staked" required step="0.00000001" min="0.00000001" max="1000000"
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                       placeholder="0.0">
                                @error('amount_staked')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Staking Period (Days) *</label>
                                <input type="number" name="staking_period_days" required min="1" max="3650"
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                       placeholder="365">
                                @error('staking_period_days')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">APY Rate (%) *</label>
                                <input type="number" name="apy_rate" required step="0.1" min="0" max="100"
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                       placeholder="5.5">
                                @error('apy_rate')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Rewards Configuration --}}
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-gift text-purple-600"></i>
                            Rewards Configuration
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Reward Frequency *</label>
                                <select name="reward_frequency" required
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    <option value="">Select Frequency</option>
                                    <option value="continuous">Continuous</option>
                                    <option value="daily">Daily</option>
                                    <option value="weekly">Weekly</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                </select>
                                @error('reward_frequency')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Lockup Period *</label>
                                <select name="lockup_period" required
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    <option value="">Select Period</option>
                                    <option value="none">None</option>
                                    <option value="flexible">Flexible</option>
                                    <option value="30_days">30 Days</option>
                                    <option value="60_days">60 Days</option>
                                    <option value="90_days">90 Days</option>
                                    <option value="180_days">180 Days</option>
                                    <option value="1_year">1 Year</option>
                                </select>
                                @error('lockup_period')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Blockchain Information --}}
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-network-wired text-purple-600"></i>
                            Blockchain Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Blockchain Network *</label>
                                <select name="blockchain_network" required
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    <option value="">Select Network</option>
                                    <option value="ethereum">Ethereum</option>
                                    <option value="polygon">Polygon</option>
                                    <option value="bnb_chain">BNB Chain</option>
                                    <option value="avalanche">Avalanche</option>
                                    <option value="arbitrum">Arbitrum</option>
                                    <option value="solana">Solana</option>
                                </select>
                                @error('blockchain_network')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Smart Contract Address *</label>
                                <input type="text" name="smart_contract_address" required
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                       placeholder="0x...">
                                @error('smart_contract_address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Risk Assessment --}}
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-shield-alt text-purple-600"></i>
                            Risk Assessment
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Risk Level *</label>
                                <select name="risk_level" required
                                        class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    <option value="">Select Risk Level</option>
                                    <option value="low">Low Risk</option>
                                    <option value="medium">Medium Risk</option>
                                    <option value="high">High Risk</option>
                                    <option value="critical">Critical Risk</option>
                                </select>
                                @error('risk_level')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Early Withdrawal Penalty (%)</label>
                                <input type="number" name="early_withdrawal_penalty" step="0.1" min="0" max="100"
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                       placeholder="0.0">
                                @error('early_withdrawal_penalty')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Optional Fields -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-cog text-purple-600"></i>
                            Optional Settings
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Staking Amount</label>
                                <input type="number" name="minimum_staking_amount" step="0.00000001" min="0"
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                       placeholder="0.0">
                                @error('minimum_staking_amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Maximum Staking Amount</label>
                                <input type="number" name="maximum_staking_amount" step="0.00000001" min="0"
                                       class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                       placeholder="0.0">
                                @error('maximum_staking_amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <label class="flex items-center gap-3">
                                <input type="checkbox" name="auto_compound" value="1"
                                       class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                <span class="text-sm font-medium text-gray-700">Enable Auto-Compound</span>
                            </label>
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                        <textarea name="notes" rows="3" maxlength="1000"
                                  class="w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                  placeholder="Additional notes about this staking position..."></textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Documents --}}
                    <div class="mb-8">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Documents</label>
                        <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-purple-400 transition-colors">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-3"></i>
                            <p class="text-sm text-gray-600 mb-2">Click to upload or drag and drop</p>
                            <p class="text-xs text-gray-500">PDF, DOC, DOCX, JPG, JPEG, PNG (MAX. 10MB per file)</p>
                            <input type="file" name="documents[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   class="hidden" id="documents-input">
                            <button type="button" onclick="document.getElementById('documents-input').click()"
                                    class="mt-3 px-4 py-2 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition-colors text-sm font-medium">
                                Choose Files
                            </button>
                        </div>
                        @error('documents.*')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Form Actions --}}
                    <div class="flex flex-col sm:flex-row gap-4">
                        <button type="submit"
                                class="flex-1 bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-3 rounded-xl hover:from-purple-700 hover:to-indigo-700 transition-all shadow-sm hover:shadow-md font-medium">
                            <i class="fas fa-save mr-2"></i> Create Staking Position
                        </button>
                        <a href="{{ route('investor.defi.staking.index') }}"
                           class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-colors font-medium">
                            <i class="fas fa-times mr-2"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
@endsection
