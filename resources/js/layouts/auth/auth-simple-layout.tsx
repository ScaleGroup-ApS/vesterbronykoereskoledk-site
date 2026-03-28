import { Link } from '@inertiajs/react';
import AppLogoIcon from '@/components/app-logo-icon';
import { home } from '@/routes';
import type { AuthLayoutProps } from '@/types';

export default function AuthSimpleLayout({
    children,
    title,
    description,
}: AuthLayoutProps) {
    return (
        <div className="marketing-public-site marketing-atmosphere relative flex min-h-svh flex-col items-center justify-center gap-6 bg-mk-base p-6 md:p-10">
            {/* Dot grid overlay */}
            <div
                className="pointer-events-none absolute inset-0 opacity-[0.35]"
                style={{
                    backgroundImage: 'radial-gradient(circle, rgba(255,255,255,0.06) 1px, transparent 1px)',
                    backgroundSize: '48px 48px',
                }}
                aria-hidden
            />
            {/* Red glow */}
            <div className="pointer-events-none absolute bottom-0 left-1/2 h-[340px] w-[700px] -translate-x-1/2 rounded-full bg-mk-accent/[0.07] blur-[110px]" aria-hidden />

            <div className="auth-glass-panel relative">
                <div className="flex flex-col gap-8">
                    <div className="flex flex-col items-center gap-4">
                        <Link
                            href={home()}
                            className="flex flex-col items-center gap-2 font-medium"
                        >
                            <div className="mb-1 flex h-10 w-10 items-center justify-center rounded-xl bg-mk-accent shadow-[0_0_28px_-6px_rgba(232,0,29,0.6)]">
                                <AppLogoIcon className="size-9 fill-current text-white" />
                            </div>
                            <span className="sr-only">{title}</span>
                        </Link>

                        <div className="space-y-2 text-center">
                            <h1 className="font-heading text-xl font-semibold tracking-tight text-mk-text">{title}</h1>
                            <p className="text-center text-sm text-mk-muted">{description}</p>
                        </div>
                    </div>
                    {children}
                </div>
            </div>
        </div>
    );
}
