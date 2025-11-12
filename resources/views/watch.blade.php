@extends('layouts.app')

@section('title', $file->title . ' - Watch Video')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="mb-4">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-play-circle"></i> {{ $file->title }}
                    </h4>
                </div>
                <div class="card-body p-0">
                    <!-- Video Player -->
                    <div class="ratio ratio-16x9 bg-black">
                        <video 
                            id="videoPlayer" 
                            controls 
                            controlsList="nodownload"
                            preload="metadata"
                            style="width: 100%; height: 100%;"
                        >
                            <source src="{{ route('video.stream', $file) }}" type="{{ $file->mime_type ?? 'video/mp4' }}">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                </div>
                <div class="card-body">
                    @if($file->description)
                    <div class="mb-3">
                        <h5>Description</h5>
                        <p class="text-muted">{{ $file->description }}</p>
                    </div>
                    @endif

                    <div class="row text-center border-top pt-3">
                        <div class="col-md-4">
                            <small class="text-muted d-block">File Size</small>
                            <strong>{{ number_format($file->file_size / 1048576, 2) }} MB</strong>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Format</small>
                            <strong>{{ strtoupper(pathinfo($file->filename, PATHINFO_EXTENSION)) }}</strong>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Download Option</small>
                            <a href="{{ route('file.download', $file) }}" class="btn btn-sm btn-outline-primary mt-1">
                                <i class="bi bi-download"></i> Download
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Video Controls Info -->
            <div class="alert alert-info mt-3">
                <i class="bi bi-info-circle"></i> 
                <strong>Tips:</strong>
                <ul class="mb-0 mt-2">
                    <li>Use the play/pause button or press <kbd>Space</kbd></li>
                    <li>Adjust volume with the slider or use <kbd>↑</kbd> <kbd>↓</kbd> arrow keys</li>
                    <li>Toggle fullscreen with the button or press <kbd>F</kbd></li>
                    <li>Seek forward/backward using <kbd>←</kbd> <kbd>→</kbd> arrow keys</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Save video progress
const video = document.getElementById('videoPlayer');
const storageKey = 'video_progress_{{ $file->id }}';

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
</script>
@endpush
@endsection
