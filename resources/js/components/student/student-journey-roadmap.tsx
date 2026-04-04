import { format, parseISO } from 'date-fns';
import { da } from 'date-fns/locale';
import { Calendar, CheckCircle2, Circle, CircleDot } from 'lucide-react';
import { cn } from '@/lib/utils';

export type JourneyStep = {
    key: string;
    label: string;
    status: 'done' | 'in_progress' | 'upcoming';
    detail: string | null;
    at: string | null;
};

export type UpcomingBookingRow = {
    id: number;
    type: string;
    type_label: string;
    starts_at_local: string;
    ends_at_local: string;
};

type Props = {
    steps: JourneyStep[];
    upcomingBookings: UpcomingBookingRow[];
    className?: string;
};

function StepIcon({ status }: { status: JourneyStep['status'] }) {
    if (status === 'done') {
        return <CheckCircle2 className="size-5 shrink-0 text-green-600 dark:text-green-500" aria-hidden />;
    }
    if (status === 'in_progress') {
        return (
            <span className="relative flex size-5 items-center justify-center">
                <span className="absolute inline-flex size-full animate-ping rounded-full bg-primary/30" />
                <CircleDot className="relative size-5 shrink-0 text-primary" aria-hidden />
            </span>
        );
    }

    return <Circle className="size-5 shrink-0 text-muted-foreground/40" aria-hidden />;
}

function lineColor(status: JourneyStep['status']): string {
    if (status === 'done') return 'bg-green-500';
    if (status === 'in_progress') return 'bg-primary/50';
    return 'bg-border';
}

export function StudentJourneyRoadmap({ steps, upcomingBookings, className }: Props) {
    return (
        <div className={cn('space-y-6', className)}>
            {steps.length > 0 && (
                <div>
                    <p className="mb-4 text-xs font-medium uppercase tracking-wide text-muted-foreground">Dit forløb</p>
                    <ol className="relative space-y-0">
                        {steps.map((step, i) => {
                            const isLast = i === steps.length - 1;
                            return (
                                <li key={step.key} className="relative flex gap-4 pb-6 last:pb-0">
                                    {/* Vertical connecting line */}
                                    {!isLast && (
                                        <div
                                            className={cn(
                                                'absolute left-[9px] top-6 w-0.5 rounded-full',
                                                lineColor(step.status),
                                            )}
                                            style={{ height: 'calc(100% - 0.75rem)' }}
                                            aria-hidden
                                        />
                                    )}

                                    {/* Step icon */}
                                    <div className="relative z-10 flex-shrink-0 pt-0.5">
                                        <StepIcon status={step.status} />
                                    </div>

                                    {/* Step content */}
                                    <div
                                        className={cn(
                                            'min-w-0 flex-1 rounded-xl border px-4 py-3 transition',
                                            step.status === 'in_progress' && 'border-primary/30 bg-primary/[0.03] shadow-sm',
                                            step.status === 'done' && 'border-green-500/20 bg-green-500/[0.02]',
                                            step.status === 'upcoming' && 'border-border/60 opacity-60',
                                        )}
                                    >
                                        <p className={cn(
                                            'font-medium leading-tight',
                                            step.status === 'upcoming' && 'text-muted-foreground',
                                        )}>
                                            {step.label}
                                        </p>
                                        {step.detail && (
                                            <p className="mt-0.5 text-sm text-muted-foreground">{step.detail}</p>
                                        )}
                                        {step.at && (
                                            <p className="mt-1 text-xs text-muted-foreground">
                                                {format(parseISO(step.at), "EEEE d. MMMM yyyy 'kl.' HH:mm", { locale: da })}
                                            </p>
                                        )}
                                    </div>
                                </li>
                            );
                        })}
                    </ol>
                </div>
            )}

            {upcomingBookings.length > 0 && (
                <div>
                    <p className="mb-3 flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-muted-foreground">
                        <Calendar className="size-3.5" aria-hidden />
                        Kommende aktiviteter
                    </p>
                    <ul className="grid gap-2 sm:grid-cols-2">
                        {upcomingBookings.map((b) => (
                            <li
                                key={b.id}
                                className="rounded-xl border bg-card/50 px-4 py-3 text-sm shadow-sm"
                            >
                                <p className="font-medium">{b.type_label}</p>
                                <p className="mt-0.5 text-muted-foreground">
                                    {b.starts_at_local}
                                    {' · '}
                                    {b.ends_at_local}
                                </p>
                            </li>
                        ))}
                    </ul>
                </div>
            )}
        </div>
    );
}
