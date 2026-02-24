import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import Heading from '@/components/heading';

interface Event {
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
}

interface Props {
    date: string;
    events: Event[];
}

export default function BookingDay({ date, events }: Props) {
    return (
        <AppLayout>
            <Head title={`Bookinger – ${date}`} />
            <div className="p-6">
                <Heading title={`Bookinger – ${date}`} />
                <p className="text-muted-foreground mt-2 text-sm">{events.length} event(s)</p>
            </div>
        </AppLayout>
    );
}
