@extends('admin.layouts.admin')

@section('title', 'إضافة منتج جديد')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8 mb-8">
            <div class="flex items-center gap-4 mb-6">
                <a href="{{ route('blockchain.metaverse.marketplace') }}" class="text-gray-600 hover:text-gray-900 transition-colors">
                    <i class="fas fa-arrow-right text-xl"></i>
                </a>
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-600 to-emerald-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-plus-circle text-white text-xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">
                        إضافة منتج جديد
                    </h1>
                </div>
            </div>
            <p class="text-gray-600 text-lg">
                أضف عقاراً جديداً إلى السوق الافتراضي
            </p>
        </div>

        <!-- Form -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 p-8">
            <form action="{{ route('blockchain.blockchain.metaverse.marketplace.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                
                <!-- Basic Information -->
                <div class="space-y-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-info-circle text-blue-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">المعلومات الأساسية</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-heading ml-2 text-blue-600"></i>
                                عنوان العقار
                            </label>
                            <input type="text" name="title" required
                                   class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-gray-50 hover:bg-white"
                                   placeholder="أدخل عنوان العقار">
                            @error('title')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-tag ml-2 text-blue-600"></i>
                                نوع العقار
                            </label>
                            <select name="property_type" required
                                    class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-gray-50 hover:bg-white">
                                <option value="">اختر نوع العقار</option>
                                <option value="residential">سكني</option>
                                <option value="commercial">تجاري</option>
                                <option value="industrial">صناعي</option>
                                <option value="mixed">مختلط</option>
                                <option value="recreational">ترفيهي</option>
                                <option value="educational">تعليمي</option>
                                <option value="healthcare">صحي</option>
                                <option value="office">مكتبي</option>
                                <option value="retail">تجزئة</option>
                                <option value="hospitality">ضيافة</option>
                            </select>
                            @error('property_type')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-align-left ml-2 text-blue-600"></i>
                            وصف العقار
                        </label>
                        <textarea name="description" required rows="4"
                                  class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-gray-50 hover:bg-white resize-none"
                                  placeholder="أدخل وصفاً مفصلاً للعقار"></textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-image ml-2 text-blue-600"></i>
                            صورة العقار
                        </label>
                        <div class="space-y-4">
                            <div class="flex items-center justify-center w-full">
                                <label for="property_image" class="flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-2xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors duration-300">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                        <p class="mb-2 text-sm text-gray-500">
                                            <span class="font-semibold">اضغط لرفع صورة</span> أو اسحب وأفلت
                                        </p>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF (حتى 10MB)</p>
                                    </div>
                                    <input id="property_image" name="property_image" type="file" class="hidden" accept="image/*" />
                                </label>
                            </div>
                            <div id="image_preview" class="hidden">
                                <img id="preview_img" src="" alt="Preview" class="w-full h-64 object-cover rounded-2xl shadow-lg">
                                <button type="button" onclick="removeImage()" class="mt-2 text-red-600 hover:text-red-700 text-sm font-medium">
                                    <i class="fas fa-trash ml-1"></i>
                                    إزالة الصورة
                                </button>
                            </div>
                        </div>
                        @error('property_image')
                            <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Location Information -->
                <div class="space-y-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-map-marker-alt text-green-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">الموقع</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-globe ml-2 text-green-600"></i>
                                العالم الافتراضي
                            </label>
                            <select name="virtual_world_id" required
                                    class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-gray-50 hover:bg-white">
                                <option value="">اختر العالم الافتراضي</option>
                                @foreach($virtualWorlds as $world)
                                <option value="{{ $world->id }}">{{ $world->name }} - {{ $world->world_type_text }}</option>
                                @endforeach
                            </select>
                            @error('virtual_world_id')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-map-pin ml-2 text-green-600"></i>
                                الإحداثيات
                            </label>
                            <input type="text" name="location_coordinates" required
                                   class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-gray-50 hover:bg-white"
                                   placeholder="مثال: (100, 50, 25)">
                            @error('location_coordinates')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Pricing Information -->
                <div class="space-y-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-yellow-600"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">التسعير</h2>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-tag ml-2 text-yellow-600"></i>
                                سعر البيع
                            </label>
                            <input type="number" name="price" required step="0.01" min="0"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-gray-50 hover:bg-white"
                                   placeholder="0.00">
                            @error('price')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-home ml-2 text-yellow-600"></i>
                                سعر الإيجار (اختياري)
                            </label>
                            <input type="number" name="rent_price" step="0.01" min="0"
                                   class="w-full px-4 py-3 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-300 bg-gray-50 hover:bg-white"
                                   placeholder="0.00">
                            @error('rent_price')
                                <p class="mt-2 text-sm text-red-600 flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="flex items-center gap-4">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="is_for_sale" value="1" checked
                                       class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="mr-3 font-medium text-gray-700">
                                    <i class="fas fa-shopping-cart ml-2 text-green-600"></i>
                                    متاح للبيع
                                </span>
                            </label>
                        </div>

                        <div class="flex items-center gap-4">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" name="is_for_rent" value="1"
                                       class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <span class="mr-3 font-medium text-gray-700">
                                    <i class="fas fa-key ml-2 text-blue-600"></i>
                                    متاح للإيجار
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 pt-8 border-t">
                    <a href="{{ route('blockchain.metaverse.marketplace') }}" 
                       class="flex-1 bg-gray-200 text-gray-700 px-8 py-4 rounded-2xl hover:bg-gray-300 transition-all duration-300 font-semibold text-center">
                        <i class="fas fa-times ml-2"></i>
                        إلغاء
                    </a>
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-green-600 to-emerald-600 text-white px-8 py-4 rounded-2xl hover:from-green-700 hover:to-emerald-700 transition-all duration-300 font-semibold shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-save ml-2"></i>
                        حفظ العقار
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Image upload preview
document.getElementById('property_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('image_preview');
    const previewImg = document.getElementById('preview_img');
    
    if (file) {
        // Check file size (10MB max)
        if (file.size > 10 * 1024 * 1024) {
            showNotification('حجم الصورة يجب أن يكون أقل من 10MB', 'error');
            e.target.value = '';
            return;
        }
        
        // Check file type
        if (!file.type.startsWith('image/')) {
            showNotification('يرجى اختيار ملف صورة صالح', 'error');
            e.target.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.classList.remove('hidden');
        }
        reader.readAsDataURL(file);
    }
});

function removeImage() {
    document.getElementById('property_image').value = '';
    document.getElementById('image_preview').classList.add('hidden');
    document.getElementById('preview_img').src = '';
}

// Auto-enable rent price field when "for rent" is checked
document.querySelector('input[name="is_for_rent"]').addEventListener('change', function() {
    const rentPriceField = document.querySelector('input[name="rent_price"]');
    if (this.checked) {
        rentPriceField.required = true;
        rentPriceField.parentElement.classList.add('ring-2', 'ring-blue-500');
    } else {
        rentPriceField.required = false;
        rentPriceField.parentElement.classList.remove('ring-2', 'ring-blue-500');
    }
});

// Form validation feedback
document.querySelector('form').addEventListener('submit', function(e) {
    const title = document.querySelector('input[name="title"]').value;
    const description = document.querySelector('textarea[name="description"]').value;
    const price = document.querySelector('input[name="price"]').value;
    
    if (!title || !description || !price) {
        e.preventDefault();
        showNotification('يرجى ملء جميع الحقول المطلوبة', 'error');
    }
});

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 left-1/2 transform -translate-x-1/2 px-6 py-3 rounded-xl shadow-lg z-50 transition-all duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
@endsection
