@extends('admin.layouts.admin')

@section('title', 'لوحة تحكم الألعاب العقارية')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 rounded-3xl shadow-2xl p-8 text-white relative overflow-hidden">
            <!-- Animated Background Pattern -->
            <div class="absolute inset-0 opacity-20">
                <div class="absolute top-0 right-0 w-96 h-96 bg-white rounded-full -mr-48 -mt-48 animate-pulse"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-white rounded-full -ml-32 -mb-32 animate-pulse" style="animation-delay: 1s;"></div>
                <div class="absolute top-1/2 left-1/2 w-48 h-48 bg-yellow-300 rounded-full -ml-24 -mt-24 animate-pulse" style="animation-delay: 2s;"></div>
            </div>
            
            <!-- Grid Pattern Overlay -->
            <div class="absolute inset-0 opacity-10">
                <div class="h-full w-full" style="background-image: linear-gradient(rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 50px 50px;"></div>
            </div>
            
            <div class="relative z-10">
                <div class="flex justify-between items-center">
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="bg-white bg-opacity-25 rounded-2xl p-4 ml-4 backdrop-blur-sm border border-white border-opacity-40 shadow-xl transform hover:scale-105 transition-transform duration-300">
                                <i class="fas fa-trophy text-white text-3xl"></i>
                            </div>
                            <div>
                                <h1 class="text-5xl font-black text-white mb-2 bg-clip-text text-transparent bg-gradient-to-r from-white to-yellow-200">لوحة تحكم الألعاب العقارية</h1>
                                <p class="text-white text-xl font-medium">تتبع تقدمك وإدارة إنجازاتك بأسلوب احترافي</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-6 mt-6">
                            <div class="bg-white bg-opacity-25 rounded-xl px-6 py-3 backdrop-blur-sm border border-white border-opacity-40 shadow-lg">
                                <span class="text-sm text-white font-medium">المستوى:</span>
                                <span class="text-lg font-black text-white ml-2" id="current-level">1</span>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-xl px-6 py-3 backdrop-blur-sm border border-white border-opacity-40 shadow-lg">
                                <span class="text-sm text-white font-medium">النقاط:</span>
                                <span class="text-lg font-black text-white ml-2" id="total-points">0</span>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-xl px-6 py-3 backdrop-blur-sm border border-white border-opacity-40 shadow-lg">
                                <span class="text-sm text-white font-medium">الشارات:</span>
                                <span class="text-lg font-black text-white ml-2" id="badges-earned">0</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-reverse space-x-4">
                        <button class="inline-flex items-center px-8 py-4 bg-white text-indigo-600 rounded-2xl text-sm font-bold hover:bg-yellow-50 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-white transition-all duration-300 transform hover:scale-105 shadow-2xl hover:shadow-yellow-300/50">
                            <i class="fas fa-sync-alt ml-2"></i>
                            تحديث
                        </button>
                        <button class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-yellow-400 to-orange-500 text-white rounded-2xl text-sm font-bold hover:from-yellow-500 hover:to-orange-600 focus:outline-none focus:ring-4 focus:ring-offset-2 focus:ring-yellow-400 transition-all duration-300 transform hover:scale-105 shadow-2xl hover:shadow-orange-500/50">
                            <i class="fas fa-download ml-2"></i>
                            تصدير
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
        <!-- Total Points -->
        <div class="group relative">
            <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-cyan-500 rounded-3xl blur opacity-25 group-hover:opacity-75 transition duration-1000 group-hover:duration-200"></div>
            <div class="relative bg-white rounded-3xl shadow-2xl border-2 border-blue-200 p-8 transform hover:scale-105 transition-all duration-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-black text-sm font-bold mb-3">نقاطي الإجمالية</p>
                        <p class="text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500" id="total-points-display">0</p>
                        <div class="mt-3 flex items-center text-sm text-black">
                            <div class="bg-gradient-to-r from-blue-600 to-cyan-500 rounded-lg p-2 ml-2">
                                <i class="fas fa-coins text-white"></i>
                            </div>
                            <span class="font-medium">نقطة</span>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-3xl p-6 group-hover:rotate-12 transition-transform duration-500 border-2 border-blue-200">
                        <i class="fas fa-coins text-4xl text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Current Level -->
        <div class="group relative">
            <div class="absolute -inset-1 bg-gradient-to-r from-emerald-600 to-teal-500 rounded-3xl blur opacity-25 group-hover:opacity-75 transition duration-1000 group-hover:duration-200"></div>
            <div class="relative bg-white rounded-3xl shadow-2xl border-2 border-emerald-200 p-8 transform hover:scale-105 transition-all duration-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-black text-sm font-bold mb-3">المستوى الحالي</p>
                        <p class="text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-500" id="current-level-display">1</p>
                        <div class="mt-3 flex items-center text-sm text-black">
                            <div class="bg-gradient-to-r from-emerald-600 to-teal-500 rounded-lg p-2 ml-2">
                                <i class="fas fa-level-up-alt text-white"></i>
                            </div>
                            <span class="font-medium">مستوى</span>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-3xl p-6 group-hover:rotate-12 transition-transform duration-500 border-2 border-emerald-200">
                        <i class="fas fa-level-up-alt text-4xl text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-500"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Badges Earned -->
        <div class="group relative">
            <div class="absolute -inset-1 bg-gradient-to-r from-purple-600 to-pink-500 rounded-3xl blur opacity-25 group-hover:opacity-75 transition duration-1000 group-hover:duration-200"></div>
            <div class="relative bg-white rounded-3xl shadow-2xl border-2 border-purple-200 p-8 transform hover:scale-105 transition-all duration-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-black text-sm font-bold mb-3">الشارات المكتسبة</p>
                        <p class="text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-500" id="badges-earned-display">0</p>
                        <div class="mt-3 flex items-center text-sm text-black">
                            <div class="bg-gradient-to-r from-purple-600 to-pink-500 rounded-lg p-2 ml-2">
                                <i class="fas fa-medal text-white"></i>
                            </div>
                            <span class="font-medium">شارة</span>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-3xl p-6 group-hover:rotate-12 transition-transform duration-500 border-2 border-purple-200">
                        <i class="fas fa-medal text-4xl text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-500"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Current Streak -->
        <div class="group relative">
            <div class="absolute -inset-1 bg-gradient-to-r from-orange-600 to-red-500 rounded-3xl blur opacity-25 group-hover:opacity-75 transition duration-1000 group-hover:duration-200"></div>
            <div class="relative bg-white rounded-3xl shadow-2xl border-2 border-orange-200 p-8 transform hover:scale-105 transition-all duration-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-black text-sm font-bold mb-3">السلسلة الحالية</p>
                        <p class="text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-orange-600 to-red-500" id="current-streak-display">0</p>
                        <div class="mt-3 flex items-center text-sm text-black">
                            <div class="bg-gradient-to-r from-orange-600 to-red-500 rounded-lg p-2 ml-2">
                                <i class="fas fa-fire text-white"></i>
                            </div>
                            <span class="font-medium">يوم</span>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-3xl p-6 group-hover:rotate-12 transition-transform duration-500 border-2 border-orange-200">
                        <i class="fas fa-fire text-4xl text-transparent bg-clip-text bg-gradient-to-r from-orange-600 to-red-500"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
        <!-- Level Progress -->
        <div class="group relative">
            <div class="absolute -inset-1 bg-gradient-to-r from-emerald-600 to-teal-500 rounded-3xl blur opacity-25 group-hover:opacity-75 transition duration-1000 group-hover:duration-200"></div>
            <div class="relative bg-white rounded-3xl shadow-2xl border-2 border-emerald-200 p-8">
                <h3 class="text-2xl font-bold text-black mb-6 flex items-center">
                    <div class="bg-gradient-to-r from-emerald-600 to-teal-500 rounded-2xl p-3 ml-4 shadow-xl">
                        <i class="fas fa-chart-line text-white text-xl"></i>
                    </div>
                    تقدم المستوى
                </h3>
                <div class="mb-6">
                    <div class="flex justify-between text-sm text-black mb-3">
                        <span class="font-medium">التقدم للمستوى التالي</span>
                        <span class="font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-500" id="level-progress-text">0%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-6 overflow-hidden">
                        <div class="bg-gradient-to-r from-emerald-500 to-teal-600 h-6 rounded-full transition-all duration-1000 flex items-center justify-center" id="level-progress-bar" style="width: 0%">
                            <span class="text-white text-xs font-bold">0%</span>
                        </div>
                    </div>
                    <p class="text-sm text-black mt-3 font-medium">نقاط الخبرة: <span class="font-black text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-500" id="exp-current">0</span> / <span class="font-bold text-black" id="exp-required">100</span></p>
                </div>
            </div>
        </div>

        <!-- Upcoming Achievements -->
        <div class="group relative">
            <div class="absolute -inset-1 bg-gradient-to-r from-purple-600 to-pink-500 rounded-3xl blur opacity-25 group-hover:opacity-75 transition duration-1000 group-hover:duration-200"></div>
            <div class="relative bg-white rounded-3xl shadow-2xl border-2 border-purple-200 p-8">
                <h3 class="text-2xl font-bold text-black mb-6 flex items-center">
                    <div class="bg-gradient-to-r from-purple-600 to-pink-500 rounded-2xl p-3 ml-4 shadow-xl">
                        <i class="fas fa-trophy text-white text-xl"></i>
                    </div>
                    الإنجازات القادمة
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-2xl border-2 border-blue-200 hover:shadow-lg transition-shadow">
                        <span class="text-black font-bold">التحديات</span>
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-gradient-to-r from-blue-600 to-cyan-500 text-white border-2 border-blue-200" id="challenges-completed">0</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl border-2 border-emerald-200 hover:shadow-lg transition-shadow">
                        <span class="text-black font-bold">المهام</span>
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-gradient-to-r from-emerald-600 to-teal-500 text-white border-2 border-emerald-200" id="quests-completed">0</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-pink-50 rounded-2xl border-2 border-purple-200 hover:shadow-lg transition-shadow">
                        <span class="text-black font-bold">الإنجازات</span>
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-gradient-to-r from-purple-600 to-pink-500 text-white border-2 border-purple-200" id="achievements-unlocked">0</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="group relative mb-10">
        <div class="absolute -inset-1 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-3xl blur opacity-25 group-hover:opacity-75 transition duration-1000 group-hover:duration-200"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl border-2 border-indigo-200 p-10">
            <h3 class="text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 mb-8 flex items-center">
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-4 ml-4 shadow-xl">
                    <i class="fas fa-bolt text-white text-2xl"></i>
                </div>
                إجراءات سريعة
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <button class="group/action flex items-center justify-center p-8 bg-gradient-to-br from-blue-50 to-cyan-50 rounded-3xl border-2 border-blue-200 hover:from-blue-100 hover:to-cyan-100 transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                    <i class="fas fa-coins text-4xl text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-cyan-500 mb-3 group-hover/action:rotate-12 transition-transform duration-300"></i>
                    <span class="text-black font-bold">كسب نقاط</span>
                </button>
                <button class="group/action flex items-center justify-center p-8 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-3xl border-2 border-emerald-200 hover:from-emerald-100 hover:to-teal-100 transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                    <i class="fas fa-medal text-4xl text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-teal-500 mb-3 group-hover/action:rotate-12 transition-transform duration-300"></i>
                    <span class="text-black font-bold">الشارات</span>
                </button>
                <button class="group/action flex items-center justify-center p-8 bg-gradient-to-br from-purple-50 to-pink-50 rounded-3xl border-2 border-purple-200 hover:from-purple-100 hover:to-pink-100 transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                    <i class="fas fa-tasks text-4xl text-transparent bg-clip-text bg-gradient-to-r from-purple-600 to-pink-500 mb-3 group-hover/action:rotate-12 transition-transform duration-300"></i>
                    <span class="text-black font-bold">التحديات</span>
                </button>
                <button class="group/action flex items-center justify-center p-8 bg-gradient-to-br from-orange-50 to-red-50 rounded-3xl border-2 border-orange-200 hover:from-orange-100 hover:to-red-100 transition-all duration-300 transform hover:scale-105 hover:shadow-xl">
                    <i class="fas fa-gift text-4xl text-transparent bg-clip-text bg-gradient-to-r from-orange-600 to-red-500 mb-3 group-hover/action:rotate-12 transition-transform duration-300"></i>
                    <span class="text-black font-bold">المكافآت</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Leaderboard -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Activity -->
        <div class="lg:col-span-2">
            <div class="group relative">
                <div class="absolute -inset-1 bg-gradient-to-r from-blue-600 to-purple-600 rounded-3xl blur opacity-25 group-hover:opacity-75 transition duration-1000 group-hover:duration-200"></div>
                <div class="relative bg-white rounded-3xl shadow-2xl border-2 border-blue-200 p-8">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-black flex items-center">
                            <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-3 ml-4 shadow-xl">
                                <i class="fas fa-history text-white text-xl"></i>
                            </div>
                            النشاط الحديث
                        </h3>
                        <div class="flex space-x-reverse space-x-2">
                            <button class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl text-sm font-bold shadow-lg">الكل</button>
                            <button class="px-4 py-2 bg-gray-100 text-black rounded-xl text-sm font-bold hover:bg-gray-200 transition-colors">نقاط</button>
                            <button class="px-4 py-2 bg-gray-100 text-black rounded-xl text-sm font-bold hover:bg-gray-200 transition-colors">شارات</button>
                            <button class="px-4 py-2 bg-gray-100 text-black rounded-xl text-sm font-bold hover:bg-gray-200 transition-colors">تحديات</button>
                        </div>
                    </div>
                    <div class="space-y-4" id="recent-activity">
                        <!-- Activity items will be loaded here -->
                        <div class="text-center py-12">
                            <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-3xl p-8 border-2 border-blue-200">
                                <i class="fas fa-history text-6xl text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600 mb-4"></i>
                                <p class="text-black font-bold text-lg">لا يوجد نشاط حديث</p>
                                <p class="text-black text-sm mt-2">ابدأ في إكمال التحديات لكسب الإنجازات!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leaderboard -->
        <div class="lg:col-span-1">
            <div class="group relative">
                <div class="absolute -inset-1 bg-gradient-to-r from-orange-600 to-red-600 rounded-3xl blur opacity-25 group-hover:opacity-75 transition duration-1000 group-hover:duration-200"></div>
                <div class="relative bg-white rounded-3xl shadow-2xl border-2 border-orange-200 p-8">
                    <h3 class="text-2xl font-bold text-black mb-6 flex items-center">
                        <div class="bg-gradient-to-r from-orange-600 to-red-600 rounded-2xl p-3 ml-4 shadow-xl">
                            <i class="fas fa-chart-bar text-white text-xl"></i>
                        </div>
                        لوحة المتصدرين
                    </h3>
                    <div class="space-y-3" id="leaderboard">
                        <!-- Leaderboard will be loaded here -->
                        <div class="text-center py-12">
                            <div class="bg-gradient-to-r from-orange-50 to-red-50 rounded-3xl p-8 border-2 border-orange-200">
                                <i class="fas fa-chart-bar text-6xl text-transparent bg-clip-text bg-gradient-to-r from-orange-600 to-red-600 mb-4"></i>
                                <p class="text-black font-bold text-lg">لا توجد بيانات</p>
                                <p class="text-black text-sm mt-2">ستظهر هنا أفضل اللاعبين!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Points Modal -->
<div class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" id="pointsModal">
    <div class="bg-white rounded-3xl shadow-2xl p-8 w-full max-w-md transform scale-95 opacity-0 transition-all duration-300">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">كسب نقاط</h3>
            <button class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeModal('pointsModal')">
                <i class="fas fa-times text-2xl"></i>
            </button>
        </div>
        <form id="earn-points-form">
            <div class="mb-6">
                <label class="block text-sm font-bold text-black mb-3">عدد النقاط</label>
                <input type="number" class="w-full px-4 py-4 border-2 border-gray-300 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-500 focus:border-transparent bg-white text-black text-lg font-medium" min="1" max="1000" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-bold text-black mb-3">السبب</label>
                <textarea class="w-full px-4 py-4 border-2 border-gray-300 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-500 focus:border-transparent bg-white text-black text-lg font-medium" rows="4" required></textarea>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-bold text-black mb-3">النوع</label>
                <select class="w-full px-4 py-4 border-2 border-gray-300 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-500 focus:border-transparent bg-white text-black text-lg font-medium">
                    <option value="">اختر النوع</option>
                    <option value="achievement">إنجاز</option>
                    <option value="challenge">تحدي</option>
                    <option value="quest">مهمة</option>
                    <option value="bonus">مكافأة</option>
                </select>
            </div>
            <div class="flex space-x-reverse space-x-4">
                <button type="button" class="flex-1 px-6 py-4 bg-gray-100 text-black rounded-2xl font-bold hover:bg-gray-200 transition-colors transform hover:scale-105" onclick="closeModal('pointsModal')">إلغاء</button>
                <button type="submit" class="flex-1 px-6 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-2xl font-bold hover:from-blue-700 hover:to-purple-700 transition-all transform hover:scale-105 shadow-xl">إرسال</button>
            </div>
        </form>
    </div>
</div>
                            @endsection

<script>
// Enhanced JavaScript for dashboard functionality
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    const modalContent = modal.querySelector('div > div');
    modalContent.classList.add('scale-95', 'opacity-0');
    modalContent.classList.remove('scale-100', 'opacity-100');
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    const modalContent = modal.querySelector('div > div');
    setTimeout(() => {
        modalContent.classList.remove('scale-95', 'opacity-0');
        modalContent.classList.add('scale-100', 'opacity-100');
    }, 10);
}

// Enhanced data loading with animations
document.addEventListener('DOMContentLoaded', function() {
    // Simulate loading data with beautiful animations
    setTimeout(() => {
        const data = {
            totalPoints: 1250,
            currentLevel: 5,
            badgesEarned: 12,
            currentStreak: 7,
            progress: 65,
            expCurrent: 325,
            expRequired: 500,
            challengesCompleted: 8,
            questsCompleted: 15,
            achievementsUnlocked: 12
        };
        
        // Animate numbers
        animateValue('total-points', 0, data.totalPoints, 2000);
        animateValue('total-points-display', 0, data.totalPoints, 2000);
        animateValue('current-level', 0, data.currentLevel, 1500);
        animateValue('current-level-display', 0, data.currentLevel, 1500);
        animateValue('badges-earned', 0, data.badgesEarned, 1800);
        animateValue('badges-earned-display', 0, data.badgesEarned, 1800);
        animateValue('current-streak', 0, data.currentStreak, 2200);
        animateValue('current-streak-display', 0, data.currentStreak, 2200);
        
        // Update progress bar
        const progressBar = document.getElementById('level-progress-bar');
        const progressText = document.getElementById('level-progress-text');
        
        setTimeout(() => {
            progressBar.style.width = data.progress + '%';
            progressBar.querySelector('span').textContent = data.progress + '%';
            progressText.textContent = data.progress + '%';
        }, 500);
        
        // Update experience points
        document.getElementById('exp-current').textContent = data.expCurrent;
        document.getElementById('exp-required').textContent = data.expRequired;
        
        // Update achievements
        animateValue('challenges-completed', 0, data.challengesCompleted, 2500);
        animateValue('quests-completed', 0, data.questsCompleted, 2600);
        animateValue('achievements-unlocked', 0, data.achievementsUnlocked, 2700);
    }, 1000);
});

// Number animation function
function animateValue(id, start, end, duration) {
    const element = document.getElementById(id);
    const range = end - start;
    const increment = range / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
            element.textContent = end;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, 16);
}

// Add click handlers for quick action buttons
document.addEventListener('DOMContentLoaded', function() {
    const actionButtons = document.querySelectorAll('button');
    actionButtons.forEach(button => {
        if (button.textContent.includes('كسب نقاط') || 
            button.textContent.includes('الشارات') || 
            button.textContent.includes('التحديات') || 
            button.textContent.includes('المكافآت')) {
            button.addEventListener('click', function() {
                // Add ripple effect
                const ripple = document.createElement('span');
                ripple.classList.add('absolute', 'bg-white', 'opacity-30', 'rounded-full', 'animate-ping');
                ripple.style.width = '20px';
                ripple.style.height = '20px';
                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        }
    });
});
</script>

<style>
/* Custom animations and enhancements */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

@keyframes pulse-glow {
    0%, 100% { box-shadow: 0 0 20px rgba(99, 102, 241, 0.3); }
    50% { box-shadow: 0 0 40px rgba(99, 102, 241, 0.6); }
}

.group:hover .group-hover\\:rotate-12 {
    transform: rotate(12deg);
}

.group:hover .group-hover\\:scale-110 {
    transform: scale(1.1);
}

/* Enhanced progress bar */
#level-progress-bar {
    background: linear-gradient(90deg, #10b981 0%, #14b8a6 50%, #06b6d4 100%);
    background-size: 200% 100%;
    animation: shimmer 2s linear infinite;
}

@keyframes shimmer {
    0% { background-position: -200% 0; }
    100% { background-position: 200% 0; }
}

/* Modal enhancements */
#pointsModal.show {
    display: flex;
}

#pointsModal.show > div {
    transform: scale(1);
    opacity: 1;
}

/* Button hover effects */
button {
    position: relative;
    overflow: hidden;
}

button::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.5);
    transform: translate(-50%, -50%);
    transition: width 0.6s, height 0.6s;
}

button:hover::before {
    width: 300px;
    height: 300px;
}
</style>
