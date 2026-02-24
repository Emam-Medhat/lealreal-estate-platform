@extends('layouts.dashboard')

@section('title', 'الملف الشخصي')

@section('content')

<div class="bg-gradient-to-br from-blue-50 to-indigo-100 min-vh-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Profile Header -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
            <div class="relative h-32 bg-gradient-to-r from-blue-600 to-indigo-600">
                <div class="absolute inset-0 bg-black opacity-20"></div>
                <div class="relative px-8 py-6">
                    <h1 class="text-3xl font-bold text-white">الملف الشخصي</h1>
                    <p class="text-blue-100">إدارة معلوماتك الشخصية وبيانات الحساب</p>
                </div>
            </div>
            
            <div class="px-8 pb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center space-x-6">
                            <div class="text-center">
                                <p class="text-sm text-gray-500">نوع الحساب</p>
                                <p class="text-lg font-semibold text-blue-600">{{ ucfirst($user->user_type ?? 'Standard') }}</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm text-gray-500">الحالة</p>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    {{ $user->account_status ?? 'نشط' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Sidebar -->
            <div class="lg:col-span-1">
                <!-- Profile Picture Card -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="p-8">
                        <div class="text-center">
                            <div class="relative inline-block">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" 
                                         alt="{{ $user->name }}" 
                                         class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg">
                                @else
                                    <div class="w-32 h-32 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-3xl font-bold shadow-lg">
                                        {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="absolute bottom-0 right-0 bg-green-500 border-4 border-white rounded-full w-8 h-8 flex items-center justify-center">
                                    <i class="fas fa-camera text-white text-sm"></i>
                                </div>
                            </div>
                            
                            <h3 class="mt-6 text-xl font-bold text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</h3>
                            <p class="text-gray-500">{{ $user->email }}</p>
                            
                            <form action="{{ route('dashboard.profile.update') }}" method="POST" enctype="multipart/form-data" class="mt-6">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">تغيير الصورة الشخصية</label>
                                    <input type="file" name="avatar" class="hidden" id="avatar-upload">
                                    <label for="avatar-upload" class="flex items-center justify-center w-full px-4 py-3 border-2 border-dashed border-gray-300 rounded-lg text-sm text-gray-600 hover:border-blue-500 hover:bg-blue-50 cursor-pointer transition-colors">
                                        <i class="fas fa-cloud-upload-alt ml-2"></i>
                                        اختر صورة جديدة
                                    </label>
                                </div>
                                <button type="submit" class="mt-3 w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-4 py-3 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg">
                                    <i class="fas fa-upload ml-2"></i>رفع الصورة
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Stats Card -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden mt-8">
                    <div class="p-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">إحصائيات الحساب</h3>
                        <div class="grid grid-cols-2 gap-6">
                            <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl">
                                <div class="text-3xl font-bold text-blue-600">{{ $user->properties_count ?? 0 }}</div>
                                <div class="text-sm text-blue-600 font-medium">العقارات</div>
                            </div>
                            <div class="text-center p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-xl">
                                <div class="text-3xl font-bold text-green-600">{{ $user->login_count ?? 0 }}</div>
                                <div class="text-sm text-green-600 font-medium">زيارات</div>
                            </div>
                            <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl">
                                <div class="text-lg font-bold text-purple-600">{{ $user->created_at->format('Y-m-d') }}</div>
                                <div class="text-sm text-purple-600 font-medium">عضو منذ</div>
                            </div>
                            <div class="text-center p-4 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl">
                                <div class="text-lg font-bold text-orange-600">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'غير متاح' }}</div>
                                <div class="text-sm text-orange-600 font-medium">آخر زيارة</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="lg:col-span-2">
                <!-- Personal Information Form -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900">
                            <i class="fas fa-user-edit text-blue-600 ml-3"></i>المعلومات الشخصية
                        </h3>
                    </div>
                    <div class="p-8">
                        <form action="{{ route('dashboard.profile.update') }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">الاسم الأول</label>
                                    <input type="text" name="first_name" value="{{ $user->first_name ?? '' }}" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">اسم العائلة</label>
                                    <input type="text" name="last_name" value="{{ $user->last_name ?? '' }}" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" required>
                                </div>
                            </div>
                            
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                                <input type="email" name="email" value="{{ $user->email }}" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg bg-gray-50" readonly>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف</label>
                                    <input type="tel" name="phone" value="{{ $user->phone ?? '' }}" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">WhatsApp</label>
                                    <input type="tel" name="whatsapp" value="{{ $user->whatsapp ?? '' }}" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">الدولة</label>
                                    <select name="country" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                        <option value="">اختر الدولة</option>
                                        <option value="Saudi Arabia" {{ $user->country == 'Saudi Arabia' ? 'selected' : '' }}>المملكة العربية السعودية</option>
                                        <option value="UAE" {{ $user->country == 'UAE' ? 'selected' : '' }}>الإمارات العربية المتحدة</option>
                                        <option value="Qatar" {{ $user->country == 'Qatar' ? 'selected' : '' }}>قطر</option>
                                        <option value="Kuwait" {{ $user->country == 'Kuwait' ? 'selected' : '' }}>الكويت</option>
                                        <option value="Bahrain" {{ $user->country == 'Bahrain' ? 'selected' : '' }}>البحرين</option>
                                        <option value="Oman" {{ $user->country == 'Oman' ? 'selected' : '' }}>عمان</option>
                                        <option value="Egypt" {{ $user->country == 'Egypt' ? 'selected' : '' }}>مصر</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">المدينة</label>
                                    <input type="text" name="city" value="{{ $user->city ?? '' }}" 
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" required>
                                </div>
                            </div>
                            
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">العنوان</label>
                                <textarea name="address" rows="3" 
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">{{ $user->address ?? '' }}</textarea>
                            </div>
                            
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">نبذة شخصية</label>
                                <textarea name="bio" rows="4" 
                                          placeholder="اكتب نبذة قصيرة عن نفسك..."
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">{{ $user->bio ?? '' }}</textarea>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">اللغة المفضلة</label>
                                    <select name="language" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                        <option value="ar" {{ $user->language == 'ar' ? 'selected' : '' }}>العربية</option>
                                        <option value="en" {{ $user->language == 'en' ? 'selected' : '' }}>English</option>
                                        <option value="fr" {{ $user->language == 'fr' ? 'selected' : '' }}>Français</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">العملة المفضلة</label>
                                    <select name="currency" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                        <option value="EGP" {{ $user->currency == 'EGP' ? 'selected' : '' }}>جنيه مصري (EGP)</option>
                                        <option value="SAR" {{ $user->currency == 'SAR' ? 'selected' : '' }}>ريال سعودي (SAR)</option>
                                        <option value="AED" {{ $user->currency == 'AED' ? 'selected' : '' }}>درهم إماراتي (AED)</option>
                                        <option value="USD" {{ $user->currency == 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                                        <option value="EUR" {{ $user->currency == 'EUR' ? 'selected' : '' }}>يورو (EUR)</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                                <div class="flex items-center">
                                    <input type="checkbox" id="public_profile" name="public_profile" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <label for="public_profile" class="ml-2 block text-sm text-gray-900">
                                        جعل الملف الشخصي عام
                                    </label>
                                </div>
                                
                                <div class="flex space-x-3">
                                    <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                        إلغاء
                                    </button>
                                    <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-3 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg">
                                        <i class="fas fa-save ml-2"></i>حفظ التغييرات
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Social Links Form -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden mt-8">
                    <div class="px-8 py-6 border-b border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-900">
                            <i class="fas fa-share-alt text-blue-600 ml-3"></i>روابط التواصل الاجتماعي
                        </h3>
                    </div>
                    <div class="p-8">
                        <form action="{{ route('dashboard.profile.update') }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fab fa-twitter text-blue-400 ml-2"></i>Twitter
                                    </label>
                                    <input type="url" name="social_twitter" 
                                           placeholder="https://twitter.com/username" 
                                           value="{{ $user->social_twitter ?? '' }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fab fa-linkedin text-blue-600 ml-2"></i>LinkedIn
                                    </label>
                                    <input type="url" name="social_linkedin" 
                                           placeholder="https://linkedin.com/in/username" 
                                           value="{{ $user->social_linkedin ?? '' }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fab fa-instagram text-pink-500 ml-2"></i>Instagram
                                    </label>
                                    <input type="url" name="social_instagram" 
                                           placeholder="https://instagram.com/username" 
                                           value="{{ $user->social_instagram ?? '' }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fab fa-facebook text-blue-600 ml-2"></i>Facebook
                                    </label>
                                    <input type="url" name="social_facebook" 
                                           placeholder="https://facebook.com/username" 
                                           value="{{ $user->social_facebook ?? '' }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                </div>
                            </div>
                            
                            <div class="pt-6 border-t border-gray-200">
                                <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-6 py-3 rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg">
                                    <i class="fas fa-save ml-2"></i>حفظ الروابط
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-check text-green-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-right">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                تم الحفظ بنجاح
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button type="button" onclick="this.closest('.fixed').remove()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            حسناً
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@endsection
