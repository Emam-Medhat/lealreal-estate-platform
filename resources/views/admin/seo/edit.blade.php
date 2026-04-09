@extends('admin.layouts.admin')

@section('title', 'تعديل SEO')

@push('styles')
<style>
    .seo-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .form-section {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }
    
    .form-section:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    
    .form-section .card-header {
        border: none;
        color: white;
        font-weight: 600;
        padding: 1.2rem;
    }
    
    .form-section.primary .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .form-section.info .card-header {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    }
    
    .form-section.warning .card-header {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }
    
    .btn-primary-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 1.5rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }
    
    .btn-secondary-custom {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 1.5rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-secondary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(108, 117, 125, 0.3);
    }
    
    .btn-info-custom {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 1.5rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    
    .btn-info-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(6, 182, 212, 0.3);
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    
    .character-counter {
        font-size: 0.875rem;
        font-weight: 600;
        transition: color 0.3s ease;
    }
    
    .character-counter.warning {
        color: #f59e0b;
    }
    
    .character-counter.danger {
        color: #ef4444;
    }
    
    .progress {
        background-color: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
        height: 8px;
    }
    
    .progress-bar {
        border-radius: 10px;
        transition: width 0.5s ease;
    }
    
    .progress-bar.success {
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
    }
    
    .progress-bar.warning {
        background: linear-gradient(90deg, #f59e0b 0%, #d97706 100%);
    }
    
    .progress-bar.danger {
        background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);
    }
    
    .tip-card {
        background: linear-gradient(180deg, #f8f9ff 0%, #e8eaff 100%);
        border: 1px solid #e9ecef;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1rem;
        transition: transform 0.2s ease;
    }
    
    .tip-card:hover {
        transform: translateY(-2px);
    }
    
    .tip-card h6 {
        color: #495057;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    
    .tip-card ul {
        margin-bottom: 0;
        font-size: 0.875rem;
    }
    
    .tip-card.info {
        background: linear-gradient(180deg, #e0f2fe 0%, #bae6fd 100%);
        border-color: #0ea5e9;
    }
    
    .tip-card.info h6 {
        color: #0284c7;
    }
    
    .tip-card.success {
        background: linear-gradient(180deg, #dcfce7 0%, #bbf7d0 100%);
        border-color: #22c55e;
    }
    
    .tip-card.success h6 {
        color: #16a34a;
    }
    
    .tip-card.warning {
        background: linear-gradient(180deg, #fef3c7 0%, #fde68a 100%);
        border-color: #f59e0b;
    }
    
    .tip-card.warning h6 {
        color: #d97706;
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .icon-accent {
        color: #667eea;
    }
    
    .image-preview {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 0.5rem;
        background: white;
        transition: border-color 0.3s ease;
    }
    
    .image-preview:hover {
        border-color: #667eea;
    }
</style>
@endpush

@section('content')
<div class="container-fluid fade-in">
    <!-- Page Header -->
    <div class="seo-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-3">
                    <i class="fas fa-edit ml-3"></i>
                    تعديل SEO
                </h1>
                <p class="mb-0 opacity-90">تعديل بيانات تحسين محركات البحث</p>
            </div>
            <div class="col-md-4 text-md-left">
                <a href="{{ route('admin.seo.show', ['seoMeta' => $seoMeta->id]) }}" class="btn btn-info-custom btn-lg ml-2">
                    <i class="fas fa-eye ml-2"></i> عرض
                </a>
                <a href="{{ route('admin.seo.index') }}" class="btn btn-secondary-custom btn-lg">
                    <i class="fas fa-arrow-left ml-2"></i> العودة
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('admin.seo.update', ['seoMeta' => $seoMeta->id]) }}" class="form-section primary">
                @csrf
                @method('PUT')
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-edit ml-2"></i>
                        تعديل بيانات SEO
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="meta_title" class="form-label">
                                    <i class="fas fa-heading icon-accent ml-2"></i>عنوان الصفحة (Meta Title)
                                    <span class="character-counter" id="title-counter">({{ strlen($seoMeta->meta_title ?? '') }}/60 حرف)</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('meta_title') is-invalid @enderror" 
                                       id="meta_title" 
                                       name="meta_title" 
                                       value="{{ old('meta_title', $seoMeta->meta_title) }}"
                                       maxlength="60"
                                       placeholder="أدخل عنوان الصفحة...">
                                <div class="progress mt-2">
                                    <div class="progress-bar" id="title-progress" role="progressbar" style="width: {{ (strlen($seoMeta->meta_title ?? '') / 60) * 100 }}%"></div>
                                </div>
                                @error('meta_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="robots" class="form-label">
                                    <i class="fas fa-robot icon-accent ml-2"></i>إعدادات Robots
                                </label>
                                <select class="form-select @error('robots') is-invalid @enderror" 
                                        id="robots" 
                                        name="robots">
                                    <option value="index, follow" {{ old('robots', $seoMeta->robots) == 'index, follow' ? 'selected' : '' }}>
                                        index, follow
                                    </option>
                                    <option value="index, nofollow" {{ old('robots', $seoMeta->robots) == 'index, nofollow' ? 'selected' : '' }}>
                                        index, nofollow
                                    </option>
                                    <option value="noindex, follow" {{ old('robots', $seoMeta->robots) == 'noindex, follow' ? 'selected' : '' }}>
                                        noindex, follow
                                    </option>
                                    <option value="noindex, nofollow" {{ old('robots', $seoMeta->robots) == 'noindex, nofollow' ? 'selected' : '' }}>
                                        noindex, nofollow
                                    </option>
                                </select>
                                @error('robots')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="meta_description" class="form-label">
                            <i class="fas fa-align-left icon-accent ml-2"></i>وصف الصفحة (Meta Description)
                            <span class="character-counter" id="desc-counter">({{ strlen($seoMeta->meta_description ?? '') }}/160 حرف)</span>
                        </label>
                        <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                  id="meta_description" 
                                  name="meta_description" 
                                  rows="4" 
                                  maxlength="160"
                                  placeholder="أدخل وصف الصفحة...">{{ old('meta_description', $seoMeta->meta_description) }}</textarea>
                        <div class="progress mt-2">
                            <div class="progress-bar" id="desc-progress" role="progressbar" style="width: {{ (strlen($seoMeta->meta_description ?? '') / 160) * 100 }}%"></div>
                        </div>
                        @error('meta_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="meta_keywords" class="form-label">
                            <i class="fas fa-key icon-accent ml-2"></i>الكلمات المفتاحية (Meta Keywords)
                        </label>
                        <input type="text" 
                               class="form-control @error('meta_keywords') is-invalid @enderror" 
                               id="meta_keywords" 
                               name="meta_keywords" 
                               value="{{ old('meta_keywords', $seoMeta->meta_keywords) }}"
                               placeholder="كلمة1, كلمة2, كلمة3">
                        @error('meta_keywords')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">افصل بين الكلمات بفاصلة</small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="canonical_url" class="form-label">
                            <i class="fas fa-link icon-accent ml-2"></i>رابط Canonical
                        </label>
                        <input type="url" 
                               class="form-control @error('canonical_url') is-invalid @enderror" 
                               id="canonical_url" 
                               name="canonical_url" 
                               value="{{ old('canonical_url', $seoMeta->canonical_url) }}"
                               placeholder="https://example.com/page">
                        @error('canonical_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="card-footer bg-light">
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="fas fa-save ml-2"></i>حفظ التغييرات
                    </button>
                    <a href="{{ route('admin.seo.show', ['seoMeta' => $seoMeta->id]) }}" class="btn btn-secondary-custom">
                        <i class="fas fa-times ml-2"></i>إلغاء
                    </a>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <!-- Open Graph Settings -->
            <div class="form-section info mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-share-alt ml-2"></i>
                        Open Graph (Social Media)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="og_title" class="form-label">
                            <i class="fas fa-heading icon-accent ml-2"></i>عنوان OG
                        </label>
                        <input type="text" 
                               class="form-control @error('og_title') is-invalid @enderror" 
                               id="og_title" 
                               name="og_title" 
                               value="{{ old('og_title', $seoMeta->og_title) }}"
                               maxlength="100"
                               placeholder="عنوان للمشاركة على وسائل التواصل">
                        @error('og_title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="og_description" class="form-label">
                            <i class="fas fa-align-left icon-accent ml-2"></i>وصف OG
                        </label>
                        <textarea class="form-control @error('og_description') is-invalid @enderror" 
                                  id="og_description" 
                                  name="og_description" 
                                  rows="3" 
                                  maxlength="300"
                                  placeholder="وصف للمشاركة على وسائل التواصل">{{ old('og_description', $seoMeta->og_description) }}</textarea>
                        @error('og_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="og_image" class="form-label">
                            <i class="fas fa-image icon-accent ml-2"></i>صورة OG
                        </label>
                        <input type="url" 
                               class="form-control @error('og_image') is-invalid @enderror" 
                               id="og_image" 
                               name="og_image" 
                               value="{{ old('og_image', $seoMeta->og_image) }}"
                               placeholder="https://example.com/image.jpg">
                        @error('og_image')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($seoMeta->og_image)
                            <div class="mt-2">
                                <div class="image-preview">
                                    <img src="{{ $seoMeta->og_image }}" class="img-fluid rounded" alt="OG Image" style="max-height: 150px;">
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- SEO Tips -->
            <div class="form-section warning mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-lightbulb ml-2"></i>
                        نصائح SEO
                    </h6>
                </div>
                <div class="card-body">
                    <div class="tip-card info">
                        <h6>
                            <i class="fas fa-info-circle ml-2"></i>عنوان الصفحة
                        </h6>
                        <ul>
                            <li>طول مثالي: 50-60 حرف</li>
                            <li>يجب أن يكون جذاباً وواضحاً</li>
                            <li>يحتوي على الكلمات المفتاحية الرئيسية</li>
                        </ul>
                    </div>
                    <div class="tip-card success">
                        <h6>
                            <i class="fas fa-info-circle ml-2"></i>وصف الصفحة
                        </h6>
                        <ul>
                            <li>طول مثالي: 150-160 حرف</li>
                            <li>يجب أن يكون جذاباً للمستخدم</li>
                            <li>يحتوي على دعوة للعمل (Call to Action)</li>
                        </ul>
                    </div>
                    <div class="tip-card warning">
                        <h6>
                            <i class="fas fa-info-circle ml-2"></i>الكلمات المفتاحية
                        </h6>
                        <ul>
                            <li>5-10 كلمات مفتاحية كحد أقصى</li>
                            <li>افصل بينها بفاصلة</li>
                            <li>ركز على الكلمات الأكثر بحثاً</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Enhanced character counters with progress bars
document.getElementById('meta_title').addEventListener('input', function() {
    const length = this.value.length;
    const counter = document.getElementById('title-counter');
    const progressBar = document.getElementById('title-progress');
    
    counter.textContent = `(${length}/60 حرف)`;
    
    // Update counter color
    counter.className = 'character-counter';
    if (length >= 55) {
        counter.classList.add('warning');
    }
    if (length >= 60) {
        counter.classList.add('danger');
    }
    
    // Update progress bar
    const percentage = (length / 60) * 100;
    progressBar.style.width = percentage + '%';
    
    // Update progress bar color
    progressBar.className = 'progress-bar';
    if (length >= 55) {
        progressBar.classList.add('warning');
    }
    if (length >= 60) {
        progressBar.classList.add('danger');
    } else {
        progressBar.classList.add('success');
    }
});

document.getElementById('meta_description').addEventListener('input', function() {
    const length = this.value.length;
    const counter = document.getElementById('desc-counter');
    const progressBar = document.getElementById('desc-progress');
    
    counter.textContent = `(${length}/160 حرف)`;
    
    // Update counter color
    counter.className = 'character-counter';
    if (length >= 150) {
        counter.classList.add('warning');
    }
    if (length >= 160) {
        counter.classList.add('danger');
    }
    
    // Update progress bar
    const percentage = (length / 160) * 100;
    progressBar.style.width = percentage + '%';
    
    // Update progress bar color
    progressBar.className = 'progress-bar';
    if (length >= 150) {
        progressBar.classList.add('warning');
    }
    if (length >= 160) {
        progressBar.classList.add('danger');
    } else {
        progressBar.classList.add('success');
    }
});

// Initialize progress bars on page load
document.addEventListener('DOMContentLoaded', function() {
    // Title progress bar
    const titleLength = document.getElementById('meta_title').value.length;
    const titleProgress = document.getElementById('title-progress');
    const titlePercentage = (titleLength / 60) * 100;
    titleProgress.style.width = titlePercentage + '%';
    titleProgress.className = 'progress-bar';
    if (titleLength >= 55) {
        titleProgress.classList.add('warning');
    }
    if (titleLength >= 60) {
        titleProgress.classList.add('danger');
    } else {
        titleProgress.classList.add('success');
    }
    
    // Description progress bar
    const descLength = document.getElementById('meta_description').value.length;
    const descProgress = document.getElementById('desc-progress');
    const descPercentage = (descLength / 160) * 100;
    descProgress.style.width = descPercentage + '%';
    descProgress.className = 'progress-bar';
    if (descLength >= 150) {
        descProgress.classList.add('warning');
    }
    if (descLength >= 160) {
        descProgress.classList.add('danger');
    } else {
        descProgress.classList.add('success');
    }
});
</script>
@endsection
