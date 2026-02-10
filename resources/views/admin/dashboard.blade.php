@extends('admin.layouts.admin')

@section('title', 'لوحة التحكم')

@section('page-title', 'نظرة عامة')

@push('styles')
<style>
    /* ============================================
       ENHANCED MODERN DASHBOARD STYLES (LIGHT & ATTRACTIVE)
       ============================================ */
    
    /* Root Variables for Light & Airy Theme */
    :root {
        --primary-blue: #6366f1; /* Indigo */
        --primary-blue-light: #818cf8;
        --secondary-purple: #a855f7;
        --success-green: #10b981;
        --warning-orange: #f59e0b;
        --danger-red: #ef4444;
        
        /* Neutral Scale */
        --bg-body: #f8fafc; /* Slate 50 */
        --bg-card: #ffffff;
        --text-main: #334155; /* Slate 700 - Softer "Light Black" */
        --text-muted: #64748b; /* Slate 500 */
        --border-color: #e2e8f0; /* Slate 200 */
        
        /* Shadows */
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.03);
        --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
        --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.05), 0 4px 6px -2px rgba(0,0,0,0.025);
        --shadow-hover: 0 20px 25px -5px rgba(0,0,0,0.08), 0 10px 10px -5px rgba(0,0,0,0.03);
        
        /* Radius */
        --radius-md: 12px;
        --radius-lg: 16px;
        --radius-xl: 24px;
        
        /* Transitions */
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Global Resets */
    * {
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        background-color: var(--bg-body);
        color: var(--text-main);
    }

    .text-modern {
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
    }
    
    /* Page Background - Clean & Light */
    .page-background {
        background-color: var(--bg-body);
        background-image: 
            radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.03) 0px, transparent 50%),
            radial-gradient(at 100% 0%, rgba(168, 85, 247, 0.03) 0px, transparent 50%);
        min-height: 100vh;
    }

    /* Cards - Soft & Floating */
    .modern-card {
        background: var(--bg-card);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        transition: var(--transition);
        overflow: hidden;
    }
    
    .modern-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary-blue-light);
    }

    /* Stat Cards - Vibrant but Soft */
    .stat-card {
        border-radius: var(--radius-xl);
        padding: 24px;
        color: white;
        position: relative;
        overflow: hidden;
        border: none;
        box-shadow: var(--shadow-md);
        transition: var(--transition);
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: -20px;
        right: -20px;
        width: 120px;
        height: 120px;
        background: rgba(255,255,255,0.15);
        border-radius: 50%;
        filter: blur(20px);
    }

    .stat-card-blue { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); }
    .stat-card-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .stat-card-orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .stat-card-red { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

    /* Management Sections - Clean Grid */
    .management-grid-item {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    
    .management-grid-item:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary-blue-light);
    }
    
    /* subtle top accent */
    .management-grid-item::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-blue), var(--secondary-purple));
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 10;
    }
    
    .management-grid-item:hover::after {
        opacity: 1;
    }

    .management-header {
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        background: rgba(248, 250, 252, 0.5);
    }

    .management-header h4 {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-main);
        margin: 0;
    }
    
    .management-body {
        padding: 16px 20px;
        flex: 1;
    }

    /* Icon Containers - Light & Airy */
    .icon-container {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        margin-right: 12px;
        transition: var(--transition);
    }
    
    .icon-container.blue { background: #e0e7ff; color: #4f46e5; }
    .icon-container.green { background: #dcfce7; color: #16a34a; }
    .icon-container.orange { background: #ffedd5; color: #ea580c; }
    .icon-container.red { background: #fee2e2; color: #dc2626; }
    .icon-container.purple { background: #f3e8ff; color: #9333ea; }
    .icon-container.cyan { background: #cffafe; color: #0891b2; }
    .icon-container.pink { background: #fce7f3; color: #db2777; }
    .icon-container.indigo { background: #e0e7ff; color: #4338ca; }
    .icon-container.gray { background: #f1f5f9; color: #64748b; }
    .icon-container.yellow { background: #fef9c3; color: #ca8a04; }

    .management-grid-item:hover .icon-container {
        transform: scale(1.1);
    }

    /* Links - Clean List */
    .link-modern {
        display: flex;
        align-items: center;
        padding: 8px 12px;
        color: #334155 !important; /* Slate 700 - "Light Black" */
        text-decoration: none !important;
        border-radius: 8px;
        transition: all 0.2s ease;
        font-size: 13px;
        font-weight: 500;
        margin-bottom: 2px;
        border: 1px solid transparent;
    }
    
    .link-modern:hover {
        background-color: #f8fafc;
        border-color: #e2e8f0;
        color: var(--primary-blue) !important;
        transform: translateX(4px);
    }
    
    .link-modern i {
        width: 20px;
        text-align: center;
        margin-right: 10px;
        font-size: 13px;
        color: #6366f1; /* Primary Blue - "Beautiful Color" */
        transition: color 0.2s;
    }
    
    .link-modern:hover i {
        color: var(--primary-blue);
    }

    /* Quick Action Buttons - Minimal Tiles */
    .quick-action-btn {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        text-decoration: none !important;
        transition: var(--transition);
        height: 100%;
    }
    
    .quick-action-btn i {
        font-size: 24px;
        margin-bottom: 12px;
        color: var(--primary-blue);
        transition: var(--transition);
    }
    
    .quick-action-btn p {
        color: var(--text-main);
        font-weight: 600;
        font-size: 13px;
        margin: 0;
    }
    
    .quick-action-btn:hover {
        border-color: var(--primary-blue);
        box-shadow: var(--shadow-md);
        transform: translateY(-3px);
    }
    
    .quick-action-btn:hover i {
        transform: scale(1.1);
    }

    /* Section Headers */
    .section-header {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 20px;
        position: relative;
        padding-bottom: 10px;
    }
    
    .section-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 40px;
        height: 3px;
        background: var(--primary-blue);
        border-radius: 2px;
    }

    /* Chart Containers */
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }

    /* Status Indicators */
    .status-indicator {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .status-indicator.healthy {
        background: #dcfce7;
        color: #15803d;
    }
    
    .status-indicator.warning {
        background: #fef9c3;
        color: #a16207;
    }

    /* Buttons */
    .btn-info, .btn-success, .btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        transition: var(--transition);
        border: none;
        cursor: pointer;
        gap: 8px;
    }
    
    .btn-info { background: #e0f2fe; color: #0284c7; }
    .btn-info:hover { background: #bae6fd; }
    
    .btn-success { background: #dcfce7; color: #16a34a; }
    .btn-success:hover { background: #bbf7d0; }

    /* Premium Dashboard Cards - Enhanced to Match First Section */
    .premium-card {
        background: aliceblue;
        border: 1px solid rgba(226, 232, 240, 0.6);
        border-radius: 18px;
        padding: 28px;
        transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        position: relative;
        overflow: hidden;
        box-shadow: 
            0 1px 3px rgba(0, 0, 0, 0.04),
            0 1px 2px rgba(0, 0, 0, 0.02),
            inset 0 1px 0 rgba(255, 255, 255, 0.8);
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .premium-card:hover {
        transform: translateY(-6px) scale(1.02);
        box-shadow: 
            0 20px 40px -10px rgba(0, 0, 0, 0.12),
            0 8px 16px -4px rgba(0, 0, 0, 0.06),
            inset 0 1px 0 rgba(255, 255, 255, 0.9);
        border-color: rgba(99, 102, 241, 0.15);
        background: linear-gradient(135deg, #ffffff 0%, #fafbfc 40%, #f1f5f9 100%);
    }
    
    .premium-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, 
            var(--primary-blue) 0%, 
            var(--secondary-purple) 50%, 
            var(--primary-blue-light) 100%);
        opacity: 0;
        transition: opacity 0.4s ease;
        border-radius: 18px 18px 0 0;
    }
    
    .premium-card:hover::before {
        opacity: 1;
    }
    
    .premium-card::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.03) 0%, transparent 70%);
        opacity: 0;
        transition: opacity 0.4s ease;
        pointer-events: none;
    }
    
    .premium-card:hover::after {
        opacity: 1;
    }
    
    .card-header {
        display: flex;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .icon-wrapper {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 18px;
        font-size: 22px;
        transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        position: relative;
        box-shadow: 
            0 2px 8px rgba(0, 0, 0, 0.08),
            inset 0 1px 0 rgba(255, 255, 255, 0.5);
    }
    
    .icon-wrapper::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: 14px;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.3) 0%, rgba(255, 255, 255, 0.1) 100%);
        pointer-events: none;
    }
    
    .icon-wrapper.blue { 
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 50%, #a5b4fc 100%); 
        color: #4f46e5;
        box-shadow: 0 4px 12px rgba(79, 70, 229, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.5);
    }
    .icon-wrapper.green { 
        background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 50%, #86efac 100%); 
        color: #16a34a;
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.5);
    }
    .icon-wrapper.emerald { 
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 50%, #6ee7b7 100%); 
        color: #059669;
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.5);
    }
    .icon-wrapper.purple { 
        background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 50%, #d8b4fe 100%); 
        color: #9333ea;
        box-shadow: 0 4px 12px rgba(147, 51, 234, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.5);
    }
    .icon-wrapper.orange { 
        background: linear-gradient(135deg, #ffedd5 0%, #fed7aa 50%, #fdba74 100%); 
        color: #ea580c;
        box-shadow: 0 4px 12px rgba(234, 88, 12, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.5);
    }
    .icon-wrapper.pink { 
        background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 50%, #f9a8d4 100%); 
        color: #db2777;
        box-shadow: 0 4px 12px rgba(219, 39, 119, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.5);
    }
    .icon-wrapper.indigo { 
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 50%, #a5b4fc 100%); 
        color: #4338ca;
        box-shadow: 0 4px 12px rgba(67, 56, 202, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.5);
    }
    .icon-wrapper.cyan { 
        background: linear-gradient(135deg, #cffafe 0%, #a5f3fc 50%, #67e8f9 100%); 
        color: #0891b2;
        box-shadow: 0 4px 12px rgba(8, 145, 178, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.5);
    }
    .icon-wrapper.gray { 
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 50%, #cbd5e1 100%); 
        color: #64748b;
        box-shadow: 0 4px 12px rgba(100, 116, 139, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.5);
    }
    .icon-wrapper.slate { 
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 50%, #cbd5e1 100%); 
        color: #475569;
        box-shadow: 0 4px 12px rgba(71, 85, 105, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.5);
    }
    .icon-wrapper.sky { 
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 50%, #7dd3fc 100%); 
        color: #0284c7;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.5);
    }
    .icon-wrapper.violet { 
        background: linear-gradient(135deg, #ede9fe 0%, #ddd6fe 50%, #c4b5fd 100%); 
        color: #7c3aed;
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.5);
    }
    .icon-wrapper.red { 
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 50%, #fca5a5 100%); 
        color: #dc2626;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.5);
    }
    .icon-wrapper.yellow { 
        background: linear-gradient(135deg, #fef9c3 0%, #fde047 50%, #facc15 100%); 
        color: #ca8a04;
        box-shadow: 0 4px 12px rgba(202, 138, 4, 0.15), inset 0 1px 0 rgba(255, 255, 255, 0.5);
    }
    
    .premium-card:hover .icon-wrapper {
        transform: scale(1.1) rotate(5deg);
    }
    
    .card-header h5 {
        font-size: 18px;
        font-weight: 700;
        color: var(--text-main);
        margin: 0;
    }
    
    .card-divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(226, 232, 240, 0.5), transparent);
        margin: 20px 0;
    }
    
    .card-content {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    
    .card-link {
        display: flex;
        align-items: center;
        padding: 14px 18px;
        border-radius: 12px;
        color: #334155; /* Slate 700 - "Light Black" */
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        border: 1px solid transparent;
        position: relative;
        overflow: hidden;
    }
    
    .card-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(99, 102, 241, 0.05), transparent);
        transition: left 0.5s ease;
    }
    
    .card-link:hover {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        color: var(--primary-blue);
        transform: translateX(6px) scale(1.02);
        border-color: rgba(99, 102, 241, 0.15);
        box-shadow: 0 2px 8px rgba(99, 102, 241, 0.08);
    }
    
    .card-link:hover::before {
        left: 100%;
    }
    
    .card-link i {
        width: 20px;
        margin-right: 12px;
        font-size: 14px;
        transition: color 0.2s ease;
        color: #6366f1; /* Primary Blue - "Beautiful Color" */
    }
    
    .card-link:hover i {
        color: var(--primary-blue);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .stat-card { padding: 20px; }
        .management-grid-item { padding: 20px; }
        .icon-container { width: 36px; height: 36px; font-size: 16px; }
        .premium-card { padding: 20px; }
        .icon-wrapper { width: 40px; height: 40px; font-size: 18px; }
    }
</style>
@endpush

@section('content')
    <div class="min-h-screen page-background text-modern">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

            <!-- Enhanced Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="stat-card stat-card-blue">
                    <div class="flex items-center justify-between relative z-10">
                        <div>
                            <p class="text-white/90 text-sm font-medium mb-1">إجمالي المستخدمين</p>
                            <p class="text-3xl font-bold mb-2">{{ $stats['site']['total_users'] }}</p>
                            <p class="text-white/80 text-sm flex items-center">
                                <i class="fas fa-arrow-up ml-1"></i>
                                +{{ $stats['site']['new_users_today'] }} اليوم
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
                            <p class="text-3xl font-bold mb-2">{{ $stats['site']['total_properties'] }}</p>
                            <p class="text-white/80 text-sm flex items-center">
                                <i class="fas fa-arrow-up ml-1"></i>
                                +{{ $stats['site']['new_properties_today'] }} اليوم
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
                            <p class="text-3xl font-bold mb-2">{{ $stats['site']['total_investors'] }}</p>
                            <p class="text-white/80 text-sm flex items-center">
                                <i class="fas fa-arrow-up ml-1"></i>
                                +{{ $stats['site']['new_investors_today'] }} اليوم
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
                            <p class="text-3xl font-bold mb-2">${{ number_format($stats['site']['total_revenue'], 0) }}</p>
                            <p class="text-white/80 text-sm flex items-center">
                                <i class="fas fa-arrow-up ml-1"></i>
                                +${{ number_format($stats['site']['revenue_today'], 0) }} اليوم
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Recent Users -->
                <div class="modern-card p-6">
                    <h3 class="section-header">Recent Users</h3>
                    <div class="space-y-3">
                        @forelse ($stats['recent_users'] as $user)
                            <div class="flex items-center p-2 hover:bg-gray-50 rounded-lg transition-colors">
                                <div class="icon-container blue mr-3">
                                    <i class="fas fa-user text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                </div>
                                <span class="text-xs text-gray-500">{{ $user->created_at->diffForHumans() }}</span>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">No recent users</p>
                        @endforelse
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <a href="{{ route('admin.users.index') }}" class="btn-info w-full justify-center">
                            <i class="fas fa-arrow-left"></i>
                            View All Users
                        </a>
                    </div>
                </div>

                <!-- Recent Properties -->
                <div class="modern-card p-6">
                    <h3 class="section-header">Recent Properties</h3>
                    <div class="space-y-3">
                        @forelse ($stats['recent_properties'] as $property)
                            <div class="flex items-center p-2 hover:bg-gray-50 rounded-lg transition-colors">
                                <div class="icon-container green mr-3">
                                    <i class="fas fa-home text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">{{ $property->title }}</p>
                                    <p class="text-xs text-gray-500">${{ number_format($property->price, 0) }}</p>
                                </div>
                                <span class="text-xs text-gray-500">{{ $property->created_at->diffForHumans() }}</span>
                            </div>
                        @empty
                            <p class="text-gray-500 text-sm">No recent properties</p>
                        @endforelse
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <a href="{{ route('admin.properties.index') }}" class="btn-success w-full justify-center">
                            <i class="fas fa-arrow-left"></i>
                            View All Properties
                        </a>
                    </div>
                </div>

                <!-- System Status -->
                <div class="modern-card p-6">
                    <h3 class="section-header">System Status</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Database</span>
                            <span class="status-indicator healthy">
                                <i class="fas fa-check-circle text-green-500"></i>
                                Healthy
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Storage</span>
                            <span class="status-indicator healthy">
                                <i class="fas fa-database text-green-500"></i>
                                65% Used
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">API</span>
                            <span class="status-indicator healthy">
                                <i class="fas fa-wifi text-green-500"></i>
                                Online
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Queue</span>
                            <span class="status-indicator warning">
                                <i class="fas fa-clock text-amber-500"></i>
                                12 Jobs
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Quick Actions -->
            <div class="modern-card p-6 mb-6">
                <h3 class="section-header">Priority Quick Actions</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    <a href="{{ route('admin.users.create') }}" class="quick-action-btn">
                        <i class="fas fa-user-plus"></i>
                        <p>New User</p>
                    </a>

                    <a href="{{ route('admin.properties.create') }}" class="quick-action-btn">
                        <i class="fas fa-plus-circle"></i>
                        <p>New Property</p>
                    </a>

                    <a href="{{ route('admin.blog.posts.create') }}" class="quick-action-btn">
                        <i class="fas fa-edit"></i>
                        <p>New Post</p>
                    </a>

                    <a href="{{ route('investor.stats.public') }}" class="quick-action-btn">
                        <i class="fas fa-chart-line"></i>
                        <p>Investor Stats</p>
                    </a>

                    <a href="{{ route('admin.settings.index') }}" class="quick-action-btn">
                        <i class="fas fa-cog"></i>
                        <p>Settings</p>
                    </a>

                    <a href="{{ route('admin.system.dashboard') }}" class="quick-action-btn">
                        <i class="fas fa-tools"></i>
                        <p>Maintenance</p>
                    </a>
                </div>
            </div>

            <!-- Premium Management Hub -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-2xl font-bold text-gray-900">Management Hub</h3>
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <i class="fas fa-th-large"></i>
                        <span>Premium Dashboard</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

                    <!-- Core Operations -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper blue">
                                <i class="fas fa-cogs"></i>
                            </div>
                            <h5>Core Operations</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('admin.users.index') }}" class="card-link">
                                <i class="fas fa-users"></i>
                                <span>All Users</span>
                            </a>
                            <a href="{{ route('admin.users.create') }}" class="card-link">
                                <i class="fas fa-user-plus"></i>
                                <span>Create User</span>
                            </a>
                            <a href="{{ route('admin.agents.index') }}" class="card-link">
                                <i class="fas fa-user-tie"></i>
                                <span>Agents</span>
                            </a>
                            <a href="{{ route('investor.index') }}" class="card-link">
                                <i class="fas fa-hand-holding-usd"></i>
                                <span>Investors</span>
                            </a>
                            <a href="{{ route('admin.system.logs') }}" class="card-link">
                                <i class="fas fa-history"></i>
                                <span>Activity Logs</span>
                            </a>
                        </div>
                    </div>

                    <!-- Property Management -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper green">
                                <i class="fas fa-city"></i>
                            </div>
                            <h5>Properties</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('admin.properties.index') }}" class="card-link">
                                <i class="fas fa-building"></i>
                                <span>All Properties</span>
                            </a>
                            <a href="{{ route('admin.properties.create') }}" class="card-link">
                                <i class="fas fa-plus-circle"></i>
                                <span>Add Property</span>
                            </a>
                            <a href="{{ route('admin.projects.index') }}" class="card-link">
                                <i class="fas fa-project-diagram"></i>
                                <span>Projects</span>
                            </a>
                            <a href="{{ route('admin.companies.index') }}" class="card-link">
                                <i class="fas fa-briefcase"></i>
                                <span>Companies</span>
                            </a>
                            <a href="{{ route('properties.search.index') }}" class="card-link">
                                <i class="fas fa-search-location"></i>
                                <span>Advanced Search</span>
                            </a>
                        </div>
                    </div>

                    <!-- Financial Hub -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper emerald">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h5>Financial Hub</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('admin.financial.expenses') }}" class="card-link">
                                <i class="fas fa-money-bill-wave"></i>
                                <span>Expenses</span>
                            </a>
                            <a href="{{ route('payments.invoices.index') }}" class="card-link">
                                <i class="fas fa-file-invoice"></i>
                                <span>Invoices</span>
                            </a>
                            <a href="{{ route('wallet.index') }}" class="card-link">
                                <i class="fas fa-wallet"></i>
                                <span>My Wallet</span>
                            </a>
                            <a href="{{ route('payments.wallets.index') }}" class="card-link">
                                <i class="fas fa-coins"></i>
                                <span>System Wallets</span>
                            </a>
                            <a href="{{ route('payments.escrow.index') }}" class="card-link">
                                <i class="fas fa-hand-holding-usd"></i>
                                <span>Escrow</span>
                            </a>
                        </div>
                    </div>

                    <!-- Revenue Streams -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper purple">
                                <i class="fas fa-dollar-sign"></i>
                            </div>
                            <h5>Revenue Streams</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('subscriptions.plans.index') }}" class="card-link">
                                <i class="fas fa-tags"></i>
                                <span>Subscription Plans</span>
                            </a>
                            <a href="{{ route('subscriptions.plans.stats') }}" class="card-link">
                                <i class="fas fa-chart-area"></i>
                                <span>Plan Statistics</span>
                            </a>
                            <a href="{{ route('subscriptions.plans.admin-compare') }}" class="card-link">
                                <i class="fas fa-balance-scale"></i>
                                <span>Compare Plans</span>
                            </a>
                            <a href="{{ route('taxes.index') }}" class="card-link">
                                <i class="fas fa-calculator"></i>
                                <span>Tax Management</span>
                            </a>
                            <a href="{{ route('financial.roi.main') }}" class="card-link">
                                <i class="fas fa-chart-pie"></i>
                                <span>ROI Calculator</span>
                            </a>
                        </div>
                    </div>

                    <!-- CRM & Sales -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper orange">
                                <i class="fas fa-funnel-dollar"></i>
                            </div>
                            <h5>CRM & Sales</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('leads.dashboard') }}" class="card-link">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Leads Dashboard</span>
                            </a>
                            <a href="{{ route('leads.pipeline') }}" class="card-link">
                                <i class="fas fa-filter"></i>
                                <span>Pipeline</span>
                            </a>
                            <a href="{{ route('leads.index') }}" class="card-link">
                                <i class="fas fa-list"></i>
                                <span>All Leads</span>
                            </a>
                            <a href="{{ route('lead-scoring.index') }}" class="card-link">
                                <i class="fas fa-star-half-alt"></i>
                                <span>Lead Scoring</span>
                            </a>
                            <a href="{{ route('lead-analytics.dashboard') }}" class="card-link">
                                <i class="fas fa-chart-pie"></i>
                                <span>Analytics</span>
                            </a>
                        </div>
                    </div>

                    <!-- Marketing Suite -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper pink">
                                <i class="fas fa-bullhorn"></i>
                            </div>
                            <h5>Marketing Suite</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('marketing.campaigns') }}" class="card-link">
                                <i class="fas fa-ad"></i>
                                <span>Campaigns</span>
                            </a>
                            <a href="{{ route('admin.seo.index') }}" class="card-link">
                                <i class="fas fa-search"></i>
                                <span>SEO Management</span>
                            </a>
                            <a href="{{ route('admin.seo.keywords') }}" class="card-link">
                                <i class="fas fa-key"></i>
                                <span>Keywords</span>
                            </a>
                            <a href="{{ route('admin.seo.analyze') }}" class="card-link">
                                <i class="fas fa-microscope"></i>
                                <span>SEO Analysis</span>
                            </a>
                            <a href="{{ route('admin.seo.sitemap') }}" class="card-link">
                                <i class="fas fa-sitemap"></i>
                                <span>Sitemap</span>
                            </a>
                        </div>
                    </div>

                    <!-- Content Hub -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper indigo">
                                <i class="fas fa-pen-nib"></i>
                            </div>
                            <h5>Content Hub</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('admin.content.dashboard') }}" class="card-link">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>CMS Dashboard</span>
                            </a>
                            <a href="{{ route('admin.blog.posts.index') }}" class="card-link">
                                <i class="fas fa-newspaper"></i>
                                <span>Blog Posts</span>
                            </a>
                            <a href="{{ route('admin.pages.index') }}" class="card-link">
                                <i class="fas fa-file-code"></i>
                                <span>Static Pages</span>
                            </a>
                            <a href="{{ route('admin.media.index') }}" class="card-link">
                                <i class="fas fa-images"></i>
                                <span>Media Library</span>
                            </a>
                            <a href="{{ route('admin.faqs.index') }}" class="card-link">
                                <i class="fas fa-question-circle"></i>
                                <span>FAQs</span>
                            </a>
                        </div>
                    </div>

                    <!-- Analytics Center -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper cyan">
                                <i class="fas fa-chart-network"></i>
                            </div>
                            <h5>Analytics Center</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('analytics.overview') }}" class="card-link">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Overview</span>
                            </a>
                            <a href="{{ route('analytics.real-time') }}" class="card-link">
                                <i class="fas fa-clock"></i>
                                <span>Real-Time</span>
                            </a>
                            <a href="{{ route('analytics.heatmap.index') }}" class="card-link">
                                <i class="fas fa-fire"></i>
                                <span>Heatmaps</span>
                            </a>
                            <a href="{{ route('reports.sales.index') }}" class="card-link">
                                <i class="fas fa-piggy-bank"></i>
                                <span>Sales Reports</span>
                            </a>
                            <a href="{{ route('admin.reports.custom') }}" class="card-link">
                                <i class="fas fa-filter"></i>
                                <span>Custom Reports</span>
                            </a>
                        </div>
                    </div>

                    <!-- System Tools -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper gray">
                                <i class="fas fa-tools"></i>
                            </div>
                            <h5>System Tools</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('admin.system.dashboard') }}" class="card-link">
                                <i class="fas fa-server"></i>
                                <span>System Dashboard</span>
                            </a>
                            <a href="{{ route('admin.backups') }}" class="card-link">
                                <i class="fas fa-save"></i>
                                <span>Backups</span>
                            </a>
                            <a href="{{ route('admin.system.queue') }}" class="card-link">
                                <i class="fas fa-tasks"></i>
                                <span>Queue Manager</span>
                            </a>
                            <a href="{{ route('admin.system.storage') }}" class="card-link">
                                <i class="fas fa-hdd"></i>
                                <span>Storage</span>
                            </a>
                            <a href="{{ route('routes.index') }}" class="card-link">
                                <i class="fas fa-route"></i>
                                <span>Route Manager</span>
                            </a>
                        </div>
                    </div>

                    <!-- Platform Settings -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper slate">
                                <i class="fas fa-cogs"></i>
                            </div>
                            <h5>Platform Settings</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('admin.settings.general') }}" class="card-link">
                                <i class="fas fa-sliders-h"></i>
                                <span>General</span>
                            </a>
                            <a href="{{ route('admin.settings.email') }}" class="card-link">
                                <i class="fas fa-envelope"></i>
                                <span>Email</span>
                            </a>
                            <a href="{{ route('admin.settings.payment') }}" class="card-link">
                                <i class="fas fa-credit-card"></i>
                                <span>Payment</span>
                            </a>
                            <a href="{{ route('admin.settings.security') }}" class="card-link">
                                <i class="fas fa-lock"></i>
                                <span>Security</span>
                            </a>
                            <a href="{{ route('settings.index') }}" class="card-link">
                                <i class="fas fa-user-cog"></i>
                                <span>My Preferences</span>
                            </a>
                        </div>
                    </div>

                    <!-- Communication Hub -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper sky">
                                <i class="fas fa-comments"></i>
                            </div>
                            <h5>Communication</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('messages.inbox') }}" class="card-link">
                                <i class="fas fa-inbox"></i>
                                <span>Inbox</span>
                            </a>
                            <a href="{{ route('messages.chat') }}" class="card-link">
                                <i class="fas fa-comment-dots"></i>
                                <span>Live Chat</span>
                            </a>
                            <a href="{{ route('messages.appointments') }}" class="card-link">
                                <i class="fas fa-calendar-check"></i>
                                <span>Appointments</span>
                            </a>
                            <a href="{{ route('messages.notifications') }}" class="card-link">
                                <i class="fas fa-bell"></i>
                                <span>Notifications</span>
                            </a>
                            <a href="#" onclick="alert('Please select a conversation to start a video call')" class="card-link">
                                <i class="fas fa-video"></i>
                                <span>Video Calls</span>
                            </a>
                        </div>
                    </div>

                    <!-- Advanced Features -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper violet">
                                <i class="fas fa-rocket"></i>
                            </div>
                            <h5>Advanced</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('metaverse.index') }}" class="card-link">
                                <i class="fas fa-vr-cardboard"></i>
                                <span>Metaverse</span>
                            </a>
                            <a href="{{ route('defi.dashboard.index') }}" class="card-link">
                                <i class="fas fa-chart-line"></i>
                                <span>DeFi Platform</span>
                            </a>
                            <a href="{{ route('bigdata.predictive-ai') }}" class="card-link">
                                <i class="fas fa-database"></i>
                                <span>Big Data</span>
                            </a>
                            <a href="{{ route('ai.dashboard') }}" class="card-link">
                                <i class="fas fa-robot"></i>
                                <span>AI Dashboard</span>
                            </a>
                            <a href="{{ route('blockchain.index') }}" class="card-link">
                                <i class="fas fa-cubes"></i>
                                <span>Blockchain Hub</span>
                            </a>
                        </div>
                    </div>

                    <!-- Maintenance Ops -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper red">
                                <i class="fas fa-wrench"></i>
                            </div>
                            <h5>Maintenance Ops</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('maintenance.index') }}" class="card-link">
                                <i class="fas fa-hammer"></i>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('maintenance.workorders.index') }}" class="card-link">
                                <i class="fas fa-clipboard-list"></i>
                                <span>Work Orders</span>
                            </a>
                            <a href="{{ route('maintenance.schedule.index') }}" class="card-link">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Schedule</span>
                            </a>
                            <a href="{{ route('maintenance.teams.index') }}" class="card-link">
                                <i class="fas fa-users"></i>
                                <span>Teams</span>
                            </a>
                            <a href="{{ route('warranties.index') }}" class="card-link">
                                <i class="fas fa-shield-alt"></i>
                                <span>Warranties</span>
                            </a>
                        </div>
                    </div>

                    <!-- Project Management -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper blue">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                            <h5>Project Mgmt</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('projects.dashboard') }}" class="card-link">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('projects.index') }}" class="card-link">
                                <i class="fas fa-list"></i>
                                <span>All Projects</span>
                            </a>
                            <a href="{{ route('projects.gantt.dashboard') }}" class="card-link">
                                <i class="fas fa-stream"></i>
                                <span>Gantt Charts</span>
                            </a>
                            <a href="{{ route('projects.milestones.dashboard') }}" class="card-link">
                                <i class="fas fa-flag"></i>
                                <span>Milestones</span>
                            </a>
                            <a href="{{ route('projects.budgets.index', ['project' => 1]) }}" class="card-link">
                                <i class="fas fa-calculator"></i>
                                <span>Budgets</span>
                            </a>
                        </div>
                    </div>

                    <!-- IoT & Smart Living -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper cyan">
                                <i class="fas fa-wifi"></i>
                            </div>
                            <h5>IoT & Smart Living</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('iot.dashboard') }}" class="card-link">
                                <i class="fas fa-home"></i>
                                <span>IoT Dashboard</span>
                            </a>
                            <a href="{{ route('iot.devices.index') }}" class="card-link">
                                <i class="fas fa-microchip"></i>
                                <span>Devices</span>
                            </a>
                            <a href="{{ route('iot.automations.index') }}" class="card-link">
                                <i class="fas fa-cogs"></i>
                                <span>Automations</span>
                            </a>
                            <a href="{{ route('iot.energy.dashboard') }}" class="card-link">
                                <i class="fas fa-bolt"></i>
                                <span>Energy Monitor</span>
                            </a>
                        </div>
                    </div>

                    <!-- Developer Hub -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper purple">
                                <i class="fas fa-code-branch"></i>
                            </div>
                            <h5>Developer Hub</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('developer.stats') }}" class="card-link">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('developer.projects.index') }}" class="card-link">
                                <i class="fas fa-project-diagram"></i>
                                <span>Projects</span>
                            </a>
                            <a href="{{ route('developer.portfolios.index') }}" class="card-link">
                                <i class="fas fa-folder-open"></i>
                                <span>Portfolios</span>
                            </a>
                            <a href="{{ route('developer.permits.index') }}" class="card-link">
                                <i class="fas fa-file-signature"></i>
                                <span>Permits</span>
                            </a>
                            <a href="{{ route('developer.profile.show') }}" class="card-link">
                                <i class="fas fa-id-card"></i>
                                <span>Dev Profile</span>
                            </a>
                        </div>
                    </div>

                    <!-- Inventory System -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper orange">
                                <i class="fas fa-boxes"></i>
                            </div>
                            <h5>Inventory System</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('inventory.index') }}" class="card-link">
                                <i class="fas fa-warehouse"></i>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('inventory.items.index') }}" class="card-link">
                                <i class="fas fa-box-open"></i>
                                <span>Items</span>
                            </a>
                            <a href="{{ route('inventory.categories.index') }}" class="card-link">
                                <i class="fas fa-tags"></i>
                                <span>Categories</span>
                            </a>
                            <a href="{{ route('inventory.movements.index') }}" class="card-link">
                                <i class="fas fa-dolly"></i>
                                <span>Movements</span>
                            </a>
                            <a href="{{ route('inventory.suppliers.index') }}" class="card-link">
                                <i class="fas fa-truck"></i>
                                <span>Suppliers</span>
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
                            <a href="{{ route('agents.dashboard') }}" class="card-link">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Agent Dash</span>
                            </a>
                            <a href="{{ route('agents.performance') }}" class="card-link">
                                <i class="fas fa-chart-line"></i>
                                <span>Performance</span>
                            </a>
                            <a href="{{ route('agents.ranking') }}" class="card-link">
                                <i class="fas fa-trophy"></i>
                                <span>Ranking</span>
                            </a>
                            <a href="{{ route('agents.goals') }}" class="card-link">
                                <i class="fas fa-bullseye"></i>
                                <span>Goals</span>
                            </a>
                            <a href="{{ route('agents.directory.full') }}" class="card-link">
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
                            <a href="{{ route('gamification.dashboard') }}" class="card-link">
                                <i class="fas fa-gamepad"></i>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('gamification.achievements') }}" class="card-link">
                                <i class="fas fa-medal"></i>
                                <span>Achievements</span>
                            </a>
                            <a href="{{ route('gamification.badges') }}" class="card-link">
                                <i class="fas fa-certificate"></i>
                                <span>Badges</span>
                            </a>
                            <a href="{{ route('gamification.leaderboard') }}" class="card-link">
                                <i class="fas fa-crown"></i>
                                <span>Leaderboard</span>
                            </a>
                            <a href="{{ route('gamification.rewards') }}" class="card-link">
                                <i class="fas fa-gift"></i>
                                <span>Rewards</span>
                            </a>
                        </div>
                    </div>

                    <!-- Documents & Legal -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper slate">
                                <i class="fas fa-file-contract"></i>
                            </div>
                            <h5>Documents & Legal</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('documents.index') }}" class="card-link">
                                <i class="fas fa-folder-open"></i>
                                <span>All Documents</span>
                            </a>
                            <a href="{{ route('documents.templates.index') }}" class="card-link">
                                <i class="fas fa-file-alt"></i>
                                <span>Templates</span>
                            </a>
                            <a href="{{ route('documents.compliance.index') }}" class="card-link">
                                <i class="fas fa-gavel"></i>
                                <span>Compliance</span>
                            </a>
                            <a href="{{ route('messages.contracts.index') }}" class="card-link">
                                <i class="fas fa-signature"></i>
                                <span>Contracts</span>
                            </a>
                        </div>
                    </div>

                    <!-- Property Settings -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper indigo">
                                <i class="fas fa-sliders-h"></i>
                            </div>
                            <h5>Property Settings</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('property-types.index') }}" class="card-link">
                                <i class="fas fa-home"></i>
                                <span>Property Types</span>
                            </a>
                            <a href="{{ route('property-types.create') }}" class="card-link">
                                <i class="fas fa-plus-circle"></i>
                                <span>Add Type</span>
                            </a>
                            <a href="{{ route('properties.features.index', ['property' => 1]) }}" class="card-link">
                                <i class="fas fa-list-ul"></i>
                                <span>Features</span>
                            </a>
                            <a href="{{ route('properties.prices.index', ['property' => 1]) }}" class="card-link">
                                <i class="fas fa-dollar-sign"></i>
                                <span>Pricing Config</span>
                            </a>
                        </div>
                    </div>

                    <!-- Enterprise B2B -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper gray">
                                <i class="fas fa-building"></i>
                            </div>
                            <h5>Enterprise B2B</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('enterprise.dashboard') }}" class="card-link">
                                <i class="fas fa-chart-pie"></i>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('enterprise.accounts') }}" class="card-link">
                                <i class="fas fa-users-cog"></i>
                                <span>Accounts</span>
                            </a>
                            <a href="{{ route('enterprise.subscriptions') }}" class="card-link">
                                <i class="fas fa-file-invoice-dollar"></i>
                                <span>Subscriptions</span>
                            </a>
                            <a href="{{ route('enterprise.reports') }}" class="card-link">
                                <i class="fas fa-file-alt"></i>
                                <span>Reports</span>
                            </a>
                        </div>
                    </div>

                    <!-- Ad Management -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper pink">
                                <i class="fas fa-ad"></i>
                            </div>
                            <h5>Ad Management</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('admin.banner-ads.index') }}" class="card-link">
                                <i class="fas fa-list"></i>
                                <span>All Banner Ads</span>
                            </a>
                            <a href="{{ route('admin.banner-ads.create') }}" class="card-link">
                                <i class="fas fa-plus-square"></i>
                                <span>Create Banner</span>
                            </a>
                            <a href="#" onclick="alert('Analytics')" class="card-link">
                                <i class="fas fa-chart-line"></i>
                                <span>Performance</span>
                            </a>
                            <a href="#" onclick="alert('Tracking')" class="card-link">
                                <i class="fas fa-mouse-pointer"></i>
                                <span>Click Tracking</span>
                            </a>
                        </div>
                    </div>

                    <!-- Blockchain Pro -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper violet">
                                <i class="fas fa-cube"></i>
                            </div>
                            <h5>Blockchain Pro</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('blockchain.wallets.index') }}" class="card-link">
                                <i class="fas fa-wallet"></i>
                                <span>Crypto Wallets</span>
                            </a>
                            <a href="{{ route('blockchain.transactions.index') }}" class="card-link">
                                <i class="fas fa-exchange-alt"></i>
                                <span>Transactions</span>
                            </a>
                            <a href="{{ route('blockchain.smartcontracts.index') }}" class="card-link">
                                <i class="fas fa-file-contract w-5"></i>
                                <span>Smart Contracts</span>
                            </a>
                            <a href="{{ route('blockchain.nfts.index') }}" class="card-link">
                                <i class="fas fa-image"></i>
                                <span>NFTs</span>
                            </a>
                            <a href="{{ route('blockchain.dao.index') }}" class="card-link">
                                <i class="fas fa-users"></i>
                                <span>DAO</span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Extended Modules -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 mt-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-2xl font-bold text-gray-900">Extended Modules</h3>
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <i class="fas fa-cubes"></i>
                        <span>All Features</span>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <!-- Content CMS -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper orange">
                                <i class="fas fa-pen-nib"></i>
                            </div>
                            <h5>Content CMS</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('admin.content.dashboard') }}" class="card-link">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>CMS Dash</span>
                            </a>
                            <a href="{{ route('admin.blog.posts.index') }}" class="card-link">
                                <i class="fas fa-newspaper"></i>
                                <span>Blog Posts</span>
                            </a>
                            <a href="{{ route('admin.pages.index') }}" class="card-link">
                                <i class="fas fa-file-code"></i>
                                <span>Static Pages</span>
                            </a>
                            <a href="{{ route('admin.news.index') }}" class="card-link">
                                <i class="fas fa-bullhorn"></i>
                                <span>News Central</span>
                            </a>
                            <a href="{{ route('admin.guides.index') }}" class="card-link">
                                <i class="fas fa-map-signs"></i>
                                <span>User Guides</span>
                            </a>
                        </div>
                    </div>

                    <!-- Content Tools -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper orange">
                                <i class="fas fa-tools"></i>
                            </div>
                            <h5>Content Tools</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('admin.faqs.index') }}" class="card-link">
                                <i class="fas fa-question-circle"></i>
                                <span>FAQs</span>
                            </a>
                            <a href="{{ route('admin.media.index') }}" class="card-link">
                                <i class="fas fa-images"></i>
                                <span>Media Lib</span>
                            </a>
                            <a href="{{ route('admin.menus.index') }}" class="card-link">
                                <i class="fas fa-bars"></i>
                                <span>Menus</span>
                            </a>
                            <a href="{{ route('admin.widgets.index') }}" class="card-link">
                                <i class="fas fa-th-large"></i>
                                <span>Widgets</span>
                            </a>
                            <a href="{{ route('testimonials.index') }}" class="card-link">
                                <i class="fas fa-quote-right"></i>
                                <span>Testimonials</span>
                            </a>
                        </div>
                    </div>

                    <!-- Maintenance Ops -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper red">
                                <i class="fas fa-wrench"></i>
                            </div>
                            <h5>Maintenance Ops</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('maintenance.index') }}" class="card-link">
                                <i class="fas fa-hammer"></i>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('maintenance.workorders.index') }}" class="card-link">
                                <i class="fas fa-tasks"></i>
                                <span>Work Orders</span>
                            </a>
                            <a href="{{ route('maintenance.schedule.index') }}" class="card-link">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Schedule</span>
                            </a>
                            <a href="{{ route('maintenance.teams.index') }}" class="card-link">
                                <i class="fas fa-users"></i>
                                <span>Teams</span>
                            </a>
                            <a href="{{ route('maintenance.reports') }}" class="card-link">
                                <i class="fas fa-chart-bar"></i>
                                <span>Reports</span>
                            </a>
                        </div>
                    </div>

                    <!-- Warranty Mgmt -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper red">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h5>Warranty Mgmt</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('warranties.index') }}" class="card-link">
                                <i class="fas fa-certificate"></i>
                                <span>Warranties</span>
                            </a>
                            <a href="{{ route('warranties.policies.index') }}" class="card-link">
                                <i class="fas fa-file-contract"></i>
                                <span>Policies</span>
                            </a>
                            <a href="{{ route('warranties.claims.index') }}" class="card-link">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span>Claims</span>
                            </a>
                            <a href="{{ route('warranties.providers.index') }}" class="card-link">
                                <i class="fas fa-handshake"></i>
                                <span>Providers</span>
                            </a>
                            <a href="{{ route('warranties.reports') }}" class="card-link">
                                <i class="fas fa-chart-pie"></i>
                                <span>Reports</span>
                            </a>
                        </div>
                    </div>

                    <!-- System Maint -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper red">
                                <i class="fas fa-cogs"></i>
                            </div>
                            <h5>System Maint</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('admin.maintenance') }}" class="card-link">
                                <i class="fas fa-tools"></i>
                                <span>System Maint.</span>
                            </a>
                             <a href="{{ route('admin.system.updates') }}" class="card-link">
                                <i class="fas fa-sync"></i>
                                <span>Updates</span>
                            </a>
                        </div>
                    </div>

                    <!-- Smart Living -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper cyan">
                                <i class="fas fa-wifi"></i>
                            </div>
                            <h5>Smart Living</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('iot.dashboard') }}" class="card-link">
                                <i class="fas fa-home"></i>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('iot.devices.index') }}" class="card-link">
                                <i class="fas fa-microchip"></i>
                                <span>Devices</span>
                            </a>
                            <a href="{{ route('iot.automations.index') }}" class="card-link">
                                <i class="fas fa-cogs"></i>
                                <span>Automations</span>
                            </a>
                            <a href="{{ route('iot.energy.dashboard') }}" class="card-link">
                                <i class="fas fa-bolt"></i>
                                <span>Energy</span>
                            </a>
                        </div>
                    </div>

                    <!-- Developer Hub -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper purple">
                                <i class="fas fa-code-branch"></i>
                            </div>
                            <h5>Developer Hub</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('developer.stats') }}" class="card-link">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('developer.projects.index') }}" class="card-link">
                                <i class="fas fa-project-diagram"></i>
                                <span>Projects</span>
                            </a>
                            <a href="{{ route('developer.metaverses.index') }}" class="card-link">
                                <i class="fas fa-vr-cardboard"></i>
                                <span>Metaverses</span>
                            </a>
                            <a href="{{ route('developer.portfolios.index') }}" class="card-link">
                                <i class="fas fa-folder-open"></i>
                                <span>Portfolios</span>
                            </a>
                            <a href="{{ route('developer.milestones.index') }}" class="card-link">
                                <i class="fas fa-flag-checkered"></i>
                                <span>Milestones</span>
                            </a>
                        </div>
                    </div>

                    <!-- Dev Projects -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper purple">
                                <i class="fas fa-laptop-code"></i>
                            </div>
                            <h5>Dev Projects</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('developer.permits.index') }}" class="card-link">
                                <i class="fas fa-file-signature"></i>
                                <span>Permits</span>
                            </a>
                            <a href="{{ route('developer.phases.index') }}" class="card-link">
                                <i class="fas fa-layer-group"></i>
                                <span>Phases</span>
                            </a>
                            <a href="{{ route('developer.units.index', ['project' => 1]) }}" class="card-link">
                                <i class="fas fa-th"></i>
                                <span>Units</span>
                            </a>
                            <a href="{{ route('developer.profile.show') }}" class="card-link">
                                <i class="fas fa-id-card"></i>
                                <span>Dev Profile</span>
                            </a>
                        </div>
                    </div>

                    <!-- Inventory Core -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper orange">
                                <i class="fas fa-boxes"></i>
                            </div>
                            <h5>Inventory Core</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('inventory.index') }}" class="card-link">
                                <i class="fas fa-warehouse"></i>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('inventory.items.index') }}" class="card-link">
                                <i class="fas fa-box-open"></i>
                                <span>Items</span>
                            </a>
                            <a href="{{ route('inventory.categories.index') }}" class="card-link">
                                <i class="fas fa-tags"></i>
                                <span>Categories</span>
                            </a>
                            <a href="{{ route('inventory.movements.index') }}" class="card-link">
                                <i class="fas fa-dolly"></i>
                                <span>Movements</span>
                            </a>
                            <a href="{{ route('inventory.suppliers.index') }}" class="card-link">
                                <i class="fas fa-truck"></i>
                                <span>Suppliers</span>
                            </a>
                        </div>
                    </div>

                    <!-- Inventory Ops -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper orange">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <h5>Inventory Ops</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('inventory.low-stock') }}" class="card-link">
                                <i class="fas fa-exclamation-circle"></i>
                                <span>Low Stock</span>
                            </a>
                            <a href="{{ route('inventory.reports') }}" class="card-link">
                                <i class="fas fa-clipboard-list"></i>
                                <span>Reports</span>
                            </a>
                        </div>
                    </div>

                    <!-- Gamification -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper orange">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <h5>Gamification</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('gamification.dashboard') }}" class="card-link">
                                <i class="fas fa-gamepad"></i>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('gamification.achievements') }}" class="card-link">
                                <i class="fas fa-medal"></i>
                                <span>Achievements</span>
                            </a>
                            <a href="{{ route('gamification.badges') }}" class="card-link">
                                <i class="fas fa-certificate"></i>
                                <span>Badges</span>
                            </a>
                            <a href="{{ route('gamification.leaderboard') }}" class="card-link">
                                <i class="fas fa-list-ol"></i>
                                <span>Leaderboard</span>
                            </a>
                        </div>
                    </div>

                    <!-- Finance Core -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper red">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <h5>Finance Core</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('admin.financial.expenses') }}" class="card-link">
                                <i class="fas fa-money-bill-wave"></i>
                                <span>Expenses</span>
                            </a>
                            <a href="{{ route('payments.invoices.index') }}" class="card-link">
                                <i class="fas fa-file-invoice"></i>
                                <span>Invoices</span>
                            </a>
                            <a href="{{ route('wallet.index') }}" class="card-link">
                                <i class="fas fa-wallet"></i>
                                <span>My Wallet</span>
                            </a>
                             <a href="{{ route('payments.wallets.index') }}" class="card-link">
                                <i class="fas fa-wallet"></i>
                                <span>Sys Wallets</span>
                            </a>
                            <a href="{{ route('payments.escrow.index') }}" class="card-link">
                                <i class="fas fa-hand-holding-usd"></i>
                                <span>Escrow</span>
                            </a>
                        </div>
                    </div>

                    <!-- Planning & Subs -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper green">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h5>Planning & Subs</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('subscriptions.plans.index') }}" class="card-link">
                                <i class="fas fa-tags"></i>
                                <span>Sub Plans</span>
                            </a>
                             <a href="{{ route('subscriptions.plans.stats') }}" class="card-link">
                                <i class="fas fa-chart-area"></i>
                                <span>Plan Stats</span>
                            </a>
                            <a href="{{ route('subscriptions.plans.admin-compare') }}" class="card-link">
                                <i class="fas fa-balance-scale"></i>
                                <span>Compare</span>
                            </a>
                            <a href="{{ route('financial.roi.main') }}" class="card-link">
                                <i class="fas fa-chart-line"></i>
                                <span>ROI Calc</span>
                            </a>
                            <a href="{{ route('financial.scenarios.index') }}" class="card-link">
                                <i class="fas fa-random"></i>
                                <span>Scenarios</span>
                            </a>
                        </div>
                    </div>

                    <!-- Fin Analysis -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper orange">
                                <i class="fas fa-chart-pie"></i>
                            </div>
                            <h5>Fin Analysis</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('financial.dashboard') }}" class="card-link">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('financial.cash_flow.index') }}" class="card-link">
                                <i class="fas fa-stream"></i>
                                <span>Cash Flow</span>
                            </a>
                            <a href="{{ route('financial.valuation.index') }}" class="card-link">
                                <i class="fas fa-tag"></i>
                                <span>Valuation</span>
                            </a>
                            <a href="{{ route('financial.portfolio.index') }}" class="card-link">
                                <i class="fas fa-briefcase"></i>
                                <span>Portfolio</span>
                            </a>
                            <a href="{{ route('financial.tax_benefits.index') }}" class="card-link">
                                <i class="fas fa-hand-holding-usd"></i>
                                <span>Tax Benefits</span>
                            </a>
                        </div>
                    </div>

                    <!-- Tax Compliance -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper blue">
                                <i class="fas fa-file-invoice-dollar"></i>
                            </div>
                            <h5>Tax Compliance</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('taxes.dashboard') }}" class="card-link">
                                <i class="fas fa-columns"></i>
                                <span>Tax Dash</span>
                            </a>
                            <a href="{{ route('taxes.property.index') }}" class="card-link">
                                <i class="fas fa-home"></i>
                                <span>Prop Taxes</span>
                            </a>
                            <a href="{{ route('taxes.filings.index') }}" class="card-link">
                                <i class="fas fa-file-signature"></i>
                                <span>Tax Filings</span>
                            </a>
                            <a href="{{ route('taxes.payments.index') }}" class="card-link">
                                <i class="fas fa-credit-card"></i>
                                <span>Payments</span>
                            </a>
                            <a href="{{ route('taxes.reports.index') }}" class="card-link">
                                <i class="fas fa-file-alt"></i>
                                <span>Tax Reports</span>
                            </a>
                        </div>
                    </div>

                    <!-- Tax Tools -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper blue">
                                <i class="fas fa-calculator"></i>
                            </div>
                            <h5>Tax Tools</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('taxes.index') }}" class="card-link">
                                <i class="fas fa-calculator"></i>
                                <span>Tax Mgmt</span>
                            </a>
                             <a href="{{ route('taxes.reports.audit') }}" class="card-link">
                                <i class="fas fa-file-invoice-dollar"></i>
                                <span>Tax Audits</span>
                            </a>
                            <a href="{{ route('taxes.vat.index') }}" class="card-link">
                                <i class="fas fa-receipt"></i>
                                <span>VAT Records</span>
                            </a>
                        </div>
                    </div>

                    <!-- Marketing Core -->
                    <div class="premium-card">
                        <div class="card-header">
                            <div class="icon-wrapper pink">
                                <i class="fas fa-bullhorn"></i>
                            </div>
                            <h5>Marketing Core</h5>
                        </div>
                        <div class="card-divider"></div>
                        <div class="card-content">
                            <a href="{{ route('marketing.index') }}" class="card-link">
                                <i class="fas fa-chart-line"></i>
                                <span>Dashboard</span>
                            </a>
                            <a href="{{ route('subscriptions.plans.admin-compare') }}" class="card-link">
                                <i class="fas fa-balance-scale"></i>
                                <span>Compare</span>
                            </a>
                            <a href="{{ route('taxes.index') }}" class="card-link">
                                <i class="fas fa-calculator"></i>
                                <span>Tax Mgmt</span>
                            </a>
                             <a href="{{ route('taxes.reports.audit') }}" class="card-link">
                                <i class="fas fa-file-invoice-dollar"></i>
                                <span>Tax Audits</span>
                            </a>
                            <a href="{{ route('financial.roi.main') }}" class="card-link">
                                <i class="fas fa-chart-line"></i>
                                <span>ROI Calc</span>
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
                            <a href="{{ route('marketing.campaigns') }}" class="card-link">
                                <i class="fas fa-ad"></i>
                                <span>Campaigns</span>
                            </a>
                            <a href="{{ route('admin.settings.seo') }}" class="card-link">
                                <i class="fas fa-cogs"></i>
                                <span>Global SEO</span>
                            </a>
                            <a href="{{ route('admin.seo.index') }}" class="card-link">
                                <i class="fas fa-search"></i>
                                <span>SEO Mgmt</span>
                            </a>
                            <a href="{{ route('admin.seo.keywords') }}" class="card-link">
                                <i class="fas fa-key"></i>
                                <span>Keywords</span>
                            </a>
                            <a href="{{ route('admin.seo.meta.tags') }}" class="card-link">
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
                            <a href="{{ route('admin.seo.robots') }}" class="card-link">
                                <i class="fas fa-robot"></i>
                                <span>Robots.txt</span>
                            </a>
                            <a href="{{ route('admin.seo.analyze') }}" class="card-link">
                                <i class="fas fa-microscope"></i>
                                <span>Analysis</span>
                            </a>
                             <a href="{{ route('admin.seo.test') }}" class="card-link">
                                <i class="fas fa-vial"></i>
                                <span>SEO Test</span>
                            </a>
                            <a href="{{ route('admin.seo.sitemap') }}" class="card-link">
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
                            <a href="{{ route('admin.banner-ads.index') }}" class="card-link">
                                <i class="fas fa-list"></i>
                                <span>All Ads</span>
                            </a>
                            <a href="{{ route('admin.banner-ads.create') }}" class="card-link">
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
                            <a href="{{ route('analytics.overview') }}" class="card-link">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Overview</span>
                            </a>
                            <a href="{{ route('analytics.real-time') }}" class="card-link">
                                <i class="fas fa-clock"></i>
                                <span>Real-Time</span>
                            </a>
                            <a href="{{ route('analytics.behavior.index') }}" class="card-link">
                                <i class="fas fa-user-tag"></i>
                                <span>Behavior</span>
                            </a>
                            <a href="{{ route('analytics.heatmap.index') }}" class="card-link">
                                <i class="fas fa-fire"></i>
                                <span>Heatmaps</span>
                            </a>
                             <a href="{{ route('geospatial.analytics.index') }}" class="card-link">
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
                            <a href="{{ route('geospatial.demographics.index') }}" class="card-link">
                                <i class="fas fa-users"></i>
                                <span>Demographics</span>
                            </a>
                            <a href="{{ route('analytics.market.trends') }}" class="card-link">
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
                            <a href="{{ route('reports.index') }}" class="card-link">
                                <i class="fas fa-copy"></i>
                                <span>Center</span>
                            </a>
                            <a href="{{ route('reports.sales.index') }}" class="card-link">
                                <i class="fas fa-piggy-bank"></i>
                                <span>Sales</span>
                            </a>
                            <a href="{{ route('admin.reports.agent.performance') }}" class="card-link">
                                <i class="fas fa-user-tie"></i>
                                <span>Agent Perf</span>
                            </a>
                            <a href="{{ route('admin.reports.client.analytics') }}" class="card-link">
                                <i class="fas fa-users"></i>
                                <span>Client Analytics</span>
                            </a>
                            <a href="{{ route('admin.reports.property.performance') }}" class="card-link">
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
                            <a href="{{ route('admin.performance.system') }}" class="card-link">
                                <i class="fas fa-rocket"></i>
                                <span>Sys Perf</span>
                            </a>
                            <a href="{{ route('admin.reports.custom') }}" class="card-link">
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
                            <a href="{{ route('surveys.index') }}" class="card-link">
                                <i class="fas fa-poll-h"></i>
                                <span>All Surveys</span>
                            </a>
                            <a href="{{ route('surveys.create') }}" class="card-link">
                                <i class="fas fa-plus-square"></i>
                                <span>Create</span>
                            </a>
                            <a href="{{ route('reviews.index') }}" class="card-link">
                                <i class="fas fa-star"></i>
                                <span>Reviews</span>
                            </a>
                            <a href="{{ route('requests.index') }}" class="card-link">
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
                            <a href="{{ route('messages.inbox') }}" class="card-link">
                                <i class="fas fa-inbox"></i>
                                <span>Inbox</span>
                            </a>
                            <a href="{{ route('messages.chat') }}" class="card-link">
                                <i class="fas fa-comment-dots"></i>
                                <span>Live Chat</span>
                            </a>
                            <a href="{{ route('messages.appointments') }}" class="card-link">
                                <i class="fas fa-calendar-check"></i>
                                <span>Appointments</span>
                            </a>
                            <a href="{{ route('messages.notifications') }}" class="card-link">
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
                            <a href="{{ route('messages.offers.index') }}" class="card-link">
                                <i class="fas fa-tag"></i>
                                <span>Offers</span>
                            </a>
                            <a href="{{ route('messages.negotiations.index') }}" class="card-link">
                                <i class="fas fa-hand-holding-usd"></i>
                                <span>Negotiations</span>
                            </a>
                            <a href="{{ route('messages.contracts.index') }}" class="card-link">
                                <i class="fas fa-file-signature"></i>
                                <span>Contracts</span>
                            </a>
                            <a href="{{ route('messages.auctions.index') }}" class="card-link">
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
                            <a href="{{ route('admin.system.dashboard') }}" class="card-link">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>System Dash</span>
                            </a>
                            <a href="{{ route('admin.system.monitoring') }}" class="card-link">
                                <i class="fas fa-heartbeat"></i>
                                <span>Monitoring</span>
                            </a>
                            <a href="{{ route('admin.system.database') }}" class="card-link">
                                <i class="fas fa-database"></i>
                                <span>Database</span>
                            </a>
                            <a href="{{ route('admin.backups') }}" class="card-link">
                                <i class="fas fa-save"></i>
                                <span>Backups</span>
                            </a>
                            <a href="{{ route('admin.system.logs') }}" class="card-link">
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
                            <a href="{{ route('admin.system.queue') }}" class="card-link">
                                <i class="fas fa-tasks"></i>
                                <span>Queues</span>
                            </a>
                            <a href="{{ route('admin.system.storage') }}" class="card-link">
                                <i class="fas fa-hdd"></i>
                                <span>Storage</span>
                            </a>
                            <a href="{{ route('routes.index') }}" class="card-link">
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
                            <a href="{{ route('admin.settings.general') }}" class="card-link">
                                <i class="fas fa-sliders-h"></i>
                                <span>General</span>
                            </a>
                            <a href="{{ route('modules.dashboard') }}" class="card-link">
                                <i class="fas fa-puzzle-piece"></i>
                                <span>Modules</span>
                            </a>
                            <a href="{{ route('admin.settings.email') }}" class="card-link">
                                <i class="fas fa-envelope"></i>
                                <span>Email</span>
                            </a>
                            <a href="{{ route('admin.settings.payment') }}" class="card-link">
                                <i class="fas fa-credit-card"></i>
                                <span>Payment</span>
                            </a>
                            <a href="{{ route('admin.settings.social') }}" class="card-link">
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
                            <a href="{{ route('admin.settings.security') }}" class="card-link">
                                <i class="fas fa-lock"></i>
                                <span>Security</span>
                            </a>
                            <a href="{{ route('settings.index') }}" class="card-link">
                                <i class="fas fa-user-cog"></i>
                                <span>My Prefs</span>
                            </a>
                            <a href="{{ route('social.accounts') }}" class="card-link">
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
                            <a href="{{ route('currency.index') }}" class="card-link">
                                <i class="fas fa-coins"></i>
                                <span>Currency</span>
                            </a>
                            <a href="{{ route('currency.converter') }}" class="card-link">
                                <i class="fas fa-exchange-alt"></i>
                                <span>Converter</span>
                            </a>
                            <a href="{{ route('language.index') }}" class="card-link">
                                <i class="fas fa-language"></i>
                                <span>Languages</span>
                            </a>
                            <a href="{{ route('admin.performance.system') }}" class="card-link">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Performance</span>
                            </a>
                            <a href="{{ route('admin.system.dashboard') }}" class="card-link">
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
                            <a href="{{ route('metaverse.index') }}" class="card-link">
                                <i class="fas fa-vr-cardboard"></i>
                                <span>Metaverse</span>
                            </a>
                            <a href="{{ route('defi.dashboard.index') }}" class="card-link">
                                <i class="fas fa-chart-line"></i>
                                <span>DeFi Plat.</span>
                            </a>
                            <a href="{{ route('bigdata.predictive-ai') }}" class="card-link">
                                <i class="fas fa-database"></i>
                                <span>Big Data</span>
                            </a>
                            <a href="{{ route('geospatial.analytics.index') }}" class="card-link">
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
                            <a href="{{ route('documents.index') }}" class="card-link">
                                <i class="fas fa-folder-open"></i>
                                <span>All Docs</span>
                            </a>
                            <a href="{{ route('documents.templates.index') }}" class="card-link">
                                <i class="fas fa-file-alt"></i>
                                <span>Templates</span>
                            </a>
                            <a href="{{ route('documents.compliance.index') }}" class="card-link">
                                <i class="fas fa-gavel"></i>
                                <span>Legal Docs</span>
                            </a>
                            <a href="{{ route('messages.contracts.index') }}" class="card-link">
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
                            <a href="{{ route('blockchain.wallets.index') }}" class="card-link">
                                <i class="fas fa-link"></i>
                                <span>Block. Hub</span>
                            </a>
                            <a href="{{ route('blockchain.wallets.index') }}" class="card-link">
                                <i class="fas fa-wallet"></i>
                                <span>Crypto Wallets</span>
                            </a>
                            <a href="{{ route('blockchain.transactions.index') }}" class="card-link">
                                <i class="fas fa-exchange-alt"></i>
                                <span>Transactions</span>
                            </a>
                            <a href="{{ route('blockchain.smartcontracts.index') }}" class="card-link">
                                <i class="fas fa-file-contract"></i>
                                <span>Smart Contr.</span>
                            </a>
                            <a href="{{ route('blockchain.nfts.index') }}" class="card-link">
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
                            <a href="{{ route('blockchain.dao.index') }}" class="card-link">
                                <i class="fas fa-users"></i>
                                <span>DAO</span>
                            </a>
                            <a href="{{ route('blockchain.defi.index') }}" class="card-link">
                                <i class="fas fa-chart-line"></i>
                                <span>DeFi</span>
                            </a>
                            <a href="{{ route('blockchain.liquidity-pools.index') }}" class="card-link">
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
                            <a href="{{ route('ai.dashboard') }}" class="card-link">
                                <i class="fas fa-robot"></i>
                                <span>AI Dash</span>
                            </a>
                            <a href="{{ route('ai.chat') }}" class="card-link">
                                <i class="fas fa-comments"></i>
                                <span>AI Chat</span>
                            </a>
                            <a href="{{ route('ai.valuation.dashboard') }}" class="card-link">
                                <i class="fas fa-home"></i>
                                <span>Prop Valuation</span>
                            </a>
                            <a href="{{ route('ai.price-predictor.dashboard') }}" class="card-link">
                                <i class="fas fa-chart-line"></i>
                                <span>Price Pred.</span>
                            </a>
                            <a href="{{ route('ai.description-generator.dashboard') }}" class="card-link">
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
                            <a href="{{ route('ai.chatbot.dashboard') }}" class="card-link">
                                <i class="fas fa-comment-dots"></i>
                                <span>Chatbot</span>
                            </a>
                            <a href="{{ route('ai.fraud-detection.index') }}" class="card-link">
                                <i class="fas fa-shield-alt"></i>
                                <span>Fraud Detect</span>
                            </a>
                            <a href="{{ route('ai.investment-advisor.index') }}" class="card-link">
                                <i class="fas fa-lightbulb"></i>
                                <span>Invest Advisor</span>
                            </a>
                            <a href="{{ route('ai.market-analysis.index') }}" class="card-link">
                                <i class="fas fa-chart-bar"></i>
                                <span>Market Anal.</span>
                            </a>
                            <a href="{{ route('ai.virtual-staging.index') }}" class="card-link">
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
                            <a href="{{ route('leads.index') }}" class="card-link">
                                <i class="fas fa-users"></i>
                                <span>All Leads</span>
                            </a>
                            <a href="{{ route('leads.create') }}" class="card-link">
                                <i class="fas fa-user-plus"></i>
                                <span>Add Lead</span>
                            </a>
                            <a href="{{ route('leads.pipeline') }}" class="card-link">
                                <i class="fas fa-filter"></i>
                                <span>Pipeline</span>
                            </a>
                            <a href="{{ route('leads.dashboard') }}" class="card-link">
                                <i class="fas fa-chart-line"></i>
                                <span>Analytics</span>
                            </a>
                            <a href="{{ route('lead-import.index') }}" class="card-link">
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
                            <a href="{{ route('projects.index') }}" class="card-link">
                                <i class="fas fa-list"></i>
                                <span>All Projects</span>
                            </a>
                            <a href="{{ route('projects.create') }}" class="card-link">
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
                            <a href="{{ route('blog.index') }}" class="card-link">
                                <i class="fas fa-globe"></i>
                                <span>View Blog</span>
                            </a>
                            <a href="{{ route('admin.blog.posts.index') }}" class="card-link">
                                <i class="fas fa-file-alt"></i>
                                <span>All Posts</span>
                            </a>
                            <a href="{{ route('admin.blog.posts.create') }}" class="card-link">
                                <i class="fas fa-pen-nib"></i>
                                <span>Write Post</span>
                            </a>
                            <a href="{{ route('admin.blog.categories.index') }}" class="card-link">
                                <i class="fas fa-tags"></i>
                                <span>Categories</span>
                            </a>
                            <a href="{{ route('cms.blog.network') }}" class="card-link">
                                <i class="fas fa-network-wired"></i>
                                <span>Network</span>
                            </a>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Activity Log -->
            <div class="modern-card p-6 mt-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="section-header mb-0">Recent Activity</h3>
                    <button onclick="refreshActivity()" class="w-8 h-8 rounded-full bg-gray-50 hover:bg-gray-100 flex items-center justify-center text-gray-600 transition-all hover:rotate-180">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                </div>

                <div class="space-y-3">
                    @forelse ($stats['recent_activity'] as $activity)
                        <div class="flex items-center p-3 rounded-xl hover:bg-gray-50 border border-transparent hover:border-gray-100 transition-all group cursor-default">
                            <div class="icon-container blue mr-4 group-hover:scale-110 transition-transform">
                                <i class="fas fa-{{ $activity['icon'] }} text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-gray-800">{{ $activity['message'] }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $activity['time'] }}</p>
                            </div>
                            <div class="w-2 h-2 rounded-full bg-blue-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-50 mb-3">
                                <i class="fas fa-history text-gray-400"></i>
                            </div>
                            <p class="text-gray-500 text-sm">No recent activity</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    <!-- Reports Modal -->
    <div id="reportsModal" class="fixed inset-0 modal-overlay flex items-center justify-center z-50 hidden backdrop-blur-sm bg-gray-900/50 transition-opacity">
        <div class="modal-content bg-white rounded-2xl shadow-2xl p-8 max-w-3xl mx-4 w-full transform transition-all scale-100">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">System Reports</h3>
                    <p class="text-sm text-gray-500 mt-1">Generate and download system analytics</p>
                </div>
                <button onclick="closeReportsModal()" class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- User Report -->
                <div class="group p-4 rounded-xl border border-gray-200 hover:border-blue-500 hover:shadow-lg transition-all cursor-pointer bg-white">
                    <div class="flex items-start gap-4">
                        <div class="icon-container blue shrink-0 group-hover:scale-110 transition-transform">
                            <i class="fas fa-users"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 mb-1 group-hover:text-blue-600 transition-colors">User Report</h4>
                            <p class="text-xs text-gray-500 mb-3 leading-relaxed">Detailed user statistics, registration trends, and activity analytics.</p>
                            <button class="text-xs font-bold text-blue-600 hover:text-blue-700 flex items-center gap-1">
                                Generate PDF <i class="fas fa-arrow-right text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Financial Report -->
                <div class="group p-4 rounded-xl border border-gray-200 hover:border-green-500 hover:shadow-lg transition-all cursor-pointer bg-white">
                    <div class="flex items-start gap-4">
                        <div class="icon-container green shrink-0 group-hover:scale-110 transition-transform">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 mb-1 group-hover:text-green-600 transition-colors">Financial Report</h4>
                            <p class="text-xs text-gray-500 mb-3 leading-relaxed">Revenue analysis, payment history, and transaction records.</p>
                            <button class="text-xs font-bold text-green-600 hover:text-green-700 flex items-center gap-1">
                                Generate PDF <i class="fas fa-arrow-right text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Property Report -->
                <div class="group p-4 rounded-xl border border-gray-200 hover:border-orange-500 hover:shadow-lg transition-all cursor-pointer bg-white">
                    <div class="flex items-start gap-4">
                        <div class="icon-container orange shrink-0 group-hover:scale-110 transition-transform">
                            <i class="fas fa-home"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 mb-1 group-hover:text-orange-600 transition-colors">Property Report</h4>
                            <p class="text-xs text-gray-500 mb-3 leading-relaxed">Property listings status, occupancy rates, and performance metrics.</p>
                            <button class="text-xs font-bold text-orange-600 hover:text-orange-700 flex items-center gap-1">
                                Generate PDF <i class="fas fa-arrow-right text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- System Report -->
                <div class="group p-4 rounded-xl border border-gray-200 hover:border-purple-500 hover:shadow-lg transition-all cursor-pointer bg-white">
                    <div class="flex items-start gap-4">
                        <div class="icon-container purple shrink-0 group-hover:scale-110 transition-transform">
                            <i class="fas fa-server"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 mb-1 group-hover:text-purple-600 transition-colors">System Report</h4>
                            <p class="text-xs text-gray-500 mb-3 leading-relaxed">Server health, error logs, and performance benchmarks.</p>
                            <button class="text-xs font-bold text-purple-600 hover:text-purple-700 flex items-center gap-1">
                                Generate PDF <i class="fas fa-arrow-right text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end mt-8 pt-4 border-t border-gray-100">
                <button onclick="closeReportsModal()" class="px-6 py-2.5 bg-gray-100 text-gray-700 font-medium rounded-xl hover:bg-gray-200 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                                label: function(context) {
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
                                callback: function(value) {
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

@endsection