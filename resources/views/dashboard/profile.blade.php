@extends('layouts.dashboard')

@section('title', 'الملف الشخصي')

@section('content')

<div class="max-w-6xl mx-auto">
    <!-- Profile Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">الملف الشخصي</h1>
                <p class="text-gray-600">إدارة معلوماتك الشخصية وبيانات الحساب</p>
            </div>
            
            <div class="flex items-center space-x-4">
                <div class="text-center">
                    <div class="text-sm text-gray-500">نوع الحساب</div>
                    <div class="font-semibold text-blue-600">{{ ucfirst($user->user_type ?? 'Standard') }}</div>
                </div>
                <div class="text-center">
                    <div class="text-sm text-gray-500">الحالة</div>
                    <span class="inline-block px-3 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                        {{ $user->account_status ?? 'نشط' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Profile Picture -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-center">
                    <img src="{{ $user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . $user->name . '&size=200&background=3b82f6&color=fff' }}" 
                         alt="{{ $user->name }}" 
                         class="w-32 h-32 rounded-full mx-auto mb-4 border-4 border-gray-200">
                    
                    <h3 class="text-lg font-semibold text-gray-900">{{ $user->name }}</h3>
                    <p class="text-gray-500">{{ $user->email }}</p>
                    
                    <div class="mt-4">
                        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                            <i class="fas fa-camera ml-2"></i>
                            تغيير الصورة
                        </button>
                    </div>
                </div>
                
                <!-- Profile Stats -->
                <div class="mt-6 pt-6 border-t">
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $user->properties_count ?? 0 }}</div>
                            <div class="text-sm text-gray-500">العقارات</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{{ $user->login_count ?? 0 }}</div>
                            <div class="text-sm text-gray-500">زيارات</div>
                        </div>
                    </div>
                </div>
                
                <!-- Account Info -->
                <div class="mt-6 pt-6 border-t">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">معلومات الحساب</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">عضو منذ:</span>
                            <span class="text-gray-900">{{ $user->created_at->format('Y-m-d') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">آخر زيارة:</span>
                            <span class="text-gray-900">{{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'غير متاح' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">م verified:</span>
                            <span class="text-gray-900">{{ $user->email_verified_at ? 'نعم' : 'لا' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">معلومات شخصية</h2>
                
                <form class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الاسم الأول</label>
                            <input type="text" 
                                   value="{{ $user->first_name ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">اسم العائلة</label>
                            <input type="text" 
                                   value="{{ $user->last_name ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني</label>
                        <input type="email" 
                               value="{{ $user->email }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف</label>
                            <input type="tel" 
                                   value="{{ $user->phone ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">WhatsApp</label>
                            <input type="tel" 
                                   value="{{ $user->whatsapp ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الدولة</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option {{ $user->country == 'Saudi Arabia' ? 'selected' : '' }}>المملكة العربية السعودية</option>
                            <option {{ $user->country == 'UAE' ? 'selected' : '' }}>الإمارات العربية المتحدة</option>
                            <option {{ $user->country == 'Qatar' ? 'selected' : '' }}>قطر</option>
                            <option {{ $user->country == 'Kuwait' ? 'selected' : '' }}>الكويت</option>
                            <option {{ $user->country == 'Bahrain' ? 'selected' : '' }}>البحرين</option>
                            <option {{ $user->country == 'Oman' ? 'selected' : '' }}>عمان</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">المدينة</label>
                        <input type="text" 
                               value="{{ $user->city ?? '' }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">العنوان</label>
                        <textarea rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $user->address ?? '' }}</textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">نبذة شخصية</label>
                        <textarea rows="4" 
                                  placeholder="اكتب نبذة قصيرة عن نفسك..."
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ $user->bio ?? '' }}</textarea>
                    </div>
                    
                    <div class="flex items-center justify-between pt-6 border-t">
                        <div class="flex items-center">
                            <input type="checkbox" id="public_profile" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="public_profile" class="ml-2 block text-sm text-gray-900">
                                جعل الملف الشخصي عام
                            </label>
                        </div>
                        
                        <div class="space-x-3">
                            <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                إلغاء
                            </button>
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                حفظ التغييرات
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Social Links -->
            <div class="bg-white rounded-lg shadow p-6 mt-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">روابط التواصل الاجتماعي</h2>
                
                <form class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fab fa-twitter ml-2"></i>
                            Twitter
                        </label>
                        <input type="url" 
                               placeholder="https://twitter.com/username" 
                               value="{{ $user->social_twitter ?? '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fab fa-linkedin ml-2"></i>
                            LinkedIn
                        </label>
                        <input type="url" 
                               placeholder="https://linkedin.com/in/username" 
                               value="{{ $user->social_linkedin ?? '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fab fa-instagram ml-2"></i>
                            Instagram
                        </label>
                        <input type="url" 
                               placeholder="https://instagram.com/username" 
                               value="{{ $user->social_instagram ?? '' }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    
                    <div class="pt-4">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            حفظ الروابط
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
