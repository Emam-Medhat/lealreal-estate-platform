@extends('admin.layouts.admin')

@section('title', 'إضافة عميل محتمل جديد')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%239CA3AF" fill-opacity="0.1"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Progress Steps -->
        <div class="flex justify-center mb-12">
            <div class="flex items-center">
                <div class="flex items-center justify-center w-10 h-10 bg-blue-500 text-white rounded-full font-bold shadow-lg">1</div>
                <div class="w-32 h-1 bg-blue-200 rounded-full"></div>
                <div class="w-32 h-1 bg-blue-300 rounded-full"></div>
                <div class="w-32 h-1 bg-blue-400 rounded-full"></div>
                <div class="w-32 h-1 bg-blue-500 rounded-full"></div>
            </div>
            <div class="flex items-center gap-2 text-sm text-gray-600">
                <span>البيانات الأساسية</span>
                <i class="fas fa-arrow-right text-blue-500"></i>
                <span>المعلومات المهنية</span>
                <i class="fas fa-arrow-right text-blue-500"></i>
                <span>تفاصيل العميل</span>
                <i class="fas fa-arrow-right text-blue-500"></i>
                <span>المعلومات الإضافية</span>
            </div>
        </div>

        <!-- Animated Header -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-3xl shadow-2xl mb-6 transform rotate-3 hover:rotate-6 transition-transform duration-300">
                <i class="fas fa-user-plus text-white text-4xl"></i>
            </div>
            <h1 class="text-5xl font-bold text-transparent bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text mb-4">
                إضافة عميل جديد
            </h1>
            <p class="text-xl text-gray-600 mb-2">أدخل بيانات العميل الجديد</p>
            <div class="flex items-center justify-center gap-2">
                <div class="h-1 w-20 bg-gradient-to-r from-blue-400 to-indigo-400 rounded"></div>
                <div class="h-1 w-20 bg-gradient-to-r from-indigo-400 to-blue-400 rounded"></div>
                <div class="h-1 w-20 bg-gradient-to-r from-blue-500 to-indigo-500 rounded"></div>
            </div>
        </div>

        <!-- Main Form Card with Glass Effect -->
        <div class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 overflow-hidden">
            <form action="{{ route('leads.store') }}" method="POST" class="p-10">
                @csrf
                
                <!-- Basic Information Section -->
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl mr-3">
                            <i class="fas fa-user text-white text-sm"></i>
                        </div>
                        المعلومات الأساسية
                        <span class="text-red-500 ml-2 animate-pulse">*</span>
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="relative group">
                            <label class="block text-lg font-medium text-gray-900 mb-3">
                                الاسم الأول
                            </label>
                            <input type="text" 
                                   name="first_name" 
                                   required 
                                   class="w-full px-4 py-4 text-lg border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-white/50 backdrop-blur-sm group-hover:bg-blue-50"
                                   value="{{ old('first_name') }}"
                                   placeholder="أدخل الاسم الأول">
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                <i class="fas fa-user text-gray-400 text-xl"></i>
                            </div>
                            @error('first_name')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="relative group">
                            <label class="block text-lg font-medium text-gray-900 mb-3">
                                الاسم الأخير
                            </label>
                            <input type="text" 
                                   name="last_name" 
                                   required 
                                   class="w-full px-4 py-4 text-lg border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-white/50 backdrop-blur-sm group-hover:bg-blue-50"
                                   value="{{ old('last_name') }}"
                                   placeholder="أدخل الاسم الأخير">
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                <i class="fas fa-user text-gray-400 text-xl"></i>
                            </div>
                            @error('last_name')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="relative group">
                            <label class="block text-lg font-medium text-gray-900 mb-3">
                                البريد الإلكتروني
                            </label>
                            <input type="email" 
                                   name="email" 
                                   required 
                                   class="w-full px-4 py-4 text-lg border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-white/50 backdrop-blur-sm group-hover:bg-blue-50"
                                   value="{{ old('email') }}"
                                   placeholder="example@email.com">
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                <i class="fas fa-envelope text-gray-400 text-xl"></i>
                            </div>
                            @error('email')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="relative group">
                            <label class="block text-lg font-medium text-gray-900 mb-3">
                                رقم الهاتف
                            </label>
                            <input type="tel" 
                                   name="phone" 
                                   class="w-full px-4 py-4 text-lg border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 bg-white/50 backdrop-blur-sm group-hover:bg-blue-50"
                                   value="{{ old('phone') }}"
                                   placeholder="+96650000000">
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                <i class="fas fa-phone text-gray-400 text-xl"></i>
                            </div>
                            @error('phone')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Professional Information Section -->
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl mr-3">
                            <i class="fas fa-briefcase text-white text-sm"></i>
                        </div>
                        المعلومات المهنية
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="relative group">
                            <label class="block text-lg font-medium text-gray-900 mb-3">
                                الشركة
                            </label>
                            <input type="text" 
                                   name="company" 
                                   class="w-full px-4 py-4 text-lg border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all duration-300 bg-white/50 backdrop-blur-sm group-hover:bg-purple-50"
                                   value="{{ old('company') }}"
                                   placeholder="اسم الشركة">
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                <i class="fas fa-building text-gray-400 text-xl"></i>
                            </div>
                            @error('company')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="relative group">
                            <label class="block text-lg font-medium text-gray-900 mb-3">
                                المنصب
                            </label>
                            <input type="text" 
                                   name="position" 
                                   class="w-full px-4 py-4 text-lg border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-purple-500/20 focus:border-purple-500 transition-all duration-300 bg-white/50 backdrop-blur-sm group-hover:bg-purple-50"
                                   value="{{ old('position') }}"
                                   placeholder="المسمى الوظيفي">
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                <i class="fas fa-id-badge text-gray-400 text-xl"></i>
                            </div>
                            @error('position')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Lead Details Section -->
                <div class="mb-8">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-green-500 to-teal-600 rounded-xl mr-3">
                            <i class="fas fa-chart-line text-white text-sm"></i>
                        </div>
                        تفاصيل العميل المحتمل
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="relative group">
                            <label class="block text-lg font-medium text-gray-900 mb-3">
                                المصدر
                            </label>
                            <select name="source_id" 
                                    class="w-full px-4 py-4 text-lg border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-green-500/20 focus:border-green-500 transition-all duration-300 bg-white/50 backdrop-blur-sm appearance-none group-hover:bg-green-50">
                                <option value="">اختر المصدر</option>
                                @foreach($sources as $source)
                                    <option value="{{ $source->id }}" {{ old('source_id') == $source->id ? 'selected' : '' }}>
                                        {{ $source->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2 opacity-0 group-hover:opacity-100 transition-all duration-300">
                                <i class="fas fa-chevron-down text-gray-400 text-xl"></i>
                            </div>
                            @error('source_id')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="relative group">
                            <label class="block text-lg font-medium text-gray-900 mb-3">
                                الحالة
                                <span class="text-red-500 ml-2 animate-pulse">*</span>
                            </label>
                            <select name="status_id" 
                                    class="w-full px-4 py-4 text-lg border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-green-500/20 focus:border-green-500 transition-all duration-300 bg-white/50 backdrop-blur-sm appearance-none group-hover:bg-green-50">
                                <option value="">اختر الحالة</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" {{ old('status_id') == $status->id ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2 pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-400 text-xl"></i>
                            </div>
                            @error('status_id')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="relative group">
                            <label class="block text-lg font-medium text-gray-900 mb-3">
                                الأولوية
                                <span class="text-red-500 ml-2 animate-pulse">*</span>
                            </label>
                            <select name="priority" 
                                    class="w-full px-4 py-4 text-lg border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-green-500/20 focus:border-green-500 transition-all duration-300 bg-white/50 backdrop-blur-sm appearance-none group-hover:bg-green-50">
                                <option value="1" {{ old('priority') == '1' ? 'selected' : '' }}>منخفضة</option>
                                <option value="2" {{ old('priority') == '2' ? 'selected' : '' }}>متوسطة</option>
                                <option value="3" {{ old('priority') == '3' ? 'selected' : '' }}>عالية</option>
                            </select>
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2 pointer-events-none">
                                <i class="fas fa-flag text-gray-400 text-xl"></i>
                            </div>
                            @error('priority')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                        <div class="relative">
                            <label class="block text-lg font-medium text-gray-900 mb-3">
                                المسؤول عنه
                            </label>
                            <select name="assigned_to" 
                                    class="w-full px-4 py-4 text-lg border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-green-500/20 focus:border-green-500 transition-all duration-300 bg-white/50 backdrop-blur-sm appearance-none">
                                <option value="">غير محدد</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('assigned_to') == $user->id ? 'selected' : '' }}>
                                        {{ $user->full_name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2 pointer-events-none">
                                <i class="fas fa-user-tie text-gray-400 text-xl"></i>
                            </div>
                            @error('assigned_to')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                
                <!-- Additional Information Section -->
                <div class="mb-10">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <div class="flex items-center justify-center w-8 h-8 bg-gradient-to-br from-orange-500 to-red-600 rounded-xl mr-3">
                            <i class="fas fa-info text-white text-sm"></i>
                        </div>
                        معلومات إضافية
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="relative">
                            <label class="block text-lg font-medium text-gray-900 mb-3">
                                القيمة المتوقعة
                            </label>
                            <input type="number" 
                                   name="estimated_value" 
                                   step="0.01"
                                   class="w-full px-4 py-4 text-lg border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-orange-500/20 focus:border-orange-500 transition-all duration-300 bg-white/50 backdrop-blur-sm"
                                   value="{{ old('estimated_value') }}"
                                   placeholder="0.00">
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                                <i class="fas fa-dollar-sign text-gray-400 text-xl"></i>
                            </div>
                            @error('estimated_value')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="relative">
                            <label class="block text-lg font-medium text-gray-900 mb-3">
                                تاريخ الإغلاق المتوقع
                            </label>
                            <input type="date" 
                                   name="expected_close_date" 
                                   class="w-full px-4 py-4 text-lg border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-orange-500/20 focus:border-orange-500 transition-all duration-300 bg-white/50 backdrop-blur-sm"
                                   value="{{ old('expected_close_date') }}">
                            <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                                <i class="fas fa-calendar text-gray-400 text-xl"></i>
                            </div>
                            @error('expected_close_date')
                                <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="relative">
                        <label class="block text-lg font-medium text-gray-900 mb-3">
                            العنوان
                        </label>
                        <textarea name="address" 
                                  rows="3"
                                  class="w-full px-4 py-4 text-lg border-2 border-gray-200 rounded-xl focus:ring-4 focus:ring-orange-500/20 focus:border-orange-500 transition-all duration-300 bg-white/50 backdrop-blur-sm resize-none"
                                  placeholder="أدخل العنوان الكامل">{{ old('address') }}</textarea>
                        <div class="absolute right-4 top-4">
                            <i class="fas fa-map-marker-alt text-gray-400 text-xl"></i>
                        </div>
                        @error('address')
                            <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                @error('tags')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                <a href="{{ route('leads.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200">إلغاء</a>
                <button type="submit" class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white px-8 py-3 rounded-lg font-medium transition-all duration-200 shadow-lg hover:shadow-xl">حفظ العميل</button>
            </div>
        </form>
    </div>
</div>
@endsection
