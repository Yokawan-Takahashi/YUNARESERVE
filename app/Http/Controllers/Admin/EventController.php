<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with('category')->latest()->paginate(20);
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        $categories = Category::where('active', true)->orderBy('sort')->get();
        return view('admin.events.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'nullable|integer',
            'title'       => 'required|string|max:200',
            'description' => 'nullable|string',
            'location'    => 'nullable|string|max:200',
            'target'      => 'nullable|string|max:200',
            'fee'         => 'nullable|integer|min:0',
            'items'       => 'nullable|string',
            'notes'       => 'nullable|string',
            'status'      => 'required|in:draft,published',
        ]);
        Event::create($data);
        return redirect()->route('admin.events.index')->with('success', 'イベントを作成しました');
    }

    public function show(Event $event)
    {
        $event->load('slots');
        return view('admin.events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        $categories = Category::where('active', true)->orderBy('sort')->get();
        $event->load('slots');
        return view('admin.events.edit', compact('event', 'categories'));
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'category_id' => 'nullable|integer',
            'title'       => 'required|string|max:200',
            'description' => 'nullable|string',
            'location'    => 'nullable|string|max:200',
            'target'      => 'nullable|string|max:200',
            'fee'         => 'nullable|integer|min:0',
            'items'       => 'nullable|string',
            'notes'       => 'nullable|string',
            'status'      => 'required|in:draft,published',
        ]);
        $event->update($data);
        return redirect()->route('admin.events.edit', $event)->with('success', 'イベントを更新しました');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'イベントを削除しました');
    }
}
