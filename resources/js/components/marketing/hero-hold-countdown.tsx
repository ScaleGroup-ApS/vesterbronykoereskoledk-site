import { format } from 'date-fns';
import { da } from 'date-fns/locale';
import { CalendarDays, MapPin } from 'lucide-react';
import { useEffect, useMemo, useState } from 'react';
import { cn } from '@/lib/utils';

type Props = {
    targetIso: string | null;
    spotsRemaining?: number | null;
};

function getDaysRemaining(targetMs: number, nowMs: number): number {
    return Math.max(0, Math.floor((targetMs - nowMs) / (1000 * 60 * 60 * 24)));
}

function formatHoldStartDa(iso: string): string {
    const d = new Date(iso);
    if (!Number.isFinite(d.getTime())) return '';
    return format(d, "EEEE d. MMMM yyyy 'kl.' HH:mm", { locale: da });
}

export function HeroHoldCountdown({ targetIso, spotsRemaining = null }: Props) {
    const targetMs = useMemo(() => {
        if (!targetIso) return null;
        const t = new Date(targetIso).getTime();
        return Number.isFinite(t) ? t : null;
    }, [targetIso]);

    const [now, setNow] = useState(() => Date.now());

    useEffect(() => {
        if (targetMs === null) return;
        const id = window.setInterval(() => setNow(Date.now()), 60_000);
        return () => window.clearInterval(id);
    }, [targetMs]);

    if (!targetIso || targetMs === null) {
        return (
            <p className="mt-8 max-w-xl text-center text-sm leading-relaxed text-mk-muted lg:text-left">
                Næste holdstart meldes ud her, når datoen er sat — eller skriv til os, hvis du vil på venteliste.
            </p>
        );
    }

    const totalMs = Math.max(0, targetMs - now);

    if (totalMs <= 0) {
        return (
            <p className="mt-8 max-w-xl text-center text-sm font-medium text-mk-muted lg:text-left">
                Holdstart er i gang — skriv til os, hvis du vil med på næste hold.
            </p>
        );
    }

    const daysLeft = getDaysRemaining(targetMs, now);
    const startSummary = formatHoldStartDa(targetIso);
    const isToday = daysLeft === 0;

    const isAlmostFull = spotsRemaining != null && spotsRemaining > 0 && spotsRemaining <= 3;

    return (
        <div
            className="mt-8 w-full max-w-xl overflow-hidden rounded-2xl border border-mk-border bg-mk-surface/80 backdrop-blur-sm"
            aria-live="polite"
        >
            {/* Header strip */}
            <div className="flex items-center justify-between border-b border-mk-border px-5 py-3">
                <div className="flex items-center gap-2">
                    <span className="h-2 w-2 animate-pulse rounded-full bg-mk-accent" aria-hidden />
                    <span className="text-[0.65rem] font-semibold uppercase tracking-[0.12em] text-mk-muted">
                        Næste holdstart
                    </span>
                </div>

                {spotsRemaining != null && spotsRemaining >= 0 ? (
                    <span
                        className={cn(
                            'rounded-full border px-2.5 py-1 text-xs font-semibold',
                            spotsRemaining === 0
                                ? 'border-mk-accent/30 bg-mk-accent/10 text-mk-accent'
                                : isAlmostFull
                                  ? 'border-amber-500/30 bg-amber-500/10 text-amber-400'
                                  : 'border-emerald-500/30 bg-emerald-500/10 text-emerald-400',
                        )}
                    >
                        {spotsRemaining === 0
                            ? 'Udsolgt'
                            : spotsRemaining === 1
                              ? '1 plads tilbage'
                              : `${spotsRemaining} pladser tilbage`}
                    </span>
                ) : null}
            </div>

            {/* Body: day counter + date */}
            <div className="flex items-center gap-5 px-5 py-5">
                {/* Day count */}
                <div className="flex min-w-[72px] flex-col items-center text-center">
                    {isToday ? (
                        <span
                            className="font-heading text-3xl font-extrabold text-mk-accent"
                            style={{ letterSpacing: '-0.03em' }}
                        >
                            I dag
                        </span>
                    ) : (
                        <>
                            <span
                                className="font-heading text-5xl font-extrabold tabular-nums text-mk-accent"
                                style={{ letterSpacing: '-0.03em' }}
                            >
                                {daysLeft}
                            </span>
                            <span className="mt-0.5 text-[0.65rem] font-medium uppercase tracking-wide text-mk-muted">
                                {daysLeft === 1 ? 'dag' : 'dage'}
                            </span>
                        </>
                    )}
                </div>

                {/* Divider */}
                <div className="h-14 w-px shrink-0 bg-mk-border" aria-hidden />

                {/* Date */}
                <div className="min-w-0 flex-1">
                    <div className="mb-1 flex items-center gap-1.5">
                        <CalendarDays className="h-3.5 w-3.5 shrink-0 text-mk-accent" aria-hidden />
                        <span className="text-[0.65rem] font-semibold uppercase tracking-[0.12em] text-mk-muted">
                            Startdato
                        </span>
                    </div>
                    <p className="font-heading text-base font-semibold leading-snug text-mk-text">
                        {startSummary || '—'}
                    </p>
                    {!isToday && (
                        <p className="mt-1 text-xs text-mk-muted">
                            {daysLeft === 1 ? 'Starter i morgen' : `Starter om ${daysLeft} dage`}
                        </p>
                    )}
                </div>
            </div>
        </div>
    );
}
