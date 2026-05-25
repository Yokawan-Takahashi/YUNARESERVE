<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicEventController extends Controller
{
    public function index(Request $request)
    {
        $tenant = app('tenant');
        $query = \App\Models\Event::where('status', 'published')
            ->with(['category', 'slots'])
            ->latest();

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $events = $query->get();
        $categories = \App\Models\Category::where('active', true)
            ->where('scope', 'external')
            ->orderBy('sort')->get();

        return view('public.index', compact('tenant', 'events', 'categories'));
    }

    public function show(\App\Models\Event $event)
    {
        if (!$event->isPublished()) {
            abort(404);
        }
        $event->load(['slots', 'category']);
        $tenant = app('tenant');
        return view('public.show', compact('tenant', 'event'));
    }
}
