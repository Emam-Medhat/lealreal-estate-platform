@extends('layouts.app')

@section('title', 'مقدمي الخدمة')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">مقدمي الخدمة</h1>
        <a href="{{ route('service-providers.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
            <i class="fas fa-plus ml-2"></i>إضافة مقدم خدمة جديد
        </a>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث عن مقدم خدمة..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع الحالات</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>موقوف</option>
                </select>
            </div>
            <div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 w-full">
                    <i class="fas fa-search ml-2"></i>بحث
                </button>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-green-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">نشط</p>
                    <p class="text-2xl font-bold text-green-600">{{ $providers->where('status', 'active')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-yellow-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-pause-circle"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">غير نشط</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $providers->where('status', 'inactive')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-red-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-ban"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">موقوف</p>
                    <p class="text-2xl font-bold text-red-600">{{ $providers->where('status', 'suspended')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-blue-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">الإجمالي</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $providers->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Providers Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">مقدم الخدمة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التخصص</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الهاتف</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">البريد الإلكتروني</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التقييم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الطلبات</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($providers as $provider)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full" src="{{ $provider->logo ?? asset('images/default-logo.png') }}" alt="">
                                    </div>
                                    <div class="mr-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $provider->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $provider->contact_person }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $provider->specialization }}</div>
                                <div class="text-sm text-gray-500">{{ $provider->services ? implode(', ', array_slice($provider->services, 0, 2)) : '' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $provider->phone }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $provider->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($provider->status)
                                    @case('active')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            نشط
                                        </span>
                                        @break
                                    @case('inactive')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            غير نشط
                                        </span>
                                        @break
                                    @case('suspended')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            موقوف
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm text-gray-900">{{ $provider->rating }}/5</div>
                                    <div class="flex text-yellow-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $provider->rating)
                                                <i class="fas fa-star text-xs"></i>
                                            @else
                                                <i class="far fa-star text-xs"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $provider->total_requests ?? 0 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                <a href="{{ route('service-providers.show', $provider) }}" class="text-blue-600 hover:text-blue-900 ml-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('service-providers.edit', $provider) }}" class="text-yellow-600 hover:text-yellow-900 ml-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('maintenance-requests.create') }}?provider_id={{ $provider->id }}" class="text-green-600 hover:text-green-900 ml-2">
                                    <i class="fas fa-plus"></i>
                                </a>
                                <a href="{{ route('service-providers.performance', $provider) }}" class="text-purple-600 hover:text-purple-900 ml-2">
                                    <i class="fas fa-chart-line"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                لا يوجد مقدمي خدمة
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            {{ $providers->links() }}
        </div>
    </div>

    <!-- Featured Providers -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h2 class="text-lg font-semibold mb-4">مقدمو الخدمة المميزون</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($providers->where('is_featured', true)->take(3) as $featured)
                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0 h-12 w-12">
                            <img class="h-12 w-12 rounded-full" src="{{ $featured->logo ?? asset('images/default-logo.png') }}" alt="">
                        </div>
                        <div class="mr-4">
                            <h3 class="text-lg font-medium text-gray-900">{{ $featured->name }}</h3>
                            <div class="flex items-center">
                                <div class="text-sm text-gray-900">{{ $featured->rating }}/5</div>
                                <div class="flex text-yellow-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $featured->rating)
                                            <i class="fas fa-star text-xs"></i>
                                        @else
                                            <i class="far fa-star text-xs"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                            مميز
                        </span>
                    </div>
                    
                    <div class="text-sm text-gray-600 mb-4">
                        {{ Str::limit($featured->description, 100) }}
                    </div>
                    
                    <div class="flex items-center text-sm text-gray-500 mb-4">
                        <span class="ml-4">
                            <i class="fas fa-briefcase ml-1"></i>
                            {{ $featured->specialization }}
                        </span>
                        <span class="ml-4">
                            <i class="fas fa-tasks ml-1"></i>
                            {{ $featured->total_requests ?? 0 }} طلب
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-500">
                            <i class="fas fa-phone ml-1"></i>
                            {{ $featured->phone }}
                        </div>
                        <a href="{{ route('service-providers.show', $featured) }}" class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                            عرض التفاصيل
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
