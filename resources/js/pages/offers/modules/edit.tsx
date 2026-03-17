import { Head, useForm } from '@inertiajs/react';
import {
    index as modulesIndex,
    update as updateModule,
} from '@/actions/App/Http/Controllers/Offers/OfferModuleController';
import { edit as editOffer, index as offersIndex } from '@/actions/App/Http/Controllers/Offers/OfferController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';

type Offer = { id: number; name: string };
type Module = { id: number; title: string };

export default function OfferModuleEdit({ offer, module }: { offer: Offer; module: Module }) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Tilbud', href: offersIndex().url },
        { title: offer.name, href: editOffer(offer).url },
        { title: 'Moduler & sider', href: modulesIndex({ offer }).url },
        { title: module.title, href: '#' },
    ];

    const form = useForm({ title: module.title });

    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        form.submit(updateModule({ offer, module }));
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Rediger modul — ${module.title}`} />

            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <Heading title="Rediger modul" />

                <form onSubmit={handleSubmit} className="max-w-lg space-y-4">
                    <div className="grid gap-2">
                        <Label htmlFor="title">Titel</Label>
                        <Input
                            id="title"
                            value={form.data.title}
                            onChange={(e) => form.setData('title', e.target.value)}
                            required
                        />
                        <InputError message={form.errors.title} />
                    </div>

                    <Button disabled={form.processing}>Gem ændringer</Button>
                </form>
            </div>
        </AppLayout>
    );
}
