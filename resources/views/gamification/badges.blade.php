@extends('layouts.app')

@section('title', 'Badges Collection')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">My Badges</h3>
                    <div class="card-tools">
                        <select class="form-control form-control-sm" id="badgeFilter">
                            <option value="all">All Badges</option>
                            <option value="owned">Owned</option>
                            <option value="locked">Locked</option>
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row" id="badgesGrid">
                        @foreach($badges as $badge)
                            <div class="col-md-3 mb-3 badge-item" 
                                 data-status="{{ $badge->userBadges->count() > 0 ? 'owned' : 'locked' }}"
                                 data-category="{{ $badge->category }}"
                                 data-rarity="{{ $badge->rarity }}">
                                <div class="card text-center {{ $badge->userBadges->count() > 0 ? 'border-warning' : 'border-secondary' }}">
                                    <div class="card-body">
                                        <div class="badge-icon mb-2">
                                            @if($badge->userBadges->count() > 0)
                                                <i class="fas fa-medal fa-3x" style="color: {{ $badge->color }}"></i>
                                            @else
                                                <i class="fas fa-lock fa-3x text-muted"></i>
                                            @endif
                                        </div>
                                        <h5 class="card-title">{{ $badge->name }}</h5>
                                        <p class="card-text small text-muted">{{ $badge->description }}</p>
                                        <div class="badge-meta">
                                            <span class="badge" style="background-color: {{ $badge->color }};">{{ $badge->category }}</span>
                                            <span class="badge badge-{{ $badge->rarity === 'legendary' ? 'danger' : ($badge->rarity === 'epic' ? 'warning' : 'secondary') }}">
                                                {{ $badge->rarity }}
                                            </span>
                                        </div>
                                        @if($badge->userBadges->count() > 0)
                                            <div class="mt-2">
                                                <small class="text-warning">
                                                    <i class="fas fa-star"></i> Earned on {{ $badge->userBadges->first()->getFormattedDate() }}
                                                </small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Badge Statistics -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Collection Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="text-primary">{{ $badges->where('userBadges', '>', 0)->count() }}</h4>
                            <small>Owned</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-secondary">{{ $badges->where('userBadges', '=', 0)->count() }}</h4>
                            <small>Locked</small>
                        </div>
                        <div class="col-4">
                            <h4 class="text-warning">{{ round(($badges->where('userBadges', '>', 0)->count() / $badges->count()) * 100) }}%</h4>
                            <small>Complete</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Rarity Distribution</h5>
                </div>
                <div class="card-body">
                    @foreach(['common', 'uncommon', 'rare', 'epic', 'legendary'] as $rarity)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>{{ ucfirst($rarity) }}</span>
                            <div class="progress" style="width: 60%;">
                                @php
                                    $count = $badges->where('rarity', $rarity)->count();
                                    $owned = $badges->where('rarity', $rarity)->where('userBadges', '>', 0)->count();
                                    $percentage = $count > 0 ? ($owned / $count) * 100 : 0;
                                @endphp
                                <div class="progress-bar" style="width: {{ $percentage }}%">
                                    {{ $owned }}/{{ $count }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('#badgeFilter').on('change', function() {
        const filter = $(this).val();
        
        if (filter === 'all') {
            $('.badge-item').show();
        } else {
            $('.badge-item').hide();
            $(`.badge-item[data-status="${filter}"]`).show();
        }
    });
});
</script>
@endpush
