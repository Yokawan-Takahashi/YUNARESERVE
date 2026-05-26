<div class="grid grid-cols-2 gap-x-6 gap-y-5">
    <div class="col-span-2">
        <label class="block text-xs font-medium text-slate-600 mb-1.5">タイトル <span class="text-rose-500">*</span></label>
        <input type="text" name="title" value="{{ old('title', $event?->title) }}"
            class="field @error('title') field-error @enderror" required placeholder="例：春のヨガ体験クラス">
        @error('title')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-xs font-medium text-slate-600 mb-1.5">カテゴリ</label>
        <select name="category_id" class="field">
            <option value="">未選択</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ old('category_id', $event?->category_id) == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
            </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-xs font-medium text-slate-600 mb-1.5">ステータス</label>
        <select name="status" class="field">
            <option value="draft"     {{ old('status', $event?->status ?? 'draft') === 'draft'     ? 'selected' : '' }}>下書き（非公開）</option>
            <option value="published" {{ old('status', $event?->status)             === 'published' ? 'selected' : '' }}>公開中</option>
        </select>
    </div>

    <div>
        <label class="block text-xs font-medium text-slate-600 mb-1.5">開催場所</label>
        <input type="text" name="location" value="{{ old('location', $event?->location) }}" class="field" placeholder="例：渋谷区○○センター3F">
    </div>

    <div>
        <label class="block text-xs font-medium text-slate-600 mb-1.5">対象者</label>
        <input type="text" name="target" value="{{ old('target', $event?->target) }}" class="field" placeholder="例：18歳以上の方">
    </div>

    <div>
        <label class="block text-xs font-medium text-slate-600 mb-1.5">参加費（円）</label>
        <input type="number" name="fee" value="{{ old('fee', $event?->fee ?? 0) }}" min="0" class="field" placeholder="0">
        <p class="text-xs text-slate-400 mt-1">0 の場合は「無料」と表示されます</p>
    </div>

    <div class="col-span-2">
        <label class="block text-xs font-medium text-slate-600 mb-1.5">説明</label>
        <textarea name="description" rows="4" class="field" placeholder="イベントの内容・魅力を入力してください">{{ old('description', $event?->description) }}</textarea>
    </div>

    <div>
        <label class="block text-xs font-medium text-slate-600 mb-1.5">持ち物・準備</label>
        <textarea name="items" rows="3" class="field" placeholder="例：動きやすい服装、タオルをご持参ください">{{ old('items', $event?->items) }}</textarea>
    </div>

    <div>
        <label class="block text-xs font-medium text-slate-600 mb-1.5">備考・注意事項</label>
        <textarea name="notes" rows="3" class="field" placeholder="例：駐車場はございません">{{ old('notes', $event?->notes) }}</textarea>
    </div>
</div>
