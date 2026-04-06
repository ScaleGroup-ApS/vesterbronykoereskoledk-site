<div class="select-none">
    {{-- Month navigation header --}}
    <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
            {{ $monthName }}
            <span class="ml-1 font-normal text-gray-500 dark:text-gray-400">{{ $year }}</span>
        </h2>

        <div class="flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-1 dark:border-gray-700 dark:bg-gray-800">
            <button
                type="button"
                wire:click="previousMonth"
                class="rounded p-1 text-gray-500 transition hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700"
                aria-label="Forrige måned"
            >
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <div class="h-5 w-px bg-gray-200 dark:bg-gray-700"></div>
            <button
                type="button"
                wire:click="nextMonth"
                class="rounded p-1 text-gray-500 transition hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700"
                aria-label="Næste måned"
            >
                <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Calendar grid --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
        {{-- Weekday headers --}}
        <div class="grid grid-cols-7 border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-800">
            @foreach(['Man', 'Tir', 'Ons', 'Tor', 'Fre', 'Lør', 'Søn'] as $day)
                <div class="py-2 text-center text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                    {{ $day }}
                </div>
            @endforeach
        </div>

        {{-- Days grid --}}
        <div class="grid grid-cols-7 border-l border-t border-gray-200 dark:border-gray-700">
            {{-- Blank cells before first day --}}
            @for ($i = 0; $i < $blankDays; $i++)
                <div class="min-h-[100px] border-b border-r border-gray-100 bg-gray-50/50 dark:border-gray-800 dark:bg-gray-800/30"></div>
            @endfor

            {{-- Day cells --}}
            @foreach ($calendarDates as $day)
                @php
                    $dateKey = \Carbon\Carbon::create($year, $month, $day)->format('Y-m-d');
                    $dayBookings = $bookingsByDate[$dateKey] ?? [];
                    $isToday = $dateKey === now()->format('Y-m-d');
                @endphp

                <div class="group min-h-[100px] border-b border-r border-gray-100 p-1.5 dark:border-gray-800">
                    {{-- Day number --}}
                    <div class="mb-1 flex justify-end">
                        <span @class([
                            'inline-flex size-6 items-center justify-center rounded-full text-xs font-medium',
                            'bg-primary-600 text-white' => $isToday,
                            'text-gray-500 dark:text-gray-400' => ! $isToday,
                        ])>
                            {{ $day }}
                        </span>
                    </div>

                    {{-- Bookings for this day --}}
                    <div class="max-h-[80px] space-y-0.5 overflow-y-auto">
                        @foreach ($dayBookings as $booking)
                            @php
                                $typeColors = [
                                    'driving_lesson' => 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-800',
                                    'theory_lesson' => 'bg-blue-100 text-blue-800 border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800',
                                    'theory_exam' => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-800',
                                    'practical_exam' => 'bg-amber-100 text-amber-800 border-amber-200 dark:bg-amber-900/30 dark:text-amber-300 dark:border-amber-800',
                                    'track_driving' => 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-800',
                                    'slippery_driving' => 'bg-green-100 text-green-800 border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-800',
                                ];
                                $dotColors = [
                                    'driving_lesson' => 'bg-green-500',
                                    'theory_lesson' => 'bg-blue-500',
                                    'theory_exam' => 'bg-amber-500',
                                    'practical_exam' => 'bg-amber-500',
                                    'track_driving' => 'bg-green-500',
                                    'slippery_driving' => 'bg-green-500',
                                ];
                                $type = $booking['type'] ?? 'driving_lesson';
                                $colorClass = $typeColors[$type] ?? 'bg-gray-100 text-gray-800 border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700';
                                $dotClass = $dotColors[$type] ?? 'bg-gray-400';
                                $studentName = $booking['student']['user']['name'] ?? 'Ukendt elev';
                                $time = \Carbon\Carbon::parse($booking['starts_at'])->format('H:i');
                                $editUrl = route('filament.admin.resources.bookings.edit', ['record' => $booking['id']]);
                            @endphp

                            <a
                                href="{{ $editUrl }}"
                                wire:navigate
                                class="flex items-center gap-1 truncate rounded border px-1 py-0.5 text-xs leading-tight transition hover:opacity-80 {{ $colorClass }}"
                                title="{{ $time }} · {{ $studentName }}"
                            >
                                <span class="size-1.5 shrink-0 rounded-full {{ $dotClass }}"></span>
                                <span class="font-medium">{{ $time }}</span>
                                <span class="truncate">{{ $studentName }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
