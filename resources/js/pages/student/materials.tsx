import { Head } from '@inertiajs/react';
import { Download, FileText, Lock, Unlock } from 'lucide-react';
import Heading from '@/components/heading';
import StudentLayout from '@/layouts/student-layout';
import { materials } from '@/routes/student';
import type { BreadcrumbItem } from '@/types';

type Material = {
    id: number;
    name: string;
    size: string;
    url: string;
    offer_name: string;
    unlock_at_lesson: number | null;
    is_unlocked: boolean;
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Materiale', href: materials().url },
];

function MaterialRow({ material }: { material: Material }) {
    if (material.is_unlocked) {
        return (
            <a
                href={material.url}
                target="_blank"
                rel="noopener noreferrer"
                className="group flex items-center justify-between px-5 py-4 transition-colors hover:bg-muted/40"
            >
                <div className="flex min-w-0 items-center gap-3">
                    <div className="flex size-9 shrink-0 items-center justify-center rounded-lg bg-primary/10">
                        <FileText className="size-4 text-primary" />
                    </div>
                    <div className="min-w-0">
                        <p className="truncate text-sm font-medium group-hover:text-primary">{material.name}</p>
                        <p className="text-xs text-muted-foreground">{material.offer_name}</p>
                    </div>
                </div>
                <div className="flex shrink-0 items-center gap-3">
                    <span className="text-xs text-muted-foreground">{material.size}</span>
                    <Download className="size-4 text-muted-foreground transition group-hover:text-primary" />
                </div>
            </a>
        );
    }

    return (
        <div className="flex items-center justify-between px-5 py-4 opacity-50">
            <div className="flex min-w-0 items-center gap-3">
                <div className="flex size-9 shrink-0 items-center justify-center rounded-lg bg-muted">
                    <Lock className="size-4 text-muted-foreground" />
                </div>
                <div className="min-w-0">
                    <p className="truncate text-sm font-medium">{material.name}</p>
                    <p className="text-xs text-muted-foreground">
                        Låses op efter lektion {material.unlock_at_lesson}
                    </p>
                </div>
            </div>
        </div>
    );
}

export default function StudentMateriale({ materials }: { materials: Material[] }) {
    const unlocked = materials.filter((m) => m.is_unlocked);
    const locked = materials.filter((m) => !m.is_unlocked);
    const total = materials.length;
    const unlockedCount = unlocked.length;
    const pct = total > 0 ? Math.round((unlockedCount / total) * 100) : 0;

    return (
        <StudentLayout breadcrumbs={breadcrumbs}>
            <Head title="Materiale" />
            <div className="flex h-full flex-1 flex-col gap-8 rounded-xl p-4 sm:p-6">
                <div className="space-y-1">
                    <Heading title="Kursusmateriale" />
                    <p className="text-sm text-muted-foreground">
                        Materiale låses op i takt med at du gennemfører teorilektioner.
                    </p>
                </div>

                {/* Unlock progress */}
                {total > 0 && (
                    <div className="space-y-2 rounded-xl border bg-card p-5 shadow-sm">
                        <div className="flex items-center justify-between text-sm">
                            <span className="flex items-center gap-1.5 font-medium">
                                <Unlock className="size-4 text-primary" />
                                Låst op
                            </span>
                            <span className="tabular-nums text-muted-foreground">{unlockedCount} / {total}</span>
                        </div>
                        <div className="h-2 overflow-hidden rounded-full bg-muted">
                            <div
                                className="h-full rounded-full bg-primary transition-all duration-700"
                                style={{ width: `${pct}%` }}
                            />
                        </div>
                    </div>
                )}

                {materials.length === 0 && (
                    <div className="flex flex-col items-center gap-3 rounded-2xl border border-dashed py-10 text-center">
                        <FileText className="size-10 text-muted-foreground/30" />
                        <div>
                            <p className="font-medium text-muted-foreground">Ingen kursusmateriale endnu</p>
                            <p className="mt-1 text-sm text-muted-foreground/70">
                                Materialer tilføjes af din køreskole og låses op automatisk.
                            </p>
                        </div>
                    </div>
                )}

                {unlocked.length > 0 && (
                    <section className="space-y-3">
                        <Heading variant="small" title="Tilgængeligt nu" />
                        <div className="divide-y rounded-xl border shadow-sm">
                            {unlocked.map((m) => <MaterialRow key={m.id} material={m} />)}
                        </div>
                    </section>
                )}

                {locked.length > 0 && (
                    <section className="space-y-3">
                        <Heading variant="small" title="Låst" />
                        <div className="divide-y rounded-xl border">
                            {locked.map((m) => <MaterialRow key={m.id} material={m} />)}
                        </div>
                    </section>
                )}
            </div>
        </StudentLayout>
    );
}
