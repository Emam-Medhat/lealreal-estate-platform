<!-- resources/views/api/integrations.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>External Integrations</h1>
    <p>Manage your external MLS and other third-party integrations.</p>

    <div class="card">
        <div class="card-header">Your Integrations</div>
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Last Sync</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Loop through integrations here --}}
                    <tr>
                        <td>MLS System A</td>
                        <td>MLS</td>
                        <td>Active</td>
                        <td>2026-01-01 10:30:00</td>
                        <td>
                            <button class="btn btn-sm btn-info">View Details</button>
                            <button class="btn btn-sm btn-warning">Edit</button>
                            <button class="btn btn-sm btn-danger">Deactivate</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <button class="btn btn-primary" data-toggle="modal" data-target="#addIntegrationModal">Add New Integration</button>
        </div>
    </div>
</div>

<!-- Add Integration Modal -->
<div class="modal fade" id="addIntegrationModal" tabindex="-1" role="dialog" aria-labelledby="addIntegrationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addIntegrationModalLabel">Add New Integration</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('api.integrations.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="integrationName">Integration Name</label>
                        <input type="text" class="form-control" id="integrationName" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="integrationType">Type</label>
                        <select class="form-control" id="integrationType" name="type" required>
                            <option value="mls">MLS</option>
                            <option value="google_maps">Google Maps</option>
                            <option value="zillow">Zillow</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="integrationConfig">Configuration (JSON)</label>
                        <textarea class="form-control" id="integrationConfig" name="config" rows="5"></textarea>
                        <small class="form-text text-muted">e.g., {"api_key": "your_key", "endpoint": "https://api.example.com"}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Integration</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
