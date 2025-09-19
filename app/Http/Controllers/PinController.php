<?php

namespace App\Http\Controllers;

use App\Models\Pin;
use Illuminate\Http\Request;

class PinController extends Controller
{
    public function index()
    {
        return response()->json(Pin::with('user:id,name')->get());
    }

    public function create()
    {
        return view('pins.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
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
        // Set title to empty string for now (should be set by geocoding in future)
        $validated['title'] = '';

        Pin::create($validated);
        return redirect()->route('dashboard')->with('success', 'Pin added!');
    }
}
