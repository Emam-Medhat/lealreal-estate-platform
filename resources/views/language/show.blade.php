@extends('layouts.app')

@section('title', 'Language Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $language->name }} Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('language.edit', $language->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('language.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Code</th>
                                    <td><code>{{ $language->code }}</code></td>
                                </tr>
                                <tr>
                                    <th>Name</th>
                                    <td>{{ $language->name }}</td>
                                </tr>
                                <tr>
                                    <th>Native Name</th>
                                    <td>{{ $language->native_name }}</td>
                                </tr>
                                <tr>
                                    <th>Direction</th>
                                    <td>
                                        <span class="badge badge-{{ $language->direction === 'rtl' ? 'warning' : 'info' }}">
                                            {{ $language->direction === 'rtl' ? 'RTL' : 'LTR' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Locale</th>
                                    <td>{{ $language->locale }}</td>
                                </tr>
                                <tr>
                                    <th>Flag</th>
                                    <td>
                                        <span class="flag-icon flag-icon-{{ $language->flag }} mr-2"></span>
                                        {{ $language->flag }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Status</th>
                                    <td>
                                        <span class="badge badge-{{ $language->is_active ? 'success' : 'secondary' }}">
                                            {{ $language->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Default</th>
                                    <td>
                                        <span class="badge badge-{{ $language->is_default ? 'primary' : 'secondary' }}">
                                            {{ $language->is_default ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Category</th>
                                    <td>{{ ucfirst($language->category) }}</td>
                                </tr>
                                <tr>
                                    <th>Sort Order</th>
                                    <td>{{ $language->sort_order }}</td>
                                </tr>
                                <tr>
                                    <th>RTL</th>
                                    <td>
                                        <span class="badge badge-{{ $language->is_rtl ? 'warning' : 'info' }}">
                                            {{ $language->is_rtl ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($language->metadata)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Metadata</h5>
                            <pre class="bg-light p-3 rounded"><code>{{ json_encode($language->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                        </div>
                    </div>
                    @endif

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="btn-group">
                                <a href="{{ route('language.edit', $language->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('language.destroy', $language->id) }}" style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this language?')">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </form>
                                <a href="{{ route('language.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Translation Statistics -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Translation Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4>{{ $language->translations()->count() }}</h4>
                            <small>Total Translations</small>
                        </div>
                        <div class="col-6">
                            <h4>{{ $language->translations()->where('is_verified', true)->count() }}</h4>
                            <small>Verified</small>
                        </div>
                    </div>
                    <div class="row text-center mt-3">
                        <div class="col-6">
                            <h4>{{ $language->translations()->where('is_published', true)->count() }}</h4>
                            <small>Published</small>
                        </div>
                        <div class="col-6">
                            <h4>{{ round(($language->translations()->count() / \App\Models\Translation::where('language', 'en')->count()) * 100, 1) }}%</h4>
                            <small>Completion</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Statistics -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">User Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4>{{ $language->users()->count() }}</h4>
                            <small>Users</small>
                        </div>
                        <div class="col-6">
                            <h4>{{ $language->users()->where('preferred_language', $language->code)->count() }}</h4>
                            <small>Preferred</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="exportTranslations()">
                            <i class="fas fa-download"></i> Export Translations
                        </button>
                        <button class="btn btn-outline-info btn-sm" onclick="importTranslations()">
                            <i class="fas fa-upload"></i> Import Translations
                        </button>
                        <button class="btn btn-outline-success btn-sm" onclick="generateFiles()">
                            <i class="fas fa-file-code"></i> Generate Files
                        </button>
                        <button class="btn btn-outline-warning btn-sm" onclick="setAsDefault()">
                            <i class="fas fa-star"></i> Set as Default
                        </button>
                    </div>
                </div>
            </div>

            <!-- Recent Translations -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Recent Translations</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @php
                            $recentTranslations = $language->translations()->orderBy('updated_at', 'desc')->limit(5)->get();
                        @endphp
                        @if($recentTranslations->count() > 0)
                            @foreach($recentTranslations as $translation)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow">
                                            <h6 class="mb-1">{{ $translation->key }}</h6>
                                            <p class="mb-1 text-muted small">{{ Str::limit($translation->value, 100) }}</p>
                                            <div class="d-flex gap-2">
                                                @if($translation->is_verified)
                                                    <span class="badge badge-success">Verified</span>
                                                @endif
                                                @if($translation->is_published)
                                                    <span class="badge badge-info">Published</span>
                                                @endif
                                            </div>
                                        </div>
                                        <small class="text-muted">{{ $translation->updated_at->format('M j, Y H:i') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-language"></i>
                                <p>No translations yet</p>
                            </div>
                        @endif
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('language.translations', $language->code) }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-list"></i> View All Translations
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
function exportTranslations() {
    const languageCode = '{{ $language->code }}';
    
    fetch(`/api/language/export?language=${languageCode}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const blob = new Blob([JSON.stringify(data.translations, null, 2)], { type: 'application/json' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `translations_${languageCode}.json`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        } else {
            alert('Failed to export translations: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error exporting translations:', error);
        alert('Error exporting translations');
    });
}

function importTranslations() {
    const languageCode = '{{ $language->code }}';
    
    // This would typically open a modal for file upload
    alert('Import functionality would be implemented here');
}

function generateFiles() {
    const languageCode = '{{ $language->code }}';
    
    fetch(`/api/language/generate-files?language=${languageCode}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Language files generated successfully!');
        } else {
            alert('Failed to generate files: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error generating files:', error);
        alert('Error generating files');
    });
}

function setAsDefault() {
    const languageCode = '{{ $language->code }}';
    
    if (confirm('Are you sure you want to set this as the default language?')) {
        fetch(`/api/language/set`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttributed('content')
            },
            body: JSON.stringify({
                language: languageCode
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Default language updated successfully!');
                location.reload();
            } else {
                alert('Failed to set default language: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error setting default language:', error);
            alert('Error setting default language');
        });
    }
}
</script>
@endpush
