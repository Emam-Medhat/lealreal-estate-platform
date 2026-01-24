@extends('layouts.app')

@section('title', 'توريق العقارات')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">توريق العقارات</h1>
                <div class="btn-group">
                    <a href="{{ route('blockchain.tokenization.marketplace') }}" class="btn btn-primary">
                        <i class="fas fa-store"></i> السوق
                    </a>
                    <a href="{{ route('blockchain.tokenization.portfolio') }}" class="btn btn-outline-primary">
                        <i class="fas fa-briefcase"></i> المحفظة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tokenization Statistics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($tokenizedProperties) }}</h4>
                            <p>العقارات الموريقة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-building fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($totalTokens) }}</h4>
                            <p>إجمالي الرموز</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-coins fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($totalValue, 2) }} ريال</h4>
                            <p>القيمة الإجمالية</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($tokenHolders) }}</h4>
                            <p>حاملي الرموز</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Properties for Tokenization -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">العقارات المتاحة للتوريق</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($availableProperties as $property)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100">
                                <div class="card-img-top position-relative">
                                    <img src="{{ $property->image_url ?? asset('images/property-placeholder.jpg') }}" 
                                         class="card-img-top" alt="{{ $property->title }}" style="height: 200px; object-fit: cover;">
                                    <span class="badge bg-success position-absolute top-0 end-0 m-2">متاح للتوريق</span>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">{{ $property->title }}</h5>
                                    <p class="card-text text-muted small">{{ $property->description }}</p>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span>القيمة:</span>
                                            <span class="fw-bold">{{ number_format($property->price, 2) }} ريال</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>المساحة:</span>
                                            <span class="fw-bold">{{ $property->area }} م²</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>الموقع:</span>
                                            <span class="fw-bold">{{ $property->location }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <small class="text-muted">الحد الأدنى للرمز:</small>
                                            <div class="fw-bold">{{ number_format($property->min_token_price, 2) }} ريال</div>
                                        </div>
                                        <a href="{{ route('blockchain.tokenization.tokenize', $property->id) }}" class="btn btn-primary">
                                            <i class="fas fa-coins"></i> توريق
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tokenized Properties -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">العقارات الموريقة</h5>
                    <a href="{{ route('blockchain.tokenization.tokens') }}" class="btn btn-sm btn-outline-primary">عرض الكل</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>العقار</th>
                                    <th>الرمز</th>
                                    <th>القيمة</th>
                                    <th>الرموز المصدرة</th>
                                    <th>سعر الرمز</th>
                                    <th>الرموز المباعة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tokenizedPropertiesList as $property)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <i class="fas fa-building text-primary"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $property->title }}</div>
                                                <small class="text-muted">{{ $property->location }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <img src="{{ $property->token_image ?? asset('images/token-placeholder.png') }}" 
                                                     width="24" height="24" class="rounded-circle" alt="Token">
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $property->token_symbol }}</div>
                                                <small class="text-muted">{{ $property->token_name }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ number_format($property->total_value, 2) }} ريال</div>
                                        <small class="text-muted">${{ number_format($property->total_value_usd, 2) }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ number_format($property->total_tokens) }}</div>
                                        <small class="text-muted">من {{ number_format($property->max_tokens) }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ number_format($property->token_price, 2) }} ريال</div>
                                        <small class="text-muted">${{ number_format($property->token_price_usd, 4) }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ number_format($property->tokens_sold) }}</div>
                                        <small class="text-muted">{{ number_format($property->sold_percentage, 1) }}%</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('blockchain.tokenization.show', $property->token_id) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('blockchain.tokenization.trade', $property->token_id) }}" class="btn btn-outline-success">
                                                <i class="fas fa-exchange-alt"></i>
                                            </a>
                                            <a href="{{ route('blockchain.tokenization.governance', $property->token_id) }}" class="btn btn-outline-info">
                                                <i class="fas fa-users"></i>
                                            </a>
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

    <!-- Investment Opportunities -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">فرص الاستثمار</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-chart-line fa-3x text-success mb-3"></i>
                                    <h6>نمو رأس المال</h6>
                                    <p class="text-muted small">متوسط نمو سنوي 8-12%</p>
                                    <div class="progress mt-2">
                                        <div class="progress-bar bg-success" style="width: 85%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-coins fa-3x text-warning mb-3"></i>
                                    <h6>دخل الإيجار</h6>
                                    <p class="text-muted small">عائد إيجاري سنوي 5-8%</p>
                                    <div class="progress mt-2">
                                        <div class="progress-bar bg-warning" style="width: 70%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <i class="fas fa-shield-alt fa-3x text-info mb-3"></i>
                                    <h6>أمان الاستثمار</h6>
                                    <p class="text-muted small">محمي بالعقار الفعلي</p>
                                    <div class="progress mt-2">
                                        <div class="progress-bar bg-info" style="width: 95%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tokenization Process -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">عملية التوريق</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary">1</div>
                                    <div class="timeline-content">
                                        <h6>اختيار العقار</h6>
                                        <p class="text-muted">اختر العقار الذي تريد توريقه</p>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary">2</div>
                                    <div class="timeline-content">
                                        <h6>التقييم</h6>
                                        <p class="text-muted">تقييم العقار وتحديد القيمة السوقية</p>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary">3</div>
                                    <div class="timeline-content">
                                        <h6>إنشاء الرموز</h6>
                                        <p class="text-muted">إنشاء الرموز الرقمية على البلوكشين</p>
                                    </div>
                                </div>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary">4</div>
                                    <div class="timeline-content">
                                        <h6>البيع للمستثمرين</h6>
                                        <p class="text-muted">بيع الرموز للمستثمرين في السوق</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Token price chart
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('tokenPriceChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
                datasets: [{
                    label: 'سعر الرمز (ريال)',
                    data: [1000, 1050, 1020, 1080, 1100, 1150],
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: false
                    }
                }
            }
        });
    }
});

// Auto-refresh token data
setInterval(() => {
    fetch('{{ route("blockchain.tokenization.refresh") }}')
        .then(response => response.json())
        .then(data => {
            // Update token statistics
            document.querySelector('#total-tokens').textContent = data.total_tokens;
            document.querySelector('#total-value').textContent = `${data.total_value} ريال`;
            document.querySelector('#token-holders').textContent = data.token_holders;
        });
}, 30000); // Refresh every 30 seconds
</script>
@endsection
