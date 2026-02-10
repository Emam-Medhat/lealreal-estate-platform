

<?php $__env->startSection('title', 'نظام الصيانة'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-3">نظام الصيانة</h1>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo e(App\Models\MaintenanceRequest::count()); ?></h4>
                            <p class="mb-0">إجمالي طلبات الصيانة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-tools fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo e(App\Models\MaintenanceRequest::where('status', 'pending')->count()); ?></h4>
                            <p class="mb-0">طلبات معلقة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo e(App\Models\MaintenanceRequest::where('status', 'completed')->count()); ?></h4>
                            <p class="mb-0">طلبات مكتملة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0"><?php echo e(App\Models\EmergencyRepair::where('status', '!=', 'completed')->count()); ?></h4>
                            <p class="mb-0">إصلاحات طارئة</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <a href="<?php echo e(route('maintenance.create')); ?>" class="btn btn-primary btn-block">
                                <i class="fas fa-plus"></i> طلب صيانة جديد
                            </a>
                        </div>
                        <div class="col-md-2 mb-3">
                            <a href="<?php echo e(route('maintenance.schedule.create')); ?>" class="btn btn-info btn-block">
                                <i class="fas fa-calendar"></i> جدولة صيانة
                            </a>
                        </div>
                        <div class="col-md-2 mb-3">
                            <a href="<?php echo e(route('maintenance.workorders.create')); ?>" class="btn btn-danger btn-block">
                                <i class="fas fa-exclamation-triangle"></i> أمر عمل جديد
                            </a>
                        </div>
                        <div class="col-md-2 mb-3">
                            <a href="<?php echo e(route('inventory.items.create')); ?>" class="btn btn-success btn-block">
                                <i class="fas fa-box"></i> إضافة مخزون
                            </a>
                        </div>
                        <div class="col-md-2 mb-3">
                            <a href="<?php echo e(route('maintenance.workorders.create')); ?>" class="btn btn-warning btn-block">
                                <i class="fas fa-clipboard"></i> أمر عمل
                            </a>
                        </div>
                        <div class="col-md-2 mb-3">
                            <a href="<?php echo e(route('payments.invoices.create')); ?>" class="btn btn-secondary btn-block">
                                <i class="fas fa-file-invoice"></i> فاتورة
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">آخر طلبات الصيانة</h5>
                </div>
                <div class="card-body">
                    <?php if($recentRequests->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>رقم الطلب</th>
                                        <th>العنوان</th>
                                        <th>الحالة</th>
                                        <th>الأولوية</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $recentRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($request->request_number); ?></td>
                                        <td><?php echo e($request->title); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo e($request->status_color); ?>">
                                                <?php echo e($request->status_label); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?php echo e($request->priority_color); ?>">
                                                <?php echo e($request->priority_label); ?>

                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">لا توجد طلبات صيانة حديثة</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">جدول الصيانة اليوم</h5>
                </div>
                <div class="card-body">
                    <?php if($todaySchedules->count() > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>الوقت</th>
                                        <th>النشاط</th>
                                        <th>العقار</th>
                                        <th>الفريق</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $todaySchedules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $schedule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($schedule->scheduled_date->format('H:i')); ?></td>
                                        <td><?php echo e($schedule->title); ?></td>
                                        <td><?php echo e($schedule->property->title ?? 'N/A'); ?></td>
                                        <td><?php echo e($schedule->maintenanceTeam->name ?? 'غير محدد'); ?></td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">لا توجد جداول صيانة اليوم</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">حالة طلبات الصيانة</h5>
                </div>
                <div class="card-body">
                    <canvas id="requestStatusChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">أولوية طلبات الصيانة</h5>
                </div>
                <div class="card-body">
                    <canvas id="requestPriorityChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Request Status Chart
    const statusCtx = document.getElementById('requestStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['في انتظار', 'مكلف', 'قيد التنفيذ', 'مكتمل', 'ملغي'],
            datasets: [{
                data: [
                    <?php echo e(App\Models\MaintenanceRequest::where('status', 'pending')->count()); ?>,
                    <?php echo e(App\Models\MaintenanceRequest::where('status', 'assigned')->count()); ?>,
                    <?php echo e(App\Models\MaintenanceRequest::where('status', 'in_progress')->count()); ?>,
                    <?php echo e(App\Models\MaintenanceRequest::where('status', 'completed')->count()); ?>,
                    <?php echo e(App\Models\MaintenanceRequest::where('status', 'cancelled')->count()); ?>

                ],
                backgroundColor: ['#6c757d', '#007bff', '#ffc107', '#28a745', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Request Priority Chart
    const priorityCtx = document.getElementById('requestPriorityChart').getContext('2d');
    new Chart(priorityCtx, {
        type: 'bar',
        data: {
            labels: ['منخفض', 'متوسط', 'عالي', 'طوارئ'],
            datasets: [{
                label: 'عدد الطلبات',
                data: [
                    <?php echo e(App\Models\MaintenanceRequest::where('priority', 'low')->count()); ?>,
                    <?php echo e(App\Models\MaintenanceRequest::where('priority', 'medium')->count()); ?>,
                    <?php echo e(App\Models\MaintenanceRequest::where('priority', 'high')->count()); ?>,
                    <?php echo e(App\Models\MaintenanceRequest::where('priority', 'emergency')->count()); ?>

                ],
                backgroundColor: ['#28a745', '#ffc107', '#fd7e14', '#dc3545']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/maintenance/index.blade.php ENDPATH**/ ?>