@extends('admin.layouts.admin')

@section('title', __('System Backup'))

@section('content')
<div class="card">
    <div class="card-header">
        <h4>{{ __('Backup Management') }}</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> {{ __('Manage your system backups from this section.') }}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('Create New Backup') }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.system.backup.create') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>{{ __('Backup Type') }}</label>
                                <select name="backup_type" class="form-control" required>
                                    <option value="full">{{ __('Full Backup (Database + Files)') }}</option>
                                    <option value="database">{{ __('Database Only') }}</option>
                                    <option value="files">{{ __('Files Only') }}</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ __('Create Backup') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('Existing Backups') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('File Name') }}</th>
                                        <th>{{ __('Size') }}</th>
                                        <th>{{ __('Created At') }}</th>
                                        <th>{{ __('Actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($backups ?? [] as $backup)
                                        <tr>
                                            <td>{{ $backup['name'] }}</td>
                                            <td>{{ $backup['size'] }}</td>
                                            <td>{{ $backup['date'] }}</td>
                                            <td>
                                                <a href="{{ route('admin.system.backup.download', $backup['name']) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-danger delete-backup" data-name="{{ $backup['name'] }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center">{{ __('No backups found.') }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Backup Modal -->
<div class="modal fade" id="deleteBackupModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Delete Backup') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="deleteBackupForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-body">
                    {{ __('Are you sure you want to delete this backup?') }}
                    <p class="text-danger"><strong>{{ __('This action cannot be undone.') }}</strong></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Handle delete backup button click
        $('.delete-backup').on('click', function() {
            const backupName = $(this).data('name');
            const form = $('#deleteBackupForm');
            // Construct the delete URL with proper parameter encoding
            const deleteUrl = '{{ url("/admin/system/backup/") }}/' + encodeURIComponent(backupName);
            form.attr('action', deleteUrl);
            $('#deleteBackupModal').modal('show');
        });
    });
</script>
@endpush
