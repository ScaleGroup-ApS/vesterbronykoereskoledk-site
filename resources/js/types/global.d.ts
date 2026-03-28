import type { Auth } from '@/types/auth';
import type { MarketingContact } from '@/types/marketing-contact';
import type { MarketingOffer } from '@/types/marketing-offer';

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        sharedPageProps: {
            name: string;
            auth: Auth;
            sidebarOpen: boolean;
            marketingOffers: MarketingOffer[];
            marketingContact: MarketingContact;
            flash?: {
                success?: string | null;
                error?: string | null;
            };
            [key: string]: unknown;
        };
    }
}
