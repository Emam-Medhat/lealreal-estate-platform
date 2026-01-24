@extends('layouts.app')

@section('title', 'المقيمين')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">المقيمين</h1>
        <a href="{{ route('appraisers.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
            <i class="fas fa-plus ml-2"></i>إضافة مقيم جديد
        </a>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث عن مقيم..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
                    <i class="fas fa-user-check"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">نشط</p>
                    <p class="text-2xl font-bold text-green-600">{{ $appraisers->where('status', 'active')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-yellow-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">غير نشط</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $appraisers->where('status', 'inactive')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-red-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-user-slash"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">موقوف</p>
                    <p class="text-2xl font-bold text-red-600">{{ $appraisers->where('status', 'suspended')->count() }}</p>
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
                    <p class="text-2xl font-bold text-blue-600">{{ $appraisers->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Appraisers Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المقيم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">البريد الإلكتروني</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الهاتف</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الرخصة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التقييم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التقييمات</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">متوسط الرسوم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($appraisers as $appraiser)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full" src="{{ $appraiser->avatar ?? asset('images/default-avatar.png') }}" alt="">
                                    </div>
                                    <div class="mr-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $appraiser->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $appraiser->specializations ? implode(', ', array_slice($appraiser->specializations, 0, 2)) : '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $appraiser->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $appraiser->phone }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div>
                                    <div>{{ $appraiser->license_number }}</div>
                                    <div class="text-xs text-gray-400">{{ $appraiser->license_expiry->format('Y-m-d') }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($appraiser->status)
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
                                    <div class="text-sm text-gray-900">{{ $appraiser->rating }}/5</div>
                                    <div class="flex text-yellow-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $appraiser->rating)
                                                <i class="fas fa-star text-xs"></i>
                                            @else
                                                <i class="far fa-star text-xs"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $appraiser->total_appraisals ?? 0 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($appraiser->average_fee)
                                    {{ number_format($appraiser->average_fee, 2) }} ريال
                                @else
                                    غير محدد
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                <a href="{{ route('appraisers.show', $appraiser) }}" class="text-blue-600 hover:text-blue-900 ml-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('appraisers.edit', $appraiser) }}" class="text-yellow-600 hover:text-yellow-900 ml-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ route('appraisals.create') }}?appraiser_id={{ $appraiser->id }}" class="text-green-600 hover:text-green-900 ml-2">
                                    <i class="fas fa-plus"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-4 text-center text-gray-500">
                                لا يوجد مقيمين
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            {{ $appraisers->links() }}
        </div>
    </div>

    <!-- Featured Appraisers -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h2 class="text-lg font-semibold mb-4">المقيمون المميزون</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($appraisers->where('is_featured', true)->take(3) as $featured)
                <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                    <div class="flex items-center mb-4">
                        <div class="flex-shrink-0 h-12 w-12">
                            <img class="h-12 w-12 rounded-full" src="{{ $featured->avatar ?? asset('images/default-avatar.png') }}" alt="">
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
                        {{ Str::limit($featured->bio, 100) }}
                    </div>
                    
                    <div class="flex items-center text-sm text-gray-500 mb-4">
                        <span class="ml-4">
                            <i class="fas fa-briefcase ml-1"></i>
                            {{ $featured->total_appraisals ?? 0 }} تقييم
                        </span>
                        <span class="ml-4">
                            <i class="fas fa-star ml-1"></i>
                            {{ $featured->rating }}/5 تقييم
                        </span>
                    </div>
                    
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-500">
                            <i class="fas fa-money-bill ml-1"></i>
                            @if($featured->average_fee)
                                {{ number_format($featured->average_fee, 2) }} ريال
                            @else
                                غير محدد
                            @endif
                        </div>
                        <a href="{{ route('appraisers.show', $featured) }}" class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                            عرض التفاصيل
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
