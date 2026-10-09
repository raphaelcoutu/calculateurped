import { Head, Link, router, usePage } from '@inertiajs/react';
import AppLayout from '@/components/app-layout';

type Bolus = {
    id: number;
    name: string;
    brandName: string;
    organization: string;
    status: 'draft' | 'published';
    version: number;
    publishedAt: string | null;
    supersededAt: string | null;
    deletedAt: string | null;
    author: string | null;
    publisher: string | null;
    unit: string;
    commercial_concentration: number;
    dosage: number;
    minimum_dose: number;
    maximum_dose: number;
    dose_precision: number;
    volume_precision: number;
    type: number;
    min_weight: number;
    max_weight: number;
    instructions: string;
    asterisk: boolean;
    activities: { id: number; action: string; date: string; author: string; changes: Record<string, unknown> | null }[];
    versions: { id: number; version: number; status: string; author: string | null; publisher: string | null; publishedAt: string | null; supersededAt: string | null; deletedAt: string | null }[];
};

const activityLabels: Record<string, string> = {
    created: 'Brouillon créé',
    updated: 'Brouillon modifié',
    revision_created: 'Nouvelle version préparée',
    copied: 'Copie créée',
    published: 'Bolus publié',
    deleted: 'Bolus supprimé',
    restored: 'Bolus restauré',
};

const changeLabels: Record<string, string> = {
    name: 'Nom',
    brand_name: 'Nom commercial',
    unit: 'Unité',
    commercial_concentration: 'Concentration',
    dosage: 'Dose par kg',
    minimum_dose: 'Dose minimale',
    maximum_dose: 'Dose maximale',
    dose_precision: 'Précision de dose',
    volume_precision: 'Précision de volume',
    type: 'Catégorie',
    min_weight: 'Poids minimal',
    max_weight: 'Poids maximal',
    instructions: 'Instructions',
    asterisk: 'Astérisque',
    source_organization: 'Organisation source',
};

export default function BolusShow({ bolus, canManage, pendingDraftId, canCopy }: {
    bolus: Bolus;
    canManage: boolean;
    pendingDraftId: number | null;
    canCopy: boolean;
}) {
    const { errors } = usePage<{ errors: Record<string, string> }>().props;
    const isDeleted = bolus.deletedAt !== null;

    return (
        <AppLayout>
            <Head title={bolus.name} />
            {isDeleted && <div role="alert" className="mb-6 rounded-xl border border-amber-300 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-900 dark:border-amber-800 dark:bg-amber-950/50 dark:text-amber-100">
                Avertissement : vous consultez une version supprimée.
            </div>}
            <div className="mb-8 flex flex-wrap items-start justify-between gap-4">
                <div>
                    <Link href={canManage ? isDeleted ? '/admin/boluses?deleted=1' : '/admin/boluses' : '/admin/boluses/catalog'} className="text-sm font-medium text-brand-700 hover:underline dark:text-brand-100">← Retour aux bolus</Link>
                    <p className="mt-5 text-xs font-semibold uppercase tracking-[0.18em] text-brand-700 dark:text-brand-100">{bolus.organization} · Version {bolus.version}</p>
                    <h1 className="mt-2 text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">{bolus.name}{bolus.asterisk && <sup className="ml-1 text-rose-600">*</sup>}</h1>
                    {bolus.brandName && <p className="mt-2 text-sm text-slate-600 dark:text-slate-300">{bolus.brandName}</p>}
                    <span className={`mt-3 inline-flex rounded-full px-3 py-1 text-xs font-semibold ${bolus.status === 'published' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-200'}`}>
                        {bolus.status === 'published' ? 'Publié' : 'Brouillon'}
                    </span>
                </div>
                <div className="flex flex-wrap gap-2">
                    {canManage && !isDeleted && bolus.status === 'draft' && <>
                        <Link href={`/admin/boluses/${bolus.id}/edit`} className="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-white/15 dark:bg-[#182524] dark:text-slate-200">Modifier le brouillon</Link>
                        <button type="button" onClick={() => router.post(`/admin/boluses/${bolus.id}/publish`)} className="rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white hover:bg-brand-700">Publier</button>
                    </>}
                    {canManage && !isDeleted && bolus.status === 'published' && bolus.supersededAt === null && pendingDraftId === null && <button type="button" onClick={() => router.post(`/admin/boluses/${bolus.id}/revise`)} className="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-white/15 dark:bg-white/5 dark:text-slate-200">Préparer une nouvelle version</button>}
                    {canManage && !isDeleted && bolus.status === 'published' && pendingDraftId !== null && <Link href={`/admin/boluses/${pendingDraftId}/edit`} className="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-amber-900 hover:bg-amber-100 dark:border-amber-900 dark:bg-amber-950 dark:text-amber-100">Continuer le brouillon en cours</Link>}
                    {canManage && !isDeleted && !bolus.supersededAt && bolus.status === 'published' && <button type="button" onClick={() => router.post(`/admin/boluses/${bolus.id}/unpublish`)} className="rounded-xl border border-slate-200 px-4 py-3 text-sm font-semibold dark:border-white/15">Dépublier</button>}
                    {canCopy && !isDeleted && <button type="button" onClick={() => router.post(`/admin/boluses/${bolus.id}/copy`)} className="rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white hover:bg-brand-700">Copier dans mes brouillons</button>}
                    {canManage && !isDeleted && <button type="button" onClick={() => {
                        if (window.confirm('Supprimer ce bolus? Il pourra être restauré par votre organisation.')) {
                            router.delete(`/admin/boluses/${bolus.id}`);
                        }
                    }} className="rounded-xl border border-rose-200 px-4 py-3 text-sm font-semibold text-rose-700 hover:bg-rose-50 dark:border-rose-900 dark:text-rose-200">Supprimer</button>}
                </div>
            </div>

            {Object.entries(errors).map(([key, error]) => <p key={key} role="alert" className="mt-4 rounded-xl bg-rose-50 p-4 text-sm text-rose-800 dark:bg-rose-950 dark:text-rose-200">{error}</p>)}
            <div className="grid gap-6 lg:grid-cols-[1.25fr_0.75fr]">
                <section className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#182524] sm:p-6">
                    <h2 className="text-lg font-semibold text-slate-950 dark:text-white">Paramètres du bolus</h2>
                    <dl className="mt-5 grid gap-4 sm:grid-cols-2">
                        <Detail label="Dose" value={`${bolus.dosage} ${bolus.unit}/kg`} />
                        <Detail label="Concentration commerciale" value={`${bolus.commercial_concentration} ${bolus.unit}/mL`} />
                        <Detail label="Dose minimale" value={`${bolus.minimum_dose} ${bolus.unit}`} />
                        <Detail label="Dose maximale" value={`${bolus.maximum_dose} ${bolus.unit}`} />
                        <Detail label="Poids minimal" value={`${bolus.min_weight} kg`} />
                        <Detail label="Poids maximal" value={`${bolus.max_weight} kg`} />
                        <Detail label="Précision de dose" value={String(bolus.dose_precision)} />
                        <Detail label="Précision de volume" value={String(bolus.volume_precision)} />
                        <Detail label="Catégorie" value={['', 'IV direct', 'Intubation séquence rapide', 'Autres médicaments'][bolus.type] ?? 'Médicament'} />
                    </dl>
                    {bolus.instructions && <div className="mt-5 rounded-xl bg-slate-50 p-4 dark:bg-black/15"><h3 className="text-sm font-semibold text-slate-900 dark:text-white">Instructions</h3><p className="mt-2 whitespace-pre-wrap text-sm leading-6 text-slate-600 dark:text-slate-300">{bolus.instructions}</p></div>}
                </section>

                <section className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#182524] sm:p-6">
                    <h2 className="text-lg font-semibold text-slate-950 dark:text-white">Historique</h2>
                    <ol className="relative ml-1.5 mt-5 space-y-5 border-l border-brand-200 pl-5 dark:border-brand-800">
                        {bolus.activities.map(activity => {
                            const changes = visibleActivityChanges(activity.changes);
                            const detailsId = `activity-${activity.id}-details`;

                            return <li key={activity.id} tabIndex={0} aria-describedby={detailsId} className="group relative rounded-lg outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-[#182524]">
                                <span aria-hidden="true" className="absolute -left-[1.62rem] top-1.5 size-3 rounded-full border-2 border-white bg-brand-600 ring-2 ring-brand-200 dark:border-[#182524] dark:ring-brand-800" />
                                <div className="cursor-help py-0.5">
                                    <p className="text-sm font-semibold text-slate-900 dark:text-white">{activityLabels[activity.action] ?? activity.action}</p>
                                    <p className="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                        <time dateTime={activity.date}>{new Date(activity.date).toLocaleDateString('fr-CA')}</time>
                                        <span aria-hidden="true"> · </span>{activity.author}
                                    </p>
                                </div>
                                <div id={detailsId} role="tooltip" className="invisible absolute left-0 top-full z-30 mt-2 max-h-64 w-72 max-w-[calc(100vw-3rem)] overflow-y-auto rounded-xl border border-slate-200 bg-white text-left opacity-0 shadow-xl transition duration-150 group-hover:visible group-hover:opacity-100 group-focus-within:visible group-focus-within:opacity-100 dark:border-white/15 dark:bg-[#101b1a]">
                                    <div className="border-b border-slate-100 px-4 py-3 dark:border-white/10">
                                        <p className="text-sm font-semibold text-slate-900 dark:text-white">{activityLabels[activity.action] ?? activity.action}</p>
                                        <p className="mt-1 text-xs text-slate-500 dark:text-slate-400">{activity.author} · {new Date(activity.date).toLocaleString('fr-CA')}</p>
                                    </div>
                                    {changes.length ? <dl className="grid gap-2 px-4 py-3">
                                        {changes.map(change => <div key={change.label} className="grid grid-cols-[auto_1fr] gap-3 text-xs">
                                            <dt className="font-medium text-slate-500 dark:text-slate-400">{change.label}</dt>
                                            <dd className="break-words text-right text-slate-800 dark:text-slate-100">{change.value}</dd>
                                        </div>)}
                                    </dl> : <p className="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">Aucun autre détail pour cette action.</p>}
                                </div>
                            </li>;
                        })}
                    </ol>
                    <p className="mt-5 text-xs text-slate-500 dark:text-slate-400">Créé par {bolus.author ?? 'Système'}{bolus.publisher ? ` · Publié par ${bolus.publisher}` : ''}{bolus.publishedAt ? ` le ${new Date(bolus.publishedAt).toLocaleDateString('fr-CA')}` : ''}</p>
                </section>
            </div>
            <section className="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#182524] sm:p-6">
                <h2 className="text-lg font-semibold text-slate-950 dark:text-white">Versions de cette recette</h2>
                <ol className="mt-4 divide-y divide-slate-100 dark:divide-white/10">{bolus.versions.map(version => <li key={version.id} className="flex flex-wrap items-center justify-between gap-3 py-3 first:pt-0 last:pb-0">
                    <div>
                        {(canManage || (version.status === 'published' && version.deletedAt === null)) ? <Link href={`/admin/boluses/${version.id}`} className="font-medium text-brand-700 hover:underline dark:text-brand-100">Version {version.version}</Link> : <span className="font-medium text-slate-900 dark:text-white">Version {version.version}</span>}
                        <p className="mt-1 text-xs text-slate-500 dark:text-slate-400">{version.status === 'published' ? 'Publiée' : 'Brouillon'}{version.author ? ` · Créée par ${version.author}` : ''}{version.publisher ? ` · Publiée par ${version.publisher}` : ''}{version.publishedAt ? ` le ${new Date(version.publishedAt).toLocaleDateString('fr-CA')}` : ''}{version.supersededAt ? ' · Remplacée' : ''}{version.deletedAt ? ' · Supprimée' : ''}</p>
                    </div>
                </li>)}</ol>
            </section>
        </AppLayout>
    );
}

function Detail({ label, value }: { label: string; value: string }) {
    return <div className="rounded-xl bg-slate-50 p-4 dark:bg-black/15"><dt className="text-xs text-slate-500 dark:text-slate-400">{label}</dt><dd className="mt-1 font-semibold text-slate-900 dark:text-white">{value}</dd></div>;
}

function visibleActivityChanges(changes: Record<string, unknown> | null): { label: string; value: string }[] {
    return Object.entries(changes ?? {})
        .filter(([key]) => !['source_version', 'version', 'published_at'].includes(key))
        .map(([key, value]) => ({
            label: changeLabels[key] ?? key,
            value: String(value),
        }));
}
