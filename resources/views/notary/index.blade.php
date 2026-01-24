@extends('layouts.app')

@section('title', 'التحقق من التوثيق')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">التحقق من التوثيق</h1>
        <div class="flex space-x-2 space-x-reverse">
            <a href="{{ route('notary.dashboard') }}" class="bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600">
                <i class="fas fa-tachometer-alt ml-2"></i>لوحة التحكم
            </a>
            <a href="{{ route('notary.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                <i class="fas fa-plus ml-2"></i>طلب تحقق جديد
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">الكل</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>موثق</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                    <option value="requires_info" {{ request('status') == 'requires_info' ? 'selected' : '' }}>مطلوب معلومات</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">نوع التحقق</label>
                <select name="verification_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">الكل</option>
                    <option value="standard" {{ request('verification_type') == 'standard' ? 'selected' : '' }}>قياسي</option>
                    <option value="expedited" {{ request('verification_type') == 'expedited' ? 'selected' : '' }}>معجل</option>
                    <option value="priority" {{ request('verification_type') == 'priority' ? 'selected' : '' }}>أولوية</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الموثق</label>
                <select name="notary_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">الكل</option>
                    @foreach($notaries ?? [] as $notary)
                        <option value="{{ $notary->id }}" {{ request('notary_id') == $notary->id ? 'selected' : '' }}>
                            {{ $notary->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
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
                    <p class="text-2xl font-bold text-yellow-600">{{ $verifications->where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-green-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">موثق</p>
                    <p class="text-2xl font-bold text-green-600">{{ $verifications->where('status', 'verified')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-red-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">مرفوض</p>
                    <p class="text-2xl font-bold text-red-600">{{ $verifications->where('status', 'rejected')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-blue-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">مطلوب معلومات</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $verifications->where('status', 'requires_info')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Verifications Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العقد</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">كود التحقق</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النوع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الموثق</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الطلب</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإكمال المتوقع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($verifications as $verification)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $verification->contract->title }}</div>
                                <div class="text-sm text-gray-500">{{ $verification->contract->contract_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-mono text-gray-900">{{ $verification->verification_code }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($verification->verification_type)
                                    @case('standard')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            قياسي
                                        </span>
                                        @break
                                    @case('expedited')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            معجل
                                        </span>
                                        @break
                                    @case('priority')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            أولوية
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($verification->status)
                                    @case('pending')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            في الانتظار
                                        </span>
                                        @break
                                    @case('verified')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            موثق
                                        </span>
                                        @break
                                    @case('rejected')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            مرفوض
                                        </span>
                                        @break
                                    @case('requires_info')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            مطلوب معلومات
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $verification->notary->name ?? 'غير محدد' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $verification->requested_at->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($verification->estimated_completion)
                                    <span class="{{ $verification->estimated_completion->isPast() ? 'text-red-600 font-semibold' : '' }}">
                                        {{ $verification->estimated_completion->format('Y-m-d') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">غير محدد</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                <a href="{{ route('notary.show', $verification) }}" class="text-blue-600 hover:text-blue-900 ml-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($verification->status === 'verified')
                                    <a href="{{ route('notary.certificate', $verification) }}" class="text-green-600 hover:text-green-900 ml-2">
                                        <i class="fas fa-certificate"></i>
                                    </a>
                                @endif
                                @if($verification->status === 'requires_info')
                                    <a href="{{ route('notary.info', $verification) }}" class="text-yellow-600 hover:text-yellow-900 ml-2">
                                        <i class="fas fa-info"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                لا توجد طلبات تحقق
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            {{ $verifications->links() }}
        </div>
    </div>
</div>
@endsection
