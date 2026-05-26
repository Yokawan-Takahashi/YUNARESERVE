@extends('admin.layouts.app')
@section('title', '予約詳細')
@section('header-actions')
<div class="flex items-center gap-3">
    @php $level = ['viewer'=>1,'staff'=>2,'admin'=>3,'owner'=>4,'superadmin'=>99][auth()->user()?->role ?? 'viewer'] ?? 0; @endphp
    @if($level >= 2)
    <a href="{{ route('admin.reservations.edit', $reservation) }}"
       class="px-3 py-1.5 text-xs font-medium bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition">
        編集
    </a>
    @if($reservation->status === 'reserved')
    <form method="POST" action="{{ route('admin.reservations.resend', $reservation) }}" class="inline">
        @csrf
        <button type="submit" onclick="return confirm('確認メールを再送しますか？')"
            class="px-3 py-1.5 text-xs font-medium bg-white border border-slate-300 text-slate-700 rounded-lg hover:bg-slate-50 transition">
            メール再送
        </button>
    </form>
    @endif
    @endif
    <a href="{{ route('admin.reservations.index') }}" class="text-xs text-slate-500 hover:text-slate-700">← 予約一覧</a>
</div>
@endsection
@section('content')

<div class="grid grid-cols-3 gap-5">

    {{-- 左：予約情報 --}}
    <div class="col-span-2 space-y-5">

        {{-- ヘッダーカード --}}
        <div class="card p-5">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <p class="text-xs text-slate-400 mb-1">予約番号</p>
                    <p class="text-xl font-mono font-bold text-slate-800 tracking-widest">{{ $reservation->code }}</p>
                </div>
                @if($reservation->status === 'reserved')
                    <span class="badge-reserved text-sm px-3 py-1">予約済</span>
                @else
                    <span class="badge-cancelled text-sm px-3 py-1">キャンセル</span>
                @endif
            </div>

            <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                <div>
                    <dt class="text-xs text-slate-400 mb-0.5">イベント</dt>
                    <dd class="font-medium text-slate-800">{{ $reservation->event->title }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 mb-0.5">日時</dt>
                    <dd class="font-medium text-slate-800">
                        {{ $reservation->slot->date->format('Y年m月d日') }}
                        {{ substr($reservation->slot->start_time, 0, 5) }}
                        @if($reservation->slot->end_time) 〜 {{ substr($reservation->slot->end_time, 0, 5) }} @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 mb-0.5">お名前</dt>
                    <dd class="font-medium text-slate-800">
                        {{ $reservation->name }} 様
                        @if($reservation->kana)<span class="text-slate-500 font-normal text-xs ml-1">（{{ $reservation->kana }}）</span>@endif
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 mb-0.5">同伴者数</dt>
                    <dd class="font-medium text-slate-800">{{ $reservation->companions }} 名</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 mb-0.5">メールアドレス</dt>
                    <dd class="text-slate-700">{{ $reservation->email }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 mb-0.5">電話番号</dt>
                    <dd class="text-slate-700">{{ $reservation->phone ?: '―' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 mb-0.5">受付日時</dt>
                    <dd class="text-slate-600">{{ $reservation->created_at->format('Y/m/d H:i') }}</dd>
                </div>
            </dl>

            @if($reservation->answers->isNotEmpty())
            <div class="mt-5 pt-5 border-t border-slate-100">
                <p class="text-xs font-medium text-slate-500 mb-3">カスタム項目の回答</p>
                <dl class="space-y-2">
                    @foreach($reservation->answers as $a)
                    <div class="flex gap-4 text-sm">
                        <dt class="text-slate-400 w-32 shrink-0">{{ $a->field_label }}</dt>
                        <dd class="text-slate-700">{{ $a->answer }}</dd>
                    </div>
                    @endforeach
                </dl>
            </div>
            @endif
        </div>

        {{-- ステータス変更 --}}
        <div class="card p-5">
            <h2 class="text-sm font-semibold text-slate-700 mb-4">ステータス変更</h2>
            <form method="POST" action="{{ route('admin.reservations.status', $reservation) }}" class="flex items-center gap-3">
                @csrf @method('PATCH')
                <select name="status" class="field max-w-[160px]">
                    <option value="reserved"  {{ $reservation->status === 'reserved'  ? 'selected' : '' }}>予約済</option>
                    <option value="cancelled" {{ $reservation->status === 'cancelled' ? 'selected' : '' }}>キャンセル</option>
                </select>
                <button type="submit" onclick="return confirm('ステータスを変更しますか？')"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                    変更する
                </button>
            </form>
        </div>
    </div>

    {{-- 右：メモ --}}
    <div class="space-y-5">
        <div class="card p-5">
            <h2 class="text-sm font-semibold text-slate-700 mb-4">スタッフメモ</h2>
            <form method="POST" action="{{ route('admin.reservations.memo', $reservation) }}">
                @csrf @method('PATCH')
                <textarea name="memo" rows="8" placeholder="スタッフ向けのメモを入力…"
                    class="field resize-none text-sm mb-3">{{ old('memo', $reservation->memo) }}</textarea>
                <button type="submit"
                    class="w-full py-2 bg-slate-700 text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition">
                    保存する
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
