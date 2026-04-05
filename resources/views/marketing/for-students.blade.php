<x-layouts.marketing :title="$metaTitle . ' | Køreskole Pro'">
    <main class="bg-mk-base py-24 md:py-32">
        <div class="container mx-auto max-w-3xl px-6 lg:px-8">

            <p class="mk-eyebrow">Til elever</p>
            <div class="mt-1 mb-3 h-px w-10 bg-mk-accent"></div>
            <h1 class="text-4xl font-bold text-mk-text sm:text-5xl leading-tight">{{ $heading }}</h1>
            <p class="mt-4 max-w-2xl text-lg leading-relaxed text-mk-muted">{{ $lead }}</p>

            <div class="mt-12 space-y-6">
                @foreach ($sections as $section)
                    <section class="rounded-2xl border border-mk-border bg-mk-surface p-6 md:p-8">
                        <h2 class="font-heading text-xl font-semibold text-mk-text">{{ $section['title'] }}</h2>
                        <p class="mt-3 leading-relaxed text-mk-muted">{{ $section['body'] }}</p>
                    </section>
                @endforeach
            </div>

        </div>
    </main>
</x-layouts.marketing>
