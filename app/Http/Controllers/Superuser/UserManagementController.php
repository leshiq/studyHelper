<?php

namespace App\Http\Controllers\Superuser;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = Student::orderBy('is_superuser', 'desc')
            ->orderBy('is_admin', 'desc')
            ->orderBy('name')
            ->paginate(20);

        return view('superuser.users.index', compact('users'));
    }
}
