import { Head, Link, useForm, usePage } from '@inertiajs/react';
import { CalendarClock, CheckCircle2, Mail, MapPin, Package, Phone } from 'lucide-react';
import { motion } from 'framer-motion';
import ContactInquiryController from '@/actions/App/Http/Controllers/Marketing/ContactInquiryController';
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import MarketingLayout from '@/layouts/marketing-layout';
import { cn } from '@/lib/utils';
import { packages } from '@/routes/marketing';
import type { MarketingContact } from '@/types/marketing-contact';
import type { ContactPageOffer, HoldStartOption } from '@/types/marketing-contact-page';

type PageProps = {
    offers: ContactPageOffer[];
    holdStartOptions: HoldStartOption[];
    marketingContact: MarketingContact;
    flash?: {
        success?: string | null;
        error?: string | null;
    };
};

export default function Kontakt() {
    const { offers, holdStartOptions, marketingContact, flash } = usePage<PageProps>().props;

    const form = useForm({
        name: '',
        email: '',
        phone: '',
        offer_id: '',
        preferred_hold_start: '',
        message: '',
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        form.post(ContactInquiryController.url(), {
            preserveScroll: true,
            onSuccess: () => form.reset(),
        });
    }

    const tel = marketingContact.phone_href;
    const phoneLabel = marketingContact.phone;
    const emailAddr = marketingContact.email;

    return (
        <MarketingLayout>
            <Head title="Kontakt | Køreskole Pro" />
            <main className="bg-white py-12 md:py-20">
                <div className="container mx-auto max-w-6xl px-4 lg:px-8">
                    <div className="mx-auto max-w-2xl text-center md:mx-0 md:max-w-none md:text-left">
                        <h1 className="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Kontakt</h1>
                        <p className="mt-3 text-lg text-slate-600">
                            Skriv til os — vælg gerne pakke og hvornår du ønsker at starte. Vi svarer på hverdage.
                        </p>
                    </div>

                    <div className="mt-12 grid gap-12 lg:grid-cols-2 lg:gap-16">
                        <motion.div
                            initial={{ opacity: 0, y: 12 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.4 }}
                            className="space-y-8"
                        >
                            {flash?.success ? (
                                <Alert className="border-emerald-200 bg-emerald-50 text-emerald-950">
                                    <CheckCircle2 className="text-emerald-600" />
                                    <AlertTitle>Beskeden er sendt</AlertTitle>
                                    <AlertDescription>{flash.success}</AlertDescription>
                                </Alert>
                            ) : null}

                            <div>
                                <h2 className="text-sm font-semibold uppercase tracking-wide text-slate-500">
                                    Direkte kontakt
                                </h2>
                                <ul className="mt-6 space-y-5 text-base">
                                    <li className="flex items-start gap-4">
                                        <MapPin className="mt-0.5 h-6 w-6 shrink-0 text-primary" aria-hidden />
                                        <div>
                                            <p className="font-medium text-slate-900">Adresse</p>
                                            <p className="text-slate-600">Køregade 123, København</p>
                                        </div>
                                    </li>
                                    <li className="flex items-start gap-4">
                                        <Phone className="mt-0.5 h-6 w-6 shrink-0 text-primary" aria-hidden />
                                        <div>
                                            <p className="font-medium text-slate-900">Telefon</p>
                                            <a href={`tel:${tel}`} className="text-slate-600 hover:text-primary">
                                                {phoneLabel}
                                            </a>
                                        </div>
                                    </li>
                                    <li className="flex items-start gap-4">
                                        <Mail className="mt-0.5 h-6 w-6 shrink-0 text-primary" aria-hidden />
                                        <div>
                                            <p className="font-medium text-slate-900">E-mail</p>
                                            <a href={`mailto:${emailAddr}`} className="text-slate-600 hover:text-primary">
                                                {emailAddr}
                                            </a>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <div className="rounded-2xl border border-slate-200/90 bg-slate-50/80 p-6">
                                <h2 className="font-semibold text-slate-900">Åbningstider (kontor)</h2>
                                <p className="mt-2 text-sm text-slate-600">
                                    Mandag–fredag 9–17 · Lørdag 9–13 · Søndag lukket. Køretimer kan bookes uden for
                                    kontortid via portalen.
                                </p>
                            </div>
                        </motion.div>

                        <motion.div
                            initial={{ opacity: 0, y: 12 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.45, delay: 0.05 }}
                            className="rounded-2xl border border-slate-200/90 bg-white p-6 shadow-sm md:p-8"
                        >
                            <h2 className="text-lg font-semibold text-slate-900">Send en besked</h2>
                            <p className="mt-1 text-sm text-slate-600">
                                Udfyld formularen — vælg den pakke du kigger på, og hvornår du helst vil starte på hold.
                            </p>

                            <form onSubmit={submit} className="mt-8 space-y-6">
                                <div className="grid gap-5 sm:grid-cols-2">
                                    <div className="space-y-1.5">
                                        <Label htmlFor="name" className="text-slate-900">
                                            Navn *
                                        </Label>
                                        <Input
                                            id="name"
                                            value={form.data.name}
                                            onChange={(e) => form.setData('name', e.target.value)}
                                            required
                                            autoComplete="name"
                                        />
                                        <InputError message={form.errors.name} />
                                    </div>
                                    <div className="space-y-1.5">
                                        <Label htmlFor="email" className="text-slate-900">
                                            E-mail *
                                        </Label>
                                        <Input
                                            id="email"
                                            type="email"
                                            value={form.data.email}
                                            onChange={(e) => form.setData('email', e.target.value)}
                                            required
                                            autoComplete="email"
                                        />
                                        <InputError message={form.errors.email} />
                                    </div>
                                </div>

                                <div className="space-y-1.5">
                                    <Label htmlFor="phone" className="text-slate-900">
                                        Telefon
                                    </Label>
                                    <Input
                                        id="phone"
                                        type="tel"
                                        value={form.data.phone}
                                        onChange={(e) => form.setData('phone', e.target.value)}
                                        autoComplete="tel"
                                    />
                                    <InputError message={form.errors.phone} />
                                </div>

                                <div className="rounded-xl border border-slate-200 bg-slate-50/50 p-4 sm:p-5">
                                    <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
                                        <div className="min-w-0">
                                            <p className="text-sm font-semibold text-slate-900">Pakke og holdstart</p>
                                            <p className="mt-1 text-sm leading-relaxed text-slate-700">
                                                Vi hjælper dig gerne uden at du vælger — men hvis du allerede ved, hvad du
                                                kigger på, kan vi svare mere konkret.
                                            </p>
                                        </div>
                                        <Link
                                            href={packages.url()}
                                            className="inline-flex shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-900 shadow-sm transition-colors hover:border-primary/35 hover:bg-slate-50 sm:py-1.5"
                                        >
                                            Sammenlign pakker
                                        </Link>
                                    </div>

                                    <fieldset className="mt-6 space-y-3">
                                        <legend className="flex items-center gap-2 text-sm font-semibold text-slate-900">
                                            <Package className="h-4 w-4 text-primary" aria-hidden />
                                            Pakke
                                        </legend>
                                        <p className="text-sm text-slate-600">Hvilken pakke er du mest interesseret i?</p>
                                        <div className="space-y-2">
                                            <label
                                                className={cn(
                                                    'flex cursor-pointer gap-3 rounded-lg border p-3 transition-colors',
                                                    form.data.offer_id === ''
                                                        ? 'border-primary bg-primary/[0.06] ring-1 ring-primary/15'
                                                        : 'border-slate-200 bg-white hover:border-slate-300',
                                                )}
                                            >
                                                <input
                                                    type="radio"
                                                    name="offer_choice"
                                                    className="mt-1 size-4 shrink-0 accent-primary"
                                                    checked={form.data.offer_id === ''}
                                                    onChange={() => form.setData('offer_id', '')}
                                                />
                                                <span className="text-sm font-medium leading-snug text-slate-900">
                                                    Ikke valgt endnu — vil gerne have anbefaling
                                                </span>
                                            </label>
                                            {offers.map((offer) => {
                                                const id = String(offer.id);
                                                const selected = form.data.offer_id === id;
                                                return (
                                                    <label
                                                        key={offer.id}
                                                        className={cn(
                                                            'flex cursor-pointer gap-3 rounded-lg border p-3 transition-colors',
                                                            selected
                                                                ? 'border-primary bg-primary/[0.06] ring-1 ring-primary/15'
                                                                : 'border-slate-200 bg-white hover:border-slate-300',
                                                        )}
                                                    >
                                                        <input
                                                            type="radio"
                                                            name="offer_choice"
                                                            className="mt-1 size-4 shrink-0 accent-primary"
                                                            checked={selected}
                                                            onChange={() => form.setData('offer_id', id)}
                                                        />
                                                        <span className="min-w-0 flex-1 text-sm leading-snug">
                                                            <span className="font-medium text-slate-900">{offer.name}</span>
                                                            <span className="mt-0.5 block text-slate-700">
                                                                {Number(offer.price).toLocaleString('da-DK')} kr.
                                                            </span>
                                                        </span>
                                                    </label>
                                                );
                                            })}
                                        </div>
                                        <InputError message={form.errors.offer_id} />
                                    </fieldset>

                                    <fieldset className="mt-8 space-y-3">
                                        <legend className="flex items-center gap-2 text-sm font-semibold text-slate-900">
                                            <CalendarClock className="h-4 w-4 text-primary" aria-hidden />
                                            Ønsket holdstart
                                        </legend>
                                        <p className="text-sm text-slate-600">Hvornår vil du helst starte?</p>
                                        <div className="grid gap-2 sm:grid-cols-2">
                                            <label
                                                className={cn(
                                                    'flex cursor-pointer items-start gap-3 rounded-lg border p-3 transition-colors sm:col-span-2',
                                                    form.data.preferred_hold_start === ''
                                                        ? 'border-primary bg-primary/[0.06] ring-1 ring-primary/15'
                                                        : 'border-slate-200 bg-white hover:border-slate-300',
                                                )}
                                            >
                                                <input
                                                    type="radio"
                                                    name="hold_start_choice"
                                                    className="mt-1 size-4 shrink-0 accent-primary"
                                                    checked={form.data.preferred_hold_start === ''}
                                                    onChange={() => form.setData('preferred_hold_start', '')}
                                                />
                                                <span className="text-sm font-medium leading-snug text-slate-900">
                                                    Ikke valgt endnu
                                                </span>
                                            </label>
                                            {holdStartOptions.map((opt) => {
                                                const selected = form.data.preferred_hold_start === opt.value;
                                                return (
                                                    <label
                                                        key={opt.value}
                                                        className={cn(
                                                            'flex cursor-pointer gap-3 rounded-lg border p-3 transition-colors',
                                                            selected
                                                                ? 'border-primary bg-primary/[0.06] ring-1 ring-primary/15'
                                                                : 'border-slate-200 bg-white hover:border-slate-300',
                                                        )}
                                                    >
                                                        <input
                                                            type="radio"
                                                            name="hold_start_choice"
                                                            className="mt-1 size-4 shrink-0 accent-primary"
                                                            checked={selected}
                                                            onChange={() => form.setData('preferred_hold_start', opt.value)}
                                                        />
                                                        <span className="text-sm font-medium leading-snug text-slate-900">
                                                            {opt.label}
                                                        </span>
                                                    </label>
                                                );
                                            })}
                                        </div>
                                        <InputError message={form.errors.preferred_hold_start} />
                                    </fieldset>
                                </div>

                                <div className="space-y-1.5">
                                    <Label htmlFor="message" className="text-slate-900">
                                        Besked
                                    </Label>
                                    <textarea
                                        id="message"
                                        rows={5}
                                        className="border-input placeholder:text-slate-500 flex min-h-[120px] w-full rounded-md border bg-white px-3 py-2 text-sm text-slate-900 shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-primary/40 focus-visible:ring-[3px] focus-visible:ring-primary/20 focus-visible:shadow-[0_0_28px_-10px_rgba(59,130,246,0.25)] md:text-sm"
                                        value={form.data.message}
                                        onChange={(e) => form.setData('message', e.target.value)}
                                        placeholder="Fx spørgsmål om hold, tilmelding eller om du vil bookes til intro …"
                                    />
                                    <InputError message={form.errors.message} />
                                </div>

                                <Button type="submit" className="w-full sm:w-auto" disabled={form.processing}>
                                    {form.processing ? 'Sender …' : 'Send besked'}
                                </Button>
                            </form>
                        </motion.div>
                    </div>
                </div>
            </main>
        </MarketingLayout>
    );
}
