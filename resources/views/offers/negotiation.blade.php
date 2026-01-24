@extends('layouts.app')

@section('title', 'Negotiation - ' . $offer->property->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center">
                <a href="{{ route('offers.show', $offer->id) }}" class="text-blue-600 hover:text-blue-700 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">Negotiation</h1>
            </div>
            
            <!-- Status Badge -->
            <div>
                @if ($negotiation->isActive())
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                        Active Negotiation
                    </span>
                @elseif ($negotiation->isPaused())
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        <span class="w-2 h-2 bg-yellow-400 rounded-full mr-2"></span>
                        Paused
                    </span>
                @elseif ($negotiation->isCompleted())
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        <span class="w-2 h-2 bg-blue-400 rounded-full mr-2"></span>
                        Completed
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                        Terminated
                    </span>
                @endif
            </div>
        </div>
        
        <!-- Property Summary -->
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="font-semibold text-lg">{{ $offer->property->title }}</h3>
                    <p class="text-gray-600 text-sm">{{ $offer->property->location }}</p>
                    <div class="flex items-center mt-2 space-x-4 text-sm text-gray-500">
                        <span>Original Offer: {{ $offer->getFormattedAmount() }}</span>
                        <span>•</span>
                        <span>Started: {{ $negotiation->created_at->format('M j, Y') }}</span>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Last Activity</p>
                    <p class="font-medium">{{ $negotiation->getLastActivityTime() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Messages -->
            <div class="bg-white rounded-lg shadow">
                <div class="border-b px-6 py-4">
                    <h2 class="text-lg font-semibold">Negotiation Messages</h2>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4 mb-6 max-h-96 overflow-y-auto" id="messagesContainer">
                        @forelse ($messages as $message)
                            <div class="flex {{ $message->user_id == auth()->id() ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-xs lg:max-w-md">
                                    <div class="flex items-center mb-1 {{ $message->user_id == auth()->id() ? 'justify-end' : '' }}">
                                        <span class="text-xs text-gray-500">{{ $message->user->name }}</span>
                                        <span class="text-xs text-gray-400 ml-2">{{ $message->getTimeAgo() }}</span>
                                    </div>
                                    <div class="{{ $message->user_id == auth()->id() ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-800' }} rounded-lg px-4 py-2">
                                        @if ($message->isProposal())
                                            <div class="border-b border-blue-400 pb-2 mb-2">
                                                <span class="text-xs font-semibold">PROPOSAL:</span>
                                            </div>
                                        @endif
                                        <p class="text-sm">{{ $message->message }}</p>
                                        
                                        @if ($message->isProposal() && $message->terms)
                                            <div class="mt-2 pt-2 border-t border-blue-400">
                                                <p class="text-xs font-semibold mb-1">Terms:</p>
                                                @foreach ($message->terms as $term)
                                                    <p class="text-xs">• {{ $term }}</p>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <p class="text-gray-500">No messages yet. Start the conversation!</p>
                            </div>
                        @endforelse
                    </div>
                    
                    <!-- Message Form -->
                    @if ($negotiation->isActive() && $negotiation->canUserParticipate(auth()->user()))
                        <form id="messageForm" action="{{ route('negotiations.message', $negotiation->id) }}" method="POST" class="border-t pt-4">
                            @csrf
                            <div class="mb-3">
                                <textarea name="message" 
                                          rows="3" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                          placeholder="Type your message..."
                                          required></textarea>
                            </div>
                            
                            <div class="flex space-x-3">
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    Send Message
                                </button>
                                <button type="button" 
                                        id="proposeTermsBtn"
                                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                                    Propose Terms
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Proposals -->
            <div class="bg-white rounded-lg shadow">
                <div class="border-b px-6 py-4">
                    <h2 class="text-lg font-semibold">Term Proposals</h2>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        @forelse ($proposals as $proposal)
                            <div class="border rounded-lg p-4 {{ $proposal->status === 'accepted' ? 'bg-green-50 border-green-200' : ($proposal->status === 'rejected' ? 'bg-red-50 border-red-200' : 'bg-gray-50 border-gray-200') }}">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <span class="font-medium">{{ $proposal->user->name }}</span>
                                        <span class="text-xs text-gray-500 ml-2">{{ $proposal->getTimeAgo() }}</span>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $proposal->status === 'accepted' ? 'bg-green-100 text-green-800' : ($proposal->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ $proposal->getStatusLabel() }}
                                    </span>
                                </div>
                                
                                <p class="text-sm text-gray-700 mb-2">{{ $proposal->message }}</p>
                                
                                @if ($proposal->terms)
                                    <div class="text-sm">
                                        <p class="font-semibold mb-1">Proposed Terms:</p>
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach ($proposal->terms as $term)
                                                <li class="text-gray-600">{{ $term }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                
                                @if ($proposal->status === 'pending' && $proposal->user_id !== auth()->id())
                                    <div class="mt-3 flex space-x-2">
                                        <form action="{{ route('negotiations.accept-proposal', [$negotiation->id, $proposal->id]) }}" method="POST">
                                            @csrf
                                            <button type="submit" 
                                                    class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700"
                                                    onclick="return confirm('Accept this proposal and complete the negotiation?')">
                                                Accept
                                            </button>
                                        </form>
                                        <form action="{{ route('negotiations.reject-proposal', [$negotiation->id, $proposal->id]) }}" method="POST">
                                            @csrf
                                            <button type="submit" 
                                                    class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700"
                                                    onclick="return confirm('Reject this proposal?')">
                                                Reject
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-gray-500 text-center py-4">No proposals yet</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Participants -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Participants</h3>
                <div class="space-y-3">
                    @foreach ($participants as $participant)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-medium">{{ strtoupper(substr($participant->user->name, 0, 1)) }}</span>
                                </div>
                                <div class="ml-3">
                                    <p class="font-medium text-sm">{{ $participant->user->name }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ $participant->user_id === $offer->user_id ? 'Buyer' : 'Seller' }}
                                    </p>
                                </div>
                            </div>
                            @if ($participant->hasUnreadMessages())
                                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Negotiation Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold mb-4">Negotiation Details</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-500">Status</p>
                        <p class="font-medium">{{ $negotiation->getStatusLabel() }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Started By</p>
                        <p class="font-medium">{{ $negotiation->initiator->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Duration</p>
                        <p class="font-medium">{{ $negotiation->created_at->diffInDays(now()) }} days</p>
                    </div>
                    @if ($negotiation->expires_at)
                        <div>
                            <p class="text-sm text-gray-500">Expires In</p>
                            <p class="font-medium">{{ $negotiation->getDaysUntilExpiration() }} days</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            @if ($negotiation->isActive())
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Actions</h3>
                    <div class="space-y-3">
                        @if ($negotiation->initiated_by === auth()->id())
                            <form action="{{ route('negotiations.pause', $negotiation->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                                    Pause Negotiation
                                </button>
                            </form>
                        @endif
                        
                        <form action="{{ route('negotiations.terminate', $negotiation->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to terminate this negotiation?')">
                            @csrf
                            <input type="hidden" name="reason" value="Mutual agreement">
                            <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                Terminate Negotiation
                            </button>
                        </form>
                    </div>
                </div>
            @elseif ($negotiation->isPaused() && $negotiation->initiated_by === auth()->id())
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold mb-4">Resume Negotiation</h3>
                    <form action="{{ route('negotiations.resume', $negotiation->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Resume Negotiation
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Proposal Modal -->
<div id="proposalModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-lg w-full mx-4 max-h-screen overflow-y-auto">
        <h3 class="text-lg font-semibold mb-4">Propose New Terms</h3>
        <form id="proposalForm" action="{{ route('negotiations.propose-terms', $negotiation->id) }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                <textarea name="message" 
                          rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Explain your proposed terms..."
                          required></textarea>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Proposed Terms</label>
                <div id="termsContainer" class="space-y-2">
                    <div class="flex space-x-2">
                        <input type="text" name="terms[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter a term...">
                    </div>
                </div>
                <button type="button" id="addTermBtn" class="mt-2 text-sm text-blue-600 hover:text-blue-700">
                    + Add another term
                </button>
            </div>
            
            <div class="flex space-x-4">
                <button type="button" id="cancelProposal" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Submit Proposal
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const messageForm = document.getElementById('messageForm');
    const messagesContainer = document.getElementById('messagesContainer');
    const proposeTermsBtn = document.getElementById('proposeTermsBtn');
    const proposalModal = document.getElementById('proposalModal');
    const cancelProposal = document.getElementById('cancelProposal');
    const proposalForm = document.getElementById('proposalForm');
    const addTermBtn = document.getElementById('addTermBtn');
    const termsContainer = document.getElementById('termsContainer');
    
    // Auto-scroll to bottom
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    // Show proposal modal
    proposeTermsBtn.addEventListener('click', function() {
        proposalModal.classList.remove('hidden');
    });
    
    // Hide proposal modal
    cancelProposal.addEventListener('click', function() {
        proposalModal.classList.add('hidden');
    });
    
    // Add term input
    addTermBtn.addEventListener('click', function() {
        const termInput = document.createElement('div');
        termInput.className = 'flex space-x-2';
        termInput.innerHTML = `
            <input type="text" name="terms[]" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter a term...">
            <button type="button" class="px-2 py-1 text-red-600 hover:text-red-700" onclick="this.parentElement.remove()">Remove</button>
        `;
        termsContainer.appendChild(termInput);
    });
    
    // Auto-refresh messages
    setInterval(function() {
        // This would typically fetch new messages via AJAX
        // For now, just refresh the last activity time
    }, 10000); // Refresh every 10 seconds
});
</script>
@endsection
