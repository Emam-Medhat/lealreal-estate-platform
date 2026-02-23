@extends('admin.layouts.admin')

@section('title', 'تعديل SEO')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-edit text-primary mr-2"></i>تعديل SEO
            </h1>
            <p class="text-muted mb-0 mt-1">تعديل بيانات تحسين محركات البحث</p>
        </div>
        <div>
            <a href="{{ route('admin.seo.show', ['seoMeta' => $seoMeta->id]) }}" class="btn btn-info shadow-sm">
                <i class="fas fa-eye fa-sm text-white-50"></i> عرض
            </a>
            <a href="{{ route('admin.seo.meta.tags') }}" class="btn btn-secondary shadow-sm mr-2">
                <i class="fas fa-arrow-left fa-sm text-white-50"></i> العودة
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <form method="POST" action="{{ route('admin.seo.update', ['seoMeta' => $seoMeta->id]) }}" class="card shadow mb-4">
                @csrf
                @method('PUT')
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-edit mr-2"></i>تعديل بيانات SEO
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="meta_title" class="form-label">
                                    <i class="fas fa-heading mr-1"></i>عنوان الصفحة (Meta Title)
                                    <small class="text-muted">({{ strlen($seoMeta->meta_title ?? '') }}/60 حرف)</small>
                                </label>
                                <input type="text" 
                                       class="form-control @error('meta_title') is-invalid @enderror" 
                                       id="meta_title" 
                                       name="meta_title" 
                                       value="{{ old('meta_title', $seoMeta->meta_title) }}"
                                       maxlength="60"
                                       placeholder="أدخل عنوان الصفحة...">
                                @error('meta_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label for="robots" class="form-label">
                                    <i class="fas fa-robot mr-1"></i>إعدادات Robots
                                </label>
                                <select class="form-control @error('robots') is-invalid @enderror" 
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
                            <i class="fas fa-align-left mr-1"></i>وصف الصفحة (Meta Description)
                            <small class="text-muted">({{ strlen($seoMeta->meta_description ?? '') }}/160 حرف)</small>
                        </label>
                        <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                  id="meta_description" 
                                  name="meta_description" 
                                  rows="4" 
                                  maxlength="160"
                                  placeholder="أدخل وصف الصفحة...">{{ old('meta_description', $seoMeta->meta_description) }}</textarea>
                        @error('meta_description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group mb-3">
                        <label for="meta_keywords" class="form-label">
                            <i class="fas fa-key mr-1"></i>الكلمات المفتاحية (Meta Keywords)
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
                            <i class="fas fa-link mr-1"></i>رابط Canonical
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

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i>حفظ التغييرات
                    </button>
                    <a href="{{ route('admin.seo.show', ['seoMeta' => $seoMeta->id]) }}" class="btn btn-secondary">
                        <i class="fas fa-times mr-1"></i>إلغاء
                    </a>
                </div>
            </form>
        </div>

        <div class="col-lg-4">
            <!-- Open Graph Settings -->
            <div class="card shadow mb-4 border-left-info">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-share-alt mr-2"></i>Open Graph (Social Media)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="og_title" class="form-label">
                            <i class="fas fa-heading mr-1"></i>عنوان OG
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
                            <i class="fas fa-align-left mr-1"></i>وصف OG
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
                            <i class="fas fa-image mr-1"></i>صورة OG
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
                                <img src="{{ $seoMeta->og_image }}" class="img-fluid rounded border" alt="OG Image" style="max-height: 150px;">
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- SEO Tips -->
            <div class="card shadow mb-4 border-left-warning">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">
                        <i class="fas fa-lightbulb mr-2"></i>نصائح SEO
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-2">
                        <h6 class="alert-heading"><i class="fas fa-info-circle mr-1"></i>عنوان الصفحة</h6>
                        <ul class="mb-0">
                            <li>طول مثالي: 50-60 حرف</li>
                            <li>يجب أن يكون جذاباً وواضحاً</li>
                            <li>يحتوي على الكلمات المفتاحية الرئيسية</li>
                        </ul>
                    </div>
                    <div class="alert alert-success mb-2">
                        <h6 class="alert-heading"><i class="fas fa-info-circle mr-1"></i>وصف الصفحة</h6>
                        <ul class="mb-0">
                            <li>طول مثالي: 150-160 حرف</li>
                            <li>يجب أن يكون جذاباً للمستخدم</li>
                            <li>يحتوي على دعوة للعمل (Call to Action)</li>
                        </ul>
                    </div>
                    <div class="alert alert-warning mb-0">
                        <h6 class="alert-heading"><i class="fas fa-info-circle mr-1"></i>الكلمات المفتاحية</h6>
                        <ul class="mb-0">
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
// Character counters
document.getElementById('meta_title').addEventListener('input', function() {
    const length = this.value.length;
    const label = this.previousElementSibling;
    const counter = label.querySelector('small');
    counter.textContent = `(${length}/60 حرف)`;
    counter.className = length > 60 ? 'text-danger' : 'text-muted';
});

document.getElementById('meta_description').addEventListener('input', function() {
    const length = this.value.length;
    const label = this.previousElementSibling;
    const counter = label.querySelector('small');
    counter.textContent = `(${length}/160 حرف)`;
    counter.className = length > 160 ? 'text-danger' : 'text-muted';
});
</script>
@endsection
