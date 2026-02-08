@extends('layouts.app')

@section('title', 'Generate AI Images')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Generate AI Property Images</h3>
                    <div class="card-tools">
                        <a href="{{ route('ai.images') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('ai.images.store') }}">
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
                                    <label for="styles" class="form-label">Image Styles</label>
                                    <div class="form-control">
                                        @foreach(['modern', 'luxury', 'traditional', 'minimal', 'aerial', '3d_render', 'virtual_tour'] as $style)
                                            <div class="form-check">
                                                <input type="checkbox" name="styles[]" value="{{ $style }}" checked>
                                                <label>{{ ucfirst($style) }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="quality" class="form-label">Image Quality</label>
                                    <select class="form-control" id="quality" name="quality">
                                        <option value="standard">Standard</option>
                                        <option value="high">High</option>
                                        <option value="ultra">Ultra</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="additional_info" class="form-label">Additional Requirements (Optional)</label>
                                    <textarea class="form-control" id="additional_info" rows="3" 
                                              placeholder="Specify any specific image requirements"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-image"></i> Generate Images
                            </button>
                            <a href="{{ route('ai.images') }}" class="btn btn-secondary">
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
