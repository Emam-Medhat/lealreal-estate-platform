@extends('layouts.app')

@section('title', 'التوقيعات الإلكترونية')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">التوقيعات الإلكترونية</h1>
        <div class="flex space-x-2 space-x-reverse">
            <a href="{{ route('esignatures.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                <i class="fas fa-plus ml-2"></i>طلب توقيع جديد
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث عن توقيع..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                    <option value="signed" {{ request('status') == 'signed' ? 'selected' : '' }}>موقّع</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
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
                    <p class="text-2xl font-bold text-yellow-600">{{ $signatures->where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-green-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-signature"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">موقّع</p>
                    <p class="text-2xl font-bold text-green-600">{{ $signatures->where('status', 'signed')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-red-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">ملغي</p>
                    <p class="text-2xl font-bold text-red-600">{{ $signatures->where('status', 'cancelled')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-blue-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-percentage"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">معدل التوقيع</p>
                    <p class="text-2xl font-bold text-blue-600">
                        @if($signatures->count() > 0)
                            {{ round(($signatures->where('status', 'signed')->count() / $signatures->count()) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Signatures Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الوثيقة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الموقّع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">البريد الإلكتروني</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نوع التوقيع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الطلب</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($signatures as $signature)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $signature->document->title }}</div>
                                <div class="text-sm text-gray-500">{{ $signature->document->file_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $signature->signer_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $signature->signer_email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($signature->status)
                                    @case('pending')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            في الانتظار
                                        </span>
                                        @break
                                    @case('signed')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            موقّع
                                        </span>
                                        @break
                                    @case('cancelled')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            ملغي
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($signature->signature_type)
                                    @case('draw')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            رسم
                                        </span>
                                        @break
                                    @case('type')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            كتابة
                                        </span>
                                        @break
                                    @case('upload')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                            رفع
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $signature->created_at->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                <a href="{{ route('esignatures.show', $signature) }}" class="text-blue-600 hover:text-blue-900 ml-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($signature->status === 'pending')
                                    <a href="{{ route('esignatures.sign', $signature->token) }}" class="text-green-600 hover:text-green-900 ml-2">
                                        <i class="fas fa-signature"></i>
                                    </a>
                                    <a href="{{ route('esignatures.remind', $signature) }}" class="text-yellow-600 hover:text-yellow-900 ml-2">
                                        <i class="fas fa-bell"></i>
                                    </a>
                                    <a href="{{ route('esignatures.cancel', $signature) }}" class="text-red-600 hover:text-red-900 ml-2">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                                @if($signature->status === 'signed')
                                    <a href="{{ route('esignatures.download', $signature) }}" class="text-gray-600 hover:text-gray-900 ml-2">
                                        <i class="fas fa-download"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                لا توجد توقيعات
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            {{ $signatures->links() }}
        </div>
    </div>
</div>
@endsection
