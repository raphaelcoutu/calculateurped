import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/components/app-layout';
import Pagination, { type PageLink } from '@/components/pagination';

type Bolus = { id: number; name: string; brandName: string; organization: string; status: string; version: number; publishedAt: string | null; author: string | null; publisher: string | null };

export default function BolusCatalog({ boluses, canCopy }: {
    boluses: { data: Bolus[]; links: PageLink[]; current_page: number; last_page: number; total: number };
    canCopy: boolean;
}) {
    return (
        <AppLayout>
            <Head title="Catalogue des bolus publiés" />
            <div className="mb-8">
                <p className="text-xs font-semibold uppercase tracking-[0.18em] text-brand-700 dark:text-brand-100">Répertoire partagé</p>
                <h1 className="mt-2 text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">Catalogue des bolus</h1>
                <p className="mt-2 max-w-2xl text-sm leading-6 text-slate-600 dark:text-slate-300">Consultez les recettes publiées par les organisations et copiez celles qui vous conviennent dans vos brouillons.</p>
            </div>
            <section className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#182524]">
                {boluses.data.length ? <ul className="divide-y divide-slate-100 dark:divide-white/10">{boluses.data.map(bolus => <li key={bolus.id} className="flex flex-wrap items-center justify-between gap-4 px-5 py-5 sm:px-6">
                    <div>
                        <Link href={`/admin/boluses/${bolus.id}`} className="font-semibold text-slate-900 hover:text-brand-700 dark:text-white dark:hover:text-brand-100">{bolus.name}</Link>
                        {bolus.brandName && <p className="mt-1 text-xs text-slate-500 dark:text-slate-400">{bolus.brandName}</p>}
                        <p className="mt-1 text-sm text-slate-500 dark:text-slate-400">{bolus.organization} · Version {bolus.version}{bolus.publisher ? ` · Publié par ${bolus.publisher}` : ''}</p>
                    </div>
                    <div className="flex items-center gap-3">
                        <Link href={`/admin/boluses/${bolus.id}`} className="text-sm font-semibold text-brand-700 hover:underline dark:text-brand-100">Détails</Link>
                        {canCopy && <button type="button" onClick={() => router.post(`/admin/boluses/${bolus.id}/copy`)} className="rounded-lg bg-brand-600 px-3 py-2 text-sm font-semibold text-white hover:bg-brand-700">Copier dans mes brouillons</button>}
                    </div>
                </li>)}</ul> : <div className="px-6 py-16 text-center"><h2 className="font-semibold text-slate-900 dark:text-white">Le catalogue est vide</h2><p className="mt-2 text-sm text-slate-500 dark:text-slate-400">Les recettes publiées par les organisations apparaîtront ici.</p></div>}
                <Pagination links={boluses.links} currentPage={boluses.current_page} lastPage={boluses.last_page} total={boluses.total} labels={{ singular: 'recette', plural: 'recettes' }} />
            </section>
        </AppLayout>
    );
}
