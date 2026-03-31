export type ConversationUser = {
    id: number;
    name: string;
};

export type LastMessage = {
    body: string | null;
    user_name: string;
    created_at: string;
};

export type Conversation = {
    id: number;
    name: string;
    type: 'direct' | 'group';
    last_message: LastMessage | null;
    unread: number;
    users: ConversationUser[];
};

export type Attachment = {
    id: number;
    name: string;
    mime_type: string;
    size: number;
    url: string;
};

export type Message = {
    id: number;
    body: string | null;
    user: { id: number; name: string };
    created_at: string;
    attachments: Attachment[];
};
