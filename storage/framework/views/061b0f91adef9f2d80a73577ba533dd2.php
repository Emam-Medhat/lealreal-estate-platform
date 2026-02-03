<?php $__env->startSection('title', 'عرض تقرير الأداء'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-3"><?php echo e($report->title); ?></h2>
                    <p class="text-muted mb-2"><?php echo e($report->description); ?></p>
                    <div class="d-flex align-items-center text-muted">
                        <small class="me-3">
                            <i class="fas fa-calendar"></i> 
                            <?php echo e($report->created_at->format('Y-m-d H:i')); ?>

                        </small>
                        <small class="me-3">
                            <i class="fas fa-user"></i> 
                            <?php echo e($report->generator->name ?? 'Unknown'); ?>

                        </small>
                        <small>
                            <span class="badge bg-<?php echo e($report->status == 'completed' ? 'success' : ($report->status == 'generating' ? 'warning' : 'danger')); ?>">
                                <?php echo e(ucfirst($report->status)); ?>

                            </span>
                        </small>
                    </div>
                </div>
                <div>
                    <a href="<?php echo e(route('reports.performance.index')); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> العودة
                    </a>
                    <button onclick="window.print()" class="btn btn-primary ms-2">
                        <i class="fas fa-print"></i> طباعة
                    </button>
                    <button onclick="exportReport()" class="btn btn-success ms-2">
                        <i class="fas fa-download"></i> تصدير
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>✅ نجاح:</strong> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>❌ خطأ:</strong> <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Report Status -->
    <?php if($report->status == 'generating'): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <div class="d-flex align-items-center">
                        <div class="spinner-border spinner-border-sm me-3" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <div>
                            <strong>جاري إنشاء التقرير...</strong>
                            <p class="mb-0">سيتم تحديث الصفحة تلقائياً عند اكتمال التقرير.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Report Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">إجمالي المبيعات</h6>
                            <h3 class="mb-0"><?php echo e($performanceReport->total_sales ?? 0); ?></h3>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">إجمالي الإيرادات</h6>
                            <h3 class="mb-0">$<?php echo e(number_format($performanceReport->total_revenue ?? 0, 2)); ?></h3>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">معدل التحويل</h6>
                            <h3 class="mb-0"><?php echo e($performanceReport->conversion_rate ?? 0); ?>%</h3>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-percentage"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-0">متوسط سعر البيع</h6>
                            <h3 class="mb-0">$<?php echo e(number_format($performanceReport->average_sale_price ?? 0, 2)); ?></h3>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="fas fa-home"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Details -->
    <div class="row">
        <div class="col-md-8">
            <!-- Performance Chart -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">أداء المبيعات</h5>
                </div>
                <div class="card-body">
                    <canvas id="performanceChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Top Agents -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">أفضل الوكلاء</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>الوكيل</th>
                                    <th>عدد العقارات</th>
                                    <th>إجمالي المبيعات</th>
                                    <th>معدل التحويل</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(isset($performanceReport->top_agents) && count($performanceReport->top_agents) > 0): ?>
                                    <?php $__currentLoopData = $performanceReport->top_agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($agent->name ?? 'Unknown'); ?></td>
                                            <td><?php echo e($agent->properties_count ?? 0); ?></td>
                                            <td>$<?php echo e(number_format($agent->total_sales ?? 0, 2)); ?></td>
                                            <td><?php echo e($agent->conversion_rate ?? 0); ?>%</td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">لا توجد بيانات متاحة</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Report Parameters -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">معلمات التقرير</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">الفترة:</label>
                        <p class="form-control-plaintext">
                            <?php echo e($report->parameters['period_start'] ?? 'N/A'); ?> إلى <?php echo e($report->parameters['period_end'] ?? 'N/A'); ?>

                        </p>
                    </div>
                    <?php if(isset($report->parameters['agent_id'])): ?>
                        <div class="mb-3">
                            <label class="form-label">الوكيل:</label>
                            <p class="form-control-plaintext">
                                <?php echo e($report->agent->name ?? 'All Agents'); ?>

                            </p>
                        </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label">التنسيق:</label>
                        <p class="form-control-plaintext">
                            <?php echo e(strtoupper($report->format ?? 'PDF')); ?>

                        </p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button onclick="refreshReport()" class="btn btn-outline-primary">
                            <i class="fas fa-sync"></i> تحديث التقرير
                        </button>
                        <button onclick="shareReport()" class="btn btn-outline-info">
                            <i class="fas fa-share"></i> مشاركة التقرير
                        </button>
                        <button onclick="duplicateReport()" class="btn btn-outline-success">
                            <i class="fas fa-copy"></i> نسخ التقرير
                        </button>
                        <button onclick="deleteReport()" class="btn btn-outline-danger">
                            <i class="fas fa-trash"></i> حذف التقرير
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Performance Chart
const ctx = document.getElementById('performanceChart').getContext('2d');
const performanceChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($performanceReport->monthly_labels ?? [], 15, 512) ?>,
        datasets: [{
            label: 'المبيعات',
            data: <?php echo json_encode($performanceReport->monthly_sales ?? [], 15, 512) ?>,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }, {
            label: 'الإيرادات',
            data: <?php echo json_encode($performanceReport->monthly_revenue ?? [], 15, 512) ?>,
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            tension: 0.1
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

// Auto-refresh if report is generating
<?php if($report->status == 'generating'): ?>
    setTimeout(() => {
        location.reload();
    }, 5000);
<?php endif; ?>

function exportReport() {
    window.location.href = `<?php echo e(route('reports.performance.export', $report)); ?>`;
}

function refreshReport() {
    location.reload();
}

function shareReport() {
    const url = window.location.href;
    navigator.clipboard.writeText(url).then(() => {
        alert('تم نسخ رابط التقرير!');
    });
}

function duplicateReport() {
    if (confirm('هل أنت متأكد من نسخ هذا التقرير؟')) {
        fetch(`/reports/performance/<?php echo e($report->id); ?>/duplicate`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.href = `/reports/performance/${data.report_id}`;
            } else {
                alert('خطأ: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('خطأ في نسخ التقرير');
        });
    }
}

function deleteReport() {
    if (confirm('هل أنت متأكد من حذف هذا التقرير؟')) {
        fetch(`/reports/performance/<?php echo e($report->id); ?>`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.href = '<?php echo e(route("reports.performance.index")); ?>';
            } else {
                alert('خطأ: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('خطأ في حذف التقرير');
        });
    }
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/reports/performance/show.blade.php ENDPATH**/ ?>