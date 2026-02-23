@extends('layouts.app')

@section('title', 'تعديل بيانات المقاول')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">تعديل بيانات المقاول: {{ $contractor->company_name }}</h3>
                    <a href="{{ route('developer.contractors.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-right"></i> العودة للقائمة
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('developer.contractors.update', $contractor->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-12 mb-4">
                                <h5 class="border-bottom pb-2">المعلومات الأساسية</h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="company_name" class="form-label">اسم الشركة <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name" value="{{ old('company_name', $contractor->company_name) }}" required>
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="specialization" class="form-label">التخصص <span class="text-danger">*</span></label>
                                <select class="form-select @error('specialization') is-invalid @enderror" id="specialization" name="specialization" required>
                                    <option value="">اختر التخصص</option>
                                    <option value="construction" {{ old('specialization', $contractor->specialization) == 'construction' ? 'selected' : '' }}>بناء</option>
                                    <option value="plumbing" {{ old('specialization', $contractor->specialization) == 'plumbing' ? 'selected' : '' }}>سباكة</option>
                                    <option value="electrical" {{ old('specialization', $contractor->specialization) == 'electrical' ? 'selected' : '' }}>كهرباء</option>
                                    <option value="hvac" {{ old('specialization', $contractor->specialization) == 'hvac' ? 'selected' : '' }}>تكييف وتبريد</option>
                                    <option value="painting" {{ old('specialization', $contractor->specialization) == 'painting' ? 'selected' : '' }}>دهانات</option>
                                    <option value="carpentry" {{ old('specialization', $contractor->specialization) == 'carpentry' ? 'selected' : '' }}>نجارة</option>
                                    <option value="masonry" {{ old('specialization', $contractor->specialization) == 'masonry' ? 'selected' : '' }}>بناء طوب</option>
                                    <option value="general" {{ old('specialization', $contractor->specialization) == 'general' ? 'selected' : '' }}>مقاولات عامة</option>
                                </select>
                                @error('specialization')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="contact_person" class="form-label">الشخص المسؤول <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('contact_person') is-invalid @enderror" id="contact_person" name="contact_person" value="{{ old('contact_person', $contractor->contact_person) }}" required>
                                @error('contact_person')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="contact_phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('contact_phone') is-invalid @enderror" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $contractor->contact_phone) }}" required>
                                @error('contact_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="contact_email" class="form-label">البريد الإلكتروني <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('contact_email') is-invalid @enderror" id="contact_email" name="contact_email" value="{{ old('contact_email', $contractor->contact_email) }}" required>
                                @error('contact_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="company_size" class="form-label">حجم الشركة</label>
                                <select class="form-select @error('company_size') is-invalid @enderror" id="company_size" name="company_size">
                                    <option value="">اختر الحجم</option>
                                    <option value="small" {{ old('company_size', $contractor->company_size) == 'small' ? 'selected' : '' }}>صغيرة (1-10 موظفين)</option>
                                    <option value="medium" {{ old('company_size', $contractor->company_size) == 'medium' ? 'selected' : '' }}>متوسطة (11-50 موظف)</option>
                                    <option value="large" {{ old('company_size', $contractor->company_size) == 'large' ? 'selected' : '' }}>كبيرة (50+ موظف)</option>
                                </select>
                                @error('company_size')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">الحالة</label>
                                <select class="form-select @error('status') is-invalid @enderror" id="status" name="status">
                                    <option value="active" {{ old('status', $contractor->status) == 'active' ? 'selected' : '' }}>نشط</option>
                                    <option value="inactive" {{ old('status', $contractor->status) == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Location -->
                            <div class="col-md-12 mb-4 mt-4">
                                <h5 class="border-bottom pb-2">العنوان والموقع</h5>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="address" class="form-label">العنوان</label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $contractor->address) }}">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">المدينة</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', $contractor->city) }}">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="country" class="form-label">الدولة</label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror" id="country" name="country" value="{{ old('country', $contractor->country) }}">
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
                                <input type="text" class="form-control @error('license_number') is-invalid @enderror" id="license_number" name="license_number" value="{{ old('license_number', $contractor->license_number) }}">
                                @error('license_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="tax_id" class="form-label">الرقم الضريبي</label>
                                <input type="text" class="form-control @error('tax_id') is-invalid @enderror" id="tax_id" name="tax_id" value="{{ old('tax_id', $contractor->tax_id) }}">
                                @error('tax_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="established_year" class="form-label">سنة التأسيس</label>
                                <input type="number" class="form-control @error('established_year') is-invalid @enderror" id="established_year" name="established_year" value="{{ old('established_year', $contractor->established_year) }}" min="1900" max="{{ date('Y') }}">
                                @error('established_year')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="experience_years" class="form-label">سنوات الخبرة</label>
                                <input type="number" class="form-control @error('experience_years') is-invalid @enderror" id="experience_years" name="experience_years" value="{{ old('experience_years', $contractor->experience_years) }}" min="0">
                                @error('experience_years')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Files -->
                            <div class="col-md-12 mb-4 mt-4">
                                <h5 class="border-bottom pb-2">الملفات والمرفقات</h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="company_logo" class="form-label">شعار الشركة (اتركه فارغاً للإبقاء على الحالي)</label>
                                <input type="file" class="form-control @error('company_logo') is-invalid @enderror" id="company_logo" name="company_logo" accept="image/*">
                                @if($contractor->company_logo)
                                    <div class="mt-2">
                                        <small class="text-muted">الشعار الحالي:</small>
                                        <img src="{{ Storage::url($contractor->company_logo) }}" alt="logo" class="img-thumbnail" style="height: 50px;">
                                    </div>
                                @endif
                                @error('company_logo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="documents" class="form-label">إضافة مستندات جديدة</label>
                                <input type="file" class="form-control @error('documents') is-invalid @enderror" id="documents" name="documents[]" multiple>
                                <small class="text-muted">الملفات الجديدة ستضاف إلى القائمة الحالية</small>
                                @error('documents')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="notes" class="form-label">ملاحظات إضافية</label>
                                <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3">{{ old('notes', $contractor->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary px-5">تحديث البيانات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
