import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { index } from '@/routes/bookings';
import { bookingTypeColors } from '@/types/booking';
import type { BreadcrumbItem } from '@/types';
import dayGridPlugin from '@fullcalendar/daygrid';
import FullCalendar from '@fullcalendar/react';
import timeGridPlugin from '@fullcalendar/timegrid';
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

const TEAM_COLOR = '#64748b';

export default function BookingDay({ date, events }: { date: string; events: DayEvent[] }) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: dashboard().url },
        { title: date, href: '#' },
    ];

    const calendarEvents = events.map((e) => ({
        id: e.id,
        title: e.title,
        start: e.start,
        end: e.end,
        backgroundColor: e.team_id !== null ? TEAM_COLOR : (bookingTypeColors[e.type as keyof typeof bookingTypeColors] ?? '#6b7280'),
        borderColor: e.team_id !== null ? TEAM_COLOR : (bookingTypeColors[e.type as keyof typeof bookingTypeColors] ?? '#6b7280'),
        opacity: e.status === 'cancelled' ? 0.4 : 1,
        extendedProps: e,
    }));

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
                        <Link href={index().url}>Alle bookinger</Link>
                    </Button>
                </div>

                <div className="rounded-xl border p-4">
                    <FullCalendar
                        plugins={[timeGridPlugin, dayGridPlugin]}
                        initialView="timeGridDay"
                        initialDate={date}
                        headerToolbar={false}
                        locale="da"
                        firstDay={1}
                        slotMinTime="07:00:00"
                        slotMaxTime="20:00:00"
                        height="auto"
                        events={calendarEvents}
                    />
                </div>
            </div>
        </AppLayout>
    );
}
