import { Head, usePage } from '@inertiajs/react';
import { router } from '@inertiajs/react';
import { useEventStream } from '@laravel/stream-react';
import axios from 'axios';
import { Plus, Users, X } from 'lucide-react';
import { useCallback, useEffect, useState } from 'react';
import { index as chatIndex } from '@/actions/App/Http/Controllers/Chat/ConversationController';
import { index as messagesIndex, store as messagesStore, stream as messagesStream } from '@/actions/App/Http/Controllers/Chat/MessageController';
import ConversationList from '@/components/chat/conversation-list';
import MessageInput from '@/components/chat/message-input';
import MessageThread from '@/components/chat/message-thread';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';
import type { Conversation, ConversationUser, Message } from '@/types/chat';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Chat', href: chatIndex().url },
];

type PageUser = { id: number; name: string; role: string };

type Props = {
    conversations: Conversation[];
    users: ConversationUser[];
};

// ─── New Conversation Dialog ──────────────────────────────────────────────────

function NewConversationDialog({ users }: { users: ConversationUser[] }) {
    const [open, setOpen] = useState(false);
    const [type, setType] = useState<'direct' | 'group'>('direct');
    const [directUserId, setDirectUserId] = useState('');
    const [groupName, setGroupName] = useState('');
    const [groupUserIds, setGroupUserIds] = useState<number[]>([]);

    function toggleGroupUser(id: number) {
        setGroupUserIds((prev) =>
            prev.includes(id) ? prev.filter((u) => u !== id) : [...prev, id],
        );
    }

    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        if (type === 'direct' && !directUserId) return;
        if (type === 'group' && !groupName.trim()) return;

        const payload =
            type === 'direct'
                ? { user_id: directUserId }
                : { name: groupName.trim(), user_ids: groupUserIds };

        router.post(chatIndex().url, payload, {
            onSuccess: () => {
                setOpen(false);
                setDirectUserId('');
                setGroupName('');
                setGroupUserIds([]);
            },
        });
    }

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <Button size="sm" variant="ghost" className="w-full justify-start gap-2 px-4">
                    <Plus className="size-4" />
                    Ny samtale
                </Button>
            </DialogTrigger>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Ny samtale</DialogTitle>
                </DialogHeader>
                <form onSubmit={handleSubmit} className="flex flex-col gap-4">
                    <div className="flex rounded-md border">
                        <button
                            type="button"
                            onClick={() => setType('direct')}
                            className={`flex-1 rounded-l-md px-3 py-2 text-sm transition-colors ${type === 'direct' ? 'bg-primary text-primary-foreground' : 'hover:bg-muted'}`}
                        >
                            Direkte
                        </button>
                        <button
                            type="button"
                            onClick={() => setType('group')}
                            className={`flex-1 rounded-r-md px-3 py-2 text-sm transition-colors ${type === 'group' ? 'bg-primary text-primary-foreground' : 'hover:bg-muted'}`}
                        >
                            Gruppe
                        </button>
                    </div>

                    {type === 'direct' ? (
                        <div className="flex flex-col gap-2">
                            <Label>Vælg bruger</Label>
                            <select
                                value={directUserId}
                                onChange={(e) => setDirectUserId(e.target.value)}
                                className="rounded-md border bg-background px-3 py-2 text-sm"
                                required
                            >
                                <option value="">Vælg...</option>
                                {users.map((u) => (
                                    <option key={u.id} value={u.id}>
                                        {u.name}
                                    </option>
                                ))}
                            </select>
                        </div>
                    ) : (
                        <>
                            <div className="flex flex-col gap-2">
                                <Label>Gruppenavn</Label>
                                <Input
                                    value={groupName}
                                    onChange={(e) => setGroupName(e.target.value)}
                                    placeholder="Fx. Hold A – teori"
                                    required
                                />
                            </div>
                            <div className="flex flex-col gap-2">
                                <Label>Tilføj medlemmer</Label>
                                <div className="max-h-48 overflow-y-auto rounded-md border">
                                    {users.map((u) => (
                                        <label
                                            key={u.id}
                                            className="flex cursor-pointer items-center gap-2 px-3 py-2 text-sm hover:bg-muted"
                                        >
                                            <input
                                                type="checkbox"
                                                checked={groupUserIds.includes(u.id)}
                                                onChange={() => toggleGroupUser(u.id)}
                                                className="size-4"
                                            />
                                            {u.name}
                                        </label>
                                    ))}
                                </div>
                            </div>
                        </>
                    )}

                    <DialogFooter>
                        <Button type="submit">Opret samtale</Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>
    );
}

// ─── Group Members Panel ──────────────────────────────────────────────────────

function GroupMembersPanel({
    conversation,
    allUsers,
    isAdmin,
}: {
    conversation: Conversation;
    allUsers: ConversationUser[];
    isAdmin: boolean;
}) {
    const [addUserId, setAddUserId] = useState('');
    const memberIds = new Set(conversation.users.map((u) => u.id));
    const nonMembers = allUsers.filter((u) => !memberIds.has(u.id));

    function handleAdd(e: React.FormEvent) {
        e.preventDefault();
        if (!addUserId) return;
        router.post(
            `/chat/${conversation.id}/members`,
            { user_id: addUserId },
            { onSuccess: () => setAddUserId('') },
        );
    }

    function handleRemove(userId: number) {
        router.delete(`/chat/${conversation.id}/members/${userId}`);
    }

    return (
        <aside className="flex w-56 shrink-0 flex-col gap-3 overflow-y-auto border-l p-4">
            <div className="flex items-center gap-2">
                <Users className="size-4 text-muted-foreground" />
                <span className="text-sm font-semibold">Medlemmer</span>
            </div>

            <div className="flex flex-col gap-1">
                {conversation.users.map((member) => (
                    <div key={member.id} className="flex items-center justify-between gap-2">
                        <span className="truncate text-sm">{member.name}</span>
                        {isAdmin && (
                            <button
                                type="button"
                                onClick={() => handleRemove(member.id)}
                                className="shrink-0 text-muted-foreground hover:text-destructive"
                            >
                                <X className="size-3.5" />
                            </button>
                        )}
                    </div>
                ))}
            </div>

            {isAdmin && nonMembers.length > 0 && (
                <form onSubmit={handleAdd} className="flex flex-col gap-2 border-t pt-3">
                    <span className="text-xs font-medium text-muted-foreground">Tilføj bruger</span>
                    <select
                        value={addUserId}
                        onChange={(e) => setAddUserId(e.target.value)}
                        className="rounded-md border bg-background px-2 py-1.5 text-xs"
                    >
                        <option value="">Vælg...</option>
                        {nonMembers.map((u) => (
                            <option key={u.id} value={u.id}>
                                {u.name}
                            </option>
                        ))}
                    </select>
                    <Button type="submit" size="sm" disabled={!addUserId}>
                        Tilføj
                    </Button>
                </form>
            )}
        </aside>
    );
}

// ─── Main Page ────────────────────────────────────────────────────────────────

export default function ChatIndex({ conversations, users }: Props) {
    const { auth } = usePage().props as { auth: { user: PageUser } };
    const isAdmin = auth.user.role === 'admin';

    const [activeId, setActiveId] = useState<number | null>(conversations[0]?.id ?? null);
    const [messages, setMessages] = useState<Message[]>([]);
    const [loadingMessages, setLoadingMessages] = useState(false);

    const activeConversation = conversations.find((c) => c.id === activeId) ?? null;
    const streamUrl = activeId ? messagesStream(activeId).url : '';

    const { close } = useEventStream(streamUrl, {
        eventName: 'message',
        onMessage: (event: MessageEvent) => {
            try {
                const message: Message = JSON.parse(event.data as string);
                setMessages((prev) => [...prev, message]);
            } catch {
                // ignore malformed events
            }
        },
    });

    useEffect(() => {
        if (!activeId) return;

        // eslint-disable-next-line react-hooks/set-state-in-effect
        setLoadingMessages(true);
        setMessages([]);

        axios
            .get<{ data: Message[] }>(messagesIndex(activeId).url)
            .then(({ data }) => {
                setMessages(data.data);
                setLoadingMessages(false);
            });

        return () => close();
    }, [activeId, close]);

    const sendMessage = useCallback(
        async (body: string, files: File[]) => {
            if (!activeId) return;

            const formData = new FormData();
            if (body) formData.append('body', body);
            files.forEach((file) => formData.append('attachments[]', file));

            const { data } = await axios.post<Message>(messagesStore(activeId).url, formData, {
                headers: { 'Content-Type': 'multipart/form-data' },
            });

            setMessages((prev) => [...prev, data]);
        },
        [activeId],
    );

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Chat" />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <Heading title="Chat" description="Samtaler med instruktører og elever" />
                <div className="flex flex-1 overflow-hidden rounded-xl border">
                    {/* Sidebar – conversation list */}
                    <aside className="flex w-64 shrink-0 flex-col overflow-hidden border-r">
                        <NewConversationDialog users={users} />
                        <div className="flex-1 overflow-y-auto">
                            <ConversationList
                                conversations={conversations}
                                activeId={activeId}
                                onSelect={setActiveId}
                            />
                        </div>
                    </aside>

                    {/* Main chat area */}
                    <div className="flex flex-1 overflow-hidden">
                        <div className="flex flex-1 flex-col overflow-hidden">
                            {activeConversation ? (
                                <>
                                    <div className="border-b px-4 py-3">
                                        <span className="font-semibold">{activeConversation.name}</span>
                                        {activeConversation.type === 'group' && (
                                            <span className="ml-2 text-xs text-muted-foreground">
                                                {activeConversation.users.length} medlemmer
                                            </span>
                                        )}
                                    </div>
                                    {loadingMessages ? (
                                        <div className="flex flex-1 items-center justify-center text-sm text-muted-foreground">
                                            Indlæser beskeder...
                                        </div>
                                    ) : (
                                        <MessageThread messages={messages} currentUserId={auth.user.id} />
                                    )}
                                    <MessageInput onSend={sendMessage} />
                                </>
                            ) : (
                                <div className="flex flex-1 items-center justify-center text-sm text-muted-foreground">
                                    Vælg en samtale for at starte.
                                </div>
                            )}
                        </div>

                        {/* Right panel – group members */}
                        {activeConversation?.type === 'group' && (
                            <GroupMembersPanel
                                conversation={activeConversation}
                                allUsers={users}
                                isAdmin={isAdmin}
                            />
                        )}
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
