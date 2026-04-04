import { Head, router, useForm } from '@inertiajs/react';
import { MessageSquare, Star, ThumbsUp, UserIcon } from 'lucide-react';
import { useState } from 'react';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import StudentLayout from '@/layouts/student-layout';
import { feedback } from '@/routes/student';
import { store } from '@/routes/student/feedback';
import type { BreadcrumbItem } from '@/types';

type PendingBooking = {
    id: number;
    type_label: string;
    range_label: string;
    instructor_name: string | null;
    driving_skills: string[];
};

type RecentFeedback = {
    id: number;
    rating: number;
    comment: string | null;
    confidence_scores: Record<string, number> | null;
    type_label: string;
    range_label: string;
    instructor_name: string | null;
    created_at: string;
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Feedback', href: feedback().url },
];

const skillLabels: Record<string, string> = {
    parking: 'Parkering',
    motorvej: 'Motorvej',
    roundabouts: 'Rundkørsel',
    city_driving: 'Bykørsel',
    overtaking: 'Overhaling',
    reversing: 'Bakring',
    lane_change: 'Filskifte',
    emergency_stop: 'Nødstop',
};

function StarRating({ value, onChange }: { value: number; onChange: (v: number) => void }) {
    const [hovered, setHovered] = useState(0);

    return (
        <div className="flex gap-1">
            {[1, 2, 3, 4, 5].map((n) => (
                <button
                    key={n}
                    type="button"
                    onClick={() => onChange(n)}
                    onMouseEnter={() => setHovered(n)}
                    onMouseLeave={() => setHovered(0)}
                    className="p-0.5 transition"
                >
                    <Star
                        className={`size-7 transition ${
                            n <= (hovered || value)
                                ? 'fill-amber-400 text-amber-400'
                                : 'text-muted-foreground/30'
                        }`}
                    />
                </button>
            ))}
        </div>
    );
}

function ConfidenceMeter({
    label,
    value,
    onChange,
}: {
    label: string;
    value: number;
    onChange: (v: number) => void;
}) {
    return (
        <div className="flex items-center justify-between gap-3">
            <span className="text-sm">{label}</span>
            <div className="flex gap-1">
                {[1, 2, 3, 4, 5].map((n) => (
                    <button
                        key={n}
                        type="button"
                        onClick={() => onChange(n)}
                        className={`size-6 rounded-md text-xs font-medium transition ${
                            n <= value
                                ? 'bg-primary text-primary-foreground'
                                : 'bg-muted text-muted-foreground hover:bg-muted/70'
                        }`}
                    >
                        {n}
                    </button>
                ))}
            </div>
        </div>
    );
}

function FeedbackForm({ booking, onDone }: { booking: PendingBooking; onDone: () => void }) {
    const { data, setData, post, processing, errors } = useForm({
        rating: 0,
        comment: '',
        confidence_scores: {} as Record<string, number>,
    });

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        post(store.url(booking.id), {
            preserveScroll: true,
            onSuccess: onDone,
        });
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-5 rounded-xl border bg-card p-5 shadow-sm">
            <div className="space-y-1">
                <p className="font-medium">{booking.type_label}</p>
                <p className="text-xs text-muted-foreground">{booking.range_label}</p>
                {booking.instructor_name && (
                    <p className="flex items-center gap-1 text-xs text-muted-foreground">
                        <UserIcon className="size-3" />
                        {booking.instructor_name}
                    </p>
                )}
            </div>

            <div className="space-y-2">
                <label className="text-sm font-medium">Bedømmelse</label>
                <StarRating value={data.rating} onChange={(v) => setData('rating', v)} />
                {errors.rating && <p className="text-xs text-destructive">{errors.rating}</p>}
            </div>

            {booking.driving_skills.length > 0 && (
                <div className="space-y-3">
                    <label className="text-sm font-medium">Hvor sikker føler du dig? (1–5)</label>
                    {booking.driving_skills.map((skill) => (
                        <ConfidenceMeter
                            key={skill}
                            label={skillLabels[skill] ?? skill}
                            value={data.confidence_scores[skill] ?? 0}
                            onChange={(v) =>
                                setData('confidence_scores', { ...data.confidence_scores, [skill]: v })
                            }
                        />
                    ))}
                </div>
            )}

            <div className="space-y-2">
                <label className="text-sm font-medium">Kommentar (valgfrit)</label>
                <textarea
                    value={data.comment}
                    onChange={(e) => setData('comment', e.target.value)}
                    maxLength={1000}
                    rows={3}
                    className="w-full rounded-lg border bg-transparent px-3 py-2 text-sm placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-primary/20"
                    placeholder="Hvordan gik lektionen?"
                />
                {errors.comment && <p className="text-xs text-destructive">{errors.comment}</p>}
            </div>

            <Button type="submit" disabled={processing || data.rating === 0}>
                <ThumbsUp className="size-4" />
                Send feedback
            </Button>
        </form>
    );
}

function DisplayStars({ rating }: { rating: number }) {
    return (
        <div className="flex gap-0.5">
            {[1, 2, 3, 4, 5].map((n) => (
                <Star
                    key={n}
                    className={`size-3.5 ${n <= rating ? 'fill-amber-400 text-amber-400' : 'text-muted-foreground/20'}`}
                />
            ))}
        </div>
    );
}

export default function BookingFeedbackIndex({
    pending_feedback,
    recent_feedback,
    avg_rating,
}: {
    pending_feedback: PendingBooking[];
    recent_feedback: RecentFeedback[];
    avg_rating: number | null;
}) {
    const [expandedId, setExpandedId] = useState<number | null>(
        pending_feedback.length > 0 ? pending_feedback[0].id : null,
    );

    return (
        <StudentLayout breadcrumbs={breadcrumbs}>
            <Head title="Feedback" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4 sm:p-6">
                <div className="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                    <div className="space-y-1">
                        <Heading title="Feedback" />
                        <p className="text-sm text-muted-foreground">
                            Giv feedback på dine lektioner og hjælp din instruktør med at tilpasse undervisningen.
                        </p>
                    </div>
                    {avg_rating !== null && (
                        <div className="flex items-center gap-2">
                            <DisplayStars rating={Math.round(avg_rating)} />
                            <span className="text-sm font-medium tabular-nums">{avg_rating}</span>
                            <span className="text-xs text-muted-foreground">gennemsnit</span>
                        </div>
                    )}
                </div>

                {/* Pending feedback */}
                {pending_feedback.length > 0 && (
                    <section className="space-y-3">
                        <div className="flex items-center gap-2">
                            <h2 className="text-base font-medium">Afventer din feedback</h2>
                            <Badge variant="secondary">{pending_feedback.length}</Badge>
                        </div>
                        <div className="space-y-4">
                            {pending_feedback.map((b) =>
                                expandedId === b.id ? (
                                    <FeedbackForm key={b.id} booking={b} onDone={() => setExpandedId(null)} />
                                ) : (
                                    <button
                                        key={b.id}
                                        type="button"
                                        onClick={() => setExpandedId(b.id)}
                                        className="flex w-full items-center justify-between rounded-xl border px-5 py-4 text-left transition hover:border-primary/30 hover:bg-muted/30"
                                    >
                                        <div>
                                            <p className="text-sm font-medium">{b.type_label}</p>
                                            <p className="text-xs text-muted-foreground">{b.range_label}</p>
                                        </div>
                                        <Badge variant="outline">Giv feedback</Badge>
                                    </button>
                                ),
                            )}
                        </div>
                    </section>
                )}

                {/* Recent feedback */}
                <section className="space-y-3">
                    <h2 className="text-base font-medium">Tidligere feedback</h2>
                    {recent_feedback.length === 0 ? (
                        <div className="flex flex-col items-center gap-3 rounded-2xl border border-dashed py-10 text-center">
                            <MessageSquare className="size-10 text-muted-foreground/30" />
                            <div>
                                <p className="font-medium text-muted-foreground">Ingen feedback endnu</p>
                                <p className="mt-1 text-sm text-muted-foreground/70">
                                    Din feedback vil vises her efter du har vurderet en lektion.
                                </p>
                            </div>
                        </div>
                    ) : (
                        <div className="divide-y rounded-xl border shadow-sm">
                            {recent_feedback.map((f) => (
                                <div key={f.id} className="space-y-2 px-5 py-4">
                                    <div className="flex items-start justify-between gap-2">
                                        <div>
                                            <p className="text-sm font-medium">{f.type_label}</p>
                                            <p className="text-xs text-muted-foreground">{f.range_label}</p>
                                        </div>
                                        <DisplayStars rating={f.rating} />
                                    </div>
                                    {f.instructor_name && (
                                        <p className="flex items-center gap-1 text-xs text-muted-foreground">
                                            <UserIcon className="size-3" />
                                            {f.instructor_name}
                                        </p>
                                    )}
                                    {f.comment && (
                                        <p className="text-sm text-muted-foreground">{f.comment}</p>
                                    )}
                                    {f.confidence_scores && Object.keys(f.confidence_scores).length > 0 && (
                                        <div className="flex flex-wrap gap-1.5">
                                            {Object.entries(f.confidence_scores).map(([skill, score]) => (
                                                <span
                                                    key={skill}
                                                    className="rounded-full border px-2.5 py-0.5 text-xs text-muted-foreground"
                                                >
                                                    {skillLabels[skill] ?? skill}: {score}/5
                                                </span>
                                            ))}
                                        </div>
                                    )}
                                </div>
                            ))}
                        </div>
                    )}
                </section>
            </div>
        </StudentLayout>
    );
}
