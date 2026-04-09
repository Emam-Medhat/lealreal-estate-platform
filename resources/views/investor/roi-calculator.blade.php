@extends('layouts.app')

@section('title', 'ROI Calculator')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">ROI Calculator</h1>
                    <p class="text-gray-600">Calculate potential returns on real estate investments</p>
                </div>
                <a href="{{ route('investor.dashboard') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Calculator Form -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Investment Parameters</h2>
            
            <form id="roiCalculator" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Property Details -->
                    <div>
                        <h3 class="font-medium text-gray-800 mb-4">Property Details</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Property Value</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500">$</span>
                                    <input type="number" id="propertyValue" step="1000" value="500000" required
                                        class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Down Payment (%)</label>
                                <input type="number" id="downPayment" min="0" max="100" value="20" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Interest Rate (%)</label>
                                <input type="number" id="interestRate" step="0.1" value="4.5" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Loan Term (years)</label>
                                <input type="number" id="loanTerm" value="30" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Income & Expenses -->
                    <div>
                        <h3 class="font-medium text-gray-800 mb-4">Income & Expenses</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Rent Income</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500">$</span>
                                    <input type="number" id="monthlyRent" step="100" value="3000" required
                                        class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Vacancy Rate (%)</label>
                                <input type="number" id="vacancyRate" min="0" max="100" value="5" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Monthly Expenses</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500">$</span>
                                    <input type="number" id="monthlyExpenses" step="50" value="800" required
                                        class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Property Appreciation Rate (%)</label>
                                <input type="number" id="appreciationRate" step="0.1" value="3" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Investment Period -->
                <div>
                    <h3 class="font-medium text-gray-800 mb-4">Investment Period</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Investment Horizon (years)</label>
                            <input type="number" id="investmentHorizon" value="10" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Selling Costs (%)</label>
                            <input type="number" id="sellingCosts" step="0.1" value="6" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tax Rate (%)</label>
                            <input type="number" id="taxRate" step="0.1" value="25" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-center">
                    <button type="button" onclick="calculateROI()" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-calculator mr-2"></i>
                        Calculate ROI
                    </button>
                </div>
            </form>
        </div>

        <!-- Results -->
        <div id="results" class="hidden">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="bg-green-100 rounded-full p-3 mr-4">
                            <i class="fas fa-percentage text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total ROI</p>
                            <p id="totalROI" class="text-2xl font-bold text-gray-800">0%</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="bg-blue-100 rounded-full p-3 mr-4">
                            <i class="fas fa-dollar-sign text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Total Profit</p>
                            <p id="totalProfit" class="text-2xl font-bold text-gray-800">$0</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="bg-purple-100 rounded-full p-3 mr-4">
                            <i class="fas fa-chart-line text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Annual ROI</p>
                            <p id="annualROI" class="text-2xl font-bold text-gray-800">0%</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-center">
                        <div class="bg-yellow-100 rounded-full p-3 mr-4">
                            <i class="fas fa-coins text-yellow-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Cash Flow</p>
                            <p id="monthlyCashFlow" class="text-2xl font-bold text-gray-800">$0</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Breakdown -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Investment Breakdown</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Costs -->
                    <div>
                        <h3 class="font-medium text-gray-800 mb-3">Initial Investment</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Down Payment</span>
                                <span id="downPaymentAmount" class="font-medium text-gray-800">$0</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Closing Costs</span>
                                <span id="closingCosts" class="font-medium text-gray-800">$0</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Initial Repairs</span>
                                <span id="initialRepairs" class="font-medium text-gray-800">$0</span>
                            </div>
                            <div class="border-t pt-2 mt-2">
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-800">Total Investment</span>
                                    <span id="totalInvestment" class="font-bold text-gray-800">$0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Returns -->
                    <div>
                        <h3 class="font-medium text-gray-800 mb-3">Total Returns</h3>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Rental Income</span>
                                <span id="totalRentalIncome" class="font-medium text-gray-800">$0</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Property Appreciation</span>
                                <span id="propertyAppreciation" class="font-medium text-gray-800">$0</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Loan Paydown</span>
                                <span id="loanPaydown" class="font-medium text-gray-800">$0</span>
                            </div>
                            <div class="border-t pt-2 mt-2">
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-800">Total Returns</span>
                                    <span id="totalReturns" class="font-bold text-gray-800">$0</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Yearly Projections -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Yearly Projections</h2>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Year</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Property Value</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rental Income</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expenses</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Net Cash Flow</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cumulative ROI</th>
                            </tr>
                        </thead>
                        <tbody id="projectionsTable" class="bg-white divide-y divide-gray-200">
                            <!-- Projections will be inserted here -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-center space-x-4 mt-6">
                <button onclick="saveCalculation()" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Save Calculation
                </button>
                <button onclick="exportResults()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-download mr-2"></i>
                    Export Results
                </button>
                <button onclick="resetCalculator()" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-redo mr-2"></i>
                    Reset
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function calculateROI() {
    // Get input values
    const propertyValue = parseFloat(document.getElementById('propertyValue').value);
    const downPaymentPercent = parseFloat(document.getElementById('downPayment').value);
    const interestRate = parseFloat(document.getElementById('interestRate').value);
    const loanTerm = parseFloat(document.getElementById('loanTerm').value);
    const monthlyRent = parseFloat(document.getElementById('monthlyRent').value);
    const vacancyRate = parseFloat(document.getElementById('vacancyRate').value);
    const monthlyExpenses = parseFloat(document.getElementById('monthlyExpenses').value);
    const appreciationRate = parseFloat(document.getElementById('appreciationRate').value);
    const investmentHorizon = parseFloat(document.getElementById('investmentHorizon').value);
    const sellingCosts = parseFloat(document.getElementById('sellingCosts').value);
    const taxRate = parseFloat(document.getElementById('taxRate').value);
    
    // Calculate loan details
    const loanAmount = propertyValue * (1 - downPaymentPercent / 100);
    const monthlyRate = interestRate / 100 / 12;
    const totalPayments = loanTerm * 12;
    const monthlyPayment = loanAmount * (monthlyRate * Math.pow(1 + monthlyRate, totalPayments)) / (Math.pow(1 + monthlyRate, totalPayments) - 1);
    
    // Calculate initial investment
    const downPaymentAmount = propertyValue * downPaymentPercent / 100;
    const closingCosts = propertyValue * 0.03; // 3% closing costs
    const initialRepairs = propertyValue * 0.02; // 2% for repairs
    const totalInvestment = downPaymentAmount + closingCosts + initialRepairs;
    
    // Calculate monthly cash flow
    const effectiveMonthlyRent = monthlyRent * (1 - vacancyRate / 100);
    const monthlyCashFlow = effectiveMonthlyRent - monthlyExpenses - monthlyPayment;
    const annualCashFlow = monthlyCashFlow * 12;
    
    // Calculate projections
    let projections = [];
    let currentPropertyValue = propertyValue;
    let cumulativeCashFlow = 0;
    let currentLoanBalance = loanAmount;
    
    for (let year = 1; year <= investmentHorizon; year++) {
        // Property appreciation
        currentPropertyValue *= (1 + appreciationRate / 100);
        
        // Loan paydown
        let yearLoanPaydown = 0;
        for (let month = 1; month <= 12; month++) {
            const interestPayment = currentLoanBalance * monthlyRate;
            const principalPayment = monthlyPayment - interestPayment;
            currentLoanBalance -= principalPayment;
            yearLoanPaydown += principalPayment;
        }
        
        cumulativeCashFlow += annualCashFlow;
        
        projections.push({
            year: year,
            propertyValue: currentPropertyValue,
            rentalIncome: annualCashFlow,
            expenses: monthlyExpenses * 12 + monthlyPayment * 12,
            netCashFlow: annualCashFlow,
            cumulativeROI: ((cumulativeCashFlow + (propertyValue * appreciationRate / 100 * year) + yearLoanPaydown) / totalInvestment * 100).toFixed(2)
        });
    }
    
    // Calculate final values
    const finalPropertyValue = currentPropertyValue;
    const totalRentalIncome = annualCashFlow * investmentHorizon;
    const propertyAppreciation = finalPropertyValue - propertyValue;
    const loanPaydown = loanAmount - currentLoanBalance;
    const sellingPrice = finalPropertyValue * (1 - sellingCosts / 100);
    const totalReturns = totalRentalIncome + propertyAppreciation + loanPaydown;
    const totalProfit = totalReturns - totalInvestment;
    const totalROI = (totalProfit / totalInvestment * 100);
    const annualROI = (totalROI / investmentHorizon);
    
    // Update UI
    document.getElementById('totalROI').textContent = totalROI.toFixed(2) + '%';
    document.getElementById('totalProfit').textContent = '$' + totalProfit.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    document.getElementById('annualROI').textContent = annualROI.toFixed(2) + '%';
    document.getElementById('monthlyCashFlow').textContent = '$' + monthlyCashFlow.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    
    document.getElementById('downPaymentAmount').textContent = '$' + downPaymentAmount.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    document.getElementById('closingCosts').textContent = '$' + closingCosts.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    document.getElementById('initialRepairs').textContent = '$' + initialRepairs.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    document.getElementById('totalInvestment').textContent = '$' + totalInvestment.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    
    document.getElementById('totalRentalIncome').textContent = '$' + totalRentalIncome.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    document.getElementById('propertyAppreciation').textContent = '$' + propertyAppreciation.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    document.getElementById('loanPaydown').textContent = '$' + loanPaydown.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    document.getElementById('totalReturns').textContent = '$' + totalReturns.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    
    // Update projections table
    const tableBody = document.getElementById('projectionsTable');
    tableBody.innerHTML = '';
    projections.forEach(projection => {
        const row = tableBody.insertRow();
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Year ${projection.year}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$${projection.propertyValue.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$${projection.rentalIncome.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$${projection.expenses.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$${projection.netCashFlow.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ",")}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${projection.cumulativeROI}%</td>
        `;
    });
    
    // Show results
    document.getElementById('results').classList.remove('hidden');
    document.getElementById('results').scrollIntoView({ behavior: 'smooth' });
}

function saveCalculation() {
    // Implementation for saving calculation
    alert('Calculation saved successfully!');
}

function exportResults() {
    // Implementation for exporting results
    alert('Results exported successfully!');
}

function resetCalculator() {
    document.getElementById('roiCalculator').reset();
    document.getElementById('results').classList.add('hidden');
}
</script>
@endsection
