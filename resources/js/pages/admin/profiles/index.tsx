import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/components/app-layout';
import Pagination from '@/components/pagination';
import type { PageLink } from '@/components/pagination';
import type { ProfileSummary } from './types';
import { buttonClass, statusLabel } from './types';

export default function ProfileIndex({ profiles, showDeleted }: {
    profiles: { data: ProfileSummary[]; links: PageLink[]; current_page: number; last_page: number; total: number }; showDeleted: boolean;
}) {
    return <AppLayout>
        <Head title="Profils d’ordonnance" />
        <div className="mb-6 flex flex-wrap items-end justify-between gap-4">
            <div><h1 className="text-3xl font-semibold">Profils d’ordonnance</h1><p className="mt-2 text-slate-600 dark:text-slate-300">Composez et publiez les profils de votre centre.</p></div>
            <div className="flex flex-wrap gap-2"><Link href={showDeleted ? '/admin/profiles' : '/admin/profiles?deleted=1'} className={buttonClass}>{showDeleted ? 'Profils actifs' : 'Profils supprimés'}</Link><Link href="/admin/profiles/create" className="rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white hover:bg-brand-700">Créer un profil</Link></div>
        </div>
        <section className="overflow-hidden rounded-2xl border border-slate-200 bg-white dark:border-white/10 dark:bg-[#182524]">
            {profiles.data.length ? <ul className="divide-y divide-slate-100 dark:divide-white/10">{profiles.data.map(profile => <li key={profile.id} className="flex flex-wrap items-center justify-between gap-4 p-5">
                <div><Link href={`/admin/profiles/${profile.id}`} className="font-semibold hover:text-brand-700 dark:hover:text-brand-100">{profile.name}</Link>
                    <p className="mt-1 text-sm text-slate-500 dark:text-slate-400">Version {profile.version} · {statusLabel(profile.status)}{profile.author ? ` · ${profile.author}` : ''}{profile.isDefault ? ' · Profil par défaut' : ''}</p>
                </div><Link href={`/admin/profiles/${profile.id}`} className="text-sm font-semibold text-brand-700 dark:text-brand-100">Consulter →</Link>
            </li>)}</ul> : <p className="p-10 text-center text-slate-500 dark:text-slate-400">{showDeleted ? 'Aucun profil supprimé.' : 'Aucun profil. Créez un premier brouillon.'}</p>}
            <Pagination links={profiles.links} currentPage={profiles.current_page} lastPage={profiles.last_page} total={profiles.total} labels={{ singular: 'profil', plural: 'profils' }} />
        </section>
    </AppLayout>;
}
