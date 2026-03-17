import { usePage } from '@inertiajs/react';
import { BookOpen, LayoutGrid } from 'lucide-react';
import { AppContent } from '@/components/app-content';
import { AppHeader } from '@/components/app-header';
import { AppShell } from '@/components/app-shell';
import ThemeProvider from '@/components/theme-provider';
import { dashboard } from '@/routes/student';
import type { AppLayoutProps, NavItem } from '@/types';

export default function StudentLayout({ children, breadcrumbs }: AppLayoutProps) {
    const { studentLearnUrl } = usePage<{ studentLearnUrl: string | null }>().props;

    const navItems: NavItem[] = [
        { title: 'Dashboard', href: dashboard(), icon: LayoutGrid },
        ...(studentLearnUrl ? [{ title: 'Kursusmateriale', href: studentLearnUrl, icon: BookOpen }] : []),
    ];

    return (
        <ThemeProvider>
            <AppShell>
                <AppHeader breadcrumbs={breadcrumbs} navItems={navItems} />
                <AppContent>{children}</AppContent>
            </AppShell>
        </ThemeProvider>
    );
}
