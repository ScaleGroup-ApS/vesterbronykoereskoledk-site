import { Head, Link, router, useForm } from '@inertiajs/react';
import { FileText, Pencil, Trash2, Upload } from 'lucide-react';
import StudentSkillController from '@/actions/App/Http/Controllers/Students/StudentSkillController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { StudentJourneyRoadmap, type JourneyStep, type UpcomingBookingRow } from '@/components/student/student-journey-roadmap';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useLoginLink } from '@/hooks/use-login-link';
import AppLayout from '@/layouts/app-layout';
import { index, show, edit, destroy } from '@/routes/students';
import { show as progressionShow } from '@/routes/students/progression';
import type { BreadcrumbItem, Student } from '@/types';

type PastBookingRow = {
    id: number;
    type_label: string;
    range_label: string;
    status: string;
    attended: boolean | null;
    instructor_note: string | null;
    driving_skills: string[];
};

const skillLabels: Record<string, string> = {
    parking: 'Parkering',
    motorvej: 'Motorvej',
    roundabouts: 'Rundkørsel',
    city_driving: 'Bykørsel',
    overtaking: 'Overhaling',
    reversing: 'Bakring',
    lane_change: 'Filskifte',
    emergency_stop: 'Nødstop',
};

type MediaItem = {
    id: number;
    file_name: string;
    collection_name: string;
    size: number;
    created_at: string;
};

type EventTimelineEntry = {
    id: string;
    summary: string;
    category: 'booking' | 'enrollment' | 'student' | 'payment' | 'other';
    created_at: string;
};

const categoryDotColors: Record<string, string> = {
    booking: 'bg-blue-500',
    enrollment: 'bg-purple-500',
    student: 'bg-green-500',
    payment: 'bg-amber-500',
    other: 'bg-muted-foreground',
};

const statusLabels: Record<string, string> = {
    active: 'Aktiv',
    inactive: 'Inaktiv',
    graduated: 'Udlært',
    dropped_out: 'Frafaldet',
};

const statusVariants: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
    active: 'default',
    inactive: 'secondary',
    graduated: 'outline',
    dropped_out: 'destructive',
};

type Readiness = {
    is_ready: boolean;
    completed: Record<string, number>;
    required: Record<string, number>;
    missing: Record<string, number>;
};

type JourneyPayload = {
    steps: JourneyStep[];
    upcoming_bookings: UpcomingBookingRow[];
};

export default function StudentShow({
    student,
    canEdit,
    readiness,
    journey,
    eventTimeline = [],
    pastBookings,
}: {
    student: Student & { media: MediaItem[] };
    canEdit: boolean;
    readiness: Readiness;
    journey: JourneyPayload;
    eventTimeline: EventTimelineEntry[];
    pastBookings?: PastBookingRow[];
}) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Elever', href: index().url },
        { title: student.user.name, href: show(student).url },
    ];

    const { sendLoginLink, processing: loginLinkProcessing } = useLoginLink(student);

    const uploadForm = useForm<{ file: File | null; collection: string }>({
        file: null,
        collection: 'documents',
    });

    function handleDelete() {
        if (confirm('Er du sikker på, at du vil slette denne elev?')) {
            router.delete(destroy(student).url);
        }
    }

    function handleUpload(e: React.FormEvent) {
        e.preventDefault();
        if (!uploadForm.data.file) {
            return;
        }

        uploadForm.post(`/students/${student.id}/media`, {
            forceFormData: true,
            preserveScroll: true,
            onSuccess: () => uploadForm.reset(),
        });
    }

    function handleMediaDelete(mediaId: number) {
        if (confirm('Er du sikker på, at du vil slette denne fil?')) {
            router.delete(`/students/${student.id}/media/${mediaId}`, {
                preserveScroll: true,
            });
        }
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={student.user.name} />

            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <Heading title={student.user.name} />
                    {canEdit && (
                        <div className="flex gap-2">
                            <Button variant="outline" type="button" onClick={sendLoginLink} disabled={loginLinkProcessing}>
                                Send login link
                            </Button>
                            <Button variant="outline" asChild>
                                <Link href={edit(student).url}>
                                    <Pencil className="mr-2 size-4" />
                                    Rediger
                                </Link>
                            </Button>
                            <Button variant="destructive" onClick={handleDelete}>
                                <Trash2 className="mr-2 size-4" />
                                Slet
                            </Button>
                        </div>
                    )}
                </div>

                <div className="max-w-2xl space-y-3 rounded-xl border p-4">
                    <div className="flex flex-wrap items-center justify-between gap-2">
                        <Heading variant="small" title="Forløb & krav" />
                        <Button variant="outline" size="sm" asChild>
                            <Link href={progressionShow(student).url}>Detaljeret fremgang</Link>
                        </Button>
                    </div>
                    <StudentJourneyRoadmap steps={journey.steps} upcomingBookings={journey.upcoming_bookings} />
                    <div className="flex flex-wrap gap-2 text-xs text-muted-foreground">
                        <span>
                            Fremgang: {readiness.is_ready ? 'Alle krav opfyldt' : 'Mangler stadig timer/prøver'}
                        </span>
                    </div>
                </div>

                <div className="grid max-w-lg gap-4">
                    <div className="grid grid-cols-2 gap-2 border-b pb-4">
                        <span className="text-sm text-muted-foreground">Email</span>
                        <span className="text-sm">{student.user.email}</span>
                    </div>
                    <div className="grid grid-cols-2 gap-2 border-b pb-4">
                        <span className="text-sm text-muted-foreground">Telefon</span>
                        <span className="text-sm">{student.phone ?? '-'}</span>
                    </div>
                    <div className="grid grid-cols-2 gap-2 border-b pb-4">
                        <span className="text-sm text-muted-foreground">Status</span>
                        <span>
                            <Badge variant={statusVariants[student.status] ?? 'secondary'}>
                                {statusLabels[student.status] ?? student.status}
                            </Badge>
                        </span>
                    </div>
                    <div className="grid grid-cols-2 gap-2 border-b pb-4">
                        <span className="text-sm text-muted-foreground">Startdato</span>
                        <span className="text-sm">{student.start_date ?? '-'}</span>
                    </div>
                    <div className="grid grid-cols-2 gap-2">
                        <span className="text-sm text-muted-foreground">Oprettet</span>
                        <span className="text-sm">{new Date(student.created_at).toLocaleDateString('da-DK')}</span>
                    </div>
                </div>

                {canEdit && (
                    <div className="max-w-2xl space-y-4">
                        <Heading variant="small" title="Færdigheder" />
                        <div className="flex flex-wrap gap-2">
                            {Object.entries(skillLabels).map(([key, label]) => {
                                const isCompleted = (student.completed_skills ?? []).includes(key);
                                return (
                                    <button
                                        key={key}
                                        type="button"
                                        onClick={() => {
                                            const current = student.completed_skills ?? [];
                                            const updated = isCompleted
                                                ? current.filter((s) => s !== key)
                                                : [...current, key];
                                            router.patch(
                                                StudentSkillController(student).url,
                                                { skills: updated },
                                                { preserveScroll: true },
                                            );
                                        }}
                                        className={`rounded-full border px-3 py-1.5 text-xs font-medium transition-colors ${
                                            isCompleted
                                                ? 'border-primary bg-primary text-primary-foreground'
                                                : 'border-border bg-background text-muted-foreground hover:border-primary/50'
                                        }`}
                                    >
                                        {label}
                                    </button>
                                );
                            })}
                        </div>
                    </div>
                )}

                <div className="max-w-lg space-y-4">
                    <Heading variant="small" title="Dokumenter" />

                    {student.media.length > 0 ? (
                        <ul className="space-y-2">
                            {student.media.map((media) => (
                                <li key={media.id} className="flex items-center justify-between rounded-lg border px-4 py-3">
                                    <a
                                        href={`/students/${student.id}/media/${media.id}`}
                                        className="flex items-center gap-2 text-sm hover:underline"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                    >
                                        <FileText className="size-4 text-muted-foreground" />
                                        {media.file_name}
                                    </a>
                                    {canEdit && (
                                        <Button variant="ghost" size="sm" onClick={() => handleMediaDelete(media.id)}>
                                            <Trash2 className="size-4" />
                                        </Button>
                                    )}
                                </li>
                            ))}
                        </ul>
                    ) : (
                        <p className="text-sm text-muted-foreground">Ingen dokumenter uploadet.</p>
                    )}

                    {canEdit && (
                        <form onSubmit={handleUpload} className="space-y-4 rounded-lg border p-4">
                            <div className="grid gap-2">
                                <Label htmlFor="file">Upload fil</Label>
                                <Input
                                    id="file"
                                    type="file"
                                    onChange={(e) => uploadForm.setData('file', e.target.files?.[0] ?? null)}
                                />
                                <InputError message={uploadForm.errors.file} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="collection">Kollektion</Label>
                                <select
                                    id="collection"
                                    value={uploadForm.data.collection}
                                    onChange={(e) => uploadForm.setData('collection', e.target.value)}
                                    className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors"
                                >
                                    <option value="documents">Dokumenter</option>
                                    <option value="photos">Fotos</option>
                                </select>
                            </div>

                            <Button disabled={uploadForm.processing || !uploadForm.data.file}>
                                <Upload className="mr-2 size-4" />
                                Upload
                            </Button>
                        </form>
                    )}
                </div>

                {pastBookings && pastBookings.length > 0 && (
                    <div className="max-w-2xl space-y-4">
                        <Heading variant="small" title="Lektionshistorik" />
                        <div className="divide-y rounded-xl border">
                            {pastBookings.map((row) => (
                                <div key={row.id} className="px-4 py-3">
                                    <div className="flex flex-wrap items-start justify-between gap-2">
                                        <div>
                                            <p className="text-sm font-medium">{row.type_label}</p>
                                            <p className="text-xs text-muted-foreground">{row.range_label}</p>
                                        </div>
                                        <div className="flex items-center gap-2">
                                            {row.attended === true && (
                                                <span className="text-xs font-medium text-green-600">Mødt</span>
                                            )}
                                            {row.attended === false && (
                                                <span className="text-xs font-medium text-destructive">Ikke mødt</span>
                                            )}
                                        </div>
                                    </div>
                                    {row.driving_skills.length > 0 && (
                                        <div className="mt-2 flex flex-wrap gap-1.5">
                                            {row.driving_skills.map((key) => (
                                                <span
                                                    key={key}
                                                    className="rounded-full border border-primary/30 bg-primary/5 px-2.5 py-0.5 text-xs font-medium text-primary"
                                                >
                                                    {skillLabels[key] ?? key}
                                                </span>
                                            ))}
                                        </div>
                                    )}
                                    {row.instructor_note && (
                                        <blockquote className="mt-2 border-l-2 border-muted pl-3 text-sm italic text-muted-foreground">
                                            {row.instructor_note}
                                        </blockquote>
                                    )}
                                </div>
                            ))}
                        </div>
                    </div>
                )}

                {canEdit && (
                    <div className="max-w-lg space-y-4">
                        <Heading variant="small" title="Hændelseslog" />
                        {eventTimeline.length > 0 ? (
                            <ol className="relative border-l pl-6">
                                {eventTimeline.map((entry) => (
                                    <li key={entry.id} className="relative mb-4 last:mb-0">
                                        <span
                                            className={`absolute -left-6 top-1 size-3 -translate-x-1/2 rounded-full ${categoryDotColors[entry.category] ?? 'bg-muted-foreground'}`}
                                        />
                                        <p className="text-sm">{entry.summary}</p>
                                        <p className="text-xs text-muted-foreground">
                                            {new Date(entry.created_at).toLocaleString('da-DK')}
                                        </p>
                                    </li>
                                ))}
                            </ol>
                        ) : (
                            <p className="text-sm text-muted-foreground">Ingen hændelser registreret.</p>
                        )}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
