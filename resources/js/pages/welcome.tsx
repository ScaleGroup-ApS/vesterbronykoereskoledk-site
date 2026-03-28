import { Head, Link, usePage } from '@inertiajs/react';
import { motion } from 'framer-motion';
import {
    ArrowRight,
    BookOpen,
    Car,
    MessageCircle,
    Package,
    Sparkles,
    Users,
    type LucideIcon,
} from 'lucide-react';
import { HeroHoldCountdown } from '@/components/marketing/hero-hold-countdown';
import { TestimonialCarousel } from '@/components/marketing/testimonial-carousel';
import MarketingLayout from '@/layouts/marketing-layout';
import { login } from '@/routes';
import { show as enrollShow } from '@/routes/enrollment';
import { about, contact, features, instructors, packages } from '@/routes/marketing';
import type {
    MarketingHomeCopyProps,
    MarketingTestimonialProps,
    MarketingValueBlockProps,
} from '@/types/marketing-public';

const iconMap: Record<string, LucideIcon> = {
    book_open: BookOpen,
    users: Users,
    car: Car,
    package: Package,
    sparkles: Sparkles,
    message_circle: MessageCircle,
};

function iconFor(key: string): LucideIcon {
    return iconMap[key] ?? BookOpen;
}

/** Default: one photo, three crops. Set each tile’s `src` to different files under public/images/ when you have them. */
const HERO_IMAGE = '/images/hero.png';

const heroCollageTiles = [
    {
        src: HERO_IMAGE,
        alt: 'Køreelev og kørelærer ved bilen',
        imgClassName: 'object-cover object-[center_36%]',
        wrapClassName: 'col-span-2 aspect-[16/10] sm:aspect-[2.1/1]',
        caption: 'Hele vejen — fra første time til køreprøven',
    },
    {
        src: HERO_IMAGE,
        alt: 'Fokus på undervisning og struktur',
        imgClassName: 'object-cover object-[center_62%]',
        wrapClassName: 'aspect-[4/5] sm:aspect-square',
        caption: 'Struktur & tryghed',
    },
    {
        src: HERO_IMAGE,
        alt: 'Træning i rigtig trafik',
        imgClassName: 'object-cover object-[center_48%]',
        wrapClassName: 'aspect-[4/5] sm:aspect-square',
        caption: 'Rigtig trafik',
    },
] as const;

export default function Welcome() {
    const { homeCopy, valueBlocks, testimonials, nextHoldStartAt, tilmeldHoldstartOfferSlug } = usePage()
        .props as unknown as {
            homeCopy: MarketingHomeCopyProps;
            valueBlocks: MarketingValueBlockProps[];
            testimonials: MarketingTestimonialProps[];
            nextHoldStartAt: string | null;
            tilmeldHoldstartOfferSlug: string | null;
        };

    const tilmeldHoldstartHref =
        tilmeldHoldstartOfferSlug !== null && tilmeldHoldstartOfferSlug !== ''
            ? enrollShow.url({ offer: tilmeldHoldstartOfferSlug })
            : packages.url();

    const explore = [
        {
            title: 'Fordele',
            description: 'Hvorfor det giver mening at vælge os — bilpark, struktur og hvordan vi planlægger.',
            href: features.url(),
            icon: Sparkles,
        },
        {
            title: 'Pakker',
            description: 'Se pris og indhold side om side. Ingen småt med småt på siden.',
            href: packages.url(),
            icon: Package,
        },
        {
            title: 'Om os',
            description: 'Hvem vi er, og hvordan vi arbejder med elever i praksis.',
            href: about.url(),
            icon: Car,
        },
        {
            title: 'Kontakt',
            description: 'Adresse, telefon, mail — eller skriv, hvis du har et konkret spørgsmål.',
            href: contact.url(),
            icon: MessageCircle,
        },
    ];

    return (
        <MarketingLayout>
            <Head title="Velkommen | Køreskole Pro" />

            <main>
                <section className="relative overflow-hidden pt-16 pb-20 md:pb-24 lg:pt-20 lg:pb-28">
                    <div className="pointer-events-none absolute inset-0 bg-gradient-to-br from-primary/[0.06] via-transparent to-accent/[0.04] -z-10" />
                    <div className="container mx-auto max-w-6xl px-4 lg:px-8">
                        <div className="grid items-center gap-12 lg:grid-cols-2 lg:gap-14">
                            <motion.div
                                initial={{ opacity: 0, x: -24 }}
                                animate={{ opacity: 1, x: 0 }}
                                transition={{ duration: 0.55 }}
                                className="flex flex-col text-center lg:text-left"
                            >
                                <h1 className="text-4xl font-extrabold tracking-tight sm:text-5xl md:text-6xl lg:text-[2.75rem] xl:text-7xl">
                                    <span className="marketing-hero-line block">{homeCopy.hero_headline_prefix}</span>
                                    <span className="mt-2 block text-primary">{homeCopy.hero_headline_accent}</span>
                                </h1>
                                {homeCopy.hero_subtitle ? (
                                    <p className="marketing-lead mx-auto mt-6 max-w-xl text-lg leading-relaxed sm:text-xl lg:mx-0 lg:max-w-[540px]">
                                        {homeCopy.hero_subtitle}
                                    </p>
                                ) : null}
                                <div className="mt-10 flex flex-col flex-wrap justify-center gap-3 sm:flex-row lg:justify-start">
                                    <Link
                                        href={packages.url()}
                                        className="inline-flex h-12 w-full min-w-[200px] items-center justify-center rounded-xl bg-primary px-8 text-base font-medium text-primary-foreground shadow-[0_8px_32px_-8px_rgba(37,99,235,0.35)] transition-colors hover:bg-primary/90 sm:w-auto"
                                    >
                                        Se priser
                                    </Link>
                                    <Link
                                        href={tilmeldHoldstartHref}
                                        className="inline-flex h-12 w-full min-w-[200px] items-center justify-center rounded-xl border-2 border-slate-200 bg-white px-8 text-base font-medium text-slate-900 shadow-sm transition-colors hover:border-primary/35 hover:bg-slate-50 sm:w-auto"
                                    >
                                        Tilmeld holdstart
                                    </Link>
                                </div>
                                <HeroHoldCountdown targetIso={nextHoldStartAt} />
                            </motion.div>

                            <motion.div
                                initial={{ opacity: 0, x: 24 }}
                                animate={{ opacity: 1, x: 0 }}
                                transition={{ duration: 0.55, delay: 0.08 }}
                                className="relative mx-auto w-full max-w-[520px] lg:mx-0 lg:max-w-none"
                            >
                                <div className="pointer-events-none absolute -left-10 top-1/4 -z-10 h-40 w-40 rounded-full bg-primary/15 blur-3xl" />
                                <div className="pointer-events-none absolute -right-6 bottom-0 -z-10 h-36 w-36 rounded-full bg-accent/15 blur-3xl" />

                                <div className="grid grid-cols-2 gap-3 sm:gap-4">
                                    {heroCollageTiles.map((tile, index) => (
                                        <div
                                            key={`hero-tile-${index}`}
                                            className={`group overflow-hidden rounded-2xl border border-slate-200/90 bg-slate-100 shadow-md ring-1 ring-black/[0.04] ${tile.wrapClassName}`}
                                        >
                                            <div className="relative h-full w-full overflow-hidden">
                                                <img
                                                    src={tile.src}
                                                    alt={tile.alt}
                                                    className={`h-full w-full transition duration-500 ease-out group-hover:scale-[1.03] ${tile.imgClassName}`}
                                                />
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </motion.div>
                        </div>
                    </div>
                </section>

                <section className="relative border-y border-slate-200/80 bg-white py-20">
                    <div className="container mx-auto px-4 lg:px-8">
                        <div className="mx-auto mb-12 max-w-2xl text-center">
                            <h2 className="marketing-headline-on-light text-2xl font-bold tracking-tight sm:text-3xl">
                                {homeCopy.why_title}
                            </h2>
                            {homeCopy.why_lead ? (
                                <p className="marketing-lead mt-3 leading-relaxed">{homeCopy.why_lead}</p>
                            ) : null}
                        </div>
                        <div className="mx-auto grid max-w-5xl gap-6 sm:grid-cols-2">
                            {valueBlocks.map((item, blockIndex) => {
                                const Icon = iconFor(item.icon);
                                return (
                                    <motion.div
                                        key={item.id}
                                        initial={{ opacity: 0, y: 14 }}
                                        whileInView={{ opacity: 1, y: 0 }}
                                        viewport={{ once: true, margin: '-40px' }}
                                        transition={{ duration: 0.4, delay: blockIndex * 0.06 }}
                                        className="marketing-card-elevated flex flex-col gap-3"
                                    >
                                        <div className="flex h-11 w-11 items-center justify-center rounded-xl bg-primary/10 text-primary">
                                            <Icon className="h-5 w-5" aria-hidden />
                                        </div>
                                        <h3 className="text-lg font-semibold leading-snug text-slate-900">{item.title}</h3>
                                        <p className="text-sm leading-relaxed text-slate-600">{item.body}</p>
                                    </motion.div>
                                );
                            })}
                        </div>
                    </div>
                </section>

                <section className="relative bg-white py-20">
                    <div className="container mx-auto px-4 lg:px-8">
                        <div className="mx-auto mb-10 max-w-2xl text-center">
                            <h2 className="marketing-headline-on-light text-2xl font-bold tracking-tight sm:text-3xl">
                                {homeCopy.reviews_title}
                            </h2>
                            {homeCopy.reviews_lead ? (
                                <p className="marketing-lead mt-3 leading-relaxed">{homeCopy.reviews_lead}</p>
                            ) : null}
                        </div>
                        <TestimonialCarousel items={testimonials} />
                        {homeCopy.reviews_footnote ? (
                            <p className="mx-auto mt-8 max-w-2xl text-center text-xs text-slate-500">
                                {homeCopy.reviews_footnote}
                            </p>
                        ) : null}
                    </div>
                </section>

                <section className="relative border-t border-slate-200/60 bg-slate-50/50 py-16">
                    <div className="pointer-events-none absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-slate-200/80 to-transparent" />
                    <div className="container mx-auto px-4 lg:px-8">
                        <div className="mx-auto mb-14 max-w-2xl text-center">
                            <h2 className="marketing-headline-on-light text-2xl font-bold tracking-tight sm:text-3xl">
                                {homeCopy.explore_title}
                            </h2>
                            {homeCopy.explore_lead ? (
                                <p className="marketing-lead mt-3 leading-relaxed">{homeCopy.explore_lead}</p>
                            ) : null}
                        </div>
                        <div className="mx-auto grid max-w-4xl gap-6 sm:grid-cols-2">
                            {explore.map((item, index) => {
                                const Icon = item.icon;
                                return (
                                    <motion.div
                                        key={item.href}
                                        initial={{ opacity: 0, y: 16 }}
                                        whileInView={{ opacity: 1, y: 0 }}
                                        viewport={{ once: true, margin: '-40px' }}
                                        transition={{ duration: 0.4, delay: index * 0.05 }}
                                    >
                                        <Link
                                            href={item.href}
                                            className="marketing-card-elevated group flex h-full flex-col transition-shadow hover:shadow-md"
                                        >
                                            <div className="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10 text-primary group-hover:bg-primary/15">
                                                <Icon className="h-6 w-6" />
                                            </div>
                                            <h3 className="text-lg font-semibold text-slate-900">{item.title}</h3>
                                            <p className="mt-2 flex-1 text-sm leading-relaxed text-slate-600">
                                                {item.description}
                                            </p>
                                            <span className="mt-4 inline-flex items-center text-sm font-medium text-primary">
                                                Gå til side
                                                <ArrowRight className="ml-1 h-4 w-4 transition-transform group-hover:translate-x-0.5" />
                                            </span>
                                        </Link>
                                    </motion.div>
                                );
                            })}
                        </div>
                        <div className="mx-auto mt-12 flex max-w-3xl flex-col justify-center gap-3 rounded-2xl border border-slate-200/90 bg-white px-6 py-5 text-center shadow-sm sm:flex-row sm:items-center sm:gap-6 sm:text-left">
                            <p className="text-sm text-slate-600">
                                Vil du se holdene bag rattet først?{' '}
                                <Link
                                    href={instructors.url()}
                                    className="font-semibold text-primary underline-offset-4 hover:underline"
                                >
                                    Vores kørelærere
                                </Link>
                            </p>
                        </div>
                    </div>
                </section>

                <section className="relative overflow-hidden bg-primary py-24 text-white">
                    <div className="pointer-events-none absolute inset-0 bg-[radial-gradient(ellipse_80%_60%_at_50%_-20%,rgba(255,255,255,0.12),transparent_55%)]" />
                    <motion.div
                        initial={{ opacity: 0, scale: 0.98 }}
                        whileInView={{ opacity: 1, scale: 1 }}
                        viewport={{ once: true }}
                        transition={{ duration: 0.55 }}
                        className="container relative mx-auto max-w-2xl px-4 text-center lg:px-8"
                    >
                        <h2 className="text-3xl font-bold tracking-tight text-white sm:text-4xl">{homeCopy.cta_title}</h2>
                        {homeCopy.cta_lead ? (
                            <p className="mt-5 text-lg leading-relaxed text-white/90">{homeCopy.cta_lead}</p>
                        ) : null}
                        <div className="mt-10 flex flex-col flex-wrap items-center justify-center gap-4 sm:flex-row">
                            <Link
                                href={contact.url()}
                                className="inline-flex h-12 items-center justify-center rounded-full border border-white/35 bg-white/10 px-8 text-base font-semibold text-white backdrop-blur-sm transition-colors hover:bg-white/20"
                            >
                                Skriv til os
                            </Link>
                            <Link
                                href={login()}
                                className="inline-flex h-12 items-center justify-center rounded-full bg-white px-8 text-base font-semibold text-primary shadow-lg transition-transform hover:scale-[1.02] active:scale-[0.98]"
                            >
                                Log ind som elev
                            </Link>
                        </div>
                    </motion.div>
                </section>
            </main>
        </MarketingLayout>
    );
}
