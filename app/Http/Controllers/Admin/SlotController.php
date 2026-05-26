<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SlotController extends Controller
{
    public function store(Request $request, \App\Models\Event $event)
    {
        $data = $request->validate([
            'date'       => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'nullable|date_format:H:i|after:start_time',
            'capacity'   => 'required|integer|min:1',
        ]);
        $event->slots()->create($data);
        return redirect()->route('admin.events.edit', $event)->with('success', '枠を追加しました');
    }

    public function update(Request $request, \App\Models\Event $event, \App\Models\Slot $slot)
    {
        $data = $request->validate([
            'date'       => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'nullable|date_format:H:i|after:start_time',
            'capacity'   => 'required|integer|min:1',
            'status'     => 'required|in:open,full,closed',
        ]);
        $slot->update($data);
        return redirect()->route('admin.events.edit', $event)->with('success', '枠を更新しました');
    }

    public function toggleStatus(\App\Models\Event $event, \App\Models\Slot $slot)
    {
        $newStatus = $slot->status === 'closed' ? 'open' : 'closed';
        $slot->update(['status' => $newStatus]);
        $label = $newStatus === 'closed' ? '受付を停止しました' : '受付を再開しました';
        return back()->with('success', $label);
    }

    public function destroy(\App\Models\Event $event, \App\Models\Slot $slot)
    {
        $slot->delete();
        return redirect()->route('admin.events.edit', $event)->with('success', '枠を削除しました');
    }
}
