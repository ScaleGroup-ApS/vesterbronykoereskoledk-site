@props(['title' => config('app.name')])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ $title }} | {{ config('app.name') }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,400;9..40,500;9..40,600;9..40,700;9..40,800&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css'])
        @livewireStyles
    </head>
    <body class="antialiased">
        <div
            class="marketing-public-site marketing-atmosphere min-h-screen bg-mk-base font-sans text-mk-text"
            x-data="{
                scrolled: false,
                mobileOpen: false,
                init() {
                    this.scrolled = window.scrollY > 80;
                }
            }"
            @scroll.window="scrolled = window.scrollY > 80"
        >
            {{-- ── HEADER ── --}}
            <header
                class="sticky top-0 z-50 w-full transition-all duration-300"
                :class="scrolled
                    ? 'border-b border-white/[0.06] bg-[rgba(10,10,10,0.88)] backdrop-blur-xl shadow-[0_4px_40px_-12px_rgba(232,0,29,0.1)]'
                    : 'border-b border-transparent bg-transparent'"
            >
                <div class="container mx-auto flex h-auto min-h-16 flex-wrap items-center justify-between gap-y-3 px-4 py-3 sm:px-6 lg:flex-nowrap lg:px-8 lg:py-0">
                    {{-- Logo --}}
                    <a href="{{ route('home') }}" class="flex shrink-0 items-center gap-2.5 text-white">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-mk-accent text-white shadow-[0_0_20px_-4px_rgba(232,0,29,0.5)]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M19 17H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h3l2-3h4l2 3h3a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2Z"/>
                                <circle cx="7.5" cy="14.5" r="1.5"/>
                                <circle cx="16.5" cy="14.5" r="1.5"/>
                            </svg>
                        </div>
                        <span class="font-heading text-xl font-bold tracking-tight">Køreskole Pro</span>
                    </a>

                    {{-- Desktop nav --}}
                    <nav class="hidden flex-1 items-center justify-center gap-x-4 text-sm font-medium lg:flex xl:gap-x-5" aria-label="Primær navigation">
                        <a href="{{ route('marketing.packages') }}" class="text-sm font-medium transition-colors duration-150 {{ request()->routeIs('marketing.packages*') ? 'text-mk-accent' : 'text-white/65 hover:text-white' }}">
                            Pakker
                        </a>
                        <a href="{{ route('marketing.faq') }}" class="text-sm font-medium transition-colors duration-150 {{ request()->routeIs('marketing.faq') ? 'text-mk-accent' : 'text-white/65 hover:text-white' }}">
                            FAQ
                        </a>
                        <a href="{{ route('marketing.instructors') }}" class="text-sm font-medium transition-colors duration-150 {{ request()->routeIs('marketing.instructors') ? 'text-mk-accent' : 'text-white/65 hover:text-white' }}">
                            Vores kørelærere
                        </a>
                        <a href="{{ route('marketing.about') }}" class="text-sm font-medium transition-colors duration-150 {{ request()->routeIs('marketing.about') ? 'text-mk-accent' : 'text-white/65 hover:text-white' }}">
                            Om os
                        </a>
                        <a href="{{ route('marketing.contact') }}" class="text-sm font-medium transition-colors duration-150 {{ request()->routeIs('marketing.contact') ? 'text-mk-accent' : 'text-white/65 hover:text-white' }}">
                            Kontakt
                        </a>
                    </nav>

                    {{-- Desktop contact info --}}
                    <div class="hidden shrink-0 flex-col items-end gap-0.5 text-right lg:flex">
                        <a
                            href="tel:{{ config('marketing.contact.phone_href') }}"
                            class="font-heading text-lg font-semibold tabular-nums tracking-tight text-white transition-colors hover:text-mk-accent"
                        >
                            {{ config('marketing.contact.phone') }}
                        </a>
                        <a
                            href="mailto:{{ config('marketing.contact.email') }}"
                            class="inline-flex items-center gap-1.5 text-sm text-white/50 transition-colors hover:text-white/80"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            {{ config('marketing.contact.email') }}
                        </a>
                    </div>

                    {{-- Auth + hamburger --}}
                    <div class="flex shrink-0 items-center gap-2 md:gap-3 lg:ml-8 xl:ml-10">
                        {{-- Mobile hamburger --}}
                        <button
                            type="button"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/15 bg-white/5 text-white transition-colors hover:bg-white/10 lg:hidden"
                            :aria-expanded="mobileOpen"
                            :aria-label="mobileOpen ? 'Luk menu' : 'Åbn menu'"
                            @click="mobileOpen = !mobileOpen"
                        >
                            <svg x-show="!mobileOpen" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
                            <svg x-show="mobileOpen" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                        </button>

                        @auth
                            @if(auth()->user()->isAdmin() || auth()->user()->isInstructor())
                                <a
                                    href="/admin"
                                    class="inline-flex h-9 items-center justify-center gap-2 rounded-full bg-mk-accent px-5 py-2 text-sm font-semibold text-white shadow-sm transition-all duration-200 hover:bg-mk-accent-soft hover:scale-[1.04] active:scale-[0.97]"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                                    Kontrolpanel
                                </a>
                            @else
                                <a
                                    href="/app"
                                    class="inline-flex h-9 items-center justify-center gap-2 rounded-full bg-mk-accent px-5 py-2 text-sm font-semibold text-white shadow-sm transition-all duration-200 hover:bg-mk-accent-soft hover:scale-[1.04] active:scale-[0.97]"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                                    Mit panel
                                </a>
                            @endif
                        @else
                            <a
                                href="{{ route('login') }}"
                                class="inline-flex h-9 items-center justify-center gap-2 rounded-full bg-mk-accent px-5 py-2 text-sm font-semibold text-white shadow-sm transition-all duration-200 hover:bg-mk-accent-soft hover:scale-[1.04] active:scale-[0.97]"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>
                                Log ind
                            </a>
                        @endauth
                    </div>
                </div>

                {{-- Mobile menu --}}
                <div
                    x-show="mobileOpen"
                    x-transition:enter="transition duration-200 ease-out"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition duration-150 ease-in"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    class="flex max-h-[min(70vh,calc(100vh-8rem))] flex-col border-t border-white/[0.06] bg-mk-base lg:hidden"
                    style="display: none;"
                >
                    <div class="min-h-0 flex-1 overflow-y-auto px-4 py-4">
                        <nav class="flex flex-col gap-1.5 text-sm font-medium" aria-label="Mobil navigation">
                            <a href="{{ route('marketing.packages') }}"
                                class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm transition-colors {{ request()->routeIs('marketing.packages*') ? 'border-l-2 border-mk-accent pl-[10px] font-semibold text-mk-accent' : 'text-mk-text/80 hover:bg-white/[0.04] hover:text-mk-text' }}"
                                @click="mobileOpen = false">
                                Pakker
                            </a>
                            <a href="{{ route('marketing.faq') }}"
                                class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm transition-colors {{ request()->routeIs('marketing.faq') ? 'border-l-2 border-mk-accent pl-[10px] font-semibold text-mk-accent' : 'text-mk-text/80 hover:bg-white/[0.04] hover:text-mk-text' }}"
                                @click="mobileOpen = false">
                                FAQ
                            </a>
                            <a href="{{ route('marketing.instructors') }}"
                                class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm transition-colors {{ request()->routeIs('marketing.instructors') ? 'border-l-2 border-mk-accent pl-[10px] font-semibold text-mk-accent' : 'text-mk-text/80 hover:bg-white/[0.04] hover:text-mk-text' }}"
                                @click="mobileOpen = false">
                                Vores kørelærere
                            </a>
                            <a href="{{ route('marketing.about') }}"
                                class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm transition-colors {{ request()->routeIs('marketing.about') ? 'border-l-2 border-mk-accent pl-[10px] font-semibold text-mk-accent' : 'text-mk-text/80 hover:bg-white/[0.04] hover:text-mk-text' }}"
                                @click="mobileOpen = false">
                                Om os
                            </a>
                            <a href="{{ route('marketing.contact') }}"
                                class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm transition-colors {{ request()->routeIs('marketing.contact') ? 'border-l-2 border-mk-accent pl-[10px] font-semibold text-mk-accent' : 'text-mk-text/80 hover:bg-white/[0.04] hover:text-mk-text' }}"
                                @click="mobileOpen = false">
                                Kontakt
                            </a>
                        </nav>
                    </div>
                    <div class="shrink-0 border-t border-white/[0.06] bg-mk-surface/60 px-4 py-4">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-widest text-mk-muted">Kontakt</p>
                        <ul class="space-y-3 text-sm">
                            <li class="flex items-start gap-2.5 text-mk-text">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 shrink-0 text-mk-accent" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                <span>Køregade 123, København</span>
                            </li>
                            <li>
                                <a
                                    href="tel:{{ config('marketing.contact.phone_href') }}"
                                    class="inline-flex items-center gap-2 font-semibold tabular-nums text-mk-text transition-colors hover:text-mk-accent"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-mk-accent" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.99 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.9 1.18h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9a16 16 0 0 0 6.29 6.29l1.06-1.06a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92Z"/></svg>
                                    {{ config('marketing.contact.phone') }}
                                </a>
                            </li>
                            <li>
                                <a
                                    href="mailto:{{ config('marketing.contact.email') }}"
                                    class="inline-flex items-center gap-2 break-all text-mk-muted transition-colors hover:text-mk-text"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-mk-accent" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                    {{ config('marketing.contact.email') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            {{-- ── PAGE CONTENT ── --}}
            {{ $slot }}

            {{-- ── FOOTER ── --}}
            <footer class="marketing-footer-dark overflow-hidden py-14">
                <div class="container mx-auto grid gap-10 px-4 sm:grid-cols-2 lg:grid-cols-4 lg:px-8">
                    {{-- Brand --}}
                    <div class="sm:col-span-2 lg:col-span-1">
                        <a href="{{ route('home') }}" class="mb-6 flex items-center gap-2.5 text-white">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-mk-accent text-white shadow-[0_0_20px_-4px_rgba(232,0,29,0.4)]">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 17H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h3l2-3h4l2 3h3a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2Z"/><circle cx="7.5" cy="14.5" r="1.5"/><circle cx="16.5" cy="14.5" r="1.5"/></svg>
                            </div>
                            <span class="font-heading text-xl font-bold tracking-tight">Køreskole Pro</span>
                        </a>
                        <p class="marketing-footer-muted max-w-xs text-sm leading-relaxed">
                            Vi forbinder motiverede elever med dygtige kørelærere. Vi gør det nemt, sikkert og sjovt at tage kørekort.
                        </p>
                    </div>

                    {{-- Contact --}}
                    <div>
                        <h4 class="mb-6 text-xs font-semibold uppercase tracking-widest text-mk-muted">Kontakt os</h4>
                        <ul class="space-y-4 text-sm">
                            <li class="marketing-footer-muted flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-mk-accent" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                <span>Køregade 123, København</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-mk-accent" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.99 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.9 1.18h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9a16 16 0 0 0 6.29 6.29l1.06-1.06a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92Z"/></svg>
                                <a href="tel:{{ config('marketing.contact.phone_href') }}" class="text-white/70 transition-colors hover:text-white">
                                    {{ config('marketing.contact.phone') }}
                                </a>
                            </li>
                            <li class="flex items-center gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 text-mk-accent" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                <a href="mailto:{{ config('marketing.contact.email') }}" class="text-white/70 transition-colors hover:text-white">
                                    {{ config('marketing.contact.email') }}
                                </a>
                            </li>
                        </ul>
                    </div>

                    {{-- Useful links --}}
                    <div>
                        <h4 class="mb-6 text-xs font-semibold uppercase tracking-widest text-mk-muted">Brugbare links</h4>
                        <ul class="space-y-3">
                            @foreach([
                                ['label' => 'Rådet for Sikker Trafik', 'href' => 'https://www.sikkertrafik.dk/'],
                                ['label' => 'Dansk Kørelærer-Union', 'href' => 'https://www.dku.dk/'],
                                ['label' => 'Vejdirektoratet', 'href' => 'https://www.vejdirektoratet.dk/'],
                                ['label' => 'FDM', 'href' => 'https://fdm.dk/'],
                                ['label' => 'Køreprøvebooking', 'href' => 'https://koreprovebooking.dk/'],
                                ['label' => 'Færdselsstyrelsen', 'href' => 'https://fstyr.dk/da/'],
                            ] as $link)
                                <li>
                                    <a
                                        href="{{ $link['href'] }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="group flex items-start gap-2 text-sm text-white/50 transition-colors hover:text-mk-accent"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 shrink-0 text-mk-border transition-colors group-hover:text-mk-accent" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg>
                                        <span>{{ $link['label'] }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Legal --}}
                    <div>
                        <h4 class="mb-6 text-xs font-semibold uppercase tracking-widest text-mk-muted">Juridisk</h4>
                        <ul class="space-y-4 text-sm">
                            <li>
                                <a href="{{ route('marketing.terms') }}" class="text-white/50 transition-colors hover:text-mk-accent">
                                    Handelsbetingelser
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('marketing.privacy') }}" class="text-white/50 transition-colors hover:text-mk-accent">
                                    Privatlivspolitik
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('marketing.cookies') }}" class="text-white/50 transition-colors hover:text-mk-accent">
                                    Cookiepolitik
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="container mx-auto mt-12 border-t border-white/[0.06] px-4 pt-8 text-center text-sm text-mk-muted lg:px-8">
                    &copy; {{ date('Y') }} Køreskole Pro. Alle rettigheder forbeholdes.
                </div>
            </footer>
        </div>

        @livewireScripts
    </body>
</html>
