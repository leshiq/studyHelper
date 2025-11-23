<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentManagementController extends Controller
{
    public function index()
    {
        $students = Student::withCount('downloadableFiles', 'downloadLogs')->latest()->get();
        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        return view('admin.students.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email',
            'password' => 'required|string|min:8|confirmed',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $student = Student::create($validated);

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'Student created successfully.');
    }

    public function show(Student $student)
    {
        $student->load(['downloadableFiles', 'downloadLogs' => function($query) {
            $query->latest()->limit(50);
        }]);

        // Get available files (not yet assigned to this student)
        $availableFiles = \App\Models\DownloadableFile::where('is_active', true)
            ->whereNotIn('id', $student->downloadableFiles->pluck('id'))
            ->get();

        return view('admin.students.show', compact('student', 'availableFiles'));
    }

    public function edit(Student $student)
    {
        return view('admin.students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $student->update($validated);

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    public function grantAccess(Request $request, Student $student)
    {
        $validated = $request->validate([
            'file_id' => 'required|exists:downloadable_files,id',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $student->downloadableFiles()->attach($validated['file_id'], [
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        return back()->with('success', 'File access granted successfully.');
    }

    public function revokeAccess(Student $student, $fileId)
    {
        $student->downloadableFiles()->detach($fileId);

        return back()->with('success', 'File access revoked successfully.');
    }
}
