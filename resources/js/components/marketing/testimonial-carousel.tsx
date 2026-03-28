import { useCallback, useEffect, useRef, useState } from 'react';
import { AnimatePresence, motion } from 'framer-motion';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import { cn } from '@/lib/utils';
import type { MarketingTestimonialProps } from '@/types/marketing-public';

type Props = {
    items: MarketingTestimonialProps[];
};

const INTERVAL_MS = 8000;

function AuthorAvatar({ name }: { name: string }) {
    const initials = name
        .split(' ')
        .slice(0, 2)
        .map((w) => w[0]?.toUpperCase() ?? '')
        .join('');

    return (
        <div className="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-mk-accent/10 ring-1 ring-mk-accent/25 text-sm font-bold text-mk-accent">
            {initials}
        </div>
    );
}

export function TestimonialCarousel({ items }: Props) {
    const [index, setIndex] = useState(0);
    const [progress, setProgress] = useState(0);
    const startRef = useRef<number>(Date.now());
    const rafRef = useRef<number>(0);

    const go = useCallback(
        (dir: number) => {
            if (items.length === 0) return;
            setIndex((i) => (i + dir + items.length) % items.length);
        },
        [items.length],
    );

    /* Progress bar + auto-advance */
    useEffect(() => {
        if (items.length <= 1) return;
        startRef.current = Date.now();

        const tick = () => {
            const elapsed = Date.now() - startRef.current;
            const pct = Math.min(elapsed / INTERVAL_MS, 1);
            setProgress(pct);
            if (pct < 1) {
                rafRef.current = requestAnimationFrame(tick);
            } else {
                go(1);
            }
        };

        rafRef.current = requestAnimationFrame(tick);
        return () => cancelAnimationFrame(rafRef.current);
    }, [index, go, items.length]);

    if (items.length === 0) return null;

    const current = items[index];

    return (
        <div className="mx-auto max-w-4xl">
            {/* Card */}
            <div className="relative overflow-hidden rounded-2xl border border-mk-border bg-mk-surface">
                {/* Decorative large quote mark */}
                <div
                    className="pointer-events-none absolute -top-4 left-5 select-none font-heading text-[10rem] font-black leading-none text-mk-accent/[0.06]"
                    aria-hidden
                >
                    &ldquo;
                </div>

                <AnimatePresence mode="wait">
                    <motion.figure
                        key={current.id}
                        initial={{ opacity: 0, x: 24 }}
                        animate={{ opacity: 1, x: 0 }}
                        exit={{ opacity: 0, x: -24 }}
                        transition={{ duration: 0.3, ease: [0.16, 1, 0.3, 1] }}
                        className="relative flex flex-col gap-6 px-8 pb-8 pt-10 md:px-12 md:pb-10 md:pt-12"
                    >
                        {/* Quote */}
                        <blockquote className="text-lg font-medium leading-relaxed text-mk-text md:text-xl lg:text-2xl lg:leading-relaxed">
                            &ldquo;{current.quote}&rdquo;
                        </blockquote>

                        {/* Author row */}
                        <figcaption className="flex items-center gap-4">
                            <AuthorAvatar name={current.author_name} />
                            <div>
                                <p className="font-semibold text-mk-text">{current.author_name}</p>
                                {current.author_detail ? (
                                    <p className="text-sm text-mk-muted">{current.author_detail}</p>
                                ) : null}
                            </div>
                        </figcaption>
                    </motion.figure>
                </AnimatePresence>

                {/* Progress bar */}
                {items.length > 1 ? (
                    <div className="h-[2px] w-full bg-mk-border">
                        <motion.div
                            className="h-full bg-mk-accent"
                            style={{ width: `${progress * 100}%` }}
                            transition={{ duration: 0 }}
                        />
                    </div>
                ) : null}
            </div>

            {/* Controls */}
            {items.length > 1 ? (
                <div className="mt-6 flex items-center justify-between">
                    {/* Dots */}
                    <div className="flex gap-2" role="tablist" aria-label="Vælg udtalelse">
                        {items.map((item, i) => (
                            <button
                                key={item.id}
                                type="button"
                                role="tab"
                                aria-selected={i === index}
                                onClick={() => setIndex(i)}
                                className={cn(
                                    'h-2 rounded-full transition-all duration-300',
                                    i === index ? 'w-6 bg-mk-accent' : 'w-2 bg-mk-muted/30 hover:bg-mk-muted/50',
                                )}
                            />
                        ))}
                    </div>

                    {/* Prev / Next */}
                    <div className="flex gap-2">
                        <button
                            type="button"
                            onClick={() => go(-1)}
                            aria-label="Forrige udtalelse"
                            className="flex h-9 w-9 items-center justify-center rounded-full border border-mk-border text-mk-muted transition-colors hover:border-mk-accent/40 hover:text-mk-accent"
                        >
                            <ChevronLeft className="size-4" />
                        </button>
                        <button
                            type="button"
                            onClick={() => go(1)}
                            aria-label="Næste udtalelse"
                            className="flex h-9 w-9 items-center justify-center rounded-full border border-mk-border text-mk-muted transition-colors hover:border-mk-accent/40 hover:text-mk-accent"
                        >
                            <ChevronRight className="size-4" />
                        </button>
                    </div>
                </div>
            ) : null}
        </div>
    );
}
