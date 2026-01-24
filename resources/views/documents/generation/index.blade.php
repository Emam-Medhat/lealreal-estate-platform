@extends('layouts.app')

@section('title', 'توليد الوثائق')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">توليد الوثائق</h1>
        <div class="flex space-x-2 space-x-reverse">
            <a href="{{ route('document-generation.create') }}" class="bg-green-500 text-white px-4 py-2 منحن- rounded-lg hover:bg-green-600">
                <i class="fas fa-plus ml-2"></i>توليد وثيقة جديدة
            </a>
            <a href="{{ route('document-generation.history') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                <i class="fas fa-history ml-2"></i>سجل التوليد
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث في المستندات المولدة..." class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <select name="template_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">جميع القوالب</option>
                    @foreach($templates as $template)
                        <option value="{{ $template->id }}" {{ request('template_id') == $template->id ? 'selected' : '' }}>
                            {{ $template->name }}
                        </option>
                    @endforeach
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
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-blue-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">القوالب المتاحة</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $templates->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-green-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">المستندات المولدة</p>
                    <p class="text-2xl font-bold text-green-600">{{ $recentDocuments->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-purple-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">معدد التوليد اليوم</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $todayGenerations ?? 0 }}</p>
                </div>
            </div>
        </div>
        <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-orange-500 text-white rounded-full p-3 ml-3">
                    <i class="fas fa-percentage"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">معدد التوليد هذا الشهر</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $thisMonthGenerations ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Documents -->
    @if($recentDocuments->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-lg font-semibold mb-4">المستندات المولدة حديثاً</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($recentDocuments as $document)
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-900">{{ $document->title }}</h3>
                                <p class="text-sm text-gray-600">{{ $document->template->name }}</p>
                            </div>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                مكتمل
                            </span>
                        </div>
                        
                        <div class="flex items-center text-sm text-gray-500 mb-2">
                            <span class="ml-4">
                                <i class="fas fa-calendar ml-1"></i>
                                {{ $document->created_at->format('Y-m-d') }}
                            </span>
                            <span class="ml-4">
                                <i class="fas fa-user ml-1"></i>
                                {{ $document->createdBy->name }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-500">
                                <i class="fas fa-file ml-1"></i>
                                {{ $document->file_name }}
                            </div>
                            <div class="flex space-x-2 space-x-reverse">
                                <a href="{{ route('documents.show', $document) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('documents.download', $document) }}" class="text-gray-600 hover:text-gray-900">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Templates Grid -->
    @if($templates->count() > 0)
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold mb-4">قوالب الوثائق</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($templates as $template)
                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-lg transition-shadow">
                        <div class="flex items-center mb-4">
                            <div class="bg-blue-100 text-blue-600 rounded-full p-3 ml-3">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $template->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $template->description }}</p>
                            </div>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $template->is_active ? 'نشط' : 'غير نشط' }}
                            </span>
                        </div>
                        
                        <div class="text-sm text-gray-600 mb-4">
                            {{ Str::limit($template->description, 100) }}
                        </div>
                        
                        <div class="flex items-center text-sm text-gray-500 mb-4">
                            <span class="ml-4">
                                <i class="fas fa-layer-group ml-1"></i>
                                {{ $template->variables_count ?? 0 }} متغير
                            </span>
                            <span class="ml-4">
                                <i class="fas fa-file ml-1"></i>
                                {{ $template->documents_count ?? 0 }} مستند
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <div class="text-sm text-gray-500">
                                <i class="fas fa-calendar ml-1"></i>
                                {{ $template->updated_at->format('Y-m-d') }}
                            </div>
                            <div class="flex space-x-2 space-x-reverse">
                                <a href="{{ route('document-generation.create', $template) }}" class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600">
                                    <i class="fas fa-plus ml-1"></i>توليد
                                </a>
                                <a href="{{ route('document-templates.preview', $template) }}" class="text-blue-500 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('document-templates.show', $template) }}" class="text-gray-500 hover:text-gray-900">
                                    <i class="fas fa-info-circle"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold mb-4">إجراءات سريعة</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="border border-gray-200 rounded-lg p-4 text-center hover:bg-gray-50 transition-colors">
                <div class="bg-blue-100 text-blue-600 rounded-full p-3 mx-auto mb-3">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">توليد سريع</h3>
                <p class="text-sm text-gray-600 mb-4">توليد مستندات متعددة باستخدام قالب محدد</p>
                <button onclick="showBulkGenerationModal()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 w-full">
                    <i class="fas fa-layer-group ml-2"></i>توليد جماعي
                </button>
            </div>
            
            <div class="border border-gray-200 rounded-lg p-4 text-center hover:bg-gray-50 transition-colors">
                <div class="bg-green-100 text-green-600 rounded-full p-3 mx-auto mb-3">
                    <i class="fas fa-download"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">تصدير جماعي</h3>
                <p class="text-sm text-gray-600 mb-4">تصدير المستندات المولدة بتنسيقات مختلفة</p>
                <button onclick="showBulkExportModal()" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 w-full">
                    <i class="fas fa-file-export ml-2"></i>تصدير جماعي
                </button>
            </div>
            
            <div class="border border-gray-200 rounded-lg p-4 text-center hover:bg-gray-50 transition-colors">
                <div class="bg-purple-100 text-purple-600 rounded-full p-3 mx-auto mb-3">
                    <i class="fas fa-cog"></i>
                </div>
                <h3 class="font-semibold text-gray-900 mb-2">إعدادات متقدمة</h3>
                <p class="text-sm text-gray-600 mb-4">تكوين إعدادات التوليد المتقدم</p>
                <button onclick="showAdvancedSettingsModal()" class="bg-purple-500 text-white px-4 py-2 rounded-lg hover:bg-purple-600 w-full">
                    <i class="fas fa-cogs ml-2"></i>إعدادات
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Generation Modal -->
<div id="bulkGenerationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full z-50">
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
            <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">توليد جماعي</h3>
                <button onclick="hideBulkGenerationModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="p-6">
                <form id="bulkGenerationForm">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">اختر القالب</label>
                        <select name="template_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">اختر قالب</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}">{{ $template->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">بيانات المستندات (واحدة لكل مستند)</label>
                        <div id="documentsContainer" class="space-y-2 max-h-60 overflow-y-auto">
                            <div class="border border-gray-200 rounded p-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">المستند 1</label>
                                <input type="text" name="documents[0][title]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="عنوان المستند">
                                <input type="text" name="documents[0][description]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="وصف المستند">
                            </div>
                        </div>
                        </div>
                        <button type="button" onclick="addDocumentField()" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                            <i class="fas fa-plus ml-2"></i>إضافة مستند
                        </button>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">متغيرات القالب (اختياري)</label>
                        <div id="templateVariables" class="space-y-2">
                            <!-- Variables will be loaded dynamically -->
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-4 space-x-reverse">
                        <button type="button" onclick="hideBulkGenerationModal()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                            إلغاء
                        </button>
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                            <i class="fas fa-layer-group ml-2"></i>توليد المستندات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts'>
let documentCount = 1;

function showBulkGenerationModal() {
    document.getElementById('bulkGenerationModal').classList.remove('hidden');
}

function hideBulkGenerationModal() {
    document.getElementById('bulkGenerationModal').classList.add('hidden');
}

function showBulkExportModal() {
    alert('سيتم تفعيل تصدير جماعي قريباً');
}

function showAdvancedSettingsModal() {
    alert('سيتم تفعيل الإعدادات المتقدمة قريباً');
}

function addDocumentField() {
    const container = document.getElementById('documentsContainer');
    const documentDiv = document.createElement('div');
    documentDiv.className = 'border border-gray-200 rounded p-3';
    documentDiv.innerHTML = `
        <label class="block text-sm font-medium text-gray-700 mb-1">المستند ${documentCount + 1}</label>
        <input type="text" name="documents[${documentCount}][title]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="عنوان المستند">
        <input type="text" name="documents[${documentCount}][description]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="وصف المستند">
    `;
    container.appendChild(documentDiv);
    documentCount++;
}

// Load template variables when template is selected
document.querySelector('select[name="template_id"]').addEventListener('change', function() {
    const templateId = this.value;
    if (templateId) {
        // Load template variables via AJAX
        fetch(`/document-templates/${templateId}/variables`)
            .then(response => response.json())
            .then(data => {
                const variablesContainer = document.getElementById('templateVariables');
                variablesContainer.innerHTML = '';
                
                if (data.variables && data.variables.length > 0) {
                    data.variables.forEach((variable, index) => {
                        const variableDiv = document.createElement('div');
                        variableDiv.className = 'border border-gray-200 rounded p-3';
                        variableDiv.innerHTML = `
                            <label class="block text-sm font-medium text-gray-700 mb-1">${variable.description}</label>
                            <input type="text" name="variables[${index}][value]" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="${variable.example || ''}">
                        `;
                        variablesContainer.appendChild(variableDiv);
                    });
                }
            });
    }
});

// Bulk generation form submission
document.getElementById('bulkGenerationForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('{{ route('document-generation.bulk') }}', {
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
            hideBulkGenerationModal();
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
