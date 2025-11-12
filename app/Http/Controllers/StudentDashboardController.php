<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentDashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\Student $student */
        $student = Auth::user();
        
        // Get files accessible to this student
        $files = $student->downloadableFiles()
            ->where('is_active', true)
            ->where(function($query) {
                $query->whereNull('file_accesses.expires_at')
                      ->orWhere('file_accesses.expires_at', '>', now());
            })
            ->withCount(['downloadLogs' => function($query) use ($student) {
                $query->where('student_id', $student->id);
            }])
            ->get();

        return view('dashboard', compact('files'));
    }
}
