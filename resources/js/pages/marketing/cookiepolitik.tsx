import { Head } from '@inertiajs/react';
import MarketingLayout from '@/layouts/marketing-layout';

export default function Cookiepolitik() {
    return (
        <MarketingLayout>
            <Head title="Cookiepolitik | Køreskole Pro" />
            <main className="bg-white py-16 md:py-24">
                <article className="prose prose-slate container mx-auto max-w-3xl px-4 lg:px-8">
                    <h1>Cookiepolitik</h1>
                    <p>
                        Beskriv hvilke cookies I bruger (nødvendige, funktionelle, statistik, marketing), formål og hvordan brugeren kan
                        tilbagekalde samtykke. Juster til jeres faktiske setup og samtykke-banner.
                    </p>
                    <h2>Nødvendige cookies</h2>
                    <p>Eksempel: session og sikkerhed — kræves for at websitet fungerer.</p>
                </article>
            </main>
        </MarketingLayout>
    );
}
