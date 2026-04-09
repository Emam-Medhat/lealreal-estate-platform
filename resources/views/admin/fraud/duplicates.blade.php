@extends('admin.layouts.admin')

@section('title', 'مراجعة الإعلانات المكررة')
@section('page-title', 'مراجعة الإعلانات المكررة')

@push('styles')
    <style>
        :root {
            --brand-blue:   #0D6EFD;
            --brand-purple: #6F42C1;
            --brand-green:  #198754;
            --brand-orange: #FD7E14;
            --brand-red:    #DC3545;
            --brand-teal:   #0DCAF0;
            --brand-yellow: #FFC107;

            --fraud-bg:     #F1F3F8;
            --fraud-card:   #FFFFFF;
            --fraud-border: #E5E7EB;
            --fraud-text:   #1A1F36;
            --fraud-muted:  #6B7280;
        }

        .dup-dashboard {
            background: var(--fraud-bg);
            min-height: 100vh;
            color: var(--fraud-text);
        }

        .dup-card {
            background: var(--fraud-card);
            border: 1px solid var(--fraud-border);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 1rem;
            transition: transform 0.2s, box-shadow 0.2s;
            box-shadow: 0 8px 24px rgba(13,110,253,.08);
        }

        .dup-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 36px rgba(13,110,253,.12);
        }

        .similarity-ring {
            width: 80px;
            height: 80px;
            position: relative;
            flex-shrink: 0;
        }

        .similarity-ring svg {
            transform: rotate(-90deg);
        }

        .similarity-ring .score-text {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            transform: rotate(0deg);
        }

        .property-mini-card {
            background: var(--fraud-card);
            border: 1px solid var(--fraud-border);
            border-radius: 12px;
            padding: 1rem;
            flex: 1;
            box-shadow: 0 4px 16px rgba(0,0,0,.06);
        }

        .weight-bar-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
        }

        .weight-bar {
            height: 6px;
            border-radius: 10px;
            background: var(--fraud-border);
            flex: 1;
            overflow: hidden;
        }

        .weight-bar-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 1s ease;
        }

        .dup-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 20px;
            border-radius: 10px;
            font-size: 0.83rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }

        .dup-btn-confirm {
            background: linear-gradient(90deg, var(--brand-red), #b02a37);
            color: #fff;
            box-shadow: 0 6px 18px rgba(220,53,69,.25);
        }

        .dup-btn-reject {
            background: linear-gradient(90deg, var(--brand-green), #146c43);
            color: #fff;
            box-shadow: 0 6px 18px rgba(25,135,84,.25);
        }

        .dup-btn:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
        }

        .filter-bar {
            background: var(--fraud-card);
            border: 1px solid var(--fraud-border);
            border-radius: 14px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
            box-shadow: 0 6px 20px rgba(13,110,253,.06);
        }

        .filter-chip {
            padding: 5px 16px;
            border-radius: 50px;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid var(--fraud-border);
            color: var(--fraud-muted);
            background: transparent;
        }

        .filter-chip.active {
            background: linear-gradient(90deg, rgba(13,110,253,.12), rgba(111,66,193,.12));
            color: var(--brand-purple);
            border-color: rgba(111,66,193,.35);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        /* Controls */
        .dup-dashboard select {
            background: #fff;
            border: 1px solid var(--fraud-border);
            color: var(--fraud-text);
            border-radius: 10px;
            padding: 6px 10px;
        }
        .dup-dashboard .text-slate-100 { color: var(--fraud-text) !important; }
        .dup-dashboard .text-slate-500 { color: var(--fraud-muted) !important; }
        .dup-dashboard .text-slate-400 { color: var(--fraud-muted) !important; }
        .dup-dashboard .bg-slate-700 { background: var(--brand-blue) !important; }
        .dup-dashboard .hover\:bg-slate-600:hover { background: #0B5ED7 !important; }
    </style>
@endpush

@section('content')
    <div class="dup-dashboard p-1">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.fraud.dashboard') }}"
                    class="w-9 h-9 rounded-xl bg-slate-700 hover:bg-slate-600 flex items-center justify-center transition-colors">
                    <i class="fas fa-arrow-right text-slate-400"></i>
                </a>
                <div>
                    <h1 class="text-xl font-bold text-slate-100">مراجعة الإعلانات المكررة</h1>
                    <p class="text-slate-500 text-sm">{{ $duplicates->total() }} زوج بانتظار المراجعة</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-slate-500">نظام الكشف الذكي بالأوزان:</span>
                <span class="text-purple-400 text-xs font-semibold">صور 40% | موقع 30% | عنوان 15% | سعر 15%</span>
            </div>
        </div>

        {{-- Filters --}}
        <div class="filter-bar">
            <span class="text-slate-400 text-sm font-semibold ml-2">التصفية:</span>
            @foreach(['pending' => 'قيد الانتظار', 'confirmed' => 'مؤكد', 'rejected' => 'مرفوض', 'merged' => 'مدمج'] as $val => $label)
                <a href="{{ request()->fullUrlWithQuery(['status' => $val]) }}"
                    class="filter-chip {{ $status === $val ? 'active' : '' }}">
                    {{ $label }}
                </a>
            @endforeach

            <div class="mr-auto flex items-center gap-3">
                <label class="text-slate-400 text-sm">الحد الأدنى للتشابه:</label>
                <form method="GET">
                    <input type="hidden" name="status" value="{{ $status }}">
                    <select name="min_score" onchange="this.form.submit()"
                        class="bg-slate-700 border border-slate-600 text-slate-300 text-sm rounded-lg px-3 py-1.5">
                        @foreach([60, 70, 80, 90, 95] as $s)
                            <option value="{{ $s }}" {{ $minScore == $s ? 'selected' : '' }}>{{ $s }}%+</option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        {{-- Duplicate Cards --}}
        @forelse($duplicates as $dup)
            @php
                $score = $dup->similarity_score;
                $scoreColor = $score >= 95 ? '#ef4444' : ($score >= 85 ? '#f97316' : ($score >= 75 ? '#eab308' : '#3b82f6'));
                $circumference = 2 * M_PI * 30;
                $dashOffset = $circumference * (1 - $score / 100);
            @endphp
            <div class="dup-card" id="dup-card-{{ $dup->id }}">
                <div class="p-5">
                    <div class="flex flex-col lg:flex-row gap-4">

                        {{-- Similarity Ring --}}
                        <div class="flex items-center gap-4">
                            <div class="similarity-ring">
                                <svg width="80" height="80" viewBox="0 0 80 80">
                                    <circle cx="40" cy="40" r="30" fill="none" stroke="#334155" stroke-width="8" />
                                    <circle cx="40" cy="40" r="30" fill="none" stroke="{{ $scoreColor }}" stroke-width="8"
                                        stroke-linecap="round" stroke-dasharray="{{ $circumference }}"
                                        stroke-dashoffset="{{ $dashOffset }}" />
                                </svg>
                                <div class="score-text">
                                    <span class="text-lg font-bold" style="color: {{ $scoreColor }};">{{ $score }}%</span>
                                    <span class="text-slate-500 text-xs">تشابه</span>
                                </div>
                            </div>
                        </div>

                        {{-- Properties Comparison --}}
                        <div class="flex flex-1 gap-3 flex-col md:flex-row">

                            {{-- Property A --}}
                            <div class="property-mini-card">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="status-dot bg-blue-400"></span>
                                    <span class="text-xs text-slate-500 font-semibold">الإعلان الأصلي</span>
                                </div>
                                <p class="text-slate-200 text-sm font-semibold mb-1">
                                    {{ Str::limit($dup->property?->title ?? 'غير معروف', 50) }}</p>
                                <div class="flex gap-3 text-xs text-slate-400">
                                    <span><i class="fas fa-city ml-1 opacity-50"></i>{{ $dup->property?->city ?? '—' }}</span>
                                    <span><i
                                            class="fas fa-tag ml-1 opacity-50"></i>{{ number_format($dup->property?->price ?? 0) }}</span>
                                </div>
                                <div class="text-xs text-slate-600 mt-1">#{{ $dup->property_id }}</div>
                            </div>

                            {{-- VS Divider --}}
                            <div class="hidden md:flex items-center justify-center px-2">
                                <div class="text-center">
                                    <div
                                        class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-slate-400 text-xs font-bold mb-1">
                                        VS</div>
                                    <i class="fas fa-arrows-alt-h text-slate-600"></i>
                                </div>
                            </div>

                            {{-- Property B --}}
                            <div class="property-mini-card border-orange-500/20">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="status-dot bg-orange-400"></span>
                                    <span class="text-xs text-slate-500 font-semibold">الإعلان المشبوه</span>
                                </div>
                                <p class="text-slate-200 text-sm font-semibold mb-1">
                                    {{ Str::limit($dup->duplicateProperty?->title ?? 'غير معروف', 50) }}</p>
                                <div class="flex gap-3 text-xs text-slate-400">
                                    <span><i
                                            class="fas fa-city ml-1 opacity-50"></i>{{ $dup->duplicateProperty?->city ?? '—' }}</span>
                                    <span><i
                                            class="fas fa-tag ml-1 opacity-50"></i>{{ number_format($dup->duplicateProperty?->price ?? 0) }}</span>
                                </div>
                                <div class="text-xs text-slate-600 mt-1">#{{ $dup->duplicate_property_id }}</div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        @if($status === 'pending')
                            <div class="flex flex-row lg:flex-col gap-2 justify-start lg:justify-center">
                                <button onclick="reviewDuplicate({{ $dup->id }}, 'confirm')" class="dup-btn dup-btn-confirm">
                                    <i class="fas fa-check-circle"></i> تأكيد مكرر
                                </button>
                                <button onclick="reviewDuplicate({{ $dup->id }}, 'reject')" class="dup-btn dup-btn-reject">
                                    <i class="fas fa-times-circle"></i> رفض
                                </button>
                            </div>
                        @else
                            <div class="flex items-center">
                                <span
                                    class="text-xs px-3 py-1.5 rounded-lg border
                                    {{ $dup->status === 'confirmed' ? 'border-red-500/30 text-red-400 bg-red-500/10' : 'border-green-500/30 text-green-400 bg-green-500/10' }}">
                                    {{ $dup->status === 'confirmed' ? '🚫 مؤكد مكرر' : '✅ مرفوض' }}
                                    @if($dup->reviewer)
                                        <br><span class="text-slate-500 text-xs">بواسطة: {{ $dup->reviewer->name }}</span>
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Weight Breakdown --}}
                    <div class="mt-4 pt-4 border-t border-slate-700">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @php
                                $components = [
                                    ['label' => '🖼️ صور', 'val' => $dup->image_similarity, 'color' => '#8b5cf6'],
                                    ['label' => '📍 موقع', 'val' => $dup->location_similarity, 'color' => '#3b82f6'],
                                    ['label' => '📝 عنوان', 'val' => $dup->title_similarity, 'color' => '#eab308'],
                                    ['label' => '💰 سعر', 'val' => $dup->price_similarity, 'color' => '#f97316'],
                                ];
                            @endphp
                            @foreach($components as $comp)
                                <div>
                                    <div class="weight-bar-row">
                                        <span class="text-slate-500 text-xs w-14">{{ $comp['label'] }}</span>
                                        <div class="weight-bar">
                                            <div class="weight-bar-fill"
                                                style="width: {{ $comp['val'] }}%; background: {{ $comp['color'] }};"></div>
                                        </div>
                                        <span class="text-slate-300 text-xs w-8 text-right">{{ $comp['val'] }}%</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-20 text-slate-500">
                <i class="fas fa-check-double text-5xl mb-4 opacity-20"></i>
                <p class="text-lg">لا توجد إعلانات مكررة {{ $status === 'pending' ? 'قيد الانتظار' : '' }}</p>
                <p class="text-sm mt-1 opacity-60">سيظهر هنا أي إعلان يتجاوز نسبة التشابه المحددة</p>
            </div>
        @endforelse

        {{-- Pagination --}}
        @if($duplicates->hasPages())
            <div class="mt-6 text-slate-400 text-sm">
                {{ $duplicates->links() }}
            </div>
        @endif

    </div>

    {{-- Toast --}}
    <div id="fraud-toast" class="fixed top-4 right-4 z-50 hidden">
        <div class="flex items-center gap-3 px-5 py-3 rounded-xl shadow-2xl border text-sm font-medium" id="toast-content">
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;

        async function reviewDuplicate(id, action) {
            const labels = { confirm: 'تأكيد الإعلان المكرر وحظره', reject: 'رفض هذا التكرار' };
            if (!confirm(labels[action] + '؟')) return;

            try {
                const res = await fetch(`/admin/fraud/duplicates/${id}/review`, {
                    method: 'PATCH',
                    headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action })
                });
                const data = await res.json();

                if (data.success) {
                    showToast(data.message, 'success');
                    document.getElementById(`dup-card-${id}`)?.remove();
                } else {
                    showToast(data.message || 'حدث خطأ', 'error');
                }
            } catch (e) {
                showToast('خطأ في الاتصال', 'error');
            }
        }

        function showToast(msg, type = 'info') {
            const toast = document.getElementById('fraud-toast');
            const content = document.getElementById('toast-content');
            const styles = {
                success: 'bg-slate-800 border-green-500/50 text-green-400',
                error: 'bg-slate-800 border-red-500/50 text-red-400',
                info: 'bg-slate-800 border-blue-500/50 text-blue-400',
            };
            content.className = `flex items-center gap-3 px-5 py-3 rounded-xl shadow-2xl border text-sm font-medium ${styles[type]}`;
            content.innerHTML = `<i class="fas fa-${type === 'success' ? 'check' : 'times'}-circle"></i> ${msg}`;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 4000);
        }
    </script>
@endpush
