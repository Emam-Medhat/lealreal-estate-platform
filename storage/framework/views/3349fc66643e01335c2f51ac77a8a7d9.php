<?php $__env->startSection('title', 'Modules Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Module Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <h3>6</h3>
                            <p>Active Modules</p>
                        </div>
                        <div class="col-md-3">
                            <h3>2.0</h3>
                            <p>Average Version</p>
                        </div>
                        <div class="col-md-3">
                            <h3>100%</h3>
                            <p>System Health</p>
                        </div>
                        <div class="col-md-3">
                            <h3>24/7</h3>
                            <p>Uptime</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Module Grid -->
    <div class="row" id="modulesGrid">
        <!-- Modules will be loaded here -->
    </div>

    <!-- Module Details Modal -->
    <div class="modal fade" id="moduleDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Module Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="moduleDetailsContent">
                    <!-- Module details will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const apiRoutes = {
    list: "<?php echo e(route('modules.list')); ?>",
    details: "<?php echo e(route('modules.details', ['moduleKey' => ':key'])); ?>",
    toggle: "<?php echo e(route('modules.toggle', ['moduleKey' => ':key'])); ?>",
    configure: "<?php echo e(route('modules.configure', ['moduleKey' => ':key'])); ?>"
};

$(document).ready(function() {
    loadModules();
});

function loadModules() {
    $.ajax({
        url: apiRoutes.list,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                renderModules(response.modules);
            }
        },
        error: function() {
            $('#modulesGrid').html(`
                <div class="col-12">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle"></i> Failed to load modules
                    </div>
                </div>
            `);
        }
    });
}

function renderModules(modules) {
    const grid = $('#modulesGrid');
    grid.empty();

    Object.keys(modules).forEach(key => {
        const module = modules[key];
        const moduleCard = createModuleCard(key, module);
        grid.append(moduleCard);
    });
}

function createModuleCard(key, module) {
    const statusBadge = getStatusBadge(module.status);
    const colorClass = getColorClass(module.color);
    
    return `
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card module-card ${colorClass}" data-module-key="${key}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="module-icon">
                            <i class="${module.icon} fa-3x text-${module.color}"></i>
                        </div>
                        <div class="module-status">
                            ${statusBadge}
                        </div>
                    </div>
                    
                    <h5 class="card-title">${module.name}</h5>
                    <p class="card-text text-muted">${module.description}</p>
                    
                    <div class="module-routes mb-3">
                        <small class="text-muted">Routes:</small>
                        <div class="d-flex flex-wrap">
                            ${Object.keys(module.routes).slice(0, 3).map(routeKey => 
                                `<span class="badge badge-outline-${module.color} me-1 mb-1">${module.routes[routeKey]}</span>`
                            ).join('')}
                            ${Object.keys(module.routes).length > 3 ? 
                                `<span class="badge badge-outline-secondary me-1 mb-1">+${Object.keys(module.routes).length - 3} more</span>` : ''
                            }
                        </div>
                    </div>
                    
                    <div class="module-actions">
                        <div class="btn-group btn-group-sm w-100">
                            <button class="btn btn-outline-primary view-module" data-module-key="${key}">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button class="btn btn-outline-success configure-module" data-module-key="${key}">
                                <i class="fas fa-cog"></i> Configure
                            </button>
                            <button class="btn btn-outline-info toggle-module" data-module-key="${key}" data-status="${module.status}">
                                <i class="fas fa-power-off"></i> ${module.status === 'active' ? 'Disable' : 'Enable'}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function getStatusBadge(status) {
    switch(status) {
        case 'active':
            return '<span class="badge badge-success">Active</span>';
        case 'inactive':
            return '<span class="badge badge-secondary">Inactive</span>';
        case 'maintenance':
            return '<span class="badge badge-warning">Maintenance</span>';
        default:
            return '<span class="badge badge-secondary">Unknown</span>';
    }
}

function getColorClass(color) {
    return `border-${color}`;
}

// Module Actions
$(document).on('click', '.view-module', function() {
    const moduleKey = $(this).data('module-key');
    viewModuleDetails(moduleKey);
});

$(document).on('click', '.configure-module', function() {
    const moduleKey = $(this).data('module-key');
    configureModule(moduleKey);
});

$(document).on('click', '.toggle-module', function() {
    const moduleKey = $(this).data('module-key');
    const currentStatus = $(this).data('status');
    const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
    
    toggleModule(moduleKey, newStatus);
});

function viewModuleDetails(moduleKey) {
    $.ajax({
        url: apiRoutes.details.replace(':key', moduleKey),
        method: 'GET',
        success: function(response) {
            if (response.success) {
                renderModuleDetails(response.module);
                $('#moduleDetailsModal').modal('show');
            }
        },
        error: function() {
            alert('Failed to load module details');
        }
    });
}

function renderModuleDetails(module) {
    const statistics = Object.keys(module.statistics).map(key => 
        `<div class="col-md-3 text-center">
            <h4>${module.statistics[key]}</h4>
            <small class="text-muted">${key.replace('_', ' ').toUpperCase()}</small>
        </div>`
    ).join('');

    const recentActivity = module.recent_activity.map(activity => 
        `<div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <small class="text-muted">${new Date(activity.timestamp).toLocaleString()}</small>
                <div>${activity.message}</div>
            </div>
            <small class="text-muted">${activity.user}</small>
        </div>`
    ).join('');

    const content = `
        <div class="module-details">
            <div class="row mb-4">
                <div class="col-md-8">
                    <h4>${module.name}</h4>
                    <p class="text-muted">${module.description}</p>
                    <div class="module-meta">
                        <span class="badge badge-primary">Version ${module.version}</span>
                        <span class="badge badge-success">${module.status}</span>
                    </div>
                </div>
                <div class="col-md-4 text-right">
                    <i class="${module.icon} fa-4x text-${module.color}"></i>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-12">
                    <h6>Statistics</h6>
                    <div class="row">
                        ${statistics}
                    </div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <h6>Available Routes</h6>
                    <div class="d-flex flex-wrap">
                        ${Object.keys(module.routes).map(routeKey => 
                            `<span class="badge badge-outline-primary me-1 mb-1">${module.routes[routeKey]}</span>`
                        ).join('')}
                    </div>
                </div>
                <div class="col-md-6">
                    <h6>Recent Activity</h6>
                    <div class="activity-list">
                        ${recentActivity}
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-12">
                    <h6>Settings</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" ${module.settings.enabled ? 'checked' : ''} disabled>
                                <label class="form-check-label">Enabled</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" ${module.settings.auto_moderation ? 'checked' : ''} disabled>
                                <label class="form-check-label">Auto Moderation</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" ${module.settings.notifications ? 'checked' : ''} disabled>
                                <label class="form-check-label">Notifications</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" ${module.settings.analytics_tracking ? 'checked' : ''} disabled>
                                <label class="form-check-label">Analytics Tracking</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#moduleDetailsContent').html(content);
}

function configureModule(moduleKey) {
    // Redirect to module configuration page
    window.open(apiRoutes.configure.replace(':key', moduleKey), '_blank');
}

function toggleModule(moduleKey, status) {
    $.ajax({
        url: apiRoutes.toggle.replace(':key', moduleKey),
        method: 'POST',
        data: {
            status: status,
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                alert(response.message);
                loadModules(); // Reload modules to show updated status
            } else {
                alert('Failed to toggle module: ' + response.message);
            }
        },
        error: function() {
            alert('Error toggling module');
        }
    });
}
</script>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.module-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.module-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.module-icon {
    text-align: center;
    margin-bottom: 15px;
}

.module-status {
    text-align: right;
}

.module-routes {
    border-top: 1px solid #eee;
    padding-top: 10px;
}

.module-actions {
    border-top: 1px solid #eee;
    padding-top: 15px;
}

.activity-list {
    max-height: 200px;
    overflow-y: auto;
}

.module-details {
    max-height: 500px;
    overflow-y: auto;
}
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/modules/dashboard.blade.php ENDPATH**/ ?>