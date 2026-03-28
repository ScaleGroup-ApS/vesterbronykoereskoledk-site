import { Head } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { Car, Clock, ShieldCheck } from 'lucide-react';
import MarketingLayout from '@/layouts/marketing-layout';
import { accentLineVariants, cardContainerVariants, cardVariants, sectionHeadVariants, sectionLineVariants } from '@/lib/motion';

const features = [
    {
        icon: ShieldCheck,
        title: 'Høj beståelsesprocent',
        body: 'Vores skræddersyede undervisningsmetoder sikrer, at du er fuldt forberedt til at bestå både teori- og køreprøve.',
        color: 'text-mk-accent',
        bg: 'bg-mk-accent/10',
    },
    {
        icon: Car,
        title: 'Moderne biler',
        body: 'Lær at køre i sikre, letkørte og miljøvenlige biler, der er udstyret med de nyeste sikkerhedsfunktioner.',
        color: 'text-mk-accent',
        bg: 'bg-mk-accent/10',
    },
    {
        icon: Clock,
        title: 'Fleksibel planlægning',
        body: 'Book dine køre- og teoritimer på tidspunkter, der passer til din travle hverdag via vores online portal.',
        color: 'text-mk-accent',
        bg: 'bg-mk-accent/10',
    },
];

export default function Fordele() {
    return (
        <MarketingLayout>
            <Head title="Fordele | Køreskole Pro" />
            <main className="overflow-hidden bg-mk-base py-24 md:py-32">
                <div className="container mx-auto max-w-7xl px-6 lg:px-8">
                    <motion.div
                        className="mx-auto mb-16 max-w-2xl text-center"
                        variants={sectionHeadVariants}
                        initial="hidden"
                        animate="visible"
                    >
                        <motion.p className="mk-eyebrow" variants={sectionLineVariants}>Fordele</motion.p>
                        <motion.div variants={accentLineVariants} className="mx-auto mt-1 mb-3 h-px w-10 origin-left bg-mk-accent" />
                        <motion.h1 className="text-4xl font-bold text-mk-text sm:text-5xl leading-tight" variants={sectionLineVariants}>
                            Hvorfor vælge os?
                        </motion.h1>
                        <motion.p className="mt-4 text-lg text-mk-muted" variants={sectionLineVariants}>
                            Alt hvad du har brug for for at bestå din køreprøve — og mere til.
                        </motion.p>
                    </motion.div>

                    <motion.div
                        variants={cardContainerVariants}
                        initial="hidden"
                        animate="visible"
                        className="grid gap-8 sm:grid-cols-2 lg:grid-cols-3"
                    >
                        {features.map((feature, i) => {
                            const Icon = feature.icon;
                            return (
                                <motion.div
                                    key={feature.title}
                                    variants={cardVariants}
                                    whileHover={{ y: -8 }}
                                    className={`mk-card flex flex-col items-center p-8 text-center${i === 2 ? ' sm:col-span-2 lg:col-span-1' : ''}`}
                                >
                                    <motion.div
                                        whileHover={{ rotate: 10, scale: 1.15 }}
                                        transition={{ type: 'spring', stiffness: 280 }}
                                        className="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-mk-accent/10 text-mk-accent"
                                    >
                                        <Icon size={30} strokeWidth={1.8} />
                                    </motion.div>
                                    <h2 className="mb-3 font-heading text-xl font-bold text-mk-text">{feature.title}</h2>
                                    <p className="text-sm leading-relaxed text-mk-muted">{feature.body}</p>
                                </motion.div>
                            );
                        })}
                    </motion.div>
                </div>
            </main>
        </MarketingLayout>
    );
}
