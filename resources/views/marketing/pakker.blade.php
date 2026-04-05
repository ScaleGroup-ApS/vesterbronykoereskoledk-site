<x-layouts.marketing title="Pakker | Køreskole Pro">
    <main class="overflow-hidden bg-mk-base py-24 md:py-32">
        <div class="container mx-auto max-w-7xl px-6 lg:px-8">

            {{-- Header --}}
            <div class="mx-auto mb-16 max-w-2xl text-center">
                <p class="mk-eyebrow">Priser & pakker</p>
                <div class="mx-auto mt-1 mb-3 h-px w-10 bg-mk-accent"></div>
                <h1 class="text-4xl font-bold text-mk-text sm:text-5xl leading-tight">Vores pakker</h1>
                <p class="mt-4 text-lg text-mk-muted">Gennemskuelige priser uden skjulte gebyrer.</p>
            </div>

            {{-- Package cards --}}
            <div class="mx-auto grid max-w-5xl gap-8 md:grid-cols-2">
                @forelse ($offers as $index => $offer)
                    <div class="mk-card relative flex flex-col p-8 transition-transform duration-200 hover:-translate-y-1.5">
                        {{-- Most popular badge --}}
                        @if ($index === 0)
                            <div class="absolute right-6 top-0 inline-flex -translate-y-1/2 items-center gap-1.5 rounded-full bg-mk-accent/10 border border-mk-accent/30 px-3 py-1 text-xs font-semibold text-mk-accent">
                                <span class="h-1.5 w-1.5 rounded-full bg-mk-accent animate-pulse"></span>
                                Mest populære
                            </div>
                        @endif

                        <div class="mb-6">
                            <h2 class="font-heading text-2xl font-bold text-mk-text">
                                <a href="{{ route('marketing.packages.show', $offer) }}" class="transition-colors hover:text-mk-accent focus-visible:outline-none">
                                    {{ $offer->name }}
                                </a>
                            </h2>
                            <p class="mt-2 text-mk-muted">{{ $offer->description }}</p>
                            <p class="mt-2">
                                <a href="{{ route('marketing.packages.show', $offer) }}" class="text-sm font-medium text-mk-accent hover:underline underline-offset-4">
                                    Læs mere om pakken &rarr;
                                </a>
                            </p>

                            {{-- Price --}}
                            <div class="mt-6 flex items-baseline gap-1">
                                <span class="text-5xl font-extrabold text-mk-accent font-heading" style="letter-spacing: -0.03em">
                                    {{ number_format($offer->price, 0, ',', '.') }}
                                </span>
                                <span class="text-lg font-medium text-mk-muted">DKK</span>
                            </div>
                        </div>

                        {{-- Features --}}
                        <ul class="mb-8 flex-1 space-y-3">
                            @if ($offer->theory_lessons > 0)
                                <li class="flex items-center gap-3 text-mk-text">
                                    <svg class="h-5 w-5 shrink-0 text-mk-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    <span class="text-sm">{{ $offer->theory_lessons }} Teoritimer</span>
                                </li>
                            @endif
                            @if ($offer->driving_lessons > 0)
                                <li class="flex items-center gap-3 text-mk-text">
                                    <svg class="h-5 w-5 shrink-0 text-mk-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    <span class="text-sm">{{ $offer->driving_lessons }} Kørelektioner (45 min)</span>
                                </li>
                            @endif
                            @if ($offer->track_required)
                                <li class="flex items-center gap-3 text-mk-text">
                                    <svg class="h-5 w-5 shrink-0 text-mk-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    <span class="text-sm">Manøvrebane</span>
                                </li>
                            @endif
                            @if ($offer->slippery_required)
                                <li class="flex items-center gap-3 text-mk-text">
                                    <svg class="h-5 w-5 shrink-0 text-mk-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    <span class="text-sm">Køreteknisk anlæg (Glatbane)</span>
                                </li>
                            @endif
                        </ul>

                        {{-- CTA --}}
                        <a
                            href="/book/{{ $offer->slug }}"
                            class="mt-auto inline-flex h-12 w-full items-center justify-center rounded-xl border border-mk-border bg-white/[0.04] px-6 text-sm font-semibold text-mk-text transition-all duration-200 hover:bg-mk-accent hover:border-mk-accent hover:text-white hover:scale-[1.01]"
                        >
                            Tilmeld dig nu
                            <svg class="ml-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                        </a>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-mk-muted">
                        Ingen pakker tilgængelige i øjeblikket. Tjek venligst tilbage senere.
                    </div>
                @endforelse
            </div>

            {{-- Addons --}}
            @if ($addons->isNotEmpty())
                <section class="mx-auto mt-24 max-w-3xl">
                    <div class="mb-10 text-center">
                        <p class="mk-eyebrow">Ekstra</p>
                        <div class="mx-auto mt-1 mb-3 h-px w-10 bg-mk-accent"></div>
                        <h2 class="text-3xl font-bold text-mk-text">Tilvalg og ekstra priser</h2>
                        <p class="mx-auto mt-3 max-w-xl text-mk-muted">
                            Tillæg ud over din hovedpakke — bookes typisk sammen med eller efter tilmelding.
                        </p>
                    </div>
                    <ul class="divide-y divide-mk-border overflow-hidden rounded-2xl border border-mk-border bg-mk-surface">
                        @foreach ($addons as $addon)
                            <li class="flex flex-col gap-2 px-5 py-4 transition-colors hover:bg-white/[0.02] sm:flex-row sm:items-center sm:justify-between sm:gap-6">
                                <div class="min-w-0">
                                    <p class="font-semibold text-mk-text">{{ $addon->name }}</p>
                                    @if ($addon->description)
                                        <p class="mt-1 text-sm leading-relaxed text-mk-muted">{{ $addon->description }}</p>
                                    @endif
                                </div>
                                <p class="shrink-0 font-heading text-lg font-semibold tabular-nums text-mk-accent">
                                    {{ number_format($addon->price, 0, ',', '.') }}
                                    <span class="text-base font-medium text-mk-muted">kr.</span>
                                </p>
                            </li>
                        @endforeach
                    </ul>
                    <p class="mt-8 text-center text-sm text-mk-muted">
                        Spørgsmål om tilvalg?
                        <a href="{{ route('marketing.contact') }}" class="font-medium text-mk-accent hover:underline underline-offset-4">Skriv til os</a>
                    </p>
                </section>
            @endif

        </div>
    </main>
</x-layouts.marketing>
