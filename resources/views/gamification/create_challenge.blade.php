@extends('layouts.app')

@section('title', 'Create Challenge')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Create Challenge</h3>
                    <div class="card-tools">
                        <a href="{{ route('gamification.challenges') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('gamification.challenges.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Challenge Name</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           placeholder="Property Sales Challenge" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type" class="form-label">Challenge Type</label>
                                    <select class="form-control" id="type" name="type" required>
                                        <option value="daily">Daily</option>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                        <option value="seasonal">Seasonal</option>
                                        <option value="special">Special</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="difficulty" class="form-label">Difficulty</label>
                                    <select class="form-control" id="difficulty" name="difficulty" required>
                                        <option value="easy">Easy</option>
                                        <option value="medium">Medium</option>
                                        <option value="hard">Hard</option>
                                        <option value="expert">Expert</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="reward_points" class="form-label">Reward Points</label>
                                    <input type="number" class="form-control" id="reward_points" name="reward_points" 
                                           value="500" min="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date" class="form-label">Start Date</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" 
                                           value="{{ now()->format('Y-m-d') }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" 
                                           value="{{ now()->addDays(30)->format('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="max_participants" class="form-label">Max Participants</label>
                                    <input type="number" class="form-control" id="max_participants" name="max_participants" 
                                           placeholder="100">
                                    <small class="text-muted">Leave empty for unlimited</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="reward_badge_id" class="form-label">Reward Badge (Optional)</label>
                                    <select class="form-control" id="reward_badge_id" name="reward_badge_id">
                                        <option value="">No badge</option>
                                        @foreach($badges as $badge)
                                            <option value="{{ $badge->id }}">{{ $badge->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4" 
                                              placeholder="Complete 10 property sales this month to earn bonus points" required></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="requirements" class="form-label">Requirements</label>
                                    <textarea class="form-control" id="requirements" name="requirements" rows="4" 
                                              placeholder='{"sales_count": 10, "total_amount": 1000000, "properties_sold": 5}' required></textarea>
                                    <small class="text-muted">Requirements in JSON format</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="created_by" class="form-label">Created By</label>
                                    <select class="form-control" id="created_by" name="created_by" required>
                                        <option value="{{ auth()->id() }}">{{ auth()->user()->name }}</option>
                                        @foreach($users as $user)
                                            @if($user->id !== auth()->id())
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
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
                                <i class="fas fa-plus"></i> Create Challenge
                            </button>
                            <a href="{{ route('gamification.challenges') }}" class="btn btn-secondary">
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
