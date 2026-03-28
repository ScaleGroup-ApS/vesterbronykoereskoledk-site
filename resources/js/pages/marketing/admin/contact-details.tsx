import { Head, useForm, usePage } from '@inertiajs/react';
// import { edit, update } from '@/actions/App/Http/Controllers/Marketing/Admin/MarketingContactDetailController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { index as testimonialsIndex } from '@/routes/marketing/testimonials';
import type { BreadcrumbItem } from '@/types';

type Detail = {
    id: number;
    phone: string;
    phone_href: string;
    email: string;
    opening_hours: string | null;
    address_line: string | null;
};

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Hjemmeside', href: edit.url() },
    { title: 'Kontaktoplysninger', href: edit.url() },
];

export default function MarketingContactDetailsEdit({ detail }: { detail: Detail }) {
    const { flash } = usePage().props as { flash?: { success?: string | null } };

    const form = useForm({
        phone: detail.phone,
        phone_href: detail.phone_href,
        email: detail.email,
        opening_hours: detail.opening_hours ?? '',
        address_line: detail.address_line ?? '',
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        form.put(update.url());
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Kontaktoplysninger" />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <Heading
                    title="Kontaktoplysninger"
                    description="Telefon, e-mail, adresse og åbningstider vises i header, footer og på kontaktsiden på den offentlige hjemmeside."
                />

                {flash?.success ? (
                    <div className="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-950">
                        {flash.success}
                    </div>
                ) : null}

                <form onSubmit={submit} className="max-w-2xl space-y-6">
                    <div className="space-y-2">
                        <Label htmlFor="phone">Telefon (vist for besøgende)</Label>
                        <Input
                            id="phone"
                            value={form.data.phone}
                            onChange={(e) => form.setData('phone', e.target.value)}
                            autoComplete="tel"
                        />
                        <InputError message={form.errors.phone} />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="phone_href">Telefon til link (href)</Label>
                        <p className="text-xs text-muted-foreground">
                            Bruges i <code className="rounded bg-muted px-1">tel:</code> — typisk kun cifre med landekode, fx{' '}
                            <span className="whitespace-nowrap">+4512345678</span>.
                        </p>
                        <Input
                            id="phone_href"
                            value={form.data.phone_href}
                            onChange={(e) => form.setData('phone_href', e.target.value)}
                        />
                        <InputError message={form.errors.phone_href} />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="email">E-mail</Label>
                        <Input
                            id="email"
                            type="email"
                            value={form.data.email}
                            onChange={(e) => form.setData('email', e.target.value)}
                            autoComplete="email"
                        />
                        <InputError message={form.errors.email} />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="address_line">Adresse (én linje)</Label>
                        <Input
                            id="address_line"
                            value={form.data.address_line}
                            onChange={(e) => form.setData('address_line', e.target.value)}
                        />
                        <InputError message={form.errors.address_line} />
                    </div>

                    <div className="space-y-2">
                        <Label htmlFor="opening_hours">Åbningstider</Label>
                        <p className="text-xs text-muted-foreground">Du kan bruge flere linjer — de bevares som indtastet.</p>
                        <textarea
                            id="opening_hours"
                            rows={5}
                            className="border-input placeholder:text-muted-foreground flex min-h-[120px] w-full rounded-md border bg-transparent px-3 py-2 text-sm shadow-xs transition-[color,box-shadow] outline-none focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50"
                            value={form.data.opening_hours}
                            onChange={(e) => form.setData('opening_hours', e.target.value)}
                        />
                        <InputError message={form.errors.opening_hours} />
                    </div>

                    <div className="flex flex-wrap gap-2">
                        <Button type="submit" disabled={form.processing}>
                            {form.processing ? 'Gemmer …' : 'Gem'}
                        </Button>
                        <Button type="button" variant="outline" asChild>
                            <a href={testimonialsIndex.url()} rel="noreferrer">
                                Til udtalelser
                            </a>
                        </Button>
                    </div>
                </form>
            </div>
        </AppLayout>
    );
}
