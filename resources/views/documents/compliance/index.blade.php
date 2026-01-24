@extends('layouts.app')

@section('title', 'فحص الامتثال للوثائق')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">فحص الامتثال للوثائق</h1>
        <div class="flex space-x-2 space-x-reverse">
            <a href="{{ route('documents.compliance.report') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                <i class="fas fa-chart-bar ml-2"></i>تقارير الامتثال
            </a>
            <a href="{{ route('documents.compliance.reminders') }}" class="bg-yellow-500 text-white px-4 py-2 rounded-lg hover:bg-yellow-600">
                <i class="fas fa-bell ml-2"></i>التذكيرات
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">حالة الامتثال</label>
                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">الكل</option>
                    <option value="compliant" {{ request('status') == 'compliant' ? 'selected' : '' }}>ممتثل</option>
                    <option value="non_compliant" {{ request('status') == 'non_compliant' ? 'selected' : '' }}>غير ممتثل</option>
                    <option value="needs_review" {{ request('status') == 'needs_review' ? 'selected' : '' }}>يحتاج مراجعة</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">نوع الوثيقة</label>
                <select name="document_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">الكل</option>
                    <option value="contract" {{ request('document_type') == 'contract' ? 'selected' : '' }}>عقد</option>
                    <option value="legal_document" {{ request('document_type') == 'legal_document' ? 'selected' : '' }}>وثيقة قانونية</option>
                    <option value="financial_document" {{ request('document_type') == 'financial_document' ? 'selected' : '' }}>وثيقة مالية</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الفترة</label>
                <input type="date" name="date_range" value="{{ request('date_range') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 w-full">
                    <i class="fas fa-search ml-2"></i>بحث
                </button>
            </div>
        </form>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-green-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-check"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">ممتثل</p>
                    <p class="text-2xl font-bold text-green-600">{{ $compliances->where('overall_status', 'compliant')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-red-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-times"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">غير ممتثل</p>
                    <p class="text-2xl font-bold text-red-600">{{ $compliances->where('overall_status', 'non_compliant')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-yellow-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-exclamation"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">يحتاج مراجعة</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ $compliances->where('overall_status', 'needs_review')->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-blue-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">متوسط الامتثال</p>
                    <p class="text-2xl font-bold text-blue-600">{{ number_format($compliances->avg('compliance_score') ?? 0, 1) }}%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Compliances Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الوثيقة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">درجة الامتثال</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الفاحص</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الفحص</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المراجعة التالية</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($compliances as $compliance)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $compliance->document->title }}</div>
                                <div class="text-sm text-gray-500">{{ $compliance->document->type }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @switch($compliance->overall_status)
                                    @case('compliant')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            ممتثل
                                        </span>
                                        @break
                                    @case('non_compliant')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            غير ممتثل
                                        </span>
                                        @break
                                    @case('needs_review')
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            يحتاج مراجعة
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="text-sm font-medium text-gray-900">{{ number_format($compliance->compliance_score, 1) }}%</div>
                                    <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                        <div class="bg-{{ $compliance->compliance_score >= 80 ? 'green' : ($compliance->compliance_score >= 60 ? 'yellow' : 'red') }}-500 h-2 rounded-full" style="width: {{ $compliance->compliance_score }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $compliance->checkedBy->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $compliance->checked_at->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($compliance->next_review_date)
                                    <span class="{{ $compliance->next_review_date->isPast() ? 'text-red-600 font-semibold' : '' }}">
                                        {{ $compliance->next_review_date->format('Y-m-d') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">غير محدد</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-left text-sm font-medium">
                                <a href="{{ route('documents.compliance.show', $compliance) }}" class="text-blue-600 hover:text-blue-900 ml-2">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('documents.compliance.edit', $compliance) }}" class="text-yellow-600 hover:text-yellow-900 ml-2">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                لا توجد نتائج فحص امتثال
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="bg-gray-50 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
            {{ $compliances->links() }}
        </div>
    </div>
</div>
@endsection
