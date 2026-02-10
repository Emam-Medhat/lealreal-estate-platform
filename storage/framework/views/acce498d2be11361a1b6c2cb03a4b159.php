<?php $__env->startSection('title', 'لوحة النظام'); ?>
<?php $__env->startSection('page-title', 'لوحة النظام'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    /* Premium Dashboard Styles */
    .hero-dashboard {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 2rem;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
    }
    
    .hero-dashboard::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: float 8s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-30px) rotate(180deg); }
    }
    
    .stat-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 2px;
    }
    
    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 16px 48px rgba(0, 0, 0, 0.15);
    }
    
    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 1rem;
        position: relative;
        z-index: 2;
    }
    
    .stat-value {
        font-size: 2.5rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.5rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .system-health {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 20px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
    }
    
    .health-item {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 1rem;
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }
    
    .health-item:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: scale(1.05);
    }
    
    .progress-ring {
        width: 120px;
        height: 120px;
        position: relative;
    }
    
    .progress-ring svg {
        transform: rotate(-90deg);
    }
    
    .progress-ring circle {
        fill: none;
        stroke-width: 8;
        stroke-linecap: round;
        transition: stroke-dashoffset 0.5s ease;
    }
    
    .progress-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 1.5rem;
        font-weight: 700;
    }
    
    .activity-item {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
        margin-bottom: 1rem;
    }
    
    .activity-item:hover {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        transform: translateX(8px);
    }
    
    .pulse-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(102, 126, 234, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(102, 126, 234, 0); }
        100% { transform: section(0.95); box-shadow: 0 0 0 0 rgba(102, 126, 234, 0); }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-50">
    <div class="max-w-7xl mx-auto p-6">
        <!-- Hero Section -->
        <div class="hero-dashboard">
            <div class="relative z-10">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-4xl font-bold mb-3 flex items-center">
                            <i class="fas fa-tachometer-alt ml-3 mr-3"></i>
                            لوحة النظام
                        </h1>
                        <p class="text-xl opacity-90">مراقبة أداء النظام والحالة العامة</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <div class="text-sm opacity-75 mb-1">آخر تحديث</div>
                            <div class="font-semibold"><?php echo e(now()->format('Y-m-d H:i')); ?></div>
                        </div>
                        <button class="bg-white bg-opacity-20 backdrop-blur-sm px-4 py-2 rounded-xl hover:bg-opacity-30 transition-all">
                            <i class="fas fa-cog"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- CPU Usage -->
            <div class="stat-card">
                <div class="p-6">
                    <div class="stat-icon bg-blue-500">
                        <i class="fas fa-microchip"></i>
                    </div>
                    <div class="stat-value">45%</div>
                    <div class="stat-label">استخدام المعالج</div>
                    <div class="mt-4">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: 45%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Memory Usage -->
            <div class="stat-card">
                <div class="p-6">
                    <div class="stat-icon bg-green-500">
                        <i class="fas fa-memory"></i>
                    </div>
                    <div class="stat-value">2.8GB</div>
                    <div class="stat-label">استخدام الذاكرة</div>
                    <div class="mt-4">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: 65%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Storage Usage -->
            <div class="stat-card">
                <div class="p-6">
                    <div class="stat-icon bg-purple-500">
                        <i class="fas fa-hdd"></i>
                    </div>
                    <div class="stat-value">78%</div>
                    <div class="stat-label">مساحة التخزين</div>
                    <div class="mt-4">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-purple-500 h-2 rounded-full" style="width: 78%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Users -->
            <div class="stat-card">
                <div class="p-6">
                    <div class="stat-icon bg-orange-500">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-value">1,234</div>
                    <div class="stat-label">المستخدمين النشطين</div>
                    <div class="mt-4">
                        <div class="flex items-center text-sm">
                            <div class="pulse-dot bg-green-500 mr-2"></div>
                            <span>جاري التشغيل</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Health Section -->
        <div class="system-health">
            <div class="flex items-center mb-6">
                <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-xl p-3 ml-4">
                    <i class="fas fa-heartbeat text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold">حالة النظام</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="health-item">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-white opacity-75 mb-1">قاعدة البيانات</div>
                            <div class="font-semibold text-lg">متصلة</div>
                        </div>
                        <div class="w-8 h-8 bg-green-400 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-white text-sm"></i>
                        </div>
                    </div>
                </div>
                
                <div class="health-item">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-white opacity-75 mb-1">الخادم</div>
                            <div class="font-semibold text-lg">يعمل</div>
                        </div>
                        <div class="w-8 h-8 bg-green-400 rounded-full flex items-center justify-center">
                            <i class="fas fa-check text-white text-sm"></i>
                        </div>
                    </div>
                </div>
                
                <div class="health-item">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-white opacity-75 mb-1">الذاكرة المؤقتة</div>
                            <div class="font-semibold text-lg">سريعة</div>
                        </div>
                        <div class="w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center">
                            <i class="fas fa-exclamation text-white text-sm"></i>
                        </div>
                    </div>
                </div>
                
                <div class="health-item">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-white opacity-75 mb-1">الأمان</div>
                            <div class="font-semibold text-lg">آمن</div>
                        </div>
                        <div class="w-8 h-8 bg-green-400 rounded-full flex items-center justify-center">
                            <i class="fas fa-shield-alt text-white text-sm"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Overview -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- System Performance -->
            <div class="stat-card">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-chart-line text-blue-500 ml-3 mr-2"></i>
                        أداء النظام
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">وقت الاستجابة</span>
                            <span class="font-semibold">120ms</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">معدل الطلبات</span>
                            <span class="font-semibold">1,245/ثانية</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">معدل الخطأ</span>
                            <span class="font-semibold text-green-600">0.1%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="stat-card">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-clock text-purple-500 ml-3 mr-2"></i>
                        النشاط الحديث
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="activity-item">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                                    <div>
                                        <div class="font-semibold">تحديث نظام</div>
                                        <div class="text-sm text-gray-500">منذ 5 دقائق</div>
                                    </div>
                                </div>
                                <i class="fas fa-check-circle text-green-500"></i>
                            </div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                                    <div>
                                        <div class="font-semibold">نسخ احتياطي</div>
                                        <div class="text-sm text-gray-500">منذ ساعة</div>
                                    </div>
                                </div>
                                <i class="fas fa-check-circle text-blue-500"></i>
                            </div>
                        </div>
                        
                        <div class="activity-item">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="w-2 h-2 bg-yellow-500 rounded-full mr-3"></div>
                                    <div>
                                        <div class="font-semibold">فحص الأمان</div>
                                        <div class="text-sm text-gray-500">منذ ساعتين</div>
                                    </div>
                                </div>
                                <i class="fas fa-shield-alt text-yellow-500"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="stat-card">
            <div class="p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-bolt text-orange-500 ml-3 mr-2"></i>
                    إجراءات سريعة
                </h3>
                
                <div class="grid grid-cols-2 gap-4">
                    <button class="bg-blue-500 text-white px-4 py-3 rounded-xl hover:bg-blue-600 transition-all flex items-center justify-center">
                        <i class="fas fa-broom ml-2"></i>
                        مسح الكاش
                    </button>
                    <button class="bg-green-500 text-white px-4 py-3 rounded-xl hover:bg-green-600 transition-all flex items-center justify-center">
                        <i class="fas fa-sync ml-2"></i>
                        إعادة التشغيل
                    </button>
                    <button class="bg-purple-500 text-white px-4 py-3 rounded-xl hover:bg-purple-600 transition-all flex items-center justify-center">
                        <i class="fas fa-download ml-2"></i>
                        تحديث النظام
                    </button>
                    <button class="bg-red-500 text-white px-4 py-3 rounded-xl hover:bg-red-600 transition-all flex items-center justify-center">
                        <i class="fas fa-file-export ml-2"></i>
                        تصدير السجلات
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Add real-time updates
document.addEventListener('DOMContentLoaded', function() {
    // Simulate real-time stat updates
    setInterval(() => {
        // Update CPU usage
        const cpuElement = document.querySelector('.stat-value');
        if (cpuElement) {
            const currentCpu = parseInt(cpuElement.textContent);
            const newCpu = Math.max(20, Math.min(80, currentCpu + (Math.random() - 0.5) * 10));
            cpuElement.textContent = newCpu + '%';
            
            // Update progress bar
            const progressBar = cpuElement.closest('.stat-card').querySelector('.bg-blue-500');
            if (progressBar) {
                progressBar.style.width = newCpu + '%';
            }
        }
    }, 3000);
    
    // Add hover effects to cards
    document.querySelectorAll('.stat-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px) scale(1.02)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });
    
    // Quick action handlers
    document.querySelectorAll('.grid button').forEach(button => {
        button.addEventListener('click', function() {
            const action = this.textContent.trim();
            showNotification(`جاري تنفيذ: ${action}`, 'info');
        });
    });
});

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 left-4 bg-${type === 'success' ? 'green' : type === 'error' ? 'red' : 'blue'}-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-pulse`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} ml-2"></i>
            ${message}
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/admin/system/dashboard.blade.php ENDPATH**/ ?>