<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>予約フォーム — {{ $event->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <header class="bg-white shadow px-6 py-4">
        <a href="{{ route('public.events.show', $event) }}" class="text-indigo-600 hover:underline text-sm">← イベント詳細へ戻る</a>
        <h1 class="text-xl font-bold mt-1">{{ $tenant?->company_name ?? config('app.name') }}</h1>
    </header>

    <main class="max-w-lg mx-auto p-6">
        <h2 class="text-xl font-bold mb-1">予約フォーム</h2>
        <p class="text-sm text-gray-600 mb-1">{{ $event->title }}</p>
        <p class="text-sm text-indigo-700 mb-6">
            {{ $slot->date->format('Y年m月d日') }} {{ substr($slot->start_time, 0, 5) }}
            @if($slot->end_time) 〜 {{ substr($slot->end_time, 0, 5) }} @endif
            （残{{ $slot->remainingCapacity() }}席）
        </p>

        @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded text-sm">
            <ul class="list-disc pl-4 space-y-1">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('public.book.store', [$event, $slot]) }}" class="bg-white rounded shadow p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1">お名前 <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2 @error('name') border-red-400 @enderror" required>
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">ふりがな</label>
                <input type="text" name="kana" value="{{ old('kana') }}" class="w-full border rounded px-3 py-2 @error('kana') border-red-400 @enderror" placeholder="ひらがなで入力">
                @error('kana')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">メールアドレス <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2 @error('email') border-red-400 @enderror" required>
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">メールアドレス（確認） <span class="text-red-500">*</span></label>
                <input type="email" name="email_confirm" value="{{ old('email_confirm') }}" class="w-full border rounded px-3 py-2 @error('email_confirm') border-red-400 @enderror" required>
                @error('email_confirm')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">電話番号</label>
                <input type="tel" name="phone" value="{{ old('phone') }}" class="w-full border rounded px-3 py-2 @error('phone') border-red-400 @enderror" placeholder="例: 090-1234-5678">
                @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">同伴者数（本人を除く）</label>
                <input type="number" name="companions" value="{{ old('companions', 0) }}" min="0" max="99" class="w-full border rounded px-3 py-2">
            </div>

            {{-- カスタム項目 --}}
            @foreach($fields as $field)
                @if(!$field->hidden)
                <div>
                    <label class="block text-sm font-medium mb-1">
                        {{ $field->label }}
                        @if($field->required)<span class="text-red-500">*</span>@endif
                    </label>
                    @if($field->type === 'textarea')
                        <textarea name="custom_{{ $field->id }}" rows="3"
                            class="w-full border rounded px-3 py-2 @error('custom_'.$field->id) border-red-400 @enderror"
                            {{ $field->required ? 'required' : '' }}>{{ old('custom_'.$field->id) }}</textarea>
                    @elseif($field->type === 'select')
                        <select name="custom_{{ $field->id }}"
                            class="w-full border rounded px-3 py-2"
                            {{ $field->required ? 'required' : '' }}>
                            <option value="">選択してください</option>
                            @foreach($field->options ?? [] as $opt)
                                <option value="{{ $opt }}" {{ old('custom_'.$field->id) === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="{{ $field->type }}" name="custom_{{ $field->id }}"
                            value="{{ old('custom_'.$field->id) }}"
                            class="w-full border rounded px-3 py-2 @error('custom_'.$field->id) border-red-400 @enderror"
                            {{ $field->required ? 'required' : '' }}>
                    @endif
                    @error('custom_'.$field->id)<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                @endif
            @endforeach

            <div class="pt-2">
                <button type="submit" class="w-full bg-indigo-600 text-white py-3 rounded font-bold hover:bg-indigo-700">
                    予約する
                </button>
            </div>
        </form>
    </main>
</body>
</html>
