@extends('admin.layouts.admin')

@section('title', 'إنشاء فريق صيانة')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">إنشاء فريق صيانة</h1>
            <p class="text-gray-600 mt-1">إضافة فريق صيانة جديد للنظام</p>
        </div>
        <div class="flex items-center space-x-3 space-x-reverse">
            <a href="{{ route('maintenance.teams.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium flex items-center space-x-2 space-x-reverse transition-colors duration-200">
                <i class="fas fa-arrow-right"></i>
                <span>عودة</span>
            </a>
        </div>
    </div>
</div>

<!-- Create Form -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">معلومات الفريق</h2>
    </div>
    
    <form method="POST" action="{{ route('maintenance.teams.store') }}" class="p-6">
        @csrf
        
        <!-- Basic Information -->
        <div class="space-y-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">اسم الفريق *</label>
                    <input type="text" id="name" name="name" required
                           value="{{ old('name') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="أدخل اسم الفريق">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="team_code" class="block text-sm font-medium text-gray-700 mb-2">كود الفريق</label>
                    <input type="text" id="team_code" name="team_code"
                           value="{{ old('team_code') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="مثال: MT-001">
                    @error('team_code')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">وصف الفريق</label>
                <textarea id="description" name="description" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="أدخل وصفاً للفريق ومهامه">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <!-- Leadership -->
        <div class="space-y-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">القيادة</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="team_leader_id" class="block text-sm font-medium text-gray-700 mb-2">قائد الفريق</label>
                    <select id="team_leader_id" name="team_leader_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">اختر قائد الفريق</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('team_leader_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('team_leader_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="specialization" class="block text-sm font-medium text-gray-700 mb-2">التخصص</label>
                    <select id="specialization" name="specialization"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">اختر التخصص</option>
                        <option value="plumbing" {{ old('specialization') == 'plumbing' ? 'selected' : '' }}>سباكة</option>
                        <option value="electrical" {{ old('specialization') == 'electrical' ? 'selected' : '' }}>كهرباء</option>
                        <option value="hvac" {{ old('specialization') == 'hvac' ? 'selected' : '' }}>تكييف وتبريد</option>
                        <option value="carpentry" {{ old('specialization') == 'carpentry' ? 'selected' : '' }}>نجارة</option>
                        <option value="painting" {{ old('specialization') == 'painting' ? 'selected' : '' }}>دهان</option>
                        <option value="general" {{ old('specialization') == 'general' ? 'selected' : '' }}>صيانة عامة</option>
                        <option value="landscaping" {{ old('specialization') == 'landscaping' ? 'selected' : '' }}>تنسيق حدائق</option>
                        <option value="cleaning" {{ old('specialization') == 'cleaning' ? 'selected' : '' }}>تنظيف</option>
                        <option value="security" {{ old('specialization') == 'security' ? 'selected' : '' }}>أمن</option>
                        <option value="it" {{ old('specialization') == 'it' ? 'selected' : '' }}>تقنية معلومات</option>
                    </select>
                    @error('specialization')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- Contact Information -->
        <div class="space-y-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات الاتصال</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">هاتف الاتصال</label>
                    <input type="tel" id="contact_phone" name="contact_phone"
                           value="{{ old('contact_phone') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="أدخل رقم الهاتف">
                    @error('contact_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                    <input type="email" id="contact_email" name="contact_email"
                           value="{{ old('contact_email') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           placeholder="أدخل البريد الإلكتروني">
                    @error('contact_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- Working Hours -->
        <div class="space-y-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">ساعات العمل</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="working_hours_start" class="block text-sm font-medium text-gray-700 mb-2">وقت البدء</label>
                    <input type="time" id="working_hours_start" name="working_hours_start"
                           value="{{ old('working_hours_start', '08:00') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('working_hours_start')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="working_hours_end" class="block text-sm font-medium text-gray-700 mb-2">وقت الانتهاء</label>
                    <input type="time" id="working_hours_end" name="working_hours_end"
                           value="{{ old('working_hours_end', '17:00') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('working_hours_end')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="max_concurrent_jobs" class="block text-sm font-medium text-gray-700 mb-2">الحد الأقصى للوظائف</label>
                    <input type="number" id="max_concurrent_jobs" name="max_concurrent_jobs"
                           value="{{ old('max_concurrent_jobs', 5) }}"
                           min="1" max="20"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('max_concurrent_jobs')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <!-- Additional Information -->
        <div class="space-y-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">معلومات إضافية</h3>
            
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">ملاحظات</label>
                <textarea id="notes" name="notes" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="أي ملاحظات إضافية عن الفريق">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <!-- Status -->
        <div class="space-y-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">الحالة</h3>
            
            <div class="flex items-center">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                       {{ old('is_active', '1') ? 'checked' : '' }}
                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="is_active" class="mr-2 block text-sm text-gray-700">
                    فريق نشط
                </label>
            </div>
        </div>
        
        <!-- Form Actions -->
        <div class="flex items-center justify-between pt-6 border-t border-gray-200">
            <a href="{{ route('maintenance.teams.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                <i class="fas fa-times ml-2"></i>
                إلغاء
            </a>
            
            <div class="flex items-center space-x-3 space-x-reverse">
                <button type="reset" 
                        class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                    <i class="fas fa-redo ml-2"></i>
                    إعادة تعيين
                </button>
                
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200">
                    <i class="fas fa-save ml-2"></i>
                    حفظ الفريق
                </button>
            </div>
        </div>
    </form>
</div>

<!-- JavaScript for form enhancements -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate team code based on team name
    const nameInput = document.getElementById('name');
    const codeInput = document.getElementById('team_code');
    
    nameInput.addEventListener('input', function() {
        if (!codeInput.value) {
            const name = this.value.trim();
            if (name) {
                // Generate a simple code from the name
                const code = name.substring(0, 3).toUpperCase() + '-' + 
                            Math.floor(Math.random() * 1000).toString().padStart(3, '0');
                codeInput.value = code;
            }
        }
    });
    
    // Validate working hours
    const startTimeInput = document.getElementById('working_hours_start');
    const endTimeInput = document.getElementById('working_hours_end');
    
    function validateWorkingHours() {
        const startTime = startTimeInput.value;
        const endTime = endTimeInput.value;
        
        if (startTime && endTime && startTime >= endTime) {
            endTimeInput.setCustomValidity('وقت الانتهاء يجب أن يكون بعد وقت البدء');
        } else {
            endTimeInput.setCustomValidity('');
        }
    }
    
    startTimeInput.addEventListener('change', validateWorkingHours);
    endTimeInput.addEventListener('change', validateWorkingHours);
});
</script>
@endsection
