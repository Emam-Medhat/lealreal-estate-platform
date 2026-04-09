@extends('admin.layouts.admin')

@section('title', 'لوحة تحكم الاحتيال الذكي')
@section('page-title', 'نظام كشف الاحتيال الذكي')

@push('styles')
    <style>
        :root {
            /* Brand palette aligned with site */
            --fraud-blue:   #0D6EFD;
            --fraud-purple: #6F42C1;
            --fraud-green:  #198754;
            --fraud-orange: #FD7E14;
            --fraud-red:    #DC3545;
            --fraud-teal:   #0DCAF0;
            --fraud-yellow: #FFC107;

            /* Surfaces and text for light theme */
            --fraud-bg:     #F1F3F8;
            --fraud-card:   #FFFFFF;
            --fraud-border: #E5E7EB;
            --fraud-text:   #1A1F36;
            --fraud-muted:  #6B7280;
        }

        .fraud-dashboard {
            background: var(--fraud-bg);
            min-height: 100vh;
            color: var(--fraud-text);
            font-family: 'Tajawal', sans-serif;
        }

        /* ── Header Banner ── */
        .fraud-hero {
            background: linear-gradient(135deg, var(--fraud-blue) 0%, var(--fraud-purple) 100%);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(13, 110, 253, 0.25);
            position: relative;
            overflow: hidden;
        }

        .fraud-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.15) 0%, transparent 70%);
            pointer-events: none;
        }

        .fraud-hero-title {
            font-size: 1.9rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff, #f2f6ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .fraud-hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(34, 197, 94, 0.15);
            border: 1px solid rgba(34, 197, 94, 0.3);
            color: #4ade80;
            border-radius: 50px;
            padding: 4px 14px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* ── Stat Cards ── */
        .fraud-stat-card {
            background: var(--fraud-card);
            border: 1px solid var(--fraud-border);
            border-radius: 16px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
            box-shadow: 0 8px 24px rgba(0,0,0,.08);
        }

        .fraud-stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
        }

        .fraud-stat-card .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .fraud-stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
        }

        .fraud-stat-card .stat-label {
            font-size: 0.8rem;
            color: var(--fraud-muted);
            margin-top: 4px;
        }

        .fraud-stat-card .stat-change {
            font-size: 0.75rem;
            margin-top: 0.75rem;
        }

        .fraud-stat-card::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            border-radius: 0 0 16px 16px;
        }

        .stat-danger::after {
            background: var(--fraud-red);
        }

        .stat-warning::after {
            background: var(--fraud-orange);
        }

        .stat-info::after {
            background: var(--fraud-blue);
        }

        .stat-success::after {
            background: var(--fraud-green);
        }

        .stat-purple::after {
            background: var(--fraud-purple);
        }

        /* ── Fraud Score Badge ── */
        .fraud-score-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 3px 10px;
            border-radius: 50px;
        }

        .score-critical {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.4);
        }

        .score-high {
            background: rgba(249, 115, 22, 0.2);
            color: #fb923c;
            border: 1px solid rgba(249, 115, 22, 0.4);
        }

        .score-medium {
            background: rgba(234, 179, 8, 0.2);
            color: #facc15;
            border: 1px solid rgba(234, 179, 8, 0.4);
        }

        .score-low {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
            border: 1px solid rgba(34, 197, 94, 0.4);
        }

        /* ── Score Meter ── */
        .score-meter {
            width: 100%;
            height: 6px;
            background: var(--fraud-border);
            border-radius: 10px;
            overflow: hidden;
            margin-top: 6px;
        }

        .score-meter-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 1s ease;
        }

        /* ── Cards ── */
        .fraud-card {
            background: var(--fraud-card);
            border: 1px solid var(--fraud-border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 12px 30px rgba(13,110,253,.08);
        }

        .fraud-card-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--fraud-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: linear-gradient(90deg, rgba(13,110,253,.10), rgba(111,66,193,.10));
        }

        .fraud-card-title {
            font-size: 1rem;
            font-weight: 600;
            color: var(--fraud-text);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .fraud-card-body {
            padding: 1.5rem;
        }

        /* ── Table ── */
        .fraud-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .fraud-table th {
            background: linear-gradient(90deg, rgba(13,110,253,.10), rgba(111,66,193,.10));
            padding: 0.75rem 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--fraud-muted);
            border-bottom: 1px solid var(--fraud-border);
            text-align: right;
        }

        .fraud-table td {
            padding: 0.875rem 1rem;
            font-size: 0.85rem;
            border-bottom: 1px solid var(--fraud-border);
            color: var(--fraud-text);
            vertical-align: middle;
        }

        .fraud-table tr:last-child td {
            border-bottom: none;
        }

        .fraud-table tr:hover td {
            background: rgba(255, 255, 255, 0.03);
        }

        /* ── Detection Module Indicator ── */
        .module-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 0.7rem;
            padding: 2px 8px;
            border-radius: 50px;
            font-weight: 500;
        }

        .module-image {
            background: rgba(139, 92, 246, 0.2);
            color: #a78bfa;
        }

        .module-address {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
        }

        .module-price {
            background: rgba(234, 179, 8, 0.2);
            color: #fbbf24;
        }

        .module-account {
            background: rgba(249, 115, 22, 0.2);
            color: #fb923c;
        }

        .module-duplicate {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
        }

        /* ── Pulse animation for live indicator ── */
        @keyframes pulse-red {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }
        }

        .live-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--fraud-green);
            animation: pulse-red 2s infinite;
            display: inline-block;
        }

        /* ── Action buttons ── */
        .fraud-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 16px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .fraud-btn-danger {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .fraud-btn-success {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .fraud-btn-warning {
            background: rgba(234, 179, 8, 0.2);
            color: #facc15;
            border: 1px solid rgba(234, 179, 8, 0.3);
        }

        .fraud-btn-info {
            background: linear-gradient(90deg, #0D6EFD, #0a58ca);
            color: #fff;
            box-shadow: 0 6px 18px rgba(13,110,253,.25);
        }

        .fraud-btn:hover {
            transform: translateY(-1px);
            filter: brightness(1.15);
        }

        /* ── Chart Container ── */
        .chart-container {
            position: relative;
            height: 260px;
        }

        /* ── Status Badges ── */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.72rem;
            font-weight: 600;
            padding: 3px 10px;
            border-radius: 50px;
        }

        .status-blocked {
            background: rgba(239, 68, 68, 0.2);
            color: #f87171;
        }

        .status-suspicious {
            background: rgba(234, 179, 8, 0.2);
            color: #facc15;
        }

        .status-clean {
            background: rgba(34, 197, 94, 0.2);
            color: #4ade80;
        }

        .status-reviewed {
            background: rgba(59, 130, 246, 0.2);
            color: #60a5fa;
        }

        .status-cleared {
            background: rgba(34, 197, 94, 0.15);
            color: #86efac;
        }

        /* ── Loading Spinner ── */
        .fraud-spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            border-top-color: var(--fraud-purple);
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ── Responsive tweaks ── */
        @media (max-width: 768px) {
            .fraud-hero-title {
                font-size: 1.3rem;
            }

            .stat-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
    </style>
@endpush

@section('content')
    <div class="fraud-dashboard p-1">

        {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ HERO BANNER ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
        <div class="fraud-hero mb-6">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div
                            class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-600 to-indigo-700 flex items-center justify-center shadow-lg">
                            <i class="fas fa-shield-alt text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="fraud-hero-title">نظام كشف الاحتيال الذكي</h1>
                            <p class="text-slate-400 text-sm">AI Fraud Detection & Smart Duplicate Detection</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 mt-3">
                        <span class="fraud-hero-badge"><span class="live-indicator"></span> الحماية نشطة</span>
                        <span class="module-pill module-image"><i class="fas fa-image"></i> تحليل الصور</span>
                        <span class="module-pill module-address"><i class="fas fa-map-marker-alt"></i> التحقق من
                            العنوان</span>
                        <span class="module-pill module-price"><i class="fas fa-chart-line"></i> شذوذ الأسعار</span>
                        <span class="module-pill module-account"><i class="fas fa-user-shield"></i> حسابات وهمية</span>
                        <span class="module-pill module-duplicate"><i class="fas fa-copy"></i> إعلانات مكررة</span>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button onclick="refreshStats()" class="fraud-btn fraud-btn-info">
                        <i class="fas fa-sync-alt" id="refresh-icon"></i>
                        تحديث
                    </button>
                    <a href="{{ route('admin.fraud.duplicates') }}" class="fraud-btn fraud-btn-warning">
                        <i class="fas fa-copy"></i>
                        مراجعة المكررات
                    </a>
                </div>
            </div>
        </div>

        {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ STAT CARDS ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-6 stat-grid">

            <div class="fraud-stat-card stat-danger">
                <div class="stat-icon" style="background: rgba(239,68,68,0.15);">
                    <i class="fas fa-ban" style="color: var(--fraud-red);"></i>
                </div>
                <div class="stat-value" style="color: #f87171;">{{ $stats['blocked_properties']['total'] }}</div>
                <div class="stat-label">إعلانات محظورة</div>
            </div>

            <div class="fraud-stat-card stat-warning">
                <div class="stat-icon" style="background: rgba(249,115,22,0.15);">
                    <i class="fas fa-exclamation-triangle" style="color: var(--fraud-orange);"></i>
                </div>
                <div class="stat-value" style="color: #fb923c;">{{ $stats['flagged_properties']['last_24h'] }}</div>
                <div class="stat-label">مشبوه (24 ساعة)</div>
            </div>

            <div class="fraud-stat-card stat-purple">
                <div class="stat-icon" style="background: rgba(139,92,246,0.15);">
                    <i class="fas fa-copy" style="color: var(--fraud-purple);"></i>
                </div>
                <div class="stat-value" style="color: #a78bfa;">{{ $stats['duplicate_pairs']['pending'] }}</div>
                <div class="stat-label">مكررات قيد المراجعة</div>
            </div>

            <div class="fraud-stat-card stat-danger">
                <div class="stat-icon" style="background: rgba(239,68,68,0.15);">
                    <i class="fas fa-user-slash" style="color: var(--fraud-red);"></i>
                </div>
                <div class="stat-value" style="color: #f87171;">{{ $stats['restricted_users']['count'] }}</div>
                <div class="stat-label">مستخدمين مقيدين</div>
            </div>

            <div class="fraud-stat-card stat-info">
                <div class="stat-icon" style="background: rgba(59,130,246,0.15);">
                    <i class="fas fa-user-secret" style="color: var(--fraud-blue);"></i>
                </div>
                <div class="stat-value" style="color: #60a5fa;">{{ $stats['high_risk_users']['count'] }}</div>
                <div class="stat-label">مستخدمين عالي الخطورة</div>
            </div>

        </div>

        {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ CHARTS ROW ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

            {{-- Trend Chart --}}
            <div class="fraud-card lg:col-span-2">
                <div class="fraud-card-header">
                    <span class="fraud-card-title">
                        <i class="fas fa-chart-area text-purple-400"></i>
                        تحليلات الاحتيال - آخر 14 يوم
                    </span>
                    <select id="chartDays" onchange="updateChart(this.value)"
                        class="bg-slate-700 text-slate-300 text-xs border border-slate-600 rounded-lg px-3 py-1.5">
                        <option value="7">7 أيام</option>
                        <option value="14" selected>14 يوم</option>
                        <option value="30">30 يوم</option>
                    </select>
                </div>
                <div class="fraud-card-body">
                    <div class="chart-container">
                        <canvas id="fraudTrendChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Detection Type Breakdown --}}
            <div class="fraud-card">
                <div class="fraud-card-header">
                    <span class="fraud-card-title">
                        <i class="fas fa-chart-pie text-blue-400"></i>
                        توزيع أنواع الكشف
                    </span>
                </div>
                <div class="fraud-card-body">
                    <div class="chart-container" style="height: 180px;">
                        <canvas id="typeBreakdownChart"></canvas>
                    </div>
                    <div class="mt-4 space-y-2">
                        @foreach($detectionByType as $type)
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center gap-2">
                                                    <span class="module-pill module-{{ str_replace('_', '-', $type->detection_type) }}">
                                                        {{ match ($type->detection_type) {
                                'image_hash' => '🖼️ صور',
                                'address_validation' => '📍 عنوان',
                                'price_anomaly' => '💰 سعر',
                                'fake_account' => '👤 حساب',
                                'duplicate_detection' => '📋 مكرر',
                                default => $type->detection_type
                            } }}
                                                    </span>
                                                </div>
                                                <div class="text-right">
                                                    <span class="text-slate-200 text-sm font-semibold">{{ $type->count }}</span>
                                                    <span class="text-slate-500 text-xs mr-1">حالة</span>
                                                </div>
                                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ FLAGGED PROPERTIES TABLE ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
        <div class="fraud-card mb-6">
            <div class="fraud-card-header">
                <span class="fraud-card-title">
                    <i class="fas fa-flag text-red-400"></i>
                    الإعلانات المشبوهة والمحظورة
                </span>
                <div class="flex gap-2">
                    <a href="{{ route('admin.fraud.high-risk-users') }}" class="fraud-btn fraud-btn-warning text-xs">
                        <i class="fas fa-users"></i>
                        المستخدمون الخطرون
                    </a>
                </div>
            </div>
            <div style="overflow-x: auto;">
                <table class="fraud-table">
                    <thead>
                        <tr>
                            <th>الإعلان</th>
                            <th>المستخدم</th>
                            <th>درجة الاحتيال</th>
                            <th>الحالة</th>
                            <th>العلامات</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($flaggedProperties as $property)
                                            <tr>
                                                <td>
                                                    <div class="font-medium text-slate-200">{{ Str::limit($property->title, 40) }}</div>
                                                    <div class="text-slate-500 text-xs mt-0.5">{{ $property->city }} •
                                                        {{ number_format($property->price) }} {{ $property->currency ?? 'EGP' }}</div>
                                                </td>
                                                <td>
                                                    <div class="text-slate-300 text-sm">{{ $property->user?->name ?? 'غير معروف' }}</div>
                                                    <div class="text-slate-500 text-xs">{{ $property->user?->email }}</div>
                                                </td>
                                                <td>
                                                    @php $score = $property->fraud_score ?? 0; @endphp
                                                    <span
                                                        class="fraud-score-badge {{ $score >= 80 ? 'score-critical' : ($score >= 60 ? 'score-high' : ($score >= 40 ? 'score-medium' : 'score-low')) }}">
                                                        <i class="fas fa-circle" style="font-size: 6px;"></i>
                                                        {{ $score }}/100
                                                    </span>
                                                    <div class="score-meter mt-1" style="width: 80px;">
                                                        <div class="score-meter-fill"
                                                            style="width: {{ $score }}%; background: {{ $score >= 80 ? '#ef4444' : ($score >= 60 ? '#f97316' : ($score >= 40 ? '#eab308' : '#22c55e')) }};">
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="status-badge status-{{ $property->fraud_status }}">
                                                        {{ match ($property->fraud_status) {
                                'blocked' => '🚫 محظور',
                                'suspicious' => '⚠️ مشبوه',
                                'reviewed' => '🔍 مراجع',
                                'cleared' => '✅ نظيف',
                                default => '— نظيف'
                            } }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if(!empty($property->fraud_flags))
                                                        @foreach(array_slice((array) $property->fraud_flags, 0, 3) as $flag)
                                                            <span
                                                                class="module-pill module-duplicate text-xs mb-0.5">{{ str_replace('_', ' ', $flag) }}</span>
                                                        @endforeach
                                                    @else
                                                        <span class="text-slate-600 text-xs">—</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="flex items-center gap-2">
                                                        <button onclick="analyzeProperty({{ $property->id }}, this)"
                                                            class="fraud-btn fraud-btn-info px-2 py-1 text-xs" title="تحليل">
                                                            <i class="fas fa-search"></i>
                                                        </button>
                                                        <a href="{{ route('admin.fraud.property-detail', $property->id) }}"
                                                            class="fraud-btn fraud-btn-warning px-2 py-1 text-xs" title="تفاصيل">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <button onclick="updateStatus({{ $property->id }}, 'cleared')"
                                                            class="fraud-btn fraud-btn-success px-2 py-1 text-xs" title="تصفية">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        <button onclick="updateStatus({{ $property->id }}, 'blocked')"
                                                            class="fraud-btn fraud-btn-danger px-2 py-1 text-xs" title="حظر">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-12">
                                    <div class="flex flex-col items-center gap-3 text-slate-500">
                                        <i class="fas fa-shield-check text-4xl text-green-500 opacity-50"></i>
                                        <p class="text-sm">لا توجد إعلانات مشبوهة حالياً</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($flaggedProperties->hasPages())
                <div class="fraud-card-body border-t border-slate-700 pt-4">
                    <div class="text-slate-500 text-xs">
                        {{ $flaggedProperties->links() }}
                    </div>
                </div>
            @endif
        </div>

        {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ RECENT ALERTS + DETECTION LOGS ━━━━━━━━━━━━━━━━━━━━━━━━ --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Recent Alerts --}}
            <div class="fraud-card">
                <div class="fraud-card-header">
                    <span class="fraud-card-title">
                        <i class="fas fa-bell text-yellow-400"></i>
                        تنبيهات الاحتيال الأخيرة
                    </span>
                    <span class="text-xs text-slate-500">قيد الانتظار: {{ $stats['recent_alerts']->count() }}</span>
                </div>
                <div class="space-y-0">
                    @forelse($recentAlerts as $alert)
                        <div
                            class="flex items-start gap-3 p-4 border-b border-slate-700 hover:bg-slate-700/30 transition-colors">
                            <div
                                class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center
                                {{ $alert->risk_level === 'critical' ? 'bg-red-500/20' : ($alert->risk_level === 'high' ? 'bg-orange-500/20' : 'bg-yellow-500/20') }}">
                                <i
                                    class="fas fa-exclamation text-xs
                                    {{ $alert->risk_level === 'critical' ? 'text-red-400' : ($alert->risk_level === 'high' ? 'text-orange-400' : 'text-yellow-400') }}"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span
                                        class="text-slate-200 text-xs font-semibold">{{ str_replace('_', ' ', $alert->alert_type) }}</span>
                                    <span
                                        class="fraud-score-badge {{ $alert->risk_level === 'critical' ? 'score-critical' : ($alert->risk_level === 'high' ? 'score-high' : 'score-medium') }} text-xs">
                                        {{ $alert->risk_level }}
                                    </span>
                                </div>
                                <p class="text-slate-400 text-xs leading-relaxed">{{ Str::limit($alert->description, 80) }}</p>
                                <div class="flex items-center justify-between mt-2">
                                    <span class="text-slate-600 text-xs">{{ $alert->created_at?->diffForHumans() }}</span>
                                    <div class="flex gap-1">
                                        <button onclick="resolveAlert({{ $alert->id }}, 'resolved')"
                                            class="fraud-btn fraud-btn-success text-xs px-2 py-0.5">حل</button>
                                        <button onclick="resolveAlert({{ $alert->id }}, 'dismissed')"
                                            class="fraud-btn fraud-btn-danger text-xs px-2 py-0.5">رفض</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12 text-slate-500">
                            <i class="fas fa-bell-slash text-3xl mb-3 opacity-30"></i>
                            <p class="text-sm">لا توجد تنبيهات نشطة</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Detection Module Stats --}}
            <div class="fraud-card">
                <div class="fraud-card-header">
                    <span class="fraud-card-title">
                        <i class="fas fa-cogs text-purple-400"></i>
                        أداء وحدات الكشف (30 يوم)
                    </span>
                </div>
                <div class="fraud-card-body space-y-4">
                    @php
                        $modules = [
                            ['key' => 'image_hash', 'label' => 'كشف الصور المكررة', 'icon' => 'fa-image', 'color' => '#8b5cf6', 'class' => 'module-image'],
                            ['key' => 'address_validation', 'label' => 'التحقق من العنوان', 'icon' => 'fa-map-marker-alt', 'color' => '#3b82f6', 'class' => 'module-address'],
                            ['key' => 'price_anomaly', 'label' => 'كشف شذوذ الأسعار', 'icon' => 'fa-chart-line', 'color' => '#eab308', 'class' => 'module-price'],
                            ['key' => 'fake_account', 'label' => 'كشف الحسابات الوهمية', 'icon' => 'fa-user-shield', 'color' => '#f97316', 'class' => 'module-account'],
                            ['key' => 'duplicate_detection', 'label' => 'كشف الإعلانات المكررة', 'icon' => 'fa-copy', 'color' => '#ef4444', 'class' => 'module-duplicate'],
                        ];
                    @endphp

                    @foreach($modules as $module)
                        @php
                            $moduleData = $detectionByType->firstWhere('detection_type', $module['key']);
                            $count = $moduleData?->count ?? 0;
                            $avgScore = round($moduleData?->avg_score ?? 0);
                            $maxBars = max($detectionByType->max('count'), 1);
                            $barWidth = $count > 0 ? min(100, ($count / $maxBars) * 100) : 0;
                        @endphp
                        <div class="bg-slate-800/50 rounded-xl p-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs"
                                        style="background: {{ $module['color'] }}22; color: {{ $module['color'] }};">
                                        <i class="fas {{ $module['icon'] }}"></i>
                                    </div>
                                    <span class="text-slate-300 text-sm">{{ $module['label'] }}</span>
                                </div>
                                <div class="text-left">
                                    <span class="text-slate-200 font-bold text-sm">{{ $count }}</span>
                                    <span class="text-slate-500 text-xs mr-1">حالة</span>
                                    @if($avgScore > 0)
                                        <span class="text-slate-500 text-xs">| متوسط: {{ $avgScore }}%</span>
                                    @endif
                                </div>
                            </div>
                            <div class="score-meter">
                                <div class="score-meter-fill"
                                    style="width: {{ $barWidth }}%; background: {{ $module['color'] }};"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ NOTIFICATION TOAST ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
    <div id="fraud-toast" class="fixed top-4 right-4 z-50 hidden">
        <div class="flex items-center gap-3 px-5 py-3 rounded-xl shadow-2xl border text-sm font-medium" id="toast-content">
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        // Fraud Dashboard JavaScript
        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;

        // ── Trend Chart ──────────────────────────────────────────
        let trendChart;
        const trendData = @json($trendData);

        function initTrendChart(data) {
            const ctx = document.getElementById('fraudTrendChart').getContext('2d');
            if (trendChart) trendChart.destroy();

            trendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'تنبيهات الاحتيال',
                        data: data.alert_counts,
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#8b5cf6',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            borderColor: '#334155',
                            borderWidth: 1,
                            titleColor: '#e2e8f0',
                            bodyColor: '#94a3b8',
                            callbacks: {
                                title: ctx => ctx[0].label,
                                label: ctx => `${ctx.raw} تنبيه`,
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: 'rgba(51,65,85,0.5)' },
                            ticks: { color: '#94a3b8', font: { size: 11 } }
                        },
                        y: {
                            grid: { color: 'rgba(51,65,85,0.5)' },
                            ticks: { color: '#94a3b8', font: { size: 11 }, stepSize: 1 },
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // ── Donut Chart ──────────────────────────────────────────
        function initTypeChart() {
            const ctx = document.getElementById('typeBreakdownChart').getContext('2d');
            const detectionData = @json($detectionByType);

            const labels = detectionData.map(d => ({
                'image_hash': '🖼️ صور',
                'address_validation': '📍 عنوان',
                'price_anomaly': '💰 سعر',
                'fake_account': '👤 حساب',
                'duplicate_detection': '📋 مكرر'
            }[d.detection_type] || d.detection_type));

            const values = detectionData.map(d => d.count);
            const colours = ['#8b5cf6', '#3b82f6', '#eab308', '#f97316', '#ef4444'];

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [{
                        data: values.length ? values : [1],
                        backgroundColor: values.length ? colours : ['#334155'],
                        borderColor: '#0f172a',
                        borderWidth: 3,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#1e293b',
                            borderColor: '#334155',
                            borderWidth: 1,
                            titleColor: '#e2e8f0',
                            bodyColor: '#94a3b8',
                        }
                    }
                }
            });
        }

        // ── Analyze Property ─────────────────────────────────────
        async function analyzeProperty(id, btn) {
            const icon = btn.querySelector('i');
            icon.className = 'fas fa-spinner fa-spin';
            btn.disabled = true;

            try {
                const res = await fetch(`/admin/fraud/analyze/property/${id}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' }
                });
                const data = await res.json();

                if (data.success) {
                    showToast(`تم التحليل: درجة الاحتيال ${data.result.total_fraud_score}/100`, 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(data.error || 'فشل التحليل', 'error');
                }
            } catch (e) {
                showToast('خطأ في الاتصال بالسيرفر', 'error');
            } finally {
                icon.className = 'fas fa-search';
                btn.disabled = false;
            }
        }

        // ── Update Property Status ───────────────────────────────
        async function updateStatus(id, status) {
            const labels = { cleared: '✅ تصفية', blocked: '🚫 حظر', suspicious: '⚠️ إشارة', reviewed: '🔍 مراجعة' };
            if (!confirm(`هل أنت متأكد من ${labels[status] || status}؟`)) return;

            try {
                const res = await fetch(`/admin/fraud/properties/${id}/status`, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ status })
                });
                const data = await res.json();
                showToast(data.message || 'تم التحديث', data.success ? 'success' : 'error');
                if (data.success) setTimeout(() => location.reload(), 1000);
            } catch (e) {
                showToast('خطأ في الاتصال', 'error');
            }
        }

        // ── Resolve Alert ────────────────────────────────────────
        async function resolveAlert(id, resolution) {
            try {
                const res = await fetch(`/admin/fraud/alerts/${id}/resolve`, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ resolution })
                });
                const data = await res.json();
                showToast(data.message || 'تم', data.success ? 'success' : 'error');
                if (data.success) setTimeout(() => location.reload(), 1000);
            } catch (e) {
                showToast('خطأ في الاتصال', 'error');
            }
        }

        // ── Refresh Stats ────────────────────────────────────────
        async function refreshStats() {
            const icon = document.getElementById('refresh-icon');
            icon.classList.add('fa-spin');

            try {
                const res = await fetch('/admin/fraud/stats', { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                showToast('تم تحديث البيانات بنجاح', 'success');
                // Full reload to update all numbers
                setTimeout(() => location.reload(), 500);
            } catch (e) {
                showToast('فشل تحديث البيانات', 'error');
            } finally {
                setTimeout(() => icon.classList.remove('fa-spin'), 500);
            }
        }

        // ── Update Chart Days ────────────────────────────────────
        async function updateChart(days) {
            try {
                const res = await fetch(`/admin/fraud/chart-data?days=${days}`, { headers: { 'Accept': 'application/json' } });
                const data = await res.json();
                initTrendChart(data);
            } catch (e) {
                console.error('Chart update failed:', e);
            }
        }

        // ── Toast Utility ────────────────────────────────────────
        function showToast(msg, type = 'info') {
            const toast = document.getElementById('fraud-toast');
            const content = document.getElementById('toast-content');
            const styles = {
                success: 'bg-slate-800 border-green-500/50 text-green-400',
                error: 'bg-slate-800 border-red-500/50   text-red-400',
                info: 'bg-slate-800 border-blue-500/50  text-blue-400',
            };
            const icons = { success: 'check-circle', error: 'times-circle', info: 'info-circle' };

            content.className = `flex items-center gap-3 px-5 py-3 rounded-xl shadow-2xl border text-sm font-medium ${styles[type]}`;
            content.innerHTML = `<i class="fas fa-${icons[type]}"></i> ${msg}`;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 4000);
        }

        // ── Initialize ───────────────────────────────────────────
        document.addEventListener('DOMContentLoaded', function () {
            initTrendChart(trendData);
            initTypeChart();
        });
    </script>
@endpush
