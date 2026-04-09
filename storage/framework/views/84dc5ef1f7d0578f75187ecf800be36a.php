

<?php $__env->startSection('title', 'الإشعارات'); ?>

<?php $__env->startPush('styles'); ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
<style>
/* 
   COLORFUL VARIABLES & BASE
 */
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

.notif-wrap {
    font-family: 'Tajawal', sans-serif;
    background: linear-gradient(135deg, #F1F3F8 0%, #E8EEF7 100%) !important;
    min-height: 100vh;
    padding: 130px 1rem 5rem;
    direction: rtl;
}

.notif-inner {
    max-width: 1200px;
    margin: 0 auto;
}

/* 
   COLORFUL HEADER
 */
.n-header {
    background: linear-gradient(135deg, rgba(79, 70, 229, 0.95), rgba(147, 51, 234, 0.95)) !important;
    backdrop-filter: blur(10px) !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    border-radius: 18px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    box-shadow: 0 8px 32px rgba(79, 70, 229, 0.3);
    transition: all 0.3s ease;
}

.n-header:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 40px rgba(79, 70, 229, 0.4);
}

.n-header-text h1 {
    font-size: 2rem;
    font-weight: 800;
    color: white;
    letter-spacing: -.025em;
    line-height: 1.2;
    margin-bottom: .3rem;
    text-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.n-header-text p {
    font-size: .9rem;
    color: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    gap: .35rem;
}

.n-header-text p i {
    color: rgba(255, 255, 255, 0.8);
    font-size: .6rem;
}

.n-mark-all-btn {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    height: 44px;
    padding: 0 1.5rem;
    border-radius: 12px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    color: white;
    font-family: 'Tajawal', sans-serif;
    font-size: .85rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
    text-decoration: none;
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
}
.n-mark-all-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.2);
}

/* ══════════════════════════════════════════
   STATS
══════════════════════════════════════════ */
.n-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: .6rem;
    margin-bottom: 1.5rem;
}

.n-stat {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--r);
    padding: 1rem 1.1rem;
    display: flex;
    align-items: center;
    gap: .8rem;
    transition: box-shadow var(--t), transform var(--t);
}
.n-stat:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,.07);
    transform: translateY(-1px);
}

.n-stat-ico {
    width: 38px;
    height: 38px;
    flex-shrink: 0;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .82rem;
}
.sico-b { background: var(--blue-s);  color: var(--blue);  }
.sico-a { background: var(--amber-s); color: var(--amber); }
.sico-g { background: var(--green-s); color: var(--green); }

.n-stat-val {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--ink);
    line-height: 1;
    letter-spacing: -.02em;
}
.n-stat-lbl {
    font-size: .68rem;
    color: var(--ink3);
    margin-top: .12rem;
    font-weight: 500;
}

.n-last-update {
    font-size: .78rem;
    color: var(--ink3);
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: 8px;
    padding: .5rem .85rem;
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    margin-bottom: 1.5rem;
    float: left;
}
.n-last-update i { color: var(--blue); font-size: .7rem; }

/* ══════════════════════════════════════════
   NOTIFICATION ITEM
══════════════════════════════════════════ */
.n-list {
    display: flex;
    flex-direction: column;
    gap: .4rem;
    clear: both;
}

.notification-item {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--r);
    padding: 1rem 1.15rem;
    display: flex;
    align-items: flex-start;
    gap: .9rem;
    transition: all var(--t);
    position: relative;
    /* reveal animation */
    animation: fadeSlideIn .4s ease both;
}
.notification-item:nth-child(1)  { animation-delay: .03s; }
.notification-item:nth-child(2)  { animation-delay: .07s; }
.notification-item:nth-child(3)  { animation-delay: .11s; }
.notification-item:nth-child(4)  { animation-delay: .15s; }
.notification-item:nth-child(5)  { animation-delay: .19s; }
.notification-item:nth-child(6)  { animation-delay: .23s; }
.notification-item:nth-child(7)  { animation-delay: .27s; }
.notification-item:nth-child(8)  { animation-delay: .31s; }
.notification-item:nth-child(9)  { animation-delay: .35s; }
.notification-item:nth-child(10) { animation-delay: .39s; }

@keyframes fadeSlideIn {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: none; }
}

.notification-item:hover {
    box-shadow: 0 5px 20px rgba(0,0,0,.08);
    transform: translateY(-1px);
    border-color: #D4D4D2;
    z-index: 1;
}

.notification-item.unread {
    border-right: 3px solid var(--blue);
    background: #FAFCFF;
}
.notification-item.unread:hover { background: #F3F8FF; }

/* Icon */
.n-ico {
    width: 40px;
    height: 40px;
    flex-shrink: 0;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .85rem;
    margin-top: .05rem;
}
.n-ico.unread { background: var(--blue-s);  color: var(--blue); }
.n-ico.read   { background: var(--bg);      color: var(--ink3); }

/* Content */
.n-content { flex: 1; min-width: 0; }

.n-title-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: .5rem;
    margin-bottom: .25rem;
}

.n-title {
    font-size: .9rem;
    font-weight: 700;
    color: var(--ink);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.notification-item.unread .n-title { color: #0B1835; }

.n-time-badge {
    font-size: .7rem;
    color: var(--ink3);
    white-space: nowrap;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    gap: .25rem;
}

.n-unread-dot {
    display: inline-block;
    width: 6px;
    height: 6px;
    background: var(--blue);
    border-radius: 50%;
    margin-left: .35rem;
    vertical-align: middle;
    flex-shrink: 0;
}

.n-msg {
    font-size: .82rem;
    color: var(--ink3);
    line-height: 1.65;
    margin-bottom: 0;
}
.notification-item.unread .n-msg { color: var(--ink2); }

.n-action-link {
    display: inline-flex;
    align-items: center;
    gap: .3rem;
    font-size: .75rem;
    font-weight: 700;
    color: var(--blue);
    margin-top: .5rem;
    text-decoration: none;
    transition: gap var(--t);
}
.n-action-link:hover { gap: .55rem; }

.n-read-at {
    font-size: .7rem;
    color: var(--green);
    display: flex;
    align-items: center;
    gap: .25rem;
    margin-top: .35rem;
}

/* Right side: time + mark btn */
.n-right {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: .5rem;
    flex-shrink: 0;
}

.n-mark-btn {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    height: 32px;
    padding: 0 .85rem;
    border-radius: 8px;
    border: 1.5px solid var(--border);
    background: transparent;
    color: var(--ink3);
    font-family: 'Tajawal', sans-serif;
    font-size: .72rem;
    font-weight: 600;
    cursor: pointer;
    transition: all var(--t);
    white-space: nowrap;
}
.n-mark-btn:hover {
    background: var(--green-s);
    border-color: var(--green);
    color: var(--green);
}

/* ══════════════════════════════════════════
   EMPTY STATE
══════════════════════════════════════════ */
.n-empty {
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--r);
    padding: 5rem 2rem;
    text-align: center;
}
.n-empty-ico {
    width: 68px;
    height: 68px;
    background: var(--bg);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.1rem;
    font-size: 1.5rem;
    color: var(--ink3);
}
.n-empty h3 {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--ink);
    margin-bottom: .4rem;
}
.n-empty p {
    font-size: .85rem;
    color: var(--ink3);
    line-height: 1.7;
    max-width: 360px;
    margin: 0 auto 1.5rem;
}
.n-empty-btns {
    display: flex;
    justify-content: center;
    gap: .6rem;
    flex-wrap: wrap;
}
.n-ebtn {
    display: inline-flex;
    align-items: center;
    gap: .4rem;
    height: 40px;
    padding: 0 1.1rem;
    border-radius: 9px;
    font-family: 'Tajawal', sans-serif;
    font-size: .82rem;
    font-weight: 600;
    text-decoration: none;
    transition: all var(--t);
}
.n-ebtn-dark  { background: var(--ink);    color: #fff;        border: 1.5px solid var(--ink); }
.n-ebtn-light { background: var(--white);  color: var(--ink2); border: 1.5px solid var(--border); }
.n-ebtn-dark:hover  { background: var(--blue); border-color: var(--blue); color: #fff; transform: translateY(-1px); }
.n-ebtn-light:hover { background: var(--bg); transform: translateY(-1px); }

/* ══════════════════════════════════════════
   PAGINATION WRAPPER
══════════════════════════════════════════ */
.n-pagination {
    margin-top: 2rem;
    background: var(--white);
    border: 1px solid var(--border);
    border-radius: var(--r);
    padding: 1.1rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    flex-wrap: wrap;
}
.n-pag-info {
    font-size: .78rem;
    color: var(--ink3);
    font-family: 'Tajawal', sans-serif;
}
.n-pag-info strong { color: var(--ink2); font-weight: 700; }

/* Tailwind pagination override */
.n-pagination nav {
    display: flex;
    align-items: center;
    gap: .3rem;
}
.n-pagination nav span,
.n-pagination nav a {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 34px;
    height: 34px;
    padding: 0 .5rem;
    border-radius: 8px;
    border: 1.5px solid var(--border);
    background: var(--white);
    color: var(--ink2);
    font-family: 'Tajawal', sans-serif;
    font-size: .8rem;
    font-weight: 600;
    text-decoration: none;
    transition: all var(--t);
}
.n-pagination nav a:hover {
    background: var(--ink);
    color: #fff;
    border-color: var(--ink);
}
.n-pagination nav span[aria-current="page"] {
    background: var(--ink);
    color: #fff;
    border-color: var(--ink);
}
.n-pagination nav span[aria-disabled="true"],
.n-pagination nav span.disabled {
    opacity: .35;
    cursor: default;
    pointer-events: none;
}

/* ══════════════════════════════════════════
   TOAST
══════════════════════════════════════════ */
.n-toast {
    position: fixed;
    bottom: 2rem;
    left: 50%;
    transform: translateX(-50%) translateY(80px);
    background: var(--ink);
    color: #fff;
    padding: .65rem 1.6rem;
    border-radius: 40px;
    font-size: .82rem;
    font-weight: 600;
    font-family: 'Tajawal', sans-serif;
    box-shadow: 0 8px 32px rgba(0,0,0,.18);
    z-index: 9999;
    transition: transform .35s cubic-bezier(.4,0,.2,1);
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: .45rem;
}
.n-toast.show { transform: translateX(-50%) translateY(0); }

/* ══════════════════════════════════════════
   RESPONSIVE
══════════════════════════════════════════ */
@media (max-width: 600px) {
    .notif-wrap   { padding: 1.5rem .9rem 4rem; }
    .n-header     { flex-direction: column; gap: .75rem; }
    .n-stats      { gap: .45rem; }
    .n-stat       { padding: .85rem .9rem; gap: .65rem; }
    .n-stat-val   { font-size: 1.2rem; }
    .n-pagination { flex-direction: column; align-items: flex-start; }
    .n-right      { display: none; }
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="notif-wrap">
<div class="notif-inner">

    
    <div class="n-header">
        <div class="n-header-text">
            <h1>الإشعارات</h1>
            <p><i class="fas fa-circle"></i> آخر تحديث: <?php echo e(now()->format('H:i')); ?></p>
        </div>

        <?php if($notifications->count() > 0): ?>
        <form action="<?php echo e(route('user.notifications.read.all')); ?>" method="POST" style="display:inline">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <button type="submit" class="n-mark-all-btn">
                <i class="fas fa-check-double"></i>
                تعيين الكل كمقروء
            </button>
        </form>
        <?php endif; ?>
    </div>

    
    <div class="n-stats">
        <div class="n-stat">
            <div class="n-stat-ico sico-b"><i class="fas fa-inbox"></i></div>
            <div>
                <div class="n-stat-val"><?php echo e($notifications->total()); ?></div>
                <div class="n-stat-lbl">الإجمالي</div>
            </div>
        </div>
        <div class="n-stat">
            <div class="n-stat-ico sico-a"><i class="fas fa-envelope"></i></div>
            <div>
                <div class="n-stat-val"><?php echo e($unreadCount); ?></div>
                <div class="n-stat-lbl">غير مقروء</div>
            </div>
        </div>
        <div class="n-stat">
            <div class="n-stat-ico sico-g"><i class="fas fa-envelope-open"></i></div>
            <div>
                <div class="n-stat-val"><?php echo e($notifications->total() - $unreadCount); ?></div>
                <div class="n-stat-lbl">مقروء</div>
            </div>
        </div>
    </div>

    
    <?php if($notifications->count() > 0): ?>

        <div class="n-list">
            <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <?php
                $isUnread   = !$notification->read_at;
                $data       = is_array($notification->data) ? $notification->data : [];
                $title      = $data['title']   ?? (is_string($notification->data) ? $notification->data : 'إشعار');
                $message    = $data['message'] ?? $data['body'] ?? (is_string($notification->data) ? $notification->data : 'لا يوجد محتوى');
                $actionUrl  = $data['action_url']  ?? null;
                $actionText = $data['action_text'] ?? 'عرض التفاصيل';
            ?>

            <div class="notification-item <?php echo e($isUnread ? 'unread' : 'read'); ?>">

                
                <div class="n-ico <?php echo e($isUnread ? 'unread' : 'read'); ?>">
                    <i class="fas <?php echo e($isUnread ? 'fa-bell' : 'fa-envelope-open'); ?>"></i>
                </div>

                
                <div class="n-content">
                    <div class="n-title-row">
                        <span class="n-title">
                            <?php if($isUnread): ?><span class="n-unread-dot"></span><?php endif; ?>
                            <?php echo e($title); ?>

                        </span>
                        <span class="n-time-badge">
                            <i class="fas fa-clock"></i>
                            <?php echo e($notification->created_at->diffForHumans()); ?>

                        </span>
                    </div>

                    <p class="n-msg"><?php echo e($message); ?></p>

                    <?php if($actionUrl): ?>
                        <a href="<?php echo e($actionUrl); ?>" class="n-action-link">
                            <?php echo e($actionText); ?> <i class="fas fa-arrow-left"></i>
                        </a>
                    <?php endif; ?>

                    <?php if($notification->read_at): ?>
                        <div class="n-read-at">
                            <i class="fas fa-check-circle"></i>
                            تم القراءة <?php echo e($notification->read_at->diffForHumans()); ?>

                        </div>
                    <?php endif; ?>
                </div>

                
                <?php if($isUnread): ?>
                <div class="n-right">
                    <form action="<?php echo e(route('user.notifications.read', $notification->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        <button type="submit" class="n-mark-btn">
                            <i class="fas fa-check"></i> تأشير كمقروء
                        </button>
                    </form>
                </div>
                <?php endif; ?>

            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <div class="n-pagination">
            <span class="n-pag-info">
                عرض <strong><?php echo e($notifications->firstItem()); ?>–<?php echo e($notifications->lastItem()); ?></strong>
                من <strong><?php echo e($notifications->total()); ?></strong> إشعار
            </span>
            <?php echo e($notifications->links('pagination::tailwind')); ?>

        </div>

    <?php else: ?>

        
        <div class="n-empty">
            <div class="n-empty-ico"><i class="fas fa-bell-slash"></i></div>
            <h3>لا توجد إشعارات</h3>
            <p>جميع إشعاراتك ستظهر هنا. ستصلك إشعارات عند حدوث أي نشاط مهم يتعلق بحسابك.</p>
            <div class="n-empty-btns">
                <a href="<?php echo e(url('/dashboard')); ?>" class="n-ebtn n-ebtn-dark">
                    <i class="fas fa-home"></i> لوحة التحكم
                </a>
                <a href="<?php echo e(url('/profile')); ?>" class="n-ebtn n-ebtn-light">
                    <i class="fas fa-user"></i> الملف الشخصي
                </a>
            </div>
        </div>

    <?php endif; ?>

</div>
</div>


<div class="n-toast" id="n-toast">
    <i class="fas fa-check-circle" style="color:#0A9E6E"></i>
    <span id="n-toast-msg">تم</span>
</div>

<script>
// Auto-refresh notifications count every 30 seconds
setInterval(() => {
    fetch('<?php echo e(route("user.notifications.count")); ?>')
        .then(response => response.json())
        .then(data => {
            if (data.count !== undefined) {
                document.querySelector('.n-stat:nth-child(2) .n-stat-val').textContent = data.count;
            }
        })
        .catch(error => console.error('Error fetching notification count:', error));
}, 30000);

// Toast helper (optional use)
function showToast(msg) {
    const t = document.getElementById('n-toast');
    document.getElementById('n-toast-msg').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2800);
}

// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href'))?.scrollIntoView({ behavior: 'smooth' });
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\larvel state big\resources\views/user/notifications.blade.php ENDPATH**/ ?>