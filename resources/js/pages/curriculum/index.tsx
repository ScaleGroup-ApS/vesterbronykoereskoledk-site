import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import type { BreadcrumbItem } from '@/types';

type CurriculumTopic = {
    id: number;
    lesson_number: number;
    title: string;
    description: string | null;
};

type Offer = {
    id: number;
    name: string;
    slug: string;
};

type Props = {
    offer: Offer;
    topics: CurriculumTopic[];
    materials: Array<{
        id: number;
        name: string;
        size: string;
        unlock_at_lesson: number | null;
    }>;
};

export default function CurriculumIndex({ offer, topics }: Props) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Tilbud', href: '/offers' },
        { title: offer.name, href: `/offers/${offer.id}/edit` },
        { title: 'Pensum', href: '#' },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Pensum – ${offer.name}`} />
            <div className="p-6">
                <h1 className="text-2xl font-bold mb-4">Pensum – {offer.name}</h1>
                <ul>
                    {topics.map((topic) => (
                        <li key={topic.id}>
                            {topic.lesson_number}. {topic.title}
                        </li>
                    ))}
                </ul>
            </div>
        </AppLayout>
    );
}
