@extends('layouts.app')

@section('title', 'DeFi Loans')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">DeFi Loans</h1>
                    <p class="text-gray-600">Access decentralized finance lending options</p>
                </div>
                <div class="flex items-center space-x-3">
                    <button onclick="connectWallet()" class="bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-wallet mr-2"></i>
                        Connect Wallet
                    </button>
                    <a href="{{ route('investor.dashboard') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Wallet Status -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-wallet text-purple-600"></i>
                    </div>
                    <div>
                        <h3 class="font-medium text-gray-800">Wallet Status</h3>
                        <p id="walletAddress" class="text-sm text-gray-600">Not connected</p>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-sm text-gray-600">Available Balance</div>
                    <div id="walletBalance" class="text-2xl font-bold text-gray-800">$0.00</div>
                </div>
            </div>
        </div>

        <!-- DeFi Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-dollar-sign text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Borrowed</p>
                        <p class="text-2xl font-bold text-gray-800">${{ number_format($stats['total_borrowed'], 2) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-percentage text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Avg. Interest Rate</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['avg_interest_rate clinician'] }}%</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-coins text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Active Loans</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['active_loans'] }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-shield-alt text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Collateral Ratio</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['collateral_ratio'] }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Loans -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold text-gray-800">Available DeFi Loans</h2>
                <div class="flex items-center space-x-3">
                    <select class="px-3 py-2 border rounded-lg text-sm">
                        <option>All Protocols</option>
                        <option>Aave</option>
                        <option>Compound</option>
                        <option>MakerDAO</option>
                        <option>Uniswap</option>
                    </select>
                    <select class="px-3 py-2 border rounded-lg text-sm">
                        <option>All Collateral</option>
                        <option>ETH</option>
                        <option>USDC</option>
                        <option>WBTC</option>
                        <option>DAI</option>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($availableLoans as $loan)
                    <div class="border rounded-lg p-6 hover:shadow-lg transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="bg-purple-100 rounded-full p-2 mr-3">
                                    <i class="fas fa-coins text-purple-600"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">{{ $loan->protocol }}</h3>
                                    <p class="text-sm text-gray-600">{{ $loan->token_pair }}</p>
                                </div>
                            </div>
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                Available
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3 text-sm mb-4">
                            <div>
                                <span class="text-gray-600">APY:</span>
                                <span class="font-medium text-gray-800 ml-1">{{ $loan->apy }}%</span>
                            </div>
                            <div>
                                <span class="text-gray-600">LTV:</span>
                                <span class="font-medium text-gray-800 ml-1">{{ $loan->ltv }}%</span>
                            </div>
                            <div>
                                <span class="text-gray-600">TVL:</span>
                                <span class="font-medium text-gray-800 ml-1">${{ number_format($loan->tvl, 0) }}M</span>
                            </div>
                            <div>
                                <span class="text-gray-600">24h Volume:</span>
                                <span class="font-medium text-gray-800 ml-1">${{ number_format($loan->volume_24h, 0) }}M</span>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Utilization</span>
                                <span class="font-medium text-gray-800">{{ $loan->utilization }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $loan->utilization }}%"></div>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Min Borrow:</span>
                                <span class="font-medium text-gray-800">${{ number_format($loan->min_borrow, 0) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Max Borrow:</span>
                                <span class="font-medium text-gray-800">${{ number_format($loan->max_borrow, 0) }}</span>
                            </div>
                        </div>
                        
                        <div class="flex space-x-2 mt-4">
                            <button onclick="borrowLoan({{ $loan->id }})" class="flex-1 bg-purple-600 text-white px-3 py-2 rounded hover:bg-purple-700 transition-colors text-sm">
                                Borrow
                            </button>
                            <button onclick="viewDetails({{ $loan->id }})" class="flex-1 border border-gray-300 text-gray-700 px-3 py-2 rounded hover:bg-gray-50 transition-colors text-sm">
                                Details
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <i class="fas fa-coins text-6xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Loans Available</h3>
                        <p class="text-gray-500 mb-6">Connect your wallet to see available DeFi lending options.</p>
                        <button onclick="connectWallet()" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-colors">
                            <i class="fas fa-wallet mr-2"></i>
                            Connect Wallet
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Active Loans -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold text-gray-800">Your Active Loans</h2>
                <button onclick="viewAllLoans()" class="text-blue-600 hover:text-blue-800 text-sm">
                    View All →
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Protocol</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Collateral</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Borrowed</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">APY</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Health Factor</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($activeLoans as $loan)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-purple-100 rounded-full w-8 h-8 mr-3 flex items-center justify-center">
                                            <i class="fas fa-coins text-purple-600 text-xs"></i>
                                        </div>
                                        <div class="text-sm font-medium text-gray-900">{{ $loan->protocol }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $loan->collateral_amount }} {{ $loan->collateral_token }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ${{ number_format($loan->borrowed_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $loan->apy }}%
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-{{ $loan->health_factor >= 1.5 ? 'green' : ($loan->health_factor >= 1.2 ? 'yellow' : 'red') }}-600">
                                        {{ $loan->health_factor }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex space-x-2">
                                        <button onclick="repayLoan({{ $loan->id }})" class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-arrow-left"></i>
                                        </button>
                                        <button onclick="addCollateral({{ $loan->id }})" class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                        <button onclick="viewLoanDetails({{ $loan->id }})" class="text-gray-600 hover:text-gray-900">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <i class="fas fa-coins text-6xl text-gray-300 mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Active Loans</h3>
                                    <p class="text-gray-500 mb-4">Start borrowing from DeFi protocols.</p>
                                    <button onclick="exploreLoans()" class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition-colors">
                                        <i class="fas fa-search mr-2"></i>
                                        Explore Loans
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Loan Calculator -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Loan Calculator</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-medium text-gray-800 mb-4">Loan Parameters</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Collateral Amount</label>
                            <div class="relative">
                                <input type="number" id="collateralAmount" step="0.01" value="1000"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <select id="collateralToken" class="absolute right-0 top-0 h-full px-3 py-2 border-l border-gray-300 rounded-r-lg">
                                    <option>ETH</option>
                                    <option>USDC</option>
                                    <option>WBTC</option>
                                    <option>DAI</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">LTV Ratio</label>
                            <input type="number" id="ltvRatio" min="0" max="100" value="75" step="1"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Interest Rate (APY)</label>
                            <input type="number" id="interestRate" step="0.1" value="5.5"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Loan Term</label>
                            <select id="loanTerm" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
                                <option>1 Month</option>
                                <option>3 Months</option>
                                <option>6 Months</option>
                                <option>1 Year</option>
                                <option>Open</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h3 class="font-medium text-gray-800 mb-4">Loan Summary</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Borrowable Amount</span>
                                <span id="borrowableAmount" class="font-medium text-gray-800">$0.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Interest</span>
                                <span id="totalInterest" class="font-medium text-gray-800">$0.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Monthly Payment</span>
                                <span id="monthlyPayment" class="font-medium text-gray-800">$0.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Repayment</span>
                                <span id="totalRepayment" class="font-medium text-gray-800">$0.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Health Factor</span>
                                <span id="healthFactor" class="font-medium text-green-600">1.5</span>
                            </div>
                        </div>
                        
                        <button onclick="calculateLoan()" class="w-full bg-purple-600 text-white px-4 py-2 rounded-lg hover:bg-purple-700 transition-colors mt-4">
                            Calculate Loan
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Risk Warnings -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <div class="flex items-start">
                <div class="bg-yellow-100 rounded-full p-2 mr-3">
                    <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                </div>
                <div>
                    <h3 class="font-medium text-gray-800 mb-2">Important Risk Information</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• DeFi loans are subject to smart contract risks and market volatility</li>
                        <li>• Your collateral can be liquidated if the health factor drops below 1.0</li>
                        <li>• Interest rates are variable and may change based on market conditions</li>
                        <li>• Always maintain adequate collateral to avoid liquidation</li>
                        <li>• Do your own research before investing in DeFi protocols</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let walletConnected = false;
let walletAddress = '';
let walletBalance = 0;

function connectWallet() {
    // Simulate wallet connection
    walletConnected = true;
    walletAddress = '0x1234...5678';
    walletBalance = 15000.50;
    
    document.getElementById('walletAddress').textContent = walletAddress;
    document.getElementById('walletBalance').textContent = '$' + walletBalance.toFixed(2);
    
    // Update button
    event.target.textContent = 'Connected';
    event.target.classList.remove('bg-purple-600', 'hover:bg-purple-700');
    event.target.classList.add('bg-green-600', 'hover:bg-green-700');
    
    // Refresh available loans
    location.reload();
}

function borrowLoan(loanId) {
    if (!walletConnected) {
        alert('Please connect your wallet first');
        return;
    }
    window.location.href = '/investor/defi-loans/' + loanId + '/borrow';
}

function viewDetails(loanId) {
    window.location.href = '/investor/defi-loans/' + loanId;
}

function repayLoan(loanId) {
    window.location.href = '/investor/defi-loans/' + loanId + '/repay';
}

function addCollateral(loanId) {
    window.location.href = '/investor/defi-loans/' + loanId + '/collateral';
}

function viewLoanDetails(loanId) {
    window.location.href = '/investor/defi-loans/' + loanId + '/details';
}

function viewAllLoans() {
    window.location.href = '/investor/defi-loans/active';
}

function exploreLoans() {
    document.getElementById('availableLoans').scrollIntoView({ behavior: 'smooth' });
}

function calculateLoan() {
    const collateralAmount = parseFloat(document.getElementById('collateralAmount').value);
    const ltvRatio = parseFloat(document.getElementById('ltvRatio').value);
    const interestRate = parseFloat(document.getElementById('interestRate').value);
    const loanTerm = document.getElementById('loanTerm').value;
    
    // Calculate borrowable amount
    const borrowableAmount = collateralAmount * (ltvRatio / 100);
    
    // Calculate interest based on term
    let months = 1;
    if (loanTerm === '3 Months') months = 3;
    else if (loanTerm === '6 Months') months = 6;
    else if (loanTerm === '1 Year') months = 12;
    else if (loanTerm === 'Open') months = 12; // Assume 1 year for calculation
    
    const totalInterest = borrowableAmount * (interestRate / 100) * (months / 12);
    const totalRepayment = borrowableAmount + totalInterest;
    const monthlyPayment = loanTerm === 'Open' ? 0 : totalRepayment / months;
    
    // Calculate health factor (simplified)
    const healthFactor = 1.5; // Simplified calculation
    
    // Update UI
    document.getElementById('borrowableAmount').textContent = '$' + borrowableAmount.toFixed(2);
    document.getElementById('totalInterest').textContent = '$' + totalInterest.toFixed(2);
    document.getElementById('monthlyPayment').textContent = loanTerm === 'Open' ? 'Variable' : '$' + monthlyPayment.toFixed(2);
    document.getElementById('totalRepayment').textContent = '$' + totalRepayment.toFixed(2);
    document.getElementById('healthFactor').textContent = healthFactor.toFixed(2);
    
    // Update health factor color
    const healthElement = document.getElementById('healthFactor');
    healthElement.className = 'font-medium text-' + (healthFactor >= 1.5 ? 'green' : (healthFactor >= 1.2 ? 'yellow' : 'red')) + '-600';
}
</script>
@endsection
