<?php

namespace App\Http\Controllers;

use App\Models\Pin;
use App\Models\PinUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PinController extends Controller
{
    // Dedicated JSON endpoint for map
    public function json(Request $request)
    {
        $query = Pin::with('user:id,name,avatar');

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        return $query->get();
    }

    public function index(Request $request)
    {
        $query = Pin::with('user:id,name,avatar')->withCount('updates');

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
        $pin->load(['user:id,name,avatar', 'updates.user:id,name,avatar']);
        return view('pins.show', compact('pin'));
    }

    public function create()
    {
        return view('pins.create');
    }

    public function store(Request $request)
    {
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

        $pin = Pin::create($validated);

        // Record the initial state as the first timeline entry
        PinUpdate::create([
            'pin_id' => $pin->id,
            'user_id' => $pin->user_id,
            'status' => $pin->status,
            'photo' => $pin->photo,
            'notes' => 'Initial pin creation.',
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Pin added successfully!', 'pin' => $pin], 201);
        }

        return redirect()->route('pins.show', $pin)->with('success', 'Pin added!');
    }

    public function edit(Pin $pin)
    {
        if ($pin->user_id !== auth()->id()) {
            abort(403, 'You can only edit your own pins.');
        }

        return view('pins.edit', compact('pin'));
    }

    public function update(Request $request, Pin $pin)
    {
        if ($pin->user_id !== auth()->id()) {
            abort(403, 'You can only edit your own pins.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'status' => 'required|in:New,Worn,Needs replaced',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'photo' => 'nullable|image|max:4096',
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

        return redirect()->route('pins.show', $pin)->with('success', 'Pin updated!');
    }

    public function destroy(Pin $pin)
    {
        if ($pin->user_id !== auth()->id()) {
            abort(403, 'You can only delete your own pins.');
        }

        // Delete photo from storage
        if ($pin->photo) {
            Storage::disk('public')->delete($pin->photo);
        }

        $pin->delete();
        return redirect()->route('pins.index')->with('success', 'Pin deleted!');
    }
}
