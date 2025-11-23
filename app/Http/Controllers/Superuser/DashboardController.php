<?php

namespace App\Http\Controllers\Superuser;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\DownloadLog;
use App\Models\StudentInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get statistics
        $stats = [
            'total_users' => Student::count(),
            'superusers' => Student::where('is_superuser', true)->count(),
            'admins' => Student::where('is_admin', true)->where('is_superuser', false)->count(),
            'students' => Student::where('is_admin', false)->where('is_superuser', false)->count(),
            'active_users' => Student::where('is_active', true)->count(),
            'total_downloads' => DownloadLog::count(),
            'downloads_today' => DownloadLog::whereDate('created_at', today())->count(),
            'invitations_sent' => StudentInvitation::count(),
            'invitations_used' => StudentInvitation::whereNotNull('used_at')->count(),
        ];

        // Recent user registrations
        $recent_users = Student::orderBy('created_at', 'desc')->limit(5)->get();

        // Recent downloads
        $recent_downloads = DownloadLog::with(['student', 'downloadableFile'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Most active users (by download count)
        $active_users = Student::select('students.*', DB::raw('COUNT(download_logs.id) as download_count'))
            ->leftJoin('download_logs', 'students.id', '=', 'download_logs.student_id')
            ->groupBy('students.id')
            ->orderBy('download_count', 'desc')
            ->limit(5)
            ->get();

        return view('superuser.dashboard', compact('stats', 'recent_users', 'recent_downloads', 'active_users'));
    }
}

