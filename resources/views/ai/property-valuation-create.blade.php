@extends('layouts.app')

@section('title', 'تقييم عقار جديد')

@push('styles')
<style>
/* Custom Styles for Create Valuation Page */
.valuation-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 15px;
    margin-bottom: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.valuation-header h1 {
    margin: 0;
    font-weight: 700;
    font-size: 2rem;
}

.form-card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
    background: white;
    overflow: hidden;
}

.form-card .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 1.5rem;
}

.form-card .card-header h5 {
    margin: 0;
    font-weight: 600;
}

.form-card .card-body {
    padding: 2rem;
}

.form-control, .form-select {
    border-radius: 10px;
    border: 1px solid #dee2e6;
    padding: 0.75rem 1rem;
    transition: all 0.2s ease;
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

.btn-primary-custom {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 10px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
}

.btn-secondary-custom {
    background: #6c757d;
    border: none;
    border-radius: 10px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-secondary-custom:hover {
    background: #5a6268;
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(108, 117, 125, 0.3);
}

.property-preview {
    border: 2px dashed #dee2e6;
    border-radius: 10px;
    padding: 1.5rem;
    text-align: center;
    background-color: #f8f9fa;
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.property-preview.has-property {
    border-color: #667eea;
    background-color: rgba(102, 126, 234, 0.05);
}

.valuation-method-card {
    border: 2px solid #dee2e6;
    border-radius: 10px;
    padding: 1.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    text-align: center;
}

.valuation-method-card:hover {
    border-color: #667eea;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.2);
}

.valuation-method-card.selected {
    border-color: #667eea;
    background-color: rgba(102, 126, 234, 0.1);
}

.valuation-method-card i {
    font-size: 2rem;
    color: #667eea;
    margin-bottom: 0.5rem;
}

.progress-section {
    display: none;
}

.progress-custom {
    height: 8px;
    border-radius: 10px;
    background-color: #e9ecef;
}

.progress-bar-custom {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
}

@media (max-width: 768px) {
    .valuation-header {
        padding: 1.5rem;
    }
    
    .valuation-header h1 {
        font-size: 1.5rem;
    }
    
    .form-card .card-body {
        padding: 1.5rem;
    }
}

/* Loading spinner custom */
.spinner-custom {
    width: 20px;
    height: 20px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    display: inline-block;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="valuation-header d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fas fa-brain me-3"></i>
            تقييم عقار جديد
        </h1>
        <div>
            <a href="{{ route('ai.valuation.index') }}" class="btn btn-light btn-lg">
                <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card form-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>
                        معلومات التقييم
                    </h5>
                </div>
                <div class="card-body">
                    <form id="valuationForm" method="POST" action="{{ route('ai.valuation.store') }}">
                        @csrf
                        
                        <!-- Property Selection -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label">اختر العقار *</label>
                                <select name="property_id" class="form-select" required>
                                    <option value="">اختر العقار من القائمة</option>
                                    @foreach ($properties ?? [] as $property)
                                        <option value="{{ $property->id }}">{{ $property->id }} - {{ $property->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Property Preview -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label">معاينة العقار</label>
                                <div id="propertyPreview" class="property-preview">
                                    <div class="text-muted">
                                        <i class="fas fa-building fa-3x mb-3"></i>
                                        <p>يرجى اختيار عقار لعرض معاينة المعلومات</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Valuation Method -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label">طريقة التقييم *</label>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="valuation-method-card" data-method="comparable_sales">
                                            <i class="fas fa-chart-bar"></i>
                                            <h6>مقارنة المبيعات</h6>
                                            <small class="text-muted">تحليل بناءً على مبيعات العقارات المماثلة</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="valuation-method-card" data-method="income_approach">
                                            <i class="fas fa-dollar-sign"></i>
                                            <h6>طريقة الدخل</h6>
                                            <small class="text-muted">تقييم بناءً على الإيرادات المتوقعة</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="valuation-method-card" data-method="cost_approach">
                                            <i class="fas fa-hammer"></i>
                                            <h6>طريقة التكلفة</h6>
                                            <small class="text-muted">تحليل بناءً على تكلفة الإنشاء</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="valuation-method-card" data-method="ai_hybrid">
                                            <i class="fas fa-robot"></i>
                                            <h6>ذكاء اصطناعي مركب</h6>
                                            <small class="text-muted">تحليل شامل باستخدام الذكاء الاصطناعي</small>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="valuation_method" id="valuationMethod" required>
                            </div>
                        </div>

                        <!-- Additional Parameters -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label">مستوى الدقة المطلوب</label>
                                <select name="accuracy_level" class="form-select">
                                    <option value="standard">قياسي</option>
                                    <option value="high">عالي</option>
                                    <option value="premium">مميز</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">أولوية التقييم</label>
                                <select name="priority" class="form-select">
                                    <option value="normal">عادية</option>
                                    <option value="high">عالية</option>
                                    <option value="urgent">عاجلة</option>
                                </select>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label">ملاحظات إضافية</label>
                                <textarea name="notes" class="form-control" rows="4" placeholder="أدخل أي ملاحظات أو متطلبات خاصة للتقييم..."></textarea>
                            </div>
                        </div>

                        <!-- Progress Section -->
                        <div id="progressSection" class="progress-section">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-cog fa-spin me-2"></i>جاري إجراء التقييم...</h6>
                                <p class="mb-2">يرجى الانتظار بينما يقوم النظام بتحليل العقار وتحديد القيمة التقديرية.</p>
                                <div class="progress progress-custom">
                                    <div class="progress-bar progress-bar-custom" role="progressbar" style="width: 0%"></div>
                                </div>
                                <small class="text-muted mt-2 d-block">قد تستغرق هذه العملية من 30 إلى 60 ثانية</small>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex gap-3 justify-content-between">
                                    <a href="{{ route('ai.valuation.index') }}" class="btn btn-secondary-custom">
                                        <i class="fas fa-times me-2"></i>إلغاء
                                    </a>
                                    <button type="submit" class="btn btn-primary-custom btn-lg" id="submitBtn">
                                        <i class="fas fa-brain me-2"></i>بدء التقييم
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Valuation Method Selection
    const methodCards = document.querySelectorAll('.valuation-method-card');
    const methodInput = document.getElementById('valuationMethod');
    
    methodCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove selected class from all cards
            methodCards.forEach(c => c.classList.remove('selected'));
            
            // Add selected class to clicked card
            this.classList.add('selected');
            
            // Set the hidden input value
            methodInput.value = this.dataset.method;
        });
    });

    // Property Selection Preview
    const propertySelect = document.querySelector('select[name="property_id"]');
    const propertyPreview = document.getElementById('propertyPreview');
    
    propertySelect.addEventListener('change', function() {
        const propertyId = this.value;
        
        if (propertyId) {
            // Simulate loading property details
            propertyPreview.innerHTML = `
                <div class="spinner-custom"></div>
                <p class="mt-2">جاري تحميل بيانات العقار...</p>
            `;
            propertyPreview.classList.add('has-property');
            
            // Simulate API call to get property details
            setTimeout(() => {
                propertyPreview.innerHTML = `
                    <div class="text-start">
                        <h6 class="text-primary">عقار رقم ${propertyId}</h6>
                        <p class="mb-1"><strong>العنوان:</strong> الرياض، حي النخيل</p>
                        <p class="mb-1"><strong>المساحة:</strong> 250 متر مربع</p>
                        <p class="mb-1"><strong>النوع:</strong> شقة سكنية</p>
                        <p class="mb-1"><strong>الغرف:</strong> 3 غرف + صالة</p>
                        <p class="mb-0"><strong>الحالة:</strong> ممتازة</p>
                    </div>
                `;
            }, 1000);
        } else {
            propertyPreview.innerHTML = `
                <div class="text-muted">
                    <i class="fas fa-building fa-3x mb-3"></i>
                    <p>يرجى اختيار عقار لعرض معاينة المعلومات</p>
                </div>
            `;
            propertyPreview.classList.remove('has-property');
        }
    });

    // Form Submission
    const form = document.getElementById('valuationForm');
    const submitBtn = document.getElementById('submitBtn');
    const progressSection = document.getElementById('progressSection');
    const progressBar = document.querySelector('.progress-bar');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validate form
        if (!methodInput.value) {
            Swal.fire({
                icon: 'warning',
                title: 'تنبيه!',
                text: 'يرجى اختيار طريقة التقييم',
                confirmButtonText: 'موافق'
            });
            return;
        }
        
        // Show progress
        progressSection.style.display = 'block';
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<div class="spinner-custom me-2"></div>جاري التقييم...';
        
        // Simulate progress
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress > 90) progress = 90;
            progressBar.style.width = progress + '%';
        }, 500);
        
        // Submit form
        const formData = new FormData(form);
        
        fetch('{{ route("ai.valuation.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            clearInterval(interval);
            progressBar.style.width = '100%';
            
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'نجح!',
                    text: data.message,
                    confirmButtonText: 'موافق'
                }).then(() => {
                    window.location.href = '{{ route("ai.valuation.index") }}';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ!',
                    text: data.message,
                    confirmButtonText: 'موافق'
                }).then(() => {
                    // Reset form
                    progressSection.style.display = 'none';
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-brain me-2"></i>بدء التقييم';
                    progressBar.style.width = '0%';
                });
            }
        })
        .catch(error => {
            clearInterval(interval);
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'خطأ!',
                text: 'حدث خطأ أثناء التقييم',
                confirmButtonText: 'موافق'
            }).then(() => {
                // Reset form
                progressSection.style.display = 'none';
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-brain me-2"></i>بدء التقييم';
                progressBar.style.width = '0%';
            });
        });
    });
});
</script>
@endpush
