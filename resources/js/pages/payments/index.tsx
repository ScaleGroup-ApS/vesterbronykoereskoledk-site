import { Head, Link, router } from '@inertiajs/react';
import { Plus, Search, Trash2, X } from 'lucide-react';
import { useState } from 'react';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';
import { index, create, destroy } from '@/routes/payments';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Betalinger', href: index().url },
];

type Payment = {
    id: number;
    student: { user: { name: string } };
    amount: string;
    method: string;
    recorded_at: string;
    notes: string | null;
};

type PaginatedPayments = {
    data: Payment[];
    links: { prev: string | null; next: string | null };
    meta: { from: number | null; to: number | null; total: number; last_page: number };
};

type Filters = {
    search: string;
    method: string;
};

const methodLabels: Record<string, string> = {
    cash: 'Kontant',
    card: 'Kort',
    mobile_pay: 'MobilePay',
    invoice: 'Faktura',
};

export default function PaymentsIndex({
    payments,
    filters,
}: {
    payments: PaginatedPayments;
    filters?: Filters;
}) {
    const [search, setSearch] = useState(filters?.search ?? '');

    function applyFilters(overrides: Partial<Filters>) {
        const params = { ...filters, ...overrides };
        router.get(index().url, Object.fromEntries(Object.entries(params).filter(([, v]) => v)), {
            preserveState: true,
            replace: true,
        });
    }

    function handleSearch(e: React.FormEvent) {
        e.preventDefault();
        applyFilters({ search });
    }

    function clearSearch() {
        setSearch('');
        applyFilters({ search: '' });
    }

    function handleDelete(payment: Payment) {
        if (confirm('Er du sikker på, at du vil slette denne betaling?')) {
            router.delete(destroy(payment).url);
        }
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Betalinger" />

            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <Heading title="Betalinger" description="Registrerede betalinger fra elever" />
                    <Button asChild>
                        <Link href={create().url}>
                            <Plus className="mr-2 size-4" />
                            Registrer betaling
                        </Link>
                    </Button>
                </div>

                <div className="flex flex-wrap items-center gap-3">
                    <form onSubmit={handleSearch} className="relative flex-1 sm:max-w-xs">
                        <Search className="absolute left-2.5 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Søg elevnavn…"
                            className="pl-9 pr-8"
                        />
                        {search && (
                            <button
                                type="button"
                                onClick={clearSearch}
                                className="absolute right-2.5 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                            >
                                <X className="size-4" />
                            </button>
                        )}
                    </form>
                    <Select
                        value={filters?.method || 'all'}
                        onValueChange={(v) => applyFilters({ method: v === 'all' ? '' : v })}
                    >
                        <SelectTrigger className="w-40 bg-background">
                            <SelectValue placeholder="Alle metoder" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">Alle metoder</SelectItem>
                            <SelectItem value="cash">Kontant</SelectItem>
                            <SelectItem value="card">Kort</SelectItem>
                            <SelectItem value="mobile_pay">MobilePay</SelectItem>
                            <SelectItem value="invoice">Faktura</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <div className="rounded-xl border">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b text-left">
                                <th className="px-4 py-3 font-medium">Elev</th>
                                <th className="px-4 py-3 font-medium">Beløb</th>
                                <th className="px-4 py-3 font-medium">Metode</th>
                                <th className="px-4 py-3 font-medium">Dato</th>
                                <th className="px-4 py-3 font-medium"></th>
                            </tr>
                        </thead>
                        <tbody>
                            {payments.data.map((payment) => (
                                <tr key={payment.id} className="border-b last:border-0">
                                    <td className="px-4 py-3 font-medium">{payment.student.user.name}</td>
                                    <td className="px-4 py-3">{Number(payment.amount).toLocaleString('da-DK')} kr.</td>
                                    <td className="px-4 py-3">{methodLabels[payment.method] ?? payment.method}</td>
                                    <td className="px-4 py-3 text-muted-foreground">
                                        {new Date(payment.recorded_at).toLocaleDateString('da-DK')}
                                    </td>
                                    <td className="px-4 py-3 text-right">
                                        <Button variant="ghost" size="sm" onClick={() => handleDelete(payment)}>
                                            <Trash2 className="size-4" />
                                        </Button>
                                    </td>
                                </tr>
                            ))}
                            {payments.data.length === 0 && (
                                <tr>
                                    <td colSpan={5} className="px-4 py-8 text-center text-muted-foreground">
                                        Ingen betalinger fundet.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {payments.meta.last_page > 1 && (
                    <div className="flex items-center justify-between">
                        <p className="text-sm text-muted-foreground">
                            Viser {payments.meta.from}-{payments.meta.to} af {payments.meta.total} betalinger
                        </p>
                        <div className="flex gap-2">
                            {payments.links.prev && (
                                <Button variant="outline" size="sm" asChild>
                                    <Link href={payments.links.prev}>Forrige</Link>
                                </Button>
                            )}
                            {payments.links.next && (
                                <Button variant="outline" size="sm" asChild>
                                    <Link href={payments.links.next}>Næste</Link>
                                </Button>
                            )}
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
