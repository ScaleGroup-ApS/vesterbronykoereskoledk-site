import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, CheckCircle, Clock, RotateCcw, XCircle } from 'lucide-react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import StudentLayout from '@/layouts/student-layout';
import { theoryPractice } from '@/routes/student';
import { start } from '@/routes/student/theory-practice';
import type { BreadcrumbItem } from '@/types';

type AttemptResult = {
    id: number;
    score: number;
    total: number;
    percentage: number;
    duration_seconds: number;
    attempted_at: string;
};

type QuestionResult = {
    id: number;
    question: string;
    options: string[];
    correct_option: number;
    explanation: string | null;
    student_answer: number | null;
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Teoritræning', href: theoryPractice().url },
    { title: 'Resultat', href: '#' },
];

function formatDuration(seconds: number): string {
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    return `${m}:${s.toString().padStart(2, '0')}`;
}

export default function TheoryPracticeResult({
    attempt,
    questions,
}: {
    attempt: AttemptResult;
    questions: QuestionResult[];
}) {
    const passed = attempt.percentage >= 90;

    return (
        <StudentLayout breadcrumbs={breadcrumbs}>
            <Head title={`Resultat – ${attempt.percentage}%`} />
            <div className="flex h-full flex-1 flex-col gap-6 p-4 sm:p-6">
                {/* Result summary */}
                <div className={`flex flex-col items-center gap-4 rounded-2xl border p-8 text-center ${passed ? 'border-green-500/30 bg-green-500/5' : 'border-amber-500/30 bg-amber-500/5'}`}>
                    <div className={`flex size-20 items-center justify-center rounded-full ${passed ? 'bg-green-500/10' : 'bg-amber-500/10'}`}>
                        <span className={`text-3xl font-bold ${passed ? 'text-green-600' : 'text-amber-600'}`}>
                            {attempt.percentage}%
                        </span>
                    </div>
                    <div>
                        <h1 className="text-xl font-bold">
                            {passed ? 'Bestået!' : 'Ikke bestået'}
                        </h1>
                        <p className="mt-1 text-sm text-muted-foreground">
                            {attempt.score} ud af {attempt.total} rigtige svar
                        </p>
                    </div>
                    <div className="flex items-center gap-1 text-sm text-muted-foreground">
                        <Clock className="size-3.5" />
                        Tid brugt: {formatDuration(attempt.duration_seconds)}
                    </div>
                    <div className="flex gap-3">
                        <Button variant="outline" asChild>
                            <Link href={theoryPractice().url}>
                                <ArrowLeft className="size-4" />
                                Tilbage
                            </Link>
                        </Button>
                        <Button asChild>
                            <Link href={start().url}>
                                <RotateCcw className="size-4" />
                                Prøv igen
                            </Link>
                        </Button>
                    </div>
                </div>

                {/* Question review */}
                <section className="space-y-4">
                    <h2 className="text-base font-medium">Gennemgang af svar</h2>
                    <div className="space-y-4">
                        {questions.map((q, i) => {
                            const isCorrect = q.student_answer === q.correct_option;
                            return (
                                <div key={q.id} className="rounded-xl border shadow-sm">
                                    <div className="flex items-start gap-3 px-5 py-4">
                                        <span className="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-full bg-muted text-xs font-medium">
                                            {i + 1}
                                        </span>
                                        <div className="flex-1 space-y-3">
                                            <div className="flex items-start justify-between gap-2">
                                                <p className="text-sm font-medium">{q.question}</p>
                                                {isCorrect ? (
                                                    <Badge variant="default" className="shrink-0 bg-green-500">
                                                        <CheckCircle className="mr-1 size-3" />
                                                        Rigtigt
                                                    </Badge>
                                                ) : (
                                                    <Badge variant="destructive" className="shrink-0">
                                                        <XCircle className="mr-1 size-3" />
                                                        Forkert
                                                    </Badge>
                                                )}
                                            </div>
                                            <div className="space-y-1.5">
                                                {q.options.map((opt, optIdx) => {
                                                    const isStudentAnswer = q.student_answer === optIdx;
                                                    const isCorrectAnswer = q.correct_option === optIdx;
                                                    let classes = 'rounded-lg border px-4 py-2.5 text-sm';
                                                    if (isCorrectAnswer) {
                                                        classes += ' border-green-500/30 bg-green-500/5 text-green-700 dark:text-green-400';
                                                    } else if (isStudentAnswer && !isCorrect) {
                                                        classes += ' border-destructive/30 bg-destructive/5 text-destructive';
                                                    } else {
                                                        classes += ' text-muted-foreground';
                                                    }
                                                    return (
                                                        <div key={optIdx} className={classes}>
                                                            <span className="mr-2 font-medium">
                                                                {String.fromCharCode(65 + optIdx)}.
                                                            </span>
                                                            {opt}
                                                            {isCorrectAnswer && (
                                                                <CheckCircle className="ml-2 inline size-3.5 text-green-500" />
                                                            )}
                                                        </div>
                                                    );
                                                })}
                                            </div>
                                            {q.explanation && (
                                                <p className="rounded-lg bg-muted/50 px-4 py-3 text-sm text-muted-foreground">
                                                    {q.explanation}
                                                </p>
                                            )}
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                </section>
            </div>
        </StudentLayout>
    );
}
