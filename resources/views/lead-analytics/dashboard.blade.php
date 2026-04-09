@extends('admin.layouts.admin')

@section('title', 'تحليلات العملاء المحتملين')

@section('content')
<!-- Page Header -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">تحليلات العملاء المحتملين</h1>
    <form action="{{ route('lead-analytics.export') }}" method="POST" class="d-inline">
        @csrf
        <input type="hidden" name="type" value="leads">
        <input type="hidden" name="format" value="xlsx">
        <button type="submit" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> تحميل التقرير (Excel)
        </button>
    </form>
</div>

<!-- Stats Cards -->
<div class="row">
    <!-- Total Leads -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            إجمالي العملاء</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_leads'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- New Leads -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            عملاء جدد (آخر 30 يوم)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['new_leads'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-plus fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Conversion Rate -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">معدل التحويل
                        </div>
                        <div class="row no-gutters align-items-center">
                            <div class="col-auto">
                                <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                    {{ number_format($stats['conversion_rate'], 1) }}%
                                </div>
                            </div>
                            <div class="col">
                                <div class="progress progress-sm mr-2">
                                    <div class="progress-bar bg-info" role="progressbar"
                                        style="width: {{ $stats['conversion_rate'] }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Value -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            القيمة الإجمالية</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($stats['total_value']) }} ريال
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Monthly Leads Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">نمو العملاء الشهري</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="monthlyLeadsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lead Status Chart -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">توزيع الحالات</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="leadStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Conversion Sources Row -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">أداء مصادر العملاء</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>المصدر</th>
                                    <th>عدد التحويلات</th>
                                    <th>القيمة الإجمالية</th>
                                    <th>نسبة الأداء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($conversionBySource as $source)
                                <tr>
                                    <td>{{ $source->source }}</td>
                                    <td>{{ $source->conversions }}</td>
                                    <td>{{ number_format($source->value) }} ريال</td>
                                    <td>
                                        @php
                                            $percentage = ($source->conversions / max($stats['converted_leads'], 1)) * 100;
                                        @endphp
                                        <div class="progress">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: {{ $percentage }}%">
                                                {{ number_format($percentage, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
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
    // Monthly Leads Chart
    var ctx = document.getElementById("monthlyLeadsChart");
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlyLeads->map(fn($item) => $item->year . '-' . $item->month)) !!},
            datasets: [{
                label: "العملاء الجدد",
                lineTension: 0.3,
                backgroundColor: "rgba(78, 115, 223, 0.05)",
                borderColor: "rgba(78, 115, 223, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(78, 115, 223, 1)",
                pointBorderColor: "rgba(78, 115, 223, 1)",
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                data: {!! json_encode($monthlyLeads->pluck('count')) !!},
            }],
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
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }],
            },
            legend: {
                display: false
            },
        }
    });

    // Lead Status Chart
    var ctx = document.getElementById("leadStatusChart");
    var myPieChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($leadByStatus->pluck('status')) !!},
            datasets: [{
                data: {!! json_encode($leadByStatus->pluck('count')) !!},
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617'],
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
            },
            legend: {
                display: true,
                position: 'bottom'
            },
            cutoutPercentage: 80,
        },
    });
</script>
@endpush