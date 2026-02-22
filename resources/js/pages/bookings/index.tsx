import { Head, Link, router } from '@inertiajs/react';
import { Plus, Trash2 } from 'lucide-react';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';
import { index, create, destroy } from '@/routes/bookings';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Bookinger', href: index().url },
];

type Booking = {
    id: number;
    student: { user: { name: string } };
    instructor: { name: string };
    vehicle: { name: string } | null;
    type: string;
    status: string;
    starts_at: string;
    ends_at: string;
};

type PaginatedBookings = {
    data: Booking[];
    links: { prev: string | null; next: string | null };
    meta: { from: number | null; to: number | null; total: number; last_page: number };
};

const statusLabels: Record<string, string> = {
    scheduled: 'Planlagt',
    completed: 'Gennemført',
    cancelled: 'Annulleret',
    no_show: 'Udeblevet',
};

const typeLabels: Record<string, string> = {
    driving_lesson: 'Køretime',
    theory_lesson: 'Teorilektion',
    track_driving: 'Banekørsel',
    slippery_driving: 'Glat bane',
    exam: 'Eksamen',
};

export default function BookingsIndex({ bookings }: { bookings: PaginatedBookings }) {
    function handleCancel(booking: Booking) {
        if (confirm(`Er du sikker på, at du vil annullere denne booking?`)) {
            router.delete(destroy(booking).url);
        }
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Bookinger" />

            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <Heading title="Bookinger" description="Administrer køretimer og lektioner" />
                    <Button asChild>
                        <Link href={create().url}>
                            <Plus className="mr-2 size-4" />
                            Opret booking
                        </Link>
                    </Button>
                </div>

                <div className="rounded-xl border">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b text-left">
                                <th className="px-4 py-3 font-medium">Elev</th>
                                <th className="px-4 py-3 font-medium">Instruktør</th>
                                <th className="px-4 py-3 font-medium">Type</th>
                                <th className="px-4 py-3 font-medium">Status</th>
                                <th className="px-4 py-3 font-medium">Start</th>
                                <th className="px-4 py-3 font-medium"></th>
                            </tr>
                        </thead>
                        <tbody>
                            {bookings.data.map((booking) => (
                                <tr key={booking.id} className="border-b last:border-0">
                                    <td className="px-4 py-3 font-medium">{booking.student.user.name}</td>
                                    <td className="px-4 py-3">{booking.instructor.name}</td>
                                    <td className="px-4 py-3">{typeLabels[booking.type] ?? booking.type}</td>
                                    <td className="px-4 py-3">
                                        <Badge variant={booking.status === 'completed' ? 'default' : 'secondary'}>
                                            {statusLabels[booking.status] ?? booking.status}
                                        </Badge>
                                    </td>
                                    <td className="px-4 py-3 text-muted-foreground">
                                        {new Date(booking.starts_at).toLocaleString('da-DK')}
                                    </td>
                                    <td className="px-4 py-3 text-right">
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            onClick={() => handleCancel(booking)}
                                        >
                                            <Trash2 className="size-4" />
                                        </Button>
                                    </td>
                                </tr>
                            ))}
                            {bookings.data.length === 0 && (
                                <tr>
                                    <td colSpan={6} className="px-4 py-8 text-center text-muted-foreground">
                                        Ingen bookinger fundet.
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
