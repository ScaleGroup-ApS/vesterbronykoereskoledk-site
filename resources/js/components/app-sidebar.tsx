import { Link, usePage } from '@inertiajs/react';
import {
    BookOpen,
    CalendarDays,
    CreditCard,
    GraduationCap,
    LayoutGrid,
    LifeBuoy,
    MessageSquare,
    ScrollText,
    Tag,
    UserCog,
    UserPlus,
} from 'lucide-react';
import { index as enrollmentsIndex } from '@/actions/App/Http/Controllers/Enrollment/EnrollmentApprovalController';
import { index as staffIndex } from '@/actions/App/Http/Controllers/Staff/StaffController';
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
import { index as bookingsIndex } from '@/routes/bookings';
import { index as chatIndex } from '@/routes/chat';
import { index as coursesIndex } from '@/routes/courses';
import { index as offersIndex } from '@/routes/offers';
import { index as paymentsIndex } from '@/routes/payments';
import { index as studentsIndex } from '@/routes/students';
import { index as timelineIndex } from '@/routes/timeline';
import type { NavItem } from '@/types';
import AppLogo from './app-logo';

const sharedNavItems: NavItem[] = [
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
        title: 'Tilbud',
        href: offersIndex(),
        icon: Tag,
    },
    {
        title: 'Læringsindhold',
        href: offersIndex(),
        icon: Library,
    },
    {
        title: 'Kurser',
        href: coursesIndex(),
        icon: BookOpen,
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
        title: 'Support',
        href: '/support',
        icon: LifeBuoy,
    },
];

const adminOnlyNavItems: NavItem[] = [
    {
        title: 'Medarbejdere',
        href: staffIndex(),
        icon: UserCog,
    },
    {
        title: 'Tilmeldinger',
        href: enrollmentsIndex(),
        icon: UserPlus,
    },
    {
        title: 'Hændelseslog',
        href: timelineIndex(),
        icon: ScrollText,
    },
];

const footerNavItems: NavItem[] = [];

export function AppSidebar() {
    const { auth } = usePage().props;
    const isAdmin = auth.user?.role === 'admin';

    const mainNavItems = isAdmin
        ? [...sharedNavItems, ...adminOnlyNavItems]
        : sharedNavItems;

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
