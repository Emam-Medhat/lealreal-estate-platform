<?php
    // Category keywords for highly relevant expressive images
    $catKeywords = [
        'core' => 'technology,server,database',
        'financial' => 'finance,chart,business',
        'ai' => 'artificial,intelligence,data',
        'blockchain' => 'crypto,network,blockchain',
        'properties' => 'realestate,architecture,city',
        'advanced' => 'metaverse,data,iot',
        'growth' => 'growth,marketing,team',
        'operations' => 'office,desk,planning'
    ];
?>

<?php $__env->startPush('styles'); ?>
<style>
    .cat-section {
        margin-bottom: 3.5rem;
    }

    .cat-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
        direction: rtl;
    }

    .cat-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        color: white;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .cat-title-group {
        display: flex;
        flex-direction: column;
    }

    .cat-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: #1e293b;
        margin: 0;
    }

    .cat-subtitle {
        font-size: 0.75rem;
        color: #94a3b8;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .modules-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
        direction: rtl;
    }

    /* Premium Module Card */
    .hub-mod-card {
        background: #ffffff;
        border: 1px solid rgba(229, 231, 235, 0.8);
        border-radius: 20px;
        padding: 0;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        position: relative;
        overflow: hidden;
    }

    .hub-mod-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
        border-color: rgba(99, 102, 241, 0.2);
    }

    /* Card Image Section */
    .mod-card-image {
        width: 100%;
        height: 160px;
        position: relative;
        overflow: hidden;
        border-bottom: 1px solid rgba(229, 231, 235, 0.5);
    }

    .mod-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }

    .hub-mod-card:hover .mod-card-image img {
        transform: scale(1.1);
    }

    .mod-card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.4) 100%);
    }

    .mod-card-body {
        padding: 24px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .mod-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .mod-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        background: #f8fafc;
        color: #6366f1;
        border: 1px solid #f1f5f9;
        transition: all 0.3s ease;
    }

    .hub-mod-card:hover .mod-icon-wrapper {
        transform: scale(1.1) rotate(5deg);
        background: #eef2ff;
        color: #4f46e5;
    }

    .status-badge {
        font-size: 0.65rem;
        font-weight: 800;
        padding: 4px 10px;
        border-radius: 8px;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    .status-active {
        background: #f0fdf4;
        color: #16a34a;
        border: 1px solid #dcfce7;
    }

    .status-planned {
        background: #f8fafc;
        color: #64748b;
        border: 1px solid #f1f5f9;
    }

    .mod-info h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 8px;
    }

    .mod-info p {
        font-size: 0.85rem;
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 20px;
    }

    .mod-features {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 24px;
    }

    .feature-tag {
        font-size: 0.7rem;
        font-weight: 700;
        color: #6366f1;
        background: #f5f3ff;
        padding: 4px 10px;
        border-radius: 8px;
        border: 1px solid #ede9fe;
    }

    .mod-card-footer {
        margin-top: auto;
        padding-top: 16px;
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .mod-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 10px;
        font-size: 0.8rem;
        font-weight: 700;
        transition: all 0.3s ease;
        text-decoration: none !important;
    }

    .btn-active {
        background: #1e293b;
        color: white !important;
    }

    .btn-active:hover {
        background: #4f46e5;
        transform: scale(1.05);
    }

    .btn-soon {
        background: #f8fafc;
        color: #94a3b8 !important;
        border: 1px solid #f1f5f9;
        cursor: not-allowed;
    }

    .team-avatars {
        display: flex;
        align-items: center;
    }

    .avatar-mini {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        border: 2px solid white;
        background: #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.6rem;
        font-weight: 800;
        color: #64748b;
        margin-right: -8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .avatar-mini:first-child {
        margin-right: 0;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__currentLoopData = $data['categories']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="cat-section category-section" data-category="<?php echo e($category['id']); ?>">

    
    <div class="cat-header">
        <div class="cat-icon-box" style="background: <?php echo e($category['color'] ?? '#6366f1'); ?>">
            <i class="fas <?php echo e($category['icon'] ?? 'fa-layer-group'); ?>"></i>
        </div>
        <div class="cat-title-group">
            <h2 class="cat-title"><?php echo e($category['name']); ?></h2>
            <span class="cat-subtitle"><?php echo e($category['id']); ?> system</span>
        </div>
    </div>

    
    <div class="modules-grid">
        <?php $__currentLoopData = $category['modules']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $route    = $module['route'] ?? null;
            $hasRoute = !empty($route) && Route::has($route);
            $status   = $hasRoute ? (app()->getLocale() == 'ar' ? 'نشط' : 'Active') : (app()->getLocale() == 'ar' ? 'قريباً' : 'Soon');
            
            // Dynamic Expressive Image generation using LoremFlickr + Category Keywords
            $seed = crc32($module['name']); // Stable image per module
            $keyword = $catKeywords[$category['id']] ?? 'business,technology';
            $imgUrl = "https://loremflickr.com/500/250/{$keyword}/all?lock={$seed}";
        ?>

        <div class="hub-mod-card module-card">
            
            <div class="mod-card-image" style="background: <?php echo e($category['color'] ?? '#6366f1'); ?>;">
                <img src="<?php echo e($imgUrl); ?>" alt="<?php echo e($module['name']); ?>" 
                     onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='flex';" 
                     style="width: 100%; height: 100%; object-fit: cover;">
                
                
                <div class="fallback-gradient" style="display: none; width: 100%; height: 100%; background: linear-gradient(135deg, <?php echo e($category['color'] ?? '#6366f1'); ?>cc, <?php echo e($category['color'] ?? '#6366f1'); ?>); align-items: center; justify-content: center; position: absolute; top:0; left:0;">
                    <i class="fas <?php echo e($module['icon'] ?? 'fa-cube'); ?>" style="font-size: 80px; color: rgba(255,255,255,0.4);"></i>
                </div>
                
                <div class="mod-card-overlay"></div>
            </div>

            <div class="mod-card-body">
                
                <div class="mod-header">
                    <div class="mod-icon-wrapper">
                        <i class="fas <?php echo e($module['icon'] ?? 'fa-cube'); ?>"></i>
                    </div>
                    <span class="status-badge <?php echo e($hasRoute ? 'status-active' : 'status-planned'); ?>">
                        <?php echo e($status); ?>

                    </span>
                </div>

                
                <div class="mod-info">
                    <h3><?php echo e($module['name']); ?></h3>
                    <p><?php echo e($module['description']); ?></p>

                    <div class="mod-features">
                        <?php $__currentLoopData = $module['features']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="feature-tag"><?php echo e($feature); ?></span>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                
                <div class="mod-card-footer">
                    <div class="team-avatars">
                        <div class="avatar-mini" style="background: #e0e7ff; color: #4338ca;">AI</div>
                        <div class="avatar-mini" style="background: #ecfeff; color: #0891b2;">RD</div>
                    </div>

                    <?php if($hasRoute): ?>
                    <a href="<?php echo e(route($route)); ?>" class="mod-action-btn btn-active">
                        <?php echo e(app()->getLocale() === 'ar' ? 'دخول' : 'Enter'); ?>

                        <i class="fas fa-arrow-left text-[10px] mr-1"></i>
                    </a>
                    <?php else: ?>
                    <span class="mod-action-btn btn-soon">
                        <?php echo e(app()->getLocale() === 'ar' ? 'قريباً' : 'Soon'); ?>

                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php /**PATH D:\larvel state big\resources\views/admin/partials/hub_grid.blade.php ENDPATH**/ ?>