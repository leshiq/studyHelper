@extends('layouts.sidebar')

@section('title', 'Email Testing')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('superuser.settings.index') }}" class="btn btn-outline-secondary me-3">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <h2 class="mb-0">
            <i class="bi bi-envelope-check"></i> Email Testing
        </h2>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-send"></i> Send Test Email
                    </h5>
                </div>
                <div class="card-body">
                    <form id="emailTestForm" data-send-route="{{ route('superuser.settings.email-test.send') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="recipient" class="form-label">Recipient Email Address</label>
                            <input 
                                type="email" 
                                class="form-control" 
                                id="recipient" 
                                name="recipient" 
                                placeholder="test@example.com"
                                required
                            >
                            <div class="form-text">Enter the email address where the test email should be sent</div>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Email Subject</label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="subject" 
                                name="subject" 
                                value="Study Helper - Test Email"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Email Message</label>
                            <textarea 
                                class="form-control" 
                                id="message" 
                                name="message" 
                                rows="5"
                                required
                            >This is a test email from Study Helper application.

If you received this email, it means your email configuration is working correctly.

Timestamp: {{ date('Y-m-d H:i:s') }}</textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send-fill"></i> Send Test Email
                            </button>
                        </div>
                    </form>

                    <div id="result" class="mt-4"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm bg-light">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle"></i> Email Configuration Info
                    </h6>
                </div>
                <div class="card-body">
                    <dl class="mb-0">
                        <dt class="text-muted small">Mail Driver</dt>
                        <dd class="mb-2">
                            <code>{{ config('mail.default') }}</code>
                            @if(config('mail.default') === 'log')
                                <span class="badge bg-warning text-dark ms-1">Dev Mode</span>
                            @endif
                        </dd>

                        @if(config('mail.default') === 'smtp')
                        <dt class="text-muted small">SMTP Host</dt>
                        <dd class="mb-2"><code>{{ config('mail.mailers.smtp.host') ?: 'Not configured' }}</code></dd>

                        <dt class="text-muted small">SMTP Port</dt>
                        <dd class="mb-2"><code>{{ config('mail.mailers.smtp.port') ?: 'Not configured' }}</code></dd>
                        @endif

                        <dt class="text-muted small">From Address</dt>
                        <dd class="mb-2"><code>{{ config('mail.from.address') ?: 'Not configured' }}</code></dd>

                        <dt class="text-muted small">From Name</dt>
                        <dd class="mb-0"><code>{{ config('mail.from.name') ?: 'Not configured' }}</code></dd>
                    </dl>
                </div>
            </div>

            @if(config('mail.default') === 'log')
            <div class="card shadow-sm mt-3 border-warning">
                <div class="card-header bg-warning bg-opacity-10">
                    <h6 class="mb-0 text-warning">
                        <i class="bi bi-exclamation-triangle"></i> Development Mode
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small mb-2">Mail driver is set to <code>log</code>. Emails will be written to:</p>
                    <code class="small">storage/logs/laravel.log</code>
                    <hr class="my-2">
                    <p class="small mb-0 text-muted">To check logs on server, run:</p>
                    <pre class="small bg-dark text-light p-2 rounded mt-1"><code>tail -f storage/logs/laravel.log</code></pre>
                </div>
            </div>
            @endif

            <div class="card shadow-sm mt-3">
                <div class="card-header bg-secondary bg-opacity-10">
                    <h6 class="mb-0">
                        <i class="bi bi-gear"></i> Setup SMTP
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small mb-2">Add to your <code>.env</code> file:</p>
                    <pre class="small bg-dark text-light p-2 rounded"><code>MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"</code></pre>
                    <p class="small text-muted mb-0 mt-2">Remember to run <code>php artisan config:clear</code> after changes!</p>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-header bg-warning bg-opacity-10">
                    <h6 class="mb-0 text-warning">
                        <i class="bi bi-exclamation-triangle"></i> Important Notes
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="small mb-0 ps-3">
                        <li class="mb-2">Check spam/junk folders if emails don't arrive</li>
                        <li class="mb-2">Gmail requires App Passwords (not your regular password)</li>
                        <li>Test emails may take a few moments to deliver</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
@endpush
@endsection
