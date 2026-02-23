<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StickerType;
use Illuminate\Http\Request;

class StickerTypeController extends Controller
{
    public function index()
    {
        $stickerTypes = StickerType::withCount('pins')->ordered()->get();
        return view('admin.sticker-types.index', compact('stickerTypes'));
    }

    public function create()
    {
        return view('admin.sticker-types.form', [
            'stickerType' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:sticker_types,name|alpha_dash',
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'color' => 'required|string|max:7',
        ]);

        StickerType::create($validated);

        return redirect()->route('admin.sticker-types.index')->with('success', 'Sticker type created!');
    }

    public function edit(StickerType $stickerType)
    {
        return view('admin.sticker-types.form', compact('stickerType'));
    }

    public function update(Request $request, StickerType $stickerType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|alpha_dash|unique:sticker_types,name,' . $stickerType->id,
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'color' => 'required|string|max:7',
        ]);

        $stickerType->update($validated);

        return redirect()->route('admin.sticker-types.index')->with('success', 'Sticker type updated!');
    }

    public function destroy(StickerType $stickerType)
    {
        if ($stickerType->pins()->exists()) {
            return back()->with('error', 'Cannot delete a sticker type that still has pins. Reassign or delete the pins first.');
        }

        $stickerType->delete();

        // If the deleted type was the active one in anyone's session, they'll just see "All" next time
        return redirect()->route('admin.sticker-types.index')->with('success', 'Sticker type deleted!');
    }
}
