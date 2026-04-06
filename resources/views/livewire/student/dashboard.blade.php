<div class="space-y-6">
    @if(! $student)
        <div class="rounded-xl border bg-card p-6 text-center text-sm text-gray-500">
            Der er ikke knyttet et elevforløb til din konto. Kontakt din køreskole for at komme i gang.
        </div>
    @else

        {{-- Pending enrollment banner --}}
        @if($pendingEnrollment)
            @php $isPendingPayment = $pendingEnrollment['status'] === 'pending_payment'; @endphp
            <div class="rounded-xl border p-4 {{ $isPendingPayment ? 'border-amber-300 bg-amber-50 dark:bg-amber-950/30' : 'border-blue-300 bg-blue-50 dark:bg-blue-950/30' }}">
                <div class="flex items-start gap-3">
                    <svg class="mt-0.5 size-5 shrink-0 {{ $isPendingPayment ? 'text-amber-500' : 'text-blue-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        @if($isPendingPayment)
                            <p class="text-sm font-medium text-amber-800 dark:text-amber-200">Afventer betaling</p>
                            <p class="mt-0.5 text-sm text-amber-700 dark:text-amber-300">
                                Din tilmelding afventer betaling på {{ number_format($pendingEnrollment['offer_price'], 0, ',', '.') }} kr. Kontakt køreskolen for at afslutte tilmeldingen.
                            </p>
                        @else
                            <p class="text-sm font-medium text-blue-800 dark:text-blue-200">Tilmelding til godkendelse</p>
                            <p class="mt-0.5 text-sm text-blue-700 dark:text-blue-300">
                                Din tilmelding er modtaget og afventer godkendelse fra køreskolen.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Welcome + balance chip --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="space-y-0.5">
                <h2 class="text-xl font-semibold">Velkommen, {{ $userName ?? 'elev' }}</h2>
                <p class="text-sm text-gray-500">Her er et hurtigt overblik over dit forløb.</p>
            </div>
            @if($balance && $balance['outstanding'] != 0)
                <div class="rounded-lg border px-4 py-2 text-right">
                    <p class="text-xs text-gray-400">Udestående</p>
                    <p class="text-lg font-semibold {{ $balance['outstanding'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ number_format(abs($balance['outstanding']), 0, ',', '.') }} kr.
                    </p>
                </div>
            @endif
        </div>

        {{-- Next event --}}
        <div class="space-y-2">
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Næste aktivitet</h3>
            @if($nextEvent)
                <div class="rounded-xl border bg-card p-5 shadow-sm">
                    <p class="font-medium">{{ $nextEvent['title'] }}</p>
                    <p class="mt-1 text-sm text-gray-500">{{ $nextEvent['range_label'] }}</p>
                    @if(! empty($nextEvent['instructor_name']))
                        <p class="mt-1 text-xs text-gray-400">{{ $nextEvent['instructor_name'] }}</p>
                    @endif
                </div>
            @else
                <p class="text-sm text-gray-500">Ingen kommende aktiviteter registreret endnu.</p>
            @endif
        </div>

        {{-- Next theory topic --}}
        @if($nextTheoryTopic)
            <div class="space-y-2">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Næste teorilektion</h3>
                <div class="rounded-xl border bg-card p-5 shadow-sm">
                    <p class="text-xs font-medium text-gray-400">Lektion {{ $nextTheoryTopic['lesson_number'] }}</p>
                    <p class="mt-1 font-medium">{{ $nextTheoryTopic['title'] }}</p>
                    @if($nextTheoryTopic['description'])
                        <p class="mt-1 text-sm text-gray-500">{{ $nextTheoryTopic['description'] }}</p>
                    @endif
                </div>
            </div>
        @endif

    @endif
</div>
