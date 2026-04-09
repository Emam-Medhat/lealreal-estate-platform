@extends('admin.layouts.admin')

@section('title', 'مركز الألعاب والتحفيز')

@section('content')
<div class="flex">
    <!-- Sidebar -->
    <div class="w-64 min-h-screen bg-white border-l border-gray-200">
        <div class="p-6">
            <div class="flex items-center mb-6">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-3 ml-3">
                    <i class="fas fa-gamepad text-white text-xl"></i>
                </div>
                <h2 class="text-lg font-bold text-black">القائمة</h2>
            </div>
            
            <nav class="space-y-2">
                <a href="{{ route('gamification.index') }}" class="flex items-center p-3 rounded-lg bg-slate-100 text-black font-medium border border-slate-200">
                    <i class="fas fa-home ml-3 text-slate-600"></i>
                    الرئيسية
                </a>
                <a href="{{ route('gamification.achievements') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 text-black transition-colors border border-transparent hover:border-gray-200">
                    <i class="fas fa-trophy ml-3 text-blue-600"></i>
                    الإنجازات
                </a>
                <a href="{{ route('gamification.badges') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 text-black transition-colors border border-transparent hover:border-gray-200">
                    <i class="fas fa-medal ml-3 text-purple-600"></i>
                    الشارات
                </a>
                <a href="{{ route('gamification.challenges') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 text-black transition-colors border border-transparent hover:border-gray-200">
                    <i class="fas fa-gamepad ml-3 text-green-600"></i>
                    التحديات
                </a>
                <a href="{{ route('gamification.leaderboard') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 text-black transition-colors border border-transparent hover:border-gray-200">
                    <i class="fas fa-chart-bar ml-3 text-amber-600"></i>
                    لوحة الصدارة
                </a>
                <a href="{{ route('gamification.rewards') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-50 text-black transition-colors border border-transparent hover:border-gray-200">
                    <i class="fas fa-gift ml-3 text-red-600"></i>
                    المكافآت
                </a>
            </nav>
            
            <!-- User Stats in Sidebar -->
            <div class="mt-8 p-4 bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl border border-slate-200">
                <h3 class="text-sm font-bold text-black mb-3">إحصائياتك</h3>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-black">المستوى:</span>
                        <span class="text-black font-bold">{{ auth()->user()->gamificationLevel->level ?? 1 }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-black">النقاط:</span>
                        <span class="text-black font-bold">{{ auth()->user()->gamificationLevel->total_points ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-black">الإنجازات:</span>
                        <span class="text-black font-bold">{{ \App\Models\Gamification\UserAchievement::where('user_id', auth()->id())->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-slate-700 to-slate-900 rounded-2xl shadow-2xl p-8 text-white relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full -mr-32 -mt-32"></div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-white rounded-full -ml-24 -mb-24"></div>
                </div>
                
                <div class="relative z-10">
                    <div class="flex justify-between items-center">
                        <div>
                            <div class="flex items-center mb-3">
                                <div class="bg-white bg-opacity-20 rounded-xl p-3 ml-4 backdrop-blur-sm border border-white border-opacity-30">
                                    <i class="fas fa-gamepad text-white text-2xl"></i>
                                </div>
                                <div>
                                    <h1 class="text-4xl font-bold text-white mb-1">مركز الألعاب والتحفيز</h1>
                                    <p class="text-white text-lg">اكسب النقاط والإنجازات وتحدى الآخرين</p>
                                </div>
                            </div>
                            <div class="flex items-center space-x-4 mt-4">
                                <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2 backdrop-blur-sm border border-white border-opacity-30">
                                    <span class="text-sm text-white">مستواك:</span>
                                    <span class="text-sm font-bold text-white">{{ auth()->user()->gamificationLevel->level ?? 1 }}</span>
                                </div>
                                <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2 backdrop-blur-sm border border-white border-opacity-30">
                                    <span class="text-sm text-white">النقاط الكلية:</span>
                                    <span class="text-sm font-bold text-white">{{ auth()->user()->gamificationLevel->total_points ?? 0 }}</span>
                                </div>
                                <div class="bg-white bg-opacity-20 rounded-lg px-4 py-2 backdrop-blur-sm border border-white border-opacity-30">
                                    <span class="text-sm text-white">الإنجازات:</span>
                                    <span class="text-sm font-bold text-white">{{ \App\Models\Gamification\UserAchievement::where('user_id', auth()->id())->count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-reverse space-x-3">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 bg-white text-black rounded-xl text-sm font-medium hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-300 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-arrow-left ml-2"></i>
                                العودة للوحة
                            </a>
                            <button class="inline-flex items-center px-6 py-3 bg-slate-600 text-white rounded-xl text-sm font-medium hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-600 transition-all duration-300 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-trophy ml-2"></i>
                                عرض الإنجازات
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

<!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Points -->
            <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-300 p-6 transform hover:scale-105 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-blue-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-black text-sm font-bold mb-2">النقاط الكلية</p>
                            <p class="text-4xl font-black text-black">{{ auth()->user()->gamificationLevel->total_points ?? 0 }}</p>
                            <div class="mt-2 flex items-center text-sm text-black">
                                <i class="fas fa-star ml-1 text-blue-600"></i>
                                <span>جميع النقاط</span>
                            </div>
                        </div>
                        <div class="bg-blue-100 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 border-2 border-blue-200">
                            <i class="fas fa-star text-blue-700 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Achievements -->
            <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-300 p-6 transform hover:scale-105 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-green-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-black text-sm font-bold mb-2">الإنجازات</p>
                            <p class="text-4xl font-black text-black">{{ \App\Models\Gamification\UserAchievement::where('user_id', auth()->id())->count() }}</p>
                            <div class="mt-2 flex items-center text-sm text-black">
                                <i class="fas fa-trophy ml-1 text-green-600"></i>
                                <span>الإنجازات المكتملة</span>
                            </div>
                        </div>
                        <div class="bg-green-100 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 border-2 border-green-200">
                            <i class="fas fa-trophy text-green-700 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Badges -->
            <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-300 p-6 transform hover:scale-105 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-purple-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-black text-sm font-bold mb-2">الشارات</p>
                            <p class="text-4xl font-black text-black">{{ \App\Models\Gamification\UserBadge::where('user_id', auth()->id())->count() }}</p>
                            <div class="mt-2 flex items-center text-sm text-black">
                                <i class="fas fa-medal ml-1 text-purple-600"></i>
                                <span>الشارات المكتسبة</span>
                            </div>
                        </div>
                        <div class="bg-purple-100 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 border-2 border-purple-200">
                            <i class="fas fa-medal text-purple-700 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Active Challenges -->
            <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-300 p-6 transform hover:scale-105 transition-all duration-300 relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-br from-amber-50 to-amber-100 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-black text-sm font-bold mb-2">التحديات النشطة</p>
                            <p class="text-4xl font-black text-black">{{ \App\Models\Gamification\UserChallenge::where('user_id', auth()->id())->where('status', 'active')->count() }}</p>
                            <div class="mt-2 flex items-center text-sm text-black">
                                <i class="fas fa-gamepad ml-1 text-amber-600"></i>
                                <span>التحديات الجارية</span>
                            </div>
                        </div>
                        <div class="bg-amber-100 rounded-2xl p-4 group-hover:scale-110 transition-transform duration-300 border-2 border-amber-200">
                            <i class="fas fa-gamepad text-amber-700 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 mb-8">
            <h3 class="text-2xl font-bold text-black mb-6 flex items-center">
                <div class="bg-slate-600 rounded-lg p-2 ml-3 shadow-md">
                    <i class="fas fa-bolt text-white"></i>
                </div>
                إجراءات سريعة
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="{{ route('gamification.achievements') }}" class="flex items-center justify-center p-6 bg-blue-50 rounded-lg border border-blue-200 hover:bg-blue-100 transition-colors group">
                    <i class="fas fa-trophy text-blue-600 text-xl ml-3 group-hover:scale-110 transition-transform"></i>
                    <span class="text-black font-medium">الإنجازات</span>
                </a>
                <a href="{{ route('gamification.badges') }}" class="flex items-center justify-center p-6 bg-purple-50 rounded-lg border border-purple-200 hover:bg-purple-100 transition-colors group">
                    <i class="fas fa-medal text-purple-600 text-xl ml-3 group-hover:scale-110 transition-transform"></i>
                    <span class="text-black font-medium">شاراتي</span>
                </a>
                <a href="{{ route('gamification.challenges') }}" class="flex items-center justify-center p-6 bg-green-50 rounded-lg border border-green-200 hover:bg-green-100 transition-colors group">
                    <i class="fas fa-gamepad text-green-600 text-xl ml-3 group-hover:scale-110 transition-transform"></i>
                    <span class="text-black font-medium">التحديات</span>
                </a>
                <a href="{{ route('gamification.leaderboard') }}" class="flex items-center justify-center p-6 bg-amber-50 rounded-lg border border-amber-200 hover:bg-amber-100 transition-colors group">
                    <i class="fas fa-chart-bar text-amber-600 text-xl ml-3 group-hover:scale-110 transition-transform"></i>
                    <span class="text-black font-medium">لوحة الصدارة</span>
                </a>
            </div>
        </div>

        <!-- Recent Activity & Current Level -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Activity -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-black mb-6 flex items-center">
                        <div class="bg-slate-600 rounded-lg p-2 ml-3 shadow-md">
                            <i class="fas fa-history text-white"></i>
                        </div>
                        النشاط الحديث
                    </h3>
                    <div class="space-y-4">
                        @php
                            $recentAchievements = \App\Models\Gamification\UserAchievement::where('user_id', auth()->id())
                                ->with('achievement')
                                ->orderBy('awarded_at', 'desc')
                                ->limit(5)
                                ->get();
                        @endphp
                        
                        @if($recentAchievements->count() > 0)
                            @foreach($recentAchievements as $userAchievement)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200 hover:bg-gray-100 transition-colors">
                                <div class="flex items-center">
                                    <div class="bg-blue-100 rounded-lg p-2 ml-3">
                                        <i class="fas fa-trophy text-blue-600"></i>
                                    </div>
                                    <div>
                                        <h4 class="text-black font-medium">{{ $userAchievement->achievement->name }}</h4>
                                        <p class="text-black text-sm">{{ $userAchievement->achievement->description }}</p>
                                        <p class="text-black text-xs mt-1">
                                            <i class="fas fa-clock ml-1"></i>
                                            {{ $userAchievement->awarded_at->format('Y-m-d H:i') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-left">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-black border border-green-200">
                                        +{{ $userAchievement->points_awarded }} نقطة
                                    </span>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-history text-gray-400 text-4xl mb-4"></i>
                                <p class="text-black">لا يوجد نشاط حديث. ابدأ في إكمال التحديات لكسب الإنجازات!</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Current Level -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
                    <h3 class="text-lg font-semibold text-black mb-6 flex items-center">
                        <div class="bg-slate-600 rounded-lg p-2 ml-3 shadow-md">
                            <i class="fas fa-level-up-alt text-white"></i>
                        </div>
                        المستوى الحالي
                    </h3>
                    <div class="text-center">
                        @php
                            $userLevel = auth()->user()->gamificationLevel;
                            $currentLevel = $userLevel ? $userLevel->level : 1;
                            $currentPoints = $userLevel ? $userLevel->total_points : 0;
                        @endphp
                        
                        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-6 text-white mb-4">
                            <h2 class="text-3xl font-bold mb-2">المستوى {{ $currentLevel }}</h2>
                            <p class="text-xl">{{ $currentPoints }} نقطة</p>
                        </div>
                        
                        <div class="mb-4">
                            @php
                                $nextLevelPoints = ($currentLevel + 1) * 500;
                                $progress = min(($currentPoints / $nextLevelPoints) * 100, 100);
                            @endphp
                            <div class="flex justify-between text-sm text-black mb-2">
                                <span>التقدم للمستوى التالي</span>
                                <span>{{ number_format($progress, 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                            </div>
                            <p class="text-xs text-black mt-2">{{ $currentPoints }} / {{ $nextLevelPoints }} نقطة للمستوى التالي</p>
                        </div>
                        
                        <div class="space-y-2">
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <span class="text-black text-sm">النقاط اليوم</span>
                                <span class="text-black font-bold">+{{ rand(10, 50) }}</span>
                            </div>
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <span class="text-black text-sm">الإنجازات هذا الأسبوع</span>
                                <span class="text-black font-bold">{{ rand(1, 5) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
