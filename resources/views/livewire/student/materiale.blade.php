<div class="space-y-6">
    @if(empty($available) && empty($locked))
        <p class="text-sm text-gray-500">Ingen kursusmateriale tilknyttet dit forløb endnu.</p>
    @endif

    @if(! empty($available))
        <div class="space-y-3">
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Tilgængeligt nu</h3>
            <div class="divide-y rounded-xl border">
                @foreach($available as $material)
                    <a href="{{ $material['url'] }}"
                       target="_blank"
                       rel="noopener noreferrer"
                       class="flex items-center justify-between px-4 py-3 transition-colors hover:bg-gray-50 dark:hover:bg-white/5">
                        <div class="flex min-w-0 items-center gap-2">
                            <svg class="size-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <div class="min-w-0">
                                <p class="truncate text-sm">{{ $material['name'] }}</p>
                                <p class="text-xs text-gray-400">{{ $material['offer_name'] }}</p>
                            </div>
                        </div>
                        <div class="flex shrink-0 items-center gap-3">
                            <span class="text-xs text-gray-400">{{ $material['size'] }}</span>
                            <svg class="size-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    @if(! empty($locked))
        <div class="space-y-3">
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Låst</h3>
            <div class="divide-y rounded-xl border">
                @foreach($locked as $material)
                    <div class="flex items-center justify-between px-4 py-3 opacity-60">
                        <div class="flex min-w-0 items-center gap-2">
                            <svg class="size-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <div class="min-w-0">
                                <p class="truncate text-sm">{{ $material['name'] }}</p>
                                <p class="text-xs text-gray-400">
                                    Låses op efter lektion {{ $material['unlock_at_lesson'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
