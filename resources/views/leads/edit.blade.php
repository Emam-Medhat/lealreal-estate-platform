@extends('layouts.app')

@section('title', 'تعديل العميل المحتمل')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">تعديل العميل المحتمل: {{ $lead->full_name }}</h5>
                        <a href="{{ route('leads.show', $lead) }}" class="btn btn-sm btn-info text-white">
                            <i class="fas fa-eye"></i> عرض التفاصيل
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('leads.update', $lead) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">الاسم الأول *</label>
                                        <input type="text" name="first_name" class="form-control"
                                            value="{{ old('first_name', $lead->first_name) }}" required>
                                        @error('first_name')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">الاسم الأخير *</label>
                                        <input type="text" name="last_name" class="form-control"
                                            value="{{ old('last_name', $lead->last_name) }}" required>
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
                                        <input type="email" name="email" class="form-control"
                                            value="{{ old('email', $lead->email) }}" required>
                                        @error('email')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">الهاتف</label>
                                        <input type="tel" name="phone" class="form-control"
                                            value="{{ old('phone', $lead->phone) }}">
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
                                        <input type="text" name="company" class="form-control"
                                            value="{{ old('company', $lead->company) }}">
                                        @error('company')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">المنصب</label>
                                        <input type="text" name="position" class="form-control"
                                            value="{{ old('position', $lead->position) }}">
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
                                                <option value="{{ $source->id }}" {{ old('source_id', $lead->source_id) == $source->id ? 'selected' : '' }}>
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
                                                <option value="{{ $status->id }}" {{ old('status_id', $lead->status_id) == $status->id ? 'selected' : '' }}>
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
                                                <option value="{{ $user->id }}" {{ old('assigned_to', $lead->assigned_to) == $user->id ? 'selected' : '' }}>
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
                                        @php
                                            $priorityMap = ['low' => 1, 'medium' => 2, 'high' => 3];
                                            $priorityValue = is_numeric($lead->priority) ? $lead->priority : ($priorityMap[strtolower($lead->priority)] ?? 2);
                                        @endphp
                                        <select name="priority" class="form-select">
                                            <option value="1" {{ old('priority', $priorityValue) == '1' ? 'selected' : '' }}>
                                                منخفضة</option>
                                            <option value="2" {{ old('priority', $priorityValue) == '2' ? 'selected' : '' }}>
                                                متوسطة</option>
                                            <option value="3" {{ old('priority', $priorityValue) == '3' ? 'selected' : '' }}>
                                                عالية</option>
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
                                        <input type="number" name="estimated_value" class="form-control" step="0.01"
                                            value="{{ old('estimated_value', $lead->estimated_value) }}">
                                        @error('estimated_value')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">تاريخ الإغلاق المتوقع</label>
                                        <input type="date" name="expected_close_date" class="form-control"
                                            value="{{ old('expected_close_date', $lead->expected_close_date ? $lead->expected_close_date->format('Y-m-d') : '') }}">
                                        @error('expected_close_date')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">العنوان</label>
                                <textarea name="address" class="form-control"
                                    rows="2">{{ old('address', $lead->address) }}</textarea>
                                @error('address')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">المدينة</label>
                                        <input type="text" name="city" class="form-control"
                                            value="{{ old('city', $lead->city) }}">
                                        @error('city')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">الدولة</label>
                                        <input type="text" name="country" class="form-control"
                                            value="{{ old('country', $lead->country) }}">
                                        @error('country')
                                            <div class="text-danger">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control"
                                    rows="4">{{ old('notes', $lead->notes) }}</textarea>
                                @error('notes')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">الوسوم (فصل بينها بفاصلة)</label>
                                @php
                                    $leadTags = $lead->tags;
                                    if (is_string($leadTags)) {
                                        $decoded = json_decode($leadTags);
                                        $tagsString = is_array($decoded) ? implode(', ', $decoded) : $leadTags;
                                    } else if (is_array($leadTags)) {
                                        $tagsString = implode(', ', $leadTags);
                                    } else {
                                        $tagsString = '';
                                    }
                                @endphp
                                <input type="text" name="tags" class="form-control" value="{{ old('tags', $tagsString) }}"
                                    placeholder="مثال: مهم، VIP، عقارات">
                                @error('tags')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('leads.index') }}" class="btn btn-secondary">إلغاء</a>
                                <button type="submit" class="btn btn-warning">تحديث البيانات</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">إحصائيات سريعة</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>تاريخ الإنشاء:</strong> {{ $lead->created_at->format('Y-m-d') }}</p>
                        <p><strong>آخر تحديث:</strong> {{ $lead->updated_at->format('Y-m-d') }}</p>
                        <p><strong>التقييم الحالي:</strong> {{ $lead->score ?? 0 }}/100</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">نصائح التحديث</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <i class="fas fa-info-circle text-primary"></i>
                                تأكد من تحديث الحالة عند حدوث أي تقدم مع العميل
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-info-circle text-primary"></i>
                                إضافة ملاحظات مفصلة تساعد الزملاء الآخرين
                            </li>
                            <li class="mb-2">
                                <i class="fas fa-info-circle text-primary"></i>
                                استخدم تاريخ الإغلاق المتوقع لمتابعة الأداء
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection