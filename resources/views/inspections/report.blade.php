@extends('layouts.app')

@section('title', 'تقرير الفحص')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3">تقرير الفحص</h1>
                <div class="d-flex gap-2">
                    <a href="{{ route('inspection-reports.download', $report) }}" class="btn btn-primary">
                        <i class="fas fa-download"></i> تحميل PDF
                    </a>
                    <a href="{{ route('inspection-reports.email', $report) }}" class="btn btn-info">
                        <i class="fas fa-envelope"></i> إرسال بالبريد
                    </a>
                    @if($report->inspection->status === 'completed' && !$report->inspection->report)
                        <a href="{{ route('inspection-reports.create', $report->inspection) }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> إنشاء تقرير
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
                    <h5>معلومات الفحص</h5>
                    <table class="table table-sm">
                        <tr>
                            <td><strong>رقم الفحص:</strong></td>
                            <td>#{{ $report->inspection->id }}</td>
                        </tr>
                        <tr>
                            <td><strong>العقار:</strong></td>
                            <td>{{ $report->inspection->property->title }}</td>
                        </tr>
                        <tr>
                            <td><strong>المفتش:</strong></td>
                            <td>{{ $report->inspector->name }}</td>
                        </tr>
                        <tr>
                            <td><strong>تاريخ الفحص:</strong></td>
                            <td>{{ $report->inspection->scheduled_date->format('Y-m-d H:i') }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5>تقييم الحالة</h5>
                    <div class="text-center">
                        <div class="display-4 text-{{ $report->getConditionColor() }}">
                            {{ $report->getConditionLabel() }}
                        </div>
                        <div class="mt-2">
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar bg-{{ $report->getConditionColor() }}" style="width: {{ $report->getScore() }}%">
                                    {{ $report->getScore() }}/100
                                </div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="badge bg-primary">الدرجة: {{ $report->getGrade() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Summary -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">ملخص التقرير</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>الوصف العام</h6>
                    <p>{{ $report->summary }}</p>
                </div>
                <div class="col-md-6">
                    <h6>التوصيات</h6>
                    <p>{{ $report->recommendations ?? 'لا توجد توصيات حالياً' }}</p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="text-center">
                        <h4 class="text-primary">{{ $report->getDefectCount() }}</h4>
                        <small class="text-muted">إجمالي العيوب</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <h4 class="text-danger">{{ $report->getCriticalDefectCount() }}</h4>
                        <small class="text-muted">عيوب حرجة</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <h4 class="text-warning">{{ number_format($report->getTotalDefectCost(), 2) }}</h4>
                        <small class="text-muted">تكلفة التقديرية</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Defects List -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">قائمة العيوب</h5>
            <a href="{{ route('defects.create', ['inspection_report_id' => $report->id]) }}" class="btn btn-sm btn-success">
                <i class="fas fa-plus"></i> إضافة عيب
            </a>
        </div>
        <div class="card-body">
            @if($report->defects->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>الوصف</th>
                                <th>الموقع</th>
                                <th>الشدة</th>
                                <th>الأولوية</th>
                                <th>التكلفة التقديرية</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($report->defects as $defect)
                                <tr>
                                    <td>{{ $defect->description }}</td>
                                    <td>{{ $defect->location }}</td>
                                    <td>
                                        <span class="badge bg-{{ $defect->getSeverityColor() }}">
                                            {{ $defect->getSeverityLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $defect->getUrgencyColor() }}">
                                            {{ $defect->getUrgencyLabel() }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($defect->estimated_cost, 2) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $defect->getStatusColor() }}">
                                            {{ $defect->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('defects.show', $defect) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('defects.edit', $defect) }}" class="btn btn-outline-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if(!$defect->hasRepairEstimate())
                                                <a href="{{ route('repair-estimates.create', ['defect_id' => $defect->id]) }}" class="btn btn-outline-info">
                                                    <i class="fas fa-calculator"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5>لا توجد عيوب</h5>
                    <p class="text-muted">لم يتم تسجيل أي عيوب في هذا الفحص</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Photos -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">الصور</h5>
            <button class="btn btn-sm btn-primary" onclick="document.getElementById('photo-upload').click()">
                <i class="fas fa-camera"></i> إضافة صور
            </button>
        </div>
        <div class="card-body">
            <form id="photo-upload-form" action="{{ route('inspection-reports.add-photo', $report) }}" method="POST" enctype="multipart/form-data" class="d-none">
                @csrf
                <input type="file" id="photo-upload" name="photo" accept="image/*" onchange="this.form.submit()">
            </form>
            
            <div class="row">
                @if($report->photos->count() > 0)
                    @foreach($report->photos as $photo)
                        <div class="col-md-3 mb-3">
                            <div class="card">
                                <img src="{{ asset('storage/' . $photo->file_path) }}" class="card-img-top" alt="Inspection Photo" style="height: 150px; object-fit: cover;">
                                <div class="card-body p-2">
                                    <small class="text-muted">{{ $photo->file_name }}</small>
                                    <form action="{{ route('inspection-reports.remove-photo', [$report, $photo]) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger float-start" onclick="return confirm('هل أنت متأكد من حذف هذه الصورة؟')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12 text-center py-4">
                        <i class="fas fa-images fa-3x text-muted mb-3"></i>
                        <h5>لا توجد صور</h5>
                        <p class="text-muted">لم يتم إضافة أي صور لهذا الفحص</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Next Inspection -->
    @if($report->next_inspection_date)
        <div class="card mb-4">
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-calendar-check"></i> الفحص التالي</h6>
                    <p class="mb-0">التاريخ المقترح للفحص التالي: {{ $report->next_inspection_date->format('Y-m-d') }}</p>
                    <p class="mb-0">المتبقي: {{ $report->getDaysUntilNextInspection() }} يوم</p>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
