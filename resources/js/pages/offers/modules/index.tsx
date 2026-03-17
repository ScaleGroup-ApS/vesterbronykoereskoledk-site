import { Head, Form, Link, router } from '@inertiajs/react';
import { ChevronDown, ChevronUp, Pencil, Plus, Trash2 } from 'lucide-react';
import {
    index as modulesIndex,
    store as storeModule,
    edit as editModule,
    destroy as destroyModule,
    moveUp as moveModuleUp,
    moveDown as moveModuleDown,
} from '@/actions/App/Http/Controllers/Offers/OfferModuleController';
import {
    store as storePage,
    edit as editPage,
    destroy as destroyPage,
    moveUp as movePageUp,
    moveDown as movePageDown,
} from '@/actions/App/Http/Controllers/Offers/OfferPageController';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { edit as editOffer, index as offersIndex } from '@/actions/App/Http/Controllers/Offers/OfferController';
import type { BreadcrumbItem } from '@/types';

type Offer = { id: number; name: string };

type Page = { id: number; title: string; sort_order: number };

type Module = {
    id: number;
    title: string;
    sort_order: number;
    pages: Page[];
};

export default function OfferModulesIndex({ offer, modules }: { offer: Offer; modules: Module[] }) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Tilbud', href: offersIndex().url },
        { title: offer.name, href: editOffer(offer).url },
        { title: 'Moduler & sider', href: '#' },
    ];

    function confirmDestroyModule(module: Module) {
        if (confirm(`Slet modulet "${module.title}" og alle dets sider?`)) {
            router.delete(destroyModule({ offer, module }).url);
        }
    }

    function confirmDestroyPage(page: Page, module: Module) {
        if (confirm(`Slet siden "${page.title}"?`)) {
            router.delete(destroyPage({ offer, module, page }).url);
        }
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Moduler — ${offer.name}`} />

            <div className="flex h-full flex-1 flex-col gap-6 rounded-xl p-4">
                <Heading title="Moduler & sider" description={`Administrer læringsindhold for ${offer.name}`} />

                <div className="space-y-4">
                    {modules.map((module) => (
                        <div key={module.id} className="rounded-xl border">
                            <div className="flex items-center justify-between border-b px-4 py-3">
                                <span className="font-semibold">{module.title}</span>
                                <div className="flex items-center gap-1">
                                    <Form {...moveModuleUp({ offer, module })} method="post">
                                        {({ processing }) => (
                                            <button
                                                type="submit"
                                                disabled={processing}
                                                className="rounded p-1 hover:bg-muted disabled:opacity-50"
                                                aria-label="Flyt op"
                                            >
                                                <ChevronUp className="size-4" />
                                            </button>
                                        )}
                                    </Form>
                                    <Form {...moveModuleDown({ offer, module })} method="post">
                                        {({ processing }) => (
                                            <button
                                                type="submit"
                                                disabled={processing}
                                                className="rounded p-1 hover:bg-muted disabled:opacity-50"
                                                aria-label="Flyt ned"
                                            >
                                                <ChevronDown className="size-4" />
                                            </button>
                                        )}
                                    </Form>
                                    <Button variant="ghost" size="sm" asChild>
                                        <Link href={editModule({ offer, module }).url}>
                                            <Pencil className="size-4" />
                                        </Link>
                                    </Button>
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        onClick={() => confirmDestroyModule(module)}
                                        className="text-muted-foreground hover:text-destructive"
                                    >
                                        <Trash2 className="size-4" />
                                    </Button>
                                </div>
                            </div>

                            <div className="divide-y">
                                {module.pages.map((page) => (
                                    <div key={page.id} className="flex items-center justify-between px-6 py-2 text-sm">
                                        <span>{page.title}</span>
                                        <div className="flex items-center gap-1">
                                            <Form {...movePageUp({ offer, module, page })} method="post">
                                                {({ processing }) => (
                                                    <button
                                                        type="submit"
                                                        disabled={processing}
                                                        className="rounded p-1 hover:bg-muted disabled:opacity-50"
                                                        aria-label="Flyt op"
                                                    >
                                                        <ChevronUp className="size-3" />
                                                    </button>
                                                )}
                                            </Form>
                                            <Form {...movePageDown({ offer, module, page })} method="post">
                                                {({ processing }) => (
                                                    <button
                                                        type="submit"
                                                        disabled={processing}
                                                        className="rounded p-1 hover:bg-muted disabled:opacity-50"
                                                        aria-label="Flyt ned"
                                                    >
                                                        <ChevronDown className="size-3" />
                                                    </button>
                                                )}
                                            </Form>
                                            <Button variant="ghost" size="sm" asChild>
                                                <Link href={editPage({ offer, module, page }).url}>
                                                    <Pencil className="size-4" />
                                                </Link>
                                            </Button>
                                            <Button
                                                variant="ghost"
                                                size="sm"
                                                onClick={() => confirmDestroyPage(page, module)}
                                                className="text-muted-foreground hover:text-destructive"
                                            >
                                                <Trash2 className="size-4" />
                                            </Button>
                                        </div>
                                    </div>
                                ))}

                                <div className="px-6 py-3">
                                    <Form {...storePage({ offer, module })} method="post" className="flex gap-2" resetOnSuccess>
                                        {({ processing }) => (
                                            <>
                                                <input
                                                    type="text"
                                                    name="title"
                                                    placeholder="Ny sidetitel…"
                                                    required
                                                    className="flex h-8 flex-1 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm"
                                                />
                                                <Button type="submit" variant="outline" size="sm" disabled={processing}>
                                                    <Plus className="size-4 mr-1" />
                                                    Tilføj side
                                                </Button>
                                            </>
                                        )}
                                    </Form>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>

                <div className="rounded-xl border p-4">
                    <h2 className="mb-3 text-sm font-semibold">Tilføj modul</h2>
                    <Form {...storeModule({ offer })} method="post" className="flex gap-2" resetOnSuccess>
                        {({ processing }) => (
                            <>
                                <input
                                    type="text"
                                    name="title"
                                    placeholder="Modulnavn…"
                                    required
                                    className="flex h-9 flex-1 rounded-md border border-input bg-transparent px-3 py-1 text-sm shadow-sm"
                                />
                                <Button type="submit" disabled={processing}>
                                    <Plus className="size-4 mr-1" />
                                    Tilføj modul
                                </Button>
                            </>
                        )}
                    </Form>
                </div>
            </div>
        </AppLayout>
    );
}
