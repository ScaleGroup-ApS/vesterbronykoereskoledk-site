<div class="space-y-6">

    @if(! $balance)
        <p class="text-sm text-gray-500">Ingen betalingsinformation tilknyttet din konto.</p>
    @else

        {{-- Balance summary --}}
        <div class="grid grid-cols-3 gap-3">
            <div class="rounded-xl border bg-card p-4 text-center">
                <p class="text-xl font-bold">{{ number_format($balance['total_owed'], 0, ',', '.') }} kr.</p>
                <p class="mt-0.5 text-xs text-gray-500">Samlet pris</p>
            </div>
            <div class="rounded-xl border bg-card p-4 text-center">
                <p class="text-xl font-bold text-green-600">{{ number_format($balance['total_paid'], 0, ',', '.') }} kr.</p>
                <p class="mt-0.5 text-xs text-gray-500">Betalt</p>
            </div>
            <div class="rounded-xl border p-4 text-center {{ $balance['outstanding'] > 0 ? 'border-red-200 bg-red-50 dark:bg-red-950/20' : 'border-green-200 bg-green-50 dark:bg-green-950/20' }}">
                <p class="text-xl font-bold {{ $balance['outstanding'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                    {{ number_format(abs($balance['outstanding']), 0, ',', '.') }} kr.
                </p>
                <p class="mt-0.5 text-xs text-gray-500">{{ $balance['outstanding'] > 0 ? 'Udestående' : 'Overbetalt' }}</p>
            </div>
        </div>

        {{-- Offer prices --}}
        @if(! empty($offerPrices))
            <div class="space-y-2">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Tilmeldte pakker</h3>
                <div class="divide-y rounded-xl border">
                    @foreach($offerPrices as $offer)
                        <div class="flex items-center justify-between px-4 py-3">
                            <span class="text-sm">{{ $offer['name'] }}</span>
                            <span class="text-sm font-medium">{{ number_format($offer['price'], 0, ',', '.') }} kr.</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Payment history --}}
        @if(! empty($payments))
            <div class="space-y-2">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Betalingshistorik</h3>
                <div class="divide-y rounded-xl border">
                    @foreach($payments as $payment)
                        <div class="flex items-start justify-between px-4 py-3">
                            <div>
                                <p class="text-sm font-medium">{{ $payment['method_label'] }}</p>
                                <p class="text-xs text-gray-400">{{ $payment['recorded_at'] }}</p>
                                @if($payment['notes'])
                                    <p class="mt-0.5 text-xs text-gray-400 italic">{{ $payment['notes'] }}</p>
                                @endif
                            </div>
                            <p class="text-sm font-semibold text-green-600">+{{ number_format($payment['amount'], 0, ',', '.') }} kr.</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <p class="text-sm text-gray-500">Ingen registrerede betalinger endnu.</p>
        @endif

    @endif

</div>
