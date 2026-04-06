@php
    $passPct = 90;
@endphp

<div>

{{-- ── INDEX ─────────────────────────────────────────────────────── --}}
@if($step === 'index')
<div class="space-y-6">

    {{-- Stats strip --}}
    @if($stats)
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
        <div class="rounded-xl border bg-card p-4 text-center">
            <p class="text-2xl font-bold">{{ $stats['total_attempts'] }}</p>
            <p class="mt-0.5 text-xs text-gray-500">Øvelser</p>
        </div>
        <div class="rounded-xl border bg-card p-4 text-center">
            <p class="text-2xl font-bold text-green-600">{{ $stats['pass_count'] }}</p>
            <p class="mt-0.5 text-xs text-gray-500">Bestået (≥ {{ $passPct }}%)</p>
        </div>
        <div class="rounded-xl border bg-card p-4 text-center">
            <p class="text-2xl font-bold">{{ $stats['best_score'] !== null ? $stats['best_score'].'%' : '–' }}</p>
            <p class="mt-0.5 text-xs text-gray-500">Bedste resultat</p>
        </div>
        <div class="rounded-xl border bg-card p-4 text-center">
            <p class="text-2xl font-bold">{{ $stats['available_questions'] }}</p>
            <p class="mt-0.5 text-xs text-gray-500">Spørgsmål</p>
        </div>
    </div>
    @endif

    {{-- Start button --}}
    @if($hasQuestions)
        <button wire:click="startExam"
                wire:loading.attr="disabled"
                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary/90 disabled:opacity-60">
            <span wire:loading.remove wire:target="startExam">Start ny øvelse (25 spørgsmål · 25 min)</span>
            <span wire:loading wire:target="startExam">Henter spørgsmål…</span>
        </button>
    @else
        <p class="rounded-xl border border-dashed p-6 text-center text-sm text-gray-400">
            Der er ikke tilknyttet spørgsmål til dit forløb endnu.
        </p>
    @endif

    {{-- Recent attempts --}}
    @if(! empty($attempts))
        <div class="space-y-3">
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Seneste øvelser</h3>
            <div class="divide-y rounded-xl border">
                @foreach($attempts as $attempt)
                    @php $passed = $attempt['percentage'] >= $passPct; @endphp
                    <div class="flex items-center justify-between px-4 py-3">
                        <div>
                            <p class="text-sm text-gray-500">{{ $attempt['attempted_at'] }}</p>
                            <p class="text-xs text-gray-400">
                                {{ gmdate('i:s', $attempt['duration_seconds']) }} minutter
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold {{ $passed ? 'text-green-600' : 'text-red-500' }}">
                                {{ $attempt['percentage'] }}%
                            </p>
                            <p class="text-xs text-gray-400">{{ $attempt['score'] }} / {{ $attempt['total'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
@endif

{{-- ── EXAM ──────────────────────────────────────────────────────── --}}
@if($step === 'exam')
<div
    x-data="{
        timeLeft: {{ $timeLimitSeconds }},
        elapsed: 0,
        timer: null,
        start() {
            this.timer = setInterval(() => {
                if (this.timeLeft > 0) { this.timeLeft--; this.elapsed++; }
                else { clearInterval(this.timer); $wire.submitExam(this.elapsed); }
            }, 1000);
        },
        get formatted() {
            const m = Math.floor(this.timeLeft / 60).toString().padStart(2, '0');
            const s = (this.timeLeft % 60).toString().padStart(2, '0');
            return m + ':' + s;
        }
    }"
    x-init="start()"
    class="space-y-6"
>
    {{-- Timer bar --}}
    <div class="flex items-center justify-between rounded-xl border bg-card px-4 py-3">
        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
            Spørgsmål {{ count($questions) }} — vælg ét svar per spørgsmål
        </p>
        <div class="flex items-center gap-2">
            <svg class="size-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="font-mono text-sm font-medium" :class="timeLeft < 120 ? 'text-red-500' : 'text-gray-600 dark:text-gray-300'" x-text="formatted"></span>
        </div>
    </div>

    {{-- Questions --}}
    <div class="space-y-4">
        @foreach($questions as $i => $q)
            <div class="rounded-xl border bg-card p-5">
                <p class="mb-3 text-xs font-medium text-gray-400">Spørgsmål {{ $i + 1 }}</p>
                <p class="mb-4 font-medium">{{ $q['question'] }}</p>
                <div class="space-y-2">
                    @foreach($q['options'] as $optIdx => $option)
                        <label class="flex cursor-pointer items-center gap-3 rounded-lg border px-4 py-3 transition-colors
                            {{ ($answers[$i] ?? null) === $optIdx ? 'border-primary bg-primary/5' : 'hover:bg-gray-50 dark:hover:bg-white/5' }}">
                            <input type="radio"
                                   wire:model="answers.{{ $i }}"
                                   value="{{ $optIdx }}"
                                   class="accent-primary">
                            <span class="text-sm">{{ $option }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    {{-- Submit --}}
    <button @click="clearInterval(timer); $wire.submitExam(elapsed)"
            wire:loading.attr="disabled"
            class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary/90 disabled:opacity-60">
        <span wire:loading.remove>Afslut og se resultat</span>
        <span wire:loading>Beregner…</span>
    </button>

</div>
@endif

{{-- ── RESULT ────────────────────────────────────────────────────── --}}
@if($step === 'result' && $result)
@php
    $passed = $result['percentage'] >= $passPct;
    $mins = intdiv($result['duration_seconds'], 60);
    $secs = $result['duration_seconds'] % 60;
@endphp
<div class="space-y-6">

    {{-- Score card --}}
    <div class="rounded-xl border bg-card p-6 text-center">
        <p class="text-5xl font-bold {{ $passed ? 'text-green-600' : 'text-red-500' }}">
            {{ $result['percentage'] }}%
        </p>
        <p class="mt-1 text-sm text-gray-500">
            {{ $result['score'] }} rigtige ud af {{ $result['total'] }} spørgsmål
        </p>
        <p class="mt-0.5 text-xs text-gray-400">Tid: {{ $mins }}:{{ str_pad($secs, 2, '0', STR_PAD_LEFT) }}</p>
        <span class="mt-3 inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
            {{ $passed ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
            {{ $passed ? 'Bestået' : 'Ikke bestået — prøv igen' }}
        </span>
    </div>

    {{-- Question review --}}
    <div class="space-y-3">
        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Gennemgang af svar</h3>
        <div class="space-y-3">
            @foreach($result['questions_with_answers'] as $i => $qr)
                @php
                    $correct = $qr['student_answer'] === $qr['correct_option'];
                @endphp
                <div class="rounded-xl border p-4 {{ $correct ? 'border-green-200 bg-green-50/50 dark:bg-green-900/10' : 'border-red-200 bg-red-50/50 dark:bg-red-900/10' }}">
                    <div class="flex items-start gap-2">
                        @if($correct)
                            <svg class="mt-0.5 size-4 shrink-0 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @else
                            <svg class="mt-0.5 size-4 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @endif
                        <p class="text-sm font-medium">{{ $qr['question'] }}</p>
                    </div>
                    <div class="mt-3 space-y-1">
                        @foreach($qr['options'] as $optIdx => $option)
                            @php
                                $isCorrect = $optIdx === $qr['correct_option'];
                                $isStudentAnswer = $optIdx === $qr['student_answer'];
                            @endphp
                            <div class="flex items-center gap-2 rounded-md px-3 py-1.5 text-sm
                                {{ $isCorrect ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300' : ($isStudentAnswer && ! $isCorrect ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300' : 'text-gray-600 dark:text-gray-400') }}">
                                <span class="w-4 shrink-0 text-xs font-bold">
                                    {{ $isCorrect ? '✓' : ($isStudentAnswer ? '✗' : '') }}
                                </span>
                                {{ $option }}
                            </div>
                        @endforeach
                    </div>
                    @if($qr['explanation'])
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ $qr['explanation'] }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex gap-3">
        <button wire:click="reset"
                class="flex-1 rounded-xl border px-4 py-2.5 text-sm font-medium transition hover:bg-gray-50 dark:hover:bg-white/5">
            Tilbage til oversigt
        </button>
        <button wire:click="startExam"
                wire:loading.attr="disabled"
                class="flex-1 rounded-xl bg-primary px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-primary/90 disabled:opacity-60">
            <span wire:loading.remove wire:target="startExam">Prøv igen</span>
            <span wire:loading wire:target="startExam">Henter…</span>
        </button>
    </div>

</div>
@endif

</div>
