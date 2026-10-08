import { Link, usePage } from '@inertiajs/react';
import type { ReactNode } from 'react';

export default function AuthLayout({ children }: { children: ReactNode }) {
    const { flash } = usePage<{ flash: { status?: string | null } }>().props;

    return (
        <main className="grid min-h-screen place-items-center bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-brand-100 via-slate-50 to-slate-100 px-4 py-10 dark:from-brand-900 dark:via-[#101a1a] dark:to-[#101a1a]">
            <div className="w-full max-w-md">
                <Link href="/" className="mb-8 flex flex-col items-center gap-3 text-center no-underline">
                    <span className="grid size-14 place-items-center rounded-2xl bg-brand-600 text-2xl font-bold text-white shadow-lg shadow-brand-600/20">+</span>
                    <span>
                        <span className="block text-lg font-semibold text-slate-900 dark:text-white">Calculateur pédiatrique</span>
                        <span className="block text-sm text-slate-600 dark:text-slate-300">Espace sécurisé des organisations</span>
                    </span>
                </Link>
                {flash.status && <div role="status" className="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900 dark:border-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-100">{flash.status}</div>}
                {children}
                <p className="mt-6 text-center text-xs text-slate-500 dark:text-slate-400">Accès réservé aux comptes autorisés.</p>
            </div>
        </main>
    );
}
