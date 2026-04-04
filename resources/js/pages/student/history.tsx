import { Head } from '@inertiajs/react';
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
};

const bookingStatusLabels: Record<string, string> = {
    scheduled: 'Planlagt',
    completed: 'Gennemført',
    cancelled: 'Annulleret',
    no_show: 'Udeblevet',
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

export default function StudentHistorik({ past_bookings }: { past_bookings: PastBooking[] }) {
    return (
        <StudentLayout breadcrumbs={breadcrumbs}>
            <Head title="Historik" />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <div className="space-y-1">
                    <Heading title="Historik" />
                    <p className="text-sm text-muted-foreground">Dine tidligere lektioner og noter fra instruktøren.</p>
                </div>

                {past_bookings.length === 0 ? (
                    <p className="text-sm text-muted-foreground">Ingen tidligere bookinger endnu.</p>
                ) : (
                    <div className="divide-y rounded-xl border">
                        {past_bookings.map((row) => (
                            <div key={row.id} className="px-4 py-3">
                                <div className="flex flex-wrap items-start justify-between gap-2">
                                    <div>
                                        <p className="text-sm font-medium">{row.type_label}</p>
                                        <p className="text-xs text-muted-foreground">{row.range_label}</p>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <Badge variant="outline" className="text-xs font-normal">
                                            {bookingStatusLabels[row.status] ?? row.status}
                                        </Badge>
                                        {row.attended === true && (
                                            <span className="text-xs font-medium text-green-600">Mødt</span>
                                        )}
                                        {row.attended === false && (
                                            <span className="text-xs font-medium text-destructive">Ikke mødt</span>
                                        )}
                                    </div>
                                </div>

                                {row.driving_skills.length > 0 && (
                                    <div className="mt-2 flex flex-wrap gap-1.5">
                                        {row.driving_skills.map((key) => (
                                            <span
                                                key={key}
                                                className="rounded-full border border-primary/30 bg-primary/5 px-2.5 py-0.5 text-xs font-medium text-primary"
                                            >
                                                {skillLabels[key] ?? key}
                                            </span>
                                        ))}
                                    </div>
                                )}

                                {row.instructor_note && (
                                    <blockquote className="mt-2 border-l-2 border-muted pl-3 text-sm italic text-muted-foreground">
                                        {row.instructor_note}
                                    </blockquote>
                                )}
                            </div>
                        ))}
                    </div>
                )}
            </div>
        </StudentLayout>
    );
}
