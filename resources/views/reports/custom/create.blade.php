@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">إنشاء تقرير جديد</h5>
                    <a href="{{ route('reports.custom.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> رجوع
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('reports.custom.store') }}" method="POST" id="createReportForm">
                        @csrf
                        
                        @if(request()->has('template'))
                            <input type="hidden" name="template_id" value="{{ request('template') }}">
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="title" class="form-label">اسم التقرير</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                       id="title" name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="category" class="form-label">التصنيف</label>
                                <select class="form-select @error('category') is-invalid @enderror" 
                                        id="category" name="category">
                                    <option value="">اختر التصنيف</option>
                                    <option value="sales" {{ old('category') == 'sales' ? 'selected' : '' }}>المبيعات</option>
                                    <option value="customers" {{ old('category') == 'customers' ? 'selected' : '' }}>العملاء</option>
                                    <option value="inventory" {{ old('category') == 'inventory' ? 'selected' : '' }}>المخزون</option>
                                    <option value="financial" {{ old('category') == 'financial' ? 'selected' : '' }}>مالي</option>
                                </select>
                                @error('category')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="data_sources" class="form-label">مصدر البيانات</label>
                            <select class="form-select @error('data_sources') is-invalid @enderror" 
                                    id="data_sources" name="data_sources[]" multiple required>
                                <option value="properties">العقارات</option>
                                <option value="users">المستخدمين</option>
                                <option value="transactions">المعاملات</option>
                                <option value="agents">الوكلاء</option>
                            </select>
                            <small class="text-muted">يمكنك اختيار أكثر من مصدر (اضغط Ctrl للتحديد المتعدد)</small>
                            @error('data_sources')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <!-- Hidden fields for query config until builder UI is ready -->
                        <input type="hidden" name="query_config[columns][]" value="*">
                        <input type="hidden" name="query_config[limit]" value="100">

                        <div class="mb-3">
                            <label for="description" class="form-label">الوصف</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                     id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">إعدادات التقرير</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                   id="is_public" name="is_public" {{ old('is_public') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_public">تقرير عام</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" role="switch" 
                                                   id="is_template" name="is_template" {{ old('is_template') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_template">حفظ كقالب</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> حفظ التقرير
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
