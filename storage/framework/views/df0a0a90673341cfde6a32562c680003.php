

<?php $__env->startSection('title', 'مراقبة الطلبات'); ?>

<?php $__env->startSection('content'); ?>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<div class="flex h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside class="w-64 bg-white border-r border-gray-200 overflow-y-auto sticky top-0 h-screen">
        <!-- Logo -->
        <!-- <div class="p-6 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold text-lg">
                    عق
                </div>
                <div>
                    <h2 class="font-bold text-gray-900">عقاري</h2>
                    <p class="text-xs text-gray-500">منصة العقارات</p>
                </div>
            </div>
        </div> -->

        <!-- Navigation -->
        <nav class="p-4 space-y-2">
            <a href="<?php echo e(route('requests.index')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-lg bg-blue-50 text-blue-700 font-semibold border-r-4 border-blue-600">
                <i class="fas fa-chart-line w-5"></i>
                <span>مراقبة الطلبات</span>
            </a>
            <a href="<?php echo e(route('requests.index')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                <i class="fas fa-list w-5"></i>
                <span>جميع الطلبات</span>
            </a>
            <a href="#" onclick="showDatabaseInfo()" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                <i class="fas fa-database w-5"></i>
                <span>قاعدة البيانات</span>
                <span class="ml-auto bg-blue-600 text-white text-xs font-bold px-2 py-1 rounded-full">12</span>
            </a>

            <div class="border-t border-gray-200 my-4 pt-4">
                <p class="text-xs font-bold text-gray-500 uppercase px-4 mb-2">الإدارة</p>
                <a href="<?php echo e(url('/dashboard')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                    <i class="fas fa-tachometer-alt w-5"></i>
                    <span>لوحة التحكم</span>
                </a>
                <a href="<?php echo e(url('/settings')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                    <i class="fas fa-cog w-5"></i>
                    <span>الإعدادات</span>
                </a>
                <a href="<?php echo e(url('/profile')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                    <i class="fas fa-user w-5"></i>
                    <span>الملف الشخصي</span>
                </a>
                <a href="<?php echo e(url('/logout')); ?>" class="flex items-center gap-3 px-4 py-3 rounded-lg text-gray-700 hover:bg-gray-100 transition">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    <span>تسجيل الخروج</span>
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Bar -->
        <header class="bg-white border-b border-gray-200 sticky top-0 z-50">
            <div class="px-8 py-4 flex items-center justify-between">
                <!-- Left Section -->
                <div class="flex items-center gap-6 flex-1">
                    <!-- <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center text-white font-bold">
                            عق
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">عقاري</h3>
                            <p class="text-xs text-gray-500">منصة العقارات الذكية</p>
                        </div>
                    </div> -->

                    <div class="relative flex-1 max-w-md">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                        <input type="text" placeholder="ابحث عن طلب..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Right Section -->
                <div class="flex items-center gap-4">
                    <!-- Export Button -->
                    <button onclick="exportData()" class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                        <i class="fas fa-download"></i>
                        تصدير
                    </button>
                    
                    <!-- Clear Button -->
                    <button onclick="clearOldData()" class="flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold">
                        <i class="fas fa-trash"></i>
                        تنظيف
                    </button>
                    
                    <!-- Notifications -->
                    <div class="relative cursor-pointer group">
                        <i class="fas fa-bell text-gray-600 text-lg hover:text-blue-600 transition"></i>
                        <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">3</span>
                    </div>

                    <!-- User Profile -->
                    <!-- <div class="flex items-center gap-3 pl-4 border-l border-gray-200">
                        <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                            أ
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">أحمد محمد</p>
                            <p class="text-xs text-gray-500">مسؤول النظام</p>
                        </div>
                    </div> -->
                </div>
            </div>
        </header>

        <!-- Scrollable Content -->
        <main class="flex-1 overflow-y-auto">
            <div class="p-8">
                <!-- Page Title -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                        <i class="fas fa-chart-line text-blue-600"></i>
                        مراقبة الطلبات
                    </h1>
                    <p class="text-gray-600 mt-2">مراقبة حالة جميع الطلبات الواردة إلى النظام</p>
                </div>

                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                    <!-- Total Requests -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 text-2xl">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm font-semibold uppercase">إجمالي الطلبات</p>
                                <h3 class="text-3xl font-bold text-gray-900 mt-1" id="totalRequests"><?php echo e($stats['total'] ?? 0); ?></h3>
                            </div>
                        </div>
                    </div>

                    <!-- Pending -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition border-l-4 border-l-yellow-500">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-yellow-100 rounded-lg flex items-center justify-center text-yellow-600 text-2xl">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm font-semibold uppercase">قيد الانتظار</p>
                                <h3 class="text-3xl font-bold text-gray-900 mt-1" id="pendingRequests"><?php echo e($stats['pending'] ?? 0); ?></h3>
                            </div>
                        </div>
                    </div>

                    <!-- Processing -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition border-l-4 border-l-cyan-500">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-cyan-100 rounded-lg flex items-center justify-center text-cyan-600 text-2xl">
                                <i class="fas fa-cog animate-spin"></i>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm font-semibold uppercase">قيد المعالجة</p>
                                <h3 class="text-3xl font-bold text-gray-900 mt-1" id="processingRequests"><?php echo e($stats['processing'] ?? 0); ?></h3>
                            </div>
                        </div>
                    </div>

                    <!-- Failed -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition border-l-4 border-l-red-500">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-red-100 rounded-lg flex items-center justify-center text-red-600 text-2xl">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm font-semibold uppercase">فشلت</p>
                                <h3 class="text-3xl font-bold text-gray-900 mt-1" id="failedRequests"><?php echo e($stats['failed'] ?? 0); ?></h3>
                            </div>
                        </div>
                    </div>

                    <!-- Completed -->
                    <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition border-l-4 border-l-green-500">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center text-green-600 text-2xl">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm font-semibold uppercase">مكتملة</p>
                                <h3 class="text-3xl font-bold text-gray-900 mt-1" id="completedRequests"><?php echo e($stats['completed'] ?? 0); ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters Section -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">الحالة</label>
                            <select id="statusFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">الكل</option>
                                <option value="pending">قيد الانتظار</option>
                                <option value="processing">قيد المعالجة</option>
                                <option value="completed">مكتمل</option>
                                <option value="failed">فشل</option>
                            </select>
                        </div>

                        <!-- Method Filter -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">الطريقة</label>
                            <select id="methodFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">الكل</option>
                                <option value="GET">GET</option>
                                <option value="POST">POST</option>
                                <option value="PUT">PUT</option>
                                <option value="DELETE">DELETE</option>
                                <option value="PATCH">PATCH</option>
                            </select>
                        </div>

                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">البحث</label>
                            <input type="text" id="searchInput" placeholder="ابحث..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <!-- Sort -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">الترتيب</label>
                            <select id="sortBy" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="created_at">الأحدث أولاً</option>
                                <option value="response_time">أسرع استجابة</option>
                                <option value="status">حسب الحالة</option>
                            </select>
                        </div>
                    </div>

                    <!-- Filter Buttons -->
                    <div class="flex gap-3">
                        <button onclick="applyFilters()" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-semibold flex items-center gap-2">
                            <i class="fas fa-filter"></i>
                            تطبيق الفلاتر
                        </button>
                        <button onclick="resetFilters()" class="px-6 py-2 bg-gray-200 text-gray-900 rounded-lg hover:bg-gray-300 transition font-semibold flex items-center gap-2">
                            <i class="fas fa-redo"></i>
                            إعادة تعيين
                        </button>
                        <button onclick="refreshData()" class="px-6 py-2 bg-gray-200 text-gray-900 rounded-lg hover:bg-gray-300 transition font-semibold flex items-center gap-2 ml-auto">
                            <i class="fas fa-sync-alt"></i>
                            تحديث
                        </button>
                    </div>
                </div>

                <!-- Requests List -->
                <div id="requestsContainer" class="space-y-4">
                    <?php if(isset($recentRequests) && $recentRequests->count() > 0): ?>
                        <?php $__currentLoopData = $recentRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition cursor-pointer border-l-4 border-l-<?php echo e(getStatusColor($request->status)); ?>-500" onclick="showRequestDetails('<?php echo e($request->id); ?>')">
                            <!-- Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <span class="px-3 py-1 rounded-lg text-xs font-bold bg-<?php echo e(getMethodColor($request->method)); ?>-100 text-<?php echo e(getMethodColor($request->method)); ?>-700 uppercase">
                                        <?php echo e($request->method); ?>

                                    </span>
                                    <span class="px-3 py-1 rounded-lg text-xs font-bold bg-<?php echo e(getStatusColor($request->status)); ?>-100 text-<?php echo e(getStatusColor($request->status)); ?>-700 uppercase">
                                        <?php echo e(getStatusLabel($request->status)); ?>

                                    </span>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?php echo e($request->created_at->diffForHumans()); ?>

                                </div>
                            </div>

                            <!-- URL -->
                            <div class="bg-gray-50 rounded-lg p-3 mb-4 border-l-4 border-l-blue-600">
                                <p class="text-sm font-mono text-blue-600 break-all">
                                    <?php echo e(Str::limit($request->url, 120)); ?>

                                </p>
                            </div>

                            <!-- Details Grid -->
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                                <div>
                                    <p class="text-xs text-gray-600 font-semibold mb-1">الكود</p>
                                    <p class="text-lg font-bold text-gray-900"><?php echo e($request->response_code ?? '-'); ?></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 font-semibold mb-1">الاستجابة</p>
                                    <p class="text-lg font-bold text-gray-900"><?php echo e($request->response_time ? number_format($request->response_time, 0) . 'ms' : '-'); ?></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 font-semibold mb-1">IP</p>
                                    <p class="text-sm font-mono text-gray-700"><?php echo e($request->ip_address); ?></p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-600 font-semibold mb-1">الوقت</p>
                                    <p class="text-sm text-gray-700"><?php echo e($request->created_at->format('H:i')); ?></p>
                                </div>
                            </div>

                            <!-- Footer -->
                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <div class="flex items-center gap-3">
                                    <?php if($request->user): ?>
                                        <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm">
                                            <?php echo e(substr($request->user->name, 0, 1)); ?>

                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900"><?php echo e($request->user->name); ?></p>
                                            <p class="text-xs text-gray-500"><?php echo e(substr($request->user->email, 0, 20)); ?></p>
                                        </div>
                                    <?php else: ?>
                                        <div class="w-10 h-10 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold">
                                            ؟
                                        </div>
                                        <span class="text-sm text-gray-600">زائر</span>
                                    <?php endif; ?>
                                </div>

                                <div class="flex gap-2" onclick="event.stopPropagation();">
                                    <button class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 transition flex items-center justify-center" title="عرض التفاصيل" onclick="showRequestDetails(<?php echo e($request->id); ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <?php if($request->status === 'failed'): ?>
                                        <button class="w-9 h-9 rounded-lg bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition flex items-center justify-center" title="إعادة المحاولة" onclick="retryRequest(<?php echo e($request->id); ?>)">
                                            <i class="fas fa-redo"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <div class="bg-white rounded-lg border border-gray-200 p-12 text-center">
                            <i class="fas fa-inbox text-6xl text-gray-300 mb-4 block"></i>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">لا توجد طلبات حالياً</h3>
                            <p class="text-gray-600">سيظهر هنا الطلبات حالما تبدأ بالوصول إلى النظام</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Details Modal -->
<div id="detailsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" onclick="if(event.target === this) closeModal()">
    <div class="bg-white rounded-lg max-w-2xl w-full mx-4 max-h-96 overflow-y-auto">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 text-white px-8 py-6 flex items-center justify-between sticky top-0">
            <h2 class="text-2xl font-bold">تفاصيل الطلب</h2>
            <button onclick="closeModal()" class="text-2xl hover:opacity-80">×</button>
        </div>

        <!-- Content -->
        <div class="p-8" id="modalContent">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<script>
function getStatusColor(status) {
    const colors = {
        'pending': 'yellow',
        'processing': 'cyan',
        'completed': 'green',
        'failed': 'red'
    };
    return colors[status] || 'gray';
}

function getStatusLabel(status) {
    const labels = {
        'pending': 'قيد الانتظار',
        'processing': 'قيد المعالجة',
        'completed': 'مكتمل',
        'failed': 'فشل'
    };
    return labels[status] || 'غير معروف';
}

function getMethodColor(method) {
    const colors = {
        'GET': 'green',
        'POST': 'yellow',
        'PUT': 'cyan',
        'DELETE': 'red',
        'PATCH': 'purple'
    };
    return colors[method] || 'gray';
}

async function showRequestDetails(requestId) {
    try {
        const response = await fetch(`/requests/${requestId}`);
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        if (!data.request) {
            throw new Error('No request data found in response');
        }
        
        const request = data.request;

        const content = document.getElementById('modalContent');
        
        // Safely get status label
        const statusLabel = request.status ? getStatusLabel(request.status) : 'غير معروف';
        const responseTime = request.response_time ? Math.round(request.response_time) + ' ms' : '-';
        const responseCode = request.response_code || '-';
        const responseCodeColor = request.response_code && request.response_code < 400 ? 'green' : 'red';
        
        content.innerHTML = `
            <div class="space-y-6">
                <!-- Basic Info -->
                <div>
                    <h3 class="font-bold text-gray-900 mb-4">المعلومات الأساسية</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-600 font-semibold mb-1">رقم الطلب</p>
                            <p class="font-mono text-sm text-blue-600">${request.id || '-'}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 font-semibold mb-1">الطريقة</p>
                            <p class="font-bold">${request.method || '-'}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 font-semibold mb-1">الحالة</p>
                            <p class="font-bold">${statusLabel}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 font-semibold mb-1">الكود</p>
                            <p class="font-bold text-${responseCodeColor}-600">${responseCode}</p>
                        </div>
                    </div>
                </div>

                <!-- URL & Network -->
                <div>
                    <h3 class="font-bold text-gray-900 mb-4">الرابط والشبكة</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-gray-600 font-semibold mb-1">الرابط</p>
                            <p class="font-mono text-sm bg-gray-100 p-3 rounded text-blue-600 break-all">${request.url || '-'}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 font-semibold mb-1">عنوان IP</p>
                            <p class="font-mono text-sm">${request.ip_address || '-'}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 font-semibold mb-1">وقت الاستجابة</p>
                            <p class="font-bold">${responseTime}</p>
                        </div>
                    </div>
                </div>

                <!-- Timing -->
                <div>
                    <h3 class="font-bold text-gray-900 mb-4">معلومات التوقيت</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-600 font-semibold mb-1">وقت البدء</p>
                            <p class="text-sm">${request.started_at ? new Date(request.started_at).toLocaleString('ar-SA') : '-'}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-600 font-semibold mb-1">وقت الانتهاء</p>
                            <p class="text-sm">${request.completed_at ? new Date(request.completed_at).toLocaleString('ar-SA') : '-'}</p>
                        </div>
                    </div>
                </div>

                ${request.error_message ? `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <h4 class="font-bold text-red-700 mb-2">رسالة الخطأ</h4>
                        <p class="font-mono text-sm text-red-600 break-all">${request.error_message}</p>
                    </div>
                ` : ''}
            </div>
        `;

        document.getElementById('detailsModal').classList.remove('hidden');
    } catch (error) {
        console.error('Error loading request details:', error);
        
        // Show user-friendly error message
        const content = document.getElementById('modalContent');
        content.innerHTML = `
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                <i class="fas fa-exclamation-triangle text-red-600 text-4xl mb-4"></i>
                <h3 class="text-lg font-bold text-red-800 mb-2">حدث خطأ أثناء تحميل التفاصيل</h3>
                <p class="text-red-600">${error.message}</p>
                <button onclick="closeModal()" class="mt-4 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    إغلاق
                </button>
            </div>
        `;
        
        document.getElementById('detailsModal').classList.remove('hidden');
    }
}

function closeModal() {
    document.getElementById('detailsModal').classList.add('hidden');
}

function applyFilters() {
    const status = document.getElementById('statusFilter').value;
    const method = document.getElementById('methodFilter').value;
    const search = document.getElementById('searchInput').value;
    const sortBy = document.getElementById('sortBy').value;

    const params = new URLSearchParams({ status, method, search, sort_by: sortBy });

    fetch(`/requests/get?${params}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.requests) {
                updateRequestsDisplay(data.requests);
                if (data.stats) {
                    updateStats(data.stats);
                }
                showNotification(`تم تحديث ${data.requests.length} طلب`, 'success');
            } else {
                showNotification('لا توجد بيانات مرتجعة', 'warning');
            }
        })
        .catch(error => {
            console.error('Filter error:', error);
            showNotification('حدث خطأ أثناء تطبيق الفلاتر', 'error');
            // Fallback: reload the page
            setTimeout(() => {
                location.reload();
            }, 2000);
        });
}

function resetFilters() {
    document.getElementById('statusFilter').value = '';
    document.getElementById('methodFilter').value = '';
    document.getElementById('searchInput').value = '';
    document.getElementById('sortBy').value = 'created_at';
    applyFilters();
}

function refreshData() {
    location.reload();
}

function exportData() {
    const status = document.getElementById('statusFilter').value;
    const method = document.getElementById('methodFilter').value;
    const search = document.getElementById('searchInput').value;
    
    const params = new URLSearchParams({ status, method, search });
    
    fetch(`/requests/export?${params}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification('تم تصدير البيانات بنجاح', 'success');
            
            // Download file if available
            if (data.download_url) {
                const link = document.createElement('a');
                link.href = data.download_url;
                link.download = data.filename || 'requests_export.csv';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        } else {
            showNotification(data.message || 'حدث خطأ أثناء التصدير', 'error');
        }
    })
    .catch(error => {
        console.error('Export error:', error);
        showNotification('حدث خطأ أثناء التصدير', 'error');
    });
}

function clearOldData() {
    if (confirm('هل أنت متأكد من حذف الطلبات القديمة (أكبر من 30 يوم)؟\nهذا الإجراء لا يمكن التراجع عنه.')) {
        fetch('/requests/clear-old', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                // Refresh data after clearing
                setTimeout(() => {
                    refreshData();
                }, 1500);
            } else {
                showNotification(data.message || 'حدث خطأ أثناء الحذف', 'error');
            }
        })
        .catch(error => {
            console.error('Clear error:', error);
            showNotification('حدث خطأ أثناء الحذف', 'error');
        });
    }
}

function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification-toast');
    existingNotifications.forEach(notification => notification.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification-toast fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
    
    // Set colors based on type
    const colors = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        warning: 'bg-yellow-500 text-white',
        info: 'bg-blue-500 text-white'
    };
    
    notification.className += ` ${colors[type] || colors.info}`;
    
    notification.innerHTML = `
        <div class="flex items-center gap-3">
            <i class="fas fa-${type === 'success' ? 'check-circle' : (type === 'error' ? 'exclamation-circle' : 'info-circle')}"></i>
            <span class="font-semibold">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 hover:opacity-80">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
        notification.classList.add('translate-x-0');
    }, 100);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 300);
    }, 5000);
}

// Database info function
function showDatabaseInfo() {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';
    modal.onclick = function(e) { if (e.target === modal) modal.remove(); };
    
    modal.innerHTML = `
        <div class="bg-white rounded-lg max-w-md w-full mx-4 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">معلومات قاعدة البيانات</h3>
                <button onclick="this.closest('.fixed').remove()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">عدد الجداول:</span>
                    <span class="font-semibold">12</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">حجم قاعدة البيانات:</span>
                    <span class="font-semibold">45.2 MB</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">آخر نسخة احتياطية:</span>
                    <span class="font-semibold">منذ ساعتين</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">حالة الاتصال:</span>
                    <span class="font-semibold text-green-600">متصل</span>
                </div>
            </div>
            <div class="mt-6 pt-4 border-t">
                <button onclick="this.closest('.fixed').remove()" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                    إغلاق
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

// Auto-refresh functionality
let autoRefreshInterval;
let isAutoRefreshEnabled = true;

function startAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
    
    if (isAutoRefreshEnabled) {
        autoRefreshInterval = setInterval(() => {
            updateStats();
            loadRequests();
        }, 5000); // Refresh every 5 seconds
        
        showNotification('تم تفعيل التحديث التلقائي', 'success');
    }
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
        showNotification('تم إيقاف التحديث التلقائي', 'info');
    }
}

function toggleAutoRefresh() {
    isAutoRefreshEnabled = !isAutoRefreshEnabled;
    
    if (isAutoRefreshEnabled) {
        startAutoRefresh();
    } else {
        stopAutoRefresh();
    }
    
    // Update button state
    const btn = document.getElementById('autoRefreshBtn');
    if (btn) {
        btn.innerHTML = isAutoRefreshEnabled ? 
            '<i class="fas fa-pause"></i> إيقاف التحديث' : 
            '<i class="fas fa-play"></i> تشغيل التحديث';
        btn.className = isAutoRefreshEnabled ?
            'px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition font-semibold flex items-center gap-2' :
            'px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold flex items-center gap-2';
    }
}

// Update stats function
function updateStats(stats = null) {
    if (stats) {
        // Use provided stats
        document.getElementById('totalRequests').textContent = stats.total || 0;
        document.getElementById('pendingRequests').textContent = stats.pending || 0;
        document.getElementById('processingRequests').textContent = stats.processing || 0;
        document.getElementById('completedRequests').textContent = stats.completed || 0;
    } else {
        // Fetch fresh stats
        fetch('/requests/stats')
            .then(response => response.json())
            .then(stats => {
                document.getElementById('totalRequests').textContent = stats.total || 0;
                document.getElementById('pendingRequests').textContent = stats.pending || 0;
                document.getElementById('processingRequests').textContent = stats.processing || 0;
                document.getElementById('completedRequests').textContent = stats.completed || 0;
            })
            .catch(error => console.error('Error updating stats:', error));
    }
}

// Store all requests data for client-side filtering
let allRequests = [];

// Load requests function
function loadRequests() {
    const status = document.getElementById('statusFilter').value;
    const method = document.getElementById('methodFilter').value;
    const search = document.getElementById('searchInput').value;
    const sortBy = document.getElementById('sortBy').value;

    // Try server-side filtering first
    const params = new URLSearchParams({ status, method, search, sort_by: sortBy });
    const url = `/requests/get?${params}`;

    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.requests) {
                allRequests = data.requests;
                updateRequestsDisplay(data.requests);
                if (data.stats) {
                    updateStats(data.stats);
                }
                showNotification(`تم تحديث ${data.requests.length} طلب`, 'success');
            } else {
                // Fallback to client-side filtering
                applyClientSideFilters();
            }
        })
        .catch(error => {
            console.error('Server filtering failed, using client-side:', error);
            // Fallback to client-side filtering
            applyClientSideFilters();
        });
}

// Client-side filtering function
function applyClientSideFilters() {
    // Get all request elements from the page
    const requestElements = document.querySelectorAll('#requestsContainer > div');
    const status = document.getElementById('statusFilter').value;
    const method = document.getElementById('methodFilter').value;
    const search = document.getElementById('searchInput').value.toLowerCase();
    const sortBy = document.getElementById('sortBy').value;
    
    let visibleCount = 0;
    const requestsData = [];
    
    requestElements.forEach(element => {
        let show = true;
        
        // Get request data from element
        const requestText = element.textContent.toLowerCase();
        const statusElement = element.querySelector('[class*="border-l-"]');
        const methodElement = element.querySelector('span[class*="uppercase"]');
        
        // Filter by status
        if (status && statusElement) {
            const elementStatus = statusElement.className.match(/border-l-(\w+)-500/);
            if (elementStatus && elementStatus[1] !== status) {
                show = false;
            }
        }
        
        // Filter by method
        if (method && methodElement) {
            const elementMethod = methodElement.textContent.trim();
            if (elementMethod !== method) {
                show = false;
            }
        }
        
        // Filter by search
        if (search && !requestText.includes(search)) {
            show = false;
        }
        
        // Show/hide element
        element.style.display = show ? 'block' : 'none';
        if (show) {
            visibleCount++;
            // Extract data for sorting
            const timeElement = element.querySelector('.text-gray-500');
            const responseElement = element.querySelector('.text-lg');
            requestsData.push({
                element: element,
                time: timeElement ? timeElement.textContent : '',
                responseTime: responseElement ? responseElement.textContent : ''
            });
        }
    });
    
    // Sort visible elements
    if (sortBy === 'response_time') {
        requestsData.sort((a, b) => {
            const timeA = parseInt(a.responseTime) || 0;
            const timeB = parseInt(b.responseTime) || 0;
            return timeA - timeB;
        });
    } else if (sortBy === 'status') {
        requestsData.sort((a, b) => {
            const statusA = a.element.querySelector('[class*="border-l-"]').className;
            const statusB = b.element.querySelector('[class*="border-l-"]').className;
            return statusA.localeCompare(statusB);
        });
    }
    
    // Reorder elements
    const container = document.getElementById('requestsContainer');
    requestsData.forEach(item => {
        container.appendChild(item.element);
    });
    
    showNotification(`تم عرض ${visibleCount} طلب`, 'success');
}

function applyFilters() {
    applyClientSideFilters();
}

function resetFilters() {
    document.getElementById('statusFilter').value = '';
    document.getElementById('methodFilter').value = '';
    document.getElementById('searchInput').value = '';
    document.getElementById('sortBy').value = 'created_at';
    applyClientSideFilters();
}

// Update requests display
function updateRequestsDisplay(requests) {
    const container = document.getElementById('requestsContainer');

    // Check if there are no requests
    if (requests.length === 0) {
        container.innerHTML = `
            <div class="bg-white rounded-lg border border-gray-200 p-12 text-center">
                <i class="fas fa-inbox text-6xl text-gray-300 mb-4 block"></i>
                <h3 class="text-xl font-bold text-gray-900 mb-2">لا توجد طلبات حالياً</h3>
                <p class="text-gray-600">سيظهر هنا الطلبات حالما تبدأ بالوصول إلى النظام</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = requests.map(request => `
        <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition cursor-pointer border-l-4 border-l-${getStatusColor(request.status)}-500" onclick="showRequestDetails('${request.id}')">
            <!-- Header -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 rounded-lg text-xs font-bold bg-${getMethodColor(request.method)}-100 text-${getMethodColor(request.method)}-700 uppercase">
                        ${request.method}
                    </span>
                    <span class="px-3 py-1 rounded-lg text-xs font-bold bg-${getStatusColor(request.status)}-100 text-${getStatusColor(request.status)}-700 uppercase">
                        ${getStatusLabel(request.status)}
                    </span>
                </div>
                <div class="text-sm text-gray-500">
                    ${new Date(request.created_at).toLocaleString('ar-SA')}
                </div>
            </div>

            <!-- URL -->
            <div class="bg-gray-50 rounded-lg p-3 mb-4 border-l-4 border-l-blue-600">
                <p class="text-sm font-mono text-blue-600 break-all">
                    ${request.url.substring(0, 120)}${request.url.length > 120 ? '...' : ''}
                </p>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                <div>
                    <p class="text-xs text-gray-600 font-semibold mb-1">الكود</p>
                    <p class="text-lg font-bold text-gray-900">${request.response_code || '-'}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-600 font-semibold mb-1">الاستجابة</p>
                    <p class="text-lg font-bold text-gray-900">${request.response_time ? Math.round(request.response_time) + 'ms' : '-'}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-600 font-semibold mb-1">IP</p>
                    <p class="text-sm font-mono text-gray-700">${request.ip_address}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-600 font-semibold mb-1">الوقت</p>
                    <p class="text-sm text-gray-700">${new Date(request.created_at).toLocaleTimeString('ar-SA')}</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <div class="flex items-center gap-3">
                    ${request.user ? 
                        `<div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm">
                            ${request.user.name ? request.user.name.charAt(0) : '?'}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">${request.user.name || 'Unknown'}</p>
                            <p class="text-xs text-gray-500">${request.user.email ? request.user.email.substring(0, 20) : ''}</p>
                        </div>` : 
                        `<div class="w-10 h-10 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold">
                            ؟
                        </div>
                        <span class="text-sm text-gray-600">زائر</span>`
                    }
                </div>

                <div class="flex gap-2" onclick="event.stopPropagation();">
                    <button onclick="showRequestDetails('${request.id}')" class="w-9 h-9 rounded-lg bg-blue-100 text-blue-600 hover:bg-blue-200 transition flex items-center justify-center" title="عرض التفاصيل">
                        <i class="fas fa-eye"></i>
                    </button>
                    ${request.status === 'failed' ? 
                        `<button onclick="retryRequest('${request.id}')" class="w-9 h-9 rounded-lg bg-yellow-100 text-yellow-600 hover:bg-yellow-200 transition flex items-center justify-center" title="إعادة المحاولة">
                            <i class="fas fa-redo"></i>
                        </button>` : ''
                    }
                </div>
            </div>
        </div>
    `).join('');
}

// Show request details modal
function showRequestDetails(requestId) {
    fetch(`/requests/${requestId}`)
        .then(response => response.json())
        .then(data => {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
            modal.onclick = function(e) { if (e.target === modal) modal.remove(); };
            
            modal.innerHTML = `
                <div class="bg-white rounded-lg max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                    <div class="sticky top-0 bg-white border-b border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <h3 class="text-2xl font-bold text-gray-900">تفاصيل الطلب</h3>
                            <button onclick="this.closest('.fixed').remove()" class="text-gray-500 hover:text-gray-700">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        <!-- Request Header -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-semibold text-gray-900 mb-3">معلومات أساسية</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">رقم الطلب:</span>
                                        <span class="font-mono text-sm">${data.request.request_id || 'N/A'}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">الطريقة:</span>
                                        <span class="px-2 py-1 rounded text-xs font-bold bg-${getMethodColor(data.request.method)}-100 text-${getMethodColor(data.request.method)}-700">
                                            ${data.request.method}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">الحالة:</span>
                                        <span class="px-2 py-1 rounded text-xs font-bold bg-${getStatusColor(data.request.status)}-100 text-${getStatusColor(data.request.status)}-700">
                                            ${getStatusLabel(data.request.status)}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">كود الاستجابة:</span>
                                        <span class="font-bold">${data.request.response_code || '-'}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-semibold text-gray-900 mb-3">معلومات الوقت</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">وقت البدء:</span>
                                        <span class="text-sm">${data.request.started_at ? new Date(data.request.started_at).toLocaleString('ar-SA') : '-'}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">وقت الانتهاء:</span>
                                        <span class="text-sm">${data.request.completed_at ? new Date(data.request.completed_at).toLocaleString('ar-SA') : '-'}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">وقت الاستجابة:</span>
                                        <span class="font-bold">${data.request.response_time ? Math.round(data.request.response_time) + 'ms' : '-'}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">المدة:</span>
                                        <span class="font-bold">${data.duration || '-'}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- URL Section -->
                        <div class="bg-blue-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">رابط الطلب</h4>
                            <p class="font-mono text-sm text-blue-600 break-all">${data.request.url}</p>
                        </div>
                        
                        <!-- User Info -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-3">معلومات المستخدم</h4>
                            <div class="flex items-center gap-4">
                                ${data.request.user ? 
                                    `<div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                                            ${data.request.user.name ? data.request.user.name.charAt(0) : '?'}
                                        </div>
                                        <div>
                                            <p class="font-semibold">${data.request.user.name || 'Unknown'}</p>
                                            <p class="text-sm text-gray-600">${data.request.user.email || ''}</p>
                                        </div>
                                    </div>` : 
                                    `<div class="flex items-center gap-3">
                                        <div class="w-12 h-12 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold">
                                            ؟
                                        </div>
                                        <span class="text-gray-600">زائر</span>
                                    </div>`
                                }
                            </div>
                        </div>
                        
                        <!-- IP and User Agent -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-semibold text-gray-900 mb-2">عنوان IP</h4>
                                <p class="font-mono text-sm">${data.request.ip_address}</p>
                            </div>
                            
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-semibold text-gray-900 mb-2">User Agent</h4>
                                <p class="text-xs text-gray-600 break-all">${data.request.user_agent || 'N/A'}</p>
                            </div>
                        </div>
                        
                        <!-- Error Message (if any) -->
                        ${data.request.error_message ? `
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                                <h4 class="font-semibold text-red-900 mb-2">رسالة الخطأ</h4>
                                <p class="text-red-700">${data.request.error_message}</p>
                            </div>
                        ` : ''}
                        
                        <!-- Headers and Payload (if available) -->
                        ${data.request.headers ? `
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Headers</h4>
                                <pre class="text-xs text-gray-600 overflow-x-auto">${JSON.stringify(data.request.headers, null, 2)}</pre>
                            </div>
                        ` : ''}
                        
                        ${data.request.payload ? `
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h4 class="font-semibold text-gray-900 mb-2">Payload</h4>
                                <pre class="text-xs text-gray-600 overflow-x-auto">${JSON.stringify(data.request.payload, null, 2)}</pre>
                            </div>
                        ` : ''}
                    </div>
                    
                    <div class="sticky bottom-0 bg-white border-t border-gray-200 p-6">
                        <button onclick="this.closest('.fixed').remove()" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition font-semibold">
                            إغلاق
                        </button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
        })
        .catch(error => {
            console.error('Error loading request details:', error);
            showNotification('حدث خطأ أثناء تحميل التفاصيل', 'error');
        });
}

document.addEventListener('DOMContentLoaded', function() {
    // Load initial data
    loadRequests();
    
    // Start auto-refresh
    startAutoRefresh();
    
    // Add auto-refresh toggle button to the header
    const rightSection = document.querySelector('.flex.items-center.gap-4');
    if (rightSection) {
        const autoRefreshBtn = document.createElement('button');
        autoRefreshBtn.id = 'autoRefreshBtn';
        autoRefreshBtn.onclick = toggleAutoRefresh;
        autoRefreshBtn.className = 'flex items-center gap-2 px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition font-semibold';
        autoRefreshBtn.innerHTML = '<i class="fas fa-pause"></i> إيقاف التحديث';
        rightSection.insertBefore(autoRefreshBtn, rightSection.firstChild);
    }
});

<?php
function getStatusColor($status) {
    return ['pending' => 'yellow', 'processing' => 'cyan', 'completed' => 'green', 'failed' => 'red'][$status] ?? 'gray';
}

function getStatusLabel($status) {
    return ['pending' => 'قيد الانتظار', 'processing' => 'قيد المعالجة', 'completed' => 'مكتمل', 'failed' => 'فشل'][$status] ?? 'غير معروف';
}

function getMethodColor($method) {
    return ['GET' => 'green', 'POST' => 'yellow', 'PUT' => 'cyan', 'DELETE' => 'red', 'PATCH' => 'purple'][$method] ?? 'gray';
}
?>
</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/requests/index.blade.php ENDPATH**/ ?>