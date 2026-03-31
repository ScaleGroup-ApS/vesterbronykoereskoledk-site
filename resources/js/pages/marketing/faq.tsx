import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { ChevronDown } from 'lucide-react';
import MarketingLayout from '@/layouts/marketing-layout';
import { accentLineVariants, sectionHeadVariants, sectionLineVariants } from '@/lib/motion';
import { contact } from '@/routes/marketing';

type FaqItem = { question: string; answer: string };

export default function Faq({ items }: { items: FaqItem[] }) {
    return (
        <MarketingLayout>
            <Head title="FAQ | Køreskole Pro" />
            <main className="bg-mk-base py-24 md:py-32">
                <div className="container mx-auto max-w-2xl px-6 lg:px-8">
                    <motion.div
                        className="mb-12"
                        variants={sectionHeadVariants}
                        initial="hidden"
                        animate="visible"
                    >
                        <motion.p className="mk-eyebrow" variants={sectionLineVariants}>FAQ</motion.p>
                        <motion.div variants={accentLineVariants} className="mt-1 mb-3 h-px w-10 origin-left bg-mk-accent" />
                        <motion.h1 className="text-4xl font-bold text-mk-text sm:text-5xl leading-tight" variants={sectionLineVariants}>Ofte stillede spørgsmål</motion.h1>
                        <motion.div className="mt-5 space-y-3 leading-relaxed text-mk-muted" variants={sectionLineVariants}>
                            <p>
                                Her kan du finde svar på det, vi oftest bliver spurgt om. Mange ting er dækket her — så læs
                                gerne igennem, før du ringer. Finder du ikke det, du leder efter, er du altid velkommen til at
                                skrive fra{' '}
                                <Link href={contact.url()} className="font-medium text-mk-accent underline-offset-4 hover:underline">
                                    kontaktsiden
                                </Link>
                                .
                            </p>
                            <p className="text-sm text-mk-muted/60">
                                Gebyrer til prøver og krav til papir kan ændre sig. Vi henviser til Færdselsstyrelsen og
                                borger.dk for gældende regler.
                            </p>
                        </motion.div>
                    </motion.div>

                    <motion.div
                        initial="hidden"
                        animate="visible"
                        variants={{
                            hidden: {},
                            visible: { transition: { staggerChildren: 0.05 } },
                        }}
                        className="space-y-3"
                    >
                        {items.map((item, i) => (
                            <motion.div
                                key={i}
                                variants={{
                                    hidden: { opacity: 0, y: 12 },
                                    visible: { opacity: 1, y: 0 },
                                }}
                                transition={{ duration: 0.35 }}
                            >
                            <details className="group rounded-xl border border-mk-border bg-mk-surface open:border-mk-accent/20 open:shadow-[0_0_24px_rgba(232,0,29,0.06)] transition-all duration-200"
                            >
                                <summary className="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-5 text-left text-[15px] font-medium leading-snug text-mk-text marker:content-none [&::-webkit-details-marker]:hidden">
                                    {item.question}
                                    <ChevronDown className="size-4 shrink-0 text-mk-muted transition-transform duration-200 group-open:rotate-180 group-open:text-mk-accent" />
                                </summary>
                                <div className="border-t border-mk-border/60 px-5 pb-5 pt-4 text-sm leading-relaxed text-mk-muted">
                                    {item.answer}
                                </div>
                            </details>
                            </motion.div>
                        ))}
                    </motion.div>
                </div>
            </main>
        </MarketingLayout>
    );
}
