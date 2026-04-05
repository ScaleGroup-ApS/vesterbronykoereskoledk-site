import { usePage } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import StudentLayout from '@/layouts/student-layout';
import type { AppLayoutProps } from '@/types';

export default function RoleLayout({ children, breadcrumbs }: AppLayoutProps) {
    const { auth } = usePage<{ auth: { user: { role: string } } }>().props;

    if (auth.user.role === 'student') {
        return <StudentLayout breadcrumbs={breadcrumbs}>{children}</StudentLayout>;
    }

    return <AppLayout breadcrumbs={breadcrumbs}>{children}</AppLayout>;
}
