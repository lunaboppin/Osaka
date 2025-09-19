<?php

namespace App\Http\Controllers;

use App\Models\Pin;
use App\Models\PinUpdate;
use Illuminate\Http\Request;

class PinUpdateController extends Controller
{
    public function store(Request $request, Pin $pin)
    {
        $validated = $request->validate([
            'status' => 'required|in:New,Worn,Needs replaced',
            'photo' => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('pin_updates', 'public');
            $validated['photo'] = $path;
        }
        $validated['pin_id'] = $pin->id;
        $validated['user_id'] = $request->user()->id;
    $update = PinUpdate::create($validated);
    // Update parent pin's status
    $pin->status = $update->status;
    $pin->save();
    return redirect()->route('pins.edit', $pin)->with('success', 'Update added!');
    }
}
