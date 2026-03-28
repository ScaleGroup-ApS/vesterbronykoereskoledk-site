import { Link, usePage } from '@inertiajs/react';
import { ChevronDown } from 'lucide-react';
import { useEffect, useState } from 'react';
import { cn } from '@/lib/utils';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
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

const tilEleverLinks: { slug: string; label: string }[] = [
    { slug: 'elektronisk-lektionsplan', label: 'Den elektroniske lektionsplan' },
    { slug: 'online-teori', label: 'Online teoriundervisning' },
    { slug: 'korekort-bil', label: 'Viden om kørekort til bil' },
    { slug: 'korekort-motorcykel', label: 'Viden om kørekort til motorcykel' },
    { slug: 'boedetakster', label: 'Bødetakster' },
    { slug: 'elevportal', label: 'Køreklar sammen – elevportal' },
];

export function MarketingNavDesktop() {
    const { url } = usePage();
    const { marketingOffers } = usePage().props as {
        marketingOffers?: MarketingOffer[];
    };
    const offers = marketingOffers ?? [];

    const linkClass = (active: boolean) =>
        cn(
            'transition-colors',
            active ? 'font-semibold text-sky-400' : 'text-white/80 hover:text-white',
        );

    const packagesOpen = url === packages.url() || url.startsWith('/pakker/');
    const tilEleverOpen = url.startsWith('/til-elever/');

    const dropdownTriggerClass = (open: boolean) =>
        cn(
            'inline-flex h-8 items-center gap-1 rounded-md px-3 text-sm font-medium outline-none transition-colors',
            'focus-visible:ring-2 focus-visible:ring-sky-400/60 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950',
            open
                ? 'text-sky-400'
                : 'text-white/80 hover:bg-white/10 hover:text-white data-[state=open]:text-sky-400',
        );

    return (
        <nav className="hidden flex-1 flex-wrap items-center justify-center gap-x-4 gap-y-2 text-sm font-medium md:flex lg:gap-x-5 xl:gap-x-6">
            <DropdownMenu>
                <DropdownMenuTrigger asChild>
                    <button type="button" className={dropdownTriggerClass(packagesOpen)}>
                        Pakker
                        <ChevronDown className="size-3.5 opacity-70" />
                    </button>
                </DropdownMenuTrigger>
                <DropdownMenuContent
                    align="center"
                    className="w-[min(90vw,28rem)] rounded-xl border border-border bg-popover p-0 shadow-lg"
                >
                    <DropdownMenuLabel className="px-3 py-2 text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                        Vores pakker
                    </DropdownMenuLabel>
                    <DropdownMenuGroup>
                        {offers.length > 0 ? (
                            offers.map((offer) => (
                                <DropdownMenuItem key={offer.id} asChild>
                                    <Link
                                        href={marketingPackageShow.url(offer.slug)}
                                        className="cursor-pointer"
                                    >
                                        {offer.name}
                                    </Link>
                                </DropdownMenuItem>
                            ))
                        ) : (
                            <div className="px-3 py-2 text-sm text-muted-foreground">Ingen pakker endnu</div>
                        )}
                    </DropdownMenuGroup>
                    <DropdownMenuSeparator />
                    <DropdownMenuItem asChild>
                        <Link href={packages.url()} className="cursor-pointer font-medium">
                            Se alle pakker
                        </Link>
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>

            <DropdownMenu>
                <DropdownMenuTrigger asChild>
                    <button type="button" className={dropdownTriggerClass(tilEleverOpen)}>
                        Til elever
                        <ChevronDown className="size-3.5 opacity-70" />
                    </button>
                </DropdownMenuTrigger>
                <DropdownMenuContent
                    align="center"
                    className="w-[min(90vw,28rem)] rounded-xl border border-border bg-popover p-0 shadow-lg"
                >
                    <DropdownMenuLabel className="px-3 py-2 text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                        Ressourcer
                    </DropdownMenuLabel>
                    <DropdownMenuGroup>
                        {tilEleverLinks.map((item) => (
                            <DropdownMenuItem key={item.slug} asChild>
                                <Link
                                    href={tilEleverShow.url(item.slug)}
                                    className="cursor-pointer"
                                >
                                    {item.label}
                                </Link>
                            </DropdownMenuItem>
                        ))}
                    </DropdownMenuGroup>
                </DropdownMenuContent>
            </DropdownMenu>

            <Link href={faq.url()} className={linkClass(url === faq.url())}>
                FAQ
            </Link>
            <Link href={instructors.url()} className={linkClass(url === instructors.url())}>
                Vores kørelærere
            </Link>
            <Link href={about.url()} className={linkClass(url === about.url())}>
                Om os
            </Link>
            <Link href={contact.url()} className={linkClass(url === contact.url())}>
                Kontakt
            </Link>
        </nav>
    );
}

export function MarketingNavMobile({
    onNavigate,
}: {
    onNavigate?: () => void;
}) {
    const { url } = usePage();
    const { marketingOffers } = usePage().props as {
        marketingOffers?: MarketingOffer[];
    };
    const offers = marketingOffers ?? [];

    const isPackagesRoute = url === packages.url() || url.startsWith('/pakker/');
    const isTilEleverRoute = url.startsWith('/til-elever/');

    const [pakkerOpen, setPakkerOpen] = useState(() => isPackagesRoute);
    const [tilEleverOpen, setTilEleverOpen] = useState(() => isTilEleverRoute);

    useEffect(() => {
        setPakkerOpen(isPackagesRoute);
    }, [isPackagesRoute]);

    useEffect(() => {
        setTilEleverOpen(isTilEleverRoute);
    }, [isTilEleverRoute]);

    const linkClass = (active: boolean) =>
        cn(
            'block rounded-md px-2 py-2 transition-colors',
            active ? 'bg-primary/10 font-medium text-primary' : 'text-foreground hover:bg-muted/70',
        );

    const submenuButtonClass =
        'flex w-full items-center justify-between gap-2 rounded-md border border-border bg-muted/30 px-3 py-2.5 text-left text-sm font-semibold text-foreground transition-colors hover:bg-muted/50';

    return (
        <nav className="flex flex-col gap-1 text-sm font-medium" aria-label="Mobil navigation">
            <div className="rounded-lg border border-border bg-muted/20">
                <button
                    type="button"
                    className={submenuButtonClass}
                    aria-expanded={pakkerOpen}
                    aria-controls="mobile-nav-pakker"
                    id="mobile-nav-pakker-trigger"
                    onClick={() => setPakkerOpen((o) => !o)}
                >
                    <span>Pakker</span>
                    <ChevronDown
                        className={cn('size-4 shrink-0 text-muted-foreground transition-transform duration-200', pakkerOpen && 'rotate-180')}
                        aria-hidden
                    />
                </button>
                <div
                    id="mobile-nav-pakker"
                    role="region"
                    aria-labelledby="mobile-nav-pakker-trigger"
                    hidden={!pakkerOpen}
                >
                    <div className="space-y-0 border-t border-border/80 px-2 pb-2 pt-1">
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
                            <p className="px-2 py-2 text-sm text-muted-foreground">Ingen pakker endnu</p>
                        )}
                        <Link
                            href={packages.url()}
                            className={linkClass(url === packages.url())}
                            onClick={onNavigate}
                        >
                            Se alle pakker
                        </Link>
                    </div>
                </div>
            </div>

            <div className="rounded-lg border border-border bg-muted/20">
                <button
                    type="button"
                    className={submenuButtonClass}
                    aria-expanded={tilEleverOpen}
                    aria-controls="mobile-nav-til-elever"
                    id="mobile-nav-til-elever-trigger"
                    onClick={() => setTilEleverOpen((o) => !o)}
                >
                    <span>Til elever</span>
                    <ChevronDown
                        className={cn(
                            'size-4 shrink-0 text-muted-foreground transition-transform duration-200',
                            tilEleverOpen && 'rotate-180',
                        )}
                        aria-hidden
                    />
                </button>
                <div
                    id="mobile-nav-til-elever"
                    role="region"
                    aria-labelledby="mobile-nav-til-elever-trigger"
                    hidden={!tilEleverOpen}
                >
                    <div className="space-y-0 border-t border-border/80 px-2 pb-2 pt-1">
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
                </div>
            </div>

            <Link href={faq.url()} className={linkClass(url === faq.url())} onClick={onNavigate}>
                FAQ
            </Link>
            <Link href={instructors.url()} className={linkClass(url === instructors.url())} onClick={onNavigate}>
                Vores kørelærere
            </Link>
            <Link href={about.url()} className={linkClass(url === about.url())} onClick={onNavigate}>
                Om os
            </Link>
            <Link href={contact.url()} className={linkClass(url === contact.url())} onClick={onNavigate}>
                Kontakt
            </Link>
        </nav>
    );
}
