@extends('admin.layouts.admin')

@section('title', 'لوحة تحكم الذكاء الاصطناعي التنبؤي')

@section('content')
<div class="container-fluid" dir="rtl">
    <!-- Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="fas fa-chart-line text-primary ml-2"></i>
            لوحة تحكم الذكاء الاصطناعي التنبؤي
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group mr-2">
                <a href="{{ route('bigdata.predictive-ai') }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-arrow-left ml-1"></i>
                    العودة
                </a>
                <button type="button" class="btn btn-sm btn-outline-success" onclick="generateReport()">
                    <i class="fas fa-file-download ml-1"></i>
                    تقرير
                </button>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">اتجاهات التنبؤات</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="predictionTrendsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">دقة التنبؤات</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="accuracyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Stats -->
    <div class="row">
        <!-- Property Price Predictions -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-home ml-2"></i>
                        تنبؤات أسعار العقارات
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h4 class="text-success">+12.5%</h4>
                        <p class="text-muted">متوسط التغير المتوقع في الأسعار</p>
                    </div>
                    <div class="row text-center">
                        <div class="col-4">
                            <h6 class="text-primary">+8.2%</h6>
                            <small>الربع القادم</small>
                        </div>
                        <div class="col-4">
                            <h6 class="text-success">+15.7%</h6>
                            <small>السنة القادمة</small>
                        </div>
                        <div class="col-4">
                            <h6 class="text-info">+32.4%</h6>
                            <small>3 سنوات</small>
                        </div>
                    </div>
                    <div class="mt-3">
                        <h6 class="text-sm">الأسواق الساخنة:</h6>
                        <div class="d-flex flex-wrap">
                            <span class="badge badge-primary m-1">الرياض</span>
                            <span class="badge badge-success m-1">جدة</span>
                            <span class="badge badge-info m-1">الدمام</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Market Trends -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-area ml-2"></i>
                        اتجاهات السوق
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h4 class="text-success">إيجابي</h4>
                        <p class="text-muted">اتجاه السوق العام</p>
                    </div>
                    <div class="row text-center">
                        <div class="col-4">
                            <h6 class="text-primary">78.5</h6>
                            <small>مؤشر الطلب</small>
                        </div>
                        <div class="col-4">
                            <h6 class="text-warning">65.2</h6>
                            <small>مؤشر العرض</small>
                        </div>
                        <div class="col-4">
                            <h6 class="text-info">72.8</h6>
                            <small>مؤشر القدرة</small>
                        </div>
                    </div>
                    <div class="mt-3">
                        <h6 class="text-sm">المؤشرات الرئيسية:</h6>
                        <div class="progress mb-2" style="height: 15px;">
                            <div class="progress-bar bg-success" style="width: 78%">الطلب</div>
                        </div>
                        <div class="progress mb-2" style="height: 15px;">
                            <div class="progress-bar bg-warning" style="width: 65%">العرض</div>
                        </div>
                        <div class="progress" style="height: 15px;">
                            <div class="progress-bar bg-info" style="width: 73%">القدرة الشرائية</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Investment Opportunities -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-coins ml-2"></i>
                        فرص الاستثمار
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="text-sm mb-3">المناطق ذات الإمكانات العالية:</h6>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">الرياض الشمال</h6>
                                    <small class="text-muted">عائد متوقع: 18-22%</small>
                                </div>
                                <span class="badge badge-success">مرتفع</span>
                            </div>
                        </div>
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">جدة الغرب</h6>
                                    <small class="text-muted">عائد متوقع: 15-18%</small>
                                </div>
                                <span class="badge badge-primary">جيد</span>
                            </div>
                        </div>
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">الدمام الشرق</h6>
                                    <small class="text-muted">عائد متوقع: 12-16%</small>
                                </div>
                                <span class="badge badge-info">متوسط</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Risk Assessment -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-exclamation-triangle ml-2"></i>
                        تقييم المخاطر
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h4 class="text-warning">متوسط</h4>
                        <p class="text-muted">مستوى المخاطر العام</p>
                    </div>
                    <div class="row text-center">
                        <div class="col-4">
                            <h6 class="text-success">منخفض</h6>
                            <small>مخاطر السوق</small>
                        </div>
                        <div class="col-4">
                            <h6 class="text-success">منخفض</h6>
                            <small>مخاطر اقتصادية</small>
                        </div>
                        <div class="col-4">
                            <h6 class="text-success">منخفض</h6>
                            <small>مخاطر تنظيمية</small>
                        </div>
                    </div>
                    <div class="mt-3">
                        <h6 class="text-sm">عوامل المخاطر الرئيسية:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-chevron-left text-primary ml-2"></i>تغيرات الأسعار</li>
                            <li><i class="fas fa-chevron-left text-primary ml-2"></i>سياسات الحكومة</li>
                            <li><i class="fas fa-chevron-left text-primary ml-2"></i>ظروف السوق العالمية</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(document).ready(function() {
    // Prediction Trends Chart
    var ctx1 = document.getElementById("predictionTrendsChart");
    var predictionTrendsChart = new Chart(ctx1, {
        type: 'line',
        data: {
            labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
            datasets: [{
                label: 'التنبؤات الفعلية',
                lineTension: 0.3,
                backgroundColor: "rgba(78, 115, 223, 0.05)",
                borderColor: "rgba(78, 115, 223, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(78, 115, 223, 1)",
                pointBorderColor: "rgba(78, 115, 223, 1)",
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                data: [65, 72, 78, 81, 85, 89]
            }, {
                label: 'القيم الفعلية',
                lineTension: 0.3,
                backgroundColor: "rgba(28, 200, 138, 0.05)",
                borderColor: "rgba(28, 200, 138, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(28, 200, 138, 1)",
                pointBorderColor: "rgba(28, 200, 138, 1)",
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(28, 200, 138, 1)",
                pointHoverBorderColor: "rgba(28, 200, 138, 1)",
                data: [68, 70, 80, 79, 86, 87]
            }]
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                xAxes: [{
                    time: {
                        unit: 'date'
                    },
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 7
                    }
                }],
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        callback: function(value) {
                            return number + '%';
                        }
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }]
            },
            legend: {
                display: true,
                position: 'top'
            },
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                intersect: false,
                mode: 'index',
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, chart) {
                        var label = chart.datasets[tooltipItem.datasetIndex].label || '';
                        if (label) {
                            label += ': ';
                        }
                        if (tooltipItem.yLabel !== null) {
                            label += tooltipItem.yLabel + '%';
                        }
                        return label;
                    }
                }
            }
        }
    });

    // Accuracy Chart
    var ctx2 = document.getElementById("accuracyChart");
    var accuracyChart = new Chart(ctx2, {
        type: 'doughnut',
        data: {
            labels: ['دقيق', 'قريب', 'بعيد'],
            datasets: [{
                data: [87.5, 9.2, 3.3],
                backgroundColor: ['#1cc88a', '#36b9cc', '#e74a3b'],
                hoverBackgroundColor: ['#17a673', '#2c9faf', '#c0392b'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, chart) {
                        var label = chart.labels[tooltipItem.index] || '';
                        if (label) {
                            label += ': ';
                        }
                        if (tooltipItem.parsed !== null) {
                            label += tooltipItem.parsed + '%';
                        }
                        return label;
                    }
                }
            },
            legend: {
                display: true,
                position: 'bottom'
            }
        }
    });
});

function generateReport() {
    $.ajax({
        url: '{{ route("bigdata.predictive-ai.generate-report") }}',
        method: 'POST',
        data: {
            report_type: 'comprehensive',
            format: 'pdf'
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                window.open(response.report_url, '_blank');
            } else {
                alert('فشل إنشاء التقرير: ' + response.message);
            }
        },
        error: function(xhr) {
            alert('حدث خطأ: ' + xhr.responseJSON?.message || 'يرجى المحاولة مرة أخرى');
        }
    });
}
</script>
@endpush
