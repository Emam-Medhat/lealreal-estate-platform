@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">User Details</h3>
                    <div class="card-tools">
                        <a href="{{ route('users.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit User
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- User Profile Section -->
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                @if($user->avatar)
                                    <img src="{{ Storage::url($user->avatar) }}" alt="{{ $user->full_name }}" class="img-fluid rounded-circle mb-3" style="max-width: 200px;">
                                @else
                                    <img src="{{ asset('images/default-avatar.png') }}" alt="{{ $user->full_name }}" class="img-fluid rounded-circle mb-3" style="max-width: 200px;">
                                @endif
                                <h4>{{ $user->full_name }}</h4>
                                <p class="text-muted">{{ $user->email }}</p>
                                <div class="mb-2">
                                    <span class="badge badge-{{ $user->user_type == 'admin' ? 'danger' : ($user->user_type == 'agent' ? 'info' : 'primary') }}">
                                        {{ ucfirst($user->user_type) }}
                                    </span>
                                    <span class="badge badge-{{ $user->account_status == 'active' ? 'success' : ($user->account_status == 'inactive' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($user->account_status) }}
                                    </span>
                                    <span class="badge badge-{{ $user->kyc_status == 'verified' ? 'success' : ($user->kyc_status == 'pending' ? 'warning' : 'secondary') }}">
                                        KYC: {{ ucfirst($user->kyc_status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5>Personal Information</h5>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>First Name:</strong></td>
                                            <td>{{ $user->first_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Last Name:</strong></td>
                                            <td>{{ $user->last_name }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Email:</strong></td>
                                            <td>{{ $user->email }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Phone:</strong></td>
                                            <td>{{ $user->phone ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>UUID:</strong></td>
                                            <td><code>{{ $user->uuid }}</code></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h5>Account Information</h5>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>User Type:</strong></td>
                                            <td>{{ ucfirst($user->user_type) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Account Status:</strong></td>
                                            <td>{{ ucfirst($user->account_status) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>KYC Status:</strong></td>
                                            <td>{{ ucfirst($user->kyc_status) }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Created:</strong></td>
                                            <td>{{ $user->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Last Login:</strong></td>
                                            <td>{{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            @if($user->profile)
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5>Profile Information</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-sm">
                                                <tr>
                                                    <td><strong>Bio:</strong></td>
                                                    <td>{{ $user->profile->bio ?? 'N/A' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Website:</strong></td>
                                                    <td>{{ $user->profile->website ? '<a href="' . $user->profile->website . '" target="_blank">' . $user->profile->website . '</a>' : 'N/A' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-sm">
                                                <tr>
                                                    <td><strong>Social Links:</strong></td>
                                                    <td>
                                                        @if($user->profile->social_links && count($user->profile->social_links) > 0)
                                                            @foreach($user->profile->social_links as $platform => $link)
                                                                <a href="{{ $link }}" target="_blank" class="btn btn-sm btn-outline-primary mr-1">{{ ucfirst($platform) }}</a>
                                                            @endforeach
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Location Information -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5>Location Information</h5>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Country:</strong></td>
                                            <td>{{ $user->country ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>City:</strong></td>
                                            <td>{{ $user->city ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Timezone:</strong></td>
                                            <td>{{ $user->timezone ?? 'UTC' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <h5>Additional Information</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <table class="table table-sm">
                                                <tr>
                                                    <td><strong>Is Agent:</strong></td>
                                                    <td>{{ $user->is_agent ? 'Yes' : 'No' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Is Company:</strong></td>
                                                    <td>{{ $user->is_company ? 'Yes' : 'No' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Is Developer:</strong></td>
                                                    <td>{{ $user->is_developer ? 'Yes' : 'No' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <table class="table table-sm">
                                                <tr>
                                                    <td><strong>Is Investor:</strong></td>
                                                    <td>{{ $user->is_investor ? 'Yes' : 'No' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Email Verified:</strong></td>
                                                    <td>{{ $user->email_verified_at ? 'Yes' : 'No' }}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Two Factor:</strong></td>
                                                    <td>{{ $user->two_factor_enabled ? 'Enabled' : 'Disabled' }}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="btn-group">
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Edit User
                                </a>
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">
                                        <i class="fas fa-trash"></i> Delete User
                                    </button>
                                </form>
                                <a href="{{ route('users.activity', $user->id) }}" class="btn btn-info">
                                    <i class="fas fa-history"></i> View Activity
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Activity Log -->
    @if($userActivity && $userActivity->count() > 0)
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Recent Activity</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Action</th>
                                    <th>Description</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userActivity as $activity)
                                <tr>
                                    <td>{{ $activity->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $activity->action == 'login' ? 'success' : ($activity->action == 'logout' ? 'warning' : 'info') }}">
                                            {{ ucfirst($activity->action) }}
                                        </span>
                                    </td>
                                    <td>{{ $activity->description }}</td>
                                    <td>{{ $activity->ip_address }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
