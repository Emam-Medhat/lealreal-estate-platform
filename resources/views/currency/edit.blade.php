@extends('layouts.app')

@section('title', 'Edit Currency')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Currency: {{ $currency->name }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('currency.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('currency.update', $currency->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code" class="form-label">Currency Code</label>
                                    <input type="text" class="form-control" id="code" name="code" 
                                           value="{{ $currency->code }}" maxlength="3" required>
                                    <small class="text-muted">3-letter currency code (e.g., USD, EUR, GBP)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name" class="form-label">Currency Name</label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="{{ $currency->name }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="native_name" class="form-label">Native Name</label>
                                    <input type="text" class="form-control" id="native_name" name="native_name" 
                                           value="{{ $currency->native_name }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="symbol" class="form-label">Symbol</label>
                                    <input type="text" class="form-control" id="symbol" name="symbol" 
                                           value="{{ $currency->symbol }}" maxlength="10" required>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="precision" class="form-label">Decimal Precision</label>
                                    <input type="number" class="form-control" id="precision" name="precision" 
                                           value="{{ $currency->precision }}" min="0" max="8" required>
                                    <small class="text-muted">Number of decimal places</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="exchange_rate_provider" class="form-label">Exchange Rate Provider</label>
                                    <select class="form-control" id="exchange_rate_provider" name="exchange_rate_provider">
                                        <option value="fixer" {{ $currency->exchange_rate_provider === 'fixer' ? 'selected' : '' }}>Fixer.io</option>
                                        <option value="exchangerate" {{ $currency->exchange_rate_provider === 'exchangerate' ? 'selected' : '' }}>Exchangerate</option>
                                        <option value="currencylayer" {{ $currency->exchange_rate_provider === 'currencylayer' ? 'selected' : '' }}>CurrencyLayer</option>
                                        <option value="openexchange" {{ $currency->exchange_rate_provider === 'openexchange' ? 'selected' : '' }}>OpenExchangeRates</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_default" name="is_default" 
                                           {{ $currency->is_default ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_default">
                                        Set as default currency
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" 
                                           {{ $currency->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="metadata" class="form-label">Metadata (JSON)</label>
                            <textarea class="form-control" id="metadata" name="metadata" rows="3">{{ $currency->metadata ? json_encode($currency->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '' }}</textarea>
                            <small class="text-muted">Additional metadata in JSON format</small>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Currency
                            </button>
                            <a href="{{ route('currency.show', $currency->id) }}" class="btn btn-info">
                                <i class="fas fa-eye"></i> View
                            </a>
                            <a href="{{ route('currency.index') }}" class="btn btn-secondary">
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
