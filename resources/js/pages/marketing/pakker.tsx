import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { ArrowRight, CheckCircle2 } from 'lucide-react';
import MarketingLayout from '@/layouts/marketing-layout';
import type { MarketingOffer } from '@/types/marketing-offer';
import { show as bookOffer } from '@/routes/enrollment';
import { contact } from '@/routes/marketing';
import { show as packageShow } from '@/routes/marketing/packages';

type PakkerProps = {
    offers?: MarketingOffer[];
    addons?: MarketingOffer[];
};

export default function Pakker({ offers = [], addons = [] }: PakkerProps) {
    return (
        <MarketingLayout>
            <Head title="Pakker | Køreskole Pro" />
            <main className="overflow-hidden bg-white py-16 md:py-24">
                <div className="container mx-auto px-4 lg:px-8">
                    <motion.div
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.5 }}
                        className="mx-auto mb-16 max-w-2xl text-center"
                    >
                        <h1 className="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Vores pakker</h1>
                        <p className="mt-4 text-lg text-slate-600">Gennemskuelige priser uden skjulte gebyrer.</p>
                    </motion.div>

                    <div className="mx-auto grid max-w-5xl gap-8 md:grid-cols-2">
                        {offers.length > 0 ? (
                            offers.map((offer, index) => (
                                <motion.div
                                    key={offer.id}
                                    initial={{ opacity: 0, y: 30 }}
                                    whileInView={{ opacity: 1, y: 0 }}
                                    viewport={{ once: true, margin: '-50px' }}
                                    transition={{ duration: 0.5, delay: index * 0.1 }}
                                    className="marketing-glass-panel relative flex flex-col p-8"
                                >
                                    {index === 0 && (
                                        <div className="absolute right-8 top-0 inline-flex -translate-y-1/2 items-center gap-1 rounded-full bg-primary px-4 py-1 text-sm font-semibold text-primary-foreground">
                                            Mest populære
                                        </div>
                                    )}
                                    <div className="mb-6">
                                        <h2 className="text-2xl font-bold text-slate-900">
                                            <Link
                                                href={packageShow.url(offer.slug)}
                                                className="rounded-sm transition-colors hover:text-primary focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring ring-offset-2"
                                            >
                                                {offer.name}
                                            </Link>
                                        </h2>
                                        <p className="mt-2 text-muted-foreground">{offer.description}</p>
                                        <p className="mt-3">
                                            <Link
                                                href={packageShow.url(offer.slug)}
                                                className="text-sm font-medium text-primary hover:underline"
                                            >
                                                Læs mere om pakken
                                            </Link>
                                        </p>
                                        <div className="mt-6 flex items-baseline pb-2 text-5xl font-extrabold">
                                            {Number(offer.price).toLocaleString('da-DK')}
                                            <span className="ml-1 text-xl font-medium text-slate-500">DKK</span>
                                        </div>
                                    </div>
                                    <ul className="mb-8 flex-1 space-y-4 text-slate-800">
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
                                    <Link
                                        href={bookOffer.url(offer.slug)}
                                        className="mt-auto inline-flex h-12 w-full items-center justify-center rounded-xl bg-primary px-6 text-base font-medium text-primary-foreground transition-colors hover:bg-primary/90"
                                    >
                                        Tilmeld dig nu <ArrowRight className="ml-2 h-5 w-5" />
                                    </Link>
                                </motion.div>
                            ))
                        ) : (
                            <div className="col-span-full py-12 text-center text-slate-600">
                                Ingen pakker tilgængelige i øjeblikket. Tjek venligst tilbage senere.
                            </div>
                        )}
                    </div>

                    {addons.length > 0 ? (
                        <motion.section
                            initial={{ opacity: 0, y: 16 }}
                            whileInView={{ opacity: 1, y: 0 }}
                            viewport={{ once: true, margin: '-40px' }}
                            transition={{ duration: 0.45 }}
                            className="mx-auto mt-20 max-w-3xl"
                        >
                            <h2 className="text-center text-2xl font-bold tracking-tight text-slate-900">
                                Tilvalg og ekstra priser
                            </h2>
                            <p className="mx-auto mt-3 max-w-xl text-center text-slate-600">
                                Tillæg ud over din hovedpakke — bookes typisk sammen med eller efter tilmelding. Kontakt os
                                gerne, hvis du er i tvivl om, hvad du skal bruge.
                            </p>
                            <ul className="mt-10 divide-y divide-slate-200 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50/50 shadow-sm">
                                {addons.map((addon) => (
                                    <li
                                        key={addon.id}
                                        className="flex flex-col gap-2 px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:gap-6"
                                    >
                                        <div className="min-w-0">
                                            <p className="font-semibold text-slate-900">{addon.name}</p>
                                            {addon.description ? (
                                                <p className="mt-1 text-sm leading-relaxed text-slate-600">
                                                    {addon.description}
                                                </p>
                                            ) : null}
                                        </div>
                                        <p className="shrink-0 text-lg font-semibold tabular-nums text-slate-900">
                                            {Number(addon.price).toLocaleString('da-DK')}{' '}
                                            <span className="text-base font-medium text-slate-500">kr.</span>
                                        </p>
                                    </li>
                                ))}
                            </ul>
                            <p className="mt-8 text-center text-sm text-slate-600">
                                Spørgsmål om tilvalg?{' '}
                                <Link href={contact.url()} className="font-medium text-primary hover:underline">
                                    Skriv til os
                                </Link>
                            </p>
                        </motion.section>
                    ) : null}
                </div>
            </main>
        </MarketingLayout>
    );
}
