import { Link, usePage } from '@inertiajs/react';
import { ChevronDown } from 'lucide-react';
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

    const linkClass = (active: boolean) =>
        cn(
            'block rounded-md px-2 py-2 transition-colors',
            active ? 'bg-primary/10 font-medium text-primary' : 'text-foreground hover:bg-muted/70',
        );

    return (
        <div className="flex flex-col gap-2 text-sm font-medium">
            <div className="rounded-md border border-border bg-muted/30 px-2 py-2">
                <p className="mb-2 px-2 text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                    Pakker
                </p>
                {offers.map((offer) => (
                    <Link
                        key={offer.id}
                        href={marketingPackageShow.url(offer.slug)}
                        className={linkClass(url === marketingPackageShow.url(offer.slug))}
                        onClick={onNavigate}
                    >
                        {offer.name}
                    </Link>
                ))}
                <Link
                    href={packages.url()}
                    className={linkClass(url === packages.url())}
                    onClick={onNavigate}
                >
                    Se alle pakker
                </Link>
            </div>

            <div className="rounded-md border border-border bg-muted/30 px-2 py-2">
                <p className="mb-2 px-2 text-xs font-semibold uppercase tracking-wide text-muted-foreground">
                    Til elever
                </p>
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
        </div>
    );
}
