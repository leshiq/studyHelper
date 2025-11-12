<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm($token)
    {
        $invitation = StudentInvitation::where('token', $token)->firstOrFail();

        if (!$invitation->isValid()) {
            return view('auth.register-expired');
        }

        return view('auth.register', compact('invitation'));
    }

    public function register(Request $request, $token)
    {
        $invitation = StudentInvitation::where('token', $token)->firstOrFail();

        if (!$invitation->isValid()) {
            return redirect()->route('login')
                ->with('error', 'This invitation link has expired or already been used.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:students',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $student = Student::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => true,
            'is_admin' => false,
        ]);

        $invitation->markAsUsed($student);

        Auth::login($student);

        return redirect()->route('dashboard')
            ->with('success', 'Registration successful! Welcome to Study Helper.');
    }
}
