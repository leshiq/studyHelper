<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CredentialChangeController extends Controller
{
    public function show()
    {
        // Only allow users who must change credentials
        if (!Auth::user()->must_change_credentials) {
            return redirect()->route('dashboard');
        }

        return view('auth.change-credentials');
    }

    public function update(Request $request)
    {
        /** @var \App\Models\Student $user */
        $user = Auth::user();

        // Verify user must change credentials
        if (!$user->must_change_credentials) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'email' => ['required', 'email', 'unique:students,email,' . $user->id],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Prevent using the default credentials
        if ($validated['email'] === 'superadmin@studyhelper.com' || $request->password === 'superadmin') {
            return back()->withErrors([
                'email' => 'You cannot use the default credentials. Please choose different ones.',
            ]);
        }

        // Update credentials
        $user->update([
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'must_change_credentials' => false,
        ]);

        // Redirect based on user role
        if ($user->is_superuser) {
            return redirect()->route('superuser.dashboard')
                ->with('success', 'Credentials updated successfully! Welcome to the Superuser Panel.');
        } elseif ($user->is_admin) {
            return redirect()->route('admin.files.index')
                ->with('success', 'Credentials updated successfully!');
        } else {
            return redirect()->route('dashboard')
                ->with('success', 'Credentials updated successfully!');
        }
    }
}
