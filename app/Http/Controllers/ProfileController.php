<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\StickerType;
use App\Models\User;
use App\Services\XpService;
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
        $stickerTypeId = StickerType::currentId();
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
        $stickerTypeId = StickerType::currentId();
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
            'stickerTypes' => StickerType::ordered()->get(),
            'themes' => config('osaka.profile.themes', []),
            'avatarFrames' => config('osaka.profile.avatar_frames', []),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->safe()->only(['name', 'bio']));

        // Handle default sticker type (explicit null when clearing)
        $user->default_sticker_type_id = $request->input('default_sticker_type_id') ?: null;

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
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

        // Handle banner upload
        if ($request->hasFile('banner')) {
            if ($user->banner_path) {
                Storage::disk('public')->delete($user->banner_path);
            }
            $user->banner_path = $request->file('banner')->store('banners', 'public');
        }

        // Handle banner removal
        if ($request->boolean('remove_banner')) {
            if ($user->banner_path) {
                Storage::disk('public')->delete($user->banner_path);
            }
            $user->banner_path = null;
        }

        // Handle accent colour
        if ($request->boolean('clear_accent_color')) {
            $user->accent_color = null;
        } elseif ($request->filled('accent_color')) {
            $user->accent_color = $request->input('accent_color');
        }

        // Handle profile theme (validate level requirement)
        if ($request->filled('profile_theme')) {
            $theme = $request->input('profile_theme');
            $themeConfig = config("osaka.profile.themes.{$theme}");
            if ($themeConfig && ($themeConfig['min_level'] ?? 1) <= $user->level) {
                $user->profile_theme = $theme;
            }
        }

        // Handle avatar frame (validate level requirement)
        if ($request->has('avatar_frame')) {
            $frame = $request->input('avatar_frame');
            if ($frame === '' || $frame === 'none') {
                $user->avatar_frame = null;
            } else {
                $frameConfig = config("osaka.profile.avatar_frames.{$frame}");
                if ($frameConfig && ($frameConfig['min_level'] ?? 1) <= $user->level) {
                    $user->avatar_frame = $frame;
                }
            }
        }

        // Handle displayed badges (validate they're actually available)
        if ($request->has('displayed_badges')) {
            $requested = $request->input('displayed_badges', []);
            $maxBadges = config('osaka.profile.max_displayed_badges', 5);
            $user->displayed_badges = collect($requested)
                ->filter(fn($key) => isset($user->available_badges[$key]))
                ->take($maxBadges)
                ->values()
                ->all();
        }

        $user->save();

        // One-time profile completion XP
        if ($user->bio && $user->avatar) {
            $alreadyAwarded = $user->xpTransactions()->where('action', 'profile_completed')->exists();
            if (!$alreadyAwarded) {
                app(XpService::class)->award($user, 'profile_completed', 'Profile completed (bio & avatar set)');
            }
        }

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
