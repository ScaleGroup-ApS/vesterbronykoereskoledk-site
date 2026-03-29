import { Link, usePage } from '@inertiajs/react';
import {
    BookOpen,
    CalendarDays,
    Car,
    CreditCard,
    FileText,
    GraduationCap,
    LayoutGrid,
    ListChecks,
    MessageSquare,
    MessageSquareQuote,
    ScrollText,
    Tag,
    UserPlus,
    Users,
} from 'lucide-react';
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
import { index as enrollmentsIndex } from '@/actions/App/Http/Controllers/Enrollment/EnrollmentApprovalController';
import { dashboard } from '@/routes';
import { index as bookingsIndex } from '@/routes/bookings';
import { index as chatIndex } from '@/routes/chat';
import { index as coursesIndex } from '@/routes/courses';
import { edit as editHomeCopy } from '@/routes/marketing/home-copy';
import { index as testimonialsIndex } from '@/routes/marketing/testimonials';
import { index as valueBlocksIndex } from '@/routes/marketing/value-blocks';
import { index as offersIndex } from '@/routes/offers';
import { index as paymentsIndex } from '@/routes/payments';
import { index as studentsIndex } from '@/routes/students';
import { index as teamsIndex } from '@/routes/teams';
import { index as timelineIndex } from '@/routes/timeline';
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
        title: 'Kurser',
        href: coursesIndex(),
        icon: BookOpen,
    },
    {
        title: 'Tilmeldinger',
        href: enrollmentsIndex(),
        icon: UserPlus,
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
    {
        title: 'Hændelseslog',
        href: timelineIndex(),
        icon: ScrollText,
    },
];

const footerNavItems: NavItem[] = [];

const marketingNavItems: NavItem[] = [
    {
        title: 'Forsidetekster',
        href: editHomeCopy.url(),
        icon: FileText,
    },
    {
        title: 'USP-blokke',
        href: valueBlocksIndex.url(),
        icon: ListChecks,
    },
    {
        title: 'Udtalelser',
        href: testimonialsIndex.url(),
        icon: MessageSquareQuote,
    },
];

export function AppSidebar() {
    const { auth } = usePage().props;
    const showMarketing = auth.user?.role === 'admin';

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
                {showMarketing ? <NavMain items={marketingNavItems} groupLabel="Hjemmeside" /> : null}
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
