import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, CheckCircle, Trophy, XCircle } from 'lucide-react';
import Heading from '@/components/heading';
import { StudentJourneyRoadmap } from '@/components/student/student-journey-roadmap';
import type { JourneyStep, UpcomingBookingRow } from '@/components/student/student-journey-roadmap';
import { StudentLessonProgress } from '@/components/student/student-lesson-progress';
import type { LessonProgressRow } from '@/components/student/student-lesson-progress';
import { Badge } from '@/components/ui/badge';
import StudentLayout from '@/layouts/student-layout';
import { dashboard, progress } from '@/routes/student';
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
    { title: 'Mit forløb', href: progress().url },
];

function ReadinessSection({ readiness }: { readiness: Readiness }) {
    const items = Object.entries(readiness.required).filter(([, needed]) => needed > 0);
    const metCount = items.filter(([type, needed]) => (readiness.completed[type] ?? 0) >= needed).length;
    const totalCount = items.length;

    return (
        <section className="space-y-4">
            <div className="flex items-center justify-between gap-2">
                <Heading variant="small" title="Fremgang mod eksamen" />
                {readiness.is_ready ? (
                    <Badge className="gap-1 bg-green-500/10 text-green-600 hover:bg-green-500/10">
                        <Trophy className="size-3" />
                        Klar til eksamen
                    </Badge>
                ) : (
                    <Badge variant="secondary" className="gap-1">
                        {metCount}/{totalCount} opfyldt
                    </Badge>
                )}
            </div>

            <div className="divide-y rounded-xl border shadow-sm">
                {items.map(([type, needed]) => {
                    const done = readiness.completed[type] ?? 0;
                    const met = done >= needed;
                    const pct = Math.min(100, Math.round((done / needed) * 100));
                    return (
                        <div key={type} className="flex items-center gap-4 px-5 py-3.5">
                            {met ? (
                                <CheckCircle className="size-4 shrink-0 text-green-500" />
                            ) : (
                                <XCircle className="size-4 shrink-0 text-muted-foreground/40" />
                            )}
                            <div className="flex-1 space-y-1.5">
                                <div className="flex items-center justify-between text-sm">
                                    <span className={met ? 'font-medium' : ''}>{readinessTypeLabels[type] ?? type}</span>
                                    <span className={`tabular-nums ${met ? 'font-medium text-green-600' : 'text-muted-foreground'}`}>
                                        {done} / {needed}
                                    </span>
                                </div>
                                <div className="h-1.5 overflow-hidden rounded-full bg-muted">
                                    <div
                                        className={`h-full rounded-full transition-all duration-500 ${met ? 'bg-green-500' : 'bg-primary'}`}
                                        style={{ width: `${pct}%` }}
                                    />
                                </div>
                            </div>
                        </div>
                    );
                })}
            </div>
        </section>
    );
}

export default function StudentForloeb({
    journey,
    readiness,
    balance,
    curriculum_by_lesson,
    lesson_progress,
}: {
    journey: JourneyPayload;
    readiness: Readiness;
    balance: Balance;
    curriculum_by_lesson: Record<number, string>;
    lesson_progress: LessonProgressRow[];
}) {
    return (
        <StudentLayout breadcrumbs={breadcrumbs}>
            <Head title="Mit forløb" />
            <div className="flex h-full flex-1 flex-col gap-8 rounded-xl p-4 sm:p-6">
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

                <section className="space-y-4">
                    <Heading variant="small" title="Kørekortsforløb" />
                    <StudentJourneyRoadmap steps={journey.steps} upcomingBookings={journey.upcoming_bookings} />
                </section>

                <section className="space-y-4">
                    <Heading variant="small" title="Dit pakkeforløb" />
                    <p className="text-sm text-muted-foreground">
                        Krav fra dit tilbud sammenholdt med, hvad der er gennemført, booket frem i tiden, og hvad der
                        mangler.
                    </p>
                    <StudentLessonProgress rows={lesson_progress} variant="full" />
                </section>

                <ReadinessSection readiness={readiness} />

                {balance.outstanding > 0 && (
                    <div className="flex items-center justify-between rounded-xl border border-amber-500/30 bg-amber-500/5 px-5 py-4">
                        <div>
                            <p className="text-sm font-medium">Udestående saldo</p>
                            <p className="text-xs text-muted-foreground">Kontakt køreskolen for betalingsmuligheder</p>
                        </div>
                        <span className="text-lg font-semibold tabular-nums">
                            {Number(balance.outstanding).toLocaleString('da-DK')} kr.
                        </span>
                    </div>
                )}

                {Object.keys(curriculum_by_lesson).length > 0 && (
                    <section className="space-y-4">
                        <Heading variant="small" title="Læringsplan" />
                        <div className="divide-y rounded-xl border shadow-sm">
                            {Object.entries(curriculum_by_lesson)
                                .sort(([a], [b]) => Number(a) - Number(b))
                                .map(([lessonNumber, title]) => (
                                    <div key={lessonNumber} className="flex items-center gap-3 px-5 py-3 text-sm">
                                        <span className="flex size-7 shrink-0 items-center justify-center rounded-full bg-muted text-xs font-medium tabular-nums">
                                            {lessonNumber}
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
