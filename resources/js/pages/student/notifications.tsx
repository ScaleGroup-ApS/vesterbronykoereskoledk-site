import { Head, router } from '@inertiajs/react';
import { Bell, BellOff, CheckCheck, Circle, Mail, MailOpen } from 'lucide-react';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import StudentLayout from '@/layouts/student-layout';
import { notifications } from '@/routes/student';
import { read, readAll } from '@/routes/student/notifications';
import type { BreadcrumbItem } from '@/types';

type Notification = {
    id: string;
    type: string;
    data: Record<string, string>;
    read_at: string | null;
    created_at: string;
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Notifikationer', href: notifications().url },
];

function timeAgo(dateStr: string): string {
    const diff = Date.now() - new Date(dateStr).getTime();
    const minutes = Math.floor(diff / 60000);
    if (minutes < 1) return 'Lige nu';
    if (minutes < 60) return `${minutes} min siden`;
    const hours = Math.floor(minutes / 60);
    if (hours < 24) return `${hours}t siden`;
    const days = Math.floor(hours / 24);
    if (days < 7) return `${days}d siden`;
    return new Date(dateStr).toLocaleDateString('da-DK', { day: 'numeric', month: 'short' });
}

function NotificationItem({ notification }: { notification: Notification }) {
    const isUnread = !notification.read_at;
    const title = notification.data.title ?? notification.data.subject ?? notification.type;
    const body = notification.data.body ?? notification.data.message ?? null;

    const handleClick = () => {
        if (isUnread) {
            router.post(read.url(notification.id), {}, { preserveScroll: true });
        }
    };

    return (
        <button
            type="button"
            onClick={handleClick}
            className={`flex w-full items-start gap-3 px-5 py-4 text-left transition ${
                isUnread ? 'bg-primary/[0.02] hover:bg-primary/5' : 'hover:bg-muted/30'
            }`}
        >
            <div className="mt-0.5 shrink-0">
                {isUnread ? (
                    <div className="flex size-8 items-center justify-center rounded-full bg-primary/10">
                        <Mail className="size-4 text-primary" />
                    </div>
                ) : (
                    <div className="flex size-8 items-center justify-center rounded-full bg-muted">
                        <MailOpen className="size-4 text-muted-foreground" />
                    </div>
                )}
            </div>
            <div className="min-w-0 flex-1">
                <div className="flex items-start justify-between gap-2">
                    <p className={`text-sm ${isUnread ? 'font-semibold' : 'font-medium text-muted-foreground'}`}>
                        {title}
                    </p>
                    <span className="shrink-0 text-xs text-muted-foreground">
                        {timeAgo(notification.created_at)}
                    </span>
                </div>
                {body && (
                    <p className="mt-0.5 line-clamp-2 text-xs text-muted-foreground">{body}</p>
                )}
            </div>
            {isUnread && <Circle className="mt-1.5 size-2 shrink-0 fill-primary text-primary" />}
        </button>
    );
}

export default function StudentNotifications({
    notifications: items,
    unread_count,
}: {
    notifications: Notification[];
    unread_count: number;
}) {
    const handleMarkAllRead = () => {
        router.post(readAll.url(), {}, { preserveScroll: true });
    };

    return (
        <StudentLayout breadcrumbs={breadcrumbs}>
            <Head title="Notifikationer" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4 sm:p-6">
                <div className="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div className="space-y-1">
                        <div className="flex items-center gap-2">
                            <Heading title="Notifikationer" />
                            {unread_count > 0 && (
                                <span className="inline-flex items-center justify-center rounded-full bg-primary px-2 py-0.5 text-xs font-medium text-primary-foreground">
                                    {unread_count}
                                </span>
                            )}
                        </div>
                        <p className="text-sm text-muted-foreground">
                            Hold dig opdateret med beskeder fra din køreskole.
                        </p>
                    </div>
                    {unread_count > 0 && (
                        <Button variant="outline" size="sm" onClick={handleMarkAllRead}>
                            <CheckCheck className="size-4" />
                            Markér alle som læst
                        </Button>
                    )}
                </div>

                {items.length === 0 ? (
                    <div className="flex flex-col items-center gap-3 rounded-2xl border border-dashed py-10 text-center">
                        <BellOff className="size-10 text-muted-foreground/30" />
                        <div>
                            <p className="font-medium text-muted-foreground">Ingen notifikationer</p>
                            <p className="mt-1 text-sm text-muted-foreground/70">
                                Du har ingen notifikationer endnu. De vil dukke op her.
                            </p>
                        </div>
                    </div>
                ) : (
                    <div className="divide-y rounded-xl border shadow-sm">
                        {items.map((n) => (
                            <NotificationItem key={n.id} notification={n} />
                        ))}
                    </div>
                )}
            </div>
        </StudentLayout>
    );
}
