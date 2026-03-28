import { Link, usePage } from '@inertiajs/react';
import { Car, ChevronRight, LayoutDashboard, Mail, MapPin, Menu, Phone, UserRound, X } from 'lucide-react';
import { type ReactNode, useState } from 'react';
import { MarketingNavDesktop, MarketingNavMobile } from '@/components/marketing/marketing-nav';
import type { MarketingContact } from '@/types/marketing-contact';
import { dashboard, home, login } from '@/routes';
import {
    contact,
    cookies,
    privacy,
    terms,
} from '@/routes/marketing';

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

    const tel = marketingContact.phone_href;
    const phoneLabel = marketingContact.phone;
    const email = marketingContact.email;

    return (
        <div className="marketing-public-site marketing-atmosphere min-h-screen bg-white font-sans text-foreground selection:bg-primary selection:text-primary-foreground">
            <header className="marketing-header-bar sticky top-0 z-50 w-full">
                <div className="container mx-auto flex h-auto min-h-16 flex-wrap items-center justify-between gap-y-3 px-4 py-3 lg:flex-nowrap lg:px-8 lg:py-0">
                    <Link href={home()} className="flex shrink-0 items-center gap-2 text-white">
                        <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-primary text-primary-foreground shadow-[0_0_20px_-4px_rgba(56,189,248,0.35)]">
                            <Car size={20} />
                        </div>
                        <span className="text-xl font-bold tracking-tight">Køreskole Pro</span>
                    </Link>

                    <MarketingNavDesktop />

                    <div className="hidden shrink-0 flex-col items-end gap-0.5 text-right lg:flex">
                        <a
                            href={`tel:${tel}`}
                            className="text-lg font-semibold tabular-nums tracking-tight text-white hover:text-sky-300"
                        >
                            {phoneLabel}
                        </a>
                        <a
                            href={`mailto:${email}`}
                            className="inline-flex items-center gap-1.5 text-sm text-white/70 transition-colors hover:text-white"
                        >
                            <Mail className="size-3.5 shrink-0 opacity-80" aria-hidden />
                            {email}
                        </a>
                    </div>

                    <div className="flex shrink-0 items-center gap-2 md:gap-3 lg:ml-8 xl:ml-10">
                        <button
                            type="button"
                            className="inline-flex h-9 w-9 items-center justify-center rounded-md border border-white/20 bg-white/10 text-white hover:bg-white/15 lg:hidden"
                            aria-expanded={mobileOpen}
                            aria-label={mobileOpen ? 'Luk menu' : 'Åbn menu'}
                            onClick={() => setMobileOpen((o) => !o)}
                        >
                            {mobileOpen ? <X size={20} /> : <Menu size={20} />}
                        </button>
                        {auth.user ? (
                            <Link
                                href={dashboard()}
                                className="inline-flex h-9 items-center justify-center gap-2 rounded-lg bg-sky-500 px-4 py-2 text-sm font-semibold text-slate-950 shadow-sm transition-colors hover:bg-sky-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-300 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950"
                            >
                                <LayoutDashboard className="size-4 shrink-0" aria-hidden />
                                Kontrolpanel
                            </Link>
                        ) : (
                            <Link
                                href={login()}
                                className="inline-flex h-9 items-center justify-center gap-2 rounded-lg bg-sky-500 px-4 py-2 text-sm font-semibold text-slate-950 shadow-sm transition-colors hover:bg-sky-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-300 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950"
                            >
                                <UserRound className="size-4 shrink-0" aria-hidden />
                                Log ind
                            </Link>
                        )}
                    </div>
                </div>

                {mobileOpen ? (
                    <div className="border-t border-border bg-background px-4 py-4 lg:hidden max-h-[min(70vh,calc(100vh-8rem))] overflow-y-auto">
                        <div className="mb-4 flex flex-col gap-1 rounded-xl border border-border bg-muted/20 p-4">
                            <a
                                href={`tel:${tel}`}
                                className="text-xl font-semibold tabular-nums text-foreground hover:text-primary"
                            >
                                {phoneLabel}
                            </a>
                            <a
                                href={`mailto:${email}`}
                                className="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-foreground"
                            >
                                <Mail className="size-4 shrink-0" aria-hidden />
                                {email}
                            </a>
                        </div>
                        <MarketingNavMobile onNavigate={() => setMobileOpen(false)} />
                    </div>
                ) : null}
            </header>

            {children}

            <footer className="marketing-footer-dark overflow-hidden py-12">
                <div className="container mx-auto grid gap-10 px-4 sm:grid-cols-2 lg:grid-cols-4 lg:px-8">
                    <div className="sm:col-span-2 lg:col-span-1">
                        <Link href={home()} className="mb-6 flex items-center gap-2 text-white">
                            <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-500 text-slate-950 shadow-[0_0_20px_-4px_rgba(56,189,248,0.45)]">
                                <Car size={20} />
                            </div>
                            <span className="text-xl font-bold tracking-tight">Køreskole Pro</span>
                        </Link>
                        <p className="marketing-footer-muted max-w-xs text-sm leading-relaxed">
                            Vi forbinder motiverede elever med dygtige kørelærere. Vi gør det nemt, sikkert og sjovt at tage kørekort.
                        </p>
                    </div>
                    <div>
                        <h4 className="mb-6 text-sm font-semibold uppercase tracking-wide text-white">Kontakt os</h4>
                        <ul className="space-y-4 text-sm">
                            <li className="marketing-footer-muted flex items-center gap-3">
                                <MapPin size={16} className="shrink-0 text-sky-400" aria-hidden />
                                <span>Køregade 123, København</span>
                            </li>
                            <li className="flex items-center gap-3">
                                <Phone size={16} className="shrink-0 text-sky-400" aria-hidden />
                                <a href={`tel:${tel}`} className="text-slate-200 hover:text-white">
                                    {phoneLabel}
                                </a>
                            </li>
                            <li className="flex items-center gap-3">
                                <Mail size={16} className="shrink-0 text-sky-400" aria-hidden />
                                <a href={`mailto:${email}`} className="text-slate-200 hover:text-white">
                                    {email}
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h4 className="mb-6 text-sm font-semibold uppercase tracking-wide text-white">Brugbare links</h4>
                        <ul className="space-y-3">
                            {usefulLinks.map((item) => (
                                <li key={item.href}>
                                    <a
                                        href={item.href}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="group flex items-start gap-2 text-sm text-slate-300 transition-colors hover:text-sky-400"
                                    >
                                        <ChevronRight
                                            className="mt-0.5 h-4 w-4 shrink-0 text-sky-500/90 transition-colors group-hover:text-sky-400"
                                            aria-hidden
                                        />
                                        <span>{item.label}</span>
                                    </a>
                                </li>
                            ))}
                        </ul>
                    </div>
                    <div>
                        <h4 className="mb-6 text-sm font-semibold uppercase tracking-wide text-white">Juridisk</h4>
                        <ul className="space-y-4 text-sm">
                            <li>
                                <Link href={terms.url()} className="text-slate-300 hover:text-sky-400">
                                    Handelsbetingelser
                                </Link>
                            </li>
                            <li>
                                <Link href={privacy.url()} className="text-slate-300 hover:text-sky-400">
                                    Privatlivspolitik
                                </Link>
                            </li>
                            <li>
                                <Link href={cookies.url()} className="text-slate-300 hover:text-sky-400">
                                    Cookiepolitik
                                </Link>
                            </li>
                        </ul>
                    </div>
                </div>
                <div className="container mx-auto mt-12 px-4 pt-8 text-center text-sm text-slate-500 lg:px-8">
                    &copy; {new Date().getFullYear()} Køreskole Pro. Alle rettigheder forbeholdes.
                </div>
            </footer>
        </div>
    );
}
