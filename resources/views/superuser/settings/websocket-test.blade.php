@extends('layouts.sidebar')

@section('title', 'WebSocket Testing')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('superuser.settings.index') }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h2 class="mb-0">
            <i class="bi bi-diagram-3"></i> WebSocket Testing
        </h2>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Reverb Installation Check -->
            @if (!file_exists(config_path('reverb.php')))
            <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                <div class="flex-grow-1">
                    <h5 class="alert-heading mb-1">
                        <i class="bi bi-x-circle"></i> Laravel Reverb Not Installed
                    </h5>
                    <p class="mb-2">
                        The Reverb WebSocket server is not installed on this system. WebSocket functionality will not work until Reverb is installed.
                    </p>
                    <hr>
                    <div class="mb-2">
                        <strong>Installation Steps:</strong>
                    </div>
                    <ol class="small mb-2 ps-3">
                        <li>Run: <code class="bg-dark text-light px-2 py-1 rounded">php artisan reverb:install</code></li>
                        <li>Configure environment variables in <code>.env</code> file</li>
                        <li>Start the server: <code class="bg-dark text-light px-2 py-1 rounded">php artisan reverb:start</code></li>
                        <li>For production: Set up as a systemd service or use Supervisor</li>
                    </ol>
                    <p class="mb-0 small text-muted">
                        <i class="bi bi-info-circle"></i> After installation, restart the web server and refresh this page.
                    </p>
                </div>
            </div>
            @endif

            <!-- Connection Status Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-broadcast"></i> Connection Status
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <div id="statusIndicator" class="status-indicator status-disconnected"></div>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1" id="statusText">Disconnected</h6>
                            <small class="text-muted" id="statusDetail">Not connected to WebSocket server</small>
                        </div>
                        <div>
                            <button 
                                id="connectBtn" 
                                class="btn btn-success"
                                data-app-key="{{ env('REVERB_APP_KEY') }}"
                                data-ws-host="{{ env('REVERB_HOST') }}"
                                data-ws-port="{{ env('REVERB_PORT') }}"
                                data-scheme="{{ env('REVERB_SCHEME') }}"
                            >
                                <i class="bi bi-plug"></i> Connect
                            </button>
                            <button id="disconnectBtn" class="btn btn-danger d-none">
                                <i class="bi bi-x-circle"></i> Disconnect
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Send Test Message Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-chat-dots"></i> Send Test Message
                    </h5>
                </div>
                <div class="card-body">
                    <form id="messageForm" data-broadcast-route="{{ route('superuser.settings.websocket-test.broadcast') }}">
                        <div class="mb-3">
                            <label for="channel" class="form-label">Channel Name</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="channel" 
                                name="channel" 
                                value="test-channel"
                                placeholder="test-channel"
                                required
                            >
                            <div class="form-text">Public channel to broadcast the message</div>
                        </div>

                        <div class="mb-3">
                            <label for="event" class="form-label">Event Name</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="event" 
                                name="event" 
                                value="TestEvent"
                                placeholder="TestEvent"
                                required
                            >
                            <div class="form-text">Event name that listeners will subscribe to</div>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Message Data (JSON)</label>
                            <textarea 
                                class="form-control font-monospace" 
                                id="message" 
                                name="message" 
                                rows="5"
                                required
                            >{
  "text": "Hello from WebSocket!",
  "timestamp": "{{ date('Y-m-d H:i:s') }}",
  "user": "{{ auth()->user()->name }}"
}</textarea>
                            <div class="form-text">JSON data to send with the event</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success" id="sendBtn" disabled>
                                <i class="bi bi-send-fill"></i> Broadcast Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Received Messages Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-inbox"></i> Received Messages
                    </h5>
                    <button id="clearMessages" class="btn btn-sm btn-light">
                        <i class="bi bi-trash"></i> Clear
                    </button>
                </div>
                <div class="card-body">
                    <div id="messagesContainer" class="messages-container">
                        <div class="text-muted text-center py-4">
                            <i class="bi bi-inbox display-4"></i>
                            <p class="mt-2">No messages received yet</p>
                            <small>Connect to the WebSocket server and send a test message</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Configuration Info Card -->
            <div class="card shadow-sm bg-light mb-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle"></i> Reverb Configuration
                    </h6>
                </div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt class="text-muted small">App ID</dt>
                        <dd class="mb-2"><code>{{ env('REVERB_APP_ID') }}</code></dd>

                        <dt class="text-muted small">Host</dt>
                        <dd class="mb-2"><code>{{ env('REVERB_HOST') }}</code></dd>

                        <dt class="text-muted small">Port</dt>
                        <dd class="mb-2"><code>{{ env('REVERB_PORT') }}</code></dd>

                        <dt class="text-muted small">Scheme</dt>
                        <dd class="mb-2"><code>{{ env('REVERB_SCHEME') }}</code></dd>

                        <dt class="text-muted small">Server Status</dt>
                        <dd class="mb-0">
                            <span id="serverStatus" class="badge bg-secondary">Unknown</span>
                        </dd>
                    </dl>
                </div>
            </div>

            <!-- Server Management Card -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-warning bg-opacity-10">
                    <h6 class="mb-0">
                        <i class="bi bi-server"></i> Local Development
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small mb-2">Start Reverb server locally:</p>
                    <pre class="small bg-dark text-light p-2 rounded"><code>php artisan reverb:start</code></pre>
                    
                    <p class="small mb-2 mt-3">Or with debug output:</p>
                    <pre class="small bg-dark text-light p-2 rounded"><code>php artisan reverb:start --debug</code></pre>
                    
                    <p class="small text-muted mb-0 mt-2">Server will run on port {{ env('REVERB_PORT', 8080) }}</p>
                </div>
            </div>

            <!-- Production Setup Card -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-secondary bg-opacity-10">
                    <h6 class="mb-0">
                        <i class="bi bi-cloud"></i> Production Setup
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small mb-2">For production, run as systemd service:</p>
                    <pre class="small bg-dark text-light p-2 rounded"><code>sudo systemctl start reverb
sudo systemctl enable reverb</code></pre>
                    
                    <p class="small text-muted mb-0 mt-2">Requires Supervisor or systemd configuration</p>
                </div>
            </div>

            <!-- Connection Tips Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-success bg-opacity-10">
                    <h6 class="mb-0 text-success">
                        <i class="bi bi-lightbulb"></i> Testing Tips
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="small mb-0 ps-3">
                        <li class="mb-2">Make sure Reverb server is running</li>
                        <li class="mb-2">Open this page in multiple browser tabs to test broadcasting</li>
                        <li class="mb-2">Check browser console for detailed logs</li>
                        <li>Messages are shown in real-time as they arrive</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.status-indicator {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    position: relative;
}

.status-indicator::before {
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

.status-disconnected {
    background-color: #dc3545;
}

.status-connecting {
    background-color: #ffc107;
}

.status-connected {
    background-color: #28a745;
}

.status-connected::before {
    animation: pulse-success 2s infinite;
}

@keyframes pulse-success {
    0% {
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
    }
}

.messages-container {
    max-height: 400px;
    overflow-y: auto;
}

.message-item {
    border-left: 3px solid #0d6efd;
    padding: 0.75rem;
    margin-bottom: 0.5rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
}

[data-bs-theme="dark"] .message-item {
    background-color: #2b3035;
    border-left-color: #0d6efd;
}

.message-item .message-meta {
    font-size: 0.875rem;
    color: #6c757d;
}

.message-item .message-content {
    font-family: 'Courier New', monospace;
    font-size: 0.875rem;
    background-color: #fff;
    padding: 0.5rem;
    border-radius: 0.25rem;
    overflow-x: auto;
}

[data-bs-theme="dark"] .message-item .message-content {
    background-color: #1a1d20;
    color: #e9ecef;
}
</style>

@push('scripts')
<!-- Pusher loaded via Vite bundle -->
@endpush
@endsection
