import { Head } from '@inertiajs/react';
import MarketingLayout from '@/layouts/marketing-layout';

export default function Handelsbetingelser() {
    return (
        <MarketingLayout>
            <Head title="Handelsbetingelser | Køreskole Pro" />
            <main className="bg-white py-16 md:py-24">
                <article className="prose prose-slate container mx-auto max-w-3xl px-4 lg:px-8">
                    <h1>Handelsbetingelser</h1>
                    <p>
                        Disse betingelser er et udgangspunkt for dit køb og dit forløb hos os. Erstat indholdet med jeres juridisk
                        korrekte tekst (typisk med hjælp fra advokat).
                    </p>
                    <h2>Tilmelding og betaling</h2>
                    <p>Beskriv depositum, fakturering, fortrydelse og hvornår forløbet anses for påbegyndt.</p>
                    <h2>Undervisning og aflysning</h2>
                    <p>Beskriv regler for aflysning fra skolens og elevens side, gebyrer og gensbooking.</p>
                </article>
            </main>
        </MarketingLayout>
    );
}
