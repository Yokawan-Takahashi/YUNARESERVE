@extends('admin.layouts.app')
@section('title', 'イベント編集')
@section('header-actions')
<a href="{{ route('admin.events.index') }}" class="text-xs text-slate-500 hover:text-slate-700">← イベント一覧</a>
@endsection
@section('content')
<div class="max-w-5xl space-y-5">

    {{-- イベント編集フォーム --}}
    <div class="card p-6">
        <h2 class="text-sm font-semibold text-slate-700 mb-5">イベント情報</h2>
        <form method="POST" action="{{ route('admin.events.update', $event) }}">
            @csrf @method('PUT')
            @include('admin.events._form', ['event' => $event])
            <div class="flex gap-3 mt-6 pt-5 border-t border-slate-100">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                    更新する
                </button>
                <a href="{{ route('admin.events.index') }}" class="px-6 py-2 bg-slate-100 text-slate-600 text-sm font-medium rounded-lg hover:bg-slate-200 transition">
                    一覧へ
                </a>
            </div>
        </form>
    </div>

    {{-- 予約枠管理 --}}
    <div class="card overflow-hidden" x-data="{ editing: null }">
        <style>[x-cloak]{display:none!important}</style>
        <div class="px-5 py-4 border-b border-slate-100">
            <h2 class="text-sm font-semibold text-slate-700">予約枠の管理</h2>
        </div>

        @if($event->slots->count())
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-100">
                    <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">日付</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">開始</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">終了</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">定員</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">予約済</th>
                    <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">状態</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($event->slots as $slot)
                {{-- 通常行 --}}
                <tr class="hover:bg-slate-50/60 transition border-t border-slate-100">
                    <td class="px-5 py-3 text-slate-700">{{ $slot->date->format('Y/m/d') }}</td>
                    <td class="px-5 py-3 text-slate-600">{{ substr($slot->start_time, 0, 5) }}</td>
                    <td class="px-5 py-3 text-slate-600">{{ $slot->end_time ? substr($slot->end_time, 0, 5) : '―' }}</td>
                    <td class="px-5 py-3 text-slate-600">{{ $slot->capacity }}</td>
                    <td class="px-5 py-3">
                        <span class="{{ $slot->reserved_count > 0 ? 'text-indigo-600 font-medium' : 'text-slate-400' }}">
                            {{ $slot->reserved_count }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        @if($slot->status === 'full')
                            <span class="badge-full">満席</span>
                        @elseif($slot->status === 'open')
                            <span class="badge-reserved">受付中</span>
                        @else
                            <span class="badge-cancelled">受付停止</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-3">
                            <button type="button"
                                @click="editing = (editing === {{ $slot->id }}) ? null : {{ $slot->id }}"
                                class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">
                                <span x-text="editing === {{ $slot->id }} ? '閉じる' : '編集'">編集</span>
                            </button>
                            <form method="POST" action="{{ route('admin.events.slots.toggle', [$event, $slot]) }}">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs font-medium {{ $slot->status === 'closed' ? 'text-emerald-600 hover:text-emerald-700' : 'text-amber-600 hover:text-amber-700' }}">
                                    {{ $slot->status === 'closed' ? '受付再開' : '受付停止' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.events.slots.destroy', [$event, $slot]) }}" onsubmit="return confirm('この枠を削除しますか？')">
                                @csrf @method('DELETE')
                                <button class="text-xs text-rose-500 hover:text-rose-600 font-medium">削除</button>
                            </form>
                        </div>
                    </td>
                </tr>
                {{-- インライン編集行 --}}
                <tr x-show="editing === {{ $slot->id }}" x-cloak class="bg-indigo-50 border-t border-indigo-100">
                    <td colspan="7" class="px-5 py-4">
                        <form method="POST" action="{{ route('admin.events.slots.update', [$event, $slot]) }}"
                              class="grid grid-cols-5 gap-3 items-end">
                            @csrf @method('PUT')
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">日付</label>
                                <input type="date" name="date" value="{{ $slot->date->format('Y-m-d') }}" class="field" required>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">開始時間</label>
                                <input type="time" name="start_time" value="{{ substr($slot->start_time, 0, 5) }}" class="field" required>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">終了時間</label>
                                <input type="time" name="end_time" value="{{ $slot->end_time ? substr($slot->end_time, 0, 5) : '' }}" class="field">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">定員</label>
                                <input type="number" name="capacity" value="{{ $slot->capacity }}" min="1" class="field" required>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1.5">ステータス</label>
                                <select name="status" class="field">
                                    <option value="open"   {{ $slot->status === 'open'   ? 'selected' : '' }}>受付中</option>
                                    <option value="full"   {{ $slot->status === 'full'   ? 'selected' : '' }}>満席</option>
                                    <option value="closed" {{ $slot->status === 'closed' ? 'selected' : '' }}>受付停止</option>
                                </select>
                            </div>
                            <div class="col-span-5 flex gap-2 pt-1">
                                <button type="submit" class="px-4 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition">
                                    更新する
                                </button>
                                <button type="button" @click="editing = null"
                                    class="px-4 py-1.5 bg-slate-100 text-slate-600 text-xs font-medium rounded-lg hover:bg-slate-200 transition">
                                    キャンセル
                                </button>
                            </div>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p class="px-5 py-8 text-center text-sm text-slate-400">枠がありません。下のフォームから追加してください。</p>
        @endif

        {{-- 枠追加フォーム --}}
        <div class="px-5 py-5 bg-slate-50 border-t border-slate-100">
            <h3 class="text-xs font-semibold text-slate-600 mb-3">枠を追加</h3>
            <form method="POST" action="{{ route('admin.events.slots.store', $event) }}" class="grid grid-cols-5 gap-3 items-end">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">日付 <span class="text-rose-500">*</span></label>
                    <input type="date" name="date" class="field" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">開始時間 <span class="text-rose-500">*</span></label>
                    <input type="time" name="start_time" class="field" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">終了時間</label>
                    <input type="time" name="end_time" class="field">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1.5">定員 <span class="text-rose-500">*</span></label>
                    <input type="number" name="capacity" min="1" class="field" required placeholder="10">
                </div>
                <div>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                        追加する
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
