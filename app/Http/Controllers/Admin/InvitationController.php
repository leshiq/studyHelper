<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StudentInvitation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvitationController extends Controller
{
    public function index()
    {
        $invitations = StudentInvitation::with(['creator', 'student'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.invitations.index', compact('invitations'));
    }

    public function create()
    {
        $token = StudentInvitation::generateToken();
        
        /** @var \App\Models\Student $admin */
        $admin = Auth::user();

        $invitation = StudentInvitation::create([
            'token' => $token,
            'expires_at' => now()->addHours(24),
            'created_by' => $admin->id,
        ]);

        return redirect()
            ->route('admin.invitations.index')
            ->with('invitation_created', $invitation);
    }

    public function destroy(StudentInvitation $invitation)
    {
        $invitation->delete();

        return redirect()
            ->route('admin.invitations.index')
            ->with('success', 'Invitation link deleted successfully.');
    }
}
