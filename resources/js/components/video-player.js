/**
 * Video Player with Progress Tracking and Keyboard Shortcuts
 */
(function() {
    const video = document.getElementById('videoPlayer');
    if (!video) return;

    const fileId = video.dataset.fileId;
    const storageKey = `video_progress_${fileId}`;

    // Load saved progress
    const savedTime = localStorage.getItem(storageKey);
    if (savedTime && savedTime > 0) {
        video.currentTime = parseFloat(savedTime);
    }

    // Save progress every 5 seconds
    video.addEventListener('timeupdate', function() {
        if (video.currentTime > 0) {
            localStorage.setItem(storageKey, video.currentTime);
        }
    });

    // Clear progress when video ends
    video.addEventListener('ended', function() {
        localStorage.removeItem(storageKey);
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
