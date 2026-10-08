import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/components/app-layout';

type Organization = { id: number; name: string; users_count: number };
type PageLink = { url: string | null; label: string; active: boolean };

export default function OrganizationsIndex({ organizations }: {
    organizations: { data: Organization[]; links: PageLink[]; current_page: number; last_page: number; total: number };
}) {
    return (
        <AppLayout>
            <Head title="Organisations hospitalières" />
            <div className="mb-8 flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p className="text-xs font-semibold uppercase tracking-[0.18em] text-brand-700 dark:text-brand-100">Administration</p>
                    <h1 className="mt-2 text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">Organisations hospitalières</h1>
                    <p className="mt-2 text-sm text-slate-600 dark:text-slate-300">Gérez les organisations et leurs accès administrateurs.</p>
                </div>
                <Link href="/admin/organizations/create" className="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
                    <span className="text-lg leading-none">+</span> Créer une organisation
                </Link>
            </div>
            <section className="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-white/10 dark:bg-[#182524]">
                {organizations.data.length ? (
                    <ul className="divide-y divide-slate-100 dark:divide-white/10">
                        {organizations.data.map(organization => (
                            <li key={organization.id}>
                                <Link href={`/admin/organizations/${organization.id}`} className="flex flex-wrap items-center justify-between gap-4 px-5 py-5 no-underline transition hover:bg-slate-50 dark:hover:bg-white/5 sm:px-6">
                                    <span className="flex items-center gap-4">
                                        <span className="grid size-11 place-items-center rounded-xl bg-brand-50 text-brand-700 dark:bg-brand-900/50 dark:text-brand-100">✚</span>
                                        <span>
                                            <span className="block font-semibold text-slate-900 dark:text-white">{organization.name}</span>
                                            <span className="mt-1 block text-sm text-slate-500 dark:text-slate-400">{organization.users_count} administrateur{organization.users_count === 1 ? '' : 's'}</span>
                                        </span>
                                    </span>
                                    <span className="text-sm font-medium text-brand-700 dark:text-brand-100">Gérer <span aria-hidden="true">→</span></span>
                                </Link>
                            </li>
                        ))}
                    </ul>
                ) : (
                    <div className="px-6 py-16 text-center">
                        <div className="mx-auto grid size-14 place-items-center rounded-2xl bg-slate-100 text-2xl text-slate-400 dark:bg-white/10">⌂</div>
                        <h2 className="mt-4 font-semibold text-slate-900 dark:text-white">Aucune organisation pour l’instant</h2>
                        <p className="mt-1 text-sm text-slate-500 dark:text-slate-400">Créez une organisation pour inviter son premier administrateur.</p>
                    </div>
                )}
                <div className="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-5 py-4 text-sm dark:border-white/10">
                    <span className="text-slate-500 dark:text-slate-400">{organizations.total} organisation{organizations.total === 1 ? '' : 's'} · Page {organizations.current_page} sur {organizations.last_page}</span>
                    <nav className="flex gap-1" aria-label="Pagination">
                        {organizations.links.map((link, index) => {
                            const label = link.label.replace(/<[^>]+>/g, '').replaceAll('&laquo;', '←').replaceAll('&raquo;', '→').replace('Previous', 'Précédent').replace('Next', 'Suivant');
                            return link.url ? (
                                <Link key={`${label}-${index}`} href={link.url} className={`rounded-lg px-3 py-2 ${link.active ? 'bg-brand-600 font-semibold text-white' : 'text-slate-600 hover:bg-slate-100 dark:text-slate-300 dark:hover:bg-white/10'}`} aria-current={link.active ? 'page' : undefined}>{label}</Link>
                            ) : <span key={`${label}-${index}`} className="px-3 py-2 text-slate-300 dark:text-slate-600">{label}</span>;
                        })}
                    </nav>
                </div>
            </section>
        </AppLayout>
    );
}
