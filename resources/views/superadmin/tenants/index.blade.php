@extends('superadmin.layouts.app')
@section('title', 'テナント一覧')
@section('header-actions')
<a href="{{ route('superadmin.tenants.create') }}"
   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white text-xs font-medium rounded-lg hover:bg-indigo-700 transition">
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    テナント新規作成
</a>
@endsection
@section('content')

{{-- サマリー --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="card p-5">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-3">総テナント数</p>
        <div class="flex items-baseline gap-1">
            <span class="text-3xl font-bold text-slate-800">{{ $tenants->total() }}</span>
            <span class="text-sm text-slate-400">社</span>
        </div>
    </div>
    <div class="card p-5">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-3">有効テナント</p>
        <div class="flex items-baseline gap-1">
            <span class="text-3xl font-bold text-emerald-600">{{ $tenants->filter(fn($t) => $t->status === 'active')->count() }}</span>
            <span class="text-sm text-slate-400">社</span>
        </div>
    </div>
    <div class="card p-5">
        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-3">停止中テナント</p>
        <div class="flex items-baseline gap-1">
            <span class="text-3xl font-bold text-rose-500">{{ $tenants->filter(fn($t) => $t->status !== 'active')->count() }}</span>
            <span class="text-sm text-slate-400">社</span>
        </div>
    </div>
</div>

<div class="card overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-100">
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">会社名</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">スラッグ</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">業種</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">ユーザー数</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">課金</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">ステータス</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">作成日</th>
                <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">公開URL</th>
                <th class="px-5 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($tenants as $tenant)
            <tr class="hover:bg-slate-50/60 transition">
                <td class="px-5 py-3.5 font-medium text-slate-800">{{ $tenant->company_name }}</td>
                <td class="px-5 py-3.5 font-mono text-xs text-slate-500">{{ $tenant->slug }}</td>
                <td class="px-5 py-3.5 text-slate-500">{{ $tenant->industry ?? '―' }}</td>
                <td class="px-5 py-3.5 text-slate-600">{{ $tenant->users->count() }} 名</td>
                <td class="px-5 py-3.5">
                    @php $sub = $tenant->subscriptions->first(); @endphp
                    @if($sub && $sub->active())
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">課金中</span>
                    @elseif($sub && $sub->canceled())
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">解約済</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-500">未契約</span>
                    @endif
                </td>
                <td class="px-5 py-3.5">
                    @if($tenant->status === 'active')
                        <span class="badge-published">有効</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-700">停止</span>
                    @endif
                </td>
                <td class="px-5 py-3.5 text-slate-400 text-xs">{{ $tenant->created_at->format('Y/m/d') }}</td>
                <td class="px-5 py-3.5">
                    <a href="{{ url($tenant->slug) }}" target="_blank"
                       class="text-xs text-slate-400 hover:text-indigo-600 font-mono transition">
                        /{{ $tenant->slug }}
                    </a>
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('superadmin.tenants.show', $tenant) }}" class="text-xs text-indigo-600 hover:text-indigo-700 font-medium">詳細</a>
                        <a href="{{ route('superadmin.tenants.edit', $tenant) }}" class="text-xs text-slate-600 hover:text-slate-700 font-medium">編集</a>
                        <form method="POST" action="{{ route('superadmin.tenants.toggle', $tenant) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="text-xs font-medium {{ $tenant->status === 'active' ? 'text-rose-500 hover:text-rose-600' : 'text-emerald-600 hover:text-emerald-700' }} transition"
                                onclick="return confirm('ステータスを変更しますか？')">
                                {{ $tenant->status === 'active' ? '停止' : '有効化' }}
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="px-5 py-12 text-center text-slate-400">テナントがありません</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $tenants->links() }}</div>
@endsection
