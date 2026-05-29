@extends('superadmin.layouts.app')
@section('title', $tenant->company_name . ' — 詳細')
@section('header-actions')
<a href="{{ route('superadmin.tenants.edit', $tenant) }}"
   class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
    編集
</a>
<a href="{{ route('superadmin.tenants.index') }}" class="text-xs text-slate-500 hover:text-slate-700">← テナント一覧</a>
@endsection
@section('content')

<div class="grid grid-cols-3 gap-5">

    {{-- 左：基本情報 --}}
    <div class="col-span-2 space-y-5">

        {{-- 基本情報カード --}}
        <div class="card p-6">
            <div class="flex items-start justify-between mb-5">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">{{ $tenant->company_name }}</h2>
                    <p class="text-xs font-mono text-slate-400 mt-0.5">{{ $tenant->slug }}</p>
                </div>
                <div class="flex items-center gap-2">
                    @if($tenant->status === 'active')
                        <span class="badge-published">有効</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-700">停止</span>
                    @endif
                    <form method="POST" action="{{ route('superadmin.tenants.toggle', $tenant) }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                            class="text-xs font-medium px-3 py-1.5 rounded-lg border transition {{ $tenant->status === 'active' ? 'border-rose-200 text-rose-600 hover:bg-rose-50' : 'border-emerald-200 text-emerald-700 hover:bg-emerald-50' }}"
                            onclick="return confirm('ステータスを変更しますか？')">
                            {{ $tenant->status === 'active' ? '停止する' : '有効化する' }}
                        </button>
                    </form>
                </div>
            </div>

            <dl class="grid grid-cols-2 gap-x-6 gap-y-4 text-sm">
                <div>
                    <dt class="text-xs text-slate-400 mb-0.5">業種</dt>
                    <dd class="text-slate-700">{{ $tenant->industry ?? '―' }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 mb-0.5">ブランドカラー</dt>
                    <dd class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded border border-slate-200 inline-block" style="background-color: {{ $tenant->color ?? '#4f46e5' }}"></span>
                        <span class="font-mono text-slate-700">{{ $tenant->color ?? '#4f46e5' }}</span>
                    </dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 mb-0.5">作成日</dt>
                    <dd class="text-slate-700">{{ $tenant->created_at->format('Y年m月d日') }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-slate-400 mb-0.5">通知メール</dt>
                    <dd class="text-slate-700">{{ $tenant->notify_email ?? '未設定' }}</dd>
                </div>
            </dl>
        </div>

        {{-- 利用統計 --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="card p-4 text-center">
                <p class="text-xs text-slate-500 mb-2">ユーザー数</p>
                <p class="text-2xl font-bold text-slate-800">{{ $tenant->users_count }}</p>
                <p class="text-xs text-slate-400">名</p>
            </div>
            <div class="card p-4 text-center">
                <p class="text-xs text-slate-500 mb-2">イベント数</p>
                <p class="text-2xl font-bold text-slate-800">{{ $tenant->events_count }}</p>
                <p class="text-xs text-slate-400">件</p>
            </div>
            <div class="card p-4 text-center">
                <p class="text-xs text-slate-500 mb-2">有効予約数</p>
                <p class="text-2xl font-bold text-indigo-600">{{ $activeReservations }}</p>
                <p class="text-xs text-slate-400">件</p>
            </div>
        </div>

        {{-- ユーザー一覧 --}}
        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-700">スタッフ一覧</h3>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100">
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">名前</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">メールアドレス</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">ロール</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-slate-500">登録日</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($tenant->users as $user)
                    <tr class="hover:bg-slate-50/60 transition">
                        <td class="px-5 py-3 font-medium text-slate-800">{{ $user->name }}</td>
                        <td class="px-5 py-3 text-slate-600">{{ $user->email }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $user->role === 'owner' ? 'bg-violet-100 text-violet-700' : '' }}
                                {{ $user->role === 'admin' ? 'bg-indigo-100 text-indigo-700' : '' }}
                                {{ $user->role === 'staff' ? 'bg-sky-100 text-sky-700' : '' }}
                                {{ $user->role === 'viewer' ? 'bg-slate-100 text-slate-600' : '' }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-slate-400 text-xs">{{ $user->created_at->format('Y/m/d') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- 右：課金情報 --}}
    <div class="space-y-5">
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-slate-700 mb-4">課金ステータス</h3>

            @if($subscription && $subscription->active())
                <div class="flex items-center gap-2 mb-4">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-sm font-medium text-emerald-700">有効（課金中）</span>
                </div>
                <dl class="space-y-3 text-xs">
                    <div>
                        <dt class="text-slate-400 mb-0.5">プラン</dt>
                        <dd class="text-slate-700 font-medium">スタンダード</dd>
                    </div>
                    @if($subscription->trial_ends_at)
                    <div>
                        <dt class="text-slate-400 mb-0.5">トライアル終了</dt>
                        <dd class="text-slate-700">{{ $subscription->trial_ends_at->format('Y/m/d') }}</dd>
                    </div>
                    @endif
                    @if($subscription->ends_at)
                    <div>
                        <dt class="text-slate-400 mb-0.5">解約予定日</dt>
                        <dd class="text-amber-600 font-medium">{{ $subscription->ends_at->format('Y/m/d') }}</dd>
                    </div>
                    @endif
                </dl>
            @elseif($subscription && $subscription->canceled())
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    <span class="text-sm font-medium text-amber-700">解約済み</span>
                </div>
            @else
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-2 h-2 rounded-full bg-slate-300"></span>
                    <span class="text-sm text-slate-500">未契約</span>
                </div>
                <p class="text-xs text-slate-400">Stripeサブスクリプションなし</p>
            @endif

            @if($tenant->stripe_id)
            <div class="mt-4 pt-4 border-t border-slate-100">
                <p class="text-xs text-slate-400 mb-1">Stripe顧客ID</p>
                <p class="font-mono text-xs text-slate-600 break-all mb-2">{{ $tenant->stripe_id }}</p>
                <a href="https://dashboard.stripe.com/customers/{{ $tenant->stripe_id }}"
                   target="_blank"
                   class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    Stripeで課金管理 →
                </a>
            </div>
            @endif
        </div>

        {{-- ロゴプレビュー --}}
        @if($tenant->logo_path)
        <div class="card p-5">
            <h3 class="text-sm font-semibold text-slate-700 mb-3">ロゴ</h3>
            <img src="{{ asset('storage/' . $tenant->logo_path) }}" alt="ロゴ" class="max-h-16 object-contain">
        </div>
        @endif
    </div>
</div>
@endsection
