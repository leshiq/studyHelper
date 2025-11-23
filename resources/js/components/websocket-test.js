/**
 * WebSocket Testing Interface
 */
(function() {
    let pusher = null;
    let channel = null;

    const statusIndicator = document.getElementById('statusIndicator');
    const statusText = document.getElementById('statusText');
    const statusDetail = document.getElementById('statusDetail');
    const connectBtn = document.getElementById('connectBtn');
    const disconnectBtn = document.getElementById('disconnectBtn');
    const sendBtn = document.getElementById('sendBtn');
    const messagesContainer = document.getElementById('messagesContainer');
    const serverStatus = document.getElementById('serverStatus');
    const messageForm = document.getElementById('messageForm');
    
    if (!statusIndicator || !connectBtn) return;

    // Get config from data attributes
    const config = {
        appKey: connectBtn.dataset.appKey,
        wsHost: connectBtn.dataset.wsHost,
        wsPort: parseInt(connectBtn.dataset.wsPort),
        scheme: connectBtn.dataset.scheme,
        broadcastRoute: messageForm ? messageForm.dataset.broadcastRoute : null,
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.content
    };

    function updateStatus(status, text, detail) {
        statusIndicator.className = `status-indicator status-${status}`;
        statusText.textContent = text;
        statusDetail.textContent = detail;
        
        if (status === 'connected') {
            connectBtn.classList.add('d-none');
            disconnectBtn.classList.remove('d-none');
            sendBtn.disabled = false;
            serverStatus.className = 'badge bg-success';
            serverStatus.textContent = 'Running';
        } else if (status === 'connecting') {
            connectBtn.disabled = true;
            sendBtn.disabled = true;
            serverStatus.className = 'badge bg-warning';
            serverStatus.textContent = 'Connecting...';
        } else {
            connectBtn.classList.remove('d-none');
            connectBtn.disabled = false;
            disconnectBtn.classList.add('d-none');
            sendBtn.disabled = true;
            serverStatus.className = 'badge bg-danger';
            serverStatus.textContent = 'Not Running';
        }
    }

    function addMessage(event, channelName, data) {
        // Clear empty state if present
        if (messagesContainer.querySelector('.text-muted')) {
            messagesContainer.innerHTML = '';
        }
        
        const messageEl = document.createElement('div');
        messageEl.className = 'message-item';
        messageEl.innerHTML = `
            <div class="d-flex justify-content-between align-items-start mb-2">
                <strong class="text-primary">${escapeHtml(event)}</strong>
                <small class="message-meta">${new Date().toLocaleTimeString()}</small>
            </div>
            <div class="message-meta mb-1">
                <i class="bi bi-broadcast"></i> Channel: <code>${escapeHtml(channelName)}</code>
            </div>
            <div class="message-content">
                <pre class="mb-0">${JSON.stringify(data, null, 2)}</pre>
            </div>
        `;
        
        messagesContainer.insertBefore(messageEl, messagesContainer.firstChild);
        
        // Keep only last 20 messages
        while (messagesContainer.children.length > 20) {
            messagesContainer.removeChild(messagesContainer.lastChild);
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function connect() {
        updateStatus('connecting', 'Connecting...', 'Attempting to connect to Reverb server');
        
        console.log('Connecting to Reverb with config:', {
            appKey: config.appKey,
            host: config.wsHost,
            port: config.wsPort,
            scheme: config.scheme
        });
        
        pusher = new Pusher(config.appKey, {
            wsHost: config.wsHost,
            wsPort: config.wsPort,
            wssPort: config.wsPort,
            forceTLS: config.scheme === 'https',
            encrypted: config.scheme === 'https',
            disableStats: true,
            enabledTransports: ['ws', 'wss'],
            cluster: 'mt1'
        });
        
        pusher.connection.bind('connecting', () => {
            console.log('Connection state: connecting');
            updateStatus('connecting', 'Connecting...', 'Establishing WebSocket connection');
        });
        
        pusher.connection.bind('connected', () => {
            console.log('Connection state: connected');
            updateStatus('connected', 'Connected', 'Successfully connected to Reverb server');
            
            // Subscribe to test channel
            const channelName = document.getElementById('channel').value || 'test-channel';
            channel = pusher.subscribe(channelName);
            
            channel.bind_global((event, data) => {
                console.log('Received event:', event, data);
                addMessage(event, channelName, data);
            });
        });
        
        pusher.connection.bind('disconnected', () => {
            console.log('Connection state: disconnected');
            updateStatus('disconnected', 'Disconnected', 'Connection to Reverb server lost');
        });
        
        pusher.connection.bind('error', (err) => {
            console.error('Connection error:', err);
            updateStatus('disconnected', 'Connection Error', err.message || 'Failed to connect to Reverb server');
        });
    }

    function disconnect() {
        if (pusher) {
            pusher.disconnect();
            pusher = null;
            channel = null;
        }
        updateStatus('disconnected', 'Disconnected', 'Manually disconnected from server');
    }

    connectBtn.addEventListener('click', connect);
    disconnectBtn.addEventListener('click', disconnect);

    document.getElementById('clearMessages').addEventListener('click', () => {
        messagesContainer.innerHTML = `
            <div class="text-muted text-center py-4">
                <i class="bi bi-inbox display-4"></i>
                <p class="mt-2">No messages received yet</p>
                <small>Connect to the WebSocket server and send a test message</small>
            </div>
        `;
    });

    if (messageForm) {
        messageForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const channelInput = document.getElementById('channel').value;
            const eventInput = document.getElementById('event').value;
            const messageInput = document.getElementById('message').value;
            
            try {
                const messageData = JSON.parse(messageInput);
                
                const response = await fetch(config.broadcastRoute, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': config.csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        channel: channelInput,
                        event: eventInput,
                        data: messageData
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Show success feedback
                    const alert = document.createElement('div');
                    alert.className = 'alert alert-success alert-dismissible fade show mt-3';
                    alert.innerHTML = `
                        <i class="bi bi-check-circle"></i> Message broadcasted successfully!
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    messageForm.appendChild(alert);
                    setTimeout(() => alert.remove(), 3000);
                }
            } catch (error) {
                console.error('Error sending message:', error);
                alert('Error: ' + error.message);
            }
        });
    }
})();
