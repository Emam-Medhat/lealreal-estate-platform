@extends('layouts.app')

@section('title', 'System Backups')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">System Backups</h1>
                    <p class="text-gray-600">Manage system backups and restore points</p>
                </div>
                <div class="flex items-center space-x-3">
                    <form action="{{ route('admin.backups.create') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i>
                            Create Backup
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Backup Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 rounded-full p-3 mr-4">
                        <i class="fas fa-database text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Backups</p>
                        <p class="text-2xl font-bold text-gray-800">{{ count($backups) }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 rounded-full p-3 mr-4">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Last Backup</p>
                        <p class="text-lg font-bold text-gray-800">{{ $backups[0]->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 rounded-full p-3 mr-4">
                        <i class="fas fa-hdd text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Size</p>
                        <p class="text-lg font-bold text-gray-800">~45 MB</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 rounded-full p-3 mr-4">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Auto Backup</p>
                        <p class="text-lg font-bold text-gray-800">Daily</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Backups Table -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">Backup History</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                File Name
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Size
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($backups as $backup)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="bg-blue-100 rounded-full p-2 mr-3">
                                            <i class="fas fa-file-archive text-blue-600 text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $backup->filename }}</div>
                                            <div class="text-sm text-gray-500">ID: {{ $backup->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900">{{ $backup->size }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $backup->type === 'full' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ucfirst($backup->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $backup->created_at->format('M j, Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('admin.backups.download', $backup->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                    <button class="text-red-600 hover:text-red-900" onclick="restoreBackup({{ $backup->id }})">
                                        <i class="fas fa-undo"></i> Restore
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No backups found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Backup Settings -->
        <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Backup Settings</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Auto Backup Frequency</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="daily" selected>Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                        <option value="disabled">Disabled</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Retention Period</label>
                    <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="7">7 Days</option>
                        <option value="30" selected>30 Days</option>
                        <option value="90">90 Days</option>
                        <option value="365">1 Year</option>
                    </select>
                </div>
            </div>
            
            <div class="mt-4">
                <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    Save Settings
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function restoreBackup(backupId) {
    if (confirm('Are you sure you want to restore this backup? This will replace current data.')) {
        // Implement restore logic
        alert('Restore functionality not implemented yet');
    }
}
</script>
@endsection
