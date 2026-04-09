@extends('admin.layouts.admin')

@section('title', 'الأعضاء - ' . $dao->name)

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-4 space-x-reverse">
                <a href="{{ route('blockchain.dao.show', $dao->id) }}" class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-right"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">أعضاء المنظمة</h1>
                    <p class="text-gray-600">{{ $dao->name }} - {{ $dao->members->count() }} عضو</p>
                </div>
            </div>
            
            <div class="flex gap-2">
                <a href="{{ route('blockchain.dao.members.add', $dao->id) }}" class="bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 transition-colors duration-200 font-medium">
                    <i class="fas fa-user-plus ml-2"></i>
                    إضافة عضو جديد
                </a>
                <a href="{{ route('blockchain.dao.members.export', $dao->id) }}" class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors duration-200 font-medium">
                    <i class="fas fa-download ml-2"></i>
                    تصدير
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Members -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">إجمالي الأعضاء</p>
                    <p class="text-3xl font-bold">{{ $dao->members->count() }}</p>
                    <p class="text-blue-100 text-xs mt-2">
                        <i class="fas fa-users ml-1"></i>
                        جميع الأعضاء
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-users text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Admin Members -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">المسؤولون</p>
                    <p class="text-3xl font-bold">{{ $dao->members->where('role', 'admin')->count() }}</p>
                    <p class="text-purple-100 text-xs mt-2">
                        <i class="fas fa-crown ml-1"></i>
                        صلاحيات كاملة
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-crown text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Voting Power -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">إجمالي قوة التصويت</p>
                    <p class="text-3xl font-bold">{{ number_format($dao->members->sum('voting_power'), 2) }}</p>
                    <p class="text-green-100 text-xs mt-2">
                        <i class="fas fa-vote-yea ml-1"></i>
                        {{ $dao->token_symbol }}
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-vote-yea text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" placeholder="بحث عن عضو..." class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex gap-2">
                <select class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option>جميع الأدوار</option>
                    <option>مسؤول</option>
                    <option>عضو</option>
                    <option>أمين الخزينة</option>
                </select>
                <select class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option>ترتيب حسب</option>
                    <option>الأحدث</option>
                    <option>الأعلى قوة تصويت</option>
                    <option>الأقل قوة تصويت</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Members Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-900">قائمة الأعضاء</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العضو</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الدور</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الرموز</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">قوة التصويت</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الانضمام</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if($dao->members->count() > 0)
                        @foreach($dao->members as $member)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-user text-blue-600"></i>
                                        </div>
                                    </div>
                                    <div class="mr-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            المستخدم {{ $member->user_id }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            ID: {{ $member->id }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($member->role === 'admin')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-crown ml-1"></i>
                                        مسؤول
                                    </span>
                                @elseif($member->role === 'treasurer')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-coins ml-1"></i>
                                        أمين الخزينة
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-user ml-1"></i>
                                        عضو
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($member->tokens_held, 2) }} {{ $dao->token_symbol }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ number_format($member->voting_power, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ is_string($member->joined_at) ? $member->joined_at : $member->joined_at->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end space-x-2 space-x-reverse">
                                    <a href="{{ route('blockchain.dao.members.show', [$dao->id, $member->id]) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('blockchain.dao.members.edit', [$dao->id, $member->id]) }}" class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($member->role !== 'admin')
                                    <form action="{{ route('blockchain.dao.members.delete', [$dao->id, $member->id]) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا العضو؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fas fa-users text-4xl mb-4"></i>
                                    <p class="text-lg font-medium mb-2">لا يوجد أعضاء حالياً</p>
                                    <p class="text-sm">ابدأ بإضافة أعضاء جدد للمنظمة</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center mt-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 px-4 py-3">
            <p class="text-sm text-gray-600">
                عرض {{ $dao->members->count() }} عضو من إجمالي {{ $dao->members->count() }}
            </p>
        </div>
    </div>
</div>
@endsection
