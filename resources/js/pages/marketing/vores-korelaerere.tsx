import { Head } from '@inertiajs/react';
import { motion } from 'framer-motion';
import MarketingLayout from '@/layouts/marketing-layout';

type TeamRow = {
    id: number;
    name: string;
    description: string | null;
};

export default function VoresKorelaerere({ teams }: { teams: TeamRow[] }) {
    return (
        <MarketingLayout>
            <Head title="Vores kørelærere | Køreskole Pro" />
            <main className="bg-white py-16 md:py-24">
                <div className="container mx-auto max-w-4xl px-4 lg:px-8">
                    <h1 className="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Vores kørelærere</h1>
                    <p className="mt-4 max-w-2xl text-lg text-slate-600">
                        Vi arbejder i teams, så du møder faste kørelærere, der kender dig og dit forløb. Her er de hold, der underviser hos os.
                    </p>
                    <div className="mt-12 grid gap-6 sm:grid-cols-2">
                        {teams.length > 0 ? (
                            teams.map((team, index) => (
                                <motion.div
                                    key={team.id}
                                    initial={{ opacity: 0, y: 12 }}
                                    whileInView={{ opacity: 1, y: 0 }}
                                    viewport={{ once: true }}
                                    transition={{ duration: 0.35, delay: index * 0.05 }}
                                    className="marketing-glass-card p-6"
                                >
                                    <h2 className="text-lg font-semibold text-slate-900">{team.name}</h2>
                                    {team.description ? (
                                        <p className="mt-2 text-sm leading-relaxed text-slate-600">
                                            {team.description}
                                        </p>
                                    ) : (
                                        <p className="mt-2 text-sm italic text-slate-500">
                                            Beskrivelse kommer snart.
                                        </p>
                                    )}
                                </motion.div>
                            ))
                        ) : (
                            <p className="col-span-full py-12 text-center text-slate-600">
                                Ingen teams er registreret endnu. Kom tilbage senere — eller kontakt os direkte.
                            </p>
                        )}
                    </div>
                </div>
            </main>
        </MarketingLayout>
    );
}
