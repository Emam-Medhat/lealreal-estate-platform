@extends('layouts.app')

@section('title', 'Language Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Language Management</h3>
                    <div class="card-tools">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTranslationModal">
                            <i class="fas fa-plus"></i> Add Translation
                        </button>
                        <button class="btn btn-info" onclick="generateLanguageFiles()">
                            <i class="fas fa-download"></i> Generate Files
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="languageFilter" class="form-label">Filter by Language</label>
                            <select class="form-select" id="languageFilter">
                                <option value="">All Languages</option>
                                @foreach($languages as $language)
                                    <option value="{{ $language->code }}">{{ $language->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="groupFilter" class="form-label">Filter by Group</label>
                            <select class="form-select" id="groupFilter">
                                <option value="">All Groups</option>
                                <option value="general">General</option>
                                <option value="properties">Properties</option>
                                <option value="users">Users</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="searchInput" class="form-label">Search</label>
                            <input type="text" class="form-control" id="searchInput" placeholder="Search translations...">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Key</th>
                                    <th>Translation</th>
                                    <th>Language</th>
                                    <th>Group</th>
                                    <th>Status</th>
                                    <th>Updated</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="translationsTable">
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-4">
                                        <i class="fas fa-spinner fa-spin"></i> Loading translations...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Language Statistics -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $languages->count() }}</h4>
                            <p>Total Languages</p>
                        </div>
                        <div>
                            <i class="fas fa-language fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ \App\Models\Translation::count() }}</h4>
                            <p>Total Translations</p>
                        </div>
                        <div>
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ \App\Models\Translation::where('is_verified', true)->count() }}</h4>
                            <p>Verified</p>
                        </div>
                        <div>
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $languages->count() > 0 ? round(($languages->sum('completion_percentage') / $languages->count()), 1) : 0 }}%</h4>
                            <p>Avg Completion</p>
                        </div>
                        <div>
                            <i class="fas fa-chart-pie fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Translation Modal -->
<div class="modal fade" id="addTranslationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Translation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addTranslationForm">
                    <div class="mb-3">
                        <label for="translationKey" class="form-label">Translation Key</label>
                        <input type="text" class="form-control" id="translationKey" required>
                        <small class="text-muted">Example: welcome.message</small>
                    </div>
                    <div class="mb-3">
                        <label for="translationValue" class="form-label">Translation Value</label>
                        <textarea class="form-control" id="translationValue" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="translationLanguage" class="form-label">Language</label>
                        <select class="form-select" id="translationLanguage" required>
                            @foreach($languages as $language)
                                <option value="{{ $language->code }}">{{ $language->name }} ({{ $language->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="translationGroup" class="form-label">Group</label>
                        <select class="form-select" id="translationGroup">
                            <option value="general">General</option>
                            <option value="properties">Properties</option>
                            <option value="users">Users</option>
                            <option value="admin">Admin</option>
                            <option value="notifications">Notifications</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="addTranslation()">Add Translation</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    loadTranslations();
    
    // Filter handlers
    $('#languageFilter, #groupFilter, #searchInput').on('change keyup', function() {
        loadTranslations();
    });
});

function loadTranslations() {
    const params = {
        language: $('#languageFilter').val(),
        group: $('#groupFilter').val(),
        search: $('#searchInput').val()
    };

    $.ajax({
        url: '/api/language/translations',
        method: 'GET',
        data: params,
        success: function(response) {
            if (response.success) {
                updateTranslationsTable(response.translations);
            }
        },
        error: function() {
            $('#translationsTable').html(`
                <tr>
                    <td colspan="8" class="text-center text-danger py-4">
                        <i class="fas fa-exclamation-triangle"></i> Failed to load translations
                    </td>
                </tr>
            `);
        }
    });
}

function updateTranslationsTable(translations) {
    const tbody = $('#translationsTable');
    tbody.empty();

    if (translations.length === 0) {
        tbody.html(`
            <tr>
                <td colspan="8" class="text-center text-muted py-4">
                    <i class="fas fa-info-circle"></i> No translations found
                </td>
            </tr>
        `);
        return;
    }

    translations.forEach(translation => {
        const row = `
            <tr>
                <td><code>${translation.key}</code></td>
                <td>${Str::limit($translation.value, 100)}</td>
                <td>
                    <span class="flag-icon flag-icon-${translation.language.toLowerCase()} mr-2"></span>
                    ${translation.language}
                </td>
                <td><span class="badge badge-secondary">${translation.group || 'general'}</span></td>
                <td>
                    ${translation.is_verified ? '<span class="badge badge-success">Verified</span>' : ''}
                    ${translation.is_published ? '<span class="badge badge-info">Published</span>' : ''}
                </td>
                <td>${new Date(translation.updated_at).toLocaleString()}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary edit-translation" data-id="${translation.id}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-success verify-translation" data-id="${translation.id}">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn btn-outline-danger delete-translation" data-id="${translation.id}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(row);
    });
}

function addTranslation() {
    const formData = {
        key: $('#translationKey').val(),
        value: $('#translationValue').val(),
        language: $('#translationLanguage').val(),
        group: $('#translationGroup').val()
    };

    $.ajax({
        url: '/api/language/add-translation',
        method: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                alert('Translation added successfully!');
                $('#addTranslationModal').modal('hide');
                loadTranslations();
            } else {
                alert('Failed to add translation: ' + response.message);
            }
        },
        error: function() {
            alert('Error adding translation');
        }
    });
}

function generateLanguageFiles() {
    if (confirm('This will generate language files for all languages. Continue?')) {
        $.ajax({
            url: '/api/language/generate-files',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert('Language files generated successfully!');
                } else {
                    alert('Failed to generate files: ' + response.message);
                }
            },
            error: function() {
                alert('Error generating language files');
            }
        });
    }
}

// Translation actions
$(document).on('click', '.edit-translation', function() {
    const id = $(this).data('id');
    // Implement edit functionality
    alert('Edit functionality will be implemented');
});

$(document).on('click', '.verify-translation', function() {
    const id = $(this).data('id');
    // Implement verify functionality
    alert('Verify functionality will be implemented');
});

$(document).on('click', '.delete-translation', function() {
    const id = $(this).data('id');
    if (confirm('Are you sure you want to delete this translation?')) {
        // Implement delete functionality
        alert('Delete functionality will be implemented');
    }
});
</script>
@endpush
