@extends('layouts.dashboard')

@section('title', 'تفاصيل المطور')

@section('page-title', 'تفاصيل المطور')

@push('styles')
<style>
    .developer-card {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    .developer-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    .action-button {
        transition: all 0.2s ease;
    }
    .action-button:hover {
        transform: translateY(-1px);
    }
    .status-badge {
        animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }
    .loading-spinner {
        display: none;
        width: 16px;
        height: 16px;
        border: 2px solid #ffffff;
        border-top: 2px solid transparent;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .notification {
        position: fixed;
        top: 80px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 99999;
        padding: 16px 24px;
        border-radius: 12px;
        color: white;
        font-weight: 500;
        font-size: 14px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        animation: slideDown 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-width: 300px;
        max-width: 500px;
    }
    .notification.success { 
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-color: rgba(16, 185, 129, 0.3);
    }
    .notification.error { 
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border-color: rgba(239, 68, 68, 0.3);
    }
    .notification.warning { 
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        border-color: rgba(245, 158, 11, 0.3);
    }
    .notification::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
        border-radius: 12px;
        pointer-events: none;
    }
    .notification-icon {
        margin-right: 12px;
        font-size: 18px;
        flex-shrink: 0;
    }
    @keyframes slideDown {
        0% { 
            opacity: 0; 
            transform: translate(-50%, -100%) scale(0.8);
        }
        50% {
            opacity: 1;
            transform: translate(-50%, 5px) scale(1.02);
        }
        100% { 
            opacity: 1; 
            transform: translate(-50%, 0) scale(1);
        }
    }
    @keyframes slideUp {
        0% { 
            opacity: 1; 
            transform: translate(-50%, 0) scale(1);
        }
        100% { 
            opacity: 0; 
            transform: translate(-50%, -100%) scale(0.8);
        }
    }
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9998;
    }
    .modal-content {
        background: white;
        border-radius: 12px;
        padding: 24px;
        max-width: 400px;
        width: 90%;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@section('content')
@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
        <strong class="font-bold">عفوًا!</strong>
        <span class="block sm:inline">حدثت بعض الأخطاء في إدخالك.</span>
        <ul class="mt-3 list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="space-y-6 p-4 sm:p-6 lg:p-8">
    <!-- Header Section -->
    <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between">
        <div class="flex items-center mb-4 sm:mb-0">
            <a href="{{ route('developer.index') }}" class="text-gray-500 hover:text-gray-700 ml-4 transition-colors duration-200">
                <i class="fas fa-arrow-right text-xl"></i>
            </a>
            @if($developer->logo_url)
                <img src="{{ $developer->logo_url }}" alt="{{ $developer->company_name }} Logo" class="w-16 h-16 rounded-full object-cover shadow-sm ml-4">
            @else
                <div class="w-16 h-16 rounded-full bg-blue-100 flex items-center justify-center ml-4 text-blue-600 text-2xl font-bold">
                    {{ mb_substr($developer->company_name, 0, 1) }}
                </div>
            @endif
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900">{{ $developer->company_name }}</h1>
                <p class="text-sm text-gray-600 mt-1">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        @if($developer->status == 'active') bg-green-100 text-green-800
                        @elseif($developer->status == 'pending') bg-yellow-100 text-yellow-800
                        @elseif($developer->status == 'suspended') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ $developer->getStatusLabelAttribute() }}
                    </span>
                </p>
            </div>
        </div>
        <div class="flex items-center space-x-3">
            <a href="{{ route('developer.edit', $developer->id) }}" class="inline-flex items-center px-5 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                <i class="fas fa-edit ml-2"></i>
                تعديل المطور
            </a>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Basic Info, Contact Info, Description -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Company Details Card -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-5 border-b pb-3">
                    <i class="fas fa-building text-blue-500 ml-2"></i> معلومات الشركة
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-5 gap-x-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">اسم الشركة</label>
                        <p class="mt-1 text-base text-gray-900">{{ $developer->company_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">الاسم العربي</label>
                        <p class="mt-1 text-base text-gray-900">{{ $developer->company_name_ar ?? 'غير متوفر' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">رقم الترخيص</label>
                        <p class="mt-1 text-base text-gray-900">{{ $developer->license_number }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">السجل التجاري</label>
                        <p class="mt-1 text-base text-gray-900">{{ $developer->commercial_register }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">نوع المطور</label>
                        <p class="mt-1 text-base text-gray-900">{{ $developer->getTypeLabelAttribute() }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">سنة التأسيس</label>
                        <p class="mt-1 text-base text-gray-900">{{ $developer->established_year ?? 'غير محدد' }}</p>
                    </div>
                </div>
            </div>

            <!-- Contact Info Card -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-5 border-b pb-3">
                    <i class="fas fa-address-book text-green-500 ml-2"></i> معلومات الاتصال
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-5 gap-x-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600">البريد الإلكتروني</label>
                        <p class="mt-1 text-base text-gray-900">
                            @if($developer->email)
                                <a href="mailto:{{ $developer->email }}" class="text-blue-600 hover:text-blue-800">{{ $developer->email }}</a>
                            @else
                                غير متوفر
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">الهاتف</label>
                        <p class="mt-1 text-base text-gray-900">
                            @if($developer->phone)
                                <a href="tel:{{ $developer->phone }}" class="text-blue-600 hover:text-blue-800">{{ $developer->phone }}</a>
                            @else
                                غير متوفر
                            @endif
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600">الموقع الإلكتروني</label>
                        <p class="mt-1 text-base text-gray-900">
                            @if($developer->website)
                                <a href="{{ $developer->getWebsiteUrlAttribute() }}" target="_blank" class="text-blue-600 hover:text-blue-800">
                                    {{ $developer->website }} <i class="fas fa-external-link-alt text-xs mr-1"></i>
                                </a>
                            @else
                                غير متوفر
                            @endif
                        </p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-600">العنوان</label>
                        <div class="mt-1 text-base text-gray-900">
                            @php
                                $addressData = null;
                                if($developer->address) {
                                    if(is_string($developer->address)) {
                                        $addressData = json_decode($developer->address, true);
                                    } elseif(is_array($developer->address)) {
                                        $addressData = $developer->address;
                                    }
                                }
                            @endphp
                            @if($addressData && is_array($addressData))
                                <div class="space-y-1">
                                    @if(!empty($addressData['street']))
                                        <div class="flex items-center">
                                            <i class="fas fa-map-marker-alt text-gray-400 ml-2 text-sm"></i>
                                            <span>{{ $addressData['street'] }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($addressData['city']) || !empty($addressData['state']))
                                        <div class="flex items-center">
                                            <i class="fas fa-city text-gray-400 ml-2 text-sm"></i>
                                            <span>{{ implode(', ', array_filter([
                                                $addressData['city'] ?? '',
                                                $addressData['state'] ?? ''
                                            ])) }}</span>
                                        </div>
                                    @endif
                                    @if(!empty($addressData['country']) || !empty($addressData['postal_code']))
                                        <div class="flex items-center">
                                            <i class="fas fa-globe text-gray-400 ml-2 text-sm"></i>
                                            <span>{{ implode(', ', array_filter([
                                                $addressData['country'] ?? '',
                                                $addressData['postal_code'] ?? ''
                                            ])) }}</span>
                                        </div>
                                    @endif
                                </div>
                            @elseif($developer->address)
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt text-gray-400 ml-2 text-sm"></i>
                                    <span>{{ $developer->address }}</span>
                                </div>
                            @else
                                <span class="text-gray-500">غير متوفر</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description Card -->
            @if($developer->description)
            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-5 border-b pb-3">
                    <i class="fas fa-info-circle text-purple-500 ml-2"></i> الوصف
                </h2>
                <p class="text-gray-700 leading-relaxed">{{ $developer->description }}</p>
            </div>
            @endif
        </div>

        <!-- Right Column: Stats, Status, Actions -->
        <div class="space-y-6">
            <!-- Stats Card -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-5 border-b pb-3">
                    <i class="fas fa-chart-bar text-orange-500 ml-2"></i> الإحصائيات
                </h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 flex items-center"><i class="fas fa-project-diagram ml-2 text-gray-400"></i> عدد المشاريع</span>
                        <span class="text-base font-semibold text-gray-900">{{ $developer->total_projects }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 flex items-center"><i class="fas fa-money-bill-wave ml-2 text-gray-400"></i> إجمالي الاستثمار</span>
                        <span class="text-base font-semibold text-gray-900">{{ number_format($developer->total_investment, 2) }} SAR</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 flex items-center"><i class="fas fa-star ml-2 text-gray-400"></i> التقييم</span>
                        <div class="flex items-center">
                            <span class="text-base font-semibold text-gray-900 ml-2">{{ number_format($developer->rating, 1) }}</span>
                            <div class="flex">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star text-sm {{ $i <= $developer->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                @endfor
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 flex items-center"><i class="fas fa-comments ml-2 text-gray-400"></i> عدد التقييمات</span>
                        <span class="text-base font-semibold text-gray-900">{{ $developer->review_count }}</span>
                    </div>
                </div>
            </div>

            <!-- Status Card -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-5 border-b pb-3">
                    <i class="fas fa-info-circle text-cyan-500 ml-2"></i> حالة المطور
                </h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 flex items-center"><i class="fas fa-toggle-on ml-2 text-gray-400"></i> الحالة</span>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                            @if($developer->status == 'active') bg-green-100 text-green-800
                            @elseif($developer->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($developer->status == 'suspended') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $developer->getStatusLabelAttribute() }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 flex items-center"><i class="fas fa-shield-alt ml-2 text-gray-400"></i> موثق</span>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                            @if($developer->is_verified) bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800 @endif">
                            @if($developer->is_verified) <i class="fas fa-check-circle ml-1"></i> موثق @else <i class="fas fa-times-circle ml-1"></i> غير موثق @endif
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 flex items-center"><i class="fas fa-star ml-2 text-gray-400"></i> مميز</span>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                            @if($developer->is_featured) bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800 @endif">
                            @if($developer->is_featured) <i class="fas fa-star ml-1"></i> مميز @else <i class="far fa-star ml-1"></i> عادي @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-5 border-b pb-3">
                    <i class="fas fa-cogs text-red-500 ml-2"></i> الإجراءات
                </h2>
                <div class="space-y-3">
                    @if($developer->is_verified)
                        <button onclick="toggleVerification({{ $developer->id }})" class="action-button w-full inline-flex items-center justify-center px-5 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors duration-200">
                            <i class="fas fa-times-circle ml-2"></i>
                            <span class="button-text">إلغاء التحقق</span>
                            <div class="loading-spinner"></div>
                        </button>
                    @else
                        <button onclick="toggleVerification({{ $developer->id }})" class="action-button w-full inline-flex items-center justify-center px-5 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                            <i class="fas fa-check-circle ml-2"></i>
                            <span class="button-text">تحقق من المطور</span>
                            <div class="loading-spinner"></div>
                        </button>
                    @endif
                    
                    @if($developer->is_featured)
                        <button onclick="toggleFeatured({{ $developer->id }})" class="action-button w-full inline-flex items-center justify-center px-5 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-gray-700 bg-gray-200 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200">
                            <i class="fas fa-star-half-alt ml-2"></i>
                            <span class="button-text">إلغاء التمييز</span>
                            <div class="loading-spinner"></div>
                        </button>
                    @else
                        <button onclick="toggleFeatured({{ $developer->id }})" class="action-button w-full inline-flex items-center justify-center px-5 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200">
                            <i class="fas fa-star ml-2"></i>
                            <span class="button-text">تمييز المطور</span>
                            <div class="loading-spinner"></div>
                        </button>
                    @endif

                    <button onclick="confirmDelete({{ $developer->id }}, '{{ $developer->company_name }}')" class="action-button w-full inline-flex items-center justify-center px-5 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                        <i class="fas fa-trash-alt ml-2"></i>
                        <span class="button-text">حذف المطور</span>
                        <div class="loading-spinner"></div>
                    </button>
                </div>
            </div>

            <!-- Recent Projects Card -->
            <div class="developer-card bg-white rounded-xl shadow-md border border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-5 border-b pb-3">
                    <i class="fas fa-folder-open text-indigo-500 ml-2"></i> المشاريع الأخيرة
                </h2>
                <div class="space-y-3">
                    @if($developer->projects && $developer->projects->count() > 0)
                        @foreach($developer->projects->take(3) as $project)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800 text-sm">{{ $project->name ?? 'مشروع بدون اسم' }}</h4>
                                    <p class="text-xs text-gray-600 mt-1">{{ $project->type ?? 'نوع غير محدد' }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($project->status == 'completed') bg-green-100 text-green-800
                                    @elseif($project->status == 'ongoing') bg-blue-100 text-blue-800
                                    @elseif($project->status == 'planned') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $project->status ?? 'غير محدد' }}
                                </span>
                            </div>
                        @endforeach
                        @if($developer->projects->count() > 3)
                            <a href="#" class="block text-center text-sm text-blue-600 hover:text-blue-800 font-medium mt-3">
                                عرض جميع المشاريع ({{ $developer->projects->count() }})
                            </a>
                        @endif
                    @else
                        <div class="text-center py-6 text-gray-500">
                            <i class="fas fa-folder-open text-3xl mb-3 text-gray-300"></i>
                            <p class="text-sm">لا توجد مشاريع حالياً</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="flex items-center mb-4">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center ml-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-gray-900">تأكيد الحذف</h3>
                <p class="text-sm text-gray-600">هل أنت متأكد من حذف هذا المطور؟</p>
            </div>
        </div>
        <div class="bg-gray-50 p-3 rounded-lg mb-4">
            <p class="text-sm font-medium text-gray-900">اسم الشركة: <span id="deleteDeveloperName" class="text-red-600"></span></p>
        </div>
        <div class="text-sm text-gray-600 mb-6">
            <p class="mb-2">⚠️ هذا الإجراء سيؤدي إلى:</p>
            <ul class="list-disc list-inside space-y-1 text-red-600">
                <li>حذف جميع بيانات المطور نهائياً</li>
                <li>حذف جميع المشاريع المرتبطة بالمطور</li>
                <li>لا يمكن التراجع عن هذا الإجراء</li>
            </ul>
        </div>
        <div class="flex space-x-3 space-x-reverse">
            <button onclick="closeDeleteModal()" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                إلغاء
            </button>
            <button onclick="deleteDeveloper()" class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                <span class="delete-button-text">تأكيد الحذف</span>
                <div class="loading-spinner"></div>
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
let developerIdToDelete = null;

function showNotification(message, type = 'success') {
    console.log('Notification:', message, type);
    
    // Remove any existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    
    // Create icon based on type
    let icon = '';
    if (type === 'success') {
        icon = '<i class="fas fa-check-circle notification-icon"></i>';
    } else if (type === 'error') {
        icon = '<i class="fas fa-exclamation-circle notification-icon"></i>';
    } else if (type === 'warning') {
        icon = '<i class="fas fa-exclamation-triangle notification-icon"></i>';
    }
    
    notification.innerHTML = `
        <div style="display: flex; align-items: center; width: 100%;">
            <span>${message}</span>
            ${icon}
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 4 seconds
    setTimeout(() => {
        notification.style.animation = 'slideUp 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55)';
        setTimeout(() => notification.remove(), 400);
    }, 4000);
}

function setLoading(button, isLoading = true) {
    const spinner = button.querySelector('.loading-spinner');
    const text = button.querySelector('.button-text, .delete-button-text');
    
    if (isLoading) {
        spinner.style.display = 'inline-block';
        text.style.display = 'none';
        button.disabled = true;
    } else {
        spinner.style.display = 'none';
        text.style.display = 'inline';
        button.disabled = false;
    }
}

async function toggleFeatured(developerId) {
    console.log('Toggle Featured called for developer:', developerId);
    const button = event.currentTarget;
    setLoading(button, true);
    
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            console.error('CSRF token not found');
            showNotification('CSRF token not found', 'error');
            setLoading(button, false);
            return;
        }
        
        console.log('Making request to:', '{{ route('developer.toggleFeatured', ['developer' => $developer->id]) }}');
        const response = await fetch('{{ route('developer.toggleFeatured', ['developer' => $developer->id]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers.get('content-type'));
        
        // Handle different response types
        if (!response.ok) {
            if (response.status === 404) {
                throw new Error('Route not found - please check if routes are properly registered');
            } else if (response.status === 403) {
                throw new Error('Access denied - you may not have permission to perform this action');
            } else if (response.status === 500) {
                throw new Error('Server error - please check Laravel logs');
            } else {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
        }
        
        // Check if response is HTML (error page) or JSON
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('text/html')) {
            const htmlText = await response.text();
            console.error('Server returned HTML instead of JSON:', htmlText.substring(0, 200));
            throw new Error('Server returned HTML instead of JSON - check Laravel logs for errors');
        }
        
        const data = await response.json();
        console.log('Response data:', data);
        
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'حدث خطأ ما', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('حدث خطأ في الاتصال: ' + error.message, 'error');
    } finally {
        setLoading(button, false);
    }
}

async function toggleVerification(developerId) {
    console.log('Toggle Verification called for developer:', developerId);
    const button = event.currentTarget;
    setLoading(button, true);
    
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            console.error('CSRF token not found');
            showNotification('CSRF token not found', 'error');
            setLoading(button, false);
            return;
        }
        
        console.log('Making request to:', '{{ route('developer.toggleVerification', ['developer' => $developer->id]) }}');
        const response = await fetch('{{ route('developer.toggleVerification', ['developer' => $developer->id]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers.get('content-type'));
        
        // Handle different response types
        if (!response.ok) {
            if (response.status === 404) {
                throw new Error('Route not found - please check if routes are properly registered');
            } else if (response.status === 403) {
                throw new Error('Access denied - you may not have permission to perform this action');
            } else if (response.status === 500) {
                throw new Error('Server error - please check Laravel logs');
            } else {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
        }
        
        // Check if response is HTML (error page) or JSON
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('text/html')) {
            const htmlText = await response.text();
            console.error('Server returned HTML instead of JSON:', htmlText.substring(0, 200));
            throw new Error('Server returned HTML instead of JSON - check Laravel logs for errors');
        }
        
        const data = await response.json();
        console.log('Response data:', data);
        
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'حدث خطأ ما', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('حدث خطأ في الاتصال: ' + error.message, 'error');
    } finally {
        setLoading(button, false);
    }
}

function confirmDelete(developerId, companyName) {
    console.log('Confirm delete called for:', developerId, companyName);
    developerIdToDelete = developerId;
    document.getElementById('deleteDeveloperName').textContent = companyName;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    developerIdToDelete = null;
}

async function deleteDeveloper() {
    if (!developerIdToDelete) return;
    
    console.log('Delete developer called for:', developerIdToDelete);
    const button = event.currentTarget;
    setLoading(button, true);
    
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            console.error('CSRF token not found');
            showNotification('CSRF token not found', 'error');
            setLoading(button, false);
            return;
        }
        
        const response = await fetch(`/developer/${developerIdToDelete}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        });
        
        if (response.ok) {
            showNotification('تم حذف المطور بنجاح', 'success');
            setTimeout(() => {
                window.location.href = '/developer';
            }, 1000);
        } else {
            showNotification('حدث خطأ أثناء الحذف', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('حدث خطأ في الاتصال: ' + error.message, 'error');
    } finally {
        setLoading(button, false);
        closeDeleteModal();
    }
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

// Test if functions are accessible
console.log('JavaScript loaded, functions available:', {
    toggleFeatured: typeof toggleFeatured,
    toggleVerification: typeof toggleVerification,
    confirmDelete: typeof confirmDelete,
    deleteDeveloper: typeof deleteDeveloper
});
</script>
@endpush

@endsection