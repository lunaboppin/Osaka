<?php

namespace App\Http\Controllers;

use App\Models\Pin;
use App\Models\PinUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PinUpdateController extends Controller
{
    /**
     * Store a new update for a pin (timeline entry).
     */
    public function store(Request $request, Pin $pin)
    {
        $validated = $request->validate([
            'status' => 'required|in:New,Worn,Needs replaced',
            'notes' => 'nullable|string|max:2000',
            'photo' => 'nullable|image|max:4096',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('pin-updates', 'public');
        }

        $update = PinUpdate::create([
            'pin_id' => $pin->id,
            'user_id' => $request->user()->id,
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
            'photo' => $photoPath,
        ]);

        // Sync the pin's current status and photo to match the latest update
        $pinData = ['status' => $validated['status'], 'last_checked_at' => now()];
        if ($photoPath) {
            $pinData['photo'] = $photoPath;
        }
        $pin->update($pinData);

        return redirect()->route('pins.show', $pin)->with('success', 'Update added to timeline!');
    }

    /**
     * Delete a timeline entry.
     */
    public function destroy(Pin $pin, PinUpdate $update)
    {
        // Only the update's author or the pin owner can delete an update
        if ($update->user_id !== auth()->id() && $pin->user_id !== auth()->id()) {
            abort(403, 'You can only delete your own updates.');
        }

        // Clean up stored photo
        if ($update->photo) {
            Storage::disk('public')->delete($update->photo);
        }

        $update->delete();

        // If the deleted update was the latest, revert pin status/photo to the new latest
        $latest = $pin->updates()->first();
        if ($latest) {
            $pin->update([
                'status' => $latest->status,
                'photo' => $latest->photo ?? $pin->photo,
            ]);
        }

        return redirect()->route('pins.show', $pin)->with('success', 'Update removed from timeline.');
    }
}
