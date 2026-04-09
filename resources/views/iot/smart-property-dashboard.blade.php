@extends('admin.layouts.admin')

@push('styles')
<style>
/* Admin Dashboard Style - Light Theme */

/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem 1rem;
    margin-bottom: 1.5rem;
    border-radius: 1rem;
}

.hero-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.hero-subtitle {
    font-size: 1rem;
    opacity: 0.9;
}

.btn-modern {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: white;
    color: #667eea;
    font-weight: 600;
    border-radius: 0.5rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.btn-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.3);
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-card {
    background: white;
    border-radius: 0.75rem;
    padding: 1rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    border: 1px solid #e5e7eb;
    transition: all 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    margin-bottom: 0.75rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.stat-icon.success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.stat-icon.info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.stat-icon.warning {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.25rem;
}

.stat-label {
    color: #6b7280;
    font-size: 0.8rem;
}

/* Content Cards */
.content-card {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.card-header {
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-title {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.card-title i {
    color: #667eea;
}

.btn-link {
    color: #667eea;
    text-decoration: none;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.875rem;
}

.btn-link:hover {
    color: #764ba2;
}

/* Table */
.table-modern {
    width: 100%;
    border-collapse: collapse;
}

.table-modern th {
    background: #f9fafb;
    padding: 1rem;
    text-align: right;
    font-weight: 600;
    color: #374151;
    font-size: 0.875rem;
    border-bottom: 1px solid #e5e7eb;
}

.table-modern td {
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
    color: #4b5563;
    font-size: 0.875rem;
}

.table-modern tr:hover td {
    background: #f9fafb;
}

.property-link {
    color: #667eea;
    font-weight: 600;
    text-decoration: none;
}

.property-link:hover {
    color: #764ba2;
}

/* Status Badge */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-badge.active {
    background: #d1fae5;
    color: #065f46;
}

.status-badge.inactive {
    background: #f3f4f6;
    color: #6b7280;
}

.status-badge::before {
    content: '';
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
}

/* Device Count */
.device-count {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.25rem 0.625rem;
    background: #f3f4f6;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    color: #4b5563;
}

/* Action Buttons */
.action-btn {
    width: 32px;
    height: 32px;
    border-radius: 0.5rem;
    border: 1px solid #e5e7eb;
    background: white;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.action-btn:hover {
    background: #f3f4f6;
    color: #374151;
    border-color: #d1d5db;
}

/* Quick Actions Card */
.quick-actions-card {
    background: white;
    border-radius: 1rem;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    border: 1px solid #e5e7eb;
    overflow: hidden;
    margin-bottom: 1.5rem;
}

.quick-action-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #e5e7eb;
    color: #374151;
    text-decoration: none;
    transition: all 0.2s;
}

.quick-action-item:last-child {
    border-bottom: none;
}

.quick-action-item:hover {
    background: #f9fafb;
    color: #667eea;
}

.quick-action-icon {
    width: 36px;
    height: 36px;
    border-radius: 0.625rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
}

.quick-action-icon.purple {
    background: #ede9fe;
    color: #7c3aed;
}

.quick-action-icon.green {
    background: #d1fae5;
    color: #059669;
}

.quick-action-icon.cyan {
    background: #cffafe;
    color: #0891b2;
}

.quick-action-icon.orange {
    background: #ffedd5;
    color: #ea580c;
}

.quick-action-text {
    font-weight: 500;
    font-size: 0.875rem;
}

/* Progress Bars */
.progress-container {
    margin-bottom: 1.25rem;
}

.progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}

.progress-label {
    font-size: 0.875rem;
    color: #4b5563;
}

.progress-value {
    font-size: 0.875rem;
    font-weight: 600;
}

.progress-value.success {
    color: #059669;
}

.progress-value.info {
    color: #0891b2;
}

.progress-value.warning {
    color: #ea580c;
}

.progress-bar-bg {
    height: 8px;
    background: #e5e7eb;
    border-radius: 9999px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    border-radius: 9999px;
    transition: width 0.5s ease;
}

.progress-bar-fill.success {
    background: linear-gradient(90deg, #10b981, #34d399);
}

.progress-bar-fill.info {
    background: linear-gradient(90deg, #3b82f6, #06b6d4);
}

.progress-bar-fill.warning {
    background: linear-gradient(90deg, #f59e0b, #fbbf24);
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-in {
    animation: fadeInUp 0.6s ease forwards;
}

/* Responsive */
@media (max-width: 1024px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }
    
    .hero-title {
        font-size: 1.5rem;
    }
}

@media (max-width: 640px) {
    .stats-grid {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
    
    .hero-section {
        padding: 1rem;
    }
    
    .hero-title {
        font-size: 1.25rem;
    }
    
    .stat-card {
        padding: 0.75rem;
    }
    
    .stat-icon {
        width: 32px;
        height: 32px;
        font-size: 0.875rem;
    }
    
    .stat-value {
        font-size: 1.25rem;
    }
    
    .stat-label {
        font-size: 0.75rem;
    }
}
</style>
@endpush

@section('title', 'لوحة تحكم العقارات الذكية')
@section('page-title', 'العقارات الذكية')

@section('content')
<div class="w-full">
    <!-- Hero Section -->
    <div class="hero-section mb-4">
        <div class="px-4">
            <div class="flex flex-col lg:flex-row items-center justify-between">
                <div class="mb-4 lg:mb-0">
                    <h1 class="hero-title animate-in">العقارات الذكية</h1>
                    <p class="hero-subtitle animate-in" style="animation-delay: 0.1s">
                        إدارة ومراقبة العقارات المتصلة بأنظمة IoT المتقدمة
                    </p>
                </div>
                <button class="btn-modern animate-in" style="animation-delay: 0.2s">
                    <i class="fas fa-plus"></i>
                    إضافة عقار ذكي
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid px-4 mb-4">
        <div class="stat-card animate-in">
            <div class="stat-icon">
                <i class="fas fa-building"></i>
            </div>
            <div class="stat-value">{{ $stats['total_properties'] ?? 0 }}</div>
            <div class="stat-label">إجمالي العقارات</div>
        </div>
        <div class="stat-card animate-in" style="animation-delay: 0.1s">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-value">{{ $stats['active_properties'] ?? 0 }}</div>
            <div class="stat-label">عقارات نشطة</div>
        </div>
        <div class="stat-card animate-in" style="animation-delay: 0.2s">
            <div class="stat-icon info">
                <i class="fas fa-microchip"></i>
            </div>
            <div class="stat-value">{{ $stats['total_devices'] ?? 0 }}</div>
            <div class="stat-label">الأجهزة المتصلة</div>
        </div>
        <div class="stat-card animate-in" style="animation-delay: 0.3s">
            <div class="stat-icon warning">
                <i class="fas fa-bell"></i>
            </div>
            <div class="stat-value">{{ $stats['active_alerts'] ?? 0 }}</div>
            <div class="stat-label">التنبيهات النشطة</div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="px-4 grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Properties Table -->
        <div class="lg:col-span-2">
            <div class="content-card animate-in" style="animation-delay: 0.4s">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-list"></i>
                        العقارات الحديثة
                    </h5>
                    <a href="#" class="btn-link">
                        عرض الكل
                        <i class="fas fa-arrow-left small"></i>
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="table-modern w-full">
                        <thead>
                            <tr>
                                <th>اسم العقار</th>
                                <th>العنوان</th>
                                <th>الحالة</th>
                                <th>الأجهزة</th>
                                <th>آخر تحديث</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentProperties ?? [] as $property)
                            <tr>
                                <td>
                                    <a href="#" class="property-link">
                                        {{ $property->property_name ?? 'عقار #' . $property->id }}
                                    </a>
                                </td>
                                <td>{{ $property->address ?? 'غير محدد' }}</td>
                                <td>
                                    <span class="status-badge {{ $property->status == 'active' ? 'active' : 'inactive' }}">
                                        {{ $property->status == 'active' ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="device-count">
                                        <i class="fas fa-microchip small"></i>
                                        {{ $property->devices_count ?? 0 }}
                                    </span>
                                </td>
                                <td>{{ $property->updated_at?->diffForHumans() ?? 'الآن' }}</td>
                                <td>
                                    <div class="flex gap-2">
                                        <button class="action-btn" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="action-btn" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <!-- Quick Actions -->
            <div class="quick-actions-card animate-in mb-4" style="animation-delay: 0.5s">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-bolt"></i>
                        إجراءات سريعة
                    </h5>
                </div>
                <div>
                    <a href="#" class="quick-action-item">
                        <div class="quick-action-icon purple">
                            <i class="fas fa-plus"></i>
                        </div>
                        <span class="quick-action-text">إضافة جهاز جديد</span>
                    </a>
                    <a href="#" class="quick-action-item">
                        <div class="quick-action-icon green">
                            <i class="fas fa-robot"></i>
                        </div>
                        <span class="quick-action-text">إنشاء أتمتة</span>
                    </a>
                    <a href="#" class="quick-action-item">
                        <div class="quick-action-icon cyan">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <span class="quick-action-text">إعداد مراقبة الطاقة</span>
                    </a>
                    <a href="#" class="quick-action-item">
                        <div class="quick-action-icon orange">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <span class="quick-action-text">إعداد الأمان</span>
                    </a>
                </div>
            </div>

            <!-- System Status -->
            <div class="content-card animate-in" style="animation-delay: 0.6s">
                <div class="card-header">
                    <h5 class="card-title">
                        <i class="fas fa-chart-line"></i>
                        حالة النظام
                    </h5>
                </div>
                <div class="p-4">
                    @php
                        $devicePercentage = ($stats['total_devices'] ?? 0) > 0 ? 
                            (($stats['active_devices'] ?? 0) / ($stats['total_devices'] ?? 1)) * 100 : 0;
                    @endphp
                    
                    <div class="progress-container">
                        <div class="progress-header">
                            <span class="progress-label">الأجهزة النشطة</span>
                            <span class="progress-value success">
                                {{ $stats['active_devices'] ?? 0 }}/{{ $stats['total_devices'] ?? 0 }}
                            </span>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill success" style="width: {{ $devicePercentage }}%"></div>
                        </div>
                    </div>

                    <div class="progress-container">
                        <div class="progress-header">
                            <span class="progress-label">كفاءة الطاقة</span>
                            <span class="progress-value info">{{ $stats['energy_efficiency'] ?? 0 }}%</span>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill info" style="width: {{ $stats['energy_efficiency'] ?? 0 }}%"></div>
                        </div>
                    </div>

                    <div class="progress-container mb-0">
                        <div class="progress-header">
                            <span class="progress-label">مستوى الأمان</span>
                            <span class="progress-value warning">{{ $stats['security_level'] ?? 0 }}%</span>
                        </div>
                        <div class="progress-bar-bg">
                            <div class="progress-bar-fill warning" style="width: {{ $stats['security_level'] ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
