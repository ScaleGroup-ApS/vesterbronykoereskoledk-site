<div class="flex flex-col gap-4">
    @php
        $categoryColors = [
            'student'    => 'bg-blue-100 text-blue-700',
            'booking'    => 'bg-purple-100 text-purple-700',
            'payment'    => 'bg-green-100 text-green-700',
            'enrollment' => 'bg-orange-100 text-orange-700',
            'curriculum' => 'bg-yellow-100 text-yellow-700',
            'other'      => 'bg-gray-100 text-gray-600',
        ];
    @endphp

    @forelse ($events as $event)
        <div class="flex items-start gap-4 rounded-xl border bg-white p-4 shadow-sm dark:bg-gray-900 dark:border-gray-700">
            {{-- Icon badge --}}
            <div class="mt-0.5 shrink-0">
                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $categoryColors[$event->category] ?? $categoryColors['other'] }}">
                    {{ $event->category }}
                </span>
            </div>

            {{-- Content --}}
            <div class="flex min-w-0 flex-1 flex-col gap-0.5">
                <div class="flex items-center justify-between gap-2">
                    <span class="truncate text-sm font-semibold text-gray-900 dark:text-gray-100">
                        {{ $event->event_label }}
                    </span>
                    <span class="shrink-0 text-xs text-gray-400 dark:text-gray-500">
                        {{ \Carbon\Carbon::parse($event->occurred_at)->format('d.m.Y H:i') }}
                    </span>
                </div>

                @if ($event->description)
                    <p class="truncate text-sm text-gray-600 dark:text-gray-400">
                        {{ $event->description }}
                    </p>
                @endif
            </div>
        </div>
    @empty
        <div class="py-12 text-center text-sm text-gray-500">
            Ingen hændelser fundet.
        </div>
    @endforelse

    @if ($hasMore)
        <div class="pt-2 text-center">
            <button
                wire:click="loadMore"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 disabled:opacity-50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
            >
                <span wire:loading.remove wire:target="loadMore">Vis mere</span>
                <span wire:loading wire:target="loadMore">Indlæser...</span>
            </button>
        </div>
    @endif
</div>
