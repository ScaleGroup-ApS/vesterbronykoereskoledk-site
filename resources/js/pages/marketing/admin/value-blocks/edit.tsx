import { Head, Link, useForm } from '@inertiajs/react';
import { update } from '@/actions/App/Http/Controllers/Marketing/Admin/MarketingValueBlockController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { index } from '@/routes/marketing/value-blocks';
import type { BreadcrumbItem } from '@/types';
import type { MarketingValueBlockProps } from '@/types/marketing-public';

const VALUE_BLOCK_ICONS: { value: string; label: string }[] = [
    { value: 'book_open', label: 'Bog / teori' },
    { value: 'users', label: 'Personer / team' },
    { value: 'car', label: 'Bil' },
    { value: 'package', label: 'Pakke' },
    { value: 'sparkles', label: 'Sparkles' },
    { value: 'message_circle', label: 'Besked' },
];

export default function MarketingValueBlocksEdit({ block }: { block: MarketingValueBlockProps }) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'USP-blokke', href: index.url() },
        { title: block.title, href: '#' },
    ];

    const form = useForm({
        title: block.title,
        body: block.body,
        icon: block.icon,
        sort_order: block.sort_order,
        is_active: block.is_active,
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        form.put(update.url(block.id));
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Rediger: ${block.title}`} />

            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <Heading title="Rediger USP-blok" description={block.title} />
                    <Button variant="outline" asChild>
                        <Link href={index.url()}>Tilbage</Link>
                    </Button>
                </div>

                <form onSubmit={submit} className="max-w-2xl space-y-6">
                    <div className="space-y-1">
                        <Label htmlFor="title">Titel</Label>
                        <Input
                            id="title"
                            value={form.data.title}
                            onChange={(e) => form.setData('title', e.target.value)}
                            required
                        />
                        <InputError message={form.errors.title} />
                    </div>

                    <div className="space-y-1">
                        <Label htmlFor="body">Tekst</Label>
                        <textarea
                            id="body"
                            rows={5}
                            className="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs"
                            value={form.data.body}
                            onChange={(e) => form.setData('body', e.target.value)}
                            required
                        />
                        <InputError message={form.errors.body} />
                    </div>

                    <div className="space-y-1">
                        <Label htmlFor="icon">Ikon</Label>
                        <select
                            id="icon"
                            className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-xs"
                            value={form.data.icon}
                            onChange={(e) => form.setData('icon', e.target.value)}
                        >
                            {VALUE_BLOCK_ICONS.map((opt) => (
                                <option key={opt.value} value={opt.value}>
                                    {opt.label}
                                </option>
                            ))}
                        </select>
                        <InputError message={form.errors.icon} />
                    </div>

                    <div className="space-y-1">
                        <Label htmlFor="sort_order">Rækkefølge</Label>
                        <Input
                            id="sort_order"
                            type="number"
                            min={0}
                            value={form.data.sort_order}
                            onChange={(e) => form.setData('sort_order', Number(e.target.value))}
                        />
                        <InputError message={form.errors.sort_order} />
                    </div>

                    <div className="flex items-center gap-2">
                        <input
                            id="is_active"
                            type="checkbox"
                            className="size-4 rounded border-input"
                            checked={form.data.is_active}
                            onChange={(e) => form.setData('is_active', e.target.checked)}
                        />
                        <Label htmlFor="is_active" className="font-normal">
                            Vis på forsiden
                        </Label>
                    </div>

                    <Button type="submit" disabled={form.processing}>
                        Gem ændringer
                    </Button>
                </form>
            </div>
        </AppLayout>
    );
}
