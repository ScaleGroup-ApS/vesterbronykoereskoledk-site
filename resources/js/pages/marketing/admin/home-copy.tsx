import { Head, Link, useForm } from '@inertiajs/react';
import { update as updateHomeCopy } from '@/actions/App/Http/Controllers/Marketing/Admin/MarketingHomeCopyController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { index as testimonialsIndex } from '@/routes/marketing/testimonials';
import { index as valueBlocksIndex } from '@/routes/marketing/value-blocks';
import type { BreadcrumbItem } from '@/types';
import type { MarketingHomeCopyProps } from '@/types/marketing-public';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Hjemmeside', href: '#' },
    { title: 'Forsidetekster', href: '#' },
];

export default function MarketingHomeCopyEdit({ copy }: { copy: MarketingHomeCopyProps }) {
    const form = useForm({
        hero_headline_prefix: copy.hero_headline_prefix,
        hero_headline_accent: copy.hero_headline_accent,
        hero_subtitle: copy.hero_subtitle ?? '',
        why_title: copy.why_title,
        why_lead: copy.why_lead ?? '',
        reviews_title: copy.reviews_title,
        reviews_lead: copy.reviews_lead ?? '',
        reviews_footnote: copy.reviews_footnote ?? '',
        explore_title: copy.explore_title,
        explore_lead: copy.explore_lead ?? '',
        cta_title: copy.cta_title,
        cta_lead: copy.cta_lead ?? '',
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        form.put(updateHomeCopy.url());
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Forsidetekster" />
            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <Heading
                        title="Forsidetekster"
                        description="Rediger overskrifter og brødtekst på den offentlige forside. USP-blokke og udtalelser har egne sider."
                    />
                    <div className="flex flex-wrap gap-2">
                        <Button variant="outline" asChild>
                            <Link href={valueBlocksIndex.url()}>Til USP-blokke</Link>
                        </Button>
                        <Button variant="outline" asChild>
                            <Link href={testimonialsIndex.url()}>Til udtalelser</Link>
                        </Button>
                    </div>
                </div>

                <form onSubmit={submit} className="max-w-3xl space-y-8">
                    <div className="space-y-4 rounded-xl border p-4">
                        <h3 className="text-sm font-semibold">Hero</h3>
                        <div className="grid gap-4 sm:grid-cols-2">
                            <div className="space-y-1">
                                <Label htmlFor="hero_headline_prefix">Overskrift (del 1)</Label>
                                <Input
                                    id="hero_headline_prefix"
                                    value={form.data.hero_headline_prefix}
                                    onChange={(e) => form.setData('hero_headline_prefix', e.target.value)}
                                />
                                <InputError message={form.errors.hero_headline_prefix} />
                            </div>
                            <div className="space-y-1">
                                <Label htmlFor="hero_headline_accent">Overskrift (accent)</Label>
                                <Input
                                    id="hero_headline_accent"
                                    value={form.data.hero_headline_accent}
                                    onChange={(e) => form.setData('hero_headline_accent', e.target.value)}
                                />
                                <InputError message={form.errors.hero_headline_accent} />
                            </div>
                        </div>
                        <div className="space-y-1">
                            <Label htmlFor="hero_subtitle">Intro tekst</Label>
                            <textarea
                                id="hero_subtitle"
                                rows={3}
                                className="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs"
                                value={form.data.hero_subtitle}
                                onChange={(e) => form.setData('hero_subtitle', e.target.value)}
                            />
                            <InputError message={form.errors.hero_subtitle} />
                        </div>
                    </div>

                    <div className="space-y-4 rounded-xl border p-4">
                        <h3 className="text-sm font-semibold">Hvorfor vælge os</h3>
                        <div className="space-y-1">
                            <Label htmlFor="why_title">Titel</Label>
                            <Input
                                id="why_title"
                                value={form.data.why_title}
                                onChange={(e) => form.setData('why_title', e.target.value)}
                            />
                            <InputError message={form.errors.why_title} />
                        </div>
                        <div className="space-y-1">
                            <Label htmlFor="why_lead">Intro</Label>
                            <textarea
                                id="why_lead"
                                rows={2}
                                className="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs"
                                value={form.data.why_lead}
                                onChange={(e) => form.setData('why_lead', e.target.value)}
                            />
                            <InputError message={form.errors.why_lead} />
                        </div>
                    </div>

                    <div className="space-y-4 rounded-xl border p-4">
                        <h3 className="text-sm font-semibold">Elevudtalelser</h3>
                        <div className="space-y-1">
                            <Label htmlFor="reviews_title">Titel</Label>
                            <Input
                                id="reviews_title"
                                value={form.data.reviews_title}
                                onChange={(e) => form.setData('reviews_title', e.target.value)}
                            />
                            <InputError message={form.errors.reviews_title} />
                        </div>
                        <div className="space-y-1">
                            <Label htmlFor="reviews_lead">Intro</Label>
                            <textarea
                                id="reviews_lead"
                                rows={2}
                                className="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs"
                                value={form.data.reviews_lead}
                                onChange={(e) => form.setData('reviews_lead', e.target.value)}
                            />
                            <InputError message={form.errors.reviews_lead} />
                        </div>
                        <div className="space-y-1">
                            <Label htmlFor="reviews_footnote">Fodnote</Label>
                            <Input
                                id="reviews_footnote"
                                value={form.data.reviews_footnote}
                                onChange={(e) => form.setData('reviews_footnote', e.target.value)}
                            />
                            <InputError message={form.errors.reviews_footnote} />
                        </div>
                    </div>

                    <div className="space-y-4 rounded-xl border p-4">
                        <h3 className="text-sm font-semibold">Find det du leder efter</h3>
                        <div className="space-y-1">
                            <Label htmlFor="explore_title">Titel</Label>
                            <Input
                                id="explore_title"
                                value={form.data.explore_title}
                                onChange={(e) => form.setData('explore_title', e.target.value)}
                            />
                            <InputError message={form.errors.explore_title} />
                        </div>
                        <div className="space-y-1">
                            <Label htmlFor="explore_lead">Intro</Label>
                            <textarea
                                id="explore_lead"
                                rows={2}
                                className="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs"
                                value={form.data.explore_lead}
                                onChange={(e) => form.setData('explore_lead', e.target.value)}
                            />
                            <InputError message={form.errors.explore_lead} />
                        </div>
                    </div>

                    <div className="space-y-4 rounded-xl border p-4">
                        <h3 className="text-sm font-semibold">Afsluttende CTA</h3>
                        <div className="space-y-1">
                            <Label htmlFor="cta_title">Titel</Label>
                            <Input
                                id="cta_title"
                                value={form.data.cta_title}
                                onChange={(e) => form.setData('cta_title', e.target.value)}
                            />
                            <InputError message={form.errors.cta_title} />
                        </div>
                        <div className="space-y-1">
                            <Label htmlFor="cta_lead">Tekst</Label>
                            <textarea
                                id="cta_lead"
                                rows={2}
                                className="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs"
                                value={form.data.cta_lead}
                                onChange={(e) => form.setData('cta_lead', e.target.value)}
                            />
                            <InputError message={form.errors.cta_lead} />
                        </div>
                    </div>

                    <Button type="submit" disabled={form.processing}>
                        Gem ændringer
                    </Button>
                </form>
            </div>
        </AppLayout>
    );
}
