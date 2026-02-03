

<?php $__env->startSection('title', 'User Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Welcome back, <?php echo e(Auth::user()->name); ?>!</h1>
                    <p class="text-gray-600">Here's what's happening with your account today</p>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-gray-500"><?php echo e(now()->format('M j, Y')); ?></span>
                    <button onclick="refreshDashboard()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Refresh
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-home text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Properties Viewed</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo e($stats['properties_viewed']); ?></p>
                        <p class="text-sm text-green-600">+<?php echo e($stats['properties_viewed_today']); ?> today</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-heart text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Saved Properties</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo e($stats['saved_properties']); ?></p>
                        <p class="text-sm text-green-600">+<?php echo e($stats['saved_today']); ?> today</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-search text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Searches</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo e($stats['searches']); ?></p>
                        <p class="text-sm text-green-600">+<?php echo e($stats['searches_today']); ?> today</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-bell text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Notifications</p>
                        <p class="text-2xl font-bold text-gray-800"><?php echo e($stats['notifications']); ?></p>
                        <p class="text-sm text-green-600"><?php echo e($stats['unread_notifications']); ?> unread</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity & Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Activity</h3>
                <div class="space-y-3">
                    <?php $__empty_1 = true; $__currentLoopData = $stats['recent_activity']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <div class="bg-blue-100 rounded-full p-2 mr-3">
                                <i class="fas fa-<?php echo e($activity['icon']); ?> text-blue-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-800"><?php echo e($activity['message']); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e($activity['time']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-gray-500 text-sm">No recent activity</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Saved Properties -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Recently Saved</h3>
                <div class="space-y-3">
                    <?php $__empty_1 = true; $__currentLoopData = $stats['recently_saved']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $property): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <div class="bg-green-100 rounded-full p-2 mr-3">
                                <i class="fas fa-home text-green-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800"><?php echo e($property['title']); ?></p>
                                <p class="text-xs text-gray-500">$<?php echo e(number_format($property['price'], 0)); ?></p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-gray-500 text-sm">No saved properties</p>
                    <?php endif; ?>
                </div>
                <div class="mt-4">
                    <a href="<?php echo e(route('properties.favorites')); ?>" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View All Saved →
                    </a>
                </div>
            </div>
            
            <!-- Notifications -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Latest Notifications</h3>
                <div class="space-y-3">
                    <?php $__empty_1 = true; $__currentLoopData = $stats['latest_notifications']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <div class="bg-yellow-100 rounded-full p-2 mr-3">
                                <i class="fas fa-bell text-yellow-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-800"><?php echo e($notification['message']); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e($notification['time']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-gray-500 text-sm">No notifications</p>
                    <?php endif; ?>
                </div>
                <div class="mt-4">
                    <a href="<?php echo e(route('user.notifications')); ?>" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View All Notifications →
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="<?php echo e(route('properties.index')); ?>" class="bg-blue-50 border border-blue-200 rounded-lg p-4 hover:bg-blue-100 transition-colors text-center">
                    <i class="fas fa-search text-blue-600 text-2xl mb-2"></i>
                    <p class="font-medium text-gray-800">Browse Properties</p>
                </a>
                
                <a href="<?php echo e(route('properties.favorites')); ?>" class="bg-green-50 border border-green-200 rounded-lg p-4 hover:bg-green-100 transition-colors text-center">
                    <i class="fas fa-heart text-green-600 text-2xl mb-2"></i>
                    <p class="font-medium text-gray-800">Saved Properties</p>
                </a>
                
                <a href="<?php echo e(route('user.profile.edit')); ?>" class="bg-purple-50 border border-purple-200 rounded-lg p-4 hover:bg-purple-100 transition-colors text-center">
                    <i class="fas fa-user text-purple-600 text-2xl mb-2"></i>
                    <p class="font-medium text-gray-800">Edit Profile</p>
                </a>
                
                <a href="<?php echo e(route('wallet.index')); ?>" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 hover:bg-yellow-100 transition-colors text-center">
                    <i class="fas fa-wallet text-yellow-600 text-2xl mb-2"></i>
                    <p class="font-medium text-gray-800">My Wallet</p>
                </a>
            </div>
        </div>

        <!-- Property Recommendations -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Recommended for You</h3>
                <a href="<?php echo e(route('properties.recommendations')); ?>" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    View All →
                </a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <?php $__empty_1 = true; $__currentLoopData = $stats['recommendations']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $property): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="border rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                        <div class="h-48 bg-gray-200 relative">
                            <?php if($property['image']): ?>
                                <img src="<?php echo e($property['image']); ?>" alt="<?php echo e($property['title']); ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <i class="fas fa-home text-gray-400 text-4xl"></i>
                                </div>
                            <?php endif; ?>
                            <div class="absolute top-2 right-2">
                                <span class="bg-green-500 text-white px-2 py-1 rounded text-xs">
                                    <?php echo e($property['match_score']); ?>% Match
                                </span>
                            </div>
                        </div>
                        <div class="p-4">
                            <h4 class="font-semibold text-gray-800 mb-2"><?php echo e($property['title']); ?></h4>
                            <p class="text-gray-600 text-sm mb-2"><?php echo e($property['location']); ?></p>
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold text-gray-800">$<?php echo e(number_format($property['price'], 0)); ?></span>
                                <button class="bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                                    View Details
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="col-span-full text-center py-8">
                        <i class="fas fa-home text-4xl text-gray-300 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No recommendations yet</h3>
                        <p class="text-gray-500">Start browsing properties to get personalized recommendations.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function refreshDashboard() {
    location.reload();
}

// Auto-refresh every 5 minutes
setInterval(refreshDashboard, 300000);
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/user/dashboard.blade.php ENDPATH**/ ?>