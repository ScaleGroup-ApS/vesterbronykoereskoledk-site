import { Link } from '@inertiajs/react';
import { Car, CreditCard, GraduationCap, LayoutGrid, MessageSquare, Tag } from 'lucide-react';
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
import { dashboard } from '@/routes';
import { index as chatIndex } from '@/routes/chat';
import { index as offersIndex } from '@/routes/offers';
import { index as paymentsIndex } from '@/routes/payments';
import { index as studentsIndex } from '@/routes/students';
import { index as vehiclesIndex } from '@/routes/vehicles';
import type { NavItem } from '@/types';
import AppLogo from './app-logo';

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
    // {
    //     title: 'Hold',
    //     href: teamsIndex(),
    //     icon: Users,
    // },
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
    // {
    //     title: 'Kurser',
    //     href: coursesIndex(),
    //     icon: CalendarDays,
    // },
    // {
    //     title: 'Tilmeldinger',
    //     href: enrollmentsIndex(),
    //     icon: CalendarDays,
    // },
    // {
    //     title: 'Bookinger',
    //     href: bookingsIndex(),
    //     icon: CalendarDays,
    // },
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

const footerNavItems: NavItem[] = [];

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
