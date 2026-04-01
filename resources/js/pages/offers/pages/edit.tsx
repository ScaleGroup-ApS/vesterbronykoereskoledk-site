import { Head, useForm, Form, router, usePoll } from '@inertiajs/react';
import { CheckCircle, FileText, Loader2, Plus, Trash2, Upload } from 'lucide-react';
import { useRef, useState } from 'react';
import { edit as editOffer, index as offersIndex } from '@/actions/App/Http/Controllers/Offers/OfferController';
import {
    index as modulesIndex,
} from '@/actions/App/Http/Controllers/Offers/OfferModuleController';
import {
    store as storeBanner,
    show as showBanner,
    destroy as destroyBanner,
} from '@/actions/App/Http/Controllers/Offers/OfferPageBannerController';
import {
    update as updatePage,
} from '@/actions/App/Http/Controllers/Offers/OfferPageController';
import {
    store as storeAttachment,
    show as showAttachment,
    destroy as destroyAttachment,
} from '@/actions/App/Http/Controllers/Offers/OfferPageMediaController';
import {
    store as storeQuestion,
    destroy as destroyQuestion,
} from '@/actions/App/Http/Controllers/Offers/OfferPageQuizController';
import {
    store as storeVideo,
    show as showVideo,
    destroy as destroyVideo,
} from '@/actions/App/Http/Controllers/Offers/OfferPageVideoController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';

type Offer = { id: number; name: string; slug: string };
type Module = { id: number; title: string };
type AttachmentMedia = { id: number; name: string; file_name: string; mime_type: string; size: string };
type BannerMedia = { id: number; file_name: string; size: string };
type VideoMedia = { id: number; file_name: string; size: string; processing: boolean; thumbnail_url: string | null };
type QuizQuestion = {
    id: number;
    question: string;
    options: string[];
    correct_option: number;
    explanation: string | null;
    sort_order: number;
};
type Page = {
    id: number;
    title: string;
    body: string | null;
    quiz_questions: QuizQuestion[];
    attachments: AttachmentMedia[];
};

export default function OfferPageEdit({
    offer,
    module,
    page,
    banner,
    video,
}: {
    offer: Offer;
    module: Module;
    page: Page;
    banner: BannerMedia | null;
    video: VideoMedia | null;
}) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Tilbud', href: offersIndex().url },
        { title: offer.name, href: editOffer(offer).url },
        { title: 'Moduler & sider', href: modulesIndex({ offer }).url },
        { title: page.title, href: '#' },
    ];

    const form = useForm({
        title: page.title,
        body: page.body ?? '',
    });

    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        form.submit(updatePage({ offer, module, page }));
    }

    function confirmDestroyQuestion(question: QuizQuestion) {
        if (confirm('Slet dette spørgsmål?')) {
            router.delete(destroyQuestion({ offer, module, page, question }).url);
        }
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Rediger side — ${page.title}`} />

            <div className="flex h-full flex-1 flex-col gap-8 rounded-xl p-4">
                <Heading title="Rediger side" />

                {/* ── Main fields ──────────────────────────────────────────── */}
                <form onSubmit={handleSubmit} className="max-w-2xl space-y-6">
                    <div className="grid gap-2">
                        <Label htmlFor="title">Titel</Label>
                        <Input
                            id="title"
                            value={form.data.title}
                            onChange={(e) => form.setData('title', e.target.value)}
                            required
                        />
                        <InputError message={form.errors.title} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="body">Indhold (HTML)</Label>
                        <textarea
                            id="body"
                            value={form.data.body}
                            onChange={(e) => form.setData('body', e.target.value)}
                            rows={8}
                            className="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm font-mono"
                        />
                        <InputError message={form.errors.body} />
                    </div>

                    <Button disabled={form.processing}>Gem ændringer</Button>
                </form>

                {/* ── Banner ───────────────────────────────────────────────── */}
                <div className="max-w-2xl">
                    <h2 className="mb-4 text-lg font-semibold">Bannerbillede</h2>
                    <BannerSection offer={offer} module={module} page={page} banner={banner} />
                </div>

                {/* ── Video ────────────────────────────────────────────────── */}
                <div className="max-w-2xl">
                    <h2 className="mb-4 text-lg font-semibold">Video</h2>
                    <VideoSection offer={offer} module={module} page={page} video={video} />
                </div>

                {/* ── Attachments ──────────────────────────────────────────── */}
                <div className="max-w-2xl">
                    <h2 className="mb-4 text-lg font-semibold">Vedhæftede filer</h2>
                    <AttachmentsSection offer={offer} module={module} page={page} attachments={page.attachments} />
                </div>

                {/* ── Quiz questions ───────────────────────────────────────── */}
                <div className="max-w-2xl">
                    <h2 className="mb-4 text-lg font-semibold">Quiz-spørgsmål</h2>

                    {page.quiz_questions.length === 0 ? (
                        <p className="mb-4 text-sm text-muted-foreground">Ingen spørgsmål endnu.</p>
                    ) : (
                        <div className="mb-6 space-y-3">
                            {page.quiz_questions.map((question, i) => (
                                <div key={question.id} className="rounded-xl border p-4">
                                    <div className="flex items-start justify-between gap-2">
                                        <div className="flex-1">
                                            <p className="font-medium text-sm">{i + 1}. {question.question}</p>
                                            <ul className="mt-2 space-y-1">
                                                {question.options.map((opt, idx) => (
                                                    <li
                                                        key={idx}
                                                        className={`text-sm pl-2 ${idx === question.correct_option ? 'text-green-600 font-medium' : 'text-muted-foreground'}`}
                                                    >
                                                        {idx === question.correct_option ? '✓ ' : '• '}{opt}
                                                    </li>
                                                ))}
                                            </ul>
                                            {question.explanation && (
                                                <p className="mt-2 text-xs text-muted-foreground italic">{question.explanation}</p>
                                            )}
                                        </div>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            onClick={() => confirmDestroyQuestion(question)}
                                            className="text-muted-foreground hover:text-destructive shrink-0"
                                        >
                                            <Trash2 className="size-4" />
                                        </Button>
                                    </div>
                                </div>
                            ))}
                        </div>
                    )}

                    <AddQuizQuestionForm offer={offer} module={module} page={page} />
                </div>
            </div>
        </AppLayout>
    );
}

function BannerSection({
    offer,
    module,
    page,
    banner,
}: {
    offer: Offer;
    module: Module;
    page: Page;
    banner: BannerMedia | null;
}) {
    const inputRef = useRef<HTMLInputElement>(null);
    const [uploading, setUploading] = useState(false);

    function handleUpload(e: React.ChangeEvent<HTMLInputElement>) {
        const file = e.target.files?.[0];
        if (!file) return;
        setUploading(true);
        router.post(
            storeBanner({ offer, module, page }).url,
            { file },
            {
                forceFormData: true,
                preserveScroll: true,
                onFinish: () => {
                    if (inputRef.current) inputRef.current.value = '';
                    setUploading(false);
                },
            },
        );
    }

    function handleDelete() {
        if (!confirm('Slet bannerbilledet?')) return;
        router.delete(destroyBanner({ offer, module, page }).url, { preserveScroll: true });
    }

    return (
        <div className="space-y-3">
            {banner && (
                <div className="group relative overflow-hidden rounded-lg border">
                    <img
                        src={showBanner({ offer, module, page }).url}
                        alt={banner.file_name}
                        className="aspect-video w-full object-cover"
                    />
                    <button
                        type="button"
                        onClick={handleDelete}
                        className="absolute right-1.5 top-1.5 rounded-md bg-black/60 p-1 text-white opacity-0 transition-opacity hover:bg-destructive group-hover:opacity-100"
                        aria-label="Slet bannerbillede"
                    >
                        <Trash2 className="size-3.5" />
                    </button>
                    <p className="truncate px-2 py-1 text-xs text-muted-foreground">{banner.file_name} · {banner.size}</p>
                </div>
            )}

            {!banner && (
                <label className="flex cursor-pointer items-center gap-2 rounded-md border border-dashed border-input px-3 py-2 text-sm text-muted-foreground transition-colors hover:border-foreground/30 hover:text-foreground">
                    <Upload className="size-4 shrink-0" />
                    <span>{uploading ? 'Uploader…' : 'Upload bannerbillede'}</span>
                    <input
                        ref={inputRef}
                        type="file"
                        accept="image/jpeg,image/png,image/gif,image/webp"
                        onChange={handleUpload}
                        disabled={uploading}
                        className="sr-only"
                    />
                </label>
            )}
        </div>
    );
}

function VideoSection({
    offer,
    module,
    page,
    video,
}: {
    offer: Offer;
    module: Module;
    page: Page;
    video: VideoMedia | null;
}) {
    const inputRef = useRef<HTMLInputElement>(null);
    const [uploading, setUploading] = useState(false);

    usePoll(3000, { only: ['video'] }, { autoStart: video?.processing === true });

    function handleUpload(e: React.ChangeEvent<HTMLInputElement>) {
        const file = e.target.files?.[0];
        if (!file) return;
        setUploading(true);
        router.post(
            storeVideo({ offer, module, page }).url,
            { file },
            {
                forceFormData: true,
                preserveScroll: true,
                onFinish: () => {
                    if (inputRef.current) inputRef.current.value = '';
                    setUploading(false);
                },
            },
        );
    }

    function handleDelete() {
        if (!confirm('Slet videoen?')) return;
        router.delete(destroyVideo({ offer, module, page }).url, { preserveScroll: true });
    }

    return (
        <div className="space-y-3">
            {video && (
                <div className="overflow-hidden rounded-lg border">
                    {video.processing ? (
                        <div className="flex aspect-video items-center justify-center gap-3 bg-muted text-muted-foreground">
                            <Loader2 className="size-6 animate-spin" />
                            <span className="text-sm">Behandler video…</span>
                        </div>
                    ) : (
                        <video
                            src={showVideo({ offer, module, page }).url}
                            poster={video.thumbnail_url ?? undefined}
                            controls
                            className="aspect-video w-full bg-black"
                        />
                    )}
                    <div className="flex items-center justify-between gap-2 border-t bg-muted/50 px-3 py-2">
                        <div className="flex min-w-0 items-center gap-1.5">
                            {video.processing
                                ? <Loader2 className="size-3.5 shrink-0 animate-spin text-muted-foreground" />
                                : <CheckCircle className="size-3.5 shrink-0 text-green-500" />
                            }
                            <span className="truncate text-xs text-muted-foreground">{video.file_name}</span>
                            <span className="text-xs text-muted-foreground">({video.size})</span>
                        </div>
                        <button
                            type="button"
                            onClick={handleDelete}
                            className="shrink-0 text-muted-foreground transition-colors hover:text-destructive"
                            aria-label="Slet video"
                        >
                            <Trash2 className="size-4" />
                        </button>
                    </div>
                </div>
            )}

            {!video && (
                <label className="flex cursor-pointer items-center gap-2 rounded-md border border-dashed border-input px-3 py-2 text-sm text-muted-foreground transition-colors hover:border-foreground/30 hover:text-foreground">
                    <Upload className="size-4 shrink-0" />
                    <span>{uploading ? 'Uploader…' : 'Upload video'}</span>
                    <input
                        ref={inputRef}
                        type="file"
                        accept="video/mp4,video/quicktime,video/avi,video/webm"
                        onChange={handleUpload}
                        disabled={uploading}
                        className="sr-only"
                    />
                </label>
            )}
        </div>
    );
}

function AttachmentsSection({
    offer,
    module,
    page,
    attachments,
}: {
    offer: Offer;
    module: Module;
    page: Page;
    attachments: AttachmentMedia[];
}) {
    const inputRef = useRef<HTMLInputElement>(null);
    const [uploading, setUploading] = useState(false);

    function handleUpload(e: React.ChangeEvent<HTMLInputElement>) {
        const file = e.target.files?.[0];
        if (!file) return;
        setUploading(true);
        router.post(
            storeAttachment({ offer, module, page }).url,
            { file },
            {
                forceFormData: true,
                preserveScroll: true,
                onFinish: () => {
                    if (inputRef.current) inputRef.current.value = '';
                    setUploading(false);
                },
            },
        );
    }

    function handleDelete(attachment: AttachmentMedia) {
        if (!confirm('Slet denne fil?')) return;
        router.delete(destroyAttachment({ offer, module, page, media: attachment }).url, { preserveScroll: true });
    }

    return (
        <div className="space-y-3">
            {attachments.length > 0 && (
                <ul className="divide-y divide-border rounded-md border text-sm">
                    {attachments.map((att) => (
                        <li key={att.id} className="flex items-center gap-3 px-3 py-2">
                            <FileText className="size-4 shrink-0 text-muted-foreground" />
                            <a
                                href={showAttachment({ offer, module, page, media: att }).url}
                                target="_blank"
                                rel="noopener noreferrer"
                                className="flex-1 truncate hover:underline"
                            >
                                {att.file_name}
                            </a>
                            <span className="shrink-0 text-xs text-muted-foreground">{att.size}</span>
                            <button
                                type="button"
                                onClick={() => handleDelete(att)}
                                className="shrink-0 text-muted-foreground transition-colors hover:text-destructive"
                                aria-label="Slet fil"
                            >
                                <Trash2 className="size-4" />
                            </button>
                        </li>
                    ))}
                </ul>
            )}

            <label className="flex cursor-pointer items-center gap-2 rounded-md border border-dashed border-input px-3 py-2 text-sm text-muted-foreground transition-colors hover:border-foreground/30 hover:text-foreground">
                <Upload className="size-4 shrink-0" />
                <span>{uploading ? 'Uploader…' : 'Upload vedhæftet fil (PDF, Word, ZIP)'}</span>
                <input
                    ref={inputRef}
                    type="file"
                    accept="application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/zip"
                    onChange={handleUpload}
                    disabled={uploading}
                    className="sr-only"
                />
            </label>
        </div>
    );
}

function AddQuizQuestionForm({ offer, module, page }: { offer: Offer; module: Module; page: Page }) {
    return (
        <div className="rounded-xl border p-4">
            <h3 className="mb-4 font-medium">Tilføj spørgsmål</h3>
            <Form {...storeQuestion({ offer, module, page })} method="post" className="space-y-4" resetOnSuccess>
                {({ processing, errors }) => (
                    <>
                        <div className="grid gap-2">
                            <Label htmlFor="question">Spørgsmål</Label>
                            <Input id="question" name="question" required />
                            {errors.question && <p className="text-sm text-destructive">{errors.question}</p>}
                        </div>

                        <OptionsField errors={errors} />

                        <div className="grid gap-2">
                            <Label htmlFor="explanation">Forklaring (valgfri)</Label>
                            <Input id="explanation" name="explanation" />
                        </div>

                        <Button type="submit" disabled={processing}>
                            <Plus className="size-4 mr-1" />
                            Tilføj spørgsmål
                        </Button>
                    </>
                )}
            </Form>
        </div>
    );
}

function OptionsField({ errors }: { errors: Record<string, string> }) {
    return (
        <div className="grid gap-2">
            <Label>Svarmuligheder (min. 2)</Label>
            {[0, 1, 2, 3].map((idx) => (
                <div key={idx} className="flex items-center gap-2">
                    <input
                        type="text"
                        name={`options[${idx}]`}
                        placeholder={`Mulighed ${idx + 1}${idx < 2 ? ' (påkrævet)' : ' (valgfri)'}`}
                        required={idx < 2}
                        className="flex h-9 flex-1 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm"
                    />
                    <label className="flex items-center gap-1 text-sm whitespace-nowrap">
                        <input type="radio" name="correct_option" value={idx} required={idx === 0} />
                        Korrekt
                    </label>
                </div>
            ))}
            {errors['options'] && <p className="text-sm text-destructive">{errors['options']}</p>}
            {errors['correct_option'] && <p className="text-sm text-destructive">{errors['correct_option']}</p>}
        </div>
    );
}
