@extends('layouts.app')

@section('title', 'Generate Property Descriptions')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Generate AI Property Description</h3>
                    <div class="card-tools">
                        <a href="{{ route('ai.descriptions') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('ai.descriptions.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="property_id" class="form-label">Select Property</label>
                                    <select class="form-control" id="property_id" name="property_id" required>
                                        <option value="">Choose a property</option>
                                        @foreach($properties as $property)
                                            <option value="{{ $property->id }}">{{ $property->title }} - {{ $property->location }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="style" class="form-label">Description Style</label>
                                    <select class="form-control" id="style" name="style" required>
                                        <option value="professional">Professional</option>
                                        <option value="casual">Casual</option>
                                        <option value="marketing">Marketing</option>
                                        <option value="technical">Technical</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="additional_info" class="form-label">Additional Information (Optional)</label>
                                    <textarea class="form-control" id="additional_info" name="additional_info" rows="3" 
                                              placeholder="Add specific features or highlights you want to emphasize"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-magic"></i> Generate Description
                            </button>
                            <a href="{{ route('ai.descriptions') }}" class="btn btn-secondary">
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
