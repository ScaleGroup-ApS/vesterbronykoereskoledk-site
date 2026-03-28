import { Head, Link, useForm } from '@inertiajs/react';
import { format } from 'date-fns';
import { da } from 'date-fns/locale';
import { ArrowLeft, Banknote, Car, CheckCircle2, ChevronRight, CreditCard, Info } from 'lucide-react';
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
        <div className="min-h-screen bg-background text-foreground font-sans">
            <Head title={`Tilmeld dig – ${offer.name}`} />

            {/* Navbar */}
            <header className="sticky top-0 z-50 w-full border-b border-border/40 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
                <div className="container mx-auto flex h-16 items-center px-4 lg:px-8">
                    <Link href={home()} className="flex items-center gap-2">
                        <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-primary text-primary-foreground">
                            <Car size={20} />
                        </div>
                        <span className="text-xl font-bold tracking-tight">Køreskole Pro</span>
                    </Link>
                </div>
            </header>

            <main className="container mx-auto px-4 lg:px-8 py-12">
                <Link
                    href={home()}
                    className="inline-flex items-center gap-2 text-sm text-muted-foreground hover:text-primary transition-colors mb-8"
                >
                    <ArrowLeft size={16} />
                    Tilbage til forsiden
                </Link>

                <div className="grid lg:grid-cols-[3fr_2fr] gap-8 max-w-6xl">
                    {/* Wizard */}
                    <div className="order-2 lg:order-1">
                        <h1 className="text-3xl font-bold tracking-tight mb-2">Tilmeld dig nu</h1>
                        <p className="text-muted-foreground mb-6">Opret din konto og kom i gang med kørekortet.</p>

                        {/* Step indicator */}
                        <div className="flex items-center gap-3 mb-8">
                            {STEPS.map(({ n, label }, i) => (
                                <div key={n} className="flex items-center gap-2">
                                    <span
                                        className={cn(
                                            'flex h-6 w-6 items-center justify-center rounded-full text-xs font-bold',
                                            step === n && 'bg-primary text-primary-foreground',
                                            step > n && 'bg-primary/20 text-primary',
                                            step < n && 'bg-muted text-muted-foreground',
                                        )}
                                    >
                                        {step > n ? '✓' : n}
                                    </span>
                                    <span className={cn('text-sm', step === n ? 'font-semibold' : 'text-muted-foreground')}>
                                        {label}
                                    </span>
                                    {i < STEPS.length - 1 && <ChevronRight className="size-3 text-muted-foreground" />}
                                </div>
                            ))}
                        </div>

                        {/* Step 1: Vælg hold */}
                        {step === 1 && (
                            <div className="space-y-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="course_id">Vælg hold</Label>
                                    {courseEvents.length === 0 ? (
                                        <p className="rounded-lg border border-dashed p-4 text-sm text-muted-foreground">
                                            Der er ingen kommende hold endnu. Kontakt køreskolen for at komme på venteliste.
                                        </p>
                                    ) : (
                                        <Select
                                            value={
                                                form.data.course_id != null
                                                    ? String(form.data.course_id)
                                                    : undefined
                                            }
                                            onValueChange={(value) => {
                                                const course = courseEvents.find((c) => c.id === Number(value));
                                                if (course) {
                                                    setSelectedCourse(course);
                                                    form.setData('course_id', course.id);
                                                    form.clearErrors('course_id');
                                                }
                                            }}
                                        >
                                            <SelectTrigger
                                                id="course_id"
                                                className="h-11 w-full bg-background text-left"
                                            >
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
                                    <div className="flex items-center gap-2 rounded-lg border border-primary/30 bg-primary/5 p-3 text-sm">
                                        <CheckCircle2 className="size-4 shrink-0 text-primary" />
                                        <span>
                                            <strong>{selectedCourse.title}</strong>
                                            {' — '}
                                            {formatCourseOption(selectedCourse)}
                                        </span>
                                    </div>
                                ) : courseEvents.length > 0 ? (
                                    <p className="text-sm text-muted-foreground">
                                        Vælg det hold du vil tilmelde dig.
                                    </p>
                                ) : null}
                                {form.errors.course_id && <InputError message={form.errors.course_id} />}
                                <Button onClick={() => setStep(2)} disabled={!selectedCourse} className="w-full">
                                    Videre →
                                </Button>
                            </div>
                        )}

                        {/* Step 2: Student info */}
                        {step === 2 && (
                            <div className="space-y-5">
                                <div className="grid gap-2">
                                    <Label htmlFor="name">Fulde navn</Label>
                                    <Input
                                        id="name"
                                        value={form.data.name}
                                        onChange={(e) => form.setData('name', e.target.value)}
                                        onBlur={() => form.validate('name')}
                                        required
                                        autoFocus
                                        autoComplete="name"
                                        placeholder="Dit fulde navn"
                                    />
                                    <InputError message={form.errors.name} />
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="email">E-mailadresse</Label>
                                    <Input
                                        id="email"
                                        type="email"
                                        value={form.data.email}
                                        onChange={(e) => form.setData('email', e.target.value)}
                                        onBlur={() => form.validate('email')}
                                        required
                                        autoComplete="email"
                                        placeholder="din@email.dk"
                                    />
                                    <InputError message={form.errors.email} />
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="phone">Telefonnummer</Label>
                                    <Input
                                        id="phone"
                                        type="tel"
                                        value={form.data.phone}
                                        onChange={(e) => form.setData('phone', e.target.value)}
                                        onBlur={() => form.validate('phone')}
                                        autoComplete="tel"
                                        placeholder="+45 12 34 56 78"
                                    />
                                    <InputError message={form.errors.phone} />
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="cpr">
                                        CPR-nummer{' '}
                                        <span className="text-muted-foreground font-normal">(valgfrit)</span>
                                    </Label>
                                    <Input
                                        id="cpr"
                                        value={form.data.cpr}
                                        onChange={(e) => form.setData('cpr', formatCpr(e.target.value))}
                                        onBlur={() => form.validate('cpr')}
                                        placeholder="DDMMÅÅ-XXXX"
                                        pattern="[0-9]{6}-?[0-9]{4}"
                                        title="CPR-nummer i formatet DDMMÅÅ-XXXX"
                                        minLength={10}
                                        maxLength={11}
                                    />
                                    <InputError message={form.errors.cpr} />
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="password">Adgangskode</Label>
                                    <Input
                                        id="password"
                                        type="password"
                                        value={form.data.password}
                                        onChange={(e) => form.setData('password', e.target.value)}
                                        onBlur={() => form.validate('password')}
                                        required
                                        autoComplete="new-password"
                                        placeholder="Min. 8 tegn"
                                    />
                                    <InputError message={form.errors.password} />
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="password_confirmation">Bekræft adgangskode</Label>
                                    <Input
                                        id="password_confirmation"
                                        type="password"
                                        value={form.data.password_confirmation}
                                        onChange={(e) => form.setData('password_confirmation', e.target.value)}
                                        onBlur={() => form.validate('password_confirmation')}
                                        required
                                        autoComplete="new-password"
                                        placeholder="Gentag adgangskode"
                                    />
                                    <InputError message={form.errors.password_confirmation} />
                                </div>
                                <div className="flex gap-3">
                                    <Button variant="outline" onClick={() => setStep(1)} className="flex-1">
                                        ← Tilbage
                                    </Button>
                                    <Button onClick={() => setStep(3)} className="flex-1">
                                        Videre →
                                    </Button>
                                </div>
                            </div>
                        )}

                        {/* Step 3: Payment + submit */}
                        {step === 3 && (
                            <div className="space-y-5">
                                {/* Selected course summary */}
                                {selectedCourse && (
                                    <div className="rounded-lg border bg-muted/30 p-4 text-sm">
                                        <p className="font-medium">{selectedCourse.title}</p>
                                        <p className="mt-0.5 text-muted-foreground">
                                            {formatCourseOption(selectedCourse)}
                                        </p>
                                    </div>
                                )}

                                <div className="grid gap-2">
                                    <Label>Betalingsmetode</Label>
                                    <div className="grid grid-cols-2 gap-3">
                                        <button
                                            type="button"
                                            onClick={() => form.setData('payment_method', 'stripe')}
                                            className={[
                                                'flex flex-col items-start gap-1.5 rounded-xl border p-4 text-left transition-colors',
                                                form.data.payment_method === 'stripe'
                                                    ? 'border-primary bg-primary/5'
                                                    : 'border-border hover:border-muted-foreground/50',
                                            ].join(' ')}
                                        >
                                            <CreditCard className="size-5 text-primary" />
                                            <span className="font-medium text-sm">Kortbetaling</span>
                                            <span className="text-xs text-muted-foreground">Via Stripe Checkout</span>
                                        </button>
                                        <button
                                            type="button"
                                            onClick={() => form.setData('payment_method', 'cash')}
                                            className={[
                                                'flex flex-col items-start gap-1.5 rounded-xl border p-4 text-left transition-colors',
                                                form.data.payment_method === 'cash'
                                                    ? 'border-primary bg-primary/5'
                                                    : 'border-border hover:border-muted-foreground/50',
                                            ].join(' ')}
                                        >
                                            <Banknote className="size-5 text-primary" />
                                            <span className="font-medium text-sm">Kontant</span>
                                            <span className="text-xs text-muted-foreground">Betales ved fremmøde</span>
                                        </button>
                                    </div>
                                    <InputError message={form.errors.payment_method} />

                                    {form.data.payment_method === 'stripe' && (
                                        <div className="flex items-start gap-2 rounded-lg border bg-muted/50 p-3 text-sm text-muted-foreground">
                                            <Info className="mt-0.5 size-4 shrink-0" />
                                            <span>Du vil blive videresendt til Stripe Checkout for sikker betaling.</span>
                                        </div>
                                    )}
                                    {form.data.payment_method === 'cash' && (
                                        <div className="flex items-start gap-2 rounded-lg border bg-muted/50 p-3 text-sm text-muted-foreground">
                                            <Info className="mt-0.5 size-4 shrink-0" />
                                            <span>Din tilmelding afventer godkendelse fra en instruktør, inden den aktiveres.</span>
                                        </div>
                                    )}
                                </div>

                                <div className="flex gap-3">
                                    <Button variant="outline" onClick={() => setStep(2)} disabled={form.processing} className="flex-1">
                                        ← Tilbage
                                    </Button>
                                    <Button onClick={handleSubmit} disabled={form.processing} className="flex-1 h-12 text-base">
                                        {form.processing && <Spinner />}
                                        {form.data.payment_method === 'stripe' ? 'Gå til betaling' : 'Opret konto og tilmeld'}
                                    </Button>
                                </div>
                            </div>
                        )}
                    </div>

                    {/* Offer Card */}
                    <div className="order-1 lg:order-2">
                        <div className="sticky top-24 rounded-3xl border border-border bg-card p-8 shadow-sm">
                            {offer.type === 'primary' && (
                                <div className="inline-flex items-center rounded-full bg-primary px-4 py-1 text-sm font-semibold text-primary-foreground mb-6">
                                    Mest populære
                                </div>
                            )}
                            <h2 className="text-2xl font-bold">{offer.name}</h2>
                            {offer.description && (
                                <p className="mt-2 text-muted-foreground">{offer.description}</p>
                            )}
                            <div className="mt-6 flex items-baseline text-5xl font-extrabold">
                                {Number(offer.price).toLocaleString('da-DK')}
                                <span className="ml-1 text-xl font-medium text-muted-foreground">DKK</span>
                            </div>
                            <ul className="mt-8 space-y-4">
                                {offer.theory_lessons > 0 && (
                                    <li className="flex items-center gap-3">
                                        <CheckCircle2 className="h-5 w-5 shrink-0 text-primary" />
                                        <span>{offer.theory_lessons} Teoritimer</span>
                                    </li>
                                )}
                                {offer.driving_lessons > 0 && (
                                    <li className="flex items-center gap-3">
                                        <CheckCircle2 className="h-5 w-5 shrink-0 text-primary" />
                                        <span>{offer.driving_lessons} Kørelektioner (45 min)</span>
                                    </li>
                                )}
                                {offer.track_required && (
                                    <li className="flex items-center gap-3">
                                        <CheckCircle2 className="h-5 w-5 shrink-0 text-primary" />
                                        <span>Manøvrebane</span>
                                    </li>
                                )}
                                {offer.slippery_required && (
                                    <li className="flex items-center gap-3">
                                        <CheckCircle2 className="h-5 w-5 shrink-0 text-primary" />
                                        <span>Køreteknisk anlæg (Glatbane)</span>
                                    </li>
                                )}
                            </ul>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    );
}
