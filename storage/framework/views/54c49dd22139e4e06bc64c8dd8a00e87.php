<?php $__env->startSection('title', 'Investment Crowdfunding'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Investment Crowdfunding</h1>
                    <p class="mt-2 text-gray-600">Discover and invest in innovative projects through crowdfunding</p>
                </div>
                <div class="flex gap-2">
                    <a href="<?php echo e(route('investor.funds.index')); ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-chart-line ml-2"></i>
                        Investment Funds
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <form method="GET" action="<?php echo e(route('investor.crowdfunding.index')); ?>" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                        <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
                               placeholder="Search campaigns..." 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">All Categories</option>
                            <option value="Real Estate" <?php echo e(request('category') == 'Real Estate' ? 'selected' : ''); ?>>Real Estate</option>
                            <option value="Technology" <?php echo e(request('category') == 'Technology' ? 'selected' : ''); ?>>Technology</option>
                            <option value="Renewable Energy" <?php echo e(request('category') == 'Renewable Energy' ? 'selected' : ''); ?>>Renewable Energy</option>
                            <option value="Healthcare" <?php echo e(request('category') == 'Healthcare' ? 'selected' : ''); ?>>Healthcare</option>
                            <option value="Education" <?php echo e(request('category') == 'Education' ? 'selected' : ''); ?>>Education</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Min Investment</label>
                        <input type="number" name="min_investment" value="<?php echo e(request('min_investment')); ?>" 
                               placeholder="Minimum amount" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Max Investment</label>
                        <input type="number" name="max_investment" value="<?php echo e(request('max_investment')); ?>" 
                               placeholder="Maximum amount" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-filter ml-2"></i>
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Campaigns Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php $__empty_1 = true; $__currentLoopData = $campaigns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $campaign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden">
                    <!-- Header -->
                    <div class="relative h-48 bg-gradient-to-r from-blue-500 to-purple-600">
                        <div class="absolute inset-0 bg-black bg-opacity-20"></div>
                        <div class="absolute bottom-4 left-4 right-4">
                            <span class="inline-block px-3 py-1 text-xs font-semibold text-white bg-yellow-500 rounded-full">
                                <?php echo e($campaign->risk_level); ?>

                            </span>
                            <h3 class="mt-2 text-xl font-bold text-white"><?php echo e($campaign->campaign_name); ?></h3>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <div class="mb-4">
                            <span class="inline-block px-2 py-1 text-xs font-medium text-blue-600 bg-blue-100 rounded">
                                <?php echo e($campaign->category); ?>

                            </span>
                        </div>

                        <p class="text-gray-600 mb-4 line-clamp-3"><?php echo e($campaign->description); ?></p>

                        <!-- Progress -->
                        <div class="mb-4">
                            <div class="flex justify-between text-sm text-gray-600 mb-2">
                                <span>Progress</span>
                                <span><?php echo e(number_format((float)$campaign->total_raised / (float)$campaign->funding_goal * 100, 1)); ?>%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo e(min((float)$campaign->total_raised / (float)$campaign->funding_goal * 100, 100)); ?>%"></div>
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <p class="text-xs text-gray-500">Raised</p>
                                <p class="text-lg font-semibold text-gray-800">$<?php echo e(number_format((float)$campaign->total_raised, 0)); ?></p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500">Goal</p>
                                <p class="text-lg font-semibold text-gray-800">$<?php echo e(number_format((float)$campaign->funding_goal, 0)); ?></p>
                            </div>
                        </div>

                        <!-- Additional Info -->
                        <div class="text-sm text-gray-600 mb-4">
                            <div class="flex justify-between">
                                <span>Investors:</span>
                                <span><?php echo e($campaign->investor_count); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Min Investment:</span>
                                <span>$<?php echo e(number_format((float)$campaign->minimum_investment, 0)); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span>Expected Return:</span>
                                <span><?php echo e($campaign->projected_return_rate); ?>%</span>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="flex gap-2">
                            <a href="<?php echo e(route('investor.crowdfunding.show', $campaign)); ?>" 
                               class="flex-1 bg-blue-600 text-white text-center px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                View Details
                            </a>
                            <button onclick="watchCampaign(<?php echo e($campaign->id); ?>)" 
                                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="far fa-bookmark"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-span-full text-center py-12">
                    <div class="text-gray-400 mb-4">
                        <i class="fas fa-search text-6xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">No campaigns found</h3>
                    <p class="text-gray-600">Try adjusting your filters or check back later for new opportunities.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if($campaigns->hasPages()): ?>
            <div class="mt-8">
                <?php echo e($campaigns->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function watchCampaign(campaignId) {
    fetch(`/investor/crowdfunding/${campaignId}/watch`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Campaign added to your watchlist!');
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH F:\larvel state big\resources\views/investor/crowdfunding/index.blade.php ENDPATH**/ ?>