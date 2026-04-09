@extends('layouts.dashboard')

@section('title', 'سجل البلوكشين (Audit Ledger)')

@section('content')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">سجل النظام غير القابل للتعديل</h1>
                    <p class="text-gray-600">نظام تدقيق المعاملات (Immutable Audit Ledger) لضمان نزاهة البيانات</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="verifyChain()" id="verify-btn"
                        class="btn-primary bg-gradient-to-r from-blue-600 to-indigo-700 border-none">
                        <i class="fas fa-shield-check ml-2"></i>
                        تحقق من سلامة السلسلة
                    </button>
                </div>
            </div>
        </div>

        <!-- Chain Status Alert (Hidden by default) -->
        <div id="chain-status-alert"
            class="hidden px-6 py-4 rounded-xl border flex items-center justify-between transition-all duration-500">
            <div class="flex items-center">
                <div id="status-icon-container" class="p-2 rounded-lg mr-4">
                    <i id="status-icon" class="fas text-xl"></i>
                </div>
                <div>
                    <div id="status-title" class="font-black text-sm uppercase tracking-wider">جاري الفحص...</div>
                    <div id="status-message" class="text-xs mt-0.5 opacity-90">يتم الآن التحقق من SHA-256 لكل مدخل في السجل.
                    </div>
                </div>
            </div>
            <div id="status-stats" class="text-left font-mono text-[10px] space-y-0.5 opacity-70">
                <!-- Stats like total checked will go here -->
            </div>
        </div>

        <!-- Ledger Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <h2 class="text-xl font-bold text-gray-900">سجل العمليات</h2>
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-blue-100 text-blue-700">
                        SHARED-SECRET CHAIN
                    </span>
                </div>
                <div class="flex gap-2">
                    <div class="relative">
                        <i class="fas fa-search absolute right-3 top-3 text-gray-400 text-sm"></i>
                        <input type="text" placeholder="بحث في السجل..."
                            class="search-input pr-10 text-sm focus:ring-2 focus:ring-blue-500 outline-none border-none shadow-sm">
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead>
                        <tr class="text-gray-400 text-xs uppercase tracking-widest border-b border-gray-100 italic">
                            <th class="px-6 py-4 font-bold">الرقم</th>
                            <th class="px-6 py-4 font-bold">العملية</th>
                            <th class="px-6 py-4 font-bold">المرجع</th>
                            <th class="px-6 py-4 font-bold">الهاش الحالي (Current Hash)</th>
                            <th class="px-6 py-4 font-bold">الهاش السابق (Previous Hash)</th>
                            <th class="px-6 py-4 font-bold">بواسطة</th>
                            <th class="px-6 py-4 font-bold">التاريخ</th>
                            <th class="px-6 py-4 font-bold"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($data as $entry)
                            <tr class="hover:bg-gray-50/80 transition-colors group">
                                <td class="px-6 py-4 text-xs font-bold text-gray-400">#{{ $entry['id'] }}</td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2 py-1 rounded-md text-[10px] font-black uppercase tracking-wider bg-gray-100 text-gray-700 group-hover:bg-blue-100 group-hover:text-blue-700 transition-colors">
                                        {{ $entry['action'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs font-bold text-gray-900">{{ $entry['reference_type'] }}</div>
                                    <div class="text-[10px] text-gray-400">ID: {{ $entry['reference_id'] }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-[9px] font-mono text-blue-600 bg-blue-50/50 p-1.5 rounded border border-blue-100/50 inline-block max-w-[120px] truncate"
                                        title="{{ $entry['current_hash'] }}">
                                        {{ $entry['current_hash'] }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-[9px] font-mono text-gray-400 {{ $entry['previous_hash'] === 'genesis' ? 'italic font-bold text-indigo-400' : '' }} max-w-[120px] truncate"
                                        title="{{ $entry['previous_hash'] }}">
                                        {{ $entry['previous_hash'] }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-700">
                                    {{ $entry['recorded_by'] ?? 'System' }}
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500 whitespace-nowrap">
                                    {{ $entry['created_at'] }}
                                </td>
                                <td class="px-6 py-4 text-left">
                                    <button onclick="viewPayload({{ $entry['id'] }})"
                                        class="text-gray-400 hover:text-blue-600 transition">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500 italic">
                                    لا توجد سجلات في النظام بعد.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(is_object($data) && method_exists($data, 'links'))
                <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
                    {{ $data->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Payload Modal (Simple) -->
    <div id="payload-modal"
        class="hidden fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900">تفاصيل المعاملة (IPFS-ready Payload)</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6">
                <pre id="payload-content"
                    class="bg-gray-900 text-green-400 p-6 rounded-xl overflow-auto text-xs font-mono leading-relaxed max-h-[400px] shadow-inner"></pre>
            </div>
            <div class="p-6 border-t border-gray-100 flex justify-end">
                <button onclick="closeModal()" class="btn-secondary">إغلاق</button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            async function verifyChain() {
                const btn = document.getElementById('verify-btn');
                const alert = document.getElementById('chain-status-alert');
                const iconContainer = document.getElementById('status-icon-container');
                const icon = document.getElementById('status-icon');
                const title = document.getElementById('status-title');
                const message = document.getElementById('status-message');
                const stats = document.getElementById('status-stats');

                // Loading state
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin ml-2"></i> جاري التحقق...';

                alert.classList.remove('hidden', 'bg-red-50', 'border-red-200', 'bg-green-50', 'border-green-200');
                alert.classList.add('bg-blue-50', 'border-blue-200', 'flex');
                iconContainer.className = 'p-2 rounded-lg mr-4 bg-blue-100 text-blue-600';
                icon.className = 'fas fa-spinner fa-spin text-xl';
                title.textContent = 'جاري تدقيق السلسلة';
                title.className = 'font-black text-sm uppercase tracking-wider text-blue-800';
                message.textContent = 'نقوم بإعادة حساب الهاشات والتحقق من سلامة كل كتلة في السجل...';
                stats.innerHTML = '';

                try {
                    const response = await fetch('{{ route("blockchain.ledger.verify") }}');
                    const result = await response.json();

                    // Result state
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-shield-check ml-2"></i> تحقق من سلامة السلسلة';

                    if (result.valid) {
                        alert.classList.replace('bg-blue-50', 'bg-green-50');
                        alert.classList.replace('border-blue-200', 'border-green-200');
                        iconContainer.className = 'p-2 rounded-lg mr-4 bg-green-100 text-green-600';
                        icon.className = 'fas fa-check-double text-xl';
                        title.textContent = 'سلامة البيانات مؤكدة';
                        title.className = 'font-black text-sm uppercase tracking-wider text-green-800';
                        message.textContent = result.message;
                    } else {
                        alert.classList.replace('bg-blue-50', 'bg-red-50');
                        alert.classList.replace('border-blue-200', 'border-red-200');
                        iconContainer.className = 'p-2 rounded-lg mr-4 bg-red-100 text-red-600';
                        icon.className = 'fas fa-exclamation-triangle text-xl';
                        title.textContent = '⚠ تم اكتشاف تلاعب بالبيانات';
                        title.className = 'font-black text-sm uppercase tracking-wider text-red-800';
                        message.textContent = result.message;
                    }

                    stats.innerHTML = `
                        <div>Checked: ${result.total_checked} entries</div>
                        <div>Status: ${result.valid ? 'SECURE' : 'COMPROMISED'}</div>
                        <div>ID: ${result.broken_entry_id || 'NONE'}</div>
                    `;

                } catch (error) {
                    console.error('Verification failed:', error);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-shield-check ml-2"></i> تحقق من سلامة السلسلة';
                    alert.classList.add('hidden');
                }
            }

            async function viewPayload(id) {
                try {
                    const response = await fetch(`{{ url('blockchain/ledger') }}/${id}`);
                    const data = await response.json();

                    document.getElementById('payload-content').textContent = JSON.stringify(data.payload, null, 4);
                    document.getElementById('payload-modal').classList.remove('hidden');
                    document.getElementById('payload-modal').classList.add('flex');
                } catch (error) {
                    console.error('Failed to fetch payload:', error);
                }
            }

            function closeModal() {
                document.getElementById('payload-modal').classList.add('hidden');
                document.getElementById('payload-modal').classList.remove('flex');
            }
        </script>
    @endpush
@endsection