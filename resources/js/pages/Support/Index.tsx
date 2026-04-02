import { Head, router, useForm } from '@inertiajs/react';
import { ChevronLeft, ChevronRight, LifeBuoy, Plus } from 'lucide-react';
import { useState } from 'react';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Support', href: '/support' }];

const PAGE_SIZE = 10;

type Ticket = {
    id: number;
    subject: string;
    status: 'open' | 'waiting' | 'solved' | 'closed';
    priority: 'low' | 'normal' | 'high' | 'urgent';
    createdAt: string;
    origin?: string | null;
};

const statusLabel: Record<Ticket['status'], string> = {
    open: 'Åben',
    waiting: 'Venter',
    solved: 'Løst',
    closed: 'Lukket',
};

const statusClass: Record<Ticket['status'], string> = {
    open: 'bg-green-100 text-green-800',
    waiting: 'bg-amber-100 text-amber-800',
    solved: 'bg-gray-100 text-gray-700',
    closed: 'bg-gray-200 text-gray-600',
};

const priorityLabel: Record<Ticket['priority'], string> = {
    low: 'Lav',
    normal: 'Normal',
    high: 'Høj',
    urgent: 'Haster',
};

const priorityClass: Record<Ticket['priority'], string> = {
    low: 'bg-blue-50 text-blue-700',
    normal: 'bg-blue-100 text-blue-800',
    high: 'bg-orange-100 text-orange-800',
    urgent: 'bg-red-100 text-red-800',
};

export default function SupportIndex({ tickets }: { tickets: Ticket[] }) {
    const [page, setPage] = useState(1);
    const [open, setOpen] = useState(false);

    const { data, setData, post, processing, errors, reset } = useForm({
        subject: '',
        message: '',
        priority: 'normal',
    });

    const openCount = tickets.filter((t) => t.status === 'open' || t.status === 'waiting').length;
    const resolvedCount = tickets.filter((t) => t.status === 'solved' || t.status === 'closed').length;

    const totalPages = Math.max(1, Math.ceil(tickets.length / PAGE_SIZE));
    const paginated = tickets.slice((page - 1) * PAGE_SIZE, page * PAGE_SIZE);

    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        post('/support', {
            onSuccess: () => {
                reset();
                setOpen(false);
            },
        });
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Support" />

            <div className="px-4 py-6 sm:px-6">
                <div className="mb-6 flex items-center justify-between">
                    <Heading title="Support" description="Administrer supporttickets til CRM-teamet." />
                    <Button onClick={() => setOpen(true)}>
                        <Plus className="mr-2 h-4 w-4" />
                        Ny ticket
                    </Button>
                </div>

                {/* Stats */}
                <div className="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-2">
                    <div className="rounded-lg border bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                        <p className="text-sm text-gray-500 dark:text-gray-400">Åbne tickets</p>
                        <p className="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{openCount}</p>
                    </div>
                    <div className="rounded-lg border bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                        <p className="text-sm text-gray-500 dark:text-gray-400">Løste tickets</p>
                        <p className="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{resolvedCount}</p>
                    </div>
                </div>

                {/* Ticket list */}
                <div className="overflow-hidden rounded-lg border bg-white dark:border-gray-700 dark:bg-gray-800">
                    {paginated.length === 0 ? (
                        <div className="flex flex-col items-center justify-center py-16 text-center text-gray-400">
                            <LifeBuoy className="mb-3 h-10 w-10 opacity-40" />
                            <p className="text-sm">Ingen tickets endnu.</p>
                            <p className="text-xs">Klik på &ldquo;Ny ticket&rdquo; for at oprette en.</p>
                        </div>
                    ) : (
                        <table className="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead className="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Emne
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Prioritet
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Status
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Oprettet
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-gray-100 dark:divide-gray-700">
                                {paginated.map((ticket) => (
                                    <tr
                                        key={ticket.id}
                                        className="cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/40"
                                        onClick={() => router.visit(`/support/${ticket.id}`)}
                                    >
                                        <td className="px-6 py-4">
                                            <span className="font-medium text-gray-900 dark:text-white">
                                                {ticket.subject}
                                            </span>
                                        </td>
                                        <td className="px-6 py-4">
                                            <Badge className={priorityClass[ticket.priority]}>
                                                {priorityLabel[ticket.priority]}
                                            </Badge>
                                        </td>
                                        <td className="px-6 py-4">
                                            <Badge className={statusClass[ticket.status]}>
                                                {statusLabel[ticket.status]}
                                            </Badge>
                                        </td>
                                        <td className="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {new Date(ticket.createdAt).toLocaleDateString('da-DK', {
                                                day: 'numeric',
                                                month: 'short',
                                                year: 'numeric',
                                            })}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    )}
                </div>

                {/* Pagination */}
                {totalPages > 1 && (
                    <div className="mt-4 flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                        <span>
                            Viser {(page - 1) * PAGE_SIZE + 1}–{Math.min(page * PAGE_SIZE, tickets.length)} af{' '}
                            {tickets.length}
                        </span>
                        <div className="flex gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                disabled={page === 1}
                                onClick={() => setPage((p) => p - 1)}
                            >
                                <ChevronLeft className="h-4 w-4" />
                                Forrige
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                disabled={page === totalPages}
                                onClick={() => setPage((p) => p + 1)}
                            >
                                Næste
                                <ChevronRight className="h-4 w-4" />
                            </Button>
                        </div>
                    </div>
                )}
            </div>

            {/* New ticket dialog */}
            <Dialog open={open} onOpenChange={setOpen}>
                <DialogContent className="sm:max-w-lg">
                    <DialogHeader>
                        <DialogTitle>Ny ticket</DialogTitle>
                    </DialogHeader>
                    <form onSubmit={handleSubmit} className="space-y-4">
                        <div className="space-y-1">
                            <Label htmlFor="subject">Emne</Label>
                            <Input
                                id="subject"
                                value={data.subject}
                                onChange={(e) => setData('subject', e.target.value)}
                                placeholder="Kort beskrivelse af problemet"
                            />
                            {errors.subject && <p className="text-sm text-red-500">{errors.subject}</p>}
                        </div>

                        <div className="space-y-1">
                            <Label htmlFor="message">Besked</Label>
                            <Textarea
                                id="message"
                                rows={4}
                                value={data.message}
                                onChange={(e) => setData('message', e.target.value)}
                                placeholder="Beskriv problemet eller spørgsmålet i detaljer..."
                            />
                            {errors.message && <p className="text-sm text-red-500">{errors.message}</p>}
                        </div>

                        <div className="space-y-1">
                            <Label htmlFor="priority">Prioritet</Label>
                            <Select value={data.priority} onValueChange={(v) => setData('priority', v)}>
                                <SelectTrigger id="priority">
                                    <SelectValue />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="low">Lav</SelectItem>
                                    <SelectItem value="normal">Normal</SelectItem>
                                    <SelectItem value="high">Høj</SelectItem>
                                    <SelectItem value="urgent">Haster</SelectItem>
                                </SelectContent>
                            </Select>
                            {errors.priority && <p className="text-sm text-red-500">{errors.priority}</p>}
                        </div>

                        <DialogFooter>
                            <Button type="button" variant="outline" onClick={() => setOpen(false)}>
                                Annuller
                            </Button>
                            <Button type="submit" disabled={processing}>
                                {processing ? 'Opretter...' : 'Opret ticket'}
                            </Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </AppLayout>
    );
}
