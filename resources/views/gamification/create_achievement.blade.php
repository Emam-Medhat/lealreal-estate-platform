@extends('layouts.app')

@section('title', 'Create Achievement')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create Achievement</h3>
                    <div class="card-tools">
                        <a href="{{ route('gamification.achievements') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('gamification.achievements.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="key" class="form-label">Achievement Key</label>
                                    <input type="text" class="form-control" id="key" name="key" 
                                           placeholder="first_property_sale" required>
                                    <small class="text-muted">Unique identifier for the achievement</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Achievement Name</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           placeholder="First Property Sale" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" 
                                              placeholder="Awarded for completing your first property sale" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type" class="form-label">Achievement Type</label>
                                    <select class="form-control" id="type" name="type" required>
                                        <option value="property_listing">Property Listing</option>
                                        <option value="property_sale">Property Sale</option>
                                        <option value="user_engagement">User Engagement</option>
                                        <option value="social_activity">Social Activity</option>
                                        <option value="investment">Investment</option>
                                        <option value="milestone">Milestone</option>
                                        <option value="special">Special</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="points_reward" class="form-label">Points Reward</label>
                                    <input type="number" class="form-control" id="points_reward" name="points_reward" 
                                           value="100" min="0" required>
                                    <small class="text-muted">Points awarded for this achievement</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="badge_id" class="form-label">Badge (Optional)</label>
                                    <select class="form-control" id="badge_id" name="badge_id">
                                        <option value="">No badge</option>
                                        @foreach($badges as $badge)
                                            <option value="{{ $badge->id }}">{{ $badge->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="icon" class="form-label">Icon</label>
                                    <input type="text" class="form-control" id="icon" name="icon" 
                                           placeholder="fas fa-trophy">
                                    <small class="text-muted">Font Awesome icon class</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="requirements" class="form-label">Requirements</label>
                                    <textarea class="form-control" id="requirements" name="requirements" rows="4" 
                                              placeholder='{"property_count": 1, "sale_amount": 100000, "user_level": 1}' required></textarea>
                                    <small class="text-muted">Requirements in JSON format</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sort_order" class="form-label">Sort Order</label>
                                    <input type="number" class="form-control" id="sort_order" name="sort_order" 
                                           value="0" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" checked>
                                    <label class="form-check-label" for="is_active">
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Achievement
                            </button>
                            <a href="{{ route('gamification.achievements') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
