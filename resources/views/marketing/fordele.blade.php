<x-layouts.marketing title="Fordele | Køreskole Pro">
    <main class="overflow-hidden bg-mk-base py-24 md:py-32">
        <div class="container mx-auto max-w-7xl px-6 lg:px-8">

            <div class="mx-auto mb-16 max-w-2xl text-center">
                <p class="mk-eyebrow">Fordele</p>
                <div class="mx-auto mt-1 mb-3 h-px w-10 bg-mk-accent"></div>
                <h1 class="text-4xl font-bold text-mk-text sm:text-5xl leading-tight">Hvorfor vælge os?</h1>
                <p class="mt-4 text-lg text-mk-muted">Alt hvad du har brug for for at bestå din køreprøve — og mere til.</p>
            </div>

            <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">

                {{-- Feature 1: Høj beståelsesprocent --}}
                <div class="mk-card flex flex-col items-center p-8 text-center transition-transform duration-200 hover:-translate-y-2">
                    <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-mk-accent/10 text-mk-accent">
                        {{-- ShieldCheck icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" width="30" height="30" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                        </svg>
                    </div>
                    <h2 class="mb-3 font-heading text-xl font-bold text-mk-text">Høj beståelsesprocent</h2>
                    <p class="text-sm leading-relaxed text-mk-muted">
                        Vores skræddersyede undervisningsmetoder sikrer, at du er fuldt forberedt til at bestå både teori- og køreprøve.
                    </p>
                </div>

                {{-- Feature 2: Moderne biler --}}
                <div class="mk-card flex flex-col items-center p-8 text-center transition-transform duration-200 hover:-translate-y-2">
                    <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-mk-accent/10 text-mk-accent">
                        {{-- Car icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" width="30" height="30" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                        </svg>
                    </div>
                    <h2 class="mb-3 font-heading text-xl font-bold text-mk-text">Moderne biler</h2>
                    <p class="text-sm leading-relaxed text-mk-muted">
                        Lær at køre i sikre, letkørte og miljøvenlige biler, der er udstyret med de nyeste sikkerhedsfunktioner.
                    </p>
                </div>

                {{-- Feature 3: Fleksibel planlægning --}}
                <div class="mk-card flex flex-col items-center p-8 text-center transition-transform duration-200 hover:-translate-y-2 sm:col-span-2 lg:col-span-1">
                    <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-mk-accent/10 text-mk-accent">
                        {{-- Clock icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" width="30" height="30" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <h2 class="mb-3 font-heading text-xl font-bold text-mk-text">Fleksibel planlægning</h2>
                    <p class="text-sm leading-relaxed text-mk-muted">
                        Book dine køre- og teoritimer på tidspunkter, der passer til din travle hverdag via vores online portal.
                    </p>
                </div>

            </div>
        </div>
    </main>
</x-layouts.marketing>
