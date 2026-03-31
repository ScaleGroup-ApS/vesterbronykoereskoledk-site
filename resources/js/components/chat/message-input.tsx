import { Paperclip, SendHorizonal, X } from 'lucide-react';
import type { FormEvent } from 'react';
import { useRef, useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

type Props = {
    onSend: (body: string, files: File[]) => void;
    disabled?: boolean;
};

export default function MessageInput({ onSend, disabled = false }: Props) {
    const [body, setBody] = useState('');
    const [files, setFiles] = useState<File[]>([]);
    const fileInputRef = useRef<HTMLInputElement>(null);

    function handleSubmit(e: FormEvent) {
        e.preventDefault();
        const trimmed = body.trim();
        if (!trimmed && files.length === 0) return;
        onSend(trimmed, files);
        setBody('');
        setFiles([]);
        if (fileInputRef.current) fileInputRef.current.value = '';
    }

    function handleFileChange(e: React.ChangeEvent<HTMLInputElement>) {
        const selected = Array.from(e.target.files ?? []);
        setFiles((prev) => [...prev, ...selected].slice(0, 10));
    }

    function removeFile(index: number) {
        setFiles((prev) => prev.filter((_, i) => i !== index));
    }

    const canSubmit = !disabled && (body.trim().length > 0 || files.length > 0);

    return (
        <div className="border-t">
            {files.length > 0 && (
                <div className="flex flex-wrap gap-2 px-3 pt-3">
                    {files.map((file, i) => (
                        <div
                            key={i}
                            className="flex items-center gap-1 rounded-md border bg-muted px-2 py-1 text-xs"
                        >
                            <span className="max-w-32 truncate">{file.name}</span>
                            <button
                                type="button"
                                onClick={() => removeFile(i)}
                                className="text-muted-foreground hover:text-foreground"
                            >
                                <X className="size-3" />
                            </button>
                        </div>
                    ))}
                </div>
            )}
            <form onSubmit={handleSubmit} className="flex items-center gap-2 p-3">
                <input
                    ref={fileInputRef}
                    type="file"
                    multiple
                    className="hidden"
                    onChange={handleFileChange}
                    disabled={disabled}
                />
                <Button
                    type="button"
                    size="icon"
                    variant="ghost"
                    disabled={disabled}
                    onClick={() => fileInputRef.current?.click()}
                >
                    <Paperclip className="size-4" />
                </Button>
                <Input
                    value={body}
                    onChange={(e) => setBody(e.target.value)}
                    placeholder="Skriv en besked..."
                    disabled={disabled}
                    className="flex-1"
                />
                <Button type="submit" size="icon" disabled={!canSubmit}>
                    <SendHorizonal className="size-4" />
                </Button>
            </form>
        </div>
    );
}
