import { CheckCircle } from 'lucide-react';
import { cn } from '@/lib/utils';

export type LessonProgressRow = {
    type: string;
    label: string;
    required: number;
    completed: number;
    scheduled: number;
    remaining: number;
};

type Props = {
    rows: LessonProgressRow[];
    variant?: 'compact' | 'full';
    /** When false, hides the help text under the full table (e.g. staff booking form). */
    showFooterNote?: boolean;
};

function barColor(pct: number): string {
    if (pct >= 100) return 'bg-green-500';
    if (pct >= 60) return 'bg-primary';
    if (pct >= 30) return 'bg-amber-500';
    return 'bg-amber-500';
}

export function StudentLessonProgress({ rows, variant = 'full', showFooterNote = true }: Props) {
    if (rows.length === 0) {
        return (
            <p className="text-sm text-muted-foreground">
                Ingen lektionskrav registreret på dit tilbud endnu. Kontakt køreskolen, hvis du er i tvivl.
            </p>
        );
    }

    if (variant === 'compact') {
        return (
            <ul className="space-y-4">
                {rows.map((row) => {
                    const pct = row.required > 0 ? Math.min(100, Math.round((row.completed / row.required) * 100)) : 0;
                    const isComplete = pct >= 100;
                    return (
                        <li key={row.type} className="space-y-2">
                            <div className="flex items-center justify-between gap-2 text-sm">
                                <span className="flex items-center gap-1.5 font-medium">
                                    {isComplete && <CheckCircle className="size-3.5 text-green-500" />}
                                    {row.label}
                                </span>
                                <span className={cn(
                                    'tabular-nums',
                                    isComplete ? 'font-medium text-green-600' : 'text-muted-foreground',
                                )}>
                                    {row.completed} / {row.required}
                                </span>
                            </div>
                            <div className="relative h-2.5 overflow-hidden rounded-full bg-muted">
                                {/* Milestone markers */}
                                {row.required >= 4 && [25, 50, 75].map((milestone) => (
                                    <div
                                        key={milestone}
                                        className="absolute top-0 z-10 h-full w-px bg-background/60"
                                        style={{ left: `${milestone}%` }}
                                    />
                                ))}
                                <div
                                    className={cn(
                                        'h-full rounded-full transition-all duration-700 ease-out',
                                        barColor(pct),
                                    )}
                                    style={{ width: `${pct}%` }}
                                />
                            </div>
                        </li>
                    );
                })}
            </ul>
        );
    }

    return (
        <div className="overflow-x-auto rounded-xl border">
            <table className="w-full min-w-[28rem] text-sm">
                <thead>
                    <tr className="border-b bg-muted/40 text-left">
                        <th className="px-4 py-3 font-medium">Aktivitet</th>
                        <th className="px-4 py-3 font-medium tabular-nums">Krav</th>
                        <th className="px-4 py-3 font-medium tabular-nums">Fuldført</th>
                        <th className="px-4 py-3 font-medium tabular-nums">Planlagt</th>
                        <th className="px-4 py-3 font-medium tabular-nums">Mangler</th>
                    </tr>
                </thead>
                <tbody>
                    {rows.map((row) => {
                        const pct = row.required > 0 ? Math.min(100, Math.round((row.completed / row.required) * 100)) : 0;
                        const isComplete = pct >= 100;
                        return (
                            <tr key={row.type} className="border-b border-border/60 last:border-0">
                                <td className="px-4 py-3">
                                    <div className="flex items-center gap-2 font-medium">
                                        {isComplete && <CheckCircle className="size-3.5 text-green-500" />}
                                        {row.label}
                                    </div>
                                </td>
                                <td className="px-4 py-3 tabular-nums text-muted-foreground">{row.required}</td>
                                <td className={cn(
                                    'px-4 py-3 tabular-nums',
                                    isComplete ? 'font-medium text-green-600' : 'text-muted-foreground',
                                )}>
                                    {row.completed}
                                </td>
                                <td className="px-4 py-3 tabular-nums text-muted-foreground">{row.scheduled}</td>
                                <td className={cn(
                                    'px-4 py-3 tabular-nums',
                                    row.remaining === 0 ? 'text-green-600' : 'text-muted-foreground',
                                )}>
                                    {row.remaining}
                                </td>
                            </tr>
                        );
                    })}
                </tbody>
            </table>
            {showFooterNote ? (
                <p className="border-t bg-muted/20 px-4 py-2 text-xs text-muted-foreground">
                    «Mangler» er hvad der endnu ikke er fuldført eller booket frem i tiden. Kontakt os for at booke
                    flere timer.
                </p>
            ) : null}
        </div>
    );
}
