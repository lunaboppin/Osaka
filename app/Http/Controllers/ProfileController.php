<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $pinStats = [
            'total' => $user->pins()->count(),
            'new' => $user->pins()->where('status', 'New')->count(),
            'worn' => $user->pins()->where('status', 'Worn')->count(),
            'needs_replaced' => $user->pins()->where('status', 'Needs replaced')->count(),
        ];
        $recentPins = $user->pins()->latest()->take(6)->get();

        return view('profile.edit', [
            'user' => $user,
            'pinStats' => $pinStats,
            'recentPins' => $recentPins,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
