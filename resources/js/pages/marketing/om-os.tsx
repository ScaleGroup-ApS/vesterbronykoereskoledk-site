import { Head, Link } from '@inertiajs/react';
import { motion } from 'framer-motion';
import MarketingLayout from '@/layouts/marketing-layout';
import { contact } from '@/routes/marketing';

export default function OmOs() {
    return (
        <MarketingLayout>
            <Head title="Om os | Køreskole Pro" />
            <main className="bg-white py-16 md:py-24">
                <div className="container mx-auto max-w-3xl px-4 lg:px-8">
                    <motion.div
                        initial={{ opacity: 0, y: 16 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.45 }}
                    >
                        <h1 className="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                            Om Køreskole Pro
                        </h1>
                        <p className="mt-6 text-lg leading-relaxed text-slate-600">
                            Vi er et team af erfarne kørelærere, der brænder for at gøre danske veje sikrere — én elev ad gangen. Siden
                            starten har vi fokuseret på tydelig kommunikation, struktureret undervisning og et læringsmiljø, hvor du kan
                            stille spørgsmål uden at føle dig presset.
                        </p>
                        <p className="mt-4 text-lg text-muted-foreground leading-relaxed">
                            Vores bilpark opdateres løbende, og vi følger udviklingen i både teknologi og færdselsregler, så du møder
                            undervisning, der matcher det, du møder ved køreprøven og i trafikken.
                        </p>
                        <h2 className="mt-12 text-xl font-semibold tracking-tight text-slate-900">Vores værdier</h2>
                        <ul className="mt-4 space-y-3 text-slate-600">
                            <li>
                                <strong className="text-slate-900">Tryghed:</strong> Du skal vide, hvad der forventes, og hvordan vi
                                når målet sammen.
                            </li>
                            <li>
                                <strong className="text-slate-900">Respekt:</strong> Alle lærer i deres eget tempo — vi tilpasser os
                                dig.
                            </li>
                            <li>
                                <strong className="text-slate-900">Kvalitet:</strong> Vi investerer i materialer, biler og løbende
                                efteruddannelse af vores team.
                            </li>
                        </ul>
                        <p className="mt-10">
                            <Link
                                href={contact.url()}
                                className="inline-flex h-11 items-center justify-center rounded-md bg-primary px-6 text-sm font-medium text-primary-foreground shadow hover:bg-primary/90"
                            >
                                Kontakt os
                            </Link>
                        </p>
                    </motion.div>
                </div>
            </main>
        </MarketingLayout>
    );
}
