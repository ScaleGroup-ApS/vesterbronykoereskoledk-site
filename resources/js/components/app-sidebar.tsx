import { Link } from '@inertiajs/react';
import { BookOpen, CalendarDays, Car, CreditCard, Folder, GraduationCap, LayoutGrid, MessageSquare, Tag, Users } from 'lucide-react';
import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import type { NavItem } from '@/types';
import AppLogo from './app-logo';
import { dashboard } from '@/routes';
import { index as studentsIndex } from '@/routes/students';
import { index as teamsIndex } from '@/routes/teams';
import { index as vehiclesIndex } from '@/routes/vehicles';
import { index as offersIndex } from '@/routes/offers';
import { index as bookingsIndex } from '@/routes/bookings';
import { index as paymentsIndex } from '@/routes/payments';
import { index as chatIndex } from '@/routes/chat';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Elever',
        href: studentsIndex(),
        icon: GraduationCap,
    },
    {
        title: 'Hold',
        href: teamsIndex(),
        icon: Users,
    },
    {
        title: 'Køretøjer',
        href: vehiclesIndex(),
        icon: Car,
    },
    {
        title: 'Tilbud',
        href: offersIndex(),
        icon: Tag,
    },
    {
        title: 'Bookinger',
        href: bookingsIndex(),
        icon: CalendarDays,
    },
    {
        title: 'Betalinger',
        href: paymentsIndex(),
        icon: CreditCard,
    },
    {
        title: 'Chat',
        href: chatIndex(),
        icon: MessageSquare,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'Repository',
        href: 'https://github.com/laravel/react-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#react',
        icon: BookOpen,
    },
];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
