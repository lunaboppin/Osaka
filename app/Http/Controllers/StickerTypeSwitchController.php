<?php

namespace App\Http\Controllers;

use App\Models\StickerType;
use Illuminate\Http\Request;

class StickerTypeSwitchController extends Controller
{
    public function __invoke(Request $request)
    {
        $validated = $request->validate([
            'sticker_type_id' => 'nullable|exists:sticker_types,id',
        ]);

        // Store in session — empty string means explicitly chose "All Types"
        $stickerTypeId = $validated['sticker_type_id'] ?? null;
        $request->session()->put('current_sticker_type_id', $stickerTypeId);

        return back();
    }
}
