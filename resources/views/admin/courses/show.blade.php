@extends('layouts.app')

@section('title', $course->title . ' - Course Details')

@section('content')
<div class="container-fluid py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">Courses</a></li>
            <li class="breadcrumb-item active">{{ $course->title }}</li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h2>{{ $course->title }}</h2>
                    <p class="text-muted mb-2">
                        <i class="bi bi-person"></i> Teacher: {{ $course->teacher->name }}
                    </p>
                    <div class="d-flex gap-2 mb-3">
                        <span class="badge {{ $course->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $course->is_active ? 'Active' : 'Inactive' }}
                        </span>
                        @if($course->is_available_to_all)
                            <span class="badge bg-warning">Public Course</span>
                        @endif
                        <span class="badge bg-info">{{ $course->lessons->count() }} Lessons</span>
                        <span class="badge bg-primary">{{ $course->approvedStudents->count() }} Students</span>
                    </div>
                </div>
                <a href="{{ route('admin.courses.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Courses
                </a>
            </div>
        </div>
    </div>

    @if($course->description)
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <strong>Course Description</strong>
            </div>
            <div class="card-body">
                <p class="mb-0">{{ $course->description }}</p>
            </div>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header">
            <strong><i class="bi bi-book"></i> Course Lessons ({{ $course->lessons->count() }})</strong>
        </div>
        <div class="card-body">
            @if($course->lessons->isEmpty())
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle"></i> No lessons have been added to this course yet.
                </div>
            @else
                <div class="list-group">
                    @foreach($course->lessons as $lesson)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <h5 class="mb-0">{{ $lesson->order }}. {{ $lesson->title }}</h5>
                                        <span class="badge {{ $lesson->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $lesson->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                    
                                    @if($lesson->description)
                                        <p class="text-muted mb-2">{{ Str::limit($lesson->description, 150) }}</p>
                                    @endif

                                    <div class="d-flex gap-3 text-muted small">
                                        @if($lesson->file)
                                            <span>
                                                <i class="bi bi-file-earmark"></i> 
                                                {{ $lesson->file->filename }} ({{ number_format($lesson->file->size / 1024, 2) }} KB)
                                            </span>
                                        @endif
                                        
                                        @php
                                            $quizCount = $lesson->quizzes->count();
                                            $totalAttempts = $lesson->quizzes->sum(fn($q) => $q->attempts->count());
                                        @endphp
                                        
                                        @if($quizCount > 0)
                                            <span>
                                                <i class="bi bi-clipboard-check"></i> 
                                                {{ $quizCount }} {{ Str::plural('Quiz', $quizCount) }}
                                            </span>
                                        @endif
                                        
                                        @if($totalAttempts > 0)
                                            <span>
                                                <i class="bi bi-graph-up"></i> 
                                                {{ $totalAttempts }} {{ Str::plural('Attempt', $totalAttempts) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <a href="{{ route('admin.courses.lessons.show', [$course, $lesson]) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View Details
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @if($course->approvedStudents->isNotEmpty())
        <div class="card mt-4 shadow-sm">
            <div class="card-header">
                <strong><i class="bi bi-people"></i> Enrolled Students ({{ $course->approvedStudents->count() }})</strong>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Email</th>
                                <th>Enrolled</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($course->approvedStudents as $student)
                                <tr>
                                    <td>{{ $student->name }}</td>
                                    <td>{{ $student->email }}</td>
                                    <td class="text-muted small">
                                        {{ $student->pivot->created_at->format('M d, Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
