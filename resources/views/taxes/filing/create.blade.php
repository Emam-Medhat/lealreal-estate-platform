@extends('layouts.app')

@section('title', 'إنشاء إقرار ضريبي جديد')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white flex items-center">
                            <i class="fas fa-plus-circle ml-3"></i>
                            إنشاء إقرار ضريبي جديد
                        </h1>
                        <p class="text-blue-100 mt-2">إضافة إقرار ضريبي جديد للعقارات الخاصة بك</p>
                    </div>
                    <div class="flex space-x-reverse space-x-3">
                        <a href="{{ route('taxes.filing') }}" 
                           class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i class="fas fa-arrow-left ml-2"></i>
                            العودة
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-edit text-blue-600 ml-2"></i>
                    تفاصيل الإقرار الضريبي
                </h2>
            </div>
            
            <div class="p-6">
                <form action="{{ route('taxes.filing.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <!-- Property Tax Selection -->
                    <div>
                        <label for="property_tax_id" class="block text-sm font-medium text-gray-700 mb-2">
                            العقار <span class="text-red-500">*</span>
                        </label>
                        <select id="property_tax_id" name="property_tax_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">اختر العقار</option>
                            @foreach($propertyTaxes as $propertyTax)
                                <option value="{{ $propertyTax->id }}">
                                    {{ $propertyTax->property->title }} - {{ $propertyTax->property->location ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                        @error('property_tax_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Filing Type -->
                    <div>
                        <label for="filing_type" class="block text-sm font-medium text-gray-700 mb-2">
                            نوع الإقرار <span class="text-red-500">*</span>
                        </label>
                        <select id="filing_type" name="filing_type" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">اختر النوع</option>
                            <option value="annual">سنوي</option>
                            <option value="quarterly">ربع سنوي</option>
                            <option value="monthly">شهري</option>
                            <option value="provisional">مؤقت</option>
                            <option value="amended">معدل</option>
                            <option value="final">نهائي</option>
                        </select>
                        @error('filing_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tax Year -->
                    <div>
                        <label for="tax_year" class="block text-sm font-medium text-gray-700 mb-2">
                            السنة الضريبية <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="tax_year" name="tax_year" required
                               value="{{ now()->year }}" min="2020" max="{{ now()->year + 1 }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        @error('tax_year')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Period (for quarterly/monthly filings) -->
                    <div id="period_field" class="hidden">
                        <label for="period" class="block text-sm font-medium text-gray-700 mb-2">
                            الفترة
                        </label>
                        <select id="period" name="period"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="">اختر الفترة</option>
                            <option value="Q1">الربع الأول</option>
                            <option value="Q2">الربع الثاني</option>
                            <option value="Q3">الربع الثالث</option>
                            <option value="Q4">الربع الرابع</option>
                            <option value="H1">نصف الأول</option>
                            <option value="H2">نصف الثاني</option>
                            <option value="annual">سنوي</option>
                        </select>
                        @error('period')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Attachments -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            المرفقات
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                            <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                            <p class="text-sm text-gray-600 mb-2">اسحب وأفلت الملفات هنا أو انقر للاختيار</p>
                            <p class="text-xs text-gray-500">PDF, DOC, DOCX, JPG, JPEG, PNG (حتى 10MB)</p>
                            <input type="file" id="attachments" name="attachments[]" multiple
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                   class="hidden">
                            <button type="button" onclick="document.getElementById('attachments').click()"
                                    class="mt-3 bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                                <i class="fas fa-folder-open ml-2"></i>
                                اختيار ملفات
                            </button>
                        </div>
                        <div id="file_list" class="mt-3 space-y-2"></div>
                        @error('attachments')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            ملاحظات
                        </label>
                        <textarea id="notes" name="notes" rows="4"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                  placeholder="أدخل أي ملاحظات إضافية..."></textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-reverse space-x-3 pt-6 border-t border-gray-200">
                        <a href="{{ route('taxes.filing') }}" 
                           class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                            <i class="fas fa-times ml-2"></i>
                            إلغاء
                        </a>
                        <button type="submit" 
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium flex items-center">
                            <i class="fas fa-save ml-2"></i>
                            حفظ الإقرار
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Help Section -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-xl p-6">
            <div class="flex items-start">
                <div class="bg-blue-100 rounded-lg p-2 ml-4">
                    <i class="fas fa-info-circle text-blue-600"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-blue-900 mb-2">مساعدة</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• تأكد من اختيار العقار الصحيح قبل التقديم</li>
                        <li>• يمكن إرفاق مستندات داعمة مثل فواتير الضرائب السابقة</li>
                        <li>• الإقرارات المسودة يمكن تعديلها قبل التقديم النهائي</li>
                        <li>• استشر محاسب ضريبي للحصول على مساعدة احترافية</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show/hide period field based on filing type
    const filingTypeSelect = document.getElementById('filing_type');
    const periodField = document.getElementById('period_field');
    
    filingTypeSelect.addEventListener('change', function() {
        const quarterlyTypes = ['quarterly', 'monthly'];
        if (quarterlyTypes.includes(this.value)) {
            periodField.classList.remove('hidden');
        } else {
            periodField.classList.add('hidden');
            document.getElementById('period').value = '';
        }
    });

    // File upload handling
    const fileInput = document.getElementById('attachments');
    const fileList = document.getElementById('file_list');
    
    fileInput.addEventListener('change', function() {
        fileList.innerHTML = '';
        
        Array.from(this.files).forEach((file, index) => {
            const fileItem = document.createElement('div');
            fileItem.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-lg';
            
            const fileInfo = document.createElement('div');
            fileInfo.className = 'flex items-center';
            fileInfo.innerHTML = `
                <i class="fas fa-file text-gray-400 ml-2"></i>
                <div>
                    <p class="text-sm font-medium text-gray-900">${file.name}</p>
                    <p class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                </div>
            `;
            
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'text-red-500 hover:text-red-700 transition-colors';
            removeBtn.innerHTML = '<i class="fas fa-times"></i>';
            removeBtn.onclick = function() {
                fileItem.remove();
                // Create new FileList without this file
                const dt = new DataTransfer();
                Array.from(fileInput.files).forEach((f, i) => {
                    if (i !== index) dt.items.add(f);
                });
                fileInput.files = dt.files;
            };
            
            fileItem.appendChild(fileInfo);
            fileItem.appendChild(removeBtn);
            fileList.appendChild(fileItem);
        });
    });

    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const propertyTaxId = document.getElementById('property_tax_id').value;
        const filingType = document.getElementById('filing_type').value;
        const taxYear = document.getElementById('tax_year').value;
        
        if (!propertyTaxId || !filingType || !taxYear) {
            e.preventDefault();
            alert('يرجى ملء جميع الحقول المطلوبة');
            return;
        }
    });
});
</script>
@endpush
