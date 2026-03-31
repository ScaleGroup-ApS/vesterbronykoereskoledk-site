import { Head, Link } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { index, create } from '@/actions/App/Http/Controllers/Staff/StaffController';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Medarbejdere', href: index().url },
];

const roleLabels: Record<string, string> = {
    admin: 'Admin',
    instructor: 'Instruktør',
};

const roleVariants: Record<string, 'default' | 'secondary'> = {
    admin: 'default',
    instructor: 'secondary',
};

type StaffUser = {
    id: number;
    name: string;
    email: string;
    role: string;
    created_at: string;
};

export default function StaffIndex({ staff }: { staff: StaffUser[] }) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Medarbejdere" />

            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <Heading title="Medarbejdere" description="Administrer administratorer og instruktører" />
                    <Button asChild>
                        <Link href={create().url}>
                            <Plus className="mr-2 size-4" />
                            Tilføj medarbejder
                        </Link>
                    </Button>
                </div>

                <div className="rounded-xl border">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b text-left">
                                <th className="px-4 py-3 font-medium">Navn</th>
                                <th className="px-4 py-3 font-medium">Email</th>
                                <th className="px-4 py-3 font-medium">Rolle</th>
                            </tr>
                        </thead>
                        <tbody>
                            {staff.map((user) => (
                                <tr key={user.id} className="border-b transition-colors hover:bg-muted/50 last:border-0">
                                    <td className="px-4 py-3 font-medium">{user.name}</td>
                                    <td className="px-4 py-3 text-muted-foreground">{user.email}</td>
                                    <td className="px-4 py-3">
                                        <Badge variant={roleVariants[user.role] ?? 'secondary'}>
                                            {roleLabels[user.role] ?? user.role}
                                        </Badge>
                                    </td>
                                </tr>
                            ))}
                            {staff.length === 0 && (
                                <tr>
                                    <td colSpan={3} className="px-4 py-8 text-center text-muted-foreground">
                                        Ingen medarbejdere fundet.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>
        </AppLayout>
    );
}
