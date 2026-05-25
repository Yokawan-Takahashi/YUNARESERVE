<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $weekStart = Carbon::today()->startOfWeek();
        $weekEnd = Carbon::today()->endOfWeek();

        $todayCount = Reservation::whereHas('slot', fn($q) => $q->whereDate('date', $today))
            ->where('status', 'reserved')
            ->count();

        $weekCount = Reservation::whereHas('slot', fn($q) => $q->whereBetween('date', [$weekStart, $weekEnd]))
            ->where('status', 'reserved')
            ->count();

        $totalCount = Reservation::where('status', 'reserved')->count();

        $recent = Reservation::with(['event', 'slot'])
            ->where('status', 'reserved')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('todayCount', 'weekCount', 'totalCount', 'recent'));
    }
}
