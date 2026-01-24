@extends('layouts.app')

@section('title', 'Risk Assessment')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Risk Assessment</h1>
                    <p class="text-gray-600">Analyze and mitigate investment risks</p>
                </div>
                <a href="{{ route('investor.dashboard') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Risk Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-shield-alt text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Risk Score</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $riskScore }}/100</p>
                        <p class="text-xs text-green-600 mt-1">
                            {{ $riskLevel }}
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-chart-line text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Risk Tolerance</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $riskTolerance }}/10</p>
                        <p class="text-xs text-blue-600 mt-1">
                            Your profile
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-exclamation-triangle text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">High Risks</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $highRisks }}</p>
                        <p class="text-xs text-purple-600 mt-1">
                            Need attention
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-lightbulb text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Mitigations</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $mitigations }}</p>
                        <p class="text-xs text-yellow-600 mt-1">
                            In place
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Risk Assessment Tool -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Investment Risk Calculator</h2>
            
            <form id="riskCalculator" class="space-y-6">
                <!-- Market Risk -->
                <div class="border rounded-lg p-4">
                    <h3 class="font-medium text-gray-800 mb-4">Market Risk Factors</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Local Market Stability</label>
                            <select name="marketStability" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="1">Very Stable</option>
                                <option value="2">Stable</option>
                                <option value="3">Moderate</option>
                                <option value="4">Unstable</option>
                                <option value="5">Very Unstable</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Property Demand</label>
                            <select name="propertyDemand" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="1">Very High</option>
                                <option value="2">High</option>
                                <option value="3">Moderate</option>
                                <option value="4">Low</option>
                                <option value="5">Very Low</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Economic Outlook</label>
                            <select name="economicOutlook" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="1">Excellent</option>
                                <option value="2">Good</option>
                                <option value="3">Fair</option>
                                <option value="4">Poor</option>
                                <option value="5">Very Poor</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Interest Rate Risk</label>
                            <select name="interestRateRisk" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="1">Very Low</option>
                                <option value="2">Low</option>
                                <option value="3">Moderate</option>
                                <option value="4">High</option>
                                <option value="5">Very High</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Property Risk -->
                <div class="border rounded-lg p-4">
                    <h3 class="font-medium text-gray-800 mb-4">Property Risk Factors</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Property Age</label>
                            <select name="propertyAge" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="1">New (0-5 years)</option>
                                <option value="2">Modern (5-15 years)</option>
                                <option value="3">Established (15-30 years)</option>
                                <option value="4">Old (30-50 years)</option>
                                <option value="5">Very Old (50+ years)</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Property Condition</label>
                            <select name="propertyCondition" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="1">Excellent</option>
                                <option value="2">Good</option>
                                <option value="3">Fair</option>
                                <option value="4">Poor</option>
                                <option value="5">Very Poor</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Location Quality</label>
                            <select name="locationQuality" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="1">Prime</option>
                                <option value="2">Excellent</option>
                                <option value="3">Good</option>
                                <option value="4">Fair</option>
                                <option value="5">Poor</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Neighborhood Trend</label>
                            <select name="neighborhoodTrend" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="1">Improving Rapidly</option>
                                <option value="2">Improving</option>
                                <option value="3">Stable</option>
                                <option value="4">Declining</option>
                                <option value="5">Declining Rapidly</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Financial Risk -->
                <div class="border rounded-lg p-4">
                    <h3 class="font-medium text-gray-800 mb-4">Financial Risk Factors</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Loan-to-Value Ratio</label>
                            <select name="ltvRatio" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="1">Very Low (&lt;50%)</option>
                                <option value="2">Low (50-70%)</option>
                                <option value="3">Moderate (70-80%)</option>
                                <option value="4">High (80-90%)</option>
                                <option value="5">Very High (&gt;90%)</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Debt Service Coverage</label>
                            <select name="debtService" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="1">Excellent (&gt;2.0)</option>
                                <option value="2">Good (1.5-2.0)</option>
                                <option value="3">Fair (1.2-1.5)</option>
                                <option value="4">Poor (1.0-1.2)</option>
                                <option value="5">Very Poor (&lt;1.0)</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cash Flow Stability</label>
                            <select name="cashFlowStability" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="1">Very Stable</option>
                                <option value="2">Stable</option>
                                <option value="3">Moderate</option>
                                <option value="4">Unstable</option>
                                <option value="5">Very Unstable</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Liquidity Risk</label>
                            <select name="liquidityRisk" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="1">Very Low</option>
                                <option value="2">Low</option>
                                <option value="3">Moderate</option>
                                <option value="4">High</option>
                                <option value="5">Very High</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-center">
                    <button type="button" onclick="calculateRisk()" class="bg-red-600 text-white px-8 py-3 rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-calculator mr-2"></i>
                        Calculate Risk Score
                    </button>
                </div>
            </form>
        </div>

        <!-- Risk Results -->
        <div id="riskResults" class="hidden">
            <!-- Overall Risk Score -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Risk Assessment Results</h2>
                
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-32 h-32 rounded-full bg-gray-100 mb-4">
                        <div class="text-center">
                            <div id="overallRiskScore" class="text-3xl font-bold text-gray-800">0</div>
                            <div class="text-sm text-gray-600">Risk Score</div>
                        </div>
                    </div>
                    <div id="riskLevelBadge" class="inline-flex px-4 py-2 text-lg font-semibold rounded-full">
                        Risk Level
                    </div>
                </div>
                
                <!-- Risk Breakdown -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-800 mb-3">Market Risk</h4>
                        <div class="flex items-center mb-2">
                            <div class="flex-1 bg-gray-200 rounded-full h-3 mr-3">
                                <div id="marketRiskBar" class="bg-blue-600 h-3 rounded-full" style="width: 0%"></div>
                            </div>
                            <span id="marketRiskScore" class="text-sm font-medium text-gray-800">0</span>
                        </div>
                        <p id="marketRiskAssessment" class="text-sm text-gray-600"></p>
                    </div>
                    
                    <div>
                        <h4 class="font-medium text-gray-800 mb-3">Property Risk</h4>
                        <div class="flex items-center mb-2">
                            <div class="flex-1 bg-gray-200 rounded-full h-3 mr-3">
                                <div id="propertyRiskBar" class="bg-purple-600 h-3 rounded-full" style="width: 0%"></div>
                            </div>
                            <span id="propertyRiskScore" class="text-sm font-medium text-gray-800">0</span>
                        </div>
                        <p id="propertyRiskAssessment" class="text-sm text-gray-600"></p>
                    </div>
                    
                    <div>
                        <h4 class="font-medium text-gray-800 mb-3">Financial Risk</h4>
                        <div class="flex items-center mb-2">
                            <div class="flex-1 bg-gray-200 rounded-full h-3 mr-3">
                                <div id="financialRiskBar" class="bg-yellow-600 h-3 rounded-full" style="width: 0%"></div>
                            </div>
                            <span id="financialRiskScore" class="text-sm font-medium text-gray-800">0</span>
                        </div>
                        <p id="financialRiskAssessment" class="text-sm text-gray-600"></p>
                    </div>
                </div>
            </div>

            <!-- Risk Mitigation Strategies -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Recommended Mitigation Strategies</h2>
                
                <div id="mitigationStrategies" class="space-y-4">
                    <!-- Strategies will be inserted here -->
                </div>
            </div>

            <!-- Risk Comparison -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Risk Comparison</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-800 mb-3">Your Risk Profile vs. Average</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Your Risk Score</span>
                                <span id="yourRiskComparison" class="text-sm font-medium text-gray-800">0</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Average Investor</span>
                                <span class="text-sm font-medium text-gray-800">35</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Conservative</span>
                                <span class="text-sm font-medium text-gray-800">20</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Aggressive</span>
                                <span class="text-sm font-medium text-gray-800">50</span>
                            </div>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="font-medium text-gray-800 mb-3">Investment Recommendations</h4>
                        <div id="investmentRecommendations" class="space-y-2">
                            <!-- Recommendations will be inserted here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-center space-x-4 mt-6">
                <button onclick="saveAssessment()" class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Save Assessment
                </button>
                <button onclick="exportReport()" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-download mr-2"></i>
                    Export Report
                </button>
                <button onclick="resetCalculator()" class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-redo mr-2"></i>
                    Reset
                </button>
            </div>
        </div>

        <!-- Historical Risk Assessments -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Previous Assessments</h2>
            
            <div class="space-y-3">
                @forelse ($previousAssessments as $assessment)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-gray-200 rounded-full w-10 h-10 mr-3 flex items-center justify-center">
                                <i class="fas fa-shield-alt text-gray-400"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">{{ $assessment->property_name }}</h4>
                                <p class="text-sm text-gray-600">{{ $assessment->created_at->format('M j, Y') }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-lg font-bold text-gray-800">{{ $assessment->risk_score }}/100</div>
                            <div class="text-sm text-gray-600">{{ $assessment->risk_level }}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No previous assessments</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
function calculateRisk() {
    const form = document.getElementById('riskCalculator');
    const formData = new FormData(form);
    
    let marketRisk = 0;
    let propertyRisk = 0;
    let financialRisk = 0;
    
    // Calculate market risk
    marketRisk += parseInt(formData.get('marketStability')) * 5;
    marketRisk += parseInt(formData.get('propertyDemand')) * 4;
    marketRisk += parseInt(formData.get('economicOutlook')) * 3;
    marketRisk += parseInt(formData.get('interestRateRisk')) * 4;
    
    // Calculate property risk
    propertyRisk += parseInt(formData.get('propertyAge')) * 3;
    propertyRisk += parseInt(formData.get('propertyCondition')) * 4;
    propertyRisk += parseInt(formData.get('locationQuality')) * 3;
    propertyRisk += parseInt(formData.get('neighborhoodTrend')) * 4;
    
    // Calculate financial risk
    financialRisk += parseInt(formData.get('ltvRatio')) * 4;
    financialRisk += parseInt(formData.get('debtService')) * 4;
    financialRisk += parseInt(formData.get('cashFlowStability')) * 3;
    financialRisk += parseInt(formData.get('liquidityRisk')) * 3;
    
    // Normalize scores (max 100 each)
    marketRisk = Math.min(100, marketRisk);
    propertyRisk = Math.min(100, propertyRisk);
    financialRisk = Math.min(100, financialRisk);
    
    // Calculate overall risk score
    const overallRisk = Math.round((marketRisk + propertyRisk + financialRisk) / 3);
    
    // Determine risk level
    let riskLevel = '';
    let riskLevelClass = '';
    if (overallRisk <= 20) {
        riskLevel = 'Low Risk';
        riskLevelClass = 'bg-green-100 text-green-800';
    } else if (overallRisk <= 40) {
        riskLevel = 'Moderate Risk';
        riskLevelClass = 'bg-yellow-100 text-yellow-800';
    } else if (overallRisk <= 60) {
        riskLevel = 'High Risk';
        riskLevelClass = 'bg-orange-100 text-orange-800';
    } else {
        riskLevel = 'Very High Risk';
        riskLevelClass = 'bg-red-100 text-red-800';
    }
    
    // Update UI
    document.getElementById('overallRiskScore').textContent = overallRisk;
    document.getElementById('riskLevelBadge').textContent = riskLevel;
    document.getElementById('riskLevelBadge').className = 'inline-flex px-4 py-2 text-lg font-semibold rounded-full ' + riskLevelClass;
    
    // Update risk bars
    document.getElementById('marketRiskBar').style.width = marketRisk + '%';
    document.getElementById('marketRiskScore').textContent = marketRisk;
    document.getElementById('marketRiskAssessment').textContent = getRiskAssessment(marketRisk);
    
    document.getElementById('propertyRiskBar').style.width = propertyRisk + '%';
    document.getElementById('propertyRiskScore').textContent = propertyRisk;
    document.getElementById('propertyRiskAssessment').textContent = getRiskAssessment(propertyRisk);
    
    document.getElementById('financialRiskBar').style.width = financialRisk + '%';
    document.getElementById('financialRiskScore').textContent = financialRisk;
    document.getElementById('financialRiskAssessment').textContent = getRiskAssessment(financialRisk);
    
    // Update comparison
    document.getElementById('yourRiskComparison').textContent = overallRisk;
    
    // Generate mitigation strategies
    generateMitigationStrategies(marketRisk, propertyRisk, financialRisk);
    
    // Generate investment recommendations
    generateInvestmentRecommendations(overallRisk);
    
    // Show results
    document.getElementById('riskResults').classList.remove('hidden');
    document.getElementById('riskResults').scrollIntoView({ behavior: 'smooth' });
}

function getRiskAssessment(score) {
    if (score <= 20) return 'Low risk - favorable conditions';
    if (score <= 40) return 'Moderate risk - manageable with proper planning';
    if (score <= 60) return 'High risk - requires careful mitigation';
    return 'Very high risk - significant concerns';
}

function generateMitigationStrategies(marketRisk, propertyRisk, financialRisk) {
    const strategies = [];
    
    if (marketRisk > 50) {
        strategies.push({
            title: 'Market Risk Mitigation',
            description: 'Diversify across different markets and property types. Consider longer investment horizons to weather market volatility.',
            priority: 'High'
        });
    }
    
    if (propertyRisk > 50) {
        strategies.push({
            title: 'Property Risk Mitigation',
            description: 'Conduct thorough property inspections. Allocate budget for immediate repairs and maintenance. Consider properties in better locations.',
            priority: 'High'
        });
    }
    
    if (financialRisk > 50) {
        strategies.push({
            title: 'Financial Risk Mitigation',
            description: 'Maintain higher cash reserves. Consider lower LTV ratios. Secure longer-term fixed-rate financing when possible.',
            priority: 'High'
        });
    }
    
    const container = document.getElementById('mitigationStrategies');
    container.innerHTML = strategies.map(strategy => `
        <div class="border-l-4 border-${strategy.priority === 'High' ? 'red' : 'yellow'}-500 pl-4 py-3">
            <h4 class="font-medium text-gray-800">${strategy.title}</h4>
            <p class="text-sm text-gray-600 mt-1">${strategy.description}</p>
            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-${strategy.priority === 'High' ? 'red' : 'yellow'}-100 text-${strategy.priority === 'High' ? 'red' : 'yellow'}-800 mt-2">
                ${strategy.priority} Priority
            </span>
        </div>
    `).join('');
}

function generateInvestmentRecommendations(riskScore) {
    const recommendations = [];
    
    if (riskScore <= 20) {
        recommendations.push('Consider value-add opportunities for higher returns');
        recommendations.push('Leverage can be used conservatively to enhance returns');
    } else if (riskScore <= 40) {
        recommendations.push('Focus on stable, income-producing properties');
        recommendations.push('Maintain adequate cash reserves for unexpected expenses');
    } else if (riskScore <= 60) {
        recommendations.push('Prioritize principal preservation over high returns');
        recommendations.push('Consider shorter-term investments to reduce exposure');
    } else {
        recommendations.push('Reconsider investment or seek professional advice');
        recommendations.push('Look for lower-risk alternative investments');
    }
    
    const container = document.getElementById('investmentRecommendations');
    container.innerHTML = recommendations.map(rec => `
        <div class="flex items-start">
            <i class="fas fa-lightbulb text-yellow-500 mr-2 mt-1"></i>
            <span class="text-sm text-gray-700">${rec}</span>
        </div>
    `).join('');
}

function saveAssessment() {
    alert('Risk assessment saved successfully!');
}

function exportReport() {
    alert('Risk report exported successfully!');
}

function resetCalculator() {
    document.getElementById('riskCalculator').reset();
    document.getElementById('riskResults').classList.add('hidden');
}
</script>
@endsection
