import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/components/app-layout';
import type { Recipe, Gap, Preparation } from './types';

type Version = { id: number; version: number; status: string; author: string | null; publisher: string | null; publishedAt: string | null; supersededAt: string | null; deletedAt: string | null };
type Infusion = Recipe & Version & {
    organization: string;
    activities: { id: number; action: string; date: string; author: string; changes: Record<string, unknown> | null }[];
    versions: Version[];
};
const actions: Record<string, string> = { created: 'Création du brouillon', updated: 'Modification du brouillon', copied: 'Copie depuis le catalogue', revision_created: 'Création d’une nouvelle version', published: 'Publication', deleted: 'Suppression', restored: 'Restauration' };
const labels: Record<string, string> = {
    name: 'Médicament', brand_name: 'Nom commercial', concentration: 'Concentration commerciale',
    debit_min: 'Dose minimale', debit_max: 'Dose maximale', debit_dose_unit: 'Unité de dose',
    debit_time_unit: 'Unité de temps', debit_min_limit: 'Débit minimal', debit_max_limit: 'Débit maximal',
    dose_unit: 'Unité de dose combinée', debit_limit_unit: 'Unité de débit par heure', dosage_precision: 'Décimales des doses', type: 'Catégorie', order: 'Ordre de la fiche',
    source_organization: 'Centre d’origine', source_version: 'Version d’origine', version: 'Version', published_at: 'Date de publication',
};
const buttonClass = 'rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold hover:bg-slate-100 dark:border-white/15 dark:hover:bg-white/10';

export default function InfusionShow({ infusion, canManage, canCopy, pendingDraftId, gaps }: {
    infusion: Infusion; canManage: boolean; canCopy: boolean; pendingDraftId: number | null; gaps: Gap[];
}) {
    const active = !infusion.deletedAt && !infusion.supersededAt;
    return <AppLayout>
        <Head title={infusion.name} />
        <Link href={canManage ? '/admin/infusions' : '/admin/infusions/catalog'} className="text-sm text-brand-700 dark:text-brand-100">← Retour aux perfusions</Link>
        <div className="mt-5 flex flex-wrap items-end justify-between gap-4">
            <div><h1 className="text-3xl font-semibold">{infusion.name}</h1><p className="mt-2 text-slate-600 dark:text-slate-300">{infusion.brand_name} · {infusion.organization} · Version {infusion.version}</p><p className="mt-2 text-sm">{infusion.deletedAt ? 'Supprimée' : infusion.supersededAt ? 'Remplacée' : infusion.status === 'published' ? 'Publiée' : 'Brouillon privé'}</p></div>
            <div className="flex flex-wrap gap-2">
                {canManage && active && infusion.status === 'draft' && <><Link href={`/admin/infusions/${infusion.id}/edit`} className={buttonClass}>Modifier</Link><button type="button" onClick={() => router.post(`/admin/infusions/${infusion.id}/publish`)} className={buttonClass}>Publier</button></>}
                {canManage && active && infusion.status === 'published' && (pendingDraftId ? <Link href={`/admin/infusions/${pendingDraftId}/edit`} className={buttonClass}>Continuer le brouillon</Link> : <button type="button" onClick={() => router.post(`/admin/infusions/${infusion.id}/revise`)} className={buttonClass}>Préparer une nouvelle version</button>)}
                {canCopy && <button type="button" onClick={() => router.post(`/admin/infusions/${infusion.id}/copy`)} className={buttonClass}>Copier dans mes brouillons</button>}
                {canManage && !infusion.deletedAt && <button type="button" onClick={() => { if (window.confirm('Supprimer cette version de la fiche et ses préparations ?')) router.delete(`/admin/infusions/${infusion.id}`); }} className={buttonClass}>Supprimer</button>}
            </div>
        </div>
        {gaps.length > 0 && <p role="status" className="mt-6 rounded-xl bg-amber-50 p-4 text-sm text-amber-900 dark:bg-amber-950 dark:text-amber-100">Plages non couvertes entre 0 et 100 kg : {gaps.map(gap => `[${gap.min}, ${gap.max}[ kg`).join(', ')}. Cet avertissement n’empêche pas la publication.</p>}
        <section className="mt-6 rounded-2xl border border-slate-200 bg-white p-5 dark:border-white/10 dark:bg-[#182524]">
            <h2 className="text-lg font-semibold">Médicament et doses</h2>
            <dl className="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <Detail label="Concentration commerciale" value={infusion.concentration} />
                <Detail label="Dose" value={`${infusion.debit_min}${infusion.debit_max > 0 ? ` à ${infusion.debit_max}` : ''} ${infusion.dose_unit}`} />
                <Detail label="Intervalle de débit" value={formatDebitInterval(infusion.debit_min_limit, infusion.debit_max_limit, `${infusion.debit_limit_unit}/h`)} />
                <Detail label="Décimales des doses" value={String(infusion.dosage_precision)} />
                <Detail label="Catégorie" value={infusion.type === 1 ? 'Sédation' : infusion.type === 2 ? 'Cardiovasculaire' : 'Autres médicaments'} />
                <Detail label="Ordre de la fiche" value={String(infusion.order)} />
            </dl>
        </section>
        <section className="mt-6 rounded-2xl border border-slate-200 bg-white p-5 dark:border-white/10 dark:bg-[#182524]">
            <h2 id="preparations-heading" className="text-lg font-semibold">Préparations dans l’ordre d’évaluation</h2>
            <p className="mt-2 text-sm text-slate-500 dark:text-slate-400">La première plage compatible est retenue. Le poids minimal est inclus et le poids maximal est exclu.</p>
            <div className="mt-4 overflow-x-auto rounded-xl border border-slate-200 dark:border-white/10">
                <table aria-labelledby="preparations-heading" className="w-full min-w-[640px] text-left text-sm">
                    <thead className="bg-slate-50 text-slate-600 dark:bg-black/15 dark:text-slate-300">
                        <tr>
                            <th scope="col" className="whitespace-nowrap px-4 py-3 font-semibold">Poids min (kg)</th>
                            <th scope="col" className="whitespace-nowrap px-4 py-3 font-semibold">Poids max (kg)</th>
                            <th scope="col" className="px-4 py-3 font-semibold">Concentration</th>
                            <th scope="col" className="px-4 py-3 font-semibold">Volume</th>
                            <th scope="col" className="w-full px-4 py-3 font-semibold">Instructions</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-slate-200 dark:divide-white/10">
                        {infusion.preparations.map((preparation, index) => <tr key={index} className="align-top">
                            <td className="px-4 py-4 tabular-nums">{preparation.min_weight}</td>
                            <td className="px-4 py-4 tabular-nums">{preparation.max_weight ?? 'Sans limite'}</td>
                            <td className="whitespace-nowrap px-4 py-4 tabular-nums">{preparation.concentration} {preparation.concentration_unit}/mL</td>
                            <td className="whitespace-nowrap px-4 py-4 tabular-nums">{preparation.total_volume} mL</td>
                            <td className="min-w-64 whitespace-pre-wrap break-words px-4 py-4 text-slate-600 dark:text-slate-300">{preparation.instructions}</td>
                        </tr>)}
                    </tbody>
                </table>
            </div>
        </section>
        <div className="mt-6 grid gap-6 lg:grid-cols-2">
            <section className="rounded-2xl border border-slate-200 bg-white p-5 dark:border-white/10 dark:bg-[#182524]">
                <h2 className="text-lg font-semibold">Historique des changements</h2>
                <ol className="mt-4 space-y-3">{infusion.activities.map(activity => <li key={activity.id}><details className="rounded-xl bg-slate-50 p-4 dark:bg-black/15"><summary className="cursor-pointer text-sm font-semibold">{actions[activity.action] ?? activity.action}<span className="mt-1 block font-normal text-slate-500 dark:text-slate-400">{activity.author} · {new Date(activity.date).toLocaleString('fr-CA')}</span></summary>
                    <dl className="mt-4 grid gap-2 text-sm">{Object.entries(activity.changes ?? {}).filter(([key]) => labels[key]).map(([key, value]) => <div key={key} className="flex flex-wrap justify-between gap-2"><dt className="text-slate-500 dark:text-slate-400">{labels[key]}</dt><dd>{String(value ?? '')}</dd></div>)}</dl>
                    {Array.isArray(activity.changes?.preparations) && <ol className="mt-4 space-y-3">{(activity.changes.preparations as Preparation[]).map((preparation, index) => <li key={index}><PreparationSummary preparation={preparation} index={index} /></li>)}</ol>}
                </details></li>)}</ol>
            </section>
            <section className="rounded-2xl border border-slate-200 bg-white p-5 dark:border-white/10 dark:bg-[#182524]">
                <h2 className="text-lg font-semibold">Versions de la fiche</h2>
                <ol className="mt-4 space-y-4">{infusion.versions.map(version => <li key={version.id}>
                    {canManage || !version.deletedAt ? <Link href={`/admin/infusions/${version.id}`} className="font-semibold text-brand-700 dark:text-brand-100">Version {version.version}</Link> : <span>Version {version.version}</span>}
                    <p className="mt-1 text-sm text-slate-500 dark:text-slate-400">{version.status === 'published' ? 'Publiée' : 'Brouillon'}{version.author ? ` · Créée par ${version.author}` : ''}{version.publisher ? ` · Publiée par ${version.publisher}` : ''}{version.publishedAt ? ` le ${new Date(version.publishedAt).toLocaleString('fr-CA')}` : ''}{version.supersededAt ? ' · Remplacée' : ''}{version.deletedAt ? ' · Supprimée' : ''}</p>
                </li>)}</ol>
            </section>
        </div>
    </AppLayout>;
}

function Detail({ label, value }: { label: string; value: string }) {
    return <div className="rounded-xl bg-slate-50 p-4 dark:bg-black/15"><dt className="text-xs text-slate-500 dark:text-slate-400">{label}</dt><dd className="mt-1 font-semibold">{value}</dd></div>;
}

function formatDebitInterval(min: number, max: number, unit: string): string {
    if (min === 0 && max === 0) return '-';
    if (min === 0) return `≤ ${max} ${unit}`;
    if (max === 0) return `≥ ${min} ${unit}`;
    return `${min} à ${max} ${unit}`;
}

function PreparationSummary({ preparation, index }: { preparation: Preparation; index: number }) {
    return <><p className="font-semibold">{index + 1}. [{preparation.min_weight}, {preparation.max_weight ?? '∞'}[ kg</p><p className="mt-2 text-sm">{preparation.concentration} {preparation.concentration_unit}/mL · {preparation.total_volume} mL</p><p className="mt-2 whitespace-pre-wrap text-sm text-slate-600 dark:text-slate-300">{preparation.instructions}</p></>;
}
