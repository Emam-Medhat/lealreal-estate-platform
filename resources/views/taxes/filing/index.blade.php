@extends('admin.layouts.admin')

@section('title', 'تقديم الإقرارات الضريبية')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white flex items-center">
                            <i class="fas fa-file-invoice-dollar ml-3"></i>
                            تقديم الإقرارات الضريبية
                        </h1>
                        <p class="text-blue-100 mt-2">إدارة وتتبع جميع الإقرارات الضريبية الخاصة بك</p>
                    </div>
                    <div class="flex space-x-reverse space-x-3">
                        <a href="{{ route('taxes.filing.create') }}" 
                           class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                            <i class="fas fa-plus ml-2"></i>
                            إقرار جديد
                        </a>
                        <a href="{{ route('taxes.index') }}" 
                           class="bg-blue-700 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-800 transition-all duration-200">
                            <i class="fas fa-arrow-left ml-2"></i>
                            العودة
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">إجمالي الإقرارات</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $filings->total() }}</p>
                    </div>
                    <div class="bg-blue-100 rounded-lg p-3">
                        <i class="fas fa-file-alt text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">مسودة</p>
                        <p class="text-2xl font-bold text-gray-600">{{ $filings->where('status', 'draft')->count() }}</p>
                    </div>
                    <div class="bg-gray-100 rounded-lg p-3">
                        <i class="fas fa-edit text-gray-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">مقدمة</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $filings->where('status', 'submitted')->count() }}</p>
                    </div>
                    <div class="bg-yellow-100 rounded-lg p-3">
                        <i class="fas fa-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">معتمدة</p>
                        <p class="text-2xl font-bold text-green-600">{{ $filings->where('status', 'approved')->count() }}</p>
                    </div>
                    <div class="bg-green-100 rounded-lg p-3">
                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex items-center mb-4">
                <i class="fas fa-filter text-blue-600 ml-2"></i>
                <h2 class="text-lg font-semibold text-gray-900">البحث والتصفية</h2>
            </div>
            
            <form method="GET" action="{{ route('taxes.filing') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                id="status" name="status">
                            <option value="">جميع الحالات</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                            <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>مقدم</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>معتمد</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="tax_year" class="block text-sm font-medium text-gray-700 mb-2">السنة الضريبية</label>
                        <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                               id="tax_year" name="tax_year" value="{{ request('tax_year') }}" min="2020" max="{{ now()->year }}">
                    </div>
                    
                    <div>
                        <label for="filing_type" class="block text-sm font-medium text-gray-700 mb-2">نوع الإقرار</label>
                        <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" 
                                id="filing_type" name="filing_type">
                            <option value="">جميع الأنواع</option>
                            <option value="annual">سنوي</option>
                            <option value="quarterly">ربع سنوي</option>
                            <option value="amended">معدل</option>
                        </select>
                    </div>
                    
                    <div class="flex items-end space-x-reverse space-x-2">
                        <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            <i class="fas fa-search ml-2"></i>
                            بحث
                        </button>
                        <a href="{{ route('taxes.filing') }}" 
                           class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                            <i class="fas fa-redo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Filings List -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-list text-blue-600 ml-2"></i>
                    قائمة الإقرارات
                    <span class="mr-auto text-sm text-gray-500">{{ $filings->total() }} إقرار</span>
                </h2>
            </div>
            
            <div class="overflow-x-auto">
                @if($filings->count() > 0)
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم الإقرار</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العقار</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">السنة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النوع</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التاريخ</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المبلغ</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($filings as $filing)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">#{{ str_pad($filing->id, 6, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">{{ $filing->propertyTax->property->title ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">{{ $filing->tax_year }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @switch($filing->filing_type)
                                        @case('annual')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">سنوي</span>
                                            @break
                                        @case('quarterly')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">ربع سنوي</span>
                                            @break
                                        @case('amended')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">معدل</span>
                                            @break
                                        @default
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ $filing->filing_type }}</span>
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @switch($filing->status)
                                        @case('draft')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">مسودة</span>
                                            @break
                                        @case('submitted')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">مقدم</span>
                                            @break
                                        @case('approved')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">معتمد</span>
                                            @break
                                        @case('rejected')
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">مرفوض</span>
                                            @break
                                        @default
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">{{ $filing->status }}</span>
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">{{ $filing->submission_date ? $filing->submission_date->format('Y-m-d') : '-' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-gray-900">
                                        {{ $filing->approved_amount ? number_format($filing->approved_amount, 2) . ' ريال' : '-' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-left">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('taxes.filing.show', $filing) }}" 
                                           class="text-blue-600 hover:text-blue-900 transition-colors" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($filing->canBeEdited())
                                            <a href="{{ route('taxes.filing.edit', $filing) }}" 
                                               class="text-gray-600 hover:text-gray-900 transition-colors" title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                        @if($filing->status === 'draft')
                                            <button type="button" class="text-green-600 hover:text-green-900 transition-colors" 
                                                    onclick="submitFiling({{ $filing->id }})" title="تقديم">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <!-- Pagination -->
                    <div class="px-6 py-4 border-t border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                عرض {{ $filings->firstItem() }} إلى {{ $filings->lastItem() }} من {{ $filings->total() }} نتيجة
                            </div>
                            {{ $filings->links() }}
                        </div>
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-file-alt text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد إقرارات</h3>
                        <p class="text-gray-500 mb-6">لم يتم العثور على أي إقرارات ضريبية تطابق معايير البحث</p>
                        <a href="{{ route('taxes.filing.create') }}" 
                           class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            <i class="fas fa-plus ml-2"></i>
                            إنشاء إقرار جديد
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function submitFiling(filingId) {
    if (confirm('هل أنت متأكد من تقديم هذا الإقرار؟ لا يمكن تعديله بعد التقديم.')) {
        fetch(`/taxes/filings/${filingId}/submit`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ: ' + (data.message || 'يرجى المحاولة مرة أخرى'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ. يرجى المحاولة مرة أخرى.');
        });
    }
}

// Add smooth animations
document.addEventListener('DOMContentLoaded', function() {
    // Animate cards on scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-fade-in');
            }
        });
    });

    document.querySelectorAll('.bg-white').forEach(el => {
        observer.observe(el);
    });
});
</script>

@push('styles')
<style>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-out;
}

/* Custom scrollbar */
.overflow-x-auto::-webkit-scrollbar {
    height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Pagination styling */
.pagination {
    display: flex;
    gap: 0.25rem;
}

.pagination .page-item .page-link {
    border: none;
    color: #6b7280;
    padding: 0.5rem 0.75rem;
    border-radius: 0.375rem;
    transition: all 0.2s;
}

.pagination .page-item.active .page-link {
    background-color: #2563eb;
    color: white;
}

.pagination .page-item:not(.active):not(.disabled) .page-link:hover {
    background-color: #f3f4f6;
    color: #1f2937;
}
</style>
@endpush
