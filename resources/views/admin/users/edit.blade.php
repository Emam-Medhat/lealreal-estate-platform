@extends('layouts.dashboard')

@section('title', 'تعديل المستخدم')

@section('content')
    <div class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen p-6">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-xl overflow-hidden mb-8">
                <div class="relative h-32 bg-gradient-to-r from-blue-600 to-indigo-600">
                    <div class="absolute inset-0 bg-black opacity-20"></div>
                    <div class="relative px-8 py-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-3xl font-bold text-white mb-2">تعديل المستخدم</h1>
                                <p class="text-blue-100">تحديث بيانات المستخدم وصلاحياته</p>
                            </div>
                            <a href="{{ route('admin.users.index') }}"
                                class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-6 py-3 rounded-xl transition-all duration-200 flex items-center">
                                <i class="fas fa-arrow-right ml-2"></i>
                                العودة للمستخدمين
                            </a>
                        </div>
                    </div>
                </div>

                <div class="px-8 pb-8">
                    <div class="flex items-center space-x-6">
                        <div class="h-16 w-16 flex-shrink-0">
                            @if($user->avatar_url)
                                <img src="{{ $user->avatar_url }}"
                                    class="h-16 w-16 rounded-full object-cover border-3 border-white shadow-lg"
                                    alt="{{ $user->first_name }} {{ $user->last_name }}">
                            @else
                                <div
                                    class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center shadow-lg border-3 border-white">
                                    <i class="fas fa-user text-gray-400 text-2xl"></i>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</h2>
                            <p class="text-gray-600">{{ $user->email }}</p>
                            <div class="flex items-center space-x-3 mt-2">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold shadow-sm
                                    {{ $user->user_type === 'admin' ? 'bg-gradient-to-r from-purple-500 to-purple-600 text-white' :
        ($user->user_type === 'agent' ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white' : 'bg-gradient-to-r from-green-500 to-green-600 text-white') }}">
                                    {{ ucfirst($user->user_type ?? 'user') }}
                                </span>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold shadow-sm
                                    {{ $user->account_status === 'active' ? 'bg-gradient-to-r from-green-500 to-emerald-600 text-white' : 'bg-gradient-to-r from-red-500 to-pink-600 text-white' }}">
                                    {{ ucfirst($user->account_status ?? 'active') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success and Error Messages -->
            @if(session('success'))
                <div
                    class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 p-4 mb-6 rounded-xl shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 ml-3 text-xl"></i>
                        <p class="text-green-800 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 p-4 mb-6 rounded-xl shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-500 ml-3 text-xl"></i>
                        <p class="text-red-800 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Edit Form -->
            <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Column - Personal Info -->
                    <div class="lg:col-span-2 space-y-8">
                        <!-- Personal Information -->
                        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                            <div class="px-8 py-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                                <h3 class="text-xl font-semibold text-gray-900">
                                    <i class="fas fa-user text-blue-600 ml-3"></i>المعلومات الشخصية
                                </h3>
                            </div>
                            <div class="p-8">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">الاسم الأول</label>
                                        <input type="text" name="first_name"
                                            value="{{ old('first_name', $user->first_name) }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                            required>
                                        @error('first_name')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">اسم العائلة</label>
                                        <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                            required>
                                        @error('last_name')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">البريد الإلكتروني</label>
                                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                                            required>
                                        @error('email')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">رقم الهاتف</label>
                                        <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                        @error('phone')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">WhatsApp</label>
                                        <input type="tel" name="whatsapp" value="{{ old('whatsapp', $user->whatsapp) }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                        @error('whatsapp')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">اسم المستخدم</label>
                                        <input type="text" name="username" value="{{ old('username', $user->username) }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                        @error('username')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">الموقع الإلكتروني</label>
                                        <input type="url" name="website" value="{{ old('website', $user->website) }}"
                                            placeholder="https://example.com"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">فيسبوك</label>
                                        <input type="url" name="facebook_url"
                                            value="{{ old('facebook_url', $user->facebook_url) }}"
                                            placeholder="https://facebook.com/username"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">تويتر</label>
                                        <input type="text" name="twitter_url" value="{{ old('twitter_url', $user->twitter_url) }}"
                                            placeholder="@username"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">لينكد إن</label>
                                        <input type="url" name="linkedin_url"
                                            value="{{ old('linkedin_url', $user->linkedin_url) }}"
                                            placeholder="https://linkedin.com/in/username"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">إنستجرام</label>
                                        <input type="url" name="instagram_url"
                                            value="{{ old('instagram_url', $user->instagram_url) }}"
                                            placeholder="https://instagram.com/username"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <label class="block text-sm font-bold text-gray-700 mb-2">نبذة شخصية</label>
                                    <textarea name="bio" rows="4" placeholder="اكتب نبذة قصيرة عن المستخدم..."
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">{{ old('bio', $user->bio) }}</textarea>
                                    @error('bio')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Location Information -->
                        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                            <div class="px-8 py-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                                <h3 class="text-xl font-semibold text-gray-900">
                                    <i class="fas fa-map-marker-alt text-blue-600 ml-3"></i>معلومات الموقع
                                </h3>
                            </div>
                            <div class="p-8">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">الدولة</label>
                                        <select name="country"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                            <option value="">اختر الدولة</option>
                                            <option value="Saudi Arabia" {{ old('country', $user->country) == 'Saudi Arabia' ? 'selected' : '' }}>المملكة العربية السعودية</option>
                                            <option value="UAE" {{ old('country', $user->country) == 'UAE' ? 'selected' : '' }}>الإمارات العربية المتحدة</option>
                                            <option value="Qatar" {{ old('country', $user->country) == 'Qatar' ? 'selected' : '' }}>قطر</option>
                                            <option value="Kuwait" {{ old('country', $user->country) == 'Kuwait' ? 'selected' : '' }}>الكويت</option>
                                            <option value="Bahrain" {{ old('country', $user->country) == 'Bahrain' ? 'selected' : '' }}>البحرين</option>
                                            <option value="Oman" {{ old('country', $user->country) == 'Oman' ? 'selected' : '' }}>عمان</option>
                                            <option value="Egypt" {{ old('country', $user->country) == 'Egypt' ? 'selected' : '' }}>مصر</option>
                                        </select>
                                        @error('country')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">المدينة</label>
                                        <input type="text" name="city" value="{{ old('city', $user->city) }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                        @error('city')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">الولاية/المنطقة</label>
                                        <input type="text" name="state" value="{{ old('state', $user->state) }}"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                        @error('state')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">اللغة المفضلة</label>
                                        <select name="preferred_language"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                            <option value="ar" {{ old('preferred_language', $user->preferred_language) == 'ar' ? 'selected' : '' }}>العربية</option>
                                            <option value="en" {{ old('preferred_language', $user->preferred_language) == 'en' ? 'selected' : '' }}>English</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">العملة المفضلة</label>
                                        <select name="preferred_currency"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                            <option value="EGP" {{ old('preferred_currency', $user->preferred_currency) == 'EGP' ? 'selected' : '' }}>جنيه مصري (EGP)
                                            </option>
                                            <option value="SAR" {{ old('preferred_currency', $user->preferred_currency) == 'SAR' ? 'selected' : '' }}>ريال سعودي (SAR)
                                            </option>
                                            <option value="USD" {{ old('preferred_currency', $user->preferred_currency) == 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)
                                            </option>
                                            <option value="EUR" {{ old('preferred_currency', $user->preferred_currency) == 'EUR' ? 'selected' : '' }}>يورو (EUR)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">المنطقة الزمنية</label>
                                        <select name="timezone"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                            <option value="Africa/Cairo" {{ old('timezone', $user->timezone) == 'Africa/Cairo' ? 'selected' : '' }}>القاهرة (GMT+2)</option>
                                            <option value="Asia/Riyadh" {{ old('timezone', $user->timezone) == 'Asia/Riyadh' ? 'selected' : '' }}>الرياض (GMT+3)</option>
                                            <option value="UTC" {{ old('timezone', $user->timezone) == 'UTC' ? 'selected' : '' }}>UTC</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Account & Settings -->
                    <div class="space-y-8">
                        <!-- Account Settings -->
                        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                            <div class="px-8 py-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                                <h3 class="text-xl font-semibold text-gray-900">
                                    <i class="fas fa-cog text-blue-600 ml-3"></i>إعدادات الحساب
                                </h3>
                            </div>
                            <div class="p-8 space-y-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">نوع المستخدم</label>
                                    <select name="user_type"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                        <option value="user" {{ old('user_type', $user->user_type) == 'user' ? 'selected' : '' }}>مستخدم عادي</option>
                                        <option value="agent" {{ old('user_type', $user->user_type) == 'agent' ? 'selected' : '' }}>وكيل عقاري</option>
                                        <option value="admin" {{ old('user_type', $user->user_type) == 'admin' ? 'selected' : '' }}>مدير نظام</option>
                                    </select>
                                    @error('user_type')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">حالة الحساب</label>
                                    <select name="account_status"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                        <option value="active" {{ old('account_status', $user->account_status) == 'active' ? 'selected' : '' }}>نشط</option>
                                        <option value="inactive" {{ old('account_status', $user->account_status) == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                        <option value="suspended" {{ old('account_status', $user->account_status) == 'suspended' ? 'selected' : '' }}>موقوف</option>
                                    </select>
                                    @error('account_status')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">حالة KYC</label>
                                    <select name="kyc_status"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                        <option value="pending" {{ old('kyc_status', $user->kyc_status) == 'pending' ? 'selected' : '' }}>في انتظار المراجعة</option>
                                        <option value="verified" {{ old('kyc_status', $user->kyc_status) == 'verified' ? 'selected' : '' }}>تم التحقق</option>
                                        <option value="rejected" {{ old('kyc_status', $user->kyc_status) == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                    </select>
                                    @error('kyc_status')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">كلمة المرور الجديدة</label>
                                    <input type="password" name="password"
                                        placeholder="اتركها فارغة للحفاظ على كلمة المرور الحالية"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                    @error('password')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Profile Picture -->
                        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                            <div class="px-8 py-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                                <h3 class="text-xl font-semibold text-gray-900">
                                    <i class="fas fa-camera text-blue-600 ml-3"></i>الصورة الشخصية
                                </h3>
                            </div>
                            <div class="p-8">
                                <div class="text-center">
                                    <div class="mb-6">
                                        @if($user->avatar_url)
                                            <img src="{{ $user->avatar_url }}"
                                                class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg mx-auto"
                                                alt="{{ $user->first_name }} {{ $user->last_name }}">
                                        @else
                                            <div
                                                class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center shadow-lg mx-auto border-4 border-white">
                                                <i class="fas fa-user text-gray-400 text-5xl"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-gray-700 mb-2">تغيير الصورة
                                            الشخصية</label>
                                        <input type="file" name="avatar" accept="image/*"
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                        @error('avatar')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Special Roles -->
                        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                            <div class="px-8 py-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                                <h3 class="text-xl font-semibold text-gray-900">
                                    <i class="fas fa-user-tag text-blue-600 ml-3"></i>الأدوار الخاصة
                                </h3>
                            </div>
                            <div class="p-8 space-y-4">
                                <div class="flex items-center justify-between">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_agent" {{ old('is_agent', $user->is_agent) ? 'checked' : '' }}
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <span class="mr-3 text-sm font-medium text-gray-700">وكيل عقاري</span>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_company" {{ old('is_company', $user->is_company) ? 'checked' : '' }}
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <span class="mr-3 text-sm font-medium text-gray-700">شركة</span>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_developer" {{ old('is_developer', $user->is_developer) ? 'checked' : '' }}
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <span class="mr-3 text-sm font-medium text-gray-700">مطور عقاري</span>
                                    </label>
                                </div>
                                <div class="flex items-center justify-between">
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_investor" {{ old('is_investor', $user->is_investor) ? 'checked' : '' }}
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <span class="mr-3 text-sm font-medium text-gray-700">مستثمر</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-8 bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="px-8 py-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <a href="{{ route('admin.users.index') }}"
                                    class="px-6 py-3 text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-all duration-200 flex items-center">
                                    <i class="fas fa-times ml-2"></i>
                                    إلغاء
                                </a>
                                <button type="button" onclick="confirmDelete()"
                                    class="px-6 py-3 text-red-600 bg-red-50 rounded-xl hover:bg-red-100 transition-all duration-200 flex items-center">
                                    <i class="fas fa-trash ml-2"></i>
                                    حذف المستخدم
                                </button>
                            </div>
                            <button type="submit"
                                class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-3 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg flex items-center">
                                <i class="fas fa-save ml-2"></i>
                                حفظ التغييرات
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <form id="deleteForm" action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function confirmDelete() {
            if (confirm('هل أنت متأكد من حذف هذا المستخدم؟ لا يمكن التراجع عن هذا الإجراء.')) {
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
@endsection