@extends('layouts.app')

@section('title', 'معاينة القالب')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">معاينة القالب</h1>
        <div class="flex space-x-2 space-x-reverse">
            <a href="{{ route('document-templates.generate', $template) }}" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">
                <i class="fas fa-file-alt ml-2"></i>إنشاء مستند
            </a>
            <a href="{{ route('document-templates.show', $template) }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600">
                <i class="fas fa-arrow-right ml-2"></i>عودة
            </a>
        </div>
    </div>

    <!-- Template Info -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">{{ $template->name }}</h2>
                <p class="text-gray-600">{{ $template->category }} - {{ $template->description }}</p>
            </div>
            <div class="flex items-center space-x-4 space-x-reverse">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $template->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ $template->is_active ? 'نشط' : 'غير نشط' }}
                </span>
                @if($template->requires_signature)
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                        يتطلب توقيع
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Preview Controls -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h2 class="text-lg font-semibold mb-4">إعدادات المعاينة</h2>
        
        <form method="POST" action="{{ route('document-templates.preview', $template) }}" class="space-y-4">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @if($template->variables && count($template->variables) > 0)
                    @foreach($template->variables as $variable)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">{{ $variable['description'] }}</label>
                            @switch($variable['type'])
                                @case('text')
                                    <input type="text" name="preview_data[{{ $variable['name'] }}]" value="{{ $variable['example'] ?? 'مثال نص' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @break
                                @case('date')
                                    <input type="date" name="preview_data[{{ $variable['name'] }}]" value="{{ $variable['example'] ?? now()->format('Y-m-d') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @break
                                @case('number')
                                    <input type="number" name="preview_data[{{ $variable['name'] }}]" value="{{ $variable['example'] ?? 123 }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @break
                                @case('currency')
                                    <input type="number" step="0.01" name="preview_data[{{ $variable['name'] }}]" value="{{ $variable['example'] ?? 1000.00 }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @break
                                @case('email')
                                    <input type="email" name="preview_data[{{ $variable['name'] }}]" value="{{ $variable['example'] ?? 'example@email.com' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @break
                                @case('phone')
                                    <input type="tel" name="preview_data[{{ $variable['name'] }}]" value="{{ $variable['example'] ?? '+966500000000' }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @break
                            @endswitch
                        </div>
                    @endforeach
                @else
                    <div class="col-span-full">
                        <p class="text-gray-500 text-center py-4">لا توجد متغيرات في هذا القالب</p>
                    </div>
                @endif
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">
                    <i class="fas fa-sync ml-2"></i>تحديث المعاينة
                </button>
            </div>
        </form>
    </div>

    <!-- Preview Display -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold">معاينة المستند</h2>
                <div class="flex items-center space-x-2 space-x-reverse">
                    <button onclick="toggleOrientation()" class="bg-gray-500 text-white px-3 py-1 rounded text-sm hover:bg-gray-600">
                        <i class="fas fa-rotate ml-1"></i>تدوير
                    </button>
                    <button onclick="printPreview()" class="bg-blue-500 text-white px-3 py-1 rounded text-sm hover:bg-blue-600">
                        <i class="fas fa-print ml-1"></i>طباعة
                    </button>
                </div>
            </div>
        </div>
        
        <div class="p-6 bg-gray-50">
            <div id="preview-content" class="bg-white shadow-lg mx-auto" style="width: 210mm; min-height: 297mm; padding: 20mm; {{ $template->direction == 'rtl' ? 'direction: rtl' : 'direction: ltr' }}">
                <div class="prose max-w-none">
                    {!! $content !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Variables Reference -->
    @if($template->variables && count($template->variables) > 0)
        <div class="bg-white rounded-lg shadow-md p-6 mt-6">
            <h2 class="text-lg font-semibold mb-4">مرجع المتغيرات</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المتغير</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الوصف</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النوع</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">مثال</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($template->variables as $variable)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <code class="bg-gray-100 px-2 py-1 rounded text-sm">{{ '{' . $variable['name'] . '}' }}</code>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">{{ $variable['description'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        {{ $variable['type'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $variable['example'] ?? '---' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

@section('scripts')
<script>
function toggleOrientation() {
    const content = document.getElementById('preview-content');
    if (content.style.width === '210mm') {
        content.style.width = '297mm';
        content.style.minHeight = '210mm';
    } else {
        content.style.width = '210mm';
        content.style.minHeight = '297mm';
    }
}

function printPreview() {
    const content = document.getElementById('preview-content');
    const printWindow = window.open('', '_blank');
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>معاينة الطباعة</title>
            <style>
                body { margin: 0; padding: 20px; }
                @media print {
                    body { margin: 0; padding: 0; }
                }
            </style>
        </head>
        <body>
            ${content.innerHTML}
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.print();
}

// Auto-update preview on input change
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input[name^="preview_data"]');
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            // Optional: Add real-time preview update
        });
    });
});
</script>
@endsection
