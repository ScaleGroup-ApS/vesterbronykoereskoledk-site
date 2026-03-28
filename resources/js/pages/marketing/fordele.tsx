import { Head } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { Car, Clock, ShieldCheck } from 'lucide-react';
import MarketingLayout from '@/layouts/marketing-layout';

export default function Fordele() {
    return (
        <MarketingLayout>
            <Head title="Fordele | Køreskole Pro" />
            <main className="overflow-hidden bg-white py-16 md:py-24">
                <div className="container mx-auto px-4 lg:px-8">
                    <motion.div
                        initial={{ opacity: 0, y: 20 }}
                        animate={{ opacity: 1, y: 0 }}
                        transition={{ duration: 0.5 }}
                        className="text-center mb-16 max-w-2xl mx-auto"
                    >
                        <h1 className="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">
                            Hvorfor vælge os?
                        </h1>
                        <p className="mt-4 text-lg text-slate-600">
                            Alt hvad du har brug for for at bestå din køreprøve og mere til.
                        </p>
                    </motion.div>
                    <div className="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                        <motion.div
                            initial={{ opacity: 0, y: 20 }}
                            whileInView={{ opacity: 1, y: 0 }}
                            viewport={{ once: true, margin: '-50px' }}
                            transition={{ duration: 0.5, delay: 0.1 }}
                            className="marketing-glass-card flex flex-col items-center text-center p-6 hover:-translate-y-1"
                        >
                            <div className="h-14 w-14 rounded-full bg-primary/10 flex items-center justify-center mb-6 text-primary">
                                <ShieldCheck size={28} />
                            </div>
                            <h2 className="text-xl font-bold mb-3">Høj beståelsesprocent</h2>
                            <p className="text-muted-foreground">
                                Vores skræddersyede undervisningsmetoder sikrer, at du er fuldt forberedt til at bestå både teori- og køreprøve.
                            </p>
                        </motion.div>
                        <motion.div
                            initial={{ opacity: 0, y: 20 }}
                            whileInView={{ opacity: 1, y: 0 }}
                            viewport={{ once: true, margin: '-50px' }}
                            transition={{ duration: 0.5, delay: 0.2 }}
                            className="marketing-glass-card flex flex-col items-center text-center p-6 hover:-translate-y-1"
                        >
                            <div className="h-14 w-14 rounded-full bg-accent/10 flex items-center justify-center mb-6 text-accent">
                                <Car size={28} />
                            </div>
                            <h2 className="mb-3 text-xl font-bold text-slate-900">Moderne biler</h2>
                            <p className="text-slate-600">
                                Lær at køre i sikre, letkørte og miljøvenlige biler, der er udstyret med de nyeste sikkerhedsfunktioner.
                            </p>
                        </motion.div>
                        <motion.div
                            initial={{ opacity: 0, y: 20 }}
                            whileInView={{ opacity: 1, y: 0 }}
                            viewport={{ once: true, margin: '-50px' }}
                            transition={{ duration: 0.5, delay: 0.3 }}
                            className="marketing-glass-card flex flex-col items-center text-center p-6 hover:-translate-y-1 sm:col-span-2 lg:col-span-1"
                        >
                            <div className="h-14 w-14 rounded-full bg-primary/10 flex items-center justify-center mb-6 text-primary">
                                <Clock size={28} />
                            </div>
                            <h2 className="mb-3 text-xl font-bold text-slate-900">Fleksibel planlægning</h2>
                            <p className="text-slate-600">
                                Book dine køre- og teoritimer på tidspunkter, der passer til din travle hverdag via vores online portal.
                            </p>
                        </motion.div>
                    </div>
                </div>
            </main>
        </MarketingLayout>
    );
}
