import { createContext, useContext, useRef, useState } from 'react';
import { router, usePoll } from '@inertiajs/react';
import { CheckCircle, FileText, Loader2, Trash2 } from 'lucide-react';
import { FilePond, registerPlugin } from 'react-filepond';
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import 'filepond/dist/filepond.min.css';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';
import {
    store as storeMedia,
    show as showMedia,
    destroy as destroyMedia,
} from '@/actions/App/Http/Controllers/MediaController';

registerPlugin(FilePondPluginImagePreview, FilePondPluginFileValidateType, FilePondPluginFileValidateSize);

// ── Types ─────────────────────────────────────────────────────────────────────

export type ImageMedia      = { id: number; file_name: string; size: string };
export type VideoMedia      = { id: number; file_name: string; size: string; processing: boolean; thumbnail_url: string | null };
export type AttachmentMedia = { id: number; name: string; file_name: string; mime_type: string; size: string };
export type Aspect          = 'video' | 'square' | 'auto';

// ── Collection registry ───────────────────────────────────────────────────────

const COLLECTION_TYPES: Record<string, string[]> = {
    images:      ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
    video:       ['video/mp4', 'video/quicktime', 'video/avi', 'video/webm'],
    attachments: ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
};

function collectionFor(file: File, allowed: string[]): string {
    return allowed.find((c) => COLLECTION_TYPES[c]?.includes(file.type)) ?? allowed[0];
}

function maxSizeFor(collections: string[]): string {
    if (collections.includes('video'))       return '2000MB';
    if (collections.includes('attachments')) return '50MB';
    return '10MB';
}

// ── Context ───────────────────────────────────────────────────────────────────

type MediaContextType = {
    modelType: string;
    modelId: number;
    upload: (file: File, collection: string, onDone: () => void) => void;
    remove: (media: { id: number }) => void;
};

const MediaContext = createContext<MediaContextType | null>(null);

function useMedia(): MediaContextType {
    const ctx = useContext(MediaContext);
    if (!ctx) throw new Error('Media subcomponents must be used within <Media>');
    return ctx;
}

// ── Root ──────────────────────────────────────────────────────────────────────

function MediaRoot({ modelType, modelId, children }: { modelType: string; modelId: number; children: React.ReactNode }) {
    function upload(file: File, collection: string, onDone: () => void) {
        router.post(
            storeMedia().url,
            { model_type: modelType, model_id: modelId, collection, file },
            { forceFormData: true, preserveScroll: true, onFinish: onDone },
        );
    }

    function remove(media: { id: number }) {
        if (confirm('Er du sikker på, at du vil slette denne fil?')) {
            router.delete(destroyMedia({ media }).url, { preserveScroll: true });
        }
    }

    return (
        <MediaContext.Provider value={{ modelType, modelId, upload, remove }}>
            <div className="space-y-4">{children}</div>
        </MediaContext.Provider>
    );
}

// ── Images ────────────────────────────────────────────────────────────────────

function MediaImages({ items, aspect = 'video' }: { items: ImageMedia[]; aspect?: Aspect }) {
    const { remove } = useMedia();
    if (items.length === 0) return null;

    const aspectClass = aspect === 'video' ? 'aspect-video' : aspect === 'square' ? 'aspect-square' : '';

    return (
        <div className="grid grid-cols-2 gap-3 sm:grid-cols-3">
            {items.map((img) => (
                <div key={img.id} className="group relative overflow-hidden rounded-lg border">
                    <img
                        src={showMedia({ media: img }).url}
                        alt={img.file_name}
                        className={`w-full object-cover ${aspectClass}`}
                    />
                    <button
                        type="button"
                        onClick={() => remove(img)}
                        className="absolute right-1.5 top-1.5 rounded-md bg-black/60 p-1 text-white opacity-0 transition-opacity hover:bg-destructive group-hover:opacity-100"
                        aria-label="Slet billede"
                    >
                        <Trash2 className="size-3.5" />
                    </button>
                    <p className="truncate px-2 py-1 text-xs text-muted-foreground">{img.size}</p>
                </div>
            ))}
        </div>
    );
}

// ── Videos ────────────────────────────────────────────────────────────────────

function MediaVideos({ items }: { items: VideoMedia[] }) {
    const { remove } = useMedia();

    usePoll(3000, { only: ['videos'] }, { autoStart: items.some((v) => v.processing) });

    if (items.length === 0) return null;

    return (
        <div className="space-y-3">
            {items.map((vid) => (
                <div key={vid.id} className="overflow-hidden rounded-lg border">
                    {vid.processing ? (
                        <div className="flex aspect-video items-center justify-center gap-3 bg-muted text-muted-foreground">
                            <Loader2 className="size-6 animate-spin" />
                            <span className="text-sm">Behandler video…</span>
                        </div>
                    ) : (
                        <video
                            src={showMedia({ media: vid }).url}
                            poster={vid.thumbnail_url ?? undefined}
                            controls
                            className="aspect-video w-full bg-black"
                        />
                    )}
                    <div className="flex items-center justify-between gap-2 border-t bg-muted/50 px-3 py-2">
                        <div className="flex min-w-0 items-center gap-1.5">
                            {vid.processing
                                ? <Loader2 className="size-3.5 shrink-0 animate-spin text-muted-foreground" />
                                : <CheckCircle className="size-3.5 shrink-0 text-green-500" />
                            }
                            <span className="truncate text-xs text-muted-foreground">{vid.file_name}</span>
                            <span className="text-xs text-muted-foreground">({vid.size})</span>
                            {vid.processing && <span className="text-xs text-amber-600">Thumbnail genereres…</span>}
                        </div>
                        <button
                            type="button"
                            onClick={() => remove(vid)}
                            className="shrink-0 text-muted-foreground transition-colors hover:text-destructive"
                            aria-label="Slet video"
                        >
                            <Trash2 className="size-4" />
                        </button>
                    </div>
                </div>
            ))}
        </div>
    );
}

// ── Attachments ───────────────────────────────────────────────────────────────

function MediaAttachments({ items }: { items: AttachmentMedia[] }) {
    const { remove } = useMedia();
    if (items.length === 0) return null;

    return (
        <ul className="divide-y divide-border rounded-md border text-sm">
            {items.map((att) => (
                <li key={att.id} className="flex items-center gap-3 px-3 py-2">
                    <FileText className="size-4 shrink-0 text-muted-foreground" />
                    <a
                        href={showMedia({ media: att }).url}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="flex-1 truncate hover:underline"
                    >
                        {att.file_name}
                    </a>
                    <span className="shrink-0 text-xs text-muted-foreground">{att.size}</span>
                    <button
                        type="button"
                        onClick={() => remove(att)}
                        className="shrink-0 text-muted-foreground transition-colors hover:text-destructive"
                        aria-label="Slet fil"
                    >
                        <Trash2 className="size-4" />
                    </button>
                </li>
            ))}
        </ul>
    );
}

// ── Upload ────────────────────────────────────────────────────────────────────

function MediaUpload({ collections = Object.keys(COLLECTION_TYPES) }: { collections?: string[] }) {
    const { upload } = useMedia();
    const pondRef = useRef<FilePond>(null);
    const [uploading, setUploading] = useState(false);

    return (
        <FilePond
            ref={pondRef}
            onaddfile={(_error, fileItem) => {
                const file = fileItem.file as File;
                setUploading(true);
                upload(file, collectionFor(file, collections), () => {
                    pondRef.current?.removeFiles();
                    setUploading(false);
                });
            }}
            allowMultiple={false}
            disabled={uploading}
            acceptedFileTypes={collections.flatMap((c) => COLLECTION_TYPES[c] ?? [])}
            maxFileSize={maxSizeFor(collections)}
            labelIdle='Træk & slip eller <span class="filepond--label-action">vælg fil</span>'
            labelFileTypeNotAllowed="Filtypen er ikke tilladt"
            fileValidateTypeLabelExpectedTypes="Tilladt: billede, video, PDF, Word, ZIP"
            labelMaxFileSizeExceeded="Filen er for stor"
            labelMaxFileSize="Maks: {filesize}"
            credits={false}
        />
    );
}

// ── Compound export ───────────────────────────────────────────────────────────

const Media = Object.assign(MediaRoot, {
    Images:      MediaImages,
    Videos:      MediaVideos,
    Attachments: MediaAttachments,
    Upload:      MediaUpload,
});

export default Media;
