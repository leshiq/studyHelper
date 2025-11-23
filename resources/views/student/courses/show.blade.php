@extends('layouts.app')

@section('title', $course->title . ' - Study Helper')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-5">
                <i class="bi bi-book-fill"></i> {{ $course->title }}
            </h1>
            <p class="text-muted">
                <i class="bi bi-person"></i> Instructor: {{ $course->teacher->name }}
            </p>
        </div>
        <div class="col-auto">
            <a href="{{ route('courses.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to Courses
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- Course Description -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-info-circle"></i> About This Course</h5>
                    @if($course->description)
                        <p class="card-text">{{ $course->description }}</p>
                    @else
                        <p class="text-muted">No description provided</p>
                    @endif
                </div>
            </div>

            <!-- Course Lessons -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-journal-text"></i> Course Lessons</h5>
                </div>
                <div class="card-body">
                    @if($course->lessons->isEmpty())
                        <p class="text-muted">No lessons available yet.</p>
                    @else
                        <div class="list-group">
                            @foreach($course->lessons->sortBy('order') as $lesson)
                            <div class="list-group-item">
                                <div class="d-flex w-100 align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <span class="badge bg-primary me-2">{{ $lesson->order }}</span>
                                            {{ $lesson->title }}
                                        </h6>
                                        @if($lesson->description)
                                            <p class="mb-2 small">{{ $lesson->description }}</p>
                                        @endif
                                        @if($lesson->file)
                                            <div class="mt-2">
                                                @if($enrollment)
                                                    @if(Str::startsWith($lesson->file->mime_type, 'video/'))
                                                        <a href="{{ route('video.watch', $lesson->file) }}" class="btn btn-sm btn-primary me-2">
                                                            <i class="bi bi-play-circle"></i> Watch Video
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('file.download', $lesson->file) }}" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-download"></i> Download
                                                    </a>
                                                    <small class="text-muted ms-2">
                                                        {{ number_format($lesson->file->file_size / 1048576, 2) }} MB
                                                    </small>
                                                @else
                                                    <div class="alert alert-warning py-2 mb-0">
                                                        <small><i class="bi bi-lock"></i> Enroll to access course materials</small>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Enrollment Status -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-person-check"></i> Enrollment Status</h5>
                    @if($enrollment)
                        <div class="alert alert-success mb-0">
                            <i class="bi bi-check-circle"></i> You are enrolled in this course!
                            <hr>
                            <small class="text-muted">
                                Enrolled: {{ $enrollment->approved_at->format('M d, Y') }}
                            </small>
                        </div>
                    @else
                        <p class="text-muted">You are not currently enrolled in this course.</p>
                        @if($course->is_available_to_all)
                            <form action="{{ route('courses.request', $course) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-plus-circle"></i> Request Enrollment
                                </button>
                            </form>
                        @else
                            <div class="alert alert-info mb-0">
                                <small><i class="bi bi-lock"></i> This is a private course. Contact the instructor for enrollment.</small>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Course Info -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-list-ul"></i> Course Details</h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-journal-text text-primary"></i>
                            <strong>{{ $course->lessons->count() }}</strong> lesson{{ $course->lessons->count() != 1 ? 's' : '' }}
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-person text-primary"></i>
                            Instructor: <strong>{{ $course->teacher->name }}</strong>
                        </li>
                        @if($course->is_available_to_all)
                        <li class="mb-2">
                            <i class="bi bi-globe text-primary"></i>
                            <span class="badge bg-info">Public Course</span>
                        </li>
                        @else
                        <li class="mb-2">
                            <i class="bi bi-lock text-primary"></i>
                            <span class="badge bg-warning">Private Course</span>
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            @if($enrollment)
            <!-- Course Chat -->
            <div class="card shadow-sm mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-chat-dots"></i> Course Discussion</h5>
                </div>
                <div class="card-body p-0">
                    <div id="chatMessages" class="p-3" style="height: 400px; overflow-y: auto; background-color: var(--bs-light);">
                        <div class="text-center text-muted">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Loading messages...</p>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <form id="chatForm" class="d-flex gap-2">
                        @csrf
                        <input type="text" id="chatInput" class="form-control" placeholder="Type your message..." maxlength="1000" required>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@if($enrollment)
@push('scripts')
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script>
    const courseId = {{ $course->id }};
    const currentUserId = {{ auth()->id() }};
    const chatMessages = document.getElementById('chatMessages');
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');

    // Pusher setup
    const pusher = new Pusher('studyhelper-key', {
        wsHost: 'studyhelper.iforlive.com',
        wsPort: 443,
        wssPort: 443,
        forceTLS: true,
        encrypted: true,
        disableStats: true,
        enabledTransports: ['ws', 'wss'],
        cluster: 'mt1'
    });

    // Connection event listeners
    pusher.connection.bind('connected', function() {
        console.log('WebSocket connected!');
    });

    pusher.connection.bind('error', function(err) {
        console.error('WebSocket connection error:', err);
    });

    const channel = pusher.subscribe('course.' + courseId);

    channel.bind('pusher:subscription_succeeded', function() {
        console.log('Subscribed to course.' + courseId);
    });

    channel.bind('pusher:subscription_error', function(status) {
        console.error('Subscription error:', status);
    });

    // Listen for new messages
    channel.bind('chat.message', function(data) {
        console.log('Received message:', data);
        appendMessage(data);
    });

    // Load existing messages
    async function loadMessages() {
        try {
            const response = await fetch(`/courses/${courseId}/chat`);
            const messages = await response.json();
            
            chatMessages.innerHTML = '';
            
            if (messages.length === 0) {
                chatMessages.innerHTML = '<div class="text-center text-muted"><p>No messages yet. Start the conversation!</p></div>';
            } else {
                messages.forEach(message => {
                    appendMessage(message, false);
                });
                scrollToBottom();
            }
        } catch (error) {
            console.error('Error loading messages:', error);
            chatMessages.innerHTML = '<div class="text-center text-danger"><p>Error loading messages</p></div>';
        }
    }

    // Append message to chat
    function appendMessage(data, scroll = true) {
        const isOwnMessage = data.student.id === currentUserId;
        const messageEl = document.createElement('div');
        messageEl.className = `mb-3 ${isOwnMessage ? 'text-end' : ''}`;
        
        const avatarHtml = data.student.avatar_small 
            ? `<img src="/avatars/small/${data.student.avatar_small}" alt="${data.student.name}" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">`
            : `<div style="width: 30px; height: 30px; border-radius: 50%; background: var(--bs-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">${data.student.name.charAt(0).toUpperCase()}</div>`;
        
        messageEl.innerHTML = `
            <div class="d-inline-block ${isOwnMessage ? 'text-end' : ''}" style="max-width: 80%;">
                <div class="d-flex align-items-start gap-2 ${isOwnMessage ? 'flex-row-reverse' : ''}">
                    ${avatarHtml}
                    <div>
                        <div class="small text-muted mb-1">
                            <strong>${data.student.name}</strong>
                            <span class="ms-2">${new Date(data.created_at).toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'})}</span>
                        </div>
                        <div class="p-2 rounded ${isOwnMessage ? 'bg-primary text-white' : 'bg-white'}" style="word-wrap: break-word;">
                            ${escapeHtml(data.message)}
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        chatMessages.appendChild(messageEl);
        
        if (scroll) {
            scrollToBottom();
        }
    }

    // Scroll to bottom
    function scrollToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Escape HTML
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Send message
    chatForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const message = chatInput.value.trim();
        if (!message) return;
        
        console.log('Sending message:', message);
        
        try {
            const response = await fetch(`/courses/${courseId}/chat`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ message })
            });
            
            console.log('Response status:', response.status);
            const data = await response.json();
            console.log('Response data:', data);
            
            if (response.ok) {
                chatInput.value = '';
                console.log('Message sent successfully');
            } else {
                console.error('Error response:', data);
                alert('Error sending message');
            }
        } catch (error) {
            console.error('Error sending message:', error);
            alert('Error sending message');
        }
    });

    // Load messages on page load
    loadMessages();
</script>
@endpush
@endif
@endsection
