<div>
    <label class="block text-sm font-medium mb-1">タイトル <span class="text-red-500">*</span></label>
    <input type="text" name="title" value="{{ old('title', $event?->title) }}" class="w-full border rounded px-3 py-2" required>
    @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
</div>
<div>
    <label class="block text-sm font-medium mb-1">カテゴリ</label>
    <select name="category_id" class="w-full border rounded px-3 py-2">
        <option value="">未選択</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ old('category_id', $event?->category_id) == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
            </option>
        @endforeach
    </select>
</div>
<div>
    <label class="block text-sm font-medium mb-1">説明</label>
    <textarea name="description" rows="4" class="w-full border rounded px-3 py-2">{{ old('description', $event?->description) }}</textarea>
</div>
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium mb-1">開催場所</label>
        <input type="text" name="location" value="{{ old('location', $event?->location) }}" class="w-full border rounded px-3 py-2">
    </div>
    <div>
        <label class="block text-sm font-medium mb-1">対象者</label>
        <input type="text" name="target" value="{{ old('target', $event?->target) }}" class="w-full border rounded px-3 py-2">
    </div>
</div>
<div>
    <label class="block text-sm font-medium mb-1">参加費（円）</label>
    <input type="number" name="fee" value="{{ old('fee', $event?->fee ?? 0) }}" min="0" class="w-full border rounded px-3 py-2">
</div>
<div>
    <label class="block text-sm font-medium mb-1">持ち物・準備</label>
    <textarea name="items" rows="2" class="w-full border rounded px-3 py-2">{{ old('items', $event?->items) }}</textarea>
</div>
<div>
    <label class="block text-sm font-medium mb-1">備考・注意事項</label>
    <textarea name="notes" rows="2" class="w-full border rounded px-3 py-2">{{ old('notes', $event?->notes) }}</textarea>
</div>
<div>
    <label class="block text-sm font-medium mb-1">ステータス</label>
    <select name="status" class="w-full border rounded px-3 py-2">
        <option value="draft" {{ old('status', $event?->status ?? 'draft') === 'draft' ? 'selected' : '' }}>下書き</option>
        <option value="published" {{ old('status', $event?->status) === 'published' ? 'selected' : '' }}>公開</option>
    </select>
</div>
