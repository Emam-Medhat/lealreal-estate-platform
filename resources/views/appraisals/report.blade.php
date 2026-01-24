@extends('layouts.app')

@section('title', 'تقرير التقييم')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3">تقرير التقييم</h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('appraisal-reports.download', $report) }}" class="btn btn-primary">
                        <i class="fas fa-download"></i> تحميل PDF
                    </a>
                    <a href="{{ route('appraisal-reports.email', $report) }}" class="btn btn-info">
                        <i class="fas fa-envelope"></i> إرسال بالبريد
                    </a>
                    @if($report->isPending())
                        <a href="{{ route('appraisal-reports.approve', $report) }}" class="btn btn-success">
                            <i class="fas fa-check"></i> اعتماد
                        </a>
                        <a href="{{ route('appraisal-reports.reject', $report) }}" class="btn btn-danger">
                            <i class="fas fa-times"></i> رفض
                        </a>
                    @endif
                    @if($report->isApproved())
                        <a href="{{ route('appraisal-reports.certificate', $report) }}" class="btn btn-warning">
                            <i class="fas fa-certificate"></i> شهادة
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Report Header -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>معلومات التقييم</h5>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>رقم التقييم:</strong></td>
                            <td>#{{ $report->appraisal->id }}</td>
                        </tr>
                        <tr>
                            <td><strong>العقار:</strong></td>
                            <td>{{ $report->appraisal->property->title }}</td>
                        </tr>
                        <tr>
                            <td><strong>المقيم:</strong></td>
                            <td>{{ $report->appraiser->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>تاريخ التقييم:</strong></td>
                            <td>{{ $report->appraisal->scheduled_date->format('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>نوع التقييم:</strong></td>
                            <td>{{ $report->appraisal->getTypeLabel() }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>القيمة التقديرية</h5>
                    <div class="text-center">
                        <div class="display-4 text-primary">
                            {{ number_format($report->estimated_value, 2) }}
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">ريال سعودي</small>
                        </div>
                        <div class="mt-2">
                            <span class="badge bg-info">{{ number_format($report->value_per_sqm, 2) }} ريال/م²</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Status -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h5>حالة التقرير</h5>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-{{ $report->getStatusColor() }} fs-6 me-3">
                            {{ $report->getStatusLabel() }}
                        </span>
                        <div>
                            <small class="text-muted">
                                @if($report->isApproved())
                                    معتمد بواسطة: {{ $report->approved_by }} في {{ $report->approved_at->format('Y-m-d H:i') }}
                                @elseif($report->isRejected())
                                    مرفوض بواسطة: {{ $report->rejected_by }} في {{ $report->rejected_at->format('Y-m-d H:i') }}
                                @else
                                    في انتظار المراجعة
                                @endif
                            </small>
                        </div>
                    </div>
                    @if($report->approval_notes)
                        <div class="mt-2">
                            <strong>ملاحظات الاعتماد:</strong>
                            <p class="mb-0">{{ $report->approval_notes }}</p>
                        </div>
                    @endif
                    @if($report->rejection_reason)
                        <div class="mt-2">
                            <strong>سبب الرفض:</strong>
                            <p class="mb-0 text-danger">{{ $report->rejection_reason }}</p>
                        </div>
                    @endif
                </div>
                <div class="col-md-4">
                    <h5>مستوى الثقة</h5>
                    <div class="text-center">
                        <div class="display-6 text-{{ $report->getConfidenceColor() }}">
                            {{ $report->getConfidenceLevel() }}
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">بناءً على {{ $report->getComparableCount() }} عقار مماثل</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Market Analysis -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">تحليل السوق</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>التحليل العام</h6>
                    <p>{{ $report->market_analysis }}</p>
                </div>
                <div class="col-md-6">
                    <h6>حالة العقار</h6>
                    <p>{{ $report->property_condition }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparable Properties -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">العقارات المماثلة</h5>
        </div>
        <div class="card-body">
            @if($report->getComparableCount() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>العقار</th>
                                <th>المساحة</th>
                                <th>القيمة</th>
                                <th>القيمة/م²</th>
                                <th>التعديلات</th>
                                <th>القيمة المعدلة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report->comparable_properties ?? [] as $index => $comparable)
                                <tr>
                                    <td>{{ $comparable['title'] ?? 'عقار #' . ($index + 1) }}</td>
                                    <td>{{ $comparable['area'] ?? '-' }} م²</td>
                                    <td>{{ number_format($comparable['value'] ?? 0, 2) }}</td>
                                    <td>{{ number_format($comparable['value_per_sqm'] ?? 0, 2) }}</td>
                                    <td>{{ number_format($comparable['adjustment'] ?? 0, 2) }}</td>
                                    <td>{{ number_format($comparable['adjusted_value'] ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h5>لا توجد عقارات مماثلة كافية</h5>
                    <p class="text-muted">يحتاج التقرير إلى 3 عقارات مماثلة على الأقل</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Adjustments -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">التعديلات والتكاليف</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>التعديلات</h6>
                    @if($report->getAdjustmentCount() > 0)
                        <ul class="list-group">
                            @foreach($report->adjustments ?? [] as $adjustment)
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>{{ $adjustment['description'] }}</span>
                                    <span class="badge bg-{{ $adjustment['amount'] >= 0 ? 'success' : 'danger' }}">
                                        {{ number_format($adjustment['amount'], 2) }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">لا توجد تعديلات</p>
                    @endif
                </div>
                <div class="col-md-6">
                    <h6>ملخص التكاليف</h6>
                    <table class="table table-sm">
                        <tr>
                            <td>القيمة الأساسية:</td>
                            <td>{{ number_format($report->estimated_value, 2) }}</td>
                        </tr>
                        <tr>
                            <td>إجمالي التعديلات:</td>
                            <td class="{{ $report->getTotalAdjustmentAmount() >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($report->getTotalAdjustmentAmount(), 2) }}
                            </td>
                        </tr>
                        <tr class="fw-bold">
                            <td>القيمة النهائية:</td>
                            <td>{{ number_format($report->getAdjustedValue(), 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Conclusion and Recommendations -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">الاستنتاج والتوصيات</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>الاستنتاج</h6>
                    <p>{{ $report->conclusion }}</p>
                </div>
                <div class="col-md-6">
                    <h6>التوصيات</h6>
                    <p>{{ $report->recommendations ?? 'لا توجد توصيات حالياً' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Photos and Attachments -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">الصور والمرفقات</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>الصور</h6>
                    <div class="row">
                        @foreach($report->photos->take(6) as $photo)
                            <div class="col-4 mb-2">
                                <img src="{{ asset('storage/' . $photo->file_path) }}" class="img-thumbnail" alt="Property Photo">
                            </div>
                        @endforeach
                    </div>
                    @if($report->photos->count() > 6)
                        <small class="text-muted">+{{ $report->photos->count() - 6 }} صور أخرى</small>
                    @endif
                </div>
                <div class="col-md-6">
                    <h6>المرفقات</h6>
                    @if($report->attachments->count() > 0)
                        <ul class="list-group">
                            @foreach($report->attachments as $attachment)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ $attachment->file_name }}</span>
                                    <a href="{{ asset('storage/' . $attachment->file_path) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">لا توجد مرفقات</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
