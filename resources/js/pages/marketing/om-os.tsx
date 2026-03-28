import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import MarketingLayout from '@/layouts/marketing-layout';
import { contact } from '@/routes/marketing';
import { accentLineVariants, sectionHeadVariants, sectionLineVariants } from '@/lib/motion';

const values = [
    {
        title: 'Tryghed',
        body: 'Du skal vide, hvad der forventes, og hvordan vi når målet sammen.',
    },
    {
        title: 'Respekt',
        body: 'Alle lærer i deres eget tempo — vi tilpasser os dig.',
    },
    {
        title: 'Kvalitet',
        body: 'Vi investerer i materialer, biler og løbende efteruddannelse af vores team.',
    },
];

export default function OmOs() {
    return (
        <MarketingLayout>
            <Head title="Om os | Køreskole Pro" />
            <main className="bg-mk-base py-24 md:py-32">
                <div className="container mx-auto max-w-3xl px-6 lg:px-8">
                    <motion.div
                        variants={sectionHeadVariants}
                        initial="hidden"
                        animate="visible"
                    >
                        <motion.p className="mk-eyebrow" variants={sectionLineVariants}>Om os</motion.p>
                        <motion.div variants={accentLineVariants} className="mt-1 mb-3 h-px w-10 origin-left bg-mk-accent" />
                        <motion.h1 className="text-4xl font-bold text-mk-text sm:text-5xl leading-tight" variants={sectionLineVariants}>
                            Om Køreskole Pro
                        </motion.h1>
                        <p className="mt-6 text-lg leading-relaxed text-mk-muted">
                            Vi er et team af erfarne kørelærere, der brænder for at gøre danske veje sikrere — én elev ad gangen. Siden
                            starten har vi fokuseret på tydelig kommunikation, struktureret undervisning og et læringsmiljø, hvor du kan
                            stille spørgsmål uden at føle dig presset.
                        </p>
                        <p className="mt-4 text-lg leading-relaxed text-mk-muted">
                            Vores bilpark opdateres løbende, og vi følger udviklingen i både teknologi og færdselsregler, så du møder
                            undervisning, der matcher det, du møder ved køreprøven og i trafikken.
                        </p>

                        {/* Values */}
                        <div className="mt-16">
                            <p className="mk-eyebrow">Vores fundament</p>
                            <h2 className="text-2xl font-semibold text-mk-text">Vores værdier</h2>
                            <div className="mt-8 space-y-4">
                                {values.map((value, i) => (
                                    <motion.div
                                        key={value.title}
                                        initial={{ opacity: 0, x: -16 }}
                                        whileInView={{ opacity: 1, x: 0 }}
                                        viewport={{ once: true }}
                                        transition={{ duration: 0.4, delay: i * 0.08 }}
                                        className="flex gap-4 rounded-xl border border-mk-border bg-mk-surface p-5"
                                    >
                                        <div className="mt-0.5 h-2 w-2 shrink-0 rounded-full bg-mk-accent" />
                                        <div>
                                            <p className="font-semibold text-mk-text">{value.title}</p>
                                            <p className="mt-1 text-sm leading-relaxed text-mk-muted">{value.body}</p>
                                        </div>
                                    </motion.div>
                                ))}
                            </div>
                        </div>

                        <p className="mt-12">
                            <Link href={contact.url()} className="mk-btn-primary">
                                Kontakt os
                            </Link>
                        </p>
                    </motion.div>
                </div>
            </main>
        </MarketingLayout>
    );
}
