import { Link } from '@inertiajs/react';

export type UsingProfile = {
    id: number;
    name: string;
    version: number;
    status: 'draft' | 'published' | 'unpublished';
    supersededAt: string | null;
    url: string;
};

export default function RecipeProfileUsage({ profiles }: { profiles: UsingProfile[] }) {
    return <section className="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm dark:border-white/10 dark:bg-[#182524] sm:p-6">
        <h2 className="text-lg font-semibold text-slate-950 dark:text-white">Utilisé par</h2>
        {profiles.length ? <ul className="mt-4 divide-y divide-slate-100 dark:divide-white/10">
            {profiles.map(profile => <li key={profile.id} className="flex flex-wrap items-center justify-between gap-3 py-3 first:pt-0 last:pb-0">
                <div className="min-w-0 flex-1">
                    <Link href={profile.url} className="break-words font-medium text-brand-700 hover:underline dark:text-brand-100">{profile.name}</Link>
                    <p className="mt-1 text-xs text-slate-500 dark:text-slate-400">Version {profile.version}</p>
                </div>
                <span className={`rounded-full px-3 py-1 text-xs font-semibold ${profile.supersededAt ? 'bg-slate-100 text-slate-700 dark:bg-white/10 dark:text-slate-300' : profile.status === 'published' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-200' : 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-200'}`}>
                    {profile.supersededAt ? 'Remplacé' : profile.status === 'published' ? 'Publié' : profile.status === 'unpublished' ? 'Dépublié' : 'Brouillon'}
                </span>
            </li>)}
        </ul> : <p className="mt-4 text-sm text-slate-500 dark:text-slate-400">Aucun profil d’ordonnance n’utilise cette version.</p>}
    </section>;
}
