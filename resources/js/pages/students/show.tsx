import { Head, Link, router } from '@inertiajs/react';
import { Pencil, Trash2 } from 'lucide-react';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem, Student } from '@/types';
import { index, show, edit, destroy } from '@/routes/students';

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

export default function StudentShow({ student }: { student: Student }) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Elever', href: index().url },
        { title: student.user.name, href: show(student).url },
    ];

    function handleDelete() {
        if (confirm('Er du sikker på, at du vil slette denne elev?')) {
            router.delete(destroy(student).url);
        }
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={student.user.name} />

            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <Heading title={student.user.name} />
                    <div className="flex gap-2">
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
            </div>
        </AppLayout>
    );
}
