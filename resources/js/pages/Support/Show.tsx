import { Head, router, useForm } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { useEffect } from 'react';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Textarea } from '@/components/ui/textarea';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';

type Comment = {
    id: number;
    content: string;
    authorType: 'customer' | 'staff' | 'system';
    createdAt: string;
    author?: { name: string } | null;
};

type Thread = {
    id: number;
    comments: Comment[];
};

type Ticket = {
    id: number;
    subject: string;
    status: 'open' | 'waiting' | 'solved' | 'closed';
    priority: 'low' | 'normal' | 'high' | 'urgent';
    createdAt: string;
    origin?: string | null;
    threads: Thread[];
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

export default function SupportShow({ ticket }: { ticket: Ticket }) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Support', href: '/support' },
        { title: ticket.subject, href: `/support/${ticket.id}` },
    ];

    const { data, setData, post, processing, errors, reset } = useForm({ message: '' });

    // Poll for new comments every 5 seconds
    useEffect(() => {
        const interval = setInterval(() => {
            router.reload({ only: ['ticket'] });
        }, 5000);

        return () => clearInterval(interval);
    }, []);

    const allComments = ticket.threads.flatMap((t) => t.comments);
    const [firstComment, ...replyComments] = allComments;

    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        post(`/support/${ticket.id}/comments`, {
            onSuccess: () => reset(),
        });
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={ticket.subject} />

            <div className="px-4 py-6 sm:px-6">
                {/* Back link */}
                <button
                    onClick={() => router.visit('/support')}
                    className="mb-4 flex items-center gap-1 text-sm text-gray-500 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200"
                >
                    <ArrowLeft className="h-4 w-4" />
                    Tilbage til support
                </button>

                {/* Ticket header */}
                <div className="mb-6">
                    <Heading title={ticket.subject} />
                    <div className="mt-2 flex flex-wrap items-center gap-2">
                        <Badge className={statusClass[ticket.status]}>{statusLabel[ticket.status]}</Badge>
                        <Badge className={priorityClass[ticket.priority]}>{priorityLabel[ticket.priority]}</Badge>
                        {ticket.origin && (
                            <Badge className="bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                {ticket.origin}
                            </Badge>
                        )}
                        <span className="text-sm text-gray-400 dark:text-gray-500">
                            Oprettet{' '}
                            {new Date(ticket.createdAt).toLocaleDateString('da-DK', {
                                day: 'numeric',
                                month: 'long',
                                year: 'numeric',
                            })}
                        </span>
                    </div>
                </div>

                {/* Message thread */}
                <div className="space-y-4">
                    {/* Initial message */}
                    {firstComment && (
                        <div className="rounded-lg border bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
                            <div className="mb-2 flex items-center justify-between">
                                <span className="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Oprindelig besked
                                </span>
                                <span className="text-xs text-gray-400">
                                    {new Date(firstComment.createdAt).toLocaleString('da-DK', {
                                        day: 'numeric',
                                        month: 'short',
                                        hour: '2-digit',
                                        minute: '2-digit',
                                    })}
                                </span>
                            </div>
                            <p className="whitespace-pre-wrap text-sm text-gray-800 dark:text-gray-200">
                                {firstComment.content}
                            </p>
                        </div>
                    )}

                    {/* Reply comments */}
                    {replyComments.map((comment) => {
                        const isStaff = comment.authorType === 'staff';
                        const isSystem = comment.authorType === 'system';

                        if (isSystem) {
                            return (
                                <div key={comment.id} className="flex justify-center">
                                    <span className="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                                        {comment.content}
                                    </span>
                                </div>
                            );
                        }

                        return (
                            <div key={comment.id} className={`flex ${isStaff ? 'justify-end' : 'justify-start'}`}>
                                <div
                                    className={`max-w-[80%] rounded-lg px-4 py-3 text-sm ${
                                        isStaff
                                            ? 'bg-blue-600 text-white'
                                            : 'border bg-white text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200'
                                    }`}
                                >
                                    <p className="whitespace-pre-wrap">{comment.content}</p>
                                    <p
                                        className={`mt-1 text-xs ${
                                            isStaff ? 'text-blue-200' : 'text-gray-400 dark:text-gray-500'
                                        }`}
                                    >
                                        {new Date(comment.createdAt).toLocaleString('da-DK', {
                                            day: 'numeric',
                                            month: 'short',
                                            hour: '2-digit',
                                            minute: '2-digit',
                                        })}
                                    </p>
                                </div>
                            </div>
                        );
                    })}
                </div>

                {/* Reply form */}
                {ticket.status !== 'solved' && ticket.status !== 'closed' && (
                    <form onSubmit={handleSubmit} className="mt-6 space-y-3">
                        <Textarea
                            value={data.message}
                            onChange={(e) => setData('message', e.target.value)}
                            placeholder="Skriv et svar..."
                            rows={3}
                        />
                        {errors.message && <p className="text-sm text-red-500">{errors.message}</p>}
                        <div className="flex justify-end">
                            <Button type="submit" disabled={processing}>
                                {processing ? 'Sender...' : 'Send svar'}
                            </Button>
                        </div>
                    </form>
                )}
            </div>
        </AppLayout>
    );
}
