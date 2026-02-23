<?php

namespace App\Http\Controllers;

use App\Models\Pin;
use App\Models\StickerType;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $stickerTypeId = session('current_sticker_type_id');

        $stats = [
            'total' => Pin::forStickerType($stickerTypeId)->count(),
            'new' => Pin::forStickerType($stickerTypeId)->where('status', 'New')->count(),
            'worn' => Pin::forStickerType($stickerTypeId)->where('status', 'Worn')->count(),
            'needs_replaced' => Pin::forStickerType($stickerTypeId)->where('status', 'Needs replaced')->count(),
            'overdue' => Pin::forStickerType($stickerTypeId)->overdue()->count(),
        ];

        $recentPins = Pin::forStickerType($stickerTypeId)
            ->with('user:id,name,avatar')
            ->withCount('updates')
            ->latest()
            ->take(6)
            ->get();

        $currentStickerType = $stickerTypeId ? StickerType::find($stickerTypeId) : null;

        return view('dashboard', compact('stats', 'recentPins', 'currentStickerType'));
    }
}
