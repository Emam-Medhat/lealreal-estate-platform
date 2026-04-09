@extends('admin.layouts.admin')

@section('title', 'Template Preview')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                    <i class="fas fa-eye text-blue-600 mr-3"></i>
                    Template Preview
                </h1>
                <p class="text-gray-600 text-lg">Preview and test document template with sample data</p>
            </div>
            <div class="flex gap-3">
                <form method="POST" action="{{ route('document-templates.generate', $template) }}" class="inline">
                    @csrf
                    <button type="submit" 
                            class="px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-xl hover:from-green-600 hover:to-green-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-file-alt mr-2"></i>
                        Generate Document
                    </button>
                </form>
                <a href="{{ route('document-templates.show', $template) }}" 
                   class="px-6 py-3 bg-gradient-to-r from-gray-200 to-gray-300 text-gray-700 rounded-xl hover:from-gray-300 hover:to-gray-400 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back
                </a>
            </div>
        </div>

        <!-- Template Info -->
        <div class="bg-white rounded-2xl border border-gray-200 p-8 mb-8 shadow-xl">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-3">{{ $template->name }}</h2>
                    <p class="text-gray-600 text-lg">{{ ucfirst($template->category) }} - {{ $template->description ?: 'No description' }}</p>
                </div>
                <div class="flex gap-3">
                    <span class="px-4 py-2 text-sm font-semibold rounded-full 
                        @if($template->is_active) 
                            bg-gradient-to-r from-green-100 to-green-200 text-green-800 border border-green-300
                        @else 
                            bg-gradient-to-r from-gray-100 to-gray-200 text-gray-800 border border-gray-300
                        @endif shadow-md">
                        {{ $template->is_active ? 'Active' : 'Inactive' }}
                    </span>
                    @if($template->requires_signature)
                        <span class="px-4 py-2 text-sm font-semibold rounded-full bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 border border-blue-300 shadow-md">
                            Requires Signature
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Preview Controls -->
        <div class="bg-white rounded-2xl border border-gray-200 p-8 mb-8 shadow-xl">
            <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                <i class="fas fa-sliders-h text-purple-500 mr-3"></i>
                Preview Settings
            </h2>
            
            <form method="POST" action="{{ route('document-templates.preview', $template) }}" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @if($template->variables && count($template->variables) > 0)
                        @foreach($template->variables as $variable)
                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">{{ $variable['description'] }}</label>
                                @switch($variable['type'])
                                    @case('text')
                                        <input type="text" name="preview_data[{{ $variable['name'] }}]" 
                                               value="{{ $variable['example'] ?? 'Sample text' }}" 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white shadow-sm">
                                        @break
                                    @case('date')
                                        <input type="date" name="preview_data[{{ $variable['name'] }}]" 
                                               value="{{ $variable['example'] ?? now()->format('Y-m-d') }}" 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white shadow-sm">
                                        @break
                                    @case('number')
                                        <input type="number" name="preview_data[{{ $variable['name'] }}]" 
                                               value="{{ $variable['example'] ?? 123 }}" 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white shadow-sm">
                                        @break
                                    @case('currency')
                                        <input type="number" step="0.01" name="preview_data[{{ $variable['name'] }}]" 
                                               value="{{ $variable['example'] ?? 1000.00 }}" 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white shadow-sm">
                                        @break
                                    @case('email')
                                        <input type="email" name="preview_data[{{ $variable['name'] }}]" 
                                               value="{{ $variable['example'] ?? 'example@email.com' }}" 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white shadow-sm">
                                        @break
                                    @case('phone')
                                        <input type="tel" name="preview_data[{{ $variable['name'] }}]" 
                                               value="{{ $variable['example'] ?? '+966500000000' }}" 
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white shadow-sm">
                                        @break
                                @endswitch
                            </div>
                        @endforeach
                    @else
                        <div class="col-span-full">
                            <div class="text-center py-12">
                                <i class="fas fa-info-circle text-gray-300 text-6xl mb-4"></i>
                                <p class="text-gray-500 text-xl font-medium">No variables in this template</p>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" 
                            class="px-8 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-sync mr-2"></i>
                        Update Preview
                    </button>
                </div>
            </form>
        </div>

        <!-- Preview Display -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden mb-8 shadow-xl">
            <div class="border-b border-gray-200 px-8 py-6 bg-gradient-to-r from-gray-50 to-gray-100">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-file-alt text-green-500 mr-3"></i>
                        Document Preview
                    </h2>
                    <div class="flex gap-3">
                        <button onclick="toggleOrientation()" 
                                class="px-6 py-3 bg-gradient-to-r from-gray-200 to-gray-300 text-gray-700 rounded-xl hover:from-gray-300 hover:to-gray-400 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center">
                            <i class="fas fa-rotate mr-2"></i>
                            Rotate
                        </button>
                        <button onclick="printPreview()" 
                                class="px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center">
                            <i class="fas fa-print mr-2"></i>
                            Print
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="p-8 bg-gradient-to-br from-gray-100 to-gray-200 overflow-auto">
                <div class="flex justify-center">
                    <div id="preview-content" 
                         class="bg-white shadow-2xl rounded-2xl" 
                         style="width: 210mm; min-height: 297mm; padding: 20mm; {{ $template->direction == 'rtl' ? 'direction: rtl' : 'direction: ltr' }}; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);">
                        <div class="prose max-w-none">
                            {!! $content !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Variables Reference -->
        @if($template->variables && count($template->variables) > 0)
            <div class="bg-white rounded-2xl border border-gray-200 p-8 shadow-xl">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-code text-indigo-500 mr-3"></i>
                    Variables Reference
                </h2>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Variable</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Example</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($template->variables as $variable)
                                <tr class="hover:bg-gradient-to-r hover:from-gray-50 hover:to-gray-100 transition-all duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <code class="bg-gradient-to-r from-gray-100 to-gray-200 px-4 py-2 rounded-lg text-sm font-mono text-gray-800 border border-gray-300">{{ '{' . $variable['name'] . '}' }}</code>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $variable['description'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-4 py-2 text-xs font-bold rounded-full bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 border border-blue-300">
                                            {{ ucfirst($variable['type']) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-600">{{ $variable['example'] ?? '---' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
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
            <title>Print Preview</title>
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
@endpush
@endsection
