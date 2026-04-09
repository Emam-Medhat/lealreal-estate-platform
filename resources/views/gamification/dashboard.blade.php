@extends('admin.layouts.hub')

@section('title', 'لوحة تحكم الألعاب العقارية')

@section('content')
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    corePlugins: {
      preflight: false,
    }
  }
</script>
<div class="container mx-auto px-4 py-6">
    <!-- Page Header -->
    <div class="mb-6">
        <div class="bg-gradient-to-r from-purple-600 via-pink-500 to-orange-400 rounded-xl shadow-xl p-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <div class="flex items-center mb-3">
                        <div class="bg-white bg-opacity-25 rounded-lg p-3 ml-3 backdrop-blur-sm border border-white border-opacity-20">
                            <i class="fas fa-trophy text-yellow-300 text-2xl drop-shadow-lg"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-white drop-shadow-md">لوحة تحكم الألعاب العقارية</h1>
                            <p class="text-white text-sm opacity-95 drop-shadow">تتبع تقدمك وإدارة إنجازاتك</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="bg-white bg-opacity-25 rounded-lg px-4 py-2 backdrop-blur-sm border border-white border-opacity-20">
                            <span class="text-xs text-yellow-300 font-medium">المستوى:</span>
                            <span class="text-sm font-bold text-white mr-1" id="current-level">1</span>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-lg px-4 py-2 backdrop-blur-sm border border-white border-opacity-20">
                            <span class="text-xs text-cyan-300 font-medium">النقاط:</span>
                            <span class="text-sm font-bold text-white mr-1" id="total-points">0</span>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-lg px-4 py-2 backdrop-blur-sm border border-white border-opacity-20">
                            <span class="text-xs text-green-300 font-medium">الشارات:</span>
                            <span class="text-sm font-bold text-white mr-1" id="badges-earned">0</span>
                        </div>
                    </div>
                </div>
                <div class="flex space-x-reverse space-x-2">
                    <button class="inline-flex items-center px-4 py-2 bg-white bg-opacity-90 text-purple-600 rounded-lg text-xs font-medium hover:bg-opacity-100 transition-all transform hover:scale-105 shadow-lg">
                        <i class="fas fa-sync-alt ml-1"></i>
                        تحديث
                    </button>
                    <button class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-yellow-400 to-orange-500 text-white rounded-lg text-xs font-medium hover:from-yellow-500 hover:to-orange-600 transition-all transform hover:scale-105 shadow-lg">
                        <i class="fas fa-download ml-1"></i>
                        تصدير
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- User Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <!-- Total Points -->
        <div class="bg-gradient-to-br from-blue-50 to-cyan-50 rounded-xl shadow-lg border border-blue-200 p-4 hover:shadow-xl transition-all transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-700 text-xs font-semibold mb-1">نقاطي الإجمالية</p>
                    <p class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-cyan-600 bg-clip-text text-transparent" id="total-points-display">0</p>
                    <div class="mt-2 flex items-center text-xs text-blue-600">
                        <i class="fas fa-coins text-yellow-500 ml-1"></i>
                        <span>نقطة</span>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-blue-100 to-cyan-100 rounded-xl p-3 border border-blue-300">
                    <i class="fas fa-coins text-yellow-500 text-2xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Current Level -->
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl shadow-lg border border-green-200 p-4 hover:shadow-xl transition-all transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-700 text-xs font-semibold mb-1">المستوى الحالي</p>
                    <p class="text-3xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent" id="current-level-display">1</p>
                    <div class="mt-2 flex items-center text-xs text-green-600">
                        <i class="fas fa-level-up-alt text-green-500 ml-1"></i>
                        <span>مستوى</span>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-green-100 to-emerald-100 rounded-xl p-3 border border-green-300">
                    <i class="fas fa-level-up-alt text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Badges Earned -->
        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl shadow-lg border border-purple-200 p-4 hover:shadow-xl transition-all transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-700 text-xs font-semibold mb-1">الشارات المكتسبة</p>
                    <p class="text-3xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent" id="badges-earned-display">0</p>
                    <div class="mt-2 flex items-center text-xs text-purple-600">
                        <i class="fas fa-medal text-yellow-500 ml-1"></i>
                        <span>شارة</span>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-purple-100 to-pink-100 rounded-xl p-3 border border-purple-300">
                    <i class="fas fa-medal text-yellow-500 text-2xl"></i>
                </div>
            </div>
        </div>
        
        <!-- Current Streak -->
        <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl shadow-lg border border-orange-200 p-4 hover:shadow-xl transition-all transform hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-700 text-xs font-semibold mb-1">السلسلة الحالية</p>
                    <p class="text-3xl font-bold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent" id="current-streak-display">0</p>
                    <div class="mt-2 flex items-center text-xs text-orange-600">
                        <i class="fas fa-fire text-orange-500 ml-1"></i>
                        <span>يوم</span>
                    </div>
                </div>
                <div class="bg-gradient-to-br from-orange-100 to-red-100 rounded-xl p-3 border border-orange-300">
                    <i class="fas fa-fire text-orange-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
        <!-- Level Progress -->
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl shadow-lg border border-green-200 p-4">
            <h3 class="text-sm font-bold text-green-800 mb-4 flex items-center">
                <i class="fas fa-chart-line text-green-600 ml-2 text-lg"></i>
                تقدم المستوى
            </h3>
            <div>
                <div class="flex justify-between text-xs text-green-700 mb-2">
                    <span class="font-medium">التقدم للمستوى التالي</span>
                    <span class="font-bold text-green-600" id="level-progress-text">0%</span>
                </div>
                <div class="w-full bg-green-100 rounded-full h-3 overflow-hidden shadow-inner">
                    <div class="bg-gradient-to-r from-green-500 via-emerald-500 to-teal-600 h-3 rounded-full transition-all duration-1000 shadow-lg" id="level-progress-bar" style="width: 0%"></div>
                </div>
                <p class="text-xs text-green-700 mt-2 font-medium">نقاط الخبرة: <span class="font-bold text-green-600" id="exp-current">0</span> / <span class="font-bold text-green-800" id="exp-required">100</span></p>
            </div>
        </div>

        <!-- Upcoming Achievements -->
        <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-xl shadow-lg border border-purple-200 p-4">
            <h3 class="text-sm font-bold text-purple-800 mb-4 flex items-center">
                <i class="fas fa-trophy text-purple-600 ml-2 text-lg"></i>
                الإنجازات القادمة
            </h3>
            <div class="space-y-2">
                <div class="flex items-center justify-between p-3 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-lg border border-blue-200">
                    <span class="text-xs font-semibold text-blue-700">التحديات</span>
                    <span class="text-xs font-bold bg-gradient-to-r from-blue-500 to-cyan-500 text-white px-3 py-1 rounded-full shadow-md" id="challenges-completed">0</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg border border-green-200">
                    <span class="text-xs font-semibold text-green-700">المهام</span>
                    <span class="text-xs font-bold bg-gradient-to-r from-green-500 to-emerald-500 text-white px-3 py-1 rounded-full shadow-md" id="quests-completed">0</span>
                </div>
                <div class="flex items-center justify-between p-3 bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg border border-purple-200">
                    <span class="text-xs font-semibold text-purple-700">الإنجازات</span>
                    <span class="text-xs font-bold bg-gradient-to-r from-purple-500 to-pink-500 text-white px-3 py-1 rounded-full shadow-md" id="achievements-unlocked">0</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-xl shadow-lg border border-indigo-200 p-4 mb-6">
        <h3 class="text-sm font-bold text-indigo-800 mb-4 flex items-center">
            <i class="fas fa-bolt text-indigo-600 ml-2 text-lg"></i>
            إجراءات سريعة
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <button class="flex flex-col items-center justify-center p-4 bg-gradient-to-br from-blue-50 to-cyan-50 rounded-lg hover:from-blue-100 hover:to-cyan-100 transition-all transform hover:-translate-y-1 border border-blue-200 shadow-md hover:shadow-lg">
                <i class="fas fa-coins text-yellow-500 text-xl mb-2"></i>
                <span class="text-xs font-semibold text-blue-700">كسب نقاط</span>
            </button>
            <button class="flex flex-col items-center justify-center p-4 bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg hover:from-green-100 hover:to-emerald-100 transition-all transform hover:-translate-y-1 border border-green-200 shadow-md hover:shadow-lg">
                <i class="fas fa-medal text-yellow-500 text-xl mb-2"></i>
                <span class="text-xs font-semibold text-green-700">الشارات</span>
            </button>
            <button class="flex flex-col items-center justify-center p-4 bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg hover:from-purple-100 hover:to-pink-100 transition-all transform hover:-translate-y-1 border border-purple-200 shadow-md hover:shadow-lg">
                <i class="fas fa-tasks text-purple-600 text-xl mb-2"></i>
                <span class="text-xs font-semibold text-purple-700">التحديات</span>
            </button>
            <button class="flex flex-col items-center justify-center p-4 bg-gradient-to-br from-orange-50 to-red-50 rounded-lg hover:from-orange-100 hover:to-red-100 transition-all transform hover:-translate-y-1 border border-orange-200 shadow-md hover:shadow-lg">
                <i class="fas fa-gift text-orange-600 text-xl mb-2"></i>
                <span class="text-xs font-semibold text-orange-700">المكافآت</span>
            </button>
        </div>
    </div>

    <!-- Recent Activity & Leaderboard -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Recent Activity -->
        <div class="lg:col-span-2">
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl shadow-lg border border-blue-200 p-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-sm font-bold text-blue-800 flex items-center">
                        <i class="fas fa-history text-blue-600 ml-2 text-lg"></i>
                        النشاط الحديث
                    </h3>
                    <div class="flex space-x-reverse space-x-1">
                        <button class="px-3 py-1 bg-gradient-to-r from-blue-500 to-indigo-500 text-white rounded-lg text-xs font-medium shadow-md">الكل</button>
                        <button class="px-3 py-1 bg-white text-blue-600 rounded-lg text-xs font-medium hover:bg-blue-50 transition-colors border border-blue-200">نقاط</button>
                        <button class="px-3 py-1 bg-white text-purple-600 rounded-lg text-xs font-medium hover:bg-purple-50 transition-colors border border-purple-200">شارات</button>
                        <button class="px-3 py-1 bg-white text-orange-600 rounded-lg text-xs font-medium hover:bg-orange-50 transition-colors border border-orange-200">تحديات</button>
                    </div>
                </div>
                <div class="space-y-2" id="recent-activity">
                    <!-- Activity items will be loaded here -->
                    <div class="text-center py-8">
                        <i class="fas fa-history text-4xl text-blue-400 mb-3"></i>
                        <p class="text-blue-600 text-sm font-semibold">لا يوجد نشاط حديث</p>
                        <p class="text-blue-500 text-xs mt-1">ابدأ في إكمال التحديات لكسب الإنجازات!</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leaderboard -->
        <div class="lg:col-span-1">
            <div class="bg-gradient-to-br from-orange-50 to-red-50 rounded-xl shadow-lg border border-orange-200 p-4">
                <h3 class="text-sm font-bold text-orange-800 mb-4 flex items-center">
                    <i class="fas fa-chart-bar text-orange-600 ml-2 text-lg"></i>
                    لوحة المتصدرين
                </h3>
                <div class="space-y-2" id="leaderboard">
                    <!-- Leaderboard will be loaded here -->
                    <div class="text-center py-8">
                        <i class="fas fa-chart-bar text-4xl text-orange-400 mb-3"></i>
                        <p class="text-orange-600 text-sm font-semibold">لا توجد بيانات</p>
                        <p class="text-orange-500 text-xs mt-1">ستظهر هنا أفضل اللاعبين!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Points Modal -->
<div class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50" id="pointsModal">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md transform scale-95 opacity-0 transition-all duration-300">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-800">كسب نقاط</h3>
            <button class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeModal('pointsModal')">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <form id="earn-points-form">
            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-700 mb-2">عدد النقاط</label>
                <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" min="1" max="1000" required>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-700 mb-2">السبب</label>
                <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm" rows="3" required></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-700 mb-2">النوع</label>
                <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    <option value="">اختر النوع</option>
                    <option value="achievement">إنجاز</option>
                    <option value="challenge">تحدي</option>
                    <option value="quest">مهمة</option>
                    <option value="bonus">مكافأة</option>
                </select>
            </div>
            <div class="flex space-x-reverse space-x-2">
                <button type="button" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors" onclick="closeModal('pointsModal')">إلغاء</button>
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-500 text-white rounded-lg text-sm font-medium hover:bg-blue-600 transition-colors">إرسال</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// Simplified JavaScript for dashboard functionality
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

// Simplified data loading
document.addEventListener('DOMContentLoaded', function() {
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
        
        // Update values
        animateValue('total-points', 0, data.totalPoints, 1500);
        animateValue('total-points-display', 0, data.totalPoints, 1500);
        animateValue('current-level', 0, data.currentLevel, 1000);
        animateValue('current-level-display', 0, data.currentLevel, 1000);
        animateValue('badges-earned', 0, data.badgesEarned, 1200);
        animateValue('badges-earned-display', 0, data.badgesEarned, 1200);
        animateValue('current-streak', 0, data.currentStreak, 1400);
        animateValue('current-streak-display', 0, data.currentStreak, 1400);
        
        // Update progress bar
        const progressBar = document.getElementById('level-progress-bar');
        const progressText = document.getElementById('level-progress-text');
        
        setTimeout(() => {
            progressBar.style.width = data.progress + '%';
            progressText.textContent = data.progress + '%';
        }, 300);
        
        // Update experience points
        document.getElementById('exp-current').textContent = data.expCurrent;
        document.getElementById('exp-required').textContent = data.expRequired;
        
        // Update achievements
        animateValue('challenges-completed', 0, data.challengesCompleted, 1600);
        animateValue('quests-completed', 0, data.questsCompleted, 1700);
        animateValue('achievements-unlocked', 0, data.achievementsUnlocked, 1800);
    }, 500);
});

// Simplified number animation
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
</script>
@endpush

@push('styles')
<style>
/* Enhanced styles with better colors and animations */
button:hover {
    transform: translateY(-2px);
    transition: all 0.3s ease;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

/* Smooth progress bar animation */
#level-progress-bar {
    transition: width 1.5s cubic-bezier(0.4, 0, 0.2, 1);
    background: linear-gradient(90deg, #10b981, #34d399, #6ee7b7);
    background-size: 200% 100%;
    animation: shimmer 2s linear infinite;
}

@keyframes shimmer {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Card hover effects */
.hover\:shadow-xl:hover {
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Gradient text effects */
.bg-clip-text {
    -webkit-background-clip: text;
    background-clip: text;
}

/* Enhanced button animations */
.transform:hover {
    transform: translateY(-2px) scale(1.02);
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #8b5cf6, #ec4899);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, #7c3aed, #db2777);
}
</style>
@endpush
@endsection
