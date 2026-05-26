@extends('admin.layouts.app')
@section('title', 'スタッフ管理')
@section('content')

<div class="grid grid-cols-5 gap-6">

    {{-- スタッフ一覧 --}}
    <div class="col-span-3">
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-700">スタッフ一覧</h2>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">名前</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">メールアドレス</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">ロール</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($staff as $member)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-bold text-indigo-600 shrink-0">
                                    {{ mb_substr($member->name, 0, 1) }}
                                </div>
                                <span class="font-medium text-slate-800">{{ $member->name }}</span>
                                @if($member->id === auth()->id())
                                    <span class="text-xs text-slate-400">（自分）</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-slate-600">{{ $member->email }}</td>
                        <td class="px-5 py-3.5">
                            @if($member->role === 'owner')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-violet-100 text-violet-700">owner</span>
                            @elseif($member->role === 'admin')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700">admin</span>
                            @elseif($member->role === 'staff')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-sky-100 text-sky-700">staff</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-600">viewer</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @if($member->role !== 'owner')
                            <div class="flex items-center justify-end gap-3">
                                {{-- ロール変更 --}}
                                <form method="POST" action="{{ route('admin.staff.role', $member) }}" class="flex items-center gap-1">
                                    @csrf @method('PATCH')
                                    <select name="role" class="text-xs rounded-lg border-slate-300 py-1 pr-6 text-slate-600 focus:border-indigo-400 focus:ring-indigo-200">
                                        <option value="admin"  {{ $member->role === 'admin'  ? 'selected' : '' }}>admin</option>
                                        <option value="staff"  {{ $member->role === 'staff'  ? 'selected' : '' }}>staff</option>
                                        <option value="viewer" {{ $member->role === 'viewer' ? 'selected' : '' }}>viewer</option>
                                    </select>
                                    <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium px-2">変更</button>
                                </form>
                                {{-- 削除 --}}
                                @if($member->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.staff.destroy', $member) }}"
                                      onsubmit="return confirm('「{{ $member->name }}」を削除しますか？')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-rose-500 hover:text-rose-600 font-medium">削除</button>
                                </form>
                                @endif
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ロール説明 --}}
        <div class="mt-4 p-4 bg-slate-50 rounded-xl border border-slate-100 text-xs text-slate-500 space-y-1.5">
            <p><span class="font-semibold text-violet-700">owner</span>：全機能。テナント設定・課金管理。自動付与（変更不可）</p>
            <p><span class="font-semibold text-indigo-700">admin</span>：owner 以外の全機能（設定・課金含む）</p>
            <p><span class="font-semibold text-sky-700">staff</span>：予約・イベント管理。設定変更不可</p>
            <p><span class="font-semibold text-slate-600">viewer</span>：閲覧のみ（予約・イベントの表示）</p>
        </div>
    </div>

    {{-- 追加フォーム --}}
    <div class="col-span-2">
        <div class="card p-5">
            <h2 class="text-sm font-semibold text-slate-700 mb-4">スタッフを追加</h2>
            <form method="POST" action="{{ route('admin.staff.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">名前 <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="field" required placeholder="山田 花子">
                    @error('name')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">メールアドレス <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" class="field" required placeholder="staff@example.com">
                    @error('email')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">初期パスワード <span class="text-rose-500">*</span></label>
                    <input type="text" name="password" value="{{ old('password') }}" class="field font-mono" required minlength="8" placeholder="8文字以上">
                    <p class="text-xs text-slate-400 mt-1">本人に別途お知らせください</p>
                    @error('password')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1.5">ロール <span class="text-rose-500">*</span></label>
                    <select name="role" class="field">
                        <option value="staff"  {{ old('role') === 'staff'  ? 'selected' : '' }}>staff — 予約・イベント管理</option>
                        <option value="admin"  {{ old('role') === 'admin'  ? 'selected' : '' }}>admin — 全機能（設定除く）</option>
                        <option value="viewer" {{ old('role') === 'viewer' ? 'selected' : '' }}>viewer — 閲覧のみ</option>
                    </select>
                    @error('role')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="w-full py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                    追加する
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
