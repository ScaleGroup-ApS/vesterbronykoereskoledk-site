<x-layouts.marketing title="Log ind">
    <div class="flex min-h-screen items-center justify-center">
        <div class="w-full max-w-sm rounded-lg bg-white p-8 shadow">
            <h1 class="mb-6 text-2xl font-bold">Log ind</h1>

            @if (session('status'))
                <p class="mb-4 text-sm text-green-600">{{ session('status') }}</p>
            @endif

            <form method="POST" action="{{ route('login.store') }}">
                @csrf

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium">E-mail</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="mt-1 w-full rounded border px-3 py-2">
                    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium">Adgangskode</label>
                    <input id="password" type="password" name="password" required
                        class="mt-1 w-full rounded border px-3 py-2">
                    @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="w-full rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700">
                    Log ind
                </button>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="mt-4 block text-center text-sm text-gray-500 hover:underline">
                        Glemt adgangskode?
                    </a>
                @endif
            </form>
        </div>
    </div>
</x-layouts.marketing>
