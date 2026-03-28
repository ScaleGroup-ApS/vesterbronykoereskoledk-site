import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { ArrowLeft, ArrowRight, CheckCircle2, ChevronRight } from 'lucide-react';
import MarketingLayout from '@/layouts/marketing-layout';
import type { MarketingOffer } from '@/types/marketing-offer';
import { home } from '@/routes';
import { show as bookOffer } from '@/routes/enrollment';
import { contact, packages } from '@/routes/marketing';

export default function PakkeShow({ offer }: { offer: MarketingOffer }) {
    const priceFormatted = Number(offer.price).toLocaleString('da-DK');

    return (
        <MarketingLayout>
            <Head title={`${offer.name} | Køreskole Pro`} />
            <main className="overflow-hidden bg-white">
                <section className="border-b border-slate-200/80 bg-gradient-to-b from-slate-50 to-white">
                    <div className="container mx-auto max-w-6xl px-4 pb-10 pt-8 md:pb-14 md:pt-12 lg:px-8">
                        <motion.div
                            initial={{ opacity: 0, y: 10 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.4 }}
                        >
                            <nav
                                className="flex flex-wrap items-center gap-x-2 gap-y-1 text-sm text-slate-500"
                                aria-label="Brødkrumme"
                            >
                                <Link href={home()} className="hover:text-primary">
                                    Forside
                                </Link>
                                <ChevronRight className="h-4 w-4 shrink-0 text-slate-400" aria-hidden />
                                <Link href={packages.url()} className="hover:text-primary">
                                    Pakker
                                </Link>
                                <ChevronRight className="h-4 w-4 shrink-0 text-slate-400" aria-hidden />
                                <span className="font-medium text-slate-800">{offer.name}</span>
                            </nav>

                            <Link
                                href={packages.url()}
                                className="mt-6 inline-flex items-center gap-2 text-sm font-medium text-primary hover:underline"
                            >
                                <ArrowLeft className="h-4 w-4" />
                                Tilbage til alle pakker
                            </Link>

                            <div className="mt-8">
                                <p className="text-xs font-semibold uppercase tracking-wider text-primary/90">Lovpakke</p>
                                <h1 className="mt-2 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl lg:text-[2.5rem] lg:leading-tight">
                                    {offer.name}
                                </h1>
                                {offer.description && (
                                    <p className="marketing-lead mt-4 max-w-2xl whitespace-pre-line text-lg text-slate-600">
                                        {offer.description}
                                    </p>
                                )}
                            </div>
                        </motion.div>
                    </div>
                </section>

                <div className="container mx-auto max-w-6xl px-4 py-12 md:py-16 lg:px-8">
                    <div className="grid gap-10 lg:grid-cols-12 lg:gap-12 lg:items-start">
                        <motion.div
                            initial={{ opacity: 0, y: 16 }}
                            animate={{ opacity: 1, y: 0 }}
                            transition={{ duration: 0.45, delay: 0.05 }}
                            className="space-y-10 lg:col-span-7"
                        >
                            <section className="marketing-glass-panel p-6 md:p-8">
                                <h2 className="text-lg font-semibold tracking-tight text-slate-900">
                                    Inkluderet i pakken
                                </h2>
                                <ul className="mt-6 space-y-4 text-slate-800">
                                    {offer.theory_lessons > 0 && (
                                        <li className="flex items-center gap-3">
                                            <CheckCircle2 className="h-5 w-5 shrink-0 text-primary" />
                                            <span>{offer.theory_lessons} teoritimer</span>
                                        </li>
                                    )}
                                    {offer.driving_lessons > 0 && (
                                        <li className="flex items-center gap-3">
                                            <CheckCircle2 className="h-5 w-5 shrink-0 text-primary" />
                                            <span>{offer.driving_lessons} kørelektioner (45 min)</span>
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
                                            <span>Køreteknisk anlæg (glatbane)</span>
                                        </li>
                                    )}
                                </ul>
                            </section>
                        </motion.div>

                        <aside className="lg:col-span-5">
                            <div className="marketing-glass-panel sticky top-24 space-y-6 p-6 md:p-8">
                                <div>
                                    <p className="text-sm font-medium text-slate-500">Samlet pris</p>
                                    <div className="mt-1 flex items-baseline text-4xl font-extrabold tracking-tight text-slate-900 md:text-5xl">
                                        {priceFormatted}
                                        <span className="ml-2 text-xl font-medium text-slate-500">DKK</span>
                                    </div>
                                </div>
                                <Link
                                    href={bookOffer.url(offer.slug)}
                                    className="inline-flex h-12 w-full items-center justify-center rounded-xl bg-primary px-6 text-base font-medium text-primary-foreground shadow-[0_8px_32px_-8px_rgba(37,99,235,0.45)] transition-colors hover:bg-primary/90"
                                >
                                    Tilmeld dig nu
                                    <ArrowRight className="ml-2 h-5 w-5" />
                                </Link>
                                <p className="text-center text-sm text-slate-600">
                                    Har du spørgsmål?{' '}
                                    <Link href={contact.url()} className="font-medium text-primary hover:underline">
                                        Kontakt os
                                    </Link>
                                </p>
                            </div>
                        </aside>
                    </div>
                </div>
            </main>
        </MarketingLayout>
    );
}
