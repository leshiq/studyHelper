<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DownloadableFile;
use App\Models\Student;
use Illuminate\Http\Request;

class FileManagementController extends Controller
{
    public function index()
    {
        $files = DownloadableFile::withCount('students', 'downloadLogs')->latest()->get();
        return view('admin.files.index', compact('files'));
    }

    public function create()
    {
        return view('admin.files.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'filename' => 'required|string|max:255',
            'file_path' => 'required|string|max:500',
            'file_size' => 'nullable|integer',
            'mime_type' => 'nullable|string|max:100',
            'max_downloads' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $file = DownloadableFile::create($validated);

        return redirect()->route('admin.files.show', $file)
            ->with('success', 'File created successfully.');
    }

    public function show(DownloadableFile $file)
    {
        $file->load(['students', 'downloadLogs' => function($query) {
            $query->latest()->limit(50);
        }]);
        
        $availableStudents = Student::where('is_active', true)
            ->whereNotIn('id', $file->students->pluck('id'))
            ->get();

        return view('admin.files.show', compact('file', 'availableStudents'));
    }

    public function edit(DownloadableFile $file)
    {
        return view('admin.files.edit', compact('file'));
    }

    public function update(Request $request, DownloadableFile $file)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'filename' => 'required|string|max:255',
            'file_path' => 'required|string|max:500',
            'file_size' => 'nullable|integer',
            'mime_type' => 'nullable|string|max:100',
            'max_downloads' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $file->update($validated);

        return redirect()->route('admin.files.show', $file)
            ->with('success', 'File updated successfully.');
    }

    public function destroy(DownloadableFile $file)
    {
        $file->delete();

        return redirect()->route('admin.files.index')
            ->with('success', 'File deleted successfully.');
    }

    public function grantAccess(Request $request, DownloadableFile $file)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'expires_at' => 'nullable|date|after:now',
        ]);

        $file->students()->attach($validated['student_id'], [
            'expires_at' => $validated['expires_at'] ?? null,
        ]);

        return back()->with('success', 'Access granted successfully.');
    }

    public function revokeAccess(DownloadableFile $file, Student $student)
    {
        $file->students()->detach($student->id);

        return back()->with('success', 'Access revoked successfully.');
    }
}
