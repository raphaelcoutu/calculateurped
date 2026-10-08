import { Head, Link, router } from '@inertiajs/react';
import AppLayout from '@/components/app-layout';

type Bolus = {
    id: number;
    name: string;
    brandName: string;
    organization: string;
    status: 'draft' | 'published';
    version: number;
    publishedAt: string | null;
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

export default function BolusShow({ bolus, canManage, canCopy }: {
    bolus: Bolus;
    canManage: boolean;
    canCopy: boolean;
}) {
    return (
        <AppLayout>
            <Head title={bolus.name} />
            <div className="mb-8 flex flex-wrap items-start justify-between gap-4">
                <div>
                    <Link href={canManage ? '/admin/boluses' : '/admin/boluses/catalog'} className="text-sm font-medium text-brand-700 hover:underline dark:text-brand-100">← Retour aux bolus</Link>
                    <p className="mt-5 text-xs font-semibold uppercase tracking-[0.18em] text-brand-700 dark:text-brand-100">{bolus.organization} · Version {bolus.version}</p>
                    <h1 className="mt-2 text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">{bolus.name}{bolus.asterisk && <sup className="ml-1 text-rose-600">*</sup>}</h1>
                    {bolus.brandName && <p className="mt-2 text-sm text-slate-600 dark:text-slate-300">{bolus.brandName}</p>}
                    <span className={`mt-3 inline-flex rounded-full px-3 py-1 text-xs font-semibold ${bolus.status === 'published' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-200'}`}>
                        {bolus.status === 'published' ? 'Publié' : 'Brouillon'}
                    </span>
                </div>
                <div className="flex flex-wrap gap-2">
                    {canManage && bolus.status === 'draft' && <>
                        <Link href={`/admin/boluses/${bolus.id}/edit`} className="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-white/15 dark:bg-[#182524] dark:text-slate-200">Modifier le brouillon</Link>
                        <button type="button" onClick={() => router.post(`/admin/boluses/${bolus.id}/publish`)} className="rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white hover:bg-brand-700">Publier sans approbation</button>
                    </>}
                    {canManage && bolus.status === 'published' && <button type="button" onClick={() => router.post(`/admin/boluses/${bolus.id}/revise`)} className="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 dark:border-white/15 dark:bg-[#182524] dark:text-slate-200">Préparer une nouvelle version</button>}
                    {canCopy && <button type="button" onClick={() => router.post(`/admin/boluses/${bolus.id}/copy`)} className="rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white hover:bg-brand-700">Copier dans mes brouillons</button>}
                    {canManage && <button type="button" onClick={() => {
                        if (window.confirm('Supprimer ce bolus? Il pourra être restauré par votre organisation.')) {
                            router.delete(`/admin/boluses/${bolus.id}`);
                        }
                    }} className="rounded-xl border border-rose-200 px-4 py-3 text-sm font-semibold text-rose-700 hover:bg-rose-50 dark:border-rose-900 dark:text-rose-200">Supprimer</button>}
                </div>
            </div>

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
                    <ol className="mt-5 space-y-4">
                        {bolus.activities.map(activity => <li key={activity.id} className="border-l-2 border-brand-200 pl-4 dark:border-brand-800">
                            <p className="text-sm font-semibold text-slate-900 dark:text-white">{activityLabels[activity.action] ?? activity.action}</p>
                            <p className="mt-1 text-xs text-slate-500 dark:text-slate-400">{activity.author} · {new Date(activity.date).toLocaleString('fr-CA')}</p>
                            {activity.changes && <p className="mt-2 text-xs leading-5 text-slate-500 dark:text-slate-400">{Object.entries(activity.changes).filter(([key]) => !['source_version', 'version', 'published_at'].includes(key)).map(([key, value]) => `${changeLabels[key] ?? key} : ${String(value)}`).join(' · ')}</p>}
                        </li>)}
                    </ol>
                    <p className="mt-5 text-xs text-slate-500 dark:text-slate-400">Créé par {bolus.author ?? 'Système'}{bolus.publisher ? ` · Publié par ${bolus.publisher}` : ''}{bolus.publishedAt ? ` le ${new Date(bolus.publishedAt).toLocaleDateString('fr-CA')}` : ''}</p>
                </section>
            </div>
            <section className="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#182524] sm:p-6">
                <h2 className="text-lg font-semibold text-slate-950 dark:text-white">Versions de cette recette</h2>
                <ol className="mt-4 divide-y divide-slate-100 dark:divide-white/10">{bolus.versions.map(version => <li key={version.id} className="flex flex-wrap items-center justify-between gap-3 py-3 first:pt-0 last:pb-0">
                    <div>
                        {(version.status === 'published' || canManage) ? <Link href={`/admin/boluses/${version.id}`} className="font-medium text-brand-700 hover:underline dark:text-brand-100">Version {version.version}</Link> : <span className="font-medium text-slate-900 dark:text-white">Version {version.version}</span>}
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
