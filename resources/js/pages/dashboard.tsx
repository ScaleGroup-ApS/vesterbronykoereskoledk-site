import { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import { Calendar, dateFnsLocalizer, View } from 'react-big-calendar';
import { format, parse, startOfWeek, getDay } from 'date-fns';
import { da } from 'date-fns/locale';
import 'react-big-calendar/lib/css/react-big-calendar.css';
import { CalendarDays, Clock, CreditCard, TrendingDown, Users, Wallet } from 'lucide-react';
import AppLayout from '@/layouts/app-layout';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import InputError from '@/components/input-error';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Sheet, SheetContent, SheetHeader, SheetTitle } from '@/components/ui/sheet';
import type { BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';
import { store as storeCourse } from '@/actions/App/Http/Controllers/Offers/CourseController';
import { show as showCourse } from '@/actions/App/Http/Controllers/Courses/CourseController';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: dashboard().url }];

const localizer = dateFnsLocalizer({
    format,
    parse,
    startOfWeek: () => startOfWeek(new Date(), { weekStartsOn: 1 }),
    getDay,
    locales: { da },
});

type AdminKpis = {
    total_students: number;
    upcoming_bookings: number;
    no_show_rate: number;
    total_outstanding: number;
};

type InstructorKpis = {
    upcoming_bookings: number;
    no_show_rate: number;
};

type Kpis = AdminKpis | InstructorKpis | Record<string, never>;

type PendingEnrollment = {
    id: number;
    status: 'pending_payment' | 'pending_approval';
    payment_method: 'stripe' | 'cash';
} | null;

type OfferMeta = { id: number; name: string; color: string };
type CourseEvent = { id: string; title: string; start: string; end: string; offer_id: number };

function KpiCard({ icon: Icon, label, value }: { icon: React.ElementType; label: string; value: string | number }) {
    return (
        <div className="rounded-xl border p-5">
            <div className="flex items-center gap-3">
                <div className="flex size-10 items-center justify-center rounded-lg bg-muted">
                    <Icon className="size-5 text-muted-foreground" />
                </div>
                <div>
                    <p className="text-sm text-muted-foreground">{label}</p>
                    <p className="text-2xl font-semibold">{value}</p>
                </div>
            </div>
        </div>
    );
}

export default function Dashboard({
    kpis,
    pendingEnrollment,
    courseEvents = [],
    offers = [],
}: {
    kpis: Kpis;
    pendingEnrollment: PendingEnrollment;
    courseEvents: CourseEvent[];
    offers: OfferMeta[];
}) {
    const isAdmin = 'total_students' in kpis;
    const isInstructor = 'upcoming_bookings' in kpis && !isAdmin;

    const [view, setView] = useState<View>('month');
    const [sheetOpen, setSheetOpen] = useState(false);
    const [slotStart, setSlotStart] = useState('');
    const [slotEnd, setSlotEnd] = useState('');
    const [selectedOfferId, setSelectedOfferId] = useState<string>('');
    const [createErrors, setCreateErrors] = useState<Record<string, string>>({});
    const [creating, setCreating] = useState(false);

    const offerColors = Object.fromEntries(offers.map((o) => [o.id, o.color]));

    const calendarEvents = courseEvents.map((e) => ({
        id: e.id,
        title: e.title,
        start: new Date(e.start),
        end: new Date(e.end),
        resource: { offer_id: e.offer_id },
    }));

    function eventPropGetter(event: (typeof calendarEvents)[number]) {
        const color = offerColors[event.resource.offer_id] ?? '#6366f1';
        return { style: { backgroundColor: color, borderColor: color, color: '#fff' } };
    }

    function handleSelectSlot({ start, end }: { start: Date; end: Date }) {
        const fmt = (d: Date) => d.toISOString().slice(0, 16);
        const endDate =
            start.getTime() === end.getTime() - 86400000
                ? new Date(start.getTime() + 8 * 3600 * 1000)
                : end;
        setSlotStart(fmt(start));
        setSlotEnd(fmt(endDate));
        setSelectedOfferId(offers[0] ? String(offers[0].id) : '');
        setCreateErrors({});
        setSheetOpen(true);
    }

    function handleCreateCourse() {
        if (!selectedOfferId) {
            return;
        }
        setCreating(true);
        router.post(
            storeCourse({ offer: Number(selectedOfferId) }).url,
            { start_at: slotStart, end_at: slotEnd },
            {
                onError: (errs) => {
                    setCreateErrors(errs);
                    setCreating(false);
                },
                onSuccess: () => {
                    setSheetOpen(false);
                    setCreating(false);
                },
            },
        );
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                {pendingEnrollment && (
                    <Alert>
                        {pendingEnrollment.payment_method === 'stripe' ? (
                            <CreditCard className="size-4" />
                        ) : (
                            <Clock className="size-4" />
                        )}
                        <AlertTitle>
                            {pendingEnrollment.payment_method === 'stripe'
                                ? 'Afventer betaling'
                                : 'Afventer godkendelse'}
                        </AlertTitle>
                        <AlertDescription>
                            {pendingEnrollment.payment_method === 'stripe'
                                ? 'Din tilmelding afventer betalingsbekræftelse fra Stripe. Kontakt os, hvis du har problemer.'
                                : 'Din tilmelding afventer godkendelse fra en instruktør. Du vil modtage besked, når den er behandlet.'}
                        </AlertDescription>
                    </Alert>
                )}

                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    {isAdmin && (
                        <>
                            <KpiCard
                                icon={Users}
                                label="Aktive elever"
                                value={(kpis as AdminKpis).total_students}
                            />
                            <KpiCard
                                icon={CalendarDays}
                                label="Kommende bookinger (7 dage)"
                                value={(kpis as AdminKpis).upcoming_bookings}
                            />
                            <KpiCard
                                icon={TrendingDown}
                                label="No-show rate"
                                value={`${(kpis as AdminKpis).no_show_rate}%`}
                            />
                            <KpiCard
                                icon={Wallet}
                                label="Udestående saldo"
                                value={`${Number((kpis as AdminKpis).total_outstanding).toLocaleString('da-DK')} kr.`}
                            />
                        </>
                    )}
                    {isInstructor && (
                        <>
                            <KpiCard
                                icon={CalendarDays}
                                label="Kommende bookinger (7 dage)"
                                value={(kpis as InstructorKpis).upcoming_bookings}
                            />
                            <KpiCard
                                icon={TrendingDown}
                                label="No-show rate"
                                value={`${(kpis as InstructorKpis).no_show_rate}%`}
                            />
                        </>
                    )}
                    {!isAdmin && !isInstructor && !pendingEnrollment && (
                        <div className="col-span-full rounded-xl border px-4 py-6 text-center text-sm text-muted-foreground">
                            Ingen KPI-data tilgængelig.
                        </div>
                    )}
                </div>

                {(isAdmin || isInstructor) && (
                    <>
                        <div className="min-h-0 h-[600px]">
                            <Calendar
                                localizer={localizer}
                                events={calendarEvents}
                                defaultView="month"
                                view={view}
                                onView={setView}
                                views={['month', 'week']}
                                culture="da"
                                selectable={isAdmin}
                                eventPropGetter={eventPropGetter}
                                onSelectSlot={isAdmin ? handleSelectSlot : undefined}
                                onSelectEvent={(e) => router.visit(showCourse({ course: Number(e.id) }).url)}
                                style={{ height: '100%' }}
                                messages={{
                                    next: '›',
                                    previous: '‹',
                                    today: 'I dag',
                                    month: 'Måned',
                                    week: 'Uge',
                                    noEventsInRange: 'Ingen kurser.',
                                }}
                            />
                        </div>

                        {isAdmin && <Sheet open={sheetOpen} onOpenChange={setSheetOpen}>
                            <SheetContent>
                                <SheetHeader>
                                    <SheetTitle>Opret kursus</SheetTitle>
                                </SheetHeader>
                                <div className="mt-6 space-y-4 px-1">
                                    <div className="space-y-1.5">
                                        <Label htmlFor="offer">Tilbud</Label>
                                        <Select value={selectedOfferId} onValueChange={setSelectedOfferId}>
                                            <SelectTrigger id="offer">
                                                <SelectValue placeholder="Vælg tilbud" />
                                            </SelectTrigger>
                                            <SelectContent>
                                                {offers.map((o) => (
                                                    <SelectItem key={o.id} value={String(o.id)}>
                                                        {o.name}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                        <InputError message={createErrors.offer_id} />
                                    </div>
                                    <div className="space-y-1.5">
                                        <Label htmlFor="start_at">Start</Label>
                                        <Input
                                            id="start_at"
                                            type="datetime-local"
                                            value={slotStart}
                                            onChange={(e) => setSlotStart(e.target.value)}
                                        />
                                        <InputError message={createErrors.start_at} />
                                    </div>
                                    <div className="space-y-1.5">
                                        <Label htmlFor="end_at">Slut</Label>
                                        <Input
                                            id="end_at"
                                            type="datetime-local"
                                            value={slotEnd}
                                            onChange={(e) => setSlotEnd(e.target.value)}
                                        />
                                        <InputError message={createErrors.end_at} />
                                    </div>
                                    <Button
                                        className="w-full"
                                        onClick={handleCreateCourse}
                                        disabled={creating || !selectedOfferId}
                                    >
                                        {creating ? 'Opretter...' : 'Opret kursus'}
                                    </Button>
                                </div>
                            </SheetContent>
                        </Sheet>
                    </>
                )}
            </div>
        </AppLayout>
    );
}
