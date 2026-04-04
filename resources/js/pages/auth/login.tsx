import { Form, Head } from '@inertiajs/react';
import { MailIcon, KeyRoundIcon, ArrowLeftIcon, CheckCircleIcon } from 'lucide-react';
import { useEffect, useState } from 'react';
import InputError from '@/components/input-error';
import TextLink from '@/components/text-link';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthLayout from '@/layouts/auth-layout';
import { store, magicLink } from '@/routes/login';
import { request } from '@/routes/password';

const RETURNING_USER_KEY = 'koereskole_returning_user';

type ReturningUser = {
    name: string;
    email: string;
};

function getReturningUser(): ReturningUser | null {
    try {
        const stored = localStorage.getItem(RETURNING_USER_KEY);
        if (!stored) return null;
        const parsed = JSON.parse(stored);
        if (parsed?.name && parsed?.email) return parsed;
        return null;
    } catch {
        return null;
    }
}

type Props = {
    status?: string;
    canResetPassword: boolean;
    canRequestMagicLink?: boolean;
};

type LoginMode = 'password' | 'magic-link';

export default function Login({
    status,
    canResetPassword,
    canRequestMagicLink = false,
}: Props) {
    const [mode, setMode] = useState<LoginMode>('password');
    const [returningUser, setReturningUser] = useState<ReturningUser | null>(null);

    useEffect(() => {
        setReturningUser(getReturningUser());
    }, []);

    const title = returningUser
        ? `Velkommen tilbage, ${returningUser.name.split(' ')[0]}`
        : 'Log ind på din konto';

    const description = mode === 'magic-link'
        ? 'Indtast din e-mail, så sender vi dig et login-link'
        : 'Indtast din e-mail og adgangskode nedenfor for at logge ind';

    return (
        <AuthLayout title={title} description={description}>
            <Head title="Log ind" />

            {mode === 'password' && (
                <PasswordForm
                    canResetPassword={canResetPassword}
                    canRequestMagicLink={canRequestMagicLink}
                    defaultEmail={returningUser?.email}
                    onSwitchToMagicLink={() => setMode('magic-link')}
                />
            )}

            {mode === 'magic-link' && (
                <MagicLinkForm
                    defaultEmail={returningUser?.email}
                    onSwitchToPassword={() => setMode('password')}
                />
            )}

            {status && (
                <div className="flex items-center justify-center gap-2 rounded-lg border border-green-500/20 bg-green-500/10 px-4 py-3 text-center text-sm font-medium text-green-400">
                    <CheckCircleIcon className="size-4 shrink-0" />
                    {status}
                </div>
            )}
        </AuthLayout>
    );
}

function PasswordForm({
    canResetPassword,
    canRequestMagicLink,
    defaultEmail,
    onSwitchToMagicLink,
}: {
    canResetPassword: boolean;
    canRequestMagicLink: boolean;
    defaultEmail?: string;
    onSwitchToMagicLink: () => void;
}) {
    return (
        <Form
            {...store.form()}
            resetOnSuccess={['password']}
            className="flex flex-col gap-6"
        >
            {({ processing, errors }) => (
                <>
                    <div className="grid gap-6">
                        <div className="grid gap-2">
                            <Label htmlFor="email">E-mailadresse</Label>
                            <Input
                                id="email"
                                type="email"
                                name="email"
                                defaultValue={defaultEmail}
                                required
                                autoFocus
                                tabIndex={1}
                                autoComplete="email"
                                placeholder="din@email.dk"
                            />
                            <InputError message={errors.email} />
                        </div>

                        <div className="grid gap-2">
                            <div className="flex items-center">
                                <Label htmlFor="password">Adgangskode</Label>
                                {canResetPassword && (
                                    <TextLink
                                        href={request()}
                                        className="ml-auto text-sm"
                                        tabIndex={5}
                                    >
                                        Glemt adgangskode?
                                    </TextLink>
                                )}
                            </div>
                            <Input
                                id="password"
                                type="password"
                                name="password"
                                required
                                tabIndex={2}
                                autoComplete="current-password"
                                placeholder="Adgangskode"
                            />
                            <InputError message={errors.password} />
                        </div>

                        <div className="flex items-center space-x-3">
                            <Checkbox
                                id="remember"
                                name="remember"
                                tabIndex={3}
                            />
                            <Label htmlFor="remember">Husk mig</Label>
                        </div>

                        <Button
                            type="submit"
                            className="mt-2 w-full bg-mk-accent text-white shadow-[0_8px_28px_-8px_rgba(232,0,29,0.45)] hover:bg-mk-accent-soft"
                            tabIndex={4}
                            disabled={processing}
                            data-test="login-button"
                        >
                            {processing && <Spinner />}
                            <KeyRoundIcon className="size-4" />
                            Log ind
                        </Button>
                    </div>

                    {canRequestMagicLink && (
                        <div className="relative">
                            <div className="absolute inset-0 flex items-center">
                                <span className="w-full border-t border-white/10" />
                            </div>
                            <div className="relative flex justify-center text-xs uppercase">
                                <span className="bg-mk-surface px-2 text-mk-muted">eller</span>
                            </div>
                        </div>
                    )}

                    {canRequestMagicLink && (
                        <Button
                            type="button"
                            variant="outline"
                            className="w-full border-white/10 text-mk-muted hover:border-white/20 hover:text-mk-text"
                            onClick={onSwitchToMagicLink}
                            tabIndex={6}
                        >
                            <MailIcon className="size-4" />
                            Send mig et login-link
                        </Button>
                    )}
                </>
            )}
        </Form>
    );
}

function MagicLinkForm({
    defaultEmail,
    onSwitchToPassword,
}: {
    defaultEmail?: string;
    onSwitchToPassword: () => void;
}) {
    return (
        <Form
            action={magicLink.url()}
            method="post"
            className="flex flex-col gap-6"
        >
            {({ processing, errors }) => (
                <>
                    <div className="grid gap-6">
                        <div className="grid gap-2">
                            <Label htmlFor="magic-email">E-mailadresse</Label>
                            <Input
                                id="magic-email"
                                type="email"
                                name="email"
                                defaultValue={defaultEmail}
                                required
                                autoFocus
                                tabIndex={1}
                                autoComplete="email"
                                placeholder="din@email.dk"
                            />
                            <InputError message={errors.email} />
                        </div>

                        <Button
                            type="submit"
                            className="mt-2 w-full bg-mk-accent text-white shadow-[0_8px_28px_-8px_rgba(232,0,29,0.45)] hover:bg-mk-accent-soft"
                            tabIndex={2}
                            disabled={processing}
                        >
                            {processing && <Spinner />}
                            <MailIcon className="size-4" />
                            Send login-link
                        </Button>
                    </div>

                    <Button
                        type="button"
                        variant="ghost"
                        className="w-full text-mk-muted hover:text-mk-text"
                        onClick={onSwitchToPassword}
                        tabIndex={3}
                    >
                        <ArrowLeftIcon className="size-4" />
                        Log ind med adgangskode
                    </Button>
                </>
            )}
        </Form>
    );
}
