@extends('layouts.app')

@section('title', 'تقديم شكوى')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">تقديم شكوى</h1>
        <p class="text-gray-600">نحن هنا لمساعدتك في حل أي مشكلة تواجهها</p>
    </div>

    <!-- Guidelines -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
        <h3 class="text-lg font-semibold text-blue-900 mb-3">
            <i class="fas fa-info-circle ml-2"></i>إرشادات تقديم الشكوى
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h4 class="font-medium text-blue-800 mb-2">قبل التقديم:</h4>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• كن واضحاً ومحدداً في وصف المشكلة</li>
                    <li>• قدم كل المعلومات ذات الصلة</li>
                    <li>• كن محترماً في لغتك</li>
                    <li>• تجنب المعلومات الشخصية غير الضرورية</li>
                </ul>
            </div>
            <div>
                <h4 class="font-medium text-blue-800 mb-2">بعد التقديم:</h4>
                <ul class="text-sm text-blue-700 space-y-1">
                    <li>• ستحصل على رقم مرجعي لتتبع الشكوى</li>
                    <li>• سيتم مراجعة شكوتك في غضون 24-48 ساعة</li>
                    <li>• سنتواصل معك عبر القناة المفضلة</li>
                    <li>• يمكنك متابعة حالة الشكوى في أي وقت</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Complaint Form -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <form action="{{ route('complaints.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        نوع الشكوى <span class="text-red-500">*</span>
                    </label>
                    <select name="type" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">اختر نوع الشكوى</option>
                        <option value="service_quality">جودة الخدمة</option>
                        <option value="property_issue">مشكلة في العقار</option>
                        <option value="payment_dis pars">نزاع دفعchy</optionieh
                        <option value="communication">مشكلة تواصل</option>
                        <option value="contract_v indiscretion">,">انتهاك.
                        <.
                       ,>انتهاكtv
                       ,>انتها.
                        <option value="safety_concern">قضية أمان</option>
                        <option value="discrimination">تمييز</option>
                        <option value="fraud">احتيال</option>
                        <option value="other">أخرى</option>
                    </select>
                </div>

                <!-- Urgency Level -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        مستوى الإلحاح <span class="text-red-500">*</span>
                    </label>
                    <select name="urgency_level" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">اختر مستوى الإلحاح</option>
                        <option value="low">منخفض</option>
                        <option value="medium">متوسط</option>
                        <option value="high">مرتفع</option>
                        <option value="critical">حرج</option>
                    </select>
                </div>

                <!-- Title -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        عنوان الشكوى <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" required maxlength="255"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="أدخل عنواناً موجزاً للشكوى">
                </div>

                <!-- Description -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        وصف الشكوى <span class="text-red-500">*</span>
                    </label>
                    <textarea name="description" rows="6" required minlength="50"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="صف المشكلة بالتفصيل... ماذا حدث؟ متى حدث؟ من كان involved؟"></textarea>
                    <p class="text-xs text-gray-500 mt-1">يجب أن يكون على الأقل 50 حرفاً</p>
                </div>

                <!-- Expected Resolution -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        الحل المتوقع (اختياري)
                    </label>
                    <textarea name="expected_resolution" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="ما هو الحل الذي تتوقعونه؟"></textarea>
                </div>

                <!-- Contact Preference -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        طريقة التواصل المفضلة <span class="text-red-500">*</span>
                    </label>
                    <select name="contact_preference" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">اختر طريقة التواصل</option>
                        <option value="email">البريد الإلكتروني</option>
                        <option value="phone">الهاتف</option>
                        <option value="sms">رسالة نصية</option>
                        <option value="whatsapp">واتساب</option>
                        <option value="in_person">شخصياً</option>
                    </select>
                </div>

                <!-- Contact Details -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        تفاصيل التواصل <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="contact_details" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="البريد الإلكتروني أو رقم الهاتف">
                </div>

                <!-- Attachments -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        مرفقات (اختياري)
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center">
                        <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-3"></i>
                        <p class="text-sm text-gray-600 mb-2">اسحب وأفلت الملفات هنا أو</p>
                        <input type="file" name="attachments[]" multiple accept="image/*,application/pdf,.doc,.docx"
                               class="hidden" id="file-input">
                        <button type="button" onclick="document.getElementById('file-input').click()"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            اختر الملفات
                        </button>
                        <p class="text-xs text-gray-500 mt-2">الصور، PDF، Word (الحد الأقصى: 5 ميجابايت)</p>
                        <div id="file-list" class="mt-3 text-left"></div>
                    </div>
                </div>
            </div>

            <!-- Terms and Conditions -->
            <div class="mt-6">
                <label class="flex items-start cursor-pointer">
                    <input type="checkbox" name="terms" value="1" required class="ml-2 mt-1">
                    <span class="text-sm text-gray-700">
                        أوافق على 
                        <a href="#" class="text-blue-600 hover:text-blue-800">الشروط والأحكام</a>
                        و 
                        <a href="#" class="text-blue-600 hover:text-blue-800">سياسة الخصوصية</a>
                        وأقر بأن جميع المعلومات المقدمة صحيحة ودقيقة.
                    </span>
                </label>
            </div>

            <!-- Form Actions -->
            <div class="mt-8 flex justify-between">
                <a href="{{ route('complaints.index') }}" 
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    <i class="fas fa-arrow-right ml-2"></i>
                    إلغاء
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-paper-plane ml-2"></i>
                    تقديم الشكوى
                </button>
            </div>
        </form>
    </div>

    <!-- Contact Info -->
    <div class="mt-8 bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">هل تحتاج إلى مساعدة عاجلة؟</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="text-center">
                <i class="fas fa-phone text-2xl text-blue-600 mb-2"></i>
                <p class="font-medium">الهاتف</p>
                <p class="text-sm text-gray-600">966-50-123-4567</p>
            </div>
            <div class="text-center">
                <i class="fas fa-envelope text-2xl text-blue-600 mb-2"></i>
                <p class="font-medium">البريد الإلكتروني</p>
                <p class="text-sm text-gray-600">support@example.com</p>
            </div>
            <div class="text-center">
                <i class="fas fa-comments text-2xl text-blue-600 mb-2"></i>
                <p class="font-medium">الدردشة المباشرة</p>
                <p class="text-sm text-gray-600">24/7 متاحة</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('file-input');
    const fileList = document.getElementById('file-list');
    
    fileInput.addEventListener('change', function() {
        fileList.innerHTML = '';
        
        for (let i = 0; i < this.files.length; i++) {
            const file = this.files[i];
            const fileItem = document.createElement('div');
            fileItem.className = 'flex items-center justify-between bg-gray-100 rounded p-2 mb-2';
            
            const fileInfo = document.createElement('div');
            fileInfo.className = 'flex items-center';
            fileInfo.innerHTML = `
                <i class="fas fa-file ml-2 text-gray-600"></i>
                <span class="text-sm text-gray-700">${file.name}</span>
                <span class="text-xs text-gray-500 mr-2">(${(file.size / 1024 / 1024).toFixed(2)} MB)</span>
            `;
            
            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'text-red-600 hover:text-red-800';
            removeBtn.innerHTML = '<i class="fas fa-times"></i>';
            removeBtn.onclick = function() {
                fileItem.remove();
            };
            
            fileItem.appendChild(fileInfo);
            fileItem.appendChild(removeBtn);
            fileList.appendChild(fileItem);
        }
    });

    // Drag and drop
    const dropZone = fileInput.parentElement;
    
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('border-blue-400', 'bg-blue-50');
    });
    
    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('border-blue-400', 'bg-blue-50');
    });
    
    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('border-blue-400', 'bg-blue-50');
        
        fileInput.files = e.dataTransfer.files;
        fileInput.dispatchEvent(new Event('change'));
    });
});
</script>
@endsection
