@extends('admin.layouts.admin')

@section('title', 'إنشاء تقرير مبيعات جديد')
@section('page-title', 'إنشاء تقرير مبيعات جديد')

@section('content')
    <!-- Page Header -->
    <div class="mb-8">
        <div
            class="bg-gradient-to-br from-blue-600 via-indigo-600 to-purple-600 rounded-3xl shadow-2xl p-8 text-white relative overflow-hidden">
            <!-- Animated Background Pattern -->
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full -mr-48 -mt-48 animate-pulse"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-white rounded-full -ml-32 -mb-32 animate-pulse"
                    style="animation-delay: 1s;"></div>
                <div class="absolute top-1/2 left-1/2 w-48 h-48 bg-yellow-300 rounded-full -ml-24 -mt-24 animate-pulse"
                    style="animation-delay: 2s;"></div>
            </div>

            <!-- Grid Pattern Overlay -->
            <div class="absolute inset-0 opacity-10">
                <div class="h-full w-full"
                    style="background-image: linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 50px 50px;">
                </div>
            </div>

            <div class="relative z-10">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="flex items-center mb-4">
                            <div
                                class="bg-white bg-opacity-25 rounded-2xl p-4 ml-4 backdrop-blur-sm border border-white border-opacity-40 shadow-xl transform hover:scale-105 transition-transform duration-300">
                                <i class="fas fa-file-alt text-white text-3xl"></i>
                            </div>
                            <div>
                                <h1
                                    class="text-5xl font-black text-white mb-2 bg-clip-text text-transparent bg-gradient-to-r from-white to-yellow-200">
                                    إنشاء تقرير مبيعات جديد</h1>
                                <p class="text-white text-xl font-medium">اختر نوع التقرير وحدد المعلمات المطلوبة</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('reports.sales.index') }}"
                            class="bg-white bg-opacity-25 backdrop-blur-sm border border-white border-opacity-40 text-white px-6 py-3 rounded-xl font-medium hover:bg-white hover:bg-opacity-30 transition-all duration-300 shadow-lg">
                            <i class="fas fa-arrow-left ml-2"></i>
                            العودة
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Content -->
    <form method="POST" action="{{ route('reports.sales.store') }}" id="reportForm" class="space-y-8">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Basic Information Section -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mb-8">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-500 px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-info-circle ml-3"></i>
                            المعلومات الأساسية
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                                عنوان التقرير <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-500 focus:border-transparent text-lg transition-all duration-300 @error('title') border-red-500 @enderror"
                                id="title" name="title" value="{{ old('title') }}" placeholder="أدخل عنوان التقرير..."
                                required>
                            @error('title')
                                <div class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle ml-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                الوصف
                            </label>
                            <textarea
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-500 focus:border-transparent text-lg transition-all duration-300 resize-none @error('description') border-red-500 @enderror"
                                id="description" name="description" rows="4"
                                placeholder="أدخل وصف التقرير...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle ml-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div>
                            <label for="template_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                قالب التقرير <span class="text-red-500">*</span>
                            </label>
                            <select
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-500 focus:border-transparent text-lg transition-all duration-300 @error('template_id') border-red-500 @enderror"
                                id="template_id" name="template_id" required>
                                <option value="">اختر قالب...</option>
                                @foreach($templates ?? [] as $template)
                                    <option value="{{ $template->id }}" {{ old('template_id') == $template->id ? 'selected' : '' }}>
                                        {{ $template->name }} - {{ ucfirst($template->type) }}
                                    </option>
                                @endforeach
                            </select>
                            @error('template_id')
                                <div class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle ml-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div>
                            <label for="format" class="block text-sm font-semibold text-gray-700 mb-2">
                                التنسيق <span class="text-red-500">*</span>
                            </label>
                            <select
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-4 focus:ring-blue-500 focus:border-transparent text-lg transition-all duration-300 @error('format') border-red-500 @enderror"
                                id="format" name="format" required>
                                <option value="pdf" {{ old('format') == 'pdf' ? 'selected' : '' }}>PDF</option>
                                <option value="excel" {{ old('format') == 'excel' ? 'selected' : '' }}>Excel</option>
                                <option value="csv" {{ old('format') == 'csv' ? 'selected' : '' }}>CSV</option>
                                <option value="html" {{ old('format') == 'html' ? 'selected' : '' }}>HTML</option>
                            </select>
                            @error('format')
                                <div class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle ml-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Date Range Section -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-emerald-500 px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-calendar-alt ml-3"></i>
                            نطاق التاريخ
                        </h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div>
                            <label for="date_range_start" class="block text-sm font-semibold text-gray-700 mb-2">
                                من تاريخ
                            </label>
                            <input type="date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-4 focus:ring-green-500 focus:border-transparent text-lg transition-all duration-300 @error('date_range.start') border-red-500 @enderror"
                                id="date_range_start" name="date_range[start]" value="{{ old('date_range.start') }}">
                            @error('date_range.start')
                                <div class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle ml-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div>
                            <label for="date_range_end" class="block text-sm font-semibold text-gray-700 mb-2">
                                إلى تاريخ
                            </label>
                            <input type="date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-4 focus:ring-green-500 focus:border-transparent text-lg transition-all duration-300 @error('date_range.end') border-red-500 @enderror"
                                id="date_range_end" name="date_range[end]" value="{{ old('date_range.end') }}">
                            @error('date_range.end')
                                <div class="mt-2 text-sm text-red-600 flex items-center">
                                    <i class="fas fa-exclamation-circle ml-1"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">اختيار سريع</label>
                            <div class="grid grid-cols-2 gap-2">
                                <button type="button" onclick="setDateRange('today')"
                                    class="px-3 py-2 bg-gray-100 hover:bg-green-100 hover:text-green-700 rounded-lg text-sm font-medium transition-all duration-300">
                                    اليوم
                                </button>
                                <button type="button" onclick="setDateRange('week')"
                                    class="px-3 py-2 bg-gray-100 hover:bg-green-100 hover:text-green-700 rounded-lg text-sm font-medium transition-all duration-300">
                                    هذا الأسبوع
                                </button>
                                <button type="button" onclick="setDateRange('month')"
                                    class="px-3 py-2 bg-gray-100 hover:bg-green-100 hover:text-green-700 rounded-lg text-sm font-medium transition-all duration-300">
                                    هذا الشهر
                                </button>
                                <button type="button" onclick="setDateRange('quarter')"
                                    class="px-3 py-2 bg-gray-100 hover:bg-green-100 hover:text-green-700 rounded-lg text-sm font-medium transition-all duration-300">
                                    هذا الربع
                                </button>
                                <button type="button" onclick="setDateRange('year')"
                                    class="px-3 py-2 bg-gray-100 hover:bg-green-100 hover:text-green-700 rounded-lg text-sm font-medium transition-all duration-300 col-span-2">
                                    هذا العام
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Advanced Options Section -->
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-500 to-pink-500 px-6 py-4">
                        <h3 class="text-xl font-bold text-white flex items-center">
                            <i class="fas fa-sliders-h ml-3"></i>
                            خيارات متقدمة
                        </h3>
                    </div>
                    <div class="p-6 space-y-8">
                        <!-- Filters Section -->
                        <div>
                            <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-filter ml-2 text-purple-500"></i>
                                الفلاتر
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="property_type" class="block text-sm font-semibold text-gray-700 mb-2">
                                        نوع العقار
                                    </label>
                                    <select
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-4 focus:ring-purple-500 focus:border-transparent text-lg transition-all duration-300"
                                        id="property_type" name="filters[property_type]">
                                        <option value="">الكل</option>
                                        <option value="apartment">شقة</option>
                                        <option value="house">منزل</option>
                                        <option value="villa">فيلا</option>
                                        <option value="land">أرض</option>
                                        <option value="commercial">تجاري</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">
                                        الحالة
                                    </label>
                                    <select
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-4 focus:ring-purple-500 focus:border-transparent text-lg transition-all duration-300"
                                        id="status" name="filters[status]">
                                        <option value="">الكل</option>
                                        <option value="pending">قيد الانتظار</option>
                                        <option value="approved">مقبول</option>
                                        <option value="rejected">مرفوض</option>
                                        <option value="completed">مكتمل</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="agent_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                        الوكيل
                                    </label>
                                    <select
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-4 focus:ring-purple-500 focus:border-transparent text-lg transition-all duration-300"
                                        id="agent_id" name="filters[agent_id]">
                                        <option value="">الكل</option>
                                        @foreach($agents ?? [] as $agent)
                                            <option value="{{ $agent->id }}">{{ $agent->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="min_price" class="block text-sm font-semibold text-gray-700 mb-2">
                                        الحد الأدنى للسعر
                                    </label>
                                    <input type="number"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-4 focus:ring-purple-500 focus:border-transparent text-lg transition-all duration-300"
                                        id="min_price" name="filters[min_price]" placeholder="0">
                                </div>
                            </div>
                        </div>

                        <!-- Parameters (Template-specific) -->
                        <div class="mb-4">
                            <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-cog ml-2 text-purple-500"></i>
                                المعلمات المتغيرة
                            </h4>
                            <div id="templateParameters"
                                class="p-4 border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50 transition-all duration-300">
                                <!-- Dynamic parameters will be loaded here based on selected template -->
                                <div class="text-center py-4 text-gray-500 italic">
                                    <i class="fas fa-info-circle ml-2"></i> اختر قالب لعرض المعلمات المتاحة
                                </div>
                            </div>
                        </div>

                        <!-- Data Fields Section -->
                        <div>
                            <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-database ml-2 text-purple-500"></i>
                                حقول البيانات
                            </h4>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                <label
                                    class="flex items-center space-x-3 space-x-reverse p-3 border border-gray-200 rounded-lg hover:bg-purple-50 cursor-pointer transition-colors">
                                    <input type="checkbox" name="fields[]" value="property_address"
                                        class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500" checked>
                                    <span class="text-sm font-medium">عنوان العقار</span>
                                </label>
                                <label
                                    class="flex items-center space-x-3 space-x-reverse p-3 border border-gray-200 rounded-lg hover:bg-purple-50 cursor-pointer transition-colors">
                                    <input type="checkbox" name="fields[]" value="client_name"
                                        class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500" checked>
                                    <span class="text-sm font-medium">اسم العميل</span>
                                </label>
                                <label
                                    class="flex items-center space-x-3 space-x-reverse p-3 border border-gray-200 rounded-lg hover:bg-purple-50 cursor-pointer transition-colors">
                                    <input type="checkbox" name="fields[]" value="sale_price"
                                        class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500" checked>
                                    <span class="text-sm font-medium">سعر البيع</span>
                                </label>
                                <label
                                    class="flex items-center space-x-3 space-x-reverse p-3 border border-gray-200 rounded-lg hover:bg-purple-50 cursor-pointer transition-colors">
                                    <input type="checkbox" name="fields[]" value="commission"
                                        class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500">
                                    <span class="text-sm font-medium">العمولة</span>
                                </label>
                                <label
                                    class="flex items-center space-x-3 space-x-reverse p-3 border border-gray-200 rounded-lg hover:bg-purple-50 cursor-pointer transition-colors">
                                    <input type="checkbox" name="fields[]" value="sale_date"
                                        class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500" checked>
                                    <span class="text-sm font-medium">تاريخ البيع</span>
                                </label>
                                <label
                                    class="flex items-center space-x-3 space-x-reverse p-3 border border-gray-200 rounded-lg hover:bg-purple-50 cursor-pointer transition-colors">
                                    <input type="checkbox" name="fields[]" value="agent_name"
                                        class="w-4 h-4 text-purple-600 rounded focus:ring-purple-500">
                                    <span class="text-sm font-medium">اسم الوكيل</span>
                                </label>
                            </div>
                        </div>

                        <!-- Sorting Section -->
                        <div>
                            <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-sort ml-2 text-purple-500"></i>
                                الترتيب
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="sort_by" class="block text-sm font-semibold text-gray-700 mb-2">
                                        ترتيب حسب
                                    </label>
                                    <select
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-4 focus:ring-purple-500 focus:border-transparent text-lg transition-all duration-300"
                                        id="sort_by" name="sort_by">
                                        <option value="sale_date">تاريخ البيع</option>
                                        <option value="sale_price">سعر البيع</option>
                                        <option value="property_address">عنوان العقار</option>
                                        <option value="client_name">اسم العميل</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="sort_order" class="block text-sm font-semibold text-gray-700 mb-2">
                                        ترتيب
                                    </label>
                                    <select
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-4 focus:ring-purple-500 focus:border-transparent text-lg transition-all duration-300"
                                        id="sort_order" name="sort_order">
                                        <option value="desc">تنازلي</option>
                                        <option value="asc">تصاعدي</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Options -->
                        <div class="space-y-4 pt-4 border-t border-gray-100">
                            <h4 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-plus-circle ml-2 text-purple-500"></i>
                                خيارات إضافية
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <label class="flex items-center space-x-3 space-x-reverse cursor-pointer">
                                    <input type="checkbox" name="parameters[include_charts]" value="1" checked
                                        class="w-5 h-5 text-purple-600 rounded focus:ring-purple-500">
                                    <span class="text-sm font-medium">تضمين الرسوم البيانية</span>
                                </label>
                                <label class="flex items-center space-x-3 space-x-reverse cursor-pointer">
                                    <input type="checkbox" name="parameters[include_details]" value="1" checked
                                        class="w-5 h-5 text-purple-600 rounded focus:ring-purple-500">
                                    <span class="text-sm font-medium">تضمين التفاصيل الكاملة</span>
                                </label>
                                <label class="flex items-center space-x-3 space-x-reverse cursor-pointer">
                                    <input type="checkbox" name="parameters[include_summary]" value="1" checked
                                        class="w-5 h-5 text-purple-600 rounded focus:ring-purple-500">
                                    <span class="text-sm font-medium">تضمين الملخص التنفيذي</span>
                                </label>
                                <label class="flex items-center space-x-3 space-x-reverse cursor-pointer">
                                    <input type="checkbox" name="parameters[include_recommendations]" value="1"
                                        class="w-5 h-5 text-purple-600 rounded focus:ring-purple-500">
                                    <span class="text-sm font-medium">تضمين التوصيات</span>
                                </label>
                            </div>
                        </div>

                        <!-- Preview Section -->
                        <div class="pt-6 border-t border-gray-100 text-center">
                            <div class="bg-gray-50 rounded-2xl p-8 border-2 border-dashed border-gray-200">
                                <i class="fas fa-file-invoice fa-4xl text-gray-300 mb-4 block"></i>
                                <p class="text-gray-600 mb-6">يمكنك مشاهدة صورة تقديرية للتقرير قبل الحفظ النهائي</p>
                                <button type="button" onclick="previewReport()"
                                    class="px-8 py-4 bg-white border-2 border-blue-500 text-blue-600 rounded-2xl font-bold hover:bg-blue-50 transition-all duration-300 shadow-lg flex items-center mx-auto">
                                    <i class="fas fa-eye ml-2 text-xl"></i>
                                    معاينة التقرير التقديرية
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div
            class="flex flex-wrap justify-end items-center gap-4 bg-white p-6 rounded-3xl shadow-xl border border-gray-100 mt-8">
            <button type="button" onclick="scheduleReport()"
                class="px-8 py-4 bg-white border border-blue-200 text-blue-600 rounded-2xl font-bold hover:bg-blue-50 transition-all duration-300 shadow-sm flex items-center">
                <i class="fas fa-calendar-alt ml-2"></i>
                جدولة التقرير
            </button>
            <button type="button" onclick="saveAsDraft()"
                class="px-8 py-4 bg-white border border-gray-200 text-gray-700 rounded-2xl font-bold hover:bg-gray-50 transition-all duration-300 shadow-sm flex items-center">
                <i class="fas fa-save ml-2"></i>
                حفظ كمسودة
            </button>
            <div class="w-px h-10 bg-gray-200 mx-2 hidden md:block"></div>
            <a href="{{ route('reports.sales.index') }}"
                class="px-8 py-4 bg-gray-100 text-gray-600 rounded-2xl font-bold hover:bg-gray-200 transition-all duration-300">
                إلغاء
            </a>
            <button type="submit"
                class="px-12 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-2xl font-black transition-all duration-300 shadow-xl hover:shadow-2xl transform hover:scale-105 flex items-center">
                <i class="fas fa-plus ml-2 text-xl"></i>
                إنشاء التقرير الآن
            </button>
        </div>
    </form>
@endsection

@push('styles')
    <style>
        /* Custom Modal Styles */
        .custom-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.85);
            z-index: 999999;
            overflow-x: hidden;
            overflow-y: auto;
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }

        .custom-modal.show {
            display: flex !important;
            align-items: center;
            justify-content: center;
        }

        @keyframes slideIn {
            from {
                transform: scale(0.8) translateY(-50px);
                opacity: 0;
            }

            to {
                transform: scale(1) translateY(0);
                opacity: 1;
            }
        }

        .custom-modal-dialog {
            position: relative;
            width: 90%;
            max-width: 800px;
            margin: 1.75rem auto;
            animation: slideIn 0.3s ease-out;
        }

        .custom-modal-content {
            background-color: #fff;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .custom-modal-header {
            padding: 1.5rem 2rem;
            background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .custom-modal-body {
            padding: 2rem;
            max-height: 70vh;
            overflow-y: auto;
        }

        .custom-modal-footer {
            padding: 1.5rem 2rem;
            background-color: #f9fafb;
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .spinner-border {
            display: inline-block;
            width: 3rem;
            height: 3rem;
            border: 0.3em solid currentColor;
            border-right-color: transparent;
            border-radius: 50%;
            animation: spinner-border .75s linear infinite;
        }

        @keyframes spinner-border {
            to {
                transform: rotate(360deg);
            }
        }

        .btn-close {
            background: transparent;
            border: 0;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            opacity: 0.8;
        }

        .btn-close:hover {
            opacity: 1;
        }

        /* Transitions */
        .modal-open {
            overflow: hidden;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Notification UI helpers
        function showErrorNotification(message) {
            createToast(message, 'bg-red-600');
        }

        function showSuccessNotification(message) {
            createToast(message, 'bg-green-600');
        }

        function showInfoNotification(message) {
            createToast(message, 'bg-blue-600');
        }

        function createToast(message, bgColor) {
            const toast = document.createElement('div');
            toast.className = `fixed top-5 right-5 z-[1000000] text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center space-x-3 space-x-reverse transition-all duration-500 transform translate-x-full ${bgColor}`;
            toast.innerHTML = `
            <i class="fas fa-bell"></i>
            <span class="font-bold">${message}</span>
            <button onclick="this.parentElement.remove()" class="mr-4 hover:opacity-75"><i class="fas fa-times"></i></button>
        `;
            document.body.appendChild(toast);
            setTimeout(() => toast.style.transform = 'translateX(0)', 100);
            setTimeout(() => {
                toast.style.transform = 'translateX(full)';
                setTimeout(() => toast.remove(), 500);
            }, 5000);
        }

        // Enhanced Fetch Manager
        const FetchManager = {
            async safeFetch(url, options = {}) {
                try {
                    const response = await fetch(url, {
                        ...options,
                        headers: {
                            ...options.headers,
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                        }
                    });

                    const data = await response.json();
                    if (!response.ok) throw new Error(data.message || `HTTP error! status: ${response.status}`);
                    return data;
                } catch (error) {
                    console.error('Fetch error:', error);
                    showErrorNotification('خطأ في الاتصال: ' + error.message);
                    throw error;
                }
            }
        };

        // Template Manager
        const TemplateManager = {
            init() {
                const select = document.getElementById('template_id');
                if (!select) return;

                select.addEventListener('change', async (e) => {
                    const templateId = e.target.value;
                    const container = document.getElementById('templateParameters');

                    if (!templateId) {
                        container.innerHTML = '<div class="text-center py-4 text-gray-500 italic"><i class="fas fa-info-circle ml-2"></i> اختر قالب لعرض المعلمات المتاحة</div>';
                        return;
                    }

                    container.innerHTML = '<div class="flex items-center justify-center py-8"><i class="fas fa-circle-notch fa-spin text-blue-500 text-3xl"></i></div>';

                    try {
                        // Using route if defined, or fallback
                        const url = `/reports/sales/templates/${templateId}/parameters`;
                        const data = await FetchManager.safeFetch(url);

                        if (data.html) {
                            container.innerHTML = data.html;
                            showSuccessNotification('تم تحميل المعلمات المتاحة');
                        } else {
                            container.innerHTML = '<div class="text-center py-4 text-gray-400">لا يوجد معلمات متغيرة لهذا القالب</div>';
                        }
                    } catch (err) {
                        container.innerHTML = '<div class="text-center py-4 text-red-500 font-bold">فشل في تحميل المعلمات. حاول مرة أخرى.</div>';
                    }
                });
            }
        };

        // Date Range Manager
        function setDateRange(range) {
            const startInput = document.getElementById('date_range_start');
            const endInput = document.getElementById('date_range_end');
            if (!startInput || !endInput) return;

            const today = new Date();
            let start, end;

            switch (range) {
                case 'today':
                    start = end = today;
                    break;
                case 'week':
                    start = new Date(today);
                    start.setDate(today.getDate() - today.getDay());
                    end = new Date(start);
                    end.setDate(start.getDate() + 6);
                    break;
                case 'month':
                    start = new Date(today.getFullYear(), today.getMonth(), 1);
                    end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    break;
                case 'quarter':
                    const qStart = Math.floor(today.getMonth() / 3) * 3;
                    start = new Date(today.getFullYear(), qStart, 1);
                    end = new Date(today.getFullYear(), qStart + 3, 0);
                    break;
                case 'year':
                    start = new Date(today.getFullYear(), 0, 1);
                    end = new Date(today.getFullYear(), 11, 31);
                    break;
            }

            startInput.value = start.toISOString().split('T')[0];
            endInput.value = end.toISOString().split('T')[0];
        }

        // Modal System
        function hideModal() {
            const modal = document.getElementById('previewModal');
            if (modal) modal.remove();
            document.body.classList.remove('modal-open');
        }

        function previewReport() {
            const form = document.getElementById('reportForm');
            if (!form || !document.getElementById('template_id').value) {
                showErrorNotification('يرجى تعبئة الحقول الأساسية أولاً');
                return;
            }

            // Modal Background
            const backdrop = document.createElement('div');
            backdrop.id = 'previewModal';
            backdrop.className = 'custom-modal show';
            backdrop.innerHTML = `
            <div class="custom-modal-dialog">
                <div class="custom-modal-content">
                    <div class="custom-modal-header">
                        <h5 class="font-black text-xl flex items-center"><i class="fas fa-eye ml-3"></i> معاينة التقرير التقديرية</h5>
                        <button onclick="hideModal()" class="btn-close">&times;</button>
                    </div>
                    <div class="custom-modal-body bg-gray-50 flex flex-col items-center justify-center min-h-[300px]" id="modalBody">
                        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-blue-600"></div>
                        <p class="mt-4 text-gray-600 font-bold">جاري تحليل البيانات وتحضير المعاينة...</p>
                    </div>
                    <div class="custom-modal-footer bg-white border-t border-gray-100">
                        <button onclick="hideModal()" class="px-6 py-2 bg-gray-100 text-gray-600 rounded-xl">إغلاق</button>
                        <button onclick="document.getElementById('reportForm').submit()" class="px-6 py-2 bg-blue-600 text-white rounded-xl font-bold">تأكيد وإنشاء التقرير</button>
                    </div>
                </div>
            </div>
        `;
            document.body.appendChild(backdrop);
            document.body.classList.add('modal-open');

            // Simulate/Fetch actual preview data
            setTimeout(async () => {
                try {
                    const formData = new FormData(form);
                    const data = await FetchManager.safeFetch('{{ route('reports.sales.preview_data') }}', {
                        method: 'POST',
                        body: formData
                    });

                    if (data.success) {
                        renderPreviewContent(data.data);
                    } else {
                        throw new Error(data.message);
                    }
                } catch (err) {
                    document.getElementById('modalBody').innerHTML = `
                    <div class="text-center p-8">
                        <i class="fas fa-exclamation-triangle text-amber-500 text-5xl mb-4"></i>
                        <h4 class="text-xl font-bold text-gray-800 mb-2">تعذر إنشاء المعاينة حالاً</h4>
                        <p class="text-gray-500">${err.message || 'حدث خطأ في معالجة التقرير'}</p>
                    </div>
                `;
                }
            }, 1000);
        }

        function renderPreviewContent(data) {
            const container = document.getElementById('modalBody');
            container.className = "custom-modal-body p-8";
            container.innerHTML = `
            <div class="grid grid-cols-2 gap-6 mb-8">
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-blue-50 transition-all hover:shadow-md">
                    <p class="text-gray-400 text-sm font-bold uppercase tracking-wider mb-1">عدد السجلات المتوقعة</p>
                    <h2 class="text-4xl font-black text-blue-600">${data.estimated_records || 0}</h2>
                </div>
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-purple-50 transition-all hover:shadow-md">
                    <p class="text-gray-400 text-sm font-bold uppercase tracking-wider mb-1">وقت التنفيذ المقدر</p>
                    <h2 class="text-4xl font-black text-purple-600">${data.estimated_time || '0.5s'}</h2>
                </div>
            </div>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-white rounded-2xl border border-gray-100">
                    <span class="text-gray-600 font-bold">عنوان التقرير:</span>
                    <span class="text-gray-800 font-black">${data.title}</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-white rounded-2xl border border-gray-100">
                    <span class="text-gray-600 font-bold">تنسيق المخرجات:</span>
                    <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-black uppercase">${data.format}</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-white rounded-2xl border border-gray-100">
                    <span class="text-gray-600 font-bold">النطاق الزمني:</span>
                    <span class="text-gray-800">${data.date_range?.start || 'N/A'} - ${data.date_range?.end || 'N/A'}</span>
                </div>
            </div>
        `;
        }

        // Action Handlers
        async function saveAsDraft() {
            const form = document.getElementById('reportForm');
            const formData = new FormData(form);
            formData.append('save_as_draft', '1');

            showInfoNotification('جاري حفظ المسودة...');
            try {
                const data = await FetchManager.safeFetch('/reports/save-draft', {
                    method: 'POST',
                    body: formData
                });
                if (data.success) {
                    showSuccessNotification('تم حفظ المسودة بنجاح');
                    window.location.href = '/reports/drafts';
                }
            } catch (e) { }
        }

        function scheduleReport() {
            showInfoNotification('خاصية الجدولة ستتوفر قريباً في التحديث القادم');
        }

        // Lifecycle Init
        document.addEventListener('DOMContentLoaded', () => {
            TemplateManager.init();

            // Auto-save logic
            setInterval(() => {
                const title = document.getElementById('title').value;
                if (title) {
                    localStorage.setItem('sales_report_autosave', JSON.stringify({
                        title,
                        time: new Date()
                    }));
                }
            }, 60000);
        });
    </script>
@endpush