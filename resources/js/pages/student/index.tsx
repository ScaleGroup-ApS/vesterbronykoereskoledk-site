import { Head, Link, router, usePage, usePoll } from '@inertiajs/react';
import { ArrowRight, BookOpen, Clock, CreditCard, Loader2 } from 'lucide-react';
import { useEffect, useRef } from 'react';
import Heading from '@/components/heading';
import { StudentLessonProgress } from '@/components/student/student-lesson-progress';
import type { LessonProgressRow } from '@/components/student/student-lesson-progress';
import { Button } from '@/components/ui/button';
import StudentLayout from '@/layouts/student-layout';
import { dashboard, forloeb, kalender } from '@/routes/student';
import type { BreadcrumbItem } from '@/types';

type NextHighlight = {
    type: string;
    title: string;
    starts_at: string;
    ends_at: string;
    range_label: string;
} | null;

type Balance = {
    total_owed: number;
    total_paid: number;
    outstanding: number;
};

type PendingEnrollment = {
    status: 'pending_payment' | 'pending_approval';
    payment_method: 'stripe' | 'cash';
    offer_price: number;
} | null;

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Oversigt', href: dashboard().url }];

export default function StudentDashboard({
    pendingEnrollment,
    booking,
    balance,
    lesson_progress,
}: {
    pendingEnrollment: PendingEnrollment;
    booking: NextHighlight;
    balance: Balance;
    lesson_progress: LessonProgressRow[];
}) {
    const { studentLearnUrl } = usePage<{ studentLearnUrl: string | null }>().props;
    const wasPending = useRef(!!pendingEnrollment);
    const { stop } = usePoll(5000, { only: ['pendingEnrollment'] }, { autoStart: !!pendingEnrollment });

    useEffect(() => {
        if (wasPending.current && !pendingEnrollment) {
            stop();
            router.reload();
        }
        wasPending.current = !!pendingEnrollment;
    }, [pendingEnrollment, stop]);

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

    return (
        <StudentLayout breadcrumbs={breadcrumbs}>
            <Head title="Oversigt" />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <div className="space-y-1">
                    <Heading title="Oversigt" />
                    <p className="text-sm text-muted-foreground">
                        Din hurtige status — progression, materiale og historik finder du under Mit forløb.{' '}
                        <Link href={kalender().url} className="font-medium text-primary hover:underline">
                            Åbn kalender
                        </Link>
                    </p>
                </div>

                {studentLearnUrl && (
                    <div className="flex items-center justify-between rounded-xl border p-5">
                        <div className="flex items-center gap-3">
                            <BookOpen className="size-5 text-muted-foreground" />
                            <div>
                                <p className="font-medium">Kursusmateriale</p>
                                <p className="text-sm text-muted-foreground">Gennemgå moduler, videoer og quizzer</p>
                            </div>
                        </div>
                        <Button asChild>
                            <Link href={studentLearnUrl}>Gå til kursus</Link>
                        </Button>
                    </div>
                )}


                <div className="space-y-3">
                    <Heading variant="small" title="Næste aktivitet" />
                    {booking ? (
                        <div className="rounded-xl border bg-card p-5 shadow-sm">
                            <p className="font-medium">{booking.title}</p>
                            <p className="mt-1 text-sm text-muted-foreground">{booking.range_label}</p>
                        </div>
                    ) : (
                        <p className="text-sm text-muted-foreground">Ingen kommende aktivitet registreret endnu.</p>
                    )}
                </div>

                <div className="space-y-3">
                    <Heading variant="small" title="Dit pakkeforløb" />
                    <StudentLessonProgress rows={lesson_progress} variant="compact" />
                </div>

                {balance.outstanding > 0 && (
                    <div className="flex items-center justify-between rounded-xl border border-amber-500/30 bg-amber-500/5 px-4 py-3">
                        <span className="text-sm text-muted-foreground">Udestående saldo</span>
                        <span className="text-sm font-semibold tabular-nums">
                            {Number(balance.outstanding).toLocaleString('da-DK')} kr.
                        </span>
                    </div>
                )}

                <Link
                    href={forloeb().url}
                    className="group flex items-center justify-between gap-4 rounded-xl border border-primary/20 bg-gradient-to-br from-primary/10 via-transparent to-transparent p-5 shadow-sm transition hover:border-primary/40"
                >
                    <div className="space-y-1">
                        <p className="font-medium">Mit forløb</p>
                        <p className="text-sm text-muted-foreground">
                            Se kørekortsforløb, krav til eksamen, materiale og bookinghistorik.
                        </p>
                    </div>
                    <ArrowRight className="size-5 shrink-0 text-primary transition group-hover:translate-x-0.5" />
                </Link>
            </div>
        </StudentLayout>
    );
}
