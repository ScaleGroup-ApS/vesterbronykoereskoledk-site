<x-layouts.marketing title="Bekræft adgangskode">
    <div class="flex min-h-screen items-center justify-center">
        <div class="w-full max-w-sm rounded-lg bg-white p-8 shadow">
            <h1 class="mb-4 text-2xl font-bold">Bekræft adgangskode</h1>
            <p class="mb-6 text-sm text-gray-600">Bekræft din adgangskode for at fortsætte.</p>

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium">Adgangskode</label>
                    <input id="password" type="password" name="password" required autofocus
                        class="mt-1 w-full rounded border px-3 py-2">
                    @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="w-full rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700">
                    Bekræft
                </button>
            </form>
        </div>
    </div>
</x-layouts.marketing>
