@extends('layouts.app')

@section('title', 'تفاصيل نتيجة المزاد - ' . $result->auction->title)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 py-8">
    <div class="container mx-auto px-4">
        <!-- Breadcrumb -->
        <nav class="flex mb-8 text-gray-500 text-sm">
            <a href="{{ route('auctions.index') }}" class="hover:text-blue-600">المزادات</a>
            <span class="mx-2">/</span>
            <a href="{{ route('auctions.results') }}" class="hover:text-blue-600">النتائج</a>
            <span class="mx-2">/</span>
            <span class="text-gray-800">{{ $result->auction->title }}</span>
        </nav>

        <div class="bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 overflow-hidden mb-8">
            <div class="relative h-64 bg-gray-900">
                @if($result->auction->property->images && $result->auction->property->images->count() > 0)
                    <img src="{{ asset('storage/' . $result->auction->property->images->first()->path) }}" alt="{{ $result->auction->title }}" class="w-full h-full object-cover opacity-50">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-gray-800 text-gray-600">
                        <i class="fas fa-image text-6xl"></i>
                    </div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                <div class="absolute bottom-0 left-0 right-0 p-8">
                    <div class="flex items-end justify-between">
                        <div>
                            <span class="bg-emerald-500 text-white px-3 py-1 rounded-full text-sm font-bold mb-3 inline-block">
                                <i class="fas fa-check-circle ml-1"></i> مكتمل
                            </span>
                            <h1 class="text-4xl font-black text-white mb-2">{{ $result->auction->title }}</h1>
                            <p class="text-gray-300 text-lg flex items-center">
                                <i class="fas fa-map-marker-alt ml-2 text-red-400"></i>
                                {{ $result->auction->property->address }}
                            </p>
                        </div>
                        <div class="text-left text-white">
                            <p class="text-sm opacity-75">تاريخ الانتهاء</p>
                            <p class="text-xl font-bold">{{ $result->completed_at ? $result->completed_at->format('Y-m-d H:i') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-0">
                <!-- Main Info -->
                <div class="lg:col-span-2 p-8 border-l border-gray-100">
                    <!-- Winner Section -->
                    <div class="bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl p-6 mb-8 border border-emerald-100">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-20 h-20 bg-emerald-200 rounded-full flex items-center justify-center border-4 border-white shadow-lg">
                                    <i class="fas fa-trophy text-emerald-600 text-3xl"></i>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-800">الفائز بالمزاد</h2>
                                    <p class="text-emerald-700 font-medium text-lg">{{ $result->winner->name ?? 'غير معروف' }}</p>
                                </div>
                            </div>
                            <div class="text-left">
                                <p class="text-gray-500 text-sm mb-1">السعر النهائي</p>
                                <p class="text-4xl font-black text-emerald-600">{{ number_format($result->final_price, 2) }} <span class="text-lg font-medium text-emerald-500">ريال</span></p>
                            </div>
                        </div>
                        
                        @if(auth()->id() == $result->winner_id)
                            <div class="mt-6 pt-6 border-t border-emerald-100 flex justify-between items-center">
                                <p class="text-emerald-800 font-medium">
                                    <i class="fas fa-info-circle ml-2"></i>
                                    تهانينا! لقد فزت بهذا المزاد. يرجى مراجعة العقد للمتابعة.
                                </p>
                                @if($result->contract)
                                    <a href="/contracts/{{ $result->contract->id }}" class="px-6 py-2 bg-emerald-600 text-white rounded-xl hover:bg-emerald-700 transition-colors font-bold shadow-lg shadow-emerald-200">
                                        عرض العقد
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Statistics Grid -->
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-pie ml-2 text-blue-500"></i>
                        إحصائيات المزاد
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <p class="text-gray-500 text-sm mb-1">عدد المشاركين</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $participantStats['total_participants'] }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <p class="text-gray-500 text-sm mb-1">عدد المزايدات</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $participantStats['total_bids'] }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <p class="text-gray-500 text-sm mb-1">متوسط المزايدات</p>
                            <p class="text-2xl font-bold text-gray-800">{{ $participantStats['average_bids_per_participant'] }}</p>
                        </div>
                        <div class="bg-gray-50 rounded-xl p-4 text-center">
                            <p class="text-gray-500 text-sm mb-1">زيادة السعر</p>
                            <p class="text-2xl font-bold text-green-600">
                                {{ number_format((($result->final_price - $result->auction->starting_price) / $result->auction->starting_price) * 100, 1) }}%
                            </p>
                        </div>
                    </div>

                    <!-- Bid History -->
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-history ml-2 text-purple-500"></i>
                        سجل المزايدات
                    </h3>
                    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-500">المزايد</th>
                                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-500">المبلغ</th>
                                        <th class="px-6 py-4 text-right text-sm font-bold text-gray-500">التوقيت</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($bidHistory as $bid)
                                    <tr class="hover:bg-blue-50/50 transition-colors {{ $bid->user_id == $result->winner_id ? 'bg-emerald-50/30' : '' }}">
                                        <td class="px-6 py-4 flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">
                                                {{ strtoupper(substr($bid->user->name, 0, 1)) }}
                                            </div>
                                            <span class="font-medium {{ $bid->user_id == $result->winner_id ? 'text-emerald-700' : 'text-gray-700' }}">
                                                {{ $bid->user->name }}
                                                @if($bid->user_id == $result->winner_id)
                                                    <i class="fas fa-crown text-amber-400 mr-2"></i>
                                                @endif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 font-bold text-gray-800">{{ number_format($bid->amount, 2) }}</td>
                                        <td class="px-6 py-4 text-gray-500 text-sm">{{ $bid->created_at->diffForHumans() }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Info -->
                <div class="p-8 bg-gray-50/50">
                    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">تفاصيل العقار</h3>
                        <div class="space-y-4">
                            <div>
                                <p class="text-gray-500 text-sm">العنوان</p>
                                <p class="font-medium text-gray-800">{{ $result->auction->property->address }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">النوع</p>
                                <p class="font-medium text-gray-800">{{ $result->auction->property->type }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">المساحة</p>
                                <p class="font-medium text-gray-800">{{ $result->auction->property->area }} م²</p>
                            </div>
                            <div>
                                <p class="text-gray-500 text-sm">الوصف</p>
                                <p class="text-gray-600 text-sm leading-relaxed mt-1">{{ Str::limit($result->auction->property->description, 150) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">معلومات إضافية</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-500 text-sm">تاريخ البدء</span>
                                <span class="font-medium text-sm">{{ $result->auction->start_time->format('Y-m-d') }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-500 text-sm">السعر الابتدائي</span>
                                <span class="font-medium text-sm">{{ number_format($result->auction->starting_price, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-500 text-sm">الحالة</span>
                                <span class="px-2 py-1 bg-gray-100 rounded text-xs font-bold text-gray-600">{{ $result->status }}</span>
                            </div>
                        </div>
                        
                        <div class="mt-6 pt-6 border-t border-gray-100">
                            <a href="{{ route('auctions.show', $result->auction_id) }}" class="block w-full py-3 bg-white border-2 border-gray-200 text-gray-700 rounded-xl hover:border-blue-500 hover:text-blue-600 transition-all text-center font-bold">
                                عرض صفحة المزاد الأصلية
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
