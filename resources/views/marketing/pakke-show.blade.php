<x-layouts.marketing :title="$offer->name . ' | Køreskole Pro'">
    <main class="overflow-hidden bg-mk-base">

        {{-- Hero header --}}
        <section class="relative border-b border-mk-border bg-mk-surface">
            <div class="pointer-events-none absolute inset-x-0 bottom-0 h-px" style="background: linear-gradient(to right, transparent, #2A2A2A, transparent);" aria-hidden="true"></div>
            <div class="container mx-auto max-w-6xl px-4 pb-12 pt-10 lg:px-8">

                {{-- Breadcrumb --}}
                <nav class="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-mk-muted" aria-label="Brødkrumme">
                    <a href="/" class="transition-colors hover:text-mk-text">Forside</a>
                    <svg class="h-3.5 w-3.5 shrink-0 text-mk-border" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                    <a href="{{ route('marketing.packages') }}" class="transition-colors hover:text-mk-text">Pakker</a>
                    <svg class="h-3.5 w-3.5 shrink-0 text-mk-border" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>
                    <span class="font-medium text-mk-text">{{ $offer->name }}</span>
                </nav>

                <a href="{{ route('marketing.packages') }}" class="mt-5 inline-flex items-center gap-2 text-sm font-medium text-mk-muted transition-colors hover:text-mk-accent">
                    <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    Tilbage til alle pakker
                </a>

                <div class="mt-8">
                    <p class="mk-eyebrow">Lovpakke</p>
                    <div class="mt-1 mb-3 h-px w-10 bg-mk-accent"></div>
                    <h1 class="text-3xl font-bold tracking-tight text-mk-text sm:text-4xl lg:text-[2.5rem] lg:leading-tight">
                        {{ $offer->name }}
                    </h1>
                    @if ($offer->description)
                        <p class="mt-4 max-w-2xl whitespace-pre-line text-lg leading-relaxed text-mk-muted">
                            {{ $offer->description }}
                        </p>
                    @endif
                </div>

            </div>
        </section>

        {{-- Body --}}
        <div class="container mx-auto max-w-6xl px-4 py-12 md:py-16 lg:px-8">
            <div class="grid gap-10 lg:grid-cols-12 lg:items-start lg:gap-12">

                {{-- Left: features --}}
                <div class="space-y-6 lg:col-span-7">
                    <div class="mk-card p-6 md:p-8">
                        <h2 class="font-heading text-lg font-semibold text-mk-text">Inkluderet i pakken</h2>
                        <ul class="mt-6 space-y-4">
                            @if ($offer->theory_lessons > 0)
                                <li class="flex items-center gap-3 text-mk-text">
                                    <svg class="h-5 w-5 shrink-0 text-mk-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    <span>{{ $offer->theory_lessons }} teoritimer</span>
                                </li>
                            @endif
                            @if ($offer->driving_lessons > 0)
                                <li class="flex items-center gap-3 text-mk-text">
                                    <svg class="h-5 w-5 shrink-0 text-mk-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    <span>{{ $offer->driving_lessons }} kørelektioner (45 min)</span>
                                </li>
                            @endif
                            @if ($offer->track_required)
                                <li class="flex items-center gap-3 text-mk-text">
                                    <svg class="h-5 w-5 shrink-0 text-mk-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    <span>Manøvrebane</span>
                                </li>
                            @endif
                            @if ($offer->slippery_required)
                                <li class="flex items-center gap-3 text-mk-text">
                                    <svg class="h-5 w-5 shrink-0 text-mk-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                    <span>Køreteknisk anlæg (glatbane)</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>

                {{-- Right: price + CTA --}}
                <aside class="lg:col-span-5">
                    <div class="sticky top-24 overflow-hidden rounded-2xl border border-mk-border bg-mk-surface shadow-[0_0_40px_-12px_rgba(232,0,29,0.12)]">
                        <div class="h-[3px] w-full bg-mk-accent"></div>
                        <div class="space-y-6 p-6 md:p-8">
                            <div>
                                <p class="text-sm font-medium text-mk-muted">Samlet pris</p>
                                <div class="mt-1 flex items-baseline font-heading">
                                    <span class="text-4xl font-extrabold text-mk-accent md:text-5xl" style="letter-spacing: -0.03em">
                                        {{ number_format($offer->price, 0, ',', '.') }}
                                    </span>
                                    <span class="ml-2 text-xl font-medium text-mk-muted">DKK</span>
                                </div>
                            </div>
                            <a
                                href="/book/{{ $offer->slug }}"
                                class="inline-flex h-12 w-full items-center justify-center rounded-xl bg-mk-accent px-6 text-base font-semibold text-white shadow-[0_8px_32px_-8px_rgba(232,0,29,0.5)] transition-all duration-200 hover:opacity-90 hover:shadow-[0_8px_40px_-8px_rgba(232,0,29,0.65)]"
                            >
                                Tilmeld dig nu
                                <svg class="ml-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </a>
                            <p class="text-center text-sm text-mk-muted">
                                Har du spørgsmål?
                                <a href="{{ route('marketing.contact') }}" class="font-medium text-mk-accent underline-offset-4 hover:underline">Kontakt os</a>
                            </p>
                        </div>
                    </div>
                </aside>

            </div>
        </div>

    </main>
</x-layouts.marketing>
