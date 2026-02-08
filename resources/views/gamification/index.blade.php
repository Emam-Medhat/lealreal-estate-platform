@extends('layouts.app')

@section('title', 'Gamification Hub')

@section('content')
<div class="container-fluid">
    <!-- Stats Overview -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ auth()->user()->gamificationLevel->total_points ?? 0 }}</h4>
                            <p>Total Points</p>
                        </div>
                        <div>
                            <i class="fas fa-star fa-2x"></i>
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
                            <h4>{{ \App\Models\Gamification\UserAchievement::where('user_id', auth()->id())->count() }}</h4>
                            <p>Achievements</p>
                        </div>
                        <div>
                            <i class="fas fa-trophy fa-2x"></i>
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
                            <h4>{{ \App\Models\Gamification\UserBadge::where('user_id', auth()->id())->count() }}</h4>
                            <p>Badges</p>
                        </div>
                        <div>
                            <i class="fas fa-medal fa-2x"></i>
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
                            <h4>{{ \App\Models\Gamification\UserChallenge::where('user_id', auth()->id())->where('status', 'active')->count() }}</h4>
                            <p>Active Challenges</p>
                        </div>
                        <div>
                            <i class="fas fa-gamepad fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('gamification.achievements') }}" class="btn btn-outline-primary btn-block mb-2">
                                <i class="fas fa-trophy"></i> View Achievements
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('gamification.badges') }}" class="btn btn-outline-info btn-block mb-2">
                                <i class="fas fa-medal"></i> My Badges
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('gamification.challenges') }}" class="btn btn-outline-success btn-block mb-2">
                                <i class="fas fa-gamepad"></i> Challenges
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('gamification.leaderboard') }}" class="btn btn-outline-warning btn-block mb-2">
                                <i class="fas fa-chart-bar"></i> Leaderboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Recent Activity</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        @php
                            $recentAchievements = \App\Models\Gamification\UserAchievement::where('user_id', auth()->id())
                                ->with('achievement')
                                ->orderBy('awarded_at', 'desc')
                                ->limit(5)
                                ->get();
                        @endphp
                        
                        @if($recentAchievements->count() > 0)
                            @foreach($recentAchievements as $userAchievement)
                                <div class="time-label">
                                    <span class="bg-primary">{{ $userAchievement->getFormattedDate() }}</span>
                                </div>
                                <div>
                                    <i class="fas fa-trophy bg-blue"></i>
                                    <div class="timeline-item">
                                        <span class="time"><i class="fas fa-clock"></i> {{ $userAchievement->awarded_at->format('H:i') }}</span>
                                        <h3 class="timeline-header">{{ $userAchievement->achievement->name }}</h3>
                                        <div class="timeline-body">
                                            {{ $userAchievement->achievement->description }}
                                            <div class="text-success">+{{ $userAchievement->points_awarded }} points</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center">No recent activity. Start completing challenges to earn achievements!</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Current Level</h5>
                </div>
                <div class="card-body text-center">
                    @php
                        $userLevel = auth()->user()->gamificationLevel;
                        $currentLevel = $userLevel ? $userLevel->level : 1;
                        $currentPoints = $userLevel ? $userLevel->total_points : 0;
                    @endphp
                    
                    <div class="level-display">
                        <h2 class="text-primary">Level {{ $currentLevel }}</h2>
                        <p>{{ $currentPoints }} points</p>
                    </div>
                    <div class="progress mb-2">
                        @php
                            $nextLevelPoints = ($currentLevel + 1) * 500;
                            $progress = min(($currentPoints / $nextLevelPoints) * 100, 100);
                        @endphp
                        <div class="progress-bar" style="width: {{ $progress }}%">
                            {{ number_format($progress, 1) }}%
                        </div>
                    </div>
                    <small class="text-muted">{{ $currentPoints }} / {{ $nextLevelPoints }} points to next level</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
