@extends('layouts.dashboard')

@section('title', 'إنشاء مستخدم جديد')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-lg shadow-lg p-6 mb-6 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <a href="{{ route('admin.users.index') }}"
                            class="text-white hover:text-blue-200 mr-4 transition-colors">
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
                        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data"
                            id="userForm">
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
                                        <label class="block text-sm font-medium text-gray-700 mb-2">اسم المستخدم</label>
                                        <input type="text" name="username" id="username" value="{{ old('username') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                            placeholder="أدخل اسم المستخدم">
                                        @error('username')
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
                                        <label class="block text-sm font-medium text-gray-700 mb-2">واتساب</label>
                                        <input type="tel" name="whatsapp" id="whatsapp" value="{{ old('whatsapp') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                            placeholder="05xxxxxxxx">
                                        @error('whatsapp')
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
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">الجنسية</label>
                                        <select name="gender" id="gender"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                            <option value="">اختر الجنسية</option>
                                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى
                                            </option>
                                        </select>
                                        @error('gender')
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
                                        <label class="block text-sm font-medium text-gray-700 mb-2">البريد
                                            الإلكتروني</label>
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
                                                <button type="button" onclick="togglePassword('password')"
                                                    class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                                    <i class="fas fa-eye" id="passwordToggle"></i>
                                                </button>
                                            </div>
                                            @error('password')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">تأكيد كلمة
                                                المرور</label>
                                            <div class="relative">
                                                <input type="password" name="password_confirmation"
                                                    id="passwordConfirmation"
                                                    class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                    placeholder="••••••••" required>
                                                <button type="button" onclick="togglePassword('passwordConfirmation')"
                                                    class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-gray-700">
                                                    <i class="fas fa-eye" id="passwordConfirmationToggle"></i>
                                                </button>
                                            </div>
                                            @error('password_confirmation')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">نوع المستخدم</label>
                                            <select name="user_type" id="userType"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                                <option value="user" {{ old('user_type') == 'user' ? 'selected' : '' }}>مستخدم
                                                    عادي</option>
                                                <option value="agent" {{ old('user_type') == 'agent' ? 'selected' : '' }}>وكيل
                                                    عقاري</option>
                                                <option value="admin" {{ old('user_type') == 'admin' ? 'selected' : '' }}>مدير
                                                    نظام</option>
                                            </select>
                                            @error('user_type')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">حالة الحساب</label>
                                            <select name="account_status" id="accountStatus"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                                <option value="active" {{ old('account_status') == 'active' ? 'selected' : '' }}>نشط</option>
                                                <option value="inactive" {{ old('account_status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                                <option value="suspended" {{ old('account_status') == 'suspended' ? 'selected' : '' }}>معلق</option>
                                            </select>
                                            @error('account_status')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">الصورة الشخصية</label>
                                        <input type="file" name="avatar" id="avatar"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                            accept="image/*">
                                        @error('avatar')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Special Roles -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-user-tag text-orange-500 ml-2"></i>
                                    الأدوار والامتيازات
                                </h3>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 bg-gray-50 p-4 rounded-xl">
                                    <label class="flex items-center space-x-2 space-x-reverse cursor-pointer group">
                                        <input type="checkbox" name="is_agent" value="1"
                                            class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span
                                            class="text-sm font-medium text-gray-700 group-hover:text-blue-600 mr-2">وكيل</span>
                                    </label>
                                    <label class="flex items-center space-x-2 space-x-reverse cursor-pointer group">
                                        <input type="checkbox" name="is_company" value="1"
                                            class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span
                                            class="text-sm font-medium text-gray-700 group-hover:text-blue-600 mr-2">شركة</span>
                                    </label>
                                    <label class="flex items-center space-x-2 space-x-reverse cursor-pointer group">
                                        <input type="checkbox" name="is_developer" value="1"
                                            class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span
                                            class="text-sm font-medium text-gray-700 group-hover:text-blue-600 mr-2">مطور</span>
                                    </label>
                                    <label class="flex items-center space-x-2 space-x-reverse cursor-pointer group">
                                        <input type="checkbox" name="is_investor" value="1"
                                            class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span
                                            class="text-sm font-medium text-gray-700 group-hover:text-blue-600 mr-2">مستثمر</span>
                                    </label>
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
                                        <input type="text" name="state" id="state" value="{{ old('state') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                            placeholder="وسط الرياض">
                                        @error('state')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">الدولة</label>
                                        <input type="text" name="country" id="country" value="{{ old('country') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                            placeholder="المملكة العربية السعودية">
                                        @error('country')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">الرمز البريدي</label>
                                        <input type="text" name="postal_code" id="postalCode"
                                            value="{{ old('postal_code') }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                            placeholder="12345">
                                        @error('postal_code')
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

                            <!-- Additional Information -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-info-circle text-purple-500 ml-2"></i>
                                    معلومات إضافية
                                </h3>
                                <div class="space-y-4">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">الرقم
                                                التعريفي</label>
                                            <input type="text" name="id_document_number" id="idDocumentNumber"
                                                value="{{ old('id_document_number') }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                placeholder="أدخل الرقم التعريفي">
                                            @error('id_document_number')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">الوظيفة</label>
                                            <input type="text" name="job_title" id="jobTitle" value="{{ old('job_title') }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                placeholder="أدخل الوظيفة">
                                            @error('job_title')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">نبذة شخصية</label>
                                        <textarea name="bio" id="bio" rows="3"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                            placeholder="أدخل نبذة شخصية">{{ old('bio') }}</textarea>
                                        @error('bio')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">الموقع
                                                الإلكتروني</label>
                                            <input type="url" name="website" id="website" value="{{ old('website') }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                placeholder="https://example.com">
                                            @error('website')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">فيسبوك</label>
                                            <input type="url" name="facebook_url" id="facebookUrl"
                                                value="{{ old('facebook_url') }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                placeholder="https://facebook.com/username">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">تويتر</label>
                                            <input type="text" name="twitter_url" id="twitter_url"
                                                value="{{ old('twitter_url') }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                placeholder="@username">
                                            @error('twitter_url')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">لينكد إن</label>
                                            <input type="url" name="linkedin_url" id="linkedinUrl"
                                                value="{{ old('linkedin_url') }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                placeholder="https://linkedin.com/in/username">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">إنستجرام</label>
                                            <input type="url" name="instagram_url" id="instagramUrl"
                                                value="{{ old('instagram_url') }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                                placeholder="https://instagram.com/username">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Localization -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                    <i class="fas fa-globe-americas text-blue-400 ml-2"></i>
                                    العملة واللغة
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">اللغة المفضلة</label>
                                        <select name="language"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                            <option value="ar">العربية</option>
                                            <option value="en">English</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">العملة المفضلة</label>
                                        <select name="currency"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                            <option value="EGP">جنيه مصري (EGP)</option>
                                            <option value="SAR">ريال سعودي (SAR)</option>
                                            <option value="USD">دولار أمريكي (USD)</option>
                                            <option value="EUR">يورو (EUR)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">المنطقة الزمنية</label>
                                        <select name="timezone"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                            <option value="Africa/Cairo">القاهرة (GMT+2)</option>
                                            <option value="Asia/Riyadh">الرياض (GMT+3)</option>
                                            <option value="UTC">UTC</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-between border-t pt-6">
                                <a href="{{ route('admin.users.index') }}"
                                    class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors flex items-center">
                                    <i class="fas fa-times ml-2"></i>
                                    إلغاء
                                </a>
                                <button type="submit"
                                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-300 flex items-center shadow-lg hover:shadow-xl">
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

                        <!-- Avatar Preview -->
                        <div class="mb-6">
                            <h4 class="text-md font-semibold text-gray-700 mb-3">معاينة الصورة الشخصية</h4>
                            <div class="text-center py-8">
                                <div id="avatarPreview"
                                    class="w-32 h-32 rounded-full bg-gray-100 flex items-center justify-center mx-auto overflow-hidden border-4 border-gray-200">
                                    <i class="fas fa-user text-gray-400 text-4xl"></i>
                                </div>
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
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('userForm');
            const previewCard = document.getElementById('previewCard');

            const updatePreview = function () {
                const firstName = document.getElementById('firstName')?.value || '---';
                const lastName = document.getElementById('lastName')?.value || '---';
                const username = document.getElementById('username')?.value || '---';
                const email = document.getElementById('email')?.value || '---';
                const phone = document.getElementById('phone')?.value || '---';
                const birthDate = document.getElementById('birthDate')?.value || '---';
                const gender = document.getElementById('gender')?.value || '---';
                const userType = document.getElementById('userType')?.value;
                const accountStatus = document.getElementById('accountStatus')?.value || '---';
                const city = document.getElementById('city')?.value || '---';
                const state = document.getElementById('state')?.value || '---';
                const country = document.getElementById('country')?.value || '---';
                const postalCode = document.getElementById('postalCode')?.value || '---';
                const address = document.getElementById('address')?.value || '---';
                const nationalId = document.getElementById('nationalId')?.value || '---';
                const jobTitle = document.getElementById('jobTitle')?.value || '---';
                const bio = document.getElementById('bio')?.value || '---';
                const website = document.getElementById('website')?.value || '---';
                const twitter = document.getElementById('twitter')?.value || '---';

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

                const genderLabels = {
                    'male': 'ذكر',
                    'female': 'أنثى'
                };

                const accountStatusLabels = {
                    'active': 'نشط',
                    'inactive': 'غير نشط',
                    'suspended': 'معلق'
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
                            <div class="flex flex-wrap gap-2 justify-center mt-2">
                                ${gender ? `<span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs">${genderLabels[gender]}</span>` : ''}
                                ${birthDate ? `<span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs">${birthDate}</span>` : ''}
                                ${accountStatus ? `<span class="px-2 py-1 ${accountStatus === 'active' ? 'bg-green-100 text-green-800' : accountStatus === 'inactive' ? 'bg-gray-100 text-gray-800' : 'bg-red-100 text-red-800'} rounded-full text-xs">${accountStatusLabels[accountStatus]}</span>` : ''}
                            </div>
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
                                <i class="fab fa-whatsapp text-green-500 ml-3 w-4"></i>
                                <span class="text-gray-600">${document.getElementById('whatsapp')?.value || '---'}</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-user text-gray-400 ml-3 w-4"></i>
                                <span class="text-gray-600">${username}</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-map-marker-alt text-gray-400 ml-3 w-4"></i>
                                <span class="text-gray-600">${city}, ${state}</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-localization text-gray-400 ml-3 w-4"></i>
                                <span class="text-gray-600">${document.getElementsByName('preferred_language')[0]?.value || 'ar'} | ${document.getElementsByName('preferred_currency')[0]?.value || 'EGP'}</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-globe text-gray-400 ml-3 w-4"></i>
                                <span class="text-gray-600">${country}</span>
                            </div>
                            ${nationalId && nationalId !== '---' ? `
                            <div class="flex items-center text-sm">
                                <i class="fas fa-id-card text-gray-400 ml-3 w-4"></i>
                                <span class="text-gray-600">${nationalId}</span>
                            </div>` : ''}
                            ${jobTitle && jobTitle !== '---' ? `
                            <div class="flex items-center text-sm">
                                <i class="fas fa-briefcase text-gray-400 ml-3 w-4"></i>
                                <span class="text-gray-600">${jobTitle}</span>
                            </div>` : ''}
                            ${website && website !== '---' ? `
                            <div class="flex items-center text-sm">
                                <i class="fas fa-globe text-gray-400 ml-3 w-4"></i>
                                <a href="${website}" target="_blank" class="text-blue-600 hover:text-blue-800">${website}</a>
                            </div>` : ''}
                            ${twitter && twitter !== '---' ? `
                            <div class="flex items-center text-sm">
                                <i class="fab fa-twitter text-gray-400 ml-3 w-4"></i>
                                <a href="https://twitter.com/${twitter}" target="_blank" class="text-blue-600 hover:text-blue-800">@${twitter}</a>
                            </div>` : ''}
                            ${bio && bio !== '---' ? `
                            <div class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg">
                                <i class="fas fa-info-circle text-gray-400 ml-2 mb-2"></i>
                                <span class="font-medium">نبذة:</span>
                                <p class="mt-1">${bio}</p>
                            </div>` : ''}
                        </div>
                    `;
            };

            // Add event listeners to all form fields
            const inputs = form?.querySelectorAll('input, select, textarea');
            if (inputs) {
                inputs.forEach(input => {
                    input.addEventListener('input', updatePreview);
                    input.addEventListener('change', updatePreview);
                });
            }

            // Add avatar preview functionality
            const avatarInput = document.getElementById('avatar');
            if (avatarInput) {
                avatarInput.addEventListener('change', function (e) {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function (e) {
                            const avatarPreview = document.getElementById('avatarPreview');
                            if (avatarPreview) {
                                avatarPreview.innerHTML = `
                                        <div class="w-32 h-32 rounded-full bg-gray-100 flex items-center justify-center mx-auto overflow-hidden border-4 border-gray-200">
                                            <img src="${e.target.result}" class="w-full h-full object-cover" alt="Avatar preview">
                                        </div>
                                    `;
                            }
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Initial preview
            updatePreview();
        });
    </script>
@endsection