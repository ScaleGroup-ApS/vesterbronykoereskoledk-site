import { Head, Link, router, useForm, Form } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import CourseAttendanceController from '@/actions/App/Http/Controllers/Courses/CourseAttendanceController';
import { approve } from '@/actions/App/Http/Controllers/Enrollment/EnrollmentApprovalController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { destroy, update } from '@/routes/courses';
import { index } from '@/routes/courses';
import type { BreadcrumbItem } from '@/types';

type Enrollment = {
    id: number;
    status: string;
    payment_method: string;
    attended: boolean | null;
    student: { id: number; name: string; email: string };
    attended_count: number;
    total_bookings: number;
};

type SessionAttendance = {
    booking_id: number;
    student_id: number;
    attended: boolean | null;
};

type CourseSessionRow = {
    id: number;
    session_number: number;
    starts_at: string;
    ends_at: string;
    is_cancelled: boolean;
    is_past: boolean;
    attendance: SessionAttendance[];
};

type CourseDetail = {
    id: number;
    start_at: string;
    end_at: string;
    max_students: number | null;
    featured_on_home: boolean;
    public_spots_remaining: number | null;
    offer: { id: number; name: string };
    enrollments: Enrollment[];
    sessions: CourseSessionRow[];
};

export default function CourseShow({ course }: { course: CourseDetail }) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Kurser', href: index().url },
        { title: course.offer.name, href: '#' },
    ];

    const form = useForm({
        start_at: new Date(course.start_at).toISOString().slice(0, 16),
        max_students: course.max_students ? String(course.max_students) : '',
        public_spots_remaining:
            course.public_spots_remaining != null ? String(course.public_spots_remaining) : '',
        featured_on_home: course.featured_on_home,
    });

    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        form.submit(update(course));
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${course.offer.name} – kursus`} />

            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <div className="flex items-center gap-3">
                    <Link href={index().url} className="text-muted-foreground hover:text-foreground">
                        <ArrowLeft className="size-4" />
                    </Link>
                    <Heading title={course.offer.name} />
                </div>

                <div className="max-w-lg">
                    <h2 className="mb-4 text-base font-semibold">Kursusdato</h2>
                    <form onSubmit={handleSubmit} className="space-y-4">
                        <div className="grid max-w-md gap-2">
                            <Label htmlFor="start_at">Start (dato og tid)</Label>
                            <Input
                                id="start_at"
                                type="datetime-local"
                                value={form.data.start_at}
                                onChange={(e) => form.setData('start_at', e.target.value)}
                                required
                            />
                            <p className="text-xs text-muted-foreground">
                                Sluttid beregnes automatisk ud fra standard kursuslængde (konfiguration).
                            </p>
                            <InputError message={form.errors.start_at} />
                        </div>
                        <div className="grid max-w-[160px] gap-2">
                            <Label htmlFor="max_students">Maks. elever</Label>
                            <Input
                                id="max_students"
                                type="number"
                                min="1"
                                value={form.data.max_students}
                                onChange={(e) => form.setData('max_students', e.target.value)}
                                placeholder="Ingen grænse"
                            />
                            <InputError message={form.errors.max_students} />
                        </div>
                        <div className="grid max-w-[200px] gap-2">
                            <Label htmlFor="public_spots_remaining">Pladser tilbage på websitet</Label>
                            <Input
                                id="public_spots_remaining"
                                type="number"
                                min="0"
                                value={form.data.public_spots_remaining}
                                onChange={(e) => form.setData('public_spots_remaining', e.target.value)}
                                placeholder="Tom = vis ikke"
                            />
                            <p className="text-xs text-muted-foreground">
                                Vises ved nedtælling på forsiden sammen med holdstart.
                            </p>
                            <InputError message={form.errors.public_spots_remaining} />
                        </div>
                        <div className="flex items-start gap-2">
                            <Checkbox
                                id="featured_on_home"
                                checked={form.data.featured_on_home}
                                onCheckedChange={(v) => form.setData('featured_on_home', v === true)}
                                className="mt-0.5"
                            />
                            <Label htmlFor="featured_on_home" className="text-sm font-normal leading-snug">
                                Brug denne dato til nedtælling på forsiden (kun ét kursus ad gangen)
                            </Label>
                        </div>
                        <Button type="submit" disabled={form.processing}>
                            Gem ændringer
                        </Button>
                    </form>
                    <Form
                        {...destroy.form({ course })}
                        method="post"
                        onBefore={() => confirm('Er du sikker på, at du vil slette dette kursus?')}
                        className="mt-4"
                    >
                        {({ processing }) => (
                            <Button type="submit" variant="destructive" disabled={processing}>
                                Slet kursus
                            </Button>
                        )}
                    </Form>
                </div>

                <div className="max-w-2xl">
                    <h2 className="mb-4 text-base font-semibold">
                        Tilmeldte ({course.enrollments.length}
                        {course.max_students ? ` / ${course.max_students}` : ''})
                    </h2>

                    {course.enrollments.length === 0 ? (
                        <p className="text-sm text-muted-foreground">Ingen tilmeldte endnu.</p>
                    ) : (
                        <div className="rounded-md border">
                            <table className="w-full text-sm">
                                <thead>
                                    <tr className="border-b bg-muted/50">
                                        <th className="px-4 py-2 text-left font-medium">Navn</th>
                                        <th className="px-4 py-2 text-left font-medium">E-mail</th>
                                        <th className="px-4 py-2 text-left font-medium">Betaling</th>
                                        <th className="px-4 py-2 text-left font-medium">Status</th>
                                        <th className="px-4 py-2 text-left font-medium">Fremmøde</th>
                                        <th className="px-4 py-2" />
                                    </tr>
                                </thead>
                                <tbody>
                                    {course.enrollments.map((enrollment) => (
                                        <tr key={enrollment.id} className="border-b last:border-0">
                                            <td className="px-4 py-2">{enrollment.student.name}</td>
                                            <td className="px-4 py-2 text-muted-foreground">{enrollment.student.email}</td>
                                            <td className="px-4 py-2">
                                                {enrollment.payment_method === 'stripe' ? 'Kortbetaling' : 'Kontant'}
                                            </td>
                                            <td className="px-4 py-2">
                                                {enrollment.status === 'pending_approval' && 'Afventer godkendelse'}
                                                {enrollment.status === 'pending_payment' && 'Afventer betaling'}
                                                {enrollment.status === 'completed' && 'Godkendt'}
                                                {enrollment.status === 'rejected' && 'Afvist'}
                                            </td>
                                            <td className="px-4 py-2">
                                                <Checkbox
                                                    checked={enrollment.attended === true}
                                                    onCheckedChange={() =>
                                                        router.patch(
                                                            CourseAttendanceController({ course: course.id, enrollment: enrollment.id }).url,
                                                            {},
                                                            { preserveScroll: true },
                                                        )
                                                    }
                                                    aria-label={`Fremmøde for ${enrollment.student.name}`}
                                                />
                                            </td>
                                            <td className="px-4 py-2 text-right">
                                                {enrollment.status === 'pending_approval' && (
                                                    <Form {...approve.form({ enrollment })}>
                                                        {({ processing }) => (
                                                            <Button type="submit" size="sm" disabled={processing}>
                                                                Godkend
                                                            </Button>
                                                        )}
                                                    </Form>
                                                )}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>

                {course.sessions.length > 0 && (
                    <div className="max-w-2xl">
                        <h2 className="mb-4 text-base font-semibold">
                            Teoritimer ({course.sessions.filter((s) => !s.is_cancelled).length})
                        </h2>
                        <div className="rounded-md border">
                            <table className="w-full text-sm">
                                <thead>
                                    <tr className="border-b bg-muted/50">
                                        <th className="px-4 py-2 text-left font-medium">#</th>
                                        <th className="px-4 py-2 text-left font-medium">Dato</th>
                                        <th className="px-4 py-2 text-left font-medium">Tid</th>
                                        <th className="px-4 py-2 text-left font-medium">Fremmøde</th>
                                        <th className="px-4 py-2" />
                                    </tr>
                                </thead>
                                <tbody>
                                    {course.sessions.map((session) => {
                                        const start = new Date(session.starts_at);
                                        const end = new Date(session.ends_at);
                                        const presentCount = session.attendance.filter((a) => a.attended === true).length;
                                        const totalCount = session.attendance.length;

                                        return (
                                            <tr
                                                key={session.id}
                                                className={`border-b last:border-0 ${session.is_cancelled ? 'opacity-40 line-through' : ''}`}
                                            >
                                                <td className="px-4 py-2 font-medium">Teori {session.session_number}</td>
                                                <td className="px-4 py-2">
                                                    {start.toLocaleDateString('da-DK', { day: 'numeric', month: 'short', year: 'numeric' })}
                                                </td>
                                                <td className="px-4 py-2">
                                                    {start.toLocaleTimeString('da-DK', { hour: '2-digit', minute: '2-digit' })}–
                                                    {end.toLocaleTimeString('da-DK', { hour: '2-digit', minute: '2-digit' })}
                                                </td>
                                                <td className="px-4 py-2">
                                                    {session.is_cancelled
                                                        ? 'Aflyst'
                                                        : totalCount > 0
                                                            ? `${presentCount}/${totalCount}`
                                                            : '—'}
                                                </td>
                                                <td className="px-4 py-2 text-right">
                                                    {!session.is_cancelled && (
                                                        <div className="flex items-center justify-end gap-2">
                                                            <Button
                                                                size="sm"
                                                                variant="outline"
                                                                onClick={() => {
                                                                    const allStudentIds = session.attendance.map((a) => a.student_id);
                                                                    router.patch(
                                                                        `/courses/${course.id}/sessions/${session.id}/attendance`,
                                                                        { present_student_ids: allStudentIds },
                                                                        { preserveScroll: true },
                                                                    );
                                                                }}
                                                            >
                                                                Alle til stede
                                                            </Button>
                                                            <Button
                                                                size="sm"
                                                                variant="destructive"
                                                                onClick={() => {
                                                                    if (confirm('Aflys denne teoritime?')) {
                                                                        router.post(
                                                                            `/courses/${course.id}/sessions/${session.id}/cancel`,
                                                                            {},
                                                                            { preserveScroll: true },
                                                                        );
                                                                    }
                                                                }}
                                                            >
                                                                Aflys
                                                            </Button>
                                                        </div>
                                                    )}
                                                </td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </table>
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
