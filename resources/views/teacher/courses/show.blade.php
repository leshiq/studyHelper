@extends('layouts.app')

@section('title', $course->title . ' - Study Helper')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-5">
                <i class="bi bi-book-fill"></i> {{ $course->title }}
            </h1>
            <p class="text-muted">Course Management</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('teacher.courses.edit', $course) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil"></i> Edit Course
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-info-circle"></i> Course Information</h5>
                    @if($course->description)
                        <p class="card-text">{{ $course->description }}</p>
                    @else
                        <p class="text-muted">No description provided</p>
                    @endif
                    
                    <div class="mt-3">
                        @if($course->is_active)
                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Active</span>
                        @else
                            <span class="badge bg-secondary"><i class="bi bi-pause-circle"></i> Inactive</span>
                        @endif
                        
                        @if($course->is_available_to_all)
                            <span class="badge bg-info"><i class="bi bi-globe"></i> Public</span>
                        @else
                            <span class="badge bg-warning"><i class="bi bi-lock"></i> Private</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Lessons Section -->
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-journal-text"></i> Course Lessons</h5>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addLessonModal">
                        <i class="bi bi-plus"></i> Add Lesson
                    </button>
                </div>
                <div class="card-body">
                    @if($course->lessons->isEmpty())
                        <p class="text-muted">No lessons added yet. Click "Add Lesson" to create your first lesson.</p>
                    @else
                        <div class="list-group">
                            @foreach($course->lessons->sortBy('order') as $lesson)
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <span class="badge bg-secondary me-2">{{ $lesson->order }}</span>
                                            {{ $lesson->title }}
                                            @if($lesson->is_published)
                                                <span class="badge bg-success ms-2"><i class="bi bi-eye"></i> Published</span>
                                            @else
                                                <span class="badge bg-warning ms-2"><i class="bi bi-eye-slash"></i> Draft</span>
                                            @endif
                                        </h6>
                                        @if($lesson->description)
                                            <p class="mb-1 small text-muted">{{ $lesson->description }}</p>
                                        @endif
                                        @if($lesson->file)
                                            <small class="text-muted">
                                                <i class="bi bi-file-earmark"></i> {{ $lesson->file->title }}
                                            </small>
                                        @endif
                                    </div>
                                    <div class="btn-group ms-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editLessonModal{{ $lesson->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('teacher.courses.lessons.destroy', [$course, $lesson]) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this lesson?')" 
                                              class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Edit Lesson Modal -->
                            <div class="modal fade" id="editLessonModal{{ $lesson->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('teacher.courses.lessons.update', [$course, $lesson]) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Lesson</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Lesson Title <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" name="title" value="{{ $lesson->title }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Description</label>
                                                    <textarea class="form-control" name="description" rows="3">{{ $lesson->description }}</textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Attached File</label>
                                                    <select class="form-select" name="downloadable_file_id">
                                                        <option value="">No file</option>
                                                        @foreach($availableFiles as $file)
                                                            <option value="{{ $file->id }}" {{ $lesson->downloadable_file_id == $file->id ? 'selected' : '' }}>
                                                                {{ $file->title }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Order</label>
                                                    <input type="number" class="form-control" name="order" value="{{ $lesson->order }}" min="0">
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_published" value="1" {{ $lesson->is_published ? 'checked' : '' }}>
                                                    <label class="form-check-label">Published</label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
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
            <!-- Pending Enrollments -->
            @if($pendingEnrollments->isNotEmpty())
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="bi bi-clock"></i> Pending Enrollment Requests</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($pendingEnrollments as $enrollment)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong>{{ $enrollment->student->name }}</strong>
                            </div>
                            <small class="text-muted d-block mb-2">
                                <i class="bi bi-envelope"></i> {{ $enrollment->student->email }}
                            </small>
                            <small class="text-muted d-block mb-2">
                                Requested: {{ $enrollment->created_at->diffForHumans() }}
                            </small>
                            <div class="btn-group btn-group-sm w-100">
                                <form action="{{ route('teacher.courses.enrollments.approve', [$course, $enrollment]) }}" method="POST" class="flex-fill">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-check"></i> Approve
                                    </button>
                                </form>
                                <form action="{{ route('teacher.courses.enrollments.reject', [$course, $enrollment]) }}" method="POST" class="flex-fill">
                                    @csrf
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="bi bi-x"></i> Reject
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Enrolled Students -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-people"></i> Enrolled Students ({{ $approvedStudents->count() }})</h5>
                </div>
                <div class="card-body">
                    @if($approvedStudents->isEmpty())
                        <p class="text-muted mb-0">No students enrolled yet.</p>
                    @else
                        <div class="list-group list-group-flush">
                            @foreach($approvedStudents as $student)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>{{ $student->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $student->email }}</small>
                                    </div>
                                    <form action="{{ route('teacher.courses.students.remove', [$course, $student]) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Remove this student from the course?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

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
        </div>
    </div>
</div>

<!-- Add Lesson Modal -->
<div class="modal fade" id="addLessonModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('teacher.courses.lessons.store', $course) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add New Lesson</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Lesson Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Attach File</label>
                        <select class="form-select" name="downloadable_file_id">
                            <option value="">No file</option>
                            @foreach($availableFiles as $file)
                                <option value="{{ $file->id }}">{{ $file->title }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Select a video or document to attach to this lesson</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Order</label>
                        <input type="number" class="form-control" name="order" min="0" 
                               value="{{ $course->lessons->max('order') + 1 }}">
                        <div class="form-text">Lessons are displayed in order from lowest to highest</div>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_published" value="1" checked>
                        <label class="form-check-label">Publish lesson immediately</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Lesson</button>
                </div>
            </form>
        </div>
    </div>
</div>

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
@endsection
