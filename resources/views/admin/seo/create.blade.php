@extends('admin.layouts.admin')

@section('title', 'إنشاء SEO Meta')

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
    
    .seo-form-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
    }
    
    .seo-form-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    
    .section-header {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 1rem 1.5rem;
        margin: -1.5rem -1.5rem 1.5rem -1.5rem;
        border-radius: 10px 10px 0 0;
    }
    
    .form-control, .form-select {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
        background-color: #f8f9ff;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        background-color: white;
    }
    
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }
    
    .required-star {
        color: #f5576c;
        font-weight: bold;
    }
    
    .btn-primary-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 25px;
        padding: 0.75rem 2rem;
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
    
    .character-count {
        background: linear-gradient(135deg, #f8f9ff 0%, #e8eaff 100%);
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
        font-size: 0.875rem;
        font-weight: 600;
        color: #667eea;
        display: inline-block;
    }
    
    .form-section {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    
    .form-section:hover {
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    
    .help-text {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        color: #1565c0;
        margin-top: 0.5rem;
    }
    
    .fade-in {
        animation: fadeIn 0.5s ease-in;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .input-group-text {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 10px 0 0 10px;
    }
    
    .progress-indicator {
        height: 4px;
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        border-radius: 2px;
        margin-top: 0.5rem;
        transition: width 0.3s ease;
    }
    
    .icon-accent {
        color: #667eea;
    }
    
    .grid-enhanced {
        gap: 1.5rem;
    }
    
    .form-floating label {
        color: #6c757d;
    }
    
    .form-floating .form-control:focus ~ label {
        color: #667eea;
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
                    <i class="fas fa-plus-circle ml-3"></i>
                    إنشاء بيانات SEO جديدة
                </h1>
                <p class="mb-0 opacity-90">إضافة معلومات SEO لتحسين ظهور موقعك في محركات البحث</p>
            </div>
            <div class="col-md-4 text-md-left">
                <a href="{{ route('admin.seo.index') }}" class="btn btn-secondary-custom btn-lg">
                    <i class="fas fa-arrow-left ml-2"></i> العودة
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="seo-form-card">
        <div class="card-body p-4">
            <form action="{{ route('admin.seo.store') }}" method="POST">
                @csrf

                <!-- Page Information -->
                <div class="form-section">
                    <div class="section-header">
                        <h5 class="mb-0">
                            <i class="fas fa-file-alt ml-2"></i>
                            معلومات الصفحة
                        </h5>
                    </div>
                    <div class="row grid-enhanced">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-list icon-accent ml-2"></i>
                                    نوع الصفحة <span class="required-star">*</span>
                                </label>
                                <select name="page_type" required class="form-select">
                                    <option value="">اختر نوع الصفحة</option>
                                    <option value="property">عقار</option>
                                    <option value="company">شركة</option>
                                    <option value="agent">وكيل</option>
                                    <option value="blog">مقال</option>
                                    <option value="page">صفحة عادية</option>
                                    <option value="category">تصنيف</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-link icon-accent ml-2"></i>
                                    رابط الصفحة <span class="required-star">*</span>
                                </label>
                                <input type="url" name="url" required
                                    placeholder="https://example.com/page"
                                    class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEO Meta Tags -->
                <div class="form-section">
                    <div class="section-header">
                        <h5 class="mb-0">
                            <i class="fas fa-search ml-2"></i>
                            وسوم SEO الأساسية
                        </h5>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="form-label">
                                <i class="fas fa-heading icon-accent ml-2"></i>
                                عنوان الصفحة (Meta Title) <span class="required-star">*</span>
                            </label>
                            <input type="text" name="meta_title" required maxlength="60"
                                placeholder="عنوان جذاب للصفحة (60 حرف كحد أقصى)"
                                class="form-control">
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="character-count">
                                    <span id="titleCount">0</span>/60 حرف
                                </span>
                                <div class="progress-indicator" id="titleProgress" style="width: 0%"></div>
                            </div>
                        </div>

                        <div>
                            <label class="form-label">
                                <i class="fas fa-align-left icon-accent ml-2"></i>
                                وصف الصفحة (Meta Description) <span class="required-star">*</span>
                            </label>
                            <textarea name="meta_description" required maxlength="160" rows="3"
                                placeholder="وصف جذاب للصفحة (160 حرف كحد أقصى)"
                                class="form-control"></textarea>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="character-count">
                                    <span id="descCount">0</span>/160 حرف
                                </span>
                                <div class="progress-indicator" id="descProgress" style="width: 0%"></div>
                            </div>
                        </div>

                        <div>
                            <label class="form-label">
                                <i class="fas fa-tags icon-accent ml-2"></i>
                                كلمات مفتاحية (Keywords)
                            </label>
                            <input type="text" name="meta_keywords"
                                placeholder="كلمة1, كلمة2, كلمة3"
                                class="form-control">
                            <div class="help-text">
                                <i class="fas fa-info-circle ml-2"></i>
                                افصل بين الكلمات بفاصلة
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Open Graph Tags -->
                <div class="form-section">
                    <div class="section-header">
                        <h5 class="mb-0">
                            <i class="fas fa-share-alt ml-2"></i>
                            وسوم Open Graph (للسوشيال ميديا)
                        </h5>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="form-label">
                                <i class="fas fa-heading icon-accent ml-2"></i>
                                عنوان Open Graph
                            </label>
                            <input type="text" name="og_title" maxlength="100"
                                placeholder="عنوان يظهر عند المشاركة"
                                class="form-control">
                        </div>

                        <div>
                            <label class="form-label">
                                <i class="fas fa-align-left icon-accent ml-2"></i>
                                وصف Open Graph
                            </label>
                            <textarea name="og_description" rows="2"
                                placeholder="وصف يظهر عند المشاركة"
                                class="form-control"></textarea>
                        </div>

                        <div>
                            <label class="form-label">
                                <i class="fas fa-image icon-accent ml-2"></i>
                                صورة Open Graph
                            </label>
                            <input type="url" name="og_image"
                                placeholder="https://example.com/image.jpg"
                                class="form-control">
                            <div class="help-text">
                                <i class="fas fa-info-circle ml-2"></i>
                                رابط الصورة (1200x630 بكسل مثالي)
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                    <a href="{{ route('admin.seo.index') }}" class="btn btn-secondary-custom">
                        <i class="fas fa-times ml-2"></i>
                        إلغاء
                    </a>
                    <button type="submit" class="btn btn-primary-custom btn-lg">
                        <i class="fas fa-save ml-2"></i>
                        حفظ البيانات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Enhanced character counters with progress bars
document.addEventListener('DOMContentLoaded', function() {
    const titleInput = document.querySelector('input[name="meta_title"]');
    const descInput = document.querySelector('textarea[name="meta_description"]');
    const titleCount = document.getElementById('titleCount');
    const descCount = document.getElementById('descCount');
    const titleProgress = document.getElementById('titleProgress');
    const descProgress = document.getElementById('descProgress');

    function updateCharacterCount(input, countElement, progressElement, maxLength) {
        const currentLength = input.value.length;
        const percentage = (currentLength / maxLength) * 100;
        
        countElement.textContent = currentLength;
        progressElement.style.width = percentage + '%';
        
        // Change color based on percentage
        if (percentage > 90) {
            progressElement.style.background = 'linear-gradient(90deg, #f5576c 0%, #ff0844 100%)';
        } else if (percentage > 75) {
            progressElement.style.background = 'linear-gradient(90deg, #ffa726 0%, #ff6f00 100%)';
        } else {
            progressElement.style.background = 'linear-gradient(90deg, #667eea 0%, #764ba2 100%)';
        }
    }

    titleInput.addEventListener('input', function() {
        updateCharacterCount(this, titleCount, titleProgress, 60);
    });

    descInput.addEventListener('input', function() {
        updateCharacterCount(this, descCount, descProgress, 160);
    });

    // Add floating label effect
    const formInputs = document.querySelectorAll('.form-control, .form-select');
    formInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });
    });

    // Add form validation styling
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            // Show error message
            const errorAlert = document.createElement('div');
            errorAlert.className = 'alert alert-danger alert-dismissible fade show';
            errorAlert.innerHTML = `
                <i class="fas fa-exclamation-triangle ml-2"></i>
                يرجى ملء جميع الحقول المطلوبة
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            form.insertBefore(errorAlert, form.firstChild);
            
            // Remove alert after 5 seconds
            setTimeout(() => {
                errorAlert.remove();
            }, 5000);
        }
    });
});
</script>
@endpush
