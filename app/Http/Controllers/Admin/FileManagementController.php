<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DownloadableFile;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileManagementController extends Controller
{
    public function index()
    {
        // Get all files from storage directory
        $storagePath = storage_path('app/uploads/lessons');
        $filesInStorage = [];
        
        if (file_exists($storagePath)) {
            $files = scandir($storagePath);
            foreach ($files as $filename) {
                if ($filename !== '.' && $filename !== '..') {
                    $fullPath = $storagePath . '/' . $filename;
                    if (is_file($fullPath)) {
                        $filesInStorage[$filename] = [
                            'filename' => $filename,
                            'size' => filesize($fullPath),
                            'path' => 'uploads/lessons/' . $filename,
                            'mime_type' => mime_content_type($fullPath),
                            'modified' => filemtime($fullPath),
                        ];
                    }
                }
            }
        }
        
        // Get all saved files from database
        $savedFiles = DownloadableFile::withCount('students', 'downloadLogs')->get();
        
        // Mark which files are already in database
        foreach ($savedFiles as $file) {
            if (isset($filesInStorage[$file->filename])) {
                $filesInStorage[$file->filename]['db_record'] = $file;
            }
        }
        
        // Sort by modification time (newest first)
        uasort($filesInStorage, function($a, $b) {
            return $b['modified'] - $a['modified'];
        });
        
        return view('admin.files.index', compact('filesInStorage'));
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
            'file_size' => 'required|integer',
            'mime_type' => 'required|string|max:100',
            'max_downloads' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $file = DownloadableFile::create($validated);

        return redirect()->route('admin.files.index')
            ->with('success', 'File saved successfully.');
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
