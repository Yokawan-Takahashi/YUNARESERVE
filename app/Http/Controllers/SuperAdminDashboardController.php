<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Tenant;
use App\Models\User;

class SuperAdminDashboardController extends Controller
{
    public function index()
    {
        $totalTenants  = Tenant::withoutGlobalScopes()->count();
        $activeTenants = Tenant::withoutGlobalScopes()->where('status', 'active')->count();
        $totalUsers    = User::withoutGlobalScopes()->where('role', '!=', 'superadmin')->count();
        $totalReservations = Reservation::withoutGlobalScopes()->where('status', 'reserved')->count();

        $recentTenants = Tenant::withoutGlobalScopes()
            ->withCount('users')
            ->latest()
            ->take(10)
            ->get();

        return view('superadmin.dashboard', compact(
            'totalTenants', 'activeTenants', 'totalUsers', 'totalReservations', 'recentTenants'
        ));
    }

    public function showTenant(Tenant $tenant)
    {
        $tenant->loadCount(['users', 'events', 'reservations']);
        $tenant->load(['users']);

        $activeReservations = $tenant->reservations()->where('status', 'reserved')->count();
        $subscription = $tenant->subscription('default');

        return view('superadmin.tenants.show', compact('tenant', 'activeReservations', 'subscription'));
    }
}
