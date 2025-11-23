@extends('layouts.app')

@section('title', $lesson->title . ' - Student Progress')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('teacher.courses.index') }}">My Courses</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('teacher.courses.show', $course) }}">{{ $course->title }}</a></li>
                    <li class="breadcrumb-item active">{{ $lesson->title }} - Progress</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h4 class="mb-1">{{ $lesson->title }}</h4>
                    <p class="text-muted mb-0">Student Progress Tracking</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-people"></i> Student Progress ({{ $students->count() }} students)</h5>
                    <a href="{{ route('teacher.courses.show', $course) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Course
                    </a>
                </div>
                <div class="card-body p-0">
                    @if($students->isEmpty())
                        <div class="p-4 text-center text-muted">
                            <i class="bi bi-people" style="font-size: 3rem;"></i>
                            <p class="mt-2">No students enrolled in this course yet.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student</th>
                                        <th>Email</th>
                                        <th>Progress</th>
                                        <th>Watch Time</th>
                                        <th>Status</th>
                                        <th>Last Watched</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($students as $student)
                                        @php
                                            $progress = $student->lessonProgress->first();
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($student->avatar_small)
                                                        <img src="{{ asset('avatars/small/' . $student->avatar_small) }}" 
                                                             alt="{{ $student->name }}" 
                                                             class="rounded-circle me-2" 
                                                             style="width: 32px; height: 32px; object-fit: cover;">
                                                    @else
                                                        <div class="rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                                             style="width: 32px; height: 32px; background: var(--bs-primary); color: white; font-weight: bold; font-size: 14px;">
                                                            {{ substr($student->name, 0, 1) }}
                                                        </div>
                                                    @endif
                                                    <strong>{{ $student->name }}</strong>
                                                </div>
                                            </td>
                                            <td class="text-muted">{{ $student->email }}</td>
                                            <td>
                                                @if($progress)
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress flex-grow-1 me-2" style="height: 8px; width: 100px;">
                                                            <div class="progress-bar {{ $progress->is_completed ? 'bg-success' : 'bg-primary' }}" 
                                                                 style="width: {{ $progress->progress_percentage }}%"></div>
                                                        </div>
                                                        <small class="text-muted">{{ round($progress->progress_percentage) }}%</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Not started</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($progress)
                                                    <span class="text-muted">{{ $progress->formatted_watch_time }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($progress && $progress->is_completed)
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle"></i> Completed
                                                    </span>
                                                @elseif($progress)
                                                    <span class="badge bg-primary">
                                                        <i class="bi bi-play-circle"></i> In Progress
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="bi bi-dash-circle"></i> Not Started
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-muted">
                                                @if($progress && $progress->last_watched_at)
                                                    {{ $progress->last_watched_at->diffForHumans() }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
