<!-- resources/views/api/documentation.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>API Documentation</h1>
    <p>This page provides documentation for the Real Estate Integration & API system.</p>

    <h2>Available Endpoints</h2>
    <ul>
        <li><strong>GET /api/properties</strong> - Get a list of properties.</li>
        <li><strong>GET /api/properties/{id}</strong> - Get details for a specific property.</li>
        <li><strong>POST /api/properties</strong> - Create a new property.</li>
        <li><strong>PUT /api/properties/{id}</strong> - Update a property.</li>
        <li><strong>DELETE /api/properties/{id}</strong> - Delete a property.</li>
        <li><strong>POST /api/webhooks</strong> - Register a new webhook.</li>
        <li><strong>GET /api/keys</strong> - Get your API keys.</li>
        <li><strong>POST /api/keys/generate</strong> - Generate a new API key.</li>
        <li><strong>POST /api/data/sync</strong> - Initiate a data synchronization.</li>
        <li><strong>POST /api/imports</strong> - Import properties from a file.</li>
        <li><strong>POST /api/exports</strong> - Export properties to a file.</li>
    </ul>

    <h2>Authentication</h2>
    <p>All API requests must be authenticated using an API key. Include your API key in the <code>Authorization</code> header as a Bearer token:</p>
    <pre><code>Authorization: Bearer YOUR_API_KEY</code></pre>

    <h2>Webhooks</h2>
    <p>Webhooks allow you to receive real-time notifications about events in the system. To register a webhook, send a POST request to <code>/api/webhooks</code> with the following payload:</p>
    <pre><code>{
    "event": "property.created",
    "url": "https://your-callback-url.com/webhook"
}</code></pre>

    <h2>Error Codes</h2>
    <p>The API will return standard HTTP status codes for responses. Common error codes include:</p>
    <ul>
        <li><strong>401 Unauthorized</strong> - Missing or invalid API key.</li>
        <li><strong>403 Forbidden</strong> - Insufficient permissions to access the resource.</li>
        <li><strong>404 Not Found</strong> - The requested resource does not exist.</li>
        <li><strong>422 Unprocessable Entity</strong> - Validation error in the request payload.</li>
        <li><strong>500 Internal Server Error</strong> - An unexpected error occurred on the server.</li>
    </ul>
</div>
@endsection
