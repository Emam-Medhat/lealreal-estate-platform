<!-- resources/views/api/logs.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Integration Logs</h1>
    <p>View logs for all external integrations and API activities.</p>

    <div class="card">
        <div class="card-header">Recent Logs</div>
        <div class="card-body">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <table class="table">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>Integration</th>
                        <th>Level</th>
                        <th>Message</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Loop through logs here --}}
                    <tr>
                        <td>2026-01-01 11:30:00</td>
                        <td>MLS System A</td>
                        <td>INFO</td>
                        <td>Property #123 synced successfully.</td>
                        <td><button class="btn btn-sm btn-info">View Payload</button></td>
                    </tr>
                    <tr>
                        <td>2026-01-01 11:25:00</td>
                        <td>Zillow API</td>
                        <td>ERROR</td>
                        <td>Failed to update property #456.</td>
                        <td><button class="btn btn-sm btn-info">View Payload</button></td>
                    </tr>
                </tbody>
            </table>

            {{-- Pagination links --}}
            <nav>
                <ul class="pagination">
                    <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">Next</a></li>
                </ul>
            </nav>
        </div>
    </div>
</div>
@endsection
