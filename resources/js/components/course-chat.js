/**
 * Course Chat with WebSocket (Laravel Reverb + Pusher.js)
 */
(function() {
    const chatMessages = document.getElementById('chatMessages');
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    
    if (!chatMessages || !chatForm || !chatInput) return;

    const courseId = chatForm.dataset.courseId;
    const currentUserId = parseInt(chatForm.dataset.userId);
    
    // Pusher setup for Laravel Reverb
    const pusher = new Pusher('local-key', {
        wsHost: window.location.hostname,
        wsPort: 443,
        wssPort: 443,
        forceTLS: true,
        encrypted: true,
        disableStats: true,
        enabledTransports: ['ws', 'wss'],
        cluster: 'mt1' // Required by Pusher client even if not used
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
        // Remove empty state if present
        const emptyState = chatMessages.querySelector('.text-center.text-muted');
        if (emptyState) {
            chatMessages.innerHTML = '';
        }

        const isOwnMessage = data.student.id === currentUserId;
        const messageEl = document.createElement('div');
        messageEl.className = `mb-3 ${isOwnMessage ? 'text-end' : ''}`;
        
        const avatarHtml = data.student.avatar_small 
            ? `<img src="/avatars/small/${data.student.avatar_small}" alt="${escapeHtml(data.student.name)}" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover;">`
            : `<div style="width: 30px; height: 30px; border-radius: 50%; background: var(--bs-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: bold;">${data.student.name.charAt(0).toUpperCase()}</div>`;
        
        messageEl.innerHTML = `
            <div class="d-inline-block ${isOwnMessage ? 'text-end' : ''}" style="max-width: 80%;">
                <div class="d-flex align-items-start gap-2 ${isOwnMessage ? 'flex-row-reverse' : ''}">
                    ${avatarHtml}
                    <div>
                        <div class="small text-muted mb-1">
                            <strong>${escapeHtml(data.student.name)}</strong>
                            <span class="ms-2">${new Date(data.created_at).toLocaleTimeString([], {hour: '2-digit', minute: '2-digit'})}</span>
                        </div>
                        <div class="p-2 rounded ${isOwnMessage ? 'bg-primary text-white' : 'bg-light border'}" style="word-wrap: break-word;">
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

    // Escape HTML to prevent XSS
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
        
        try {
            const response = await fetch(`/courses/${courseId}/chat`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message })
            });
            
            if (response.ok) {
                chatInput.value = '';
                chatInput.focus();
            } else {
                console.error('Failed to send message:', response.status);
                alert('Failed to send message. Please try again.');
            }
        } catch (error) {
            console.error('Error sending message:', error);
            alert('Error sending message. Please try again.');
        }
    });

    // Load messages on page load
    loadMessages();
})();
