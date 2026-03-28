import { useCallback, useEffect, useState } from 'react';
import { AnimatePresence, motion } from 'framer-motion';
import { ChevronLeft, ChevronRight, Quote } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import type { MarketingTestimonialProps } from '@/types/marketing-public';

type Props = {
    items: MarketingTestimonialProps[];
};

export function TestimonialCarousel({ items }: Props) {
    const [index, setIndex] = useState(0);

    const go = useCallback(
        (dir: number) => {
            if (items.length === 0) {
                return;
            }
            setIndex((i) => (i + dir + items.length) % items.length);
        },
        [items.length],
    );

    useEffect(() => {
        if (items.length <= 1) {
            return;
        }
        const id = window.setInterval(() => go(1), 8000);

        return () => window.clearInterval(id);
    }, [go, items.length]);

    if (items.length === 0) {
        return null;
    }

    const current = items[index];

    return (
        <div className="relative mx-auto max-w-3xl">
            <div className="min-h-[240px] overflow-hidden rounded-2xl border border-slate-200/90 bg-white shadow-md md:min-h-[220px]">
                <AnimatePresence mode="wait">
                    <motion.figure
                        key={current.id}
                        initial={{ opacity: 0, y: 10 }}
                        animate={{ opacity: 1, y: 0 }}
                        exit={{ opacity: 0, y: -6 }}
                        transition={{ duration: 0.25 }}
                        className="flex h-full flex-col px-6 py-8 md:px-12 md:py-10"
                    >
                        <Quote className="mb-4 h-8 w-8 shrink-0 text-primary" aria-hidden />
                        <blockquote className="flex-1 text-base leading-relaxed text-slate-800 md:text-lg">
                            {current.quote}
                        </blockquote>
                        <figcaption className="mt-6 border-t border-slate-200 pt-4 text-sm">
                            <span className="font-semibold text-slate-900">{current.author_name}</span>
                            {current.author_detail ? (
                                <span className="mt-0.5 block text-slate-500">{current.author_detail}</span>
                            ) : null}
                        </figcaption>
                    </motion.figure>
                </AnimatePresence>
            </div>

            {items.length > 1 ? (
                <>
                    <Button
                        type="button"
                        variant="outline"
                        size="icon"
                        className="absolute left-0 top-1/2 z-10 -translate-x-1/2 -translate-y-1/2 rounded-full border-slate-200/90 bg-white text-slate-700 shadow-md hover:bg-primary/10 hover:text-primary md:-translate-x-[115%]"
                        onClick={() => go(-1)}
                        aria-label="Forrige udtalelse"
                    >
                        <ChevronLeft className="size-5" />
                    </Button>
                    <Button
                        type="button"
                        variant="outline"
                        size="icon"
                        className="absolute right-0 top-1/2 z-10 -translate-y-1/2 translate-x-1/2 rounded-full border-slate-200/90 bg-white text-slate-700 shadow-md hover:bg-primary/10 hover:text-primary md:translate-x-[115%]"
                        onClick={() => go(1)}
                        aria-label="Næste udtalelse"
                    >
                        <ChevronRight className="size-5" />
                    </Button>
                    <div
                        className="mt-6 flex justify-center gap-2"
                        role="tablist"
                        aria-label="Vælg udtalelse"
                    >
                        {items.map((item, i) => (
                            <button
                                key={item.id}
                                type="button"
                                role="tab"
                                aria-selected={i === index}
                                className={cn(
                                    'h-2.5 w-2.5 rounded-full transition-colors',
                                    i === index ? 'bg-primary' : 'bg-muted-foreground/30 hover:bg-muted-foreground/50',
                                )}
                                onClick={() => setIndex(i)}
                            />
                        ))}
                    </div>
                </>
            ) : null}
        </div>
    );
}
