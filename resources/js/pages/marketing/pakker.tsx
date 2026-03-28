import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { ArrowRight, CheckCircle2 } from 'lucide-react';
import MarketingLayout from '@/layouts/marketing-layout';
import type { MarketingOffer } from '@/types/marketing-offer';
import { show as bookOffer } from '@/routes/enrollment';
import { contact } from '@/routes/marketing';
import { show as packageShow } from '@/routes/marketing/packages';

export default function Pakker({ offers = [] }: { offers?: MarketingOffer[] }) {
    return (
        <MarketingLayout>
            <Head title="Pakker | Køreskole Pro" />
            <main className="overflow-hidden bg-white py-16 md:py-24">
                <div className="container mx-auto px-4 lg:px-8">
                    <motion.div
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.5 }}
                        className="text-center mb-16 max-w-2xl mx-auto"
                    >
                        <h1 className="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Vores pakker</h1>
                        <p className="mt-4 text-lg text-slate-600">Gennemskuelige priser uden skjulte gebyrer.</p>
                    </motion.div>
                    <div className="grid gap-8 md:grid-cols-2 max-w-5xl mx-auto">
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
                                    {offer.type === 'primary' && (
                                        <div className="absolute top-0 right-8 -translate-y-1/2 rounded-full bg-primary px-4 py-1 text-sm font-semibold text-primary-foreground inline-flex items-center gap-1">
                                            Mest populære
                                        </div>
                                    )}
                                    <div className="mb-6">
                                        <h2 className="text-2xl font-bold text-slate-900">
                                            <Link
                                                href={packageShow.url(offer.slug)}
                                                className="hover:text-primary transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring ring-offset-2 rounded-sm"
                                            >
                                                {offer.name}
                                            </Link>
                                        </h2>
                                        <p className="text-muted-foreground mt-2">{offer.description}</p>
                                        <p className="mt-3">
                                            <Link
                                                href={packageShow.url(offer.slug)}
                                                className="text-sm font-medium text-primary hover:underline"
                                            >
                                                Læs mere om pakken
                                            </Link>
                                        </p>
                                        <div className="mt-6 flex items-baseline text-5xl font-extrabold pb-2">
                                            {Number(offer.price).toLocaleString('da-DK')}
                                            <span className="ml-1 text-xl font-medium text-slate-500">DKK</span>
                                        </div>
                                        {offer.type === 'addon' && (
                                            <p className="mt-2 text-sm text-slate-500">Ekstra tillægspakke</p>
                                        )}
                                    </div>
                                    <ul className="mb-8 flex-1 space-y-4 text-slate-800">
                                        {offer.theory_lessons > 0 && (
                                            <li className="flex items-center gap-3">
                                                <CheckCircle2 className="text-primary h-5 w-5 shrink-0" />
                                                <span>{offer.theory_lessons} Teoritimer</span>
                                            </li>
                                        )}
                                        {offer.driving_lessons > 0 && (
                                            <li className="flex items-center gap-3">
                                                <CheckCircle2 className="text-primary h-5 w-5 shrink-0" />
                                                <span>{offer.driving_lessons} Kørelektioner (45 min)</span>
                                            </li>
                                        )}
                                        {offer.track_required && (
                                            <li className="flex items-center gap-3">
                                                <CheckCircle2 className="text-primary h-5 w-5 shrink-0" />
                                                <span>Manøvrebane</span>
                                            </li>
                                        )}
                                        {offer.slippery_required && (
                                            <li className="flex items-center gap-3">
                                                <CheckCircle2 className="text-primary h-5 w-5 shrink-0" />
                                                <span>Køreteknisk anlæg (Glatbane)</span>
                                            </li>
                                        )}
                                    </ul>
                                    {offer.type === 'primary' ? (
                                        <Link
                                            href={bookOffer.url(offer.slug)}
                                            className="mt-auto inline-flex h-12 w-full items-center justify-center rounded-xl px-6 text-base font-medium transition-colors bg-primary text-primary-foreground hover:bg-primary/90"
                                        >
                                            Tilmeld dig nu <ArrowRight className="ml-2 h-5 w-5" />
                                        </Link>
                                    ) : (
                                        <Link
                                            href={contact.url()}
                                            className="mt-auto inline-flex h-12 w-full items-center justify-center rounded-xl px-6 text-base font-medium transition-colors border border-input bg-background hover:bg-accent hover:text-accent-foreground hover:border-accent"
                                        >
                                            Kontakt os
                                        </Link>
                                    )}
                                </motion.div>
                            ))
                        ) : (
                            <div className="col-span-full py-12 text-center text-slate-600">
                                Ingen pakker tilgængelige i øjeblikket. Tjek venligst tilbage senere.
                            </div>
                        )}
                    </div>
                </div>
            </main>
        </MarketingLayout>
    );
}
