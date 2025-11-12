@extends('layouts.app')

@section('title', 'Dashboard - Study Helper')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1 class="display-5">
                <i class="bi bi-speedometer2"></i> My Dashboard
            </h1>
            <p class="text-muted">Welcome back, {{ Auth::user()->name }}!</p>
        </div>
    </div>

    @if($files->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> No files are currently available for you. Please check back later.
    </div>
    @else
    <div class="row">
        <div class="col-12">
            <h3 class="mb-3">Available Lesson Videos</h3>
        </div>
    </div>

    <div class="row g-4">
        @foreach($files as $file)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="bi bi-camera-video text-primary"></i> {{ $file->title }}
                    </h5>
                    
                    @if($file->description)
                    <p class="card-text text-muted">{{ Str::limit($file->description, 100) }}</p>
                    @endif

                    <div class="mt-3">
                        <div class="d-flex justify-content-between text-muted small mb-2">
                            <span>
                                <i class="bi bi-hdd"></i> 
                                {{ number_format($file->file_size / 1048576, 2) }} MB
                            </span>
                            @if($file->max_downloads)
                            <span>
                                <i class="bi bi-download"></i> 
                                {{ $file->download_logs_count }} / {{ $file->max_downloads }}
                            </span>
                            @else
                            <span>
                                <i class="bi bi-download"></i> 
                                {{ $file->download_logs_count }} downloads
                            </span>
                            @endif
                        </div>

                        @php
                            $access = $file->pivot;
                            $canDownload = true;
                            $message = '';
                            
                            if ($file->max_downloads && $file->download_logs_count >= $file->max_downloads) {
                                $canDownload = false;
                                $message = 'Download limit reached';
                            } elseif ($access->expires_at && $access->expires_at < now()) {
                                $canDownload = false;
                                $message = 'Access expired';
                            } elseif ($access->expires_at) {
                                $message = 'Expires: ' . $access->expires_at->format('M d, Y');
                            }
                        @endphp

                        @if($canDownload)
                        <a href="{{ route('file.download', $file) }}" class="btn btn-primary w-100">
                            <i class="bi bi-download"></i> Download Video
                        </a>
                        @if($message)
                        <small class="text-muted d-block mt-2 text-center">{{ $message }}</small>
                        @endif
                        @else
                        <button class="btn btn-secondary w-100" disabled>
                            <i class="bi bi-x-circle"></i> {{ $message }}
                        </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
