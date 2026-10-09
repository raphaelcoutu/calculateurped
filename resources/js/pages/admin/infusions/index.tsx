import { Head, Link, router } from '@inertiajs/react';
import { useRef } from 'react';
import type { FormEvent } from 'react';
import AppLayout from '@/components/app-layout';
import Pagination, { type PageLink } from '@/components/pagination';

type Infusion = { id: number; name: string; brandName: string; status: string; version: number; publishedAt: string | null; supersededAt: string | null; pendingDraftId: number | null; author: string | null; publisher: string | null; deletedAt: string | null; canRestore: boolean };

export default function InfusionsIndex({ infusions, search, showDeleted }: {
    infusions: { data: Infusion[]; links: PageLink[]; current_page: number; last_page: number; total: number };
    search: string;
    showDeleted: boolean;
}) {
    const searchInput = useRef<HTMLInputElement>(null);

    function submitSearch(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();

        const formData = new FormData(event.currentTarget);
        const searchValue = String(formData.get('search') ?? '');
        const selectionStart = searchInput.current?.selectionStart ?? null;
        const selectionEnd = searchInput.current?.selectionEnd ?? null;
        const data: { search?: string; deleted?: string } = {};

        if (searchValue !== '') {
            data.search = searchValue;
        }

        if (showDeleted) {
            data.deleted = '1';
        }

        router.get('/admin/infusions', data, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            onFinish: () => {
                const input = searchInput.current;

                if (input) {
                    input.focus();

                    if (selectionStart !== null && selectionEnd !== null) {
                        input.setSelectionRange(selectionStart, selectionEnd);
                    }
                }
            },
        });
    }

    return (
        <AppLayout>
            <Head title="Mes perfusions" />
            <div className="mb-8 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p className="text-xs font-semibold uppercase tracking-[0.18em] text-brand-700 dark:text-brand-100">Administration</p>
                    <h1 className="mt-2 text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">Mes perfusions</h1>
                    <p className="mt-2 text-sm text-slate-600 dark:text-slate-300">Créez des brouillons privés à votre organisation et publiez les recettes prêtes à partager.</p>
                </div>
                <div className="flex flex-wrap gap-2">
                    <Link href={showDeleted ? '/admin/infusions' : '/admin/infusions?deleted=1'} className="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-white/15 dark:bg-[#182524] dark:text-slate-200">{showDeleted ? 'Mes perfusions' : 'Éléments supprimés'}</Link>
                    {!showDeleted && <Link href="/admin/infusions/create" className="rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white hover:bg-brand-700">+ Créer une fiche de perfusion</Link>}
                </div>
            </div>
            <section className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#182524]">
                <form onSubmit={submitSearch} className="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-end dark:border-white/10">
                    <div className="flex-1">
                        <label htmlFor="infusion-search" className="mb-2 block text-sm font-medium text-slate-700 dark:text-slate-200">Rechercher une fiche de perfusion</label>
                        <input ref={searchInput} id="infusion-search" type="search" name="search" defaultValue={search} placeholder="Nom ou nom commercial" className="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 dark:border-white/15 dark:bg-[#101b1a] dark:text-white" />
                    </div>
                    <div className="flex items-center gap-2">
                        <button type="submit" className="rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white hover:bg-brand-700">Rechercher</button>
                        {search && <Link href={showDeleted ? '/admin/infusions?deleted=1' : '/admin/infusions'} className="rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-white/15 dark:text-slate-200 dark:hover:bg-white/5">Effacer</Link>}
                    </div>
                </form>
                {infusions.data.length ? <ul className="divide-y divide-slate-100 dark:divide-white/10">{infusions.data.map(infusion => <li key={infusion.id} className="flex flex-wrap items-center justify-between gap-4 px-5 py-5 sm:px-6">
                    <div>
                        <Link href={`/admin/infusions/${infusion.id}`} className="font-semibold text-slate-900 hover:text-brand-700 dark:text-white dark:hover:text-brand-100">{infusion.name}</Link>
                        <p className="mt-1 text-sm text-slate-500 dark:text-slate-400">Version {infusion.version} · {infusion.status === 'published' ? infusion.supersededAt ? `Remplacée le ${new Date(infusion.supersededAt).toLocaleDateString('fr-CA')}` : `Publiée ${infusion.publishedAt ? `le ${new Date(infusion.publishedAt).toLocaleDateString('fr-CA')}` : ''}` : 'Brouillon'}</p>
                        {infusion.status === 'published' && infusion.pendingDraftId !== null && <div className="mt-2 flex flex-wrap items-center gap-2">
                            <span className="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800 dark:bg-amber-950 dark:text-amber-200">Brouillon en cours</span>
                            <Link href={`/admin/infusions/${infusion.pendingDraftId}/edit`} className="text-xs font-semibold text-brand-700 hover:underline dark:text-brand-100">Continuer</Link>
                        </div>}
                        {showDeleted && !infusion.canRestore && <div className="mt-2 flex flex-wrap items-center gap-2">
                            <span title="Une version ultérieure existe, cette version ne peut plus être restaurée." className="rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-800 dark:bg-amber-950 dark:text-amber-200">Version remplacée</span>
                        </div>}
                    </div>
                    {showDeleted ? <>
                        <Link href={`/admin/infusions/${infusion.id}`} className="text-sm font-semibold text-brand-700 hover:underline dark:text-brand-100">Consulter →</Link>
                        {infusion.canRestore && <button type="button" onClick={() => router.post(`/admin/infusions/${infusion.id}/restore`)} className="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-white/15 dark:text-slate-200">Restaurer</button>}
                    </> : <Link href={`/admin/infusions/${infusion.id}`} className="text-sm font-semibold text-brand-700 hover:underline dark:text-brand-100">Consulter →</Link>}
                </li>)}</ul> : <div className="px-6 py-16 text-center">
                    <h2 className="font-semibold text-slate-900 dark:text-white">{showDeleted ? 'Aucune fiche de perfusion supprimé' : 'Aucune fiche de perfusion dans votre organisation'}</h2>
                    <p className="mt-2 text-sm text-slate-500 dark:text-slate-400">{showDeleted ? 'Les perfusions supprimées pourront être restaurés ici.' : 'Créez un brouillon pour commencer à gérer vos recettes.'}</p>
                </div>}
                <Pagination links={infusions.links} currentPage={infusions.current_page} lastPage={infusions.last_page} total={infusions.total} labels={{ singular: 'perfusion', plural: 'perfusions' }} />
            </section>
        </AppLayout>
    );
}
