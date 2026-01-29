@extends('layouts.app')

@section('title', 'User Preferences')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="bg-blue-100 rounded-lg p-3">
                        <i class="fas fa-cog text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">التفضيلات</h1>
                        <p class="text-gray-600 mt-1">تخصيص تجربتك وإعدادات الإشعارات</p>
                    </div>
                </div>
                <a href="{{ route('user.profile') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-right ml-2"></i>
                    العودة للملف الشخصي
                </a>
            </div>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6 flex items-center">
                <i class="fas fa-check-circle text-green-600 ml-3"></i>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Preferences Form -->
        <form action="{{ route('settings.preferences.update') }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')
            
            <!-- Notification Preferences -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white flex items-center">
                        <i class="fas fa-bell ml-3"></i>
                        تفضيلات الإشعارات
                    </h2>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Email Notifications -->
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-200 hover:bg-blue-100 transition-colors duration-200">
                        <div class="flex items-center space-x-4">
                            <div class="bg-blue-100 rounded-lg p-2">
                                <i class="fas fa-envelope text-blue-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">الإشعارات البريدية</h3>
                                <p class="text-sm text-gray-600 mt-1">استلام تحديثات البريد الإلكتروني حول نشاط حسابك</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="email_notifications" class="sr-only peer" {{ $preferences->email_notifications ?? true ? 'checked' : '' }}>
                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                        </label>
                    </div>
                    
                    <!-- SMS Notifications -->
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200 hover:bg-green-100 transition-colors duration-200">
                        <div class="flex items-center space-x-4">
                            <div class="bg-green-100 rounded-lg p-2">
                                <i class="fas fa-sms text-green-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">إشعارات الرسائل النصية</h3>
                                <p class="text-sm text-gray-600 mt-1">استلام رسائل نصية للتحديثات المهمة</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="sms_notifications" class="sr-only peer" {{ $preferences->sms_notifications ?? false ? 'checked' : '' }}>
                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                    
                    <!-- Push Notifications -->
                    <div class="flex items-center justify-between p-4 bg-purple-50 rounded-lg border border-purple-200 hover:bg-purple-100 transition-colors duration-200">
                        <div class="flex items-center space-x-4">
                            <div class="bg-purple-100 rounded-lg p-2">
                                <i class="fas fa-desktop text-purple-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">الإشعارات الفورية</h3>
                                <p class="text-sm text-gray-600 mt-1">استلام الإشعارات الفورية في المتصفح</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="push_notifications" class="sr-only peer" {{ $preferences->push_notifications ?? true ? 'checked' : '' }}>
                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-purple-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Property Alerts -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white flex items-center">
                        <i class="fas fa-home ml-3"></i>
                        تنبيهات العقارات
                    </h2>
                </div>
                
                <div class="p-6 space-y-6">
                    <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg border border-green-200 hover:bg-green-100 transition-colors duration-200">
                        <div class="flex items-center space-x-4">
                            <div class="bg-green-100 rounded-lg p-2">
                                <i class="fas fa-search text-green-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">تطابقات العقارات الجديدة</h3>
                                <p class="text-sm text-gray-600 mt-1">الحصول على إشعارات عند وجود عقارات جديدة تطابق معاييرك</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="property_alerts" class="sr-only peer" {{ $preferences->property_alerts ?? true ? 'checked' : '' }}>
                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-yellow-50 rounded-lg border border-yellow-200 hover:bg-yellow-100 transition-colors duration-200">
                        <div class="flex items-center space-x-4">
                            <div class="bg-yellow-100 rounded-lg p-2">
                                <i class="fas fa-dollar-sign text-yellow-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">تغييرات الأسعار</h3>
                                <p class="text-sm text-gray-600 mt-1">الحصول على إشعارات عند تغيير أسعار العقارات المحفوظة</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="price_drop_alerts" class="sr-only peer" {{ $preferences->price_drop_alerts ?? true ? 'checked' : '' }}>
                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-yellow-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-yellow-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-orange-50 rounded-lg border border-orange-200 hover:bg-orange-100 transition-colors duration-200">
                        <div class="flex items-center space-x-4">
                            <div class="bg-orange-100 rounded-lg p-2">
                                <i class="fas fa-calendar text-orange-600"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">إشعارات المعاينات المفتوحة</h3>
                                <p class="text-sm text-gray-600 mt-1">الحصول على إشعارات حول المعاينات المفتوحة للعقارات المحفوظة</p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="new_listing_alerts" class="sr-only peer" {{ $preferences->new_listing_alerts ?? false ? 'checked' : '' }}>
                            <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-orange-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Communication Preferences -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white flex items-center">
                        <i class="fas fa-comments ml-3"></i>
                        تفضيلات التواصل
                    </h2>
                </div>
                
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">طريقة التواصل المفضلة</label>
                            <select name="preferred_contact" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                                <option value="email" {{ ($preferences->preferred_contact ?? 'email') === 'email' ? 'selected' : '' }}>
                                    <i class="fas fa-envelope ml-2"></i> البريد الإلكتروني
                                </option>
                                <option value="phone" {{ ($preferences->preferred_contact ?? 'email') === 'phone' ? 'selected' : '' }}>
                                    <i class="fas fa-phone ml-2"></i> الهاتف
                                </option>
                                <option value="sms" {{ ($preferences->preferred_contact ?? 'email') === 'sms' ? 'selected' : '' }}>
                                    <i class="fas fa-sms ml-2"></i> الرسائل النصية
                                </option>
                                <option value="whatsapp" {{ ($preferences->preferred_contact ?? 'email') === 'whatsapp' ? 'selected' : '' }}>
                                    <i class="fab fa-whatsapp ml-2"></i> واتساب
                                </option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">تكرار التواصل</label>
                            <select name="communication_frequency" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                                <option value="immediately" {{ ($preferences->communication_frequency ?? 'immediately') === 'immediately' ? 'selected' : '' }}>
                                    <i class="fas fa-bolt ml-2"></i> فوراً
                                </option>
                                <option value="daily" {{ ($preferences->communication_frequency ?? 'immediately') === 'daily' ? 'selected' : '' }}>
                                    <i class="fas fa-calendar-day ml-2"></i> ملخص يومي
                                </option>
                                <option value="weekly" {{ ($preferences->communication_frequency ?? 'immediately') === 'weekly' ? 'selected' : '' }}>
                                    <i class="fas fa-calendar-week ml-2"></i> ملخص أسبوعي
                                </option>
                                <option value="monthly" {{ ($preferences->communication_frequency ?? 'immediately') === 'monthly' ? 'selected' : '' }}>
                                    <i class="fas fa-calendar ml-2"></i> تقرير شهري
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex justify-end">
                <button type="submit" class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                    <i class="fas fa-save ml-2"></i>
                    حفظ التفضيلات
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Custom animations */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.bg-white {
    animation: slideIn 0.5s ease-out;
}

/* Enhanced toggle switches */
input[type="checkbox"]:checked + div {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Hover effects */
.form-select:focus,
input:focus {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

/* Smooth transitions */
* {
    transition: all 0.2s ease;
}
</style>

<!-- Additional JavaScript for enhanced functionality -->
<script>
// Auto-save preferences when toggles are changed
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    const selects = document.querySelectorAll('select');
    
    // Add change listeners to all form elements
    [...checkboxes, ...selects].forEach(element => {
        element.addEventListener('change', function() {
            // Show saving indicator
            showSavingIndicator();
            
            // Auto-save after 1 second of no changes
            clearTimeout(window.saveTimeout);
            window.saveTimeout = setTimeout(() => {
                autoSave();
            }, 1000);
        });
    });
});

function showSavingIndicator() {
    const indicator = document.createElement('div');
    indicator.id = 'saving-indicator';
    indicator.className = 'fixed top-4 right-4 bg-blue-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
    indicator.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i>جاري الحفظ...';
    document.body.appendChild(indicator);
}

function hideSavingIndicator() {
    const indicator = document.getElementById('saving-indicator');
    if (indicator) {
        indicator.remove();
    }
}

function autoSave() {
    const form = document.querySelector('form');
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideSavingIndicator();
        if (data.success) {
            showSuccessMessage('تم حفظ التفضيلات تلقائياً');
        }
    })
    .catch(error => {
        hideSavingIndicator();
        console.error('Auto-save error:', error);
    });
}

function showSuccessMessage(message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
    successDiv.innerHTML = '<i class="fas fa-check ml-2"></i>' + message;
    document.body.appendChild(successDiv);
    
    setTimeout(() => {
        successDiv.remove();
    }, 3000);
}

// Reset preferences function
function resetPreferences() {
    if (confirm('هل أنت متأكد من إعادة تعيين جميع التفضيلات إلى القيم الافتراضية؟')) {
        // Reset all checkboxes to default values
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.checked = checkbox.name === 'email_notifications' || checkbox.name === 'push_notifications' || checkbox.name === 'property_alerts' || checkbox.name === 'price_drop_alerts';
        });
        
        // Reset selects to default values
        const preferredContact = document.querySelector('select[name="preferred_contact"]');
        if (preferredContact) preferredContact.value = 'email';
        
        const frequency = document.querySelector('select[name="communication_frequency"]');
        if (frequency) frequency.value = 'immediately';
        
        showSuccessMessage('تم إعادة التعيين إلى القيم الافتراضية');
    }
}
</script>
@endsection
