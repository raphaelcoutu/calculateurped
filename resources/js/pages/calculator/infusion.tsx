import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/components/app-layout';

type Infusion = { name: string; brandName: string; type: number; concentration: string; dosage: string; rate: string; instructions: string };

const categories: Record<number, string> = {
    1: 'Sédation',
    2: 'Cardiovasculaire',
    3: 'Autres médicaments',
};

export default function InfusionPage({ weight, infusions }: { weight: number; infusions: Infusion[] }) {
    return (
        <AppLayout>
            <Head title="Perfusions" />
            <div className="mb-7 flex flex-wrap items-end justify-between gap-4"><div><p className="text-xs font-semibold uppercase tracking-[0.18em] text-brand-700 dark:text-brand-100">Résultats du calcul</p><h1 className="mt-2 text-3xl font-semibold text-slate-950 dark:text-white">Perfusions pédiatriques</h1><p className="mt-2 text-sm text-slate-600 dark:text-slate-300">Poids utilisé : <strong>{weight} kg</strong></p></div><Link href="/" className="rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-white dark:border-white/15 dark:text-slate-200 dark:hover:bg-white/5">Modifier le poids</Link></div>
            <div className="grid gap-4 md:grid-cols-2">{infusions.map((item, index) => <article key={`${item.name}-${index}`} className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#182524]"><div className="flex items-start justify-between gap-3"><div><h2 className="font-semibold text-slate-950 dark:text-white">{item.name}</h2>{item.brandName && <p className="mt-1 text-xs text-slate-500 dark:text-slate-400">{item.brandName}</p>}</div><span className="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700 dark:bg-brand-900/50 dark:text-brand-100">{categories[item.type] ?? 'Médicament'}</span></div><p className="mt-4 text-sm text-slate-600 dark:text-slate-300">Concentration : <strong className="text-slate-900 dark:text-white">{item.concentration}</strong></p><div className="mt-3 grid grid-cols-2 gap-3"><div className="rounded-xl bg-slate-50 p-3 dark:bg-black/15"><span className="text-xs text-slate-500 dark:text-slate-400">Dose</span><span className="mt-1 block font-semibold text-slate-900 dark:text-white">{item.dosage}</span></div><div className="rounded-xl bg-slate-50 p-3 dark:bg-black/15"><span className="text-xs text-slate-500 dark:text-slate-400">Débit</span><span className="mt-1 block font-semibold text-slate-900 dark:text-white">{item.rate}</span></div></div>{item.instructions && <p className="mt-4 text-sm leading-6 text-slate-600 dark:text-slate-300">{item.instructions}</p>}</article>)}</div>
        </AppLayout>
    );
}
