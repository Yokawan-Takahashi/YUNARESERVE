<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Tenant;
use Illuminate\Http\Request;

class PublicEventController extends Controller
{
    public function index(Tenant $tenant, Request $request)
    {
        $query = Event::where('status', 'published')
            ->with(['category', 'slots'])
            ->latest();

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $events = $query->get();
        $categories = Category::where('active', true)
            ->where('scope', 'external')
            ->orderBy('sort')->get();

        return view('public.index', compact('tenant', 'events', 'categories'));
    }

    public function show(Tenant $tenant, Event $event)
    {
        if (!$event->isPublished()) {
            abort(404);
        }
        $event->load(['slots', 'category']);

        return view('public.show', compact('tenant', 'event'));
    }
}
