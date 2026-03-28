import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { ArrowRight, CheckCircle2 } from 'lucide-react';
import MarketingLayout from '@/layouts/marketing-layout';
import { accentLineVariants, cardContainerVariants, cardVariants, sectionHeadVariants, sectionLineVariants } from '@/lib/motion';
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
            <main className="overflow-hidden bg-mk-base py-24 md:py-32">
                <div className="container mx-auto max-w-7xl px-6 lg:px-8">
                    {/* Header */}
                    <motion.div
                        className="mx-auto mb-16 max-w-2xl text-center"
                        variants={sectionHeadVariants}
                        initial="hidden"
                        animate="visible"
                    >
                        <motion.p className="mk-eyebrow" variants={sectionLineVariants}>Priser & pakker</motion.p>
                        <motion.div variants={accentLineVariants} className="mx-auto mt-1 mb-3 h-px w-10 origin-left bg-mk-accent" />
                        <motion.h1 className="text-4xl font-bold text-mk-text sm:text-5xl leading-tight" variants={sectionLineVariants}>Vores pakker</motion.h1>
                        <motion.p className="mt-4 text-lg text-mk-muted" variants={sectionLineVariants}>Gennemskuelige priser uden skjulte gebyrer.</motion.p>
                    </motion.div>

                    {/* Package cards */}
                    <motion.div
                        variants={cardContainerVariants}
                        initial="hidden"
                        animate="visible"
                        className="mx-auto grid max-w-5xl gap-8 md:grid-cols-2"
                    >
                        {offers.length > 0 ? (
                            offers.map((offer, index) => (
                                <motion.div
                                    key={offer.id}
                                    variants={cardVariants}
                                    whileHover={{ y: -6 }}
                                    className="mk-card relative flex flex-col p-8"
                                >
                                    {/* Most popular badge */}
                                    {index === 0 && (
                                        <div className="absolute right-6 top-0 inline-flex -translate-y-1/2 items-center gap-1.5 rounded-full bg-mk-accent/10 border border-mk-accent/30 px-3 py-1 text-xs font-semibold text-mk-accent">
                                            <span className="h-1.5 w-1.5 rounded-full bg-mk-accent animate-pulse" />
                                            Mest populære
                                        </div>
                                    )}

                                    <div className="mb-6">
                                        <h2 className="font-heading text-2xl font-bold text-mk-text">
                                            <Link
                                                href={packageShow.url(offer.slug)}
                                                className="transition-colors hover:text-mk-accent focus-visible:outline-none"
                                            >
                                                {offer.name}
                                            </Link>
                                        </h2>
                                        <p className="mt-2 text-mk-muted">{offer.description}</p>
                                        <p className="mt-2">
                                            <Link
                                                href={packageShow.url(offer.slug)}
                                                className="text-sm font-medium text-mk-accent hover:underline underline-offset-4"
                                            >
                                                Læs mere om pakken →
                                            </Link>
                                        </p>

                                        {/* Price */}
                                        <div className="mt-6 flex items-baseline gap-1">
                                            <span className="text-5xl font-extrabold text-mk-accent font-heading" style={{ letterSpacing: '-0.03em' }}>
                                                {Number(offer.price).toLocaleString('da-DK')}
                                            </span>
                                            <span className="text-lg font-medium text-mk-muted">DKK</span>
                                        </div>
                                    </div>

                                    {/* Features */}
                                    <ul className="mb-8 flex-1 space-y-3">
                                        {offer.theory_lessons > 0 && (
                                            <li className="flex items-center gap-3 text-mk-text">
                                                <CheckCircle2 className="h-5 w-5 shrink-0 text-mk-accent" />
                                                <span className="text-sm">{offer.theory_lessons} Teoritimer</span>
                                            </li>
                                        )}
                                        {offer.driving_lessons > 0 && (
                                            <li className="flex items-center gap-3 text-mk-text">
                                                <CheckCircle2 className="h-5 w-5 shrink-0 text-mk-accent" />
                                                <span className="text-sm">{offer.driving_lessons} Kørelektioner (45 min)</span>
                                            </li>
                                        )}
                                        {offer.track_required && (
                                            <li className="flex items-center gap-3 text-mk-text">
                                                <CheckCircle2 className="h-5 w-5 shrink-0 text-mk-accent" />
                                                <span className="text-sm">Manøvrebane</span>
                                            </li>
                                        )}
                                        {offer.slippery_required && (
                                            <li className="flex items-center gap-3 text-mk-text">
                                                <CheckCircle2 className="h-5 w-5 shrink-0 text-mk-accent" />
                                                <span className="text-sm">Køreteknisk anlæg (Glatbane)</span>
                                            </li>
                                        )}
                                    </ul>

                                    {/* CTA */}
                                    <Link
                                        href={bookOffer.url(offer.slug)}
                                        className="mt-auto inline-flex h-12 w-full items-center justify-center rounded-xl border border-mk-border bg-white/[0.04] px-6 text-sm font-semibold text-mk-text transition-all duration-200 hover:bg-mk-accent hover:border-mk-accent hover:text-white hover:scale-[1.01]"
                                    >
                                        Tilmeld dig nu <ArrowRight className="ml-2 h-4 w-4" />
                                    </Link>
                                </motion.div>
                            ))
                        ) : (
                            <div className="col-span-full py-12 text-center text-mk-muted">
                                Ingen pakker tilgængelige i øjeblikket. Tjek venligst tilbage senere.
                            </div>
                        )}
                    </motion.div>

                    {/* Addons */}
                    {addons.length > 0 ? (
                        <motion.section
                            initial={{ opacity: 0, y: 16 }}
                            whileInView={{ opacity: 1, y: 0 }}
                            viewport={{ once: true, margin: '-40px' }}
                            transition={{ duration: 0.45 }}
                            className="mx-auto mt-24 max-w-3xl"
                        >
                            <motion.div
                                className="mb-10 text-center"
                                variants={sectionHeadVariants}
                                initial="hidden"
                                whileInView="visible"
                                viewport={{ once: true, margin: '-60px' }}
                            >
                                <motion.p className="mk-eyebrow" variants={sectionLineVariants}>Ekstra</motion.p>
                                <motion.div variants={accentLineVariants} className="mx-auto mt-1 mb-3 h-px w-10 origin-left bg-mk-accent" />
                                <motion.h2 className="text-3xl font-bold text-mk-text" variants={sectionLineVariants}>
                                    Tilvalg og ekstra priser
                                </motion.h2>
                                <motion.p className="mx-auto mt-3 max-w-xl text-mk-muted" variants={sectionLineVariants}>
                                    Tillæg ud over din hovedpakke — bookes typisk sammen med eller efter tilmelding.
                                </motion.p>
                            </motion.div>
                            <ul className="divide-y divide-mk-border overflow-hidden rounded-2xl border border-mk-border bg-mk-surface">
                                {addons.map((addon) => (
                                    <li
                                        key={addon.id}
                                        className="flex flex-col gap-2 px-5 py-4 transition-colors hover:bg-white/[0.02] sm:flex-row sm:items-center sm:justify-between sm:gap-6"
                                    >
                                        <div className="min-w-0">
                                            <p className="font-semibold text-mk-text">{addon.name}</p>
                                            {addon.description ? (
                                                <p className="mt-1 text-sm leading-relaxed text-mk-muted">
                                                    {addon.description}
                                                </p>
                                            ) : null}
                                        </div>
                                        <p className="shrink-0 font-heading text-lg font-semibold tabular-nums text-mk-accent">
                                            {Number(addon.price).toLocaleString('da-DK')}{' '}
                                            <span className="text-base font-medium text-mk-muted">kr.</span>
                                        </p>
                                    </li>
                                ))}
                            </ul>
                            <p className="mt-8 text-center text-sm text-mk-muted">
                                Spørgsmål om tilvalg?{' '}
                                <Link href={contact.url()} className="font-medium text-mk-accent hover:underline underline-offset-4">
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
