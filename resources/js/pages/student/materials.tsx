import { Head } from '@inertiajs/react';
import { Download, FileText, Lock } from 'lucide-react';
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
                className="flex items-center justify-between px-4 py-3 transition-colors hover:bg-muted/50"
            >
                <div className="flex min-w-0 items-center gap-2">
                    <FileText className="size-4 shrink-0 text-muted-foreground" />
                    <div className="min-w-0">
                        <p className="truncate text-sm">{material.name}</p>
                        <p className="text-xs text-muted-foreground">{material.offer_name}</p>
                    </div>
                </div>
                <div className="flex shrink-0 items-center gap-3">
                    <span className="text-xs text-muted-foreground">{material.size}</span>
                    <Download className="size-4 text-muted-foreground" />
                </div>
            </a>
        );
    }

    return (
        <div className="flex items-center justify-between px-4 py-3 opacity-60">
            <div className="flex min-w-0 items-center gap-2">
                <Lock className="size-4 shrink-0 text-muted-foreground" />
                <div className="min-w-0">
                    <p className="truncate text-sm">{material.name}</p>
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

    return (
        <StudentLayout breadcrumbs={breadcrumbs}>
            <Head title="Materiale" />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <div className="space-y-1">
                    <Heading title="Kursusmateriale" />
                    <p className="text-sm text-muted-foreground">
                        Materiale låses op i takt med at du gennemfører teorilektioner.
                    </p>
                </div>

                {materials.length === 0 && (
                    <p className="text-sm text-muted-foreground">Ingen kursusmateriale tilknyttet dit forløb endnu.</p>
                )}

                {unlocked.length > 0 && (
                    <section className="space-y-3">
                        <Heading variant="small" title="Tilgængeligt nu" />
                        <div className="divide-y rounded-xl border">
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
