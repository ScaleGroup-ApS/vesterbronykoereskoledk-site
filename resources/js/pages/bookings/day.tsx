import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';
import { bookingTypeColors, bookingTypeLabels } from '@/types/booking';
import { Head, Link } from '@inertiajs/react';

type DayEvent = {
    id: string;
    title: string;
    start: string;
    end: string;
    type: string;
    status: string;
    team_id: number | null;
    instructor: string | null;
    vehicle: string | null;
    notes: string | null;
};

const statusLabels: Record<string, string> = {
    scheduled: 'Planlagt',
    completed: 'Gennemført',
    cancelled: 'Annulleret',
    no_show: 'Udeblevet',
};

function formatTime(iso: string): string {
    return new Date(iso).toLocaleTimeString('da-DK', { hour: '2-digit', minute: '2-digit' });
}

export default function BookingDay({ date, events }: { date: string; events: DayEvent[] }) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: dashboard().url },
        { title: date, href: '#' },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Bookinger – ${date}`} />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <Heading
                        title={`Bookinger – ${date}`}
                        description="Køretimer og lektioner for dagen"
                    />
                    <Button variant="outline" asChild>
                        <Link href={dashboard().url}>Dashboard</Link>
                    </Button>
                </div>

                <div className="rounded-xl border">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b text-left">
                                <th className="px-4 py-3 font-medium">Tidspunkt</th>
                                <th className="px-4 py-3 font-medium">Elev / Hold</th>
                                <th className="px-4 py-3 font-medium">Type</th>
                                <th className="px-4 py-3 font-medium">Status</th>
                                <th className="px-4 py-3 font-medium">Instruktør</th>
                                <th className="px-4 py-3 font-medium">Køretøj</th>
                            </tr>
                        </thead>
                        <tbody>
                            {events.map((event) => (
                                <tr key={event.id} className="border-b last:border-0">
                                    <td className="px-4 py-3 tabular-nums text-muted-foreground">
                                        {formatTime(event.start)} – {formatTime(event.end)}
                                    </td>
                                    <td className="px-4 py-3 font-medium">{event.title}</td>
                                    <td className="px-4 py-3">
                                        <span className="flex items-center gap-1.5">
                                            <span
                                                className="inline-block size-2.5 rounded-full"
                                                style={{
                                                    backgroundColor:
                                                        bookingTypeColors[event.type as keyof typeof bookingTypeColors] ??
                                                        '#6b7280',
                                                }}
                                            />
                                            {bookingTypeLabels[event.type as keyof typeof bookingTypeLabels] ?? event.type}
                                        </span>
                                    </td>
                                    <td className="px-4 py-3 text-muted-foreground">
                                        {statusLabels[event.status] ?? event.status}
                                    </td>
                                    <td className="px-4 py-3 text-muted-foreground">{event.instructor ?? '–'}</td>
                                    <td className="px-4 py-3 text-muted-foreground">{event.vehicle ?? '–'}</td>
                                </tr>
                            ))}
                            {events.length === 0 && (
                                <tr>
                                    <td colSpan={6} className="px-4 py-8 text-center text-muted-foreground">
                                        Ingen bookinger denne dag.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>
        </AppLayout>
    );
}
