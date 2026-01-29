@extends('layouts.app')

@section('title', 'Stock Movements')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-exchange-alt me-2"></i>
                            Stock Movements
                        </h5>
                        <a href="{{ route('inventory.movements.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i>
                            Add Movement
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Item</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>Reason</th>
                                    <th>User</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($movements as $movement)
                                    <tr>
                                        <td>{{ $movement->created_at->format('Y-m-d H:i') }}</td>
                                        <td>{{ $movement->item_name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $movement->type == 'in' ? 'success' : ($movement->type == 'out' ? 'danger' : 'warning') }}">
                                                {{ strtoupper($movement->type) }}
                                            </span>
                                        </td>
                                        <td>{{ $movement->quantity }}</td>
                                        <td>{{ $movement->reason }}</td>
                                        <td>{{ $movement->user_id ?? 'System' }}</td>
                                        <td>
                                            <a href="{{ route('inventory.movements.show', $movement->id) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No movements found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
