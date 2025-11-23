<?php

namespace App\Http\Controllers\Superuser;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    public function index()
    {
        return view('superuser.settings.index');
    }

    public function emailTest()
    {
        return view('superuser.settings.email-test');
    }

    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'recipient' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            Log::info('Attempting to send test email', [
                'recipient' => $request->recipient,
                'subject' => $request->subject,
                'mail_driver' => config('mail.default'),
                'mail_host' => config('mail.mailers.smtp.host'),
                'mail_port' => config('mail.mailers.smtp.port'),
                'from_address' => config('mail.from.address'),
            ]);

            Mail::raw($request->message, function ($mail) use ($request) {
                $mail->to($request->recipient)
                     ->subject($request->subject);
            });

            Log::info('Test email sent successfully', [
                'recipient' => $request->recipient,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully to ' . $request->recipient . '. Check logs at: storage/logs/laravel.log'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send test email', [
                'recipient' => $request->recipient,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage() . ' (Check storage/logs/laravel.log for details)'
            ], 500);
        }
    }

    public function general()
    {
        return view('superuser.settings.general', [
            'allowPasswordChange' => Setting::get('allow_password_change', false)
        ]);
    }

    public function updateGeneral(Request $request)
    {
        $request->validate([
            'allow_password_change' => 'required|boolean',
        ]);

        Setting::set('allow_password_change', $request->allow_password_change, 'boolean');

        return back()->with('success', 'Settings updated successfully.');
    }

    public function appearance()
    {
        return view('superuser.settings.appearance', [
            'portalLogo' => Setting::get('portal_logo', ''),
            'loginBgImage' => Setting::get('login_bg_image', ''),
            'loginBgGradient' => Setting::get('login_bg_gradient', 'linear-gradient(135deg, #667eea, #764ba2)'),
            'loginBgColor' => Setting::get('login_bg_color', '#667eea'),
            'pagesBgImage' => Setting::get('pages_bg_image', ''),
            'pagesBgGradient' => Setting::get('pages_bg_gradient', ''),
            'pagesBgColor' => Setting::get('pages_bg_color', '#ffffff'),
            'sidebarColor' => Setting::get('sidebar_color', 'linear-gradient(180deg, #667eea 0%, #764ba2 100%)'),
        ]);
    }

    public function updateAppearance(Request $request)
    {
        $request->validate([
            'portal_logo' => 'nullable|image|max:2048',
            'login_bg_image' => 'nullable|image|max:5120',
            'login_bg_gradient' => 'nullable|string|max:500',
            'login_bg_color' => 'nullable|string|max:20',
            'pages_bg_image' => 'nullable|image|max:5120',
            'pages_bg_gradient' => 'nullable|string|max:500',
            'pages_bg_color' => 'nullable|string|max:20',
            'sidebar_color' => 'nullable|string|max:500',
        ]);

        // Handle portal logo upload
        if ($request->hasFile('portal_logo')) {
            $logoFile = $request->file('portal_logo');
            $logoName = 'logo_' . time() . '.' . $logoFile->getClientOriginalExtension();
            $logoFile->move(public_path('portal-assets/logos'), $logoName);
            
            // Delete old logo if exists
            $oldLogo = Setting::get('portal_logo', '');
            if ($oldLogo && file_exists(public_path('portal-assets/logos/' . $oldLogo))) {
                unlink(public_path('portal-assets/logos/' . $oldLogo));
            }
            
            Setting::set('portal_logo', $logoName);
        }

        // Handle login background image upload
        if ($request->hasFile('login_bg_image')) {
            $bgFile = $request->file('login_bg_image');
            $bgName = 'login_bg_' . time() . '.' . $bgFile->getClientOriginalExtension();
            $bgFile->move(public_path('portal-assets/backgrounds'), $bgName);
            
            // Delete old background if exists
            $oldBg = Setting::get('login_bg_image', '');
            if ($oldBg && file_exists(public_path('portal-assets/backgrounds/' . $oldBg))) {
                unlink(public_path('portal-assets/backgrounds/' . $oldBg));
            }
            
            Setting::set('login_bg_image', $bgName);
        }

        // Handle pages background image upload
        if ($request->hasFile('pages_bg_image')) {
            $pagesBgFile = $request->file('pages_bg_image');
            $pagesBgName = 'pages_bg_' . time() . '.' . $pagesBgFile->getClientOriginalExtension();
            $pagesBgFile->move(public_path('portal-assets/backgrounds'), $pagesBgName);
            
            // Delete old background if exists
            $oldPagesBg = Setting::get('pages_bg_image', '');
            if ($oldPagesBg && file_exists(public_path('portal-assets/backgrounds/' . $oldPagesBg))) {
                unlink(public_path('portal-assets/backgrounds/' . $oldPagesBg));
            }
            
            Setting::set('pages_bg_image', $pagesBgName);
        }

        // Update text-based settings
        if ($request->filled('login_bg_gradient')) {
            Setting::set('login_bg_gradient', $request->login_bg_gradient);
        }
        if ($request->filled('login_bg_color')) {
            Setting::set('login_bg_color', $request->login_bg_color);
        }
        if ($request->filled('pages_bg_gradient')) {
            Setting::set('pages_bg_gradient', $request->pages_bg_gradient);
        }
        if ($request->filled('pages_bg_color')) {
            Setting::set('pages_bg_color', $request->pages_bg_color);
        }
        if ($request->filled('sidebar_color')) {
            Setting::set('sidebar_color', $request->sidebar_color);
        }

        return back()->with('success', 'Appearance settings updated successfully.');
    }

    public function about()
    {
        return view('superuser.about');
    }

    public function websocketTest()
    {
        return view('superuser.settings.websocket-test');
    }

    public function broadcastTestMessage(Request $request)
    {
        $request->validate([
            'channel' => 'required|string|max:255',
            'event' => 'required|string|max:255',
            'data' => 'required|array',
        ]);

        try {
            Log::info('Broadcasting test message', [
                'channel' => $request->channel,
                'event' => $request->event,
                'data' => $request->data,
            ]);

            event(new \App\Events\TestWebSocketEvent(
                $request->channel,
                $request->event,
                $request->data
            ));

            Log::info('Test message broadcasted successfully');

            return response()->json([
                'success' => true,
                'message' => 'Message broadcasted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to broadcast test message', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to broadcast message: ' . $e->getMessage()
            ], 500);
        }
    }
}
