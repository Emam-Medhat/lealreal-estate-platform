@extends('layouts.app')

@section('title', 'AI Descriptions Generator')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">AI Property Descriptions</h3>
                    <div class="card-tools">
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateDescriptionModal">
                            <i class="fas fa-plus"></i> Generate Description
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Property</th>
                                    <th>Original Description</th>
                                    <th>AI Description</th>
                                    <th>Style</th>
                                    <th>Word Count</th>
                                    <th>Generated At</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $propertiesWithAI = \App\Models\Property::whereNotNull('ai_description')
                                        ->orderBy('updated_at', 'desc')
                                        ->get();
                                @endphp
                                
                                @if($propertiesWithAI->count() > 0)
                                    @foreach($propertiesWithAI as $property)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $property->main_image ?? '/images/default-property.jpg' }}" 
                                                         class="rounded mr-2" style="width: 50px; height: 50px; object-fit: cover;">
                                                    <div>
                                                        <strong>{{ $property->title }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $property->location }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 200px;">
                                                    {{ Str::limit($property->description ?? 'No description', 100) }}
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 250px;">
                                                    {{ Str::limit($property->ai_description, 150) }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $property->ai_description_style ?? 'professional' }}</span>
                                            </td>
                                            <td>{{ str_word_count($property->ai_description) }}</td>
                                            <td>{{ $property->ai_description_generated_at ? $property->ai_description_generated_at->format('M j, Y H:i') : 'N/A' }}</td>
                                            <td>
                                                <span class="badge badge-success">Generated</span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary view-description" data-property-id="{{ $property->id }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button class="btn btn-outline-success regenerate-description" data-property-id="{{ $property->id }}">
                                                        <i class="fas fa-sync"></i>
                                                    </button>
                                                    <button class="btn btn-outline-info apply-description" data-property-id="{{ $property->id }}">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">
                                            <i class="fas fa-file-alt fa-3x mb-3"></i>
                                            <p>No AI descriptions generated yet</p>
                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateDescriptionModal">
                                                Generate Your First AI Description
                                            </button>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4>{{ $propertiesWithAI->count() }}</h4>
                            <p>AI Descriptions</p>
                        </div>
                        <div>
                            <i class="fas fa-file-alt fa-2x"></i>
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
                            <h4>{{ $propertiesWithAI->where('ai_description_applied', true)->count() }}</h4>
                            <p>Applied</p>
                        </div>
                        <div>
                            <i class="fas fa-check-circle fa-2x"></i>
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
                            <h4>{{ $propertiesWithAI->avg('ai_description_word_count') ?? 0 }}</h4>
                            <p>Avg Words</p>
                        </div>
                        <div>
                            <i class="fas fa-font fa-2x"></i>
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
                            <h4>{{ \App\Models\Property::count() }}</h4>
                            <p>Total Properties</p>
                        </div>
                        <div>
                            <i class="fas fa-home fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Generate Description Modal -->
<div class="modal fade" id="generateDescriptionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Generate AI Description</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="generateDescriptionForm">
                    <div class="mb-3">
                        <label for="propertySelect" class="form-label">Select Property</label>
                        <select class="form-select" id="propertySelect" required>
                            <option value="">Choose a property</option>
                            @php
                                $allProperties = \App\Models\Property::whereNull('ai_description')
                                    ->orWhere('ai_description', '')
                                    ->get();
                            @endphp
                            @foreach($allProperties as $property)
                                <option value="{{ $property->id }}">{{ $property->title }} - {{ $property->location }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="descriptionStyle" class="form-label">Description Style</label>
                        <select class="form-select" id="descriptionStyle" required>
                            <option value="professional">Professional</option>
                            <option value="casual">Casual</option>
                            <option value="marketing">Marketing</option>
                            <option value="technical">Technical</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="descriptionTone" class="form-label">Tone</label>
                        <select class="form-select" id="descriptionTone">
                            <option value="neutral">Neutral</option>
                            <option value="enthusiastic">Enthusiastic</option>
                            <option value="formal">Formal</option>
                            <option value="friendly">Friendly</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="additionalInfo" class="form-label">Additional Information (Optional)</label>
                        <textarea class="form-control" id="additionalInfo" rows="3" 
                                  placeholder="Add any specific features or highlights you want to emphasize"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="generateDescription()">Generate Description</button>
            </div>
        </div>
    </div>
</div>

<!-- View Description Modal -->
<div class="modal fade" id="viewDescriptionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">AI Description Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewDescriptionContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    $('.view-description').on('click', function() {
        const propertyId = $(this).data('property-id');
        viewDescription(propertyId);
    });
    
    $('.regenerate-description').on('click', function() {
        const propertyId = $(this).data('property-id');
        regenerateDescription(propertyId);
    });
    
    $('.apply-description').on('click', function() {
        const propertyId = $(this).data('property-id');
        applyDescription(propertyId);
    });
});

function generateDescription() {
    const formData = {
        property_id: $('#propertySelect').val(),
        style: $('#descriptionStyle').val(),
        tone: $('#descriptionTone').val(),
        additional_info: $('#additionalInfo').val()
    };

    $.ajax({
        url: '/api/ai/generate-description',
        method: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                alert('AI description generated successfully!');
                $('#generateDescriptionModal').modal('hide');
                location.reload();
            } else {
                alert('Generation failed: ' + response.message);
            }
        },
        error: function() {
            alert('Error generating description');
        }
    });
}

function viewDescription(propertyId) {
    $.ajax({
        url: `/api/properties/${propertyId}`,
        method: 'GET',
        success: function(response) {
            const property = response.property;
            const content = `
                <div class="row">
                    <div class="col-md-6">
                        <h6>Property Information</h6>
                        <p><strong>Title:</strong> ${property.title}</p>
                        <p><strong>Location:</strong> ${property.location}</p>
                        <p><strong>Type:</strong> ${property.property_type}</p>
                        <p><strong>Bedrooms:</strong> ${property.bedrooms}</p>
                        <p><strong>Bathrooms:</strong> ${property.bathrooms}</p>
                        <p><strong>Area:</strong> ${property.area} sq ft</p>
                    </div>
                    <div class="col-md-6">
                        <h6>AI Description</h6>
                        <div class="border p-3 rounded" style="max-height: 300px; overflow-y: auto;">
                            ${property.ai_description}
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">
                                <strong>Style:</strong> ${property.ai_description_style}<br>
                                <strong>Words:</strong> ${property.ai_description_word_count}<br>
                                <strong>Generated:</strong> ${property.ai_description_generated_at}
                            </small>
                        </div>
                    </div>
                </div>
            `;
            $('#viewDescriptionContent').html(content);
            $('#viewDescriptionModal').modal('show');
        }
    });
}

function regenerateDescription(propertyId) {
    if (confirm('Are you sure you want to regenerate the AI description? This will replace the existing one.')) {
        $.ajax({
            url: '/api/ai/generate-description',
            method: 'POST',
            data: {
                property_id: propertyId,
                style: 'professional',
                regenerate: true
            },
            success: function(response) {
                if (response.success) {
                    alert('AI description regenerated successfully!');
                    location.reload();
                } else {
                    alert('Regeneration failed: ' + response.message);
                }
            },
            error: function() {
                alert('Error regenerating description');
            }
        });
    }
}

function applyDescription(propertyId) {
    if (confirm('Are you sure you want to apply this AI description as the main property description?')) {
        $.ajax({
            url: `/api/properties/${propertyId}/apply-ai-description`,
            method: 'POST',
            success: function(response) {
                if (response.success) {
                    alert('AI description applied successfully!');
                    location.reload();
                } else {
                    alert('Failed to apply description: ' + response.message);
                }
            },
            error: function() {
                alert('Error applying description');
            }
        });
    }
}
</script>
@endpush
