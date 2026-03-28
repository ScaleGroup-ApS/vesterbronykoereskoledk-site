import { useEffect, useMemo, useState } from 'react';

type Props = {
    targetIso: string | null;
};

type Remaining = {
    totalMs: number;
    days: number;
    hours: number;
    minutes: number;
    seconds: number;
};

function getRemaining(targetMs: number): Remaining {
    const totalMs = Math.max(0, targetMs - Date.now());
    const secondsTotal = Math.floor(totalMs / 1000);
    const days = Math.floor(secondsTotal / 86400);
    const hours = Math.floor((secondsTotal % 86400) / 3600);
    const minutes = Math.floor((secondsTotal % 3600) / 60);
    const seconds = secondsTotal % 60;

    return { totalMs, days, hours, minutes, seconds };
}

export function HeroHoldCountdown({ targetIso }: Props) {
    const targetMs = useMemo(() => {
        if (!targetIso) {
            return null;
        }
        const t = new Date(targetIso).getTime();

        return Number.isFinite(t) ? t : null;
    }, [targetIso]);

    const [, setTick] = useState(0);

    useEffect(() => {
        if (targetMs === null) {
            return;
        }
        const id = window.setInterval(() => setTick((n) => n + 1), 1000);

        return () => window.clearInterval(id);
    }, [targetMs]);

    if (!targetIso || targetMs === null) {
        return (
            <p className="mt-8 max-w-[540px] text-center text-sm leading-relaxed text-slate-600 lg:text-left">
                Næste holdstart meldes ud her, når datoen er sat — eller skriv til os, hvis du vil på venteliste.
            </p>
        );
    }

    const remaining = getRemaining(targetMs);

    if (remaining.totalMs <= 0) {
        return (
            <p className="mt-8 max-w-[540px] text-center text-sm font-medium text-slate-800 lg:text-left">
                Holdstart er i gang — skriv til os, hvis du vil med på næste hold.
            </p>
        );
    }

    const pad = (n: number) => String(n).padStart(2, '0');

    return (
        <div
            className="mt-8 max-w-[540px] rounded-xl border border-slate-200/90 bg-slate-50/90 px-4 py-4 text-center shadow-sm lg:text-left"
            aria-live="polite"
        >
            <p className="text-xs font-semibold uppercase tracking-wide text-slate-500">Næste holdstart om</p>
            <div className="mt-3 flex flex-wrap items-center justify-center gap-3 sm:gap-4 lg:justify-start">
                <div className="min-w-[3.5rem] rounded-lg bg-white px-2 py-2 text-center shadow-sm ring-1 ring-slate-200/80">
                    <div className="text-2xl font-bold tabular-nums text-slate-900">{remaining.days}</div>
                    <div className="text-[0.65rem] font-medium uppercase text-slate-500">dage</div>
                </div>
                <div className="min-w-[3.5rem] rounded-lg bg-white px-2 py-2 text-center shadow-sm ring-1 ring-slate-200/80">
                    <div className="text-2xl font-bold tabular-nums text-slate-900">{pad(remaining.hours)}</div>
                    <div className="text-[0.65rem] font-medium uppercase text-slate-500">timer</div>
                </div>
                <div className="min-w-[3.5rem] rounded-lg bg-white px-2 py-2 text-center shadow-sm ring-1 ring-slate-200/80">
                    <div className="text-2xl font-bold tabular-nums text-slate-900">{pad(remaining.minutes)}</div>
                    <div className="text-[0.65rem] font-medium uppercase text-slate-500">min.</div>
                </div>
                <div className="min-w-[3.5rem] rounded-lg bg-white px-2 py-2 text-center shadow-sm ring-1 ring-slate-200/80">
                    <div className="text-2xl font-bold tabular-nums text-slate-900">{pad(remaining.seconds)}</div>
                    <div className="text-[0.65rem] font-medium uppercase text-slate-500">sek.</div>
                </div>
            </div>
        </div>
    );
}
