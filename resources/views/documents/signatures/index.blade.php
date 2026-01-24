@extends('layouts.app')

@section('title', 'التوقيعات الرقمية')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">التوقيعات الرقمية</h1>
        <div class="flex space-x-2 space-x-reverse">
            <a href="{{ route('documents.signatures.create', $document) }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                <i class="fas fa-plus ml-2"></i>طلب توقيع جديد
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث عن توقيع..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                    <option value="signed" {{ request('status') == 'signed' ? 'selected' : '' }}>موقّع</option>
                    <option value="revoked" {{ request('status') == 'revoked' ? 'selected' : '' }}>ملغي</option>
                </select>
            </div>
            <div>
                <select name="signature_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع الأنواعف</option>
                    <option value="digital" {{ request('signature_type') == 'digital' ? 'selected' : '' }}>رقمي</option>
                    <option value="electronic" {{ request('signature_type') == 'electronic' ? 'selected' : '' }}>إلكتروني</option>
                    <option value="handwritten" {{ request('signature_type') == 'handwritten' ? 'selected' : '' }}>يدوي</option>
                    <option value="stamp" {{ request('signature_type') == 'stamp' ? 'selected' : '' }}>ختم</option>
                </select>
            </div>
            <div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 w-full">
                    <i class="fas fa-search ml-2"></i>بحث
                </button>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-yellow-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">في الانتظار</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $signatures->where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-green-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-signature"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">موقّع</p>
                    <p class="text-2xl font-bold text-green-600">{{ $signatures->where('status', 'signed')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-red-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">ملغي</p>
                    <p class="text-2xl font-bold text-red-600">{{ $signatures->where('status', 'revoked')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-blue-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-percentage"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">معدد التوقيع</p>
                    <p class="text-2xl font-bold text-blue-600">
                        @if($signatures->count() > 0)
                            {{ round(($signatures->where('status', 'signed')->count() / $signatures->count()) * 100, 1) }}%
                        @else
                            0%
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Signatures Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الوثيقة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الموقّع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">البريد الإلكتروني</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">نوع التوقيع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ التوقيع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($signatures as $signature)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $signature->document->title }}</div>
                                <div class="text-sm text-gray-500">{{ $signature->document->file_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $signature->signer_name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $signature->signer_email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($signature->signature_type)
                                    @case('digital')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            رقمي
                                        </span>
                                        @break
                                    @case('electronic')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            إلكتروني
                                        </span>
                                        @break
                                    @case('handwritten')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            يدوي
                                        </span>
                                        @break
                                    @case('stamp')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                            ختم
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($signature->status)
                                    @case('pending')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            في الانتظار
                                        </span>
                                        @break
                                    @case('signed')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            موقّع
                                        </span>
                                        @break
                                    @case('revoked')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            ملغي
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $signature->signed_at ? $signature->signed_at->format('Y-m-d H:i') : '---' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                <a href="{{ route('documents.signatures.show', $signature) }}" class="text-blue-600 hover:text-blue-900 ml-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($signature->status === 'pending')
                                    <a href="{{ route('documents.signatures.verify', $signature) }}" class="text-green-600 hover:text-green-900 ml-2">
                                        <i class="fas fa-check"></i>
                                    </a>
                                    <a href="{{ route('documents.signatures.revoke', $signature) }}" class="text-red-600 hover:text-red-900 ml-2">
                                        <i class="fas fa-times"></i>
                                    </a>
                                @endif
                                @if($signature->status === 'signed')
                                    <a href="{{ route('documents.signatures.download', $signature) }}" class="text-gray-600 hover:text-gray-900 ml-2">
                                        <i class="fas fa-download"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                لا توجد توقيعات
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            {{ $signatures->links() }}
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <h2 class="text-lg font-semibold mb-4">إجراءات جماعية</h2>
        
        <div class="flex flex-wrap gap-3">
            <button onclick="showBulkSignModal()" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                <i class="fas fa-users ml-2"></i>توقيع جماعي
            </button>
            <button onclick="exportSignatures()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                <i class="fas fa-download ml-2"></i>تصدير التوقيعات
            </button>
            <button onclick="sendReminders()" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">
                <i class="fas fa-bell ml-2"></i>إرسال تذكيرات
            </button>
        </div>
    </div>
</div>

<!-- Bulk Sign Modal -->
<div id="bulkSignModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full z-50">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">توقيع جماعي</h3>
                <button onclick="hideBulkSignModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="p-6">
                <form id="bulkSignForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">اختر التوقيعات</label>
                        <div class="space-y-2 max-h-60 overflow-y-auto">
                            @foreach($signatures->where('status', 'pending') as $signature)
                                <div class="flex items-center p-2 border rounded">
                                    <input type="checkbox" name="signature_ids[]" value="{{ $signature->id }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <label class="mr-3 text-sm text-gray-700">
                                        {{ $signature->signer_name }} - {{ $signature->document->title }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">رسالة مخصصة (اختياري)</label>
                        <textarea name="message" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="أدخل رسالة مخصصة للموقيعين..."></textarea>
                    </div>
                    
                    <div class="flex justify-end space-x-4 space-x-reverse">
                        <button type="button" onclick="hideBulkSignModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                            إلغاء
                        </button>
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                            <i class="fas fa-users ml-2"></i>توقيع المحدد
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
function showBulkSignModal() {
    document.getElementById('bulkSignModal').classList.remove('hidden');
}

function hideBulkSignModal() {
    document.getElementById('bulkSignModal').classList.add('hidden');
}

function exportSignatures() {
    window.location.href = '{{ route('documents.signatures.export') }}';
}

function sendReminders() {
    // Implementation for sending reminders
    alert('سيتم إرسال التذكيرات إلى جميع التوقيعات في الانتظار');
}

// Bulk sign form submission
document.getElementById('bulkSignForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const signatureIds = formData.getAll('signature_ids[]');
    
    if (signatureIds.length === 0) {
        alert('يرجى اختيار على الأقل توقيع واحد');
        return;
    }
    
    // Submit bulk sign request
    fetch('{{ route('documents.signatures.bulk') }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/x-www-form-urlencoded',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            hideBulkSignModal();
            location.reload();
        } else {
            alert('حدث خطأء: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأء أثناء إرسال الطلب');
    });
});
</script>
@endsection
