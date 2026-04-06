<x-layouts.marketing title="To-faktor godkendelse">
    <div class="flex min-h-screen items-center justify-center">
        <div class="w-full max-w-sm rounded-lg bg-white p-8 shadow">
            <h1 class="mb-6 text-2xl font-bold">To-faktor godkendelse</h1>

            <div x-data="{ recovery: false }">
                <div x-show="!recovery">
                    <p class="mb-4 text-sm text-gray-600">Indtast koden fra din godkendelsesapp.</p>
                    <form method="POST" action="{{ route('two-factor.login') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="code" class="block text-sm font-medium">Kode</label>
                            <input id="code" type="text" inputmode="numeric" name="code" autofocus
                                class="mt-1 w-full rounded border px-3 py-2">
                            @error('code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <button type="submit" class="w-full rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700">
                            Bekræft
                        </button>
                    </form>
                </div>

                <div x-show="recovery" x-cloak>
                    <p class="mb-4 text-sm text-gray-600">Brug en gendannelseskode.</p>
                    <form method="POST" action="{{ route('two-factor.login') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="recovery_code" class="block text-sm font-medium">Gendannelseskode</label>
                            <input id="recovery_code" type="text" name="recovery_code"
                                class="mt-1 w-full rounded border px-3 py-2">
                        </div>
                        <button type="submit" class="w-full rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700">
                            Bekræft
                        </button>
                    </form>
                </div>

                <button @click="recovery = !recovery" class="mt-4 block w-full text-center text-sm text-gray-500 hover:underline">
                    <span x-show="!recovery">Brug gendannelseskode</span>
                    <span x-show="recovery" x-cloak>Brug godkendelseskode</span>
                </button>
            </div>
        </div>
    </div>
</x-layouts.marketing>
