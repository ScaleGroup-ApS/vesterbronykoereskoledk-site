<x-layouts.marketing title="Om os | Køreskole Pro">
    <main class="bg-mk-base py-24 md:py-32">
        <div class="container mx-auto max-w-3xl px-6 lg:px-8">

            <p class="mk-eyebrow">Om os</p>
            <div class="mt-1 mb-3 h-px w-10 bg-mk-accent"></div>
            <h1 class="text-4xl font-bold text-mk-text sm:text-5xl leading-tight">Om Køreskole Pro</h1>

            <p class="mt-6 text-lg leading-relaxed text-mk-muted">
                Vi er et team af erfarne kørelærere, der brænder for at gøre danske veje sikrere — én elev ad gangen. Siden
                starten har vi fokuseret på tydelig kommunikation, struktureret undervisning og et læringsmiljø, hvor du kan
                stille spørgsmål uden at føle dig presset.
            </p>
            <p class="mt-4 text-lg leading-relaxed text-mk-muted">
                Vores bilpark opdateres løbende, og vi følger udviklingen i både teknologi og færdselsregler, så du møder
                undervisning, der matcher det, du møder ved køreprøven og i trafikken.
            </p>

            {{-- Values --}}
            <div class="mt-16">
                <p class="mk-eyebrow">Vores fundament</p>
                <h2 class="text-2xl font-semibold text-mk-text">Vores værdier</h2>
                <div class="mt-8 space-y-4">

                    <div class="flex gap-4 rounded-xl border border-mk-border bg-mk-surface p-5">
                        <div class="mt-0.5 h-2 w-2 shrink-0 rounded-full bg-mk-accent"></div>
                        <div>
                            <p class="font-semibold text-mk-text">Tryghed</p>
                            <p class="mt-1 text-sm leading-relaxed text-mk-muted">Du skal vide, hvad der forventes, og hvordan vi når målet sammen.</p>
                        </div>
                    </div>

                    <div class="flex gap-4 rounded-xl border border-mk-border bg-mk-surface p-5">
                        <div class="mt-0.5 h-2 w-2 shrink-0 rounded-full bg-mk-accent"></div>
                        <div>
                            <p class="font-semibold text-mk-text">Respekt</p>
                            <p class="mt-1 text-sm leading-relaxed text-mk-muted">Alle lærer i deres eget tempo — vi tilpasser os dig.</p>
                        </div>
                    </div>

                    <div class="flex gap-4 rounded-xl border border-mk-border bg-mk-surface p-5">
                        <div class="mt-0.5 h-2 w-2 shrink-0 rounded-full bg-mk-accent"></div>
                        <div>
                            <p class="font-semibold text-mk-text">Kvalitet</p>
                            <p class="mt-1 text-sm leading-relaxed text-mk-muted">Vi investerer i materialer, biler og løbende efteruddannelse af vores team.</p>
                        </div>
                    </div>

                </div>
            </div>

            <p class="mt-12">
                <a href="{{ route('marketing.contact') }}" class="mk-btn-primary">Kontakt os</a>
            </p>

        </div>
    </main>
</x-layouts.marketing>
