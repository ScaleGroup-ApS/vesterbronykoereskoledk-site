import { LayoutGrid, Route } from 'lucide-react';
import { AppContent } from '@/components/app-content';
import { AppHeader } from '@/components/app-header';
import { AppShell } from '@/components/app-shell';
import ThemeProvider from '@/components/theme-provider';
import { dashboard, forloeb } from '@/routes/student';
import type { AppLayoutProps, NavItem } from '@/types';

const navItems: NavItem[] = [
    { title: 'Oversigt', href: dashboard(), icon: LayoutGrid },
    { title: 'Mit forløb', href: forloeb(), icon: Route },
];

export default function StudentLayout({ children, breadcrumbs }: AppLayoutProps) {
    return (
        <ThemeProvider>
            <AppShell>
                <AppHeader breadcrumbs={breadcrumbs} navItems={navItems} />
                <AppContent>{children}</AppContent>
            </AppShell>
        </ThemeProvider>
    );
}
