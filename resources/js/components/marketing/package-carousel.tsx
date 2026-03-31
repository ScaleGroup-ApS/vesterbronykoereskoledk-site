import { Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { ArrowRight, CheckCircle2, ChevronLeft, ChevronRight } from 'lucide-react';
import { useCallback, useEffect, useState } from 'react';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import { show as bookOffer } from '@/routes/enrollment';
import { packages } from '@/routes/marketing';
import { show as packageShow } from '@/routes/marketing/packages';
import type { MarketingOffer } from '@/types/marketing-offer';

type Props = {
    items: MarketingOffer[];
};

function usePackagesPerView(): number {
    const [perView, setPerView] = useState(3);

    useEffect(() => {
        const update = (): void => {
            const w = window.innerWidth;
            if (w >= 1024) {
                setPerView(3);
            } else if (w >= 640) {
                setPerView(2);
            } else {
                setPerView(1);
            }
        };
        update();
        window.addEventListener('resize', update);

        return () => window.removeEventListener('resize', update);
    }, []);

    return perView;
}

function PackageCard({ offer }: { offer: MarketingOffer }) {
    const priceFormatted = Number(offer.price).toLocaleString('da-DK');

    return (
        <div className="flex h-full min-h-[260px] flex-col p-6 md:p-7">
            <div className="mb-3 inline-block w-fit rounded-full bg-mk-accent/10 px-3 py-1 text-xs font-semibold text-mk-accent ring-1 ring-mk-accent/20 md:text-sm">
                Lovpakke
            </div>
            <h3 className="font-heading text-xl font-bold tracking-tight text-mk-text md:text-2xl">{offer.name}</h3>
            <div className="mt-4 flex items-baseline font-heading text-3xl font-extrabold tracking-tight text-mk-accent md:text-4xl">
                {priceFormatted}
                <span className="ml-1.5 text-lg font-medium text-mk-muted md:text-xl">DKK</span>
            </div>
            <ul className="mt-4 flex-1 space-y-2.5 text-sm text-mk-text md:space-y-3 md:text-base">
                {offer.theory_lessons > 0 && (
                    <li className="flex items-center gap-2.5">
                        <CheckCircle2 className="h-4 w-4 shrink-0 text-mk-accent md:h-5 md:w-5" aria-hidden />
                        <span>{offer.theory_lessons} teoritimer</span>
                    </li>
                )}
                {offer.driving_lessons > 0 && (
                    <li className="flex items-center gap-2.5">
                        <CheckCircle2 className="h-4 w-4 shrink-0 text-mk-accent md:h-5 md:w-5" aria-hidden />
                        <span>{offer.driving_lessons} kørelektioner (45 min)</span>
                    </li>
                )}
                {offer.track_required && (
                    <li className="flex items-center gap-2.5">
                        <CheckCircle2 className="h-4 w-4 shrink-0 text-mk-accent md:h-5 md:w-5" aria-hidden />
                        <span>Manøvrebane</span>
                    </li>
                )}
                {offer.slippery_required && (
                    <li className="flex items-center gap-2.5">
                        <CheckCircle2 className="h-4 w-4 shrink-0 text-mk-accent md:h-5 md:w-5" aria-hidden />
                        <span>Køreteknisk anlæg (glatbane)</span>
                    </li>
                )}
            </ul>
            <div className="mt-6 flex flex-col gap-2.5">
                <Link
                    href={bookOffer.url(offer.slug)}
                    className="inline-flex h-10 w-full items-center justify-center rounded-xl bg-mk-accent px-4 text-sm font-semibold text-white shadow-[0_8px_32px_-8px_rgba(232,0,29,0.5)] transition-all duration-200 hover:bg-mk-accent-soft hover:shadow-[0_8px_40px_-8px_rgba(232,0,29,0.65)]"
                >
                    Tilmeld dig nu
                    <ArrowRight className="ml-2 h-4 w-4" aria-hidden />
                </Link>
                <Link
                    href={packageShow.url(offer.slug)}
                    className="inline-flex h-10 w-full items-center justify-center rounded-xl border border-mk-border bg-transparent px-4 text-sm font-medium text-mk-muted transition-colors duration-200 hover:border-mk-accent/40 hover:text-mk-text"
                >
                    Læs mere
                </Link>
            </div>
        </div>
    );
}

export function PackageCarousel({ items }: Props) {
    const perView = usePackagesPerView();
    const maxStart = Math.max(0, items.length - perView);
    const [start, setStart] = useState(0);
    const clampedStart = Math.min(start, maxStart);

    const go = useCallback(
        (dir: number) => {
            if (items.length === 0 || maxStart <= 0) {
                return;
            }
            setStart((s) => {
                const next = s + dir;
                if (next < 0) {
                    return maxStart;
                }
                if (next > maxStart) {
                    return 0;
                }

                return next;
            });
        },
        [items.length, maxStart],
    );

    const goNext = useCallback(() => {
        go(1);
    }, [go]);

    useEffect(() => {
        if (maxStart <= 0) {
            return;
        }
        const id = window.setInterval(() => goNext(), 8000);

        return () => window.clearInterval(id);
    }, [goNext, maxStart]);

    if (items.length === 0) {
        return null;
    }

    const visible = items.slice(clampedStart, clampedStart + perView);
    const showNav = maxStart > 0;
    const dotCount = maxStart + 1;

    return (
        <div className="relative mx-auto max-w-6xl">
            <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                {visible.map((offer) => (
                    <motion.div
                        key={`${clampedStart}-${offer.id}`}
                        initial={{ opacity: 0, y: 8 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.25 }}
                        className="mk-card flex min-h-0 flex-col overflow-hidden"
                    >
                        <PackageCard offer={offer} />
                    </motion.div>
                ))}
            </div>

            {showNav ? (
                <>
                    {/* Desktop: side arrows (only at lg+ where there's guaranteed margin) */}
                    <Button
                        type="button"
                        variant="outline"
                        size="icon"
                        className="absolute left-0 top-[42%] z-10 hidden -translate-x-[115%] -translate-y-1/2 rounded-full border-mk-border bg-mk-surface text-mk-muted shadow-md hover:border-mk-accent/40 hover:text-mk-accent xl:flex"
                        onClick={() => go(-1)}
                        aria-label="Forrige pakker"
                    >
                        <ChevronLeft className="size-5" />
                    </Button>
                    <Button
                        type="button"
                        variant="outline"
                        size="icon"
                        className="absolute right-0 top-[42%] z-10 hidden -translate-y-1/2 translate-x-[115%] rounded-full border-mk-border bg-mk-surface text-mk-muted shadow-md hover:border-mk-accent/40 hover:text-mk-accent xl:flex"
                        onClick={() => go(1)}
                        aria-label="Næste pakker"
                    >
                        <ChevronRight className="size-5" />
                    </Button>

                    {/* Mobile + tablet: controls row below grid */}
                    <div className="mt-8 flex items-center justify-between xl:justify-center xl:gap-4">
                        <div
                            className="flex gap-2"
                            role="tablist"
                            aria-label="Vælg pakkegruppe"
                        >
                            {Array.from({ length: dotCount }, (_, i) => (
                                <button
                                    key={i}
                                    type="button"
                                    role="tab"
                                    aria-selected={i === clampedStart}
                                    className={cn(
                                        'h-2.5 w-2.5 rounded-full transition-colors',
                                        i === clampedStart ? 'bg-mk-accent' : 'bg-mk-muted/30 hover:bg-mk-muted/50',
                                    )}
                                    onClick={() => setStart(i)}
                                />
                            ))}
                        </div>
                        <div className="flex gap-2 xl:hidden">
                            <Button
                                type="button"
                                variant="outline"
                                size="icon"
                                className="rounded-full border-mk-border bg-mk-surface text-mk-muted hover:border-mk-accent/40 hover:text-mk-accent"
                                onClick={() => go(-1)}
                                aria-label="Forrige pakker"
                            >
                                <ChevronLeft className="size-5" />
                            </Button>
                            <Button
                                type="button"
                                variant="outline"
                                size="icon"
                                className="rounded-full border-mk-border bg-mk-surface text-mk-muted hover:border-mk-accent/40 hover:text-mk-accent"
                                onClick={() => go(1)}
                                aria-label="Næste pakker"
                            >
                                <ChevronRight className="size-5" />
                            </Button>
                        </div>
                    </div>
                </>
            ) : null}

            <p className="mt-8 text-center">
                <Link
                    href={packages.url()}
                    className="inline-flex items-center text-sm font-medium text-mk-accent underline-offset-4 hover:underline"
                >
                    Se alle pakker og tilvalg
                    <ArrowRight className="ml-1 h-4 w-4" />
                </Link>
            </p>
        </div>
    );
}
