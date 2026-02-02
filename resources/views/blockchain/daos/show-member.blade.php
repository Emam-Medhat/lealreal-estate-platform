@extends('admin.layouts.admin')

@section('title', 'تفاصيل العضو - ' . $member->user_id)

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-4 space-x-reverse">
                <a href="{{ route('blockchain.dao.members', $dao->id) }}" class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-right"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">تفاصيل العضو</h1>
                    <p class="text-gray-600">{{ $dao->name }} - عضو #{{ $member->id }}</p>
                </div>
            </div>
            
            <div class="flex gap-2">
                <a href="{{ route('blockchain.dao.members.edit', [$dao->id, $member->id]) }}" class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors duration-200 font-medium">
                    <i class="fas fa-edit ml-2"></i>
                    تعديل
                </a>
                @if($member->role !== 'admin')
                <form action="{{ route('blockchain.dao.members.delete', [$dao->id, $member->id]) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا العضو؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white px-6 py-3 rounded-xl hover:bg-red-700 transition-colors duration-200 font-medium">
                        <i class="fas fa-trash ml-2"></i>
                        حذف
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Member Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Member Profile -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">الملف الشخصي</h2>
                <div class="flex items-center space-x-6 space-x-reverse mb-6">
                    <div class="flex-shrink-0">
                        <div class="h-20 w-20 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-user text-blue-600 text-2xl"></i>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">المستخدم {{ $member->user_id }}</h3>
                        <p class="text-gray-600">ID: {{ $member->id }}</p>
                        @if($member->role === 'admin')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-purple-100 text-purple-800 mt-2">
                                <i class="fas fa-crown ml-1"></i>
                                مسؤول
                            </span>
                        @elseif($member->role === 'treasurer')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 mt-2">
                                <i class="fas fa-coins ml-1"></i>
                                أمين الخزينة
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-gray-100 text-gray-800 mt-2">
                                <i class="fas fa-user ml-1"></i>
                                عضو
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">الدور</h4>
                        <p class="text-gray-600">
                            @if($member->role === 'admin')
                                مسؤول - صلاحيات كاملة في المنظمة
                            @elseif($member->role === 'treasurer')
                                أمين الخزينة - إدارة أموال المنظمة
                            @else
                                عضو - صلاحيات التصويت الأساسية
                            @endif
                        </p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">تاريخ الانضمام</h4>
                        <p class="text-gray-600">{{ is_string($member->joined_at) ? $member->joined_at : $member->joined_at->format('Y-m-d H:i:s') }}</p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">المنظمة</h4>
                        <p class="text-gray-600">{{ $dao->name }}</p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">رمز المنظمة</h4>
                        <p class="text-gray-600">{{ $dao->token_symbol }}</p>
                    </div>
                </div>
            </div>

            <!-- Financial Information -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">المعلومات المالية</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">الرموز المملوكة</h4>
                        <p class="text-3xl font-bold text-green-600">{{ number_format($member->tokens_held, 2) }}</p>
                        <p class="text-gray-600 text-sm">{{ $dao->token_symbol }}</p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">قوة التصويت</h4>
                        <p class="text-3xl font-bold text-blue-600">{{ number_format($member->voting_power, 2) }}</p>
                        <p class="text-gray-600 text-sm">قوة التصويت</p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">نسبة الملكية</h4>
                        <p class="text-3xl font-bold text-purple-600">
                            @if($dao->total_supply > 0)
                                {{ number_format(($member->tokens_held / $dao->total_supply) * 100, 2) }}%
                            @else
                                0%
                            @endif
                        </p>
                        <p class="text-gray-600 text-sm">من إجمالي العرض</p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">نسبة التصويت</h4>
                        <p class="text-3xl font-bold text-orange-600">
                            @if($dao->members->sum('voting_power') > 0)
                                {{ number_format(($member->voting_power / $dao->members->sum('voting_power')) * 100, 2) }}%
                            @else
                                0%
                            @endif
                        </p>
                        <p class="text-gray-600 text-sm">من إجمالي قوة التصويت</p>
                    </div>
                </div>
            </div>

            <!-- Voting History -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">سجل التصويت</h2>
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-vote-yea text-4xl mb-4"></i>
                    <p class="text-lg font-medium mb-2">لا يوجد سجل تصويت حالياً</p>
                    <p class="text-sm">سيظهر هنا سجل تصويت العضو في المقترحات المختلفة</p>
                </div>
            </div>
        </div>

        <!-- Right Column - Quick Actions & Stats -->
        <div class="space-y-6">
            <!-- Quick Stats -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">إحصائيات سريعة</h2>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">المرتبة في المنظمة</span>
                        <span class="font-semibold">#{{ $dao->members->where('tokens_held', '>', $member->tokens_held)->count() + 1 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">المقترحات المصوت عليها</span>
                        <span class="font-semibold">0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">المقترحات المنشأة</span>
                        <span class="font-semibold">0</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">آخر نشاط</span>
                        <span class="font-semibold">{{ is_string($member->joined_at) ? $member->joined_at : $member->joined_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">إجراءات سريعة</h2>
                <div class="space-y-3">
                    <a href="{{ route('blockchain.dao.members.edit', [$dao->id, $member->id]) }}" class="w-full bg-blue-600 text-white py-3 rounded-xl hover:bg-blue-700 transition-colors duration-200 font-medium text-center block">
                        <i class="fas fa-edit ml-2"></i>
                        تعديل بيانات العضو
                    </a>
                    <a href="{{ route('blockchain.dao.proposals', $dao->id) }}" class="w-full bg-green-600 text-white py-3 rounded-xl hover:bg-green-700 transition-colors duration-200 font-medium text-center block">
                        <i class="fas fa-file-alt ml-2"></i>
                        عرض المقترحات
                    </a>
                    <a href="{{ route('blockchain.dao.vote', $dao->id) }}" class="w-full bg-purple-600 text-white py-3 rounded-xl hover:bg-purple-700 transition-colors duration-200 font-medium text-center block">
                        <i class="fas fa-vote-yea ml-2"></i>
                        التصويت على المقترحات
                    </a>
                    <a href="{{ route('blockchain.dao.treasury', $dao->id) }}" class="w-full bg-orange-600 text-white py-3 rounded-xl hover:bg-orange-700 transition-colors duration-200 font-medium text-center block">
                        <i class="fas fa-vault ml-2"></i>
                        عرض الخزينة
                    </a>
                </div>
            </div>

            <!-- Member Rights -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">الصلاحيات</h2>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 ml-2"></i>
                        <span class="text-gray-700">التصويت على المقترحات</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 ml-2"></i>
                        <span class="text-gray-700">عرض المقترحات</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 ml-2"></i>
                        <span class="text-gray-700">الوصول للخزينة</span>
                    </div>
                    @if($member->role === 'admin')
                    <div class="flex items-center">
                        <i class="fas fa-crown text-purple-500 ml-2"></i>
                        <span class="text-gray-700 font-semibold">إدارة المنظمة</span>
                    </div>
                    @elseif($member->role === 'treasurer')
                    <div class="flex items-center">
                        <i class="fas fa-coins text-yellow-500 ml-2"></i>
                        <span class="text-gray-700 font-semibold">إدارة الخزينة</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
