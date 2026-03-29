import { Head, Link, router } from '@inertiajs/react';
import { ArrowDown, ArrowUp, ArrowUpDown, Plus, Search, Send, X } from 'lucide-react';
import { useState } from 'react';
import BulkStudentLoginLinkController from '@/actions/App/Http/Controllers/Students/BulkStudentLoginLinkController';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';
import { index, create, show } from '@/routes/students';
import type { BreadcrumbItem, PaginatedStudents } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Elever',
        href: index().url,
    },
];

const statusLabels: Record<string, string> = {
    active: 'Aktiv',
    inactive: 'Inaktiv',
    graduated: 'Udlært',
    dropped_out: 'Frafaldet',
};

const statusVariants: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
    active: 'default',
    inactive: 'secondary',
    graduated: 'outline',
    dropped_out: 'destructive',
};

type Filters = {
    search: string;
    status: string;
    sort: string;
    direction: string;
};

function SortIcon({ field, filters }: { field: string; filters: Filters }) {
    if (filters.sort !== field) {
        return <ArrowUpDown className="ml-1 inline size-3 text-muted-foreground/50" />;
    }
    return filters.direction === 'asc'
        ? <ArrowUp className="ml-1 inline size-3" />
        : <ArrowDown className="ml-1 inline size-3" />;
}

export default function StudentsIndex({
    students,
    filters,
}: {
    students: PaginatedStudents;
    filters: Filters;
}) {
    const [search, setSearch] = useState(filters.search);
    const [selected, setSelected] = useState<number[]>([]);
    const [bulkProcessing, setBulkProcessing] = useState(false);

    const allOnPage = students.data.map((s) => s.id);
    const allSelected = allOnPage.length > 0 && allOnPage.every((id) => selected.includes(id));

    function toggleAll() {
        setSelected(allSelected ? [] : allOnPage);
    }

    function toggleOne(id: number) {
        setSelected((prev) =>
            prev.includes(id) ? prev.filter((i) => i !== id) : [...prev, id],
        );
    }

    function applyFilters(overrides: Partial<Filters>) {
        const params = { ...filters, ...overrides };
        router.get(index().url, Object.fromEntries(Object.entries(params).filter(([, v]) => v)), {
            preserveState: true,
            replace: true,
        });
    }

    function handleSort(field: string) {
        const direction = filters.sort === field && filters.direction === 'asc' ? 'desc' : 'asc';
        applyFilters({ sort: field, direction });
    }

    function handleSearch(e: React.FormEvent) {
        e.preventDefault();
        applyFilters({ search });
    }

    function clearSearch() {
        setSearch('');
        applyFilters({ search: '' });
    }

    function handleBulkLoginLinks() {
        if (selected.length === 0) {
            return;
        }
        setBulkProcessing(true);
        router.post(BulkStudentLoginLinkController().url, { student_ids: selected }, {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => {
                setBulkProcessing(false);
                setSelected([]);
            },
        });
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Elever" />

            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <Heading title="Elever" description="Administrer elever" />
                    <Button asChild>
                        <Link href={create().url}>
                            <Plus className="mr-2 size-4" />
                            Opret elev
                        </Link>
                    </Button>
                </div>

                <div className="flex flex-wrap items-center gap-3">
                    <form onSubmit={handleSearch} className="relative flex-1 sm:max-w-xs">
                        <Search className="absolute left-2.5 top-1/2 size-4 -translate-y-1/2 text-muted-foreground" />
                        <Input
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Søg navn, email eller telefon…"
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
                        value={filters.status || 'all'}
                        onValueChange={(v) => applyFilters({ status: v === 'all' ? '' : v })}
                    >
                        <SelectTrigger className="w-40 bg-background">
                            <SelectValue placeholder="Alle statusser" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">Alle statusser</SelectItem>
                            <SelectItem value="active">Aktiv</SelectItem>
                            <SelectItem value="inactive">Inaktiv</SelectItem>
                            <SelectItem value="graduated">Udlært</SelectItem>
                            <SelectItem value="dropped_out">Frafaldet</SelectItem>
                        </SelectContent>
                    </Select>

                    {selected.length > 0 && (
                        <Button
                            variant="outline"
                            size="sm"
                            onClick={handleBulkLoginLinks}
                            disabled={bulkProcessing}
                            className="ml-auto gap-1.5"
                        >
                            <Send className="size-4" />
                            Send login link ({selected.length})
                        </Button>
                    )}
                </div>

                <div className="rounded-xl border">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b text-left">
                                <th className="px-4 py-3">
                                    <Checkbox
                                        checked={allSelected}
                                        onCheckedChange={toggleAll}
                                        aria-label="Vælg alle"
                                    />
                                </th>
                                <th
                                    className="cursor-pointer px-4 py-3 font-medium select-none"
                                    onClick={() => handleSort('name')}
                                >
                                    Navn
                                    <SortIcon field="name" filters={filters} />
                                </th>
                                <th
                                    className="cursor-pointer px-4 py-3 font-medium select-none"
                                    onClick={() => handleSort('email')}
                                >
                                    Email
                                    <SortIcon field="email" filters={filters} />
                                </th>
                                <th className="px-4 py-3 font-medium">Telefon</th>
                                <th
                                    className="cursor-pointer px-4 py-3 font-medium select-none"
                                    onClick={() => handleSort('status')}
                                >
                                    Status
                                    <SortIcon field="status" filters={filters} />
                                </th>
                                <th
                                    className="cursor-pointer px-4 py-3 font-medium select-none"
                                    onClick={() => handleSort('start_date')}
                                >
                                    Startdato
                                    <SortIcon field="start_date" filters={filters} />
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            {students.data.map((student) => (
                                <tr
                                    key={student.id}
                                    className="cursor-pointer border-b transition-colors hover:bg-muted/50 last:border-0"
                                >
                                    <td className="px-4 py-3" onClick={(e) => e.stopPropagation()}>
                                        <Checkbox
                                            checked={selected.includes(student.id)}
                                            onCheckedChange={() => toggleOne(student.id)}
                                            aria-label={`Vælg ${student.user.name}`}
                                        />
                                    </td>
                                    <td
                                        className="px-4 py-3 font-medium"
                                        onClick={() => router.visit(show(student).url)}
                                    >
                                        {student.user.name}
                                    </td>
                                    <td
                                        className="px-4 py-3 text-muted-foreground"
                                        onClick={() => router.visit(show(student).url)}
                                    >
                                        {student.user.email}
                                    </td>
                                    <td
                                        className="px-4 py-3 text-muted-foreground"
                                        onClick={() => router.visit(show(student).url)}
                                    >
                                        {student.phone ?? '-'}
                                    </td>
                                    <td
                                        className="px-4 py-3"
                                        onClick={() => router.visit(show(student).url)}
                                    >
                                        <Badge variant={statusVariants[student.status] ?? 'secondary'}>
                                            {statusLabels[student.status] ?? student.status}
                                        </Badge>
                                    </td>
                                    <td
                                        className="px-4 py-3 text-muted-foreground"
                                        onClick={() => router.visit(show(student).url)}
                                    >
                                        {student.start_date ?? '-'}
                                    </td>
                                </tr>
                            ))}
                            {students.data.length === 0 && (
                                <tr>
                                    <td colSpan={6} className="px-4 py-8 text-center text-muted-foreground">
                                        Ingen elever fundet.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {students.meta.last_page > 1 && (
                    <div className="flex items-center justify-between">
                        <p className="text-sm text-muted-foreground">
                            Viser {students.meta.from}-{students.meta.to} af {students.meta.total} elever
                        </p>
                        <div className="flex gap-2">
                            {students.links.prev && (
                                <Button variant="outline" size="sm" asChild>
                                    <Link href={students.links.prev}>Forrige</Link>
                                </Button>
                            )}
                            {students.links.next && (
                                <Button variant="outline" size="sm" asChild>
                                    <Link href={students.links.next}>Næste</Link>
                                </Button>
                            )}
                        </div>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
