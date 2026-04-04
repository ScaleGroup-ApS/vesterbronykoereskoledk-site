import { Head } from '@inertiajs/react';
import { CheckCircle, Clock, UserIcon, XCircle } from 'lucide-react';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import StudentLayout from '@/layouts/student-layout';
import { history } from '@/routes/student';
import type { BreadcrumbItem } from '@/types';

type PastBooking = {
    id: number;
    type: string;
    type_label: string;
    status: string;
    range_label: string;
    attended: boolean | null;
    instructor_note: string | null;
    driving_skills: string[];
    instructor_name?: string;
};

const bookingStatusConfig: Record<string, { label: string; variant: 'default' | 'secondary' | 'destructive' | 'outline' }> = {
    scheduled: { label: 'Planlagt', variant: 'outline' },
    completed: { label: 'Gennemført', variant: 'default' },
    cancelled: { label: 'Annulleret', variant: 'secondary' },
    no_show: { label: 'Udeblevet', variant: 'destructive' },
};

const skillLabels: Record<string, string> = {
    parking: 'Parkering',
    motorvej: 'Motorvej',
    roundabouts: 'Rundkørsel',
    city_driving: 'Bykørsel',
    overtaking: 'Overhaling',
    reversing: 'Bakring',
    lane_change: 'Filskifte',
    emergency_stop: 'Nødstop',
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Historik', href: history().url },
];

function BookingCard({ row }: { row: PastBooking }) {
    const statusConfig = bookingStatusConfig[row.status] ?? { label: row.status, variant: 'outline' as const };

    return (
        <div className="group relative space-y-3 px-5 py-4 transition hover:bg-muted/30">
            {/* Header row */}
            <div className="flex flex-wrap items-start justify-between gap-2">
                <div className="space-y-0.5">
                    <p className="font-medium">{row.type_label}</p>
                    <p className="text-xs text-muted-foreground">{row.range_label}</p>
                </div>
                <div className="flex items-center gap-2">
                    <Badge variant={statusConfig.variant} className="text-xs font-normal">
                        {statusConfig.label}
                    </Badge>
                    {row.attended === true && (
                        <span className="inline-flex items-center gap-1 text-xs font-medium text-green-600">
                            <CheckCircle className="size-3" />
                            Mødt
                        </span>
                    )}
                    {row.attended === false && (
                        <span className="inline-flex items-center gap-1 text-xs font-medium text-destructive">
                            <XCircle className="size-3" />
                            Ikke mødt
                        </span>
                    )}
                </div>
            </div>

            {/* Instructor name */}
            {row.instructor_name && (
                <div className="flex items-center gap-1.5 text-xs text-muted-foreground">
                    <UserIcon className="size-3" />
                    {row.instructor_name}
                </div>
            )}

            {/* Skills pills */}
            {row.driving_skills.length > 0 && (
                <div className="flex flex-wrap gap-1.5">
                    {row.driving_skills.map((key) => (
                        <span
                            key={key}
                            className="rounded-full border border-primary/20 bg-primary/5 px-2.5 py-0.5 text-xs font-medium text-primary"
                        >
                            {skillLabels[key] ?? key}
                        </span>
                    ))}
                </div>
            )}

            {/* Instructor note */}
            {row.instructor_note && (
                <blockquote className="rounded-lg border-l-2 border-primary/30 bg-muted/30 py-2 pl-3 pr-3 text-sm italic text-muted-foreground">
                    {row.instructor_note}
                </blockquote>
            )}
        </div>
    );
}

export default function StudentHistorik({ past_bookings }: { past_bookings: PastBooking[] }) {
    const completed = past_bookings.filter(b => b.status === 'completed').length;
    const total = past_bookings.length;

    return (
        <StudentLayout breadcrumbs={breadcrumbs}>
            <Head title="Historik" />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4 sm:p-6">
                <div className="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div className="space-y-1">
                        <Heading title="Historik" />
                        <p className="text-sm text-muted-foreground">Dine tidligere lektioner og noter fra instruktøren.</p>
                    </div>
                    {total > 0 && (
                        <p className="text-sm tabular-nums text-muted-foreground">
                            {completed} gennemført af {total} bookinger
                        </p>
                    )}
                </div>

                {past_bookings.length === 0 ? (
                    <div className="flex flex-col items-center gap-3 rounded-2xl border border-dashed py-10 text-center">
                        <Clock className="size-10 text-muted-foreground/30" />
                        <div>
                            <p className="font-medium text-muted-foreground">Ingen tidligere bookinger endnu</p>
                            <p className="mt-1 text-sm text-muted-foreground/70">
                                Din lektionshistorik vil vises her efter din første time.
                            </p>
                        </div>
                    </div>
                ) : (
                    <div className="divide-y rounded-xl border shadow-sm">
                        {past_bookings.map((row) => (
                            <BookingCard key={row.id} row={row} />
                        ))}
                    </div>
                )}
            </div>
        </StudentLayout>
    );
}
