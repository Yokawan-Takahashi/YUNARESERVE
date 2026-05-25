@extends('admin.layouts.app')
@section('title', 'イベント編集')
@section('content')
<h1 class="text-xl font-bold mb-4">イベント編集：{{ $event->title }}</h1>

<form method="POST" action="{{ route('admin.events.update', $event) }}" class="bg-white rounded shadow p-6 max-w-2xl space-y-4 mb-8">
    @csrf @method('PUT')
    @include('admin.events._form', ['event' => $event])
    <div class="flex gap-3">
        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">更新</button>
        <a href="{{ route('admin.events.index') }}" class="px-6 py-2 rounded border hover:bg-gray-50">一覧へ</a>
    </div>
</form>

{{-- 枠管理 --}}
<div class="bg-white rounded shadow p-6 max-w-2xl">
    <h2 class="text-lg font-bold mb-4">予約枠</h2>

    @if($event->slots->count())
    <table class="w-full text-sm mb-6">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-3 py-2 text-left">日付</th>
                <th class="px-3 py-2 text-left">開始</th>
                <th class="px-3 py-2 text-left">終了</th>
                <th class="px-3 py-2 text-left">定員</th>
                <th class="px-3 py-2 text-left">予約済</th>
                <th class="px-3 py-2 text-left">状態</th>
                <th class="px-3 py-2"></th>
            </tr>
        </thead>
        <tbody>
            @foreach($event->slots as $slot)
            <tr class="border-b">
                <td class="px-3 py-2">{{ $slot->date->format('Y/m/d') }}</td>
                <td class="px-3 py-2">{{ substr($slot->start_time, 0, 5) }}</td>
                <td class="px-3 py-2">{{ $slot->end_time ? substr($slot->end_time, 0, 5) : '―' }}</td>
                <td class="px-3 py-2">{{ $slot->capacity }}</td>
                <td class="px-3 py-2">{{ $slot->reserved_count }}</td>
                <td class="px-3 py-2">{{ $slot->status }}</td>
                <td class="px-3 py-2">
                    <form method="POST" action="{{ route('admin.events.slots.destroy', [$event, $slot]) }}"
                          onsubmit="return confirm('この枠を削除しますか？')">
                        @csrf @method('DELETE')
                        <button class="text-red-600 hover:underline text-xs">削除</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <h3 class="font-semibold mb-2 text-sm">枠を追加</h3>
    <form method="POST" action="{{ route('admin.events.slots.store', $event) }}" class="grid grid-cols-2 gap-3">
        @csrf
        <div>
            <label class="block text-xs font-medium mb-1">日付 <span class="text-red-500">*</span></label>
            <input type="date" name="date" class="w-full border rounded px-2 py-1 text-sm" required>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">定員 <span class="text-red-500">*</span></label>
            <input type="number" name="capacity" min="1" class="w-full border rounded px-2 py-1 text-sm" required>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">開始時間 <span class="text-red-500">*</span></label>
            <input type="time" name="start_time" class="w-full border rounded px-2 py-1 text-sm" required>
        </div>
        <div>
            <label class="block text-xs font-medium mb-1">終了時間</label>
            <input type="time" name="end_time" class="w-full border rounded px-2 py-1 text-sm">
        </div>
        <div class="col-span-2">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-1.5 rounded text-sm hover:bg-indigo-700">枠を追加</button>
        </div>
    </form>
</div>
@endsection
