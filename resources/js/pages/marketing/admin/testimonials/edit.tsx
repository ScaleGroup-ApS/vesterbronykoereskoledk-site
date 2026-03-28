import { Head, Link, useForm } from '@inertiajs/react';
import { update } from '@/actions/App/Http/Controllers/Marketing/Admin/MarketingTestimonialController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { index } from '@/routes/marketing/testimonials';
import type { BreadcrumbItem } from '@/types';
import type { MarketingTestimonialProps } from '@/types/marketing-public';

export default function MarketingTestimonialsEdit({ testimonial }: { testimonial: MarketingTestimonialProps }) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Udtalelser', href: index.url() },
        { title: testimonial.author_name, href: '#' },
    ];

    const form = useForm({
        quote: testimonial.quote,
        author_name: testimonial.author_name,
        author_detail: testimonial.author_detail ?? '',
        sort_order: testimonial.sort_order,
        is_active: testimonial.is_active,
    });

    function submit(e: React.FormEvent) {
        e.preventDefault();
        form.put(update.url(testimonial.id));
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Rediger: ${testimonial.author_name}`} />

            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <Heading title="Rediger udtalelse" description={testimonial.author_name} />
                    <Button variant="outline" asChild>
                        <Link href={index.url()}>Tilbage</Link>
                    </Button>
                </div>

                <form onSubmit={submit} className="max-w-2xl space-y-6">
                    <div className="space-y-1">
                        <Label htmlFor="quote">Citat</Label>
                        <textarea
                            id="quote"
                            rows={5}
                            className="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs"
                            value={form.data.quote}
                            onChange={(e) => form.setData('quote', e.target.value)}
                            required
                        />
                        <InputError message={form.errors.quote} />
                    </div>

                    <div className="space-y-1">
                        <Label htmlFor="author_name">Navn</Label>
                        <Input
                            id="author_name"
                            value={form.data.author_name}
                            onChange={(e) => form.setData('author_name', e.target.value)}
                            required
                        />
                        <InputError message={form.errors.author_name} />
                    </div>

                    <div className="space-y-1">
                        <Label htmlFor="author_detail">Detalje (fx hold, by)</Label>
                        <Input
                            id="author_detail"
                            value={form.data.author_detail}
                            onChange={(e) => form.setData('author_detail', e.target.value)}
                        />
                        <InputError message={form.errors.author_detail} />
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
