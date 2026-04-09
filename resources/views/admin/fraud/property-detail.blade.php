@extends('admin.layouts.admin')

@section('title', 'تفاصيل تحليل الاحتيال - ' . $property->title)

@section('styles')
<style>
    :root {
        --fraud-red: #ef4444;
        --fraud-orange: #f59e0b;
        --fraud-yellow: #fbbf24;
        --fraud-green: #10b981;
        --fraud-blue: #3b82f6;
        --fraud-purple: #8b5cf6;
    }

    .fraud-bg-dark { background-color: #0f172a; }
    .fraud-card {
        background: rgba(30, 41, 59, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        color: white;
    }
    
    .score-badge {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: bold;
        border: 4px solid;
    }
    
    .score-low { color: var(--fraud-green); border-color: var(--fraud-green); background: rgba(16, 185, 129, 0.1); }
    .score-medium { color: var(--fraud-yellow); border-color: var(--fraud-yellow); background: rgba(251, 191, 36, 0.1); }
    .score-high { color: var(--fraud-orange); border-color: var(--fraud-orange); background: rgba(245, 158, 11, 0.1); }
    .score-critical { color: var(--fraud-red); border-color: var(--fraud-red); background: rgba(239, 68, 68, 0.1); }

    .indicator-item {
        transition: all 0.3s ease;
        border-right: 4px solid transparent;
    }
    .indicator-item:hover {
        background: rgba(255, 255, 255, 0.03);
        transform: translateX(-5px);
    }
    .indicator-flagged { border-right-color: var(--fraud-red); background: rgba(239, 68, 68, 0.05); }
</style>
@endsection

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white mb-2">تحليل الاحتيال الذكي</h1>
            <p class="text-slate-400">تحليل مفصل للعقار: <span class="text-blue-400">#{{ $property->id }} - {{ $property->title }}</span></p>
        </div>
        <div class="flex gap-3">
            <button onclick="triggerAnalysis({{ $property->id }})" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition-all">
                <i class="fas fa-sync-alt" id="analyzeIcon"></i>
                إعادة التحليل الآن
            </button>
            <a href="{{ route('admin.properties.edit', $property->id) }}" class="bg-slate-700 hover:bg-slate-600 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <i class="fas fa-edit"></i>
                تعديل العقار
            </a>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Column: Summary & Property Info -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Score Card -->
            <div class="fraud-card p-6 flex flex-col items-center text-center">
                <h3 class="text-lg font-semibold mb-4 text-slate-300">درجة الاحتيال الإجمالية</h3>
                @php
                    $score = $property->fraud_score ?? 0;
                    $class = 'score-low';
                    if($score > 70) $class = 'score-critical';
                    elseif($score > 40) $class = 'score-high';
                    elseif($score > 20) $class = 'score-medium';
                @endphp
                <div class="score-badge {{ $class }} mb-4">
                    {{ $score }}%
                </div>
                <div class="text-xl font-bold mb-2">
                    @if($score > 70) حرج (Critical)
                    @elseif($score > 40) مرتفع (High)
                    @elseif($score > 20) متوسط (Medium)
                    @else منخفض (Safe)
                    @endif
                </div>
                <p class="text-slate-400 text-sm">تم التحديث: {{ $property->updated_at->diffForHumans() }}</p>
                
                <div class="w-full mt-6 pt-6 border-t border-slate-700">
                    <div class="flex justify-between mb-2">
                        <span class="text-slate-400">الحالة الحالية:</span>
                        <span class="px-2 py-0.5 rounded text-xs font-bold 
                            @if($property->fraud_status === 'clean') bg-green-500/20 text-green-400
                            @elseif($property->fraud_status === 'suspicious') bg-yellow-500/20 text-yellow-400
                            @elseif($property->fraud_status === 'blocked') bg-red-500/20 text-red-400
                            @else bg-blue-500/20 text-blue-400
                            @endif">
                            {{ $property->fraud_status }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Property Basic Info -->
            <div class="fraud-card p-6">
                <h3 class="text-lg font-semibold mb-4 border-b border-slate-700 pb-2">بيانات العقار</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-slate-400">المعلن:</span>
                        <a href="{{ $property->user ? route('admin.users.show', $property->user_id) : '#' }}" class="text-blue-400 hover:underline">
                            {{ $property->user->name ?? 'غير معروف' }}
                        </a>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">السعر:</span>
                        <span class="text-white">{{ number_format($property->price) }} {{ $property->currency }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">المدينة:</span>
                        <span class="text-white">{{ $property->city }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">تاريخ النشر:</span>
                        <span class="text-white">{{ $property->created_at->format('Y/m/d') }}</span>
                    </div>
                </div>
            </div>

            <!-- Change Status Actions -->
            <div class="fraud-card p-6">
                <h3 class="text-lg font-semibold mb-4">تغيير الحالة يدوياً</h3>
                <div class="space-y-3">
                    <button onclick="updateStatus('clean')" class="w-full text-right px-4 py-2 rounded bg-green-500/10 hover:bg-green-500/20 text-green-400 border border-green-500/30 transition-all">
                        <i class="fas fa-check-circle ml-2"></i> تعليم كـ سليم (Safe)
                    </button>
                    <button onclick="updateStatus('suspicious')" class="w-full text-right px-4 py-2 rounded bg-yellow-500/10 hover:bg-yellow-500/20 text-yellow-400 border border-yellow-500/30 transition-all">
                        <i class="fas fa-exclamation-triangle ml-2"></i> تعليم كـ مشتبه به
                    </button>
                    <button onclick="updateStatus('blocked')" class="w-full text-right px-4 py-2 rounded bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/30 transition-all">
                        <i class="fas fa-ban ml-2"></i> حظر الإعلان الآن
                    </button>
                </div>
            </div>
        </div>

        <!-- Right Column: Analysis Details -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Indicators Breakdown -->
            <div class="fraud-card overflow-hidden">
                <div class="p-4 border-b border-slate-700 bg-slate-800/50">
                    <h3 class="font-bold flex items-center gap-2">
                        <i class="fas fa-microchip text-blue-400"></i>
                        تحليل المؤشرات الذكية
                    </h3>
                </div>
                <div class="divide-y divide-slate-700/50">
                    @forelse($fraudLogs as $log)
                        <div class="indicator-item p-4 flex justify-between items-start {{ $log->fraud_score > 30 ? 'indicator-flagged' : '' }}">
                            <div>
                                <div class="font-semibold text-slate-200 mb-1">
                                    {{ str_replace('_', ' ', strtoupper($log->detection_type)) }}
                                </div>
                                <div class="text-xs text-slate-400 line-clamp-2 max-w-md">
                                    @foreach($log->details as $key => $val)
                                        @if(is_array($val))
                                            {{ $key }}: {{ count($val) }} items |
                                        @else
                                            {{ $key }}: {{ $val }} |
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            <div class="text-left">
                                <div class="text-lg font-bold {{ $log->fraud_score > 50 ? 'text-red-400' : ($log->fraud_score > 20 ? 'text-yellow-400' : 'text-green-400') }}">
                                    {{ $log->fraud_score }}%
                                </div>
                                <div class="text-[10px] text-slate-500">{{ $log->created_at->format('H:i:s') }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-500 italic">لا توجد سجلات تحليل لهذا العقار حتى الآن.</div>
                    @endforelse
                </div>
            </div>

            <!-- Duplicate Matches -->
            @if($duplicateMatches->count() > 0)
            <div class="fraud-card">
                <div class="p-4 border-b border-slate-700 flex justify-between items-center">
                    <h3 class="font-bold flex items-center gap-2">
                        <i class="fas fa-copy text-purple-400"></i>
                        التكرارات المحتملة ({{ $duplicateMatches->count() }})
                    </h3>
                    <a href="{{ route('admin.fraud.duplicates') }}" class="text-xs text-blue-400 hover:underline">عرض الكل</a>
                </div>
                <div class="p-4 space-y-4">
                    @foreach($duplicateMatches as $match)
                        @php 
                            $target = ($match->property_id == $property->id) ? $match->duplicateProperty : $match->property;
                        @endphp
                        <div class="flex items-center justify-between p-3 bg-slate-800/40 rounded-lg border border-slate-700">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-slate-700 rounded overflow-hidden">
                                    @if($target->main_image)
                                        <img src="{{ $target->main_image }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-slate-800 text-slate-500">
                                            <i class="fas fa-building text-sm"></i>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-white">{{ $target->title }}</div>
                                    <div class="text-xs text-slate-400">ID: #{{ $target->id }} | السعر: {{ number_format($target->price) }}</div>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-purple-400">{{ $match->similarity_score }}%</div>
                                <div class="text-[10px] uppercase text-slate-500">Similarity</div>
                            </div>
                            <div>
                                <a href="{{ route('admin.fraud.property-detail', $target->id) }}" class="text-slate-400 hover:text-white p-2">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- AI Alerts History -->
            <div class="fraud-card">
                <div class="p-4 border-b border-slate-700 bg-slate-800/50">
                    <h3 class="font-bold flex items-center gap-2">
                        <i class="fas fa-bell text-yellow-400"></i>
                        تنبيهات النظام الذكي
                    </h3>
                </div>
                <div class="p-0 overflow-x-auto text-sm">
                    <table class="w-full text-right">
                        <thead class="bg-slate-800/30 text-slate-400">
                            <tr>
                                <th class="px-4 py-2 font-medium">النوع</th>
                                <th class="px-4 py-2 font-medium">الخطر</th>
                                <th class="px-4 py-2 font-medium">الثقة</th>
                                <th class="px-4 py-2 font-medium">الحالة</th>
                                <th class="px-4 py-2 font-medium">التاريخ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/50">
                            @foreach($alerts as $alert)
                                <tr>
                                    <td class="px-4 py-3">{{ $alert->alert_type_label }}</td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold 
                                            @if($alert->risk_level === 'critical') bg-red-500 text-white
                                            @elseif($alert->risk_level === 'high') bg-orange-500 text-white
                                            @elseif($alert->risk_level === 'medium') bg-yellow-500 text-black
                                            @else bg-green-500 text-white
                                            @endif">
                                            {{ $alert->risk_level_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ $alert->confidence_score * 100 }}%</td>
                                    <td class="px-4 py-3 text-slate-400">{{ $alert->resolution_status_label }}</td>
                                    <td class="px-4 py-3 text-slate-500">{{ $alert->created_at->format('Y/m/d H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

{{-- Toast Notification --}}
<div id="fraud-toast" class="fixed top-4 right-4 z-50 hidden">
    <div class="flex items-center gap-3 px-5 py-3 rounded-xl shadow-2xl border text-sm font-medium" id="toast-content">
    </div>
</div>
@endsection

@section('scripts')
<script>
    function triggerAnalysis(id) {
        const btn = event.currentTarget;
        const icon = document.getElementById('analyzeIcon');
        
        btn.disabled = true;
        icon.classList.add('fa-spin');
        
        fetch(`/admin/fraud/analyze/property/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('نجاح', 'اكتمل تحليل الاحتيال بنجاح. جاري تحديث الصفحة...', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast('خطأ', data.error || 'فشل التحليل', 'danger');
            }
        })
        .finally(() => {
            btn.disabled = false;
            icon.classList.remove('fa-spin');
        });
    }

    function updateStatus(status) {
        if(!confirm('هل أنت متأكد من تغيير حالة الاحتيال لهذا العقار؟')) return;
        
        fetch(`/admin/fraud/properties/{{ $property->id }}/status`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('تم التحديث', data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            }
        });
    }

    // ── Toast Utility ────────────────────────────────────────
    function showToast(title, msg, type = 'info') {
        const toast = document.getElementById('fraud-toast');
        const content = document.getElementById('toast-content');
        
        const styles = {
            success: 'bg-slate-800 border-green-500/50 text-green-400',
            error: 'bg-slate-800 border-red-500/50 text-red-400',
            danger: 'bg-slate-800 border-red-500/50 text-red-400',
            info: 'bg-slate-800 border-blue-500/50 text-blue-400',
        };
        
        const icons = {
            success: 'check-circle',
            error: 'times-circle',
            danger: 'exclamation-triangle',
            info: 'info-circle'
        };

        content.className = `flex items-center gap-3 px-5 py-3 rounded-xl shadow-2xl border text-sm font-medium ${styles[type] || styles.info}`;
        content.innerHTML = `<i class="fas fa-${icons[type] || icons.info}"></i> <strong>${title}:</strong> ${msg}`;
        
        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.add('hidden'), 4000);
    }
</script>
@endsection
