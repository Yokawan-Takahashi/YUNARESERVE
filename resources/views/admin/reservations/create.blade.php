@extends('admin.layouts.app')
@section('title', '予約手動登録')
@section('header-actions')
<a href="{{ route('admin.reservations.index') }}" class="text-xs text-slate-500 hover:text-slate-700">← 予約一覧</a>
@endsection
@section('content')

<div class="max-w-2xl"
     x-data="{
         eventId: '{{ old('event_id') }}',
         slotId: '{{ old('slot_id') }}',
         eventsData: {{ Js::from($eventsJson) }},
         get slots() {
             const e = this.eventsData.find(e => String(e.id) === String(this.eventId));
             return e ? e.slots : [];
         }
     }">

    <div class="card p-6">
        <h2 class="text-sm font-semibold text-slate-700 mb-5">予約情報を入力</h2>

        <form method="POST" action="{{ route('admin.reservations.store') }}" class="space-y-5">
            @csrf

            {{-- イベント選択 --}}
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1.5">イベント <span class="text-rose-500">*</span></label>
                <select name="event_id" x-model="eventId" @change="slotId = ''" class="field @error('event_id') field-error @enderror" required>
                    <option value="">イベントを選択してください</option>
                    @foreach(json_decode(json_encode($eventsJson), true) as $ev)
                    <option value="{{ $ev['id'] }}" {{ old('event_id') == $ev['id'] ? 'selected' : '' }}>{{ $ev['title'] }}</option>
                    @endforeach
                </select>
                @error('event_id')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            {{-- 枠選択 --}}
            <div x-show="eventId">
                <label class="block text-xs font-medium text-slate-600 mb-1.5">予約枠 <span class="text-rose-500">*</span></label>
                <select name="slot_id" x-model="slotId" class="field @error('slot_id') field-error @enderror" required>
                    <option value="">枠を選択してください</option>
                    <template x-for="slot in slots" :key="slot.id">
                        <option :value="slot.id"
                                :selected="String(slot.id) === String(slotId)"
                                :disabled="slot.status === 'closed'"
                                x-text="slot.label + (slot.status === 'closed' ? '（受付停止）' : slot.remaining <= 0 ? '（満席）' : '（残' + slot.remaining + '）')">
                        </option>
                    </template>
                </select>
                <p class="text-xs text-slate-400 mt-1">受付停止中の枠は選択できません。</p>
                @error('slot_id')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="border-t border-slate-100 pt-5 space-y-5">

                {{-- お名前 --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">お名前 <span class="text-rose-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="field @error('name') field-error @enderror" required placeholder="山田 太郎">
                        @error('name')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">ふりがな</label>
                        <input type="text" name="kana" value="{{ old('kana') }}" class="field @error('kana') field-error @enderror" placeholder="やまだ たろう">
                        @error('kana')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- メール --}}
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">メールアドレス <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" class="field @error('email') field-error @enderror" required placeholder="example@email.com">
                    @error('email')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- 電話・同伴者 --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">電話番号</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" class="field @error('phone') field-error @enderror" placeholder="090-1234-5678">
                        @error('phone')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1.5">同伴者数（本人除く）</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="companions" value="{{ old('companions', 0) }}" min="0" max="99" class="field max-w-[100px]">
                            <span class="text-sm text-slate-500">名</span>
                        </div>
                    </div>
                </div>

                {{-- スタッフメモ --}}
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">スタッフメモ（内部用）</label>
                    <textarea name="memo" rows="3" class="field resize-none text-sm" placeholder="電話予約・内部向けメモなど">{{ old('memo') }}</textarea>
                </div>

                {{-- メール送信オプション --}}
                <div class="flex items-center gap-3 bg-slate-50 rounded-xl px-4 py-3">
                    <input type="hidden" name="send_email" value="0">
                    <input type="checkbox" name="send_email" id="send_email" value="1"
                        {{ old('send_email', '1') == '1' ? 'checked' : '' }}
                        class="rounded border-slate-300 text-indigo-600 w-4 h-4">
                    <label for="send_email" class="text-sm text-slate-700 cursor-pointer">
                        予約確認メールを送信する
                    </label>
                    <p class="text-xs text-slate-400 ml-auto">チェックを外すと顧客へのメール通知をスキップします</p>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                    予約を登録する
                </button>
                <a href="{{ route('admin.reservations.index') }}" class="px-6 py-2.5 bg-slate-100 text-slate-600 text-sm font-medium rounded-lg hover:bg-slate-200 transition">
                    キャンセル
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
