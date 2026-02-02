@extends('admin.layouts.admin')

@section('title', 'المقترحات - ' . $dao->name)

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
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">المقترحات</h1>
                    <p class="text-gray-600">{{ $dao->name }} - {{ $dao->proposals->count() }} مقترح</p>
                </div>
            </div>
            
            <div class="flex gap-2">
                <button class="bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 transition-colors duration-200 font-medium">
                    <i class="fas fa-plus ml-2"></i>
                    إنشاء مقترح جديد
                </button>
                <button class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors duration-200 font-medium">
                    <i class="fas fa-download ml-2"></i>
                    تصدير
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Total Proposals -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">إجمالي المقترحات</p>
                    <p class="text-3xl font-bold">{{ $dao->proposals->count() }}</p>
                    <p class="text-blue-100 text-xs mt-2">
                        <i class="fas fa-file-alt ml-1"></i>
                        جميع المقترحات
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-file-alt text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Proposals -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">المقترحات النشطة</p>
                    <p class="text-3xl font-bold">{{ $dao->proposals->where('status', 'active')->count() }}</p>
                    <p class="text-green-100 text-xs mt-2">
                        <i class="fas fa-clock ml-1"></i>
                        قيد التصويت
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-clock text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Passed Proposals -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">المقترحات المقبولة</p>
                    <p class="text-3xl font-bold">{{ $dao->proposals->where('status', 'passed')->count() }}</p>
                    <p class="text-purple-100 text-xs mt-2">
                        <i class="fas fa-check-circle ml-1"></i>
                        تم الموافقة
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-check-circle text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Executed Proposals -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium mb-1">المقترحات المنفذة</p>
                    <p class="text-3xl font-bold">{{ $dao->proposals->where('status', 'executed')->count() }}</p>
                    <p class="text-orange-100 text-xs mt-2">
                        <i class="fas fa-play-circle ml-1"></i>
                        تم التنفيذ
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-play-circle text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" placeholder="بحث عن مقترح..." class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="flex gap-2">
                <select class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option>جميع الحالات</option>
                    <option>نشط</option>
                    <option>مقبول</option>
                    <option>مرفوض</option>
                    <option>منفذ</option>
                </select>
                <select class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option>جميع الأنواع</option>
                    <option>تمويل</option>
                    <option>تغيير معاملات</option>
                    <option>عضوية</option>
                    <option>أخرى</option>
                </select>
                <select class="px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option>ترتيب حسب</option>
                    <option>الأحدث</option>
                    <option>الأعلى أصوات</option>
                    <option>الأقل أصوات</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Proposals Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @if($dao->proposals->count() > 0)
            @foreach($dao->proposals as $proposal)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-900 text-lg mb-2">{{ $proposal->title }}</h3>
                            <p class="text-gray-600 text-sm">{{ Str::limit($proposal->description, 100) }}</p>
                        </div>
                        @if($proposal->status === 'active')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <span class="w-2 h-2 bg-green-400 rounded-full ml-2"></span>
                                نشط
                            </span>
                        @elseif($proposal->status === 'passed')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                <span class="w-2 h-2 bg-blue-400 rounded-full ml-2"></span>
                                مقبول
                            </span>
                        @elseif($proposal->status === 'executed')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                <span class="w-2 h-2 bg-purple-400 rounded-full ml-2"></span>
                                منفذ
                            </span>
                        @elseif($proposal->status === 'rejected')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <span class="w-2 h-2 bg-red-400 rounded-full ml-2"></span>
                                مرفوض
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                {{ $proposal->status }}
                            </span>
                        @endif
                    </div>

                    <div class="space-y-3 mb-4">
                        <div class="flex justify-between">
                            <span class="text-gray-600">النوع:</span>
                            <span class="font-semibold">
                                @if($proposal->type === 'funding')
                                    تمويل
                                @elseif($proposal->type === 'parameter_change')
                                    تغيير معاملات
                                @elseif($proposal->type === 'membership')
                                    عضوية
                                @else
                                    أخرى
                                @endif
                            </span>
                        </div>
                        @if($proposal->amount_requested)
                        <div class="flex justify-between">
                            <span class="text-gray-600">المبلغ المطلوب:</span>
                            <span class="font-semibold">{{ number_format($proposal->amount_requested, 2) }} ETH</span>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-600">الناخب:</span>
                            <span class="font-semibold">{{ $proposal->proposer_id }}</span>
                        </div>
                    </div>

                    <!-- Voting Progress -->
                    <div class="mb-4">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600">التقدم</span>
                            <span class="font-semibold">
                                @if($proposal->votes_for + $proposal->votes_against + $proposal->votes_abstain > 0)
                                    {{ number_format(($proposal->votes_for / ($proposal->votes_for + $proposal->votes_against + $proposal->votes_abstain)) * 100, 1) }}%
                                @else
                                    0%
                                @endif
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: @if($proposal->votes_for + $proposal->votes_against + $proposal->votes_abstain > 0) {{ number_format(($proposal->votes_for / ($proposal->votes_for + $proposal->votes_against + $proposal->votes_abstain)) * 100, 1) }}% @else 0% @endif"></div>
                        </div>
                    </div>

                    <!-- Voting Results -->
                    <div class="flex justify-between text-sm mb-4">
                        <div class="flex items-center">
                            <span class="text-green-600 mr-1">👍</span>
                            <span class="font-semibold">{{ $proposal->votes_for }}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-red-600 mr-1">👎</span>
                            <span class="font-semibold">{{ $proposal->votes_against }}</span>
                        </div>
                        <div class="flex items-center">
                            <span class="text-gray-600 mr-1">🤷</span>
                            <span class="font-semibold">{{ $proposal->votes_abstain }}</span>
                        </div>
                    </div>

                    <!-- Voting Dates -->
                    <div class="flex justify-between text-sm text-gray-600 mb-4">
                        <div>
                            <span>📅 البداية:</span>
                            <span class="font-medium">{{ is_string($proposal->voting_starts_at) ? $proposal->voting_starts_at : $proposal->voting_starts_at->format('Y-m-d') }}</span>
                        </div>
                        <div>
                            <span>📅 النهاية:</span>
                            <span class="font-medium">{{ is_string($proposal->voting_ends_at) ? $proposal->voting_ends_at : $proposal->voting_ends_at->format('Y-m-d') }}</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex space-x-2 space-x-reverse">
                        <a href="{{ route('blockchain.dao.proposals.show', [$dao->id, $proposal->id]) }}" class="flex-1 bg-blue-600 text-white py-2 rounded-xl hover:bg-blue-700 transition-colors duration-200 font-medium text-sm text-center">
                            <i class="fas fa-eye ml-2"></i>
                            عرض
                        </a>
                        @if($proposal->status === 'active')
                        <a href="{{ route('blockchain.dao.vote', [$dao->id, $proposal->id]) }}" class="flex-1 bg-green-600 text-white py-2 rounded-xl hover:bg-green-700 transition-colors duration-200 font-medium text-sm text-center">
                            <i class="fas fa-vote-yea ml-2"></i>
                            تصويت
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="col-span-full">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                    <div class="text-gray-500">
                        <i class="fas fa-file-alt text-4xl mb-4"></i>
                        <p class="text-lg font-medium mb-2">لا توجد مقترحات حالياً</p>
                        <p class="text-sm">ابدأ بإنشاء مقترحات جديدة للمنظمة</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    <div class="flex justify-center mt-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 px-4 py-3">
            <p class="text-sm text-gray-600">
                عرض {{ $dao->proposals->count() }} مقترح من إجمالي {{ $dao->proposals->count() }}
            </p>
        </div>
    </div>
</div>
@endsection
