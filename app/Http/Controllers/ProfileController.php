<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * View any user's public profile.
     */
    public function show(User $user): View
    {
        $user->load('roles');
        $stickerTypeId = session('current_sticker_type_id');
        $pinQuery = $user->pins()->when($stickerTypeId, fn($q) => $q->where('sticker_type_id', $stickerTypeId));
        $pinStats = [
            'total' => (clone $pinQuery)->count(),
            'new' => (clone $pinQuery)->where('status', 'New')->count(),
            'worn' => (clone $pinQuery)->where('status', 'Worn')->count(),
            'needs_replaced' => (clone $pinQuery)->where('status', 'Needs replaced')->count(),
        ];
        $recentPins = (clone $pinQuery)->with('user:id,name,avatar')->withCount('updates')->latest()->take(6)->get();

        return view('profile.show', [
            'user' => $user,
            'pinStats' => $pinStats,
            'recentPins' => $recentPins,
        ]);
    }

    /**
     * Display the user's own profile edit form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        $user->load('roles');
        $stickerTypeId = session('current_sticker_type_id');
        $pinQuery = $user->pins()->when($stickerTypeId, fn($q) => $q->where('sticker_type_id', $stickerTypeId));
        $pinStats = [
            'total' => (clone $pinQuery)->count(),
            'new' => (clone $pinQuery)->where('status', 'New')->count(),
            'worn' => (clone $pinQuery)->where('status', 'Worn')->count(),
            'needs_replaced' => (clone $pinQuery)->where('status', 'Needs replaced')->count(),
        ];
        $recentPins = (clone $pinQuery)->latest()->take(6)->get();

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
        $user = $request->user();
        $user->fill($request->safe()->only(['name', 'bio']));

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old uploaded avatar if it's a local file
            if ($user->avatar && str_starts_with($user->avatar, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $user->avatar));
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = '/storage/' . $path;
        }

        // Handle avatar removal
        if ($request->boolean('remove_avatar')) {
            if ($user->avatar && str_starts_with($user->avatar, '/storage/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $user->avatar));
            }
            $user->avatar = null;
        }

        $user->save();

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
