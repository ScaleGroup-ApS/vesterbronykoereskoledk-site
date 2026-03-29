import { BookOpen, CalendarDays, ClipboardList, LayoutGrid, Route, Sparkles } from 'lucide-react';
import { AppContent } from '@/components/app-content';
import { AppHeader } from '@/components/app-header';
import { AppShell } from '@/components/app-shell';
import ThemeProvider from '@/components/theme-provider';
import { dashboard, faerdigheder, forloeb, historik, kalender, materiale } from '@/routes/student';
import type { AppLayoutProps, NavItem } from '@/types';

const navItems: NavItem[] = [
    { title: 'Oversigt', href: dashboard(), icon: LayoutGrid },
    { title: 'Kalender', href: kalender(), icon: CalendarDays },
    { title: 'Mit forløb', href: forloeb(), icon: Route },
    { title: 'Færdigheder', href: faerdigheder(), icon: Sparkles },
    { title: 'Materiale', href: materiale(), icon: BookOpen },
    { title: 'Historik', href: historik(), icon: ClipboardList },
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
