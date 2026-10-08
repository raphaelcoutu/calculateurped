import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';
import type { ComponentType } from 'react';

type PageModule = { default: ComponentType<any> };

const pages = import.meta.glob<PageModule>('./pages/**/*.tsx');

void createInertiaApp({
    title: (title) => title ? `${title} · Calculateur pédiatrique` : 'Calculateur pédiatrique',
    resolve: async (name) => {
        const loadPage = pages[`./pages/${name}.tsx`];

        if (!loadPage) {
            throw new Error(`Page Inertia introuvable : ${name}`);
        }

        return loadPage().then(page => page.default);
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
    progress: {
        color: '#117463',
    },
});

const appearance = window.localStorage.getItem('appearance');
const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
document.documentElement.classList.toggle('dark', appearance === 'dark' || (appearance === null && prefersDark));
