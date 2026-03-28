import { Head } from '@inertiajs/react';
import MarketingLayout from '@/layouts/marketing-layout';

export default function Privatlivspolitik() {
    return (
        <MarketingLayout>
            <Head title="Privatlivspolitik | Køreskole Pro" />
            <main className="bg-white py-16 md:py-24">
                <article className="prose prose-slate container mx-auto max-w-3xl px-4 lg:px-8">
                    <h1>Privatlivspolitik</h1>
                    <p>
                        Her beskriver I hvilke personoplysninger I indsamler, formål, retligt grundlag, opbevaring og rettigheder efter
                        GDPR. Erstat med jeres endelige politik.
                    </p>
                    <h2>Dataansvarlig</h2>
                    <p>Angiv firmanavn, CVR og kontaktoplysninger.</p>
                    <h2>Cookies</h2>
                    <p>Henvis til cookiepolitikken for detaljer om cookies på websitet.</p>
                </article>
            </main>
        </MarketingLayout>
    );
}
