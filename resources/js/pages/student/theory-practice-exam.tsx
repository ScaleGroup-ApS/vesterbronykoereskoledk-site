import { Head, router } from '@inertiajs/react';
import { AlertTriangle, ChevronLeft, ChevronRight, Clock, Send } from 'lucide-react';
import { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import { Button } from '@/components/ui/button';
import { store } from '@/routes/student/theory-practice';

type Question = {
    id: number;
    question: string;
    options: string[];
};

function formatTime(seconds: number): string {
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    return `${m}:${s.toString().padStart(2, '0')}`;
}

export default function TheoryPracticeExam({
    questions,
    time_limit_seconds,
}: {
    questions: Question[];
    time_limit_seconds: number;
}) {
    const [answers, setAnswers] = useState<Record<number, number>>({});
    const [currentIndex, setCurrentIndex] = useState(0);
    const [timeLeft, setTimeLeft] = useState(time_limit_seconds);
    const [submitting, setSubmitting] = useState(false);
    const startedAt = useRef(Date.now());

    useEffect(() => {
        const timer = setInterval(() => {
            setTimeLeft((prev) => {
                if (prev <= 1) {
                    clearInterval(timer);
                    return 0;
                }
                return prev - 1;
            });
        }, 1000);
        return () => clearInterval(timer);
    }, []);

    const submit = useCallback(() => {
        if (submitting) return;
        setSubmitting(true);
        const durationSeconds = Math.round((Date.now() - startedAt.current) / 1000);
        const orderedAnswers = questions.map((_, i) => answers[i] ?? -1);
        router.post(store.url(), {
            answers: orderedAnswers,
            question_ids: questions.map((q) => q.id),
            duration_seconds: durationSeconds,
        });
    }, [submitting, answers, questions]);

    useEffect(() => {
        if (timeLeft === 0 && !submitting) {
            submit();
        }
    }, [timeLeft, submitting, submit]);

    const answeredCount = useMemo(() => Object.keys(answers).length, [answers]);
    const q = questions[currentIndex];
    const isLast = currentIndex === questions.length - 1;
    const timeWarning = timeLeft < 120;

    return (
        <>
            <Head title="Teoriprøve" />
            <div className="flex min-h-screen flex-col bg-background">
                {/* Top bar */}
                <div className="sticky top-0 z-10 border-b bg-background/95 backdrop-blur">
                    <div className="mx-auto flex h-14 max-w-3xl items-center justify-between px-4">
                        <span className="text-sm font-medium">
                            Spørgsmål {currentIndex + 1} / {questions.length}
                        </span>
                        <div className={`flex items-center gap-1.5 text-sm font-medium tabular-nums ${timeWarning ? 'text-destructive' : 'text-muted-foreground'}`}>
                            {timeWarning && <AlertTriangle className="size-3.5" />}
                            <Clock className="size-3.5" />
                            {formatTime(timeLeft)}
                        </div>
                    </div>
                    {/* Progress bar */}
                    <div className="h-1 bg-muted">
                        <div
                            className="h-full bg-primary transition-all duration-300"
                            style={{ width: `${((currentIndex + 1) / questions.length) * 100}%` }}
                        />
                    </div>
                </div>

                {/* Question */}
                <div className="mx-auto flex w-full max-w-3xl flex-1 flex-col gap-6 p-4 sm:p-8">
                    <h2 className="text-lg font-semibold leading-snug sm:text-xl">{q.question}</h2>

                    <div className="flex flex-col gap-3">
                        {q.options.map((option, optIdx) => {
                            const isSelected = answers[currentIndex] === optIdx;
                            return (
                                <button
                                    key={optIdx}
                                    type="button"
                                    onClick={() => setAnswers((prev) => ({ ...prev, [currentIndex]: optIdx }))}
                                    className={`rounded-xl border px-5 py-4 text-left text-sm transition ${
                                        isSelected
                                            ? 'border-primary bg-primary/5 font-medium text-primary ring-1 ring-primary/20'
                                            : 'hover:border-primary/30 hover:bg-muted/30'
                                    }`}
                                >
                                    <span className="mr-2 inline-flex size-6 items-center justify-center rounded-full border text-xs font-medium">
                                        {String.fromCharCode(65 + optIdx)}
                                    </span>
                                    {option}
                                </button>
                            );
                        })}
                    </div>
                </div>

                {/* Bottom nav */}
                <div className="sticky bottom-0 border-t bg-background/95 backdrop-blur">
                    <div className="mx-auto flex h-16 max-w-3xl items-center justify-between px-4">
                        <Button
                            variant="ghost"
                            size="sm"
                            disabled={currentIndex === 0}
                            onClick={() => setCurrentIndex((i) => i - 1)}
                        >
                            <ChevronLeft className="size-4" />
                            Forrige
                        </Button>

                        <span className="text-xs text-muted-foreground">
                            {answeredCount} / {questions.length} besvaret
                        </span>

                        {isLast ? (
                            <Button size="sm" onClick={submit} disabled={submitting}>
                                <Send className="size-4" />
                                Aflever
                            </Button>
                        ) : (
                            <Button
                                variant="ghost"
                                size="sm"
                                onClick={() => setCurrentIndex((i) => i + 1)}
                            >
                                Næste
                                <ChevronRight className="size-4" />
                            </Button>
                        )}
                    </div>
                </div>

                {/* Question dots */}
                <div className="border-t px-4 py-3">
                    <div className="mx-auto flex max-w-3xl flex-wrap gap-1.5">
                        {questions.map((_, i) => (
                            <button
                                key={i}
                                type="button"
                                onClick={() => setCurrentIndex(i)}
                                className={`size-7 rounded-md text-xs font-medium transition ${
                                    i === currentIndex
                                        ? 'bg-primary text-primary-foreground'
                                        : answers[i] !== undefined
                                            ? 'bg-primary/10 text-primary'
                                            : 'bg-muted text-muted-foreground hover:bg-muted/70'
                                }`}
                            >
                                {i + 1}
                            </button>
                        ))}
                    </div>
                </div>
            </div>
        </>
    );
}
