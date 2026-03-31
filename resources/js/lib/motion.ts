/** Shared Framer Motion variants used across marketing pages */

export const sectionHeadVariants = {
    hidden: {},
    visible: { transition: { staggerChildren: 0.11 } },
} as const;

export const sectionLineVariants = {
    hidden: { opacity: 0, y: 28 },
    visible: { opacity: 1, y: 0, transition: { duration: 0.6, ease: [0.16, 1, 0.3, 1] as const } },
} as const;

export const accentLineVariants = {
    hidden: { scaleX: 0, opacity: 0 },
    visible: { scaleX: 1, opacity: 1, transition: { duration: 0.5, delay: 0.2, ease: [0.16, 1, 0.3, 1] as const } },
} as const;

export const cardVariants = {
    hidden: { opacity: 0, y: 24 },
    visible: { opacity: 1, y: 0, transition: { duration: 0.55, ease: [0.16, 1, 0.3, 1] as const } },
} as const;

export const cardContainerVariants = {
    hidden: {},
    visible: { transition: { staggerChildren: 0.08 } },
} as const;
