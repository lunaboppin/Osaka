<?php

namespace App\Http\Controllers;

use App\Models\Pin;
use Illuminate\Http\Request;

class PinController extends Controller
{
    public function index()
    {
        $pins = Pin::with('user:id,name')->get();
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
            'status' => 'required|in:New,Worn,Needs replaced',
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

        Pin::create($validated);
        return redirect()->route('dashboard')->with('success', 'Pin added!');
    }
}
