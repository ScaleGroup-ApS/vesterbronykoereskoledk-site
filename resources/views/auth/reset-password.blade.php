<x-layouts.marketing title="Ny adgangskode">
    <div class="flex min-h-screen items-center justify-center">
        <div class="w-full max-w-sm rounded-lg bg-white p-8 shadow">
            <h1 class="mb-6 text-2xl font-bold">Ny adgangskode</h1>

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium">E-mail</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $email ?? '') }}" required autofocus
                        class="mt-1 w-full rounded border px-3 py-2">
                    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium">Ny adgangskode</label>
                    <input id="password" type="password" name="password" required
                        class="mt-1 w-full rounded border px-3 py-2">
                    @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium">Bekræft adgangskode</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                        class="mt-1 w-full rounded border px-3 py-2">
                </div>

                <button type="submit" class="w-full rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700">
                    Gem adgangskode
                </button>
            </form>
        </div>
    </div>
</x-layouts.marketing>
