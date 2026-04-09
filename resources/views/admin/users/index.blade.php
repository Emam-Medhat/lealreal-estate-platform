@extends('layouts.dashboard')

@section('title', __('admin.users_management'))

@push('styles')
<style>
/* Custom checkbox styling */
input[type="checkbox"] {
    appearance: none;
    background-color: #ffffff;
    border: 2px solid #d1d5db;
    position: relative;
    transition: all 0.2s ease;
}

input[type="checkbox"]:checked {
    background-color: #3b82f6;
    border-color: #3b82f6;
}

input[type="checkbox"]:checked::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 14px;
    font-weight: bold;
}

input[type="checkbox"]:hover {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

input[type="checkbox"]:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
}

/* Pagination styling */
.pagination {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.pagination .page-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 2.5rem;
    height: 2.5rem;
    padding: 0 0.75rem;
    border-radius: 0.5rem;
    border: 1px solid #d1d5db;
    background-color: #ffffff;
    color: #374151;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
}

.pagination .page-link:hover {
    background-color: #f3f4f6;
    border-color: #9ca3af;
    color: #111827;
}

.pagination .page-item.active .page-link {
    background-color: #3b82f6;
    border-color: #3b82f6;
    color: #ffffff;
}

.pagination .page-item.disabled .page-link {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}

/* Enhance table header */
thead th {
    position: sticky;
    top: 0;
    z-index: 10;
}

/* Better hover effects */
tr:hover input[type="checkbox"] {
    border-color: #3b82f6;
    transform: scale(1.05);
}

/* Selected row highlighting */
tr:has(input[type="checkbox"]:checked) {
    background-color: #eff6ff !important;
}

/* Bulk actions improvements */
#bulk-actions {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Selected count badge */
#selected-count {
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.8); }
    to { opacity: 1; transform: scale(1); }
}
</style>
@endpush

@section('content')
<div class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen p-6">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl shadow-xl overflow-hidden mb-8">
            <div class="relative h-32 bg-gradient-to-r from-blue-600 to-indigo-600">
                <div class="absolute inset-0 bg-black opacity-20"></div>
                <div class="relative px-8 py-6">
                    <h1 class="text-3xl font-bold text-white mb-2">{{ __('admin.users_management') }}</h1>
                    <p class="text-blue-100">{{ __('admin.manage_all_users') }}</p>
                </div>
            </div>
            
            <div class="px-8 pb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-6">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $users->total() }}</div>
                            <div class="text-sm text-gray-600">{{ __('admin.total_users') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ App\Models\User::where('account_status', 'active')->count() }}</div>
                            <div class="text-sm text-gray-600">{{ __('admin.active') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ App\Models\User::where('user_type', 'admin')->count() }}</div>
                            <div class="text-sm text-gray-600">{{ __('admin.admins') }}</div>
                        </div>
                    </div>
                    
                    <a href="{{ route('admin.users.create') }}" class="bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-3 rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all duration-200 shadow-lg flex items-center">
                        <i class="fas fa-user-plus ml-2"></i>
                        {{ __('admin.add_user') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Success and Error Messages -->
        @if(session('success'))
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 p-4 mb-6 rounded-xl shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-500 ml-3 text-xl"></i>
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 p-4 mb-6 rounded-xl shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-500 ml-3 text-xl"></i>
                    <p class="text-red-800 font-medium">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Users Table -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600 font-medium">
                            {{ __('admin.showing_results', ['from' => $users->firstItem(), 'to' => $users->lastItem(), 'total' => $users->total()]) }}
                        </span>
                        <span id="selected-count" class="hidden text-sm font-bold text-blue-600 bg-blue-50 px-3 py-1 rounded-full">
                            {{ __('admin.selected_count') }} <span id="selected-number">0</span> {{ __('admin.users') }}
                        </span>
                    </div>
                    <div class="flex flex-col sm:flex-row items-center gap-3">
                        <!-- Search Form -->
                        <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center space-x-2">
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('admin.search_placeholder') }}" 
                                       class="pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white shadow-sm w-64">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                            <button type="submit" class="px-4 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-sm">
                                <i class="fas fa-search"></i>
                            </button>
                            @if(request('search'))
                                <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-all duration-200">
                                    <i class="fas fa-times"></i>
                                </a>
                            @endif
                        </form>
                        
                        <!-- Bulk Actions -->
                        <div id="bulk-actions" class="hidden flex items-center space-x-2">
                            <button onclick="selectAll()" class="px-3 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-all duration-200 text-sm">
                                <i class="fas fa-check-square ml-2"></i>
                                {{ __('admin.select_all') }}
                            </button>
                            <button onclick="deselectAll()" class="px-3 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-all duration-200 text-sm">
                                <i class="fas fa-square ml-2"></i>
                                {{ __('admin.deselect_all') }}
                            </button>
                            <button onclick="bulkDelete()" class="px-4 py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-200 shadow-sm">
                                <i class="fas fa-trash ml-2"></i>
                                {{ __('admin.delete_selected') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <form id="bulk-delete-form" method="POST" action="{{ route('admin.users.bulkDelete') }}" style="display: none;">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="user_ids" id="bulk-user-ids">
                </form>
                
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-center">
                                <input type="checkbox" id="select-all-checkbox" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">المستخدم</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">البريد الإلكتروني</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">النوع</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">تاريخ الانضمام</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($users as $user)
                            <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all duration-200">
                                <td class="px-6 py-4 text-center">
                                    <input type="checkbox" class="user-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500" value="{{ $user->id }}">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-12 w-12 flex-shrink-0">
                                            @if($user->avatar_url)
                                                <img src="{{ $user->avatar_url }}" 
                                                     class="h-12 w-12 rounded-full object-cover border-2 border-white shadow-md" 
                                                     alt="{{ $user->first_name }} {{ $user->last_name }}">
                                            @else
                                                <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center shadow-md">
                                                    <i class="fas fa-user text-gray-400 text-xl"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="mr-4">
                                            <div class="text-sm font-bold text-gray-900">{{ $user->first_name }} {{ $user->last_name }}</div>
                                            <div class="text-sm text-gray-500 font-medium">{{ '@' . ($user->username ?? ($user->first_name . $user->last_name)) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-medium">{{ $user->email }}</div>
                                    @if($user->phone)
                                        <div class="text-sm text-gray-500">{{ $user->phone }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold shadow-sm
                                        {{ $user->user_type === 'admin' ? 'bg-gradient-to-r from-purple-500 to-purple-600 text-white' : 
                                           ($user->user_type === 'agent' ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white' : 'bg-gradient-to-r from-green-500 to-green-600 text-white') }}">
                                        <i class="fas {{ $user->user_type === 'admin' ? 'fa-crown' : 
                                                   ($user->user_type === 'agent' ? 'fa-briefcase' : 'fa-user') }} ml-2 text-xs"></i>
                                        {{ ucfirst($user->user_type ?? 'user') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold shadow-sm
                                        {{ $user->account_status === 'active' ? 'bg-gradient-to-r from-green-500 to-emerald-600 text-white' : 'bg-gradient-to-r from-red-500 to-pink-600 text-white' }}">
                                        <span class="w-2 h-2 rounded-full bg-white mr-2 {{ $user->account_status === 'active' ? 'animate-pulse' : '' }}"></span>
                                        {{ ucfirst($user->account_status ?? 'active') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                    <div>{{ $user->created_at->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2 space-x-reverse">
                                        <a href="{{ route('admin.users.show', $user) }}" 
                                           class="text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition-all duration-200"
                                           title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" 
                                           class="text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 p-2 rounded-lg transition-all duration-200"
                                           title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition-all duration-200"
                                                    title="حذف"
                                                    onclick="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد مستخدمين</h3>
                                        <p class="text-gray-500 mb-6">لم يتم العثور على أي مستخدمين في النظام</p>
                                        <a href="{{ route('admin.users.create') }}" 
                                           class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-6 py-3 rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all duration-200 shadow-lg">
                                            <i class="fas fa-user-plus ml-2"></i>
                                            إضافة أول مستخدم
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($users->hasPages())
                <div class="px-8 py-6 border-t border-gray-200 bg-gray-50">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-sm text-gray-700">
                            عرض {{ $users->firstItem() }} إلى {{ $users->lastItem() }} من {{ $users->total() }} نتيجة
                        </div>
                        <div class="flex items-center space-x-1">
                            {{ $users->links('pagination::tailwind') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Bulk selection and delete functionality
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all-checkbox');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const selectedCount = document.getElementById('selected-count');
    const selectedNumber = document.getElementById('selected-number');
    const bulkActions = document.getElementById('bulk-actions');
    
    // Select all checkbox functionality
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            userCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });
    }
    
    // Individual checkbox functionality
    userCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllCheckbox();
            updateSelectedCount();
        });
    });
    
    function updateSelectAllCheckbox() {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        const totalBoxes = document.querySelectorAll('.user-checkbox');
        
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = checkedBoxes.length === totalBoxes.length && totalBoxes.length > 0;
            selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < totalBoxes.length;
        }
    }
    
    function updateSelectedCount() {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        const count = checkedBoxes.length;
        
        if (count > 0) {
            selectedCount.classList.remove('hidden');
            bulkActions.classList.remove('hidden');
            selectedNumber.textContent = count;
        } else {
            selectedCount.classList.add('hidden');
            bulkActions.classList.add('hidden');
        }
        
        updateSelectAllCheckbox();
    }
    
    // Initialize on page load
    updateSelectedCount();
});

function selectAll() {
    const checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(checkbox => checkbox.checked = true);
    
    const event = new Event('change');
    document.getElementById('select-all-checkbox').dispatchEvent(event);
}

function deselectAll() {
    const checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(checkbox => checkbox.checked = false);
    
    const event = new Event('change');
    document.getElementById('select-all-checkbox').dispatchEvent(event);
}

function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
    
    if (checkedBoxes.length === 0) {
        alert('الرجاء تحديد مستخدم واحد على الأقل للحذف');
        return;
    }
    
    const userIds = Array.from(checkedBoxes).map(cb => cb.value);
    const userNames = [];
    
    // Get user names for confirmation message
    checkedBoxes.forEach(checkbox => {
        const row = checkbox.closest('tr');
        const userNameElement = row.querySelector('.text-sm.font-bold.text-gray-900');
        if (userNameElement) {
            userNames.push(userNameElement.textContent.trim());
        }
    });
    
    const confirmMessage = `هل أنت متأكد من حذف ${checkedBoxes.length} مستخدم؟\n\nالمستخدمون الذين سيتم حذفهم:\n${userNames.join('\n')}\n\nهذا الإجراء لا يمكن التراجع عنه.`;
    
    if (confirm(confirmMessage)) {
        document.getElementById('bulk-user-ids').value = userIds.join(',');
        document.getElementById('bulk-delete-form').submit();
    }
}

// Search functionality improvements
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    
    if (searchInput) {
        // Add clear button functionality
        searchInput.addEventListener('input', function() {
            if (this.value.length > 0) {
                this.classList.add('pr-10');
            } else {
                this.classList.remove('pr-10');
            }
        });
    }
});
</script>
@endsection
