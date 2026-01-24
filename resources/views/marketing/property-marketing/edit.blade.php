@extends('layouts.app')

@section('title')
    تعديل حملة التسويق
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">تعديل حملة التسويق</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('marketing.property-marketing.update', $campaign) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">العنوان <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="title" value="{{ $campaign->title }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">العقار <span class="text-danger">*</span></label>
                                <select class="form-select" name="property_id" required>
                                    <option value="">اختر العقار</option>
                                    @foreach($properties as $property)
                                        <option value="{{ $property->id }}" {{ $campaign->property_id == $property->id ? 'selected' : '' }}>
                                            {{ $property->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">الوصف</label>
                                <textarea class="form-control" name="description" rows="4">{{ $campaign->description }}</textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">نوع الحملة</label>
                                <select class="form-select" name="campaign_type" required>
                                    <option value="brand_awareness" {{ $campaign->campaign_type === 'brand_awareness' ? 'selected' : '' }}>توعي بالعلامة التجارية</option>
                                    <option value="lead_generation" {{ $campaign->campaign_type === 'lead_generation' ? 'selected' : '' }}>توليد العملاء المحتملين</option>
                                    <option value="property_promotion" {{ $campaign->campaign_type === 'property_promotion' ? 'selected' : '' }}>ترويج العقار</option>
                                    <option value="neighborhood_showcase" {{ $campaign->campaign_type === 'neighborhood_showcase' ? 'selected' : '' }}>عرض الحي</option>
                                    <option value="event_marketing" {{ $campaign->campaign_type === 'event_marketing' ? 'selected' : '' }}>تسويق الفعاليات</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الحالة</label>
                                <select class="form-select" name="status">
                                    <option value="draft" {{ $campaign->status === 'draft' ? 'selected' : '' }}>مسودة</option>
                                    <option value="scheduled" {{ $campaign->status === 'scheduled' ? 'selected' : '' }}>مجدولة</option>
                                    <option value="active" {{ $campaign->status === 'active' ? 'selected' : '' }}>نشطة</option>
                                    <option value="paused" {{ $campaign->status === 'paused' ? 'selected' : '' }}>موقفة</option>
                                    <option value="completed" {{ $campaign->status === 'completed' ? 'selected' : '' }}>مكتملة</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">تاريخ البدء</label>
                                <input type="date" class="form-control" name="start_date" value="{{ $campaign->start_date?->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">الميزانية</label>
                                <input type="text" class="form-control" name="budget" value="{{ $campaign->budget }}" placeholder="0.00" step="0.01">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">تاريخ الانتهاء</label>
                                <input type="date" class="form-control" name="end_date" value="{{ $campaign->end_date?->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">العملة</label>
                                <input type="text" class="form-control" name="currency" value="{{ $campaign->currency ?? 'SAR' }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">الجمهور المستهدف</label>
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label">الفئة العمرية</label>
                                        <select class="form-select" name="target_audience[age_range]">
                                            <option value="18-24" {{ $campaign->target_audience['age_range'] === '18-24' ? 'selected' : '' }}>18-24 سنة</option>
                                            <option value="25-34" {{ $campaign->target_audience['age_range'] === '25-34' ? 'selected' : '' }}>25-34 سنة</option>
                                            <option value="35-44" {{ $campaign->target_audience['age_range'] === '35-44' ? 'selected' : '' }}>35-44 سنة</option>
                                            <option value="45-54" {{ $campaign->target_audience['age_range'] === '45-54' ? 'selected' : '' }}>45-54 سنة</option>
                                            <option value="55+" {{ $campaign->target_audience['age_range'] === '55+' ? 'selected' : '' }}>55+ سنة</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label">الجنس</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="target_audience[genders][]" value="male" {{ in_array('male', $campaign->target_audience['genders'] ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label">ذكر</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="target_audience[genders][]" value="female" {{ in_array('female', $campaign->target_audience['genders'] ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label">أنثى</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label">المناطق</label>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="target_audience[locations][]" value="الرياض" {{ in_array('الرياض', $campaign->target_audience['locations'] ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label">الرياض</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="target_audience[locations][]" value="جدة" {{ in_array('جدة', $campaign->target_audience['locations'] ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label">جدة</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="target_audience[locations][]" value="الدمام" {{ in_array('الدمام', $campaign->target_audience['locations'] ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label">الدمام</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">قنوات التسويق</label>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="marketing_channels[]" value="social_media" {{ in_array('social_media', $campaign->marketing_channels ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label">وسائل التواصل الاجتماعي</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="marketing_channels[]" value="email" {{ in_array('email', $campaign->marketing_channels ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label">البريد الإلكتروني</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="marketing_channels[]" value="search" {{ in_array('search', $campaign->marketing_channels ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label">محركات البحث</label>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="marketing_channels[]" value="display" {{ in_array('display', $campaign->marketing_channels ?? []) ? 'checked' : '' }}>
                                            <label class="form-check-label">العرض</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">استراتيجية المحتوى</label>
                                <textarea class="form-control" name="content_strategy" rows="3">{{ $campaign->content_strategy }}</textarea>
                            </div>
                        </div>

                        <!-- Performance Goals -->
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">أهداف الأداء</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-2">
                                            <label class="form-label">معدل النقرات (%)</label>
                                            <input type="number" class="form-control" name="performance_goals[click_through_rate]" value="{{ $campaign->performance_goals['click_through_rate'] ?? '' }}" step="0.01" min="0" max="100">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-2">
                                            <label class="form-label">معدل التحويل (%)</label>
                                            <input type="number" class="form-control" name="performance_goals[conversion_rate]" value="{{ $campaign->performance_goals['conversion_rate'] ?? '' }}" step="0.01" min="0" max="100">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-2">
                                            <label class="form-label">تكلفة التحويل</label>
                                            <input type="number" class="form-control" name="performance_goals[cost_per_conversion]" value="{{ $campaign->performance_goals['cost_per_conversion'] ?? '' }}" step="0.01" min="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer">
                            <a href="{{ route('marketing.property-marketing.show', $campaign) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-right me-1"></i>
                                عودة
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                حفظ التغييرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">معلومات الحملة الحالية</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>الإحصائيات الحالية</h6>
                        <table class="table table-sm">
                            <tr>
                                <th>الانطباعات</th>
                                <td>{{ number_format($campaign->total_impressions) }}</td>
                            </tr>
                            <tr>
                                <th>النقرات</th>
                                <td>{{ number_format($campaign->total_clicks) }}</td>
                            </tr>
                            <tr>
                                <th>التحويلات</th>
                                <td>{{ number_format($campaign->total_conversions) }}</td>
                            </tr>
                            <tr>
                                <th>معدل التحويل</th>
                                <td>{{ $campaign->conversion_rate }}%</td>
                            </tr>
                            <tr>
                                <th>الإنفاق</th>
                                <td>{{ number_format($campaign->total_spent, 2) }} {{ $campaign->currency }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="mb-3">
                        <h6>الجدول الزمني</h6>
                        <ul class="list-unstyled">
                            <li><strong>الإنشاء:</strong> {{ $campaign->created_at->format('Y-m-d H:i') }}</li>
                            @if($campaign->launched_at)
                                <li><strong>الإطلاق:</strong> {{ $campaign->launched_at->format('Y-m-d H:i') }}</li>
                            @endif
                            @if($campaign->completed_at)
                                <li><strong>الإكمال:</strong> {{ $campaign->completed_at->format('Y-m-d H:i') }}</li>
                            @endif
                            <li><strong>التحديث:</strong> {{ $campaign->updated_at->format('Y-m-d H:i') }}</li>
                        </ul>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>ملاحظة:</strong>
                        تغيير حالة الحملة قد يؤثر على الأداء الحالي.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
