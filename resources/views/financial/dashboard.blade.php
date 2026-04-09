@extends('admin.layouts.admin')

@section('title', 'لوحة التحكم المالية')
@section('page-title', 'نظرة عامة مالية')

@section('content')
<style>
    :root {
        --blue: #0D6EFD;
        --blue-g: linear-gradient(135deg, #0D6EFD, #0a58ca);
        --purple: #6F42C1;
        --purple-g: linear-gradient(135deg, #6F42C1, #59359a);
        --green: #10b981;
        --green-g: linear-gradient(135deg, #10b981, #059669);
        --orange: #FD7E14;
        --orange-g: linear-gradient(135deg, #FD7E14, #dc6a0c);
        --sc-blue: linear-gradient(135deg, #0D6EFD 0%, #0a58ca 100%);
        --sc-purple: linear-gradient(135deg, #6F42C1 0%, #59359a 100%);
        --sc-green: linear-gradient(135deg, #10b981 0%, #059669 100%);
        --sc-orange: linear-gradient(135deg, #FD7E14 0%, #dc6a0c 100%);
        --bg: #F8FAFC;
        --card: #FFFFFF;
        --sh-sm: 0 1px 4px rgba(0, 0, 0, .06);
        --sh-md: 0 4px 16px rgba(0, 0, 0, .08);
        --sh-lg: 0 12px 32px rgba(0, 0, 0, .12);
        --r1: 10px;
        --r2: 14px;
        --r3: 20px;
        --tr: all .3s cubic-bezier(.4, 0, .2, 1);
    }

    .premium-card {
        background: var(--card);
        border: 1px solid rgba(229, 231, 235, 0.8);
        border-radius: var(--r3);
        transition: var(--tr);
        box-shadow: var(--sh-sm);
        position: relative;
        overflow: hidden;
    }
    .premium-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--sh-lg);
        border-color: rgba(16, 185, 129, 0.2);
    }

    .sc {
        border-radius: var(--r3);
        padding: 24px;
        position: relative;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        color: white;
    }
    .sc:hover { transform: translateY(-8px) scale(1.02); }
    .sc-blue { background: var(--sc-blue); box-shadow: 0 8px 24px rgba(13, 110, 253, .25); }
    .sc-green { background: var(--sc-green); box-shadow: 0 8px 24px rgba(16, 185, 129, .25); }
    .sc-purple { background: var(--sc-purple); box-shadow: 0 8px 24px rgba(111, 66, 193, .25); }
    .sc-orange { background: var(--sc-orange); box-shadow: 0 8px 24px rgba(253, 126, 20, .25); }

    .sc-head { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 16px; }
    .sc-label { font-size: 13px; font-weight: 700; opacity: .9; text-transform: uppercase; letter-spacing: 0.5px; }
    .sc-ic { width: 48px; height: 48px; border-radius: 14px; background: rgba(255, 255, 255, .2); display: flex; align-items: center; justify-content: center; font-size: 20px; backdrop-filter: blur(4px); }
    .sc-val { font-size: 32px; font-weight: 900; line-height: 1; margin-bottom: 12px; }
    .sc-footer { display: flex; align-items: center; gap: 8px; font-size: 12px; font-weight: 600; }
    .sc-delta { padding: 4px 10px; border-radius: 20px; background: rgba(255, 255, 255, .2); display: inline-flex; align-items: center; gap: 4px; }

    .glass-header {
        background: linear-gradient(135deg, rgba(79, 70, 229, 0.95), rgba(147, 51, 234, 0.95));
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .fin-table th {
        background: #F8FAFC;
        padding: 18px 20px;
        font-size: 12px;
        font-weight: 800;
        color: #64748B;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 2px solid #F1F5F9;
    }
    .fin-table td {
        padding: 18px 20px;
        border-bottom: 1px solid #F1F5F9;
        font-size: 14px;
        font-weight: 600;
        color: #334155;
    }
    .fin-table tr:hover { background: #FDFDFD; }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .anim { animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards; opacity: 0; }
    .a1 { animation-delay: 0.1s; }
    .a2 { animation-delay: 0.2s; }
    .a3 { animation-delay: 0.3s; }
    .a4 { animation-delay: 0.4s; }
    .a5 { animation-delay: 0.5s; }

    .status-pill {
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 800;
    }
    .status-success { background: #DCFCE7; color: #16A34A; }
    .status-primary { background: #DBEAFE; color: #2563EB; }

    .quick-box {
        border: 1px solid #F1F5F9;
        border-radius: 16px;
        padding: 20px;
        transition: var(--tr);
        text-align: center;
    }
    .quick-box:hover {
        background: #F8FAFC;
        border-color: #10b981;
        transform: translateY(-4px);
    }
    .quick-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        font-size: 24px;
        transition: var(--tr);
    }
    .quick-box:hover .quick-icon { transform: scale(1.1) rotate(5deg); }
    
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
                                <i class="fas fa-coins text-white text-3xl"></i>
                            </div>
                            <div>
                                <h1 class="text-4xl font-black text-white tracking-tight">لوحة التحكم المالية</h1>
                                <p class="text-indigo-100 text-lg font-medium opacity-90">نظرة عامة على الأداء المالي والمعاملات</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <button onclick="window.location.reload()" class="group bg-white text-indigo-700 px-8 py-4 rounded-2xl hover:bg-indigo-50 transition-all duration-300 shadow-xl hover:shadow-2xl hover:scale-105 flex items-center">
                            <i class="fas fa-sync-alt ml-3 group-hover:rotate-180 transition-transform duration-500"></i>
                            <span class="font-bold text-lg">تحديث</span>
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
                    <div class="sc-label">إجمالي الإيرادات</div>
                    <div class="sc-ic"><i class="fas fa-coins fa-lg"></i></div>
                </div>
                <div class="sc-val">{{ number_format($stats['total_revenue'] ?? 0, 2) }}</div>
                <div class="sc-footer">
                    <span class="sc-delta"><i class="fas fa-arrow-up"></i> +12.5%</span>
                </div>
            </div>

            <div class="sc sc-green anim a2">
                <div class="sc-head">
                    <div class="sc-label">العمليات الناجحة</div>
                    <div class="sc-ic"><i class="fas fa-check-double fa-lg"></i></div>
                </div>
                <div class="sc-val">{{ $stats['total_payments'] ?? 0 }}</div>
                <div class="sc-footer">
                    <span class="sc-delta"><i class="fas fa-sync"></i> مباشر</span>
                </div>
            </div>

            <div class="sc sc-purple anim a3">
                <div class="sc-head">
                    <div class="sc-label">المحافظ النشطة</div>
                    <div class="sc-ic"><i class="fas fa-wallet fa-lg"></i></div>
                </div>
                <div class="sc-val">{{ $stats['active_wallets'] ?? 0 }}</div>
                <div class="sc-footer">
                    <span class="sc-delta"><i class="fas fa-user-shield"></i> مؤمنة</span>
                </div>
            </div>

            <div class="sc sc-orange anim a4">
                <div class="sc-head">
                    <div class="sc-label">إجمالي السيولة</div>
                    <div class="sc-ic"><i class="fas fa-vault fa-lg"></i></div>
                </div>
                <div class="sc-val">{{ number_format($stats['total_wallet_balance'] ?? 0, 2) }}</div>
                <div class="sc-footer">
                    <span class="sc-delta"><i class="fas fa-chart-pie"></i> متوازن</span>
                </div>
            </div>
        </div>

        <!-- Financial Modules Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-10 text-right" id="financialModulesGrid">
            <!-- Wallet Management -->
            <div class="premium-card p-8 flex flex-col h-full">
                <div class="flex items-start justify-between mb-6">
                    <div class="bg-indigo-50 p-4 rounded-2xl">
                        <i class="fas fa-wallet text-3xl text-indigo-600"></i>
                    </div>
                    <span class="status-badge status-active">نشط</span>
                </div>
                
                <h3 class="text-xl font-black text-gray-900 mb-2">إدارة المحافظ</h3>
                <p class="text-gray-500 text-sm mb-6 leading-relaxed flex-grow">التحكم في أرصدة العملاء والتحويلات الداخلية</p>
                
                <div class="space-y-4 mb-8">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">الروابط المدمجة</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-gray-50 text-gray-600 px-3 py-1 rounded-lg text-xs font-bold border border-gray-100">المحافظ</span>
                        <span class="bg-gray-50 text-gray-600 px-3 py-1 rounded-lg text-xs font-bold border border-gray-100">التحويلات</span>
                        <span class="bg-gray-50 text-gray-600 px-3 py-1 rounded-lg text-xs font-bold border border-gray-100">التقارير</span>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <a href="{{ route('admin.financial.wallets') }}" class="mod-btn mod-btn-secondary flex-1">
                        <i class="fas fa-eye"></i> عرض
                    </a>
                    <a href="{{ route('admin.financial.wallets') }}" class="mod-btn mod-btn-primary flex-1">
                        <i class="fas fa-cog"></i> إعدادات
                    </a>
                </div>
            </div>

            <!-- Payment System -->
            <div class="premium-card p-8 flex flex-col h-full">
                <div class="flex items-start justify-between mb-6">
                    <div class="bg-indigo-50 p-4 rounded-2xl">
                        <i class="fas fa-credit-card text-3xl text-indigo-600"></i>
                    </div>
                    <span class="status-badge status-active">نشط</span>
                </div>
                
                <h3 class="text-xl font-black text-gray-900 mb-2">نظام المدفوعات</h3>
                <p class="text-gray-500 text-sm mb-6 leading-relaxed flex-grow">معالجة المدفوعات الإلكترونية والفواتير الآلية</p>
                
                <div class="space-y-4 mb-8">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">الروابط المدمجة</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-gray-50 text-gray-600 px-3 py-1 rounded-lg text-xs font-bold border border-gray-100">المدفوعات</span>
                        <span class="bg-gray-50 text-gray-600 px-3 py-1 rounded-lg text-xs font-bold border border-gray-100">الفواتير</span>
                        <span class="bg-gray-50 text-gray-600 px-3 py-1 rounded-lg text-xs font-bold border border-gray-100">التقارير</span>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <a href="{{ route('admin.financial.payments') }}" class="mod-btn mod-btn-secondary flex-1">
                        <i class="fas fa-eye"></i> عرض
                    </a>
                    <a href="{{ route('admin.financial.payments') }}" class="mod-btn mod-btn-primary flex-1">
                        <i class="fas fa-cog"></i> إعدادات
                    </a>
                </div>
            </div>

            <!-- Transaction History -->
            <div class="premium-card p-8 flex flex-col h-full">
                <div class="flex items-start justify-between mb-6">
                    <div class="bg-indigo-50 p-4 rounded-2xl">
                        <i class="fas fa-exchange-alt text-3xl text-indigo-600"></i>
                    </div>
                    <span class="status-badge status-active">نشط</span>
                </div>
                
                <h3 class="text-xl font-black text-gray-900 mb-2">سجل المعاملات</h3>
                <p class="text-gray-500 text-sm mb-6 leading-relaxed flex-grow">مراجعة شاملة لكافة الحركات المالية على المنصة</p>
                
                <div class="space-y-4 mb-8">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">الروابط المدمجة</p>
                    <div class="flex flex-wrap gap-2">
                        <span class="bg-gray-50 text-gray-600 px-3 py-1 rounded-lg text-xs font-bold border border-gray-100">السجل</span>
                        <span class="bg-gray-50 text-gray-600 px-3 py-1 rounded-lg text-xs font-bold border border-gray-100">الفلاتر</span>
                        <span class="bg-gray-50 text-gray-600 px-3 py-1 rounded-lg text-xs font-bold border border-gray-100">التصدير</span>
                    </div>
                </div>
                
                <div class="flex gap-3">
                    <a href="{{ route('admin.financial.transactions') }}" class="mod-btn mod-btn-secondary flex-1">
                        <i class="fas fa-eye"></i> عرض
                    </a>
                    <a href="{{ route('admin.financial.transactions') }}" class="mod-btn mod-btn-primary flex-1">
                        <i class="fas fa-cog"></i> إعدادات
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
