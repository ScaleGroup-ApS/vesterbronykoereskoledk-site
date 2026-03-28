import { Head, Link, useForm } from '@inertiajs/react';
import { format } from 'date-fns';
import { da } from 'date-fns/locale';
import { Plus } from 'lucide-react';
import { show, store } from '@/routes/courses';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';

type OfferOption = { id: number; name: string };

type CourseRow = {
    id: number;
    start_at: string;
    end_at: string;
    offer: { id: number; name: string };
    enrollments_count: number;
    max_students: number | null;
    featured_on_home: boolean;
    public_spots_remaining: number | null;
};

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Kurser', href: '#' }];

export default function CoursesIndex({ courses, offers }: { courses: CourseRow[]; offers: OfferOption[] }) {
    const createForm = useForm({
        offer_id: '',
        start_at: '',
        max_students: '',
        public_spots_remaining: '',
        featured_on_home: false,
    });

    function submitCreate(e: React.FormEvent) {
        e.preventDefault();
        createForm.post(store.url(), {
            preserveScroll: true,
            onSuccess: () => createForm.reset(),
        });
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Kurser" />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <Heading title="Kurser" />

                {offers.length > 0 ? (
                    <div className="max-w-2xl rounded-xl border p-6">
                        <h2 className="mb-4 text-base font-semibold">Opret kursusdato</h2>
                        <form onSubmit={submitCreate} className="space-y-4">
                            <div className="grid gap-2">
                                <Label htmlFor="new_offer_id">Lovpakke (tilbud)</Label>
                                <Select
                                    value={createForm.data.offer_id || undefined}
                                    onValueChange={(v) => createForm.setData('offer_id', v)}
                                    required
                                >
                                    <SelectTrigger id="new_offer_id" className="w-full bg-background">
                                        <SelectValue placeholder="Vælg lovpakke…" />
                                    </SelectTrigger>
                                    <SelectContent position="popper" className="z-[200]">
                                        {offers.map((o) => (
                                            <SelectItem key={o.id} value={String(o.id)}>
                                                {o.name}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="grid max-w-md gap-2">
                                <Label htmlFor="new_start_at">Start (dato og tid)</Label>
                                <Input
                                    id="new_start_at"
                                    type="datetime-local"
                                    value={createForm.data.start_at}
                                    onChange={(e) => createForm.setData('start_at', e.target.value)}
                                    required
                                    min={new Date().toISOString().slice(0, 16)}
                                />
                                <p className="text-xs text-muted-foreground">
                                    Sluttid beregnes automatisk ud fra standard kursuslængde (konfiguration).
                                </p>
                            </div>
                            <div className="grid max-w-[200px] gap-2">
                                <Label htmlFor="new_max_students">Maks. elever (valgfrit)</Label>
                                <Input
                                    id="new_max_students"
                                    type="number"
                                    min="1"
                                    value={createForm.data.max_students}
                                    onChange={(e) => createForm.setData('max_students', e.target.value)}
                                    placeholder="—"
                                />
                            </div>
                            <div className="grid max-w-[200px] gap-2">
                                <Label htmlFor="new_public_spots">Pladser tilbage på websitet (valgfrit)</Label>
                                <Input
                                    id="new_public_spots"
                                    type="number"
                                    min="0"
                                    value={createForm.data.public_spots_remaining}
                                    onChange={(e) => createForm.setData('public_spots_remaining', e.target.value)}
                                    placeholder="Vises på forsiden"
                                />
                                <p className="text-xs text-muted-foreground">
                                    Tallet vises ved nedtælling til holdstart. Tom = vis ikke antal.
                                </p>
                            </div>
                            <div className="flex items-start gap-2">
                                <Checkbox
                                    id="new_featured"
                                    checked={createForm.data.featured_on_home}
                                    onCheckedChange={(v) => createForm.setData('featured_on_home', v === true)}
                                    className="mt-0.5"
                                />
                                <Label htmlFor="new_featured" className="text-sm font-normal leading-snug">
                                    Brug denne dato til nedtælling på forsiden (kun ét kursus ad gangen)
                                </Label>
                            </div>
                            <Button type="submit" size="sm" disabled={createForm.processing}>
                                <Plus className="mr-1 size-4" />
                                Opret kursus
                            </Button>
                        </form>
                    </div>
                ) : (
                    <p className="text-sm text-muted-foreground">
                        Opret et <strong>primært tilbud</strong> (lovpakke) under Tilbud for at tilføje kursusdatoer.
                    </p>
                )}

                {courses.length === 0 ? (
                    <div className="rounded-xl border px-4 py-10 text-center text-sm text-muted-foreground">
                        Ingen kommende kurser.
                    </div>
                ) : (
                    <div className="rounded-xl border">
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b text-left text-muted-foreground">
                                    <th className="px-4 py-3 font-medium">Dato</th>
                                    <th className="px-4 py-3 font-medium">Tilbud</th>
                                    <th className="px-4 py-3 font-medium">Tilmeldte</th>
                                    <th className="px-4 py-3 font-medium">Forside</th>
                                    <th className="px-4 py-3 font-medium">Pladser (web)</th>
                                    <th className="px-4 py-3" />
                                </tr>
                            </thead>
                            <tbody>
                                {courses.map((course) => (
                                    <tr key={course.id} className="border-b last:border-0">
                                        <td className="px-4 py-3">
                                            {format(new Date(course.start_at), 'PPP', { locale: da })}
                                        </td>
                                        <td className="px-4 py-3">{course.offer.name}</td>
                                        <td className="px-4 py-3">
                                            {course.enrollments_count}
                                            {course.max_students != null && ` / ${course.max_students}`}
                                        </td>
                                        <td className="px-4 py-3">{course.featured_on_home ? 'Ja' : '—'}</td>
                                        <td className="px-4 py-3">
                                            {course.public_spots_remaining != null ? course.public_spots_remaining : '—'}
                                        </td>
                                        <td className="px-4 py-3 text-right">
                                            <Link
                                                href={show({ course: course.id }).url}
                                                className="text-primary hover:underline"
                                            >
                                                Se kursus
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
