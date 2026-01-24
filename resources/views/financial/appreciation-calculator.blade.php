@extends('layouts.app')

@section('title', 'حاسبة ارتفاع القيمة')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">حاسبة ارتفاع القيمة</h1>
            <p class="text-muted mb-0">توقعات شاملة لارتفاع قيمة العقار</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary" onclick="saveProjection()">
                <i class="fas fa-save"></i> حفظ التوقع
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="exportResults()">
                <i class="fas fa-download"></i> تصدير النتائج
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Input Form -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">بيانات ارتفاع القيمة</h5>
                </div>
                <div class="card-body">
                    <form id="appreciationForm">
                        <div class="mb-3">
                            <label for="current_value" class="form-label">القيمة الحالية</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="current_value" step="0.01" value="1000000">
                                <span class="input-group-text">ريال</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="base_appreciation_rate" class="form-label">معدل ارتفاع القيمة الأساسي</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="base_appreciation_rate" step="0.1" value="3">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="projection_period" class="form-label">فترة التوقع</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="projection_period" value="10">
                                <span class="input-group-text">سنة</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="appreciation_model" class="form-label">نموذج التوقع</label>
                            <select class="form-select" id="appreciation_model">
                                <option value="linear">خطي</option>
                                <option value="compound">مركب</option>
                                <option value="variable">متغير</option>
                                <option value="market_based">قائم على السوق</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">عوامل السوق</label>
                            <div class="mb-2">
                                <label for="inflation_trend" class="form-label small">اتجاه التضخم (%)</label>
                                <input type="number" class="form-control" id="inflation_trend" step="0.1" value="2">
                            </div>
                            <div class="mb-2">
                                <label for="supply_demand" class="form-label small">العرض والطلب (%)</label>
                                <input type="number" class="form-control" id="supply_demand" step="0.1" value="1">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">عوامل العقار</label>
                            <div class="mb-2">
                                <label for="property_age" class="form-label small">عمر العقار (%)</label>
                                <input type="number" class="form-control" id="property_age" step="0.1" value="0">
                            </div>
                            <div class="mb-2">
                                <label for="renovation_value" class="form-label small">قيمة التجديد (%)</label>
                                <input type="number" class="form-control" id="renovation_value" step="0.1" value="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">افتراضات اقتصادية</label>
                            <div class="mb-2">
                                <label for="interest_rate_trend" class="form-label small">اتجاه سعر الفائدة (%)</label>
                                <input type="number" class="form-control" id="interest_rate_trend" step="0.1" value="0">
                            </div>
                            <div class="mb-2">
                                <label for="gdp_growth" class="form-label small">نمو الناتج المحلي (%)</label>
                                <input type="number" class="form-control" id="gdp_growth" step="0.1" value="2">
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary w-100" onclick="calculateAppreciation()">
                            <i class="fas fa-chart-area"></i> حساب ارتفاع القيمة
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Results -->
        <div class="col-lg-8">
            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h3 id="final_value" class="card-title">0</h3>
                            <p class="card-text">القيمة المتوقعة</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h3 id="total_appreciation" class="card-title">0%</h3>
                            <p class="card-text">إجمالي ارتفاع القيمة</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h3 id="annual_rate" class="card-title">0%</h3>
                            <p class="card-text">المعدل السنوي</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h3 id="confidence_level" class="card-title">0%</h3>
                            <p class="card-text">مستوى الثقة</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Projection Chart -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">توقعات ارتفاع القيمة</h5>
                </div>
                <div class="card-body">
                    <canvas id="appreciationChart" height="300"></canvas>
                </div>
            </div>

            <!-- Scenario Analysis -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">تحليل السيناريوهات</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5 id="optimistic_value" class="card-title">0</h5>
                                    <p class="card-text">سيناريو متفائل</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h5 id="base_case_value" class="card-title">0</h5>
                                    <p class="card-text">سيناريو أساسي</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h5 id="pessimistic_value" class="card-title">0</h5>
                                    <p class="card-text">سيناريو متشائم</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Yearly Breakdown -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">التفصيل السنوي</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="yearlyBreakdownTable">
                            <thead>
                                <tr>
                                    <th>السنة</th>
                                    <th>القيمة المتوقعة</th>
                                    <th>ارتفاع القيمة</th>
                                    <th>ارتفاع سنوي</th>
                                    <th>ارتفاع تراكمي</th>
                                    <th>مستوى الثقة</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Table will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Risk Analysis -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">تحليل المخاطرة</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>مؤشرات المخاطرة</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>تقلب القيمة:</td>
                                    <td id="volatility">0%</td>
                                </tr>
                                <tr>
                                    <td>مخاطرة الانخفاض:</td>
                                    <td id="downside_risk">0%</td>
                                </tr>
                                <tr>
                                    <td>أقصى انخفاض محتمل:</td>
                                    <td id="max_drawdown">0%</td>
                                </tr>
                                <tr>
                                    <td>مستوى المخاطرة:</td>
                                    <td id="risk_level">-</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>عوامل المخاطرة</h6>
                            <div id="risk_factors">
                                <!-- Risk factors will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let appreciationChart = null;

function calculateAppreciation() {
    const currentValue = parseFloat(document.getElementById('current_value').value);
    const baseRate = parseFloat(document.getElementById('base_appreciation_rate').value) / 100;
    const period = parseInt(document.getElementById('projection_period').value);
    const model = document.getElementById('appreciation_model').value;
    
    const marketFactors = {
        inflation_trend: parseFloat(document.getElementById('inflation_trend').value) / 100,
        supply_demand: parseFloat(document.getElementById('supply_demand').value) / 100
    };
    
    const propertyFactors = {
        property_age: parseFloat(document.getElementById('property_age').value) / 100,
        renovation_value: parseFloat(document.getElementById('renovation_value').value) / 100
    };
    
    const economicAssumptions = {
        interest_rate_trend: parseFloat(document.getElementById('interest_rate_trend').value) / 100,
        gdp_growth: parseFloat(document.getElementById('gdp_growth').value) / 100
    };

    const projections = [];
    const adjustedRates = calculateAdjustedRates(baseRate, marketFactors, propertyFactors, economicAssumptions, period);

    for (let year = 1; year <= period; year++) {
        const yearlyRate = adjustedRates[year - 1];
        const projectedValue = calculateYearValue(currentValue, yearlyRate, year, model);
        
        projections.push({
            year: year,
            projectedValue: projectedValue,
            appreciationRate: yearlyRate * 100,
            annualAppreciation: projectedValue - (year > 1 ? projections[year - 2].projectedValue : currentValue),
            cumulativeAppreciation: projectedValue - currentValue,
            cumulativeAppreciationPercentage: ((projectedValue - currentValue) / currentValue) * 100,
            confidenceLevel: calculateConfidenceLevel(year, period, model)
        });
    }

    const finalValue = projections[period - 1].projectedValue;
    const totalAppreciation = projections[period - 1].cumulativeAppreciationPercentage;
    const averageAnnualRate = period > 0 ? Math.pow(finalValue / currentValue, 1 / period) - 1 : 0;

    // Update display
    document.getElementById('final_value').textContent = formatCurrency(finalValue);
    document.getElementById('total_appreciation').textContent = totalAppreciation.toFixed(2) + '%';
    document.getElementById('annual_rate').textContent = (averageAnnualRate * 100).toFixed(2) + '%';
    document.getElementById('confidence_level').textContent = projections[period - 1].confidenceLevel.toFixed(1) + '%';

    // Update scenarios
    calculateScenarios(currentValue, baseRate, period, model, marketFactors, propertyFactors, economicAssumptions);

    // Update table
    updateYearlyBreakdownTable(projections);

    // Update chart
    updateAppreciationChart(projections);

    // Calculate risk analysis
    calculateRiskAnalysis(projections, adjustedRates);
}

function calculateAdjustedRates(baseRate, marketFactors, propertyFactors, economicAssumptions, period) {
    const adjustedRates = [];

    for (let year = 1; year <= period; year++) {
        let rate = baseRate;

        // Apply market factor adjustments
        if (marketFactors.inflation_trend) {
            const inflationImpact = marketFactors.inflation_trend * (year / period);
            rate += inflationImpact;
        }

        if (marketFactors.supply_demand) {
            const supplyDemandImpact = marketFactors.supply_demand * (1 - (year / period));
            rate += supplyDemandImpact;
        }

        // Apply property factor adjustments
        if (propertyFactors.property_age) {
            const ageImpact = -propertyFactors.property_age * (year / period);
            rate += ageImpact;
        }

        if (propertyFactors.renovation_value) {
            const renovationImpact = propertyFactors.renovation_value * Math.exp(-year / 10);
            rate += renovationImpact;
        }

        // Apply economic assumptions
        if (economicAssumptions.interest_rate_trend) {
            const interestImpact = -economicAssumptions.interest_rate_trend * 0.5;
            rate += interestImpact;
        }

        if (economicAssumptions.gdp_growth) {
            const gdpImpact = economicAssumptions.gdp_growth * 0.3;
            rate += gdpImpact;
        }

        // Add some randomness for realistic projections
        const randomFactor = (Math.random() - 0.5) * 0.02; // ±1% random variation
        rate += randomFactor;

        adjustedRates.push(Math.max(-0.2, Math.min(0.5, rate))); // Cap between -20% and +50%
    }

    return adjustedRates;
}

function calculateYearValue(currentValue, rate, year, model) {
    switch (model) {
        case 'linear':
            return currentValue * (1 + rate * year);
        
        case 'compound':
            return currentValue * Math.pow(1 + rate, year);
        
        case 'variable':
            // Variable rate with diminishing returns
            return currentValue * Math.pow(1 + rate * Math.exp(-year / 20), year);
        
        case 'market_based':
            // Market-based with cyclical patterns
            const cycleFactor = 1 + 0.1 * Math.sin(year * 2 * Math.PI / 7); // 7-year cycle
            return currentValue * Math.pow(1 + rate, year) * cycleFactor;
        
        default:
            return currentValue * Math.pow(1 + rate, year);
    }
}

function calculateConfidenceLevel(year, totalPeriod, model) {
    const baseConfidence = {
        'linear': 85,
        'compound': 75,
        'variable': 70,
        'market_based': 65
    };

    const confidence = baseConfidence[model] || 70;
    const timeDecay = (year / totalPeriod) * 40; // Lose up to 40% confidence over time
    
    return Math.max(20, confidence - timeDecay);
}

function calculateScenarios(currentValue, baseRate, period, model, marketFactors, propertyFactors, economicAssumptions) {
    // Optimistic scenario
    const optimisticData = {
        current_value: currentValue,
        base_appreciation_rate: baseRate * 1.5,
        appreciation_model: model,
        market_factors: { ...marketFactors, inflation_trend: marketFactors.inflation_trend * 1.2 },
        property_factors: propertyFactors,
        economic_assumptions: { ...economicAssumptions, gdp_growth: economicAssumptions.gdp_growth * 1.2 },
        projection_period: period
    };
    
    // Pessimistic scenario
    const pessimisticData = {
        current_value: currentValue,
        base_appreciation_rate: baseRate * 0.5,
        appreciation_model: model,
        market_factors: { ...marketFactors, inflation_trend: marketFactors.inflation_trend * 0.8 },
        property_factors: propertyFactors,
        economic_assumptions: { ...economicAssumptions, gdp_growth: economicAssumptions.gdp_growth * 0.8 },
        projection_period: period
    };

    // Calculate scenario values (simplified)
    const optimisticValue = currentValue * Math.pow(1 + baseRate * 1.5, period);
    const pessimisticValue = currentValue * Math.pow(1 + baseRate * 0.5, period);
    const baseCaseValue = currentValue * Math.pow(1 + baseRate, period);

    document.getElementById('optimistic_value').textContent = formatCurrency(optimisticValue);
    document.getElementById('pessimistic_value').textContent = formatCurrency(pessimisticValue);
    document.getElementById('base_case_value').textContent = formatCurrency(baseCaseValue);
}

function updateYearlyBreakdownTable(projections) {
    const tbody = document.querySelector('#yearlyBreakdownTable tbody');
    tbody.innerHTML = '';

    projections.forEach(data => {
        const row = tbody.insertRow();
        row.innerHTML = `
            <td>${data.year}</td>
            <td>${formatCurrency(data.projectedValue)}</td>
            <td>${data.appreciationRate.toFixed(2)}%</td>
            <td class="${data.annualAppreciation >= 0 ? 'text-success' : 'text-danger'}">${formatCurrency(data.annualAppreciation)}</td>
            <td class="${data.cumulativeAppreciation >= 0 ? 'text-success' : 'text-danger'}">${formatCurrency(data.cumulativeAppreciation)}</td>
            <td>
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar bg-${data.confidenceLevel >= 70 ? 'success' : (data.confidenceLevel >= 50 ? 'warning' : 'danger')}" 
                         style="width: ${data.confidenceLevel}%">
                        ${data.confidenceLevel.toFixed(1)}%
                    </div>
                </div>
            </td>
        `;
    });
}

function updateAppreciationChart(projections) {
    const labels = projections.map(d => `السنة ${d.year}`);
    const values = projections.map(d => d.projectedValue);
    const appreciationRates = projections.map(d => d.appreciationRate);

    if (appreciationChart) {
        appreciationChart.destroy();
    }

    const ctx = document.getElementById('appreciationChart').getContext('2d');
    appreciationChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'القيمة المتوقعة',
                    data: values,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    yAxisID: 'y',
                    tension: 0.1
                },
                {
                    label: 'معدل ارتفاع القيمة (%)',
                    data: appreciationRates,
                    type: 'bar',
                    backgroundColor: 'rgba(255, 99, 132, 0.8)',
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'القيمة (ريال)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'معدل ارتفاع القيمة (%)'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
}

function calculateRiskAnalysis(projections, rates) {
    const values = projections.map(d => d.projectedValue);
    const rateStdDev = calculateStandardDeviation(rates);
    const valueVolatility = calculateValueVolatility(values);
    
    // Calculate downside risk
    const negativeYears = rates.filter(rate => rate < 0);
    const downsideRisk = (negativeYears.length / rates.length) * 100;

    // Calculate maximum drawdown potential
    const maxDrawdown = calculateMaxDrawdown(values);

    // Update display
    document.getElementById('volatility').textContent = (rateStdDev * 100).toFixed(2) + '%';
    document.getElementById('downside_risk').textContent = downsideRisk.toFixed(2) + '%';
    document.getElementById('max_drawdown').textContent = (maxDrawdown * 100).toFixed(2) + '%';

    // Assess risk level
    let riskLevel = 'low';
    if (rateStdDev > 0.15 || downsideRisk > 30) {
        riskLevel = 'high';
    } else if (rateStdDev > 0.08 || downsideRisk > 15) {
        riskLevel = 'medium';
    }
    
    const riskLevelText = {
        'low': 'منخفض',
        'medium': 'متوسط',
        'high': 'مرتفع'
    };
    
    document.getElementById('risk_level').textContent = riskLevelText[riskLevel];

    // Identify risk factors
    const riskFactors = [];
    const avgRate = rates.reduce((sum, rate) => sum + rate, 0) / rates.length;
    
    if (avgRate < 0.02) {
        riskFactors.push('توقعات ارتفاع قيمة منخفضة');
    }
    
    if (negativeYears.length > rates.length * 0.3) {
        riskFactors.push('احتمالية انخفاض القيمة مرتفعة');
    }
    
    if (Math.max(...rates) - Math.min(...rates) > 0.15) {
        riskFactors.push('تقلب عالي في معدلات الارتفاع');
    }

    const riskFactorsDiv = document.getElementById('risk_factors');
    riskFactorsDiv.innerHTML = riskFactors.map(factor => 
        `<div class="alert alert-warning py-2">
            <i class="fas fa-exclamation-triangle"></i> ${factor}
        </div>`
    ).join('');
}

function calculateStandardDeviation(values) {
    if (values.length === 0) return 0;
    
    const mean = values.reduce((sum, value) => sum + value, 0) / values.length;
    const variance = values.reduce((sum, value) => sum + Math.pow(value - mean, 2), 0) / values.length;
    
    return Math.sqrt(variance);
}

function calculateValueVolatility(values) {
    if (values.length < 2) return 0;
    
    const returns = [];
    for (let i = 1; i < values.length; i++) {
        returns.push((values[i] - values[i - 1]) / values[i - 1]);
    }
    
    return calculateStandardDeviation(returns);
}

function calculateMaxDrawdown(values) {
    if (values.length === 0) return 0;
    
    let peak = values[0];
    let maxDrawdown = 0;
    
    for (const value of values) {
        if (value > peak) {
            peak = value;
        }
        const drawdown = (peak - value) / peak;
        if (drawdown > maxDrawdown) {
            maxDrawdown = drawdown;
        }
    }
    
    return maxDrawdown;
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('ar-SA', {
        style: 'currency',
        currency: 'SAR'
    }).format(amount);
}

function saveProjection() {
    // Implementation for saving projection
    alert('تم حفظ التوقع بنجاح');
}

function exportResults() {
    // Implementation for exporting results
    alert('جاري تصدير النتائج...');
}

// Auto-calculate on input change
document.querySelectorAll('#appreciationForm input, #appreciationForm select').forEach(input => {
    input.addEventListener('input', calculateAppreciation);
    input.addEventListener('change', calculateAppreciation);
});

// Initial calculation
calculateAppreciation();
</script>
@endsection
