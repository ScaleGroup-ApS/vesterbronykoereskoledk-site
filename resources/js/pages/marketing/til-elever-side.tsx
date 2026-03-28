import { Head } from '@inertiajs/react';
import MarketingLayout from '@/layouts/marketing-layout';

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
            <main className="bg-white py-16 md:py-24">
                <article className="container mx-auto max-w-3xl px-4 lg:px-8">
                    <h1 className="text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">{heading}</h1>
                    <p className="mt-4 text-lg leading-relaxed text-slate-600">{lead}</p>
                    <div className="mt-12 space-y-10">
                        {sections.map((section, i) => (
                            <section key={i}>
                                <h2 className="text-xl font-semibold tracking-tight text-slate-900">{section.title}</h2>
                                <p className="mt-3 leading-relaxed text-slate-600">
                                    {section.body}
                                </p>
                            </section>
                        ))}
                    </div>
                </article>
            </main>
        </MarketingLayout>
    );
}
