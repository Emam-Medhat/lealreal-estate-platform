<?php $__env->startSection('title', 'لوحة تحكم المحتوى'); ?>

<?php $__env->startSection('content'); ?>
<style>
    :root {
        --blue: #0D6EFD;
        --blue-g: linear-gradient(135deg, #0D6EFD, #0a58ca);
        --purple: #6F42C1;
        --purple-g: linear-gradient(135deg, #6F42C1, #59359a);
        --green: #198754;
        --green-g: linear-gradient(135deg, #198754, #146c43);
        --orange: #FD7E14;
        --orange-g: linear-gradient(135deg, #FD7E14, #dc6a0c);
        --teal: #0DCAF0;
        --red: #DC3545;
        --yellow: #FFC107;
        --sc-blue: linear-gradient(135deg, #0D6EFD 0%, #0a58ca 100%);
        --sc-purple: linear-gradient(135deg, #6F42C1 0%, #59359a 100%);
        --sc-green: linear-gradient(135deg, #198754 0%, #146c43 100%);
        --sc-orange: linear-gradient(135deg, #FD7E14 0%, #dc6a0c 100%);
        --bg: #F1F3F8;
        --bg2: #EAECF3;
        --card: #FFFFFF;
        --sh-sm: 0 1px 4px rgba(0, 0, 0, .06);
        --sh-md: 0 4px 16px rgba(0, 0, 0, .08);
        --sh-blue: 0 6px 24px rgba(13, 110, 253, .3);
        --sh-purple: 0 6px 24px rgba(111, 66, 193, .25);
        --sh-green: 0 6px 24px rgba(25, 135, 84, .25);
        --r1: 10px;
        --r2: 14px;
        --r3: 18px;
        --tr: all .25s cubic-bezier(.4, 0, .2, 1);
        --tr-sp: all .35s cubic-bezier(.34, 1.3, .64, 1);
    }

    .premium-card {
        background: var(--card);
        border: 1px solid #E5E7EB;
        border-radius: var(--r3);
        transition: var(--tr);
        box-shadow: var(--sh-sm);
    }
    .premium-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--sh-md);
    }

    .sc {
        border-radius: var(--r3);
        padding: 22px 20px;
        position: relative;
        overflow: hidden;
        transition: var(--tr-sp);
        color: white;
    }
    .sc:hover { transform: translateY(-6px) scale(1.015); }
    .sc-blue { background: var(--sc-blue); box-shadow: var(--sh-blue); }
    .sc-purple { background: var(--sc-purple); box-shadow: var(--sh-purple); }
    .sc-green { background: var(--sc-green); box-shadow: var(--sh-green); }
    .sc-orange { background: var(--sc-orange); box-shadow: var(--sh-orange); }

    .sc-head { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 14px; position: relative; z-index: 1; }
    .sc-label { font-size: 12px; font-weight: 700; opacity: .88; }
    .sc-ic { width: 42px; height: 42px; border-radius: var(--r1); background: rgba(255, 255, 255, .18); display: flex; align-items: center; justify-content: center; font-size: 17px; }
    .sc-val { font-size: 32px; font-weight: 800; line-height: 1; margin-bottom: 10px; position: relative; z-index: 1; }
    .sc-footer { display: flex; align-items: center; gap: 8px; position: relative; z-index: 1; }
    .sc-delta { display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 20px; font-size: 11px; font-weight: 700; background: rgba(255, 255, 255, .18); }

    .glass-header {
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.95), rgba(147, 51, 234, 0.95));
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
</style>

<div class="content-wrapper">

            <!-- Animated Header -->
            <div class="relative overflow-hidden glass-header rounded-3xl shadow-2xl p-8 mb-10 transform hover:scale-[1.01] transition-all duration-500">
                <div class="absolute inset-0 bg-gradient-to-r from-white/10 to-transparent pointer-events-none"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between flex-wrap gap-6">
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <div class="bg-white/20 backdrop-blur-md p-4 rounded-2xl shadow-inner">
                                    <i class="fas fa-edit text-white text-3xl"></i>
                                </div>
                                <div>
                                    <h1 class="text-4xl font-black text-white tracking-tight">لوحة تحكم المحتوى</h1>
                                    <p class="text-indigo-100 text-lg font-medium opacity-90">إدارة المحتوى الرقمي والإبداعي</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <a href="<?php echo e(route('admin.blog.posts.create')); ?>" class="group bg-white text-indigo-700 px-8 py-4 rounded-2xl hover:bg-indigo-50 transition-all duration-300 shadow-xl hover:shadow-2xl hover:scale-105 flex items-center">
                                <i class="fas fa-plus ml-3 group-hover:rotate-90 transition-transform duration-500"></i>
                                <span class="font-bold text-lg">مقال جديد</span>
                            </a>
                            <a href="<?php echo e(route('admin.pages.create')); ?>" class="group bg-white/10 backdrop-blur-xl text-white px-8 py-4 rounded-2xl hover:bg-white/20 transition-all duration-300 border border-white/20 hover:border-white/40 hover:shadow-2xl flex items-center">
                                <i class="fas fa-file-alt ml-3 group-hover:scale-110 transition-transform"></i>
                                <span class="font-bold text-lg">صفحة جديدة</span>
                            </a>
                        </div>
                    </div>
                </div>
                <!-- Decorative elements -->
                <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-3xl opacity-50"></div>
                <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl opacity-50"></div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10 text-center">
                <a href="<?php echo e(route('admin.blog.posts.create')); ?>" class="group premium-card p-6">
                    <div class="bg-blue-50 text-blue-600 p-4 rounded-2xl mb-3 group-hover:scale-110 group-hover:bg-blue-600 group-hover:text-white transition-all duration-300">
                        <i class="fas fa-plus text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-1">مقال جديد</h3>
                    <p class="text-xs text-gray-500">إنشاء مقال في المدونة</p>
                </a>
                <a href="<?php echo e(route('admin.pages.create')); ?>" class="group premium-card p-6">
                    <div class="bg-purple-50 text-purple-600 p-4 rounded-2xl mb-3 group-hover:scale-110 group-hover:bg-purple-600 group-hover:text-white transition-all duration-300">
                        <i class="fas fa-file-alt text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-1">صفحة جديدة</h3>
                    <p class="text-xs text-gray-500">إضافة صفحة محتوى</p>
                </a>
                <a href="<?php echo e(route('admin.news.create')); ?>" class="group premium-card p-6">
                    <div class="bg-green-50 text-green-600 p-4 rounded-2xl mb-3 group-hover:scale-110 group-hover:bg-green-600 group-hover:text-white transition-all duration-300">
                        <i class="fas fa-newspaper text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-1">خبر جديد</h3>
                    <p class="text-xs text-gray-500">نشر تحديث جديد</p>
                </a>
                <a href="#" class="group premium-card p-6">
                    <div class="bg-yellow-50 text-yellow-600 p-4 rounded-2xl mb-3 group-hover:scale-110 group-hover:bg-yellow-600 group-hover:text-white transition-all duration-300">
                        <i class="fas fa-upload text-2xl"></i>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-1">رفع ملفات</h3>
                    <p class="text-xs text-gray-500">مكتبة الوسائط</p>
                </a>
            </div>

            <!-- Enhanced Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6 mb-10">
                <!-- Blog Posts Card -->
                <div class="sc sc-blue anim a1">
                    <div class="sc-head">
                        <div class="sc-label">إجمالي المقالات</div>
                        <div class="sc-ic"><i class="fas fa-blog fa-lg"></i></div>
                    </div>
                    <div class="sc-val"><?php echo e($stats['blog_posts'] ?? 0); ?></div>
                    <div class="sc-footer">
                        <span class="sc-delta"><i class="fas fa-check-circle"></i> <?php echo e($stats['published_posts'] ?? 0); ?> منشور</span>
                        <a href="<?php echo e(route('admin.blog.posts.index')); ?>" class="mr-auto text-white/90 hover:text-white transition-colors">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                </div>

                <!-- Pages Card -->
                <div class="sc sc-purple anim a2">
                    <div class="sc-head">
                        <div class="sc-label">الصفحات</div>
                        <div class="sc-ic"><i class="fas fa-file-alt fa-lg"></i></div>
                    </div>
                    <div class="sc-val"><?php echo e($stats['pages'] ?? 0); ?></div>
                    <div class="sc-footer">
                        <span class="sc-delta"><i class="fas fa-globe"></i> نشطة</span>
                        <a href="<?php echo e(route('admin.pages.index')); ?>" class="mr-auto text-white/90 hover:text-white transition-colors">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                </div>

                <!-- News Card -->
                <div class="sc sc-green anim a3">
                    <div class="sc-head">
                        <div class="sc-label">الأخبار</div>
                        <div class="sc-ic"><i class="fas fa-newspaper fa-lg"></i></div>
                    </div>
                    <div class="sc-val"><?php echo e($stats['news'] ?? 0); ?></div>
                    <div class="sc-footer">
                        <span class="sc-delta"><i class="fas fa-bolt"></i> جديدة</span>
                        <a href="<?php echo e(route('admin.news.index')); ?>" class="mr-auto text-white/90 hover:text-white transition-colors">
                            <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                </div>

                <!-- Guides Card -->
                <div class="sc sc-orange anim a4">
                    <div class="sc-head">
                        <div class="sc-label">الأدلة التعليمية</div>
                        <div class="sc-ic"><i class="fas fa-graduation-cap fa-lg"></i></div>
                    </div>
                    <div class="sc-val"><?php echo e($stats['guides'] ?? 0); ?></div>
                    <div class="sc-footer">
                        <span class="sc-delta"><i class="fas fa-book"></i> متوفرة</span>
                    </div>
                </div>

                <!-- Drafts Card -->
                <div class="sc sc-blue anim a5" style="filter: hue-rotate(180deg)">
                    <div class="sc-head">
                        <div class="sc-label">المسودات</div>
                        <div class="sc-ic"><i class="fas fa-pen-nib fa-lg"></i></div>
                    </div>
                    <div class="sc-val"><?php echo e($stats['draft_posts'] ?? 0); ?></div>
                    <div class="sc-footer">
                        <span class="sc-delta"><i class="fas fa-clock"></i> قيد المراجعة</span>
                    </div>
                </div>
            </div>

            <!-- Content Overview Chart -->
            <div class="premium-card p-8 mb-10 overflow-hidden relative">
                <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-bl-full opacity-50"></div>
                <div class="flex items-center justify-between mb-8 relative z-10">
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 mb-1">نظرة عامة على المحتوى</h2>
                        <p class="text-sm text-gray-500">توزيع المحتوى الرقمي عبر المنصة</p>
                    </div>
                    <div class="flex gap-2">
                        <button class="px-5 py-2 bg-indigo-600 text-white rounded-xl shadow-lg shadow-indigo-200 text-sm font-bold">هذا الشهر</button>
                        <button class="px-5 py-2 bg-gray-100 text-gray-600 rounded-xl text-sm font-bold hover:bg-gray-200 transition-colors">السنة</button>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 relative z-10">
                    <div class="space-y-3">
                        <div class="flex justify-between items-end">
                            <span class="text-sm font-bold text-gray-600">المقالات</span>
                            <span class="text-lg font-black text-blue-600"><?php echo e($stats['blog_posts'] ?? 0); ?></span>
                        </div>
                        <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full transition-all duration-1000" style="width: <?php echo e(min(($stats['blog_posts'] ?? 0) * 5, 100)); ?>%"></div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-end">
                            <span class="text-sm font-bold text-gray-600">الصفحات</span>
                            <span class="text-lg font-black text-purple-600"><?php echo e($stats['pages'] ?? 0); ?></span>
                        </div>
                        <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-purple-500 to-pink-600 rounded-full transition-all duration-1000" style="width: <?php echo e(min(($stats['pages'] ?? 0) * 5, 100)); ?>%"></div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-end">
                            <span class="text-sm font-bold text-gray-600">الأخبار</span>
                            <span class="text-lg font-black text-green-600"><?php echo e($stats['news'] ?? 0); ?></span>
                        </div>
                        <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-green-500 to-emerald-600 rounded-full transition-all duration-1000" style="width: <?php echo e(min(($stats['news'] ?? 0) * 5, 100)); ?>%"></div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex justify-between items-end">
                            <span class="text-sm font-bold text-gray-600">الأدلة</span>
                            <span class="text-lg font-black text-orange-600"><?php echo e($stats['guides'] ?? 0); ?></span>
                        </div>
                        <div class="h-3 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-orange-500 to-red-600 rounded-full transition-all duration-1000" style="width: <?php echo e(min(($stats['guides'] ?? 0) * 5, 100)); ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Content Sections -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Blog Posts -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-indigo-100">
                    <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-600 px-8 py-6">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-white flex items-center">
                                <div class="bg-white/20 backdrop-blur p-2 rounded-lg ml-3">
                                    <i class="fas fa-blog text-white"></i>
                                </div>
                                أحدث المقالات
                            </h2>
                            <div class="bg-white/20 backdrop-blur text-white px-4 py-2 rounded-lg text-sm font-medium">
                                <?php echo e($recent_posts->count() ?? 0); ?> مقال
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <?php if(($recent_posts->count() ?? 0) > 0): ?>
                            <div class="space-y-4">
                                <?php $__currentLoopData = $recent_posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="group relative bg-gradient-to-r from-indigo-50 to-purple-50 border border-indigo-200 rounded-xl p-4 hover:from-indigo-100 hover:to-purple-100 transition-all duration-300 hover:shadow-lg">
                                        <div class="absolute top-0 right-0 w-2 h-full bg-gradient-to-b from-indigo-500 to-purple-500 rounded-r-xl"></div>
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <h4 class="font-bold text-gray-900 mb-2 flex items-center">
                                                    <div class="bg-indigo-100 text-indigo-600 p-2 rounded-lg ml-3">
                                                        <i class="fas fa-file-alt text-sm"></i>
                                                    </div>
                                                    <?php echo e($post->title ?? 'Untitled'); ?>

                                                </h4>
                                                <div class="flex items-center space-x-4 space-x-reverse text-sm">
                                                    <span class="bg-gradient-to-r from-indigo-100 to-purple-100 text-indigo-700 px-3 py-1 rounded-lg text-xs font-semibold">
                                                        <?php echo e($post->status ?? 'draft'); ?>

                                                    </span>
                                                    <span class="text-gray-500 flex items-center">
                                                        <i class="fas fa-calendar ml-2 text-indigo-500"></i>
                                                        <?php echo e($post->created_at->format('Y-m-d') ?? 'Unknown'); ?>

                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex space-x-2 space-x-reverse">
                                                <a href="<?php echo e(route('admin.blog.posts.edit', $post)); ?>" class="group/btn bg-gradient-to-r from-indigo-500 to-purple-500 text-white hover:from-indigo-600 hover:to-purple-600 px-4 py-2 rounded-xl transition-all duration-300 hover:scale-105 shadow-lg">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-12">
                                <div class="bg-gradient-to-r from-indigo-100 to-purple-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-blog text-4xl text-indigo-500"></i>
                                </div>
                                <p class="text-xl font-bold text-gray-600 mb-2">لا توجد مقالات</p>
                                <p class="text-sm text-gray-500">ابدأ بكتابة أول مقال</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Recent News -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-purple-100">
                    <div class="bg-gradient-to-r from-purple-600 via-pink-600 to-purple-600 px-8 py-6">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-white flex items-center">
                                <div class="bg-white/20 backdrop-blur p-2 rounded-lg ml-3">
                                    <i class="fas fa-newspaper text-white"></i>
                                </div>
                                أحدث الأخبار
                            </h2>
                            <div class="bg-white/20 backdrop-blur text-white px-4 py-2 rounded-lg text-sm font-medium">
                                <?php echo e($recent_news->count() ?? 0); ?> خبر
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <?php if(($recent_news->count() ?? 0) > 0): ?>
                            <div class="space-y-4">
                                <?php $__currentLoopData = $recent_news; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $news): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="group relative bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 rounded-xl p-4 hover:from-purple-100 hover:to-pink-100 transition-all duration-300 hover:shadow-lg">
                                        <div class="absolute top-0 right-0 w-2 h-full bg-gradient-to-b from-purple-500 to-pink-500 rounded-r-xl"></div>
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <h4 class="font-bold text-gray-900 mb-2 flex items-center">
                                                    <div class="bg-purple-100 text-purple-600 p-2 rounded-lg ml-3">
                                                        <i class="fas fa-newspaper text-sm"></i>
                                                    </div>
                                                    <?php echo e($news->title ?? 'Untitled'); ?>

                                                </h4>
                                                <div class="flex items-center space-x-4 space-x-reverse text-sm">
                                                    <span class="bg-gradient-to-r from-purple-100 to-pink-100 text-purple-700 px-3 py-1 rounded-lg text-xs font-semibold">
                                                        <?php echo e($news->status ?? 'draft'); ?>

                                                    </span>
                                                    <span class="text-gray-500 flex items-center">
                                                        <i class="fas fa-calendar ml-2 text-purple-500"></i>
                                                        <?php echo e($news->created_at->format('Y-m-d') ?? 'Unknown'); ?>

                                                    </span>
                                                </div>
                                            </div>
                                            <div class="flex space-x-2 space-x-reverse">
                                                <a href="<?php echo e(route('admin.news.edit', $news)); ?>" class="group/btn bg-gradient-to-r from-purple-500 to-pink-500 text-white hover:from-purple-600 hover:to-pink-600 px-4 py-2 rounded-xl transition-all duration-300 hover:scale-105 shadow-lg">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-12">
                                <div class="bg-gradient-to-r from-purple-100 to-pink-100 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-newspaper text-4xl text-purple-500"></i>
                                </div>
                                <p class="text-xl font-bold text-gray-600 mb-2">لا توجد أخبار</p>
                                <p class="text-sm text-gray-500">ابدأ بنشر أول خبر</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Additional Tools Section -->
            <div class="premium-card p-8 mt-10">
                <h2 class="text-2xl font-black text-gray-900 mb-8 flex items-center">
                    <div class="bg-indigo-600 text-white p-3 rounded-2xl ml-4 shadow-lg shadow-indigo-100">
                        <i class="fas fa-tools"></i>
                    </div>
                    أدوات إضافية
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 text-right">
                    <a href="#" class="group border border-gray-100 rounded-2xl p-6 hover:bg-indigo-50 hover:border-indigo-200 transition-all duration-300">
                        <div class="bg-indigo-50 text-indigo-600 p-3 rounded-xl mb-4 group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300 w-fit">
                            <i class="fas fa-images text-xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-1">مكتبة الوسائط</h3>
                        <p class="text-xs text-gray-500">إدارة الصور والفيديوهات</p>
                    </a>

                    <a href="#" class="group border border-gray-100 rounded-2xl p-6 hover:bg-green-50 hover:border-green-200 transition-all duration-300">
                        <div class="bg-green-50 text-green-600 p-3 rounded-xl mb-4 group-hover:scale-110 group-hover:bg-green-600 group-hover:text-white transition-all duration-300 w-fit">
                            <i class="fas fa-search text-xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-1">تحسين المحركات</h3>
                        <p class="text-xs text-gray-500">SEO Tools</p>
                    </a>

                    <a href="#" class="group border border-gray-100 rounded-2xl p-6 hover:bg-purple-50 hover:border-purple-200 transition-all duration-300">
                        <div class="bg-purple-50 text-purple-600 p-3 rounded-xl mb-4 group-hover:scale-110 group-hover:bg-purple-600 group-hover:text-white transition-all duration-300 w-fit">
                            <i class="fas fa-tags text-xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-1">إدارة التصنيفات</h3>
                        <p class="text-xs text-gray-500">تنظيم المحتوى</p>
                    </a>

                    <a href="#" class="group border border-gray-100 rounded-2xl p-6 hover:bg-orange-50 hover:border-orange-200 transition-all duration-300">
                        <div class="bg-orange-50 text-orange-600 p-3 rounded-xl mb-4 group-hover:scale-110 group-hover:bg-orange-600 group-hover:text-white transition-all duration-300 w-fit">
                            <i class="fas fa-cog text-xl"></i>
                        </div>
                        <h3 class="font-bold text-gray-900 mb-1">الإعدادات</h3>
                        <p class="text-xs text-gray-500">تكوين النظام</p>
                    </a>
                </div>
            </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\larvel state big\resources\views/content/dashboard.blade.php ENDPATH**/ ?>