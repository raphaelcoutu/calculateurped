import { Link, router, usePage } from '@inertiajs/react';
import type { ReactNode } from 'react';

type SharedProps = {
    appName: string;
    appVersion: string;
    phpVersion: string;
    flash: { status?: string | null };
    auth: {
        user: null | {
            id: number;
            name: string;
            email: string;
            is_superuser: boolean;
        };
    };
};

export default function AppLayout({ children }: { children: ReactNode }) {
    const { appName, appVersion, auth, flash, phpVersion } = usePage<SharedProps>().props;

    return (
        <div className="min-h-screen bg-slate-50 text-slate-900 dark:bg-[#101a1a] dark:text-slate-100">
            <header className="border-b border-slate-200 bg-white/90 dark:border-white/10 dark:bg-[#142120]">
                <div className="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
                    <Link href="/" className="flex items-center gap-3 no-underline">
                        <span className="grid size-10 place-items-center rounded-xl bg-brand-600 text-lg font-bold text-white shadow-sm">+</span>
                        <span>
                            <span className="block text-sm font-semibold text-slate-950 dark:text-white">{appName || 'Calculateur pédiatrique'}</span>
                            <span className="block text-xs text-slate-500 dark:text-slate-400">Urgences et soins intensifs</span>
                        </span>
                    </Link>
                    <nav className="flex items-center gap-2 text-sm">
                        {auth.user ? (
                            <>
                                {auth.user.is_superuser ? (
                                    <Link href="/admin/organizations" className="rounded-lg px-3 py-2 font-medium text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10">Organisations</Link>
                                ) : (
                                    <Link href="/organization/profile" className="rounded-lg px-3 py-2 font-medium text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10">Mon organisation</Link>
                                )}
                                <span className="hidden text-slate-500 sm:inline">{auth.user.name}</span>
                                <button type="button" onClick={() => router.post('/logout')} className="rounded-lg border border-slate-200 px-3 py-2 font-medium text-slate-700 hover:bg-slate-50 dark:border-white/15 dark:text-slate-200 dark:hover:bg-white/10">Déconnexion</button>
                            </>
                        ) : (
                            <Link href="/login" className="rounded-lg px-3 py-2 font-medium text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10">Connexion</Link>
                        )}
                        <button type="button" onClick={() => {
                            const dark = !document.documentElement.classList.contains('dark');
                            document.documentElement.classList.toggle('dark', dark);
                            window.localStorage.setItem('appearance', dark ? 'dark' : 'light');
                        }} aria-label="Changer le thème" className="grid size-10 place-items-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 dark:border-white/15 dark:text-slate-200 dark:hover:bg-white/10">
                            <span aria-hidden="true">◐</span>
                        </button>
                    </nav>
                </div>
            </header>
            <main className="mx-auto w-full max-w-6xl px-4 py-8 sm:px-6 sm:py-10">
                {flash.status && <div role="status" className="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900 dark:border-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-100">{flash.status}</div>}
                {children}
            </main>
            <footer className="mx-auto max-w-6xl px-4 pb-8 text-center text-xs text-slate-500 dark:text-slate-400 sm:px-6">
                {appVersion && <span>Version {appVersion} · </span>}PHP {phpVersion} · Outil d’aide à la décision clinique
            </footer>
        </div>
    );
}
