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
import { accentLineVariants, sectionHeadVariants, sectionLineVariants } from '@/lib/motion';
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
            <main className="bg-mk-base py-24 md:py-32">
                <div className="container mx-auto max-w-6xl px-6 lg:px-8">
                    <motion.div
                        className="mb-12"
                        variants={sectionHeadVariants}
                        initial="hidden"
                        animate="visible"
                    >
                        <motion.p className="mk-eyebrow" variants={sectionLineVariants}>Kontakt</motion.p>
                        <motion.div variants={accentLineVariants} className="mt-1 mb-3 h-px w-10 origin-left bg-mk-accent" />
                        <motion.h1 className="text-4xl font-bold text-mk-text sm:text-5xl leading-tight" variants={sectionLineVariants}>Kontakt os</motion.h1>
                        <motion.p className="mt-3 text-lg text-mk-muted" variants={sectionLineVariants}>
                            Skriv til os — vælg gerne pakke og hvornår du ønsker at starte. Vi svarer på hverdage.
                        </motion.p>
                    </motion.div>

                    <div className="grid gap-12 lg:grid-cols-2 lg:gap-16">
                        {/* Left: contact info */}
                        <motion.div
                            initial={{ opacity: 0, y: 12 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.4 }}
                            className="space-y-8"
                        >
                            {flash?.success ? (
                                <Alert className="border-emerald-500/30 bg-emerald-500/10 text-emerald-400">
                                    <CheckCircle2 className="text-emerald-400" />
                                    <AlertTitle className="text-emerald-300">Beskeden er sendt</AlertTitle>
                                    <AlertDescription className="text-emerald-400/80">{flash.success}</AlertDescription>
                                </Alert>
                            ) : null}

                            <div>
                                <h2 className="text-xs font-semibold uppercase tracking-widest text-mk-muted">
                                    Direkte kontakt
                                </h2>
                                <ul className="mt-6 space-y-6">
                                    <li className="flex items-start gap-4">
                                        <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-mk-accent/10">
                                            <MapPin className="h-5 w-5 text-mk-accent" aria-hidden />
                                        </div>
                                        <div>
                                            <p className="font-semibold text-mk-text">Adresse</p>
                                            <p className="mt-0.5 text-mk-muted">Køregade 123, København</p>
                                        </div>
                                    </li>
                                    <li className="flex items-start gap-4">
                                        <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-mk-accent/10">
                                            <Phone className="h-5 w-5 text-mk-accent" aria-hidden />
                                        </div>
                                        <div>
                                            <p className="font-semibold text-mk-text">Telefon</p>
                                            <a href={`tel:${tel}`} className="mt-0.5 text-mk-muted hover:text-mk-accent transition-colors">
                                                {phoneLabel}
                                            </a>
                                        </div>
                                    </li>
                                    <li className="flex items-start gap-4">
                                        <div className="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-mk-accent/10">
                                            <Mail className="h-5 w-5 text-mk-accent" aria-hidden />
                                        </div>
                                        <div>
                                            <p className="font-semibold text-mk-text">E-mail</p>
                                            <a href={`mailto:${emailAddr}`} className="mt-0.5 text-mk-muted hover:text-mk-accent transition-colors">
                                                {emailAddr}
                                            </a>
                                        </div>
                                    </li>
                                </ul>
                            </div>

                            <div className="rounded-2xl border border-mk-border bg-mk-surface p-6">
                                <h2 className="font-semibold text-mk-text">Åbningstider (kontor)</h2>
                                <p className="mt-2 text-sm leading-relaxed text-mk-muted">
                                    Mandag–fredag 9–17 · Lørdag 9–13 · Søndag lukket. Køretimer kan bookes uden for
                                    kontortid via portalen.
                                </p>
                            </div>
                        </motion.div>

                        {/* Right: contact form */}
                        <motion.div
                            initial={{ opacity: 0, y: 12 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.45, delay: 0.05 }}
                            className="rounded-2xl border border-mk-border bg-mk-surface p-6 md:p-8"
                        >
                            <h2 className="font-heading text-lg font-semibold text-mk-text">Send en besked</h2>
                            <p className="mt-1 text-sm text-mk-muted">
                                Udfyld formularen — vælg den pakke du kigger på, og hvornår du helst vil starte på hold.
                            </p>

                            <form onSubmit={submit} className="mt-8 space-y-6">
                                <div className="grid gap-5 sm:grid-cols-2">
                                    <div className="space-y-1.5">
                                        <Label htmlFor="name" className="text-mk-text/80 text-sm">
                                            Navn *
                                        </Label>
                                        <Input
                                            id="name"
                                            value={form.data.name}
                                            onChange={(e) => form.setData('name', e.target.value)}
                                            required
                                            autoComplete="name"
                                            className="border-mk-border bg-mk-surface-2 text-mk-text placeholder:text-mk-muted/50 focus-visible:border-mk-accent/40 focus-visible:ring-mk-accent/20"
                                        />
                                        <InputError message={form.errors.name} />
                                    </div>
                                    <div className="space-y-1.5">
                                        <Label htmlFor="email" className="text-mk-text/80 text-sm">
                                            E-mail *
                                        </Label>
                                        <Input
                                            id="email"
                                            type="email"
                                            value={form.data.email}
                                            onChange={(e) => form.setData('email', e.target.value)}
                                            required
                                            autoComplete="email"
                                            className="border-mk-border bg-mk-surface-2 text-mk-text placeholder:text-mk-muted/50 focus-visible:border-mk-accent/40 focus-visible:ring-mk-accent/20"
                                        />
                                        <InputError message={form.errors.email} />
                                    </div>
                                </div>

                                <div className="space-y-1.5">
                                    <Label htmlFor="phone" className="text-mk-text/80 text-sm">
                                        Telefon
                                    </Label>
                                    <Input
                                        id="phone"
                                        type="tel"
                                        value={form.data.phone}
                                        onChange={(e) => form.setData('phone', e.target.value)}
                                        autoComplete="tel"
                                        className="border-mk-border bg-mk-surface-2 text-mk-text placeholder:text-mk-muted/50 focus-visible:border-mk-accent/40 focus-visible:ring-mk-accent/20"
                                    />
                                    <InputError message={form.errors.phone} />
                                </div>

                                <div className="rounded-xl border border-mk-border bg-mk-surface-2/50 p-4 sm:p-5">
                                    <div className="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
                                        <div className="min-w-0">
                                            <p className="text-sm font-semibold text-mk-text">Pakke og holdstart</p>
                                            <p className="mt-1 text-sm leading-relaxed text-mk-muted">
                                                Vi hjælper dig gerne uden at du vælger — men hvis du allerede ved, hvad du kigger på, kan vi svare mere konkret.
                                            </p>
                                        </div>
                                        <Link
                                            href={packages.url()}
                                            className="inline-flex shrink-0 items-center justify-center rounded-lg border border-mk-border bg-mk-surface px-3 py-2 text-sm font-medium text-mk-text/80 shadow-sm transition-colors hover:border-mk-accent/30 hover:text-mk-text sm:py-1.5"
                                        >
                                            Sammenlign pakker
                                        </Link>
                                    </div>

                                    <fieldset className="mt-6 space-y-3">
                                        <legend className="flex items-center gap-2 text-sm font-semibold text-mk-text">
                                            <Package className="h-4 w-4 text-mk-accent" aria-hidden />
                                            Pakke
                                        </legend>
                                        <p className="text-sm text-mk-muted">Hvilken pakke er du mest interesseret i?</p>
                                        <div className="space-y-2">
                                            <label
                                                className={cn(
                                                    'flex cursor-pointer gap-3 rounded-lg border p-3 transition-colors',
                                                    form.data.offer_id === ''
                                                        ? 'border-mk-accent bg-mk-accent/[0.06] ring-1 ring-mk-accent/20'
                                                        : 'border-mk-border bg-mk-surface hover:border-mk-border/80',
                                                )}
                                            >
                                                <input
                                                    type="radio"
                                                    name="offer_choice"
                                                    className="mt-1 size-4 shrink-0 accent-[#E8001D]"
                                                    checked={form.data.offer_id === ''}
                                                    onChange={() => form.setData('offer_id', '')}
                                                />
                                                <span className="text-sm font-medium leading-snug text-mk-text">
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
                                                                ? 'border-mk-accent bg-mk-accent/[0.06] ring-1 ring-mk-accent/20'
                                                                : 'border-mk-border bg-mk-surface hover:border-mk-border/80',
                                                        )}
                                                    >
                                                        <input
                                                            type="radio"
                                                            name="offer_choice"
                                                            className="mt-1 size-4 shrink-0 accent-[#E8001D]"
                                                            checked={selected}
                                                            onChange={() => form.setData('offer_id', id)}
                                                        />
                                                        <span className="min-w-0 flex-1 text-sm leading-snug">
                                                            <span className="font-medium text-mk-text">{offer.name}</span>
                                                            <span className="mt-0.5 block text-mk-muted">
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
                                        <legend className="flex items-center gap-2 text-sm font-semibold text-mk-text">
                                            <CalendarClock className="h-4 w-4 text-mk-accent" aria-hidden />
                                            Ønsket holdstart
                                        </legend>
                                        <p className="text-sm text-mk-muted">Hvornår vil du helst starte?</p>
                                        <div className="grid gap-2 sm:grid-cols-2">
                                            <label
                                                className={cn(
                                                    'flex cursor-pointer items-start gap-3 rounded-lg border p-3 transition-colors sm:col-span-2',
                                                    form.data.preferred_hold_start === ''
                                                        ? 'border-mk-accent bg-mk-accent/[0.06] ring-1 ring-mk-accent/20'
                                                        : 'border-mk-border bg-mk-surface hover:border-mk-border/80',
                                                )}
                                            >
                                                <input
                                                    type="radio"
                                                    name="hold_start_choice"
                                                    className="mt-1 size-4 shrink-0 accent-[#E8001D]"
                                                    checked={form.data.preferred_hold_start === ''}
                                                    onChange={() => form.setData('preferred_hold_start', '')}
                                                />
                                                <span className="text-sm font-medium leading-snug text-mk-text">
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
                                                                ? 'border-mk-accent bg-mk-accent/[0.06] ring-1 ring-mk-accent/20'
                                                                : 'border-mk-border bg-mk-surface hover:border-mk-border/80',
                                                        )}
                                                    >
                                                        <input
                                                            type="radio"
                                                            name="hold_start_choice"
                                                            className="mt-1 size-4 shrink-0 accent-[#E8001D]"
                                                            checked={selected}
                                                            onChange={() => form.setData('preferred_hold_start', opt.value)}
                                                        />
                                                        <span className="text-sm font-medium leading-snug text-mk-text">
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
                                    <Label htmlFor="message" className="text-mk-text/80 text-sm">
                                        Besked
                                    </Label>
                                    <textarea
                                        id="message"
                                        rows={5}
                                        className="flex min-h-[120px] w-full rounded-md border border-mk-border bg-mk-surface-2 px-3 py-2 text-sm text-mk-text shadow-xs placeholder:text-mk-muted/50 transition-[color,box-shadow] outline-none focus-visible:border-mk-accent/40 focus-visible:ring-[3px] focus-visible:ring-mk-accent/20 md:text-sm"
                                        value={form.data.message}
                                        onChange={(e) => form.setData('message', e.target.value)}
                                        placeholder="Fx spørgsmål om hold, tilmelding eller om du vil bookes til intro …"
                                    />
                                    <InputError message={form.errors.message} />
                                </div>

                                <Button
                                    type="submit"
                                    className="w-full rounded-full bg-mk-accent text-white hover:bg-mk-accent-soft hover:scale-[1.02] transition-all duration-200 sm:w-auto"
                                    disabled={form.processing}
                                >
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
