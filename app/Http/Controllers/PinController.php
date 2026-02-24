<?php

namespace App\Http\Controllers;

use App\Models\Pin;
use App\Models\PinUpdate;
use App\Models\StickerType;
use App\Services\DiscordWebhookService;
use App\Services\XpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PinController extends Controller
{
    // Dedicated JSON endpoint for map
    public function json(Request $request)
    {
        $stickerTypeId = StickerType::currentId();
        $query = Pin::forStickerType($stickerTypeId)->with('user:id,name,avatar');

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        return $query->get();
    }

    public function index(Request $request)
    {
        $stickerTypeId = StickerType::currentId();
        $query = Pin::forStickerType($stickerTypeId)->with('user:id,name,avatar')->withCount('updates');

        // Search by title or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by current user's pins
        if ($request->filled('mine') && $request->mine == '1') {
            $query->where('user_id', auth()->id());
        }

        $query->latest();

        // If AJAX or expects JSON, return pins as JSON
        if ($request->wantsJson() || $request->ajax()) {
            return $query->get();
        }

        $pins = $query->paginate(12)->withQueryString();

        return view('pins.index', compact('pins'));
    }

    public function show(Pin $pin)
    {
        $pin->load(['user:id,name,avatar', 'user.roles', 'stickerType', 'updates.user:id,name,avatar', 'updates.user.roles']);
        return view('pins.show', compact('pin'));
    }

    public function create()
    {
        if (!auth()->user()->hasPermission('pins.create')) {
            abort(403, 'You do not have permission to create pins.');
        }

        return view('pins.create');
    }

    public function store(Request $request)
    {
        if (!$request->user()->hasPermission('pins.create')) {
            abort(403, 'You do not have permission to create pins.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'status' => 'required|in:New,Worn,Needs replaced',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('pins', 'public');
            $validated['photo'] = $path;
        }

        $validated['user_id'] = $request->user()->id;
        $validated['last_checked_at'] = now();

        // Auto-assign to current sticker type, or fall back to first available
        $stickerTypeId = StickerType::currentId();
        if (!$stickerTypeId) {
            $stickerTypeId = StickerType::ordered()->value('id');
        }
        $validated['sticker_type_id'] = $stickerTypeId;

        $pin = Pin::create($validated);

        // Record the initial state as the first timeline entry
        PinUpdate::create([
            'pin_id' => $pin->id,
            'user_id' => $pin->user_id,
            'status' => $pin->status,
            'photo' => $pin->photo,
            'notes' => 'Initial pin creation.',
        ]);

        // Award XP
        $xp = app(XpService::class);
        $oldLevel = $request->user()->level;
        $xp->award($request->user(), 'pin_created', "Created pin: {$pin->title}", $pin);
        if ($request->hasFile('photo')) {
            $xp->award($request->user(), 'photo_added', "Added photo to pin: {$pin->title}", $pin);
        }
        $request->user()->refresh();

        // Discord webhook
        app(DiscordWebhookService::class)->notifyPinCreated($pin, $request->user());

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Pin added successfully!', 'pin' => $pin], 201);
        }

        $flash = 'Pin added!';
        if ($request->user()->level > $oldLevel) {
            $flash .= " Level up! You're now Level {$request->user()->level} — {$request->user()->level_name}!";
        }

        return redirect()->route('pins.show', $pin)->with('success', $flash);
    }

    public function edit(Pin $pin)
    {
        $user = auth()->user();
        if ($pin->user_id !== $user->id && !$user->hasPermission('pins.edit_any')) {
            abort(403, 'You do not have permission to edit this pin.');
        }
        if ($pin->user_id === $user->id && !$user->hasPermission('pins.edit_own')) {
            abort(403, 'You do not have permission to edit pins.');
        }

        return view('pins.edit', compact('pin'));
    }

    public function update(Request $request, Pin $pin)
    {
        $user = $request->user();
        if ($pin->user_id !== $user->id && !$user->hasPermission('pins.edit_any')) {
            abort(403, 'You do not have permission to edit this pin.');
        }
        if ($pin->user_id === $user->id && !$user->hasPermission('pins.edit_own')) {
            abort(403, 'You do not have permission to edit pins.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'status' => 'required|in:New,Worn,Needs replaced',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'photo' => 'nullable|image|max:102400',
        ]);

        // Track what changed for the timeline
        $oldStatus = $pin->status;
        $oldPhoto = $pin->photo;

        // Handle photo replacement
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('pins', 'public');
            $validated['photo'] = $path;
        }

        // Handle photo removal
        if ($request->has('remove_photo') && $request->remove_photo == '1') {
            if ($pin->photo) {
                Storage::disk('public')->delete($pin->photo);
            }
            $validated['photo'] = null;
        }

        $pin->update($validated);

        // Auto-record a timeline entry if status or photo changed
        $statusChanged = $pin->status !== $oldStatus;
        $photoChanged = $pin->photo !== $oldPhoto;
        if ($statusChanged || $photoChanged) {
            $notes = [];
            if ($statusChanged) {
                $notes[] = "Status changed from {$oldStatus} to {$pin->status}.";
            }
            if ($photoChanged && $pin->photo) {
                $notes[] = 'Photo updated.';
            }

            PinUpdate::create([
                'pin_id' => $pin->id,
                'user_id' => auth()->id(),
                'status' => $pin->status,
                'photo' => $pin->photo,
                'notes' => implode(' ', $notes),
            ]);

            $pin->update(['last_checked_at' => now()]);
        }

        // Award XP for meaningful edits
        $xp = app(XpService::class);
        $oldLevel = $request->user()->level;
        if ($statusChanged || $photoChanged) {
            $xp->award($request->user(), 'pin_updated', "Updated pin: {$pin->title}", $pin);
        }
        if ($photoChanged && $pin->photo && $request->hasFile('photo')) {
            $xp->award($request->user(), 'photo_added', "Added photo to pin: {$pin->title}", $pin);
        }
        $request->user()->refresh();
        $flash = 'Pin updated!';
        if ($request->user()->level > $oldLevel) {
            $flash .= " Level up! You're now Level {$request->user()->level} — {$request->user()->level_name}!";
        }

        return redirect()->route('pins.show', $pin)->with('success', $flash);
    }

    public function destroy(Pin $pin)
    {
        $user = auth()->user();
        if ($pin->user_id !== $user->id && !$user->hasPermission('pins.delete_any')) {
            abort(403, 'You do not have permission to delete this pin.');
        }
        if ($pin->user_id === $user->id && !$user->hasPermission('pins.delete_own')) {
            abort(403, 'You do not have permission to delete pins.');
        }

        // Delete photo from storage
        if ($pin->photo) {
            Storage::disk('public')->delete($pin->photo);
        }

        // Deduct XP
        app(XpService::class)->deduct($user, 'pin_deleted', "Deleted pin: {$pin->title}", $pin);

        // Discord webhook
        app(DiscordWebhookService::class)->notifyPinDeleted($pin, $user);

        $pin->delete();
        return redirect()->route('pins.index')->with('success', 'Pin deleted!');
    }
}
