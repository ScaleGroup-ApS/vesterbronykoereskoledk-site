<x-layouts.marketing title="Kontakt | Køreskole Pro">
    <main class="bg-mk-base py-24 md:py-32">
        <div class="container mx-auto max-w-6xl px-6 lg:px-8">

            <div class="mb-12">
                <p class="mk-eyebrow">Kontakt</p>
                <div class="mt-1 mb-3 h-px w-10 bg-mk-accent"></div>
                <h1 class="text-4xl font-bold text-mk-text sm:text-5xl leading-tight">Kontakt os</h1>
                <p class="mt-3 text-lg text-mk-muted">
                    Skriv til os — vælg gerne pakke og hvornår du ønsker at starte. Vi svarer på hverdage.
                </p>
            </div>

            <div class="grid gap-12 lg:grid-cols-2 lg:gap-16">

                {{-- Left: contact info --}}
                <div class="space-y-8">

                    @if ($success)
                        <div class="flex items-start gap-3 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-5 py-4 text-emerald-400">
                            <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            <div>
                                <p class="font-semibold text-emerald-300">Beskeden er sendt</p>
                                <p class="mt-0.5 text-sm text-emerald-400/80">{{ $success }}</p>
                            </div>
                        </div>
                    @endif

                    <div>
                        <h2 class="text-xs font-semibold uppercase tracking-widest text-mk-muted">Direkte kontakt</h2>
                        <ul class="mt-6 space-y-6">
                            <li class="flex items-start gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-mk-accent/10">
                                    {{-- MapPin icon --}}
                                    <svg class="h-5 w-5 text-mk-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-mk-text">Adresse</p>
                                    <p class="mt-0.5 text-mk-muted">Køregade 123, København</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-mk-accent/10">
                                    {{-- Phone icon --}}
                                    <svg class="h-5 w-5 text-mk-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-mk-text">Telefon</p>
                                    <a href="tel:{{ $phone_href }}" class="mt-0.5 text-mk-muted hover:text-mk-accent transition-colors">{{ $phone }}</a>
                                </div>
                            </li>
                            <li class="flex items-start gap-4">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-mk-accent/10">
                                    {{-- Mail icon --}}
                                    <svg class="h-5 w-5 text-mk-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-semibold text-mk-text">E-mail</p>
                                    <a href="mailto:{{ $email }}" class="mt-0.5 text-mk-muted hover:text-mk-accent transition-colors">{{ $email }}</a>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="rounded-2xl border border-mk-border bg-mk-surface p-6">
                        <h2 class="font-semibold text-mk-text">Åbningstider (kontor)</h2>
                        <p class="mt-2 text-sm leading-relaxed text-mk-muted">
                            Mandag–fredag 9–17 &middot; Lørdag 9–13 &middot; Søndag lukket. Køretimer kan bookes uden for
                            kontortid via portalen.
                        </p>
                    </div>

                </div>

                {{-- Right: contact form --}}
                <div class="rounded-2xl border border-mk-border bg-mk-surface p-6 md:p-8">
                    <h2 class="font-heading text-lg font-semibold text-mk-text">Send en besked</h2>
                    <p class="mt-1 text-sm text-mk-muted">
                        Udfyld formularen — vælg den pakke du kigger på, og hvornår du helst vil starte på hold.
                    </p>

                    <form method="POST" action="{{ route('marketing.contact.store') }}" class="mt-8 space-y-6">
                        @csrf

                        {{-- Name + Email --}}
                        <div class="grid gap-5 sm:grid-cols-2">
                            <div class="space-y-1.5">
                                <label for="name" class="block text-sm text-mk-text/80">Navn *</label>
                                <input
                                    id="name"
                                    name="name"
                                    type="text"
                                    required
                                    autocomplete="name"
                                    value="{{ old('name') }}"
                                    class="flex w-full rounded-md border border-mk-border bg-mk-surface-2 px-3 py-2 text-sm text-mk-text shadow-xs placeholder:text-mk-muted/50 outline-none transition-[color,box-shadow] focus-visible:border-mk-accent/40 focus-visible:ring-[3px] focus-visible:ring-mk-accent/20"
                                />
                                @error('name')
                                    <p class="text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-1.5">
                                <label for="email" class="block text-sm text-mk-text/80">E-mail *</label>
                                <input
                                    id="email"
                                    name="email"
                                    type="email"
                                    required
                                    autocomplete="email"
                                    value="{{ old('email') }}"
                                    class="flex w-full rounded-md border border-mk-border bg-mk-surface-2 px-3 py-2 text-sm text-mk-text shadow-xs placeholder:text-mk-muted/50 outline-none transition-[color,box-shadow] focus-visible:border-mk-accent/40 focus-visible:ring-[3px] focus-visible:ring-mk-accent/20"
                                />
                                @error('email')
                                    <p class="text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Phone --}}
                        <div class="space-y-1.5">
                            <label for="phone" class="block text-sm text-mk-text/80">Telefon</label>
                            <input
                                id="phone"
                                name="phone"
                                type="tel"
                                autocomplete="tel"
                                value="{{ old('phone') }}"
                                class="flex w-full rounded-md border border-mk-border bg-mk-surface-2 px-3 py-2 text-sm text-mk-text shadow-xs placeholder:text-mk-muted/50 outline-none transition-[color,box-shadow] focus-visible:border-mk-accent/40 focus-visible:ring-[3px] focus-visible:ring-mk-accent/20"
                            />
                            @error('phone')
                                <p class="text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Package + Hold start selection box --}}
                        <div class="rounded-xl border border-mk-border bg-mk-surface-2/50 p-4 sm:p-5">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-mk-text">Pakke og holdstart</p>
                                    <p class="mt-1 text-sm leading-relaxed text-mk-muted">
                                        Vi hjælper dig gerne uden at du vælger — men hvis du allerede ved, hvad du kigger på, kan vi svare mere konkret.
                                    </p>
                                </div>
                                <a
                                    href="{{ route('marketing.packages') }}"
                                    class="inline-flex shrink-0 items-center justify-center rounded-lg border border-mk-border bg-mk-surface px-3 py-2 text-sm font-medium text-mk-text/80 shadow-sm transition-colors hover:border-mk-accent/30 hover:text-mk-text sm:py-1.5"
                                >
                                    Sammenlign pakker
                                </a>
                            </div>

                            {{-- Offer selection --}}
                            <fieldset class="mt-6 space-y-3" x-data="{ offer: '{{ old('offer_id', '') }}' }">
                                <legend class="flex items-center gap-2 text-sm font-semibold text-mk-text">
                                    {{-- Package icon --}}
                                    <svg class="h-4 w-4 text-mk-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                                    </svg>
                                    Pakke
                                </legend>
                                <p class="text-sm text-mk-muted">Hvilken pakke er du mest interesseret i?</p>
                                <div class="space-y-2">
                                    <label
                                        :class="offer === '' ? 'border-mk-accent bg-mk-accent/[0.06] ring-1 ring-mk-accent/20' : 'border-mk-border bg-mk-surface hover:border-mk-border/80'"
                                        class="flex cursor-pointer gap-3 rounded-lg border p-3 transition-colors"
                                    >
                                        <input
                                            type="radio"
                                            name="offer_id"
                                            value=""
                                            class="mt-1 size-4 shrink-0 accent-[#E8001D]"
                                            x-model="offer"
                                        />
                                        <span class="text-sm font-medium leading-snug text-mk-text">
                                            Ikke valgt endnu — vil gerne have anbefaling
                                        </span>
                                    </label>
                                    @foreach ($offers as $offer)
                                        <label
                                            :class="offer === '{{ $offer->id }}' ? 'border-mk-accent bg-mk-accent/[0.06] ring-1 ring-mk-accent/20' : 'border-mk-border bg-mk-surface hover:border-mk-border/80'"
                                            class="flex cursor-pointer gap-3 rounded-lg border p-3 transition-colors"
                                        >
                                            <input
                                                type="radio"
                                                name="offer_id"
                                                value="{{ $offer->id }}"
                                                class="mt-1 size-4 shrink-0 accent-[#E8001D]"
                                                x-model="offer"
                                                @if (old('offer_id') == $offer->id) checked @endif
                                            />
                                            <span class="min-w-0 flex-1 text-sm leading-snug">
                                                <span class="font-medium text-mk-text">{{ $offer->name }}</span>
                                                <span class="mt-0.5 block text-mk-muted">{{ number_format($offer->price, 0, ',', '.') }} kr.</span>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('offer_id')
                                    <p class="text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </fieldset>

                            {{-- Hold start selection --}}
                            <fieldset class="mt-8 space-y-3" x-data="{ holdStart: '{{ old('preferred_hold_start', '') }}' }">
                                <legend class="flex items-center gap-2 text-sm font-semibold text-mk-text">
                                    {{-- CalendarClock icon --}}
                                    <svg class="h-4 w-4 text-mk-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                                    </svg>
                                    Ønsket holdstart
                                </legend>
                                <p class="text-sm text-mk-muted">Hvornår vil du helst starte?</p>
                                <div class="grid gap-2 sm:grid-cols-2">
                                    <label
                                        :class="holdStart === '' ? 'border-mk-accent bg-mk-accent/[0.06] ring-1 ring-mk-accent/20' : 'border-mk-border bg-mk-surface hover:border-mk-border/80'"
                                        class="flex cursor-pointer items-start gap-3 rounded-lg border p-3 transition-colors sm:col-span-2"
                                    >
                                        <input
                                            type="radio"
                                            name="preferred_hold_start"
                                            value=""
                                            class="mt-1 size-4 shrink-0 accent-[#E8001D]"
                                            x-model="holdStart"
                                        />
                                        <span class="text-sm font-medium leading-snug text-mk-text">Ikke valgt endnu</span>
                                    </label>
                                    @foreach ($holdStartOptions as $option)
                                        <label
                                            :class="holdStart === '{{ $option['value'] }}' ? 'border-mk-accent bg-mk-accent/[0.06] ring-1 ring-mk-accent/20' : 'border-mk-border bg-mk-surface hover:border-mk-border/80'"
                                            class="flex cursor-pointer gap-3 rounded-lg border p-3 transition-colors"
                                        >
                                            <input
                                                type="radio"
                                                name="preferred_hold_start"
                                                value="{{ $option['value'] }}"
                                                class="mt-1 size-4 shrink-0 accent-[#E8001D]"
                                                x-model="holdStart"
                                                @if (old('preferred_hold_start') === $option['value']) checked @endif
                                            />
                                            <span class="text-sm font-medium leading-snug text-mk-text">{{ $option['label'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('preferred_hold_start')
                                    <p class="text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </fieldset>
                        </div>

                        {{-- Message --}}
                        <div class="space-y-1.5">
                            <label for="message" class="block text-sm text-mk-text/80">Besked</label>
                            <textarea
                                id="message"
                                name="message"
                                rows="5"
                                placeholder="Fx spørgsmål om hold, tilmelding eller om du vil bookes til intro …"
                                class="flex min-h-[120px] w-full rounded-md border border-mk-border bg-mk-surface-2 px-3 py-2 text-sm text-mk-text shadow-xs placeholder:text-mk-muted/50 outline-none transition-[color,box-shadow] focus-visible:border-mk-accent/40 focus-visible:ring-[3px] focus-visible:ring-mk-accent/20 md:text-sm"
                            >{{ old('message') }}</textarea>
                            @error('message')
                                <p class="text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <button
                            type="submit"
                            class="inline-flex h-11 items-center justify-center rounded-full bg-mk-accent px-6 text-sm font-semibold text-white transition-all duration-200 hover:opacity-90 hover:scale-[1.02] w-full sm:w-auto"
                        >
                            Send besked
                        </button>

                    </form>
                </div>

            </div>
        </div>
    </main>
</x-layouts.marketing>
