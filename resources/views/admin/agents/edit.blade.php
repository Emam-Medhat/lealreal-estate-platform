@extends('admin.layouts.admin')

@section('title', 'تعديل الوكيل')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">تعديل الوكيل</h1>
            <p class="text-muted mb-0">تعديل بيانات الوكيل: {{ $agent->name }}</p>
        </div>
        <div>
            <a href="{{ route('admin.agents.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
            </a>
            <a href="{{ route('admin.agents.show', $agent) }}" class="btn btn-info">
                <i class="fas fa-eye me-2"></i>عرض
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.agents.update', $agent) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">الاسم <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $agent->name }}" required>
                            @error('name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ $agent->email }}" required>
                            @error('email')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control" value="{{ $agent->phone ?? '' }}" required>
                            @error('phone')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">الحالة</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ $agent->status == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="inactive" {{ $agent->status == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                <option value="pending" {{ $agent->status == 'pending' ? 'selected' : '' }}>في انتظار</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="mb-3">
                            <label class="form-label">العنوان</label>
                            <textarea name="address" class="form-control" rows="3">{{ $agent->address ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>تحديث الوكيل
                        </button>
                        <a href="{{ route('admin.agents.index') }}" class="btn btn-outline-secondary">
                            إلغاء
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
