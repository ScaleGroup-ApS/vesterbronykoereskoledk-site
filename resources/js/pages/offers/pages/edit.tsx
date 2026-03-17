import { Head, useForm, Form, router } from '@inertiajs/react';
import { Plus, Trash2 } from 'lucide-react';
import {
    index as modulesIndex,
} from '@/actions/App/Http/Controllers/Offers/OfferModuleController';
import {
    update as updatePage,
} from '@/actions/App/Http/Controllers/Offers/OfferPageController';
import {
    store as storeQuestion,
    destroy as destroyQuestion,
} from '@/actions/App/Http/Controllers/Offers/OfferPageQuizController';
import { edit as editOffer, index as offersIndex } from '@/actions/App/Http/Controllers/Offers/OfferController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';

type Offer = { id: number; name: string };
type Module = { id: number; title: string };
type QuizQuestion = {
    id: number;
    question: string;
    options: string[];
    correct_option: number;
    explanation: string | null;
    sort_order: number;
};
type Attachment = {
    id: number;
    name: string;
    file_name: string;
    mime_type: string;
    size: string;
};
type Page = {
    id: number;
    title: string;
    body: string | null;
    video_url: string | null;
    quiz_questions: QuizQuestion[];
    attachments: Attachment[];
};

export default function OfferPageEdit({
    offer,
    module,
    page,
}: {
    offer: Offer;
    module: Module;
    page: Page;
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
        video_url: page.video_url ?? '',
        attachment: null as File | null,
    });

    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        form.submit(updatePage({ offer, module, page }), {
            forceFormData: true,
        });
    }

    function confirmDestroyQuestion(question: QuizQuestion) {
        if (confirm('Slet dette spørgsmål?')) {
            router.delete(destroyQuestion({ offer, module, page, question }).url);
        }
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Rediger side — ${page.title}`} />

            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <Heading title="Rediger side" />

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

                    <div className="grid gap-2">
                        <Label htmlFor="video_url">Video URL</Label>
                        <Input
                            id="video_url"
                            type="url"
                            value={form.data.video_url}
                            onChange={(e) => form.setData('video_url', e.target.value)}
                            placeholder="https://www.youtube.com/embed/…"
                        />
                        <InputError message={form.errors.video_url} />
                    </div>

                    <div className="grid gap-2">
                        <Label>Vedhæftning</Label>
                        {page.attachments.length > 0 && (
                            <ul className="mb-2 divide-y divide-border rounded-md border text-sm">
                                {page.attachments.map((att) => (
                                    <li key={att.id} className="flex items-center justify-between px-3 py-2">
                                        <span>{att.file_name} ({att.size})</span>
                                    </li>
                                ))}
                            </ul>
                        )}
                        <input
                            type="file"
                            accept=".pdf,.doc,.docx,.zip"
                            onChange={(e) => form.setData('attachment', e.target.files?.[0] ?? null)}
                            className="text-sm"
                        />
                        <p className="text-xs text-muted-foreground">Upload ny fil for at erstatte eksisterende (pdf, doc, docx, zip — maks. 50 MB)</p>
                        <InputError message={form.errors.attachment} />
                    </div>

                    <Button disabled={form.processing}>Gem ændringer</Button>
                </form>

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
