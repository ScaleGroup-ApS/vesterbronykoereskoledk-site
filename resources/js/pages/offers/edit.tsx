import { Form, Head, Link, router, useForm } from '@inertiajs/react';
import { BookOpen, FileText, Image, Plus, Trash2, Upload, Video } from 'lucide-react';
import { useRef, useState } from 'react';
import { destroy as destroyCourse, store as storeCourse } from '@/actions/App/Http/Controllers/Offers/CourseController';
import { update } from '@/actions/App/Http/Controllers/Offers/OfferController';
import {
    destroy as destroyMaterial,
    store as storeMaterial,
} from '@/actions/App/Http/Controllers/Offers/OfferMediaController';
import { show as showMaterialVariants } from '@/actions/App/Http/Controllers/MediaController';
import { index as modulesIndex } from '@/actions/App/Http/Controllers/Offers/OfferModuleController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';

import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { index } from '@/routes/offers';
import type { BreadcrumbItem } from '@/types';

type Offer = {
    id: number;
    name: string;
    description: string | null;
    price: string;
    type: string;
    theory_lessons: number;
    driving_lessons: number;
    track_required: boolean;
    slippery_required: boolean;
    requires_theory_exam: boolean;
    requires_practical_exam: boolean;
};

type OfferType = { value: string; label: string };
type Course = { id: number; start_at: string; end_at: string };
type MaterialMedia = { id: number; file_name: string; mime_type: string; size: string };

export default function OfferEdit({
    offer,
    offerTypes,
    courses,
    materials,
}: {
    offer: Offer;
    offerTypes: OfferType[];
    courses: Course[];
    materials: MaterialMedia[];
}) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Tilbud', href: index().url },
        { title: offer.name, href: '#' },
    ];

    const form = useForm({
        name: offer.name,
        description: offer.description ?? '',
        price: offer.price,
        type: offer.type,
        theory_lessons: String(offer.theory_lessons),
        driving_lessons: String(offer.driving_lessons),
        track_required: offer.track_required,
        slippery_required: offer.slippery_required,
        requires_theory_exam: offer.requires_theory_exam,
        requires_practical_exam: offer.requires_practical_exam,
    });

    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        form.submit(update(offer));
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Rediger ${offer.name}`} />

            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <Heading title={`Rediger ${offer.name}`} />

                <form onSubmit={handleSubmit} className="max-w-lg space-y-6">
                    <div className="grid gap-2">
                        <Label htmlFor="name">Navn</Label>
                        <Input
                            id="name"
                            value={form.data.name}
                            onChange={(e) => form.setData('name', e.target.value)}
                            required
                        />
                        <InputError message={form.errors.name} />
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="type">Type</Label>
                        <select
                            id="type"
                            value={form.data.type}
                            onChange={(e) => form.setData('type', e.target.value)}
                            className="flex h-9 w-full rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm"
                        >
                            {offerTypes.map((t) => (
                                <option key={t.value} value={t.value}>
                                    {t.label}
                                </option>
                            ))}
                        </select>
                    </div>

                    <div className="grid gap-2">
                        <Label htmlFor="price">Pris (kr.)</Label>
                        <Input
                            id="price"
                            type="number"
                            min="0"
                            step="0.01"
                            value={form.data.price}
                            onChange={(e) => form.setData('price', e.target.value)}
                            required
                        />
                        <InputError message={form.errors.price} />
                    </div>

                    <div className="grid gap-4 sm:grid-cols-2">
                        <div className="grid gap-2">
                            <Label htmlFor="theory_lessons">Teorilektioner</Label>
                            <Input
                                id="theory_lessons"
                                type="number"
                                min="0"
                                value={form.data.theory_lessons}
                                onChange={(e) => form.setData('theory_lessons', e.target.value)}
                            />
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="driving_lessons">Køretimer</Label>
                            <Input
                                id="driving_lessons"
                                type="number"
                                min="0"
                                value={form.data.driving_lessons}
                                onChange={(e) => form.setData('driving_lessons', e.target.value)}
                            />
                        </div>
                    </div>

                    <div className="space-y-2">
                        <div className="flex items-center gap-2">
                            <Checkbox
                                id="track_required"
                                checked={form.data.track_required}
                                onCheckedChange={(v) => form.setData('track_required', v === true)}
                            />
                            <Label htmlFor="track_required">Bane krævet</Label>
                        </div>
                        <div className="flex items-center gap-2">
                            <Checkbox
                                id="slippery_required"
                                checked={form.data.slippery_required}
                                onCheckedChange={(v) => form.setData('slippery_required', v === true)}
                            />
                            <Label htmlFor="slippery_required">Glat bane krævet</Label>
                        </div>
                        <div className="flex items-center gap-2">
                            <Checkbox
                                id="requires_theory_exam"
                                checked={form.data.requires_theory_exam}
                                onCheckedChange={(v) => form.setData('requires_theory_exam', v === true)}
                            />
                            <Label htmlFor="requires_theory_exam">Teoriprøve i forløbet</Label>
                        </div>
                        <div className="flex items-center gap-2">
                            <Checkbox
                                id="requires_practical_exam"
                                checked={form.data.requires_practical_exam}
                                onCheckedChange={(v) => form.setData('requires_practical_exam', v === true)}
                            />
                            <Label htmlFor="requires_practical_exam">Køreprøve i forløbet</Label>
                        </div>
                    </div>

                    <Button disabled={form.processing}>Gem ændringer</Button>
                </form>

                <div className="max-w-lg">
                    <Button variant="outline" asChild>
                        <Link href={modulesIndex({ offer }).url}>
                            <BookOpen className="mr-2 size-4" />
                            Moduler & sider
                        </Link>
                    </Button>
                </div>

                <div className="max-w-lg mt-8">
                    <h2 className="text-lg font-semibold mb-4">Kursusdatoer</h2>

                    {courses.length === 0 ? (
                        <p className="text-sm text-muted-foreground mb-4">Ingen kursusdatoer endnu.</p>
                    ) : (
                        <ul className="mb-4 divide-y divide-border rounded-md border">
                            {courses.map((course) => (
                                <li key={course.id} className="flex items-center justify-between px-4 py-2 text-sm">
                                    <span>
                                        {new Date(course.start_at).toLocaleString('da-DK', { dateStyle: 'long', timeStyle: 'short' })}
                                        {' – '}
                                        {new Date(course.end_at).toLocaleTimeString('da-DK', { timeStyle: 'short' })}
                                    </span>
                                    <Form {...destroyCourse({ offer, course })} method="delete">
                                        {({ processing }) => (
                                            <button
                                                type="submit"
                                                disabled={processing}
                                                className="text-muted-foreground hover:text-destructive transition-colors"
                                                aria-label="Slet kursusdato"
                                            >
                                                <Trash2 className="size-4" />
                                            </button>
                                        )}
                                    </Form>
                                </li>
                            ))}
                        </ul>
                    )}

                    <Form {...storeCourse(offer)} className="flex gap-2" resetOnSuccess>
                        {({ processing }) => (
                            <>
                                <input
                                    type="datetime-local"
                                    name="start_at"
                                    required
                                    min={new Date().toISOString().slice(0, 16)}
                                    className="flex h-9 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm"
                                />
                                <Button type="submit" variant="outline" size="sm" disabled={processing}>
                                    <Plus className="mr-1 size-4" />
                                    Tilføj dato
                                </Button>
                            </>
                        )}
                    </Form>
                </div>

                <div className="max-w-2xl mt-8">
                    <h2 className="text-lg font-semibold mb-4">Kursusmaterialer</h2>
                    <MaterialsList offer={offer} materials={materials} />
                </div>
            </div>
        </AppLayout>
    );
}

function MaterialsList({ offer, materials }: { offer: Offer; materials: MaterialMedia[] }) {
    const inputRef = useRef<HTMLInputElement>(null);
    const [uploading, setUploading] = useState(false);

    function handleUpload(e: React.ChangeEvent<HTMLInputElement>) {
        const file = e.target.files?.[0];
        if (!file) return;
        setUploading(true);
        router.post(
            storeMaterial(offer).url,
            { file },
            {
                forceFormData: true,
                preserveScroll: true,
                onFinish: () => {
                    if (inputRef.current) inputRef.current.value = '';
                    setUploading(false);
                },
            },
        );
    }

    function handleDelete(material: MaterialMedia) {
        if (!confirm('Slet denne fil?')) return;
        router.delete(destroyMaterial({ offer, media: material }).url, { preserveScroll: true });
    }

    const isImage = (mime: string) => mime.startsWith('image/');
    const isVideo = (mime: string) => mime.startsWith('video/');

    return (
        <div className="space-y-3">
            {materials.length > 0 && (
                <ul className="divide-y divide-border rounded-md border text-sm">
                    {materials.map((m) => (
                        <li key={m.id} className="flex items-center gap-3 px-3 py-2">
                            {isImage(m.mime_type) ? (
                                <Image className="size-4 shrink-0 text-muted-foreground" />
                            ) : isVideo(m.mime_type) ? (
                                <Video className="size-4 shrink-0 text-muted-foreground" />
                            ) : (
                                <FileText className="size-4 shrink-0 text-muted-foreground" />
                            )}
                            <a
                                href={showMaterialVariants['/offers/{offer}/media/{media}']({ offer, media: m }).url}
                                target="_blank"
                                rel="noopener noreferrer"
                                className="flex-1 truncate hover:underline"
                            >
                                {m.file_name}
                            </a>
                            <span className="shrink-0 text-xs text-muted-foreground">{m.size}</span>
                            <button
                                type="button"
                                onClick={() => handleDelete(m)}
                                className="shrink-0 text-muted-foreground transition-colors hover:text-destructive"
                                aria-label="Slet fil"
                            >
                                <Trash2 className="size-4" />
                            </button>
                        </li>
                    ))}
                </ul>
            )}

            <label className="flex cursor-pointer items-center gap-2 rounded-md border border-dashed border-input px-3 py-2 text-sm text-muted-foreground transition-colors hover:border-foreground/30 hover:text-foreground">
                <Upload className="size-4 shrink-0" />
                <span>{uploading ? 'Uploader…' : 'Upload billede eller video'}</span>
                <input
                    ref={inputRef}
                    type="file"
                    accept="image/jpeg,image/png,image/gif,image/webp,video/mp4,video/quicktime,video/avi,video/webm"
                    onChange={handleUpload}
                    disabled={uploading}
                    className="sr-only"
                />
            </label>
        </div>
    );
}
