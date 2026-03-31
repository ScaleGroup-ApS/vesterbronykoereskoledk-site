import { Link } from '@inertiajs/react';
import { format, isBefore, startOfDay } from 'date-fns';
import { da } from 'date-fns/locale';
import { Users } from 'lucide-react';
import { show as showCourse } from '@/routes/courses';

export type CourseHoldRow = {
    id: number;
    title: string;
    start: string;
    end: string;
    max_students: number | null;
    public_spots_remaining: number | null;
    enrollments_completed_count: number;
    enrollments_pending_count: number;
};

type Props = {
    courses: CourseHoldRow[];
};

function spotsRemaining(row: CourseHoldRow): number | null {
    if (row.max_students == null) {
        return null;
    }

    const taken = row.enrollments_completed_count + row.enrollments_pending_count;

    return Math.max(0, row.max_students - taken);
}

export function CourseHoldCapacityChart({ courses }: Props) {
    const upcoming = courses.filter((c) => {
        const start = new Date(c.start);
        if (Number.isNaN(start.getTime())) {
            return false;
        }

        return !isBefore(startOfDay(start), startOfDay(new Date()));
    });

    if (upcoming.length === 0) {
        return (
            <div className="rounded-xl border border-dashed p-6 text-center text-sm text-muted-foreground">
                <Users className="mx-auto mb-2 size-8 opacity-40" />
                Ingen kommende hold endnu. Opret et kursus i kalenderen nedenfor.
            </div>
        );
    }

    return (
        <div className="space-y-4">
            <div className="flex flex-wrap items-center gap-4 text-xs text-muted-foreground">
                <span className="inline-flex items-center gap-1.5">
                    <span className="size-2.5 rounded-sm bg-primary" aria-hidden />
                    Godkendt tilmelding
                </span>
                <span className="inline-flex items-center gap-1.5">
                    <span className="size-2.5 rounded-sm bg-amber-500/90" aria-hidden />
                    Afventer (betaling/godkendelse)
                </span>
                <span className="inline-flex items-center gap-1.5">
                    <span className="size-2.5 rounded-sm bg-muted" aria-hidden />
                    Ledig plads
                </span>
            </div>

            <ul className="space-y-5">
                {upcoming.map((row) => {
                    const completed = row.enrollments_completed_count;
                    const pending = row.enrollments_pending_count;
                    const max = row.max_students;
                    const remaining = spotsRemaining(row);

                    let completedW = 0;
                    let pendingW = 0;
                    let emptyW = 0;

                    if (max != null && max > 0) {
                        const filled = completed + pending;
                        if (filled > max) {
                            const t = filled;
                            completedW = (completed / t) * 100;
                            pendingW = (pending / t) * 100;
                            emptyW = 0;
                        } else {
                            completedW = (completed / max) * 100;
                            pendingW = (pending / max) * 100;
                            emptyW = Math.max(0, 100 - completedW - pendingW);
                        }
                    } else if (completed + pending > 0) {
                        const total = completed + pending;
                        completedW = (completed / total) * 100;
                        pendingW = (pending / total) * 100;
                    }

                    const showBar = max != null || completed + pending > 0;

                    return (
                        <li key={row.id} className="space-y-2">
                            <div className="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <Link
                                        href={showCourse(row.id).url}
                                        className="font-medium text-foreground underline-offset-4 hover:underline"
                                    >
                                        {row.title}
                                    </Link>
                                    <p className="text-sm text-muted-foreground">
                                        {format(new Date(row.start), "EEEE d. MMMM yyyy 'kl.' HH:mm", {
                                            locale: da,
                                        })}
                                    </p>
                                </div>
                                <div className="text-right text-sm text-muted-foreground sm:min-w-[11rem]">
                                    {max != null ? (
                                        <>
                                            <span className="font-medium text-foreground">
                                                {remaining ?? 0} ledige
                                            </span>
                                            {' · '}
                                            {completed + pending} / {max} tilmeldt
                                        </>
                                    ) : (
                                        <span>
                                            {completed} godkendt
                                            {pending > 0 ? ` · ${pending} afventer` : ''}
                                            <span className="text-muted-foreground"> · Ingen max.</span>
                                        </span>
                                    )}
                                    {row.public_spots_remaining != null && (
                                        <p className="text-xs text-muted-foreground">
                                            Vist på forsiden: {row.public_spots_remaining} pladser
                                        </p>
                                    )}
                                </div>
                            </div>

                            {showBar ? (
                                <div
                                    className="flex h-3 w-full overflow-hidden rounded-full bg-muted"
                                    role="img"
                                    aria-label={`Kapacitet for ${row.title}`}
                                >
                                    {completedW > 0 && (
                                        <div
                                            className="h-full shrink-0 bg-primary transition-all"
                                            style={{ width: `${completedW}%` }}
                                        />
                                    )}
                                    {pendingW > 0 && (
                                        <div
                                            className="h-full shrink-0 bg-amber-500/90 transition-all"
                                            style={{ width: `${pendingW}%` }}
                                        />
                                    )}
                                    {emptyW > 0 && (
                                        <div
                                            className="h-full shrink-0 bg-muted transition-all"
                                            style={{ width: `${emptyW}%` }}
                                        />
                                    )}
                                </div>
                            ) : (
                                <p className="text-xs text-muted-foreground">Ingen tilmeldinger endnu.</p>
                            )}
                        </li>
                    );
                })}
            </ul>
        </div>
    );
}
