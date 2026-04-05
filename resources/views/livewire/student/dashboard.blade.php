<div class="space-y-6">
    @if(! $student)
        <div class="rounded-xl border bg-card p-6 text-center text-sm text-gray-500">
            Der er ikke knyttet et elevforløb til din konto. Kontakt din køreskole for at komme i gang.
        </div>
    @else
        <div class="space-y-1">
            <h2 class="text-xl font-semibold">Velkommen, {{ $userName ?? 'elev' }}</h2>
            <p class="text-sm text-gray-500">Her er et hurtigt overblik over dit forløb.</p>
        </div>

        {{-- Next booking --}}
        <div class="space-y-2">
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Næste aktivitet</h3>
            @if($nextBooking)
                <div class="rounded-xl border bg-card p-5 shadow-sm">
                    <p class="font-medium">{{ $nextBooking['title'] }}</p>
                    <p class="mt-1 text-sm text-gray-500">{{ $nextBooking['range_label'] }}</p>
                </div>
            @else
                <p class="text-sm text-gray-500">Ingen kommende aktivitet registreret endnu.</p>
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
