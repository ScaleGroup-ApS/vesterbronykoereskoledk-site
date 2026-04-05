<x-layouts.marketing title="FAQ | Køreskole Pro">
    <main class="bg-mk-base py-24 md:py-32">
        <div class="container mx-auto max-w-2xl px-6 lg:px-8">

            <div class="mb-12">
                <p class="mk-eyebrow">FAQ</p>
                <div class="mt-1 mb-3 h-px w-10 bg-mk-accent"></div>
                <h1 class="text-4xl font-bold text-mk-text sm:text-5xl leading-tight">Ofte stillede spørgsmål</h1>
                <div class="mt-5 space-y-3 leading-relaxed text-mk-muted">
                    <p>
                        Her kan du finde svar på det, vi oftest bliver spurgt om. Mange ting er dækket her — så læs
                        gerne igennem, før du ringer. Finder du ikke det, du leder efter, er du altid velkommen til at
                        skrive fra
                        <a href="{{ route('marketing.contact') }}" class="font-medium text-mk-accent underline-offset-4 hover:underline">kontaktsiden</a>.
                    </p>
                    <p class="text-sm text-mk-muted/60">
                        Gebyrer til prøver og krav til papir kan ændre sig. Vi henviser til Færdselsstyrelsen og
                        borger.dk for gældende regler.
                    </p>
                </div>
            </div>

            <div class="space-y-3">
                @foreach ($items as $item)
                    <details class="group rounded-xl border border-mk-border bg-mk-surface open:border-mk-accent/20 open:shadow-[0_0_24px_rgba(232,0,29,0.06)] transition-all duration-200">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-5 text-left text-[15px] font-medium leading-snug text-mk-text marker:content-none [&::-webkit-details-marker]:hidden">
                            {{ $item['question'] }}
                            <svg class="size-4 shrink-0 text-mk-muted transition-transform duration-200 group-open:rotate-180 group-open:text-mk-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                            </svg>
                        </summary>
                        <div class="border-t border-mk-border/60 px-5 pb-5 pt-4 text-sm leading-relaxed text-mk-muted">
                            {{ $item['answer'] }}
                        </div>
                    </details>
                @endforeach
            </div>

        </div>
    </main>
</x-layouts.marketing>
