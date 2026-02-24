import { Head } from '@inertiajs/react';
import { format } from 'date-fns';
import { da } from 'date-fns/locale';
import { CheckCircle, XCircle } from 'lucide-react';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes/student';

type Booking = {
    type: string;
    starts_at: string;
    ends_at: string;
} | null;

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

const bookingTypeLabels: Record<string, string> = {
    driving_lesson: 'Køretest',
    theory_lesson: 'Teoritime',
    track_driving: 'Banekørsel',
    slippery_driving: 'Glat bane',
    exam: 'Eksamen',
};

const readinessTypeLabels: Record<string, string> = {
    driving_lesson: 'Køretimer',
    theory_lesson: 'Teorilektioner',
    track_driving: 'Banekørsel',
    slippery_driving: 'Glat bane',
};

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: dashboard().url }];

export default function StudentDashboard({
    booking,
    readiness,
    balance,
}: {
    booking: Booking;
    readiness: Readiness;
    balance: Balance;
}) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">

                {/* Upcoming booking */}
                <div className="space-y-3">
                    <Heading variant="small" title="Næste lektion" />
                    {booking ? (
                        <div className="rounded-xl border p-5">
                            <p className="font-medium">
                                {bookingTypeLabels[booking.type] ?? booking.type}
                            </p>
                            <p className="mt-1 text-sm text-muted-foreground">
                                {format(new Date(booking.starts_at), 'PPPp', { locale: da })}
                                {' – '}
                                {format(new Date(booking.ends_at), 'p', { locale: da })}
                            </p>
                        </div>
                    ) : (
                        <p className="text-sm text-muted-foreground">Ingen kommende lektioner.</p>
                    )}
                </div>

                {/* Progression */}
                <div className="space-y-3">
                    <div className="flex items-center justify-between">
                        <Heading variant="small" title="Fremgang" />
                        <Badge variant={readiness.is_ready ? 'default' : 'secondary'}>
                            {readiness.is_ready ? '✓ Klar til eksamen' : 'Ikke klar endnu'}
                        </Badge>
                    </div>

                    <div className="rounded-xl border divide-y">
                        {Object.entries(readiness.required).map(([type, needed]) => {
                            const done = readiness.completed[type] ?? 0;
                            const met = done >= needed;
                            return (
                                <div key={type} className="flex items-center justify-between px-4 py-3">
                                    <div className="flex items-center gap-2">
                                        {met
                                            ? <CheckCircle className="size-4 text-green-600" />
                                            : <XCircle className="size-4 text-muted-foreground" />
                                        }
                                        <span className="text-sm">
                                            {readinessTypeLabels[type] ?? type}
                                        </span>
                                    </div>
                                    <span className="text-sm text-muted-foreground">
                                        {done} / {needed}
                                    </span>
                                </div>
                            );
                        })}
                    </div>

                    {balance.outstanding > 0 && (
                        <div className="rounded-xl border px-4 py-3 flex items-center justify-between">
                            <span className="text-sm text-muted-foreground">Udestående saldo</span>
                            <span className="text-sm font-medium">
                                {Number(balance.outstanding).toLocaleString('da-DK')} kr.
                            </span>
                        </div>
                    )}
                </div>

            </div>
        </AppLayout>
    );
}
