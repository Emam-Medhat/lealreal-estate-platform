@extends('admin.layouts.admin')

@section('title', 'إنشاء الوثائق')

@push('styles')
<style>
    /* ============================================
       DOCUMENTS GENERATION PAGE STYLES
       ============================================ */
    
    /* Root Variables */
    :root {
        --primary-blue: #6366f1;
        --primary-blue-light: #818cf8;
        --secondary-purple: #a855f7;
        --success-green: #10b981;
        --warning-orange: #f59e0b;
        --danger-red: #ef4444;
        
        --bg-body: #f8fafc;
        --bg-card: #ffffff;
        --text-main: #334155;
        --text-muted: #64748b;
        --border-color: #e2e8f0;
        
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.05), 0 1px 2px rgba(0,0,0,0.03);
        --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
        --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.05), 0 4px 6px -2px rgba(0,0,0,0.025);
        --shadow-hover: 0 20px 25px -5px rgba(0,0,0,0.08), 0 10px 10px -5px rgba(0,0,0,0.03);
        
        --radius-md: 12px;
        --radius-lg: 16px;
        --radius-xl: 24px;
        
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Page Background */
    .page-background {
        background-color: var(--bg-body);
        background-image: 
            radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.03) 0px, transparent 50%),
            radial-gradient(at 100% 0%, rgba(168, 85, 247, 0.03) 0px, transparent 50%);
        min-height: 100vh;
    }
    
    /* Modern Cards */
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
    
    /* Stat Cards */
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
    
    /* Icon Containers */
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
    
    /* Template List Items */
    .template-item {
        display: flex;
        align-items: center;
        padding: 16px 20px;
        border-bottom: 1px solid var(--border-color);
        transition: var(--transition);
        text-decoration: none;
        color: var(--text-main);
        position: relative;
        overflow: hidden;
    }
    
    .template-item:hover {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        color: var(--primary-blue);
        transform: translateX(4px);
    }
    
    .template-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(99, 102, 241, 0.05), transparent);
        transition: left 0.5s ease;
    }
    
    .template-item:hover::before {
        left: 100%;
    }
    
    .template-item:last-child {
        border-bottom: none;
    }
    
    /* Action Buttons */
    .action-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--primary-blue);
        color: white;
        transition: var(--transition);
        border: none;
        cursor: pointer;
        font-size: 14px;
    }
    
    .action-btn:hover {
        background: var(--primary-blue-light);
        transform: scale(1.1);
    }
    
    /* Table Styles */
    .modern-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .modern-table thead {
        background: #f8fafc;
    }
    
    .modern-table th {
        padding: 12px 16px;
        text-align: right;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        color: var(--text-muted);
        border-bottom: 2px solid var(--border-color);
    }
    
    .modern-table td {
        padding: 16px;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }
    
    .modern-table tbody tr {
        transition: var(--transition);
    }
    
    .modern-table tbody tr:hover {
        background: #f8fafc;
    }
    
    /* Badges */
    .badge-modern {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        background: #f1f5f9;
        color: var(--text-main);
        border: 1px solid var(--border-color);
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
        right: 0;
        width: 40px;
        height: 3px;
        background: var(--primary-blue);
        border-radius: 2px;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 40px;
    }
    
    .empty-state img {
        opacity: 0.6;
        margin-bottom: 20px;
    }
    
    .empty-state p {
        color: var(--text-muted);
        margin-bottom: 20px;
    }
    
    /* Buttons */
    .btn-primary-modern {
        background: var(--primary-blue);
        color: white;
        padding: 10px 20px;
        border-radius: 10px;
        border: none;
        font-weight: 600;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
    }
    
    .btn-primary-modern:hover {
        background: var(--primary-blue-light);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }
    
    .btn-info-modern {
        background: #e0f2fe;
        color: #0284c7;
        padding: 8px 16px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        font-size: 14px;
    }
    
    .btn-info-modern:hover {
        background: #bae6fd;
        transform: translateY(-1px);
    }
    
    /* Avatar */
    .avatar-small {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        margin-left: 8px;
        border: 2px solid white;
        box-shadow: var(--shadow-sm);
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .stat-card { padding: 20px; }
        .modern-card { margin-bottom: 16px; }
        .template-item { padding: 12px 16px; }
        .modern-table { font-size: 14px; }
        .modern-table th, .modern-table td { padding: 8px 12px; }
    }
</style>
@endpush

@section('content')
<div class="min-h-screen page-background">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">إنشاء الوثائق</h1>
                <p class="text-gray-600">إنشاء وإدارة الوثائق من القوالب المتاحة</p>
            </div>
            <a href="{{ route('document-templates.index') }}" class="btn-primary-modern mt-4 sm:mt-0">
                <i class="fas fa-cogs"></i>
                إدارة القوالب
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Templates Count -->
            <div class="stat-card stat-card-blue">
                <div class="flex items-center justify-between relative z-10">
                    <div>
                        <p class="text-white/90 text-sm font-medium mb-1">القوالب النشطة</p>
                        <p class="text-3xl font-bold mb-2">{{ $templates->count() }}</p>
                        <p class="text-white/80 text-sm flex items-center">
                            <i class="fas fa-file-alt ml-1"></i>
                            قوالب متاحة
                        </p>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-full p-4">
                        <i class="fas fa-file-alt text-2xl text-white"></i>
                    </div>
                </div>
            </div>

            <!-- Today's Generations -->
            <div class="stat-card stat-card-green">
                <div class="flex items-center justify-between relative z-10">
                    <div>
                        <p class="text-white/90 text-sm font-medium mb-1">تم إنشاؤها اليوم</p>
                        <p class="text-3xl font-bold mb-2">{{ $todayGenerations }}</p>
                        <p class="text-white/80 text-sm flex items-center">
                            <i class="fas fa-calendar-day ml-1"></i>
                            وثائق جديدة
                        </p>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-full p-4">
                        <i class="fas fa-calendar-day text-2xl text-white"></i>
                    </div>
                </div>
            </div>

            <!-- Monthly Generations -->
            <div class="stat-card stat-card-orange">
                <div class="flex items-center justify-between relative z-10">
                    <div>
                        <p class="text-white/90 text-sm font-medium mb-1">هذا الشهر</p>
                        <p class="text-3xl font-bold mb-2">{{ $thisMonthGenerations }}</p>
                        <p class="text-white/80 text-sm flex items-center">
                            <i class="fas fa-calendar-alt ml-1"></i>
                            إجمالي الشهر
                        </p>
                    </div>
                    <div class="bg-white/20 backdrop-blur-sm rounded-full p-4">
                        <i class="fas fa-calendar-alt text-2xl text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Generate Document Form -->
            <div class="modern-card">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="section-header m-0">إنشاء وثيقة جديدة</h3>
                        <div class="icon-container blue">
                            <i class="fas fa-plus"></i>
                        </div>
                    </div>
                    @if($templates->isEmpty())
                        <div class="empty-state">
                            <div class="icon-container gray mx-auto mb-4" style="width: 48px; height: 48px; font-size: 20px;">
                                <i class="fas fa-folder-open"></i>
                            </div>
                            <p class="text-gray-500 mb-4">لا توجد قوالب نشطة متاحة.</p>
                            <a href="{{ route('document-templates.create') }}" class="btn-primary-modern">
                                <i class="fas fa-plus"></i>
                                إنشاء قالب جديد
                            </a>
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach($templates as $template)
                                <a href="{{ route('document-templates.generate-form', $template) }}" class="template-item">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between mb-1">
                                            <h4 class="font-semibold text-gray-900 m-0">{{ $template->name }}</h4>
                                            <span class="badge-modern">{{ $template->category ?? 'عام' }}</span>
                                        </div>
                                        <p class="text-sm text-gray-600 m-0">{{ Str::limit($template->description, 100) }}</p>
                                    </div>
                                    <div class="action-btn mr-3">
                                        <i class="fas fa-chevron-left"></i>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Documents -->
            <div class="modern-card">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="section-header m-0">أحدث الوثائق</h3>
                        <div class="flex items-center gap-3">
                            <div class="icon-container green">
                                <i class="fas fa-clock"></i>
                            </div>
                            <a href="{{ route('documents.index') }}" class="btn-info-modern">
                                <i class="fas fa-list"></i>
                                عرض الكل
                            </a>
                        </div>
                    </div>
                    @if($recentDocuments->isEmpty())
                        <div class="empty-state">
                            <div class="icon-container gray mx-auto mb-4" style="width: 48px; height: 48px; font-size: 20px;">
                                <i class="fas fa-file"></i>
                            </div>
                            <p class="text-gray-500">لم يتم إنشاء أي وثائق بعد.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="modern-table">
                                <thead>
                                    <tr>
                                        <th>العنوان</th>
                                        <th>القالب</th>
                                        <th>بواسطة</th>
                                        <th>التاريخ</th>
                                        <th class="text-center">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentDocuments as $doc)
                                        <tr>
                                            <td class="font-semibold text-gray-900">{{ $doc->title }}</td>
                                            <td>
                                                <span class="badge-modern">{{ $doc->template->name ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <div class="flex items-center gap-2">
                                                    @if($doc->createdBy && $doc->createdBy->avatar_url)
                                                        <img class="avatar-small" src="{{ $doc->createdBy->avatar_url }}" alt="Avatar">
                                                    @else
                                                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center">
                                                            <i class="fas fa-user text-gray-500 text-xs"></i>
                                                        </div>
                                                    @endif
                                                    <span class="text-sm text-gray-700">{{ $doc->createdBy->name ?? 'System' }}</span>
                                                </div>
                                            </td>
                                            <td class="text-sm text-gray-600">{{ $doc->created_at->format('Y-m-d') }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('documents.show', $doc) }}" class="action-btn" title="عرض">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection