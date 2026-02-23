<?php

namespace App\Http\Controllers;

use App\Models\Pin;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $stats = [
            'total' => Pin::count(),
            'new' => Pin::where('status', 'New')->count(),
            'worn' => Pin::where('status', 'Worn')->count(),
            'needs_replaced' => Pin::where('status', 'Needs replaced')->count(),
            'overdue' => Pin::overdue()->count(),
        ];

        $recentPins = Pin::with('user:id,name,avatar')
            ->latest()
            ->take(6)
            ->get();

        return view('dashboard', compact('stats', 'recentPins'));
    }
}
