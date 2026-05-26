@extends('public.layouts.app')
@section('title', '予約フォーム — ' . $event->title)
@section('content')
@php $brandColor = $tenant?->color ?? '#4f46e5'; @endphp

<div class="max-w-xl mx-auto px-5 py-8">
    <a href="{{ route('public.events.show', $event) }}" class="inline-flex items-center text-sm text-slate-500 hover:text-slate-700 mb-6">
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        イベント詳細へ
    </a>

    {{-- 予約枠サマリー --}}
    <div class="rounded-xl p-4 mb-6 text-white" style="background-color: {{ $brandColor }};">
        <p class="font-bold text-lg">{{ $event->title }}</p>
        <p class="text-white/80 text-sm mt-1">
            {{ $slot->date->format('Y年m月d日') }}
            {{ substr($slot->start_time, 0, 5) }}
            @if($slot->end_time) 〜 {{ substr($slot->end_time, 0, 5) }} @endif
            ／ 残り {{ $slot->remainingCapacity() }} 席
        </p>
    </div>

    {{-- エラー表示 --}}
    @if($errors->any())
    <div class="mb-5 flex items-start gap-3 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-sm">
        <svg class="w-4 h-4 mt-0.5 shrink-0 text-rose-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        <ul class="space-y-0.5">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    {{-- フォーム --}}
    <form method="POST" action="{{ route('public.book.store', [$event, $slot]) }}"
          class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        @csrf
        <div class="p-6 space-y-5">

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                    お名前 <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}" autocomplete="name"
                    class="field @error('name') field-error @enderror" required placeholder="山田 太郎">
                @error('name')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">ふりがな</label>
                <input type="text" name="kana" value="{{ old('kana') }}"
                    class="field @error('kana') field-error @enderror" placeholder="やまだ たろう（ひらがな）">
                @error('kana')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                    メールアドレス <span class="text-rose-500">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email') }}" autocomplete="email"
                    class="field @error('email') field-error @enderror" required placeholder="example@email.com">
                @error('email')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                    メールアドレス（確認） <span class="text-rose-500">*</span>
                </label>
                <input type="email" name="email_confirm" value="{{ old('email_confirm') }}" autocomplete="off"
                    class="field @error('email_confirm') field-error @enderror" required>
                @error('email_confirm')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">電話番号</label>
                <input type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel"
                    class="field @error('phone') field-error @enderror" placeholder="090-1234-5678">
                @error('phone')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">同伴者数（本人を除く）</label>
                <div class="flex items-center gap-2">
                    <input type="number" name="companions" value="{{ old('companions', 0) }}" min="0" max="99"
                        class="field max-w-[100px]">
                    <span class="text-sm text-slate-500">名</span>
                </div>
            </div>

            {{-- カスタム項目 --}}
            @foreach($fields as $field)
            @if(!$field->hidden)
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">
                    {{ $field->label }}
                    @if($field->required)<span class="text-rose-500">*</span>@endif
                </label>
                @if($field->type === 'textarea')
                    <textarea name="custom_{{ $field->id }}" rows="3"
                        class="field @error('custom_'.$field->id) field-error @enderror"
                        {{ $field->required ? 'required' : '' }}>{{ old('custom_'.$field->id) }}</textarea>
                @elseif($field->type === 'select')
                    <select name="custom_{{ $field->id }}" class="field" {{ $field->required ? 'required' : '' }}>
                        <option value="">選択してください</option>
                        @foreach($field->options ?? [] as $opt)
                        <option value="{{ $opt }}" {{ old('custom_'.$field->id) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                    </select>
                @else
                    <input type="{{ $field->type }}" name="custom_{{ $field->id }}"
                        value="{{ old('custom_'.$field->id) }}"
                        class="field @error('custom_'.$field->id) field-error @enderror"
                        {{ $field->required ? 'required' : '' }}>
                @endif
                @error('custom_'.$field->id)<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            @endif
            @endforeach
        </div>

        {{-- プライバシーポリシー同意 --}}
        @if($tenant?->privacy_policy_url)
        <div class="px-6 pt-2">
            <label class="flex items-start gap-2 cursor-pointer">
                <input type="checkbox" name="privacy_consent" value="1"
                    class="mt-0.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                    {{ old('privacy_consent') ? 'checked' : '' }} required>
                <span class="text-xs text-slate-600">
                    <a href="{{ $tenant->privacy_policy_url }}" target="_blank" rel="noopener noreferrer"
                        class="underline text-indigo-600 hover:text-indigo-800">プライバシーポリシー</a>
                    に同意する <span class="text-rose-500">*</span>
                </span>
            </label>
            @error('privacy_consent')<p class="text-rose-500 text-xs mt-1 ml-5">{{ $message }}</p>@enderror
        </div>
        @endif

        {{-- 送信 --}}
        <div class="px-6 pb-6 pt-4">
            <button type="submit"
                class="w-full py-3.5 rounded-xl text-sm font-bold text-white transition brand-hover"
                style="background-color: {{ $brandColor }};">
                予約を確定する
            </button>
            <p class="text-xs text-slate-400 text-center mt-3">
                ご入力いただいたメールアドレスへ確認メールを送信します
            </p>
        </div>
    </form>
</div>
@endsection
