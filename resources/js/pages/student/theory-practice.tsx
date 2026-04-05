import { Head, Link } from '@inertiajs/react';
import { BarChart3, CheckCircle, Clock, GraduationCap, Play, Trophy } from 'lucide-react';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import StudentLayout from '@/layouts/student-layout';
import { theoryPractice } from '@/routes/student';
import { start } from '@/routes/student/theory-practice';
import type { BreadcrumbItem } from '@/types';

type Attempt = {
    id: number;
    score: number;
    total: number;
    percentage: number;
    duration_seconds: number;
    attempted_at: string;
};

type Stats = {
    total_attempts: number;
    pass_count: number;
    best_score: number | null;
    available_questions: number;
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Teoritræning', href: theoryPractice().url },
];

function formatDuration(seconds: number): string {
    const m = Math.floor(seconds / 60);
    const s = seconds % 60;
    return `${m}:${s.toString().padStart(2, '0')}`;
}

function StatCard({ icon: Icon, label, value, color }: { icon: React.ElementType; label: string; value: string | number; color: string }) {
    return (
        <div className="flex flex-col items-center gap-2 rounded-xl border bg-card p-5 shadow-sm">
            <Icon className={`size-5 ${color}`} />
            <span className="text-2xl font-bold tabular-nums">{value}</span>
            <span className="text-xs text-muted-foreground">{label}</span>
        </div>
    );
}

export default function TheoryPracticeIndex({ attempts, stats }: { attempts: Attempt[]; stats: Stats }) {
    const passRate = stats.total_attempts > 0 ? Math.round((stats.pass_count / stats.total_attempts) * 100) : 0;

    return (
        <StudentLayout breadcrumbs={breadcrumbs}>
            <Head title="Teoritræning" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4 sm:p-6">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div className="space-y-1">
                        <Heading title="Teoritræning" />
                        <p className="text-sm text-muted-foreground">
                            Øv dig på teoriprøven med spørgsmål fra dit kursusmateriale.
                        </p>
                    </div>
                    {stats.available_questions > 0 && (
                        <Button asChild>
                            <Link href={start().url}>
                                <Play className="size-4" />
                                Start prøve
                            </Link>
                        </Button>
                    )}
                </div>

                {/* Stats grid */}
                <div className="grid grid-cols-2 gap-3 sm:grid-cols-4">
                    <StatCard icon={BarChart3} label="Forsøg i alt" value={stats.total_attempts} color="text-blue-500" />
                    <StatCard icon={CheckCircle} label="Bestået" value={stats.pass_count} color="text-green-500" />
                    <StatCard icon={Trophy} label="Bedste score" value={stats.best_score !== null ? `${stats.best_score}%` : '–'} color="text-amber-500" />
                    <StatCard icon={GraduationCap} label="Tilgængelige spørgsmål" value={stats.available_questions} color="text-purple-500" />
                </div>

                {stats.total_attempts > 0 && (
                    <div className="flex items-center gap-3 rounded-xl border bg-card p-4 shadow-sm">
                        <div className="flex-1">
                            <p className="text-sm font-medium">Bestålsesrate</p>
                            <div className="mt-2 h-2 overflow-hidden rounded-full bg-muted">
                                <div
                                    className="h-full rounded-full bg-green-500 transition-all duration-500"
                                    style={{ width: `${passRate}%` }}
                                />
                            </div>
                        </div>
                        <span className="text-lg font-bold tabular-nums">{passRate}%</span>
                    </div>
                )}

                {/* Attempt history */}
                <section className="space-y-3">
                    <h2 className="text-base font-medium">Seneste forsøg</h2>
                    {attempts.length === 0 ? (
                        <div className="flex flex-col items-center gap-3 rounded-2xl border border-dashed py-10 text-center">
                            <GraduationCap className="size-10 text-muted-foreground/30" />
                            <div>
                                <p className="font-medium text-muted-foreground">Ingen forsøg endnu</p>
                                <p className="mt-1 text-sm text-muted-foreground/70">
                                    Start din første prøve for at se dine resultater her.
                                </p>
                            </div>
                        </div>
                    ) : (
                        <div className="divide-y rounded-xl border shadow-sm">
                            {attempts.map((a) => {
                                const passed = a.percentage >= 90;
                                return (
                                    <Link
                                        key={a.id}
                                        href={`${theoryPractice().url}/${a.id}`}
                                        className="flex items-center justify-between gap-4 px-5 py-4 transition hover:bg-muted/30"
                                    >
                                        <div className="flex items-center gap-3">
                                            {passed ? (
                                                <CheckCircle className="size-5 shrink-0 text-green-500" />
                                            ) : (
                                                <div className="size-5 shrink-0 rounded-full border-2 border-muted-foreground/30" />
                                            )}
                                            <div>
                                                <p className="text-sm font-medium">
                                                    {a.score} / {a.total} rigtige
                                                </p>
                                                <p className="text-xs text-muted-foreground">
                                                    {new Date(a.attempted_at).toLocaleDateString('da-DK', {
                                                        day: 'numeric',
                                                        month: 'short',
                                                        year: 'numeric',
                                                        hour: '2-digit',
                                                        minute: '2-digit',
                                                    })}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-3">
                                            <div className="flex items-center gap-1 text-xs text-muted-foreground">
                                                <Clock className="size-3" />
                                                {formatDuration(a.duration_seconds)}
                                            </div>
                                            <Badge variant={passed ? 'default' : 'secondary'}>
                                                {a.percentage}%
                                            </Badge>
                                        </div>
                                    </Link>
                                );
                            })}
                        </div>
                    )}
                </section>
            </div>
        </StudentLayout>
    );
}
