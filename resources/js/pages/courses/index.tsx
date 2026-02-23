import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import { Calendar, dateFnsLocalizer, View } from 'react-big-calendar';
import { format, parse, startOfWeek, getDay } from 'date-fns';
import { da } from 'date-fns/locale';
import 'react-big-calendar/lib/css/react-big-calendar.css';
import Heading from '@/components/heading';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';
import { show } from '@/actions/App/Http/Controllers/Courses/CourseController';

type CalendarEvent = {
    id: string;
    title: string;
    start: string;
    end: string;
    calendarId: string;
    offer_id: number;
};

type OfferMeta = {
    id: number;
    name: string;
    color: string;
};

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Kurser', href: '#' }];

const localizer = dateFnsLocalizer({
    format,
    parse,
    startOfWeek: () => startOfWeek(new Date(), { weekStartsOn: 1 }),
    getDay,
    locales: { da },
});

export default function CoursesIndex({
    events,
    offers,
}: {
    events: CalendarEvent[];
    offers: OfferMeta[];
}) {
    const [view, setView] = useState<View>('month');

    const offerColors = Object.fromEntries(offers.map((o) => [o.id, o.color]));

    const calendarEvents = events.map((e) => ({
        id: e.id,
        title: e.title,
        start: new Date(e.start),
        end: new Date(e.end),
        resource: { offer_id: e.offer_id },
    }));

    function eventPropGetter(event: (typeof calendarEvents)[number]) {
        const color = offerColors[event.resource.offer_id] ?? '#6366f1';
        return {
            style: {
                backgroundColor: color,
                borderColor: color,
                color: '#fff',
            },
        };
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Kurser" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <Heading title="Kurser" />
                <div className="flex-1 min-h-0">
                    <Calendar
                        localizer={localizer}
                        events={calendarEvents}
                        defaultView="month"
                        view={view}
                        onView={setView}
                        views={['month', 'week']}
                        culture="da"
                        eventPropGetter={eventPropGetter}
                        onSelectEvent={(event) => router.visit(show({ course: Number(event.id) }).url)}
                        style={{ height: '100%' }}
                        messages={{
                            next: '›',
                            previous: '‹',
                            today: 'I dag',
                            month: 'Måned',
                            week: 'Uge',
                            noEventsInRange: 'Ingen kurser i denne periode.',
                        }}
                    />
                </div>
            </div>
        </AppLayout>
    );
}
