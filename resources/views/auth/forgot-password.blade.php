<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        パスワードをお忘れですか？登録されたメールアドレスを入力してください。パスワード再設定用のリンクをお送りします。
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="email" value="メールアドレス" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                再設定リンクを送信
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
