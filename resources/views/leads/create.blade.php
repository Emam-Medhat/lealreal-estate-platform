@extends('layouts.app')

@section('title', 'إضافة عميل محتمل جديد')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">إضافة عميل محتمل جديد</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('leads.store') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">الاسم الأول *</label>
                                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                                    @error('first_name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">الاسم الأخير *</label>
                                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                                    @error('last_name')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">البريد الإلكتروني *</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                                    @error('email')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">الهاتف</label>
                                    <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">الشركة</label>
                                    <input type="text" name="company" class="form-control" value="{{ old('company') }}">
                                    @error('company')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">المنصب</label>
                                    <input type="text" name="position" class="form-control" value="{{ old('position') }}">
                                    @error('position')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">المصدر</label>
                                    <select name="source_id" class="form-select">
                                        <option value="">اختر المصدر</option>
                                        @foreach($sources as $source)
                                            <option value="{{ $source->id }}" {{ old('source_id') == $source->id ? 'selected' : '' }}>
                                                {{ $source->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('source_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">الحالة</label>
                                    <select name="status_id" class="form-select">
                                        <option value="">اختر الحالة</option>
                                        @foreach($statuses as $status)
                                            <option value="{{ $status->id }}" {{ old('status_id') == $status->id ? 'selected' : '' }}>
                                                {{ $status->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status_id')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">المسؤول</label>
                                    <select name="assigned_to" class="form-select">
                                        <option value="">غير محدد</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                                {{ $user->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('assigned_to')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">الأولوية</label>
                                    <select name="priority" class="form-select">
                                        <option value="1" {{ old('priority') == '1' ? 'selected' : '' }}>منخفضة</option>
                                        <option value="2" {{ old('priority') == '2' ? 'selected' : '' }}>متوسطة</option>
                                        <option value="3" {{ old('priority') == '3' ? 'selected' : '' }}>عالية</option>
                                    </select>
                                    @error('priority')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">القيمة المتوقعة</label>
                                    <input type="number" name="estimated_value" class="form-control" step="0.01" value="{{ old('estimated_value') }}">
                                    @error('estimated_value')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">تاريخ الإغلاق المتوقع</label>
                                    <input type="date" name="expected_close_date" class="form-control" value="{{ old('expected_close_date') }}">
                                    @error('expected_close_date')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">العنوان</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">المدينة</label>
                                    <input type="text" name="city" class="form-control" value="{{ old('city') }}">
                                    @error('city')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">الدولة</label>
                                    <input type="text" name="country" class="form-control" value="{{ old('country') }}">
                                    @error('country')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ملاحظات</label>
                            <textarea name="notes" class="form-control" rows="4">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">الوسوم (فصل بينها بفاصلة)</label>
                            <input type="text" name="tags" class="form-control" value="{{ old('tags') }}" placeholder="مثال: مهم، VIP، عقارات">
                            @error('tags')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('leads.index') }}" class="btn btn-secondary">إلغاء</a>
                            <button type="submit" class="btn btn-primary">حفظ العميل</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">نصائح</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary"></i>
                            أدخل معلومات دقيقة لتسهيل التواصل
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary"></i>
                            حدد المصدر والحالة لتتبع أداء المبيعات
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary"></i>
                            استخدم الوسوم لتصنيف العملاء المحتملين
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-info-circle text-primary"></i>
                            حدد قيمة متوقعة لتقييم الأولويات
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
