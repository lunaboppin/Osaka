<?php

namespace App\Http\Controllers;

use App\Models\Pin;
use App\Models\PinUpdate;
use Illuminate\Http\Request;

class ReminderController extends Controller
{
    public function index(Request $request)
    {
        $defaults = config('osaka.reminders');
        $stickerTypeId = session('current_sticker_type_id');

        // Allow user to override threshold via query params (persisted in URL)
        $overdueDays = (int) $request->input('overdue_days', $defaults['overdue_days']);
        $warningDays = (int) $request->input('warning_days', $defaults['warning_days']);

        // Clamp to min/max
        $overdueDays = max($defaults['min_days'], min($defaults['max_days'], $overdueDays));
        $warningDays = max($defaults['min_days'], min($overdueDays - 1, $warningDays));

        // Status filter
        $statusFilter = $request->input('status');

        // Build the query for ALL pins that need attention (warning + overdue)
        $query = Pin::forStickerType($stickerTypeId)
            ->with('user:id,name,avatar')
            ->needsAttention($warningDays);

        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        // Sort: most overdue first (oldest last_checked_at / created_at first)
        $query->orderByRaw('COALESCE(last_checked_at, created_at) ASC');

        $pins = $query->paginate(12)->withQueryString();

        // Counts for stat cards (using default threshold)
        $overdueCount = Pin::forStickerType($stickerTypeId)->overdue($overdueDays)->count();
        $warningCount = Pin::forStickerType($stickerTypeId)->warning($warningDays, $overdueDays)->count();
        $totalNeedAttention = $overdueCount + $warningCount;

        return view('reminders.index', compact(
            'pins',
            'overdueDays',
            'warningDays',
            'overdueCount',
            'warningCount',
            'totalNeedAttention',
            'statusFilter',
            'defaults'
        ));
    }

    /**
     * Mark a pin as "checked" — updates last_checked_at to now.
     */
    public function check(Request $request, Pin $pin)
    {
        $pin->update(['last_checked_at' => now()]);

        // Record a timeline entry for the check
        PinUpdate::create([
            'pin_id' => $pin->id,
            'user_id' => $request->user()->id,
            'status' => $pin->status,
            'photo' => $pin->photo,
            'notes' => 'Marked as checked — no changes.',
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Pin marked as checked!',
                'last_checked_at' => $pin->last_checked_at->toISOString(),
            ]);
        }

        return back()->with('success', "\"" . ($pin->title ?: 'Pin') . "\" marked as checked!");
    }

    /**
     * Bulk-check: mark multiple pins as checked.
     */
    public function bulkCheck(Request $request)
    {
        $validated = $request->validate([
            'pin_ids' => 'required|array',
            'pin_ids.*' => 'exists:pins,id',
        ]);

        Pin::whereIn('id', $validated['pin_ids'])->update(['last_checked_at' => now()]);

        // Record timeline entries for bulk checks
        $pins = Pin::whereIn('id', $validated['pin_ids'])->get();
        foreach ($pins as $pin) {
            PinUpdate::create([
                'pin_id' => $pin->id,
                'user_id' => $request->user()->id,
                'status' => $pin->status,
                'photo' => $pin->photo,
                'notes' => 'Marked as checked — no changes.',
            ]);
        }

        $count = count($validated['pin_ids']);

        return back()->with('success', "{$count} pin(s) marked as checked!");
    }
}
