<?php

namespace App\Http\Controllers;

use App\Models\DownloadableFile;
use App\Models\DownloadLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileDownloadController extends Controller
{
    public function download(DownloadableFile $file)
    {
        /** @var \App\Models\Student $student */
        $student = Auth::user();

        // Check if file is active
        if (!$file->is_active) {
            abort(403, 'This file is not available for download.');
        }

        // Check if student has access
        $access = $student->fileAccesses()
            ->where('downloadable_file_id', $file->id)
            ->first();

        if (!$access) {
            abort(403, 'You do not have permission to download this file.');
        }

        // Check if access has expired
        if ($access->expires_at && $access->expires_at < now()) {
            abort(403, 'Your access to this file has expired.');
        }

        // Check download limit
        if ($file->max_downloads) {
            $downloadCount = DownloadLog::where('student_id', $student->id)
                ->where('downloadable_file_id', $file->id)
                ->count();

            if ($downloadCount >= $file->max_downloads) {
                abort(403, 'You have reached the maximum download limit for this file.');
            }
        }

        // Log the download
        DownloadLog::create([
            'student_id' => $student->id,
            'downloadable_file_id' => $file->id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        // Return file download
        return response()->download(
            storage_path('app/' . $file->file_path),
            $file->filename
        );
    }
}
