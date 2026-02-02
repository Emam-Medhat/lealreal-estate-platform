@extends('admin.layouts.admin')

@section('title', 'تفاصيل المنظمة - ' . $dao->name)

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-4 space-x-reverse">
                <a href="{{ route('blockchain.dao.index') }}" class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                    <i class="fas fa-arrow-right"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $dao->name }}</h1>
                    <p class="text-gray-600">{{ $dao->purpose }}</p>
                </div>
            </div>
            
            <div class="flex gap-2">
                @if($dao->status === 'active')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <span class="w-2 h-2 bg-green-400 rounded-full ml-2"></span>
                        نشط
                    </span>
                @elseif($dao->status === 'inactive')
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        غير نشط
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        مقترح
                    </span>
                @endif
                
                <button class="bg-blue-600 text-white px-6 py-3 rounded-xl hover:bg-blue-700 transition-colors duration-200 font-medium">
                    <i class="fas fa-edit ml-2"></i>
                    تعديل
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- DAO Overview -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">نظرة عامة</h2>
                <div class="space-y-4">
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-2">الوصف</h3>
                        <p class="text-gray-600">{{ $dao->description ?: 'لا يوجد وصف متاح' }}</p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">الرمز</h4>
                            <p class="text-gray-600">{{ $dao->token_symbol }}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">العنوان</h4>
                            <p class="text-gray-600 font-mono text-sm">{{ $dao->contract_address ?: 'غير متوفر' }}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">البروتوكول</h4>
                            <p class="text-gray-600">{{ $dao->protocol ?? 'غير محدد' }}</p>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-900 mb-2">المنشئ</h4>
                            <p class="text-gray-600">{{ $dao->creator ? $dao->creator->name : 'غير معروف' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Governance Settings -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">إعدادات الحوكمة</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">النصاب</h4>
                        <p class="text-gray-600">{{ number_format($dao->quorum, 2) }}%</p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">فترة التصويت</h4>
                        <p class="text-gray-600">{{ $dao->voting_period }} يوم</p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">إجمالي العرض</h4>
                        <p class="text-gray-600">{{ number_format($dao->total_supply, 2) }} {{ $dao->token_symbol }}</p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">قوة التصويت</h4>
                        <p class="text-gray-600">{{ number_format($dao->voting_power, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Recent Proposals -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-gray-900">المقترحات الأخيرة</h2>
                    <a href="{{ route('blockchain.dao.proposals', $dao->id) }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                        عرض الكل
                        <i class="fas fa-arrow-left mr-1"></i>
                    </a>
                </div>
                
                @if($dao->proposals->count() > 0)
                    <div class="space-y-4">
                        @foreach($dao->proposals->take(3) as $proposal)
                        <div class="border border-gray-200 rounded-xl p-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $proposal->title }}</h3>
                                    <p class="text-gray-600 text-sm mt-1">{{ Str::limit($proposal->description, 100) }}</p>
                                </div>
                                @if($proposal->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        نشط
                                    </span>
                                @elseif($proposal->status === 'passed')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        تم الموافقة
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ $proposal->status }}
                                    </span>
                                @endif
                            </div>
                            
                            <div class="flex items-center gap-4 mt-3 text-sm text-gray-600">
                                <span>👍 {{ $proposal->votes_for }}</span>
                                <span>👎 {{ $proposal->votes_against }}</span>
                                <span>🤷 {{ $proposal->votes_abstain }}</span>
                                <span>📅 {{ is_string($proposal->voting_ends_at) ? $proposal->voting_ends_at : $proposal->voting_ends_at->format('Y-m-d') }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-4"></i>
                        <p>لا توجد مقترحات حالياً</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Right Column - Stats & Actions -->
        <div class="space-y-6">
            <!-- Statistics -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">الإحصائيات</h2>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">الأعضاء</span>
                        <span class="font-semibold">{{ $dao->members->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">المقترحات</span>
                        <span class="font-semibold">{{ $dao->proposals->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">المقترحات النشطة</span>
                        <span class="font-semibold">{{ $dao->proposals->where('status', 'active')->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">المقترحات المنفذة</span>
                        <span class="font-semibold">{{ $dao->proposals->where('status', 'executed')->count() }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">إجراءات سريعة</h2>
                <div class="space-y-3">
                    <a href="{{ route('blockchain.dao.members', $dao->id) }}" class="w-full bg-blue-600 text-white py-3 rounded-xl hover:bg-blue-700 transition-colors duration-200 font-medium text-center block">
                        <i class="fas fa-users ml-2"></i>
                        إدارة الأعضاء
                    </a>
                    <a href="{{ route('blockchain.dao.proposals', $dao->id) }}" class="w-full bg-green-600 text-white py-3 rounded-xl hover:bg-green-700 transition-colors duration-200 font-medium text-center block">
                        <i class="fas fa-file-alt ml-2"></i>
                        عرض المقترحات
                    </a>
                    <a href="{{ route('blockchain.dao.vote', $dao->id) }}" class="w-full bg-purple-600 text-white py-3 rounded-xl hover:bg-purple-700 transition-colors duration-200 font-medium text-center block">
                        <i class="fas fa-vote-yea ml-2"></i>
                        التصويت
                    </a>
                    <a href="{{ route('blockchain.dao.treasury', $dao->id) }}" class="w-full bg-orange-600 text-white py-3 rounded-xl hover:bg-orange-700 transition-colors duration-200 font-medium text-center block">
                        <i class="fas fa-vault ml-2"></i>
                        الخزينة
                    </a>
                </div>
            </div>

            <!-- Links -->
            @if($dao->contract_address || $dao->social_links)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">روابط</h2>
                <div class="space-y-3">
                    @if($dao->contract_address)
                    <a href="https://etherscan.io/address/{{ $dao->contract_address }}" target="_blank" class="flex items-center text-blue-600 hover:text-blue-700">
                        <i class="fas fa-external-link-alt ml-2"></i>
                        Ethereum Explorer
                    </a>
                    @endif
                    
                    @if($dao->social_links)
                        @if(isset($dao->social_links['website']))
                        <a href="{{ $dao->social_links['website'] }}" target="_blank" class="flex items-center text-blue-600 hover:text-blue-700">
                            <i class="fas fa-globe ml-2"></i>
                            الموقع الرسمي
                        </a>
                        @endif
                        
                        @if(isset($dao->social_links['twitter']))
                        <a href="{{ $dao->social_links['twitter'] }}" target="_blank" class="flex items-center text-blue-600 hover:text-blue-700">
                            <i class="fab fa-twitter ml-2"></i>
                            Twitter
                        </a>
                        @endif
                        
                        @if(isset($dao->social_links['discord']))
                        <a href="{{ $dao->social_links['discord'] }}" target="_blank" class="flex items-center text-blue-600 hover:text-blue-700">
                            <i class="fab fa-discord ml-2"></i>
                            Discord
                        </a>
                        @endif
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
