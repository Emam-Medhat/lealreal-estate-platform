<!-- resources/views/api/webhooks.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Webhooks</h1>
    <p>Manage your registered webhooks for real-time notifications.</p>

    <div class="card">
        <div class="card-header">Your Webhooks</div>
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <table class="table">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>URL</th>
                        <th>Status</th>
                        <th>Last Triggered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Loop through webhooks here --}}
                    <tr>
                        <td>property.created</td>
                        <td>https://your-callback-url.com/webhook</td>
                        <td>Active</td>
                        <td>2026-01-01 11:00:00</td>
                        <td>
                            <button class="btn btn-sm btn-danger">Deactivate</button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <button class="btn btn-primary" data-toggle="modal" data-target="#addWebhookModal">Add New Webhook</button>
        </div>
    </div>
</div>

<!-- Add Webhook Modal -->
<div class="modal fade" id="addWebhookModal" tabindex="-1" role="dialog" aria-labelledby="addWebhookModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addWebhookModalLabel">Add New Webhook</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('api.webhooks.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="webhookEvent">Event</label>
                        <input type="text" class="form-control" id="webhookEvent" name="event" required>
                        <small class="form-text text-muted">e.g., property.created, property.updated</small>
                    </div>
                    <div class="form-group">
                        <label for="webhookUrl">Callback URL</label>
                        <input type="url" class="form-control" id="webhookUrl" name="url" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Webhook</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
