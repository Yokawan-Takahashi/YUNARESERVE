@extends('admin.layouts.app')
@section('title', 'イベント新規作成')
@section('content')
<h1 class="text-xl font-bold mb-4">イベント新規作成</h1>
<form method="POST" action="{{ route('admin.events.store') }}" class="bg-white rounded shadow p-6 max-w-2xl space-y-4">
    @csrf
    @include('admin.events._form', ['event' => null])
    <div class="flex gap-3">
        <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">作成</button>
        <a href="{{ route('admin.events.index') }}" class="px-6 py-2 rounded border hover:bg-gray-50">キャンセル</a>
    </div>
</form>
@endsection
