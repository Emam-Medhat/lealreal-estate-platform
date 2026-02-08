@extends('layouts.app')

@section('title', 'Create Enterprise Account')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-building text-primary me-2"></i>
            Create Enterprise Account
        </h1>
        <a href="{{ route('enterprise.accounts') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>
            Back to Accounts
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Account Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('enterprise.accounts.store') }}">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tenant_name" class="form-label">Account Name *</label>
                                <input type="text" class="form-control" id="tenant_name" name="tenant_name" required>
                                @error('tenant_name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="user_id" class="form-label">Account Owner *</label>
                                <select class="form-select" id="user_id" name="user_id" required>
                                    <option value="">Select User</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="pending_verification">Pending Verification</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="suspended">Suspended</option>
                                    <option value="banned">Banned</option>
                                </select>
                                @error('status')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="subscription_plan_id" class="form-label">Subscription Plan</label>
                                <select class="form-select" id="subscription_plan_id" name="subscription_plan_id">
                                    <option value="">Select Plan</option>
                                    <option value="1">Basic Plan</option>
                                    <option value="2">Professional Plan</option>
                                    <option value="3">Enterprise Plan</option>
                                </select>
                                @error('subscription_plan_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="max_users" class="form-label">Max Users</label>
                                <input type="number" class="form-control" id="max_users" name="max_users" min="1">
                                @error('max_users')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="storage_limit" class="form-label">Storage Limit (GB)</label>
                                <input type="number" class="form-control" id="storage_limit" name="storage_limit" min="1">
                                @error('storage_limit')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="bandwidth_limit" class="form-label">Bandwidth Limit (GB)</label>
                                <input type="number" class="form-control" id="bandwidth_limit" name="bandwidth_limit" min="1">
                                @error('bandwidth_limit')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-3">
                                <label for="api_calls_limit" class="form-label">API Calls Limit</label>
                                <input type="number" class="form-control" id="api_calls_limit" name="api_calls_limit" min="1">
                                @error('api_calls_limit')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="trial_expires_at" class="form-label">Trial Expires At</label>
                                <input type="datetime-local" class="form-control" id="trial_expires_at" name="trial_expires_at">
                                @error('trial_expires_at')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Add any additional notes..."></textarea>
                            @error('notes')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('enterprise.accounts') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Create Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Help</h6>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <p><strong>Account Name:</strong> The name of the enterprise account.</p>
                        <p><strong>Account Owner:</strong> The user who owns this account.</p>
                        <p><strong>Status:</strong> Current status of the account.</p>
                        <p><strong>Limits:</strong> Set resource limits for the account.</p>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
