@extends('superadmin.layouts.app')
@section('title', 'お問い合わせ一覧')
@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-slate-800">お問い合わせ一覧</h1>
        <p class="text-xs text-slate-400 mt-0.5">{{ $inquiries->total() }}件 ／ 未対応 {{ $inquiries->getCollection()->where('contacted_at', null)->count() }}件</p>
    </div>
</div>

<div class="card overflow-hidden">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">受信日時</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">会社名</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">担当者</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">メール</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">業種</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">対応状況</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($inquiries as $inquiry)
            <tr x-data="{ open: false }" class="hover:bg-slate-50 cursor-pointer" @click="open = !open">
                <td class="px-4 py-3 text-slate-500 whitespace-nowrap">
                    {{ $inquiry->created_at->format('m/d H:i') }}
                </td>
                <td class="px-4 py-3 font-medium text-slate-800">{{ $inquiry->company_name }}</td>
                <td class="px-4 py-3 text-slate-600">{{ $inquiry->contact_name }}</td>
                <td class="px-4 py-3 text-slate-600">
                    <a href="mailto:{{ $inquiry->email }}" class="text-indigo-600 hover:underline" @click.stop>
                        {{ $inquiry->email }}
                    </a>
                </td>
                <td class="px-4 py-3 text-slate-500">{{ $inquiry->industry ?? '—' }}</td>
                <td class="px-4 py-3">
                    @if($inquiry->contacted_at)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                            対応済 {{ $inquiry->contacted_at->format('m/d') }}
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                            未対応
                        </span>
                    @endif
                </td>
                <td class="px-4 py-3 text-right" @click.stop>
                    <div class="flex items-center justify-end gap-2">
                        @if(!$inquiry->contacted_at)
                        <form method="POST" action="{{ route('superadmin.inquiries.contact', $inquiry) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                class="text-xs px-2.5 py-1.5 rounded-lg bg-emerald-600 text-white hover:bg-emerald-700 transition font-medium whitespace-nowrap">
                                対応済にする
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('superadmin.tenants.create', [
                                'company_name' => $inquiry->company_name,
                                'owner_name'   => $inquiry->contact_name,
                                'owner_email'  => $inquiry->email,
                                'industry'     => $inquiry->industry,
                            ]) }}"
                           class="text-xs px-2.5 py-1.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition font-medium whitespace-nowrap">
                            テナント作成
                        </a>
                    </div>
                </td>
            </tr>
            <tr x-show="open" class="bg-indigo-50" style="display:none">
                <td colspan="7" class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                        <div>
                            <p class="font-medium text-slate-700 mb-1">電話番号</p>
                            <p class="text-slate-600">{{ $inquiry->phone ?: '未記入' }}</p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="font-medium text-slate-700 mb-1">お問い合わせ内容</p>
                            <p class="text-slate-600 whitespace-pre-wrap">{{ $inquiry->message ?: '（内容なし）' }}</p>
                        </div>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                    まだお問い合わせはありません
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($inquiries->hasPages())
<div class="mt-6">
    {{ $inquiries->links() }}
</div>
@endif

@endsection
