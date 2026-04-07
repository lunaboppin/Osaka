<?php

namespace App\Http\Controllers\Auth;

use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;

class AuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('authentik')->redirect();
    }

    public function callback(Request $request)
    {
        try {
            $driver = Socialite::driver('authentik');

            try {
                $authentikUser = $driver->user();
            } catch (InvalidStateException $e) {
                $authentikUser = $driver->stateless()->user();
            }

            $user = User::updateOrCreate([
                'email' => $authentikUser->getEmail(),
            ], [
                'name' => $authentikUser->getName(),
                'authentik_id' => $authentikUser->getId(),
            ]);

            // Only set avatar from Authentik if the user hasn't uploaded a local one
            if (!$user->avatar && $authentikUser->getAvatar()) {
                $user->update(['avatar' => $authentikUser->getAvatar()]);
            }

            // Auto-assign default member role to new users
            if ($user->wasRecentlyCreated) {
                $user->assignRole('member');
                AuditLog::log('created', "New user registered via OAuth: {$user->name}", $user, null, null, $user->id);
            }

            Auth::login($user);
            $request->session()->regenerate();

            AuditLog::log('login', "User logged in: {$user->name}", $user, null, null, $user->id);

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            report($e);

            return redirect('/')->with('error', 'Authentication failed. Please try again. If this keeps happening, clear cookies for both the app and identity domains.');
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::user();

        AuditLog::log('logout', "User logged out: {$user->name}", $user);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
