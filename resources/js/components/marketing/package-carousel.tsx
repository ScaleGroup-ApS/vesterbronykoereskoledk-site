import { Link } from '@inertiajs/react';
import { useCallback, useEffect, useState } from 'react';
import { motion } from 'framer-motion';
import { ArrowRight, CheckCircle2, ChevronLeft, ChevronRight } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import type { MarketingOffer } from '@/types/marketing-offer';
import { show as bookOffer } from '@/routes/enrollment';
import { show as packageShow } from '@/routes/marketing/packages';
import { packages } from '@/routes/marketing';

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
            <div className="mb-3 inline-block w-fit rounded-full bg-primary px-3 py-1 text-xs font-semibold text-primary-foreground md:text-sm">
                Lovpakke
            </div>
            <h3 className="text-xl font-bold tracking-tight text-slate-900 md:text-2xl">{offer.name}</h3>
            {offer.description ? (
                <p className="mt-2 line-clamp-3 text-sm leading-relaxed text-slate-600 md:text-base">{offer.description}</p>
            ) : null}
            <div className="mt-4 flex items-baseline text-3xl font-extrabold tracking-tight text-slate-900 md:text-4xl">
                {priceFormatted}
                <span className="ml-1.5 text-lg font-medium text-slate-500 md:text-xl">DKK</span>
            </div>
            <ul className="mt-4 flex-1 space-y-2.5 text-sm text-slate-800 md:space-y-3 md:text-base">
                {offer.theory_lessons > 0 && (
                    <li className="flex items-center gap-2.5">
                        <CheckCircle2 className="h-4 w-4 shrink-0 text-primary md:h-5 md:w-5" aria-hidden />
                        <span>{offer.theory_lessons} teoritimer</span>
                    </li>
                )}
                {offer.driving_lessons > 0 && (
                    <li className="flex items-center gap-2.5">
                        <CheckCircle2 className="h-4 w-4 shrink-0 text-primary md:h-5 md:w-5" aria-hidden />
                        <span>{offer.driving_lessons} kørelektioner (45 min)</span>
                    </li>
                )}
                {offer.track_required && (
                    <li className="flex items-center gap-2.5">
                        <CheckCircle2 className="h-4 w-4 shrink-0 text-primary md:h-5 md:w-5" aria-hidden />
                        <span>Manøvrebane</span>
                    </li>
                )}
                {offer.slippery_required && (
                    <li className="flex items-center gap-2.5">
                        <CheckCircle2 className="h-4 w-4 shrink-0 text-primary md:h-5 md:w-5" aria-hidden />
                        <span>Køreteknisk anlæg (glatbane)</span>
                    </li>
                )}
            </ul>
            <div className="mt-6 flex flex-col gap-2.5">
                <Link
                    href={bookOffer.url(offer.slug)}
                    className="inline-flex h-10 w-full items-center justify-center rounded-xl bg-primary px-4 text-sm font-medium text-primary-foreground shadow-[0_8px_32px_-8px_rgba(37,99,235,0.35)] transition-colors hover:bg-primary/90"
                >
                    Tilmeld dig nu
                    <ArrowRight className="ml-2 h-4 w-4" aria-hidden />
                </Link>
                <Link
                    href={packageShow.url(offer.slug)}
                    className="inline-flex h-10 w-full items-center justify-center rounded-xl border-2 border-slate-200 bg-white px-4 text-sm font-medium text-slate-900 transition-colors hover:border-primary/35 hover:bg-slate-50"
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

    useEffect(() => {
        setStart((s) => Math.min(s, maxStart));
    }, [maxStart]);

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

    const visible = items.slice(start, start + perView);
    const showNav = maxStart > 0;
    const dotCount = maxStart + 1;

    return (
        <div className="relative mx-auto max-w-6xl">
            <div className="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                {visible.map((offer) => (
                    <motion.div
                        key={`${start}-${offer.id}`}
                        initial={{ opacity: 0, y: 8 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.25 }}
                        className="marketing-glass-panel flex min-h-0 flex-col overflow-hidden shadow-md"
                    >
                        <PackageCard offer={offer} />
                    </motion.div>
                ))}
            </div>

            {showNav ? (
                <>
                    <Button
                        type="button"
                        variant="outline"
                        size="icon"
                        className="absolute left-0 top-[42%] z-10 -translate-x-1/2 -translate-y-1/2 rounded-full border-slate-200/90 bg-white text-slate-700 shadow-md hover:bg-primary/10 hover:text-primary sm:-translate-x-[115%]"
                        onClick={() => go(-1)}
                        aria-label="Forrige pakker"
                    >
                        <ChevronLeft className="size-5" />
                    </Button>
                    <Button
                        type="button"
                        variant="outline"
                        size="icon"
                        className="absolute right-0 top-[42%] z-10 -translate-y-1/2 translate-x-1/2 rounded-full border-slate-200/90 bg-white text-slate-700 shadow-md hover:bg-primary/10 hover:text-primary sm:translate-x-[115%]"
                        onClick={() => go(1)}
                        aria-label="Næste pakker"
                    >
                        <ChevronRight className="size-5" />
                    </Button>
                    <div
                        className="mt-8 flex justify-center gap-2"
                        role="tablist"
                        aria-label="Vælg pakkegruppe"
                    >
                        {Array.from({ length: dotCount }, (_, i) => (
                            <button
                                key={i}
                                type="button"
                                role="tab"
                                aria-selected={i === start}
                                className={cn(
                                    'h-2.5 w-2.5 rounded-full transition-colors',
                                    i === start ? 'bg-primary' : 'bg-muted-foreground/30 hover:bg-muted-foreground/50',
                                )}
                                onClick={() => setStart(i)}
                            />
                        ))}
                    </div>
                </>
            ) : null}

            <p className="mt-8 text-center">
                <Link
                    href={packages.url()}
                    className="inline-flex items-center text-sm font-medium text-primary hover:underline"
                >
                    Se alle pakker og tilvalg
                    <ArrowRight className="ml-1 h-4 w-4" />
                </Link>
            </p>
        </div>
    );
}
