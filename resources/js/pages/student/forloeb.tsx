import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, CheckCircle, XCircle } from 'lucide-react';
import Heading from '@/components/heading';
import { StudentJourneyRoadmap } from '@/components/student/student-journey-roadmap';
import type { JourneyStep, UpcomingBookingRow } from '@/components/student/student-journey-roadmap';
import { Badge } from '@/components/ui/badge';
import StudentLayout from '@/layouts/student-layout';
import { dashboard, forloeb } from '@/routes/student';
import type { BreadcrumbItem } from '@/types';

type JourneyPayload = {
    steps: JourneyStep[];
    upcoming_bookings: UpcomingBookingRow[];
};

type Readiness = {
    is_ready: boolean;
    completed: Record<string, number>;
    required: Record<string, number>;
    missing: Record<string, number>;
};

type Balance = {
    total_owed: number;
    total_paid: number;
    outstanding: number;
};

const readinessTypeLabels: Record<string, string> = {
    driving_lesson: 'Køretimer',
    theory_lesson: 'Teorilektioner',
    track_driving: 'Banekørsel',
    slippery_driving: 'Glat bane',
    theory_exam: 'Teoriprøve',
    practical_exam: 'Køreprøve',
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Oversigt', href: dashboard().url },
    { title: 'Mit forløb', href: forloeb().url },
];

export default function StudentForloeb({
    journey,
    readiness,
    balance,
    curriculum_by_lesson,
}: {
    journey: JourneyPayload;
    readiness: Readiness;
    balance: Balance;
    curriculum_by_lesson: Record<number, string>;
}) {
    return (
        <StudentLayout breadcrumbs={breadcrumbs}>
            <Head title="Mit forløb" />
            <div className="flex h-full flex-1 flex-col gap-8 rounded-xl p-4">
                <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div className="space-y-1">
                        <Heading title="Mit forløb" />
                        <p className="text-sm text-muted-foreground">
                            Se din progression, krav til eksamen og læringsplan.
                        </p>
                    </div>
                    <Link
                        href={dashboard().url}
                        className="inline-flex items-center gap-2 text-sm font-medium text-primary hover:underline"
                    >
                        <ArrowLeft className="size-4" />
                        Tilbage til oversigt
                    </Link>
                </div>

                <section className="space-y-3">
                    <Heading variant="small" title="Kørekortsforløb" />
                    <StudentJourneyRoadmap steps={journey.steps} upcomingBookings={journey.upcoming_bookings} />
                </section>

                <section className="space-y-3">
                    <div className="flex items-center justify-between gap-2">
                        <Heading variant="small" title="Fremgang mod eksamen" />
                        <Badge variant={readiness.is_ready ? 'default' : 'secondary'}>
                            {readiness.is_ready ? 'Klar til eksamen' : 'Ikke eksamensklar endnu'}
                        </Badge>
                    </div>

                    <div className="divide-y rounded-xl border">
                        {Object.entries(readiness.required)
                            .filter(([, needed]) => needed > 0)
                            .map(([type, needed]) => {
                                const done = readiness.completed[type] ?? 0;
                                const met = done >= needed;
                                return (
                                    <div key={type} className="flex items-center justify-between px-4 py-3">
                                        <div className="flex items-center gap-2">
                                            {met ? (
                                                <CheckCircle className="size-4 text-green-600" />
                                            ) : (
                                                <XCircle className="size-4 text-muted-foreground" />
                                            )}
                                            <span className="text-sm">{readinessTypeLabels[type] ?? type}</span>
                                        </div>
                                        <span className="text-sm text-muted-foreground">
                                            {done} / {needed}
                                        </span>
                                    </div>
                                );
                            })}
                    </div>

                    {balance.outstanding > 0 && (
                        <div className="flex items-center justify-between rounded-xl border bg-muted/30 px-4 py-3">
                            <span className="text-sm text-muted-foreground">Udestående saldo</span>
                            <span className="text-sm font-semibold tabular-nums">
                                {Number(balance.outstanding).toLocaleString('da-DK')} kr.
                            </span>
                        </div>
                    )}
                </section>

                {Object.keys(curriculum_by_lesson).length > 0 && (
                    <section className="space-y-3">
                        <Heading variant="small" title="Læringsplan" />
                        <div className="divide-y rounded-xl border">
                            {Object.entries(curriculum_by_lesson)
                                .sort(([a], [b]) => Number(a) - Number(b))
                                .map(([lessonNumber, title]) => (
                                    <div key={lessonNumber} className="flex items-center gap-3 px-4 py-2.5 text-sm">
                                        <span className="w-20 shrink-0 text-xs font-medium text-muted-foreground">
                                            Lektion {lessonNumber}
                                        </span>
                                        <span>{title}</span>
                                    </div>
                                ))}
                        </div>
                    </section>
                )}
            </div>
        </StudentLayout>
    );
}
