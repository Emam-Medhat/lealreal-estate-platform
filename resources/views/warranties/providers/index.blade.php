@extends('admin.layouts.admin')

@section('title', 'مقدمو الخدمة')

@section('page-title', 'مقدمو الخدمة')

@push('styles')
<style>
    /* Override global link styles for specific icons */
    .stats-card i,
    .header-icon i,
    .action-icons i,
    .search-icon i,
    .status-indicator i {
        color: inherit !important;
    }
    
    .stats-card .fa-users { color: #0d9488 !important; }
    .stats-card .fa-check-circle { color: #16a34a !important; }
    .stats-card .fa-shield-alt { color: #2563eb !important; }
    .stats-card .fa-pause-circle { color: #d97706 !important; }
    
    .header-icon i { color: #ffffff !important; }
    .search-icon i { color: #6b7280 !important; }
    
    .action-icons .fa-eye { color: #0d9488 !important; }
    .action-icons .fa-edit { color: #2563eb !important; }
    .action-icons .fa-trash { color: #dc2626 !important; }
    
    .status-indicator .fa-circle { color: inherit !important; }
    
    /* Fix header button - remove problematic opacity */
    .header-btn {
        background-color: rgba(255, 255, 255, 0.95) !important;
        color: #0d9488 !important;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
    }
    
    .header-btn:hover {
        background-color: rgba(255, 255, 255, 1) !important;
        color: #0f766e !important;
    }
    
    .header-btn i {
        color: #0d9488 !important;
    }
    
    /* Fix avatar backgrounds */
    .avatar-circle {
        background: linear-gradient(135deg, #0d9488 0%, #06b6d4 100%) !important;
        color: #ffffff !important;
    }
    
    /* Ensure text visibility */
    .text-on-gradient {
        color: #ffffff !important;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    }
    
    .text-on-gradient-light {
        color: #f0fdfa !important;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }
    
    /* Fix stats card text visibility */
    .stats-card p,
    .stats-card .text-gray-600,
    .stats-card .text-gray-900,
    .stats-card .text-green-600,
    .stats-card .text-blue-600,
    .stats-card .text-amber-600 {
        color: inherit !important;
    }
    
    .stats-card .text-gray-600 { color: #6b7280 !important; }
    .stats-card .text-gray-900 { color: #111827 !important; }
    .stats-card .text-green-600 { color: #16a34a !important; }
    .stats-card .text-blue-600 { color: #2563eb !important; }
    .stats-card .text-amber-600 { color: #d97706 !important; }
    
    /* Fix table text visibility */
    .table-text-fix * {
        color: inherit !important;
    }
    
    .text-gray-900 { color: #111827 !important; }
    .text-gray-800 { color: #1f2937 !important; }
    .text-gray-700 { color: #374151 !important; }
    .text-gray-600 { color: #6b7280 !important; }
    .text-gray-500 { color: #6b7280 !important; }
    .text-blue-800 { color: #1e40af !important; }
    .text-green-800 { color: #166534 !important; }
    .text-blue-600 { color: #2563eb !important; }
    .text-teal-600 { color: #0d9488 !important; }
    .text-teal-900 { color: #134e4a !important; }
    
    /* Fix button text */
    button, .btn {
        color: inherit !important;
    }
    
    /* Fix all text in the entire page */
    body, div, span, p, h1, h2, h3, h4, h5, h6, td, th, a, button {
        color: inherit !important;
    }
    
    /* Specific fixes for table content */
    td .text-gray-900 { color: #111827 !important; }
    td .text-gray-500 { color: #6b7280 !important; }
    th .text-gray-500 { color: #6b7280 !important; }
    
    /* Fix search input */
    input[type="text"], select {
        color: #111827 !important;
    }
    
    input[type="text"]::placeholder {
        color: #9ca3af !important;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-gradient-to-r from-teal-600 to-cyan-700 rounded-lg shadow-lg p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold mb-2 text-on-gradient">مقدمو الخدمة</h1>
                    <p class="text-on-gradient-light">إدارة ومتابعة مقدمي خدمات الضمان</p>
                </div>
                <a href="{{ route('warranties.providers.create') }}" 
                   class="header-btn px-6 py-3 rounded-lg transition-all duration-300 flex items-center">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة مقدم خدمة جديد
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow stats-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">إجمالي مقدمي الخدمة</p>
                        <p class="text-2xl font-bold text-gray-900">{{ App\Models\ServiceProvider::count() }}</p>
                    </div>
                    <div class="bg-teal-100 rounded-full p-3">
                        <i class="fas fa-users text-teal-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow stats-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">مقدمون نشطون</p>
                        <p class="text-2xl font-bold text-green-600">{{ App\Models\ServiceProvider::where('status', 'active')->count() }}</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow stats-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">الضمانات المرتبطة</p>
                        <p class="text-2xl font-bold text-blue-600">{{ App\Models\Warranty::whereNotNull('service_provider_id')->count() }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <i class="fas fa-shield-alt text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow stats-card">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">مقدمون جدد</p>
                        <p class="text-2xl font-bold text-amber-600">{{ App\Models\ServiceProvider::where('status', 'inactive')->count() }}</p>
                    </div>
                    <div class="bg-amber-100 rounded-full p-3">
                        <i class="fas fa-pause-circle text-amber-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filters -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <form method="GET" action="{{ route('warranties.providers.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="md:col-span-2">
                        <div class="relative search-icon">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="البحث بالاسم، البريد الإلكتروني، أو الهاتف..." 
                                   class="w-full px-4 py-2 pl-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        </div>
                    </div>
                    <div>
                        <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                            <option value="">جميع الحالات</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                    </div>
                    <div class="flex space-x-2">
                        <button type="submit" class="flex-1 bg-teal-600 text-white px-4 py-2 rounded-lg hover:bg-teal-700 transition-colors">
                            <i class="fas fa-search ml-2"></i>بحث
                        </button>
                        <a href="{{ route('warranties.providers.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Providers Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            @if($providers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">مقدم الخدمة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">البريد الإلكتروني</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الهاتف</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الضمانات</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($providers as $provider)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0">
                                                 <div class="h-10 w-10 rounded-full avatar-circle flex items-center justify-center font-semibold">
                                                    {{ strtoupper(substr($provider->name ?? 'A', 0, 1)) }}
                                                </div>
                                            </div>
                                            <div class="mr-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $provider->name }}</div>
                                                @if($provider->contact_person)
                                                    <div class="text-xs text-gray-500">{{ $provider->contact_person }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $provider->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $provider->phone }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $provider->warranties->count() }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($provider->status == 'active')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 status-indicator">
                                                <i class="fas fa-circle text-green-400 ml-1.5" style="font-size: 6px;"></i>
                                                نشط
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 status-indicator">
                                                <i class="fas fa-circle text-gray-400 ml-1.5" style="font-size: 6px;"></i>
                                                غير نشط
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                        <div class="flex space-x-2 space-x-reverse action-icons">
                                            <a href="{{ route('warranties.providers.show', $provider) }}" 
                                               class="text-teal-600 hover:text-teal-900 transition-colors"
                                               title="عرض التفاصيل">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('warranties.providers.edit', $provider) }}" 
                                               class="text-blue-600 hover:text-blue-900 transition-colors"
                                               title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" 
                                                  action="{{ route('warranties.providers.destroy', $provider) }}" 
                                                  class="inline" 
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا المقدم؟')">
                                                @csrf
                                                <button type="submit" 
                                                        class="text-red-600 hover:text-red-900 transition-colors"
                                                        title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="bg-gray-50 px-6 py-3 border-t border-gray-200">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                            عرض {{ $providers->firstItem() }} إلى {{ $providers->lastItem() }} من {{ $providers->total() }} مقدم خدمة
                        </div>
                        <div>
                            {{ $providers->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                        <i class="fas fa-user-tie text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد مقدمو خدمة</h3>
                    <p class="text-gray-500 mb-6">لم يتم إضافة أي مقدمي خدمة حتى الآن</p>
                    <a href="{{ route('warranties.providers.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors">
                        <i class="fas fa-plus ml-2"></i>
                        إضافة مقدم خدمة جديد
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
