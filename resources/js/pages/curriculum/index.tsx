import { Head, router, useForm } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
import { useState } from 'react';
import CurriculumMaterialUnlockController from '@/actions/App/Http/Controllers/Curriculum/CurriculumMaterialUnlockController';
import {
    destroy,
    index as curriculumIndex,
    store,
} from '@/actions/App/Http/Controllers/Curriculum/CurriculumTopicController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { index as offersIndex } from '@/routes/offers';
import type { BreadcrumbItem } from '@/types';

type CurriculumTopic = {
    id: number;
    lesson_number: number;
    title: string;
    description: string | null;
};

type Material = {
    id: number;
    name: string;
    size: string;
    unlock_at_lesson: number | null;
};

type Offer = {
    id: number;
    name: string;
    slug: string;
};

type Props = {
    offer: Offer;
    topics: CurriculumTopic[];
    materials: Material[];
};

export default function CurriculumIndex({ offer, topics, materials }: Props) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Tilbud', href: offersIndex().url },
        { title: offer.name, href: `/offers/${offer.id}/edit` },
        { title: 'Læringsplan', href: curriculumIndex(offer).url },
    ];

    // Create topic form
    const createForm = useForm({
        lesson_number: '',
        title: '',
        description: '',
    });

    function submitCreate(e: React.FormEvent) {
        e.preventDefault();
        createForm.post(store(offer).url, {
            preserveScroll: true,
            onSuccess: () => createForm.reset(),
        });
    }

    function handleDelete(topic: CurriculumTopic) {
        if (confirm(`Er du sikker på, at du vil slette emnet "${topic.title}"?`)) {
            router.delete(destroy(topic).url, { preserveScroll: true });
        }
    }

    // Material unlock state (local copy for editing)
    const [unlockValues, setUnlockValues] = useState<Record<number, string>>(
        Object.fromEntries(
            materials.map((m) => [m.id, m.unlock_at_lesson != null ? String(m.unlock_at_lesson) : '']),
        ),
    );
    const [savingUnlock, setSavingUnlock] = useState(false);

    function saveUnlock() {
        setSavingUnlock(true);
        const payload: Record<string, number | null> = {};
        for (const [id, val] of Object.entries(unlockValues)) {
            payload[id] = val === '' ? null : Number(val);
        }
        router.patch(
            CurriculumMaterialUnlockController(offer).url,
            { materials: payload },
            {
                preserveScroll: true,
                onFinish: () => setSavingUnlock(false),
            },
        );
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Læringsplan – ${offer.name}`} />

            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <Heading title={`Læringsplan – ${offer.name}`} description="Administrer emner og materialeadgang pr. lektion" />

                {/* Topics list */}
                <div className="rounded-xl border">
                    <div className="border-b px-4 py-3">
                        <h2 className="text-sm font-semibold">Emner</h2>
                    </div>
                    {topics.length === 0 ? (
                        <p className="px-4 py-8 text-center text-sm text-muted-foreground">Ingen emner endnu.</p>
                    ) : (
                        <table className="w-full text-sm">
                            <thead>
                                <tr className="border-b text-left text-muted-foreground">
                                    <th className="px-4 py-3 font-medium">Lektion</th>
                                    <th className="px-4 py-3 font-medium">Titel</th>
                                    <th className="px-4 py-3 font-medium">Beskrivelse</th>
                                    <th className="px-4 py-3 font-medium"></th>
                                </tr>
                            </thead>
                            <tbody>
                                {[...topics]
                                    .sort((a, b) => a.lesson_number - b.lesson_number)
                                    .map((topic) => (
                                        <tr key={topic.id} className="border-b last:border-0">
                                            <td className="px-4 py-3 tabular-nums">{topic.lesson_number}</td>
                                            <td className="px-4 py-3 font-medium">{topic.title}</td>
                                            <td className="px-4 py-3 text-muted-foreground">{topic.description ?? '—'}</td>
                                            <td className="px-4 py-3 text-right">
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() => handleDelete(topic)}
                                                >
                                                    <Trash2 className="size-4" />
                                                </Button>
                                            </td>
                                        </tr>
                                    ))}
                            </tbody>
                        </table>
                    )}
                </div>

                {/* Add topic form */}
                <div className="max-w-2xl rounded-xl border p-6">
                    <Heading variant="small" title="Tilføj emne" />
                    <form onSubmit={submitCreate} className="mt-4 space-y-4">
                        <div className="grid max-w-[160px] gap-2">
                            <Label htmlFor="lesson_number">Lektionsnummer</Label>
                            <Input
                                id="lesson_number"
                                type="number"
                                min="1"
                                value={createForm.data.lesson_number}
                                onChange={(e) => createForm.setData('lesson_number', e.target.value)}
                                required
                            />
                            <InputError message={createForm.errors.lesson_number} />
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="title">Titel</Label>
                            <Input
                                id="title"
                                type="text"
                                value={createForm.data.title}
                                onChange={(e) => createForm.setData('title', e.target.value)}
                                required
                            />
                            <InputError message={createForm.errors.title} />
                        </div>
                        <div className="grid gap-2">
                            <Label htmlFor="description">Beskrivelse (valgfrit)</Label>
                            <Input
                                id="description"
                                type="text"
                                value={createForm.data.description}
                                onChange={(e) => createForm.setData('description', e.target.value)}
                            />
                            <InputError message={createForm.errors.description} />
                        </div>
                        <Button type="submit" size="sm" disabled={createForm.processing}>
                            Tilføj emne
                        </Button>
                    </form>
                </div>

                {/* Material unlock section */}
                {materials.length > 0 && (
                    <div className="max-w-2xl rounded-xl border p-6">
                        <Heading variant="small" title="Materialeadgang" />
                        <p className="mb-4 mt-1 text-sm text-muted-foreground">
                            Angiv efter hvilken lektion hvert materiale låses op for eleven.
                        </p>
                        <div className="space-y-3">
                            {materials.map((material) => (
                                <div key={material.id} className="flex items-center gap-4">
                                    <div className="flex-1">
                                        <span className="text-sm font-medium">{material.name}</span>
                                        <span className="ml-2 text-xs text-muted-foreground">{material.size}</span>
                                    </div>
                                    <div className="flex w-36 items-center gap-2">
                                        <Label htmlFor={`unlock-${material.id}`} className="sr-only">
                                            Lås op efter lektion
                                        </Label>
                                        <Input
                                            id={`unlock-${material.id}`}
                                            type="number"
                                            min="0"
                                            placeholder="—"
                                            value={unlockValues[material.id] ?? ''}
                                            onChange={(e) =>
                                                setUnlockValues((prev) => ({
                                                    ...prev,
                                                    [material.id]: e.target.value,
                                                }))
                                            }
                                            className="w-20"
                                        />
                                        <span className="text-xs text-muted-foreground">lektion</span>
                                    </div>
                                </div>
                            ))}
                        </div>
                        <Button
                            type="button"
                            size="sm"
                            className="mt-4"
                            onClick={saveUnlock}
                            disabled={savingUnlock}
                        >
                            Gem
                        </Button>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
