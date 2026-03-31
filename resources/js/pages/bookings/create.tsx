import { Head, router, useForm } from '@inertiajs/react';
import { useEffect } from 'react';
import { store } from '@/actions/App/Http/Controllers/Bookings/BookingController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { StudentLessonProgress } from '@/components/student/student-lesson-progress';
import type { LessonProgressRow } from '@/components/student/student-lesson-progress';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { create, index } from '@/routes/bookings';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Bookinger', href: index().url },
    { title: 'Opret booking', href: '#' },
];

type Student = { id: number; user: { name: string } };
type UserModel = { id: number; name: string };
type Vehicle = { id: number; name: string };
type BookingTypeOption = { value: string; label: string };

function bookingCreatePath(studentId: string): string {
    if (!studentId) {
        return create().url;
    }

    return `${create().url}?${new URLSearchParams({ student_id: studentId }).toString()}`;
}

export default function BookingCreate({
    students,
    instructors,
    vehicles,
    bookingTypes,
    selectedStudentId,
    studentLessonProgress,
}: {
    students: Student[];
    instructors: UserModel[];
    vehicles: Vehicle[];
    bookingTypes: BookingTypeOption[];
    selectedStudentId: number | null;
    studentLessonProgress: LessonProgressRow[] | null;
}) {
    const form = useForm({
        student_id: selectedStudentId ? String(selectedStudentId) : '',
        instructor_id: '',
        vehicle_id: '',
        type: 'driving_lesson',
        starts_at: '',
        ends_at: '',
        notes: '',
    });

    useEffect(() => {
        form.setData('student_id', selectedStudentId ? String(selectedStudentId) : '');
    }, [selectedStudentId]);

    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        form.submit(store());
    }

    function handleStudentChange(value: string) {
        form.setData('student_id', value);
        router.get(
            bookingCreatePath(value),
            {},
            { preserveState: true, replace: true, only: ['selectedStudentId', 'studentLessonProgress'] },
        );
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Opret booking" />

            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <Heading title="Opret booking" />

                <div className="max-w-3xl space-y-6">
                    <form onSubmit={handleSubmit} className="max-w-lg space-y-6">
                        <div className="grid gap-2">
                            <Label htmlFor="student_id">Elev</Label>
                            <select
                                id="student_id"
                                value={form.data.student_id}
                                onChange={(e) => handleStudentChange(e.target.value)}
                                className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm"
                                required
                            >
                                <option value="">Vælg elev...</option>
                                {students.map((s) => (
                                    <option key={s.id} value={s.id}>
                                        {s.user.name}
                                    </option>
                                ))}
                            </select>
                            <InputError message={form.errors.student_id} />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="instructor_id">Instruktør</Label>
                            <select
                                id="instructor_id"
                                value={form.data.instructor_id}
                                onChange={(e) => form.setData('instructor_id', e.target.value)}
                                className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm"
                                required
                            >
                                <option value="">Vælg instruktør...</option>
                                {instructors.map((i) => (
                                    <option key={i.id} value={i.id}>
                                        {i.name}
                                    </option>
                                ))}
                            </select>
                            <InputError message={form.errors.instructor_id} />
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="vehicle_id">Køretøj</Label>
                            <select
                                id="vehicle_id"
                                value={form.data.vehicle_id}
                                onChange={(e) => form.setData('vehicle_id', e.target.value)}
                                className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm"
                            >
                                <option value="">Intet køretøj</option>
                                {vehicles.map((v) => (
                                    <option key={v.id} value={v.id}>
                                        {v.name}
                                    </option>
                                ))}
                            </select>
                        </div>

                        <div className="grid gap-2">
                            <Label htmlFor="type">Type</Label>
                            <select
                                id="type"
                                value={form.data.type}
                                onChange={(e) => form.setData('type', e.target.value)}
                                className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm"
                            >
                                {bookingTypes.map((t) => (
                                    <option key={t.value} value={t.value}>
                                        {t.label}
                                    </option>
                                ))}
                            </select>
                            <InputError message={form.errors.type} />
                        </div>

                        <div className="grid gap-4 sm:grid-cols-2">
                            <div className="grid gap-2">
                                <Label htmlFor="starts_at">Start</Label>
                                <Input
                                    id="starts_at"
                                    type="datetime-local"
                                    value={form.data.starts_at}
                                    onChange={(e) => form.setData('starts_at', e.target.value)}
                                    required
                                />
                                <InputError message={form.errors.starts_at} />
                            </div>
                            <div className="grid gap-2">
                                <Label htmlFor="ends_at">Slut</Label>
                                <Input
                                    id="ends_at"
                                    type="datetime-local"
                                    value={form.data.ends_at}
                                    onChange={(e) => form.setData('ends_at', e.target.value)}
                                    required
                                />
                                <InputError message={form.errors.ends_at} />
                            </div>
                        </div>

                        <InputError message={form.errors.conflicts} />

                        <Button disabled={form.processing}>Opret booking</Button>
                    </form>

                    {studentLessonProgress !== null ? (
                        <div className="space-y-2">
                            <p className="text-sm font-medium">Elevens pakkeforløb</p>
                            <StudentLessonProgress
                                rows={studentLessonProgress}
                                variant="full"
                                showFooterNote={false}
                            />
                        </div>
                    ) : null}
                </div>
            </div>
        </AppLayout>
    );
}
