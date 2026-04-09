@extends('admin.layouts.admin')

@section('title', 'تعديل المورد')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">تعديل المورد</h1>
            <p class="text-gray-600 mt-1">تعديل معلومات المورد</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('inventory.suppliers.show', $supplier) }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium flex items-center space-x-2 space-x-reverse transition-colors duration-200">
                <i class="fas fa-arrow-right"></i>
                <span>عودة</span>
            </a>
        </div>
    </div>
</div>

<!-- Form -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <form method="POST" action="{{ route('inventory.suppliers.update', $supplier) }}" class="p-6">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Basic Information -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">المعلومات الأساسية</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">اسم المورد *</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $supplier->name) }}" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل اسم المورد">
                        </div>
                        
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-2">كود المورد</label>
                            <input type="text" id="code" name="code" value="{{ old('code', $supplier->code) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل كود المورد">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                            <input type="email" id="email" name="email" value="{{ old('email', $supplier->email) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل البريد الإلكتروني">
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">الهاتف</label>
                            <input type="tel" id="phone" name="phone" value="{{ old('phone', $supplier->phone) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل رقم الهاتف">
                        </div>
                        
                        <div>
                            <label for="website" class="block text-sm font-medium text-gray-700 mb-2">الموقع الإلكتروني</label>
                            <input type="url" id="website" name="website" value="{{ old('website', $supplier->website) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل الموقع الإلكتروني">
                        </div>
                        
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">الحالة *</label>
                            <select id="status" name="status" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="active" {{ old('status', $supplier->status) == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="inactive" {{ old('status', $supplier->status) == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Address Information -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات العنوان</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">العنوان</label>
                            <input type="text" id="address" name="address" value="{{ old('address', $supplier->address) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل العنوان">
                        </div>
                        
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">المدينة</label>
                            <input type="text" id="city" name="city" value="{{ old('city', $supplier->city) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل المدينة">
                        </div>
                        
                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700 mb-2">الولاية/المنطقة</label>
                            <input type="text" id="state" name="state" value="{{ old('state', $supplier->state) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل الولاية أو المنطقة">
                        </div>
                        
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-2">البلد</label>
                            <input type="text" id="country" name="country" value="{{ old('country', $supplier->country) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل البلد">
                        </div>
                        
                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">الرمز البريدي</label>
                            <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code', $supplier->postal_code) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل الرمز البريدي">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Business Information -->
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات العمل</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="أدخل وصف المورد">{{ old('description', $supplier->description) }}</textarea>
                </div>
                
                <div>
                    <label for="payment_terms" class="block text-sm font-medium text-gray-700 mb-2">شروط الدفع</label>
                    <input type="text" id="payment_terms" name="payment_terms" value="{{ old('payment_terms', $supplier->payment_terms) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="أدخل شروط الدفع">
                </div>
                
                <div>
                    <label for="credit_limit" class="block text-sm font-medium text-gray-700 mb-2">الحد الائتماني</label>
                    <input type="number" id="credit_limit" name="credit_limit" step="0.01" value="{{ old('credit_limit', $supplier->credit_limit) }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="أدخل الحد الائتماني">
                </div>
                
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">ملاحظات</label>
                    <textarea id="notes" name="notes" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="أدخل أي ملاحظات">{{ old('notes', $supplier->notes) }}</textarea>
                </div>
            </div>
        </div>
        
        <!-- Form Actions -->
        <div class="mt-8 flex items-center justify-end space-x-4 space-x-reverse">
            <a href="{{ route('inventory.suppliers.show', $supplier) }}" 
               class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                إلغاء
            </a>
            <button type="submit" 
                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-save ml-2"></i>
                حفظ التغييرات
            </button>
        </div>
    </form>
</div>
@endsection
