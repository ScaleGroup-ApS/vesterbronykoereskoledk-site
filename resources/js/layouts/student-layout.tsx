import { CalendarDays, CreditCard, GraduationCap, LayoutGrid, MessageSquare, Route, Star } from 'lucide-react';
import { AppContent } from '@/components/app-content';
import { AppHeader } from '@/components/app-header';
import { AppShell } from '@/components/app-shell';
import ThemeProvider from '@/components/theme-provider';
import { index as chatIndex } from '@/routes/chat';
import { calendar, dashboard, feedback, payments, progress, theoryPractice } from '@/routes/student';
import type { AppLayoutProps, NavItem } from '@/types';

export default function StudentLayout({ children, breadcrumbs }: AppLayoutProps) {
    const navItems: NavItem[] = [
        { title: 'Oversigt', href: dashboard(), icon: LayoutGrid },
        { title: 'Kalender', href: calendar(), icon: CalendarDays },
        { title: 'Mit forløb', href: progress(), icon: Route },
        { title: 'Teoritræning', href: theoryPractice(), icon: GraduationCap },
        { title: 'Betalinger', href: payments(), icon: CreditCard },
        { title: 'Feedback', href: feedback(), icon: Star },
        { title: 'Chat', href: chatIndex(), icon: MessageSquare },
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
