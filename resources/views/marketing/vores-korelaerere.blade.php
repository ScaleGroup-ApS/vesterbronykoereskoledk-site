<x-layouts.marketing title="Vores kørelærere | Køreskole Pro">
    <main class="bg-mk-base py-24 md:py-32">
        <div class="container mx-auto max-w-4xl px-6 lg:px-8">

            <div class="mb-12">
                <p class="mk-eyebrow">Teamet</p>
                <div class="mt-1 mb-3 h-px w-10 bg-mk-accent"></div>
                <h1 class="text-4xl font-bold text-mk-text sm:text-5xl leading-tight">Vores kørelærere</h1>
                <p class="mt-4 max-w-2xl text-lg text-mk-muted">
                    Vi arbejder i teams, så du møder faste kørelærere, der kender dig og dit forløb. Her er de hold, der underviser hos os.
                </p>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                @forelse ($teams as $team)
                    <div class="mk-card p-6 transition-transform duration-200 hover:-translate-y-1.5">
                        <h2 class="font-heading text-lg font-semibold text-mk-text">{{ $team->name }}</h2>
                        @if ($team->description)
                            <p class="mt-2 text-sm leading-relaxed text-mk-muted">{{ $team->description }}</p>
                        @else
                            <p class="mt-2 text-sm italic text-mk-muted/60">Beskrivelse kommer snart.</p>
                        @endif
                    </div>
                @empty
                    <p class="col-span-full py-12 text-center text-mk-muted">
                        Ingen teams er registreret endnu. Kom tilbage senere — eller kontakt os direkte.
                    </p>
                @endforelse
            </div>

        </div>
    </main>
</x-layouts.marketing>
