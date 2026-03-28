import { Head, Link } from '@inertiajs/react';
import { ChevronDown } from 'lucide-react';
import MarketingLayout from '@/layouts/marketing-layout';
import { contact } from '@/routes/marketing';

type FaqItem = { question: string; answer: string };

export default function Faq({ items }: { items: FaqItem[] }) {
    return (
        <MarketingLayout>
            <Head title="FAQ | Køreskole Pro" />
            <main className="bg-white py-16 md:py-24">
                <div className="container mx-auto max-w-2xl px-4 lg:px-8">
                    <h1 className="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Ofte stillede spørgsmål</h1>
                    <div className="mt-5 space-y-3 leading-relaxed text-slate-600">
                        <p>
                            Her kan du finde svar på det, vi oftest bliver spurgt om. Mange ting er dækket her — så læs
                            gerne igennem, før du ringer. Finder du ikke det, du leder efter, er du altid velkommen til at
                            skrive fra{' '}
                            <Link href={contact.url()} className="font-medium text-primary underline-offset-4 hover:underline">
                                kontaktsiden
                            </Link>
                            .
                        </p>
                        <p className="text-sm text-slate-500">
                            Gebyrer til prøver og krav til papir kan ændre sig. Vi henviser til Færdselsstyrelsen og
                            borger.dk for gældende regler.
                        </p>
                    </div>
                    <div className="mt-12 space-y-4">
                        {items.map((item, i) => (
                            <details
                                key={i}
                                className="marketing-glass-card group rounded-xl px-0 open:shadow-md"
                            >
                                <summary className="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-5 text-left text-[15px] font-medium leading-snug text-slate-900 marker:content-none [&::-webkit-details-marker]:hidden">
                                    {item.question}
                                    <ChevronDown className="size-4 shrink-0 text-slate-500 transition-transform duration-200 group-open:rotate-180" />
                                </summary>
                                <div className="border-t border-slate-200/80 px-5 pb-5 pt-3 text-sm leading-relaxed text-slate-600">
                                    {item.answer}
                                </div>
                            </details>
                        ))}
                    </div>
                </div>
            </main>
        </MarketingLayout>
    );
}
