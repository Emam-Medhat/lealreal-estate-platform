@extends('layouts.app')

@section('title', 'إنشاء تقرير أداء جديد')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-3">إنشاء تقرير أداء جديد</h2>
                    <p class="text-muted">إنشاء تقرير أداء جديد للوكلاء والعقارات</p>
                </div>
                <div>
                    <a href="{{ route('reports.performance.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> العودة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>✅ نجاح:</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>❌ خطأ:</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>⚠️ يرجى تصحيح الأخطاء التالية:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">معلومات التقرير</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('reports.performance.store') }}">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="agent_id" class="form-label">الوكيل</label>
                                <select class="form-select @error('agent_id') is-invalid @enderror" id="agent_id" name="agent_id">
                                    <option value="">اختر الوكيل</option>
                                    @foreach($agents as $agent)
                                        <option value="{{ $agent->id }}" {{ old('agent_id') == $agent->id ? 'selected' : '' }}>{{ $agent->user->name ?? $agent->name }}</option>
                                    @endforeach
                                </select>
                                @error('agent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="period_start" class="form-label">تاريخ البدء *</label>
                                <input type="date" class="form-control @error('period_start') is-invalid @enderror" id="period_start" name="period_start" value="{{ old('period_start') }}" required>
                                @error('period_start')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="period_end" class="form-label">تاريخ الانتهاء *</label>
                                <input type="date" class="form-control @error('period_end') is-invalid @enderror" id="period_end" name="period_end" value="{{ old('period_end') }}" required>
                                @error('period_end')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="report_type" class="form-label">نوع التقرير</label>
                                <select class="form-select" id="report_type" name="report_type">
                                    <option value="monthly">شهري</option>
                                    <option value="quarterly">ربع سنوي</option>
                                    <option value="yearly">سنوي</option>
                                    <option value="custom">مخصص</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">عنوان التقرير *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" placeholder="أدخل عنوان التقرير" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="format" class="form-label">تنسيق التقرير *</label>
                            <select class="form-select @error('format') is-invalid @enderror" id="format" name="format" required>
                                <option value="">اختر التنسيق</option>
                                <option value="pdf" {{ old('format') == 'pdf' ? 'selected' : '' }}>PDF</option>
                                <option value="excel" {{ old('format') == 'excel' ? 'selected' : '' }}>Excel</option>
                                <option value="csv" {{ old('format') == 'csv' ? 'selected' : '' }}>CSV</option>
                            </select>
                            @error('format')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">وصف التقرير</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="أدخل وصف التقرير"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="include_charts" name="include_charts" checked>
                                <label class="form-check-label" for="include_charts">
                                    تضمين الرسوم البيانية
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="include_details" name="include_details" checked>
                                <label class="form-check-label" for="include_details">
                                    تضمين التفاصيل الكاملة
                                </label>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ التقرير
                            </button>
                            <a href="{{ route('reports.performance.index') }}" class="btn btn-secondary">
                                إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">معلومات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h6><i class="fas fa-info-circle"></i> ملاحظات</h6>
                        <p class="mb-0">سيتم إنشاء التقرير تلقائياً بناءً على البيانات المتاحة في الفترة المحددة.</p>
                    </div>
                    
                    <div class="mb-3">
                        <h6>الإحصائيات المتاحة:</h6>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check text-success"></i> إجمالي المبيعات</li>
                            <li><i class="fas fa-check text-success"></i> عدد العقارات المباعة</li>
                            <li><i class="fas fa-check text-success"></i> معدل التحويل</li>
                            <li><i class="fas fa-check text-success"></i> متوسط سعر البيع</li>
                            <li><i class="fas fa-check text-success"></i> أداء الوكيل</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
