import { Head } from '@inertiajs/react';
import { CalendarDays, TrendingDown, Users, Wallet } from 'lucide-react';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';
import { dashboard } from '@/routes';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: dashboard().url },
];

type AdminKpis = {
    total_students: number;
    upcoming_bookings: number;
    no_show_rate: number;
    total_outstanding: number;
};

type InstructorKpis = {
    upcoming_bookings: number;
    no_show_rate: number;
};

type Kpis = AdminKpis | InstructorKpis | Record<string, never>;

function KpiCard({ icon: Icon, label, value }: { icon: React.ElementType; label: string; value: string | number }) {
    return (
        <div className="rounded-xl border p-5">
            <div className="flex items-center gap-3">
                <div className="flex size-10 items-center justify-center rounded-lg bg-muted">
                    <Icon className="size-5 text-muted-foreground" />
                </div>
                <div>
                    <p className="text-sm text-muted-foreground">{label}</p>
                    <p className="text-2xl font-semibold">{value}</p>
                </div>
            </div>
        </div>
    );
}

export default function Dashboard({ kpis }: { kpis: Kpis }) {
    const isAdmin = 'total_students' in kpis;
    const isInstructor = 'upcoming_bookings' in kpis && !isAdmin;

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    {isAdmin && (
                        <>
                            <KpiCard
                                icon={Users}
                                label="Aktive elever"
                                value={(kpis as AdminKpis).total_students}
                            />
                            <KpiCard
                                icon={CalendarDays}
                                label="Kommende bookinger (7 dage)"
                                value={(kpis as AdminKpis).upcoming_bookings}
                            />
                            <KpiCard
                                icon={TrendingDown}
                                label="No-show rate"
                                value={`${(kpis as AdminKpis).no_show_rate}%`}
                            />
                            <KpiCard
                                icon={Wallet}
                                label="Udestående saldo"
                                value={`${Number((kpis as AdminKpis).total_outstanding).toLocaleString('da-DK')} kr.`}
                            />
                        </>
                    )}
                    {isInstructor && (
                        <>
                            <KpiCard
                                icon={CalendarDays}
                                label="Kommende bookinger (7 dage)"
                                value={(kpis as InstructorKpis).upcoming_bookings}
                            />
                            <KpiCard
                                icon={TrendingDown}
                                label="No-show rate"
                                value={`${(kpis as InstructorKpis).no_show_rate}%`}
                            />
                        </>
                    )}
                    {!isAdmin && !isInstructor && (
                        <div className="col-span-full rounded-xl border px-4 py-6 text-center text-sm text-muted-foreground">
                            Ingen KPI-data tilgængelig.
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
