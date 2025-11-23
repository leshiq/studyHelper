@extends('layouts.app')

@section('title', 'Course Management')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2><i class="bi bi-mortarboard"></i> Course Management</h2>
            <p class="text-muted">View and manage all courses across all teachers</p>
        </div>
    </div>

    @if($courses->isEmpty())
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> No courses have been created yet.
        </div>
    @else
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Course</th>
                                <th>Teacher</th>
                                <th>Lessons</th>
                                <th>Students</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($courses as $course)
                                <tr>
                                    <td>
                                        <div>
                                            <strong>{{ $course->title }}</strong>
                                            @if($course->description)
                                                <br><small class="text-muted">{{ Str::limit($course->description, 60) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <i class="bi bi-person"></i> {{ $course->teacher->name }}
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $course->lessons_count }} lessons</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $course->approved_students_count }} students</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge {{ $course->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $course->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                            @if($course->is_available_to_all)
                                                <span class="badge bg-warning">Public</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $course->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group me-2">
                                            <a href="{{ route('admin.courses.show', $course) }}" 
                                               class="btn btn-sm btn-outline-primary me-2" 
                                               title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            
                                            <form action="{{ route('admin.courses.toggle-active', $course) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('{{ $course->is_active ? 'Deactivate' : 'Activate' }} this course?')">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-{{ $course->is_active ? 'warning' : 'success' }} me-2" 
                                                        title="{{ $course->is_active ? 'Deactivate' : 'Activate' }}">
                                                    <i class="bi bi-{{ $course->is_active ? 'pause-circle' : 'play-circle' }}"></i>
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('admin.courses.destroy', $course) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('PERMANENTLY DELETE this course?\n\nThis will delete:\n- All {{ $course->lessons_count }} lessons\n- All quizzes and questions\n- All student quiz attempts\n- All enrollments\n\nThis action CANNOT be undone!')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        title="Permanently Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
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
