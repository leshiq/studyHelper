import './bootstrap';

// ============================================================================
// THIRD-PARTY LIBRARIES
// ============================================================================
import 'bootstrap';  // Bootstrap 5 JavaScript (modals, dropdowns, etc.)
import Pusher from 'pusher-js';  // WebSocket library for real-time features

// Make Pusher available globally for our components
window.Pusher = Pusher;

// ============================================================================
// APPLICATION COMPONENTS
// ============================================================================
import './components/video-player.js';
import './components/course-chat.js';
import './components/invitations.js';
import './components/email-test.js';
import './components/appearance-settings.js';
import './components/websocket-test.js';
