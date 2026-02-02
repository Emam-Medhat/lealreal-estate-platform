@extends('layouts.app')

@section('title', 'المواعيد')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700;800&family=Cairo:wght@200;300;400;600;700;900&display=swap');
    
    :root {
        --color-primary: #2C3E50;
        --color-secondary: #E74C3C;
        --color-accent: #3498DB;
        --color-success: #27AE60;
        --color-warning: #F39C12;
        --color-muted: #95A5A6;
        --color-bg: #F8F9FA;
        --color-card: #FFFFFF;
        --color-border: #E8ECEF;
        --color-text: #2C3E50;
        --color-text-light: #7F8C8D;
        --shadow-sm: 0 2px 8px rgba(44, 62, 80, 0.04);
        --shadow-md: 0 4px 16px rgba(44, 62, 80, 0.08);
        --shadow-lg: 0 8px 32px rgba(44, 62, 80, 0.12);
        --radius: 16px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    body {
        font-family: 'Cairo', 'Almarai', sans-serif;
        background: linear-gradient(135deg, #F8F9FA 0%, #E8ECEF 100%);
        color: var(--color-text);
        line-height: 1.8;
    }
    
    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, var(--color-primary) 0%, #34495E 100%);
        padding: 4rem 0 8rem;
        position: relative;
        overflow: hidden;
        margin-bottom: -4rem;
    }
    
    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            radial-gradient(circle at 20% 50%, rgba(231, 76, 60, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(52, 152, 219, 0.1) 0%, transparent 50%);
        animation: headerPulse 8s ease-in-out infinite;
    }
    
    @keyframes headerPulse {
        0%, 100% { opacity: 0.5; transform: scale(1); }
        50% { opacity: 0.8; transform: scale(1.05); }
    }
    
    .page-header .container {
        position: relative;
        z-index: 1;
    }
    
    .page-header h1 {
        font-family: 'Almarai', sans-serif;
        font-size: 3.5rem;
        font-weight: 800;
        color: white;
        margin: 0 0 1rem;
        letter-spacing: -0.02em;
        animation: slideDown 0.6s ease-out;
    }
    
    .page-header p {
        font-size: 1.25rem;
        color: rgba(255, 255, 255, 0.9);
        margin: 0 0 2rem;
        animation: slideDown 0.6s ease-out 0.1s both;
    }
    
    .page-header-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        animation: slideDown 0.6s ease-out 0.2s both;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.875rem 1.75rem;
        font-size: 1rem;
        font-weight: 600;
        border-radius: var(--radius);
        border: none;
        cursor: pointer;
        transition: var(--transition);
        text-decoration: none;
        font-family: 'Cairo', sans-serif;
    }
    
    .btn-primary {
        background: white;
        color: var(--color-primary);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    }
    
    .btn-secondary {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        backdrop-filter: blur(10px);
    }
    
    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
    }
    
    .btn-icon {
        width: 1.25rem;
        height: 1.25rem;
    }
    
    /* Stats Cards */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }
    
    .stat-card {
        background: var(--color-card);
        border-radius: var(--radius);
        padding: 2rem;
        box-shadow: var(--shadow-md);
        position: relative;
        overflow: hidden;
        transition: var(--transition);
        animation: fadeInUp 0.6s ease-out both;
    }
    
    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:nth-child(4) { animation-delay: 0.4s; }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg);
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: var(--stat-color);
    }
    
    .stat-card:nth-child(1) { --stat-color: var(--color-primary); }
    .stat-card:nth-child(2) { --stat-color: var(--color-success); }
    .stat-card:nth-child(3) { --stat-color: var(--color-warning); }
    .stat-card:nth-child(4) { --stat-color: var(--color-accent); }
    
    .stat-value {
        font-size: 3rem;
        font-weight: 800;
        color: var(--stat-color);
        line-height: 1;
        margin-bottom: 0.5rem;
        font-family: 'Almarai', sans-serif;
    }
    
    .stat-label {
        font-size: 1rem;
        color: var(--color-text-light);
        font-weight: 500;
    }
    
    /* Appointments Section */
    .appointments-section {
        background: var(--color-card);
        border-radius: var(--radius);
        padding: 2.5rem;
        box-shadow: var(--shadow-md);
        animation: fadeInUp 0.6s ease-out 0.5s both;
    }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1.5rem;
    }
    
    .section-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--color-primary);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 0;
    }
    
    /* Filters */
    .filters {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .filter-group {
        display: flex;
        gap: 0.5rem;
        background: var(--color-bg);
        padding: 0.5rem;
        border-radius: var(--radius);
    }
    
    .filter-btn {
        padding: 0.625rem 1.25rem;
        background: transparent;
        border: none;
        border-radius: calc(var(--radius) - 4px);
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--color-text-light);
        cursor: pointer;
        transition: var(--transition);
        font-family: 'Cairo', sans-serif;
    }
    
    .filter-btn:hover,
    .filter-btn.active {
        background: white;
        color: var(--color-primary);
        box-shadow: var(--shadow-sm);
    }
    
    /* Appointment Cards */
    .appointments-list {
        display: grid;
        gap: 1.5rem;
    }
    
    .appointment-card {
        background: white;
        border: 2px solid var(--color-border);
        border-radius: var(--radius);
        padding: 2rem;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }
    
    .appointment-card:hover {
        border-color: var(--color-primary);
        box-shadow: var(--shadow-md);
        transform: translateX(-4px);
    }
    
    .appointment-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 6px;
        height: 100%;
        background: var(--appointment-color);
        transition: var(--transition);
    }
    
    .appointment-card:hover::before {
        width: 12px;
    }
    
    .appointment-card.status-confirmed { --appointment-color: var(--color-success); }
    .appointment-card.status-pending { --appointment-color: var(--color-warning); }
    .appointment-card.status-cancelled { --appointment-color: var(--color-muted); }
    
    .appointment-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 1.5rem;
        gap: 1rem;
    }
    
    .appointment-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--color-primary);
        margin: 0 0 0.5rem;
        line-height: 1.3;
    }
    
    .appointment-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--color-text-light);
        font-size: 0.95rem;
    }
    
    .meta-icon {
        width: 1.25rem;
        height: 1.25rem;
        color: var(--color-accent);
    }
    
    .participant-info {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1rem;
        background: var(--color-bg);
        border-radius: calc(var(--radius) - 4px);
        margin-bottom: 1rem;
    }
    
    .participant-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid white;
        box-shadow: var(--shadow-sm);
    }
    
    .avatar-placeholder {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--color-accent), var(--color-primary));
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.25rem;
    }
    
    .participant-details h4 {
        margin: 0 0 0.25rem;
        color: var(--color-primary);
        font-weight: 600;
    }
    
    .participant-details p {
        margin: 0;
        color: var(--color-text-light);
        font-size: 0.9rem;
    }
    
    .appointment-notes {
        background: #FFF9E6;
        border-right: 4px solid var(--color-warning);
        padding: 1rem 1.25rem;
        border-radius: calc(var(--radius) - 4px);
        margin-bottom: 1rem;
    }
    
    .appointment-notes p {
        margin: 0;
        color: var(--color-text);
        line-height: 1.6;
    }
    
    .appointment-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
        padding-top: 1.5rem;
        border-top: 2px solid var(--color-border);
    }
    
    .appointment-tags {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }
    
    .tag {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.5rem 1rem;
        background: var(--color-bg);
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--color-text-light);
    }
    
    .tag-icon {
        width: 1rem;
        height: 1rem;
    }
    
    .appointment-actions {
        display: flex;
        gap: 0.5rem;
    }
    
    .action-btn {
        padding: 0.5rem 1rem;
        border: 2px solid var(--color-border);
        background: white;
        border-radius: calc(var(--radius) - 4px);
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--color-primary);
        cursor: pointer;
        transition: var(--transition);
        font-family: 'Cairo', sans-serif;
    }
    
    .action-btn:hover {
        background: var(--color-primary);
        color: white;
        border-color: var(--color-primary);
        transform: translateY(-2px);
    }
    
    .action-btn.btn-confirm {
        background: var(--color-success);
        border-color: var(--color-success);
        color: white;
    }
    
    .action-btn.btn-confirm:hover {
        background: #229954;
        border-color: #229954;
    }
    
    /* Status Badge */
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .status-confirmed {
        background: rgba(39, 174, 96, 0.1);
        color: var(--color-success);
    }
    
    .status-pending {
        background: rgba(243, 156, 18, 0.1);
        color: var(--color-warning);
    }
    
    .status-cancelled {
        background: rgba(149, 165, 166, 0.1);
        color: var(--color-muted);
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }
    
    .empty-icon {
        width: 120px;
        height: 120px;
        margin: 0 auto 2rem;
        opacity: 0.3;
    }
    
    .empty-state h3 {
        font-size: 1.5rem;
        color: var(--color-text);
        margin-bottom: 1rem;
    }
    
    .empty-state p {
        color: var(--color-text-light);
        margin-bottom: 2rem;
    }
    
    /* Modal */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(44, 62, 80, 0.8);
        backdrop-filter: blur(8px);
        z-index: 1000;
        animation: fadeIn 0.3s ease-out;
    }
    
    .modal.active {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .modal-content {
        background: white;
        border-radius: var(--radius);
        width: 100%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 24px 64px rgba(0, 0, 0, 0.3);
        animation: slideUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(40px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    .modal-header {
        padding: 2rem;
        border-bottom: 2px solid var(--color-border);
    }
    
    .modal-header h3 {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--color-primary);
        margin: 0;
    }
    
    .modal-body {
        padding: 2rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-label {
        display: block;
        font-weight: 600;
        color: var(--color-primary);
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }
    
    .form-control {
        width: 100%;
        padding: 0.875rem 1.25rem;
        border: 2px solid var(--color-border);
        border-radius: calc(var(--radius) - 4px);
        font-size: 1rem;
        font-family: 'Cairo', sans-serif;
        transition: var(--transition);
        background: white;
    }
    
    .form-control:focus {
        outline: none;
        border-color: var(--color-primary);
        box-shadow: 0 0 0 4px rgba(44, 62, 80, 0.1);
    }
    
    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }
    
    .modal-footer {
        padding: 1.5rem 2rem;
        border-top: 2px solid var(--color-border);
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
    }
    
    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 2rem;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .page-header h1 {
            font-size: 2.5rem;
        }
        
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .section-header {
            flex-direction: column;
            align-items: stretch;
        }
        
        .filters {
            flex-direction: column;
        }
        
        .filter-group {
            width: 100%;
        }
        
        .appointment-footer {
            flex-direction: column;
            align-items: stretch;
        }
        
        .appointment-actions {
            width: 100%;
            justify-content: stretch;
        }
        
        .action-btn {
            flex: 1;
        }
    }
</style>

<div class="page-header">
    <div class="container">
        <h1>المواعيد</h1>
        <p>إدارة اجتماعاتك المجدولة بكفاءة وسهولة</p>
        <div class="page-header-actions">
            <button class="btn btn-primary" onclick="openModal()">
                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                موعد جديد
            </button>
            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                الرجوع
            </a>
            <button class="btn btn-secondary">
                <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                تصدير
            </button>
        </div>
    </div>
</div>

<div class="container" style="margin-top: 2rem; margin-bottom: 4rem;">
    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $stats['total'] }}</div>
            <div class="stat-label">إجمالي المواعيد</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['confirmed'] }}</div>
            <div class="stat-label">مؤكد</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['pending'] }}</div>
            <div class="stat-label">في الانتظار</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $stats['today'] }}</div>
            <div class="stat-label">اليوم</div>
        </div>
    </div>

    <!-- Appointments Section -->
    <div class="appointments-section">
        <div class="section-header">
            <h2 class="section-title">
                <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                قائمة المواعيد
            </h2>
            <div class="filters">
                <div class="filter-group">
                    <button class="filter-btn active">كل المواعيد</button>
                    <button class="filter-btn">اليوم</button>
                    <button class="filter-btn">هذا الأسبوع</button>
                    <button class="filter-btn">هذا الشهر</button>
                </div>
                <div class="filter-group">
                    <button class="filter-btn active">ترتيب حسب التاريخ</button>
                    <button class="filter-btn">ترتيب حسب العنوان</button>
                    <button class="filter-btn">ترتيب حسب الحالة</button>
                </div>
            </div>
        </div>

        @if($appointments->count() > 0)
            <div class="appointments-list">
                @foreach($appointments as $appointment)
                    <div class="appointment-card status-{{ $appointment->status }}">
                        <div class="appointment-header">
                            <div>
                                <h3 class="appointment-title">{{ $appointment->title }}</h3>
                                <div class="appointment-meta">
                                    <div class="meta-item">
                                        <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>{{ $appointment->start_datetime->format('Y-m-d') }}</span>
                                    </div>
                                    <div class="meta-item">
                                        <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $appointment->start_datetime->format('h:i A') }}</span>
                                    </div>
                                    <div class="meta-item">
                                        <svg class="meta-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $appointment->duration }} دقيقة</span>
                                    </div>
                                </div>
                            </div>
                            <span class="status-badge status-{{ $appointment->status }}">
                                @if($appointment->status == 'confirmed')
                                    مؤكد
                                @elseif($appointment->status == 'pending')
                                    قيد الانتظار
                                @else
                                    ملغي
                                @endif
                            </span>
                        </div>

                        @if($appointment->participant)
                            <div class="participant-info">
                                @if($appointment->participant->avatar)
                                    <img src="{{ $appointment->participant->avatar }}" alt="{{ $appointment->participant->full_name }}" class="participant-avatar">
                                @else
                                    <div class="avatar-placeholder">
                                        {{ substr($appointment->participant->full_name, 0, 1) }}
                                    </div>
                                @endif
                                <div class="participant-details">
                                    <h4>{{ $appointment->participant->full_name }}</h4>
                                    <p>{{ $appointment->participant->email }}</p>
                                </div>
                            </div>
                        @endif

                        @if($appointment->notes)
                            <div class="appointment-notes">
                                <p>{{ $appointment->notes }}</p>
                            </div>
                        @endif

                        <div class="appointment-footer">
                            <div class="appointment-tags">
                                <span class="tag">
                                    @if($appointment->appointment_type == 'video')
                                        <svg class="tag-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                        فيديو
                                    @elseif($appointment->appointment_type == 'voice')
                                        <svg class="tag-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                                        </svg>
                                        صوتي
                                    @elseif($appointment->appointment_type == 'in-person')
                                        <svg class="tag-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        شخصي
                                    @else
                                        <svg class="tag-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                        هاتفي
                                    @endif
                                </span>
                                @if($appointment->location)
                                    <span class="tag">
                                        <svg class="tag-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        {{ $appointment->location }}
                                    </span>
                                @endif
                            </div>
                            <div class="appointment-actions">
                                @if($appointment->status == 'pending' && $appointment->participant_id == Auth::id())
                                    <button class="action-btn btn-confirm">تأكيد</button>
                                @endif
                                <button class="action-btn">تعديل</button>
                                <button class="action-btn">إلغاء</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pagination">
                {{ $appointments->links() }}
            </div>
        @else
            <div class="empty-state">
                <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3>لا توجد مواعيد</h3>
                <p>ابدأ بإنشاء موعد جديد</p>
                <button class="btn btn-primary" onclick="openModal()">
                    <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    إنشاء موعد جديد
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Modal -->
<div class="modal" id="appointmentModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>موعد جديد</h3>
        </div>
        <div class="modal-body">
            <form>
                <div class="form-group">
                    <label class="form-label">عنوان الموعد</label>
                    <input type="text" class="form-control" placeholder="أدخل عنوان الموعد">
                </div>
                
                <div class="form-group">
                    <label class="form-label">المشارك</label>
                    <select class="form-control">
                        <option>اختر المشارك</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">التاريخ</label>
                    <input type="date" class="form-control">
                </div>
                
                <div class="form-group">
                    <label class="form-label">الوقت</label>
                    <input type="time" class="form-control">
                </div>
                
                <div class="form-group">
                    <label class="form-label">المدة (دقائق)</label>
                    <select class="form-control">
                        <option value="30">30 دقيقة</option>
                        <option value="60">60 دقيقة</option>
                        <option value="90">90 دقيقة</option>
                        <option value="120">120 دقيقة</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">نوع الموعد</label>
                    <select class="form-control">
                        <option value="video">مكالمة فيديو</option>
                        <option value="voice">مكالمة صوتية</option>
                        <option value="in-person">لقاء شخصي</option>
                        <option value="phone">مكالمة هاتفية</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">ملاحظات</label>
                    <textarea class="form-control" placeholder="أضف ملاحظات إضافية (اختياري)"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeModal()">إلغاء</button>
            <button class="btn btn-primary">حفظ الموعد</button>
        </div>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('appointmentModal').classList.add('active');
    }
    
    function closeModal() {
        document.getElementById('appointmentModal').classList.remove('active');
    }
    
    // Close modal on outside click
    document.getElementById('appointmentModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
    
    // Filter buttons functionality
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.filter-group').querySelectorAll('.filter-btn').forEach(b => {
                b.classList.remove('active');
            });
            this.classList.add('active');
        });
    });
</script>

@endsection````