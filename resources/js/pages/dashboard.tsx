import type { EventClickArg } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import type { DateClickArg } from '@fullcalendar/interaction';
import interactionPlugin from '@fullcalendar/interaction';
import FullCalendar from '@fullcalendar/react';
import { Form, Head, Link, router, useForm } from '@inertiajs/react';
import { CalendarDays, CheckCircle2, TrendingDown, Users, Wallet, XCircle } from 'lucide-react';
import { useState } from 'react';
import { approve, reject } from '@/actions/App/Http/Controllers/Enrollment/EnrollmentApprovalController';
import { CourseHoldCapacityChart } from '@/components/dashboard/course-hold-capacity-chart';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes';
import { show as showCourse, store as storeCourse } from '@/routes/courses';
import { index as enrollmentsIndex } from '@/routes/enrollments';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Dashboard', href: dashboard().url }];

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

type Enrollment = {
    id: number;
    student_name: string;
    student_email: string;
    offer_name: string;
    payment_method: 'stripe' | 'cash';
    status: 'pending_payment' | 'pending_approval';
    created_at: string;
};

const methodLabels: Record<string, string> = {
    stripe: 'Kortbetaling',
    cash: 'Kontant',
};

const statusLabels: Record<string, string> = {
    pending_payment: 'Afventer betaling',
    pending_approval: 'Afventer godkendelse',
};

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

type Offer = { id: number; name: string };
type CourseEvent = {
    id: number;
    title: string;
    start: string;
    end: string;
    max_students: number | null;
    public_spots_remaining: number | null;
    enrollments_completed_count: number;
    enrollments_pending_count: number;
};

export default function Dashboard({
    kpis,
    courses = [],
    enrollments = [],
    offers = [],
}: {
    kpis: Kpis;
    courses?: CourseEvent[];
    enrollments: Enrollment[];
    offers?: Offer[];
}) {
    const isAdmin = 'total_students' in kpis;
    const isInstructor = 'upcoming_bookings' in kpis && !isAdmin;

    const [enrollmentDialogOpen, setEnrollmentDialogOpen] = useState(false);
    const [rejectTarget, setRejectTarget] = useState<Enrollment | null>(null);
    const [courseDialogDate, setCourseDialogDate] = useState<string | null>(null);

    const courseForm = useForm({ offer_id: '', start_at: '', max_students: '' });

    const events = courses.map((course) => ({
        id: String(course.id),
        title: course.title,
        start: course.start,
        end: course.end,
        color: 'var(--color-primary)',
    }));

    function handleEventClick(info: EventClickArg) {
        const id = info.event.id ? Number(info.event.id) : null;
        if (id) { router.visit(showCourse(id).url); }
    }

    function handleDateClick(info: DateClickArg) {
        courseForm.setData({
            offer_id: '',
            start_at: `${info.dateStr}T09:00`,
            max_students: '',
        });
        setCourseDialogDate(info.dateStr);
    }

    function handleCourseSubmit(e: React.FormEvent) {
        e.preventDefault();
        if (!courseForm.data.offer_id) {
            return;
        }
        courseForm.post(storeCourse.url(), {
            onSuccess: () => setCourseDialogDate(null),
        });
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
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
                    {!isAdmin && !isInstructor && (
                        <div className="col-span-full rounded-xl border px-4 py-6 text-center text-sm text-muted-foreground">
                            Ingen KPI-data tilgængelig.
                        </div>
                    )}
                </div>

                {isAdmin && (
                    <div className="rounded-xl border p-5">
                        <Heading
                            title="Hold & kapacitet"
                            description="Kommende hold med godkendte tilmeldinger, afventende og ledige pladser."
                        />
                        <div className="mt-5">
                            <CourseHoldCapacityChart courses={courses} />
                        </div>
                    </div>
                )}

                {(isAdmin || isInstructor) && (
                    <>
                        <div className="flex items-center justify-between">
                            <Heading title="Kurser" description="Klik på en dag for at oprette et kursus" />
                            <Button variant="outline" asChild>
                                <Link href={enrollmentsIndex().url}>
                                    Afventende tilmeldinger ({enrollments.length})
                                </Link>
                            </Button>
                        </div>

                        <div className="rounded-xl border p-4">
                            <FullCalendar
                                plugins={[dayGridPlugin, interactionPlugin]}
                                initialView="dayGridMonth"
                                headerToolbar={{
                                    left: 'prev,next today',
                                    center: 'title',
                                    right: 'dayGridMonth',
                                }}
                                locale="da"
                                firstDay={1}
                                height="auto"
                                events={events}
                                eventClick={handleEventClick}
                                dateClick={handleDateClick}
                            />
                        </div>
                    </>
                )}
            </div>

            <Dialog open={courseDialogDate !== null} onOpenChange={(open) => !open && setCourseDialogDate(null)}>
                    <DialogContent>
                        <DialogHeader>
                            <DialogTitle>Opret kursusdato</DialogTitle>
                            <DialogDescription>
                                Tilføj et nyt kursus til et tilbud, så elever kan tilmelde sig.
                            </DialogDescription>
                        </DialogHeader>
                        <form onSubmit={handleCourseSubmit} className="grid gap-4">
                            <div className="grid gap-2">
                                <Label htmlFor="course_offer">Lovpakke (tilbud)</Label>
                                <Select
                                    value={courseForm.data.offer_id || undefined}
                                    onValueChange={(v) => courseForm.setData('offer_id', v)}
                                >
                                    <SelectTrigger id="course_offer" className="bg-background">
                                        <SelectValue placeholder="Vælg lovpakke…" />
                                    </SelectTrigger>
                                    <SelectContent position="popper" className="z-[200]">
                                        {offers.map((offer) => (
                                            <SelectItem key={offer.id} value={String(offer.id)}>
                                                {offer.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="course_start_at">Start (dato og tid)</Label>
                                <Input
                                    id="course_start_at"
                                    type="datetime-local"
                                    value={courseForm.data.start_at}
                                    onChange={(e) => courseForm.setData('start_at', e.target.value)}
                                    required
                                />
                                <p className="text-xs text-muted-foreground">
                                    Sluttid sættes automatisk efter standard kursuslængde.
                                </p>
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="course_max_students">
                                    Maks. elever{' '}
                                    <span className="font-normal text-muted-foreground">(valgfrit)</span>
                                </Label>
                                <Input
                                    id="course_max_students"
                                    type="number"
                                    min={1}
                                    value={courseForm.data.max_students}
                                    onChange={(e) => courseForm.setData('max_students', e.target.value)}
                                    placeholder="Ubegrænset"
                                />
                            </div>
                            <DialogFooter>
                                <Button type="button" variant="outline" onClick={() => setCourseDialogDate(null)}>
                                    Annuller
                                </Button>
                                <Button
                                    type="submit"
                                    disabled={courseForm.processing || !courseForm.data.offer_id}
                                    className="gap-1.5"
                                >
                                    {courseForm.processing && <Spinner />}
                                    Opret kursus
                                </Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
            </Dialog>

            {isAdmin && (
                <>
                    <Dialog open={enrollmentDialogOpen} onOpenChange={setEnrollmentDialogOpen}>
                        <DialogContent className="sm:max-w-2xl">
                            <DialogHeader>
                                <DialogTitle>Afventende tilmeldinger</DialogTitle>
                                <DialogDescription>
                                    Tilmeldinger der kræver godkendelse eller afventer betaling.
                                </DialogDescription>
                            </DialogHeader>

                            <div className="max-h-[60vh] overflow-y-auto">
                                <div className="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    {enrollments.map((enrollment) => (
                                        <div key={enrollment.id} className="flex flex-col gap-3 rounded-xl border p-4">
                                            <div>
                                                <p className="font-medium">{enrollment.student_name}</p>
                                                <p className="text-xs text-muted-foreground">{enrollment.student_email}</p>
                                            </div>
                                            <div>
                                                <p className="text-sm">{enrollment.offer_name}</p>
                                                <div className="mt-1.5 flex flex-wrap gap-1.5">
                                                    <Badge variant="outline">
                                                        {methodLabels[enrollment.payment_method] ?? enrollment.payment_method}
                                                    </Badge>
                                                    <Badge variant="secondary">
                                                        {statusLabels[enrollment.status] ?? enrollment.status}
                                                    </Badge>
                                                </div>
                                            </div>
                                            <p className="text-xs text-muted-foreground">
                                                {new Date(enrollment.created_at).toLocaleDateString('da-DK')}
                                            </p>
                                            {enrollment.status === 'pending_approval' && (
                                                <div className="flex gap-2">
                                                    <Form {...approve.form(enrollment.id)}>
                                                        {({ processing }) => (
                                                            <Button
                                                                type="submit"
                                                                size="sm"
                                                                variant="outline"
                                                                disabled={processing}
                                                                className="flex-1 gap-1.5"
                                                            >
                                                                {processing ? <Spinner /> : <CheckCircle2 className="size-4" />}
                                                                Godkend
                                                            </Button>
                                                        )}
                                                    </Form>
                                                    <Button
                                                        size="sm"
                                                        variant="outline"
                                                        className="flex-1 gap-1.5 text-destructive hover:text-destructive"
                                                        onClick={() => setRejectTarget(enrollment)}
                                                    >
                                                        <XCircle className="size-4" />
                                                        Afvis
                                                    </Button>
                                                </div>
                                            )}
                                        </div>
                                    ))}
                                    {enrollments.length === 0 && (
                                        <p className="col-span-full py-8 text-center text-sm text-muted-foreground">
                                            Ingen afventende tilmeldinger.
                                        </p>
                                    )}
                                </div>
                            </div>
                        </DialogContent>
                    </Dialog>

                    <Dialog open={rejectTarget !== null} onOpenChange={(open) => !open && setRejectTarget(null)}>
                        <DialogContent>
                            <DialogHeader>
                                <DialogTitle>Afvis tilmelding</DialogTitle>
                                <DialogDescription>
                                    Angiv årsagen til afvisningen af {rejectTarget?.student_name}s tilmelding til {rejectTarget?.offer_name}.
                                </DialogDescription>
                            </DialogHeader>

                            {rejectTarget && (
                                <Form {...reject.form(rejectTarget.id)}>
                                    {({ processing }) => (
                                        <>
                                            <div className="grid gap-2">
                                                <Label htmlFor="rejection_reason">Årsag til afvisning</Label>
                                                <textarea
                                                    id="rejection_reason"
                                                    name="rejection_reason"
                                                    rows={4}
                                                    required
                                                    className="border-input placeholder:text-muted-foreground focus-visible:border-ring focus-visible:ring-ring/50 aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive flex min-h-[60px] w-full rounded-md border bg-transparent px-3 py-2 text-base shadow-xs transition-[color,box-shadow] outline-none focus-visible:ring-[3px] disabled:cursor-not-allowed disabled:opacity-50 md:text-sm"
                                                    placeholder="F.eks. pladser er fuldt booket i den ønskede periode..."
                                                />
                                            </div>

                                            <DialogFooter>
                                                <Button
                                                    type="button"
                                                    variant="outline"
                                                    onClick={() => setRejectTarget(null)}
                                                >
                                                    Annuller
                                                </Button>
                                                <Button
                                                    type="submit"
                                                    variant="destructive"
                                                    disabled={processing}
                                                    className="gap-1.5"
                                                >
                                                    {processing && <Spinner />}
                                                    Afvis tilmelding
                                                </Button>
                                            </DialogFooter>
                                        </>
                                    )}
                                </Form>
                            )}
                        </DialogContent>
                    </Dialog>
                </>
            )}
        </AppLayout>
    );
}
