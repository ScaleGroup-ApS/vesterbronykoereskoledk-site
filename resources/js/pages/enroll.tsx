import { Head, Link, useForm } from '@inertiajs/react';
import { format } from 'date-fns';
import { da } from 'date-fns/locale';
import { ArrowLeft, Banknote, Car, CheckCircle2, ChevronRight, CreditCard, Info } from 'lucide-react';
import { motion } from 'framer-motion';
import { useState } from 'react';
import { store } from '@/actions/App/Http/Controllers/Enrollment/EnrollmentController';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import { cn } from '@/lib/utils';
import { home } from '@/routes';

interface Offer {
    id: number;
    slug: string;
    name: string;
    description: string | null;
    price: string;
    type: 'primary' | 'addon';
    theory_lessons: number;
    driving_lessons: number;
    track_required: boolean;
    slippery_required: boolean;
}

type CourseEvent = {
    id: number;
    title: string;
    start: string;
    end: string;
};

type PaymentMethod = 'stripe' | 'cash';

const STEPS = [
    { n: 1 as const, label: 'Hold' },
    { n: 2 as const, label: 'Info' },
    { n: 3 as const, label: 'Betaling' },
];

function formatCourseOption(course: CourseEvent): string {
    const start = new Date(course.start);
    const end = new Date(course.end);
    if (!Number.isFinite(start.getTime()) || !Number.isFinite(end.getTime())) {
        return course.title;
    }
    const datePart = format(start, "EEEE d. MMMM yyyy 'kl.' HH:mm", { locale: da });
    const endPart = format(end, 'HH:mm', { locale: da });
    return `${datePart} – ${endPart}`;
}

export default function Enroll({
    offer,
    courseEvents,
}: {
    offer: Offer;
    courseEvents: CourseEvent[];
}) {
    const [step, setStep] = useState<1 | 2 | 3>(1);
    const [selectedCourse, setSelectedCourse] = useState<CourseEvent | null>(null);

    const form = useForm({
        name: '',
        email: '',
        phone: '',
        cpr: '',
        password: '',
        password_confirmation: '',
        course_id: null as number | null,
        payment_method: 'stripe' as PaymentMethod,
    }).withPrecognition(store(offer.slug));

    function formatCpr(value: string): string {
        const digits = value.replace(/\D/g, '').slice(0, 10);
        return digits.length > 6 ? `${digits.slice(0, 6)}-${digits.slice(6)}` : digits;
    }

    function handleSubmit() {
        if (!form.data.course_id) { return; }
        form.post(store(offer.slug).url, {
            onError: (errs) => {
                if (errs.course_id) { setStep(1); }
            },
        });
    }

    return (
        <div className="marketing-public-site min-h-screen bg-mk-base font-sans text-mk-text">
            <Head title={`Tilmeld dig – ${offer.name}`} />

            {/* Navbar */}
            <header className="sticky top-0 z-50 w-full border-b border-white/[0.06] bg-[rgba(10,10,10,0.88)] backdrop-blur-xl">
                <div className="container mx-auto flex h-16 items-center px-4 lg:px-8">
                    <Link href={home()} className="flex items-center gap-2.5 text-white">
                        <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-mk-accent text-white shadow-[0_0_20px_-4px_rgba(232,0,29,0.5)]">
                            <Car size={18} strokeWidth={2.5} />
                        </div>
                        <span className="font-heading text-xl font-bold tracking-tight">Køreskole Pro</span>
                    </Link>
                </div>
            </header>

            <main className="container mx-auto px-4 py-12 lg:px-8">
                <Link
                    href={home()}
                    className="mb-8 inline-flex items-center gap-2 text-sm text-mk-muted transition-colors hover:text-mk-text"
                >
                    <ArrowLeft size={16} />
                    Tilbage til forsiden
                </Link>

                <div className="grid max-w-6xl gap-8 lg:grid-cols-[3fr_2fr]">
                    {/* Wizard */}
                    <motion.div
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.45 }}
                        className="order-2 lg:order-1"
                    >
                        <h1 className="mb-2 font-heading text-3xl font-bold tracking-tight text-mk-text">Tilmeld dig nu</h1>
                        <p className="mb-6 text-mk-muted">Opret din konto og kom i gang med kørekortet.</p>

                        {/* Step indicator */}
                        <div className="mb-8 flex items-center gap-3">
                            {STEPS.map(({ n, label }, i) => (
                                <div key={n} className="flex items-center gap-2">
                                    <span
                                        className={cn(
                                            'flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold transition-colors',
                                            step === n && 'bg-mk-accent text-white',
                                            step > n && 'bg-mk-accent/20 text-mk-accent',
                                            step < n && 'bg-mk-surface-2 text-mk-muted',
                                        )}
                                    >
                                        {step > n ? '✓' : n}
                                    </span>
                                    <span className={cn('text-sm', step === n ? 'font-semibold text-mk-text' : 'text-mk-muted')}>
                                        {label}
                                    </span>
                                    {i < STEPS.length - 1 && <ChevronRight className="size-3 text-mk-muted/50" />}
                                </div>
                            ))}
                        </div>

                        {/* Step 1: Vælg hold */}
                        {step === 1 && (
                            <div className="space-y-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="course_id">Vælg hold</Label>
                                    {courseEvents.length === 0 ? (
                                        <p className="rounded-xl border border-dashed border-mk-border p-4 text-sm text-mk-muted">
                                            Der er ingen kommende hold endnu. Kontakt køreskolen for at komme på venteliste.
                                        </p>
                                    ) : (
                                        <Select
                                            value={form.data.course_id != null ? String(form.data.course_id) : undefined}
                                            onValueChange={(value) => {
                                                const course = courseEvents.find((c) => c.id === Number(value));
                                                if (course) {
                                                    setSelectedCourse(course);
                                                    form.setData('course_id', course.id);
                                                    form.clearErrors('course_id');
                                                }
                                            }}
                                        >
                                            <SelectTrigger id="course_id" className="h-11 w-full text-left">
                                                <SelectValue placeholder="Vælg et hold…" />
                                            </SelectTrigger>
                                            <SelectContent position="popper" className="z-[100] max-h-72">
                                                {courseEvents.map((c) => (
                                                    <SelectItem key={c.id} value={String(c.id)}>
                                                        {formatCourseOption(c)}
                                                    </SelectItem>
                                                ))}
                                            </SelectContent>
                                        </Select>
                                    )}
                                </div>
                                {selectedCourse ? (
                                    <div className="flex items-center gap-2 rounded-xl border border-mk-accent/30 bg-mk-accent/5 p-3 text-sm">
                                        <CheckCircle2 className="size-4 shrink-0 text-mk-accent" />
                                        <span className="text-mk-text">
                                            <strong>{selectedCourse.title}</strong>
                                            {' — '}
                                            {formatCourseOption(selectedCourse)}
                                        </span>
                                    </div>
                                ) : courseEvents.length > 0 ? (
                                    <p className="text-sm text-mk-muted">Vælg det hold du vil tilmelde dig.</p>
                                ) : null}
                                {form.errors.course_id && <InputError message={form.errors.course_id} />}
                                <Button
                                    onClick={() => setStep(2)}
                                    disabled={!selectedCourse}
                                    className="w-full bg-mk-accent text-white hover:bg-mk-accent-soft shadow-[0_8px_28px_-8px_rgba(232,0,29,0.45)]"
                                >
                                    Videre →
                                </Button>
                            </div>
                        )}

                        {/* Step 2: Student info */}
                        {step === 2 && (
                            <div className="space-y-5">
                                <div className="grid gap-2">
                                    <Label htmlFor="name">Fulde navn</Label>
                                    <Input id="name" value={form.data.name} onChange={(e) => form.setData('name', e.target.value)} onBlur={() => form.validate('name')} required autoFocus autoComplete="name" placeholder="Dit fulde navn" />
                                    <InputError message={form.errors.name} />
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="email">E-mailadresse</Label>
                                    <Input id="email" type="email" value={form.data.email} onChange={(e) => form.setData('email', e.target.value)} onBlur={() => form.validate('email')} required autoComplete="email" placeholder="din@email.dk" />
                                    <InputError message={form.errors.email} />
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="phone">Telefonnummer</Label>
                                    <Input id="phone" type="tel" value={form.data.phone} onChange={(e) => form.setData('phone', e.target.value)} onBlur={() => form.validate('phone')} autoComplete="tel" placeholder="+45 12 34 56 78" />
                                    <InputError message={form.errors.phone} />
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="cpr">
                                        CPR-nummer{' '}
                                        <span className="font-normal text-mk-muted">(valgfrit)</span>
                                    </Label>
                                    <Input id="cpr" value={form.data.cpr} onChange={(e) => form.setData('cpr', formatCpr(e.target.value))} onBlur={() => form.validate('cpr')} placeholder="DDMMÅÅ-XXXX" pattern="[0-9]{6}-?[0-9]{4}" title="CPR-nummer i formatet DDMMÅÅ-XXXX" minLength={10} maxLength={11} />
                                    <InputError message={form.errors.cpr} />
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="password">Adgangskode</Label>
                                    <Input id="password" type="password" value={form.data.password} onChange={(e) => form.setData('password', e.target.value)} onBlur={() => form.validate('password')} required autoComplete="new-password" placeholder="Min. 8 tegn" />
                                    <InputError message={form.errors.password} />
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="password_confirmation">Bekræft adgangskode</Label>
                                    <Input id="password_confirmation" type="password" value={form.data.password_confirmation} onChange={(e) => form.setData('password_confirmation', e.target.value)} onBlur={() => form.validate('password_confirmation')} required autoComplete="new-password" placeholder="Gentag adgangskode" />
                                    <InputError message={form.errors.password_confirmation} />
                                </div>
                                <div className="flex gap-3">
                                    <Button variant="outline" onClick={() => setStep(1)} className="flex-1 border-mk-border text-mk-muted hover:border-mk-accent/40 hover:text-mk-text">
                                        ← Tilbage
                                    </Button>
                                    <Button onClick={() => setStep(3)} className="flex-1 bg-mk-accent text-white hover:bg-mk-accent-soft shadow-[0_8px_28px_-8px_rgba(232,0,29,0.45)]">
                                        Videre →
                                    </Button>
                                </div>
                            </div>
                        )}

                        {/* Step 3: Payment + submit */}
                        {step === 3 && (
                            <div className="space-y-5">
                                {selectedCourse && (
                                    <div className="rounded-xl border border-mk-border bg-mk-surface/60 p-4 text-sm">
                                        <p className="font-medium text-mk-text">{selectedCourse.title}</p>
                                        <p className="mt-0.5 text-mk-muted">{formatCourseOption(selectedCourse)}</p>
                                    </div>
                                )}

                                <div className="grid gap-2">
                                    <Label>Betalingsmetode</Label>
                                    <div className="grid grid-cols-2 gap-3">
                                        <button
                                            type="button"
                                            onClick={() => form.setData('payment_method', 'stripe')}
                                            className={cn(
                                                'flex flex-col items-start gap-1.5 rounded-xl border p-4 text-left transition-colors duration-150',
                                                form.data.payment_method === 'stripe'
                                                    ? 'border-mk-accent bg-mk-accent/5'
                                                    : 'border-mk-border hover:border-mk-border/80 hover:bg-white/[0.02]',
                                            )}
                                        >
                                            <CreditCard className="size-5 text-mk-accent" />
                                            <span className="text-sm font-medium text-mk-text">Kortbetaling</span>
                                            <span className="text-xs text-mk-muted">Via Stripe Checkout</span>
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => form.setData('payment_method', 'cash')}
                                            className={cn(
                                                'flex flex-col items-start gap-1.5 rounded-xl border p-4 text-left transition-colors duration-150',
                                                form.data.payment_method === 'cash'
                                                    ? 'border-mk-accent bg-mk-accent/5'
                                                    : 'border-mk-border hover:border-mk-border/80 hover:bg-white/[0.02]',
                                            )}
                                        >
                                            <Banknote className="size-5 text-mk-accent" />
                                            <span className="text-sm font-medium text-mk-text">Kontant</span>
                                            <span className="text-xs text-mk-muted">Betales ved fremmøde</span>
                                        </button>
                                    </div>
                                    <InputError message={form.errors.payment_method} />

                                    {form.data.payment_method === 'stripe' && (
                                        <div className="flex items-start gap-2 rounded-xl border border-mk-border bg-mk-surface/60 p-3 text-sm text-mk-muted">
                                            <Info className="mt-0.5 size-4 shrink-0 text-mk-accent" />
                                            <span>Du vil blive videresendt til Stripe Checkout for sikker betaling.</span>
                                        </div>
                                    )}
                                    {form.data.payment_method === 'cash' && (
                                        <div className="flex items-start gap-2 rounded-xl border border-mk-border bg-mk-surface/60 p-3 text-sm text-mk-muted">
                                            <Info className="mt-0.5 size-4 shrink-0 text-mk-accent" />
                                            <span>Din tilmelding afventer godkendelse fra en instruktør, inden den aktiveres.</span>
                                        </div>
                                    )}
                                </div>

                                <div className="flex gap-3">
                                    <Button variant="outline" onClick={() => setStep(2)} disabled={form.processing} className="flex-1 border-mk-border text-mk-muted hover:border-mk-accent/40 hover:text-mk-text">
                                        ← Tilbage
                                    </Button>
                                    <Button
                                        onClick={handleSubmit}
                                        disabled={form.processing}
                                        className="h-12 flex-1 bg-mk-accent text-base text-white hover:bg-mk-accent-soft shadow-[0_8px_28px_-8px_rgba(232,0,29,0.45)]"
                                    >
                                        {form.processing && <Spinner />}
                                        {form.data.payment_method === 'stripe' ? 'Gå til betaling' : 'Opret konto og tilmeld'}
                                    </Button>
                                </div>
                            </div>
                        )}
                    </motion.div>

                    {/* Offer Card */}
                    <motion.div
                        initial={{ opacity: 0, x: 20 }}
                        animate={{ opacity: 1, x: 0 }}
                        transition={{ duration: 0.5, delay: 0.1 }}
                        className="order-1 lg:order-2"
                    >
                        <div className="sticky top-24 overflow-hidden rounded-2xl border border-mk-border bg-mk-surface shadow-[0_0_40px_-12px_rgba(232,0,29,0.12)]">
                            {/* Red top bar (like mk-card) */}
                            <div className="h-[3px] w-full bg-mk-accent" />
                            <div className="p-8">
                                {offer.type === 'primary' && (
                                    <div className="mb-5 inline-flex items-center gap-1.5 rounded-full border border-mk-accent/30 bg-mk-accent/10 px-3 py-1 text-xs font-semibold text-mk-accent">
                                        <span className="h-1.5 w-1.5 animate-pulse rounded-full bg-mk-accent" />
                                        Mest populære
                                    </div>
                                )}
                                <h2 className="font-heading text-2xl font-bold text-mk-text">{offer.name}</h2>
                                {offer.description && (
                                    <p className="mt-2 text-sm leading-relaxed text-mk-muted">{offer.description}</p>
                                )}
                                <div className="mt-6 flex items-baseline font-heading">
                                    <span className="text-5xl font-extrabold text-mk-accent" style={{ letterSpacing: '-0.03em' }}>
                                        {Number(offer.price).toLocaleString('da-DK')}
                                    </span>
                                    <span className="ml-1.5 text-xl font-medium text-mk-muted">DKK</span>
                                </div>
                                <ul className="mt-8 space-y-3">
                                    {offer.theory_lessons > 0 && (
                                        <li className="flex items-center gap-3 text-sm text-mk-text">
                                            <CheckCircle2 className="h-4 w-4 shrink-0 text-mk-accent" />
                                            {offer.theory_lessons} Teoritimer
                                        </li>
                                    )}
                                    {offer.driving_lessons > 0 && (
                                        <li className="flex items-center gap-3 text-sm text-mk-text">
                                            <CheckCircle2 className="h-4 w-4 shrink-0 text-mk-accent" />
                                            {offer.driving_lessons} Kørelektioner (45 min)
                                        </li>
                                    )}
                                    {offer.track_required && (
                                        <li className="flex items-center gap-3 text-sm text-mk-text">
                                            <CheckCircle2 className="h-4 w-4 shrink-0 text-mk-accent" />
                                            Manøvrebane
                                        </li>
                                    )}
                                    {offer.slippery_required && (
                                        <li className="flex items-center gap-3 text-sm text-mk-text">
                                            <CheckCircle2 className="h-4 w-4 shrink-0 text-mk-accent" />
                                            Køreteknisk anlæg (Glatbane)
                                        </li>
                                    )}
                                </ul>
                            </div>
                        </div>
                    </motion.div>
                </div>
            </main>
        </div>
    );
}
