@extends('admin.layouts.app')
@section('title', 'イベント新規作成')
@section('header-actions')
<a href="{{ route('admin.events.index') }}" class="text-xs text-slate-500 hover:text-slate-700">← イベント一覧</a>
@endsection
@section('content')
<div class="max-w-2xl">
    <div class="card p-6">
        <form method="POST" action="{{ route('admin.events.store') }}">
            @csrf
            @include('admin.events._form', ['event' => null])
            <div class="flex gap-3 mt-6 pt-5 border-t border-slate-100">
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                    作成する
                </button>
                <a href="{{ route('admin.events.index') }}" class="px-6 py-2 bg-slate-100 text-slate-600 text-sm font-medium rounded-lg hover:bg-slate-200 transition">
                    キャンセル
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
