<x-layouts.marketing title="Bekræft e-mail">
    <div class="flex min-h-screen items-center justify-center">
        <div class="w-full max-w-sm rounded-lg bg-white p-8 shadow">
            <h1 class="mb-4 text-2xl font-bold">Bekræft din e-mail</h1>

            @if (session('status') === 'verification-link-sent')
                <p class="mb-4 text-sm text-green-600">Et nyt bekræftelseslink er sendt til din e-mail.</p>
            @endif

            <p class="mb-6 text-sm text-gray-600">
                Inden du fortsætter, bedes du bekræfte din e-mailadresse ved at klikke på det link, vi sendte dig.
            </p>

            <form method="POST" action="{{ route('verification.send') }}" class="mb-4">
                @csrf
                <button type="submit" class="w-full rounded bg-red-600 px-4 py-2 text-white hover:bg-red-700">
                    Send bekræftelseslink igen
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-sm text-gray-500 hover:underline">Log ud</button>
            </form>
        </div>
    </div>
</x-layouts.marketing>
