import { Head, Form, Link } from '@inertiajs/react';
import { useState, useEffect, useRef } from 'react';
import { CheckCircle, Circle, ChevronLeft, ChevronRight, FileDown } from 'lucide-react';
import {
    show as showPage,
    markComplete,
} from '@/actions/App/Http/Controllers/Student/StudentLearnController';
import { store as storeQuizAttempt } from '@/actions/App/Http/Controllers/Student/StudentQuizAttemptController';
import { Button } from '@/components/ui/button';
import StudentLayout from '@/layouts/student-layout';
import type { BreadcrumbItem } from '@/types';

type Offer = { id: number; name: string };

type ModuleRef = { id: number; title: string; sort_order: number };

type PageRef = {
    id: number;
    title: string;
    sort_order: number;
};

type ModuleWithPages = ModuleRef & { pages: PageRef[] };

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
    url: string;
};

type ImageMedia = {
    id: number;
    url: string;
    file_name: string;
};

type VideoMedia = {
    id: number;
    url: string;
    file_name: string;
    thumbnail_url: string | null;
};

type Page = {
    id: number;
    title: string;
    body: string | null;
    images: ImageMedia[];
    videos: VideoMedia[];
    quiz_questions: QuizQuestion[];
    attachments: Attachment[];
};

type QuizAttempt = {
    answers: number[];
    score: number;
    total: number;
    attempted_at: string;
};

type Props = {
    offer: Offer;
    module: ModuleRef;
    modules: ModuleWithPages[];
    page: Page;
    completedPageIds: number[];
    latestQuizAttempt: QuizAttempt | null;
    prevPage: PageRef | null;
    nextPage: PageRef | null;
};

export default function StudentLearnShow({
    offer,
    module,
    modules,
    page,
    completedPageIds,
    latestQuizAttempt,
    prevPage,
    nextPage,
}: Props) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: offer.name, href: '#' },
        { title: page.title, href: '#' },
    ];

    const isCompleted = completedPageIds.includes(page.id);

    return (
        <StudentLayout breadcrumbs={breadcrumbs}>
            <Head title={`${page.title} — ${offer.name}`} />

            <div className="flex h-full flex-1">
                {/* Sidebar TOC */}
                <aside className="hidden w-64 shrink-0 border-r lg:block">
                    <div className="sticky top-0 overflow-y-auto p-4">
                        <p className="mb-3 text-xs font-semibold uppercase tracking-wide text-muted-foreground">Indhold</p>
                        <nav className="space-y-4">
                            {modules.map((mod) => (
                                <div key={mod.id}>
                                    <p className="mb-1 text-sm font-medium">{mod.title}</p>
                                    <ul className="space-y-0.5 pl-2">
                                        {mod.pages.map((p) => {
                                            const done = completedPageIds.includes(p.id);
                                            const isCurrent = p.id === page.id;

                                            const resolvedModule = modules.find((m) =>
                                                m.pages.some((mp) => mp.id === p.id)
                                            );

                                            return (
                                                <li key={p.id}>
                                                    <Link
                                                        href={resolvedModule ? showPage({ offer, module: resolvedModule, page: p }).url : '#'}
                                                        className={`flex items-center gap-2 rounded px-2 py-1 text-sm transition-colors ${
                                                            isCurrent
                                                                ? 'bg-accent font-medium'
                                                                : 'hover:bg-accent/50'
                                                        }`}
                                                    >
                                                        {done ? (
                                                            <CheckCircle className="size-3.5 shrink-0 text-green-500" />
                                                        ) : (
                                                            <Circle className="size-3.5 shrink-0 text-muted-foreground" />
                                                        )}
                                                        <span className="truncate">{p.title}</span>
                                                    </Link>
                                                </li>
                                            );
                                        })}
                                    </ul>
                                </div>
                            ))}
                        </nav>
                    </div>
                </aside>

                {/* Main content */}
                <main className="flex-1 overflow-y-auto p-6">
                    <div className="mx-auto max-w-3xl space-y-8">
                        <div>
                            <p className="text-sm text-muted-foreground">{module.title}</p>
                            <h1 className="mt-1 text-2xl font-bold">{page.title}</h1>
                        </div>

                        {/* Images carousel */}
                        {page.images.length > 0 && (
                            <MediaCarousel>
                                {page.images.map((img) => (
                                    <img
                                        key={img.id}
                                        src={img.url}
                                        alt={img.file_name}
                                        className="h-full w-full object-contain"
                                    />
                                ))}
                            </MediaCarousel>
                        )}

                        {/* Videos carousel */}
                        {page.videos.length > 0 && (
                            <MediaCarousel>
                                {page.videos.map((vid) => (
                                    <video
                                        key={vid.id}
                                        src={vid.url}
                                        poster={vid.thumbnail_url ?? undefined}
                                        controls
                                        className="h-full w-full bg-black"
                                    />
                                ))}
                            </MediaCarousel>
                        )}

                        {/* Body */}
                        {page.body && (
                            <div
                                className="prose prose-sm max-w-none dark:prose-invert"
                                dangerouslySetInnerHTML={{ __html: page.body }}
                            />
                        )}

                        {/* Attachments */}
                        {page.attachments.length > 0 && (
                            <div>
                                <h2 className="mb-3 font-semibold">Filer</h2>
                                <ul className="space-y-2">
                                    {page.attachments.map((att) => (
                                        <li key={att.id}>
                                            <a
                                                href={att.url}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                className="flex items-center gap-2 rounded-lg border px-4 py-3 text-sm hover:bg-accent transition-colors"
                                            >
                                                <FileDown className="size-4 shrink-0 text-muted-foreground" />
                                                <span className="flex-1 truncate">{att.file_name}</span>
                                                <span className="text-xs text-muted-foreground">{att.size}</span>
                                            </a>
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        )}

                        {/* Quiz */}
                        {page.quiz_questions.length > 0 && (
                            <LearnQuiz
                                offer={offer}
                                module={module}
                                page={page}
                                latestQuizAttempt={latestQuizAttempt}
                            />
                        )}

                        {/* Navigation */}
                        <div className="flex items-center justify-between border-t pt-6">
                            <div>
                                {prevPage ? (
                                    <Button variant="outline" asChild>
                                        <Link href={(() => {
                                            const m = modules.find((m) => m.pages.some((p) => p.id === prevPage.id));
                                            return m ? showPage({ offer, module: m, page: prevPage }).url : '#';
                                        })()}>
                                            <ChevronLeft className="size-4 mr-1" />
                                            Forrige
                                        </Link>
                                    </Button>
                                ) : (
                                    <div />
                                )}
                            </div>

                            <Form {...markComplete.form({ offer, module, page })}>
                                {({ processing }) => (
                                    <Button type="submit" disabled={processing || isCompleted}>
                                        {isCompleted ? (
                                            <>
                                                <CheckCircle className="size-4 mr-2 text-green-500" />
                                                Gennemført
                                            </>
                                        ) : (
                                            <>
                                                {nextPage ? 'Gennemfør & næste' : 'Markér som gennemført'}
                                                {nextPage && <ChevronRight className="size-4 ml-1" />}
                                            </>
                                        )}
                                    </Button>
                                )}
                            </Form>
                        </div>
                    </div>
                </main>
            </div>
        </StudentLayout>
    );
}

function MediaCarousel({ children }: { children: React.ReactNode[] }) {
    const [current, setCurrent] = useState(0);
    const trackRef = useRef<HTMLDivElement>(null);
    const count = children.length;

    function goTo(index: number) {
        const next = Math.max(0, Math.min(index, count - 1));
        setCurrent(next);
        trackRef.current?.children[next]?.scrollIntoView({ behavior: 'smooth', inline: 'start', block: 'nearest' });
    }

    if (count === 1) {
        return (
            <div className="overflow-hidden rounded-xl border aspect-video bg-black">
                {children[0]}
            </div>
        );
    }

    return (
        <div className="space-y-2">
            <div className="relative overflow-hidden rounded-xl border aspect-video bg-black">
                <div
                    ref={trackRef}
                    className="flex h-full overflow-x-hidden"
                    style={{ scrollSnapType: 'x mandatory' }}
                >
                    {children.map((child, i) => (
                        <div
                            key={i}
                            className="h-full w-full shrink-0"
                            style={{ scrollSnapAlign: 'start' }}
                        >
                            {child}
                        </div>
                    ))}
                </div>

                <button
                    type="button"
                    onClick={() => goTo(current - 1)}
                    disabled={current === 0}
                    className="absolute left-2 top-1/2 -translate-y-1/2 rounded-full bg-black/60 p-1.5 text-white disabled:opacity-30 hover:bg-black/80 transition-colors"
                    aria-label="Forrige"
                >
                    <ChevronLeft className="size-5" />
                </button>

                <button
                    type="button"
                    onClick={() => goTo(current + 1)}
                    disabled={current === count - 1}
                    className="absolute right-2 top-1/2 -translate-y-1/2 rounded-full bg-black/60 p-1.5 text-white disabled:opacity-30 hover:bg-black/80 transition-colors"
                    aria-label="Næste"
                >
                    <ChevronRight className="size-5" />
                </button>
            </div>

            {/* Dot indicators */}
            <div className="flex justify-center gap-1.5">
                {children.map((_, i) => (
                    <button
                        key={i}
                        type="button"
                        onClick={() => goTo(i)}
                        className={`h-1.5 rounded-full transition-all ${
                            i === current ? 'w-4 bg-foreground' : 'w-1.5 bg-muted-foreground/40'
                        }`}
                        aria-label={`Gå til ${i + 1}`}
                    />
                ))}
            </div>
        </div>
    );
}

function LearnQuiz({
    offer,
    module,
    page,
    latestQuizAttempt,
}: {
    offer: Offer;
    module: ModuleRef;
    page: Page;
    latestQuizAttempt: QuizAttempt | null;
}) {
    const [shownAttempt, setShownAttempt] = useState(latestQuizAttempt);
    const hasAttempt = shownAttempt !== null;

    useEffect(() => {
        setShownAttempt(latestQuizAttempt);
    }, [latestQuizAttempt]);

    return (
        <div className="rounded-xl border p-6">
            <h2 className="mb-4 font-semibold">Quiz</h2>

            {hasAttempt && (
                <div className="mb-6 rounded-lg bg-muted px-4 py-3 text-sm">
                    <p className="font-medium">
                        Dit resultat: {shownAttempt.score}/{shownAttempt.total} korrekte
                    </p>
                </div>
            )}

            <Form {...storeQuizAttempt.form({ offer, module, page })} className="space-y-6">
                {({ processing }) => (
                    <>
                        {page.quiz_questions.map((question, qi) => {
                            const selectedAnswer = hasAttempt ? shownAttempt.answers[qi] : undefined;
                            const isCorrect = selectedAnswer === question.correct_option;

                            return (
                                <div key={question.id} className="space-y-3">
                                    <p className="font-medium text-sm">
                                        {qi + 1}. {question.question}
                                    </p>
                                    <div className="space-y-2">
                                        {question.options.map((opt, oi) => {
                                            const wasSelected = hasAttempt && shownAttempt.answers[qi] === oi;
                                            const isCorrectOpt = oi === question.correct_option;

                                            let optClass = 'flex items-center gap-3 rounded-lg border px-4 py-3 text-sm';
                                            if (hasAttempt) {
                                                if (isCorrectOpt) {
                                                    optClass += ' border-green-500 bg-green-50 dark:bg-green-950/20';
                                                } else if (wasSelected && !isCorrectOpt) {
                                                    optClass += ' border-red-400 bg-red-50 dark:bg-red-950/20';
                                                }
                                            }

                                            return (
                                                <label key={oi} className={optClass}>
                                                    <input
                                                        type="radio"
                                                        name={`answers[${qi}]`}
                                                        value={oi}
                                                        defaultChecked={wasSelected}
                                                        required={!hasAttempt && qi === 0}
                                                        disabled={hasAttempt}
                                                        className="shrink-0"
                                                    />
                                                    <span>{opt}</span>
                                                </label>
                                            );
                                        })}
                                    </div>
                                    {hasAttempt && question.explanation && !isCorrect && (
                                        <p className="text-sm text-muted-foreground italic pl-1">
                                            💡 {question.explanation}
                                        </p>
                                    )}
                                </div>
                            );
                        })}

                        {!hasAttempt && (
                            <Button type="submit" disabled={processing}>
                                Indsend svar
                            </Button>
                        )}

                        {hasAttempt && (
                            <Button type="button" variant="outline" onClick={() => setShownAttempt(null)}>
                                Prøv igen
                            </Button>
                        )}
                    </>
                )}
            </Form>
        </div>
    );
}
