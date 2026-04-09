@extends('layouts.app')

@section('title', 'تفاصيل المقاول')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">تفاصيل المقاول: {{ $contractor->company_name }}</h3>
                    <div>
                        <a href="{{ route('developer.contractors.edit', $contractor->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> تعديل
                        </a>
                        <a href="{{ route('developer.contractors.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-right"></i> العودة للقائمة
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Company Info -->
                        <div class="col-md-4 text-center mb-4">
                            @if($contractor->company_logo)
                                <img src="{{ Storage::url($contractor->company_logo) }}" alt="{{ $contractor->company_name }}" class="img-fluid rounded-circle mb-3" style="max-height: 150px;">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center rounded-circle mx-auto mb-3" style="width: 150px; height: 150px;">
                                    <i class="fas fa-building fa-4x text-secondary"></i>
                                </div>
                            @endif
                            <h4>{{ $contractor->company_name }}</h4>
                            <p class="text-muted">{{ $contractor->specialization }}</p>
                            <span class="badge bg-{{ $contractor->status == 'active' ? 'success' : 'secondary' }}">
                                {{ $contractor->status == 'active' ? 'نشط' : 'غير نشط' }}
                            </span>
                        </div>

                        <!-- Contact Details -->
                        <div class="col-md-8">
                            <h5 class="border-bottom pb-2">معلومات الاتصال</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>الشخص المسؤول:</strong> {{ $contractor->contact_person }}
                                </div>
                                <div class="col-md-6">
                                    <strong>الهاتف:</strong> {{ $contractor->contact_phone }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>البريد الإلكتروني:</strong> {{ $contractor->contact_email }}
                                </div>
                                <div class="col-md-6">
                                    <strong>العنوان:</strong> {{ $contractor->address }}, {{ $contractor->city }}, {{ $contractor->country }}
                                </div>
                            </div>

                            <h5 class="border-bottom pb-2 mt-4">التفاصيل المهنية</h5>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>رقم الترخيص:</strong> {{ $contractor->license_number ?? 'غير متوفر' }}
                                </div>
                                <div class="col-md-6">
                                    <strong>الرقم الضريبي:</strong> {{ $contractor->tax_id ?? 'غير متوفر' }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>سنة التأسيس:</strong> {{ $contractor->established_year ?? 'غير متوفر' }}
                                </div>
                                <div class="col-md-6">
                                    <strong>سنوات الخبرة:</strong> {{ $contractor->experience_years ?? 0 }} سنوات
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>حجم الشركة:</strong> 
                                    @switch($contractor->company_size)
                                        @case('small') صغيرة @break
                                        @case('medium') متوسطة @break
                                        @case('large') كبيرة @break
                                        @default غير محدد
                                    @endswitch
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab">التفاصيل والملاحظات</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">المستندات</button>
                                </li>
                            </ul>
                            <div class="tab-content p-3 border border-top-0 rounded-bottom" id="myTabContent">
                                <div class="tab-pane fade show active" id="details" role="tabpanel">
                                    @if($contractor->notes)
                                        <h6>ملاحظات:</h6>
                                        <p>{{ $contractor->notes }}</p>
                                    @else
                                        <p class="text-muted">لا توجد ملاحظات إضافية.</p>
                                    @endif
                                </div>
                                <div class="tab-pane fade" id="documents" role="tabpanel">
                                    @if(!empty($contractor->documents))
                                        <div class="list-group">
                                            @foreach($contractor->documents as $doc)
                                                <a href="{{ Storage::url($doc['path']) }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <i class="fas fa-file-alt me-2"></i> {{ $doc['name'] }}
                                                    </div>
                                                    <span class="badge bg-secondary rounded-pill">{{ $doc['type'] }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted">لا توجد مستندات مرفقة.</p>
                                    @endif
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
