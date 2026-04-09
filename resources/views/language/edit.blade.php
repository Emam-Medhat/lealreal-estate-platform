@extends('layouts.app')

@section('title', 'Edit Language')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Language: {{ $language->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('language.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('language.update', $language->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code" class="form-label">Language Code</label>
                                    <input type="text" class="form-control" id="code" name="code" 
                                           value="{{ $language->code }}" maxlength="2" required>
                                    <small class="text-muted">2-letter language code (e.g., en, ar, fr)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Language Name</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="{{ $language->name }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="native_name" class="form-label">Native Name</label>
                                    <input type="text" class="form-control" id="native_name" name="native_name" 
                                           value="{{ $language->native_name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="direction" class="form-label">Text Direction</label>
                                    <select class="form-control" id="direction" name="direction">
                                        <option value="ltr" {{ $language->direction === 'ltr' ? 'selected' : '' }}>Left to Right (LTR)</option>
                                        <option value="rtl" {{ $language->direction === 'rtl' ? 'selected' : '' }}>Right to Left (RTL)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="locale" class="form-label">Locale</label>
                                    <input type="text" class="form-control" id="locale" name="locale" 
                                           value="{{ $language->locale }}" required>
                                    <small class="text-muted">Locale code (e.g., en_US, ar_EG)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="flag" class="form-label">Flag Icon</label>
                                    <input type="text" class="form-control" id="flag" name="flag" 
                                           value="{{ $language->flag }}" maxlength="10">
                                    <small class="text-muted">Flag icon name (e.g., us, gb, fr)</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sort_order" class="form-label">Sort Order</label>
                                    <input type="number" class="form-control" id="sort_order" name="sort_order" 
                                           value="{{ $language->sort_order }}" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category" class="form-label">Category</label>
                                    <select class="form-control" id="category" name="category">
                                        <option value="european" {{ $language->category === 'european' ? 'selected' : '' }}>European</option>
                                        <option value="asian" {{ $language->category === 'asian' ? 'selected' : '' }}>Asian</option>
                                        <option value="middle-eastern" {{ $language->category === 'middle-eastern' ? 'selected' : '' }}>Middle Eastern</option>
                                        <option value="african" {{ $language->category === 'african' ? 'selected' : '' }}>African</option>
                                        <option value="americas" {{ $language->category === 'americas' ? 'selected' : '' }}>Americas</option>
                                        <option value="oceania" {{ $language->category === 'oceania' ? 'selected' : '' }}>Oceania</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_default" name="is_default" 
                                           {{ $language->is_default ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default">
                                        Set as default language
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                           {{ $language->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="metadata" class="form-label">Metadata (JSON)</label>
                            <textarea class="form-control" id="metadata" name="metadata" rows="3">{{ $language->metadata ? json_encode($language->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '' }}</textarea>
                            <small class="text-muted">Additional metadata in JSON format</small>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Language
                            </button>
                            <a href="{{ route('language.show', $language->id) }}" class="btn btn-info">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('language.index') }}" class="btn btn-secondary">
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
