@extends('layouts.app')

@section('title', 'إضافة مقاول جديد')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">إضافة مقاول جديد</h3>
                    <a href="{{ route('developer.contractors.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right"></i> العودة للقائمة
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('developer.contractors.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-12 mb-4">
                                <h5 class="border-bottom pb-2">المعلومات الأساسية</h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="company_name" class="form-label">اسم الشركة <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name" value="{{ old('company_name') }}" required>
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="specialization" class="form-label">التخصص <span class="text-danger">*</span></label>
                                <select class="form-select @error('specialization') is-invalid @enderror" id="specialization" name="specialization" required>
                                    <option value="">اختر التخصص</option>
                                    <option value="construction" {{ old('specialization') == 'construction' ? 'selected' : '' }}>بناء</option>
                                    <option value="plumbing" {{ old('specialization') == 'plumbing' ? 'selected' : '' }}>سباكة</option>
                                    <option value="electrical" {{ old('specialization') == 'electrical' ? 'selected' : '' }}>كهرباء</option>
                                    <option value="hvac" {{ old('specialization') == 'hvac' ? 'selected' : '' }}>تكييف وتبريد</option>
                                    <option value="painting" {{ old('specialization') == 'painting' ? 'selected' : '' }}>دهانات</option>
                                    <option value="carpentry" {{ old('specialization') == 'carpentry' ? 'selected' : '' }}>نجارة</option>
                                    <option value="masonry" {{ old('specialization') == 'masonry' ? 'selected' : '' }}>بناء طوب</option>
                                    <option value="general" {{ old('specialization') == 'general' ? 'selected' : '' }}>مقاولات عامة</option>
                                </select>
                                @error('specialization')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="contact_person" class="form-label">الشخص المسؤول <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('contact_person') is-invalid @enderror" id="contact_person" name="contact_person" value="{{ old('contact_person') }}" required>
                                @error('contact_person')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="contact_phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('contact_phone') is-invalid @enderror" id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}" required>
                                @error('contact_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="contact_email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('contact_email') is-invalid @enderror" id="contact_email" name="contact_email" value="{{ old('contact_email') }}" required>
                                @error('contact_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="company_size" class="form-label">حجم الشركة</label>
                                <select class="form-select @error('company_size') is-invalid @enderror" id="company_size" name="company_size">
                                    <option value="">اختر الحجم</option>
                                    <option value="small" {{ old('company_size') == 'small' ? 'selected' : '' }}>صغيرة (1-10 موظفين)</option>
                                    <option value="medium" {{ old('company_size') == 'medium' ? 'selected' : '' }}>متوسطة (11-50 موظف)</option>
                                    <option value="large" {{ old('company_size') == 'large' ? 'selected' : '' }}>كبيرة (50+ موظف)</option>
                                </select>
                                @error('company_size')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Location -->
                            <div class="col-md-12 mb-4 mt-4">
                                <h5 class="border-bottom pb-2">العنوان والموقع</h5>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="address" class="form-label">العنوان</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address') }}">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">المدينة</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city') }}">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="country" class="form-label">الدولة</label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror" id="country" name="country" value="{{ old('country') }}">
                                @error('country')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Professional Details -->
                            <div class="col-md-12 mb-4 mt-4">
                                <h5 class="border-bottom pb-2">التفاصيل المهنية</h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="license_number" class="form-label">رقم الترخيص</label>
                                <input type="text" class="form-control @error('license_number') is-invalid @enderror" id="license_number" name="license_number" value="{{ old('license_number') }}">
                                @error('license_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tax_id" class="form-label">الرقم الضريبي</label>
                                <input type="text" class="form-control @error('tax_id') is-invalid @enderror" id="tax_id" name="tax_id" value="{{ old('tax_id') }}">
                                @error('tax_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="established_year" class="form-label">سنة التأسيس</label>
                                <input type="number" class="form-control @error('established_year') is-invalid @enderror" id="established_year" name="established_year" value="{{ old('established_year') }}" min="1900" max="{{ date('Y') }}">
                                @error('established_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="experience_years" class="form-label">سنوات الخبرة</label>
                                <input type="number" class="form-control @error('experience_years') is-invalid @enderror" id="experience_years" name="experience_years" value="{{ old('experience_years') }}" min="0">
                                @error('experience_years')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Files -->
                            <div class="col-md-12 mb-4 mt-4">
                                <h5 class="border-bottom pb-2">الملفات والمرفقات</h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="company_logo" class="form-label">شعار الشركة</label>
                                <input type="file" class="form-control @error('company_logo') is-invalid @enderror" id="company_logo" name="company_logo" accept="image/*">
                                @error('company_logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="documents" class="form-label">المستندات (سجل تجاري، تراخيص، شهادات)</label>
                                <input type="file" class="form-control @error('documents') is-invalid @enderror" id="documents" name="documents[]" multiple>
                                <small class="text-muted">يمكنك اختيار ملفات متعددة</small>
                                @error('documents')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="notes" class="form-label">ملاحظات إضافية</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary px-5">حفظ المقاول</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
