@extends('admin.layouts.admin')

@section('title', 'مقدمو الخدمة')

@section('page-title', 'مقدمو الخدمة')

@section('content')
<!-- Page Header -->
<div class="mb-8">
    <div class="bg-gradient-to-r from-teal-600 via-teal-700 to-cyan-800 rounded-2xl shadow-xl p-8 text-white">
        <div class="flex justify-between items-center">
            <div>
                <div class="flex items-center mb-3">
                    <div class="bg-white bg-opacity-20 rounded-xl p-3 ml-4">
                        <i class="fas fa-user-tie text-white text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white">مقدمو الخدمة</h1>
                        <p class="text-teal-100 mt-1">إدارة ومتابعة مقدمي خدمات الضمان</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4 mt-4">
                    <div class="bg-white bg-opacity-20 rounded-lg px-3 py-1">
                        <span class="text-sm text-teal-100">الإجمالي:</span>
                        <span class="text-sm font-semibold text-white">{{ $providers->total() }}</span>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-lg px-3 py-1">
                        <span class="text-sm text-teal-100">النشطون:</span>
                        <span class="text-sm font-semibold text-white">{{ $providers->where('status', 'active')->count() }}</span>
                    </div>
                </div>
            </div>
            <div class="flex space-x-reverse space-x-3">
                <a href="{{ route('warranties.providers.create') }}" class="inline-flex items-center px-6 py-3 bg-white bg-opacity-20 backdrop-blur-sm border border-white border-opacity-30 rounded-xl text-sm font-medium text-white hover:bg-opacity-30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-300">
                    <i class="fas fa-plus ml-2"></i>
                    مقدم خدمة جديد
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-teal-100 text-sm font-medium mb-2">إجمالي مقدمي الخدمة</p>
                <p class="text-3xl font-bold text-white">{{ App\Models\ServiceProvider::count() }}</p>
                <div class="mt-2 flex items-center text-xs text-teal-100">
                    <i class="fas fa-users ml-1"></i>
                    <span>جميع المقدمين</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-users text-white text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-green-100 text-sm font-medium mb-2">مقدمون نشطون</p>
                <p class="text-3xl font-bold text-white">{{ App\Models\ServiceProvider::where('status', 'active')->count() }}</p>
                <div class="mt-2 flex items-center text-xs text-green-100">
                    <i class="fas fa-check-circle ml-1"></i>
                    <span>متاحون للخدمة</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-check-circle text-white text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-blue-100 text-sm font-medium mb-2">الضمانات المرتبطة</p>
                <p class="text-3xl font-bold text-white">{{ App\Models\Warranty::whereNotNull('service_provider_id')->count() }}</p>
                <div class="mt-2 flex items-center text-xs text-blue-100">
                    <i class="fas fa-shield-alt ml-1"></i>
                    <span>ضمانات نشطة</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-shield-alt text-white text-2xl"></i>
            </div>
        </div>
    </div>
    
    <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl shadow-xl p-6 transform hover:scale-105 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-amber-100 text-sm font-medium mb-2">مقدمون جدد</p>
                <p class="text-3xl font-bold text-white">{{ App\Models\ServiceProvider::where('status', 'inactive')->count() }}</p>
                <div class="mt-2 flex items-center text-xs text-amber-100">
                    <i class="fas fa-pause-circle ml-1"></i>
                    <span>غير نشطين</span>
                </div>
            </div>
            <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-2xl p-4">
                <i class="fas fa-pause-circle text-white text-2xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Providers Table -->
<div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center mb-4 lg:mb-0">
                <div class="bg-teal-500 rounded-lg p-2 ml-3">
                    <i class="fas fa-list text-white"></i>
                </div>
                قائمة مقدمي الخدمة
            </h3>
            
            <!-- Search and Filters -->
            <form method="GET" class="flex flex-wrap gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم، البريد، أو الهاتف" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-500">
                    <option value="">جميع الحالات</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                </select>
                
                <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors">
                    <i class="fas fa-search ml-2"></i>
                    بحث
                </button>
                
                <a href="{{ route('warranties.providers.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-redo ml-2"></i>
                    إعادة تعيين
                </a>
            </form>
        </div>
    </div>
    
    <div class="p-6">
        @if($providers->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الاسم</th>
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
                                <div class="text-sm font-medium text-gray-900">{{ $provider->name }}</div>
                                @if($provider->contact_person)
                                    <div class="text-xs text-gray-500">الشخص المسؤول: {{ $provider->contact_person }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $provider->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $provider->phone }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $provider->warranties->count() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($provider->status == 'active') bg-green-100 text-green-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    @if($provider->status == 'active') نشط
                                    @else غير نشط @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-reverse space-x-2">
                                    <a href="{{ route('warranties.providers.show', $provider) }}" class="text-teal-600 hover:text-teal-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('warranties.providers.edit', $provider) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('warranties.providers.destroy', $provider) }}" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المقدم؟')">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900">
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
            <div class="mt-6">
                {{ $providers->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-user-tie text-gray-400 text-5xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد مقدمو خدمة</h3>
                <p class="text-gray-500 mb-6">لم يتم إضافة أي مقدمي خدمة حتى الآن</p>
                <a href="{{ route('warranties.providers.create') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-teal-500">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة مقدم خدمة جديد
                </a>
            </div>
        @endif
    </div>
</div>

@endsection
