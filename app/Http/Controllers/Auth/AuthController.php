<?php

namespace App\Http\Controllers\Auth;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('authentik')->redirect();
    }

    public function callback()
    {
        try {
            $authentikUser = Socialite::driver('authentik')->user();

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

            Auth::login($user, true);

            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['error' => 'Authentication failed.']);
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
