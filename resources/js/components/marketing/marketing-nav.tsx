import { Link, usePage } from '@inertiajs/react';
import {
    Bike,
    BookOpen,
    Car,
    ChevronDown,
    ChevronRight,
    Monitor,
    Package,
    Receipt,
    Users,
} from 'lucide-react';
import { AnimatePresence, motion } from 'framer-motion';
import { useEffect, useRef, useState } from 'react';
import { cn } from '@/lib/utils';
import type { MarketingOffer } from '@/types/marketing-offer';
import {
    about,
    contact,
    faq,
    instructors,
    packages,
} from '@/routes/marketing';
import { show as marketingPackageShow } from '@/routes/marketing/packages';
import { show as tilEleverShow } from '@/routes/marketing/til-elever';

const tilEleverLinks: { slug: string; label: string; description: string; icon: React.ElementType }[] = [
    { slug: 'elektronisk-lektionsplan', label: 'Elektronisk lektionsplan', description: 'Følg din fremgang løbende', icon: BookOpen },
    { slug: 'online-teori', label: 'Online teoriundervisning', description: 'Lær teorien i dit eget tempo', icon: Monitor },
    { slug: 'korekort-bil', label: 'Kørekort til bil', description: 'Alt du skal vide om kategori B', icon: Car },
    { slug: 'korekort-motorcykel', label: 'Kørekort til motorcykel', description: 'Kategori A og A2 forklaret', icon: Bike },
    { slug: 'boedetakster', label: 'Bødetakster', description: 'Gældende satser og regler', icon: Receipt },
    { slug: 'elevportal', label: 'Elevportal', description: 'Følg dit forløb online', icon: Users },
];

const panelVariants = {
    hidden: { opacity: 0, y: -6, scale: 0.99 },
    visible: { opacity: 1, y: 0, scale: 1, transition: { duration: 0.18, ease: [0.16, 1, 0.3, 1] } },
    exit: { opacity: 0, y: -6, scale: 0.99, transition: { duration: 0.13 } },
};

type OpenMenu = 'pakker' | 'til-elever' | null;

function useHoverMenu() {
    const [open, setOpen] = useState<OpenMenu>(null);
    const timer = useRef<ReturnType<typeof setTimeout> | null>(null);

    function enter(menu: OpenMenu) {
        if (timer.current) clearTimeout(timer.current);
        setOpen(menu);
    }

    function leave() {
        timer.current = setTimeout(() => setOpen(null), 100);
    }

    function cancelLeave() {
        if (timer.current) clearTimeout(timer.current);
    }

    useEffect(() => () => { if (timer.current) clearTimeout(timer.current); }, []);

    return { open, enter, leave, cancelLeave, close: () => setOpen(null) };
}

export function MarketingNavDesktop() {
    const { url } = usePage();
    const { marketingOffers } = usePage().props as { marketingOffers?: MarketingOffer[] };
    const offers = marketingOffers ?? [];
    const { open, enter, leave, cancelLeave, close } = useHoverMenu();

    const packagesActive = url === packages.url() || url.startsWith('/pakker/');
    const tilEleverActive = url.startsWith('/til-elever/');

    const triggerClass = (active: boolean, menuKey: OpenMenu) =>
        cn(
            'inline-flex h-8 items-center gap-1.5 rounded-md px-3 text-sm font-medium outline-none transition-colors duration-150',
            active || open === menuKey ? 'text-mk-accent' : 'text-white/65 hover:text-white',
        );

    const linkClass = (active: boolean) =>
        cn(
            'text-sm font-medium transition-colors duration-150',
            active ? 'text-mk-accent' : 'text-white/65 hover:text-white',
        );

    return (
        <nav
            className="relative hidden flex-1 items-center justify-center gap-x-3 text-sm font-medium lg:flex lg:gap-x-4 xl:gap-x-5"
            aria-label="Primær navigation"
        >
            {/* ── Pakker ── */}
            <div
                onMouseEnter={() => enter('pakker')}
                onMouseLeave={leave}
                className="relative"
            >
                <button
                    type="button"
                    className={triggerClass(packagesActive, 'pakker')}
                    aria-expanded={open === 'pakker'}
                    aria-haspopup="true"
                >
                    Pakker
                    <ChevronDown
                        className={cn('size-3.5 opacity-60 transition-transform duration-200', open === 'pakker' && 'rotate-180')}
                    />
                </button>

                <AnimatePresence>
                    {open === 'pakker' && (
                        <motion.div
                            variants={panelVariants}
                            initial="hidden"
                            animate="visible"
                            exit="exit"
                            onMouseEnter={cancelLeave}
                            onMouseLeave={leave}
                            className="absolute left-1/2 top-full z-50 mt-2 w-[min(90vw,30rem)] -translate-x-1/2 overflow-hidden rounded-2xl border border-mk-border bg-mk-surface shadow-[0_20px_60px_rgba(0,0,0,0.5)]"
                        >
                            {/* Header */}
                            <div className="border-b border-mk-border/60 px-5 py-3">
                                <p className="text-[0.65rem] font-semibold uppercase tracking-[0.12em] text-mk-muted">Vores pakker</p>
                            </div>

                            {/* Package list */}
                            <div className="px-2 py-2">
                                {offers.length > 0 ? (
                                    offers.map((offer) => (
                                        <Link
                                            key={offer.id}
                                            href={marketingPackageShow.url(offer.slug)}
                                            onClick={close}
                                            className="group flex items-center gap-3 rounded-xl px-3 py-2.5 transition-colors hover:bg-white/[0.05]"
                                        >
                                            <div className="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-mk-accent/10 text-mk-accent transition-colors group-hover:bg-mk-accent/15">
                                                <Package className="size-4" aria-hidden />
                                            </div>
                                            <span className="text-sm font-medium text-mk-text group-hover:text-white">{offer.name}</span>
                                        </Link>
                                    ))
                                ) : (
                                    <p className="px-3 py-2 text-sm text-mk-muted">Ingen pakker endnu</p>
                                )}
                            </div>

                            {/* Footer CTA */}
                            <div className="border-t border-mk-border/60 px-4 py-3">
                                <Link
                                    href={packages.url()}
                                    onClick={close}
                                    className="group inline-flex items-center gap-1.5 text-sm font-semibold text-mk-accent"
                                >
                                    Se alle pakker og tilvalg
                                    <ChevronRight className="size-3.5 transition-transform duration-150 group-hover:translate-x-0.5" />
                                </Link>
                            </div>
                        </motion.div>
                    )}
                </AnimatePresence>
            </div>

            {/* ── Til elever ── */}
            <div
                onMouseEnter={() => enter('til-elever')}
                onMouseLeave={leave}
                className="relative"
            >
                <button
                    type="button"
                    className={triggerClass(tilEleverActive, 'til-elever')}
                    aria-expanded={open === 'til-elever'}
                    aria-haspopup="true"
                >
                    Til elever
                    <ChevronDown
                        className={cn('size-3.5 opacity-60 transition-transform duration-200', open === 'til-elever' && 'rotate-180')}
                    />
                </button>

                <AnimatePresence>
                    {open === 'til-elever' && (
                        <motion.div
                            variants={panelVariants}
                            initial="hidden"
                            animate="visible"
                            exit="exit"
                            onMouseEnter={cancelLeave}
                            onMouseLeave={leave}
                            className="absolute left-1/2 top-full z-50 mt-2 w-[min(90vw,36rem)] -translate-x-1/2 overflow-hidden rounded-2xl border border-mk-border bg-mk-surface shadow-[0_20px_60px_rgba(0,0,0,0.5)]"
                        >
                            {/* Header */}
                            <div className="border-b border-mk-border/60 px-5 py-3">
                                <p className="text-[0.65rem] font-semibold uppercase tracking-[0.12em] text-mk-muted">Ressourcer til dig som elev</p>
                            </div>

                            {/* 2-col grid */}
                            <div className="grid grid-cols-2 gap-1 px-2 py-2">
                                {tilEleverLinks.map((item) => {
                                    const Icon = item.icon;
                                    const isActive = url === tilEleverShow.url(item.slug);
                                    return (
                                        <Link
                                            key={item.slug}
                                            href={tilEleverShow.url(item.slug)}
                                            onClick={close}
                                            className={cn(
                                                'group flex items-start gap-3 rounded-xl px-3 py-2.5 transition-colors hover:bg-white/[0.05]',
                                                isActive && 'bg-mk-accent/[0.07]',
                                            )}
                                        >
                                            <div className={cn(
                                                'mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-lg transition-colors',
                                                isActive
                                                    ? 'bg-mk-accent/20 text-mk-accent'
                                                    : 'bg-mk-accent/10 text-mk-accent group-hover:bg-mk-accent/15',
                                            )}>
                                                <Icon className="size-3.5" aria-hidden />
                                            </div>
                                            <div className="min-w-0">
                                                <p className={cn(
                                                    'text-sm font-medium leading-snug',
                                                    isActive ? 'text-mk-accent' : 'text-mk-text group-hover:text-white',
                                                )}>
                                                    {item.label}
                                                </p>
                                                <p className="mt-0.5 text-xs leading-snug text-mk-muted">{item.description}</p>
                                            </div>
                                        </Link>
                                    );
                                })}
                            </div>
                        </motion.div>
                    )}
                </AnimatePresence>
            </div>

            <Link href={faq.url()} className={linkClass(url === faq.url())}>FAQ</Link>
            <Link href={instructors.url()} className={linkClass(url === instructors.url())}>Vores kørelærere</Link>
            <Link href={about.url()} className={linkClass(url === about.url())}>Om os</Link>
            <Link href={contact.url()} className={linkClass(url === contact.url())}>Kontakt</Link>
        </nav>
    );
}

export function MarketingNavMobile({ onNavigate }: { onNavigate?: () => void }) {
    const { url } = usePage();
    const { marketingOffers } = usePage().props as { marketingOffers?: MarketingOffer[] };
    const offers = marketingOffers ?? [];

    const isPackagesRoute = url === packages.url() || url.startsWith('/pakker/');
    const isTilEleverRoute = url.startsWith('/til-elever/');

    const [pakkerOpen, setPakkerOpen] = useState(() => isPackagesRoute);
    const [tilEleverOpen, setTilEleverOpen] = useState(() => isTilEleverRoute);

    useEffect(() => { setPakkerOpen(isPackagesRoute); }, [isPackagesRoute]);
    useEffect(() => { setTilEleverOpen(isTilEleverRoute); }, [isTilEleverRoute]);

    const linkClass = (active: boolean) =>
        cn(
            'flex items-center gap-2 rounded-lg px-3 py-2.5 text-sm transition-colors',
            active
                ? 'border-l-2 border-mk-accent pl-[10px] font-semibold text-mk-accent'
                : 'text-mk-text/80 hover:bg-white/[0.04] hover:text-mk-text',
        );

    return (
        <nav className="flex flex-col gap-1.5 text-sm font-medium" aria-label="Mobil navigation">
            {/* Pakker */}
            <div className="rounded-xl border border-mk-border bg-mk-surface/40">
                <button
                    type="button"
                    className="flex w-full items-center justify-between gap-2 rounded-xl border border-mk-border bg-mk-surface/60 px-3 py-2.5 text-left text-sm font-semibold text-mk-text transition-colors hover:bg-mk-surface"
                    aria-expanded={pakkerOpen}
                    onClick={() => setPakkerOpen((o) => !o)}
                >
                    <span>Pakker</span>
                    <ChevronDown className={cn('size-4 shrink-0 text-mk-muted transition-transform duration-200', pakkerOpen && 'rotate-180')} aria-hidden />
                </button>
                {pakkerOpen && (
                    <div className="space-y-0.5 border-t border-mk-border/60 px-2 pb-2 pt-1.5">
                        {offers.length > 0 ? (
                            offers.map((offer) => (
                                <Link
                                    key={offer.id}
                                    href={marketingPackageShow.url(offer.slug)}
                                    className={linkClass(url === marketingPackageShow.url(offer.slug))}
                                    onClick={onNavigate}
                                >
                                    {offer.name}
                                </Link>
                            ))
                        ) : (
                            <p className="px-3 py-2 text-sm text-mk-muted">Ingen pakker endnu</p>
                        )}
                        <Link href={packages.url()} className={linkClass(url === packages.url())} onClick={onNavigate}>
                            Se alle pakker
                        </Link>
                    </div>
                )}
            </div>

            {/* Til elever */}
            <div className="rounded-xl border border-mk-border bg-mk-surface/40">
                <button
                    type="button"
                    className="flex w-full items-center justify-between gap-2 rounded-xl border border-mk-border bg-mk-surface/60 px-3 py-2.5 text-left text-sm font-semibold text-mk-text transition-colors hover:bg-mk-surface"
                    aria-expanded={tilEleverOpen}
                    onClick={() => setTilEleverOpen((o) => !o)}
                >
                    <span>Til elever</span>
                    <ChevronDown className={cn('size-4 shrink-0 text-mk-muted transition-transform duration-200', tilEleverOpen && 'rotate-180')} aria-hidden />
                </button>
                {tilEleverOpen && (
                    <div className="space-y-0.5 border-t border-mk-border/60 px-2 pb-2 pt-1.5">
                        {tilEleverLinks.map((item) => (
                            <Link
                                key={item.slug}
                                href={tilEleverShow.url(item.slug)}
                                className={linkClass(url === tilEleverShow.url(item.slug))}
                                onClick={onNavigate}
                            >
                                {item.label}
                            </Link>
                        ))}
                    </div>
                )}
            </div>

            <Link href={faq.url()} className={linkClass(url === faq.url())} onClick={onNavigate}>FAQ</Link>
            <Link href={instructors.url()} className={linkClass(url === instructors.url())} onClick={onNavigate}>Vores kørelærere</Link>
            <Link href={about.url()} className={linkClass(url === about.url())} onClick={onNavigate}>Om os</Link>
            <Link href={contact.url()} className={linkClass(url === contact.url())} onClick={onNavigate}>Kontakt</Link>
        </nav>
    );
}
