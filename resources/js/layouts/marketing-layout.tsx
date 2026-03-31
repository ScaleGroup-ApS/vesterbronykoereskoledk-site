import { Link, usePage } from '@inertiajs/react';
import { Car, ChevronRight, LayoutDashboard, Mail, MapPin, Menu, Phone, UserRound, X } from 'lucide-react';
import { type ReactNode, useEffect, useState } from 'react';
import { MarketingNavDesktop, MarketingNavMobile } from '@/components/marketing/marketing-nav';
import { cn } from '@/lib/utils';
import { dashboard, home, login } from '@/routes';
import {
    cookies,
    privacy,
    terms,
} from '@/routes/marketing';
import type { MarketingContact } from '@/types/marketing-contact';

type MarketingLayoutProps = {
    children: ReactNode;
};

const usefulLinks: { label: string; href: string }[] = [
    { label: 'Rådet for Sikker Trafik', href: 'https://www.sikkertrafik.dk/' },
    { label: 'Dansk Kørelærer-Union', href: 'https://www.dku.dk/' },
    { label: 'Vejdirektoratet', href: 'https://www.vejdirektoratet.dk/' },
    { label: 'FDM', href: 'https://fdm.dk/' },
    { label: 'Køreprøvebooking', href: 'https://koreprovebooking.dk/' },
    { label: 'Færdselsstyrelsen', href: 'https://fstyr.dk/da/' },
];

export default function MarketingLayout({ children }: MarketingLayoutProps) {
    const { auth, marketingContact } = usePage().props as {
        auth: { user: unknown };
        marketingContact: MarketingContact;
    };
    const [mobileOpen, setMobileOpen] = useState(false);
    const [scrolled, setScrolled] = useState(false);

    useEffect(() => {
        const onScroll = () => setScrolled(window.scrollY > 80);
        window.addEventListener('scroll', onScroll, { passive: true });
        return () => window.removeEventListener('scroll', onScroll);
    }, []);

    const tel = marketingContact.phone_href;
    const phoneLabel = marketingContact.phone;
    const email = marketingContact.email;

    return (
        <div className="marketing-public-site marketing-atmosphere min-h-screen bg-mk-base font-sans text-mk-text">
            <header
                className={cn(
                    'sticky top-0 z-50 w-full transition-all duration-300',
                    scrolled
                        ? 'border-b border-white/[0.06] bg-[rgba(10,10,10,0.88)] backdrop-blur-xl shadow-[0_4px_40px_-12px_rgba(232,0,29,0.1)]'
                        : 'border-b border-transparent bg-transparent',
                )}
            >
                <div className="container mx-auto flex h-auto min-h-16 flex-wrap items-center justify-between gap-y-3 px-4 py-3 sm:px-6 lg:flex-nowrap lg:px-8 lg:py-0">
                    <Link href={home()} className="flex shrink-0 items-center gap-2.5 text-white">
                        <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-mk-accent text-white shadow-[0_0_20px_-4px_rgba(232,0,29,0.5)]">
                            <Car size={18} strokeWidth={2.5} />
                        </div>
                        <span className="font-heading text-xl font-bold tracking-tight">Køreskole Pro</span>
                    </Link>

                    <MarketingNavDesktop />

                    <div className="hidden shrink-0 flex-col items-end gap-0.5 text-right lg:flex">
                        <a
                            href={`tel:${tel}`}
                            className="font-heading text-lg font-semibold tabular-nums tracking-tight text-white transition-colors hover:text-mk-accent"
                        >
                            {phoneLabel}
                        </a>
                        <a
                            href={`mailto:${email}`}
                            className="inline-flex items-center gap-1.5 text-sm text-white/50 transition-colors hover:text-white/80"
                        >
                            <Mail className="size-3.5 shrink-0" aria-hidden />
                            {email}
                        </a>
                    </div>

                    <div className="flex shrink-0 items-center gap-2 md:gap-3 lg:ml-8 xl:ml-10">
                        <button
                            type="button"
                            className="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/15 bg-white/5 text-white transition-colors hover:bg-white/10 lg:hidden"
                            aria-expanded={mobileOpen}
                            aria-label={mobileOpen ? 'Luk menu' : 'Åbn menu'}
                            onClick={() => setMobileOpen((o) => !o)}
                        >
                            {mobileOpen ? <X size={18} /> : <Menu size={18} />}
                        </button>
                        {auth.user ? (
                            <Link
                                href={dashboard()}
                                className="inline-flex h-9 items-center justify-center gap-2 rounded-full bg-mk-accent px-5 py-2 text-sm font-semibold text-white shadow-sm transition-all duration-200 hover:bg-mk-accent-soft hover:scale-[1.04] active:scale-[0.97]"
                            >
                                <LayoutDashboard className="size-4 shrink-0" aria-hidden />
                                Kontrolpanel
                            </Link>
                        ) : (
                            <Link
                                href={login()}
                                className="inline-flex h-9 items-center justify-center gap-2 rounded-full bg-mk-accent px-5 py-2 text-sm font-semibold text-white shadow-sm transition-all duration-200 hover:bg-mk-accent-soft hover:scale-[1.04] active:scale-[0.97]"
                            >
                                <UserRound className="size-4 shrink-0" aria-hidden />
                                Log ind
                            </Link>
                        )}
                    </div>
                </div>

                {mobileOpen ? (
                    <div className="flex max-h-[min(70vh,calc(100vh-8rem))] flex-col border-t border-white/[0.06] bg-mk-base lg:hidden">
                        <div className="min-h-0 flex-1 overflow-y-auto px-4 py-4">
                            <MarketingNavMobile onNavigate={() => setMobileOpen(false)} />
                        </div>
                        <div className="shrink-0 border-t border-white/[0.06] bg-mk-surface/60 px-4 py-4">
                            <p className="mb-3 text-xs font-semibold uppercase tracking-widest text-mk-muted">
                                Kontakt
                            </p>
                            <ul className="space-y-3 text-sm">
                                <li className="flex items-start gap-2.5 text-mk-text">
                                    <MapPin className="mt-0.5 size-4 shrink-0 text-mk-accent" aria-hidden />
                                    <span>Køregade 123, København</span>
                                </li>
                                <li>
                                    <a
                                        href={`tel:${tel}`}
                                        className="inline-flex items-center gap-2 font-semibold tabular-nums text-mk-text hover:text-mk-accent transition-colors"
                                    >
                                        <Phone className="size-4 shrink-0 text-mk-accent" aria-hidden />
                                        {phoneLabel}
                                    </a>
                                </li>
                                <li>
                                    <a
                                        href={`mailto:${email}`}
                                        className="inline-flex items-center gap-2 break-all text-mk-muted hover:text-mk-text transition-colors"
                                    >
                                        <Mail className="size-4 shrink-0 text-mk-accent" aria-hidden />
                                        {email}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                ) : null}
            </header>

            {children}

            <footer className="marketing-footer-dark overflow-hidden py-14">
                <div className="container mx-auto grid gap-10 px-4 sm:grid-cols-2 lg:grid-cols-4 lg:px-8">
                    <div className="sm:col-span-2 lg:col-span-1">
                        <Link href={home()} className="mb-6 flex items-center gap-2.5 text-white">
                            <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-mk-accent text-white shadow-[0_0_20px_-4px_rgba(232,0,29,0.4)]">
                                <Car size={18} strokeWidth={2.5} />
                            </div>
                            <span className="font-heading text-xl font-bold tracking-tight">Køreskole Pro</span>
                        </Link>
                        <p className="marketing-footer-muted max-w-xs text-sm leading-relaxed">
                            Vi forbinder motiverede elever med dygtige kørelærere. Vi gør det nemt, sikkert og sjovt at tage kørekort.
                        </p>
                    </div>
                    <div>
                        <h4 className="mb-6 text-xs font-semibold uppercase tracking-widest text-mk-muted">Kontakt os</h4>
                        <ul className="space-y-4 text-sm">
                            <li className="marketing-footer-muted flex items-center gap-3">
                                <MapPin size={15} className="shrink-0 text-mk-accent" aria-hidden />
                                <span>Køregade 123, København</span>
                            </li>
                            <li className="flex items-center gap-3">
                                <Phone size={15} className="shrink-0 text-mk-accent" aria-hidden />
                                <a href={`tel:${tel}`} className="text-white/70 hover:text-white transition-colors">
                                    {phoneLabel}
                                </a>
                            </li>
                            <li className="flex items-center gap-3">
                                <Mail size={15} className="shrink-0 text-mk-accent" aria-hidden />
                                <a href={`mailto:${email}`} className="text-white/70 hover:text-white transition-colors">
                                    {email}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h4 className="mb-6 text-xs font-semibold uppercase tracking-widest text-mk-muted">Brugbare links</h4>
                        <ul className="space-y-3">
                            {usefulLinks.map((item) => (
                                <li key={item.href}>
                                    <a
                                        href={item.href}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="group flex items-start gap-2 text-sm text-white/50 transition-colors hover:text-mk-accent"
                                    >
                                        <ChevronRight
                                            className="mt-0.5 h-4 w-4 shrink-0 text-mk-border transition-colors group-hover:text-mk-accent"
                                            aria-hidden
                                        />
                                        <span>{item.label}</span>
                                    </a>
                                </li>
                            ))}
                        </ul>
                    </div>
                    <div>
                        <h4 className="mb-6 text-xs font-semibold uppercase tracking-widest text-mk-muted">Juridisk</h4>
                        <ul className="space-y-4 text-sm">
                            <li>
                                <Link href={terms.url()} className="text-white/50 hover:text-mk-accent transition-colors">
                                    Handelsbetingelser
                                </Link>
                            </li>
                            <li>
                                <Link href={privacy.url()} className="text-white/50 hover:text-mk-accent transition-colors">
                                    Privatlivspolitik
                                </Link>
                            </li>
                            <li>
                                <Link href={cookies.url()} className="text-white/50 hover:text-mk-accent transition-colors">
                                    Cookiepolitik
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
                <div className="container mx-auto mt-12 border-t border-white/[0.06] px-4 pt-8 text-center text-sm text-mk-muted lg:px-8">
                    &copy; {new Date().getFullYear()} Køreskole Pro. Alle rettigheder forbeholdes.
                </div>
            </footer>
        </div>
    );
}
