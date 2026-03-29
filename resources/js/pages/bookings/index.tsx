import type { EventClickArg } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import type { EventDropArg, EventResizeDoneArg } from '@fullcalendar/interaction';
import interactionPlugin from '@fullcalendar/interaction';
import FullCalendar from '@fullcalendar/react';
import timeGridPlugin from '@fullcalendar/timegrid';
import { Head, Link, router } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { useState } from 'react';
import BookingNoteController from '@/actions/App/Http/Controllers/Bookings/BookingNoteController';
import BookingSkillsController from '@/actions/App/Http/Controllers/Bookings/BookingSkillsController';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';
import { index, create, update, destroy } from '@/routes/bookings';
import { store as storeAttendance } from '@/routes/bookings/attendance';
import type { BreadcrumbItem } from '@/types';
import type { BookingEvent } from '@/types/booking';
import { bookingTypeColors, bookingTypeLabels } from '@/types/booking';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Bookinger', href: index().url },
];

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

type FilterOption = { id: number; name: string };
type Filters = { instructor_id: string; vehicle_id: string };

export default function BookingsIndex({
    bookings,
    instructors = [],
    vehicles = [],
    filters,
}: {
    bookings: BookingEvent[];
    instructors?: FilterOption[];
    vehicles?: FilterOption[];
    filters?: Filters;
}) {
    const [selected, setSelected] = useState<BookingEvent | null>(null);

    const events = bookings.map((b) => ({
        id: String(b.id),
        title: b.title,
        start: b.start,
        end: b.end,
        backgroundColor: bookingTypeColors[b.type],
        borderColor: bookingTypeColors[b.type],
        opacity: b.status === 'cancelled' ? 0.4 : 1,
        extendedProps: b,
    }));

    function handleDrop(info: EventDropArg) {
        const booking = info.event.extendedProps as BookingEvent;
        router.patch(update(booking).url, {
            starts_at: info.event.startStr,
            ends_at: info.event.endStr,
        }, {
            onError: () => info.revert(),
        });
    }

    function handleResize(info: EventResizeDoneArg) {
        const booking = info.event.extendedProps as BookingEvent;
        router.patch(update(booking).url, {
            starts_at: info.event.startStr,
            ends_at: info.event.endStr,
        }, {
            onError: () => info.revert(),
        });
    }

    function handleEventClick(info: EventClickArg) {
        setSelected(info.event.extendedProps as BookingEvent);
    }

    function handleCancel(booking: BookingEvent) {
        if (confirm('Er du sikker på, at du vil annullere denne booking?')) {
            router.delete(destroy(booking).url);
            setSelected(null);
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

                <div className="flex flex-wrap items-center gap-3">
                    {/* Legend */}
                    <div className="flex flex-wrap gap-3 text-sm">
                        {(Object.entries(bookingTypeLabels) as [keyof typeof bookingTypeLabels, string][]).map(([type, label]) => (
                            <span key={type} className="flex items-center gap-1.5">
                                <span
                                    className="inline-block size-3 rounded-sm"
                                    style={{ backgroundColor: bookingTypeColors[type] }}
                                />
                                {label}
                            </span>
                        ))}
                    </div>

                    <div className="ml-auto flex gap-2">
                        <Select
                            value={filters?.instructor_id || 'all'}
                            onValueChange={(v) => {
                                router.get(index().url, {
                                    ...(filters?.vehicle_id ? { vehicle_id: filters.vehicle_id } : {}),
                                    ...(v !== 'all' ? { instructor_id: v } : {}),
                                }, { preserveState: true, replace: true });
                            }}
                        >
                            <SelectTrigger className="w-44 bg-background">
                                <SelectValue placeholder="Alle instruktører" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">Alle instruktører</SelectItem>
                                {instructors.map((i) => (
                                    <SelectItem key={i.id} value={String(i.id)}>{i.name}</SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                        <Select
                            value={filters?.vehicle_id || 'all'}
                            onValueChange={(v) => {
                                router.get(index().url, {
                                    ...(filters?.instructor_id ? { instructor_id: filters.instructor_id } : {}),
                                    ...(v !== 'all' ? { vehicle_id: v } : {}),
                                }, { preserveState: true, replace: true });
                            }}
                        >
                            <SelectTrigger className="w-44 bg-background">
                                <SelectValue placeholder="Alle køretøjer" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="all">Alle køretøjer</SelectItem>
                                {vehicles.map((v) => (
                                    <SelectItem key={v.id} value={String(v.id)}>{v.name}</SelectItem>
                                ))}
                            </SelectContent>
                        </Select>
                    </div>
                </div>

                <div className="rounded-xl border p-4">
                    <FullCalendar
                        plugins={[dayGridPlugin, timeGridPlugin, interactionPlugin]}
                        initialView="timeGridWeek"
                        headerToolbar={{
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek,timeGridDay',
                        }}
                        locale="da"
                        firstDay={1}
                        slotMinTime="07:00:00"
                        slotMaxTime="20:00:00"
                        height="auto"
                        editable
                        events={events}
                        eventDrop={handleDrop}
                        eventResize={handleResize}
                        eventClick={handleEventClick}
                    />
                </div>

                {/* Detail panel */}
                {selected && (
                    <div className="rounded-xl border p-4">
                        <div className="flex items-start justify-between">
                            <div className="space-y-1">
                                <p className="font-medium">{selected.title}</p>
                                <p className="text-sm text-muted-foreground">
                                    {bookingTypeLabels[selected.type]} · {selected.instructor}
                                    {selected.vehicle && ` · ${selected.vehicle}`}
                                </p>
                                {selected.notes && (
                                    <p className="text-sm text-muted-foreground">{selected.notes}</p>
                                )}
                                {selected.attendance_recorded_at && (
                                    <p className="text-xs text-muted-foreground">
                                        Fremmøde registreret{' '}
                                        {new Date(selected.attendance_recorded_at).toLocaleString('da-DK', {
                                            dateStyle: 'short',
                                            timeStyle: 'short',
                                        })}
                                        {selected.attended === true && ' · Mødt'}
                                        {selected.attended === false && ' · Ikke mødt'}
                                    </p>
                                )}
                            </div>
                            <div className="flex flex-wrap gap-2">
                                {selected.status === 'scheduled' && (
                                    <>
                                        <Button
                                            variant="default"
                                            size="sm"
                                            onClick={() => {
                                                router.post(storeAttendance({ booking: selected.id }).url, { attended: true }, {
                                                    preserveScroll: true,
                                                    onSuccess: () => setSelected(null),
                                                });
                                            }}
                                        >
                                            Mødt
                                        </Button>
                                        <Button
                                            variant="secondary"
                                            size="sm"
                                            onClick={() => {
                                                router.post(storeAttendance({ booking: selected.id }).url, { attended: false }, {
                                                    preserveScroll: true,
                                                    onSuccess: () => setSelected(null),
                                                });
                                            }}
                                        >
                                            Mødt ikke
                                        </Button>
                                        <Button
                                            variant="destructive"
                                            size="sm"
                                            onClick={() => handleCancel(selected)}
                                        >
                                            Annuller
                                        </Button>
                                    </>
                                )}
                                <Button variant="ghost" size="sm" onClick={() => setSelected(null)}>
                                    ✕
                                </Button>
                            </div>
                        </div>
                        {/* Instructor note */}
                        <div className="mt-3 space-y-1.5">
                            <p className="text-xs font-medium text-muted-foreground">Note til elev</p>
                            <NoteForm bookingId={selected.id} initialNote={selected.instructor_note} />
                        </div>

                        {/* Driving skills — only for driving_lesson */}
                        {selected.type === 'driving_lesson' && (
                            <div className="mt-3 space-y-1.5">
                                <p className="text-xs font-medium text-muted-foreground">Færdigheder øvet</p>
                                <SkillPicker bookingId={selected.id} initialSkills={selected.driving_skills ?? []} />
                            </div>
                        )}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}

function NoteForm({ bookingId, initialNote }: { bookingId: number; initialNote: string | null }) {
    const [note, setNote] = useState(initialNote ?? '');

    function save() {
        router.patch(BookingNoteController({ id: bookingId }).url, { instructor_note: note }, { preserveScroll: true });
    }

    return (
        <textarea
            value={note}
            onChange={(e) => setNote(e.target.value)}
            onBlur={save}
            rows={3}
            placeholder="Skriv note til elev..."
            className="w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-1 focus:ring-ring"
        />
    );
}

function SkillPicker({ bookingId, initialSkills }: { bookingId: number; initialSkills: string[] }) {
    const [skills, setSkills] = useState(initialSkills);

    function toggleSkill(key: string) {
        const next = skills.includes(key) ? skills.filter((s) => s !== key) : [...skills, key];
        setSkills(next);
        router.patch(BookingSkillsController({ id: bookingId }).url, { driving_skills: next }, { preserveScroll: true });
    }

    return (
        <div className="flex flex-wrap gap-1.5">
            {ALL_SKILLS.map((skill) => (
                <button
                    key={skill.key}
                    type="button"
                    onClick={() => toggleSkill(skill.key)}
                    className={`rounded-full border px-2.5 py-0.5 text-xs transition-colors ${
                        skills.includes(skill.key)
                            ? 'border-primary bg-primary/10 font-medium text-primary'
                            : 'border-input text-muted-foreground hover:border-primary/40'
                    }`}
                >
                    {skill.label}
                </button>
            ))}
        </div>
    );
}
