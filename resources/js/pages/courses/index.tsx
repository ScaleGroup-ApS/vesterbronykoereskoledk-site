import { Head, router } from '@inertiajs/react';
import { useCalendarApp, ScheduleXCalendar } from '@schedule-x/react';
import { createViewMonthGrid, createViewWeek } from '@schedule-x/calendar';
import { CalendarEvent as ScheduleXEvent } from '@schedule-x/calendar';
import '@schedule-x/theme-default/dist/index.css';
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

export default function CoursesIndex({
    events,
    offers,
}: {
    events: CalendarEvent[];
    offers: OfferMeta[];
}) {
    const calendarsConfig = Object.fromEntries(
        offers.map((offer) => [
            `offer-${offer.id}`,
            {
                colorName: `offer-${offer.id}`,
                lightColors: {
                    main: offer.color,
                    container: offer.color + '33',
                    onContainer: offer.color,
                },
            },
        ]),
    );

    const calendar = useCalendarApp({
        defaultView: createViewMonthGrid().name,
        views: [createViewMonthGrid(), createViewWeek()],
        events,
        calendars: calendarsConfig,
        callbacks: {
            onEventClick(event: ScheduleXEvent) {
                router.visit(show({ course: Number(event.id) }).url);
            },
        },
    });

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Kurser" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <Heading title="Kurser" />
                <ScheduleXCalendar calendarApp={calendar} />
            </div>
        </AppLayout>
    );
}
