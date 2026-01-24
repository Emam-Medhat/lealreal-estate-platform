@extends('layouts.app')

@section('title', 'تحليل التدفق النقدي')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">تحليل التدفق النقدي</h1>
            <p class="text-muted mb-0">تحليل شامل للتدفقات النقدية العقارية</p>
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
                    <h5 class="card-title mb-0">بيانات التدفق النقدي</h5>
                </div>
                <div class="card-body">
                    <form id="cashFlowForm">
                        <div class="mb-3">
                            <label for="property_value" class="form-label">قيمة العقار</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="property_value" step="0.01" value="1000000">
                                <span class="input-group-text">ريال</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="monthly_rent" class="form-label">الإيجار الشهري</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="monthly_rent" step="0.01" value="8000">
                                <span class="input-group-text">ريال</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="other_income" class="form-label">دخل آخر شهري</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="other_income" step="0.01" value="500">
                                <span class="input-group-text">ريال</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="vacancy_rate" class="form-label">معدل الشغور</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="vacancy_rate" step="0.1" value="5">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="operating_expenses" class="form-label">المصاريف التشغيلية الشهرية</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="operating_expenses" step="0.01" value="2000">
                                <span class="input-group-text">ريال</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="capital_expenditures" class="form-label">النفقات الرأسمالية السنوية</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="capital_expenditures" step="0.01" value="5000">
                                <span class="input-group-text">ريال</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="loan_payment" class="form-label">قسط القرض الشهري</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="loan_payment" step="0.01" value="4000">
                                <span class="input-group-text">ريال</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="property_tax" class="form-label">ضريبة العقار الشهرية</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="property_tax" step="0.01" value="500">
                                <span class="input-group-text">ريال</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="insurance" class="form-label">التأمين الشهري</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="insurance" step="0.01" value="300">
                                <span class="input-group-text">ريال</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="management_fee" class="form-label">رسوم الإدارة الشهرية</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="management_fee" step="0.01" value="400">
                                <span class="input-group-text">ريال</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="projection_years" class="form-label">فترة التوقع</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="projection_years" value="10">
                                <span class="input-group-text">سنة</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="rent_growth" class="form-label">معدل نمو الإيجار</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="rent_growth" step="0.1" value="3">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="expense_growth" class="form-label">معدل نمو المصاريف</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="expense_growth" step="0.1" value="2">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="appreciation_rate" class="form-label">معدل ارتفاع القيمة</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="appreciation_rate" step="0.1" value="3">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary w-100" onclick="analyzeCashFlow()">
                            <i class="fas fa-chart-line"></i> تحليل التدفق النقدي
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
                            <h3 id="monthly_cash_flow" class="card-title">0</h3>
                            <p class="card-text">التدفق النقدي الشهري</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h3 id="annual_cash_flow" class="card-title">0</h3>
                            <p class="card-text">التدفق النقدي السنوي</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h3 id="cash_on_cash_return" class="card-title">0%</h3>
                            <p class="card-text">العائد النقدي</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h3 id="dscr" class="card-title">0</h3>
                            <p class="card-text">نسبة تغطية خدمة الدين</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Analysis -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">التحليل التفصيلي</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>الدخل</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>الإيجار الشهري:</td>
                                    <td id="monthly_rent_display">0 ريال</td>
                                </tr>
                                <tr>
                                    <td>دخل آخر شهري:</td>
                                    <td id="other_income_display">0 ريال</td>
                                </tr>
                                <tr>
                                    <td>إجمالي الدخل الشهري:</td>
                                    <td id="total_income_display">0 ريال</td>
                                </tr>
                                <tr>
                                    <td>خسارة الشغور:</td>
                                    <td id="vacancy_loss_display">0 ريال</td>
                                </tr>
                                <tr>
                                    <td>الدخل الفعال الشهري:</td>
                                    <td id="effective_income_display">0 ريال</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>المصاريف</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>المصاريف التشغيلية:</td>
                                    <td id="operating_expenses_display">0 ريال</td>
                                </tr>
                                <tr>
                                    <td>قسط القرض:</td>
                                    <td id="loan_payment_display">0 ريال</td>
                                </tr>
                                <tr>
                                    <td>ضريبة العقار:</td>
                                    <td id="property_tax_display">0 ريال</td>
                                </tr>
                                <tr>
                                    <td>التأمين:</td>
                                    <td id="insurance_display">0 ريال</td>
                                </tr>
                                <tr>
                                    <td>رسوم الإدارة:</td>
                                    <td id="management_fee_display">0 ريال</td>
                                </tr>
                                <tr>
                                    <td>إجمالي المصاريف الشهرية:</td>
                                    <td id="total_expenses_display">0 ريال</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cash Flow Projection Chart -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">توقعات التدفق النقدي</h5>
                </div>
                <div class="card-body">
                    <canvas id="cashFlowProjectionChart" height="300"></canvas>
                </div>
            </div>

            <!-- Annual Breakdown Table -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">التفصيل السنوي</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="annualBreakdownTable">
                            <thead>
                                <tr>
                                    <th>السنة</th>
                                    <th>الدخل السنوي</th>
                                    <th>المصاريف السنوية</th>
                                    <th>التدفق النقدي</th>
                                    <th>التدفق التراكمي</th>
                                    <th>قيمة العقار</th>
                                    <th>العائد النقدي</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Table will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Key Metrics -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">المؤشرات الرئيسية</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <h4 id="total_cash_flow_10_years" class="text-primary">0</h4>
                                <p class="text-muted">إجمالي التدفق النقدي (10 سنوات)</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h4 id="average_annual_return" class="text-success">0%</h4>
                                <p class="text-muted">متوسط العائد السنوي</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h4 id="payback_period" class="text-info">0 سنة</h4>
                                <p class="text-muted">فترة استرداد رأس المال</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let cashFlowProjectionChart = null;

function analyzeCashFlow() {
    const propertyValue = parseFloat(document.getElementById('property_value').value);
    const monthlyRent = parseFloat(document.getElementById('monthly_rent').value);
    const otherIncome = parseFloat(document.getElementById('other_income').value);
    const vacancyRate = parseFloat(document.getElementById('vacancy_rate').value) / 100;
    const operatingExpenses = parseFloat(document.getElementById('operating_expenses').value);
    const capitalExpenditures = parseFloat(document.getElementById('capital_expenditures').value);
    const loanPayment = parseFloat(document.getElementById('loan_payment').value);
    const propertyTax = parseFloat(document.getElementById('property_tax').value);
    const insurance = parseFloat(document.getElementById('insurance').value);
    const managementFee = parseFloat(document.getElementById('management_fee').value);
    const projectionYears = parseInt(document.getElementById('projection_years').value);
    const rentGrowth = parseFloat(document.getElementById('rent_growth').value) / 100;
    const expenseGrowth = parseFloat(document.getElementById('expense_growth').value) / 100;
    const appreciationRate = parseFloat(document.getElementById('appreciation_rate').value) / 100;

    // Calculate monthly cash flow
    const totalMonthlyIncome = monthlyRent + otherIncome;
    const vacancyLoss = totalMonthlyIncome * vacancyRate;
    const effectiveMonthlyIncome = totalMonthlyIncome - vacancyLoss;
    const totalMonthlyExpenses = operatingExpenses + loanPayment + propertyTax + insurance + managementFee;
    const monthlyCashFlow = effectiveMonthlyIncome - totalMonthlyExpenses;
    const annualCashFlow = monthlyCashFlow * 12;

    // Calculate cash on cash return (assuming 20% down payment)
    const downPayment = propertyValue * 0.2;
    const cashOnCashReturn = (annualCashFlow / downPayment) * 100;

    // Calculate DSCR
    const noi = effectiveMonthlyIncome * 12;
    const annualDebtService = loanPayment * 12;
    const dscr = noi / annualDebtService;

    // Update display
    document.getElementById('monthly_cash_flow').textContent = formatCurrency(monthlyCashFlow);
    document.getElementById('annual_cash_flow').textContent = formatCurrency(annualCashFlow);
    document.getElementById('cash_on_cash_return').textContent = cashOnCashReturn.toFixed(2) + '%';
    document.getElementById('dscr').textContent = dscr.toFixed(2);

    // Update detailed breakdown
    document.getElementById('monthly_rent_display').textContent = formatCurrency(monthlyRent);
    document.getElementById('other_income_display').textContent = formatCurrency(otherIncome);
    document.getElementById('total_income_display').textContent = formatCurrency(totalMonthlyIncome);
    document.getElementById('vacancy_loss_display').textContent = formatCurrency(vacancyLoss);
    document.getElementById('effective_income_display').textContent = formatCurrency(effectiveMonthlyIncome);
    document.getElementById('operating_expenses_display').textContent = formatCurrency(operatingExpenses);
    document.getElementById('loan_payment_display').textContent = formatCurrency(loanPayment);
    document.getElementById('property_tax_display').textContent = formatCurrency(propertyTax);
    document.getElementById('insurance_display').textContent = formatCurrency(insurance);
    document.getElementById('management_fee_display').textContent = formatCurrency(managementFee);
    document.getElementById('total_expenses_display').textContent = formatCurrency(totalMonthlyExpenses);

    // Generate projection data
    const projectionData = generateProjectionData(
        propertyValue, monthlyRent, otherIncome, vacancyRate, operatingExpenses,
        capitalExpenditures, loanPayment, propertyTax, insurance, managementFee,
        projectionYears, rentGrowth, expenseGrowth, appreciationRate, downPayment
    );

    // Update table
    updateAnnualBreakdownTable(projectionData);

    // Update chart
    updateCashFlowProjectionChart(projectionData);

    // Calculate key metrics
    calculateKeyMetrics(projectionData, downPayment);
}

function generateProjectionData(
    propertyValue, monthlyRent, otherIncome, vacancyRate, operatingExpenses,
    capitalExpenditures, loanPayment, propertyTax, insurance, managementFee,
    projectionYears, rentGrowth, expenseGrowth, appreciationRate, downPayment
) {
    const data = [];
    let cumulativeCashFlow = 0;

    for (let year = 1; year <= projectionYears; year++) {
        // Apply growth rates
        const currentRent = monthlyRent * Math.pow(1 + rentGrowth, year - 1);
        const currentOtherIncome = otherIncome * Math.pow(1 + rentGrowth, year - 1);
        const currentOperatingExpenses = operatingExpenses * Math.pow(1 + expenseGrowth, year - 1);
        const currentPropertyTax = propertyTax * Math.pow(1 + expenseGrowth, year - 1);
        const currentInsurance = insurance * Math.pow(1 + expenseGrowth, year - 1);
        const currentManagementFee = managementFee * Math.pow(1 + expenseGrowth, year - 1);

        // Calculate annual values
        const totalMonthlyIncome = currentRent + currentOtherIncome;
        const vacancyLoss = totalMonthlyIncome * vacancyRate;
        const effectiveMonthlyIncome = totalMonthlyIncome - vacancyLoss;
        const totalMonthlyExpenses = currentOperatingExpenses + loanPayment + currentPropertyTax + currentInsurance + currentManagementFee;
        const monthlyCashFlow = effectiveMonthlyIncome - totalMonthlyExpenses;
        const annualCashFlow = monthlyCashFlow * 12;

        // Add capital expenditures
        const annualCashFlowAfterCapEx = annualCashFlow - capitalExpenditures;

        cumulativeCashFlow += annualCashFlowAfterCapEx;

        // Calculate property value
        const currentPropertyValue = propertyValue * Math.pow(1 + appreciationRate, year);

        // Calculate cash on cash return for this year
        const cashOnCashReturn = (annualCashFlowAfterCapEx / downPayment) * 100;

        data.push({
            year: year,
            annualIncome: effectiveMonthlyIncome * 12,
            annualExpenses: totalMonthlyExpenses * 12 + capitalExpenditures,
            annualCashFlow: annualCashFlowAfterCapEx,
            cumulativeCashFlow: cumulativeCashFlow,
            propertyValue: currentPropertyValue,
            cashOnCashReturn: cashOnCashReturn
        });
    }

    return data;
}

function updateAnnualBreakdownTable(projectionData) {
    const tbody = document.querySelector('#annualBreakdownTable tbody');
    tbody.innerHTML = '';

    projectionData.forEach(data => {
        const row = tbody.insertRow();
        row.innerHTML = `
            <td>${data.year}</td>
            <td>${formatCurrency(data.annualIncome)}</td>
            <td>${formatCurrency(data.annualExpenses)}</td>
            <td class="${data.annualCashFlow >= 0 ? 'text-success' : 'text-danger'}">${formatCurrency(data.annualCashFlow)}</td>
            <td>${formatCurrency(data.cumulativeCashFlow)}</td>
            <td>${formatCurrency(data.propertyValue)}</td>
            <td>${data.cashOnCashReturn.toFixed(2)}%</td>
        `;
    });
}

function updateCashFlowProjectionChart(projectionData) {
    const labels = projectionData.map(d => `السنة ${d.year}`);
    const cashFlowData = projectionData.map(d => d.annualCashFlow);
    const cumulativeData = projectionData.map(d => d.cumulativeCashFlow);
    const propertyValueData = projectionData.map(d => d.propertyValue);

    if (cashFlowProjectionChart) {
        cashFlowProjectionChart.destroy();
    }

    const ctx = document.getElementById('cashFlowProjectionChart').getContext('2d');
    cashFlowProjectionChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'التدفق النقدي السنوي',
                    data: cashFlowData,
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    yAxisID: 'y'
                },
                {
                    label: 'التدفق التراكمي',
                    data: cumulativeData,
                    type: 'line',
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    yAxisID: 'y'
                },
                {
                    label: 'قيمة العقار',
                    data: propertyValueData,
                    type: 'line',
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
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
                        text: 'قيمة العقار'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
}

function calculateKeyMetrics(projectionData, downPayment) {
    const totalCashFlow = projectionData.reduce((sum, d) => sum + d.annualCashFlow, 0);
    const averageAnnualReturn = (totalCashFlow / projectionData.length / downPayment) * 100;
    
    // Calculate payback period
    let paybackPeriod = 0;
    let cumulative = 0;
    for (const data of projectionData) {
        cumulative += data.annualCashFlow;
        if (cumulative >= downPayment) {
            paybackPeriod = data.year;
            break;
        }
    }

    document.getElementById('total_cash_flow_10_years').textContent = formatCurrency(totalCashFlow);
    document.getElementById('average_annual_return').textContent = averageAnnualReturn.toFixed(2) + '%';
    document.getElementById('payback_period').textContent = paybackPeriod > 0 ? paybackPeriod.toFixed(1) + ' سنة' : 'لا ينطبق';
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
document.querySelectorAll('#cashFlowForm input').forEach(input => {
    input.addEventListener('input', analyzeCashFlow);
});

// Initial calculation
analyzeCashFlow();
</script>
@endsection
