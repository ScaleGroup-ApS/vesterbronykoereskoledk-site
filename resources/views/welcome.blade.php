<x-layouts.marketing title="Velkommen">
    <main>
        {{-- ── HERO ── --}}
        <section class="relative flex min-h-[92vh] items-center overflow-hidden bg-mk-base">
            {{-- Background glows --}}
            <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                <div
                    class="absolute inset-0 opacity-[0.4]"
                    style="background-image: radial-gradient(circle, rgba(255,255,255,0.06) 1px, transparent 1px); background-size: 48px 48px;"
                ></div>
                <div class="absolute bottom-0 left-1/2 h-[420px] w-[900px] -translate-x-1/2 rounded-full bg-mk-accent/[0.07] blur-[120px]"></div>
                <div class="absolute -top-20 right-0 h-[350px] w-[550px] rounded-full bg-mk-accent/[0.04] blur-[100px]"></div>
            </div>

            {{-- Road strip at bottom --}}
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-14 overflow-hidden bg-[#111]/60" aria-hidden="true">
                <div class="flex h-full items-center gap-8 overflow-hidden">
                    @for ($i = 0; $i < 24; $i++)
                        <div class="h-[3px] w-10 shrink-0 rounded-full bg-white/20"></div>
                    @endfor
                </div>
            </div>

            <div class="container relative mx-auto max-w-6xl px-6 py-24 lg:px-8">
                <div class="grid items-center gap-14 lg:grid-cols-[1fr_0.9fr] lg:gap-16">
                    {{-- Left: copy --}}
                    <div class="flex flex-col text-center lg:text-left">
                        <h1
                            class="overflow-hidden font-heading font-extrabold text-mk-text"
                            style="font-size: clamp(2.6rem, 6vw, 4.5rem); line-height: 1.08; letter-spacing: -0.03em;"
                        >
                            <span class="flex flex-wrap justify-center gap-x-[0.22em] lg:justify-start">
                                @foreach(explode(' ', $homeCopy->hero_headline_prefix) as $word)
                                    <span class="inline-block">{{ $word }}</span>
                                @endforeach
                            </span>
                            <span class="mt-1 flex flex-wrap justify-center gap-x-[0.22em] text-mk-accent lg:justify-start">
                                @foreach(explode(' ', $homeCopy->hero_headline_accent) as $word)
                                    <span class="inline-block">{{ $word }}</span>
                                @endforeach
                            </span>
                        </h1>

                        @if($homeCopy->hero_subtitle)
                            <p class="mx-auto mt-6 max-w-xl text-lg leading-relaxed text-mk-muted lg:mx-0">
                                {{ $homeCopy->hero_subtitle }}
                            </p>
                        @endif

                        <div class="mt-10 flex flex-col flex-wrap justify-center gap-3 sm:flex-row lg:justify-start">
                            <a href="{{ route('marketing.packages') }}" class="mk-btn-primary">
                                Se priser
                            </a>
                            <a
                                href="{{ $tilmeldHoldstartOfferSlug ? route('enrollment.show', $tilmeldHoldstartOfferSlug) : route('marketing.packages') }}"
                                class="mk-btn-ghost"
                            >
                                Tilmeld holdstart
                            </a>
                        </div>

                        @if($nextHoldStartAt || $heroHoldSpotsRemaining !== null)
                            <div class="mt-6">
                                <div class="inline-flex items-center gap-2 rounded-full border border-mk-border bg-mk-surface px-4 py-2 text-sm text-mk-muted">
                                    <span class="inline-block h-2 w-2 rounded-full bg-mk-accent"></span>
                                    @if($heroHoldSpotsRemaining !== null && $heroHoldSpotsRemaining <= 5)
                                        Kun {{ $heroHoldSpotsRemaining }} pladser tilbage
                                    @elseif($nextHoldStartAt)
                                        Næste holdstart: {{ \Carbon\Carbon::parse($nextHoldStartAt)->translatedFormat('j. M Y') }}
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Right: image collage --}}
                    <div class="relative mx-auto w-full max-w-[520px] lg:mx-0 lg:max-w-none">
                        <div class="pointer-events-none absolute -left-8 top-1/3 -z-10 h-48 w-48 rounded-full bg-mk-accent/10 blur-3xl"></div>
                        <div class="grid grid-cols-2 gap-3 sm:gap-4">
                            <div class="group col-span-2 aspect-[16/10] overflow-hidden rounded-2xl border border-mk-border bg-mk-surface shadow-lg sm:aspect-[2.1/1]">
                                <img
                                    src="/images/hero.png"
                                    alt="Køreelev og kørelærer ved bilen"
                                    class="h-full w-full object-cover object-[center_36%] transition duration-500 ease-out group-hover:scale-[1.04]"
                                />
                            </div>
                            <div class="group aspect-[4/5] overflow-hidden rounded-2xl border border-mk-border bg-mk-surface shadow-lg sm:aspect-square">
                                <img
                                    src="/images/hero.png"
                                    alt="Fokus på undervisning og struktur"
                                    class="h-full w-full object-cover object-[center_62%] transition duration-500 ease-out group-hover:scale-[1.04]"
                                />
                            </div>
                            <div class="group aspect-[4/5] overflow-hidden rounded-2xl border border-mk-border bg-mk-surface shadow-lg sm:aspect-square">
                                <img
                                    src="/images/hero.png"
                                    alt="Træning i rigtig trafik"
                                    class="h-full w-full object-cover object-[center_48%] transition duration-500 ease-out group-hover:scale-[1.04]"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Scroll indicator --}}
            <div class="mk-scroll-indicator" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
            </div>
        </section>

        {{-- ── WHY US / VALUE BLOCKS ── --}}
        <section class="relative bg-mk-surface py-24 md:py-32">
            <div class="pointer-events-none absolute inset-x-0 top-0 h-px" style="background: linear-gradient(to right, transparent, #2A2A2A, transparent);" aria-hidden="true"></div>
            <div class="container mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto mb-16 max-w-2xl text-center">
                    <p class="mk-eyebrow">Hvorfor os</p>
                    <div class="mx-auto mb-3 mt-1 h-px w-10 origin-left bg-mk-accent"></div>
                    <h2 class="text-4xl font-bold leading-tight text-mk-text sm:text-5xl">
                        {{ $homeCopy->why_title }}
                    </h2>
                    @if($homeCopy->why_lead)
                        <p class="mt-4 text-lg leading-relaxed text-mk-muted">{{ $homeCopy->why_lead }}</p>
                    @endif
                </div>
                <div class="mx-auto grid max-w-5xl gap-6 sm:grid-cols-2">
                    @foreach($valueBlocks as $block)
                        <div class="marketing-card-elevated">
                            <div class="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-mk-accent/10 text-mk-accent">
                                @switch($block->icon_key ?? $block->icon ?? '')
                                    @case('book_open')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                                        @break
                                    @case('users')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                        @break
                                    @case('car')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 17H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h3l2-3h4l2 3h3a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2Z"/><circle cx="7.5" cy="14.5" r="1.5"/><circle cx="16.5" cy="14.5" r="1.5"/></svg>
                                        @break
                                    @case('package')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 21.73a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73z"/><path d="M12 22V12"/><path d="m3.3 7 7.703 4.734a2 2 0 0 0 1.994 0L20.7 7"/><path d="m7.5 4.27 9 5.15"/></svg>
                                        @break
                                    @case('sparkles')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/><path d="M20 3v4"/><path d="M22 5h-4"/><path d="M4 17v2"/><path d="M5 18H3"/></svg>
                                        @break
                                    @case('message_circle')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
                                        @break
                                    @default
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                                @endswitch
                            </div>
                            <h3 class="text-lg font-semibold leading-snug text-mk-text">{{ $block->title }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-mk-muted">{{ $block->body }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px" style="background: linear-gradient(to right, transparent, #2A2A2A, transparent);" aria-hidden="true"></div>
        </section>

        {{-- ── TESTIMONIALS ── --}}
        <section class="relative bg-mk-surface py-24 md:py-32">
            <div class="pointer-events-none absolute inset-x-0 top-0 h-px" style="background: linear-gradient(to right, transparent, #2A2A2A, transparent);" aria-hidden="true"></div>
            <div class="container mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto mb-12 max-w-2xl text-center">
                    <p class="mk-eyebrow">Anmeldelser</p>
                    <div class="mx-auto mb-3 mt-1 h-px w-10 origin-left bg-mk-accent"></div>
                    <h2 class="text-4xl font-bold leading-tight text-mk-text sm:text-5xl">
                        {{ $homeCopy->reviews_title }}
                    </h2>
                    @if($homeCopy->reviews_lead)
                        <p class="mt-4 leading-relaxed text-mk-muted">{{ $homeCopy->reviews_lead }}</p>
                    @endif
                </div>

                @if($testimonials->isNotEmpty())
                    <div class="mx-auto grid max-w-5xl gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($testimonials as $testimonial)
                            <div class="mk-card p-6">
                                <div class="mb-3 flex gap-0.5">
                                    @for($i = 0; $i < 5; $i++)
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="{{ $i < ($testimonial->rating ?? 5) ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="{{ $i < ($testimonial->rating ?? 5) ? 'text-mk-accent' : 'text-mk-border' }}" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                    @endfor
                                </div>
                                <p class="text-sm leading-relaxed text-mk-muted">"{{ $testimonial->content }}"</p>
                                <p class="mt-4 text-sm font-semibold text-mk-text">{{ $testimonial->author_name }}</p>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($homeCopy->reviews_footnote)
                    <p class="mx-auto mt-8 max-w-2xl text-center text-xs text-mk-muted/60">
                        {{ $homeCopy->reviews_footnote }}
                    </p>
                @endif
            </div>
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px" style="background: linear-gradient(to right, transparent, #2A2A2A, transparent);" aria-hidden="true"></div>
        </section>

        {{-- ── EXPLORE ── --}}
        <section class="relative bg-mk-base py-24 md:py-32">
            <div class="container mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto mb-14 max-w-2xl text-center">
                    <p class="mk-eyebrow">Udforsk</p>
                    <div class="mx-auto mb-3 mt-1 h-px w-10 origin-left bg-mk-accent"></div>
                    <h2 class="text-4xl font-bold leading-tight text-mk-text sm:text-5xl">
                        {{ $homeCopy->explore_title }}
                    </h2>
                    @if($homeCopy->explore_lead)
                        <p class="mt-4 leading-relaxed text-mk-muted">{{ $homeCopy->explore_lead }}</p>
                    @endif
                </div>
                <div class="mx-auto grid max-w-4xl gap-6 sm:grid-cols-2">
                    @foreach([
                        ['title' => 'Fordele', 'description' => 'Hvorfor det giver mening at vælge os — bilpark, struktur og hvordan vi planlægger.', 'href' => route('marketing.features'), 'icon' => 'sparkles'],
                        ['title' => 'Pakker', 'description' => 'Se pris og indhold side om side. Ingen småt med småt på siden.', 'href' => route('marketing.packages'), 'icon' => 'package'],
                        ['title' => 'Om os', 'description' => 'Hvem vi er, og hvordan vi arbejder med elever i praksis.', 'href' => route('marketing.about'), 'icon' => 'car'],
                        ['title' => 'Kontakt', 'description' => 'Adresse, telefon, mail — eller skriv, hvis du har et konkret spørgsmål.', 'href' => route('marketing.contact'), 'icon' => 'message_circle'],
                    ] as $item)
                        <a href="{{ $item['href'] }}" class="mk-card group flex h-full flex-col p-6">
                            <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-mk-accent/10 text-mk-accent transition-colors duration-200 group-hover:bg-mk-accent/15">
                                @switch($item['icon'])
                                    @case('sparkles')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/></svg>
                                        @break
                                    @case('package')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 21.73a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73z"/><path d="M12 22V12"/><path d="m3.3 7 7.703 4.734a2 2 0 0 0 1.994 0L20.7 7"/></svg>
                                        @break
                                    @case('car')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 17H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h3l2-3h4l2 3h3a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2Z"/><circle cx="7.5" cy="14.5" r="1.5"/><circle cx="16.5" cy="14.5" r="1.5"/></svg>
                                        @break
                                    @case('message_circle')
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
                                        @break
                                @endswitch
                            </div>
                            <h3 class="font-heading text-lg font-semibold text-mk-text">{{ $item['title'] }}</h3>
                            <p class="mt-2 flex-1 text-sm leading-relaxed text-mk-muted">{{ $item['description'] }}</p>
                            <span class="mt-5 inline-flex items-center text-sm font-medium text-mk-accent">
                                Gå til side
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1 transition-transform duration-200 group-hover:translate-x-1" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                            </span>
                        </a>
                    @endforeach
                </div>

                {{-- Instructors teaser --}}
                <div class="mx-auto mt-10 flex max-w-3xl flex-col justify-center gap-3 rounded-2xl border border-mk-border bg-mk-surface px-6 py-5 text-center sm:flex-row sm:items-center sm:gap-6 sm:text-left">
                    <p class="text-sm text-mk-muted">
                        Vil du se holdene bag rattet først?
                        <a href="{{ route('marketing.instructors') }}" class="font-semibold text-mk-accent underline-offset-4 hover:underline">
                            Vores kørelærere
                        </a>
                    </p>
                </div>
            </div>
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px" style="background: linear-gradient(to right, transparent, #2A2A2A, transparent);" aria-hidden="true"></div>
        </section>

        {{-- ── FINAL CTA ── --}}
        <section class="relative overflow-hidden bg-mk-surface py-28">
            <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                <div class="absolute left-1/2 top-1/2 h-[400px] w-[700px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-mk-accent/[0.08] blur-[100px]"></div>
            </div>
            <div class="container relative mx-auto max-w-2xl px-6 text-center lg:px-8">
                <p class="mk-eyebrow flex justify-center">Kom i gang</p>
                <h2 class="text-4xl font-bold leading-tight text-mk-text sm:text-5xl">{{ $homeCopy->cta_title }}</h2>
                @if($homeCopy->cta_lead)
                    <p class="mt-5 text-lg leading-relaxed text-mk-muted">{{ $homeCopy->cta_lead }}</p>
                @endif
                <div class="mt-10 flex flex-col flex-wrap items-center justify-center gap-4 sm:flex-row">
                    <a href="{{ route('marketing.contact') }}" class="mk-btn-ghost">Skriv til os</a>
                    <a href="{{ route('login') }}" class="mk-btn-primary">Log ind som elev</a>
                </div>
            </div>
        </section>
    </main>
</x-layouts.marketing>
