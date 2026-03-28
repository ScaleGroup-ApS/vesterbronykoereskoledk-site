import { Head, Link, usePage } from '@inertiajs/react';
import { motion } from 'framer-motion';
import { useEffect, useState } from 'react';
import {
    ArrowRight,
    BookOpen,
    Car,
    ChevronDown,
    MessageCircle,
    Package,
    Sparkles,
    Users,
    type LucideIcon,
} from 'lucide-react';
import { HeroHoldCountdown } from '@/components/marketing/hero-hold-countdown';
import { PackageCarousel } from '@/components/marketing/package-carousel';
import { TestimonialCarousel } from '@/components/marketing/testimonial-carousel';
import MarketingLayout from '@/layouts/marketing-layout';
import { login } from '@/routes';
import { show as enrollShow } from '@/routes/enrollment';
import { about, contact, features, instructors, packages } from '@/routes/marketing';
import type { MarketingOffer } from '@/types/marketing-offer';
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

const HERO_IMAGE = '/images/hero.png';

const trafficLightSequence = [
    { red: 1, yellow: 0, green: 0, duration: 3 },
    { red: 0.15, yellow: 1, green: 0, duration: 1 },
    { red: 0.15, yellow: 0, green: 1, duration: 3.5 },
    { red: 0.15, yellow: 1, green: 0, duration: 0.8 },
];

function TrafficLight() {
    const [phase, setPhase] = useState(0);

    useEffect(() => {
        const current = trafficLightSequence[phase];
        const id = setTimeout(() => setPhase((p) => (p + 1) % trafficLightSequence.length), current.duration * 1000);
        return () => clearTimeout(id);
    }, [phase]);

    const { red, yellow, green } = trafficLightSequence[phase];

    return (
        <div className="flex flex-col items-center gap-1 rounded-lg bg-[#111] p-1.5 shadow-lg ring-1 ring-white/[0.06]">
            {/* Red */}
            <motion.div
                animate={{ opacity: red, boxShadow: red > 0.5 ? '0 0 12px 4px rgba(232,0,29,0.7)' : 'none' }}
                transition={{ duration: 0.25 }}
                className="h-4 w-4 rounded-full bg-[#E8001D]"
            />
            {/* Yellow */}
            <motion.div
                animate={{ opacity: yellow, boxShadow: yellow > 0.5 ? '0 0 12px 4px rgba(250,180,0,0.7)' : 'none' }}
                transition={{ duration: 0.25 }}
                className="h-4 w-4 rounded-full bg-[#FAB400]"
            />
            {/* Green */}
            <motion.div
                animate={{ opacity: green, boxShadow: green > 0.5 ? '0 0 12px 4px rgba(34,197,94,0.7)' : 'none' }}
                transition={{ duration: 0.25 }}
                className="h-4 w-4 rounded-full bg-[#22C55E]"
            />
        </div>
    );
}

const heroCollageTiles = [
    {
        src: HERO_IMAGE,
        alt: 'Køreelev og kørelærer ved bilen',
        imgClassName: 'object-cover object-[center_36%]',
        wrapClassName: 'col-span-2 aspect-[16/10] sm:aspect-[2.1/1]',
    },
    {
        src: HERO_IMAGE,
        alt: 'Fokus på undervisning og struktur',
        imgClassName: 'object-cover object-[center_62%]',
        wrapClassName: 'aspect-[4/5] sm:aspect-square',
    },
    {
        src: HERO_IMAGE,
        alt: 'Træning i rigtig trafik',
        imgClassName: 'object-cover object-[center_48%]',
        wrapClassName: 'aspect-[4/5] sm:aspect-square',
    },
] as const;

import { accentLineVariants, sectionHeadVariants, sectionLineVariants } from '@/lib/motion';

/* Word-split animation variants */
const wordContainerVariants = {
    hidden: {},
    visible: {
        transition: {
            staggerChildren: 0.09,
            delayChildren: 0.15,
        },
    },
};

const wordVariants = {
    hidden: { y: 64, opacity: 0 },
    visible: {
        y: 0,
        opacity: 1,
        transition: { duration: 0.9, ease: [0.16, 1, 0.3, 1] as const },
    },
};

export default function Welcome() {
    const {
        homeCopy,
        valueBlocks,
        testimonials,
        nextHoldStartAt,
        heroHoldSpotsRemaining,
        tilmeldHoldstartOfferSlug,
        marketingOffers,
    } = usePage().props as unknown as {
        homeCopy: MarketingHomeCopyProps;
        valueBlocks: MarketingValueBlockProps[];
        testimonials: MarketingTestimonialProps[];
        nextHoldStartAt: string | null;
        heroHoldSpotsRemaining: number | null;
        tilmeldHoldstartOfferSlug: string | null;
        marketingOffers?: MarketingOffer[];
    };

    const primaryPackages = marketingOffers ?? [];

    const tilmeldHoldstartHref =
        tilmeldHoldstartOfferSlug !== null && tilmeldHoldstartOfferSlug !== ''
            ? enrollShow.url({ offer: tilmeldHoldstartOfferSlug })
            : packages.url();

    const prefixWords = homeCopy.hero_headline_prefix.split(' ');
    const accentWords = homeCopy.hero_headline_accent.split(' ');

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
                {/* ── HERO ─────────────────────────────────────── */}
                <section className="relative min-h-[92vh] flex items-center overflow-hidden bg-mk-base">
                    {/* Background: subtle grid + red glow */}
                    <div className="pointer-events-none absolute inset-0" aria-hidden>
                        {/* Subtle dot grid */}
                        <div
                            className="absolute inset-0 opacity-[0.4]"
                            style={{
                                backgroundImage:
                                    'radial-gradient(circle, rgba(255,255,255,0.06) 1px, transparent 1px)',
                                backgroundSize: '48px 48px',
                            }}
                        />
                        {/* Red glow bottom */}
                        <div className="absolute bottom-0 left-1/2 -translate-x-1/2 h-[420px] w-[900px] rounded-full bg-mk-accent/[0.07] blur-[120px]" />
                        {/* Accent glow top-right */}
                        <div className="absolute -top-20 right-0 h-[350px] w-[550px] rounded-full bg-mk-accent/[0.04] blur-[100px]" />
                    </div>

                    {/* Animated background cars + road strip */}
                    <div className="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden>
                        {/* Road strip at bottom */}
                        <div className="absolute bottom-0 inset-x-0 h-14 bg-[#111]/60 overflow-hidden">
                            {/* Animated centre-line dashes — single strip that slides */}
                            <motion.div
                                className="absolute inset-y-0 flex items-center gap-8"
                                style={{ width: '200%' }}
                                initial={{ x: 0 }}
                                animate={{ x: '-50%' }}
                                transition={{ duration: 3, repeat: Infinity, ease: 'linear' }}
                            >
                                {Array.from({ length: 24 }, (_, i) => (
                                    <div key={i} className="h-[3px] w-10 shrink-0 rounded-full bg-white/20" />
                                ))}
                            </motion.div>
                        </div>

                        {/* Cars */}
                        <motion.div
                            initial={{ x: -120 }}
                            animate={{ x: '120vw' }}
                            transition={{ duration: 18, repeat: Infinity, ease: 'linear' }}
                            className="absolute bottom-3 opacity-[0.18]"
                        >
                            <Car className="h-8 w-8 text-white" />
                        </motion.div>
                        <motion.div
                            initial={{ x: '110vw' }}
                            animate={{ x: -120 }}
                            transition={{ duration: 24, repeat: Infinity, ease: 'linear', delay: 6 }}
                            className="absolute bottom-3 opacity-[0.12] scale-x-[-1]"
                        >
                            <Car className="h-7 w-7 text-white" />
                        </motion.div>

                        {/* Traffic light — visible bottom-right corner */}
                        <div className="absolute bottom-14 right-8 opacity-70 sm:right-16">
                            <TrafficLight />
                        </div>
                    </div>

                    <div className="container relative mx-auto max-w-6xl px-6 py-24 lg:px-8">
                        <div className="grid items-center gap-14 lg:grid-cols-[1fr_0.9fr] lg:gap-16">
                            {/* Left: copy */}
                            <div className="flex flex-col text-center lg:text-left">
                                {/* Eyebrow */}

                                {/* Word-split headline */}
                                <h1
                                    className="overflow-hidden font-heading font-extrabold text-mk-text"
                                    style={{ fontSize: 'clamp(2.6rem, 6vw, 4.5rem)', lineHeight: 1.08, letterSpacing: '-0.03em' }}
                                >
                                    <motion.span
                                        className="flex flex-wrap justify-center gap-x-[0.22em] lg:justify-start"
                                        variants={wordContainerVariants}
                                        initial="hidden"
                                        animate="visible"
                                    >
                                        {prefixWords.map((word, i) => (
                                            <motion.span
                                                key={i}
                                                variants={wordVariants}
                                                className="inline-block"
                                            >
                                                {word}
                                            </motion.span>
                                        ))}
                                    </motion.span>
                                    <motion.span
                                        className="mt-1 flex flex-wrap justify-center gap-x-[0.22em] text-mk-accent lg:justify-start"
                                        variants={wordContainerVariants}
                                        initial="hidden"
                                        animate="visible"
                                    >
                                        {accentWords.map((word, i) => (
                                            <motion.span
                                                key={i}
                                                variants={wordVariants}
                                                className="inline-block"
                                            >
                                                {word}
                                            </motion.span>
                                        ))}
                                    </motion.span>
                                </h1>

                                {/* Subtitle */}
                                {homeCopy.hero_subtitle ? (
                                    <motion.p
                                        initial={{ opacity: 0, y: 20 }}
                                        animate={{ opacity: 1, y: 0 }}
                                        transition={{ duration: 0.7, delay: 0.8 }}
                                        className="mx-auto mt-6 max-w-xl text-lg leading-relaxed text-mk-muted lg:mx-0"
                                    >
                                        {homeCopy.hero_subtitle}
                                    </motion.p>
                                ) : null}

                                {/* CTAs */}
                                <motion.div
                                    initial={{ opacity: 0, y: 20 }}
                                    animate={{ opacity: 1, y: 0 }}
                                    transition={{ duration: 0.6, delay: 1.05 }}
                                    className="mt-10 flex flex-col flex-wrap justify-center gap-3 sm:flex-row lg:justify-start"
                                >
                                    <Link href={packages.url()} className="mk-btn-primary">
                                        Se priser
                                    </Link>
                                    <Link href={tilmeldHoldstartHref} className="mk-btn-ghost">
                                        Tilmeld holdstart
                                    </Link>
                                </motion.div>

                                {/* Countdown pill */}
                                <motion.div
                                    initial={{ opacity: 0 }}
                                    animate={{ opacity: 1 }}
                                    transition={{ duration: 0.6, delay: 1.3 }}
                                >
                                    <HeroHoldCountdown
                                        key={nextHoldStartAt ?? 'none'}
                                        targetIso={nextHoldStartAt}
                                        spotsRemaining={heroHoldSpotsRemaining}
                                    />
                                </motion.div>
                            </div>

                            {/* Right: image collage */}
                            <motion.div
                                initial={{ opacity: 0, x: 32 }}
                                animate={{ opacity: 1, x: 0 }}
                                transition={{ duration: 0.7, delay: 0.2 }}
                                className="relative mx-auto w-full max-w-[520px] lg:mx-0 lg:max-w-none"
                            >
                                {/* Glow behind collage */}
                                <div className="pointer-events-none absolute -left-8 top-1/3 -z-10 h-48 w-48 rounded-full bg-mk-accent/10 blur-3xl" />

                                <motion.div
                                    initial="hidden"
                                    animate="visible"
                                    variants={{
                                        hidden: {},
                                        visible: { transition: { staggerChildren: 0.1, delayChildren: 0.3 } },
                                    }}
                                    className="grid grid-cols-2 gap-3 sm:gap-4"
                                >
                                    {heroCollageTiles.map((tile, index) => (
                                        <motion.div
                                            key={`hero-tile-${index}`}
                                            variants={{
                                                hidden: { opacity: 0, y: 24, scale: 0.95 },
                                                visible: { opacity: 1, y: 0, scale: 1 },
                                            }}
                                            transition={{ duration: 0.65, ease: [0.16, 1, 0.3, 1] }}
                                            className={`group overflow-hidden rounded-2xl border border-mk-border bg-mk-surface shadow-lg ${tile.wrapClassName}`}
                                        >
                                            <div className="relative h-full w-full overflow-hidden">
                                                <img
                                                    src={tile.src}
                                                    alt={tile.alt}
                                                    className={`h-full w-full transition duration-500 ease-out group-hover:scale-[1.04] ${tile.imgClassName}`}
                                                />
                                                <div className="absolute inset-0 bg-gradient-to-t from-black/30 via-transparent to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100" />
                                                {/* Red top border on hover */}
                                                <div className="absolute inset-x-0 top-0 h-[2px] bg-mk-accent scale-x-0 origin-left transition-transform duration-300 group-hover:scale-x-100" />
                                            </div>
                                        </motion.div>
                                    ))}
                                </motion.div>
                            </motion.div>
                        </div>
                    </div>

                    {/* Scroll indicator */}
                    <div className="mk-scroll-indicator" aria-hidden>
                        <ChevronDown size={22} />
                    </div>
                </section>

                {/* ── WHY US ───────────────────────────────────── */}
                <section className="relative bg-mk-surface py-24 md:py-32">
                    <div className="pointer-events-none absolute inset-x-0 top-0 h-px" style={{ background: 'linear-gradient(to right, transparent, #2A2A2A, transparent)' }} aria-hidden />
                    <div className="container mx-auto max-w-7xl px-6 lg:px-8">
                        <motion.div
                            className="mx-auto mb-16 max-w-2xl text-center"
                            variants={sectionHeadVariants}
                            initial="hidden"
                            whileInView="visible"
                            viewport={{ once: true, margin: '-60px' }}
                        >
                            <motion.p className="mk-eyebrow" variants={sectionLineVariants}>Hvorfor os</motion.p>
                            <motion.div variants={accentLineVariants} className="mx-auto mt-1 mb-3 h-px w-10 origin-left bg-mk-accent" />
                            <motion.h2 className="text-4xl font-bold text-mk-text sm:text-5xl leading-tight" variants={sectionLineVariants}>
                                {homeCopy.why_title}
                            </motion.h2>
                            {homeCopy.why_lead ? (
                                <motion.p className="mt-4 text-lg leading-relaxed text-mk-muted" variants={sectionLineVariants}>{homeCopy.why_lead}</motion.p>
                            ) : null}
                        </motion.div>
                        <div className="mx-auto grid max-w-5xl gap-6 sm:grid-cols-2">
                            {valueBlocks.map((item, blockIndex) => {
                                const Icon = iconFor(item.icon);
                                return (
                                    <motion.div
                                        key={item.id}
                                        initial={{ opacity: 0, y: 20 }}
                                        whileInView={{ opacity: 1, y: 0 }}
                                        viewport={{ once: true, margin: '-40px' }}
                                        transition={{ duration: 0.5, delay: blockIndex * 0.07 }}
                                        whileHover={{ y: -6 }}
                                        className="marketing-card-elevated"
                                    >
                                        <motion.div
                                            whileHover={{ rotate: 8, scale: 1.12 }}
                                            transition={{ type: 'spring', stiffness: 280 }}
                                            className="mb-4 flex h-11 w-11 items-center justify-center rounded-xl bg-mk-accent/10 text-mk-accent"
                                        >
                                            <Icon className="h-5 w-5" aria-hidden />
                                        </motion.div>
                                        <h3 className="text-lg font-semibold leading-snug text-mk-text">{item.title}</h3>
                                        <p className="mt-2 text-sm leading-relaxed text-mk-muted">{item.body}</p>
                                    </motion.div>
                                );
                            })}
                        </div>
                    </div>
                    <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px" style={{ background: 'linear-gradient(to right, transparent, #2A2A2A, transparent)' }} aria-hidden />
                </section>

                {/* ── PACKAGES ─────────────────────────────────── */}
                {primaryPackages.length > 0 ? (
                    <section className="relative bg-mk-base py-24 md:py-32" aria-labelledby="home-packages-heading">
                        <div className="container mx-auto max-w-7xl px-6 lg:px-8">
                            <motion.div
                                className="mx-auto mb-12 max-w-2xl text-center"
                                variants={sectionHeadVariants}
                                initial="hidden"
                                whileInView="visible"
                                viewport={{ once: true, margin: '-60px' }}
                            >
                                <motion.p className="mk-eyebrow" variants={sectionLineVariants}>Priser & pakker</motion.p>
                                <motion.div variants={accentLineVariants} className="mx-auto mt-1 mb-3 h-px w-10 origin-left bg-mk-accent" />
                                <motion.h2 id="home-packages-heading" className="text-4xl font-bold text-mk-text sm:text-5xl leading-tight" variants={sectionLineVariants}>
                                    Vores pakker
                                </motion.h2>
                                <motion.p className="mt-4 leading-relaxed text-mk-muted" variants={sectionLineVariants}>
                                    Gennemskuelige priser uden skjulte gebyrer.
                                </motion.p>
                            </motion.div>
                            <PackageCarousel items={primaryPackages} />
                        </div>
                        <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px" style={{ background: 'linear-gradient(to right, transparent, #2A2A2A, transparent)' }} aria-hidden />
                    </section>
                ) : null}

                {/* ── TESTIMONIALS ─────────────────────────────── */}
                <section className="relative bg-mk-surface py-24 md:py-32">
                    <div className="pointer-events-none absolute inset-x-0 top-0 h-px" style={{ background: 'linear-gradient(to right, transparent, #2A2A2A, transparent)' }} aria-hidden />
                    <div className="container mx-auto max-w-7xl px-6 lg:px-8">
                        <motion.div
                            className="mx-auto mb-12 max-w-2xl text-center"
                            variants={sectionHeadVariants}
                            initial="hidden"
                            whileInView="visible"
                            viewport={{ once: true, margin: '-60px' }}
                        >
                            <motion.p className="mk-eyebrow" variants={sectionLineVariants}>Anmeldelser</motion.p>
                            <motion.div variants={accentLineVariants} className="mx-auto mt-1 mb-3 h-px w-10 origin-left bg-mk-accent" />
                            <motion.h2 className="text-4xl font-bold text-mk-text sm:text-5xl leading-tight" variants={sectionLineVariants}>
                                {homeCopy.reviews_title}
                            </motion.h2>
                            {homeCopy.reviews_lead ? (
                                <motion.p className="mt-4 leading-relaxed text-mk-muted" variants={sectionLineVariants}>{homeCopy.reviews_lead}</motion.p>
                            ) : null}
                        </motion.div>
                        <TestimonialCarousel items={testimonials} />
                        {homeCopy.reviews_footnote ? (
                            <p className="mx-auto mt-8 max-w-2xl text-center text-xs text-mk-muted/60">
                                {homeCopy.reviews_footnote}
                            </p>
                        ) : null}
                    </div>
                    <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px" style={{ background: 'linear-gradient(to right, transparent, #2A2A2A, transparent)' }} aria-hidden />
                </section>

                {/* ── EXPLORE ──────────────────────────────────── */}
                <section className="relative bg-mk-base py-24 md:py-32">
                    <div className="container mx-auto max-w-7xl px-6 lg:px-8">
                        <motion.div
                            className="mx-auto mb-14 max-w-2xl text-center"
                            variants={sectionHeadVariants}
                            initial="hidden"
                            whileInView="visible"
                            viewport={{ once: true, margin: '-60px' }}
                        >
                            <motion.p className="mk-eyebrow" variants={sectionLineVariants}>Udforsk</motion.p>
                            <motion.div variants={accentLineVariants} className="mx-auto mt-1 mb-3 h-px w-10 origin-left bg-mk-accent" />
                            <motion.h2 className="text-4xl font-bold text-mk-text sm:text-5xl leading-tight" variants={sectionLineVariants}>
                                {homeCopy.explore_title}
                            </motion.h2>
                            {homeCopy.explore_lead ? (
                                <motion.p className="mt-4 leading-relaxed text-mk-muted" variants={sectionLineVariants}>{homeCopy.explore_lead}</motion.p>
                            ) : null}
                        </motion.div>
                        <div className="mx-auto grid max-w-4xl gap-6 sm:grid-cols-2">
                            {explore.map((item, index) => {
                                const Icon = item.icon;
                                return (
                                    <motion.div
                                        key={item.href}
                                        initial={{ opacity: 0, y: 20 }}
                                        whileInView={{ opacity: 1, y: 0 }}
                                        viewport={{ once: true, margin: '-40px' }}
                                        transition={{ duration: 0.45, delay: index * 0.06 }}
                                        whileHover={{ y: -6 }}
                                    >
                                        <Link
                                            href={item.href}
                                            className="mk-card group flex h-full flex-col p-6"
                                        >
                                            <div className="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-mk-accent/10 text-mk-accent transition-colors duration-200 group-hover:bg-mk-accent/15">
                                                <Icon className="h-6 w-6" aria-hidden />
                                            </div>
                                            <h3 className="font-heading text-lg font-semibold text-mk-text">{item.title}</h3>
                                            <p className="mt-2 flex-1 text-sm leading-relaxed text-mk-muted">
                                                {item.description}
                                            </p>
                                            <span className="mt-5 inline-flex items-center text-sm font-medium text-mk-accent">
                                                Gå til side
                                                <ArrowRight className="ml-1 h-4 w-4 transition-transform duration-200 group-hover:translate-x-1" />
                                            </span>
                                        </Link>
                                    </motion.div>
                                );
                            })}
                        </div>

                        {/* Instructors teaser */}
                        <div className="mx-auto mt-10 flex max-w-3xl flex-col justify-center gap-3 rounded-2xl border border-mk-border bg-mk-surface px-6 py-5 text-center sm:flex-row sm:items-center sm:gap-6 sm:text-left">
                            <p className="text-sm text-mk-muted">
                                Vil du se holdene bag rattet først?{' '}
                                <Link
                                    href={instructors.url()}
                                    className="font-semibold text-mk-accent underline-offset-4 hover:underline"
                                >
                                    Vores kørelærere
                                </Link>
                            </p>
                        </div>
                    </div>
                    <div className="pointer-events-none absolute inset-x-0 bottom-0 h-px" style={{ background: 'linear-gradient(to right, transparent, #2A2A2A, transparent)' }} aria-hidden />
                </section>

                {/* ── FINAL CTA ────────────────────────────────── */}
                <section className="relative overflow-hidden bg-mk-surface py-28">
                    {/* Red glow */}
                    <div className="pointer-events-none absolute inset-0" aria-hidden>
                        <div className="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 h-[400px] w-[700px] rounded-full bg-mk-accent/[0.08] blur-[100px]" />
                    </div>
                    <motion.div
                        initial={{ opacity: 0, y: 24 }}
                        whileInView={{ opacity: 1, y: 0 }}
                        viewport={{ once: true }}
                        transition={{ duration: 0.6 }}
                        className="container relative mx-auto max-w-2xl px-6 text-center lg:px-8"
                    >
                        <p className="mk-eyebrow justify-center flex">Kom i gang</p>
                        <h2 className="text-4xl font-bold text-mk-text sm:text-5xl leading-tight">{homeCopy.cta_title}</h2>
                        {homeCopy.cta_lead ? (
                            <p className="mt-5 text-lg leading-relaxed text-mk-muted">{homeCopy.cta_lead}</p>
                        ) : null}
                        <div className="mt-10 flex flex-col flex-wrap items-center justify-center gap-4 sm:flex-row">
                            <Link href={contact.url()} className="mk-btn-ghost">
                                Skriv til os
                            </Link>
                            <Link href={login()} className="mk-btn-primary">
                                Log ind som elev
                            </Link>
                        </div>
                    </motion.div>
                </section>
            </main>
        </MarketingLayout>
    );
}
