import { usePage } from '@inertiajs/react';
import { BookOpen, CalendarDays, ClipboardList, LayoutGrid, MessageSquare, Route, Sparkles } from 'lucide-react';
import { AppContent } from '@/components/app-content';
import { AppHeader } from '@/components/app-header';
import { AppShell } from '@/components/app-shell';
import ThemeProvider from '@/components/theme-provider';
import { index as chatIndex } from '@/routes/chat';
import { dashboard, faerdigheder, forloeb, historik, kalender, materiale } from '@/routes/student';
import type { AppLayoutProps, NavItem } from '@/types';

export default function StudentLayout({ children, breadcrumbs }: AppLayoutProps) {
    const { studentLearnUrl } = usePage<{ studentLearnUrl: string | null }>().props;

    const navItems: NavItem[] = [
        { title: 'Oversigt', href: dashboard(), icon: LayoutGrid },
        { title: 'Kalender', href: kalender(), icon: CalendarDays },
        { title: 'Mit forløb', href: forloeb(), icon: Route },
        { title: 'Færdigheder', href: faerdigheder(), icon: Sparkles },
        { title: 'Materiale', href: materiale(), icon: BookOpen },
        { title: 'Historik', href: historik(), icon: ClipboardList },
        { title: 'Chat', href: chatIndex(), icon: MessageSquare },
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
