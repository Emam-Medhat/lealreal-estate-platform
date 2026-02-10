<?php $__env->startSection('title', 'لوحة تحكم الصناديق'); ?>
<?php $__env->startSection('page-title', 'لوحة تحكم الصناديق'); ?>

<?php $__env->startSection('content'); ?>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">لوحة تحكم الصناديق</h1>
                        <p class="mt-2 text-gray-600">إدارة وتتبع استثماراتك في الصناديق</p>
                    </div>
                    <div class="flex gap-2">
                        <a href="/investor/funds" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                            <i class="fas fa-eye ml-2"></i>
                            عرض جميع الصناديق
                        </a>
                    </div>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <form method="GET" action="">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                        <div class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4">
                            <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="البحث في الصناديق..." class="px-3 py-2 border rounded-lg text-sm w-full md:w-64">
                            <select name="fund_type" class="px-3 py-2 border rounded-lg text-sm">
                                <option value="">جميع الأنواع</option>
                                <option value="real_estate" <?php echo e(request('fund_type') == 'real_estate' ? 'selected' : ''); ?>>العقارات</option>
                                <option value="technology" <?php echo e(request('fund_type') == 'technology' ? 'selected' : ''); ?>>التكنولوجيا</option>
                                <option value="renewable_energy" <?php echo e(request('fund_type') == 'renewable_energy' ? 'selected' : ''); ?>>الطاقة المتجددة</option>
                                <option value="ecommerce" <?php echo e(request('fund_type') == 'ecommerce' ? 'selected' : ''); ?>>التجارة الإلكترونية</option>
                                <option value="education" <?php echo e(request('fund_type') == 'education' ? 'selected' : ''); ?>>التعليم</option>
                            </select>
                            <select name="risk_level" class="px-3 py-2 border rounded-lg text-sm">
                                <option value="">كل المستويات</option>
                                <option value="low" <?php echo e(request('risk_level') == 'low' ? 'selected' : ''); ?>>منخفض</option>
                                <option value="medium" <?php echo e(request('risk_level') == 'medium' ? 'selected' : ''); ?>>متوسط</option>
                                <option value="high" <?php echo e(request('risk_level') == 'high' ? 'selected' : ''); ?>>مرتفع</option>
                            </select>
                        </div>
                        <div class="flex space-x-2">
                            <button type="submit" class="bg-gray-600 text-white px-3 py-2 rounded hover:bg-gray-700 transition-colors text-sm">
                                <i class="fas fa-filter mr-1"></i>
                                فلترة
                            </button>
                            <a href="/investor/funds/dashboard" class="bg-gray-400 text-white px-3 py-2 rounded hover:bg-gray-500 transition-colors text-sm">
                                <i class="fas fa-times mr-1"></i>
                                مسح
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Funds Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php $__currentLoopData = $funds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fund): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-green-500 to-teal-600 p-4">
                        <div class="flex items-center justify-between">
                            <span class="bg-white/20 text-white text-xs font-semibold px-2 py-1 rounded-full">
                                <?php echo e(ucfirst($fund->type)); ?>

                            </span>
                            <?php if($fund->featured): ?>
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-semibold px-2 py-1 rounded-full">
                                مميز
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-3"><?php echo e($fund->name); ?></h3>
                        <p class="text-gray-600 mb-4"><?php echo e(Str::limit($fund->description, 100)); ?></p>

                        <!-- Manager -->
                        <div class="flex items-center mb-4">
                            <div class="bg-gray-100 rounded-full p-2 mr-3">
                                <i class="fas fa-building text-gray-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">مدير الصندوق</p>
                                <p class="text-sm font-semibold text-gray-800"><?php echo e($fund->manager); ?></p>
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="space-y-3 mb-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">الحد الأدنى للاستثمار</span>
                                <span class="text-sm font-semibold text-gray-800">$<?php echo e(number_format($fund->min_investment)); ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">العائد المتوقع</span>
                                <span class="text-sm font-semibold text-green-600"><?php echo e(number_format($fund->expected_return, 2)); ?>%</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">المدة</span>
                                <span class="text-sm font-semibold text-gray-800"><?php echo e($fund->duration); ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">مستوى المخاطرة</span>
                                <span class="text-sm font-semibold px-2 py-1 rounded-full <?php if($fund->risk_level == 'low'): ?> bg-green-100 text-green-800 <?php elseif($fund->risk_level == 'medium'): ?> bg-yellow-100 text-yellow-800 <?php else: ?> bg-red-100 text-red-800 <?php endif; ?>">
                                    <?php echo e($fund->risk_level == 'low' ? 'منخفض' : ($fund->risk_level == 'medium' ? 'متوسط' : 'مرتفع')); ?>

                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">عدد المستثمرين</span>
                                <span class="text-sm font-semibold text-blue-600"><?php echo e($fund->investors_count); ?></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">الممول حتى الآن</span>
                                <span class="text-sm font-semibold text-gray-800">$<?php echo e(number_format($fund->total_funded)); ?></span>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs text-gray-600">نسبة التمويل</span>
                                <span class="text-xs text-gray-800"><?php echo e(round(($fund->total_funded / $fund->funding_goal) * 100, 1)); ?>%</span>
                            </div>
                            <div class="bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: <?php echo e(($fund->total_funded / $fund->funding_goal) * 100); ?>%"></div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-2">
                            <a href="/investor/funds/<?php echo e($fund->id); ?>" class="flex-1 bg-blue-500 text-white py-2 px-4 rounded-lg hover:bg-blue-600 transition-colors text-center">
                                <i class="fas fa-chart-line ml-2"></i>
                                تفاصيل
                            </a>
                            <button onclick="investInFund(<?php echo e($fund->id); ?>)" class="flex-1 bg-green-500 text-white py-2 px-4 rounded-lg hover:bg-green-600 transition-colors">
                                <i class="fas fa-hand-holding-usd ml-2"></i>
                                استثمر
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                <?php echo e($funds->links()); ?>

            </div>

            <!-- Empty State -->
            <?php if($funds->isEmpty()): ?>
            <div class="text-center py-12">
                <div class="bg-gray-100 rounded-full p-4 w-16 h-16 mx-auto mb-4">
                    <i class="fas fa-search text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 mb-2">لا توجد صناديق استثمارية حالياً</h3>
                <p class="text-gray-600">تحقق لاحقاً للحصول على صناديق استثمارية جديدة</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Investment Modal -->
    <script>
        function investInFund(fundId) {
            // Show investment modal
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            modal.innerHTML = `
                <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">استثمار في الصندوق</h3>
                    <form id="investmentForm" onsubmit="processInvestment(event, ${fundId})">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">مبلغ الاستثمار</label>
                                <input type="number" name="amount" required min="1000" step="100" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="أدخل مبلغ الاستثمار">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
                                <textarea name="notes" rows="3" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                                          placeholder="أي ملاحظات إضافية"></textarea>
                            </div>
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" name="terms" required class="ml-2">
                                    <span class="text-sm text-gray-700">أوافق على الشروط والأحكام</span>
                                </label>
                            </div>
                            <!-- CSRF Token -->
                            <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                        </div>
                        <div class="flex justify-end space-x-2 mt-6">
                            <button type="button" onclick="closeInvestmentModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                                إلغاء
                            </button>
                            <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                                تأكيد الاستثمار
                            </button>
                        </div>
                    </form>
                </div>
            `;
            
            document.body.appendChild(modal);
        }

        function closeInvestmentModal() {
            const modal = document.querySelector('.fixed.inset-0');
            if (modal) {
                modal.remove();
            }
        }

        function processInvestment(event, fundId) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            
            // Show loading state
            const submitBtn = event.target.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري المعالجة...';
            submitBtn.disabled = true;
            
            // Send data to server
            fetch(`/api/investor/funds/${fundId}/invest`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeInvestmentModal();
                    showNotification('تم إرسال طلب الاستثمار بنجاح!', 'success');
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                } else {
                    showNotification(data.message || 'حدث خطأ في طلب الاستثمار', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('حدث خطأ في الاتصال بالخادم', 'error');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        }

        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 px-4 py-3 rounded-lg text-white z-50 ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 
                'bg-blue-500'
            }`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${
                        type === 'success' ? 'fa-check-circle' : 
                        type === 'error' ? 'fa-exclamation-circle' : 
                        'fa-info-circle'
                    } ml-2"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/investor/funds/index.blade.php ENDPATH**/ ?>