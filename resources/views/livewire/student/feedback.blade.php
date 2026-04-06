<div class="space-y-8">

    {{-- Pending feedback --}}
    @if(! empty($pendingBookings))
        <div class="space-y-3">
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Afventer din feedback</h3>
            <div class="space-y-4">
                @foreach($pendingBookings as $booking)
                    <div class="rounded-xl border bg-card p-5">
                        <div class="mb-4 flex flex-wrap items-start justify-between gap-2">
                            <div>
                                <p class="font-medium">{{ $booking['type_label'] }}</p>
                                <p class="text-sm text-gray-500">{{ $booking['range_label'] }}</p>
                                @if($booking['instructor_name'])
                                    <p class="text-xs text-gray-400">{{ $booking['instructor_name'] }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Star rating --}}
                        <div class="mb-3 flex gap-1" x-data="{ hovered: 0 }">
                            @for($star = 1; $star <= 5; $star++)
                                <button type="button"
                                        wire:click="$set('ratings.{{ $booking['id'] }}', {{ $star }})"
                                        @mouseenter="hovered = {{ $star }}"
                                        @mouseleave="hovered = 0"
                                        class="text-2xl transition-transform hover:scale-110 focus:outline-none"
                                        :class="(hovered >= {{ $star }} || {{ $ratings[$booking['id']] ?? 0 }} >= {{ $star }}) ? 'text-amber-400' : 'text-gray-300'">
                                    ★
                                </button>
                            @endfor
                            @if($ratings[$booking['id']] ?? null)
                                <span class="ml-2 self-center text-xs text-gray-400">
                                    {{ ['', 'Dårlig', 'Ikke god', 'OK', 'God', 'Fremragende'][$ratings[$booking['id']]] }}
                                </span>
                            @endif
                        </div>

                        @if(isset($errors[$booking['id']]))
                            <p class="mb-2 text-xs text-red-500">{{ $errors[$booking['id']] }}</p>
                        @endif

                        {{-- Comment --}}
                        <textarea wire:model.lazy="comments.{{ $booking['id'] }}"
                                  rows="2"
                                  placeholder="Tilføj en kommentar (valgfrit)…"
                                  class="w-full rounded-lg border bg-transparent px-3 py-2 text-sm placeholder-gray-400 focus:border-primary focus:outline-none dark:border-white/10"></textarea>

                        <button wire:click="submitFeedback({{ $booking['id'] }})"
                                wire:loading.attr="disabled"
                                class="mt-3 inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white transition hover:bg-primary/90 disabled:opacity-60">
                            <span wire:loading.remove wire:target="submitFeedback({{ $booking['id'] }})">Send feedback</span>
                            <span wire:loading wire:target="submitFeedback({{ $booking['id'] }})">Gemmer…</span>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @elseif(empty($recentFeedback))
        <p class="rounded-xl border border-dashed p-8 text-center text-sm text-gray-400">
            Du har ingen lektioner der afventer feedback endnu.
        </p>
    @endif

    {{-- Past feedback --}}
    @if(! empty($recentFeedback))
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Din feedback</h3>
                @if($avgRating)
                    <span class="text-sm font-medium text-amber-500">⌀ {{ $avgRating }} / 5</span>
                @endif
            </div>
            <div class="divide-y rounded-xl border">
                @foreach($recentFeedback as $fb)
                    <div class="px-4 py-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="text-sm font-medium">{{ $fb['type_label'] }}</p>
                                <p class="text-xs text-gray-400">{{ $fb['range_label'] }}{{ $fb['instructor_name'] ? ' · '.$fb['instructor_name'] : '' }}</p>
                            </div>
                            <span class="shrink-0 text-sm font-medium text-amber-400">
                                {{ str_repeat('★', $fb['rating']) }}<span class="text-gray-300">{{ str_repeat('★', 5 - $fb['rating']) }}</span>
                            </span>
                        </div>
                        @if($fb['comment'])
                            <p class="mt-2 text-sm text-gray-500 italic">„{{ $fb['comment'] }}"</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
