<?php $__env->startSection('title', 'لوحة تحكم الوحدات'); ?>
<?php $__env->startSection('page-title', 'إدارة الوحدات'); ?>

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
    
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
    }
    .status-active { background: #f0fdf4; color: #16a34a; border: 1px solid #dcfce7; }
    .status-inactive { background: #fef2f2; color: #dc2626; border: 1px solid #fee2e2; }

    .mod-btn {
        padding: 10px 16px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .mod-btn-primary { background: var(--sc-blue); color: white !important; }
    .mod-btn-secondary { background: #f8fafc; color: #475569 !important; border: 1px solid #e2e8f0; }
    .mod-btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
</style>

<div class="content-wrapper">
    <div class="w-full px-4 md:px-6 lg:px-8">
        
        <!-- Animated Header -->
        <div class="relative overflow-hidden glass-header rounded-3xl shadow-2xl p-8 mb-10 transform hover:scale-[1.01] transition-all duration-500">
            <div class="absolute inset-0 bg-gradient-to-r from-white/10 to-transparent pointer-events-none"></div>
            <div class="relative z-10">
                <div class="flex items-center justify-between flex-wrap gap-6 text-right">
                    <div class="space-y-3">
                        <div class="flex items-center space-x-reverse space-x-3">
                            <div class="bg-white/20 backdrop-blur-md p-4 rounded-2xl shadow-inner">
                                <i class="fas fa-cubes text-white text-3xl"></i>
                            </div>
                            <div>
                                <h1 class="text-4xl font-black text-white tracking-tight">لوحة تحكم الوحدات</h1>
                                <p class="text-indigo-100 text-lg font-medium opacity-90">إدارة وتفعيل الوحدات النظامية بأسلوب احترافي</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <button onclick="loadModules()" class="group bg-white text-indigo-700 px-8 py-4 rounded-2xl hover:bg-indigo-50 transition-all duration-300 shadow-xl hover:shadow-2xl hover:scale-105 flex items-center">
                            <i class="fas fa-sync-alt ml-3 group-hover:rotate-180 transition-transform duration-700"></i>
                            <span class="font-bold text-lg">تحديث الوحدات</span>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Decorative elements -->
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/10 rounded-full blur-3xl opacity-50"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-purple-500/20 rounded-full blur-3xl opacity-50"></div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10 text-right">
            <div class="sc sc-blue anim a1">
                <div class="sc-head">
                    <div class="sc-label">الوحدات النشطة</div>
                    <div class="sc-ic"><i class="fas fa-check-circle fa-lg"></i></div>
                </div>
                <div class="sc-val">6</div>
                <div class="sc-footer">
                    <span class="sc-delta"><i class="fas fa-box"></i> Modules</span>
                </div>
            </div>

            <div class="sc sc-green anim a2">
                <div class="sc-head">
                    <div class="sc-label">متوسط الإصدار</div>
                    <div class="sc-ic"><i class="fas fa-code-branch fa-lg"></i></div>
                </div>
                <div class="sc-val">2.0</div>
                <div class="sc-footer">
                    <span class="sc-delta"><i class="fas fa-tag"></i> Stable</span>
                </div>
            </div>

            <div class="sc sc-purple anim a3">
                <div class="sc-head">
                    <div class="sc-label">صحة النظام</div>
                    <div class="sc-ic"><i class="fas fa-heartbeat fa-lg"></i></div>
                </div>
                <div class="sc-val">100%</div>
                <div class="sc-footer">
                    <span class="sc-delta"><i class="fas fa-shield-alt"></i> Protected</span>
                </div>
            </div>

            <div class="sc sc-orange anim a4">
                <div class="sc-head">
                    <div class="sc-label">وقت التشغيل</div>
                    <div class="sc-ic"><i class="fas fa-clock fa-lg"></i></div>
                </div>
                <div class="sc-val">24/7</div>
                <div class="sc-footer">
                    <span class="sc-delta"><i class="fas fa-bolt"></i> Live</span>
                </div>
            </div>
        </div>

        <!-- Modules Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-10 text-right" id="modulesGrid">
            <!-- Content will be loaded by JS -->
        </div>

    </div>
</div>

<!-- Details Modal -->
<div class="fixed inset-0 bg-black/60 backdrop-blur-sm hidden items-center justify-center z-50 p-4" id="moduleDetailsModal">
    <div class="bg-white rounded-[32px] shadow-2xl w-full max-w-4xl transform scale-95 opacity-0 transition-all duration-300 overflow-hidden">
        <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <h3 class="text-2xl font-black text-gray-900">تفاصيل الوحدة</h3>
            <button class="w-10 h-10 flex items-center justify-center rounded-full bg-white shadow-sm border border-gray-100 text-gray-400 hover:text-red-500 transition-colors" onclick="closeModal('moduleDetailsModal')">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-8" id="moduleDetailsContent">
            <!-- Loaded by JS -->
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
    $('#modulesGrid').html(`
        <div class="col-span-full text-center py-20">
            <div class="animate-spin inline-block w-12 h-12 border-4 border-indigo-600 border-t-transparent rounded-full mb-4"></div>
            <p class="text-gray-500 font-bold">جاري تحميل الوحدات...</p>
        </div>
    `);
    
    $.ajax({
        url: apiRoutes.list,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                renderModules(response.modules);
            } else {
                showMockModules();
            }
        },
        error: function() {
            showMockModules();
        }
    });
}

function renderModules(modules) {
    let html = '';
    Object.keys(modules).forEach(key => {
        html += createModuleCard(key, modules[key]);
    });
    $('#modulesGrid').html(html);
}

function createModuleCard(key, module) {
    const statusClass = module.status === 'active' ? 'status-active' : 'status-inactive';
    const statusText = module.status === 'active' ? 'نشط' : 'غير نشط';
    
    return `
        <div class="premium-card p-8 flex flex-col h-full">
            <div class="flex items-start justify-between mb-6">
                <div class="bg-indigo-50 p-4 rounded-2xl">
                    <i class="${module.icon} text-3xl text-indigo-600"></i>
                </div>
                <span class="status-badge ${statusClass}">${statusText}</span>
            </div>
            
            <h3 class="text-xl font-black text-gray-900 mb-2">${module.name}</h3>
            <p class="text-gray-500 text-sm mb-6 leading-relaxed flex-grow">${module.description}</p>
            
            <div class="space-y-4 mb-8">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">الروابط المدمجة</p>
                <div class="flex flex-wrap gap-2">
                    ${Object.keys(module.routes).slice(0, 3).map(r => `
                        <span class="bg-gray-50 text-gray-600 px-3 py-1 rounded-lg text-xs font-bold border border-gray-100">${module.routes[r]}</span>
                    `).join('')}
                    ${Object.keys(module.routes).length > 3 ? `<span class="bg-gray-50 text-gray-400 px-3 py-1 rounded-lg text-xs font-bold border border-gray-100">+${Object.keys(module.routes).length - 3}</span>` : ''}
                </div>
            </div>
            
            <div class="flex gap-3">
                <button onclick="viewModuleDetails('${key}')" class="mod-btn mod-btn-secondary flex-1">
                    <i class="fas fa-eye"></i> عرض
                </button>
                <button onclick="toggleModule('${key}', '${module.status}')" class="mod-btn mod-btn-primary flex-1">
                    <i class="fas fa-${module.status === 'active' ? 'pause' : 'play'}"></i> ${module.status === 'active' ? 'إيقاف' : 'تشغيل'}
                </button>
            </div>
        </div>
    `;
}

function showMockModules() {
    const mock = {
        'core': { name: 'Core Real Estate', description: 'إدارة العقارات، القوائم، والميزات الأساسية للنظام العقاري المتكامل.', icon: 'fas fa-home', status: 'active', routes: {a:'Prop', b:'Users', c:'Agents'} },
        'global': { name: 'Global Services', description: 'تعدد العملات، اللغات، والميزات الدولية المتقدمة.', icon: 'fas fa-globe', status: 'active', routes: {a:'Curr', b:'Lang', c:'Gami'} },
        'ai': { name: 'AI Services', description: 'خدمات الذكاء الاصطناعي، التنبؤ بالأسعار، وتحليل الصور.', icon: 'fas fa-brain', status: 'active', routes: {a:'Pred', b:'Chat', c:'Vision'} }
    };
    renderModules(mock);
}

function viewModuleDetails(key) {
    const modal = $('#moduleDetailsModal');
    modal.removeClass('hidden').addClass('flex');
    setTimeout(() => modal.find('> div').removeClass('scale-95 opacity-0').addClass('scale-100 opacity-100'), 10);
    
    $('#moduleDetailsContent').html('<div class="text-center py-10"><div class="animate-spin w-10 h-10 border-4 border-indigo-600 border-t-transparent rounded-full inline-block"></div></div>');
    
    $.ajax({
        url: apiRoutes.details.replace(':key', key),
        method: 'GET',
        success: function(response) {
            const mod = response.module || {name: key, description: 'Details...', version: '1.0', routes: {}};
            $('#moduleDetailsContent').html(`
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-right">
                    <div class="space-y-6">
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase">اسم الوحدة</label>
                            <p class="text-xl font-black text-gray-900">${mod.name}</p>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-400 uppercase">الوصف</label>
                            <p class="text-gray-600 leading-relaxed">${mod.description}</p>
                        </div>
                        <div class="flex gap-10">
                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase">الإصدار</label>
                                <p class="font-mono font-bold text-indigo-600">${mod.version || '1.0.0'}</p>
                            </div>
                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase">الحالة</label>
                                <p class="font-bold text-green-600">نشط</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                        <label class="text-xs font-bold text-gray-400 uppercase mb-4 block">المسارات والروابط</label>
                        <div class="space-y-2">
                            ${Object.keys(mod.routes || {}).map(r => `
                                <div class="bg-white p-3 rounded-xl border border-gray-100 flex justify-between items-center">
                                    <i class="fas fa-link text-gray-300"></i>
                                    <span class="font-bold text-gray-700">${mod.routes[r]}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `);
        }
    });
}

function closeModal(id) {
    const m = $('#' + id);
    m.find('> div').addClass('scale-95 opacity-0').removeClass('scale-100 opacity-100');
    setTimeout(() => m.addClass('hidden').removeClass('flex'), 300);
}

function toggleModule(key, current) {
    // Simple mock toggle for visual feedback
    loadModules();
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\larvel state big\resources\views/modules/dashboard.blade.php ENDPATH**/ ?>