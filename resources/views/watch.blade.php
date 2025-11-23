@extends('layouts.app')

@section('title', $file->title . ' - Watch Video')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="mb-4">
                @if(isset($course))
                    <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Course
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                @endif
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
                            data-file-id="{{ $file->id }}"
                            @if(isset($courseLesson))
                            data-lesson-id="{{ $courseLesson->id }}"
                            @endif
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
@endpush
@endsection
