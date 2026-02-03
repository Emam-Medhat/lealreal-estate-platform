

<?php $__env->startSection('title', 'System Maintenance'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">System Maintenance</h1>
                    <p class="text-gray-600">Perform system maintenance tasks and optimizations</p>
                </div>
            </div>
        </div>

        <!-- Maintenance Status -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-clock text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Last Run</p>
                        <p class="text-lg font-bold text-gray-800"><?php echo e($maintenance['last_run']->format('M j, Y H:i')); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-calendar text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Next Scheduled</p>
                        <p class="text-lg font-bold text-gray-800"><?php echo e($maintenance['next_scheduled']->format('M j, Y H:i')); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-tasks text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tasks Available</p>
                        <p class="text-lg font-bold text-gray-800">3 Tasks</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance Tasks -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Maintenance Tasks</h3>
            
            <form action="<?php echo e(route('admin.maintenance.run')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-blue-100 rounded-full p-2 mr-3">
                                <i class="fas fa-broom text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">Clear System Cache</h4>
                                <p class="text-sm text-gray-500">Remove temporary files and clear application cache</p>
                            </div>
                        </div>
                        <label class="flex items-center">
                            <input type="checkbox" name="tasks[]" value="cache" class="mr-2">
                            <span class="text-sm text-gray-700">Run</span>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-green-100 rounded-full p-2 mr-3">
                                <i class="fas fa-file-alt text-green-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">Clean Log Files</h4>
                                <p class="text-sm text-gray-500">Archive and clean old system log files</p>
                            </div>
                        </div>
                        <label class="flex items-center">
                            <input type="checkbox" name="tasks[]" value="logs" class="mr-2">
                            <span class="text-sm text-gray-700">Run</span>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="bg-purple-100 rounded-full p-2 mr-3">
                                <i class="fas fa-database text-purple-600"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-800">Optimize Database</h4>
                                <p class="text-sm text-gray-500">Optimize database tables and rebuild indexes</p>
                            </div>
                        </div>
                        <label class="flex items-center">
                            <input type="checkbox" name="tasks[]" value="database" class="mr-2">
                            <span class="text-sm text-gray-700">Run</span>
                        </label>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-play mr-2"></i>
                        Run Selected Tasks
                    </button>
                </div>
            </form>
        </div>

        <!-- System Health -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">System Health</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="font-medium text-gray-700 mb-3">Storage Usage</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Database</span>
                            <span class="text-sm font-medium">125 MB</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: 25%"></div>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Uploads</span>
                            <span class="text-sm font-medium">380 MB</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: 38%"></div>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Logs</span>
                            <span class="text-sm font-medium">45 MB</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-600 h-2 rounded-full" style="width: 9%"></div>
                        </div>
                    </div>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-700 mb-3">Performance Metrics</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">CPU Usage</span>
                            <span class="text-sm font-medium text-green-600">12%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: 12%"></div>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Memory Usage</span>
                            <span class="text-sm font-medium text-yellow-600">65%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-600 h-2 rounded-full" style="width: 65%"></div>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Disk Usage</span>
                            <span class="text-sm font-medium text-green-600">45%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: 45%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/admin/maintenance/index.blade.php ENDPATH**/ ?>