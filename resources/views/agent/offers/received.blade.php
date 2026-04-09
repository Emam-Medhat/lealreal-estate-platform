@extends('layouts.agent')

@section('title', 'Received Offers')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Received Offers</h1>
            <p class="text-muted">Offers received on your properties</p>
        </div>
        <a href="{{ route('agent.offers.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>All Offers
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $offers->count() }}</h4>
                            <p class="card-text">Total Received</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-inbox fa-2x"></i>
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
                            <h4 class="card-title">{{ $offers->where('status', 'pending')->count() }}</h4>
                            <p class="card-text">Pending</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
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
                            <h4 class="card-title">{{ $offers->where('status', 'accepted')->count() }}</h4>
                            <p class="card-text">Accepted</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
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
                            <h4 class="card-title">${{ number_format($offers->where('status', 'accepted')->sum('offer_price'), 0) }}</h4>
                            <p class="card-text">Total Value</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('agent.offers.received') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            <option value="countered" {{ request('status') == 'countered' ? 'selected' : '' }}>Countered</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search offers...">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary me-2">Filter</button>
                        <a href="{{ route('agent.offers.received') }}" class="btn btn-outline-secondary">Clear</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Offers Table -->
    <div class="card">
        <div class="card-body">
            @if($offers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Property</th>
                                <th>Buyer</th>
                                <th>Offer Price</th>
                                <th>Market Price</th>
                                <th>Offer %</th>
                                <th>Status</th>
                                <th>Expiry</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($offers as $offer)
                                <tr>
                                    <td>
                                        <a href="{{ route('agent.properties.show', $offer->property) }}" class="text-decoration-none">
                                            {{ $offer->property->title }}
                                        </a>
                                    </td>
                                    <td>{{ $offer->buyer->full_name ?? 'N/A' }}</td>
                                    <td class="fw-bold">${{ number_format($offer->offer_price, 2) }}</td>
                                    <td>${{ number_format($offer->property->price, 2) }}</td>
                                    <td>
                                        @php
                                        $percentage = ($offer->offer_price / $offer->property->price) * 100;
                                        @endphp
                                        <span class="badge bg-{{ $percentage >= 95 ? 'success' : ($percentage >= 85 ? 'warning' : 'danger') }}">
                                            {{ number_format($percentage, 1) }}%
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $offer->getStatusColor() }}">
                                            {{ ucfirst($offer->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $offer->expiry_date->format('M d, Y') }}
                                        @if($offer->isExpired())
                                            <span class="badge bg-danger">Expired</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('agent.offers.show', $offer) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($offer->status == 'pending')
                                                <button type="button" class="btn btn-outline-success" onclick="acceptOffer({{ $offer->id }})">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger" onclick="rejectOffer({{ $offer->id }})">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-warning" onclick="showCounterModal({{ $offer->id }})">
                                                    <i class="fas fa-exchange-alt"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <span class="text-muted">Showing {{ $offers->firstItem() }} to {{ $offers->lastItem() }} of {{ $offers->total() }} offers</span>
                    {{ $offers->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h4>No received offers found</h4>
                    <p class="text-muted">You haven't received any offers on your properties yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Accept Offer Modal -->
<div class="modal fade" id="acceptOfferModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Accept Offer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to accept this offer?</p>
                <p class="text-muted">This will update the property status to "Under Contract".</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="acceptOfferForm" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success">Accept Offer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reject Offer Modal -->
<div class="modal fade" id="rejectOfferModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Offer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="rejectOfferForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Rejection Reason</label>
                        <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject Offer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Counter Offer Modal -->
<div class="modal fade" id="counterOfferModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Counter Offer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="counterOfferForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Counter Price</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" name="counter_price" class="form-control" 
                                   step="0.01" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes</label>
                        <textarea name="counter_notes" class="form-control" rows="3" 
                                  placeholder="Reason for counter offer..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Send Counter Offer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function acceptOffer(offerId) {
    document.getElementById('acceptOfferForm').action = `/agent/offers/${offerId}/accept`;
    new bootstrap.Modal(document.getElementById('acceptOfferModal')).show();
}

function rejectOffer(offerId) {
    document.getElementById('rejectOfferForm').action = `/agent/offers/${offerId}/reject`;
    new bootstrap.Modal(document.getElementById('rejectOfferModal')).show();
}

function showCounterModal(offerId) {
    document.getElementById('counterOfferForm').action = `/agent/offers/${offerId}/counter`;
    new bootstrap.Modal(document.getElementById('counterOfferModal')).show();
}
</script>
@endpush
