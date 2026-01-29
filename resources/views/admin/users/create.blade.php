@extends('layouts.dashboard')

@section('title', 'إنشاء مستخدم جديد')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-lg shadow-lg p-6 mb-6 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <a href="{{ route('admin.users.index') }}" class="text-white hover:text-blue-200 mr-4 transition-colors">
                        <i class="fas fa-arrow-right text-xl"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold mb-2">إنشاء مستخدم جديد</h1>
                        <p class="text-blue-100">إضافة مستخدم جديد إلى النظام</p>
                    </div>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-3">
                    <i class="fas fa-user-plus text-white text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Success and Error Messages -->
        @if(session('success'))
            <div class="bg-green-50 border-r-4 border-green-500 p-4 mb-6 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 ml-3"></i>
                    <p class="text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-r-4 border-red-500 p-4 mb-6 rounded-lg">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 ml-3"></i>
                    <p class="text-red-700">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-50 border-r-4 border-red-500 p-4 mb-6 rounded-lg">
                <div class="flex items-center mb-2">
                    <i class="fas fa-exclamation-triangle text-red-500 ml-3"></i>
                    <p class="text-red-700 font-semibold">يرجى تصحيح الأخطاء التالية:</p>
                </div>
                <ul class="list-disc list-inside text-red-600 mr-6">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Create Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <form action="{{ route('admin.users.store') }}" method="POST" id="userForm">
                        @csrf
                        
                        <!-- Personal Information -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-user text-blue-500 ml-2"></i>
                                المعلومات الشخصية
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">الاسم الأول</label>
                                    <input type="text" name="first_name" id="firstName" value="{{ old('first_name') }}" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="أدخل الاسم الأول" required>
                                    @error('first_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">اسم العائلة</label>
                                    <input type="text" name="last_name" id="lastName" value="{{ old('last_name') }}" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="أدخل اسم العائلة" required>
                                    @error('last_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف</label>
                                    <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="05xxxxxxxx">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الميلاد</label>
                                    <input type="date" name="birth_date" id="birthDate" value="{{ old('birth_date') }}" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    @error('birth_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Account Information -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-cog text-green-500 ml-2"></i>
                                معلومات الحساب
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="user@example.com" required>
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">كلمة المرور</label>
                                        <div class="relative">
                                            <input type="password" name="password" id="password" 
                                                class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                placeholder="••••••••" required>
                                            <button type="button" onclick="togglePassword('password')" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                                <i class="fas fa-eye" id="passwordToggle"></i>
                                            </button>
                                        </div>
                                        @error('password')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">تأكيد كلمة المرور</label>
                                        <div class="relative">
                                            <input type="password" name="password_confirmation" id="passwordConfirmation" 
                                                class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                placeholder="••••••••" required>
                                            <button type="button" onclick="togglePassword('passwordConfirmation')" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                                <i class="fas fa-eye" id="passwordConfirmationToggle"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">نوع المستخدم</label>
                                    <select name="user_type" id="userType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                        <option value="user" {{ old('user_type') == 'user' ? 'selected' : '' }}>مستخدم عادي</option>
                                        <option value="agent" {{ old('user_type') == 'agent' ? 'selected' : '' }}>وكيل عقاري</option>
                                        <option value="admin" {{ old('user_type') == 'admin' ? 'selected' : '' }}>مدير نظام</option>
                                    </select>
                                    @error('user_type')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Address Information -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-map-marker-alt text-red-500 ml-2"></i>
                                معلومات العنوان
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">المدينة</label>
                                    <input type="text" name="city" id="city" value="{{ old('city') }}" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="الرياض">
                                    @error('city')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">المنطقة</label>
                                    <input type="text" name="region" id="region" value="{{ old('region') }}" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="وسط الرياض">
                                    @error('region')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">العنوان الكامل</label>
                                    <textarea name="address" id="address" rows="2" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                        placeholder="أدخل العنوان الكامل">{{ old('address') }}</textarea>
                                    @error('address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-between border-t pt-6">
                            <a href="{{ route('admin.users.index') }}" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors flex items-center">
                                <i class="fas fa-times ml-2"></i>
                                إلغاء
                            </a>
                            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 flex items-center shadow-lg hover:shadow-xl">
                                <i class="fas fa-save ml-2"></i>
                                إنشاء المستخدم
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-eye text-purple-500 ml-2"></i>
                        معاينة البيانات
                    </h3>
                    <div id="previewCard" class="space-y-4">
                        <div class="text-center py-8 text-gray-400">
                            <i class="fas fa-user-circle text-6xl mb-4"></i>
                            <p>املأ النموذج لعرض المعاينة</p>
                        </div>
                    </div>
                </div>

                <!-- Statistics Card -->
                <div class="bg-gradient-to-br from-purple-600 to-indigo-600 rounded-lg shadow-lg p-6 mt-6 text-white">
                    <h3 class="text-lg font-semibold mb-4">إحصائيات سريعة</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-purple-200">إجمالي المستخدمين</span>
                            <span class="font-bold">1,234</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-purple-200">الوكلاء</span>
                            <span class="font-bold">89</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-purple-200">المديرون</span>
                            <span class="font-bold">5</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-purple-200">مضافون هذا الشهر</span>
                            <span class="font-bold">47</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const toggle = document.getElementById(fieldId + 'Toggle');
    
    if (field.type === 'password') {
        field.type = 'text';
        toggle.classList.remove('fa-eye');
        toggle.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        toggle.classList.remove('fa-eye-slash');
        toggle.classList.add('fa-eye');
    }
}

// Live preview update
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('userForm');
    const previewCard = document.getElementById('previewCard');
    
    const updatePreview = function() {
        const firstName = document.getElementById('firstName').value || '---';
        const lastName = document.getElementById('lastName').value || '---';
        const email = document.getElementById('email').value || '---';
        const phone = document.getElementById('phone').value || '---';
        const userType = document.getElementById('userType').value;
        const city = document.getElementById('city').value || '---';
        const region = document.getElementById('region').value || '---';
        
        const userTypeLabels = {
            'user': 'مستخدم عادي',
            'agent': 'وكيل عقاري',
            'admin': 'مدير نظام'
        };
        
        const userTypeColors = {
            'user': 'bg-blue-100 text-blue-800',
            'agent': 'bg-green-100 text-green-800',
            'admin': 'bg-red-100 text-red-800'
        };
        
        previewCard.innerHTML = `
            <div class="text-center mb-4">
                <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full mx-auto mb-3 flex items-center justify-center">
                    <i class="fas fa-user text-white text-2xl"></i>
                </div>
                <h4 class="font-semibold text-gray-800">${firstName} ${lastName}</h4>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-medium ${userTypeColors[userType]} mt-2">
                    ${userTypeLabels[userType]}
                </span>
            </div>
            <div class="space-y-3 border-t pt-4">
                <div class="flex items-center text-sm">
                    <i class="fas fa-envelope text-gray-400 ml-3 w-4"></i>
                    <span class="text-gray-600">${email}</span>
                </div>
                <div class="flex items-center text-sm">
                    <i class="fas fa-phone text-gray-400 ml-3 w-4"></i>
                    <span class="text-gray-600">${phone}</span>
                </div>
                <div class="flex items-center text-sm">
                    <i class="fas fa-map-marker-alt text-gray-400 ml-3 w-4"></i>
                    <span class="text-gray-600">${city}, ${region}</span>
                </div>
            </div>
        `;
    };
    
    // Add event listeners to all form fields
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('input', updatePreview);
        input.addEventListener('change', updatePreview);
    });
    
    // Initial preview
    updatePreview();
});
</script>
@endsection
