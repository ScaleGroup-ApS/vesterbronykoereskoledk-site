import { Head, Link, router, usePage, usePoll } from '@inertiajs/react';
import { formatDistanceToNow, parseISO } from 'date-fns';
import { da } from 'date-fns/locale';
import {
    ArrowRight,
    BookOpen,
    CalendarDays,
    Clock,
    CreditCard,
    Loader2,
    MapPin,
    Sparkles,
    Trophy,
    User as UserIcon,
} from 'lucide-react';
import { useEffect, useMemo, useRef } from 'react';
import { StudentLessonProgress } from '@/components/student/student-lesson-progress';
import type { LessonProgressRow } from '@/components/student/student-lesson-progress';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import StudentLayout from '@/layouts/student-layout';
import { calendar, dashboard, history, materials, progress, skills } from '@/routes/student';
import type { BreadcrumbItem, User } from '@/types';

type NextHighlight = {
    type: string;
    title: string;
    starts_at: string;
    ends_at: string;
    range_label: string;
    instructor_name?: string;
} | null;

type Balance = {
    total_owed: number;
    total_paid: number;
    outstanding: number;
};

type Readiness = {
    is_ready: boolean;
    completed: Record<string, number>;
    required: Record<string, number>;
    missing: Record<string, number>;
};

type PendingEnrollment = {
    status: 'pending_payment' | 'pending_approval';
    payment_method: 'stripe' | 'cash';
    offer_price: number;
} | null;

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Oversigt', href: dashboard().url }];

function getGreeting(): string {
    const hour = new Date().getHours();
    if (hour < 5) return 'God nat';
    if (hour < 12) return 'God morgen';
    if (hour < 18) return 'God eftermiddag';
    return 'God aften';
}

function OverallProgressRing({ percentage }: { percentage: number }) {
    const radius = 54;
    const circumference = 2 * Math.PI * radius;
    const offset = circumference - (percentage / 100) * circumference;

    const color = percentage >= 100
        ? 'text-green-500'
        : percentage >= 60
            ? 'text-primary'
            : 'text-amber-500';

    return (
        <div className="relative flex items-center justify-center">
            <svg viewBox="0 0 120 120" className="size-32 -rotate-90 sm:size-36">
                <circle
                    cx="60" cy="60" r={radius}
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="8"
                    className="text-muted/40"
                />
                <circle
                    cx="60" cy="60" r={radius}
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="8"
                    strokeLinecap="round"
                    strokeDasharray={circumference}
                    strokeDashoffset={offset}
                    className={`${color} transition-all duration-1000 ease-out`}
                />
            </svg>
            <div className="absolute flex flex-col items-center">
                <span className="text-2xl font-bold tabular-nums sm:text-3xl">{percentage}%</span>
                <span className="text-[10px] uppercase tracking-widest text-muted-foreground">fuldført</span>
            </div>
        </div>
    );
}

function CountdownCard({ booking }: { booking: NonNullable<NextHighlight> }) {
    const distance = formatDistanceToNow(parseISO(booking.starts_at), { locale: da, addSuffix: true });

    return (
        <div className="group relative overflow-hidden rounded-2xl border bg-gradient-to-br from-primary/5 via-card to-card p-5 shadow-sm transition hover:border-primary/30">
            <div className="absolute -right-6 -top-6 size-24 rounded-full bg-primary/5 blur-2xl transition group-hover:bg-primary/10" />
            <div className="relative space-y-3">
                <div className="flex items-center justify-between gap-2">
                    <Badge variant="outline" className="gap-1 font-normal">
                        <CalendarDays className="size-3" />
                        Næste aktivitet
                    </Badge>
                    <span className="text-xs font-medium text-primary">{distance}</span>
                </div>
                <div>
                    <p className="text-lg font-semibold">{booking.title}</p>
                    <p className="mt-0.5 text-sm text-muted-foreground">{booking.range_label}</p>
                </div>
                {booking.instructor_name && (
                    <div className="flex items-center gap-1.5 text-sm text-muted-foreground">
                        <UserIcon className="size-3.5" />
                        {booking.instructor_name}
                    </div>
                )}
            </div>
        </div>
    );
}

function QuickNavGrid({ hasLearnUrl }: { hasLearnUrl: boolean }) {
    const items = [
        { title: 'Kalender', description: 'Se kommende timer', href: calendar().url, icon: CalendarDays, color: 'text-blue-500' },
        { title: 'Mit forløb', description: 'Fuld progression', href: progress().url, icon: MapPin, color: 'text-emerald-500' },
        { title: 'Færdigheder', description: 'Øvede manøvrer', href: skills().url, icon: Sparkles, color: 'text-amber-500' },
        { title: 'Historik', description: 'Tidligere lektioner', href: history().url, icon: Clock, color: 'text-purple-500' },
        { title: 'Materiale', description: 'Kursusfiler', href: materials().url, icon: BookOpen, color: 'text-rose-500' },
    ];

    return (
        <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
            {items.map((item) => (
                <Link
                    key={item.title}
                    href={item.href}
                    className="group flex flex-col gap-2 rounded-xl border bg-card p-4 transition hover:border-primary/20 hover:shadow-sm"
                >
                    <item.icon className={`size-5 ${item.color}`} />
                    <div>
                        <p className="text-sm font-medium group-hover:text-primary">{item.title}</p>
                        <p className="text-xs text-muted-foreground">{item.description}</p>
                    </div>
                </Link>
            ))}
        </div>
    );
}

export default function StudentDashboard({
    pendingEnrollment,
    booking,
    balance,
    readiness,
    lesson_progress,
}: {
    pendingEnrollment: PendingEnrollment;
    booking: NextHighlight;
    balance: Balance;
    readiness: Readiness;
    lesson_progress: LessonProgressRow[];
}) {
    const { auth, studentLearnUrl } = usePage<{ auth: { user: User }; studentLearnUrl: string | null }>().props;
    const wasPending = useRef(!!pendingEnrollment);
    const { stop } = usePoll(5000, { only: ['pendingEnrollment'] }, { autoStart: !!pendingEnrollment });

    useEffect(() => {
        if (wasPending.current && !pendingEnrollment) {
            stop();
            router.reload();
        }
        wasPending.current = !!pendingEnrollment;
    }, [pendingEnrollment, stop]);

    const overallPercentage = useMemo(() => {
        const totalRequired = lesson_progress.reduce((sum, r) => sum + r.required, 0);
        const totalCompleted = lesson_progress.reduce((sum, r) => sum + r.completed, 0);
        if (totalRequired === 0) return 0;
        return Math.min(100, Math.round((totalCompleted / totalRequired) * 100));
    }, [lesson_progress]);

    if (pendingEnrollment) {
        const isStripe = pendingEnrollment.payment_method === 'stripe';

        return (
            <div className="flex min-h-screen flex-col items-center justify-center bg-background p-6 text-center">
                <Head title={isStripe ? 'Afventer betaling' : 'Afventer godkendelse'} />
                <div className="max-w-sm space-y-6">
                    <div className="flex justify-center">
                        <div className="flex size-20 items-center justify-center rounded-full bg-muted">
                            {isStripe ? (
                                <CreditCard className="size-9 text-muted-foreground" />
                            ) : (
                                <Clock className="size-9 text-muted-foreground" />
                            )}
                        </div>
                    </div>

                    <div className="space-y-2">
                        <h1 className="text-2xl font-semibold">
                            {isStripe ? 'Afventer betaling' : 'Afventer godkendelse'}
                        </h1>
                        <p className="text-sm text-muted-foreground">
                            {isStripe
                                ? 'Din tilmelding afventer betalingsbekræftelse fra Stripe. Kontakt os, hvis du har problemer.'
                                : 'Din tilmelding afventer godkendelse fra en instruktør. Du vil modtage besked, når den er behandlet.'}
                        </p>
                        <p className="text-lg font-medium">
                            {Number(pendingEnrollment.offer_price).toLocaleString('da-DK')} kr.
                        </p>
                    </div>

                    <div className="flex items-center justify-center gap-2 text-xs text-muted-foreground">
                        <Loader2 className="size-3 animate-spin" />
                        Checker for opdateringer…
                    </div>
                </div>
            </div>
        );
    }

    const firstName = auth.user.name.split(' ')[0];

    return (
        <StudentLayout breadcrumbs={breadcrumbs}>
            <Head title="Oversigt" />
            <div className="flex h-full flex-1 flex-col gap-8 p-4 sm:p-6">
                {/* Hero section: greeting + progress ring */}
                <div className="flex flex-col items-center gap-6 rounded-2xl border bg-gradient-to-br from-primary/[0.03] via-card to-card p-6 shadow-sm sm:flex-row sm:items-center sm:gap-8 sm:p-8">
                    <OverallProgressRing percentage={overallPercentage} />
                    <div className="flex-1 text-center sm:text-left">
                        <p className="text-sm font-medium text-muted-foreground">{getGreeting()}</p>
                        <h1 className="mt-1 text-2xl font-bold tracking-tight sm:text-3xl">{firstName}</h1>
                        <p className="mt-2 text-sm text-muted-foreground">
                            {readiness.is_ready ? (
                                <span className="inline-flex items-center gap-1.5 font-medium text-green-600">
                                    <Trophy className="size-4" />
                                    Du er klar til eksamen!
                                </span>
                            ) : overallPercentage === 0 ? (
                                'Dit kørekortforløb starter snart — velkommen!'
                            ) : (
                                `Du er ${overallPercentage}% igennem dit kørekortforløb.`
                            )}
                        </p>
                        {studentLearnUrl && (
                            <Button asChild size="sm" className="mt-4">
                                <Link href={studentLearnUrl}>
                                    <BookOpen className="size-4" />
                                    Fortsæt kursus
                                </Link>
                            </Button>
                        )}
                    </div>
                </div>

                {/* Next activity countdown */}
                {booking ? (
                    <CountdownCard booking={booking} />
                ) : (
                    <div className="flex flex-col items-center gap-3 rounded-2xl border border-dashed py-8 text-center">
                        <CalendarDays className="size-8 text-muted-foreground/50" />
                        <div>
                            <p className="font-medium text-muted-foreground">Ingen planlagte aktiviteter</p>
                            <p className="mt-1 text-sm text-muted-foreground/70">
                                Kontakt din instruktør for at booke din næste time.
                            </p>
                        </div>
                    </div>
                )}

                {/* Quick navigation grid */}
                <section className="space-y-4">
                    <h2 className="text-base font-medium">Genveje</h2>
                    <QuickNavGrid hasLearnUrl={!!studentLearnUrl} />
                </section>

                {/* Lesson progress */}
                <section className="space-y-4">
                    <div className="flex items-center justify-between">
                        <h2 className="text-base font-medium">Dit pakkeforløb</h2>
                        <Link href={progress().url} className="text-sm font-medium text-primary hover:underline">
                            Se detaljer
                        </Link>
                    </div>
                    <StudentLessonProgress rows={lesson_progress} variant="compact" />
                </section>

                {/* Outstanding balance */}
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

                {/* Full progression CTA */}
                <Link
                    href={progress().url}
                    className="group flex items-center justify-between gap-4 rounded-2xl border border-primary/20 bg-gradient-to-br from-primary/10 via-transparent to-transparent p-6 shadow-sm transition hover:border-primary/40 hover:shadow-md"
                >
                    <div className="space-y-1">
                        <p className="text-lg font-semibold">Mit forløb</p>
                        <p className="text-sm text-muted-foreground">
                            Se kørekortsforløb, krav til eksamen, materiale og bookinghistorik.
                        </p>
                    </div>
                    <ArrowRight className="size-5 shrink-0 text-primary transition group-hover:translate-x-1" />
                </Link>
            </div>
        </StudentLayout>
    );
}
