@extends('admin.layouts.admin')

@section('title', 'إضافة عنصر جديد')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">إضافة عنصر جديد</h1>
            <p class="text-gray-600 mt-1">إضافة عنصر جديد إلى المخزون</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('inventory.items.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium flex items-center space-x-2 space-x-reverse transition-colors duration-200">
                <i class="fas fa-arrow-right"></i>
                <span>عودة</span>
            </a>
        </div>
    </div>
</div>

<!-- Form -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <form method="POST" action="{{ route('inventory.items.store') }}" class="p-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Basic Information -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">المعلومات الأساسية</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="item_code" class="block text-sm font-medium text-gray-700 mb-2">كود العنصر *</label>
                            <input type="text" id="item_code" name="item_code" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل كود العنصر">
                        </div>
                        
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">اسم العنصر *</label>
                            <input type="text" id="name" name="name" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل اسم العنصر">
                        </div>
                        
                        <div>
                            <label for="name_ar" class="block text-sm font-medium text-gray-700 mb-2">الاسم بالعربية</label>
                            <input type="text" id="name_ar" name="name_ar"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل الاسم بالعربية">
                        </div>
                        
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                            <textarea id="description" name="description" rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="أدخل وصف العنصر"></textarea>
                        </div>
                        
                        <div>
                            <label for="description_ar" class="block text-sm font-medium text-gray-700 mb-2">الوصف بالعربية</label>
                            <textarea id="description_ar" name="description_ar" rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="أدخل الوصف بالعربية"></textarea>
                        </div>
                        
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">الفئة *</label>
                            <select id="category" name="category" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">اختر الفئة</option>
                                <option value="tools">أدوات</option>
                                <option value="materials">مواد</option>
                                <option value="equipment">معدات</option>
                                <option value="supplies">لوازم</option>
                                <option value="safety">سلامة</option>
                                <option value="other">أخرى</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">الحالة *</label>
                            <select id="status" name="status" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="active">نشط</option>
                                <option value="inactive">غير نشط</option>
                                <option value="discontinued">متوقف</option>
                                <option value="out_of_stock">نفد المخزون</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Details & Specifications -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">التفاصيل والمواصفات</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="brand" class="block text-sm font-medium text-gray-700 mb-2">العلامة التجارية</label>
                            <input type="text" id="brand" name="brand"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل العلامة التجارية">
                        </div>
                        
                        <div>
                            <label for="model" class="block text-sm font-medium text-gray-700 mb-2">الموديل</label>
                            <input type="text" id="model" name="model"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل الموديل">
                        </div>
                        
                        <div>
                            <label for="sku" class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
                            <input type="text" id="sku" name="sku"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل SKU">
                        </div>
                        
                        <div>
                            <label for="unit" class="block text-sm font-medium text-gray-700 mb-2">الوحدة *</label>
                            <input type="text" id="unit" name="unit" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل الوحدة">
                        </div>
                        
                        <div>
                            <label for="unit_ar" class="block text-sm font-medium text-gray-700 mb-2">الوحدة بالعربية</label>
                            <input type="text" id="unit_ar" name="unit_ar"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل الوحدة بالعربية">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="unit_cost" class="block text-sm font-medium text-gray-700 mb-2">التكلفة للوحدة *</label>
                                <input type="number" id="unit_cost" name="unit_cost" step="0.01" required
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="0.00">
                            </div>
                            
                            <div>
                                <label for="selling_price" class="block text-sm font-medium text-gray-700 mb-2">سعر البيع</label>
                                <input type="number" id="selling_price" name="selling_price" step="0.01"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       placeholder="0.00">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stock Information -->
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات المخزون</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-2">الكمية الحالية *</label>
                    <input type="number" id="quantity" name="quantity" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0">
                </div>
                
                <div>
                    <label for="min_quantity" class="block text-sm font-medium text-gray-700 mb-2">الحد الأدنى للكمية</label>
                    <input type="number" id="min_quantity" name="min_quantity"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0">
                </div>
                
                <div>
                    <label for="max_quantity" class="block text-sm font-medium text-gray-700 mb-2">الحد الأقصى للكمية</label>
                    <input type="number" id="max_quantity" name="max_quantity"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0">
                </div>
                
                <div>
                    <label for="reorder_point" class="block text-sm font-medium text-gray-700 mb-2">نقطة إعادة الطلب</label>
                    <input type="number" id="reorder_point" name="reorder_point"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0">
                </div>
            </div>
        </div>
        
        <!-- Supplier Information -->
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات المورد</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="supplier" class="block text-sm font-medium text-gray-700 mb-2">المورد</label>
                    <input type="text" id="supplier" name="supplier"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="أدخل اسم المورد">
                </div>
                
                <div>
                    <label for="supplier_contact" class="block text-sm font-medium text-gray-700 mb-2">بيانات الاتصال بالمورد</label>
                    <input type="text" id="supplier_contact" name="supplier_contact"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="أدخل بيانات الاتصال">
                </div>
            </div>
        </div>
        
        <!-- Additional Information -->
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات إضافية</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">الموقع</label>
                    <input type="text" id="location" name="location"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="أدخل الموقع">
                </div>
                
                <div>
                    <label for="location_ar" class="block text-sm font-medium text-gray-700 mb-2">الموقع بالعربية</label>
                    <input type="text" id="location_ar" name="location_ar"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="أدخل الموقع بالعربية">
                </div>
                
                <div>
                    <label for="barcode" class="block text-sm font-medium text-gray-700 mb-2">الباركود</label>
                    <input type="text" id="barcode" name="barcode"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="أدخل الباركود">
                </div>
                
                <div>
                    <label for="qr_code" class="block text-sm font-medium text-gray-700 mb-2">رمز QR</label>
                    <input type="text" id="qr_code" name="qr_code"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="أدخل رمز QR">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                <div>
                    <label for="warranty_expiry" class="block text-sm font-medium text-gray-700 mb-2">تاريخ انتهاء الضمان</label>
                    <input type="date" id="warranty_expiry" name="warranty_expiry"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="expiry_date" class="block text-sm font-medium text-gray-700 mb-2">تاريخ انتهاء الصلاحية</label>
                    <input type="date" id="expiry_date" name="expiry_date"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>
        
        <!-- Notes -->
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">ملاحظات</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">ملاحظات</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="أدخل أي ملاحظات"></textarea>
                </div>
                
                <div>
                    <label for="notes_ar" class="block text-sm font-medium text-gray-700 mb-2">ملاحظات بالعربية</label>
                    <textarea id="notes_ar" name="notes_ar" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="أدخل ملاحظات بالعربية"></textarea>
                </div>
            </div>
        </div>
        
        <!-- Form Actions -->
        <div class="mt-8 flex items-center justify-end space-x-4 space-x-reverse">
            <a href="{{ route('inventory.items.index') }}" 
               class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                إلغاء
            </a>
            <button type="submit" 
                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-save ml-2"></i>
                حفظ العنصر
            </button>
        </div>
    </form>
</div>
@endsection
