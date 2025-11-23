/**
 * Video Player with Progress Tracking and Keyboard Shortcuts
 */
(function() {
    const video = document.getElementById('videoPlayer');
    if (!video) return;

    const fileId = video.dataset.fileId;
    const lessonId = video.dataset.lessonId; // Course lesson ID for server tracking
    const storageKey = `video_progress_${fileId}`;
    
    let lastSaveTime = 0;
    let hasMetadata = false;

    // Load saved progress from localStorage
    const savedTime = localStorage.getItem(storageKey);
    if (savedTime && savedTime > 0) {
        video.currentTime = parseFloat(savedTime);
    }

    // Wait for video metadata to load
    video.addEventListener('loadedmetadata', function() {
        hasMetadata = true;
        // Send initial progress update with video duration
        if (lessonId) {
            saveProgressToServer(video.currentTime, video.duration);
        }
    });

    /**
     * Save progress to server via API
     */
    function saveProgressToServer(currentTime, duration) {
        if (!lessonId) return;

        fetch('/api/lesson-progress', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({
                lesson_id: lessonId,
                watch_time_seconds: Math.floor(currentTime),
                video_duration_seconds: Math.floor(duration),
            })
        }).catch(error => {
            console.error('Failed to save progress:', error);
        });
    }

    // Save progress locally and to server
    video.addEventListener('timeupdate', function() {
        if (video.currentTime > 0) {
            // Save to localStorage
            localStorage.setItem(storageKey, video.currentTime);
            
            // Save to server every 10 seconds
            if (hasMetadata && lessonId && (video.currentTime - lastSaveTime >= 10)) {
                saveProgressToServer(video.currentTime, video.duration);
                lastSaveTime = video.currentTime;
            }
        }
    });

    // Save progress when paused
    video.addEventListener('pause', function() {
        if (hasMetadata && lessonId && video.currentTime > 0) {
            saveProgressToServer(video.currentTime, video.duration);
            lastSaveTime = video.currentTime;
        }
    });

    // Clear local progress and mark as completed when video ends
    video.addEventListener('ended', function() {
        localStorage.removeItem(storageKey);
        
        if (hasMetadata && lessonId) {
            // Send final update marking as completed
            saveProgressToServer(video.duration, video.duration);
        }
    });

    // Save progress when user leaves the page
    window.addEventListener('beforeunload', function() {
        if (hasMetadata && lessonId && video.currentTime > 0) {
            // Use synchronous request for page unload
            navigator.sendBeacon('/api/lesson-progress', new Blob([JSON.stringify({
                lesson_id: lessonId,
                watch_time_seconds: Math.floor(video.currentTime),
                video_duration_seconds: Math.floor(video.duration),
            })], { type: 'application/json' }));
        }
    });

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.target.tagName.toLowerCase() !== 'input' && e.target.tagName.toLowerCase() !== 'textarea') {
            switch(e.key) {
                case ' ':
                    e.preventDefault();
                    video.paused ? video.play() : video.pause();
                    break;
                case 'f':
                case 'F':
                    if (video.requestFullscreen) {
                        video.requestFullscreen();
                    }
                    break;
                case 'ArrowRight':
                    video.currentTime += 10;
                    break;
                case 'ArrowLeft':
                    video.currentTime -= 10;
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    video.volume = Math.min(video.volume + 0.1, 1);
                    break;
                case 'ArrowDown':
                    e.preventDefault();
                    video.volume = Math.max(video.volume - 0.1, 0);
                    break;
            }
        }
    });
})();
