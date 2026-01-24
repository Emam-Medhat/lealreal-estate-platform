@extends('layouts.app')

@section('title', 'الشهادات')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">الشهادات</h1>
        <a href="{{ route('certifications.create') }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
            <i class="fas fa-plus ml-2"></i>إضافة شهادة جديدة
        </a>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث عن شهادة..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <select name="certificate_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع الأنواعف</option>
                    <option value="occupancy" {{ request('certificate_type') == 'occupancy' ? 'selected' : '' }}>شهادة الإشغال</option>
                    <option value="safety" {{ request('certificate_type') == 'safety' ? 'selected' : '' }}>شهادة السلامة</option>
                    <option value="environmental" {{ request('certificate_type') == 'environmental' ? 'selected' : '' }}>شهادة بيئية</option>
                    <option value="energy" {{ request('certificate_type') == 'energy' ? 'selected' : '' }}>شهادة كفاءة الطاقة</option>
                    <option value="accessibility" {{ request('certificate_type') == 'accessibility' ? 'selected' : '' }}>شهادة إمكانية</option>
                    <option value="fire_safety" {{ request('certificate_type') == 'fire_safety' ? 'selected' : '' }}>شهادة سلامة الحريق</option>
                    <option value="structural" {{ request('certificate_type') == 'structural' ? 'selected' : '' }}>شهادة هيكلية</option>
                </select>
            </div>
            <div>
                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                    <option value="issued" {{ request('status') == 'issued' ? 'selected' : '' }}>صادرة</option>
                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>منتهية</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>موقوفة</option>
                    <option value="revoked" {{ request('status') == 'revoked' ? 'selected' : '' }}>ملغاة</option>
                </select>
            </div>
            <div>
                <select name="is_active" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">الكل</option>
                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشطة</option>
                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشطة</option>
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
                    <i class="fas fa-certificate"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">صادرة</p>
                    <p class="text-2xl font-bold text-green-600">{{ $certifications->where('status', 'issued')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-yellow-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">في الانتظار</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $certifications->where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-red-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">منتهية</p>
                    <p class="text-2xl font-bold text-red-600">{{ $certifications->where('status', 'expired')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-blue-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-pause-circle"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">موقوفة</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $certifications->where('status', 'suspended')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Certifications Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم الشهادة</th>
                        <th class="px-66 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العقار</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نوع الشهادة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الإصدار</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الانتهاء</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 تext-xs font-medium text-gray-500 uppercase tracking-wider">الجهة المصدرة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($certifications as $certification)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $certification->certificate_number }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $certification->title }}</div>
                                <div class="text-sm text-gray-500">{{ Str::limit($certification->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($certification->certificate_type)
                                    @case('occupancy')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            شهادة الإشغال
                                        </span>
                                        @break
                                    @case('safety')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            شهادة السلامة
                                        </span>
                                        @break
                                    @case('environmental')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            شهادة بيئية
                                        </span>
                                        @break
                                    @case('energy')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            شهادة كفاءة الطاقة
                                        </span>
                                        @break
                                    @case('accessibility')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            شهادة إمكانية
                                        </span>
                                        @break
                                    @case('fire_safety')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                            شهادة سلامة الحريق
                                        </span>
                                        @break
                                    @case('structural')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            شهادة هيكلية
                                        </span>
                                        @break
                                    @default
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            {{ $certification->certificate_type }}
                                        </span>
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($certification->status)
                                    @case('pending')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            في الانتظار
                                        </span>
                                        @break
                                    @case('issued')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            صادرة
                                        </span>
                                        @break
                                    @case('expired')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            منتهية
                                        </span>
                                        @break
                                    @case('suspended')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            موقوفة
                                        </span>
                                        @break
                                    @case('revoked')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                            ملغاة
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $certification->issue_date->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($certification->expiry_date)
                                    {{ $certification->expiry_date->format('Y-m-d') }}
                                @else
                                    غير محدد
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $certification->issuing_authority }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                <a href="{{ route('certifications.show', $certification) }}" class="text-blue-600 hover:text-blue-900 ml-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('certifications.edit', $certification) }}" class="text-yellow-600 hover:text-yellow-900 ml-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @if($certification->status === 'issued' && $certification->expiry_date && $certification->expiry_date->isFuture())
                                    <a href="{{ route('certifications.renew', $certification) }}" class="text-green-600 hover:text-green-900 ml-2">
                                        <i class="fas fa-sync"></i>
                                    </a>
                                @endif
                                @if($certification->status === 'issued')
                                    <a href="{{ route('certifications.download', $certification) }}" class="text-gray-600 hover:text-gray-900 ml-2">
                                        <i class="fas fa-download"></i>
                                    </a>
                                @endif
                                <a href="{{ route('certifications.verify', $certification) }}" class="text-purple-600 hover:text-purple-900 ml-2">
                                    <i class="fas fa-check-circle"></i>
                                </a>
                                @if($certification->status === 'issued')
                                    <a href="{{ route('certifications.suspend', $certification) }}" class="text-orange-600 hover:text-orange-900 ml-2">
                                        <i class="fas fa-pause"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-4 text-center text-gray-500">
                                لا توجد شهادات مسجلة
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            {{ $certifications->links() }}
        </div>
    </div>

    <!-- Expiring Soon -->
    @if($expiringSoon->count() > 0)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mt-6">
            <h2 class="text-lg font-semibold mb-4">شهادات ستنتهية قريباً</h2>
            
            <div class="space-y-4">
                @foreach($expiringSoon as $certification)
                    <div class="flex items-center justify-between bg-white rounded-lg p-4">
                        <div class="flex-1">
                            <div class="flex items-center">
                                <div class="text-sm font-medium text-gray-900">{{ $certification->title }}</div>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                    {{ $certification->certificate_type_label }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-500">
                                تنتهي في {{ $certification->expiry_date->diffForHumans() }}
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 space-x-reverse">
                            <a href="{{ route('certifications.renew', $certification) }}" class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600">
                                <i class="fas fa-sync ml-1"></i>تجديد
                            </a>
                            <a href="{{ route('certifications.show', $certification) }}" class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                                <i class="fas fa-eye ml-1"></i>عرض
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
