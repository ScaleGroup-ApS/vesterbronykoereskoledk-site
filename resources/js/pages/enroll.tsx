import { Form, Head, Link } from '@inertiajs/react';
import { ArrowLeft, Car, CheckCircle2 } from 'lucide-react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { home } from '@/routes';
import { store } from '@/routes/enrollment';

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

export default function Enroll({ offer }: { offer: Offer }) {
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
                    {/* Offer Summary */}
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
                                            <Label htmlFor="cpr">CPR-nummer <span className="text-muted-foreground font-normal">(valgfrit)</span></Label>
                                            <Input
                                                id="cpr"
                                                name="cpr"
                                                placeholder="DDMMÅÅ-XXXX"
                                            />
                                            <InputError message={errors.cpr} />
                                        </div>

                                        <div className="grid gap-2">
                                            <Label htmlFor="start_date">Ønsket startdato <span className="text-muted-foreground font-normal">(valgfrit)</span></Label>
                                            <Input
                                                id="start_date"
                                                name="start_date"
                                                type="date"
                                            />
                                            <InputError message={errors.start_date} />
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

                                    <Button
                                        type="submit"
                                        className="w-full h-12 text-base"
                                        disabled={processing}
                                    >
                                        {processing && <Spinner />}
                                        Opret konto og tilmeld
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
