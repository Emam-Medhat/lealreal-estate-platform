@extends('layouts.app')

@section('title', 'المقاولين')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">المقاولين</h3>
                    <a href="{{ route('developer.contractors.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إضافة مقاول
                    </a>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form action="{{ route('developer.contractors.index') }}" method="GET" class="row g-3">
                                <div class="col-md-4">
                                    <input type="text" name="search" class="form-control" placeholder="بحث عن مقاول..." value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <select name="specialization" class="form-select">
                                        <option value="">كل التخصصات</option>
                                        <option value="construction" {{ request('specialization') == 'construction' ? 'selected' : '' }}>بناء</option>
                                        <option value="plumbing" {{ request('specialization') == 'plumbing' ? 'selected' : '' }}>سباكة</option>
                                        <option value="electrical" {{ request('specialization') == 'electrical' ? 'selected' : '' }}>كهرباء</option>
                                        <option value="hvac" {{ request('specialization') == 'hvac' ? 'selected' : '' }}>تكييف وتبريد</option>
                                        <option value="painting" {{ request('specialization') == 'painting' ? 'selected' : '' }}>دهانات</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-select">
                                        <option value="">كل الحالات</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100">بحث</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>اسم الشركة</th>
                                    <th>الشخص المسؤول</th>
                                    <th>التخصص</th>
                                    <th>المدينة</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contractors as $contractor)
                                    <tr>
                                        <td>{{ $contractor->company_name }}</td>
                                        <td>
                                            {{ $contractor->contact_person }}<br>
                                            <small class="text-muted">{{ $contractor->contact_phone }}</small>
                                        </td>
                                        <td>{{ $contractor->specialization }}</td>
                                        <td>{{ $contractor->city }}</td>
                                        <td>
                                            @if($contractor->status == 'active')
                                                <span class="badge bg-success">نشط</span>
                                            @else
                                                <span class="badge bg-secondary">غير نشط</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('developer.contractors.show', $contractor->id) }}" class="btn btn-sm btn-info" title="عرض">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('developer.contractors.edit', $contractor->id) }}" class="btn btn-sm btn-warning" title="تعديل">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('developer.contractors.destroy', $contractor->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">لا يوجد مقاولين مسجلين.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $contractors->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
