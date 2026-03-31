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
        return <CircleDot className="size-5 shrink-0 text-primary" aria-hidden />;
    }

    return <Circle className="size-5 shrink-0 text-muted-foreground" aria-hidden />;
}

export function StudentJourneyRoadmap({ steps, upcomingBookings, className }: Props) {
    return (
        <div className={cn('space-y-6', className)}>
            {steps.length > 0 && (
                <div>
                    <p className="mb-3 text-xs font-medium uppercase tracking-wide text-muted-foreground">Dit forløb</p>
                    <ol className="space-y-4">
                        {steps.map((step) => (
                            <li key={step.key} className="flex gap-3">
                                <div className="pt-0.5">
                                    <StepIcon status={step.status} />
                                </div>
                                <div className="min-w-0 flex-1 space-y-0.5">
                                    <p className="font-medium leading-tight">{step.label}</p>
                                    {step.detail ? (
                                        <p className="text-sm text-muted-foreground">{step.detail}</p>
                                    ) : null}
                                    {step.at ? (
                                        <p className="text-xs text-muted-foreground">
                                            {format(parseISO(step.at), "EEEE d. MMMM yyyy 'kl.' HH:mm", { locale: da })}
                                        </p>
                                    ) : null}
                                </div>
                            </li>
                        ))}
                    </ol>
                </div>
            )}

            {upcomingBookings.length > 0 && (
                <div>
                    <p className="mb-3 flex items-center gap-2 text-xs font-medium uppercase tracking-wide text-muted-foreground">
                        <Calendar className="size-3.5" aria-hidden />
                        Kommende aktiviteter
                    </p>
                    <ul className="space-y-2">
                        {upcomingBookings.map((b) => (
                            <li
                                key={b.id}
                                className="rounded-lg border border-border/80 bg-card/50 px-3 py-2.5 text-sm"
                            >
                                <p className="font-medium">{b.type_label}</p>
                                <p className="text-muted-foreground">
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
