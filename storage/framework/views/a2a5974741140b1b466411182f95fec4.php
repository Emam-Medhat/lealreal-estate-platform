<?php $__env->startSection('title', app()->getLocale() == 'ar' ? 'مركز الأنظمة' : 'Systems Hub'); ?>

<?php $__env->startSection('page-title', app()->getLocale() == 'ar' ? 'خارطة أنظمة المنصة' : 'Platform Architecture Hub'); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        :root {
            --primary: #6366f1;
            --primary-light: #eef2ff;
            --bg-body: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border-light: #f1f5f9;
            --radius: 20px;
        }

        body {
            background-color: var(--bg-body) !important;
            font-family: 'Cairo', 'Inter', sans-serif;
        }

        .page-background {
            min-height: 100vh;
            padding: 24px;
        }

        .glass-card {
            background: var(--card-bg);
            border: 1px solid rgba(229, 231, 235, 0.8);
            border-radius: var(--radius);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .glass-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08);
            border-color: rgba(99, 102, 241, 0.2);
        }

        /* Stat Cards Matching Dashboard */
        .stat-card {
            position: relative;
            overflow: hidden;
            color: white;
            padding: 24px;
            border-radius: var(--radius);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        }

        .stat-card-blue { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
        .stat-card-purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
        .stat-card-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
        .stat-card-orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }

        .icon-badge {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            color: white;
            font-size: 1.25rem;
            transition: all 0.3s ease;
        }

        .stat-card:hover .icon-badge {
            transform: scale(1.1) rotate(5deg);
        }

        /* Category Filter Chips */
        .category-chip {
            background: white;
            border: 1px solid var(--border-light);
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--text-muted);
            transition: all 0.2s ease;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .category-chip:hover {
            background: #f9fafb;
            color: var(--text-main);
            transform: translateY(-2px);
            border-color: var(--primary);
        }

        .category-chip.active {
            background: var(--primary);
            color: white !important;
            border-color: var(--primary);
            box-shadow: 0 8px 16px rgba(99, 102, 241, 0.2);
        }

        /* Search Input */
        .search-container {
            position: relative;
            width: 100%;
            max-width: 400px;
        }

        .search-input {
            width: 100%;
            padding: 12px 16px 12px 48px;
            background: white;
            border: 1px solid var(--border-light);
            border-radius: 14px;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-main);
            transition: all 0.3s ease;
            direction: rtl;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            pointer-events: none;
            transition: color 0.3s ease;
        }

        .search-input:focus + .search-icon {
            color: var(--primary);
        }

        /* Breadcrumbs */
        .breadcrumb-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
            text-decoration: none !important;
        }

        .breadcrumb-item:hover {
            color: var(--primary);
        }

        .breadcrumb-active {
            color: var(--primary);
            font-weight: 700;
            background: var(--primary-light);
            padding: 4px 12px;
            border-radius: 8px;
        }

        .gradient-text {
            background: linear-gradient(135deg, #1e293b 0%, #4f46e5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn-back {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: white;
            border: 1px solid var(--border-light);
            border-radius: 12px;
            color: var(--text-main);
            font-size: 0.85rem;
            font-weight: 700;
            transition: all 0.2s ease;
            text-decoration: none !important;
        }

        .btn-back:hover {
            background: #f9fafb;
            transform: translateX(-4px);
            border-color: var(--primary);
            color: var(--primary);
        }

        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.5s ease forwards;
        }

        /* RTL specific fixes */
        .flex-row-reverse-rtl {
            flex-direction: row-reverse;
        }

        [dir="rtl"] .search-input {
            padding: 12px 48px 12px 16px;
        }
        
        [dir="rtl"] .search-icon {
            right: 16px;
            left: auto;
        }

    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-background" dir="rtl">
        <!-- Breadcrumbs -->
        <nav class="flex mb-8 gap-3 items-center fade-in">
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="breadcrumb-item">
                <i class="fas fa-home"></i> <?php echo e(app()->getLocale() == 'ar' ? 'الرئيسية' : 'Admin'); ?>

            </a>
            <i class="fas fa-chevron-left text-[10px] opacity-30"></i>
            <span class="breadcrumb-active"><?php echo e(app()->getLocale() == 'ar' ? 'مركز الأنظمة' : 'Systems Hub'); ?></span>
        </nav>

        <!-- Header Section -->
        <div class="mb-12 flex flex-col lg:flex-row lg:items-center justify-between gap-6 fade-in">
            <div>
                <h1 class="text-4xl font-black mb-3 tracking-tight gradient-text">
                    <?php echo e(app()->getLocale() == 'ar' ? 'خارطة أنظمة المنصة' : 'Platform Architecture Hub'); ?>

                </h1>
                <p class="text-slate-500 font-medium max-w-2xl text-lg">
                    <?php echo e(app()->getLocale() == 'ar' ? 'استعراض شامل لجميع الأنظمة، الموديولات، والميزات المتقدمة المدمجة في المنصة.' : 'Comprehensive overview of all internal systems, modules, and advanced features integrated into the platform.'); ?>

                </p>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-4">
                <div class="search-container">
                    <input type="text" id="moduleSearch" 
                        placeholder="<?php echo e(app()->getLocale() == 'ar' ? 'ابحث عن نظام...' : 'Quick search...'); ?>"
                        class="search-input">
                    <i class="fas fa-search search-icon"></i>
                </div>
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn-back">
                    <i class="fas fa-arrow-right"></i>
                    <?php echo e(app()->getLocale() == 'ar' ? 'العودة' : 'Back'); ?>

                </a>
            </div>
        </div>

        <!-- Stats Bar -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12 fade-in">
            <div class="stat-card stat-card-blue">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <div class="text-xs font-semibold text-white/80 uppercase tracking-wider mb-1"><?php echo e(app()->getLocale() == 'ar' ? 'إجمالي المستخدمين' : 'Total Users'); ?></div>
                        <div class="text-3xl font-bold text-white"><?php echo e(number_format($stats['total_users'] ?? 0)); ?></div>
                    </div>
                    <div class="icon-badge">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-white/90 font-bold flex items-center gap-1">
                        <i class="fas fa-arrow-up"></i> +12.5%
                    </span>
                    <span class="text-white/70"><?php echo e(app()->getLocale() == 'ar' ? 'من الشهر الماضي' : 'from last month'); ?></span>
                </div>
            </div>

            <div class="stat-card stat-card-green">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <div class="text-xs font-semibold text-white/80 uppercase tracking-wider mb-1"><?php echo e(app()->getLocale() == 'ar' ? 'العقارات والاصول' : 'Assets & Properties'); ?></div>
                        <div class="text-3xl font-bold text-white"><?php echo e(number_format($stats['total_properties'] ?? 0)); ?></div>
                    </div>
                    <div class="icon-badge">
                        <i class="fas fa-building"></i>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-white/90 font-bold flex items-center gap-1">
                        <i class="fas fa-arrow-up"></i> +8.3%
                    </span>
                    <span class="text-white/70"><?php echo e(app()->getLocale() == 'ar' ? 'من الشهر الماضي' : 'from last month'); ?></span>
                </div>
            </div>

            <div class="stat-card stat-card-purple">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <div class="text-xs font-semibold text-white/80 uppercase tracking-wider mb-1"><?php echo e(app()->getLocale() == 'ar' ? 'الذكاء الاصطناعي' : 'AI Modules'); ?></div>
                        <div class="text-3xl font-bold text-white"><?php echo e($stats['ai_tasks'] ?? 0); ?></div>
                    </div>
                    <div class="icon-badge">
                        <i class="fas fa-brain"></i>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-white/90 font-bold flex items-center gap-1">
                        <i class="fas fa-arrow-up"></i> +24.7%
                    </span>
                    <span class="text-white/70"><?php echo e(app()->getLocale() == 'ar' ? 'من الشهر الماضي' : 'from last month'); ?></span>
                </div>
            </div>

            <div class="stat-card stat-card-orange">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <div class="text-xs font-semibold text-white/80 uppercase tracking-wider mb-1"><?php echo e(app()->getLocale() == 'ar' ? 'حالة النظام' : 'System Health'); ?></div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 bg-white rounded-full"></span>
                            <span class="text-2xl font-bold text-white"><?php echo e($stats['system_status'] ?? 'Stable'); ?></span>
                        </div>
                    </div>
                    <div class="icon-badge">
                        <i class="fas fa-heartbeat"></i>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="text-white/80 font-medium"><?php echo e(app()->getLocale() == 'ar' ? 'جميع الأنظمة تعمل بكفاءة' : 'All systems operational'); ?></span>
                </div>
            </div>
        </div>

        <!-- Category Filter -->
        <div class="flex flex-wrap gap-3 mb-10 fade-in">
            <button onclick="filterCategory('all')" 
                class="category-chip active">
                <?php echo e(app()->getLocale() == 'ar' ? 'جميع الأنظمة' : 'All Systems'); ?>

            </button>
            <?php $__currentLoopData = $data['categories']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <button onclick="filterCategory('<?php echo e($cat['id']); ?>')" 
                class="category-chip">
                <i class="fas <?php echo e($cat['icon']); ?>"></i>
                <?php echo e($cat['name']); ?>

            </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- Systems Grid -->
        <div class="fade-in">
            <?php echo $__env->make('admin.partials.hub_grid', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>

        <!-- Footer -->
        <footer class="mt-20 pt-8 border-t border-slate-200 flex flex-col md:flex-row items-center justify-between gap-6 pb-12 fade-in">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-layer-group text-white text-sm"></i>
                </div>
                <div>
                    <span class="text-xs font-bold text-slate-700 block">Platform Core v5.1.0</span>
                    <span class="text-xs text-slate-400 font-medium">Enterprise Architectural Blueprint</span>
                </div>
            </div>
            <div class="flex items-center gap-6">
                <a href="#" class="text-xs font-bold text-slate-500 hover:text-indigo-600 transition-colors"><?php echo e(app()->getLocale() == 'ar' ? 'التوثيق الفني' : 'Documentation'); ?></a>
                <a href="#" class="text-xs font-bold text-slate-500 hover:text-indigo-600 transition-colors"><?php echo e(app()->getLocale() == 'ar' ? 'بروتوكولات الأمان' : 'Security'); ?></a>
                <a href="#" class="px-4 py-2 bg-indigo-50 text-indigo-600 rounded-lg text-xs font-bold hover:bg-indigo-100 transition-all"><?php echo e(app()->getLocale() == 'ar' ? 'مركز المطورين' : 'Developer Center'); ?></a>
            </div>
        </footer>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script>
        // Category Filtering Logic
        function filterCategory(catId) {
            // Update Active Chip
            document.querySelectorAll('.category-chip').forEach(chip => {
                const isActive = (catId === 'all' && chip.textContent.includes('<?php echo e(app()->getLocale() == "ar" ? "جميع" : "All"); ?>')) || 
                                 (chip.getAttribute('onclick') && chip.getAttribute('onclick').includes("'" + catId + "'"));
                
                chip.classList.toggle('active', isActive);
            });

            // Show/Hide Sections
            document.querySelectorAll('.category-section').forEach(section => {
                if(catId === 'all' || section.getAttribute('data-category') === catId) {
                    section.classList.remove('hidden');
                    section.style.opacity = '0';
                    setTimeout(() => { 
                        section.style.transition = 'opacity 0.4s cubic-bezier(0.4, 0, 0.2, 1)'; 
                        section.style.opacity = '1'; 
                    }, 50);
                } else {
                    section.classList.add('hidden');
                }
            });
            
            // Scroll to top of content smoothly
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // Search Logic with Real-time Animation
        const searchInput = document.getElementById('moduleSearch');
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();

            document.querySelectorAll('.module-card').forEach(card => {
                const text = card.textContent.toLowerCase();
                
                if(text.includes(query)) {
                    card.classList.remove('hidden');
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                    card.classList.add('hidden');
                }
            });

            // Hide empty sections during search
            document.querySelectorAll('.category-section').forEach(section => {
                const visibleCards = section.querySelectorAll('.module-card:not(.hidden)');
                section.classList.toggle('hidden', visibleCards.length === 0 && query !== '');
            });
        });

        // Command+K Shortcut (Unified Discovery)
        document.addEventListener('keydown', (e) => {
            if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                e.preventDefault();
                searchInput.focus();
                searchInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    </script>
    <?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\larvel state big\resources\views/admin/hub.blade.php ENDPATH**/ ?>