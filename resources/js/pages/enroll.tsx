import { useState } from 'react';
import { Form, Head, Link } from '@inertiajs/react';
import { ArrowLeft, Banknote, CalendarIcon, Car, CheckCircle2, CreditCard, Info } from 'lucide-react';
import { format } from 'date-fns';
import { da } from 'date-fns/locale';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Spinner } from '@/components/ui/spinner';
import { cn } from '@/lib/utils';
import { home } from '@/routes';
import { store } from '@/actions/App/Http/Controllers/Enrollment/EnrollmentController';

interface Offer {
    id: number;
    name: string;
    description: string | null;
    price: string;
    type: 'primary' | 'addon';
    theory_lessons: number;
    driving_lessons: number;
    track_required: boolean;
    slippery_required: boolean;
}

type PaymentMethod = 'stripe' | 'cash';

export default function Enroll({
    offer,
    availableDates,
    courses,
}: {
    offer: Offer;
    availableDates: string[];
    courses: Record<string, number>;
}) {
    const [paymentMethod, setPaymentMethod] = useState<PaymentMethod>('stripe');
    const [selectedDate, setSelectedDate] = useState<Date | undefined>(undefined);

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

                <div className="grid lg:grid-cols-2 gap-12 max-w-5xl">
                    {/* Enrollment Form */}
                    <div className="order-2 lg:order-1">
                        <h1 className="text-3xl font-bold tracking-tight mb-2">Tilmeld dig nu</h1>
                        <p className="text-muted-foreground mb-8">Opret din konto og kom i gang med kørekortet.</p>

                        <Form
                            {...store.form(offer.id)}
                            className="space-y-5"
                        >
                            {({ errors, processing }) => (
                                <>
                                    <div className="grid gap-2">
                                        <Label htmlFor="name">Fulde navn</Label>
                                        <Input
                                            id="name"
                                            name="name"
                                            required
                                            autoFocus
                                            autoComplete="name"
                                            placeholder="Dit fulde navn"
                                        />
                                        <InputError message={errors.name} />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="email">E-mailadresse</Label>
                                        <Input
                                            id="email"
                                            name="email"
                                            type="email"
                                            required
                                            autoComplete="email"
                                            placeholder="din@email.dk"
                                        />
                                        <InputError message={errors.email} />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="phone">Telefonnummer</Label>
                                        <Input
                                            id="phone"
                                            name="phone"
                                            type="tel"
                                            autoComplete="tel"
                                            placeholder="+45 12 34 56 78"
                                        />
                                        <InputError message={errors.phone} />
                                    </div>

                                    <div className="grid sm:grid-cols-2 gap-4">
                                        <div className="grid gap-2">
                                            <Label htmlFor="cpr">
                                                CPR-nummer{' '}
                                                <span className="text-muted-foreground font-normal">(valgfrit)</span>
                                            </Label>
                                            <Input
                                                id="cpr"
                                                name="cpr"
                                                placeholder="DDMMÅÅ-XXXX"
                                            />
                                            <InputError message={errors.cpr} />
                                        </div>

                                        <div className="grid gap-2">
                                            <Label>
                                                Ønsket startdato{' '}
                                                <span className="text-muted-foreground font-normal">(valgfrit)</span>
                                            </Label>
                                            <Popover>
                                                <PopoverTrigger asChild>
                                                    <button
                                                        type="button"
                                                        className={cn(
                                                            'flex h-9 w-full items-center gap-2 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm transition-colors hover:bg-accent',
                                                            !selectedDate && 'text-muted-foreground',
                                                        )}
                                                    >
                                                        <CalendarIcon className="size-4 shrink-0" />
                                                        {selectedDate
                                                            ? format(selectedDate, 'd. MMMM yyyy', { locale: da })
                                                            : 'Vælg en dato'}
                                                    </button>
                                                </PopoverTrigger>
                                                <PopoverContent className="w-auto p-0" align="start">
                                                    <Calendar
                                                        mode="single"
                                                        selected={selectedDate}
                                                        onSelect={setSelectedDate}
                                                        locale={da}
                                                        disabled={(date) => {
                                                            const iso = format(date, 'yyyy-MM-dd');
                                                            return !availableDates.includes(iso);
                                                        }}
                                                        fromMonth={new Date()}
                                                    />
                                                </PopoverContent>
                                            </Popover>
                                            <InputError message={errors.course_id} />
                                        </div>
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="password">Adgangskode</Label>
                                        <Input
                                            id="password"
                                            name="password"
                                            type="password"
                                            required
                                            autoComplete="new-password"
                                            placeholder="Min. 8 tegn"
                                        />
                                        <InputError message={errors.password} />
                                    </div>

                                    <div className="grid gap-2">
                                        <Label htmlFor="password_confirmation">Bekræft adgangskode</Label>
                                        <Input
                                            id="password_confirmation"
                                            name="password_confirmation"
                                            type="password"
                                            required
                                            autoComplete="new-password"
                                            placeholder="Gentag adgangskode"
                                        />
                                        <InputError message={errors.password_confirmation} />
                                    </div>

                                    {/* Payment Method Selection */}
                                    <div className="grid gap-2">
                                        <Label>Betalingsmetode</Label>
                                        <div className="grid grid-cols-2 gap-3">
                                            <button
                                                type="button"
                                                onClick={() => setPaymentMethod('stripe')}
                                                className={[
                                                    'flex flex-col items-start gap-1.5 rounded-xl border p-4 text-left transition-colors',
                                                    paymentMethod === 'stripe'
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
                                                onClick={() => setPaymentMethod('cash')}
                                                className={[
                                                    'flex flex-col items-start gap-1.5 rounded-xl border p-4 text-left transition-colors',
                                                    paymentMethod === 'cash'
                                                        ? 'border-primary bg-primary/5'
                                                        : 'border-border hover:border-muted-foreground/50',
                                                ].join(' ')}
                                            >
                                                <Banknote className="size-5 text-primary" />
                                                <span className="font-medium text-sm">Kontant</span>
                                                <span className="text-xs text-muted-foreground">Betales ved fremmøde</span>
                                            </button>
                                        </div>
                                        <InputError message={errors.payment_method} />

                                        {paymentMethod === 'stripe' && (
                                            <div className="flex items-start gap-2 rounded-lg border bg-muted/50 p-3 text-sm text-muted-foreground">
                                                <Info className="mt-0.5 size-4 shrink-0" />
                                                <span>Du vil blive videresendt til Stripe Checkout for sikker betaling.</span>
                                            </div>
                                        )}

                                        {paymentMethod === 'cash' && (
                                            <div className="flex items-start gap-2 rounded-lg border bg-muted/50 p-3 text-sm text-muted-foreground">
                                                <Info className="mt-0.5 size-4 shrink-0" />
                                                <span>Din tilmelding afventer godkendelse fra en instruktør, inden den aktiveres.</span>
                                            </div>
                                        )}
                                    </div>

                                    {selectedDate && (
                                        <input
                                            type="hidden"
                                            name="course_id"
                                            value={courses[format(selectedDate, 'yyyy-MM-dd')] ?? ''}
                                        />
                                    )}
                                    <input type="hidden" name="payment_method" value={paymentMethod} />

                                    <Button
                                        type="submit"
                                        className="w-full h-12 text-base"
                                        disabled={processing}
                                    >
                                        {processing && <Spinner />}
                                        {paymentMethod === 'stripe' ? 'Gå til betaling' : 'Opret konto og tilmeld'}
                                    </Button>
                                </>
                            )}
                        </Form>
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
