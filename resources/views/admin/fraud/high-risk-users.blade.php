@extends('admin.layouts.admin')

@section('title', 'المستخدمين عاليي المخاطر')

@section('styles')
    <style>
        .fraud-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            color: white;
        }

        .risk-indicator {
            height: 8px;
            border-radius: 4px;
            background: #1e293b;
            overflow: hidden;
        }

        .risk-progress {
            height: 100%;
            transition: width 1s ease-in-out;
        }
    </style>
@endsection

@section('content')
    <div class="p-6">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-white mb-2">المستخدمين عاليي المخاطر</h1>
                <p class="text-slate-400">قائمة المستخدين الذين تم رصد أنشطة مشبوهة لهم بنسبة مخاطرة {{ $minRisk }}% أو أكثر
                </p>
            </div>

            <form action="{{ route('admin.fraud.high-risk-users') }}" method="GET" class="flex gap-4">
                <select name="min_risk" onchange="this.form.submit()"
                    class="bg-slate-800 text-white border border-slate-700 rounded-lg px-4 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="30" {{ $minRisk == 30 ? 'selected' : '' }}>مخاطرة +30%</option>
                    <option value="50" {{ $minRisk == 50 ? 'selected' : '' }}>مخاطرة +50%</option>
                    <option value="70" {{ $minRisk == 70 ? 'selected' : '' }}>مخاطرة +70%</option>
                    <option value="90" {{ $minRisk == 90 ? 'selected' : '' }}>مخاطرة +90%</option>
                </select>
            </form>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <div class="fraud-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-right">
                        <thead class="bg-slate-800/50 text-slate-400 text-sm uppercase">
                            <tr>
                                <th class="px-6 py-4 font-semibold">المستخدم</th>
                                <th class="px-6 py-4 font-semibold">درجة المخاطرة</th>
                                <th class="px-6 py-4 font-semibold">عوامل الخطر</th>
                                <th class="px-6 py-4 font-semibold">الحالة</th>
                                <th class="px-6 py-4 font-semibold">آخر نشاط</th>
                                <th class="px-6 py-4 font-semibold">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/50">
                            @forelse($users as $user)
                                <tr class="hover:bg-slate-800/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-full bg-slate-700 flex items-center justify-center text-blue-400 font-bold">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-medium text-white">{{ $user->name }}</div>
                                                <div class="text-xs text-slate-400">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="w-32">
                                            <div class="flex justify-between items-center mb-1">
                                                <span
                                                    class="text-xs font-bold {{ $user->risk_score > 70 ? 'text-red-400' : ($user->risk_score > 40 ? 'text-yellow-400' : 'text-green-400') }}">
                                                    {{ $user->risk_score }}%
                                                </span>
                                            </div>
                                            <div class="risk-indicator">
                                                <div class="risk-progress {{ $user->risk_score > 70 ? 'bg-red-500' : ($user->risk_score > 40 ? 'bg-yellow-500' : 'bg-green-500') }}"
                                                    style="width: {{ $user->risk_score }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @php $factors = $user->risk_factors ?? []; @endphp
                                            @forelse($factors as $factor)
                                                <span
                                                    class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 text-[10px] border border-slate-700">
                                                    {{ str_replace('_', ' ', $factor) }}
                                                </span>
                                            @empty
                                                <span class="text-slate-500 text-xs">-</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($user->is_restricted)
                                            <span
                                                class="px-2 py-1 rounded-full bg-red-500/10 text-red-400 text-xs border border-red-500/20">
                                                <i class="fas fa-user-slash mr-1"></i> مقيد
                                            </span>
                                        @else
                                            <span
                                                class="px-2 py-1 rounded-full bg-green-500/10 text-green-400 text-xs border border-green-500/20">
                                                <i class="fas fa-check mr-1"></i> نشط
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-400">
                                        {{ $user->updated_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex gap-2">
                                            <a href="{{ route('admin.users.show', $user->id) }}"
                                                class="p-2 bg-slate-700 hover:bg-slate-600 rounded text-slate-200 transition-all"
                                                title="عرض الملف">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($user->is_restricted)
                                                <button onclick="liftRestriction({{ $user->id }})"
                                                    class="p-2 bg-blue-600/20 hover:bg-blue-600/40 rounded text-blue-400 border border-blue-600/30 transition-all"
                                                    title="رفع التقييد">
                                                    <i class="fas fa-unlock"></i>
                                                </button>
                                            @else
                                                <button onclick="restrictUser({{ $user->id }})"
                                                    class="p-2 bg-red-600/20 hover:bg-red-600/40 rounded text-red-400 border border-red-600/30 transition-all"
                                                    title="تقييد المستخدم">
                                                    <i class="fas fa-user-lock"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-slate-500 italic">
                                        <div class="mb-3"><i class="fas fa-user-shield text-4xl opacity-20"></i></div>
                                        لا يوجد مستخدمين مخاطرتهم أعلى من {{ $minRisk }}% في الوقت الحالي
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-slate-700/50 bg-slate-800/20">
                    {{ $users->links() }}
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
        function liftRestriction(userId) {
            if (!confirm('هل أنت متأكد من رغبتك في رفع التقييد عن هذا المستخدم؟')) return;

            fetch(`/admin/fraud/users/${userId}/lift-restriction`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast('نجاح', 'تم رفع التقييد عن المستخدم بنجاح', 'success');
                        setTimeout(() => location.reload(), 1000);
                    }
                });
        }

        function restrictUser(userId) {
            if (!confirm('هل أنت متأكد من رغبتك في تقييد هذا المستخدم؟ لن يتمكن من إضافة إعلانات جديدة.')) return;

            // This could call a general user update or specific fraud endpoint
            fetch(`/admin/users/${userId}/restrict`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    showToast('نجاح', 'تم تقييد المستخدم بنجاح', 'success');
                    setTimeout(() => location.reload(), 1000);
                });
        }
        }

        // ── Toast Utility ────────────────────────────────────────
        function showToast(title, msg, type = 'info') {
            const toast = document.getElementById('fraud-toast');
            const content = document.getElementById('toast-content');
            
            const styles = {
                success: 'bg-slate-800 border-green-500/50 text-green-400',
                error: 'bg-slate-800 border-red-500/50 text-red-400',
                info: 'bg-slate-800 border-blue-500/50 text-blue-400',
            };
            
            const icons = {
                success: 'check-circle',
                error: 'times-circle',
                info: 'info-circle'
            };

            content.className = `flex items-center gap-3 px-5 py-3 rounded-xl shadow-2xl border text-sm font-medium ${styles[type] || styles.info}`;
            content.innerHTML = `<i class="fas fa-${icons[type] || icons.info}"></i> <strong>${title}:</strong> ${msg}`;
            
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 4000);
        }
    </script>
@endsection