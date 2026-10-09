import { Head, Link, router, usePage } from '@inertiajs/react';
import { useState } from 'react';
import type { FormEvent } from 'react';
import AppLayout from '@/components/app-layout';
import { FormField, inputClass, PrimaryButton } from '@/components/form-field';
import type { ProfileSummary, Section } from './types';
import { buttonClass, panelClass, statusLabel } from './types';

type NamedSection = { name: string; items: { type: 'bolus' | 'infusion'; recipe_id: number; name: string; status: string | null; deletedAt: string | null }[] };
type Profile = ProfileSummary & {
    deletedAt: string | null; supersededAt: string | null; publisher: string | null;
    sections: NamedSection[];
    versions: { id: number; version: number; status: string; author: string | null; publisher: string | null; publishedAt: string | null; deletedAt: string | null }[];
    activities: { id: number; action: string; author: string; date: string; changes: { name: string; status: string; version: number; sections: Section[] } }[];
};
const actionLabels: Record<string, string> = { created: 'Création', updated: 'Modification', revision_created: 'Nouvelle version en brouillon', published: 'Publication', unpublished: 'Dépublication', superseded: 'Remplacement par une nouvelle version', deleted: 'Suppression', restored: 'Restauration', default_selected: 'Choix comme profil par défaut', default_removed: 'Retrait du choix par défaut' };

export default function ProfileShow({ profile, pendingDraftId }: { profile: Profile; pendingDraftId: number | null }) {
    const { errors } = usePage<{ errors: Record<string, string> }>().props;
    const [busy, setBusy] = useState(false);
    const [preview, setPreview] = useState({ weight: '10', name: 'Patient fictif', patient_id: 'TEST' });
    const active = !profile.deletedAt && !profile.supersededAt;
    function post(action: string) {
        setBusy(true);
        router.post(`/admin/profiles/${profile.id}/${action}`, {}, { onFinish: () => setBusy(false) });
    }
    function openPreview(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();
        window.open(`/admin/profiles/${profile.id}/preview?${new URLSearchParams(preview)}`, '_blank', 'noopener,noreferrer');
    }
    return <AppLayout>
        <Head title={profile.name} />
        <Link href="/admin/profiles" className="text-sm text-brand-700 dark:text-brand-100">← Retour aux profils</Link>
        <div className="mt-5 flex flex-wrap items-end justify-between gap-4">
            <div><h1 className="text-3xl font-semibold">{profile.name}</h1><p className="mt-2 text-sm text-slate-600 dark:text-slate-300">Version {profile.version} · {profile.deletedAt ? 'Supprimé' : profile.supersededAt ? 'Remplacé' : statusLabel(profile.status)}{profile.isDefault ? ' · Profil par défaut' : ''}</p></div>
            <div className="flex flex-wrap gap-2">
                {active && profile.status !== 'published' && <><Link href={`/admin/profiles/${profile.id}/edit`} className={buttonClass}>Modifier</Link><button type="button" disabled={busy} className={buttonClass} onClick={() => post('publish')}>Publier</button></>}
                {active && profile.status === 'published' && <>
                    {pendingDraftId ? <Link href={`/admin/profiles/${pendingDraftId}/edit`} className={buttonClass}>Continuer le brouillon</Link> : <button type="button" disabled={busy} className={buttonClass} onClick={() => post('revise')}>Préparer une nouvelle version</button>}
                    <button type="button" disabled={busy} className={buttonClass} onClick={() => post('unpublish')}>Dépublier</button>
                    {!profile.isDefault && <button type="button" disabled={busy} className={buttonClass} onClick={() => post('default')}>Définir par défaut</button>}
                </>}
                {profile.deletedAt ? <button type="button" disabled={busy} className={buttonClass} onClick={() => post('restore')}>Restaurer</button> : <button type="button" disabled={busy} className={buttonClass} onClick={() => {
                    if (window.confirm('Supprimer ce profil ? Il sera dépublié et son historique sera conservé.')) {
                        setBusy(true);
                        router.delete(`/admin/profiles/${profile.id}`, { onFinish: () => setBusy(false) });
                    }
                }}>Supprimer</button>}
            </div>
        </div>
        {Object.entries(errors).map(([key, error]) => <p key={key} role="alert" className="mt-4 rounded-xl bg-rose-50 p-4 text-sm text-rose-800 dark:bg-rose-950 dark:text-rose-200">{error}</p>)}
        <div className="mt-6 flex flex-col gap-5">
            {profile.sections.map((section, index) => <section key={index} className={panelClass}>
                <h2 className="font-semibold">{index + 1}. {section.name}</h2>
                <ol className="mt-3 flex flex-col gap-2">{section.items.map((item, itemIndex) => <li key={itemIndex} className="flex flex-wrap justify-between gap-2 rounded-lg bg-slate-50 p-3 text-sm dark:bg-black/15">
                    <Link href={`/admin/${item.type === 'bolus' ? 'boluses' : 'infusions'}/${item.recipe_id}`} className="text-brand-700 dark:text-brand-100">{itemIndex + 1}. {item.name}</Link>
                    <div className="flex items-center gap-3">
                        <span>{item.type === 'bolus' ? 'Bolus' : 'Perfusion'} · {item.deletedAt ? 'Supprimée' : item.status === 'published' ? 'Publiée' : 'Brouillon'}</span>
                        <Link href={`/admin/${item.type === 'bolus' ? 'boluses' : 'infusions'}/${item.recipe_id}`} target="_blank" rel="noopener noreferrer" aria-label={`Voir les paramètres de ${item.name}, nouvel onglet`} title="Voir les paramètres dans un nouvel onglet" className="inline-flex size-9 shrink-0 items-center justify-center rounded-lg border border-slate-200 text-brand-700 hover:bg-slate-100 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand-600 dark:border-white/15 dark:text-brand-100 dark:hover:bg-white/10">
                            <svg aria-hidden="true" viewBox="0 0 24 24" className="size-5" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round"><circle cx="10.5" cy="10.5" r="6.5" /><path d="m16 16 4 4" /></svg>
                        </Link>
                    </div>
                </li>)}</ol>
                {!section.items.length && <p className="mt-3 text-sm text-slate-500 dark:text-slate-400">Section vide.</p>}
            </section>)}
            {!profile.deletedAt && <section className={panelClass}>
                <h2 className="font-semibold">Prévisualisation PDF</h2>
                <p className="mt-2 text-sm text-slate-500 dark:text-slate-400">Utilisez des renseignements fictifs. Le PDF s’ouvre dans un nouvel onglet et conserve l’ordre du profil, y compris les répétitions.</p>
                <form onSubmit={openPreview} className="mt-4 grid items-end gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <FormField id="preview-weight" label="Poids fictif (kg)"><input id="preview-weight" type="number" min="0.01" max="1000" step="any" required className={inputClass} value={preview.weight} onChange={event => setPreview({ ...preview, weight: event.target.value })} /></FormField>
                    <FormField id="preview-name" label="Nom fictif"><input id="preview-name" className={inputClass} maxLength={191} value={preview.name} onChange={event => setPreview({ ...preview, name: event.target.value })} /></FormField>
                    <FormField id="preview-id" label="Dossier fictif"><input id="preview-id" className={inputClass} maxLength={191} value={preview.patient_id} onChange={event => setPreview({ ...preview, patient_id: event.target.value })} /></FormField>
                    <PrimaryButton>Prévisualiser le PDF</PrimaryButton>
                </form>
            </section>}
            <div className="grid gap-5 lg:grid-cols-2">
                <section className={panelClass}><h2 className="font-semibold">Historique des changements</h2>
                    <ol className="mt-4 flex flex-col gap-3">{profile.activities.map(activity => <li key={activity.id}><details className="rounded-xl bg-slate-50 p-4 dark:bg-black/15">
                        <summary className="cursor-pointer text-sm font-semibold">{actionLabels[activity.action] ?? activity.action}<span className="mt-1 block font-normal text-slate-500 dark:text-slate-400">{activity.author} · {new Date(activity.date).toLocaleString('fr-CA')}</span></summary>
                        <p className="mt-3 text-sm">{activity.changes.name} · Version {activity.changes.version} · {statusLabel(activity.changes.status)}</p>
                        <ol className="mt-3 flex flex-col gap-2 text-sm">{activity.changes.sections.map((section, index) => <li key={index}>{index + 1}. {section.name}<ol className="mt-1 flex flex-col gap-1 pl-4">{section.items.map((item, position) => <li key={position}><Link className="text-brand-700 dark:text-brand-100" href={`/admin/${item.type === 'bolus' ? 'boluses' : 'infusions'}/${item.recipe_id}`}>{item.type === 'bolus' ? 'Bolus' : 'Perfusion'} #{item.recipe_id}</Link></li>)}</ol></li>)}</ol>
                    </details></li>)}</ol>
                </section>
                <section className={panelClass}><h2 className="font-semibold">Versions du profil</h2>
                    <ol className="mt-4 flex flex-col gap-4">{profile.versions.map(version => <li key={version.id}>
                        <Link href={`/admin/profiles/${version.id}`} className="font-semibold text-brand-700 dark:text-brand-100">Version {version.version}</Link>
                        <p className="mt-1 text-sm text-slate-500 dark:text-slate-400">{statusLabel(version.status)}{version.author ? ` · Créée par ${version.author}` : ''}{version.deletedAt ? ' · Supprimée' : ''}</p>
                        {version.publishedAt && <p className="mt-1 text-sm text-slate-500 dark:text-slate-400">Publiée {version.publisher ? `par ${version.publisher} ` : ''}le {new Date(version.publishedAt).toLocaleString('fr-CA')}</p>}
                    </li>)}</ol>
                </section>
            </div>
        </div>
    </AppLayout>;
}
