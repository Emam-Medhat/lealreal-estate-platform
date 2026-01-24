@extends('layouts.app')

@section('title', 'إنشاء إعلان جديد')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">إنشاء إعلان جديد</h1>
                <a href="{{ route('ads.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-right"></i> العودة للإعلانات
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('ads.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Basic Information -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="card-title">المعلومات الأساسية</h5>
                                <hr>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="campaign_id" class="form-label">الحملة الإعلانية</label>
                                    <select class="form-select" id="campaign_id" name="campaign_id" required>
                                        <option value="">اختر حملة</option>
                                        @foreach($campaigns as $campaign)
                                            <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                                        @endforeach
                                        <option value="new">+ إنشاء حملة جديدة</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">نوع الإعلان</label>
                                    <select class="form-select" id="type" name="type" required onchange="toggleAdTypeFields()">
                                        <option value="banner">بانر</option>
                                        <option value="native">أصلي</option>
                                        <option value="video">فيديو</option>
                                        <option value="popup">نافذة منبثقة</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="title" class="form-label">عنوان الإعلان</label>
                                    <input type="text" class="form-control" id="title" name="title" required
                                           value="{{ old('title') }}" maxlength="255">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">وصف الإعلان</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" required
                                              maxlength="500">{{ old('description') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Banner Ad Fields -->
                        <div id="banner-fields" class="row mb-4">
                            <div class="col-12">
                                <h5 class="card-title">إعدادات البانر</h5>
                                <hr>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="banner_size" class="form-label">حجم البانر</label>
                                    <select class="form-select" id="banner_size" name="banner_size" onchange="toggleCustomSize()">
                                        <option value="leaderboard">Leaderboard (728x90)</option>
                                        <option value="medium_rectangle">Medium Rectangle (300x250)</option>
                                        <option value="large_rectangle">Large Rectangle (336x280)</option>
                                        <option value="wide_skyscraper">Wide Skyscraper (160x600)</option>
                                        <option value="custom">مخصص</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3" id="custom-width-group" style="display: none;">
                                    <label for="custom_width" class="form-label">العرض (بكسل)</label>
                                    <input type="number" class="form-control" id="custom_width" name="custom_width" 
                                           min="100" max="1200" value="{{ old('custom_width') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3" id="custom-height-group" style="display: none;">
                                    <label for="custom_height" class="form-label">الارتفاع (بكسل)</label>
                                    <input type="number" class="form-control" id="custom_height" name="custom_height" 
                                           min="50" max="600" value="{{ old('custom_height') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="animation_type" class="form-label">نوع الحركة</label>
                                    <select class="form-select" id="animation_type" name="animation_type">
                                        <option value="none">بدون حركة</option>
                                        <option value="fade">تلاشي</option>
                                        <option value="slide">انزلاق</option>
                                        <option value="zoom">تكبير</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="banner_image" class="form-label">صورة البانر</label>
                                    <input type="file" class="form-control" id="banner_image" name="image" accept="image/*"
                                           onchange="previewImage(this, 'banner-preview')">
                                    <small class="text-muted">الصيغ المسموحة: JPG, PNG, GIF (الحد الأقصى: 2MB)</small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3" id="banner-preview"></div>
                            </div>
                        </div>

                        <!-- Video Ad Fields -->
                        <div id="video-fields" class="row mb-4" style="display: none;">
                            <div class="col-12">
                                <h5 class="card-title">إعدادات الفيديو</h5>
                                <hr>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="video_duration" class="form-label">مدة الفيديو (ثانية)</label>
                                    <input type="number" class="form-control" id="video_duration" name="video_duration" 
                                           min="5" max="300" value="{{ old('video_duration') ?? 30 }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="skip_after" class="form-label">تخطيط بعد (ثانية)</label>
                                    <input type="number" class="form-control" id="skip_after" name="skip_after" 
                                           min="5" max="60" value="{{ old('skip_after') ?? 15 }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="video_file" class="form-label">ملف الفيديو</label>
                                    <input type="file" class="form-control" id="video_file" name="video_file" accept="video/*">
                                    <small class="text-muted">الصيغ المسموحة: MP4, AVI, MOV, WMV (الحد الأقصى: 50MB)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="video_thumbnail" class="form-label">صورة مصغرة</label>
                                    <input type="file" class="form-control" id="video_thumbnail" name="thumbnail" accept="image/*"
                                           onchange="previewImage(this, 'thumbnail-preview')">
                                    <small class="text-muted">الصيغ المسموحة: JPG, PNG (الحد الأقصى: 2MB)</small>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3" id="thumbnail-preview"></div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="autoplay" name="autoplay" value="1">
                                    <label class="form-check-label" for="autoplay">
                                        تشغيل تلقائي
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="muted" name="muted" value="1" checked>
                                    <label class="form-check-label" for="muted">
                                        صامت
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="controls" name="controls" value="1" checked>
                                    <label class="form-check-label" for="controls">
                                        أزرار التحكم
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="loop" name="loop" value="1">
                                    <label class="form-check-label" for="loop">
                                        تكرار
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Native Ad Fields -->
                        <div id="native-fields" class="row mb-4" style="display: none;">
                            <div class="col-12">
                                <h5 class="card-title">إعدادات الإعلان الأصلي</h5>
                                <hr>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="native_image" class="form-label">صورة الإعلان</label>
                                    <input type="file" class="form-control" id="native_image" name="image" accept="image/*"
                                           onchange="previewImage(this, 'native-preview')">
                                    <small class="text-muted">الصيغ المسموحة: JPG, PNG (الحد الأقصى: 2MB)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="call_to_action" class="form-label">دعوة للعمل</label>
                                    <input type="text" class="form-control" id="call_to_action" name="call_to_action" 
                                           value="{{ old('call_to_action') }}" maxlength="100"
                                           placeholder="مثال: اضغط هنا، اعرف أكثر">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3" id="native-preview"></div>
                            </div>
                        </div>

                        <!-- Popup Ad Fields -->
                        <div id="popup-fields" class="row mb-4" style="display: none;">
                            <div class="col-12">
                                <h5 class="card-title">إعدادات النافذة المنبثقة</h5>
                                <hr>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="popup_image" class="form-label">صورة النافذة</label>
                                    <input type="file" class="form-control" id="popup_image" name="image" accept="image/*"
                                           onchange="previewImage(this, 'popup-preview')">
                                    <small class="text-muted">الصيغ المسموحة: JPG, PNG (الحد الأقصى: 2MB)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="popup_size" class="form-label">حجم النافذة</label>
                                    <select class="form-select" id="popup_size" name="popup_size">
                                        <option value="small">صغير (400x300)</option>
                                        <option value="medium">متوسط (600x400)</option>
                                        <option value="large">كبير (800x600)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3" id="popup-preview"></div>
                            </div>
                        </div>

                        <!-- Targeting & Placement -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="card-title">الاستهداف والمواضع</h5>
                                <hr>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="placements" class="form-label">مواضع الإعلان</label>
                                    <select class="form-select" id="placements" name="placements[]" multiple required>
                                        @foreach($placements as $placement)
                                            <option value="{{ $placement->id }}">{{ $placement->name }} ({{ $placement->dimensions }})</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">اختر جميع المواضع المناسبة للإعلان</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="target_audience" class="form-label">الجمهور المستهدف</label>
                                    <textarea class="form-control" id="target_audience" name="target_audience" rows="3"
                                              placeholder="صف الجمهور المستهدف للإعلان...">{{ old('target_audience') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Schedule & Budget -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="card-title">الجدولة والميزانية</h5>
                                <hr>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">تاريخ البدء</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" required
                                           value="{{ old('start_date', now()->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">تاريخ الانتهاء</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" required
                                           value="{{ old('end_date', now()->addDays(30)->format('Y-m-d')) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="daily_budget" class="form-label">الميزانية اليومية (ريال)</label>
                                    <input type="number" class="form-control" id="daily_budget" name="daily_budget" 
                                           step="0.01" min="1" required value="{{ old('daily_budget', 10) }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="total_budget" class="form-label">الميزانية الإجمالية (ريال)</label>
                                    <input type="number" class="form-control" id="total_budget" name="total_budget" 
                                           step="0.01" min="1" value="{{ old('total_budget') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Target URL -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="card-title">الرابط المستهدف</h5>
                                <hr>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="target_url" class="form-label">الرابط الذي سيتم توجيه المستخدم إليه</label>
                                    <input type="url" class="form-control" id="target_url" name="target_url" required
                                           value="{{ old('target_url') }}" placeholder="https://example.com">
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('ads.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> إلغاء
                                    </a>
                                    <div>
                                        <button type="submit" name="save_draft" value="1" class="btn btn-outline-primary me-2">
                                            <i class="fas fa-save"></i> حفظ كمسودة
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-check"></i> إنشاء الإعلان
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAdTypeFields() {
    const type = document.getElementById('type').value;
    const fields = ['banner-fields', 'video-fields', 'native-fields', 'popup-fields'];
    
    fields.forEach(field => {
        document.getElementById(field).style.display = 'none';
    });
    
    if (type === 'banner') {
        document.getElementById('banner-fields').style.display = 'block';
    } else if (type === 'video') {
        document.getElementById('video-fields').style.display = 'block';
    } else if (type === 'native') {
        document.getElementById('native-fields').style.display = 'block';
    } else if (type === 'popup') {
        document.getElementById('popup-fields').style.display = 'block';
    }
}

function toggleCustomSize() {
    const size = document.getElementById('banner_size').value;
    const customGroup = document.getElementById('custom-width-group').parentElement;
    const heightGroup = document.getElementById('custom-height-group').parentElement;
    
    if (size === 'custom') {
        customGroup.style.display = 'block';
        heightGroup.style.display = 'block';
    } else {
        customGroup.style.display = 'none';
        heightGroup.style.display = 'none';
    }
}

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = `
                <div class="mt-3">
                    <img src="${e.target.result}" class="img-thumbnail" style="max-width: 300px; max-height: 200px;">
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearImage('${previewId}')">
                            <i class="fas fa-times"></i> إزالة الصورة
                        </button>
                    </div>
                </div>
            `;
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

function clearImage(previewId) {
    document.getElementById(previewId).innerHTML = '';
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    toggleAdTypeFields();
});
</script>
@endsection
