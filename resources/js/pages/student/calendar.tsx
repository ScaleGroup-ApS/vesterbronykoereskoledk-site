import type { EventInput } from '@fullcalendar/core';
import daLocale from '@fullcalendar/core/locales/da';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import FullCalendar from '@fullcalendar/react';
import timeGridPlugin from '@fullcalendar/timegrid';
import { Head } from '@inertiajs/react';
import { Calendar } from 'lucide-react';
import Heading from '@/components/heading';
import StudentLayout from '@/layouts/student-layout';
import { calendar, dashboard } from '@/routes/student';
import type { BreadcrumbItem } from '@/types';
import { bookingTypeColors, bookingTypeLabels } from '@/types/booking';

export type StudentCalendarEvent = {
    id: string;
    title: string;
    start: string;
    end: string;
    type: string;
    status: string;
};

const courseHoldColor = '#64748b';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Oversigt', href: dashboard().url },
    { title: 'Kalender', href: calendar().url },
];

function colorForType(type: string): string {
    if (type === 'course_hold') {
        return courseHoldColor;
    }
    if (type in bookingTypeColors) {
        return bookingTypeColors[type as keyof typeof bookingTypeColors];
    }

    return '#64748b';
}

export default function StudentKalender({ events }: { events: StudentCalendarEvent[] }) {
    const calendarEvents: EventInput[] = events.map((e) => ({
        id: e.id,
        title: e.title,
        start: e.start,
        end: e.end,
        backgroundColor: colorForType(e.type),
        borderColor: colorForType(e.type),
        display: e.type === 'course_hold' ? 'background' : 'auto',
    }));

    return (
        <StudentLayout breadcrumbs={breadcrumbs}>
            <Head title="Kalender" />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <div className="space-y-1">
                    <Heading title="Kalender" />
                    <p className="text-sm text-muted-foreground">
                        Dine bookede aktiviteter og holdperiode. Ændringer aftales med køreskolen.
                    </p>
                </div>

                <div className="flex flex-wrap gap-3 text-xs text-muted-foreground">
                    <span className="flex items-center gap-1.5">
                        <Calendar className="size-3.5" aria-hidden />
                        <span>Forklaring:</span>
                    </span>
                    {(Object.entries(bookingTypeLabels) as [keyof typeof bookingTypeLabels, string][]).map(
                        ([type, label]) => (
                            <span key={type} className="flex items-center gap-1">
                                <span className="size-2.5 rounded-sm" style={{ background: bookingTypeColors[type] }} />
                                {label}
                            </span>
                        ),
                    )}
                    <span className="flex items-center gap-1">
                        <span className="size-2.5 rounded-sm" style={{ background: courseHoldColor }} />
                        Hold (periode)
                    </span>
                </div>

                <div className="min-h-[32rem] overflow-hidden rounded-xl border bg-card p-2">
                    <FullCalendar
                        plugins={[dayGridPlugin, timeGridPlugin, interactionPlugin]}
                        initialView="timeGridWeek"
                        locale={daLocale}
                        headerToolbar={{
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek,timeGridDay',
                        }}
                        slotMinTime="07:00:00"
                        slotMaxTime="21:00:00"
                        allDaySlot
                        height="auto"
                        events={calendarEvents}
                        editable={false}
                        selectable={false}
                        nowIndicator
                    />
                </div>
            </div>
        </StudentLayout>
    );
}
