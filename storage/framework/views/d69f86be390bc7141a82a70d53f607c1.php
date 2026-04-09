

<?php $__env->startSection('title', 'لوحة التحكم'); ?>

<?php $__env->startSection('page-title', 'نظرة عامة'); ?>

<?php $__env->startPush('styles'); ?>
  <style>
    /* ============================================================
       DASHBOARD OVERHAUL - CLEAN, MODERN, AND COLORFUL
       ============================================================ */

    :root {
      --primary: #6366f1;
      --bg-body: #f8fafc;
      --card-bg: #ffffff;
      --text-main: #1e293b;
      --text-muted: #94a3b8;
      --border-light: #f1f5f9;
      --radius: 20px;
    }

    body {
      background-color: var(--bg-body) !important;
      font-family: 'Inter', sans-serif;
    }

    .page-background {
      min-height: 100vh;
      padding: 24px;
    }

    /* Part 1: Priority Quick Actions (Vibrant & Premium) */
    .qa-container {
      background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
      border: 1px solid rgba(229, 231, 235, 0.8);
      border-radius: 24px;
      padding: 32px;
      margin-bottom: 32px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    }

    .qa-header-title {
      font-size: 1.25rem;
      font-weight: 800;
      color: #0f172a;
      display: flex;
      align-items: center;
      gap: 12px;
      margin-bottom: 24px;
    }

    .qa-header-title i {
      color: #f59e0b;
      font-size: 1.5rem;
      background: #fffbeb;
      padding: 8px;
      border-radius: 12px;
    }

    .quick-action-btn {
      background: #ffffff;
      border: 1px solid rgba(229, 231, 235, 0.6);
      border-radius: 20px;
      padding: 24px 16px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-decoration: none !important;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
      position: relative;
      overflow: hidden;
      z-index: 1;
    }

    .quick-action-btn:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.06);
      border-color: rgba(99, 102, 241, 0.2);
    }

    .qa-icon-wrapper {
      width: 56px;
      height: 56px;
      border-radius: 16px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 16px;
      font-size: 24px;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      border: 1px solid transparent;
    }

    /* Soft, Professional Colors matching the site theme */
    .qa-user { background-color: #eff6ff; color: #2563eb; border-color: #dbeafe; }
    .qa-property { background-color: #eef2ff; color: #4f46e5; border-color: #e0e7ff; }
    .qa-post { background-color: #f8fafc; color: #334155; border-color: #f1f5f9; }
    .qa-investor { background-color: #f0fdf4; color: #059669; border-color: #dcfce7; }
    .qa-settings { background-color: #f8fafc; color: #475569; border-color: #f1f5f9; }
    .qa-maintenance { background-color: #fff7ed; color: #d97706; border-color: #ffedd5; }

    .quick-action-btn:hover .qa-icon-wrapper {
      transform: scale(1.1) rotate(4deg);
      box-shadow: 0 8px 16px rgba(0,0,0,0.06);
    }

    .quick-action-btn p {
      margin: 0;
      font-size: 1rem;
      font-weight: 700;
      color: #475569;
      transition: all 0.4s ease;
    }

    .quick-action-btn:hover p {
      color: #0f172a;
    }

    /* Part 2: Management Hub & Premium Cards - Next Gen Professional UI */
    .premium-card {
      background: #ffffff;
      border: 1px solid rgba(229, 231, 235, 0.8);
      border-radius: 20px;
      padding: 24px;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      height: 100%;
      display: flex;
      flex-direction: column;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02), 0 1px 2px rgba(0, 0, 0, 0.03);
      position: relative;
      overflow: hidden;
    }

    .premium-card::after {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 100%;
      height: 100%;
      background: linear-gradient(225deg, rgba(99, 102, 241, 0.03) 0%, transparent 40%);
      pointer-events: none;
    }

    .premium-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
      border-color: rgba(99, 102, 241, 0.2);
    }

    .card-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 20px;
      flex-direction: row-reverse;
      position: relative;
      z-index: 1;
    }

    .header-title-group {
      text-align: right;
      display: flex;
      flex-direction: column;
      gap: 2px;
    }

    .header-title-group h5 {
      margin: 0;
      font-size: 1.1rem;
      font-weight: 700;
      color: #111827;
      letter-spacing: -0.01em;
    }

    .card-subtitle {
      display: block;
      font-size: 0.75rem;
      color: #6B7280;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.05em;
    }

    .icon-wrapper {
      width: 48px;
      height: 48px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      background-color: #f8fafc;
      color: #6366f1;
      flex-shrink: 0;
      transition: all 0.3s ease;
      box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.02);
      border: 1px solid rgba(229, 231, 235, 0.5);
    }

    .premium-card:hover .icon-wrapper {
      transform: scale(1.1) rotate(5deg);
      box-shadow: 0 4px 12px rgba(99, 102, 241, 0.1);
    }

    /* Refined Color Palette for Icons */
    .icon-wrapper.blue { background-color: #eff6ff; color: #2563eb; border-color: #dbeafe; }
    .icon-wrapper.green { background-color: #f0fdf4; color: #16a34a; border-color: #dcfce7; }
    .icon-wrapper.purple { background-color: #faf5ff; color: #7c3aed; border-color: #f3e8ff; }
    .icon-wrapper.orange { background-color: #fff7ed; color: #ea580c; border-color: #ffedd5; }
    .icon-wrapper.pink { background-color: #fdf2f8; color: #db2777; border-color: #fce7f3; }
    .icon-wrapper.indigo { background-color: #eef2ff; color: #4338ca; border-color: #e0e7ff; }
    .icon-wrapper.cyan { background-color: #ecfeff; color: #0891b2; border-color: #cffafe; }
    .icon-wrapper.slate { background-color: #f8fafc; color: #475569; border-color: #f1f5f9; }

    .card-content {
      display: flex;
      flex-direction: column;
      gap: 4px;
      padding-top: 16px;
      border-top: 1px solid rgba(243, 244, 246, 0.8);
      position: relative;
      z-index: 1;
    }

    .card-link {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      flex-direction: row-reverse;
      padding: 10px 12px;
      border-radius: 12px;
      color: #4b5563 !important;
      text-decoration: none !important;
      font-weight: 500;
      font-size: 0.92rem;
      transition: all 0.2s ease;
      text-align: right;
      border: 1px solid transparent;
    }

    .card-link:hover {
      background-color: #f9fafb;
      color: #111827 !important;
      transform: translateX(-6px);
      border-color: rgba(229, 231, 235, 0.5);
    }

    .card-link i {
      width: 28px;
      height: 28px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-left: 12px;
      font-size: 1.05rem;
      color: #9ca3af !important;
      transition: all 0.2s ease;
      background: #f3f4f6;
      border-radius: 8px;
    }

    .card-link:hover i {
      color: #6366f1 !important;
      background: #eef2ff;
      transform: scale(1.1);
    }

    .card-link span {
      flex: 1;
      text-align: right;
    }

    /* Summary Section Styling - Matching the User's Image */
    .summary-card {
      background: transparent;
      padding: 0;
      border: none;
      box-shadow: none;
    }

    .summary-header {
      display: flex;
      align-items: center;
      justify-content: flex-end; /* RTL style */
      gap: 12px;
      margin-bottom: 24px;
    }

    .summary-header h3 {
      font-size: 1.25rem;
      font-weight: 800;
      color: #1e293b;
      margin: 0;
    }

    .summary-header::after {
      content: '';
      width: 4px;
      height: 24px;
      background: #6366f1;
      border-radius: 4px;
    }

    .summary-list {
      display: flex;
      flex-direction: column;
      gap: 16px;
    }

    .summary-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      direction: rtl;
    }

    .summary-item-content {
      display: flex;
      align-items: center;
      gap: 12px;
      flex: 1;
    }

    .summary-item-label {
      font-size: 0.95rem;
      color: #64748b;
      font-weight: 500;
    }

    .summary-item-value {
      display: flex;
      align-items: center;
      gap: 8px;
      font-weight: 700;
      font-size: 0.95rem;
    }

    .summary-item-value.healthy { color: #10b981; }
    .summary-item-value.warning { color: #f59e0b; }

    .summary-user-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 4px 0;
    }

    .user-info-group {
      display: flex;
      align-items: center;
      gap: 10px;
      direction: rtl;
    }

    .user-info-group span {
      font-size: 0.9rem;
      color: #94a3b8;
      font-weight: 500;
    }

    .user-info-group i {
      color: #1e293b;
      font-size: 0.9rem;
    }

    .user-time {
      font-size: 0.85rem;
      color: #94a3b8;
    }

    .view-all-link {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 8px;
      margin-top: 20px;
      color: #1e293b;
      text-decoration: none;
      font-weight: 700;
      font-size: 0.95rem;
      transition: all 0.2s ease;
    }

    .view-all-link:hover {
      color: #6366f1;
      transform: translateX(-4px);
    }

    .view-all-link i {
      font-size: 0.8rem;
    }

    .stat-card {
      border-radius: var(--radius); padding: 24px; color: white;
      position: relative; overflow: hidden; transition: all 0.3s ease;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2); }
    .stat-card-blue { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
    .stat-card-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .stat-card-orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .stat-card-red { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }
    /* Summary Cards for Recent Activity & Status */
    .summary-card {
      background: #ffffff;
      border: 1px solid rgba(229, 231, 235, 0.8);
      border-radius: 20px;
      padding: 24px;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    .summary-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 12px 24px -5px rgba(0, 0, 0, 0.08);
      border-color: rgba(99, 102, 241, 0.2);
    }

    .summary-header {
      margin-bottom: 20px;
      padding-bottom: 16px;
      border-bottom: 1px solid rgba(243, 244, 246, 0.8);
    }

    .summary-header h3 {
      font-size: 1.15rem;
      font-weight: 800;
      color: #1e293b;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .summary-header h3::before {
      content: '';
      width: 4px;
      height: 20px;
      background: #6366f1;
      border-radius: 4px;
      display: inline-block;
    }

    .summary-list {
      display: flex;
      flex-direction: column;
      gap: 12px;
      margin-bottom: 20px;
      flex: 1;
    }

    .summary-item, .summary-user-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 12px 16px;
      border-radius: 12px;
      transition: all 0.2s ease;
      background: #f8fafc;
      border: 1px solid transparent;
    }

    .summary-item:hover, .summary-user-item:hover {
      background: #ffffff;
      border-color: rgba(229, 231, 235, 0.8);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
    }

    .summary-item-label {
      font-weight: 600;
      color: #475569;
      font-size: 0.9rem;
    }

    .summary-item-value {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 0.85rem;
      font-weight: 700;
      padding: 4px 10px;
      border-radius: 8px;
    }

    .summary-item-value.healthy {
      background: #f0fdf4;
      color: #16a34a;
    }

    .summary-item-value.warning {
      background: #fffbeb;
      color: #d97706;
    }

    .user-info-group {
      display: flex;
      align-items: center;
      gap: 10px;
      color: #334155;
      font-weight: 600;
      font-size: 0.9rem;
    }

    .user-info-group i {
      color: #94a3b8;
      font-size: 1.1rem;
    }

    .user-time {
      font-size: 0.8rem;
      color: #64748b;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .premium-card, .stat-card, .quick-action-btn { padding: 15px; }
      .grid-cols-6 { grid-cols-2; }
    }
  </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
  <div class="page-background">
    <div class="w-full p-6">
      <!-- Enhanced Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="stat-card stat-card-blue">
          <div class="flex items-center justify-between relative z-10">
            <div>
              <p class="text-white/90 text-sm font-medium mb-1">إجمالي المستخدمين</p>
              <p class="text-3xl font-bold mb-2"><?php echo e($stats['site']['total_users']); ?></p>
              <p class="text-white/80 text-sm flex items-center">
                <i class="fas fa-arrow-up ml-1"></i>
                +<?php echo e($stats['site']['new_users_today']); ?> اليوم
              </p>
            </div>
            <div class="bg-white/20 backdrop-blur-sm rounded-full p-4">
              <i class="fas fa-users text-2xl text-white"></i>
            </div>
          </div>
        </div>

        <div class="stat-card stat-card-green">
          <div class="flex items-center justify-between relative z-10">
            <div>
              <p class="text-white/90 text-sm font-medium mb-1">العقارات</p>
              <p class="text-3xl font-bold mb-2"><?php echo e($stats['site']['total_properties']); ?></p>
              <p class="text-white/80 text-sm flex items-center">
                <i class="fas fa-arrow-up ml-1"></i>
                +<?php echo e($stats['site']['new_properties_today']); ?> اليوم
              </p>
            </div>
            <div class="bg-white/20 backdrop-blur-sm rounded-full p-4">
              <i class="fas fa-home text-2xl text-white"></i>
            </div>
          </div>
        </div>

        <div class="stat-card stat-card-orange">
          <div class="flex items-center justify-between relative z-10">
            <div>
              <p class="text-white/90 text-sm font-medium mb-1">المستثمرون</p>
              <p class="text-3xl font-bold mb-2"><?php echo e($stats['site']['total_investors']); ?></p>
              <p class="text-white/80 text-sm flex items-center">
                <i class="fas fa-arrow-up ml-1"></i>
                +<?php echo e($stats['site']['new_investors_today']); ?> اليوم
              </p>
            </div>
            <div class="bg-white/20 backdrop-blur-sm rounded-full p-4">
              <i class="fas fa-chart-line text-2xl text-white"></i>
            </div>
          </div>
        </div>

        <div class="stat-card stat-card-red">
          <div class="flex items-center justify-between relative z-10">
            <div>
              <p class="text-white/90 text-sm font-medium mb-1">الإيرادات</p>
              <p class="text-3xl font-bold mb-2">$<?php echo e(number_format($stats['site']['total_revenue'], 0)); ?></p>
              <p class="text-white/80 text-sm flex items-center">
                <i class="fas fa-arrow-up ml-1"></i>
                +$<?php echo e(number_format($stats['site']['revenue_today'], 0)); ?> اليوم
              </p>
            </div>
            <div class="bg-white/20 backdrop-blur-sm rounded-full p-4">
              <i class="fas fa-dollar-sign text-2xl text-white"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Enhanced Charts Row -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- User Growth Chart -->
        <div class="modern-card p-6">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h3 class="text-lg font-bold text-gray-800">نمو المستخدمين</h3>
              <p class="text-sm text-gray-600">User Growth</p>
            </div>
            <div class="icon-container blue">
              <i class="fas fa-chart-line"></i>
            </div>
          </div>
          <div class="relative" style="height: 250px;">
            <canvas id="userGrowthChart"></canvas>
          </div>
        </div>

        <!-- Revenue Chart -->
        <div class="modern-card p-6">
          <div class="flex items-center justify-between mb-4">
            <div>
              <h3 class="text-lg font-bold text-gray-800">اتجاه الإيرادات</h3>
              <p class="text-sm text-gray-600">Revenue Trend</p>
            </div>
            <div class="icon-container green">
              <i class="fas fa-chart-bar"></i>
            </div>
          </div>
          <div class="relative" style="height: 250px;">
            <canvas id="revenueChart"></canvas>
          </div>
        </div>
      </div>

      <!-- Enhanced Recent Activity & Quick Actions -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
        <!-- System Status -->
        <div class="summary-card">
          <div class="summary-header">
            <h3>System Status</h3>
          </div>
          <div class="summary-list">
            <div class="summary-item">
              <div class="summary-item-value healthy">
                Healthy <i class="fas fa-check-circle"></i>
              </div>
              <span class="summary-item-label">Database</span>
            </div>
            <div class="summary-item">
              <div class="summary-item-value healthy">
                Used 65% <i class="fas fa-database"></i>
              </div>
              <span class="summary-item-label">Storage</span>
            </div>
            <div class="summary-item">
              <div class="summary-item-value healthy">
                Online <i class="fas fa-wifi"></i>
              </div>
              <span class="summary-item-label">API</span>
            </div>
            <div class="summary-item">
              <div class="summary-item-value warning">
                Jobs 12 <i class="fas fa-clock"></i>
              </div>
              <span class="summary-item-label">Queue</span>
            </div>
          </div>
        </div>

        <!-- Recent Properties -->
        <div class="summary-card">
          <div class="summary-header">
            <h3>Recent Properties</h3>
          </div>
          <div class="summary-list">
            <?php $__empty_1 = true; $__currentLoopData = $stats['recent_properties']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $property): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <div class="summary-item">
                <span class="user-time"><?php echo e($property->created_at->diffForHumans()); ?></span>
                <div class="user-info-group">
                  <span><?php echo e($property->title); ?></span>
                  <i class="fas fa-home"></i>
                </div>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <p class="text-gray-400 text-sm text-right">No recent properties</p>
            <?php endif; ?>
          </div>
          <a href="<?php echo e(route('admin.properties.index')); ?>" class="view-all-link">
            View All Properties <i class="fas fa-arrow-left"></i>
          </a>
        </div>

        <!-- Recent Users -->
        <div class="summary-card">
          <div class="summary-header">
            <h3>Recent Users</h3>
          </div>
          <div class="summary-list">
            <?php $__empty_1 = true; $__currentLoopData = $stats['recent_users']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <div class="summary-user-item">
                <span class="user-time"><?php echo e($user->created_at->diffForHumans()); ?></span>
                <div class="user-info-group">
                  <span><?php echo e($user->email); ?></span>
                  <i class="fas fa-user-circle"></i>
                </div>
              </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <p class="text-gray-400 text-sm text-right">No recent users</p>
            <?php endif; ?>
          </div>
          <a href="<?php echo e(route('admin.users.index')); ?>" class="view-all-link">
            View All Users <i class="fas fa-arrow-left"></i>
          </a>
        </div>
      </div>

      <!-- Enhanced Quick Actions -->
      <div class="qa-container">
        <div class="qa-header-title">
          <i class="fas fa-bolt"></i> Priority Quick Actions
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-5">
          <a href="<?php echo e(route('admin.users.create')); ?>" class="quick-action-btn">
            <div class="qa-icon-wrapper qa-user">
              <i class="fas fa-user-plus"></i>
            </div>
            <p>New User</p>
          </a>

          <a href="<?php echo e(route('admin.properties.create')); ?>" class="quick-action-btn">
            <div class="qa-icon-wrapper qa-property">
              <i class="fas fa-plus-circle"></i>
            </div>
            <p>New Property</p>
          </a>

          <a href="<?php echo e(route('admin.blog.posts.create')); ?>" class="quick-action-btn">
            <div class="qa-icon-wrapper qa-post">
              <i class="fas fa-edit"></i>
            </div>
            <p>New Post</p>
          </a>

          <a href="<?php echo e(route('investor.stats.public')); ?>" class="quick-action-btn">
            <div class="qa-icon-wrapper qa-investor">
              <i class="fas fa-chart-line"></i>
            </div>
            <p>Investor Stats</p>
          </a>

          <a href="<?php echo e(route('admin.settings.index')); ?>" class="quick-action-btn">
            <div class="qa-icon-wrapper qa-settings">
              <i class="fas fa-cog"></i>
            </div>
            <p>Settings</p>
          </a>

          <a href="<?php echo e(route('admin.system.dashboard')); ?>" class="quick-action-btn">
            <div class="qa-icon-wrapper qa-maintenance">
              <i class="fas fa-tools"></i>
            </div>
            <p>Maintenance</p>
          </a>
        </div>
      </div>

      <!-- Premium Management Hub -->
      <div class="bg-slate-50 rounded-xl shadow-sm border border-gray-200 p-8">
        <div class="flex items-center justify-between mb-8">
          <h3 class="text-2xl font-bold text-gray-900">Management Hub</h3>
          <div class="flex items-center gap-2 text-sm text-gray-500">
            <i class="fas fa-th-large"></i>
            <span>Premium Dashboard</span>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-4 gap-8">
          <!-- System Hub -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>مركز المسار الاستراتيجي</h5>
                <span class="card-subtitle">Strategic System Hub</span>
              </div>
              <div class="icon-wrapper blue">
                <i class="fas fa-shield-halved"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.hub')); ?>" class="card-link">
                <i class="fas fa-microchip"></i>
                <span>لوحة التحكم الرئيسية</span>
              </a>
              <a href="<?php echo e(route('admin.intelligence.index')); ?>" class="card-link">
                <i class="fas fa-brain"></i>
                <span>الذكاء العصبي</span>
              </a>
              <a href="<?php echo e(route('modules.dashboard')); ?>" class="card-link">
                <i class="fas fa-puzzle-piece"></i>
                <span>نظام الوحدات الرقمي</span>
              </a>
              <a href="<?php echo e(route('admin.settings.index')); ?>" class="card-link">
                <i class="fas fa-cogs"></i>
                <span>الاستراتيجية العالمية</span>
              </a>
            </div>
          </div>

          <!-- Core Operations -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>العمليات الأساسية</h5>
                <span class="card-subtitle">Core Operations</span>
              </div>
              <div class="icon-wrapper indigo">
                <i class="fas fa-cogs"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.users.index')); ?>" class="card-link">
                <i class="fas fa-users"></i>
                <span>كافة المستخدمين</span>
              </a>
              <a href="<?php echo e(route('admin.users.create')); ?>" class="card-link">
                <i class="fas fa-user-plus"></i>
                <span>إنشاء مستخدم</span>
              </a>
              <a href="<?php echo e(route('admin.agents.index')); ?>" class="card-link">
                <i class="fas fa-user-tie"></i>
                <span>الوكلاء</span>
              </a>
              <a href="<?php echo e(route('investor.index')); ?>" class="card-link">
                <i class="fas fa-hand-holding-usd"></i>
                <span>المستثمرين</span>
              </a>
              <a href="<?php echo e(route('admin.system.logs')); ?>" class="card-link">
                <i class="fas fa-history"></i>
                <span>سجلات النشاط</span>
              </a>
            </div>
          </div>

          <!-- Property Management -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>إدارة العقارات</h5>
                <span class="card-subtitle">Property Management</span>
              </div>
              <div class="icon-wrapper green">
                <i class="fas fa-city"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.properties.index')); ?>" class="card-link">
                <i class="fas fa-building"></i>
                <span>كافة العقارات</span>
              </a>
              <a href="<?php echo e(route('admin.properties.create')); ?>" class="card-link">
                <i class="fas fa-plus-circle"></i>
                <span>إضافة عقار</span>
              </a>
              <a href="<?php echo e(route('admin.projects.index')); ?>" class="card-link">
                <i class="fas fa-project-diagram"></i>
                <span>المشاريع الاستثمارية</span>
              </a>
              <a href="<?php echo e(route('admin.companies.index')); ?>" class="card-link">
                <i class="fas fa-briefcase"></i>
                <span>الشركات والشركاء</span>
              </a>
              <a href="<?php echo e(route('properties.search.index')); ?>" class="card-link">
                <i class="fas fa-search-location"></i>
                <span>البحث المتقدم</span>
              </a>
            </div>
          </div>

          <!-- Financial Hub -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>المركز المالي</h5>
                <span class="card-subtitle">Financial Hub</span>
              </div>
              <div class="icon-wrapper emerald">
                <i class="fas fa-chart-line"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.financial.expenses')); ?>" class="card-link">
                <i class="fas fa-money-bill-wave"></i>
                <span>المصاريف والتدفقات</span>
              </a>
              <a href="<?php echo e(route('payments.invoices.index')); ?>" class="card-link">
                <i class="fas fa-file-invoice"></i>
                <span>الفواتير والتحصيل</span>
              </a>
              <a href="<?php echo e(route('wallet.index')); ?>" class="card-link">
                <i class="fas fa-wallet"></i>
                <span>المحفظة الاستثمارية</span>
              </a>
              <a href="<?php echo e(route('payments.wallets.index')); ?>" class="card-link">
                <i class="fas fa-coins"></i>
                <span>محافظ إدارة النظام</span>
              </a>
              <a href="<?php echo e(route('payments.escrow.index')); ?>" class="card-link">
                <i class="fas fa-hand-holding-usd"></i>
                <span>الضمان المالي (Escrow)</span>
              </a>
            </div>
          </div>

          <!-- Revenue Streams -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>إدارة الإيرادات</h5>
                <span class="card-subtitle">Revenue Streams</span>
              </div>
              <div class="icon-wrapper purple">
                <i class="fas fa-dollar-sign"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('subscriptions.plans.index')); ?>" class="card-link">
                <i class="fas fa-tags"></i>
                <span>خطط العضوية</span>
              </a>
              <a href="<?php echo e(route('subscriptions.plans.stats')); ?>" class="card-link">
                <i class="fas fa-chart-area"></i>
                <span>إحصائيات الاشتراك</span>
              </a>
              <a href="<?php echo e(route('subscriptions.plans.admin-compare')); ?>" class="card-link">
                <i class="fas fa-balance-scale"></i>
                <span>تحليل الخطط</span>
              </a>
              <a href="<?php echo e(route('taxes.index')); ?>" class="card-link">
                <i class="fas fa-calculator"></i>
                <span>تنفيذ الضرائب</span>
              </a>
            </div>
          </div>

          <!-- CRM & Sales -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>إدارة العملاء والمبيعات</h5>
                <span class="card-subtitle">CRM & Sales</span>
              </div>
              <div class="icon-wrapper orange">
                <i class="fas fa-funnel-dollar"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('leads.dashboard')); ?>" class="card-link">
                <i class="fas fa-tachometer-alt"></i>
                <span>لوحة التحكم بالمبيعات</span>
              </a>
              <a href="<?php echo e(route('leads.pipeline')); ?>" class="card-link">
                <i class="fas fa-filter"></i>
                <span>خط الأنابيب (Pipeline)</span>
              </a>
              <a href="<?php echo e(route('leads.index')); ?>" class="card-link">
                <i class="fas fa-list"></i>
                <span>قاعدة بيانات العملاء</span>
              </a>
              <a href="<?php echo e(route('lead-scoring.index')); ?>" class="card-link">
                <i class="fas fa-star-half-alt"></i>
                <span>تقييم جودة العملاء</span>
              </a>
              <a href="<?php echo e(route('lead-analytics.dashboard')); ?>" class="card-link">
                <i class="fas fa-chart-pie"></i>
                <span>التحليلات والمؤشرات</span>
              </a>
            </div>
          </div>

          <!-- Marketing Suite -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>مركز التسويق الرقمي</h5>
                <span class="card-subtitle">Marketing Suite</span>
              </div>
              <div class="icon-wrapper pink">
                <i class="fas fa-bullhorn"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-ad"></i>
                <span>حملات إعلانية ذكية</span>
              </a>
              <a href="<?php echo e(route('admin.seo.index')); ?>" class="card-link">
                <i class="fas fa-search"></i>
                <span>إدارة محركات البحث SEO</span>
              </a>
              <a href="<?php echo e(route('admin.seo.keywords')); ?>" class="card-link">
                <i class="fas fa-key"></i>
                <span>الكلمات المفتاحية</span>
              </a>
              <a href="<?php echo e(route('admin.seo.analyze')); ?>" class="card-link">
                <i class="fas fa-microscope"></i>
                <span>تحليل الأداء الرقمي</span>
              </a>
            </div>
          </div>

          <!-- Content Hub -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>إدارة المحتوى CMS</h5>
                <span class="card-subtitle">Content Hub</span>
              </div>
              <div class="icon-wrapper cyan">
                <i class="fas fa-pen-nib"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.content.dashboard')); ?>" class="card-link">
                <i class="fas fa-tachometer-alt"></i>
                <span>لوحة تحكم CMS</span>
              </a>
              <a href="<?php echo e(route('admin.blog.posts.index')); ?>" class="card-link">
                <i class="fas fa-newspaper"></i>
                <span>المشاريع الإخبارية والمدونة</span>
              </a>
              <a href="<?php echo e(route('admin.pages.index')); ?>" class="card-link">
                <i class="fas fa-file-code"></i>
                <span>إدارة الصفحات الثابتة</span>
              </a>
              <a href="<?php echo e(route('admin.media.index')); ?>" class="card-link">
                <i class="fas fa-images"></i>
                <span>مكتبة الوسائط الرقمية</span>
              </a>
            </div>
          </div>

          <!-- Analytics Center -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>مركز تحليلات البيانات</h5>
                <span class="card-subtitle">Analytics Center</span>
              </div>
              <div class="icon-wrapper purple">
                <i class="fas fa-chart-line"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-chart-bar"></i>
                <span>نظرة عامة على النظام</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-clock"></i>
                <span>توقعات النمو الذكية</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-file-alt"></i>
                <span>تقارير الأداء الفنية</span>
              </a>
            </div>
          </div>

          <!-- System Tools -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>أدوات إدارة النظام</h5>
                <span class="card-subtitle">System Tools</span>
              </div>
              <div class="icon-wrapper slate">
                <i class="fas fa-tools"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.system.dashboard')); ?>" class="card-link">
                <i class="fas fa-server"></i>
                <span>مركز القيادة الفني</span>
              </a>
              <a href="<?php echo e(route('admin.system.backup')); ?>" class="card-link">
                <i class="fas fa-database"></i>
                <span>الأرشفة الرقمية</span>
              </a>
              <a href="<?php echo e(route('admin.system.logs')); ?>" class="card-link">
                <i class="fas fa-file-alt"></i>
                <span>سجلات الربط السحابي</span>
              </a>
            </div>
          </div>

          <!-- Platform Settings -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>إعدادات منصة إيوان</h5>
                <span class="card-subtitle">Platform Settings</span>
              </div>
              <div class="icon-wrapper slate">
                <i class="fas fa-sliders-h"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.settings.index')); ?>" class="card-link">
                <i class="fas fa-cogs"></i>
                <span>الإعدادات والخيارات</span>
              </a>
              <a href="<?php echo e(route('admin.settings.email')); ?>" class="card-link">
                <i class="fas fa-envelope"></i>
                <span>تكوين خوادم البريد</span>
              </a>
              <a href="<?php echo e(route('admin.settings.payment')); ?>" class="card-link">
                <i class="fas fa-credit-card"></i>
                <span>بوابات الدفع الإلكتروني</span>
              </a>
              <a href="<?php echo e(route('admin.settings.security')); ?>" class="card-link">
                <i class="fas fa-lock"></i>
                <span>معايير الأمان والخصوصية</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-user-cog"></i>
                <span>تفضيلات الحساب الشخصي</span>
              </a>
            </div>
          </div>

          <!-- Communication Hub -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>التواصل والبريد</h5>
                <span class="card-subtitle">Communication Hub</span>
              </div>
              <div class="icon-wrapper cyan">
                <i class="fas fa-comments"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-inbox"></i>
                <span>البريد الوارد</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-user-friends"></i>
                <span>الدردشة المباشرة</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-calendar-alt"></i>
                <span>المواعيد والطلبات</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-bell"></i>
                <span>الإشعارات المركزية</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-video"></i>
                <span>مكالمات الفيديو</span>
              </a>
            </div>
          </div>

          <!-- IoT & Smart City -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>إنترنت الأشياء الذكي</h5>
                <span class="card-subtitle">IoT & Smart City</span>
              </div>
              <div class="icon-wrapper green">
                <i class="fas fa-wifi"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-microchip"></i>
                <span>لوحة التحكم الذكية</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-server"></i>
                <span>الأجهزة المتصلة</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-bolt"></i>
                <span>تحليلات موارد الطاقة</span>
              </a>
            </div>
          </div>

          <!-- Developer Solutions -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>المطورين والمشاريع</h5>
                <span class="card-subtitle">Developer Solutions</span>
              </div>
              <div class="icon-wrapper blue">
                <i class="fas fa-code"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-terminal"></i>
                <span>لوحة المطور العقاري</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-tasks"></i>
                <span>إدارة المشاريع الإنشائية</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-cubes"></i>
                <span>نماذج BIM والهندسة</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-file-contract"></i>
                <span>تراخيص البناء الرقمية</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-users-cog"></i>
                <span>إدارة شؤون المقاولين</span>
              </a>
            </div>
          </div>

          <!-- Blockchain Pro -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>نظام البلوكشين الذكي</h5>
                <span class="card-subtitle">Blockchain Hub</span>
              </div>
              <div class="icon-wrapper orange">
                <i class="fas fa-link"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-wallet"></i>
                <span>إدارة المحافظ المشفرة</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-exchange-alt"></i>
                <span>سجل المعاملات الرقمية</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-file-signature"></i>
                <span>العقود الذكية المؤمنة</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-images"></i>
                <span>سوق العقارات NFTs</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-users"></i>
                <span>بروتوكولات الشراكة DAO</span>
              </a>
            </div>
          </div>

          <!-- AI Core & IQ -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>مركز الذكاء الاصطناعي</h5>
                <span class="card-subtitle">AI Core & IQ</span>
              </div>
              <div class="icon-wrapper green">
                <i class="fas fa-brain"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-robot"></i>
                <span>لوحة التحكم الذكية</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-search"></i>
                <span>البحث باللغة الطبيعية</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-magic"></i>
                <span>المستشار العقاري AI</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-chart-line"></i>
                <span>توقع أسعار السوق</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-image"></i>
                <span>تحليل صور العقارات</span>
              </a>
            </div>
          </div>

          <!-- Project Management -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>إدارة المشاريع</h5>
                <span class="card-subtitle">Project Management</span>
              </div>
              <div class="icon-wrapper blue">
                <i class="fas fa-project-diagram"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('projects.dashboard')); ?>" class="card-link">
                <i class="fas fa-tachometer-alt"></i>
                <span>لوحة متابعة المشاريع</span>
              </a>
              <a href="<?php echo e(route('projects.index')); ?>" class="card-link">
                <i class="fas fa-list"></i>
                <span>كافة المشاريع القائمة</span>
              </a>
              <a href="<?php echo e(route('projects.gantt.dashboard')); ?>" class="card-link">
                <i class="fas fa-stream"></i>
                <span>مخططات Gantt الفنية</span>
              </a>
              <a href="<?php echo e(route('projects.milestones.dashboard')); ?>" class="card-link">
                <i class="fas fa-flag"></i>
                <span>المعالم والمراحل الكبرى</span>
              </a>
            </div>
          </div>

          <!-- IoT & Smart Living -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>إنترنت الأشياء والرفاهية</h5>
                <span class="card-subtitle">Smart Living</span>
              </div>
              <div class="icon-wrapper cyan">
                <i class="fas fa-wifi"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('iot.dashboard')); ?>" class="card-link">
                <i class="fas fa-home"></i>
                <span>لوحة التحكم الذكية</span>
              </a>
              <a href="<?php echo e(route('iot.devices.index')); ?>" class="card-link">
                <i class="fas fa-microchip"></i>
                <span>الأجهزة المتصلة</span>
              </a>
              <a href="<?php echo e(route('iot.automations.index')); ?>" class="card-link">
                <i class="fas fa-cogs"></i>
                <span>الأتمتة والذكاء</span>
              </a>
              <a href="<?php echo e(route('iot.energy.dashboard')); ?>" class="card-link">
                <i class="fas fa-bolt"></i>
                <span>مراقب الطاقة</span>
              </a>
            </div>
          </div>

          <!-- Developer Hub -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>مركز المطورين</h5>
                <span class="card-subtitle">Developer Hub</span>
              </div>
              <div class="icon-wrapper purple">
                <i class="fas fa-code-branch"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('developer.stats')); ?>" class="card-link">
                <i class="fas fa-tachometer-alt"></i>
                <span>لوحة التحكم</span>
              </a>
              <a href="<?php echo e(route('developer.projects.index')); ?>" class="card-link">
                <i class="fas fa-project-diagram"></i>
                <span>المشاريع</span>
              </a>
              <a href="<?php echo e(route('developer.portfolios.index')); ?>" class="card-link">
                <i class="fas fa-folder-open"></i>
                <span>المحافظ الاستثمارية</span>
              </a>
              <a href="<?php echo e(route('developer.permits.index')); ?>" class="card-link">
                <i class="fas fa-file-signature"></i>
                <span>التصاريح</span>
              </a>
              <a href="<?php echo e(route('developer.profile.show')); ?>" class="card-link">
                <i class="fas fa-id-card"></i>
                <span>ملف المطور</span>
              </a>
            </div>
          </div>

          <!-- Inventory System -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>إدارة المخزون</h5>
                <span class="card-subtitle">Inventory Core</span>
              </div>
              <div class="icon-wrapper orange">
                <i class="fas fa-boxes"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('inventory.index')); ?>" class="card-link">
                <i class="fas fa-warehouse"></i>
                <span>لوحة التحكم</span>
              </a>
              <a href="<?php echo e(route('inventory.items.index')); ?>" class="card-link">
                <i class="fas fa-box-open"></i>
                <span>الأصناف</span>
              </a>
              <a href="<?php echo e(route('inventory.categories.index')); ?>" class="card-link">
                <i class="fas fa-tags"></i>
                <span>التصنيفات</span>
              </a>
              <a href="<?php echo e(route('inventory.movements.index')); ?>" class="card-link">
                <i class="fas fa-dolly"></i>
                <span>حركات المخزون</span>
              </a>
              <a href="<?php echo e(route('inventory.suppliers.index')); ?>" class="card-link">
                <i class="fas fa-truck"></i>
                <span>الموردين</span>
              </a>
            </div>
          </div>

          <!-- Agent Success -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper green">
                <i class="fas fa-user-check"></i>
              </div>
              <h5>Agent Success</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('agents.dashboard')); ?>" class="card-link">
                <i class="fas fa-tachometer-alt"></i>
                <span>Agent Dash</span>
              </a>
              <a href="<?php echo e(route('agents.performance')); ?>" class="card-link">
                <i class="fas fa-chart-line"></i>
                <span>Performance</span>
              </a>
              <a href="<?php echo e(route('agents.ranking')); ?>" class="card-link">
                <i class="fas fa-trophy"></i>
                <span>Ranking</span>
              </a>
              <a href="<?php echo e(route('agents.goals')); ?>" class="card-link">
                <i class="fas fa-bullseye"></i>
                <span>Goals</span>
              </a>
              <a href="<?php echo e(route('agents.directory')); ?>" class="card-link">
                <i class="fas fa-address-book"></i>
                <span>Directory</span>
              </a>
            </div>
          </div>

          <!-- Gamification -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper yellow">
                <i class="fas fa-trophy"></i>
              </div>
              <h5>Gamification</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('gamification.dashboard')); ?>" class="card-link">
                <i class="fas fa-gamepad"></i>
                <span>Dashboard</span>
              </a>
              <a href="<?php echo e(route('gamification.achievements')); ?>" class="card-link">
                <i class="fas fa-medal"></i>
                <span>Achievements</span>
              </a>
              <a href="<?php echo e(route('gamification.badges')); ?>" class="card-link">
                <i class="fas fa-certificate"></i>
                <span>Badges</span>
              </a>
              <a href="<?php echo e(route('gamification.leaderboard')); ?>" class="card-link">
                <i class="fas fa-crown"></i>
                <span>Leaderboard</span>
              </a>
              <a href="<?php echo e(route('gamification.rewards')); ?>" class="card-link">
                <i class="fas fa-gift"></i>
                <span>Rewards</span>
              </a>
            </div>
          </div>

          <!-- Documents & Legal -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>الوثائق والقوانين</h5>
                <span class="card-subtitle">Documents & Legal</span>
              </div>
              <div class="icon-wrapper slate">
                <i class="fas fa-file-contract"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('documents.index')); ?>" class="card-link">
                <i class="fas fa-folder-open"></i>
                <span>كافة المستندات</span>
              </a>
              <a href="<?php echo e(route('documents.templates.index')); ?>" class="card-link">
                <i class="fas fa-file-alt"></i>
                <span>النماذج الجاهزة</span>
              </a>
              <a href="<?php echo e(route('documents.compliance.index')); ?>" class="card-link">
                <i class="fas fa-gavel"></i>
                <span>الامتثال القانوني</span>
              </a>
              <a href="<?php echo e(route('messages.contracts.index')); ?>" class="card-link">
                <i class="fas fa-signature"></i>
                <span>عقود الأنظمة</span>
              </a>
            </div>
          </div>

          <!-- Property Settings -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>إعدادات العقارات</h5>
                <span class="card-subtitle">Property Settings</span>
              </div>
              <div class="icon-wrapper indigo">
                <i class="fas fa-sliders-h"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('property-types.index')); ?>" class="card-link">
                <i class="fas fa-home"></i>
                <span>أنواع العقارات</span>
              </a>
              <a href="<?php echo e(route('property-types.create')); ?>" class="card-link">
                <i class="fas fa-plus-circle"></i>
                <span>إضافة نوع</span>
              </a>
              <a href="<?php echo e(route('properties.features.index', ['property' => 1])); ?>" class="card-link">
                <i class="fas fa-list-ul"></i>
                <span>المميزات</span>
              </a>
              <a href="<?php echo e(route('properties.prices.index', ['property' => 1])); ?>" class="card-link">
                <i class="fas fa-dollar-sign"></i>
                <span>تكوين الأسعار</span>
              </a>
            </div>
          </div>

          <!-- Enterprise B2B -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>خدمات الشركات B2B</h5>
                <span class="card-subtitle">Enterprise B2B</span>
              </div>
              <div class="icon-wrapper slate">
                <i class="fas fa-building"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('enterprise.dashboard')); ?>" class="card-link">
                <i class="fas fa-chart-pie"></i>
                <span>لوحة التحكم</span>
              </a>
              <a href="<?php echo e(route('enterprise.accounts')); ?>" class="card-link">
                <i class="fas fa-users-cog"></i>
                <span>الحسابات</span>
              </a>
              <a href="<?php echo e(route('enterprise.subscriptions')); ?>" class="card-link">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>الاشتراكات</span>
              </a>
              <a href="<?php echo e(route('enterprise.reports')); ?>" class="card-link">
                <i class="fas fa-file-alt"></i>
                <span>التقارير</span>
              </a>
            </div>
          </div>

          <!-- Ad Management -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>إدارة الإعلانات</h5>
                <span class="card-subtitle">Ad Management</span>
              </div>
              <div class="icon-wrapper pink">
                <i class="fas fa-ad"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.banner-ads.index')); ?>" class="card-link">
                <i class="fas fa-list"></i>
                <span>كافة الإعلانات</span>
              </a>
              <a href="<?php echo e(route('admin.banner-ads.create')); ?>" class="card-link">
                <i class="fas fa-plus-square"></i>
                <span>إنشاء بنر</span>
              </a>
              <a href="#" onclick="alert('Analytics')" class="card-link">
                <i class="fas fa-chart-line"></i>
                <span>تحليل الأداء</span>
              </a>
              <a href="#" onclick="alert('Tracking')" class="card-link">
                <i class="fas fa-mouse-pointer"></i>
                <span>تتبع النقرات</span>
              </a>
            </div>
          </div>

          <!-- Blockchain Pro -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>نظام البلوكشين المتقدم</h5>
                <span class="card-subtitle">Blockchain Pro</span>
              </div>
              <div class="icon-wrapper violet">
                <i class="fas fa-cube"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('blockchain.wallets.index')); ?>" class="card-link">
                <i class="fas fa-wallet"></i>
                <span>المحافظ الرقمية</span>
              </a>
              <a href="<?php echo e(route('blockchain.transactions.index')); ?>" class="card-link">
                <i class="fas fa-exchange-alt"></i>
                <span>المعاملات</span>
              </a>
              <a href="<?php echo e(route('blockchain.smartcontracts.index')); ?>" class="card-link">
                <i class="fas fa-file-contract"></i>
                <span>العقود الذكية</span>
              </a>
              <a href="<?php echo e(route('blockchain.nfts.index')); ?>" class="card-link">
                <i class="fas fa-image"></i>
                <span>الأصول (NFTs)</span>
              </a>
              <a href="<?php echo e(route('blockchain.dao.index')); ?>" class="card-link">
                <i class="fas fa-users"></i>
                <span>الحوكمة (DAO)</span>
              </a>
            </div>
          </div>

        </div>
      </div>

      <!-- Extended Modules -->
      <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 mt-8">
        <div class="flex items-center justify-between mb-8">
          <h3 class="text-2xl font-bold text-gray-900">المزيد من الأنظمة</h3>
          <div class="flex items-center gap-2 text-sm text-gray-500">
            <i class="fas fa-cubes"></i>
            <span>Extended Modules</span>
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <!-- Content CMS -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>إدارة المحتوى</h5>
                <span class="card-subtitle">Content CMS</span>
              </div>
              <div class="icon-wrapper orange">
                <i class="fas fa-pen-nib"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.content.dashboard')); ?>" class="card-link">
                <i class="fas fa-tachometer-alt"></i>
                <span>لوحة تحكم CMS</span>
              </a>
              <a href="<?php echo e(route('admin.blog.posts.index')); ?>" class="card-link">
                <i class="fas fa-newspaper"></i>
                <span>المقالات والمدونة</span>
              </a>
              <a href="<?php echo e(route('admin.pages.index')); ?>" class="card-link">
                <i class="fas fa-file-code"></i>
                <span>الصفحات الثابتة</span>
              </a>
              <a href="<?php echo e(route('admin.news.index')); ?>" class="card-link">
                <i class="fas fa-bullhorn"></i>
                <span>مركز الأخبار</span>
              </a>
              <a href="<?php echo e(route('admin.guides.index')); ?>" class="card-link">
                <i class="fas fa-map-signs"></i>
                <span>أدلة المستخدم</span>
              </a>
            </div>
          </div>

          <!-- Content Tools -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>أدوات المحتوى</h5>
                <span class="card-subtitle">Content Tools</span>
              </div>
              <div class="icon-wrapper orange">
                <i class="fas fa-tools"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.faqs.index')); ?>" class="card-link">
                <i class="fas fa-question-circle"></i>
                <span>الأسئلة الشائعة</span>
              </a>
              <a href="<?php echo e(route('admin.media.index')); ?>" class="card-link">
                <i class="fas fa-images"></i>
                <span>مكتبة الوسائط</span>
              </a>
              <a href="<?php echo e(route('admin.menus.index')); ?>" class="card-link">
                <i class="fas fa-bars"></i>
                <span>القوائم</span>
              </a>
              <a href="<?php echo e(route('admin.widgets.index')); ?>" class="card-link">
                <i class="fas fa-th-large"></i>
                <span>الودجات</span>
              </a>
              <a href="<?php echo e(route('testimonials.index')); ?>" class="card-link">
                <i class="fas fa-quote-right"></i>
                <span>التوصيات</span>
              </a>
            </div>
          </div>

          <!-- Maintenance Ops -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>عمليات الصيانة</h5>
                <span class="card-subtitle">Maintenance Ops</span>
              </div>
              <div class="icon-wrapper red">
                <i class="fas fa-wrench"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('maintenance.index')); ?>" class="card-link">
                <i class="fas fa-hammer"></i>
                <span>لوحة التحكم</span>
              </a>
              <a href="<?php echo e(route('maintenance.workorders.index')); ?>" class="card-link">
                <i class="fas fa-tasks"></i>
                <span>أوامر العمل</span>
              </a>
              <a href="<?php echo e(route('maintenance.schedule.index')); ?>" class="card-link">
                <i class="fas fa-calendar-alt"></i>
                <span>الجدول الزمني</span>
              </a>
              <a href="<?php echo e(route('maintenance.teams.index')); ?>" class="card-link">
                <i class="fas fa-users"></i>
                <span>فرق العمل</span>
              </a>
              <a href="<?php echo e(route('maintenance.reports')); ?>" class="card-link">
                <i class="fas fa-chart-bar"></i>
                <span>التقارير</span>
              </a>
            </div>
          </div>

          <!-- Warranty Mgmt -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>إدارة الضمان</h5>
                <span class="card-subtitle">Warranty Mgmt</span>
              </div>
              <div class="icon-wrapper red">
                <i class="fas fa-shield-alt"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('warranties.index')); ?>" class="card-link">
                <i class="fas fa-certificate"></i>
                <span>الضمانات</span>
              </a>
              <a href="<?php echo e(route('warranties.policies.index')); ?>" class="card-link">
                <i class="fas fa-file-contract"></i>
                <span>السياسات</span>
              </a>
              <a href="<?php echo e(route('warranties.claims.index')); ?>" class="card-link">
                <i class="fas fa-exclamation-triangle"></i>
                <span>المطالبات</span>
              </a>
              <a href="<?php echo e(route('warranties.providers.index')); ?>" class="card-link">
                <i class="fas fa-handshake"></i>
                <span>المزودين</span>
              </a>
              <a href="<?php echo e(route('warranties.reports')); ?>" class="card-link">
                <i class="fas fa-chart-pie"></i>
                <span>التقارير</span>
              </a>
            </div>
          </div>

          <!-- System Maint -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>صيانة النظام</h5>
                <span class="card-subtitle">System Maint</span>
              </div>
              <div class="icon-wrapper red">
                <i class="fas fa-cogs"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.maintenance')); ?>" class="card-link">
                <i class="fas fa-tools"></i>
                <span>صيانة النظام</span>
              </a>
              <a href="<?php echo e(route('admin.system.updates')); ?>" class="card-link">
                <i class="fas fa-sync"></i>
                <span>التحديثات</span>
              </a>
            </div>
          </div>

          <!-- Smart Living -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>الحياة الذكية</h5>
                <span class="card-subtitle">Smart Living</span>
              </div>
              <div class="icon-wrapper cyan">
                <i class="fas fa-wifi"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('iot.dashboard')); ?>" class="card-link">
                <i class="fas fa-home"></i>
                <span>لوحة التحكم</span>
              </a>
              <a href="<?php echo e(route('iot.devices.index')); ?>" class="card-link">
                <i class="fas fa-microchip"></i>
                <span>الأجهزة</span>
              </a>
              <a href="<?php echo e(route('iot.automations.index')); ?>" class="card-link">
                <i class="fas fa-cogs"></i>
                <span>الأتمتة</span>
              </a>
              <a href="<?php echo e(route('iot.energy.dashboard')); ?>" class="card-link">
                <i class="fas fa-bolt"></i>
                <span>الطاقة</span>
              </a>
            </div>
          </div>

          <!-- Developer Hub -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>مركز المطورين</h5>
                <span class="card-subtitle">Developer Hub</span>
              </div>
              <div class="icon-wrapper purple">
                <i class="fas fa-code-branch"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('developer.stats')); ?>" class="card-link">
                <i class="fas fa-tachometer-alt"></i>
                <span>لوحة التحكم</span>
              </a>
              <a href="<?php echo e(route('developer.projects.index')); ?>" class="card-link">
                <i class="fas fa-project-diagram"></i>
                <span>المشاريع</span>
              </a>
              <a href="<?php echo e(route('developer.metaverses.index')); ?>" class="card-link">
                <i class="fas fa-vr-cardboard"></i>
                <span>الميتافيرس</span>
              </a>
              <a href="<?php echo e(route('developer.portfolios.index')); ?>" class="card-link">
                <i class="fas fa-folder-open"></i>
                <span>المحافظ الاستثمارية</span>
              </a>
              <a href="<?php echo e(route('developer.milestones.index')); ?>" class="card-link">
                <i class="fas fa-flag-checkered"></i>
                <span>مراحل الإنجاز</span>
              </a>
            </div>
          </div>

          <!-- Dev Projects -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>مشاريع المطورين</h5>
                <span class="card-subtitle">Dev Projects</span>
              </div>
              <div class="icon-wrapper purple">
                <i class="fas fa-laptop-code"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('developer.permits.index')); ?>" class="card-link">
                <i class="fas fa-file-signature"></i>
                <span>التصاريح</span>
              </a>
              <a href="<?php echo e(route('developer.phases.index')); ?>" class="card-link">
                <i class="fas fa-layer-group"></i>
                <span>المراحل</span>
              </a>
              <a href="<?php echo e(route('developer.units.index', ['project' => 1])); ?>" class="card-link">
                <i class="fas fa-th"></i>
                <span>الوحدات</span>
              </a>
              <a href="<?php echo e(route('developer.profile.show')); ?>" class="card-link">
                <i class="fas fa-id-card"></i>
                <span>ملف المطور</span>
              </a>
            </div>
          </div>

          <!-- Inventory System -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>إدارة المخزون</h5>
                <span class="card-subtitle">Inventory System</span>
              </div>
              <div class="icon-wrapper orange">
                <i class="fas fa-boxes"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('inventory.index')); ?>" class="card-link">
                <i class="fas fa-warehouse"></i>
                <span>لوحة التحكم</span>
              </a>
              <a href="<?php echo e(route('inventory.items.index')); ?>" class="card-link">
                <i class="fas fa-box-open"></i>
                <span>الأصناف</span>
              </a>
              <a href="<?php echo e(route('inventory.categories.index')); ?>" class="card-link">
                <i class="fas fa-tags"></i>
                <span>التصنيفات</span>
              </a>
              <a href="<?php echo e(route('inventory.movements.index')); ?>" class="card-link">
                <i class="fas fa-dolly"></i>
                <span>حركات المخزون</span>
              </a>
              <a href="<?php echo e(route('inventory.suppliers.index')); ?>" class="card-link">
                <i class="fas fa-truck"></i>
                <span>الموردين</span>
              </a>
            </div>
          </div>

          <!-- Gamification Hub -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>مركز التحفيز</h5>
                <span class="card-subtitle">Gamification Hub</span>
              </div>
              <div class="icon-wrapper orange">
                <i class="fas fa-trophy"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('gamification.dashboard')); ?>" class="card-link">
                <i class="fas fa-gamepad"></i>
                <span>لوحة التحكم</span>
              </a>
              <a href="<?php echo e(route('gamification.achievements')); ?>" class="card-link">
                <i class="fas fa-medal"></i>
                <span>الإنجازات</span>
              </a>
              <a href="<?php echo e(route('gamification.badges')); ?>" class="card-link">
                <i class="fas fa-certificate"></i>
                <span>الأوسمة</span>
              </a>
              <a href="<?php echo e(route('gamification.leaderboard')); ?>" class="card-link">
                <i class="fas fa-list-ol"></i>
                <span>لوحة المتصدرين</span>
              </a>
            </div>
          </div>

          <!-- Financial Ops -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>العمليات المالية</h5>
                <span class="card-subtitle">Financial Ops</span>
              </div>
              <div class="icon-wrapper red">
                <i class="fas fa-wallet"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.financial.expenses')); ?>" class="card-link">
                <i class="fas fa-money-bill-wave"></i>
                <span>المصاريف</span>
              </a>
              <a href="<?php echo e(route('payments.invoices.index')); ?>" class="card-link">
                <i class="fas fa-file-invoice"></i>
                <span>الفواتير</span>
              </a>
              <a href="<?php echo e(route('wallet.index')); ?>" class="card-link">
                <i class="fas fa-wallet"></i>
                <span>محفظتي</span>
              </a>
              <a href="<?php echo e(route('payments.wallets.index')); ?>" class="card-link">
                <i class="fas fa-coins"></i>
                <span>محافظ النظام</span>
              </a>
            </div>
          </div>

          <!-- Tax Compliance -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>الامتثال الضريبي</h5>
                <span class="card-subtitle">Tax Compliance</span>
              </div>
              <div class="icon-wrapper blue">
                <i class="fas fa-file-invoice-dollar"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('taxes.dashboard')); ?>" class="card-link">
                <i class="fas fa-columns"></i>
                <span>لوحة الضرائب</span>
              </a>
              <a href="<?php echo e(route('taxes.property.index')); ?>" class="card-link">
                <i class="fas fa-home"></i>
                <span>ضرائب العقارات</span>
              </a>
              <a href="<?php echo e(route('taxes.filings.index')); ?>" class="card-link">
                <i class="fas fa-file-signature"></i>
                <span>الإقرارات</span>
              </a>
            </div>
          </div>

          <!-- Marketing Core -->
          <div class="premium-card">
            <div class="card-header">
              <div class="header-title-group">
                <h5>أساسيات التسويق</h5>
                <span class="card-subtitle">Marketing Core</span>
              </div>
              <div class="icon-wrapper pink">
                <i class="fas fa-bullhorn"></i>
              </div>
            </div>
            <div class="card-content">
              <a href="<?php echo e(route('marketing.index')); ?>" class="card-link">
                <i class="fas fa-chart-line"></i>
                <span>لوحة التحكم</span>
              </a>
              <a href="<?php echo e(route('marketing.campaigns')); ?>" class="card-link">
                <i class="fas fa-ad"></i>
                <span>الحملات</span>
              </a>
              <a href="<?php echo e(route('admin.seo.index')); ?>" class="card-link">
                <i class="fas fa-search"></i>
                <span>إدارة SEO</span>
              </a>
            </div>
          </div>

          <!-- SEO Core -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper pink">
                <i class="fas fa-search-dollar"></i>
              </div>
              <h5>SEO Core</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('marketing.campaigns')); ?>" class="card-link">
                <i class="fas fa-ad"></i>
                <span>Campaigns</span>
              </a>
              <a href="<?php echo e(route('admin.settings.seo')); ?>" class="card-link">
                <i class="fas fa-cogs"></i>
                <span>Global SEO</span>
              </a>
              <a href="<?php echo e(route('admin.seo.index')); ?>" class="card-link">
                <i class="fas fa-search"></i>
                <span>SEO Mgmt</span>
              </a>
              <a href="<?php echo e(route('admin.seo.keywords')); ?>" class="card-link">
                <i class="fas fa-key"></i>
                <span>Keywords</span>
              </a>
              <a href="<?php echo e(route('admin.seo.meta.tags')); ?>" class="card-link">
                <i class="fas fa-tags"></i>
                <span>Meta Tags</span>
              </a>
            </div>
          </div>

          <!-- SEO Tools -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper pink">
                <i class="fas fa-microscope"></i>
              </div>
              <h5>SEO Tools</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.seo.robots')); ?>" class="card-link">
                <i class="fas fa-robot"></i>
                <span>Robots.txt</span>
              </a>
              <a href="<?php echo e(route('admin.seo.analyze')); ?>" class="card-link">
                <i class="fas fa-microscope"></i>
                <span>Analysis</span>
              </a>
              <a href="<?php echo e(route('admin.seo.test')); ?>" class="card-link">
                <i class="fas fa-vial"></i>
                <span>SEO Test</span>
              </a>
              <a href="<?php echo e(route('admin.seo.sitemap')); ?>" class="card-link">
                <i class="fas fa-sitemap"></i>
                <span>Sitemap</span>
              </a>
            </div>
          </div>

          <!-- Banner Ads -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper blue">
                <i class="fas fa-ad"></i>
              </div>
              <h5>Banner Ads</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.banner-ads.index')); ?>" class="card-link">
                <i class="fas fa-list"></i>
                <span>All Ads</span>
              </a>
              <a href="<?php echo e(route('admin.banner-ads.create')); ?>" class="card-link">
                <i class="fas fa-plus-square"></i>
                <span>Create Ad</span>
              </a>
              <a href="#" onclick="alert('Analytics')" class="card-link">
                <i class="fas fa-chart-line"></i>
                <span>Analytics</span>
              </a>
              <a href="#" onclick="alert('Tracking')" class="card-link">
                <i class="fas fa-mouse-pointer"></i>
                <span>Tracking</span>
              </a>
              <a href="#" onclick="alert('Impressions')" class="card-link">
                <i class="fas fa-eye"></i>
                <span>Impressions</span>
              </a>
            </div>
          </div>

          <!-- Analytics Core -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper purple">
                <i class="fas fa-chart-network"></i>
              </div>
              <h5>Analytics Core</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('analytics.overview')); ?>" class="card-link">
                <i class="fas fa-tachometer-alt"></i>
                <span>Overview</span>
              </a>
              <a href="<?php echo e(route('analytics.real-time')); ?>" class="card-link">
                <i class="fas fa-clock"></i>
                <span>Real-Time</span>
              </a>
              <a href="<?php echo e(route('analytics.behavior.index')); ?>" class="card-link">
                <i class="fas fa-user-tag"></i>
                <span>Behavior</span>
              </a>
              <a href="<?php echo e(route('analytics.heatmap.index')); ?>" class="card-link">
                <i class="fas fa-fire"></i>
                <span>Heatmaps</span>
              </a>
              <a href="<?php echo e(route('geospatial.analytics.index')); ?>" class="card-link">
                <i class="fas fa-globe-asia"></i>
                <span>Geo Analytics</span>
              </a>
            </div>
          </div>

          <!-- Market Data -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper purple">
                <i class="fas fa-chart-bar"></i>
              </div>
              <h5>Market Data</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('geospatial.demographics.index')); ?>" class="card-link">
                <i class="fas fa-users"></i>
                <span>Demographics</span>
              </a>
              <a href="<?php echo e(route('analytics.market.trends')); ?>" class="card-link">
                <i class="fas fa-trending-up"></i>
                <span>Trends</span>
              </a>
            </div>
          </div>

          <!-- Reports Core -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper indigo">
                <i class="fas fa-file-alt"></i>
              </div>
              <h5>Reports Core</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('reports.index')); ?>" class="card-link">
                <i class="fas fa-copy"></i>
                <span>Center</span>
              </a>
              <a href="<?php echo e(route('reports.sales.index')); ?>" class="card-link">
                <i class="fas fa-piggy-bank"></i>
                <span>Sales</span>
              </a>
              <a href="<?php echo e(route('admin.reports.agent.performance')); ?>" class="card-link">
                <i class="fas fa-user-tie"></i>
                <span>Agent Perf</span>
              </a>
              <a href="<?php echo e(route('admin.reports.client.analytics')); ?>" class="card-link">
                <i class="fas fa-users"></i>
                <span>Client Analytics</span>
              </a>
              <a href="<?php echo e(route('admin.reports.property.performance')); ?>" class="card-link">
                <i class="fas fa-home"></i>
                <span>Prop Perf</span>
              </a>
            </div>
          </div>

          <!-- Perf Reports -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper indigo">
                <i class="fas fa-rocket"></i>
              </div>
              <h5>Perf Reports</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.performance.system')); ?>" class="card-link">
                <i class="fas fa-rocket"></i>
                <span>Sys Perf</span>
              </a>
              <a href="<?php echo e(route('admin.reports.custom')); ?>" class="card-link">
                <i class="fas fa-filter"></i>
                <span>Custom Rpts</span>
              </a>
            </div>
          </div>

          <!-- Surveys -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper pink">
                <i class="fas fa-poll"></i>
              </div>
              <h5>Surveys</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('surveys.index')); ?>" class="card-link">
                <i class="fas fa-poll-h"></i>
                <span>All Surveys</span>
              </a>
              <a href="<?php echo e(route('surveys.create')); ?>" class="card-link">
                <i class="fas fa-plus-square"></i>
                <span>Create</span>
              </a>
              <a href="<?php echo e(route('reviews.index')); ?>" class="card-link">
                <i class="fas fa-star"></i>
                <span>Reviews</span>
              </a>
              <a href="<?php echo e(route('requests.index')); ?>" class="card-link">
                <i class="fas fa-envelope-open-text"></i>
                <span>Requests</span>
              </a>
            </div>
          </div>

          <!-- Communication -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper blue">
                <i class="fas fa-comments"></i>
              </div>
              <h5>Communication</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('messages.inbox')); ?>" class="card-link">
                <i class="fas fa-inbox"></i>
                <span>Inbox</span>
              </a>
              <a href="<?php echo e(route('messages.chat')); ?>" class="card-link">
                <i class="fas fa-comment-dots"></i>
                <span>Live Chat</span>
              </a>
              <a href="<?php echo e(route('messages.appointments')); ?>" class="card-link">
                <i class="fas fa-calendar-check"></i>
                <span>Appointments</span>
              </a>
              <a href="<?php echo e(route('messages.notifications')); ?>" class="card-link">
                <i class="fas fa-bell"></i>
                <span>Notifications</span>
              </a>
              <a href="#" onclick="alert('Video Calls')" class="card-link">
                <i class="fas fa-video"></i>
                <span>Video Calls</span>
              </a>
            </div>
          </div>

          <!-- Deals & Offers -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper green">
                <i class="fas fa-handshake"></i>
              </div>
              <h5>Deals & Offers</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('messages.offers.index')); ?>" class="card-link">
                <i class="fas fa-tag"></i>
                <span>Offers</span>
              </a>
              <a href="<?php echo e(route('messages.negotiations.index')); ?>" class="card-link">
                <i class="fas fa-hand-holding-usd"></i>
                <span>Negotiations</span>
              </a>
              <a href="<?php echo e(route('messages.contracts.index')); ?>" class="card-link">
                <i class="fas fa-file-signature"></i>
                <span>Contracts</span>
              </a>
              <a href="<?php echo e(route('messages.auctions.index')); ?>" class="card-link">
                <i class="fas fa-gavel"></i>
                <span>Auctions</span>
              </a>
            </div>
          </div>

          <!-- System Infra -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper gray">
                <i class="fas fa-server"></i>
              </div>
              <h5>System Infra</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.system.dashboard')); ?>" class="card-link">
                <i class="fas fa-tachometer-alt"></i>
                <span>System Dash</span>
              </a>
              <a href="<?php echo e(route('admin.system.monitoring')); ?>" class="card-link">
                <i class="fas fa-heartbeat"></i>
                <span>Monitoring</span>
              </a>
              <a href="<?php echo e(route('admin.system.database')); ?>" class="card-link">
                <i class="fas fa-database"></i>
                <span>Database</span>
              </a>
              <a href="<?php echo e(route('admin.backups')); ?>" class="card-link">
                <i class="fas fa-save"></i>
                <span>Backups</span>
              </a>
              <a href="<?php echo e(route('admin.system.logs')); ?>" class="card-link">
                <i class="fas fa-list-alt"></i>
                <span>System Logs</span>
              </a>
            </div>
          </div>

          <!-- System Ops -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper gray">
                <i class="fas fa-cogs"></i>
              </div>
              <h5>System Ops</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.system.queue')); ?>" class="card-link">
                <i class="fas fa-tasks"></i>
                <span>Queues</span>
              </a>
              <a href="<?php echo e(route('admin.system.storage')); ?>" class="card-link">
                <i class="fas fa-hdd"></i>
                <span>Storage</span>
              </a>
              <a href="<?php echo e(route('routes.index')); ?>" class="card-link">
                <i class="fas fa-route"></i>
                <span>Route Mgr</span>
              </a>
            </div>
          </div>

          <!-- Platform Set. -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper gray">
                <i class="fas fa-sliders-h"></i>
              </div>
              <h5>Platform Set.</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.settings.general')); ?>" class="card-link">
                <i class="fas fa-sliders-h"></i>
                <span>General</span>
              </a>
              <a href="<?php echo e(route('modules.dashboard')); ?>" class="card-link">
                <i class="fas fa-puzzle-piece"></i>
                <span>Modules</span>
              </a>
              <a href="<?php echo e(route('admin.settings.email')); ?>" class="card-link">
                <i class="fas fa-envelope"></i>
                <span>Email</span>
              </a>
              <a href="<?php echo e(route('admin.settings.payment')); ?>" class="card-link">
                <i class="fas fa-credit-card"></i>
                <span>Payment</span>
              </a>
              <a href="<?php echo e(route('admin.settings.social')); ?>" class="card-link">
                <i class="fas fa-share-alt"></i>
                <span>Social</span>
              </a>
            </div>
          </div>

          <!-- Acct Settings -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper gray">
                <i class="fas fa-user-cog"></i>
              </div>
              <h5>Acct Settings</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.settings.security')); ?>" class="card-link">
                <i class="fas fa-lock"></i>
                <span>Security</span>
              </a>
              <a href="<?php echo e(route('settings.index')); ?>" class="card-link">
                <i class="fas fa-user-cog"></i>
                <span>My Prefs</span>
              </a>
              <a href="<?php echo e(route('social.accounts')); ?>" class="card-link">
                <i class="fas fa-share-alt-square"></i>
                <span>Linked Accts</span>
              </a>
            </div>
          </div>

          <!-- Tools & Utils -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper gray">
                <i class="fas fa-tools"></i>
              </div>
              <h5>Tools & Utils</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('currency.index')); ?>" class="card-link">
                <i class="fas fa-coins"></i>
                <span>Currency</span>
              </a>
              <a href="<?php echo e(route('currency.converter')); ?>" class="card-link">
                <i class="fas fa-exchange-alt"></i>
                <span>Converter</span>
              </a>
              <a href="<?php echo e(route('language.index')); ?>" class="card-link">
                <i class="fas fa-language"></i>
                <span>Languages</span>
              </a>
              <a href="<?php echo e(route('admin.performance.system')); ?>" class="card-link">
                <i class="fas fa-tachometer-alt"></i>
                <span>Performance</span>
              </a>
              <a href="<?php echo e(route('admin.system.dashboard')); ?>" class="card-link">
                <i class="fas fa-tools"></i>
                <span>Maint.</span>
              </a>
            </div>
          </div>

          <!-- Advanced -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper purple">
                <i class="fas fa-rocket"></i>
              </div>
              <h5>Advanced</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('metaverse.index')); ?>" class="card-link">
                <i class="fas fa-vr-cardboard"></i>
                <span>Metaverse</span>
              </a>
              <a href="<?php echo e(route('defi.dashboard.index')); ?>" class="card-link">
                <i class="fas fa-chart-line"></i>
                <span>DeFi Plat.</span>
              </a>
              <a href="<?php echo e(route('bigdata.predictive-ai')); ?>" class="card-link">
                <i class="fas fa-database"></i>
                <span>Big Data</span>
              </a>
              <a href="<?php echo e(route('geospatial.analytics.index')); ?>" class="card-link">
                <i class="fas fa-map-marked-alt"></i>
                <span>Geospatial</span>
              </a>
              <a href="#" onclick="alert('API Notifications')" class="card-link">
                <i class="fas fa-bell"></i>
                <span>API Notif</span>
              </a>
            </div>
          </div>

          <!-- Documents -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper orange">
                <i class="fas fa-file-contract"></i>
              </div>
              <h5>Documents</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('documents.index')); ?>" class="card-link">
                <i class="fas fa-folder-open"></i>
                <span>All Docs</span>
              </a>
              <a href="<?php echo e(route('documents.templates.index')); ?>" class="card-link">
                <i class="fas fa-file-alt"></i>
                <span>Templates</span>
              </a>
              <a href="<?php echo e(route('documents.compliance.index')); ?>" class="card-link">
                <i class="fas fa-gavel"></i>
                <span>Legal Docs</span>
              </a>
              <a href="<?php echo e(route('messages.contracts.index')); ?>" class="card-link">
                <i class="fas fa-signature"></i>
                <span>Contracts</span>
              </a>
            </div>
          </div>

          <!-- Blockchain Core -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper purple">
                <i class="fas fa-cube"></i>
              </div>
              <h5>Blockchain Core</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('blockchain.wallets.index')); ?>" class="card-link">
                <i class="fas fa-link"></i>
                <span>Block. Hub</span>
              </a>
              <a href="<?php echo e(route('blockchain.wallets.index')); ?>" class="card-link">
                <i class="fas fa-wallet"></i>
                <span>Crypto Wallets</span>
              </a>
              <a href="<?php echo e(route('blockchain.transactions.index')); ?>" class="card-link">
                <i class="fas fa-exchange-alt"></i>
                <span>Transactions</span>
              </a>
              <a href="<?php echo e(route('blockchain.smartcontracts.index')); ?>" class="card-link">
                <i class="fas fa-file-contract"></i>
                <span>Smart Contr.</span>
              </a>
              <a href="<?php echo e(route('blockchain.nfts.index')); ?>" class="card-link">
                <i class="fas fa-image"></i>
                <span>NFTs</span>
              </a>
            </div>
          </div>

          <!-- DeFi & DAO -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper purple">
                <i class="fas fa-users-cog"></i>
              </div>
              <h5>DeFi & DAO</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('blockchain.dao.index')); ?>" class="card-link">
                <i class="fas fa-users"></i>
                <span>DAO</span>
              </a>
              <a href="<?php echo e(route('blockchain.defi.index')); ?>" class="card-link">
                <i class="fas fa-chart-line"></i>
                <span>DeFi</span>
              </a>
              <a href="<?php echo e(route('blockchain.liquidity-pools.index')); ?>" class="card-link">
                <i class="fas fa-water"></i>
                <span>Liquidity</span>
              </a>
            </div>
          </div>

          <!-- AI Core -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper blue">
                <i class="fas fa-brain"></i>
              </div>
              <h5>AI Core</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('ai.dashboard')); ?>" class="card-link">
                <i class="fas fa-robot"></i>
                <span>AI Dash</span>
              </a>
              <a href="<?php echo e(route('ai.chat')); ?>" class="card-link">
                <i class="fas fa-comments"></i>
                <span>AI Chat</span>
              </a>
              <a href="<?php echo e(route('ai.valuation.dashboard')); ?>" class="card-link">
                <i class="fas fa-home"></i>
                <span>Prop Valuation</span>
              </a>
              <a href="<?php echo e(route('ai.price-prediction')); ?>" class="card-link">
                <i class="fas fa-chart-line"></i>
                <span>Price Pred.</span>
              </a>
              <a href="<?php echo e(route('ai.descriptions')); ?>" class="card-link">
                <i class="fas fa-pen"></i>
                <span>AI Desc.</span>
              </a>
            </div>
          </div>

          <!-- AI Advanced -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper blue">
                <i class="fas fa-microchip"></i>
              </div>
              <h5>AI Advanced</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('ai.chatbot-ui')); ?>" class="card-link">
                <i class="fas fa-comment-dots"></i>
                <span>Chatbot</span>
              </a>
              <a href="<?php echo e(route('ai.fraud-detection')); ?>" class="card-link">
                <i class="fas fa-shield-alt"></i>
                <span>Fraud Detect</span>
              </a>
              <a href="<?php echo e(route('ai.investment-advisor')); ?>" class="card-link">
                <i class="fas fa-lightbulb"></i>
                <span>Invest Advisor</span>
              </a>
              <a href="<?php echo e(route('ai.market-insights')); ?>" class="card-link">
                <i class="fas fa-chart-bar"></i>
                <span>Market Anal.</span>
              </a>
              <a href="<?php echo e(route('ai.virtual-staging')); ?>" class="card-link">
                <i class="fas fa-cube"></i>
                <span>Virt. Staging</span>
              </a>
            </div>
          </div>

          <!-- Lead Mgmt -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper cyan">
                <i class="fas fa-user-plus"></i>
              </div>
              <h5>Lead Mgmt</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('leads.index')); ?>" class="card-link">
                <i class="fas fa-users"></i>
                <span>All Leads</span>
              </a>
              <a href="<?php echo e(route('leads.create')); ?>" class="card-link">
                <i class="fas fa-user-plus"></i>
                <span>Add Lead</span>
              </a>
              <a href="<?php echo e(route('leads.pipeline')); ?>" class="card-link">
                <i class="fas fa-filter"></i>
                <span>Pipeline</span>
              </a>
              <a href="<?php echo e(route('leads.dashboard')); ?>" class="card-link">
                <i class="fas fa-chart-line"></i>
                <span>Analytics</span>
              </a>
              <a href="<?php echo e(route('lead-import.index')); ?>" class="card-link">
                <i class="fas fa-file-import"></i>
                <span>Import</span>
              </a>
            </div>
          </div>

          <!-- Project Mgmt -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper blue">
                <i class="fas fa-project-diagram"></i>
              </div>
              <h5>Project Mgmt</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('projects.index')); ?>" class="card-link">
                <i class="fas fa-list"></i>
                <span>All Projects</span>
              </a>
              <a href="<?php echo e(route('projects.create')); ?>" class="card-link">
                <i class="fas fa-plus-circle"></i>
                <span>New Project</span>
              </a>
            </div>
          </div>

          <!-- Blog Mgmt -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper purple">
                <i class="fas fa-newspaper"></i>
              </div>
              <h5>Blog Mgmt</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('blog.index')); ?>" class="card-link">
                <i class="fas fa-globe"></i>
                <span>View Blog</span>
              </a>
              <a href="<?php echo e(route('admin.blog.posts.index')); ?>" class="card-link">
                <i class="fas fa-file-alt"></i>
                <span>All Posts</span>
              </a>
              <a href="<?php echo e(route('admin.blog.posts.create')); ?>" class="card-link">
                <i class="fas fa-pen-nib"></i>
                <span>Write Post</span>
              </a>
              <a href="<?php echo e(route('admin.blog.categories.index')); ?>" class="card-link">
                <i class="fas fa-tags"></i>
                <span>Categories</span>
              </a>
              <a href="<?php echo e(route('cms.blog.network')); ?>" class="card-link">
                <i class="fas fa-network-wired"></i>
                <span>Network</span>
              </a>
            </div>
          </div>

          <!-- Localization -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper cyan">
                <i class="fas fa-globe"></i>
              </div>
              <h5>Localization</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('language.index')); ?>" class="card-link">
                <i class="fas fa-language"></i>
                <span>Languages</span>
              </a>
              <a href="<?php echo e(route('language.index')); ?>" class="card-link">
                <i class="fas fa-flag"></i>
                <span>Countries</span>
              </a>
              <a href="<?php echo e(route('currency.index')); ?>" class="card-link">
                <i class="fas fa-money-bill"></i>
                <span>Currencies</span>
              </a>
              <a href="<?php echo e(route('language.index')); ?>" class="card-link">
                <i class="fas fa-align-right"></i>
                <span>RTL Support</span>
              </a>
              <a href="<?php echo e(route('language.index')); ?>" class="card-link">
                <i class="fas fa-calendar"></i>
                <span>Date/Time</span>
              </a>
            </div>
          </div>

          <!-- Authentication & SSO -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper blue">
                <i class="fas fa-key"></i>
              </div>
              <h5>Auth & SSO</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.settings.security')); ?>" class="card-link">
                <i class="fas fa-shield-alt"></i>
                <span>Security Center</span>
              </a>
              <a href="<?php echo e(route('admin.settings.social')); ?>" class="card-link">
                <i class="fas fa-share-alt"></i>
                <span>Social Login</span>
              </a>
              <a href="<?php echo e(route('admin.settings.index')); ?>" class="card-link">
                <i class="fas fa-fingerprint"></i>
                <span>MFA Setup</span>
              </a>
              <a href="<?php echo e(route('admin.system.logs')); ?>" class="card-link">
                <i class="fas fa-history"></i>
                <span>Auth Logs</span>
              </a>
              <a href="<?php echo e(route('admin.users.index')); ?>" class="card-link">
                <i class="fas fa-users-cog"></i>
                <span>Session Mgmt</span>
              </a>
            </div>
          </div>

          <!-- API Gateway -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper purple">
                <i class="fas fa-plug"></i>
              </div>
              <h5>API Gateway</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('routes.index')); ?>" class="card-link">
                <i class="fas fa-route"></i>
                <span>API Routes</span>
              </a>
              <a href="<?php echo e(route('admin.system.logs')); ?>" class="card-link">
                <i class="fas fa-key"></i>
                <span>API Keys</span>
              </a>
              <a href="<?php echo e(route('admin.performance.system')); ?>" class="card-link">
                <i class="fas fa-tachometer-alt"></i>
                <span>Rate Limits</span>
              </a>
              <a href="<?php echo e(route('admin.system.dashboard')); ?>" class="card-link">
                <i class="fas fa-file-code"></i>
                <span>API Docs</span>
              </a>
              <a href="<?php echo e(route('admin.backups')); ?>" class="card-link">
                <i class="fas fa-code-branch"></i>
                <span>Versioning</span>
              </a>
            </div>
          </div>

          <!-- Infrastructure -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper gray">
                <i class="fas fa-server"></i>
              </div>
              <h5>Infrastructure</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.system.monitoring')); ?>" class="card-link">
                <i class="fas fa-heartbeat"></i>
                <span>Monitoring</span>
              </a>
              <a href="<?php echo e(route('admin.system.dashboard')); ?>" class="card-link">
                <i class="fas fa-chart-area"></i>
                <span>Metrics</span>
              </a>
              <a href="<?php echo e(route('admin.system.logs')); ?>" class="card-link">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Alerts</span>
              </a>
              <a href="<?php echo e(route('admin.backups')); ?>" class="card-link">
                <i class="fas fa-hdd"></i>
                <span>Storage</span>
              </a>
              <a href="<?php echo e(route('admin.system.queue')); ?>" class="card-link">
                <i class="fas fa-network-wired"></i>
                <span>Uptime</span>
              </a>
            </div>
          </div>

          <!-- Email Marketing -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper pink">
                <i class="fas fa-envelope-open-text"></i>
              </div>
              <h5>Email Marketing</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.settings.email')); ?>" class="card-link">
                <i class="fas fa-envelope"></i>
                <span>Campaigns</span>
              </a>
              <a href="<?php echo e(route('admin.settings.email')); ?>" class="card-link">
                <i class="fas fa-users"></i>
                <span>Segments</span>
              </a>
              <a href="<?php echo e(route('admin.settings.email')); ?>" class="card-link">
                <i class="fas fa-magic"></i>
                <span>Automation</span>
              </a>
              <a href="<?php echo e(route('admin.settings.email')); ?>" class="card-link">
                <i class="fas fa-chart-line"></i>
                <span>Deliverability</span>
              </a>
              <a href="<?php echo e(route('admin.settings.email')); ?>" class="card-link">
                <i class="fas fa-paint-brush"></i>
                <span>Templates</span>
              </a>
            </div>
          </div>

          <!-- Webhooks -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper indigo">
                <i class="fas fa-webhook"></i>
              </div>
              <h5>Webhooks</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.system.logs')); ?>" class="card-link">
                <i class="fas fa-list"></i>
                <span>Endpoints</span>
              </a>
              <a href="<?php echo e(route('admin.system.logs')); ?>" class="card-link">
                <i class="fas fa-paper-plane"></i>
                <span>Event Log</span>
              </a>
              <a href="<?php echo e(route('admin.settings.security')); ?>" class="card-link">
                <i class="fas fa-lock"></i>
                <span>Security</span>
              </a>
              <a href="<?php echo e(route('admin.system.logs')); ?>" class="card-link">
                <i class="fas fa-redo"></i>
                <span>Retries</span>
              </a>
              <a href="<?php echo e(route('admin.performance.system')); ?>" class="card-link">
                <i class="fas fa-chart-bar"></i>
                <span>Analytics</span>
              </a>
            </div>
          </div>

          <!-- App Store -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper purple">
                <i class="fas fa-store"></i>
              </div>
              <h5>App Store</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('modules.dashboard')); ?>" class="card-link">
                <i class="fas fa-th-large"></i>
                <span>Browse Apps</span>
              </a>
              <a href="<?php echo e(route('modules.dashboard')); ?>" class="card-link">
                <i class="fas fa-download"></i>
                <span>Installed</span>
              </a>
              <a href="<?php echo e(route('developer.index')); ?>" class="card-link">
                <i class="fas fa-code"></i>
                <span>Developer</span>
              </a>
              <a href="<?php echo e(route('modules.dashboard')); ?>" class="card-link">
                <i class="fas fa-star"></i>
                <span>Reviews</span>
              </a>
              <a href="<?php echo e(route('modules.dashboard')); ?>" class="card-link">
                <i class="fas fa-dollar-sign"></i>
                <span>Billing</span>
              </a>
            </div>
          </div>

          <!-- Booking System -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper cyan">
                <i class="fas fa-calendar-check"></i>
              </div>
              <h5>Booking System</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('messages.appointments')); ?>" class="card-link">
                <i class="fas fa-calendar-alt"></i>
                <span>Calendar</span>
              </a>
              <a href="<?php echo e(route('messages.appointments')); ?>" class="card-link">
                <i class="fas fa-clock"></i>
                <span>Availability</span>
              </a>
              <a href="<?php echo e(route('messages.appointments')); ?>" class="card-link">
                <i class="fas fa-users"></i>
                <span>Bookings</span>
              </a>
              <a href="<?php echo e(route('maintenance.schedule.index')); ?>" class="card-link">
                <i class="fas fa-bell"></i>
                <span>Reminders</span>
              </a>
              <a href="<?php echo e(route('messages.appointments')); ?>" class="card-link">
                <i class="fas fa-ban"></i>
                <span>Blackouts</span>
              </a>
            </div>
          </div>

          <!-- E-Commerce -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper green">
                <i class="fas fa-shopping-cart"></i>
              </div>
              <h5>E-Commerce</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('inventory.items.index')); ?>" class="card-link">
                <i class="fas fa-box"></i>
                <span>Products</span>
              </a>
              <a href="<?php echo e(route('payments.invoices.index')); ?>" class="card-link">
                <i class="fas fa-shopping-bag"></i>
                <span>Orders</span>
              </a>
              <a href="<?php echo e(route('payments.wallets.index')); ?>" class="card-link">
                <i class="fas fa-shopping-cart"></i>
                <span>Cart</span>
              </a>
              <a href="<?php echo e(route('payments.escrow.index')); ?>" class="card-link">
                <i class="fas fa-truck"></i>
                <span>Shipping</span>
              </a>
              <a href="<?php echo e(route('payments.invoices.index')); ?>" class="card-link">
                <i class="fas fa-undo"></i>
                <span>Returns</span>
              </a>
            </div>
          </div>

          <!-- Data Warehouse -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper indigo">
                <i class="fas fa-database"></i>
              </div>
              <h5>Data Warehouse</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('analytics.overview')); ?>" class="card-link">
                <i class="fas fa-table"></i>
                <span>Datasets</span>
              </a>
              <a href="<?php echo e(route('analytics.overview')); ?>" class="card-link">
                <i class="fas fa-exchange-alt"></i>
                <span>ETL Pipelines</span>
              </a>
              <a href="<?php echo e(route('bigdata.predictive-ai')); ?>" class="card-link">
                <i class="fas fa-cube"></i>
                <span>Data Models</span>
              </a>
              <a href="<?php echo e(route('analytics.overview')); ?>" class="card-link">
                <i class="fas fa-search"></i>
                <span>SQL Query</span>
              </a>
              <a href="<?php echo e(route('admin.reports.custom')); ?>" class="card-link">
                <i class="fas fa-file-export"></i>
                <span>Export</span>
              </a>
            </div>
          </div>

          <!-- A/B Testing -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper orange">
                <i class="fas fa-flask"></i>
              </div>
              <h5>A/B Testing</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('analytics.overview')); ?>" class="card-link">
                <i class="fas fa-vial"></i>
                <span>Experiments</span>
              </a>
              <a href="<?php echo e(route('analytics.overview')); ?>" class="card-link">
                <i class="fas fa-layer-group"></i>
                <span>Variants</span>
              </a>
              <a href="<?php echo e(route('analytics.overview')); ?>" class="card-link">
                <i class="fas fa-chart-pie"></i>
                <span>Results</span>
              </a>
              <a href="<?php echo e(route('analytics.overview')); ?>" class="card-link">
                <i class="fas fa-calculator"></i>
                <span>Significance</span>
              </a>
              <a href="<?php echo e(route('analytics.overview')); ?>" class="card-link">
                <i class="fas fa-trophy"></i>
                <span>Winners</span>
              </a>
            </div>
          </div>

          <!-- CI/CD Pipeline -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper blue">
                <i class="fas fa-code-branch"></i>
              </div>
              <h5>CI/CD Pipeline</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.system.dashboard')); ?>" class="card-link">
                <i class="fas fa-rocket"></i>
                <span>Pipelines</span>
              </a>
              <a href="<?php echo e(route('admin.system.dashboard')); ?>" class="card-link">
                <i class="fas fa-check-circle"></i>
                <span>Builds</span>
              </a>
              <a href="<?php echo e(route('admin.system.dashboard')); ?>" class="card-link">
                <i class="fas fa-vial"></i>
                <span>Tests</span>
              </a>
              <a href="<?php echo e(route('admin.system.updates')); ?>" class="card-link">
                <i class="fas fa-cloud-upload-alt"></i>
                <span>Deployments</span>
              </a>
              <a href="<?php echo e(route('admin.system.updates')); ?>" class="card-link">
                <i class="fas fa-history"></i>
                <span>Rollback</span>
              </a>
            </div>
          </div>

          <!-- Backup & DR -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper green">
                <i class="fas fa-cloud-upload-alt"></i>
              </div>
              <h5>Backup & DR</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.backups')); ?>" class="card-link">
                <i class="fas fa-save"></i>
                <span>Backups</span>
              </a>
              <a href="<?php echo e(route('admin.backups')); ?>" class="card-link">
                <i class="fas fa-clock"></i>
                <span>Schedules</span>
              </a>
              <a href="<?php echo e(route('admin.backups')); ?>" class="card-link">
                <i class="fas fa-undo"></i>
                <span>Restore</span>
              </a>
              <a href="<?php echo e(route('admin.backups')); ?>" class="card-link">
                <i class="fas fa-globe"></i>
                <span>Geo-Redundancy</span>
              </a>
              <a href="<?php echo e(route('admin.backups')); ?>" class="card-link">
                <i class="fas fa-dolly"></i>
                <span>DR Drills</span>
              </a>
            </div>
          </div>

          <!-- Design System -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper pink">
                <i class="fas fa-paint-brush"></i>
              </div>
              <h5>Design System</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.settings.general')); ?>" class="card-link">
                <i class="fas fa-palette"></i>
                <span>Components</span>
              </a>
              <a href="<?php echo e(route('admin.settings.general')); ?>" class="card-link">
                <i class="fas fa-font"></i>
                <span>Typography</span>
              </a>
              <a href="<?php echo e(route('admin.settings.general')); ?>" class="card-link">
                <i class="fas fa-swatchbook"></i>
                <span>Tokens</span>
              </a>
              <a href="<?php echo e(route('admin.settings.general')); ?>" class="card-link">
                <i class="fas fa-universal-access"></i>
                <span>Accessibility</span>
              </a>
              <a href="<?php echo e(route('admin.settings.general')); ?>" class="card-link">
                <i class="fas fa-mobile-alt"></i>
                <span>Responsive</span>
              </a>
            </div>
          </div>

          <!-- Landing Pages -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper orange">
                <i class="fas fa-rocket"></i>
              </div>
              <h5>Landing Pages</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.pages.index')); ?>" class="card-link">
                <i class="fas fa-file-alt"></i>
                <span>All Pages</span>
              </a>
              <a href="<?php echo e(route('admin.pages.index')); ?>" class="card-link">
                <i class="fas fa-plus-square"></i>
                <span>Builder</span>
              </a>
              <a href="<?php echo e(route('admin.pages.index')); ?>" class="card-link">
                <i class="fas fa-th-large"></i>
                <span>Templates</span>
              </a>
              <a href="<?php echo e(route('analytics.overview')); ?>" class="card-link">
                <i class="fas fa-chart-line"></i>
                <span>Conversion</span>
              </a>
              <a href="<?php echo e(route('messages.contracts.index')); ?>" class="card-link">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Alerts</span>
              </a>
              <a href="<?php echo e(route('analytics.heatmap.index')); ?>" class="card-link">
                <i class="fas fa-mouse-pointer"></i>
                <span>Heatmaps</span>
              </a>
            </div>
          </div>

          <!-- Knowledge Base -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper blue">
                <i class="fas fa-book"></i>
              </div>
              <h5>Knowledge Base</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.guides.index')); ?>" class="card-link">
                <i class="fas fa-graduation-cap"></i>
                <span>Articles</span>
              </a>
              <a href="<?php echo e(route('admin.faqs.index')); ?>" class="card-link">
                <i class="fas fa-question-circle"></i>
                <span>FAQs</span>
              </a>
              <a href="<?php echo e(route('admin.guides.index')); ?>" class="card-link">
                <i class="fas fa-search"></i>
                <span>Search</span>
              </a>
              <a href="<?php echo e(route('admin.guides.index')); ?>" class="card-link">
                <i class="fas fa-thumbs-up"></i>
                <span>Feedback</span>
              </a>
              <a href="<?php echo e(route('admin.guides.index')); ?>" class="card-link">
                <i class="fas fa-tags"></i>
                <span>Categories</span>
              </a>
            </div>
          </div>

          <!-- Workflow Automation -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper purple">
                <i class="fas fa-project-diagram"></i>
              </div>
              <h5>Workflows</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.system.dashboard')); ?>" class="card-link">
                <i class="fas fa-sitemap"></i>
                <span>Automations</span>
              </a>
              <a href="<?php echo e(route('admin.system.dashboard')); ?>" class="card-link">
                <i class="fas fa-bolt"></i>
                <span>Triggers</span>
              </a>
              <a href="<?php echo e(route('admin.system.dashboard')); ?>" class="card-link">
                <i class="fas fa-tasks"></i>
                <span>Tasks</span>
              </a>
              <a href="<?php echo e(route('admin.system.dashboard')); ?>" class="card-link">
                <i class="fas fa-code-branch"></i>
                <span>Conditions</span>
              </a>
              <a href="<?php echo e(route('admin.system.dashboard')); ?>" class="card-link">
                <i class="fas fa-play-circle"></i>
                <span>Actions</span>
              </a>
            </div>
          </div>

          <!-- Audit & Compliance -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper gray">
                <i class="fas fa-clipboard-check"></i>
              </div>
              <h5>Audit & Compliance</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.system.logs')); ?>" class="card-link">
                <i class="fas fa-history"></i>
                <span>Audit Trail</span>
              </a>
              <a href="<?php echo e(route('admin.system.logs')); ?>" class="card-link">
                <i class="fas fa-shield-alt"></i>
                <span>Compliance</span>
              </a>
              <a href="<?php echo e(route('admin.system.logs')); ?>" class="card-link">
                <i class="fas fa-user-shield"></i>
                <span>GDPR</span>
              </a>
              <a href="<?php echo e(route('admin.system.logs')); ?>" class="card-link">
                <i class="fas fa-file-contract"></i>
                <span>Policies</span>
              </a>
              <a href="<?php echo e(route('admin.system.logs')); ?>" class="card-link">
                <i class="fas fa-download"></i>
                <span>Reports</span>
              </a>
            </div>
          </div>

          <!-- Feature Flags -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper yellow">
                <i class="fas fa-toggle-on"></i>
              </div>
              <h5>Feature Flags</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.settings.general')); ?>" class="card-link">
                <i class="fas fa-list"></i>
                <span>All Features</span>
              </a>
              <a href="<?php echo e(route('admin.settings.general')); ?>" class="card-link">
                <i class="fas fa-users"></i>
                <span>Targeting</span>
              </a>
              <a href="<?php echo e(route('admin.settings.general')); ?>" class="card-link">
                <i class="fas fa-percentage"></i>
                <span>Rollout</span>
              </a>
              <a href="<?php echo e(route('admin.settings.general')); ?>" class="card-link">
                <i class="fas fa-vial"></i>
                <span>Experiments</span>
              </a>
              <a href="<?php echo e(route('admin.system.logs')); ?>" class="card-link">
                <i class="fas fa-chart-line"></i>
                <span>Analytics</span>
              </a>
            </div>
          </div>

          <!-- Import/Export -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper green">
                <i class="fas fa-file-import"></i>
              </div>
              <h5>Import/Export</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('lead-import.index')); ?>" class="card-link">
                <i class="fas fa-upload"></i>
                <span>Import Data</span>
              </a>
              <a href="<?php echo e(route('admin.reports.custom')); ?>" class="card-link">
                <i class="fas fa-download"></i>
                <span>Export Data</span>
              </a>
              <a href="<?php echo e(route('lead-import.index')); ?>" class="card-link">
                <i class="fas fa-file-csv"></i>
                <span>CSV/Excel</span>
              </a>
              <a href="<?php echo e(route('lead-import.index')); ?>" class="card-link">
                <i class="fas fa-sync"></i>
                <span>Migration</span>
              </a>
              <a href="<?php echo e(route('lead-import.index')); ?>" class="card-link">
                <i class="fas fa-history"></i>
                <span>History</span>
              </a>
            </div>
          </div>

          <!-- Voice & SMS -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper red">
                <i class="fas fa-phone-alt"></i>
              </div>
              <h5>Voice & SMS</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('messages.chat')); ?>" class="card-link">
                <i class="fas fa-phone"></i>
                <span>Voice Calls</span>
              </a>
              <a href="<?php echo e(route('messages.chat')); ?>" class="card-link">
                <i class="fas fa-sms"></i>
                <span>SMS Gateway</span>
              </a>
              <a href="<?php echo e(route('admin.settings.email')); ?>" class="card-link">
                <i class="fas fa-envelope"></i>
                <span>Templates</span>
              </a>
              <a href="<?php echo e(route('messages.chat')); ?>" class="card-link">
                <i class="fas fa-list"></i>
                <span>Logs</span>
              </a>
              <a href="<?php echo e(route('admin.settings.payment')); ?>" class="card-link">
                <i class="fas fa-dollar-sign"></i>
                <span>Billing</span>
              </a>
            </div>
          </div>

          <!-- E-Signature -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper blue">
                <i class="fas fa-signature"></i>
              </div>
              <h5>E-Signature</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('esignatures.index')); ?>" class="card-link">
                <i class="fas fa-file-signature"></i>
                <span>Requests</span>
              </a>
              <a href="<?php echo e(route('esignatures.index')); ?>" class="card-link">
                <i class="fas fa-pen"></i>
                <span>Templates</span>
              </a>
              <a href="<?php echo e(route('esignatures.index')); ?>" class="card-link">
                <i class="fas fa-stamp"></i>
                <span>Notary</span>
              </a>
              <a href="<?php echo e(route('esignatures.index')); ?>" class="card-link">
                <i class="fas fa-history"></i>
                <span>History</span>
              </a>
              <a href="<?php echo e(route('esignatures.index')); ?>" class="card-link">
                <i class="fas fa-shield-alt"></i>
                <span>Legal</span>
              </a>
            </div>
          </div>

          <!-- Job Scheduler -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper gray">
                <i class="fas fa-clock"></i>
              </div>
              <h5>Job Scheduler</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.system.queue')); ?>" class="card-link">
                <i class="fas fa-tasks"></i>
                <span>Jobs</span>
              </a>
              <a href="<?php echo e(route('admin.system.queue')); ?>" class="card-link">
                <i class="fas fa-calendar-alt"></i>
                <span>Cron</span>
              </a>
              <a href="<?php echo e(route('admin.system.queue')); ?>" class="card-link">
                <i class="fas fa-list"></i>
                <span>Queue</span>
              </a>
              <a href="<?php echo e(route('admin.system.queue')); ?>" class="card-link">
                <i class="fas fa-redo"></i>
                <span>Retry</span>
              </a>
              <a href="<?php echo e(route('admin.system.queue')); ?>" class="card-link">
                <i class="fas fa-skull"></i>
                <span>Dead Letter</span>
              </a>
            </div>
          </div>

          <!-- Data Retention -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper red">
                <i class="fas fa-trash-alt"></i>
              </div>
              <h5>Data Retention</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.settings.general')); ?>" class="card-link">
                <i class="fas fa-cog"></i>
                <span>Policies</span>
              </a>
              <a href="<?php echo e(route('admin.backups')); ?>" class="card-link">
                <i class="fas fa-archive"></i>
                <span>Archiving</span>
              </a>
              <a href="<?php echo e(route('admin.system.logs')); ?>" class="card-link">
                <i class="fas fa-eraser"></i>
                <span>Cleanup</span>
              </a>
              <a href="<?php echo e(route('admin.system.logs')); ?>" class="card-link">
                <i class="fas fa-shield-alt"></i>
                <span>GDPR</span>
              </a>
              <a href="<?php echo e(route('admin.system.logs')); ?>" class="card-link">
                <i class="fas fa-history"></i>
                <span>Logs</span>
              </a>
            </div>
          </div>

          <!-- Feedback System -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper orange">
                <i class="fas fa-comment-dots"></i>
              </div>
              <h5>Feedback</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('surveys.index')); ?>" class="card-link">
                <i class="fas fa-poll"></i>
                <span>Surveys</span>
              </a>
              <a href="<?php echo e(route('reviews.index')); ?>" class="card-link">
                <i class="fas fa-star"></i>
                <span>Reviews</span>
              </a>
              <a href="<?php echo e(route('requests.index')); ?>" class="card-link">
                <i class="fas fa-envelope-open-text"></i>
                <span>Requests</span>
              </a>
              <a href="<?php echo e(route('testimonials.index')); ?>" class="card-link">
                <i class="fas fa-quote-right"></i>
                <span>Testimonials</span>
              </a>
              <a href="<?php echo e(route('surveys.index')); ?>" class="card-link">
                <i class="fas fa-chart-bar"></i>
                <span>NPS</span>
              </a>
            </div>
          </div>

          <!-- Web3 & Blockchain Hub -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper indigo">
                <i class="fas fa-cubes"></i>
              </div>
              <h5>Web3 & Blockchain</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('blockchain.wallets.index')); ?>" class="card-link">
                <i class="fas fa-wallet"></i>
                <span>Crypto Wallets</span>
              </a>
              <a href="<?php echo e(route('blockchain.smartcontracts.index')); ?>" class="card-link">
                <i class="fas fa-file-contract"></i>
                <span>Smart Contracts</span>
              </a>
              <a href="<?php echo e(route('blockchain.nfts.index')); ?>" class="card-link">
                <i class="fas fa-link"></i>
                <span>NFT Marketplace</span>
              </a>
              <a href="<?php echo e(route('blockchain.dao.index')); ?>" class="card-link">
                <i class="fas fa-users-rectangle"></i>
                <span>DAO Governance</span>
              </a>
              <a href="<?php echo e(route('blockchain.defi.index')); ?>" class="card-link">
                <i class="fas fa-hand-holding-dollar"></i>
                <span>DeFi Lending</span>
              </a>
            </div>
          </div>

          <!-- Advanced Tech & IoT -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper orange">
                <i class="fas fa-microchip"></i>
              </div>
              <h5>Advanced Technology</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="#" class="card-link">
                <i class="fas fa-tower-broadcast"></i>
                <span>IoT Ingestion</span>
              </a>
              <a href="<?php echo e(route('blockchain.geospatial.analysis')); ?>" class="card-link">
                <i class="fas fa-map-location-dot"></i>
                <span>Geospatial AI</span>
              </a>
              <a href="<?php echo e(route('blockchain.metaverse.properties')); ?>" class="card-link">
                <i class="fas fa-vr-cardboard"></i>
                <span>Metaverse View</span>
              </a>
              <a href="<?php echo e(route('admin.performance.dashboard')); ?>" class="card-link">
                <i class="fas fa-bolt"></i>
                <span>Perf Audit</span>
              </a>
              <a href="#" class="card-link">
                <i class="fas fa-database"></i>
                <span>Big Data Lake</span>
              </a>
            </div>
          </div>

          <!-- Marketing & Growth -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper pink">
                <i class="fas fa-arrow-trend-up"></i>
              </div>
              <h5>Growth & Marketing</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('leads.dashboard')); ?>" class="card-link">
                <i class="fas fa-funnel-dollar"></i>
                <span>CRM & Pipeline</span>
              </a>
              <a href="<?php echo e(route('admin.seo.index')); ?>" class="card-link">
                <i class="fas fa-magnifying-glass-chart"></i>
                <span>SEO Strategy</span>
              </a>
              <a href="#" class="card-link">
                <i class="fas fa-envelope-open-text"></i>
                <span>Campaign Builder</span>
              </a>
              <a href="#" class="card-link">
                <i class="fas fa-handshake"></i>
                <span>Affiliate Portal</span>
              </a>
              <a href="<?php echo e(route('ai.market-insights')); ?>" class="card-link">
                <i class="fas fa-lightbulb"></i>
                <span>Market Intelligence</span>
              </a>
            </div>
          </div>

          <!-- Neural Intelligence Hub -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper blue">
                <i class="fas fa-brain"></i>
              </div>
              <h5>Neural AI Hub</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.intelligence.index')); ?>" class="card-link">
                <i class="fas fa-microchip"></i>
                <span>Intelligence Brain</span>
              </a>
              <a href="<?php echo e(route('ai.smart-search')); ?>" class="card-link">
                <i class="fas fa-search-plus"></i>
                <span>Smart Search (NL)</span>
              </a>
              <a href="<?php echo e(route('ai.chat')); ?>" class="card-link">
                <i class="fas fa-comments-alt"></i>
                <span>AI Consultant</span>
              </a>
              <a href="<?php echo e(route('ai.valuation.dashboard')); ?>" class="card-link">
                <i class="fas fa-home-alt"></i>
                <span>Neural Valuation</span>
              </a>
              <a href="<?php echo e(route('ai.price-prediction')); ?>" class="card-link">
                <i class="fas fa-chart-line"></i>
                <span>Price Prediction</span>
              </a>
              <a href="<?php echo e(route('ai.virtual-reality')); ?>" class="card-link">
                <i class="fas fa-vr-cardboard"></i>
                <span>Reality (VR/AR)</span>
              </a>
            </div>
          </div>

          <!-- Advanced Prop-Tech -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper orange">
                <i class="fas fa-tools"></i>
              </div>
              <h5>Prop-Tech Tools</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('tools.valuation.index')); ?>" class="card-link">
                <i class="fas fa-calculator"></i>
                <span>Home Valuation</span>
              </a>
              <a href="<?php echo e(route('tools.predictive.index')); ?>" class="card-link">
                <i class="fas fa-chart-bar"></i>
                <span>Market Predictor</span>
              </a>
              <a href="<?php echo e(route('tools.investment.index')); ?>" class="card-link">
                <i class="fas fa-coins"></i>
                <span>Invest Simulator</span>
              </a>
              <a href="<?php echo e(route('tools.visual.index')); ?>" class="card-link">
                <i class="fas fa-image"></i>
                <span>Visual Search</span>
              </a>
              <a href="<?php echo e(route('tools.neighborhood.index')); ?>" class="card-link">
                <i class="fas fa-map-marked-alt"></i>
                <span>Neighborhood Score</span>
              </a>
              <a href="<?php echo e(route('mortgage.index')); ?>" class="card-link">
                <i class="fas fa-handshake"></i>
                <span>Mortgage Market</span>
              </a>
            </div>
          </div>

          <!-- Fraud & Security Shield -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper red">
                <i class="fas fa-shield-alt"></i>
              </div>
              <h5>Security Shield</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.fraud.dashboard')); ?>" class="card-link">
                <i class="fas fa-user-secret"></i>
                <span>Fraud Dashboard</span>
              </a>
              <a href="<?php echo e(route('admin.fraud.duplicates')); ?>" class="card-link">
                <i class="fas fa-copy"></i>
                <span>Duplicate Check</span>
              </a>
              <a href="<?php echo e(route('admin.fraud.high-risk-users')); ?>" class="card-link">
                <i class="fas fa-exclamation-triangle"></i>
                <span>High Risk Users</span>
              </a>
              <a href="<?php echo e(route('admin.settings.security')); ?>" class="card-link">
                <i class="fas fa-lock"></i>
                <span>Hardening</span>
              </a>
            </div>
          </div>

          <!-- Ops & Markets -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper green">
                <i class="fas fa-file-contract"></i>
              </div>
              <h5>Ops & Markets</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('messages.auctions.index')); ?>" class="card-link">
                <i class="fas fa-gavel"></i>
                <span>Real Auctions</span>
              </a>
              <a href="<?php echo e(route('messages.auctions.results')); ?>" class="card-link">
                <i class="fas fa-trophy"></i>
                <span>Results</span>
              </a>
              <a href="<?php echo e(route('contracts.index')); ?>" class="card-link">
                <i class="fas fa-file-signature"></i>
                <span>Blockchain Deals</span>
              </a>
              <a href="<?php echo e(route('tokenization.index')); ?>" class="card-link">
                <i class="fas fa-cubes"></i>
                <span>Tokenization Hub</span>
              </a>
            </div>
          </div>

          <!-- Performance Pro -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper gray">
                <i class="fas fa-tachometer-alt"></i>
              </div>
              <h5>Performance Pro</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.performance.dashboard')); ?>" class="card-link">
                <i class="fas fa-chart-line"></i>
                <span>Sys Performance</span>
              </a>
              <a href="<?php echo e(route('admin.performance.cache')); ?>" class="card-link">
                <i class="fas fa-bolt"></i>
                <span>Cache Tuning</span>
              </a>
              <a href="<?php echo e(route('admin.performance.database')); ?>" class="card-link">
                <i class="fas fa-database"></i>
                <span>DB Health</span>
              </a>
              <a href="<?php echo e(route('admin.performance.realtime')); ?>" class="card-link">
                <i class="fas fa-clock"></i>
                <span>Real-time Monitor</span>
              </a>
            </div>
          </div>

          <!-- Institutional Investment -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper purple">
                <i class="fas fa-building-columns"></i>
              </div>
              <h5>Institutional Invest</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('investors.opportunities.index')); ?>" class="card-link">
                <i class="fas fa-lightbulb"></i>
                <span>Opportunities</span>
              </a>
              <a href="<?php echo e(route('investor.funds.index')); ?>" class="card-link">
                <i class="fas fa-coins"></i>
                <span>Equity Funds</span>
              </a>
              <a href="<?php echo e(route('investor.crowdfunding.index')); ?>" class="card-link">
                <i class="fas fa-users-cog"></i>
                <span>Crowdfunding</span>
              </a>
              <a href="<?php echo e(route('investor.roi.index')); ?>" class="card-link">
                <i class="fas fa-calculator"></i>
                <span>Yield Analysis</span>
              </a>
              <a href="<?php echo e(route('investor.risk.index')); ?>" class="card-link">
                <i class="fas fa-shield-virus"></i>
                <span>Risk Profiling</span>
              </a>
            </div>
          </div>

          <!-- Enterprise Governance -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper purple">
                <i class="fas fa-building-shield"></i>
              </div>
              <h5>Enterprise Control</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('enterprise.dashboard')); ?>" class="card-link">
                <i class="fas fa-chart-line"></i>
                <span>Enterprise Dash</span>
              </a>
              <a href="<?php echo e(route('enterprise.accounts')); ?>" class="card-link">
                <i class="fas fa-users"></i>
                <span>Account Mgmt</span>
              </a>
              <a href="<?php echo e(route('enterprise.subscriptions')); ?>" class="card-link">
                <i class="fas fa-crown"></i>
                <span>Subscription Tiers</span>
              </a>
              <a href="<?php echo e(route('enterprise.billing')); ?>" class="card-link">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Billing & Invoicing</span>
              </a>
              <a href="<?php echo e(route('enterprise.reports')); ?>" class="card-link">
                <i class="fas fa-chart-bar"></i>
                <span>Global Reports</span>
              </a>
            </div>
          </div>

          <!-- Smart CMS & Knowledge -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper amber">
                <i class="fas fa-pen-nib"></i>
              </div>
              <h5>Smart CMS Hub</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.content.dashboard')); ?>" class="card-link">
                <i class="fas fa-edit"></i>
                <span>CMS Dashboard</span>
              </a>
              <a href="<?php echo e(route('admin.pages.index')); ?>" class="card-link">
                <i class="fas fa-file-code"></i>
                <span>Page Builder</span>
              </a>
              <a href="<?php echo e(route('admin.media.index')); ?>" class="card-link">
                <i class="fas fa-photo-video"></i>
                <span>Media Library</span>
              </a>
              <a href="<?php echo e(route('admin.guides.index')); ?>" class="card-link">
                <i class="fas fa-map-signs"></i>
                <span>Guides & FAQs</span>
              </a>
              <a href="<?php echo e(route('admin.menus.index')); ?>" class="card-link">
                <i class="fas fa-bars"></i>
                <span>Menu Engine</span>
              </a>
              <a href="<?php echo e(route('admin.widgets.index')); ?>" class="card-link">
                <i class="fas fa-puzzle-piece"></i>
                <span>UI Widgets</span>
              </a>
            </div>
          </div>

          <!-- Revenue & Growth Funnel -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper rose">
                <i class="fas fa-filter"></i>
              </div>
              <h5>Growth & Revenue</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('leads.pipeline')); ?>" class="card-link">
                <i class="fas fa-stream"></i>
                <span>Sales Pipeline</span>
              </a>
              <a href="<?php echo e(route('lead-scoring.index')); ?>" class="card-link">
                <i class="fas fa-star-half-alt"></i>
                <span>Smart Scoring</span>
              </a>
              <a href="<?php echo e(route('lead-conversions.analytics')); ?>" class="card-link">
                <i class="fas fa-chart-pie"></i>
                <span>Conversion Ads</span>
              </a>
              <a href="<?php echo e(route('marketing.campaigns')); ?>" class="card-link">
                <i class="fas fa-ad"></i>
                <span>Campaigns</span>
              </a>
            </div>
          </div>

          <!-- Transaction & Legal Center -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper teal">
                <i class="fas fa-file-contract"></i>
              </div>
              <h5>Legal & Deals</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('messages.offers.index')); ?>" class="card-link">
                <i class="fas fa-handshake"></i>
                <span>Offer Mgmt</span>
              </a>
              <a href="<?php echo e(route('messages.negotiations.index')); ?>" class="card-link">
                <i class="fas fa-comments-dollar"></i>
                <span>Negotiations</span>
              </a>
              <a href="<?php echo e(route('esignatures.index')); ?>" class="card-link">
                <i class="fas fa-signature"></i>
                <span>E-Signatures</span>
              </a>
              <a href="<?php echo e(route('blockchain.smartcontracts.index')); ?>" class="card-link">
                <i class="fas fa-code"></i>
                <span>Smart Contracts</span>
              </a>
            </div>
          </div>

          <!-- Geo-Spatial Intelligence (AI) -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper cyan">
                <i class="fas fa-map-location-dot"></i>
              </div>
              <h5>Geo-Spatial AI</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(url('/api/intelligence/layers/price-heatmap')); ?>" target="_blank" class="card-link">
                <i class="fas fa-layer-group"></i>
                <span>Price Heatmap</span>
              </a>
              <a href="<?php echo e(url('/api/intelligence/layers/demand-density')); ?>" target="_blank" class="card-link">
                <i class="fas fa-fire-alt"></i>
                <span>Demand Density</span>
              </a>
              <a href="<?php echo e(url('/api/intelligence/layers/growth-zones')); ?>" target="_blank" class="card-link">
                <i class="fas fa-chart-area"></i>
                <span>Growth Zones</span>
              </a>
              <a href="<?php echo e(url('/api/intelligence/layers/crime')); ?>" target="_blank" class="card-link">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Safety Index</span>
              </a>
              <a href="<?php echo e(route('blockchain.geospatial.analysis')); ?>" class="card-link">
                <i class="fas fa-satellite"></i>
                <span>GIS Intelligence</span>
              </a>
            </div>
          </div>

          <!-- Web3 & DeFi Economy -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper purple">
                <i class="fas fa-cubes"></i>
              </div>
              <h5>Web3 & DeFi</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('blockchain.wallets.index')); ?>" class="card-link">
                <i class="fas fa-wallet"></i>
                <span>Crypto Wallets</span>
              </a>
              <a href="<?php echo e(route('blockchain.defi.lending')); ?>" class="card-link">
                <i class="fas fa-hand-holding-dollar"></i>
                <span>DeFi Lending</span>
              </a>
              <a href="<?php echo e(route('investor.defi.staking.index')); ?>" class="card-link">
                <i class="fas fa-layer-group"></i>
                <span>Yield Staking</span>
              </a>
              <a href="<?php echo e(route('blockchain.dao.index')); ?>" class="card-link">
                <i class="fas fa-users-rectangle"></i>
                <span>DAO Governance</span>
              </a>
            </div>
          </div>

          <!-- Infrastructure & Developer -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper slate">
                <i class="fas fa-server"></i>
              </div>
              <h5>Core Infrastructure</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.system.logs')); ?>" class="card-link">
                <i class="fas fa-clock-rotate-left"></i>
                <span>Audit Logs</span>
              </a>
              <a href="<?php echo e(route('admin.system.queue')); ?>" class="card-link">
                <i class="fas fa-tasks"></i>
                <span>Job Queues</span>
              </a>
              <a href="<?php echo e(route('lead-import.index')); ?>" class="card-link">
                <i class="fas fa-file-import"></i>
                <span>Data Migration</span>
              </a>
              <a href="<?php echo e(route('admin.settings.index')); ?>" class="card-link">
                <i class="fas fa-key"></i>
                <span>API Gateway</span>
              </a>
            </div>
          </div>

          <!-- Media & News Center -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper pink">
                <i class="fas fa-bullhorn"></i>
              </div>
              <h5>Public Relations</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.blog.posts.index')); ?>" class="card-link">
                <i class="fas fa-newspaper"></i>
                <span>Blog Posts</span>
              </a>
              <a href="<?php echo e(route('admin.news.index')); ?>" class="card-link">
                <i class="fas fa-rss"></i>
                <span>News Feed</span>
              </a>
              <a href="<?php echo e(route('marketing.index')); ?>" class="card-link">
                <i class="fas fa-bullseye"></i>
                <span>PR Analytics</span>
              </a>
            </div>
          </div>

          <!-- Enterprise Governance -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper blue">
                <i class="fas fa-balance-scale"></i>
              </div>
              <h5>Enterprise Governance</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(route('admin.system.logs')); ?>" class="card-link">
                <i class="fas fa-shield-alt"></i>
                <span>Compliance</span>
              </a>
              <a href="<?php echo e(route('admin.system.logs')); ?>" class="card-link">
                <i class="fas fa-clipboard-check"></i>
                <span>Audit Trail</span>
              </a>
              <a href="<?php echo e(route('admin.settings.index')); ?>" class="card-link">
                <i class="fas fa-file-contract"></i>
                <span>Policies</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Risk Management</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-handshake"></i>
                <span>Ethics</span>
              </a>
            </div>
          </div>

          <!-- Advanced Analytics -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper purple">
                <i class="fas fa-chart-bar"></i>
              </div>
              <h5>Advanced Analytics</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-brain"></i>
                <span>Predictive Analytics</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-user-chart"></i>
                <span>Behavioral Analysis</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-smile"></i>
                <span>Sentiment Analysis</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-chart-line"></i>
                <span>Forecasting</span>
              </a>
            </div>
          </div>

          <!-- Global Operations -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper green">
                <i class="fas fa-globe"></i>
              </div>
              <h5>Global Operations</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-map-marked-alt"></i>
                <span>Regional Management</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-language"></i>
                <span>Multilingual</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-coins"></i>
                <span>Currency Exchange</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-receipt"></i>
                <span>International Tax</span>
              </a>
            </div>
          </div>

          <!-- Innovation Lab -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper orange">
                <i class="fas fa-flask"></i>
              </div>
              <h5>Innovation Lab</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-vial"></i>
                <span>Beta Features</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-microscope"></i>
                <span>R&D Projects</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-certificate"></i>
                <span>Patents</span>
              </a>
            </div>
          </div>

          <!-- Customer Experience -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper pink">
                <i class="fas fa-heart"></i>
              </div>
              <h5>Customer Experience</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-route"></i>
                <span>Journey Mapping</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-star"></i>
                <span>NPS Score</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-sync"></i>
                <span>Feedback Loop</span>
              </a>
            </div>
          </div>

          <!-- Sustainability Hub -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper green">
                <i class="fas fa-leaf"></i>
              </div>
              <h5>Sustainability Hub</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-cloud"></i>
                <span>Carbon Tracking</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-chart-pie"></i>
                <span>ESG Metrics</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-home"></i>
                <span>Green Building</span>
              </a>
            </div>
          </div>

          <!-- Security Operations -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper red">
                <i class="fas fa-shield-virus"></i>
              </div>
              <h5>Security Operations</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-eye"></i>
                <span>Threat Intelligence</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Incident Response</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-bug"></i>
                <span>Vulnerability Scan</span>
              </a>
            </div>
          </div>

          <!-- Digital Transformation -->
          <div class="premium-card">
            <div class="card-header">
              <div class="icon-wrapper blue">
                <i class="fas fa-digital-tachograph"></i>
              </div>
              <h5>Digital Transformation</h5>
            </div>
            <div class="card-divider"></div>
            <div class="card-content">
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-robot"></i>
                <span>Process Automation</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-brain"></i>
                <span>AI Integration</span>
              </a>
              <a href="<?php echo e(url('#')); ?>" class="card-link">
                <i class="fas fa-cloud-upload-alt"></i>
                <span>Cloud Migration</span>
              </a>
            </div>
          </div>

        </div>
      </div>

      <!-- Activity Log -->
      <div class="modern-card p-6 mt-6">
        <div class="flex justify-between items-center mb-6">
          <h3 class="section-header mb-0">Recent Activity</h3>
          <button onclick="refreshActivity()"
            class="w-8 h-8 rounded-full bg-gray-50 hover:bg-gray-100 flex items-center justify-center text-gray-600 transition-all hover:rotate-180">
            <i class="fas fa-sync-alt"></i>
          </button>
        </div>

        <div class="space-y-3">
          <?php $__empty_1 = true; $__currentLoopData = $stats['recent_activity']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div
              class="flex items-center p-3 rounded-xl hover:bg-gray-50 border border-transparent hover:border-gray-100 transition-all group cursor-default">
              <div class="icon-container blue mr-4 group-hover:scale-110 transition-transform">
                <i class="fas fa-<?php echo e($activity['icon']); ?> text-sm"></i>
              </div>
              <div class="flex-1">
                <p class="text-sm font-semibold text-gray-800"><?php echo e($activity['message']); ?></p>
                <p class="text-xs text-gray-500 mt-1"><?php echo e($activity['time']); ?></p>
              </div>
              <div class="w-2 h-2 rounded-full bg-blue-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center py-8">
              <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-50 mb-3">
                <i class="fas fa-history text-gray-400"></i>
              </div>
              <p class="text-gray-500 text-sm">No recent activity</p>
            </div>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </div>

  <!-- Reports Modal -->
  <div id="reportsModal"
    class="fixed inset-0 modal-overlay flex items-center justify-center z-50 hidden backdrop-blur-sm bg-gray-900/50 transition-opacity">
    <div
      class="modal-content bg-white rounded-2xl shadow-2xl p-8 max-w-3xl mx-4 w-full transform transition-all scale-100">
      <div class="flex justify-between items-center mb-6">
        <div>
          <h3 class="text-xl font-bold text-gray-900">System Reports</h3>
          <p class="text-sm text-gray-500 mt-1">Generate and download system analytics</p>
        </div>
        <button onclick="closeReportsModal()"
          class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500 transition-colors">
          <i class="fas fa-times"></i>
        </button>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- User Report -->
        <div
          class="group p-4 rounded-xl border border-gray-200 hover:border-blue-500 hover:shadow-lg transition-all cursor-pointer bg-white">
          <div class="flex items-start gap-4">
            <div class="icon-container blue shrink-0 group-hover:scale-110 transition-transform">
              <i class="fas fa-users"></i>
            </div>
            <div>
              <h4 class="font-bold text-gray-900 mb-1 group-hover:text-blue-600 transition-colors">User Report</h4>
              <p class="text-xs text-gray-500 mb-3 leading-relaxed">Detailed user statistics, registration trends, and
                activity analytics.</p>
              <button class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                Generate PDF <i class="fas fa-arrow-right text-[10px]"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- Financial Report -->
        <div
          class="group p-4 rounded-xl border border-gray-200 hover:border-green-500 hover:shadow-lg transition-all cursor-pointer bg-white">
          <div class="flex items-start gap-4">
            <div class="icon-container green shrink-0 group-hover:scale-110 transition-transform">
              <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <div>
              <h4 class="font-bold text-gray-900 mb-1 group-hover:text-green-600 transition-colors">Financial Report</h4>
              <p class="text-xs text-gray-500 mb-3 leading-relaxed">Revenue analysis, payment history, and transaction
                records.</p>
              <button class="text-xs font-bold text-green-600 hover:text-green-700 flex items-center gap-1">
                Generate PDF <i class="fas fa-arrow-right text-[10px]"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- Property Report -->
        <div
          class="group p-4 rounded-xl border border-gray-200 hover:border-orange-500 hover:shadow-lg transition-all cursor-pointer bg-white">
          <div class="flex items-start gap-4">
            <div class="icon-container orange shrink-0 group-hover:scale-110 transition-transform">
              <i class="fas fa-home"></i>
            </div>
            <div>
              <h4 class="font-bold text-gray-900 mb-1 group-hover:text-orange-600 transition-colors">Property Report</h4>
              <p class="text-xs text-gray-500 mb-3 leading-relaxed">Property listings status, occupancy rates, and
                performance metrics.</p>
              <button class="text-xs font-bold text-orange-600 hover:text-orange-700 flex items-center gap-1">
                Generate PDF <i class="fas fa-arrow-right text-[10px]"></i>
              </button>
            </div>
          </div>
        </div>

        <!-- System Report -->
        <div
          class="group p-4 rounded-xl border border-gray-200 hover:border-purple-500 hover:shadow-lg transition-all cursor-pointer bg-white">
          <div class="flex items-start gap-4">
            <div class="icon-container purple shrink-0 group-hover:scale-110 transition-transform">
              <i class="fas fa-server"></i>
            </div>
            <div>
              <h4 class="font-bold text-gray-900 mb-1 group-hover:text-purple-600 transition-colors">System Report</h4>
              <p class="text-xs text-gray-500 mb-3 leading-relaxed">Server health, error logs, and performance benchmarks.
              </p>
              <button class="text-xs font-bold text-purple-600 hover:text-purple-700 flex items-center gap-1">
                Generate PDF <i class="fas fa-arrow-right text-[10px]"></i>
              </button>
            </div>
          </div>
        </div>
      </div>

      <div class="flex justify-end mt-8 pt-4 border-t border-gray-100">
        <button onclick="closeReportsModal()"
          class="px-6 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-colors">
          Close
        </button>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // User Growth Chart
      const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
      new Chart(userGrowthCtx, {
        type: 'line',
        data: {
          labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
          datasets: [{
            label: 'المستخدمون الجدد',
            data: [12, 19, 3, 5, 2, 3],
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: 'rgb(59, 130, 246)',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 6,
            pointHoverRadius: 8
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: true,
              position: 'bottom',
              labels: {
                padding: 20,
                font: {
                  size: 12,
                  family: 'system-ui'
                }
              }
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              padding: 12,
              cornerRadius: 8,
              titleFont: {
                size: 14
              },
              bodyFont: {
                size: 13
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: 'rgba(0, 0, 0, 0.05)'
              },
              ticks: {
                font: {
                  size: 11
                }
              }
            },
            x: {
              grid: {
                display: false
              },
              ticks: {
                font: {
                  size: 11
                }
              }
            }
          }
        }
      });

      // Revenue Chart
      const revenueCtx = document.getElementById('revenueChart').getContext('2d');
      new Chart(revenueCtx, {
        type: 'bar',
        data: {
          labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
          datasets: [{
            label: 'الإيرادات ($)',
            data: [12000, 19000, 30000, 50000, 42000, 38000],
            backgroundColor: [
              'rgba(34, 197, 94, 0.8)',
              'rgba(34, 197, 94, 0.8)',
              'rgba(34, 197, 94, 0.8)',
              'rgba(34, 197, 94, 0.8)',
              'rgba(34, 197, 94, 0.8)',
              'rgba(34, 197, 94, 0.8)'
            ],
            borderColor: 'rgb(34, 197, 94)',
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: true,
              position: 'bottom',
              labels: {
                padding: 20,
                font: {
                  size: 12,
                  family: 'system-ui'
                }
              }
            },
            tooltip: {
              backgroundColor: 'rgba(0, 0, 0, 0.8)',
              padding: 12,
              cornerRadius: 8,
              titleFont: {
                size: 14
              },
              bodyFont: {
                size: 13
              },
              callbacks: {
                label: function (context) {
                  return 'الإيرادات: $' + context.parsed.y.toLocaleString();
                }
              }
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                color: 'rgba(0, 0, 0, 0.05)'
              },
              ticks: {
                font: {
                  size: 11
                },
                callback: function (value) {
                  return '$' + value.toLocaleString();
                }
              }
            },
            x: {
              grid: {
                display: false
              },
              ticks: {
                font: {
                  size: 11
                }
              }
            }
          }
        }
      });
    });

    function refreshData() {
      location.reload();
    }

    function refreshActivity() {
      fetch('/admin/activity')
        .then(response => response.json())
        .then(data => {
          location.reload();
        })
        .catch(error => {
          console.error('Error:', error);
        });
    }

    function showReportsModal() {
      document.getElementById('reportsModal').classList.remove('hidden');
    }

    function closeReportsModal() {
      document.getElementById('reportsModal').classList.add('hidden');
    }

  </script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\larvel state big\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>