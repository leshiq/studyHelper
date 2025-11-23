<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user(),
            'allowPasswordChange' => Setting::get('allow_password_change', false)
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $allowPasswordChange = Setting::get('allow_password_change', false);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('students')->ignore($user->id)],
            'theme_preference' => ['nullable', 'in:light,dark,auto'],
        ];

        // Only validate password fields if password change is allowed
        if ($allowPasswordChange) {
            $rules['current_password'] = ['nullable', 'required_with:new_password'];
            $rules['new_password'] = ['nullable', 'min:8', 'confirmed'];
        }

        $validated = $request->validate($rules);

        // Update name and email
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        // Update theme preference if provided
        if ($request->filled('theme_preference')) {
            $user->theme_preference = $validated['theme_preference'];
        }

        // Update password if provided and allowed
        if ($allowPasswordChange && $request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'The current password is incorrect.']);
            }

            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }

    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $user = Auth::user();

        // Delete old avatars if they exist
        $this->deleteUserAvatars($user);

        // Get uploaded file
        $file = $request->file('avatar');
        $filename = 'avatar_' . $user->id . '_' . time();
        
        // Create directories if they don't exist
        $directories = ['original', 'large', 'medium', 'small'];
        foreach ($directories as $dir) {
            if (!file_exists(public_path("avatars/{$dir}"))) {
                mkdir(public_path("avatars/{$dir}"), 0775, true);
            }
        }

        // Save original
        $originalPath = "avatars/original/{$filename}." . $file->getClientOriginalExtension();
        $file->move(public_path('avatars/original'), $filename . '.' . $file->getClientOriginalExtension());
        $user->avatar_original = $filename . '.' . $file->getClientOriginalExtension();

        // Create image manager instance
        $manager = new ImageManager(new Driver());

        // Process and save large (400x400)
        $largeImg = $manager->read(public_path($originalPath));
        $largeImg->cover(400, 400);
        $largeImg->save(public_path("avatars/large/{$filename}.webp"), quality: 90);
        $user->avatar_large = "{$filename}.webp";

        // Process and save medium (200x200)
        $mediumImg = $manager->read(public_path($originalPath));
        $mediumImg->cover(200, 200);
        $mediumImg->save(public_path("avatars/medium/{$filename}.webp"), quality: 85);
        $user->avatar_medium = "{$filename}.webp";

        // Process and save small (64x64)
        $smallImg = $manager->read(public_path($originalPath));
        $smallImg->cover(64, 64);
        $smallImg->save(public_path("avatars/small/{$filename}.webp"), quality: 80);
        $user->avatar_small = "{$filename}.webp";

        $user->save();

        return back()->with('success', 'Avatar updated successfully.');
    }

    public function deleteAvatar()
    {
        $user = Auth::user();
        
        $this->deleteUserAvatars($user);

        $user->avatar_original = null;
        $user->avatar_large = null;
        $user->avatar_medium = null;
        $user->avatar_small = null;
        $user->save();

        return back()->with('success', 'Avatar removed successfully.');
    }

    private function deleteUserAvatars($user)
    {
        $avatars = [
            'original' => $user->avatar_original,
            'large' => $user->avatar_large,
            'medium' => $user->avatar_medium,
            'small' => $user->avatar_small,
        ];

        foreach ($avatars as $size => $filename) {
            if ($filename && file_exists(public_path("avatars/{$size}/{$filename}"))) {
                unlink(public_path("avatars/{$size}/{$filename}"));
            }
        }
    }
}
