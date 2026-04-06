<div class="flex h-screen overflow-hidden bg-gray-50 dark:bg-gray-900">

    {{-- Left sidebar: module/page tree --}}
    <aside class="hidden w-64 shrink-0 border-r border-gray-200 bg-white dark:border-white/10 dark:bg-gray-950 lg:flex lg:flex-col">
        <div class="flex h-14 shrink-0 items-center border-b border-gray-200 px-4 dark:border-white/10">
            <a
                href="{{ url('/app') }}"
                wire:navigate
                class="flex items-center gap-2 text-sm font-medium text-gray-700 transition-colors hover:text-gray-900 dark:text-gray-300 dark:hover:text-white"
            >
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Tilbage til panel
            </a>
        </div>

        <div class="flex-1 overflow-y-auto p-4">
            <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-gray-400 dark:text-gray-500">Indhold</p>

            <nav class="space-y-4">
                @foreach($this->modulesWithPages as $mod)
                    <div x-data="{ open: {{ $mod['id'] === $module->id ? 'true' : 'false' }} }">
                        <button
                            type="button"
                            x-on:click="open = !open"
                            class="flex w-full items-center justify-between rounded px-2 py-1 text-left text-sm font-medium text-gray-700 transition-colors hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-white/5"
                        >
                            <span class="truncate">{{ $mod['title'] }}</span>
                            <svg
                                class="size-3.5 shrink-0 text-gray-400 transition-transform"
                                x-bind:class="open ? 'rotate-180' : ''"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <ul x-show="open" x-collapse class="mt-1 space-y-0.5 pl-2">
                            @foreach($mod['pages'] as $p)
                                @php
                                    $isDone = in_array($p['id'], $this->completedPageIds);
                                    $isCurrent = $p['id'] === $page->id;
                                @endphp
                                <li>
                                    <a
                                        href="{{ route('student.learn.page', ['offer' => $offer, 'module' => $mod['id'], 'page' => $p['id']]) }}"
                                        wire:navigate
                                        class="flex items-center gap-2 rounded px-2 py-1 text-sm transition-colors
                                            {{ $isCurrent ? 'bg-primary/10 font-medium text-primary' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-white/5' }}"
                                    >
                                        @if($isDone)
                                            <svg class="size-3.5 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @else
                                            <svg class="size-3.5 shrink-0 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 12m-9 0a9 9 0 1018 0 9 9 0 01-18 0" />
                                            </svg>
                                        @endif
                                        <span class="truncate">{{ $p['title'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </nav>
        </div>
    </aside>

    {{-- Main content --}}
    <main class="flex flex-1 flex-col overflow-hidden">

        {{-- Top bar --}}
        <header class="flex h-14 shrink-0 items-center justify-between border-b border-gray-200 bg-white px-6 dark:border-white/10 dark:bg-gray-950">
            <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                <a href="{{ url('/app') }}" wire:navigate class="hover:text-gray-700 dark:hover:text-gray-200 lg:hidden">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <span class="hidden lg:inline">{{ $offer->name }}</span>
                <svg class="hidden size-3 lg:inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span>{{ $module->title }}</span>
            </div>

            <span class="truncate text-sm font-medium text-gray-700 dark:text-gray-200 lg:max-w-xs">
                {{ $page->title }}
            </span>
        </header>

        {{-- Scrollable body --}}
        <div class="flex-1 overflow-y-auto">
            <div class="mx-auto max-w-3xl space-y-8 px-6 py-8">

                {{-- Page heading --}}
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $module->title }}</p>
                    <h1 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">{{ $page->title }}</h1>
                </div>

                {{-- Images carousel --}}
                @if(count($this->images) > 0)
                    <div
                        x-data="{ current: 0, count: {{ count($this->images) }} }"
                        class="space-y-2"
                    >
                        <div class="relative aspect-video overflow-hidden rounded-xl border border-gray-200 bg-black dark:border-white/10">
                            <div
                                class="flex h-full snap-x snap-mandatory overflow-x-hidden"
                                x-ref="track"
                            >
                                @foreach($this->images as $i => $img)
                                    <div class="h-full w-full shrink-0 snap-start">
                                        <img
                                            src="{{ $img['url'] }}"
                                            alt="{{ $img['file_name'] }}"
                                            class="h-full w-full object-contain"
                                        />
                                    </div>
                                @endforeach
                            </div>

                            @if(count($this->images) > 1)
                                <button
                                    type="button"
                                    x-on:click="current = Math.max(0, current - 1); $refs.track.children[current].scrollIntoView({ behavior: 'smooth', inline: 'start', block: 'nearest' })"
                                    x-bind:disabled="current === 0"
                                    class="absolute left-2 top-1/2 -translate-y-1/2 rounded-full bg-black/60 p-1.5 text-white transition-colors hover:bg-black/80 disabled:opacity-30"
                                    aria-label="Forrige billede"
                                >
                                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>

                                <button
                                    type="button"
                                    x-on:click="current = Math.min(count - 1, current + 1); $refs.track.children[current].scrollIntoView({ behavior: 'smooth', inline: 'start', block: 'nearest' })"
                                    x-bind:disabled="current === count - 1"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 rounded-full bg-black/60 p-1.5 text-white transition-colors hover:bg-black/80 disabled:opacity-30"
                                    aria-label="Næste billede"
                                >
                                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            @endif
                        </div>

                        @if(count($this->images) > 1)
                            <div class="flex justify-center gap-1.5">
                                @foreach($this->images as $i => $img)
                                    <button
                                        type="button"
                                        x-on:click="current = {{ $i }}; $refs.track.children[{{ $i }}].scrollIntoView({ behavior: 'smooth', inline: 'start', block: 'nearest' })"
                                        x-bind:class="current === {{ $i }} ? 'w-4 bg-gray-700 dark:bg-gray-200' : 'w-1.5 bg-gray-300 dark:bg-gray-600'"
                                        class="h-1.5 rounded-full transition-all"
                                        aria-label="Gå til billede {{ $i + 1 }}"
                                    ></button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Videos --}}
                @if(count($this->videos) > 0)
                    <div
                        x-data="{ current: 0, count: {{ count($this->videos) }} }"
                        class="space-y-2"
                    >
                        <div class="relative aspect-video overflow-hidden rounded-xl border border-gray-200 bg-black dark:border-white/10">
                            <div
                                class="flex h-full snap-x snap-mandatory overflow-x-hidden"
                                x-ref="videotrack"
                            >
                                @foreach($this->videos as $i => $vid)
                                    <div class="h-full w-full shrink-0 snap-start">
                                        <video
                                            src="{{ $vid['url'] }}"
                                            @if($vid['thumbnail_url']) poster="{{ $vid['thumbnail_url'] }}" @endif
                                            controls
                                            class="h-full w-full bg-black"
                                        ></video>
                                    </div>
                                @endforeach
                            </div>

                            @if(count($this->videos) > 1)
                                <button
                                    type="button"
                                    x-on:click="current = Math.max(0, current - 1); $refs.videotrack.children[current].scrollIntoView({ behavior: 'smooth', inline: 'start', block: 'nearest' })"
                                    x-bind:disabled="current === 0"
                                    class="absolute left-2 top-1/2 -translate-y-1/2 rounded-full bg-black/60 p-1.5 text-white transition-colors hover:bg-black/80 disabled:opacity-30"
                                    aria-label="Forrige video"
                                >
                                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                </button>

                                <button
                                    type="button"
                                    x-on:click="current = Math.min(count - 1, current + 1); $refs.videotrack.children[current].scrollIntoView({ behavior: 'smooth', inline: 'start', block: 'nearest' })"
                                    x-bind:disabled="current === count - 1"
                                    class="absolute right-2 top-1/2 -translate-y-1/2 rounded-full bg-black/60 p-1.5 text-white transition-colors hover:bg-black/80 disabled:opacity-30"
                                    aria-label="Næste video"
                                >
                                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </button>
                            @endif
                        </div>

                        @if(count($this->videos) > 1)
                            <div class="flex justify-center gap-1.5">
                                @foreach($this->videos as $i => $vid)
                                    <button
                                        type="button"
                                        x-on:click="current = {{ $i }}; $refs.videotrack.children[{{ $i }}].scrollIntoView({ behavior: 'smooth', inline: 'start', block: 'nearest' })"
                                        x-bind:class="current === {{ $i }} ? 'w-4 bg-gray-700 dark:bg-gray-200' : 'w-1.5 bg-gray-300 dark:bg-gray-600'"
                                        class="h-1.5 rounded-full transition-all"
                                        aria-label="Gå til video {{ $i + 1 }}"
                                    ></button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Body --}}
                @if($page->body)
                    <div class="prose prose-sm max-w-none dark:prose-invert">
                        {!! $page->body !!}
                    </div>
                @endif

                {{-- Attachments --}}
                @if(count($this->attachments) > 0)
                    <div>
                        <h2 class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">Filer</h2>
                        <ul class="space-y-2">
                            @foreach($this->attachments as $att)
                                <li>
                                    <a
                                        href="{{ $att['url'] }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="flex items-center gap-3 rounded-lg border border-gray-200 px-4 py-3 text-sm transition-colors hover:bg-gray-50 dark:border-white/10 dark:hover:bg-white/5"
                                    >
                                        <svg class="size-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        <span class="flex-1 truncate text-gray-700 dark:text-gray-300">{{ $att['file_name'] }}</span>
                                        <span class="text-xs text-gray-400">{{ $att['size'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Quiz --}}
                @if($page->quizQuestions->isNotEmpty())
                    <div class="rounded-xl border border-gray-200 p-6 dark:border-white/10">
                        <h2 class="mb-4 font-semibold text-gray-900 dark:text-white">Quiz</h2>

                        @if($submitted && $attempt)
                            <div class="mb-6 rounded-lg bg-gray-100 px-4 py-3 text-sm dark:bg-white/5">
                                <p class="font-medium text-gray-700 dark:text-gray-300">
                                    Dit resultat: {{ $attempt['score'] }}/{{ $attempt['total'] }} korrekte
                                </p>
                            </div>
                        @endif

                        <form wire:submit="submitQuiz" class="space-y-6">
                            @foreach($page->quizQuestions as $qi => $question)
                                @php
                                    $selectedAnswer = ($submitted && $attempt) ? ($attempt['answers'][$qi] ?? null) : null;
                                    $isCorrect = $selectedAnswer === $question->correct_option;
                                @endphp

                                <div class="space-y-3">
                                    <p class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ $qi + 1 }}. {{ $question->question }}
                                    </p>

                                    <div class="space-y-2">
                                        @foreach($question->options as $oi => $opt)
                                            @php
                                                $wasSelected = $submitted && $selectedAnswer === $oi;
                                                $isCorrectOpt = $oi === $question->correct_option;
                                                $optClass = 'flex cursor-pointer items-center gap-3 rounded-lg border px-4 py-3 text-sm transition-colors';

                                                if ($submitted) {
                                                    if ($isCorrectOpt) {
                                                        $optClass .= ' border-green-500 bg-green-50 dark:bg-green-950/20';
                                                    } elseif ($wasSelected) {
                                                        $optClass .= ' border-red-400 bg-red-50 dark:bg-red-950/20';
                                                    } else {
                                                        $optClass .= ' border-gray-200 dark:border-white/10';
                                                    }
                                                } else {
                                                    $optClass .= ' border-gray-200 hover:border-gray-300 hover:bg-gray-50 dark:border-white/10 dark:hover:bg-white/5';
                                                }
                                            @endphp

                                            <label class="{{ $optClass }}">
                                                <input
                                                    type="radio"
                                                    wire:model="answers.{{ $qi }}"
                                                    value="{{ $oi }}"
                                                    @if($submitted) disabled @endif
                                                    class="shrink-0"
                                                />
                                                <span class="text-gray-700 dark:text-gray-300">{{ $opt }}</span>

                                                @if($submitted && $isCorrectOpt)
                                                    <svg class="ml-auto size-4 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                @elseif($submitted && $wasSelected && !$isCorrectOpt)
                                                    <svg class="ml-auto size-4 shrink-0 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                @endif
                                            </label>
                                        @endforeach
                                    </div>

                                    @if($submitted && $question->explanation && !$isCorrect)
                                        <p class="pl-1 text-sm italic text-gray-500 dark:text-gray-400">
                                            Forklaring: {{ $question->explanation }}
                                        </p>
                                    @endif
                                </div>
                            @endforeach

                            <div class="flex gap-3">
                                @if(!$submitted)
                                    <button
                                        type="submit"
                                        wire:loading.attr="disabled"
                                        class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-primary/90 disabled:opacity-50"
                                    >
                                        <span wire:loading.remove wire:target="submitQuiz">Indsend svar</span>
                                        <span wire:loading wire:target="submitQuiz">Sender...</span>
                                    </button>
                                @else
                                    <button
                                        type="button"
                                        wire:click="retryQuiz"
                                        class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-white/10 dark:text-gray-300 dark:hover:bg-white/5"
                                    >
                                        Prøv igen
                                    </button>
                                @endif
                            </div>
                        </form>
                    </div>
                @endif

                {{-- Navigation footer --}}
                <div class="flex items-center justify-between border-t border-gray-200 pt-6 dark:border-white/10">
                    <div>
                        @if($prevPageUrl)
                            <a
                                href="{{ $prevPageUrl }}"
                                wire:navigate
                                class="inline-flex items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-white/10 dark:text-gray-300 dark:hover:bg-white/5"
                            >
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                                Forrige
                            </a>
                        @endif
                    </div>

                    <div>
                        @php $isCompleted = in_array($page->id, $this->completedPageIds); @endphp

                        @if($isCompleted)
                            <button
                                type="button"
                                disabled
                                class="inline-flex items-center gap-2 rounded-lg bg-green-100 px-4 py-2 text-sm font-medium text-green-700 dark:bg-green-950/30 dark:text-green-400"
                            >
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Gennemført
                            </button>
                        @else
                            <button
                                type="button"
                                wire:click="markComplete"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-primary/90 disabled:opacity-50"
                            >
                                <span wire:loading.remove wire:target="markComplete">
                                    @if($nextPageUrl)
                                        Gennemfør &amp; næste
                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    @else
                                        Markér som gennemført
                                    @endif
                                </span>
                                <span wire:loading wire:target="markComplete">Gemmer...</span>
                            </button>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </main>

</div>
