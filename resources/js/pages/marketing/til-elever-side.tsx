import { Head } from '@inertiajs/react';
import { motion } from 'framer-motion';
import MarketingLayout from '@/layouts/marketing-layout';
import { accentLineVariants, cardContainerVariants, cardVariants, sectionHeadVariants, sectionLineVariants } from '@/lib/motion';

type Section = { title: string; body: string };

export default function TilEleverSide({
    metaTitle,
    heading,
    lead,
    sections,
}: {
    metaTitle: string;
    heading: string;
    lead: string;
    sections: Section[];
}) {
    return (
        <MarketingLayout>
            <Head title={`${metaTitle} | Køreskole Pro`} />
            <main className="bg-mk-base py-24 md:py-32">
                <div className="container mx-auto max-w-3xl px-6 lg:px-8">
                    <motion.div
                        variants={sectionHeadVariants}
                        initial="hidden"
                        animate="visible"
                    >
                        <motion.p className="mk-eyebrow" variants={sectionLineVariants}>Til elever</motion.p>
                        <motion.div variants={accentLineVariants} className="mt-1 mb-3 h-px w-10 origin-left bg-mk-accent" />
                        <motion.h1
                            className="text-4xl font-bold text-mk-text sm:text-5xl leading-tight"
                            variants={sectionLineVariants}
                        >
                            {heading}
                        </motion.h1>
                        <motion.p
                            className="mt-4 max-w-2xl text-lg leading-relaxed text-mk-muted"
                            variants={sectionLineVariants}
                        >
                            {lead}
                        </motion.p>
                    </motion.div>

                    <motion.div
                        className="mt-12 space-y-6"
                        variants={cardContainerVariants}
                        initial="hidden"
                        animate="visible"
                    >
                        {sections.map((section, i) => (
                            <motion.section
                                key={i}
                                variants={cardVariants}
                                whileInView="visible"
                                viewport={{ once: true, margin: '-40px' }}
                                className="rounded-2xl border border-mk-border bg-mk-surface p-6 md:p-8"
                            >
                                <h2 className="font-heading text-xl font-semibold text-mk-text">{section.title}</h2>
                                <p className="mt-3 leading-relaxed text-mk-muted">{section.body}</p>
                            </motion.section>
                        ))}
                    </motion.div>
                </div>
            </main>
        </MarketingLayout>
    );
}
