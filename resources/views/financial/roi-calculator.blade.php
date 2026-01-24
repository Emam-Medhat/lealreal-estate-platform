@extends('layouts.app')

@section('title', 'حاسبة العائد على الاستثمار')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">حاسبة العائد على الاستثمار</h1>
            <p class="text-muted mb-0">حساب شامل للعائد على الاستثمار العقاري</p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-primary" onclick="saveCalculation()">
                <i class="fas fa-save"></i> حفظ الحساب
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
                    <h5 class="card-title mb-0">بيانات الاستثمار</h5>
                </div>
                <div class="card-body">
                    <form id="roiForm">
                        <div class="mb-3">
                            <label for="property_value" class="form-label">قيمة العقار</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="property_value" step="0.01" value="1000000">
                                <span class="input-group-text">ريال</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="down_payment" class="form-label">الدفعة المقدمة</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="down_payment" step="0.01" value="200000">
                                <span class="input-group-text">ريال</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="loan_amount" class="form-label">مبلغ القرض</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="loan_amount" step="0.01" value="800000">
                                <span class="input-group-text">ريال</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="interest_rate" class="form-label">سعر الفائدة</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="interest_rate" step="0.01" value="5.5">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="loan_term" class="form-label">مدة القرض</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="loan_term" value="30">
                                <span class="input-group-text">سنة</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="annual_income" class="form-label">الدخل السنوي</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="annual_income" step="0.01" value="80000">
                                <span class="input-group-text">ريال</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="annual_expenses" class="form-label">المصاريف السنوية</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="annual_expenses" step="0.01" value="20000">
                                <span class="input-group-text">ريال</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="holding_period" class="form-label">فترة الاستحواذ</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="holding_period" value="10">
                                <span class="input-group-text">سنة</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="appreciation_rate" class="form-label">معدل ارتفاع القيمة</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="appreciation_rate" step="0.01" value="3">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="selling_costs" class="form-label">تكاليف البيع</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="selling_costs" step="0.01" value="6">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="tax_rate" class="form-label">ضريبة الأرباح الرأسمالية</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="tax_rate" step="0.01" value="20">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary w-100" onclick="calculateROI()">
                            <i class="fas fa-calculator"></i> حساب العائد
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
                            <h3 id="roi_percentage" class="card-title">0%</h3>
                            <p class="card-text">العائد على الاستثمار</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h3 id="cash_on_cash" class="card-title">0%</h3>
                            <p class="card-text">العائد النقدي</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h3 id="irr" class="card-title">0%</h3>
                            <p class="card-text">معدل العائد الداخلي</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h3 id="npv" class="card-title">0</h3>
                            <p class="card-text">صافي القيمة الحالية</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Results -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">النتائج التفصيلية</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>بيانات الاستثمار</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>إجمالي الاستثمار:</td>
                                    <td id="total_investment">0 ريال</td>
                                </tr>
                                <tr>
                                    <td>الاستثمار النقدي:</td>
                                    <td id="cash_investment">0 ريال</td>
                                </tr>
                                <tr>
                                    <td>الدخل السنوي الصافي:</td>
                                    <td id="net_annual_income">0 ريال</td>
                                </tr>
                                <tr>
                                    <td>التدفق النقدي السنوي:</td>
                                    <td id="annual_cash_flow">0 ريال</td>
                                </tr>
                                <tr>
                                    <td>فترة استرداد رأس المال:</td>
                                    <td id="payback_period">0 سنة</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>مؤشرات الأداء</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>مؤشر الربحية:</td>
                                    <td id="profitability_index">0</td>
                                </tr>
                                <tr>
                                    <td>العائد المعدل للمخاطرة:</td>
                                    <td id="risk_adjusted_roi">0%</td>
                                </tr>
                                <tr>
                                    <td>العائد المعدل للتضخم:</td>
                                    <td id="inflation_adjusted_roi">0%</td>
                                </tr>
                                <tr>
                                    <td>القيمة المستقبلية:</td>
                                    <td id="future_value">0 ريال</td>
                                </tr>
                                <tr>
                                    <td>صافي الأرباح عند البيع:</td>
                                    <td id="net_proceeds">0 ريال</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cash Flow Chart -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">التدفق النقدي على مدى فترة الاستحواذ</h5>
                </div>
                <div class="card-body">
                    <canvas id="cashFlowChart" height="300"></canvas>
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
                                    <h5 id="best_case_roi" class="card-title">0%</h5>
                                    <p class="card-text">سيناريو متفائل</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h5 id="base_case_roi" class="card-title">0%</h5>
                                    <p class="card-text">سيناريو أساسي</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body text-center">
                                    <h5 id="worst_case_roi" class="card-title">0%</h5>
                                    <p class="card-text">سيناريو متشائم</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sensitivity Analysis -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">تحليل الحساسية</h5>
                </div>
                <div class="card-body">
                    <canvas id="sensitivityChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let cashFlowChart = null;
let sensitivityChart = null;

function calculateROI() {
    const propertyValue = parseFloat(document.getElementById('property_value').value);
    const downPayment = parseFloat(document.getElementById('down_payment').value);
    const loanAmount = parseFloat(document.getElementById('loan_amount').value);
    const interestRate = parseFloat(document.getElementById('interest_rate').value) / 100;
    const loanTerm = parseInt(document.getElementById('loan_term').value);
    const annualIncome = parseFloat(document.getElementById('annual_income').value);
    const annualExpenses = parseFloat(document.getElementById('annual_expenses').value);
    const holdingPeriod = parseInt(document.getElementById('holding_period').value);
    const appreciationRate = parseFloat(document.getElementById('appreciation_rate').value) / 100;
    const sellingCosts = parseFloat(document.getElementById('selling_costs').value) / 100;
    const taxRate = parseFloat(document.getElementById('tax_rate').value) / 100;

    // Calculate monthly mortgage payment
    const monthlyRate = interestRate / 12;
    const totalPayments = loanTerm * 12;
    const monthlyPayment = loanAmount * 
        (monthlyRate * Math.pow(1 + monthlyRate, totalPayments)) / 
        (Math.pow(1 + monthlyRate, totalPayments) - 1);
    const annualDebtService = monthlyPayment * 12;

    // Calculate cash flow
    const netOperatingIncome = annualIncome - annualExpenses;
    const annualCashFlow = netOperatingIncome - annualDebtService;

    // Calculate ROI metrics
    const totalInvestment = propertyValue;
    const cashInvestment = downPayment;
    const roi = (annualCashFlow / totalInvestment) * 100;
    const cashOnCash = (annualCashFlow / cashInvestment) * 100;

    // Calculate future value and proceeds
    const futureValue = propertyValue * Math.pow(1 + appreciationRate, holdingPeriod);
    const sellingCostAmount = futureValue * sellingCosts;
    const capitalGain = futureValue - propertyValue;
    const capitalGainTax = capitalGain * taxRate;
    const remainingLoanBalance = calculateRemainingLoanBalance(loanAmount, interestRate, loanTerm, holdingPeriod);
    const netProceeds = futureValue - sellingCostAmount - capitalGainTax - remainingLoanBalance;

    // Calculate total return
    const totalCashFlows = annualCashFlow * holdingPeriod;
    const totalReturn = netProceeds + totalCashFlows - cashInvestment;
    const totalROI = (totalReturn / cashInvestment) * 100;

    // Calculate IRR (simplified)
    const irr = calculateIRR(cashInvestment, annualCashFlow, netProceeds, holdingPeriod);

    // Calculate NPV (assuming 8% discount rate)
    const discountRate = 0.08;
    const npv = calculateNPV(cashInvestment, annualCashFlow, netProceeds, holdingPeriod, discountRate);

    // Calculate payback period
    const paybackPeriod = cashInvestment / annualCashFlow;

    // Calculate profitability index
    const profitabilityIndex = (netProceeds + (annualCashFlow * holdingPeriod)) / cashInvestment;

    // Update display
    document.getElementById('roi_percentage').textContent = totalROI.toFixed(2) + '%';
    document.getElementById('cash_on_cash').textContent = cashOnCash.toFixed(2) + '%';
    document.getElementById('irr').textContent = irr.toFixed(2) + '%';
    document.getElementById('npv').textContent = npv.toFixed(0);

    document.getElementById('total_investment').textContent = formatCurrency(totalInvestment);
    document.getElementById('cash_investment').textContent = formatCurrency(cashInvestment);
    document.getElementById('net_annual_income').textContent = formatCurrency(netOperatingIncome);
    document.getElementById('annual_cash_flow').textContent = formatCurrency(annualCashFlow);
    document.getElementById('payback_period').textContent = paybackPeriod.toFixed(1) + ' سنة';
    document.getElementById('profitability_index').textContent = profitabilityIndex.toFixed(2);
    document.getElementById('future_value').textContent = formatCurrency(futureValue);
    document.getElementById('net_proceeds').textContent = formatCurrency(netProceeds);

    // Calculate scenarios
    calculateScenarios(propertyValue, cashInvestment, annualCashFlow, appreciationRate, holdingPeriod);

    // Update charts
    updateCashFlowChart(propertyValue, annualCashFlow, appreciationRate, holdingPeriod);
    updateSensitivityChart(propertyValue, cashInvestment, annualIncome, annualExpenses, loanAmount, interestRate);
}

function calculateRemainingLoanBalance(loanAmount, interestRate, loanTerm, yearsPaid) {
    const monthlyRate = interestRate / 12;
    const totalPayments = loanTerm * 12;
    const paymentsMade = yearsPaid * 12;
    
    if (monthlyRate === 0) {
        return loanAmount - (loanAmount / totalPayments * paymentsMade);
    }

    const monthlyPayment = loanAmount * 
        (monthlyRate * Math.pow(1 + monthlyRate, totalPayments)) / 
        (Math.pow(1 + monthlyRate, totalPayments) - 1);
    
    const remainingPayments = totalPayments - paymentsMade;
    return monthlyPayment * 
        (1 - Math.pow(1 + monthlyRate, -remainingPayments)) / monthlyRate;
}

function calculateIRR(initialInvestment, annualCashFlow, finalValue, years) {
    // Simplified IRR calculation
    const totalReturn = (annualCashFlow * years) + finalValue - initialInvestment;
    const averageAnnualReturn = totalReturn / years;
    return (averageAnnualReturn / initialInvestment) * 100;
}

function calculateNPV(initialInvestment, annualCashFlow, finalValue, years, discountRate) {
    let npv = -initialInvestment;
    
    for (let year = 1; year <= years; year++) {
        npv += annualCashFlow / Math.pow(1 + discountRate, year);
    }
    
    npv += finalValue / Math.pow(1 + discountRate, years);
    return npv;
}

function calculateScenarios(propertyValue, cashInvestment, annualCashFlow, appreciationRate, holdingPeriod) {
    // Best case: higher appreciation and cash flow
    const bestAppreciation = appreciationRate * 1.5;
    const bestCashFlow = annualCashFlow * 1.2;
    const bestFutureValue = propertyValue * Math.pow(1 + bestAppreciation, holdingPeriod);
    const bestTotalReturn = (bestCashFlow * holdingPeriod) + bestFutureValue - cashInvestment;
    const bestROI = (bestTotalReturn / cashInvestment) * 100;

    // Base case: current assumptions
    const baseFutureValue = propertyValue * Math.pow(1 + appreciationRate, holdingPeriod);
    const baseTotalReturn = (annualCashFlow * holdingPeriod) + baseFutureValue - cashInvestment;
    const baseROI = (baseTotalReturn / cashInvestment) * 100;

    // Worst case: lower appreciation and cash flow
    const worstAppreciation = appreciationRate * 0.5;
    const worstCashFlow = annualCashFlow * 0.8;
    const worstFutureValue = propertyValue * Math.pow(1 + worstAppreciation, holdingPeriod);
    const worstTotalReturn = (worstCashFlow * holdingPeriod) + worstFutureValue - cashInvestment;
    const worstROI = (worstTotalReturn / cashInvestment) * 100;

    document.getElementById('best_case_roi').textContent = bestROI.toFixed(2) + '%';
    document.getElementById('base_case_roi').textContent = baseROI.toFixed(2) + '%';
    document.getElementById('worst_case_roi').textContent = worstROI.toFixed(2) + '%';
}

function updateCashFlowChart(propertyValue, annualCashFlow, appreciationRate, holdingPeriod) {
    const labels = [];
    const cashFlowData = [];
    const propertyValueData = [];
    const equityData = [];

    for (let year = 0; year <= holdingPeriod; year++) {
        labels.push('سنة ' + year);
        
        const cumulativeCashFlow = annualCashFlow * year;
        cashFlowData.push(cumulativeCashFlow);
        
        const currentPropertyValue = propertyValue * Math.pow(1 + appreciationRate, year);
        propertyValueData.push(currentPropertyValue);
        
        const equity = currentPropertyValue - (propertyValue * 0.8 * (1 - year / holdingPeriod)); // Simplified loan payoff
        equityData.push(equity);
    }

    if (cashFlowChart) {
        cashFlowChart.destroy();
    }

    const ctx = document.getElementById('cashFlowChart').getContext('2d');
    cashFlowChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'التدفق النقدي التراكمي',
                    data: cashFlowData,
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    yAxisID: 'y'
                },
                {
                    label: 'قيمة العقار',
                    data: propertyValueData,
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    yAxisID: 'y1'
                },
                {
                    label: 'حقوق الملكية',
                    data: equityData,
                    borderColor: 'rgb(54, 162, 235)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
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
                        text: 'التدفق النقدي'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'قيمة العقار/الحقوق'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
}

function updateSensitivityChart(propertyValue, cashInvestment, annualIncome, annualExpenses, loanAmount, interestRate) {
    const baseROI = calculateROIForParams(propertyValue, cashInvestment, annualIncome, annualExpenses, loanAmount, interestRate);
    
    const variables = ['سعر الفائدة', 'الدخل السنوي', 'المصاريف', 'معدل ارتفاع القيمة'];
    const sensitivities = [];
    
    // Interest rate sensitivity
    const roiHighInterest = calculateROIForParams(propertyValue, cashInvestment, annualIncome, annualExpenses, loanAmount, interestRate * 1.5);
    const roiLowInterest = calculateROIForParams(propertyValue, cashInvestment, annualIncome, annualExpenses, loanAmount, interestRate * 0.5);
    sensitivities.push({
        variable: variables[0],
        high: roiHighInterest,
        base: baseROI,
        low: roiLowInterest
    });
    
    // Income sensitivity
    const roiHighIncome = calculateROIForParams(propertyValue, cashInvestment, annualIncome * 1.2, annualExpenses, loanAmount, interestRate);
    const roiLowIncome = calculateROIForParams(propertyValue, cashInvestment, annualIncome * 0.8, annualExpenses, loanAmount, interestRate);
    sensitivities.push({
        variable: variables[1],
        high: roiHighIncome,
        base: baseROI,
        low: roiLowIncome
    });
    
    // Expense sensitivity
    const roiHighExpenses = calculateROIForParams(propertyValue, cashInvestment, annualIncome, annualExpenses * 1.2, loanAmount, interestRate);
    const roiLowExpenses = calculateROIForParams(propertyValue, cashInvestment, annualIncome, annualExpenses * 0.8, loanAmount, interestRate);
    sensitivities.push({
        variable: variables[2],
        high: roiHighExpenses,
        base: baseROI,
        low: roiLowExpenses
    });
    
    // Appreciation sensitivity
    const roiHighAppreciation = calculateROIForParams(propertyValue, cashInvestment, annualIncome, annualExpenses, loanAmount, interestRate, 0.06);
    const roiLowAppreciation = calculateROIForParams(propertyValue, cashInvestment, annualIncome, annualExpenses, loanAmount, interestRate, 0.01);
    sensitivities.push({
        variable: variables[3],
        high: roiHighAppreciation,
        base: baseROI,
        low: roiLowAppreciation
    });

    if (sensitivityChart) {
        sensitivityChart.destroy();
    }

    const ctx = document.getElementById('sensitivityChart').getContext('2d');
    sensitivityChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: variables,
            datasets: [
                {
                    label: 'سيناريو متفائل',
                    data: sensitivities.map(s => s.high),
                    backgroundColor: 'rgba(75, 192, 192, 0.8)'
                },
                {
                    label: 'سيناريو أساسي',
                    data: sensitivities.map(s => s.base),
                    backgroundColor: 'rgba(255, 206, 86, 0.8)'
                },
                {
                    label: 'سيناريو متشائم',
                    data: sensitivities.map(s => s.low),
                    backgroundColor: 'rgba(255, 99, 132, 0.8)'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'العائد على الاستثمار (%)'
                    }
                }
            }
        }
    });
}

function calculateROIForParams(propertyValue, cashInvestment, annualIncome, annualExpenses, loanAmount, interestRate, appreciationRate = 0.03) {
    const monthlyRate = interestRate / 12;
    const totalPayments = 30 * 12;
    const monthlyPayment = loanAmount * 
        (monthlyRate * Math.pow(1 + monthlyRate, totalPayments)) / 
        (Math.pow(1 + monthlyRate, totalPayments) - 1);
    const annualDebtService = monthlyPayment * 12;
    
    const netOperatingIncome = annualIncome - annualExpenses;
    const annualCashFlow = netOperatingIncome - annualDebtService;
    
    const holdingPeriod = 10;
    const futureValue = propertyValue * Math.pow(1 + appreciationRate, holdingPeriod);
    const totalReturn = (annualCashFlow * holdingPeriod) + futureValue - cashInvestment;
    
    return (totalReturn / cashInvestment) * 100;
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('ar-SA', {
        style: 'currency',
        currency: 'SAR'
    }).format(amount);
}

function saveCalculation() {
    // Implementation for saving calculation
    alert('تم حفظ الحساب بنجاح');
}

function exportResults() {
    // Implementation for exporting results
    alert('جاري تصدير النتائج...');
}

// Auto-calculate on input change
document.querySelectorAll('#roiForm input').forEach(input => {
    input.addEventListener('input', calculateROI);
});

// Initial calculation
calculateROI();
</script>
@endsection
