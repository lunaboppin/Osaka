<?php

namespace App\Http\Controllers;

use App\Models\Pin;
use Illuminate\Http\Request;

class PinController extends Controller
{
    // Dedicated JSON endpoint for map
    public function json()
    {
        return Pin::with(['user:id,name', 'updates.user:id,name'])->get();
    }
    public function index(Request $request)
    {
        // If AJAX or expects JSON, return pins as JSON for map
        if ($request->wantsJson() || $request->ajax()) {
            return Pin::with(['user:id,name', 'updates.user:id,name'])->get();
        }
        // Otherwise, return Blade view for web
        $pins = Pin::with(['user:id,name', 'updates.user:id,name'])->get();
        return view('pins.index', compact('pins'));
    }
    public function edit(Pin $pin)
    {
        return view('pins.edit', compact('pin'));
    }

    public function update(Request $request, Pin $pin)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'status' => 'required|in:New,Worn,Needs replaced,Missing',
        ]);
        $pin->update($validated);
        return redirect()->route('pins.index')->with('success', 'Pin updated!');
    }

    public function destroy(Pin $pin)
    {
        $pin->delete();
        return redirect()->route('pins.index')->with('success', 'Pin deleted!');
    }

    public function create()
    {
        return view('pins.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'status' => 'required|in:New,Worn,Needs replaced,Missing',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'nullable|image|max:4096',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('pins', 'public');
            $validated['photo'] = $path;
        }

        $validated['user_id'] = $request->user()->id;

        Pin::create($validated);
        return redirect()->route('dashboard')->with('success', 'Pin added!');
    }
}
