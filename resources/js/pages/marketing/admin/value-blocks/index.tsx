import { Head, Link, router } from '@inertiajs/react';
import { Pencil, Plus, Trash2 } from 'lucide-react';
import { destroy } from '@/actions/App/Http/Controllers/Marketing/Admin/MarketingValueBlockController';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { edit as editHomeCopy } from '@/routes/marketing/home-copy';
import { create, edit, index } from '@/routes/marketing/value-blocks';
import type { BreadcrumbItem } from '@/types';
import type { MarketingValueBlockProps } from '@/types/marketing-public';

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Hjemmeside', href: editHomeCopy.url() },
    { title: 'USP-blokke', href: index.url() },
];

export default function MarketingValueBlocksIndex({ blocks }: { blocks: MarketingValueBlockProps[] }) {
    function handleDelete(block: MarketingValueBlockProps) {
        if (!confirm(`Slette blokken «${block.title}»?`)) {
            return;
        }
        router.delete(destroy.url(block.id));
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="USP-blokke" />

            <div className="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <Heading
                        title="USP-blokke"
                        description="«Hvorfor vælge os»-kortene på forsiden. Aktive blokke vises sorteret efter rækkefølge."
                    />
                    <div className="flex flex-wrap gap-2">
                        <Button variant="outline" asChild>
                            <Link href={editHomeCopy.url()}>Til forsidetekster</Link>
                        </Button>
                        <Button asChild>
                            <Link href={create.url()}>
                                <Plus className="mr-2 size-4" />
                                Ny blok
                            </Link>
                        </Button>
                    </div>
                </div>

                <div className="rounded-xl border">
                    <table className="w-full text-sm">
                        <thead>
                            <tr className="border-b text-left">
                                <th className="px-4 py-3 font-medium">Rækkefølge</th>
                                <th className="px-4 py-3 font-medium">Titel</th>
                                <th className="px-4 py-3 font-medium">Ikon</th>
                                <th className="px-4 py-3 font-medium">Status</th>
                                <th className="px-4 py-3 font-medium text-right">Handlinger</th>
                            </tr>
                        </thead>
                        <tbody>
                            {blocks.map((block) => (
                                <tr key={block.id} className="border-b last:border-0">
                                    <td className="px-4 py-3 text-muted-foreground">{block.sort_order}</td>
                                    <td className="px-4 py-3 font-medium">{block.title}</td>
                                    <td className="px-4 py-3 font-mono text-xs text-muted-foreground">{block.icon}</td>
                                    <td className="px-4 py-3">
                                        <Badge variant={block.is_active ? 'default' : 'secondary'}>
                                            {block.is_active ? 'Aktiv' : 'Skjult'}
                                        </Badge>
                                    </td>
                                    <td className="px-4 py-3 text-right">
                                        <div className="flex justify-end gap-1">
                                            <Button variant="ghost" size="icon" asChild>
                                                <Link href={edit.url(block.id)}>
                                                    <Pencil className="size-4" />
                                                    <span className="sr-only">Rediger</span>
                                                </Link>
                                            </Button>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                type="button"
                                                onClick={() => handleDelete(block)}
                                            >
                                                <Trash2 className="size-4 text-destructive" />
                                                <span className="sr-only">Slet</span>
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                            {blocks.length === 0 && (
                                <tr>
                                    <td colSpan={5} className="px-4 py-8 text-center text-muted-foreground">
                                        Ingen blokke endnu. Opret den første.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>
        </AppLayout>
    );
}
