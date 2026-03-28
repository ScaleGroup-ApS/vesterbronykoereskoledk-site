import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { ArrowLeft, ArrowRight, CheckCircle2, ChevronRight } from 'lucide-react';
import MarketingLayout from '@/layouts/marketing-layout';
import type { MarketingOffer } from '@/types/marketing-offer';
import { accentLineVariants, sectionHeadVariants, sectionLineVariants } from '@/lib/motion';
import { home } from '@/routes';
import { show as bookOffer } from '@/routes/enrollment';
import { contact, packages } from '@/routes/marketing';

export default function PakkeShow({ offer }: { offer: MarketingOffer }) {
    const priceFormatted = Number(offer.price).toLocaleString('da-DK');

    return (
        <MarketingLayout>
            <Head title={`${offer.name} | Køreskole Pro`} />
            <main className="overflow-hidden bg-mk-base">

                {/* ── Hero header ── */}
                <section className="relative border-b border-mk-border bg-mk-surface">
                    {/* Subtle red glow */}
                    <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px" style={{ background: 'linear-gradient(to right, transparent, #2A2A2A, transparent)' }} aria-hidden />
                    <div className="container mx-auto max-w-6xl px-4 pb-12 pt-10 lg:px-8">
                        {/* Breadcrumb */}
                        <motion.nav
                            initial={{ opacity: 0, y: 8 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.35 }}
                            className="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-mk-muted"
                            aria-label="Brødkrumme"
                        >
                            <Link href={home()} className="transition-colors hover:text-mk-text">Forside</Link>
                            <ChevronRight className="h-3.5 w-3.5 shrink-0 text-mk-border" aria-hidden />
                            <Link href={packages.url()} className="transition-colors hover:text-mk-text">Pakker</Link>
                            <ChevronRight className="h-3.5 w-3.5 shrink-0 text-mk-border" aria-hidden />
                            <span className="font-medium text-mk-text">{offer.name}</span>
                        </motion.nav>

                        <Link
                            href={packages.url()}
                            className="mt-5 inline-flex items-center gap-2 text-sm font-medium text-mk-muted transition-colors hover:text-mk-accent"
                        >
                            <ArrowLeft className="h-4 w-4" />
                            Tilbage til alle pakker
                        </Link>

                        <motion.div
                            className="mt-8"
                            variants={sectionHeadVariants}
                            initial="hidden"
                            animate="visible"
                        >
                            <motion.p className="mk-eyebrow" variants={sectionLineVariants}>Lovpakke</motion.p>
                            <motion.div variants={accentLineVariants} className="mt-1 mb-3 h-px w-10 origin-left bg-mk-accent" />
                            <motion.h1
                                className="text-3xl font-bold tracking-tight text-mk-text sm:text-4xl lg:text-[2.5rem] lg:leading-tight"
                                variants={sectionLineVariants}
                            >
                                {offer.name}
                            </motion.h1>
                            {offer.description && (
                                <motion.p
                                    className="mt-4 max-w-2xl whitespace-pre-line text-lg leading-relaxed text-mk-muted"
                                    variants={sectionLineVariants}
                                >
                                    {offer.description}
                                </motion.p>
                            )}
                        </motion.div>
                    </div>
                </section>

                {/* ── Body ── */}
                <div className="container mx-auto max-w-6xl px-4 py-12 md:py-16 lg:px-8">
                    <div className="grid gap-10 lg:grid-cols-12 lg:items-start lg:gap-12">

                        {/* Left: features */}
                        <motion.div
                            initial={{ opacity: 0, y: 16 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.45, delay: 0.15 }}
                            className="space-y-6 lg:col-span-7"
                        >
                            <div className="mk-card p-6 md:p-8">
                                <h2 className="font-heading text-lg font-semibold text-mk-text">
                                    Inkluderet i pakken
                                </h2>
                                <ul className="mt-6 space-y-4">
                                    {offer.theory_lessons > 0 && (
                                        <li className="flex items-center gap-3 text-mk-text">
                                            <CheckCircle2 className="h-5 w-5 shrink-0 text-mk-accent" aria-hidden />
                                            <span>{offer.theory_lessons} teoritimer</span>
                                        </li>
                                    )}
                                    {offer.driving_lessons > 0 && (
                                        <li className="flex items-center gap-3 text-mk-text">
                                            <CheckCircle2 className="h-5 w-5 shrink-0 text-mk-accent" aria-hidden />
                                            <span>{offer.driving_lessons} kørelektioner (45 min)</span>
                                        </li>
                                    )}
                                    {offer.track_required && (
                                        <li className="flex items-center gap-3 text-mk-text">
                                            <CheckCircle2 className="h-5 w-5 shrink-0 text-mk-accent" aria-hidden />
                                            <span>Manøvrebane</span>
                                        </li>
                                    )}
                                    {offer.slippery_required && (
                                        <li className="flex items-center gap-3 text-mk-text">
                                            <CheckCircle2 className="h-5 w-5 shrink-0 text-mk-accent" aria-hidden />
                                            <span>Køreteknisk anlæg (glatbane)</span>
                                        </li>
                                    )}
                                </ul>
                            </div>
                        </motion.div>

                        {/* Right: price + CTA */}
                        <motion.aside
                            initial={{ opacity: 0, x: 16 }}
                            animate={{ opacity: 1, x: 0 }}
                            transition={{ duration: 0.5, delay: 0.2 }}
                            className="lg:col-span-5"
                        >
                            <div className="sticky top-24 overflow-hidden rounded-2xl border border-mk-border bg-mk-surface shadow-[0_0_40px_-12px_rgba(232,0,29,0.12)]">
                                <div className="h-[3px] w-full bg-mk-accent" />
                                <div className="space-y-6 p-6 md:p-8">
                                    <div>
                                        <p className="text-sm font-medium text-mk-muted">Samlet pris</p>
                                        <div className="mt-1 flex items-baseline font-heading">
                                            <span className="text-4xl font-extrabold text-mk-accent md:text-5xl" style={{ letterSpacing: '-0.03em' }}>
                                                {priceFormatted}
                                            </span>
                                            <span className="ml-2 text-xl font-medium text-mk-muted">DKK</span>
                                        </div>
                                    </div>
                                    <Link
                                        href={bookOffer.url(offer.slug)}
                                        className="inline-flex h-12 w-full items-center justify-center rounded-xl bg-mk-accent px-6 text-base font-semibold text-white shadow-[0_8px_32px_-8px_rgba(232,0,29,0.5)] transition-all duration-200 hover:bg-mk-accent-soft hover:shadow-[0_8px_40px_-8px_rgba(232,0,29,0.65)]"
                                    >
                                        Tilmeld dig nu
                                        <ArrowRight className="ml-2 h-5 w-5" />
                                    </Link>
                                    <p className="text-center text-sm text-mk-muted">
                                        Har du spørgsmål?{' '}
                                        <Link href={contact.url()} className="font-medium text-mk-accent underline-offset-4 hover:underline">
                                            Kontakt os
                                        </Link>
                                    </p>
                                </div>
                            </div>
                        </motion.aside>
                    </div>
                </div>
            </main>
        </MarketingLayout>
    );
}
