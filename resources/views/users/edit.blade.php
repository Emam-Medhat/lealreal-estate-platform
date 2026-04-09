@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit User: {{ $user->full_name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye"></i> View User
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <!-- Personal Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="first_name">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                           id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                                    @error('first_name')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                           id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                                    @error('last_name')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                    @error('phone')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" placeholder="Leave blank to keep current password">
                                    @error('password')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation">Confirm Password</label>
                                    <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror" 
                                           id="password_confirmation" name="password_confirmation" placeholder="Confirm new password">
                                    @error('password_confirmation')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- User Type and Status -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="user_type">User Type <span class="text-danger">*</span></label>
                                    <select class="form-control @error('user_type') is-invalid @enderror" 
                                            id="user_type" name="user_type" required>
                                        <option value="">Select User Type</option>
                                        <option value="admin" {{ old('user_type', $user->user_type) == 'admin' ? 'selected' : '' }}>Administrator</option>
                                        <option value="agent" {{ old('user_type', $user->user_type) == 'agent' ? 'selected' : '' }}>Real Estate Agent</option>
                                        <option value="company" {{ old('user_type', $user->user_type) == 'company' ? 'selected' : '' }}>Company</option>
                                        <option value="developer" {{ old('user_type', $user->user_type) == 'developer' ? 'selected' : '' }}>Property Developer</option>
                                        <option value="investor" {{ old('user_type', $user->user_type) == 'investor' ? 'selected' : '' }}>Investor</option>
                                        <option value="buyer" {{ old('user_type', $user->user_type) == 'buyer' ? 'selected' : '' }}>Property Buyer</option>
                                        <option value="tenant" {{ old('user_type', $user->user_type) == 'tenant' ? 'selected' : '' }}>Tenant</option>
                                    </select>
                                    @error('user_type')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="account_status">Account Status</label>
                                    <select class="form-control @error('account_status') is-invalid @enderror" 
                                            id="account_status" name="account_status">
                                        <option value="active" {{ old('account_status', $user->account_status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('account_status', $user->account_status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="suspended" {{ old('account_status', $user->account_status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    </select>
                                    @error('account_status')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="kyc_status">KYC Status</label>
                                    <select class="form-control @error('kyc_status') is-invalid @enderror" 
                                            id="kyc_status" name="kyc_status">
                                        <option value="pending" {{ old('kyc_status', $user->kyc_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="submitted" {{ old('kyc_status', $user->kyc_status) == 'submitted' ? 'selected' : '' }}>Submitted</option>
                                        <option value="verified" {{ old('kyc_status', $user->kyc_status) == 'verified' ? 'selected' : '' }}>Verified</option>
                                        <option value="rejected" {{ old('kyc_status', $user->kyc_status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                    @error('kyc_status')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Location Information -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="country">Country</label>
                                    <input type="text" class="form-control @error('country') is-invalid @enderror" 
                                           id="country" name="country" value="{{ old('country', $user->country) }}">
                                    @error('country')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="city">City</label>
                                    <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                           id="city" name="city" value="{{ old('city', $user->city) }}">
                                    @error('city')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="timezone">Timezone</label>
                                    <select class="form-control @error('timezone') is-invalid @enderror" 
                                            id="timezone" name="timezone">
                                        <option value="UTC" {{ old('timezone', $user->timezone) == 'UTC' ? 'selected' : '' }}>UTC</option>
                                        <option value="America/New_York" {{ old('timezone', $user->timezone) == 'America/New_York' ? 'selected' : '' }}>Eastern Time</option>
                                        <option value="America/Chicago" {{ old('timezone', $user->timezone) == 'America/Chicago' ? 'selected' : '' }}>Central Time</option>
                                        <option value="America/Denver" {{ old('timezone', $user->timezone) == 'America/Denver' ? 'selected' : '' }}>Mountain Time</option>
                                        <option value="America/Los_Angeles" {{ old('timezone', $user->timezone) == 'America/Los_Angeles' ? 'selected' : '' }}>Pacific Time</option>
                                    </select>
                                    @error('timezone')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Profile Information -->
                        <h5>Profile Information</h5>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="profile_bio">Bio</label>
                                    <textarea class="form-control @error('profile_bio') is-invalid @enderror" 
                                              id="profile_bio" name="profile[bio]" rows="3">{{ old('profile_bio', $user->profile->bio ?? '') }}</textarea>
                                    @error('profile_bio')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="avatar">Profile Picture</label>
                                    <input type="file" class="form-control @error('avatar') is-invalid @enderror" 
                                           id="avatar" name="avatar" accept="image/*">
                                    @error('avatar')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                    <small class="form-text text-muted">Allowed formats: JPG, PNG, GIF. Max size: 2MB.</small>
                                    @if($user->avatar)
                                        <div class="mt-2">
                                            <small class="text-muted">Current avatar:</small><br>
                                            <img src="{{ Storage::url($user->avatar) }}" alt="Current avatar" style="max-width: 100px; max-height: 100px;">
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="profile_website">Website</label>
                                    <input type="url" class="form-control @error('profile_website') is-invalid @enderror" 
                                           id="profile_website" name="profile[website]" value="{{ old('profile_website', $user->profile->website ?? '') }}">
                                    @error('profile_website')
                                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Update User
                                </button>
                                <a href="{{ route('users.show', $user->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
