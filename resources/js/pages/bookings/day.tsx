import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import BookingNoteController from '@/actions/App/Http/Controllers/Bookings/BookingNoteController';
import BookingSkillsController from '@/actions/App/Http/Controllers/Bookings/BookingSkillsController';
import { AttendanceCheckbox } from '@/components/bookings/attendance-checkbox';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { update } from '@/routes/bookings';
import type { BreadcrumbItem } from '@/types';
import { bookingTypeColors, bookingTypeLabels } from '@/types/booking';

type DayEvent = {
    id: string;
    booking_id: number | null;
    title: string;
    start: string;
    end: string;
    type: string;
    status: string;
    team_id: number | null;
    instructor_id: number | null;
    instructor: string | null;
    vehicle_id: number | null;
    vehicle: string | null;
    notes: string | null;
    attended: boolean | null;
    instructor_note: string | null;
    driving_skills: string[] | null;
};

type Instructor = { id: number; name: string };
type Vehicle = { id: number; name: string };

const statusLabels: Record<string, string> = {
    scheduled: 'Planlagt',
    completed: 'Gennemført',
    cancelled: 'Annulleret',
    no_show: 'Udeblevet',
};

const selectClass =
    'flex h-8 w-full rounded-md border border-input bg-transparent px-2 py-1 text-sm shadow-sm';

function formatDateTime(iso: string): string {
    return new Date(iso).toLocaleString('da-DK', {
        day: 'numeric',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit',
    });
}

const ALL_SKILLS = [
    { key: 'parking', label: 'Parkering' },
    { key: 'motorvej', label: 'Motorvej' },
    { key: 'roundabouts', label: 'Rundkørsel' },
    { key: 'city_driving', label: 'Bykørsel' },
    { key: 'overtaking', label: 'Overhaling' },
    { key: 'reversing', label: 'Bakring' },
    { key: 'lane_change', label: 'Filskifte' },
    { key: 'emergency_stop', label: 'Nødstop' },
] as const;

function InlineNoteSkills({ event }: { event: DayEvent }) {
    const [note, setNote] = useState(event.instructor_note ?? '');
    const [skills, setSkills] = useState<string[]>(event.driving_skills ?? []);

    function saveNote() {
        router.patch(BookingNoteController({ id: event.booking_id! }).url, { instructor_note: note }, { preserveScroll: true });
    }

    function toggleSkill(key: string) {
        const next = skills.includes(key) ? skills.filter((s) => s !== key) : [...skills, key];
        setSkills(next);
        router.patch(BookingSkillsController({ id: event.booking_id! }).url, { driving_skills: next }, { preserveScroll: true });
    }

    return (
        <div className="rounded-lg border bg-muted/20 px-4 py-3">
            <p className="mb-2 text-sm font-medium">{event.title} — {bookingTypeLabels[event.type as keyof typeof bookingTypeLabels] ?? event.type}</p>
            <textarea
                value={note}
                onChange={(e) => setNote(e.target.value)}
                onBlur={saveNote}
                placeholder="Skriv note til elev..."
                rows={2}
                className="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring"
            />
            {event.type === 'driving_lesson' && (
                <div className="mt-2 flex flex-wrap gap-2">
                    {ALL_SKILLS.map((skill) => (
                        <button
                            key={skill.key}
                            type="button"
                            onClick={() => toggleSkill(skill.key)}
                            className={`rounded-full border px-3 py-1 text-xs transition-colors ${
                                skills.includes(skill.key)
                                    ? 'border-primary bg-primary/10 font-medium text-primary'
                                    : 'border-input text-muted-foreground hover:border-primary/40'
                            }`}
                        >
                            {skill.label}
                        </button>
                    ))}
                </div>
            )}
        </div>
    );
}

export default function BookingDay({
    date,
    events,
    instructors,
    vehicles,
}: {
    date: string;
    events: DayEvent[];
    instructors: Instructor[];
    vehicles: Vehicle[];
}) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: dashboard().url },
        { title: date, href: '#' },
    ];

    function patchBooking(bookingId: number, data: Record<string, number | null>) {
        router.patch(update(bookingId).url, data, { preserveScroll: true });
    }

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
                                <th className="px-4 py-3 font-medium">Fremmøde</th>
                            </tr>
                        </thead>
                        <tbody>
                            {events.map((event) => (
                                <tr key={event.id} className="border-b last:border-0">
                                    <td className="px-4 py-3 tabular-nums text-muted-foreground">
                                        {formatDateTime(event.start)} – {formatDateTime(event.end)}
                                    </td>
                                    <td className="px-4 py-3 font-medium">{event.title}</td>
                                    <td className="px-4 py-3">
                                        <span className="flex items-center gap-1.5">
                                            <span
                                                className="inline-block size-2.5 rounded-full"
                                                style={{
                                                    backgroundColor:
                                                        bookingTypeColors[
                                                            event.type as keyof typeof bookingTypeColors
                                                        ] ?? '#6b7280',
                                                }}
                                            />
                                            {bookingTypeLabels[
                                                event.type as keyof typeof bookingTypeLabels
                                            ] ?? event.type}
                                        </span>
                                    </td>
                                    <td className="px-4 py-3 text-muted-foreground">
                                        {statusLabels[event.status] ?? event.status}
                                    </td>
                                    <td className="px-4 py-2">
                                        {event.booking_id !== null ? (
                                            <select
                                                className={selectClass}
                                                value={event.instructor_id ?? ''}
                                                onChange={(e) =>
                                                    patchBooking(event.booking_id!, {
                                                        instructor_id: e.target.value
                                                            ? Number(e.target.value)
                                                            : null,
                                                    })
                                                }
                                            >
                                                <option value="">Ingen</option>
                                                {instructors.map((i) => (
                                                    <option key={i.id} value={i.id}>
                                                        {i.name}
                                                    </option>
                                                ))}
                                            </select>
                                        ) : (
                                            <span className="text-muted-foreground">
                                                {event.instructor ?? '–'}
                                            </span>
                                        )}
                                    </td>
                                    <td className="px-4 py-2">
                                        {event.booking_id !== null &&
                                        event.type !== 'theory_lesson' ? (
                                            <select
                                                className={selectClass}
                                                value={event.vehicle_id ?? ''}
                                                onChange={(e) =>
                                                    patchBooking(event.booking_id!, {
                                                        vehicle_id: e.target.value
                                                            ? Number(e.target.value)
                                                            : null,
                                                    })
                                                }
                                            >
                                                <option value="">Intet</option>
                                                {vehicles.map((v) => (
                                                    <option key={v.id} value={v.id}>
                                                        {v.name}
                                                    </option>
                                                ))}
                                            </select>
                                        ) : (
                                            <span className="text-muted-foreground">
                                                {event.vehicle ?? '–'}
                                            </span>
                                        )}
                                    </td>
                                    <td className="px-4 py-2">
                                        {event.booking_id !== null ? (
                                            <AttendanceCheckbox bookingId={event.booking_id} attended={event.attended} />
                                        ) : (
                                            <span className="text-muted-foreground">–</span>
                                        )}
                                    </td>
                                </tr>
                            ))}
                            {events.length === 0 && (
                                <tr>
                                    <td
                                        colSpan={7}
                                        className="px-4 py-8 text-center text-muted-foreground"
                                    >
                                        Ingen bookinger denne dag.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {/* Instructor notes section below table */}
                {events.filter((e) => e.booking_id !== null).length > 0 && (
                    <div className="mt-4 space-y-3 rounded-xl border p-4">
                        <p className="text-sm font-medium text-muted-foreground">Notater & færdigheder</p>
                        {events
                            .filter((e) => e.booking_id !== null)
                            .map((event) => (
                                <InlineNoteSkills key={event.id} event={event} />
                            ))}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
