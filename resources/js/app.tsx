import { createInertiaApp, router } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import '../css/app.css';
import { initializeTheme } from './hooks/use-appearance';

const appName = import.meta.env.VITE_APP_NAME || 'Køreskole';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.tsx`,
            import.meta.glob('./pages/**/*.tsx'),
        ),
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(
            <StrictMode>
                <App {...props} />
            </StrictMode>,
        );
    },
    progress: {
        color: '#4B5563',
    },
});

router.on('navigate', (event) => {
    const user = event.detail.page.props?.auth?.user as { name?: string; email?: string; role?: string } | undefined;
    if (user?.name && user?.email && user.role === 'student') {
        try {
            localStorage.setItem('koereskole_returning_user', JSON.stringify({ name: user.name, email: user.email }));
        } catch { /* localStorage unavailable */ }
    }
});

// This will set light / dark mode on load...
initializeTheme();
