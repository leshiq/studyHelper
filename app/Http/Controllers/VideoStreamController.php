<?php

namespace App\Http\Controllers;

use App\Models\DownloadableFile;
use App\Services\VideoStream;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VideoStreamController extends Controller
{
    public function stream(DownloadableFile $file)
    {
        /** @var \App\Models\Student $student */
        $student = Auth::user();

        // Check if file is active
        if (!$file->is_active) {
            abort(403, 'This file is not available for streaming.');
        }

        // Check if student has access
        $access = $student->fileAccesses()
            ->where('downloadable_file_id', $file->id)
            ->first();

        if (!$access) {
            abort(403, 'You do not have permission to view this file.');
        }

        // Check if access has expired
        if ($access->expires_at && $access->expires_at < now()) {
            abort(403, 'Your access to this file has expired.');
        }

        // Get file path
        $path = storage_path('app/' . $file->file_path);

        if (!file_exists($path)) {
            abort(404, 'Video file not found.');
        }

        $stream = new VideoStream($path);
        return response()->stream(function() use ($stream) {
            $stream->start();
        }, 200, [
            'Content-Type' => $file->mime_type ?? 'video/mp4',
            'Accept-Ranges' => 'bytes',
        ]);
    }

    public function watch(DownloadableFile $file)
    {
        /** @var \App\Models\Student $student */
        $student = Auth::user();

        // Check if file is active
        if (!$file->is_active) {
            abort(403, 'This file is not available.');
        }

        // Check if student has access
        $access = $student->fileAccesses()
            ->where('downloadable_file_id', $file->id)
            ->first();

        if (!$access) {
            abort(403, 'You do not have permission to view this file.');
        }

        // Check if access has expired
        if ($access->expires_at && $access->expires_at < now()) {
            abort(403, 'Your access to this file has expired.');
        }

        // Check if this file is part of a course lesson
        $courseLesson = $file->courseLessons()->with('course')->first();
        $course = $courseLesson ? $courseLesson->course : null;

        return view('watch', compact('file', 'course', 'courseLesson'));
    }
}
