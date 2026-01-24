@extends('layouts.app')

@section('title', 'التقييمات')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">التقييمات</h1>
        <p class="text-gray-600">استعرض وإدارة جميع تقييمات المنصة</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 rounded-full">
                    <i class="fas fa-star text-blue-600 text-xl"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">إجمالي التقييمات</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $reviews->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">الموافق عليها</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $reviews->where('status', 'approved')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-yellow-100 rounded-full">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">في انتظار المراجعة</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $reviews->where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 rounded-full">
                    <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                </div>
                <div class="mr-4">
                    <p class="text-sm text-gray-600">متوسط التقييم</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($reviews->avg('rating') ?? 0, 1) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form method="GET" action="{{ route('reviews.search') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                    <div class="relative">
                        <input type="text" name="q" value="{{ request('q') }}" 
                               class="w-full pr-10 pl-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="ابحث في التقييمات...">
                        <button type="submit" class="absolute left-2 top-2.5 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                    <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">جميع الحالات</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في انتظار المراجعة</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>موافق عليه</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">التقييم</label>
                    <select name="rating" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">جميع التقييمات</option>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} نجوم</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div class="mt-4 flex justify-between">
                <div class="flex space-x-2 space-x-reverse">
                    <a href="{{ route('reviews.index') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900">
                        <i class="fas fa-redo ml-2"></i>إعادة تعيين
                    </a>
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-filter ml-2"></i>تطبيق الفلاتر
                </button>
            </div>
        </form>
    </div>

    <!-- Reviews List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            التقييم
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            المستخدم
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            العنصر
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            التقييم
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الحالة
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            التاريخ
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الإجراءات
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reviews as $review)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="fas fa-star text-yellow-400"></i>
                                            @else
                                                <i class="far fa-star text-gray-300"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <div class="mr-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $review->title }}</p>
                                        <p class="text-xs text-gray-500">{{ Str::limit($review->content, 50) }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <img class="h-10 w-10 rounded-full" src="{{ $review->user->avatar ?? 'images/default-avatar.png' }}" alt="">
                                    </div>
                                    <div class="mr-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $review->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $review->user->email }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">
                                    @if($review->reviewable_type === 'App\Models\Property')
                                        <span class="text-blue-600">عقار</span>
                                    @elseif($review->reviewable_type === 'App\Models\Agent')
                                        <span class="text-green-600">وكيل</span>
                                    @else
                                        <span class="text-gray-600">{{ $review->reviewable_type }}</span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <span class="text-lg font-bold text-gray-900">{{ $review->rating }}</span>
                                    <span class="mr-1 text-sm text-gray-500">/5</span>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($review->status === 'approved') bg-green-100 text-green-800
                                    @elseif($review->status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $review->getStatusText() }}
                                </span>
                                @if($review->is_verified)
                                    <i class="fas fa-check-circle text-green-500 mr-1" title="موثق"></i>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $review->created_at->format('Y-m-d') }}
                            </td>

                            <td class="px-6 py-4 text-left">
                                <div class="flex space-x-2 space-x-reverse">
                                    <a href="{{ route('reviews.show', $review->id) }}" 
                                       class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(auth()->user()->hasRole('admin'))
                                        @if($review->status === 'pending')
                                            <form action="{{ route('reviews.approve', $review->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('reviews.edit', $review->id) }}" 
                                           class="text-yellow-600 hover:text-yellow-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-4"></i>
                                <p>لا توجد تقييمات حالياً</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($reviews->hasPages())
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    {{ $reviews->links() }}
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            عرض
                            <span class="font-medium">{{ $reviews->firstItem() }}</span>
                            إلى
                            <span class="font-medium">{{ $reviews->lastItem() }}</span>
                            من
                            <span class="font-medium">{{ $reviews->total() }}</span>
                            نتائج
                        </p>
                    </div>
                    <div>
                        {{ $reviews->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
