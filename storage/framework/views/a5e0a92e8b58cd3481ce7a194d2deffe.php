<?php $__env->startSection('title', 'لوحة العملاء المحتملين'); ?>

<?php $__env->startPush('styles'); ?>
<style>
/* COPIED STYLES FROM ADMIN DASHBOARD TO ENSURE CONSISTENCY */
:root {
  --blue:      #0D6EFD;
  --blue2:     #0B5ED7;
  --blue-g:    linear-gradient(135deg, #0D6EFD, #0a58ca);
  --purple:    #6F42C1;
  --purple-g:  linear-gradient(135deg, #6F42C1, #59359a);
  --green:     #198754;
  --green-g:   linear-gradient(135deg, #198754, #146c43);
  --orange:    #FD7E14;
  --orange-g:  linear-gradient(135deg, #FD7E14, #dc6a0c);
  --teal:      #0DCAF0;
  --red:       #DC3545;
  --yellow:    #FFC107;
  --sc-blue:   linear-gradient(135deg, #0D6EFD 0%, #0a58ca 100%);
  --sc-purple: linear-gradient(135deg, #6F42C1 0%, #59359a 100%);
  --sc-green:  linear-gradient(135deg, #198754 0%, #146c43 100%);
  --sc-orange: linear-gradient(135deg, #FD7E14 0%, #dc6a0c 100%);
  --bg:    #F1F3F8;
  --bg2:   #EAECF3;
  --card:  #FFFFFF;
  --card2: #F8F9FC;
  --t1: #1A1F36; --t2: #374151; --t3: #6B7280; --t4: #9CA3AF;
  --bd: #E5E7EB; --bd2: #D1D5DB; --bd-blue: rgba(13,110,253,.25);
  --sh-sm:  0 1px 4px rgba(0,0,0,.06), 0 2px 8px rgba(0,0,0,.04);
  --sh-md:  0 4px 16px rgba(0,0,0,.08), 0 2px 6px rgba(0,0,0,.05);
  --sh-lg:  0 8px 32px rgba(0,0,0,.1);
  --sh-blue:   0 6px 24px rgba(13,110,253,.3);
  --sh-purple: 0 6px 24px rgba(111,66,193,.25);
  --sh-green:  0 6px 24px rgba(25,135,84,.25);
  --sh-orange: 0 6px 24px rgba(253,126,20,.25);
  --r1:10px; --r2:14px; --r3:18px; --r4:24px;
  --tr:    all .25s cubic-bezier(.4,0,.2,1);
  --tr-sp: all .35s cubic-bezier(.34,1.3,.64,1);
  --ff: 'Cairo','Plus Jakarta Sans',system-ui,sans-serif;
}

/* STAT CARDS */
.stat-row { display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:16px; margin-bottom:22px; }

.sc {
  border-radius:var(--r3); padding:22px 20px;
  position:relative; overflow:hidden;
  transition:var(--tr-sp); color:white;
}
.sc:hover { transform:translateY(-6px) scale(1.015); }
.sc-blue   { background:var(--sc-blue);   box-shadow:var(--sh-blue); }
.sc-purple { background:var(--sc-purple); box-shadow:var(--sh-purple); }
.sc-green  { background:var(--sc-green);  box-shadow:var(--sh-green); }
.sc-orange { background:var(--sc-orange); box-shadow:var(--sh-orange); }
.sc:hover.sc-blue   { box-shadow:0 16px 40px rgba(13,110,253,.4); }
.sc:hover.sc-purple { box-shadow:0 16px 40px rgba(111,66,193,.35); }
.sc:hover.sc-green  { box-shadow:0 16px 40px rgba(25,135,84,.35); }
.sc:hover.sc-orange { box-shadow:0 16px 40px rgba(253,126,20,.35); }

.sc::before {
  content:''; position:absolute;
  top:-30px; left:-30px; width:110px; height:110px;
  border-radius:50%; background:rgba(255,255,255,.08);
  pointer-events:none;
}
.sc::after {
  content:''; position:absolute;
  bottom:-20px; right:-20px; width:80px; height:80px;
  border-radius:50%; background:rgba(255,255,255,.06);
  pointer-events:none;
}

.sc-head { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:14px; position:relative; z-index:1; }
.sc-label { font-size:12px; font-weight:700; opacity:.88; }
.sc-ic {
  width:42px; height:42px; border-radius:var(--r1);
  background:rgba(255,255,255,.18); border:1px solid rgba(255,255,255,.25);
  display:flex; align-items:center; justify-content:center; font-size:17px;
  transition:var(--tr-sp); flex-shrink:0;
}
.sc:hover .sc-ic { transform:scale(1.12) rotate(8deg); }
.sc-val { font-size:clamp(26px,3vw,38px); font-weight:800; line-height:1; margin-bottom:10px; position:relative; z-index:1; }
.sc-footer { display:flex; align-items:center; gap:8px; position:relative; z-index:1; }
.sc-delta {
  display:inline-flex; align-items:center; gap:4px;
  padding:3px 8px; border-radius:20px; font-size:11px; font-weight:700;
  background:rgba(255,255,255,.18); border:1px solid rgba(255,255,255,.22);
}
.sc-ftext { font-size:11.5px; opacity:.8; }

/* PANELS */
.panel { background:var(--card); border:1px solid var(--bd); border-radius:var(--r3); overflow:hidden; box-shadow:var(--sh-sm); transition:var(--tr); margin-bottom: 22px; }
.panel:hover { box-shadow:var(--sh-md); }
.panel-head { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--bd); }
.panel-title { font-size:14.5px; font-weight:800; color:var(--t1); }
.panel-sub { font-size:11px; color:var(--t4); margin-top:2px; }
.panel-body { padding:16px 20px; }

/* ACTIVITY / LIST ITEMS */
.act-item { display:flex; align-items:center; gap:12px; padding:11px 8px; border-bottom:1px solid var(--bd); transition:background .18s; text-decoration: none; }
.act-item:last-child { border-bottom:none; }
.act-item:hover { background:var(--bg); border-radius:var(--r1); }
.act-ic { width:32px; height:32px; border-radius:9px; background:rgba(13,110,253,.1); color:var(--blue); display:flex; align-items:center; justify-content:center; font-size:12px; flex-shrink:0; }
.act-msg { font-size:12.5px; font-weight:700; color:var(--t1); line-height:1.4; }
.act-time { font-size:11px; color:var(--t4); margin-top:3px; }

/* SYSTEM ROW (for lists) */
.sys-row { display:flex; align-items:center; justify-content:space-between; padding:9px 11px; border-radius:var(--r1); border:1px solid var(--bd); margin-bottom:6px; transition:var(--tr); }
.sys-row:last-child { margin-bottom:0; }
.sys-row:hover { border-color:var(--bd-blue); background:rgba(13,110,253,.02); }
.sys-left { display:flex; align-items:center; gap:8px; }
.sys-dot  { width:7px; height:7px; border-radius:50%; }
.sys-name { font-size:12px; font-weight:600; color:var(--t2); }
.sys-tag { font-size:10.5px; font-weight:700; padding:3px 10px; border-radius:20px; display:flex; align-items:center; gap:5px; }

/* BUTTONS */
.tbtn {
  display:inline-flex; align-items:center; gap:7px;
  padding:7px 17px; border-radius:var(--r1);
  font-size:12.5px; font-weight:700;
  cursor:pointer; border:none; font-family:var(--ff);
  text-decoration:none; transition:var(--tr);
}
.tbtn-primary { background:var(--blue-g); color:white; box-shadow:var(--sh-blue); }
.tbtn-primary:hover { transform:translateY(-2px); box-shadow:0 10px 28px rgba(13,110,253,.35); color: white; }

/* GRID HELPERS */
.mid-row { display:grid; grid-template-columns:repeat(auto-fit, minmax(300px, 1fr)); gap:16px; margin-bottom:18px; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="hover:text-blue-600 transition-colors">
                    <i class="fas fa-home"></i>
                </a>
                <i class="fas fa-chevron-left text-xs"></i>
                <span>لوحة العملاء المحتملين</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900" style="font-family: var(--ff);">
                لوحة العملاء <span class="text-blue-600">المحتملين</span>
            </h1>
            <p class="text-gray-600 mt-2">متابعة وإدارة العملاء المحتملين – من التواصل الأول حتى التحويل إلى عميل فعلي</p>
        </div>
        
        <a href="<?php echo e(route('leads.create')); ?>" class="tbtn tbtn-primary">
            <i class="fas fa-plus"></i>
            <span>إضافة عميل محتمل</span>
        </a>
    </div>

    <!-- Stats Grid -->
    <div class="stat-row">
        <!-- Total Leads -->
        <div class="sc sc-orange">
            <div class="sc-head">
                <span class="sc-label">إجمالي العملاء</span>
                <div class="sc-ic"><i class="fas fa-users"></i></div>
            </div>
            <div class="sc-val"><?php echo e($stats['total_leads'] ?? 0); ?></div>
            <div class="sc-footer">
                <span class="sc-delta">إجمالي</span>
                <span class="sc-ftext">في قاعدة البيانات</span>
            </div>
        </div>

        <!-- New Leads -->
        <div class="sc sc-blue">
            <div class="sc-head">
                <span class="sc-label">عملاء جدد</span>
                <div class="sc-ic"><i class="fas fa-user-plus"></i></div>
            </div>
            <div class="sc-val"><?php echo e($stats['new_leads'] ?? 0); ?></div>
            <div class="sc-footer">
                <span class="sc-delta">جديد</span>
                <span class="sc-ftext">هذا الشهر</span>
            </div>
        </div>

        <!-- Qualified Leads -->
        <div class="sc sc-green">
            <div class="sc-head">
                <span class="sc-label">عملاء مؤهلين</span>
                <div class="sc-ic"><i class="fas fa-check-circle"></i></div>
            </div>
            <div class="sc-val"><?php echo e($stats['qualified_leads'] ?? 0); ?></div>
            <div class="sc-footer">
                <span class="sc-delta">مؤهل</span>
                <span class="sc-ftext">للمتابعة</span>
            </div>
        </div>

        <!-- Converted Leads -->
        <div class="sc sc-purple">
            <div class="sc-head">
                <span class="sc-label">عملاء محولين</span>
                <div class="sc-ic"><i class="fas fa-chart-line"></i></div>
            </div>
            <div class="sc-val"><?php echo e($stats['converted_leads'] ?? 0); ?></div>
            <div class="sc-footer">
                <span class="sc-delta">تحويل</span>
                <span class="sc-ftext">عملاء فعليين</span>
            </div>
        </div>
    </div>

    <!-- Conversion Rate -->
    <div class="panel">
        <div class="panel-body">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6 mb-4">
                <div>
                    <h3 class="text-xl font-bold text-gray-900" style="font-family: var(--ff);">معدل التحويل</h3>
                    <p class="text-sm text-gray-500 mt-1">نسبة العملاء المحتملين الذين أصبحوا عملاء فعليين</p>
                </div>
                <div class="text-center sm:text-right">
                    <p class="text-4xl font-extrabold text-blue-600"><?php echo e(number_format($stats['conversion_rate'] ?? 0, 1)); ?>%</p>
                    <p class="text-sm text-gray-400 mt-1">المعدل الحالي</p>
                </div>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-3 rounded-full transition-all duration-1000 ease-out" 
                     style="width: <?php echo e(min($stats['conversion_rate'] ?? 0, 100)); ?>%"></div>
            </div>
        </div>
    </div>

    <div class="mid-row">
        <!-- Lead Sources -->
        <div class="panel">
            <div class="panel-head">
                <h3 class="panel-title">مصادر العملاء</h3>
            </div>
            <div class="panel-body">
                <?php $__empty_1 = true; $__currentLoopData = $leadSources; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $source): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="sys-row">
                        <div class="sys-left">
                            <div class="sys-dot bg-blue-500"></div>
                            <span class="sys-name"><?php echo e($source->name); ?></span>
                        </div>
                        <span class="sys-tag" style="background:var(--bg2); color:var(--t2);"><?php echo e($source->leads_count); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-8">
                        <div class="bg-gray-50 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-300">
                            <i class="fas fa-chart-pie text-xl"></i>
                        </div>
                        <p class="text-gray-500 text-sm">لا توجد بيانات مصادر</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Lead Statuses -->
        <div class="panel">
            <div class="panel-head">
                <h3 class="panel-title">حالات العملاء</h3>
            </div>
            <div class="panel-body">
                <?php $__empty_1 = true; $__currentLoopData = $leadStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="sys-row">
                        <div class="sys-left">
                            <div class="sys-dot" style="background-color: <?php echo e($status->color ?? '#cbd5e1'); ?>"></div>
                            <span class="sys-name"><?php echo e($status->name); ?></span>
                        </div>
                        <span class="sys-tag" style="background:var(--bg2); color:var(--t2);"><?php echo e($status->leads_count); ?></span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-8">
                        <div class="bg-gray-50 w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-3 text-gray-300">
                            <i class="fas fa-list-check text-xl"></i>
                        </div>
                        <p class="text-gray-500 text-sm">لا توجد بيانات حالات</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Recent Leads -->
    <div class="panel">
        <div class="panel-head">
            <h3 class="panel-title">آخر العملاء المحتملين</h3>
            <span class="sys-tag st-ok"><?php echo e($recentLeads->count()); ?> عميل</span>
        </div>
        <div class="divide-y divide-gray-100">
            <?php $__empty_1 = true; $__currentLoopData = $recentLeads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lead): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e(route('leads.show', $lead)); ?>" class="act-item group">
                    <div class="act-ic group-hover:bg-blue-600 group-hover:text-white transition-colors">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="act-msg"><?php echo e($lead->first_name); ?> <?php echo e($lead->last_name); ?></h4>
                            <span class="act-time"><?php echo e($lead->created_at->diffForHumans()); ?></span>
                        </div>
                        <div class="flex items-center justify-between mt-1">
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <span><i class="fas fa-envelope ml-1"></i><?php echo e($lead->email); ?></span>
                                <span class="text-gray-300">|</span>
                                <span><i class="fas fa-phone ml-1"></i><?php echo e($lead->phone ?? '—'); ?></span>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold" 
                                style="background-color: <?php echo e($lead->status?->color ?? '#6c757d'); ?>15; color: <?php echo e($lead->status?->color ?? '#6c757d'); ?>;">
                                <?php echo e($lead->status?->name ?? 'غير محدد'); ?>

                            </span>
                        </div>
                    </div>
                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-12">
                    <div class="bg-gray-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                        <i class="fas fa-user-slash text-2xl"></i>
                    </div>
                    <p class="text-gray-500">لا يوجد عملاء محتملين حديثاً</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\larvel state big\resources\views/leads/dashboard.blade.php ENDPATH**/ ?>