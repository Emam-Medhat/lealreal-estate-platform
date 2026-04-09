@extends('layouts.agent')

@section('title', 'Offer Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Offer Details</h1>
            <p class="text-muted">View and manage offer information</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('agent.offers.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Offers
            </a>
            @if($offer->status == 'pending')
                <a href="{{ route('agent.offers.edit', $offer) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>Edit Offer
                </a>
                <button type="button" class="btn btn-success" onclick="acceptOffer({{ $offer->id }})">
                    <i class="fas fa-check me-2"></i>Accept
                </button>
                <button type="button" class="btn btn-danger" onclick="rejectOffer({{ $offer->id }})">
                    <i class="fas fa-times me-2"></i>Reject
                </button>
            @endif
        </div>
    </div>

    <!-- Offer Details -->
    <div class="row">
        <!-- Main Information -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Offer Information</h5>
                    <span class="badge bg-{{ $offer->getStatusColor() }} fs-6">
                        {{ ucfirst($offer->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Property</h6>
                            <p>
                                <a href="{{ route('agent.properties.show', $offer->property) }}" class="text-decoration-none">
                                    {{ $offer->property->title }}
                                </a>
                            </p>
                            <small class="text-muted">{{ $offer->property->address }}</small>
                        </div>
                        <div class="col-md-6">
                            <h6>Buyer</h6>
                            <p>{{ $offer->buyer->full_name ?? 'N/A' }}</p>
                            <small class="text-muted">{{ $offer->buyer->email ?? 'N/A' }}</small>
                        </div>
                        <div class="col-md-6">
                            <h6>Offer Price</h6>
                            <p class="h4 text-primary">${{ number_format($offer->offer_price, 2) }}</p>
                            <small class="text-muted">{{ ucfirst($offer->offer_type) }} price</small>
                        </div>
                        <div class="col-md-6">
                            <h6>Expiry Date</h6>
                            <p>{{ $offer->expiry_date->format('M d, Y') }}</p>
                            <small class="text-muted">
                                @if($offer->isExpired())
                                    <span class="text-danger">Expired</span>
                                @else
                                    {{ $offer->expiry_date->diffForHumans() }}
                                @endif
                            </small>
                        </div>
                    </div>

                    @if($offer->notes)
                        <div class="mt-3">
                            <h6>Notes</h6>
                            <p>{{ $offer->notes }}</p>
                        </div>
                    @endif

                    @if($offer->contingencies && count($offer->contingencies) > 0)
                        <div class="mt-3">
                            <h6>Contingencies</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($offer->contingencies as $contingency)
                                    <span class="badge bg-info">{{ ucfirst($contingency) }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Timeline -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <h6>Offer Created</h6>
                                <p class="text-muted">{{ $offer->created_at->format('M d, Y h:i A') }}</p>
                            </div>
                        </div>

                        @if($offer->accepted_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-success"></div>
                                <div class="timeline-content">
                                    <h6>Offer Accepted</h6>
                                    <p class="text-muted">{{ $offer->accepted_at->format('M d, Y h:i A') }}</p>
                                </div>
                            </div>
                        @endif

                        @if($offer->rejected_at)
                            <div class="timeline-item">
                                <div class="timeline-marker bg-danger"></div>
                                <div class="timeline-content">
                                    <h6>Offer Rejected</h6>
                                    <p class="text-muted">{{ $offer->rejected_at->format('M d, Y h:i A') }}</p>
                                    @if($offer->rejection_reason)
                                        <p class="text-danger">{{ $offer->rejection_reason }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        @if($offer->counterOffers->count() > 0)
                            @foreach($offer->counterOffers as $counter)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-warning"></div>
                                    <div class="timeline-content">
                                        <h6>Counter Offer</h6>
                                        <p class="text-muted">${{ number_format($counter->counter_price, 2) }}</p>
                                        <p class="text-muted">{{ $counter->created_at->format('M d, Y h:i A') }}</p>
                                        @if($counter->notes)
                                            <p>{{ $counter->notes }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Quick Actions -->
            @if($offer->status == 'pending')
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success" onclick="acceptOffer({{ $offer->id }})">
                                <i class="fas fa-check me-2"></i>Accept Offer
                            </button>
                            <button type="button" class="btn btn-warning" onclick="showCounterModal()">
                                <i class="fas fa-exchange-alt me-2"></i>Counter Offer
                            </button>
                            <button type="button" class="btn btn-danger" onclick="rejectOffer({{ $offer->id }})">
                                <i class="fas fa-times me-2"></i>Reject Offer
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Property Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Property Details</h5>
                </div>
                <div class="card-body">
                    <img src="{{ $offer->property->getFirstImageUrl() ?? '/images/placeholder-property.jpg' }}" 
                         class="img-fluid mb-3" alt="Property">
                    <h6>{{ $offer->property->title }}</h6>
                    <p class="text-muted">{{ $offer->property->address }}</p>
                    <p><strong>Price:</strong> ${{ number_format($offer->property->price, 2) }}</p>
                    <p><strong>Type:</strong> {{ $offer->property->propertyType->name ?? 'N/A' }}</p>
                    <p><strong>Status:</strong> {{ ucfirst($offer->property->status) }}</p>
                </div>
            </div>

            <!-- Buyer Details -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Buyer Details</h5>
                </div>
                <div class="card-body">
                    <h6>{{ $offer->buyer->full_name ?? 'N/A' }}</h6>
                    <p class="text-muted">{{ $offer->buyer->email ?? 'N/A' }}</p>
                    <p class="text-muted">{{ $offer->buyer->phone ?? 'N/A' }}</p>
                    <p><strong>Lead Status:</strong> {{ ucfirst($offer->buyer->lead_status) }}</p>
                </div>
            </div>
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

function showCounterModal() {
    new bootstrap.Modal(document.getElementById('counterOfferModal')).show();
}

document.getElementById('counterOfferForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const form = this;
    const formData = new FormData(form);
    
    fetch(`/agent/offers/{{ $offer->id }}/counter`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
});
</script>
@endpush
