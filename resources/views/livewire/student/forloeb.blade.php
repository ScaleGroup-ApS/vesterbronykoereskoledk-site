@php
$stepLabels = [
    'theory_lesson'   => 'Teorilektioner',
    'track_driving'   => 'Banekørsel',
    'slippery_driving'=> 'Glat bane',
    'driving_lesson'  => 'Køretimer',
    'theory_exam'     => 'Teoriprøve',
    'practical_exam'  => 'Køreprøve',
    'course_start'    => 'Holdstart',
];
@endphp

<div class="space-y-8">
    @if(! $student)
        <p class="text-sm text-gray-500">
            Der er ikke knyttet et elevforløb til din konto. Kontakt din køreskole.
        </p>
    @else

        {{-- Journey steps --}}
        @if(! empty($journey['steps']))
            <div class="space-y-3">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Kørekortsforløb</h3>
                <ol class="space-y-0">
                    @foreach($journey['steps'] as $step)
                        @php
                            $isDone      = $step['status'] === 'done';
                            $isProgress  = $step['status'] === 'in_progress';
                        @endphp
                        <li class="relative flex gap-4">
                            {{-- Vertical line --}}
                            @if(! $loop->last)
                                <div class="absolute left-3.5 top-8 h-full w-px bg-gray-200 dark:bg-white/10"></div>
                            @endif

                            {{-- Icon dot --}}
                            <div class="relative z-10 flex size-7 shrink-0 items-center justify-center rounded-full border-2
                                {{ $isDone ? 'border-green-500 bg-green-500' : ($isProgress ? 'border-primary bg-primary/10' : 'border-gray-300 bg-white dark:bg-gray-900') }}">
                                @if($isDone)
                                    <svg class="size-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                    </svg>
                                @elseif($isProgress)
                                    <span class="size-2 rounded-full bg-primary"></span>
                                @else
                                    <span class="size-2 rounded-full bg-gray-300"></span>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="pb-6 pt-0.5">
                                <p class="text-sm font-medium {{ $isDone ? 'text-green-700 dark:text-green-400' : ($isProgress ? 'text-primary' : 'text-gray-500') }}">
                                    {{ $step['label'] }}
                                </p>
                                @if($step['detail'])
                                    <p class="text-xs text-gray-400">{{ $step['detail'] }}</p>
                                @endif
                                @if($step['at'])
                                    <p class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($step['at'])->timezone(config('app.timezone'))->translatedFormat('d. MMM Y') }}
                                    </p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ol>
            </div>
        @endif

        {{-- Lesson progress --}}
        @if(! empty($lessonProgress))
            <div class="space-y-3">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Dit pakkeforløb</h3>
                <div class="divide-y rounded-xl border">
                    @foreach($lessonProgress as $row)
                        <div class="px-4 py-3">
                            <div class="mb-1 flex items-center justify-between text-sm">
                                <span class="font-medium">{{ $row['label'] }}</span>
                                <span class="text-gray-500">{{ $row['completed'] }} / {{ $row['required'] }}</span>
                            </div>
                            <div class="h-1.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-white/10">
                                @php
                                    $pct = $row['required'] > 0
                                        ? min(100, round($row['completed'] / $row['required'] * 100))
                                        : 0;
                                @endphp
                                <div class="h-full rounded-full bg-primary transition-all" style="width: {{ $pct }}%"></div>
                            </div>
                            @if($row['scheduled'] > 0)
                                <p class="mt-1 text-xs text-gray-400">{{ $row['scheduled'] }} booket frem i tid</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Exam readiness --}}
        <div class="space-y-3">
            <div class="flex items-center justify-between gap-2">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Fremgang mod eksamen</h3>
                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                    {{ $readiness['is_ready'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                    {{ $readiness['is_ready'] ? 'Klar til eksamen' : 'Ikke eksamensklar endnu' }}
                </span>
            </div>

            @php
                $requiredItems = array_filter($readiness['required'], fn($v) => $v > 0);
            @endphp

            @if(! empty($requiredItems))
                <div class="divide-y rounded-xl border">
                    @foreach($requiredItems as $type => $needed)
                        @php
                            $done = $readiness['completed'][$type] ?? 0;
                            $met  = $done >= $needed;
                            $typeLabels = [
                                'driving_lesson'  => 'Køretimer',
                                'theory_lesson'   => 'Teorilektioner',
                                'track_driving'   => 'Banekørsel',
                                'slippery_driving'=> 'Glat bane',
                                'theory_exam'     => 'Teoriprøve',
                                'practical_exam'  => 'Køreprøve',
                            ];
                        @endphp
                        <div class="flex items-center justify-between px-4 py-3">
                            <div class="flex items-center gap-2">
                                @if($met)
                                    <svg class="size-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @else
                                    <svg class="size-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                @endif
                                <span class="text-sm">{{ $typeLabels[$type] ?? $type }}</span>
                            </div>
                            <span class="text-sm text-gray-500">{{ $done }} / {{ $needed }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Curriculum / learning plan --}}
        @if(! empty($curriculumByLesson))
            <div class="space-y-3">
                <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Læringsplan</h3>
                <div class="divide-y rounded-xl border">
                    @foreach($curriculumByLesson as $lessonNumber => $title)
                        <div class="flex items-center gap-3 px-4 py-2.5 text-sm">
                            <span class="w-20 shrink-0 text-xs font-medium text-gray-400">Lektion {{ $lessonNumber }}</span>
                            <span>{{ $title }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    @endif
</div>
