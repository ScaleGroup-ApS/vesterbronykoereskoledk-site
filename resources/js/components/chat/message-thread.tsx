import { FileIcon } from 'lucide-react';
import { useEffect, useRef } from 'react';
import type { Attachment, Message } from '@/types/chat';

type Props = {
    messages: Message[];
    currentUserId: number;
};

function formatBytes(bytes: number): string {
    if (bytes < 1024) return `${bytes} B`;
    if (bytes < 1024 * 1024) return `${(bytes / 1024).toFixed(1)} KB`;
    return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

function AttachmentItem({ attachment }: { attachment: Attachment }) {
    const isImage = attachment.mime_type.startsWith('image/');

    if (isImage) {
        return (
            <a href={attachment.url} target="_blank" rel="noopener noreferrer" className="block">
                <img
                    src={attachment.url}
                    alt={attachment.name}
                    className="max-h-48 max-w-xs rounded-lg object-cover"
                />
            </a>
        );
    }

    return (
        <a
            href={attachment.url}
            target="_blank"
            rel="noopener noreferrer"
            className="flex items-center gap-2 rounded-lg border bg-background/50 px-3 py-2 text-xs hover:bg-background/80"
        >
            <FileIcon className="size-4 shrink-0" />
            <div className="min-w-0">
                <div className="truncate font-medium">{attachment.name}</div>
                <div className="text-muted-foreground">{formatBytes(attachment.size)}</div>
            </div>
        </a>
    );
}

export default function MessageThread({ messages, currentUserId }: Props) {
    const bottomRef = useRef<HTMLDivElement>(null);

    useEffect(() => {
        bottomRef.current?.scrollIntoView({ behavior: 'smooth' });
    }, [messages]);

    return (
        <div className="flex flex-1 flex-col gap-2 overflow-y-auto p-4">
            {messages.length === 0 && (
                <div className="m-auto text-sm text-muted-foreground">Ingen beskeder endnu.</div>
            )}
            {messages.map((message) => {
                const isOwn = message.user.id === currentUserId;
                return (
                    <div key={message.id} className={`flex flex-col gap-0.5 ${isOwn ? 'items-end' : 'items-start'}`}>
                        {!isOwn && (
                            <span className="text-xs text-muted-foreground">{message.user.name}</span>
                        )}
                        <div className={`flex max-w-xs flex-col gap-1 ${isOwn ? 'items-end' : 'items-start'}`}>
                            {message.body && (
                                <div
                                    className={`rounded-xl px-3 py-2 text-sm ${
                                        isOwn ? 'bg-primary text-primary-foreground' : 'bg-muted'
                                    }`}
                                >
                                    {message.body}
                                </div>
                            )}
                            {message.attachments.length > 0 && (
                                <div className="flex flex-col gap-1">
                                    {message.attachments.map((attachment) => (
                                        <AttachmentItem key={attachment.id} attachment={attachment} />
                                    ))}
                                </div>
                            )}
                        </div>
                        <span className="text-xs text-muted-foreground">
                            {new Date(message.created_at).toLocaleTimeString('da-DK', {
                                hour: '2-digit',
                                minute: '2-digit',
                            })}
                        </span>
                    </div>
                );
            })}
            <div ref={bottomRef} />
        </div>
    );
}
