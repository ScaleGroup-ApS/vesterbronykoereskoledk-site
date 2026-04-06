<x-layouts.marketing title="Nulstil adgangskode">
    <div class="flex min-h-screen items-center justify-center">
        <div class="w-full max-w-sm rounded-lg bg-white p-8 shadow">
            <h1 class="mb-4 text-2xl font-bold">Glemt adgangskode?</h1>
            <p class="mb-6 text-sm text-gray-600">Indtast din e-mail, så sender vi et link til nulstilling.</p>

            @if (session('status'))
                <p class="mb-4 text-sm text-green-600">{{ session('status') }}</p>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium">E-mail</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="mt-1 w-full rounded border px-3 py-2">
                    @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="w-full rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700">
                    Send nulstillingslink
                </button>
            </form>
        </div>
    </div>
</x-layouts.marketing>
