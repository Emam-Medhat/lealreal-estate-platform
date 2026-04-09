@extends('layouts.app')

@section('title', 'جدول الصيانة')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">جدول الصيانة</h1>
        <div class="flex space-x-2 space-x-reverse">
            <a href="{{ route('maintenance.schedule.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                <i class="fas fa-plus ml-2"></i>إضافة جدول جديد
            </a>
            <a href="{{ route('maintenance.calendar') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                <i class="fas fa-calendar ml-2"></i>عرض التقويم
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث عن جدول..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع الحالات</option>
                    <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>مجدول</option>
                    <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد التنفيذ</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                </select>
            </div>
            <div>
                <select name="priority" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع الأولويات</option>
                    <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>منخفض</option>
                    <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>متوسط</option>
                    <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>عالي</option>
                    <option value="emergency" {{ request('priority') == 'emergency' ? 'selected' : '' }}>طارئ</option>
                </select>
            </div>
            <div>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-blue-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">مجدول</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $schedules->where('status', 'scheduled')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-yellow-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-cog"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">قيد التنفيذ</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $schedules->where('status', 'in_progress')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-green-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">مكتمل</p>
                    <p class="text-2xl font-bold text-green-600">{{ $schedules->where('status', 'completed')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-red-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">متأخر</p>
                    <p class="text-2xl font-bold text-red-600">{{ $schedules->where('scheduled_date', '<', now()->format('Y-m-d'))->where('status', '!=', 'completed')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- List View -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العنوان</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العقار</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الأولوية</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الفريق</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($schedules as $schedule)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $schedule->title }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($schedule->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $schedule->property->title ?? 'N/A' }}</div>
                                <div class="text-sm text-gray-500">{{ $schedule->property->property_number ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $schedule->scheduled_date->format('Y-m-d') }}</div>
                                <div class="text-sm text-gray-500">{{ $schedule->scheduled_time ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($schedule->status)
                                    @case('scheduled')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            مجدول
                                        </span>
                                        @break
                                    @case('in_progress')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            قيد التنفيذ
                                        </span>
                                        @break
                                    @case('completed')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            مكتمل
                                        </span>
                                        @break
                                    @case('cancelled')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            ملغي
                                        </span>
                                        @break
                                    @default
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $schedule->status }}
                                        </span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($schedule->priority)
                                    @case('low')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            منخفض
                                        </span>
                                        @break
                                    @case('medium')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            متوسط
                                        </span>
                                        @break
                                    @case('high')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                            عالي
                                        </span>
                                        @break
                                    @case('emergency')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            طارئ
                                        </span>
                                        @break
                                    @default
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $schedule->priority ?? 'N/A' }}
                                        </span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $schedule->assignedTeam->name ?? 'غير محدد' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                <a href="{{ route('maintenance.schedule.show', $schedule) }}" class="text-blue-600 hover:text-blue-900 ml-2" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('maintenance.schedule.edit', $schedule) }}" class="text-yellow-600 hover:text-yellow-900 ml-2" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($schedule->status === 'scheduled')
                                    <form action="{{ route('maintenance.schedule.start', $schedule) }}" method="POST" class="inline-block ml-2">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-900 bg-transparent border-0 p-0 cursor-pointer" title="بدء">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    </form>
                                @endif
                                @if($schedule->status === 'in_progress')
                                    <form action="{{ route('maintenance.schedule.complete', $schedule) }}" method="POST" class="inline-block ml-2">
                                        @csrf
                                        <button type="submit" class="text-purple-600 hover:text-purple-900 bg-transparent border-0 p-0 cursor-pointer" title="إكمال">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                لا توجد جداول صيانة مسجلة
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            {{ $schedules->links() }}
        </div>
    </div>
</div>
@endsection
