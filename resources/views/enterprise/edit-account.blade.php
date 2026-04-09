@extends('layouts.app')

@section('title', 'Edit Enterprise Account')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-edit text-warning me-2"></i>
            Edit Enterprise Account
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
                    <h5 class="mb-0">Edit Account Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('enterprise.accounts.update', $account->id) }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="company_name" class="form-label">Company Name *</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" 
                                       value="{{ old('company_name', $account->company_name) }}" required>
                                @error('company_name')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="contact_email" class="form-label">Contact Email *</label>
                                <input type="email" class="form-control" id="contact_email" name="contact_email" 
                                       value="{{ old('contact_email', $account->contact_email) }}" required>
                                @error('contact_email')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="contact_person" class="form-label">Contact Person *</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person" 
                                       value="{{ old('contact_person', $account->contact_person) }}" required>
                                @error('contact_person')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="pending_verification" {{ $account->status == 'pending_verification' ? 'selected' : '' }}>Pending Verification</option>
                                    <option value="active" {{ $account->status == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ $account->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="suspended" {{ $account->status == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    <option value="banned" {{ $account->status == 'banned' ? 'selected' : '' }}>Banned</option>
                                </select>
                                @error('status')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="company_type" class="form-label">Company Type</label>
                                <input type="text" class="form-control" id="company_type" name="company_type" 
                                       value="{{ old('company_type', $account->company_type) }}" readonly>
                                <small class="text-muted">Company type cannot be changed</small>
                            </div>
                            <div class="col-md-6">
                                <label for="industry" class="form-label">Industry</label>
                                <input type="text" class="form-control" id="industry" name="industry" 
                                       value="{{ old('industry', $account->industry) }}" readonly>
                                <small class="text-muted">Industry cannot be changed</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="size" class="form-label">Company Size</label>
                                <input type="text" class="form-control" id="size" name="size" 
                                       value="{{ old('size', $account->size) }}" readonly>
                                <small class="text-muted">Company size cannot be changed</small>
                            </div>
                            <div class="col-md-6">
                                <label for="website" class="form-label">Website</label>
                                <input type="url" class="form-control" id="website" name="website" 
                                       value="{{ old('website', $account->website) }}">
                                @error('website')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" 
                                       value="{{ old('address', $account->address) }}">
                                @error('address')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city" 
                                       value="{{ old('city', $account->city) }}">
                                @error('city')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="country" class="form-label">Country</label>
                                <input type="text" class="form-control" id="country" name="country" 
                                       value="{{ old('country', $account->country) }}">
                                @error('country')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label for="postal_code" class="form-label">Postal Code</label>
                                <input type="text" class="form-control" id="postal_code" name="postal_code" 
                                       value="{{ old('postal_code', $account->postal_code) }}">
                                @error('postal_code')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('enterprise.accounts') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>
                                Update Account
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Current Account Info</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Account ID:</span>
                        <strong>#{{ $account->id }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Created:</span>
                        <strong>{{ $account->created_at->format('M d, Y') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Last Updated:</span>
                        <strong>{{ $account->updated_at->format('M d, Y') }}</strong>
                    </div>
                    @if($account->upgraded_at)
                        <div class="d-flex justify-content-between mb-2">
                            <span>Upgraded:</span>
                            <strong>{{ $account->upgraded_at->format('M d, Y') }}</strong>
                        </div>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Help</h6>
                </div>
                <div class="card-body">
                    <small class="text-muted">
                        <p><strong>Company Name:</strong> Update the company name.</p>
                        <p><strong>Contact Email:</strong> Change the contact email address.</p>
                        <p><strong>Status:</strong> Change account status.</p>
                        <p><strong>Read-only Fields:</strong> Some fields cannot be changed.</p>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
