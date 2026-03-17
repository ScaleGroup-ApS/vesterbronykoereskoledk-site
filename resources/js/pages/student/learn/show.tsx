import { Head, Form, Link } from '@inertiajs/react';
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

type Page = {
    id: number;
    title: string;
    body: string | null;
    video_url: string | null;
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

                        {/* Video */}
                        {page.video_url && (
                            <div className="aspect-video overflow-hidden rounded-xl border">
                                <iframe
                                    src={page.video_url}
                                    className="size-full"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowFullScreen
                                />
                            </div>
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

                            <Form {...markComplete({ offer, module, page })} method="post">
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
    const hasAttempt = latestQuizAttempt !== null;

    return (
        <div className="rounded-xl border p-6">
            <h2 className="mb-4 font-semibold">Quiz</h2>

            {hasAttempt && (
                <div className="mb-6 rounded-lg bg-muted px-4 py-3 text-sm">
                    <p className="font-medium">
                        Dit resultat: {latestQuizAttempt.score}/{latestQuizAttempt.total} korrekte
                    </p>
                </div>
            )}

            <Form {...storeQuizAttempt({ offer, module, page })} method="post" className="space-y-6">
                {({ processing }) => (
                    <>
                        {page.quiz_questions.map((question, qi) => {
                            const selectedAnswer = hasAttempt ? latestQuizAttempt.answers[qi] : undefined;
                            const isCorrect = selectedAnswer === question.correct_option;

                            return (
                                <div key={question.id} className="space-y-3">
                                    <p className="font-medium text-sm">
                                        {qi + 1}. {question.question}
                                    </p>
                                    <div className="space-y-2">
                                        {question.options.map((opt, oi) => {
                                            const wasSelected = hasAttempt && latestQuizAttempt.answers[qi] === oi;
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
                                                        required={qi === 0}
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
                            <Form {...storeQuizAttempt({ offer, module, page })} method="post">
                                {({ processing: retryProcessing }) => (
                                    <Button type="submit" variant="outline" disabled={retryProcessing}>
                                        Prøv igen
                                    </Button>
                                )}
                            </Form>
                        )}
                    </>
                )}
            </Form>
        </div>
    );
}
