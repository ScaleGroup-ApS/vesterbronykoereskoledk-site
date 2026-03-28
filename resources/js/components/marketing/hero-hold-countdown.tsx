import { format } from 'date-fns';
import { da } from 'date-fns/locale';
import { useEffect, useMemo, useState } from 'react';

type Props = {
    targetIso: string | null;
    /** Vises under nedtællingen når sat i Kurser (manuel pladsangivelse). */
    spotsRemaining?: number | null;
};

function getDaysRemainingMs(targetMs: number, nowMs: number): number {
    return Math.max(0, Math.floor((targetMs - nowMs) / (1000 * 60 * 60 * 24)));
}

function formatHoldStartDa(iso: string): string {
    const d = new Date(iso);
    if (!Number.isFinite(d.getTime())) {
        return '';
    }

    return format(d, "EEEE d. MMMM yyyy 'kl.' HH:mm", { locale: da });
}

export function HeroHoldCountdown({ targetIso, spotsRemaining = null }: Props) {
    const targetMs = useMemo(() => {
        if (!targetIso) {
            return null;
        }
        const t = new Date(targetIso).getTime();

        return Number.isFinite(t) ? t : null;
    }, [targetIso]);

    const [now, setNow] = useState(() => Date.now());

    useEffect(() => {
        if (targetMs === null) {
            return;
        }
        const id = window.setInterval(() => setNow(Date.now()), 60_000);

        return () => window.clearInterval(id);
    }, [targetMs]);

    if (!targetIso || targetMs === null) {
        return (
            <p className="mt-8 w-full max-w-xl text-center text-sm leading-relaxed text-slate-600 lg:text-left">
                Næste holdstart meldes ud her, når datoen er sat — eller skriv til os, hvis du vil på venteliste.
            </p>
        );
    }

    const totalMs = Math.max(0, targetMs - now);

    if (totalMs <= 0) {
        return (
            <p className="mt-8 w-full max-w-xl text-center text-sm font-medium text-slate-800 lg:text-left">
                Holdstart er i gang — skriv til os, hvis du vil med på næste hold.
            </p>
        );
    }

    const daysLeft = getDaysRemainingMs(targetMs, now);
    const startSummary = formatHoldStartDa(targetIso);
    const isToday = daysLeft === 0;

    return (
        <div
            className="mt-8 w-full max-w-xl rounded-2xl border border-slate-200/90 bg-gradient-to-b from-white to-slate-50/95 p-5 shadow-sm ring-1 ring-slate-900/[0.04] lg:p-6"
            aria-live="polite"
        >
            <div className="border-b border-slate-200/80 pb-4 text-center lg:text-left">
                <p className="text-[0.7rem] font-semibold uppercase tracking-[0.12em] text-slate-500">
                    Næste holdstart
                </p>
                {spotsRemaining != null && spotsRemaining >= 0 ? (
                    <p className="mt-2 text-base font-semibold tracking-tight text-slate-900">
                        {spotsRemaining === 0
                            ? 'Ingen ledige pladser lige nu'
                            : `${spotsRemaining} pladser tilbage`}
                    </p>
                ) : null}
            </div>

            <div className="mt-5 flex flex-col items-center gap-2 text-center lg:items-start lg:text-left">
                <p className="text-xs font-medium uppercase tracking-wide text-slate-500">
                    {isToday ? 'Starter' : 'Om'}
                </p>
                {isToday ? (
                    <p className="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">I dag</p>
                ) : (
                    <>
                        <div className="flex flex-wrap items-baseline justify-center gap-x-2 gap-y-1 lg:justify-start">
                            <span className="text-4xl font-bold tabular-nums tracking-tight text-slate-900 sm:text-5xl">
                                {daysLeft}
                            </span>
                            <span className="text-xl font-semibold text-slate-800 sm:text-2xl">
                                {daysLeft === 1 ? 'dag' : 'dage'}
                            </span>
                        </div>
                        <p className="text-sm text-slate-600">tilbage til holdstart</p>
                    </>
                )}
            </div>

            <div className="mt-6 rounded-xl border border-slate-200/90 bg-white/90 px-4 py-3 text-center shadow-sm lg:text-left">
                <p className="text-[0.65rem] font-semibold uppercase tracking-wide text-slate-500">
                    Dato & tidspunkt
                </p>
                <p className="mt-1.5 text-base font-medium leading-snug text-slate-900 sm:text-lg">
                    {startSummary || '—'}
                </p>
            </div>
        </div>
    );
}
