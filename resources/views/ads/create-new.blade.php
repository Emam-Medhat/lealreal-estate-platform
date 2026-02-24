@extends('layouts.app')

@section('title', 'إنشاء إعلان جديد')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-8">
    <div class="max-w-7xl mx-auto px-6">

        <!-- Header -->
        <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-8 mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-full p-3 mr-4">
                        <i class="fas fa-plus text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-slate-800">إنشاء إعلان جديد</h1>
                        <p class="text-slate-600 mt-1">أنشئ إعلاناً فعالاً للوصول لعملائك المستهدفين</p>
                    </div>
                </div>
                <a href="{{ route('ads.index') }}" 
                   class="bg-white border-2 border-slate-300 text-slate-700 px-6 py-3 rounded-2xl hover:bg-slate-50 transition-all font-medium flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i>
                    العودة للإعلانات
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('ads.store') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <!-- Basic Information -->
            <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-8">
                <div class="flex items-center mb-6">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-full p-2 mr-4">
                        <i class="fas fa-info-circle text-white"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800">المعلومات الأساسية</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Campaign -->
                    <div>
                        <label for="campaign_id" class="block text-sm font-semibold text-slate-700 mb-2">الحملة الإعلانية *</label>
                        <div class="relative">
                            <select class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 appearance-none pl-12"
                                    id="campaign_id" name="campaign_id" required>
                                <option value="">اختر حملة</option>
                                @foreach($campaigns as $campaign)
                                    <option value="{{ $campaign->id }}">{{ $campaign->name }}</option>
                                @endforeach
                            </select>
                            <i class="fas fa-bullhorn absolute right-4 top-4 text-slate-400"></i>
                            <i class="fas fa-chevron-down absolute left-4 top-4 text-slate-400"></i>
                        </div>
                    </div>

                    <!-- Ad Type -->
                    <div>
                        <label for="type" class="block text-sm font-semibold text-slate-700 mb-2">نوع الإعلان *</label>
                        <div class="relative">
                            <select class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 appearance-none pl-12"
                                    id="type" name="type" required onchange="toggleAdTypeFields()">
                                <option value="banner">بانر</option>
                                <option value="native">أصلي</option>
                                <option value="video">فيديو</option>
                                <option value="popup">نافذة منبثقة</option>
                            </select>
                            <i class="fas fa-ad absolute right-4 top-4 text-slate-400"></i>
                            <i class="fas fa-chevron-down absolute left-4 top-4 text-slate-400"></i>
                        </div>
                    </div>

                    <!-- Title -->
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-semibold text-slate-700 mb-2">عنوان الإعلان *</label>
                        <div class="relative">
                            <input type="text" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 pl-12"
                                   id="title" name="title" required value="{{ old('title') }}" maxlength="255"
                                   placeholder="أدخل عنواناً جذاباً للإعلان">
                            <i class="fas fa-heading absolute right-4 top-4 text-slate-400"></i>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">وصف الإعلان *</label>
                        <div class="relative">
                            <textarea class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 resize-none"
                                      id="description" name="description" rows="3" required maxlength="500"
                                      placeholder="صف الإعلان بشكل مفصل...">{{ old('description') }}</textarea>
                            <div class="absolute bottom-3 left-3 text-xs text-slate-400">
                                <span id="char-count">0</span>/500
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Banner Fields -->
            <div id="banner-fields" class="bg-white rounded-3xl shadow-xl border border-slate-200 p-8">
                <div class="flex items-center mb-6">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-full p-2 mr-4">
                        <i class="fas fa-image text-white"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800">إعدادات البانر</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="banner_size" class="block text-sm font-semibold text-slate-700 mb-2">حجم البانر</label>
                        <div class="relative">
                            <select class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 appearance-none pl-12"
                                    id="banner_size" name="banner_size" onchange="toggleCustomSize()">
                                <option value="leaderboard">Leaderboard (728x90)</option>
                                <option value="medium_rectangle">Medium Rectangle (300x250)</option>
                                <option value="large_rectangle">Large Rectangle (336x280)</option>
                                <option value="wide_skyscraper">Wide Skyscraper (160x600)</option>
                                <option value="custom">مخصص</option>
                            </select>
                            <i class="fas fa-expand absolute right-4 top-4 text-slate-400"></i>
                            <i class="fas fa-chevron-down absolute left-4 top-4 text-slate-400"></i>
                        </div>
                    </div>

                    <div>
                        <label for="animation_type" class="block text-sm font-semibold text-slate-700 mb-2">نوع الحركة</label>
                        <div class="relative">
                            <select class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 appearance-none pl-12"
                                    id="animation_type" name="animation_type">
                                <option value="none">بدون حركة</option>
                                <option value="fade">تلاشي</option>
                                <option value="slide">انزلاق</option>
                                <option value="zoom">تكبير</option>
                            </select>
                            <i class="fas fa-magic absolute right-4 top-4 text-slate-400"></i>
                            <i class="fas fa-chevron-down absolute left-4 top-4 text-slate-400"></i>
                        </div>
                    </div>

                    <div id="custom-width-group" class="hidden">
                        <label for="custom_width" class="block text-sm font-semibold text-slate-700 mb-2">العرض (بكسل)</label>
                        <input type="number" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                               id="custom_width" name="custom_width" min="100" max="1200" value="{{ old('custom_width') }}">
                    </div>

                    <div id="custom-height-group" class="hidden">
                        <label for="custom_height" class="block text-sm font-semibold text-slate-700 mb-2">الارتفاع (بكسل)</label>
                        <input type="number" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200"
                               id="custom_height" name="custom_height" min="50" max="600" value="{{ old('custom_height') }}">
                    </div>

                    <div class="md:col-span-2">
                        <label for="banner_image" class="block text-sm font-semibold text-slate-700 mb-2">صورة البانر</label>
                        <input type="file" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100"
                               id="banner_image" name="image" accept="image/*" onchange="previewImage(this, 'banner-preview')">
                        <p class="text-sm text-slate-500 mt-1">الصيغ المسموحة: JPG, PNG, GIF (الحد الأقصى: 2MB)</p>
                        <div id="banner-preview" class="mt-4"></div>
                    </div>
                </div>
            </div>

            <!-- Video Fields -->
            <div id="video-fields" class="hidden bg-white rounded-3xl shadow-xl border border-slate-200 p-8">
                <div class="flex items-center mb-6">
                    <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-full p-2 mr-4">
                        <i class="fas fa-video text-white"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800">إعدادات الفيديو</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="video_duration" class="block text-sm font-semibold text-slate-700 mb-2">مدة الفيديو (ثانية)</label>
                        <input type="number" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200"
                               id="video_duration" name="video_duration" min="5" max="300" value="{{ old('video_duration', 30) }}">
                    </div>
                    <div>
                        <label for="skip_after" class="block text-sm font-semibold text-slate-700 mb-2">تخطيط بعد (ثانية)</label>
                        <input type="number" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200"
                               id="skip_after" name="skip_after" min="5" max="60" value="{{ old('skip_after', 15) }}">
                    </div>
                    <div>
                        <label for="video_file" class="block text-sm font-semibold text-slate-700 mb-2">ملف الفيديو</label>
                        <input type="file" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100"
                               id="video_file" name="video_file" accept="video/*">
                        <p class="text-sm text-slate-500 mt-1">الصيغ المسموحة: MP4, AVI, MOV, WMV (الحد الأقصى: 50MB)</p>
                    </div>
                    <div>
                        <label for="video_thumbnail" class="block text-sm font-semibold text-slate-700 mb-2">صورة مصغرة</label>
                        <input type="file" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100"
                               id="video_thumbnail" name="thumbnail" accept="image/*" onchange="previewImage(this, 'thumbnail-preview')">
                        <div id="thumbnail-preview" class="mt-4"></div>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                    <label class="flex items-center p-4 bg-slate-50 rounded-xl cursor-pointer hover:bg-slate-100 transition-colors">
                        <input type="checkbox" name="autoplay" value="1" class="w-5 h-5 text-red-600 rounded focus:ring-red-500">
                        <span class="mr-3 text-slate-700">تشغيل تلقائي</span>
                    </label>
                    <label class="flex items-center p-4 bg-slate-50 rounded-xl cursor-pointer hover:bg-slate-100 transition-colors">
                        <input type="checkbox" name="muted" value="1" checked class="w-5 h-5 text-red-600 rounded focus:ring-red-500">
                        <span class="mr-3 text-slate-700">صامت</span>
                    </label>
                    <label class="flex items-center p-4 bg-slate-50 rounded-xl cursor-pointer hover:bg-slate-100 transition-colors">
                        <input type="checkbox" name="controls" value="1" checked class="w-5 h-5 text-red-600 rounded focus:ring-red-500">
                        <span class="mr-3 text-slate-700">أزرار التحكم</span>
                    </label>
                    <label class="flex items-center p-4 bg-slate-50 rounded-xl cursor-pointer hover:bg-slate-100 transition-colors">
                        <input type="checkbox" name="loop" value="1" class="w-5 h-5 text-red-600 rounded focus:ring-red-500">
                        <span class="mr-3 text-slate-700">تكرار</span>
                    </label>
                </div>
            </div>

            <!-- Native Fields -->
            <div id="native-fields" class="hidden bg-white rounded-3xl shadow-xl border border-slate-200 p-8">
                <div class="flex items-center mb-6">
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-full p-2 mr-4">
                        <i class="fas fa-align-left text-white"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800">إعدادات الإعلان الأصلي</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="native_image" class="block text-sm font-semibold text-slate-700 mb-2">صورة الإعلان</label>
                        <input type="file" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100"
                               id="native_image" name="image" accept="image/*" onchange="previewImage(this, 'native-preview')">
                        <div id="native-preview" class="mt-4"></div>
                    </div>
                    <div>
                        <label for="call_to_action" class="block text-sm font-semibold text-slate-700 mb-2">دعوة للعمل</label>
                        <div class="relative">
                            <input type="text" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-200 pl-12"
                                   id="call_to_action" name="call_to_action" value="{{ old('call_to_action') }}" maxlength="100"
                                   placeholder="مثال: اضغط هنا، اعرف أكثر">
                            <i class="fas fa-hand-point-up absolute right-4 top-4 text-slate-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Popup Fields -->
            <div id="popup-fields" class="hidden bg-white rounded-3xl shadow-xl border border-slate-200 p-8">
                <div class="flex items-center mb-6">
                    <div class="bg-gradient-to-r from-amber-500 to-amber-600 rounded-full p-2 mr-4">
                        <i class="fas fa-window-restore text-white"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800">إعدادات النافذة المنبثقة</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="popup_image" class="block text-sm font-semibold text-slate-700 mb-2">صورة النافذة</label>
                        <input type="file" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100"
                               id="popup_image" name="image" accept="image/*" onchange="previewImage(this, 'popup-preview')">
                        <div id="popup-preview" class="mt-4"></div>
                    </div>
                    <div>
                        <label for="popup_size" class="block text-sm font-semibold text-slate-700 mb-2">حجم النافذة</label>
                        <div class="relative">
                            <select class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition-all duration-200 appearance-none pl-12"
                                    id="popup_size" name="popup_size">
                                <option value="small">صغير (400x300)</option>
                                <option value="medium">متوسط (600x400)</option>
                                <option value="large">كبير (800x600)</option>
                            </select>
                            <i class="fas fa-expand absolute right-4 top-4 text-slate-400"></i>
                            <i class="fas fa-chevron-down absolute left-4 top-4 text-slate-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule & Budget -->
            <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-8">
                <div class="flex items-center mb-6">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-full p-2 mr-4">
                        <i class="fas fa-calendar-alt text-white"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800">الجدولة والميزانية</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label for="start_date" class="block text-sm font-semibold text-slate-700 mb-2">تاريخ البدء *</label>
                        <div class="relative">
                            <input type="date" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 pl-12"
                                   id="start_date" name="start_date" required value="{{ old('start_date', now()->format('Y-m-d')) }}">
                            <i class="fas fa-calendar-plus absolute right-4 top-4 text-slate-400"></i>
                        </div>
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-semibold text-slate-700 mb-2">تاريخ الانتهاء *</label>
                        <div class="relative">
                            <input type="date" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 pl-12"
                                   id="end_date" name="end_date" required value="{{ old('end_date', now()->addDays(30)->format('Y-m-d')) }}">
                            <i class="fas fa-calendar-times absolute right-4 top-4 text-slate-400"></i>
                        </div>
                    </div>
                    <div>
                        <label for="daily_budget" class="block text-sm font-semibold text-slate-700 mb-2">الميزانية اليومية *</label>
                        <div class="relative">
                            <input type="number" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 pl-12 pr-16"
                                   id="daily_budget" name="daily_budget" step="0.01" min="1" required value="{{ old('daily_budget', 10) }}">
                            <span class="absolute right-4 top-3.5 text-slate-500 font-medium">ريال</span>
                            <i class="fas fa-coins absolute left-4 top-4 text-slate-400"></i>
                        </div>
                    </div>
                    <div>
                        <label for="total_budget" class="block text-sm font-semibold text-slate-700 mb-2">الميزانية الإجمالية</label>
                        <div class="relative">
                            <input type="number" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-all duration-200 pl-12 pr-16 bg-slate-50"
                                   id="total_budget" name="total_budget" step="0.01" min="1" value="{{ old('total_budget') }}" placeholder="اختياري">
                            <span class="absolute right-4 top-3.5 text-slate-500 font-medium">ريال</span>
                            <i class="fas fa-calculator absolute left-4 top-4 text-slate-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Targeting -->
            <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-8">
                <div class="flex items-center mb-6">
                    <div class="bg-gradient-to-r from-pink-500 to-pink-600 rounded-full p-2 mr-4">
                        <i class="fas fa-bullseye text-white"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-800">الاستهداف والمواضع</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="placements" class="block text-sm font-semibold text-slate-700 mb-2">مواضع الإعلان *</label>
                        <div class="relative">
                            <select class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200 pl-12"
                                    id="placements" name="placements[]" multiple required>
                                @foreach($placements as $placement)
                                    <option value="{{ $placement->id }}">{{ $placement->name }} ({{ $placement->dimensions }})</option>
                                @endforeach
                            </select>
                            <i class="fas fa-map-marker-alt absolute right-4 top-4 text-slate-400"></i>
                        </div>
                        <p class="text-sm text-slate-500 mt-1">اختر جميع المواضع المناسبة</p>
                    </div>
                    <div>
                        <label for="target_url" class="block text-sm font-semibold text-slate-700 mb-2">الرابط المستهدف *</label>
                        <div class="relative">
                            <input type="url" class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 transition-all duration-200 pl-12"
                                   id="target_url" name="target_url" required value="{{ old('target_url') }}" placeholder="https://example.com">
                            <i class="fas fa-link absolute right-4 top-4 text-slate-400"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Section -->
            <div class="bg-white rounded-3xl shadow-xl border border-slate-200 p-8">
                <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
                    <div class="text-center sm:text-right">
                        <h3 class="text-lg font-semibold text-slate-800 mb-1">جاهز لإنشاء الإعلان؟</h3>
                        <p class="text-slate-600 text-sm">سيتم مراجعة الإعلان قبل النشر</p>
                    </div>

                    <div class="flex gap-4">
                        <a href="{{ route('ads.index') }}"
                           class="px-8 py-3 border-2 border-slate-300 text-slate-700 rounded-2xl hover:bg-slate-50 transition-colors font-medium">
                            إلغاء
                        </a>
                        <button type="submit" name="save_draft" value="1"
                                class="px-8 py-3 border-2 border-blue-300 text-blue-700 rounded-2xl hover:bg-blue-50 transition-colors font-medium">
                            <i class="fas fa-save mr-2"></i>
                            حفظ كمسودة
                        </button>
                        <button type="submit"
                                class="px-8 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-2xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 font-medium">
                            <i class="fas fa-check mr-2"></i>
                            إنشاء الإعلان
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
::-webkit-scrollbar { width: 6px; }
::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 3px; }
::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<script>
function toggleAdTypeFields() {
    const type = document.getElementById('type').value;
    const fields = ['banner-fields', 'video-fields', 'native-fields', 'popup-fields'];
    
    fields.forEach(field => {
        const el = document.getElementById(field);
        el.classList.add('hidden');
    });
    
    if (type === 'banner') {
        document.getElementById('banner-fields').classList.remove('hidden');
    } else if (type === 'video') {
        document.getElementById('video-fields').classList.remove('hidden');
    } else if (type === 'native') {
        document.getElementById('native-fields').classList.remove('hidden');
    } else if (type === 'popup') {
        document.getElementById('popup-fields').classList.remove('hidden');
    }
}

function toggleCustomSize() {
    const size = document.getElementById('banner_size').value;
    const widthGroup = document.getElementById('custom-width-group');
    const heightGroup = document.getElementById('custom-height-group');
    
    if (size === 'custom') {
        widthGroup.classList.remove('hidden');
        heightGroup.classList.remove('hidden');
    } else {
        widthGroup.classList.add('hidden');
        heightGroup.classList.add('hidden');
    }
}

function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.innerHTML = `
                <div class="relative inline-block">
                    <img src="${e.target.result}" class="rounded-2xl shadow-lg max-w-xs max-h-48 object-cover">
                    <button type="button" onclick="clearImage('${previewId}', this.parentElement.parentElement)" 
                            class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-red-600 transition-colors shadow-lg">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            `;
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

function clearImage(previewId, element) {
    if (element) {
        element.innerHTML = '';
    }
    const input = document.querySelector(`[onchange*="${previewId}"]`);
    if (input) input.value = '';
}

// Character count
document.addEventListener('DOMContentLoaded', function() {
    const description = document.getElementById('description');
    const charCount = document.getElementById('char-count');
    
    if (description && charCount) {
        description.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
        charCount.textContent = description.value.length;
    }
});
</script>
@endsection
