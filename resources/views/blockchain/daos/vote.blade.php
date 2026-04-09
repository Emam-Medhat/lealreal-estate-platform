@extends('admin.layouts.admin')

@section('title', 'التصويت - ' . $dao->name)

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
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">التصويت</h1>
                    <p class="text-gray-600">{{ $dao->name }} - مركز التصويت</p>
                </div>
            </div>
            
            <div class="flex gap-2">
                <a href="{{ route('blockchain.dao.proposals', $dao->id) }}" class="bg-green-600 text-white px-6 py-3 rounded-xl hover:bg-green-700 transition-colors duration-200 font-medium">
                    <i class="fas fa-file-alt ml-2"></i>
                    عرض المقترحات
                </a>
                <a href="{{ route('blockchain.dao.members', $dao->id) }}" class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors duration-200 font-medium">
                    <i class="fas fa-users ml-2"></i>
                    الأعضاء
                </a>
            </div>
        </div>
    </div>

    <!-- Voting Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
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

        <!-- Total Votes Cast -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium mb-1">إجمالي الأصوات</p>
                    <p class="text-3xl font-bold">{{ $dao->proposals->sum('votes_for') + $dao->proposals->sum('votes_against') + $dao->proposals->sum('votes_abstain') }}</p>
                    <p class="text-blue-100 text-xs mt-2">
                        <i class="fas fa-vote-yea ml-1"></i>
                        جميع الأصوات
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-vote-yea text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Your Voting Power -->
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium mb-1">قوة تصويتك</p>
                    <p class="text-3xl font-bold">1000.00</p>
                    <p class="text-purple-100 text-xs mt-2">
                        <i class="fas fa-bolt ml-1"></i>
                        {{ $dao->token_symbol }}
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-bolt text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Participation Rate -->
        <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl p-6 text-white shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-[1.02]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm font-medium mb-1">معدل المشاركة</p>
                    <p class="text-3xl font-bold">75.5%</p>
                    <p class="text-orange-100 text-xs mt-2">
                        <i class="fas fa-percentage ml-1"></i>
                        من الأعضاء
                    </p>
                </div>
                <div class="bg-white/20 rounded-full p-3">
                    <i class="fas fa-percentage text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Proposals for Voting -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">المقترحات المتاحة للتصويت</h2>
        
        @if($dao->proposals->where('status', 'active')->count() > 0)
            <div class="space-y-6">
                @foreach($dao->proposals->where('status', 'active') as $proposal)
                <div class="border border-gray-200 rounded-xl p-6 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="font-bold text-lg text-gray-900 mb-2">{{ $proposal->title }}</h3>
                            <p class="text-gray-600">{{ Str::limit($proposal->description, 150) }}</p>
                        </div>
                        <div class="flex items-center space-x-2 space-x-reverse">
                            @if($proposal->type === 'funding')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-coins ml-1"></i>
                                    تمويل
                                </span>
                            @elseif($proposal->type === 'parameter_change')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-cog ml-1"></i>
                                    تغيير معاملات
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <i class="fas fa-file-alt ml-1"></i>
                                    أخرى
                                </span>
                            @endif
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <span class="w-2 h-2 bg-yellow-400 rounded-full ml-2"></span>
                                ينتهي في {{ is_string($proposal->voting_ends_at) ? $proposal->voting_ends_at : $proposal->voting_ends_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>

                    @if($proposal->amount_requested)
                    <div class="mb-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">المبلغ المطلوب:</span>
                            <span class="font-semibold text-green-600">{{ number_format($proposal->amount_requested, 2) }} ETH</span>
                        </div>
                    </div>
                    @endif

                    <!-- Voting Progress -->
                    <div class="mb-4">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600">التقدم الحالي</span>
                            <span class="font-semibold">
                                @if($proposal->votes_for + $proposal->votes_against + $proposal->votes_abstain > 0)
                                    {{ number_format(($proposal->votes_for / ($proposal->votes_for + $proposal->votes_against + $proposal->votes_abstain)) * 100, 1) }}%
                                @else
                                    0%
                                @endif
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-3">
                            <div class="bg-green-500 h-3 rounded-full" style="width: @if($proposal->votes_for + $proposal->votes_against + $proposal->votes_abstain > 0) {{ number_format(($proposal->votes_for / ($proposal->votes_for + $proposal->votes_against + $proposal->votes_abstain)) * 100, 1) }}% @else 0% @endif"></div>
                        </div>
                    </div>

                    <!-- Current Voting Results -->
                    <div class="grid grid-cols-3 gap-4 mb-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $proposal->votes_for }}</div>
                            <div class="text-sm text-gray-600">موافق</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-red-600">{{ $proposal->votes_against }}</div>
                            <div class="text-sm text-gray-600">معارض</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-600">{{ $proposal->votes_abstain }}</div>
                            <div class="text-sm text-gray-600">ممتنع</div>
                        </div>
                    </div>

                    <!-- Voting Form -->
                    <form action="{{ route('blockchain.dao.proposals.vote', [$dao->id, $proposal->id]) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">اختر تصويتك:</label>
                            <div class="grid grid-cols-3 gap-4">
                                <label class="relative">
                                    <input type="radio" name="vote" value="for" class="peer sr-only" required>
                                    <div class="w-full p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-green-500 peer-checked:bg-green-50 hover:bg-gray-50 transition-colors duration-200">
                                        <div class="text-center">
                                            <i class="fas fa-thumbs-up text-green-600 text-xl mb-2"></i>
                                            <div class="font-semibold text-green-800">موافق</div>
                                            <div class="text-xs text-gray-600">دعم المقترح</div>
                                        </div>
                                    </div>
                                </label>
                                
                                <label class="relative">
                                    <input type="radio" name="vote" value="against" class="peer sr-only">
                                    <div class="w-full p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-red-500 peer-checked:bg-red-50 hover:bg-gray-50 transition-colors duration-200">
                                        <div class="text-center">
                                            <i class="fas fa-thumbs-down text-red-600 text-xl mb-2"></i>
                                            <div class="font-semibold text-red-800">معارض</div>
                                            <div class="text-xs text-gray-600">رفض المقترح</div>
                                        </div>
                                    </div>
                                </label>
                                
                                <label class="relative">
                                    <input type="radio" name="vote" value="abstain" class="peer sr-only">
                                    <div class="w-full p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-gray-500 peer-checked:bg-gray-50 hover:bg-gray-50 transition-colors duration-200">
                                        <div class="text-center">
                                            <i class="fas fa-minus text-gray-600 text-xl mb-2"></i>
                                            <div class="font-semibold text-gray-800">ممتنع</div>
                                            <div class="text-xs text-gray-600">حيادي</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">سبب التصويت (اختياري):</label>
                            <textarea id="reason" name="reason" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="اشرح سبب تصويتك..."></textarea>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600">
                                <i class="fas fa-info-circle ml-1"></i>
                                قوة تصويتك: 1000.00 {{ $dao->token_symbol }}
                            </div>
                            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors duration-200 font-medium">
                                <i class="fas fa-vote-yea ml-2"></i>
                                تأكيد التصويت
                            </button>
                        </div>
                    </form>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-vote-yea text-4xl mb-4"></i>
                <p class="text-lg font-medium mb-2">لا توجد مقترحات نشطة حالياً</p>
                <p class="text-sm">جميع المقترحات الحالية إما منتهية أو لم تتم الموافقة عليها بعد</p>
                <a href="{{ route('blockchain.dao.proposals', $dao->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 mt-4">
                    <i class="fas fa-file-alt ml-2"></i>
                    عرض جميع المقترحات
                </a>
            </div>
        @endif
    </div>

    <!-- Voting History -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">سجل تصويتك</h2>
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-history text-4xl mb-4"></i>
            <p class="text-lg font-medium mb-2">لا يوجد سجل تصويت</p>
            <p class="text-sm">سيظهر هنا سجل تصويتك في المقترحات المختلفة</p>
        </div>
    </div>
</div>
@endsection
