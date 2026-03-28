import { Head } from '@inertiajs/react';
import { motion } from 'framer-motion';
import MarketingLayout from '@/layouts/marketing-layout';
import { accentLineVariants, cardContainerVariants, cardVariants, sectionHeadVariants, sectionLineVariants } from '@/lib/motion';

type TeamRow = {
    id: number;
    name: string;
    description: string | null;
};

export default function VoresKorelaerere({ teams }: { teams: TeamRow[] }) {
    return (
        <MarketingLayout>
            <Head title="Vores kørelærere | Køreskole Pro" />
            <main className="bg-mk-base py-24 md:py-32">
                <div className="container mx-auto max-w-4xl px-6 lg:px-8">
                    <motion.div
                        className="mb-12"
                        variants={sectionHeadVariants}
                        initial="hidden"
                        animate="visible"
                    >
                        <motion.p className="mk-eyebrow" variants={sectionLineVariants}>Teamet</motion.p>
                        <motion.div variants={accentLineVariants} className="mt-1 mb-3 h-px w-10 origin-left bg-mk-accent" />
                        <motion.h1 className="text-4xl font-bold text-mk-text sm:text-5xl leading-tight" variants={sectionLineVariants}>Vores kørelærere</motion.h1>
                        <motion.p className="mt-4 max-w-2xl text-lg text-mk-muted" variants={sectionLineVariants}>
                            Vi arbejder i teams, så du møder faste kørelærere, der kender dig og dit forløb. Her er de hold, der underviser hos os.
                        </motion.p>
                    </motion.div>

                    <motion.div
                        variants={cardContainerVariants}
                        initial="hidden"
                        animate="visible"
                        className="grid gap-6 sm:grid-cols-2"
                    >
                        {teams.length > 0 ? (
                            teams.map((team) => (
                                <motion.div
                                    key={team.id}
                                    variants={cardVariants}
                                    whileHover={{ y: -6 }}
                                    className="mk-card p-6"
                                >
                                    <h2 className="font-heading text-lg font-semibold text-mk-text">{team.name}</h2>
                                    {team.description ? (
                                        <p className="mt-2 text-sm leading-relaxed text-mk-muted">
                                            {team.description}
                                        </p>
                                    ) : (
                                        <p className="mt-2 text-sm italic text-mk-muted/60">
                                            Beskrivelse kommer snart.
                                        </p>
                                    )}
                                </motion.div>
                            ))
                        ) : (
                            <p className="col-span-full py-12 text-center text-mk-muted">
                                Ingen teams er registreret endnu. Kom tilbage senere — eller kontakt os direkte.
                            </p>
                        )}
                    </motion.div>
                </div>
            </main>
        </MarketingLayout>
    );
}
