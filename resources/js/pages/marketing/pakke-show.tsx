import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { ArrowRight, CheckCircle2 } from 'lucide-react';
import MarketingLayout from '@/layouts/marketing-layout';
import type { MarketingOffer } from '@/types/marketing-offer';
import { show as bookOffer } from '@/routes/enrollment';
import { packages } from '@/routes/marketing';

export default function PakkeShow({ offer }: { offer: MarketingOffer }) {
    return (
        <MarketingLayout>
            <Head title={`${offer.name} | Køreskole Pro`} />
            <main className="overflow-hidden bg-white py-16 md:py-24">
                <div className="container mx-auto max-w-3xl px-4 lg:px-8">
                    <nav className="mb-8 text-sm text-slate-500">
                        <Link href={packages.url()} className="hover:text-primary">
                            Pakker
                        </Link>
                        <span className="mx-2">/</span>
                        <span className="text-slate-800">{offer.name}</span>
                    </nav>
                    <motion.div
                        initial={{ opacity: 0, y: 12 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.4 }}
                            className="marketing-glass-panel p-8 md:p-10"
                    >
                        {offer.type === 'primary' && (
                            <div className="mb-6 inline-block rounded-full bg-primary px-4 py-1 text-sm font-semibold text-primary-foreground">
                                Mest populære
                            </div>
                        )}
                        <h1 className="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">{offer.name}</h1>
                        {offer.description && (
                            <p className="mt-4 text-lg leading-relaxed text-slate-600">{offer.description}</p>
                        )}
                        <div className="mt-8 flex items-baseline text-5xl font-extrabold text-slate-900">
                            {Number(offer.price).toLocaleString('da-DK')}
                            <span className="ml-2 text-xl font-medium text-slate-500">DKK</span>
                        </div>
                        <ul className="mt-10 space-y-4 text-slate-800">
                            {offer.theory_lessons > 0 && (
                                <li className="flex items-center gap-3">
                                    <CheckCircle2 className="text-primary h-5 w-5 shrink-0" />
                                    <span>{offer.theory_lessons} teoritimer</span>
                                </li>
                            )}
                            {offer.driving_lessons > 0 && (
                                <li className="flex items-center gap-3">
                                    <CheckCircle2 className="text-primary h-5 w-5 shrink-0" />
                                    <span>{offer.driving_lessons} kørelektioner (45 min)</span>
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
                                    <span>Køreteknisk anlæg (glatbane)</span>
                                </li>
                            )}
                        </ul>
                        {offer.type === 'primary' ? (
                            <div className="mt-10">
                                <Link
                                    href={bookOffer.url(offer.slug)}
                                    className="inline-flex h-12 w-full items-center justify-center rounded-xl px-6 text-base font-medium transition-colors bg-primary text-primary-foreground shadow-[0_8px_32px_-8px_rgba(37,99,235,0.45)] hover:bg-primary/90 sm:w-auto"
                                >
                                    Tilmeld dig nu
                                    <ArrowRight className="ml-2 h-5 w-5" />
                                </Link>
                            </div>
                        ) : (
                            <p className="mt-10 text-sm text-slate-600">
                                Kontakt os for at høre mere om denne tillægspakke.
                            </p>
                        )}
                    </motion.div>
                </div>
            </main>
        </MarketingLayout>
    );
}
