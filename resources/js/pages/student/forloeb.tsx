import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, CheckCircle, Download, FileText, XCircle } from 'lucide-react';
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

type Material = {
    id: number;
    name: string;
    file_name: string;
    mime_type: string;
    size: string;
    url: string;
    offer_name: string;
};

type PastBooking = {
    id: number;
    type: string;
    type_label: string;
    status: string;
    starts_at: string;
    ends_at: string;
    range_label: string;
    attended: boolean | null;
    attendance_recorded_at: string | null;
};

const readinessTypeLabels: Record<string, string> = {
    driving_lesson: 'Køretimer',
    theory_lesson: 'Teorilektioner',
    track_driving: 'Banekørsel',
    slippery_driving: 'Glat bane',
    theory_exam: 'Teoriprøve',
    practical_exam: 'Køreprøve',
};

const bookingStatusLabels: Record<string, string> = {
    scheduled: 'Planlagt',
    completed: 'Gennemført',
    cancelled: 'Annulleret',
    no_show: 'Udeblevet',
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Oversigt', href: dashboard().url },
    { title: 'Mit forløb', href: forloeb().url },
];

export default function StudentForloeb({
    journey,
    readiness,
    balance,
    materials,
    past_bookings,
}: {
    journey: JourneyPayload;
    readiness: Readiness;
    balance: Balance;
    materials: Material[];
    past_bookings: PastBooking[];
}) {
    return (
        <StudentLayout breadcrumbs={breadcrumbs}>
            <Head title="Mit forløb" />
            <div className="flex h-full flex-1 flex-col gap-8 rounded-xl p-4">
                <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                    <div className="space-y-1">
                        <Heading title="Mit forløb" />
                        <p className="text-sm text-muted-foreground">
                            Her ser du din progression, materiale og tidligere bookinger.
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

                {materials.length > 0 && (
                    <section className="space-y-3">
                        <Heading variant="small" title="Kursusmateriale" />
                        <div className="divide-y rounded-xl border">
                            {materials.map((material) => (
                                <a
                                    key={material.id}
                                    href={material.url}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="flex items-center justify-between px-4 py-3 transition-colors hover:bg-muted/50"
                                >
                                    <div className="flex min-w-0 items-center gap-2">
                                        <FileText className="size-4 shrink-0 text-muted-foreground" />
                                        <span className="truncate text-sm">{material.name}</span>
                                    </div>
                                    <div className="flex shrink-0 items-center gap-3">
                                        <span className="text-xs text-muted-foreground">{material.size}</span>
                                        <Download className="size-4 text-muted-foreground" />
                                    </div>
                                </a>
                            ))}
                        </div>
                    </section>
                )}

                <section className="space-y-3">
                    <Heading variant="small" title="Historik" />
                    {past_bookings.length === 0 ? (
                        <p className="text-sm text-muted-foreground">Ingen tidligere bookinger endnu.</p>
                    ) : (
                        <div className="overflow-x-auto rounded-xl border">
                            <table className="w-full min-w-[640px] text-left text-sm">
                                <thead>
                                    <tr className="border-b bg-muted/40 text-muted-foreground">
                                        <th className="px-4 py-3 font-medium">Tidspunkt</th>
                                        <th className="px-4 py-3 font-medium">Type</th>
                                        <th className="px-4 py-3 font-medium">Status</th>
                                        <th className="px-4 py-3 font-medium">Fremmøde</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y">
                                    {past_bookings.map((row) => (
                                        <tr key={row.id} className="hover:bg-muted/30">
                                            <td className="px-4 py-3 whitespace-nowrap">{row.range_label}</td>
                                            <td className="px-4 py-3">{row.type_label}</td>
                                            <td className="px-4 py-3">
                                                <Badge variant="outline" className="font-normal">
                                                    {bookingStatusLabels[row.status] ?? row.status}
                                                </Badge>
                                            </td>
                                            <td className="px-4 py-3 text-muted-foreground">
                                                {row.attended === true && 'Mødt'}
                                                {row.attended === false && 'Ikke mødt'}
                                                {row.attended === null && '—'}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </section>
            </div>
        </StudentLayout>
    );
}
