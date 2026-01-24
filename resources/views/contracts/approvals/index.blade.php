@extends('layouts.app')

@section('title', 'موافقات العقود')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">موافقات العقود</h1>
        <div class="flex space-x-2 space-x-reverse">
            <a href="{{ route('contracts.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                <i class="fas fa-arrow-right ml-2"></i>العقود
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث عن موافقات..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>موافق</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                    <option value="delegated" {{ request('status') == 'delegated' ? 'selected' : '' }}>مفوض</option>
                    <option value="escalated" {{ request('status') == 'escalated' ? 'selected' : '' }}>مُصعّد</option>
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
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-yellow-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">في الانتظار</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $approvals->where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-green-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-check"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">موافق</p>
                    <p class="text-2xl font-bold text-green-600">{{ $approvals->where('status', 'approved')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-red-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-times"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">مرفوض</p>
                    <p class="text-2xl font-bold text-red-600">{{ $approvals->where('status', 'rejected')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-purple-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">متأخر</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $approvals->where('deadline', '<', now())->where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Approvals Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العقد</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الموافق عليه</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نوع الموافقة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الموعد النهائي</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($approvals as $approval)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $approval->contract->title }}</div>
                                <div class="text-sm text-gray-500">{{ $approval->contract->contract_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $approval->approver->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($approval->approval_type)
                                    @case('sequential')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            تسلسلي
                                        </span>
                                        @break
                                    @case('parallel')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            متوازٍ
                                        </span>
                                        @break
                                    @case('escalation')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            تصعيد
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($approval->status)
                                    @case('pending')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            في الانتظار
                                        </span>
                                        @break
                                    @case('approved')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            موافق
                                        </span>
                                        @break
                                    @case('rejected')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            مرفوض
                                        </span>
                                        @break
                                    @case('delegated')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                            مفوض
                                        </span>
                                        @break
                                    @case('escalated')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            مُصعّد
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($approval->deadline)
                                    <span class="{{ $approval->deadline->isPast() && $approval->status === 'pending' ? 'text-red-600 font-semibold' : '' }}">
                                        {{ $approval->deadline->format('Y-m-d') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">غير محدد</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                <a href="{{ route('contract-approvals.show', $approval->contract) }}" class="text-blue-600 hover:text-blue-900 ml-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($approval->status === 'pending' && $approval->canBeDecidedBy(auth()->id()))
                                    <a href="{{ route('contract-approvals.approve', $approval) }}" class="text-green-600 hover:text-green-900 ml-2">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <a href="{{ route('contract-approvals.reject', $approval) }}" class="text-red-600 hover:text-red-900 ml-2">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                لا توجد موافقات
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            {{ $approvals->links() }}
        </div>
    </div>
</div>
@endsection
