@extends('admin.layouts.admin')

@section('title', 'إنشاء أمر عمل')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">إنشاء أمر عمل</h1>
            <p class="text-gray-600 mt-1">إنشاء أمر عمل جديد للصيانة</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('maintenance.workorders.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium flex items-center space-x-2 space-x-reverse transition-colors duration-200">
                <i class="fas fa-arrow-right"></i>
                <span>عودة</span>
            </a>
        </div>
    </div>
</div>

<!-- Form -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <form method="POST" action="{{ route('maintenance.workorders.store') }}" class="p-6">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Basic Information -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">المعلومات الأساسية</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="work_order_number" class="block text-sm font-medium text-gray-700 mb-2">رقم أمر العمل</label>
                            <input type="text" id="work_order_number" name="work_order_number" 
                                   value="WO-{{ date('Y-m') }}-{{ str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT) }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="رقم أمر العمل">
                        </div>
                        
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">العنوان *</label>
                            <input type="text" id="title" name="title" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل عنوان أمر العمل">
                        </div>
                        
                        <div>
                            <label for="title_ar" class="block text-sm font-medium text-gray-700 mb-2">العنوان (عربي)</label>
                            <input type="text" id="title_ar" name="title_ar"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل العنوان بالعربية">
                        </div>
                        
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">الوصف *</label>
                            <textarea id="description" name="description" required rows="4"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="أدخل وصف أمر العمل"></textarea>
                        </div>
                        
                        <div>
                            <label for="description_ar" class="block text-sm font-medium text-gray-700 mb-2">الوصف (عربي)</label>
                            <textarea id="description_ar" name="description_ar" rows="4"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="أدخل الوصف بالعربية"></textarea>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">الأولوية *</label>
                                <select id="priority" name="priority" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="low">منخفض</option>
                                    <option value="medium" selected>متوسط</option>
                                    <option value="high">عالي</option>
                                    <option value="emergency">طوارئ</option>
                                </select>
                            </div>
                            
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">النوع *</label>
                                <select id="type" name="type" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="repair" selected>إصلاح</option>
                                    <option value="maintenance">صيانة</option>
                                    <option value="installation">تثبيت</option>
                                    <option value="inspection">فحص</option>
                                    <option value="replacement">استبدال</option>
                                    <option value="other">أخرى</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Assignment Information -->
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات التكليف</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="property_id" class="block text-sm font-medium text-gray-700 mb-2">العقار</label>
                            <select id="property_id" name="property_id"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">اختر العقار</option>
                                @foreach($properties as $property)
                                    <option value="{{ $property->id }}">{{ $property->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-2">المكلف إليه</label>
                            <select id="assigned_to" name="assigned_to"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">اختر المستخدم</option>
                                <!-- Users will be loaded dynamically -->
                            </select>
                        </div>
                        
                        <div>
                            <label for="assigned_team_id" class="block text-sm font-medium text-gray-700 mb-2">الفريق المكلف</label>
                            <select id="assigned_team_id" name="assigned_team_id"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">اختر الفريق</option>
                                @foreach($teams as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="service_provider_id" class="block text-sm font-medium text-gray-700 mb-2">مقدم الخدمة</label>
                            <select id="service_provider_id" name="service_provider_id"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">اختر مقدم الخدمة</option>
                                @foreach($serviceProviders as $provider)
                                    <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="maintenance_request_id" class="block text-sm font-medium text-gray-700 mb-2">طلب الصيانة</label>
                            <select id="maintenance_request_id" name="maintenance_request_id"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">اختر طلب الصيانة</option>
                                @foreach($maintenanceRequests as $request)
                                    <option value="{{ $request->id }}">{{ $request->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">الموقع</label>
                            <input type="text" id="location" name="location"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل الموقع">
                        </div>
                        
                        <div>
                            <label for="location_ar" class="block text-sm font-medium text-gray-700 mb-2">الموقع (عربي)</label>
                            <input type="text" id="location_ar" name="location_ar"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="أدخل الموقع بالعربية">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Scheduling Information -->
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات الجدولة</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-2">التاريخ المجدول</label>
                    <input type="date" id="scheduled_date" name="scheduled_date"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="scheduled_time" class="block text-sm font-medium text-gray-700 mb-2">الوقت المجدول</label>
                    <input type="time" id="scheduled_time" name="scheduled_time"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                
                <div>
                    <label for="estimated_duration" class="block text-sm font-medium text-gray-700 mb-2">المدة التقديرية (دقائق)</label>
                    <input type="number" id="estimated_duration" name="estimated_duration" min="1"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="مثال: 120">
                </div>
            </div>
        </div>
        
        <!-- Cost Information -->
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات التكلفة</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div>
                    <label for="estimated_cost" class="block text-sm font-medium text-gray-700 mb-2">التكلفة التقديرية</label>
                    <input type="number" id="estimated_cost" name="estimated_cost" step="0.01" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0.00">
                </div>
                
                <div>
                    <label for="labor_cost" class="block text-sm font-medium text-gray-700 mb-2">تكلفة العمالة</label>
                    <input type="number" id="labor_cost" name="labor_cost" step="0.01" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0.00">
                </div>
                
                <div>
                    <label for="material_cost" class="block text-sm font-medium text-gray-700 mb-2">تكلفة المواد</label>
                    <input type="number" id="material_cost" name="material_cost" step="0.01" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0.00">
                </div>
                
                <div>
                    <label for="other_cost" class="block text-sm font-medium text-gray-700 mb-2">تكاليف أخرى</label>
                    <input type="number" id="other_cost" name="other_cost" step="0.01" min="0"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="0.00">
                </div>
            </div>
        </div>
        
        <!-- Additional Information -->
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات إضافية</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="access_instructions" class="block text-sm font-medium text-gray-700 mb-2">تعليمات الوصول</label>
                    <textarea id="access_instructions" name="access_instructions" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="أدخل تعليمات الوصول"></textarea>
                </div>
                
                <div>
                    <label for="access_instructions_ar" class="block text-sm font-medium text-gray-700 mb-2">تعليمات الوصول (عربي)</label>
                    <textarea id="access_instructions_ar" name="access_instructions_ar" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="أدخل تعليمات الوصول بالعربية"></textarea>
                </div>
                
                <div>
                    <label for="safety_requirements" class="block text-sm font-medium text-gray-700 mb-2">متطلبات السلامة</label>
                    <textarea id="safety_requirements" name="safety_requirements" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="أدخل متطلبات السلامة"></textarea>
                </div>
                
                <div>
                    <label for="safety_requirements_ar" class="block text-sm font-medium text-gray-700 mb-2">متطلبات السلامة (عربي)</label>
                    <textarea id="safety_requirements_ar" name="safety_requirements_ar" rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                              placeholder="أدخل متطلبات السلامة بالعربية"></textarea>
                </div>
            </div>
            
            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">ملاحظات</label>
                <textarea id="notes" name="notes" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="أدخل أي ملاحظات إضافية"></textarea>
            </div>
            
            <div class="mt-4">
                <label for="notes_ar" class="block text-sm font-medium text-gray-700 mb-2">ملاحظات (عربي)</label>
                <textarea id="notes_ar" name="notes_ar" rows="3"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="أدخل الملاحظات بالعربية"></textarea>
            </div>
        </div>
        
        <!-- Form Actions -->
        <div class="mt-8 flex items-center justify-end space-x-4 space-x-reverse">
            <a href="{{ route('maintenance.workorders.index') }}" 
               class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                إلغاء
            </a>
            <button type="submit" 
                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-save ml-2"></i>
                إنشاء أمر العمل
            </button>
        </div>
    </form>
</div>
@endsection
